<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 30.01.2019
 * Time: 12:32
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/../..");

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
set_time_limit(0);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
if (\Bitrix\Main\Loader::includeModule('democontent2.pi')) {
    if ($argv[1] == \Democontent2\Pi\Sign::getInstance()->get()) {
        $temporaryTask = new \Democontent2\Pi\Iblock\TemporaryTask();
        $temporaryTask->removeExpires((isset($argv[2]) && intval($argv[2]) > 0) ? intval($argv[2]) : 10);
    }
}

die();