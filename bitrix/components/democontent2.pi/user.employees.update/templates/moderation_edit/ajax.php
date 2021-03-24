<?php
/**
 * User: Nazarkin Sergey
 * Email: nazarkin2017@mail.ru
 * Date: 07.10.2020
 * Time: 13:07
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$error = 1;

global $USER;

if ($request->isAjaxRequest() && $request->isPost() && $USER->IsAuthorized()) {
    if (intval($request->getPost('iBlockId')) && intval($request->getPost('taskId')) && intval($request->getPost('offerId'))) {
        if (\Bitrix\Main\Loader::includeModule('democontent2.pi')) {
            $item = new \Democontent2\Pi\Iblock\Item();
            $item->setIBlockId(intval($request->getPost('iBlockId')));
            $item->setItemId(intval($request->getPost('taskId')));
            $item->setUserId(intval($USER->GetID()));

            if ($item->checkOwner()) {
                $currentStatus = $item->getCurrentStatus();

                if ($currentStatus == 1 || $currentStatus == 4) {
                    $response = new \Democontent2\Pi\Iblock\Response();
                    $response->setUserId(intval($USER->GetID()));
                    $response->setTaskId(intval($request->getPost('taskId')));
                    $response->setIBlockId(intval($request->getPost('iBlockId')));

                    switch ($request->getPost('action')) {
                        case 'setDenied':
                            $response->setDenied(intval($request->getPost('offerId')));
                            break;
                        case 'unSetDenied':
                            $response->unSetDenied(intval($request->getPost('offerId')));
                            break;
                        case 'setCandidate':
                            $response->setCandidate(intval($request->getPost('offerId')));
                            break;
                        case 'unSetCandidate':
                            $response->unSetCandidate(intval($request->getPost('offerId')));
                            break;
                        case 'setExecutor':
                            $response->setExecutor(intval($request->getPost('offerId')));
                            break;
                        case 'block':
                            $response->block(intval($request->getPost('offerId')));
                            break;
                        default:
                            $response->setError(1);
                    }

                    $error = $response->getError();
                }
            }
        }
    }
}

echo \Bitrix\Main\Web\Json::encode(
    [
        'error' => $error
    ]
);