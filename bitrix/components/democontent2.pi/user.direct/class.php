<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 12.01.2019
 * Time: 17:35
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class UserDirectClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;

        if (!$USER->IsAuthorized() || !intval(\Bitrix\Main\Config\Option::get(DSPI, 'chatEnabled'))) {
            LocalRedirect(SITE_DIR, true);
        }

        $us = new \Democontent2\Pi\User(intval($USER->GetID()));
        $this->arResult['USER'] = $us->get();

        $this->includeComponentTemplate();
    }
}