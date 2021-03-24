<?php

use Democontent2\Pi\CheckList\Response;

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 14.01.2019
 * Time: 19:15
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class UserItemsDetailComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        global $APPLICATION;

        if ($USER->IsAuthorized()) {
            $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
            $item = new \Democontent2\Pi\Iblock\Item();
            $item->setIBlockId($this->arParams['iBlockId']);
            $item->setItemId($this->arParams['itemId']);
            $item->setUserId($USER->GetID());
            $this->arResult['ITEM'] = $item->getForEdit();

            if (isset($this->arResult['ITEM']['ID'])) {
                $this->arResult['RESPONSE_CHECKLIST'] = [];
                $this->arResult['ALLOW_CHECKLISTS'] = intval(\Bitrix\Main\Config\Option::get(DSPI, 'response_checklists'));

                if ($this->arResult['ALLOW_CHECKLISTS']) {
                    $responseChecklist = new Response(intval($this->arResult['ITEM']['UF_ID']));
                    $this->arResult['RESPONSE_CHECKLIST'] = $responseChecklist->getList();
                }


                if (intval($this->arResult['ITEM']['UF_STATUS']) !== 1) {
                    LocalRedirect(SITE_DIR . 'task' . $this->arResult['ITEM']['UF_ID'] . '-' . $this->arResult['ITEM']['UF_IBLOCK_ID'] . '/', true);
                }

                if ($request->isPost()) {
                    $item->edit($this->arResult['ITEM'], $_POST, $_FILES);
                    LocalRedirect($APPLICATION->GetCurPage(false), true);
                }

                $city = new \Democontent2\Pi\Iblock\City();
                $menu = new \Democontent2\Pi\Iblock\Menu();
                $this->arResult['CATEGORIES'] = $menu->get();
                $this->arResult['CITIES'] = $city->getList();
                if (count($this->arResult['ITEM']['STAGES'])) {
                    $this->setTemplateName('stages');
                }

                $this->includeComponentTemplate();
            } else {
                LocalRedirect(SITE_DIR, true);
            }
        } else {
            LocalRedirect(SITE_DIR, true);
        }
    }
}