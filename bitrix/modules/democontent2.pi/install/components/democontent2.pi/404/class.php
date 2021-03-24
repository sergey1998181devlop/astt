<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 25.09.2018
 * Time: 09:32
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class NotFound extends CBitrixComponent
{
    public function executeComponent()
    {
        CHTTP::SetStatus("404 Not Found");
        if (!defined('ERROR_404')) {
            @define('ERROR_404', 'Y');
        }
        $this->IncludeComponentTemplate();
    }
}
