<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 15.01.2019
 * Time: 10:57
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class UserBlockedComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        global $APPLICATION;

        if ($USER->IsAuthorized()) {
            $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
            $blackList = new \Democontent2\Pi\Iblock\BlackList();
            $blackList->setUserId(intval($USER->GetID()));

            $this->arResult['ITEMS'] = $blackList->getList();

            if ($request->isPost()) {
                if ($request->getPost('unBlocked')) {
                    foreach ($this->arResult['ITEMS'] as $item) {
                        if (intval($item['ID']) == intval($request->getPost('unBlocked'))) {
                            $blackList->setBlockedId(intval($item['ID']));
                            $blackList->remove();
                            break;
                        }
                    }
                }

                LocalRedirect($APPLICATION->GetCurPage(false), true);
            }

            $this->includeComponentTemplate();
        } else {
            LocalRedirect(SITE_DIR, true);
        }
    }
}