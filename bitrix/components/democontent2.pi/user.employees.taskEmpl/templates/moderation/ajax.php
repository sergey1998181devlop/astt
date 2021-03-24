<?php
/**
 * Date: 02.10.2019
 * Time: 10:35
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
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$error = 1;
$result = [];

global $USER;

if ($request->isAjaxRequest() && $request->isPost() && $USER->IsAuthorized()) {
    if ($request->getPostList()->get('id') && $request->getPostList()->get('category')) {
        $item = new \Democontent2\Pi\Iblock\Item();
        $item->setIBlockId($request->getPostList()->get('category'));
        $item->setItemId($request->getPostList()->get('id'));
        $result = $item->getMainParams();

        if (count($result)) {
            $error = 0;
            if (intval($result['UF_USER_ID']) !== intval($USER->GetID())) {
                $us = new \Democontent2\Pi\User($USER->GetID());
                $userData = $us->get();

                if (count($userData)) {
                    \Bitrix\Main\Mail\Event::send(
                        [
                            'EVENT_NAME' => 'DSPI_NEW_COMPLAIN',
                            'LID' => \Bitrix\Main\Config\Option::get(DSPI, 'siteId'),
                            'C_FIELDS' => [
                                'IP' => \Bitrix\Main\Application::getInstance()->getContext()->getServer()->get('REMOTE_ADDR'),
                                'USER_ID' => $us->getId(),
                                'USER_NAME' => $userData['NAME'],
                                'USER_EMAIL' => $userData['EMAIL'],
                                'USER_PHONE' => \Democontent2\Pi\Utils::formatPhone($userData['PERSONAL_PHONE']),
                                'IBLOCK_TYPE' => $result['UF_IBLOCK_TYPE'],
                                'IBLOCK_ID' => $request->getPostList()->get('category'),
                                'ITEM_ID' => $request->getPostList()->get('id'),
                                'FULL_URL' => (($request->isHttps()) ? 'https://' : 'http://') . \Bitrix\Main\IO\Path::normalize($request->getHttpHost() . SITE_DIR
                                        . $result['UF_IBLOCK_TYPE'] . '/' . $result['UF_IBLOCK_CODE'] . '/' . $result['UF_CODE']) . '/',
                                'TEXT' => ''
                            ]
                        ]
                    );

                    try {
                        $fireBase = new \Democontent2\Pi\FireBase($result['UF_USER_ID']);
                        $fireBase->webPush([
                            'title' => \Bitrix\Main\Localization\Loc::getMessage('DETAIL_AJAX_PUSH_TITLE'),
                            'body' => \Bitrix\Main\Localization\Loc::getMessage('DETAIL_AJAX_PUSH_BODY', ['{{TASK_ID}}' => $item->getItemId()]),
                            'url' => (($request->isHttps()) ? 'https://' : 'http://') . \Bitrix\Main\IO\Path::normalize($request->getHttpHost() . SITE_DIR
                                    . $result['UF_IBLOCK_TYPE'] . '/' . $result['UF_IBLOCK_CODE'] . '/' . $result['UF_CODE']) . '/'
                        ]);
                    } catch (\Bitrix\Main\ArgumentNullException $e) {
                    } catch (\Bitrix\Main\ArgumentOutOfRangeException $e) {
                    } catch (\Bitrix\Main\IO\InvalidPathException $e) {
                    }
                }
            }
        }
    }

    if ($request->getPostList()->get('contact-id') && $request->getPostList()->get('hash')) {
        if (md5($request->getPostList()->get('contact-id') . \Democontent2\Pi\Sign::getInstance()->get()) == $request->getPostList()->get('hash')) {
            $cost = intval(\Bitrix\Main\Config\Option::get(DSPI, 'contacts_open'));
            if ($cost) {
                $account = new \Democontent2\Pi\Balance\Account($USER->GetID());
                $balance = $account->getAmount();
                if ($balance >= $cost) {
                    $us = new \Democontent2\Pi\User($request->getPostList()->get('contact-id'));
                    $userParams = $us->get();
                    if (isset($userParams['ID'])) {
                        $account->setAmount($cost);
                        if ($account->withdrawal()) {
                            $error = 0;
                            $result = [
                                'phone' => \Democontent2\Pi\Utils::validatePhone($userParams['PERSONAL_PHONE']),
                                'formattedPhone' => \Democontent2\Pi\Utils::formatPhone($userParams['PERSONAL_PHONE']),
                                'balance' => \Democontent2\Pi\Utils::price(($balance - $cost)) . ' ' . \Bitrix\Main\Config\Option::get(DSPI, 'currency_name')
                            ];
                        }
                    }
                }
            }
        }
    }
}

echo \Bitrix\Main\Web\Json::encode(
    [
        'error' => $error,
        'result' => $result
    ]
);
