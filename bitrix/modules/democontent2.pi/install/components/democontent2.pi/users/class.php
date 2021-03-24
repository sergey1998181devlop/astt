<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 25.09.2018
 * Time: 10:34
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class UsersComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;

        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $us = new \Democontent2\Pi\User(0);
        $cities = new \Democontent2\Pi\Iblock\City();

        $this->arResult['CITY_ID'] = 0;
        $this->arResult['CITIES'] = $cities->getList();
        $this->arResult['META'] = [];
        $this->arResult['H1_POSTFIX'] = '';
        $this->arResult['FAVOURITES'] = [];

        if ($USER->IsAuthorized()) {
            $favourites = new \Democontent2\Pi\Favourites(intval($USER->GetID()));
            $this->arResult['FAVOURITES'] = $favourites->getIdList();
        }

        $iBlockType = '';
        $iBlockId = 0;

        $currentPage = 1;
        if ($request->get('page')) {
            if (intval($request->get('page')) > 1) {
                $currentPage = intval($request->get('page'));
                $us->setOffset((intval($request->get('page')) - 1) * $us->getLimit());
            }
        }

        if ($request->get('city')) {
            if (intval($request->get('city')) > 0 && isset($this->arResult['CITIES'][intval($request->get('city'))])) {
                $us->setCityId($request->get('city'));
            }
        } else {
            if (intval($request->getCookie('current_city')) > 0) {
                if (isset($this->arResult['CITIES'][intval($request->getCookie('current_city'))])) {
                    $us->setCityId(intval($request->getCookie('current_city')));
                }
            }
        }

        if ($us->getCityId()) {
            $this->arResult['CITY_ID'] = $us->getCityId();
        }

        if ($request->get('verification')) {
            if (intval($request->get('verification')) > 0) {
                $us->setVerification(1);
            }
        }

        $us->setOrder(['USER_ID' => 'ASC']);

        if ($request->get('order')) {
            switch ($request->get('order')) {
                case 'desc':
                    $us->setOrder(['USER_ID' => 'DESC']);
                    break;
                case 'rating':
                    $us->setOrder(['USER_RATING' => 'DESC']);
                    break;
            }
        }

        if (isset($this->arParams['iBlockType'])) {
            $allow = true;

            $iblock = new \Democontent2\Pi\Iblock\Iblock();
            $iblocks = $iblock->getAllList($this->arParams['iBlockType']);

            $this->arResult['META'][] = [
                'name' => $iblock->getTypeName('democontent2_pi_' . str_replace('-', '_', $this->arParams['iBlockType'])),
                'code' => $this->arParams['iBlockType']
            ];

            if (!isset($iblocks['democontent2_pi_' . str_replace('-', '_', $this->arParams['iBlockType'])])) {
                $allow = false;
            } else {
                $iBlockType = $this->arParams['iBlockType'];
            }

            if ($allow) {
                if (isset($this->arParams['iBlockCode'])) {
                    $allow = false;

                    foreach ($iblocks['democontent2_pi_' . str_replace('-', '_', $this->arParams['iBlockType'])] as $item) {
                        if ($item['code'] == $this->arParams['iBlockCode']) {
                            $this->arResult['META'][] = $item;

                            $iBlockId = intval($item['id']);
                            $allow = true;
                            break;
                        }
                    }
                }

                if ($allow) {
                    $this->arResult['ITEMS'] = $us->getExecutors($iBlockType, $iBlockId);
                    $this->arResult['TOTAL'] = $us->getTotal();
                    $this->arResult['LIMIT'] = $us->getLimit();
                    $this->arResult['OFFSET'] = $us->getOffset();
                    $this->arResult['CURRENT_PAGE'] = $currentPage;

                    $this->includeComponentTemplate();
                }
            } else {
                CHTTP::SetStatus('404 Not Found');
            }
        } else {
            $this->arResult['ITEMS'] = $us->getExecutors($iBlockType, $iBlockId);
            $this->arResult['TOTAL'] = $us->getTotal();
            $this->arResult['LIMIT'] = $us->getLimit();
            $this->arResult['OFFSET'] = $us->getOffset();
            $this->arResult['CURRENT_PAGE'] = $currentPage;

            $this->includeComponentTemplate();
        }
    }
}