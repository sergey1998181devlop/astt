<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 11.01.2019
 * Time: 08:40
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (count($arResult) > 0) {
    $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
    $title = $arResult['UF_NAME'];
    $description = '';
    $keywords = '';
    $tempPath = array();
    /*if (isset($arResult['CITY']['id']) && !$arResult['CITY']['default']) {
        if (!intval($request->getCookie('skipCity'))) {
            $tempPath[] = 'city-' . $arResult['CITY']['code'];
        }
    }
    */

    $tempPath[] = $arResult['UF_IBLOCK_TYPE'];
    $param = $APPLICATION->AddChainItem($arResult['IBLOCK_TYPE_NAME']);
//    pre($arResult);
//
    $tempPath[] = $arResult['UF_IBLOCK_CODE'];
    $APPLICATION->AddChainItem($arResult['IBLOCK_NAME']);
//
    $APPLICATION->AddChainItem($arResult['UF_NAME']);

    if (isset($arResult['META']['ELEMENT_META_TITLE'])) {
        $title = $arResult['META']['ELEMENT_META_TITLE'];
    }

    if (isset($arResult['META']['ELEMENT_META_DESCRIPTION'])) {
        $description = $arResult['META']['ELEMENT_META_DESCRIPTION'];
    }

    if (isset($arResult['META']['ELEMENT_META_KEYWORDS'])) {
        $keywords = $arResult['META']['ELEMENT_META_KEYWORDS'];
    }

    $APPLICATION->SetTitle($title);
    $APPLICATION->SetPageProperty('title', $title);

    if (strlen($description) > 0) {
        $APPLICATION->SetPageProperty('description', $description);
    }

    if (strlen($keywords) > 0) {
        $APPLICATION->SetPageProperty('keywords', $keywords);
    }

    unset($tempPath);
}