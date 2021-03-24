<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 12.01.2019
 * Time: 17:27
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$APPLICATION->SetTitle(
    \Bitrix\Main\Localization\Loc::getMessage('USER_PUBLIC_TITLE') . ' ' . $arResult['USER']['NAME'] . ((strlen($arResult['USER']['LAST_NAME'])) ? ' ' . $arResult['USER']['LAST_NAME'] : '')
);
$APPLICATION->AddChainItem($arResult['USER']['NAME'] . ((strlen($arResult['USER']['LAST_NAME'])) ? ' ' . $arResult['USER']['LAST_NAME'] : ''));