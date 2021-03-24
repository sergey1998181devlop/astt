<?php
/**
 * Date: 07.11.2019
 * Time: 20:10
 * User: Ruslan Semagin
 * Company: PIXEL365
 * Web: https://pixel365.ru
 * Email: pixel.365.24@gmail.com
 * Phone: +7 (495) 005-23-76
 * Skype: pixel365
 * Product Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 * Use of this code is allowed only under the condition of full compliance with the terms of the license agreement,
 * and only as part of the product.
 */
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/../..");

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
set_time_limit(0);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
try {
    if (\Bitrix\Main\Loader::includeModule('democontent2.pi')) {
        if ($argv[1] == \Democontent2\Pi\Sign::getInstance()->get()) {
            $pushQueue = new \Democontent2\Pi\Iblock\PushQueue();
            $pushQueue->send();
        }
    }
} catch (\Bitrix\Main\ArgumentNullException $e) {
} catch (\Bitrix\Main\ArgumentOutOfRangeException $e) {
} catch (\Bitrix\Main\ObjectPropertyException $e) {
} catch (\Bitrix\Main\ArgumentException $e) {
} catch (\Bitrix\Main\LoaderException $e) {
} catch (\Bitrix\Main\SystemException $e) {
}

die();
