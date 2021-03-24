<?php
/**
 * User: Nazarkin Sergey
 * Email: nazarkin2017@mail.ru
 * Date: 07.10.2020
 * Time: 13:07
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$titlePostfix = [];
foreach ($arResult['META'] as $meta) {
    $titlePostfix[] = $meta['name'];
}

if (count($titlePostfix) > 0) {
    $arResult['H1_POSTFIX'] = \Bitrix\Main\Localization\Loc::getMessage('USERS_COMPONENT_TITLE_IN_CATEGORY') . ' ' . implode(', ', $titlePostfix);
}
