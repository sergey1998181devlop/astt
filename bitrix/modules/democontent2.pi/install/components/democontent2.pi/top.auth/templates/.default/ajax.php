<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.01.2019
 * Time: 10:15
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$error = 1;

global $USER;

if ($request->isAjaxRequest() && $request->isPost() && !$USER->IsAuthorized()) {
    if (\Bitrix\Main\Loader::includeModule('democontent2.pi')) {
        $phoneRequired = intval(\Bitrix\Main\Config\Option::get(DSPI, 'register_phone_required')) > 0;

        if ($request->getPost('name') && $request->getPost('email')) {
            $allow = true;
            if ($phoneRequired) {
                if (!$request->getPost('phone')) {
                    $allow = false;
                }
            }

            if ($allow) {
                if (\Bitrix\Main\Config\Option::get(DSPI, 'reCaptchaPublic')
                    && \Bitrix\Main\Config\Option::get(DSPI, 'reCaptchaSecret')) {
                    $secret = \Bitrix\Main\Config\Option::get(DSPI, 'reCaptchaSecret');
                    $reCaptcha = new \ReCaptcha\ReCaptcha($secret);
                    $response = $reCaptcha->verify(
                        $request->getPost('reCaptchaCode'),
                        \Bitrix\Main\Application::getInstance()->getContext()->getServer()->get('REMOTE_ADDR')
                    );

                    if ($response->isSuccess()) {
                        $us = new \Democontent2\Pi\User(0);
                        $us->setName(urldecode($request->getPost('name')));
                        $us->setEmail($request->getPost('email'));
                        if ($phoneRequired) {
                            $us->setPhone($request->getPost('phone'));
                        }
                        if ($us->register()) {
                            $error = 0;
                        }
                    } else {
                        $error = 1;
                    }
                } else {
                    $us = new \Democontent2\Pi\User(0);
                    $us->setName(urldecode($request->getPost('name')));
                    $us->setEmail($request->getPost('email'));
                    if ($phoneRequired) {
                        $us->setPhone($request->getPost('phone'));
                    }
                    if ($us->register()) {
                        $error = 0;
                    }
                }
            }
        }

        if ($request->getPost('restorePasswordEmail') && $request->getPost('restorePasswordPhone')) {
            $us = new \Democontent2\Pi\User(0);
            $us->setEmail($request->getPost('restorePasswordEmail'));
            $us->setPhone($request->getPost('restorePasswordPhone'));
            if ($us->restorePassword()) {
                $error = 0;
            }
        }
    }
}

echo \Bitrix\Main\Web\Json::encode(
    array(
        'error' => $error
    )
);