<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.01.2019
 * Time: 13:18
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;

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