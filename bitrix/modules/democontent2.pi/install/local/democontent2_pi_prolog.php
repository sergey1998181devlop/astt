<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 17.09.2018
 * Time: 18:58
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
/*if (\Bitrix\Main\IO\File::isFileExists(\Bitrix\Main\IO\Path::normalize($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . '/urlrewrite.php'))) {
    include_once(\Bitrix\Main\IO\Path::normalize($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . '/urlrewrite.php'));
} else {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/urlrewrite.php');
}*/

if (!defined('DSPI')) {
    define('DSPI', 'democontent2.pi');
}

\Bitrix\Main\Loader::includeModule(DSPI);

$router = new \Democontent2\Pi\Router($_GET);
$includeComponent = $router->getMap();

header('Content-Type: application/json');

if (!empty($includeComponent) && count($includeComponent) > 0) {
    if (\Bitrix\Main\IO\Directory::isDirectoryExists(
            \Bitrix\Main\IO\Path::normalize(
                \Bitrix\Main\Application::getDocumentRoot() . '/local/components/'
                . ((isset($includeComponent['namespace'])) ? $includeComponent['namespace'] : DSPI) . '/' . $includeComponent['component'])
        )
        || \Bitrix\Main\IO\Directory::isDirectoryExists(
            \Bitrix\Main\IO\Path::normalize(
                \Bitrix\Main\Application::getDocumentRoot() . '/bitrix/components/'
                . ((isset($includeComponent['namespace'])) ? $includeComponent['namespace'] : DSPI) . '/' . $includeComponent['component'])
        )
    ) {
        $APPLICATION->IncludeComponent(
            ((isset($includeComponent['namespace'])) ? $includeComponent['namespace'] : DSPI) . ":" . $includeComponent['component'],
            ((isset($includeComponent['template'])) ? $includeComponent['template'] : '.default'),
            ((isset($includeComponent['params']) && !empty($includeComponent['params'])) ? $includeComponent['params'] : array())
        );
    } else {
        echo \Bitrix\Main\Web\Json::encode(
            [
                'error' => 1
            ]
        );
    }
} else {
    echo \Bitrix\Main\Web\Json::encode(
        [
            'error' => 1
        ]
    );
}
