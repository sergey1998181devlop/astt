<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 15.01.2019
 * Time: 09:35
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

try {
    if ($arParams['hash'] == \Democontent2\Pi\Sign::getInstance()->get()) {
        $payment = new \Democontent2\Pi\Payment();
        $payment->setSource(file_get_contents('php://input'));
        $payment->setParams($_REQUEST);
        $payment->log();
        $payment->verify();
        $payment->getResult();
    } else {
        die();
    }
} catch (\Exception $e) {
    die();
}