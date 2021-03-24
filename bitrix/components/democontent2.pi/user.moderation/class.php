<?php
/**
 * Date: 01.10.2019
 * Time: 14:40
 * User: Ruslan Semagin
 * Company: PIXEL365
 * Web: https://pixel365.ru
 * Email: pixel.365.24@gmail.com
 * Phone: +7 (495) 005-23-76
 * Skype: pixel365
 * Product Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 * Use of this code is allowed only under the condition of full compliance with the terms of the license agreement,
 * and only as part of the product.
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class UserModerationComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $us = new \Democontent2\Pi\User(intval($USER->GetID()));


        $items = new \Democontent2\Pi\Iblock\Items();
        $this->arResult['ITEMS'] = $items->moderation();



        $arIdes = [];
        foreach ($this->arResult['ITEMS'] as $idUserItems => $item){
            $arIdes[] = $item['UF_USER_ID'];
        }


        $this->arResult['COMPANY']['ITEMS'] = $us->getListCompanyToTask($arIdes);

        foreach ($arIdes as $id => $valueIdCompany){
            $this->arResult['ITEMS'][$id]['COMPANY'] = $this->arResult['COMPANY']['ITEMS'][$id];
        }



        $idUserCurrent = $USER->GetID();
        $rsUser = CUser::GetByID($idUserCurrent);
        $arUser = $rsUser->Fetch();


        $this->arResult['MODERATION_CURRENT_USER'] = $arUser['UF_MODERATION_ACCESS'];

        if(in_array(8, $USER->GetUserGroupArray()) ||  in_array(1, $USER->GetUserGroupArray())){

            $this->arResult['LIST_COMPANY'] = array_reverse ($us->getListCompanies());



            
        }else{
            LocalRedirect(SITE_DIR, true);
        }

        if ($request->getPost('company_update')) {

            $us->updateCompany();
            if (strlen($us->getRedirect())) {
                LocalRedirect($us->getRedirect(), true);
            }
        }
        $this->includeComponentTemplate();
    }
}
