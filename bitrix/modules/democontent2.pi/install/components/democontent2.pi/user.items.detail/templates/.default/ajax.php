<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 12.01.2019
 * Time: 17:25
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$error = 1;
$result = array();

global $USER;

if ($USER->IsAuthorized() && $request->isAjaxRequest() && $request->isPost()) {
    try {
        if (\Bitrix\Main\Loader::includeModule('democontent2.pi')) {
            if ($request->getPost('type') && intval($request->getPost('iblock')) > 0) {
                switch ($request->getPost('type')) {
                    case 'properties':
                        $properties = new \Democontent2\Pi\Iblock\Properties(intval($request->getPost('iblock')));
                        try {
                            $result = $properties->all();
                            if (count($result) > 0) {
                                $error = 0;
                            }
                        } catch (\Bitrix\Main\SystemException $e) {
                        }
                        break;
                }
            }
        }
    } catch (\Bitrix\Main\LoaderException $e) {
    }
}

echo \Bitrix\Main\Web\Json::encode(
    array(
        'error' => $error,
        'result' => $result
    )
);