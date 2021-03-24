<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.01.2019
 * Time: 12:04
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
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