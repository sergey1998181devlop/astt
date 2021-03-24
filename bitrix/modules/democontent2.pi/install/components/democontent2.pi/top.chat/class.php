<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.01.2019
 * Time: 08:44
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class TopChatComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;

        if (intval(\Bitrix\Main\Config\Option::get(DSPI, 'chatEnabled'))) {
            if ($USER->IsAuthorized()) {
                $this->includeComponentTemplate();
            } else {
                return;
            }
        } else {
            return;
        }
    }
}