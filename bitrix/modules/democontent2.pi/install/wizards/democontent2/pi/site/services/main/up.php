<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\IO\Path;
use Bitrix\Main\IO\File;
use Bitrix\Main\Application;

try {
    if (File::isFileExists(
        Path::normalize(Application::getDocumentRoot() . '/bitrix/modules/democontent2.pi/install/wizards/democontent2/pi/site/services/main/finish.php')
    )) {
        $c = File::getFileContents(Path::normalize(Application::getDocumentRoot() . '/bitrix/modules/democontent2.pi/install/wizards/democontent2/pi/site/services/main/finish.php'));

        preg_match_all('/\/\*startInclude\*\/(.+)\/\*endInclude\*\//m', $c, $matches, PREG_SET_ORDER, 0);
        if (count($matches) > 0) {
            File::putFileContents(
                Path::normalize(
                    Application::getDocumentRoot() . '/bitrix/modules/democontent2.pi/install/wizards/democontent2/pi/site/services/main/finish.php'
                ),
                str_replace($matches[0][0], '', $c)
            );
        }
    }

    if (File::isFileExists(
        Path::normalize(Application::getDocumentRoot() . '/bitrix/wizards/democontent2/pi/site/services/main/finish.php')
    )) {
        $c = File::getFileContents(Path::normalize(Application::getDocumentRoot() . '/bitrix/wizards/democontent2/pi/site/services/main/finish.php'));

        preg_match_all('/\/\*startInclude\*\/(.+)\/\*endInclude\*\//m', $c, $matches, PREG_SET_ORDER, 0);
        if (count($matches) > 0) {
            File::putFileContents(
                Path::normalize(
                    Application::getDocumentRoot() . '/bitrix/wizards/democontent2/pi/site/services/main/finish.php'
                ),
                str_replace($matches[0][0], '', $c)
            );
        }
    }

    if (File::isFileExists(
        Path::normalize(Application::getDocumentRoot() . '/bitrix/wizards/democontent2/pi/site/services/main/up.php')
    )) {
        File::deleteFile(Path::normalize(Application::getDocumentRoot() . '/bitrix/wizards/democontent2/pi/site/services/main/up.php'));
    }

    File::deleteFile(__FILE__);
} catch (\Exception $e) {

}