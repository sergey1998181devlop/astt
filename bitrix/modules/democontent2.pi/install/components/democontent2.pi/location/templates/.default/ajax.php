<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 31.10.2018
 * Time: 13:33
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$error = 1;
$result = [];

if ($request->isAjaxRequest() && $request->isPost()) {
    if (\Bitrix\Main\Loader::includeModule('democontent2.pi')) {
        if ($request->getPost('regionId') && intval($request->getPost('regionId')) > 0) {
            $city = new \Democontent2\Pi\Iblock\City();
            $result = $city->getList(0, intval($request->getPost('regionId')));

            if (count($result) > 0) {
                $error = 0;
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