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

/*
if(!empty($arResult['USER']['COMPANY'][0]['ID'])){
    $APPLICATION->AddChainItem($arResult['USER']['COMPANY'][0]['UF_COMPANY_NAME_MIN'] );
}else{
    $APPLICATION->AddChainItem($arResult['USER']['NAME'] . ((strlen($arResult['USER']['LAST_NAME'])) ? ' ' . $arResult['USER']['LAST_NAME'] : ''));
}

*/
$APPLICATION->AddChainItem("Список исполнителей" , "/users/");

if($USER->IsAuthorized() && is_string($arResult['MODERATION_CURRENT_USER'])  ){
    if(!empty($arResult['COMPANY'][0]['ID'])){
        $APPLICATION->AddChainItem($arResult['COMPANY'][0]['UF_COMPANY_NAME_MIN']);

    }else{
        $APPLICATION->AddChainItem($arResult['USER']['NAME'] . ((strlen($arResult['USER']['LAST_NAME'])) ? ' ' . $arResult['USER']['LAST_NAME'] : ''));

    }
}else{
    $APPLICATION->AddChainItem('Компания');
}