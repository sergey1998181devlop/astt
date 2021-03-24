<?php
/**
 * Date: 14.08.2019
 * Time: 11:32
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

global $USER;

if ($USER->IsAuthorized() && $request->isAjaxRequest() && $request->isPost()) {
    if (\Bitrix\Main\Loader::includeModule('democontent2.pi')) {
        if ($request->getPost('type')) {
            $us = new \Democontent2\Pi\User($USER->GetID());
            if ($us->toggleBusy($request->getPost('type'))) {
                $error = 0;
            }
        }
    }
}

echo \Bitrix\Main\Web\Json::encode(['error' => $error]);
