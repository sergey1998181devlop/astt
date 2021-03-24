<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 22.10.2018
 * Time: 14:14
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class SocketIOComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        if (!intval(\Bitrix\Main\Config\Option::get(DSPI, 'chatEnabled'))) {
            return;
        }
        $this->includeComponentTemplate();
    }
}