<?php
/**
 * User: Nazarkin Sergey
 * Email: nazarkin2017@mail.ru
 * Date: 07.10.2020
 * Time: 13:07
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;

$APPLICATION->SetTitle(\Bitrix\Main\Localization\Loc::getMessage('DETAIL_COMPONENT_EPILOG_404_MESSAGE'));
$APPLICATION->SetPageProperty('title', \Bitrix\Main\Localization\Loc::getMessage('DETAIL_COMPONENT_EPILOG_404_MESSAGE'));