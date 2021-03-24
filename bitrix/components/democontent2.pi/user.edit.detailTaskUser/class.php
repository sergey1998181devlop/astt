<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.01.2019
 * Time: 09:28
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class UserCreateComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        global $APPLICATION;

        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

        $this->arResult['CITY_ID'] = 0;
        $this->arResult['ALLOW_CHECKLISTS'] = intval(\Bitrix\Main\Config\Option::get(DSPI, 'response_checklists'));

        try {
            $this->arResult['COURIER_IBLOCKS'] = \Bitrix\Main\Web\Json::decode(
                \Bitrix\Main\Config\Option::get(DSPI, 'courierIblocks')
            );
        } catch (\Bitrix\Main\ArgumentNullException $e) {
        } catch (\Bitrix\Main\ArgumentOutOfRangeException $e) {
        } catch (\Bitrix\Main\ArgumentException $e) {
            $this->arResult['COURIER_IBLOCKS'] = [];
        }

        if ($USER->IsAuthorized()) {

            $menu = new \Democontent2\Pi\Iblock\Menu();
            $city = new \Democontent2\Pi\Iblock\City();
            $item = new \Democontent2\Pi\Iblock\Item();


            $this->arResult = $item->gatItemDetailEdit();

            //собираем все поля для предзагрузки на редактирование заявки
//            pre($this->arResult);


            $this->arResult['c'] = $item->getDemoContent( $this->arResult['UF_ITEM_ID']);

            $this->arResult['CITIES'] = $city->getList();
            $this->arResult['CATEGORIES'] = $menu->get();
            $this->arResult['COUNT_MONEY_ZA'] = $menu->getCountEd();
//            debmes( $this->arResult['COUNT_MONEY_ZA']  );

            if (intval($request->getCookie('current_city')) > 0) {
                if (isset($this->arResult['CITIES'][intval($request->getCookie('current_city'))])) {
                    $this->arResult['CITY_ID'] = intval($request->getCookie('current_city'));
                }
            }

            if ( $_POST['assetFormTask'] == 'Y' ) {


                $item = new \Democontent2\Pi\Iblock\Item(0);
                $item->setUserId(intval($USER->GetID()));
                $item->update($_POST, $_FILES);



//                LocalRedirect(SITE_DIR . 'itemcreate/success.php', true);

            }

            $this->includeComponentTemplate();
        } else{
            LocalRedirect(SITE_DIR . '/', true);
        }
    }
}