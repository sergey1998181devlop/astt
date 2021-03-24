<?php
/**
 * Date: 08.07.2019
 * Time: 16:43
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
$error = 0;

global $USER;

if ($request->isAjaxRequest() && $request->isPost() && $USER->IsAuthorized()) {
    if (intval($request->getPost('id'))) {
        if (\Bitrix\Main\Loader::includeModule('democontent2.pi')) {
            $favourites = new \Democontent2\Pi\Favourites(intval($USER->GetID()));
            $favourites->setExecutorId(intval($request->getPost('id')));
            $favourites->change();
        }
    }
}

echo \Bitrix\Main\Web\Json::encode(
    [
        'error' => $error
    ]
);