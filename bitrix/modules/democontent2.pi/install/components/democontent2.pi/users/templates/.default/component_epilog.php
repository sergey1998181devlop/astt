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
