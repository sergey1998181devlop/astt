<?php
/**
 * Date: 27.09.2019
 * Time: 07:08
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
    try {
        if (\Bitrix\Main\Loader::includeModule('democontent2.pi')) {
            if ($request->getPost('token')) {
                $firebase = new \Democontent2\Pi\FireBase($USER->GetID());
                $firebase->checkToken($request->getPost('token'), true);
                $error = 0;
            }
        }
    } catch (\Bitrix\Main\ArgumentNullException $e) {
    } catch (\Bitrix\Main\ArgumentOutOfRangeException $e) {
    } catch (\Bitrix\Main\IO\InvalidPathException $e) {
    } catch (\Bitrix\Main\LoaderException $e) {
    } catch (\Kreait\Firebase\Exception\ApiException $e) {
    }
}

echo \Bitrix\Main\Web\Json::encode(
    [
        'error' => $error,
        'result' => $result
    ]
);
