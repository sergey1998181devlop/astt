<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.01.2019
 * Time: 14:17
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;

if (isset($arResult['TYPE_META']['UF_TITLE'])) {
    $tempPath = array(
        SITE_DIR . $arParams['iBlockType']
    );
    if (isset($arResult['CITY']['id']) && !$arResult['CITY']['default']) {
        $tempPath[] = 'city-' . $arResult['CITY']['code'];
    }
    $APPLICATION->AddChainItem($arResult['TYPE_META']['UF_TITLE'], implode('/', $tempPath) . '/');
}

foreach ($arResult['META'] as $metaKey => $metaValue) {
    switch ($metaKey) {
        case 'UF_TITLE':
            if (strlen($metaValue) > 0) {
                $APPLICATION->AddChainItem($metaValue);
                $APPLICATION->SetTitle($metaValue);
                $APPLICATION->SetPageProperty('title', $metaValue);
            }
            break;
        case 'UF_DESCRIPTION':
            if (strlen($metaValue) > 0) {
                $APPLICATION->SetPageProperty('description', $metaValue);
            }
            break;
        case 'UF_KEYWORDS':
            if (strlen($metaValue) > 0) {
                $APPLICATION->SetPageProperty('keywords', $metaValue);
            }
            break;
    }
}