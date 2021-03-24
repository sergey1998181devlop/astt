<?php

/**
 * User: Nazarkin Sergey
 * Email: nazarkin2017@mail.ru
 * Date: 07.10.2020
 * Time: 13:07
 */
class AllTasksComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        $us = new \Democontent2\Pi\User(intval($USER->GetID()));
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $this->arResult['ITEMS'] = array();

        if (\Bitrix\Main\Loader::includeModule('democontent2.pi')) {
            $city = new \Democontent2\Pi\Iblock\City();
            $items = new \Democontent2\Pi\Iblock\Items();

            $cities = $city->getList();

            if (isset($this->arParams['LIMIT']) && intval($this->arParams['LIMIT']) > 0) {
                $items->setLimit(intval($this->arParams['LIMIT']));
            }

            if (count($cities) < 2 || intval($request->getCookie('skipCity'))) {
                $items->setSkipCity(true);
            }

            if (intval($request->getCookie('current_city')) > 0) {
                if (!$items->isSkipCity()) {
                    if (isset($cities[intval($request->getCookie('current_city'))])) {
                        $items->setCityId(intval($request->getCookie('current_city')));
                    }
                }
            }

            $currentPage = 1;
            if ($request->get('page')) {
                if (intval($request->get('page')) > 1) {
                    $currentPage = intval($request->get('page'));
                    $items->setOffset((intval($request->get('page')) - 1) * $items->getLimit());
                }
            }

            if ($request->get('param1')) {
                $items->setQuickly(true);
            }

            if ($request->get('param2')) {
                $items->setNoResponses(true);
            }

            if ($request->get('param3')) {
                $items->setSafe(true);
            }

            if ($request->get('param4')) {
                $items->setMin10Responses(true);
            }

            $this->arResult['ORDER'] = 'new';

            if ($request->get('order')) {
                switch ($request->get('order')) {
                    case 'old':
                        $items->setOrder(['UF_CREATED_AT' => 'ASC']);
                        $this->arResult['ORDER'] = $request->get('order');
                        break;
                    case 'expensive':
                        $items->setOrder(['UF_PRICE' => 'DESC']);
                        $this->arResult['ORDER'] = $request->get('order');
                        break;
                    case 'cheap':
                        $items->setOrder(['UF_PRICE' => 'ASC']);
                        $this->arResult['ORDER'] = $request->get('order');
                        break;
                    case 'popular':
                        $items->setOrder(['UF_COUNTER' => 'DESC']);
                        $this->arResult['ORDER'] = $request->get('order');
                        break;
                    case 'moreFeedback':
                        $items->setOrder(
                            [
                                'UF_RESPONSE_COUNT' => 'DESC',
                                'ID' => 'DESC'
                            ]
                        );
                        $this->arResult['ORDER'] = $request->get('order');
                        break;
                    case 'lessFeedback':
                        $items->setOrder(
                            [
                                'UF_RESPONSE_COUNT' => 'ASC',
                                'ID' => 'DESC'
                            ]
                        );
                        $this->arResult['ORDER'] = $request->get('order');
                        break;
                    default:
                        $items->setOrder(['UF_CREATED_AT' => 'DESC']);
                        $this->arResult['ORDER'] = 'new';
                }
            }
            //собираю все элементы
            $this->arResult['CITIES_FILTER'] = $items->getCities(1);

            $this->arResult['ITEMS'] = $items->getAllTasks();


            //собираю список id пользователей и достаю список компаний по ним
            $arIdes = [];
            foreach ($this->arResult['ITEMS'] as $idUserItems => $item){
                $arIdes[] = $item['UF_USER_ID'];
            }
            //достаю список компаний
            $this->arResult['COMPANY']['ITEMS'] = $us->getListCompanyToTask($arIdes);


            //раскидываю компании по элементам
            foreach ($arIdes as $id => $valueIdCompany){


                $this->arResult['ITEMS'][$id]['COMPANY'] = $this->arResult['COMPANY']['ITEMS'][$id];
            }

//            debmes(   $this->arResult['ITEMS'] );
//            die();
            $this->arResult['TOTAL'] = $items->getTotal();
            $this->arResult['LIMIT'] = $items->getLimit();
            $this->arResult['OFFSET'] = $items->getOffset();
            $this->arResult['CURRENT_PAGE'] = $currentPage;
            $this->arResult['CITY_NAME'] = '';




            $idUserCurrent = $USER->GetID();
            $rsUser = CUser::GetByID($idUserCurrent);
            $arUser = $rsUser->Fetch();

            $this->arResult['MODERATION_CURRENT_USER'] = $arUser['UF_MODERATION_ACCESS'];



            if($USER->IsAuthorized()){
                $favourites = new \Democontent2\Pi\FavouritesTask(intval($USER->GetID()));

                $this->arResult['status_executor'] = [];
                foreach ( $this->arResult['ITEMS'] as $id => $valueItem){
                    $this->arResult['ITEMS'][$id]['status_executor'] = $favourites->getElement($USER->GetID()  ,$valueItem['UF_ITEM_ID'] );

                }
            }


            if ($items->getCityId() && !$items->isSkipCity()) {
                if (isset($cities[$items->getCityId()])) {
                    $this->arResult['CITY_NAME'] = $cities[$items->getCityId()]['name'];
                }
            }
        }

        $this->includeComponentTemplate();
    }
}