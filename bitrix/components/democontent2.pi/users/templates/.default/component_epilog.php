<?php
/**
 * User: Nazarkin Sergey
 * Email: nazarkin2017@mail.ru
 * Date: 07.10.2020
 * Time: 13:07
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$titlePostfix = [];
$url = [];
$url[] = SITE_DIR . 'users';

$APPLICATION->AddChainItem(
    \Bitrix\Main\Localization\Loc::getMessage('USERS_COMPONENT_TITLE'),
    implode('/', $url) . '/'
);
foreach ($arResult['META'] as $meta) {
    $url[] = $meta['code'];
    $titlePostfix[] = $meta['name'];

    $APPLICATION->AddChainItem(
        $meta['name'],
        implode('/', $url) . '/'
    );
}

$APPLICATION->SetTitle(
    \Bitrix\Main\Localization\Loc::getMessage('USERS_COMPONENT_TITLE')
    . ((count($titlePostfix) > 0) ? ' ' . \Bitrix\Main\Localization\Loc::getMessage('USERS_COMPONENT_TITLE_IN_CATEGORY') . ' ' . implode(' > ', $titlePostfix) : '')
);
