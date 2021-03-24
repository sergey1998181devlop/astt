<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 07.01.2019
 * Time: 15:40
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Path;
use Bitrix\Main\IO\File;

if (!defined('DSPI')) {
    define('DSPI', 'democontent2.pi');
}

Loader::includeModule('iblock');
Loader::includeModule('highloadblock');

try {
    if (File::isFileExists(Path::normalize(Application::getDocumentRoot() . '/local/dspi/lib/Yandex/lib/autoload.php'))) {
        include_once Path::normalize(Application::getDocumentRoot() . '/local/dspi/lib/Yandex/lib/autoload.php');
    }

    if (File::isFileExists(Path::normalize(Application::getDocumentRoot() . '/local/dspi/lib/Yandex/vendor/autoload.php'))) {
        include_once Path::normalize(Application::getDocumentRoot() . '/local/dspi/lib/Yandex/vendor/autoload.php');
    }

    if (File::isFileExists(Path::normalize(Application::getDocumentRoot() . '/local/dspi/lib/MongoDB/vendor/autoload.php'))) {
        include_once Path::normalize(Application::getDocumentRoot() . '/local/dspi/lib/MongoDB/vendor/autoload.php');
    }

    if (File::isFileExists(Path::normalize(Application::getDocumentRoot() . '/local/dspi/vendor/autoload.php'))) {
        include_once Path::normalize(Application::getDocumentRoot() . '/local/dspi/vendor/autoload.php');
    }
} catch (\Bitrix\Main\IO\InvalidPathException $e) {
} catch (\Exception $e) {
}