<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 08.10.2018
 * Time: 09:38
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;

$APPLICATION->SetTitle(\Bitrix\Main\Localization\Loc::getMessage('DETAIL_COMPONENT_MODERATION_EPILOG_MESSAGE'));
$APPLICATION->SetPageProperty('title', \Bitrix\Main\Localization\Loc::getMessage('DETAIL_COMPONENT_MODERATION_EPILOG_MESSAGE'));
$APPLICATION->AddChainItem(
    $arResult['UF_NAME']
);