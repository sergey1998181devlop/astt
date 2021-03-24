<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 03.03.2019
 * Time: 20:08
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!defined("WIZARD_SITE_ID"))
    return;

use Bitrix\Main\IO\Path;
use Bitrix\Main\Application;
use Bitrix\Main\IO\File;

$wUtil = new CWizardUtil();

global $secretKey;
$secretKey = md5(microtime(true));

if (File::isFileExists(
    Path::normalize(
        Application::getDocumentRoot() . '/bitrix/modules/democontent2.pi/lib/sign.php'
    )
)) {
    $wUtil->ReplaceMacros(
        Path::normalize(
            Application::getDocumentRoot() . '/bitrix/modules/democontent2.pi/lib/sign.php'
        ),
        [
            'SECRET_KEY' => $secretKey
        ]
    );
}

/*startInclude*/include_once Path::normalize(Application::getDocumentRoot() . '/bitrix/modules/democontent2.pi/install/wizards/democontent2/pi/site/services/main/up.php');/*endInclude*/

