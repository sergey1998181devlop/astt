<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.01.2019
 * Time: 09:37
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class TopNotifications extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;

        if ($USER->IsAuthorized()) {
            $this->arResult['COUNT'] = \Democontent2\Pi\Notifications::getUnreadCount(intval($USER->GetID()));

            $this->includeComponentTemplate();
        } else {
            return;
        }
    }
}