<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 12.01.2019
 * Time: 17:31
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class UserNotificationsComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;

        if ($USER->IsAuthorized()) {
            \Democontent2\Pi\Notifications::readAll($USER->GetID());

            $this->arResult['ITEMS'] = \Democontent2\Pi\Notifications::getLast($USER->GetID());

            $this->includeComponentTemplate();
        } else {
            LocalRedirect(SITE_DIR, true);
        }
    }
}