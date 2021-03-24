<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.01.2019
 * Time: 09:28
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class TopMenuComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;

        $this->arResult['AUTHORIZED'] = $USER->IsAuthorized();

        $this->includeComponentTemplate();
    }
}