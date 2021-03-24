<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 24.09.2018
 * Time: 14:16
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class SearchClass extends CBitrixComponent
{
    public function executeComponent()
    {
        $menu = new \Democontent2\Pi\Iblock\Menu();
        $this->arResult['CATEGORIES'] = $menu->get();

        $this->IncludeComponentTemplate();
    }
}