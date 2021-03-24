<?php
/**
 * Date: 08.07.2019
 * Time: 16:50
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

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class UserFavouritesComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        $us = new \Democontent2\Pi\User(intval($USER->GetID()));

        if (!$USER->IsAuthorized()) {
            LocalRedirect(SITE_DIR, true);
        }
        $favourites = new \Democontent2\Pi\Favourites(intval($USER->GetID()));
        $items = new \Democontent2\Pi\Iblock\Items();
        $favouritesTask = new \Democontent2\Pi\FavouritesTask(intval($USER->GetID()));
        $item = new \Democontent2\Pi\Iblock\Item();
        $this->arResult['ITEMS'] = $favourites->getList();

        $this->arResult['ID_LIST_TASKS'] = $favouritesTask->getList();









        $this->arResult['c'] = $item->getDemoContent( $this->arResult['ID_LIST_TASKS']['UF_ITEM_ID']);


        $messIdsMCompany = [];
        foreach ($this->arResult['ITEMS'] as $id => $companyId){
            $messIdsMCompany[$id] = $companyId['ID'];
        }

        $companyitems = $us->getCompanyManager($messIdsMCompany);

        foreach ($companyitems as $id => $value){
            $this->arResult['ITEMS'][$id]['COMPANY'] = $value;
        }

        $idUserCurrent = $USER->GetID();
        $rsUser = CUser::GetByID($idUserCurrent);
        $arUser = $rsUser->Fetch();

        $this->arResult['MODERATION_CURRENT_USER'] = $arUser['UF_MODERATION_ACCESS'];




        $this->arResult['ITEMS_TASKS'] = $items->allTasksWithIdList(0 , $this->arResult['ID_LIST_TASKS']);

        $arIdesItemsTask = [];
        foreach ($this->arResult['ITEMS_TASKS'] as $idItemsTask => $item){
            $arIdesItemsTask[] = $item['UF_USER_ID'];
        }
        //достаю список компаний
        $itemsTasks = $us->getListCompanyToTask($arIdesItemsTask);

        //раскидываю компании по элементам
        foreach ($arIdesItemsTask as $id => $valueIdCompany){


            $this->arResult['ITEMS_TASKS'][$id]['COMPANY'] = $itemsTasks[$id];
        }


        $this->includeComponentTemplate();
    }
}
