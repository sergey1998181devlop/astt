<?php
/**
 * User: Nazarkin Sergey
 * Email: nazarkin2017@mail.ru
 * Date: 07.10.2020
 * Time: 13:07
 */
\Bitrix\Main\Loader::includeModule('iblock');
$arComponentParameters = array(
    'GROUPS' => array(
        'SETTINGS' => array(
            'NAME' => \Bitrix\Main\Localization\Loc::getMessage('SETTINGS')
        )
    ),
    'PARAMETERS' => array(
        'LIMIT' => array(
            'PARENT' => 'SETTINGS',
            'NAME' => \Bitrix\Main\Localization\Loc::getMessage('LIMIT'),
            'TYPE' => 'INTEGER',
            'MULTIPLE' => 'N',
            'DEFAULT' => 15
        ),
        'CACHE_TIME' => array(
            'PARENT' => 'SETTINGS',
            'NAME' => \Bitrix\Main\Localization\Loc::getMessage('CACHE_TIME'),
            'TYPE' => 'INTEGER',
            'MULTIPLE' => 'N',
            'DEFAULT' => 3600
        )
    )
);