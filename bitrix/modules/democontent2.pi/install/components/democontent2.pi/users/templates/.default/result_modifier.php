<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 14.03.2019
 * Time: 09:14
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$titlePostfix = [];
foreach ($arResult['META'] as $meta) {
    $titlePostfix[] = $meta['name'];
}

if (count($titlePostfix) > 0) {
    $arResult['H1_POSTFIX'] = \Bitrix\Main\Localization\Loc::getMessage('USERS_COMPONENT_TITLE_IN_CATEGORY') . ' ' . implode(', ', $titlePostfix);
}
