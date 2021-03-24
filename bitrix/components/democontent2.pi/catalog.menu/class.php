<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.01.2019
 * Time: 13:39
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class CatalogMenuComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        $menu = new \Democontent2\Pi\Iblock\Menu();
        $this->arResult['MENU'] = $menu->get();
        $this->arResult['IBLOCK_TYPE'] = '';
        $this->arResult['IBLOCK_CODE'] = '';

        if (isset($this->arParams['IBLOCK_TYPE'])) {
            $this->arResult['IBLOCK_TYPE'] = $this->arParams['IBLOCK_TYPE'];
        }

        if (isset($this->arParams['IBLOCK_CODE'])) {
            $this->arResult['IBLOCK_CODE'] = $this->arParams['IBLOCK_CODE'];
        }

        $this->includeComponentTemplate();
    }
}