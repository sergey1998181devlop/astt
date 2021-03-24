<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 07.12.2018
 * Time: 09:22
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$result = 0;

global $USER;

if ($request->isAjaxRequest() && $request->isPost() && $USER->IsAuthorized()) {
    if ($request->getPost('unreaded') && intval($request->getPost('unreaded')) > 0) {
        if (\Bitrix\Main\Loader::includeModule('democontent2.pi')) {
            $uidHash = \Democontent2\Pi\Utils::getChatId($USER->GetID());
            $login = \Bitrix\Main\Config\Option::get(DSPI, 'mongoAdminLogin');
            $password = \Bitrix\Main\Config\Option::get(DSPI, 'mongoAdminPassword');

            if ($login && $password) {
                $client = new \MongoDB\Client(\Democontent2\Pi\Utils::__mongoDbConnectString());
                $db = $client->selectDatabase(\Democontent2\Pi\Utils::mid())->selectCollection('rooms');
                $rooms = $db->find(
                    [
                        'taskId' => [
                            '$gt' => 0,
                            '$exists' => true
                        ],
                        '$or' => [
                            [
                                'user1' => $uidHash
                            ],
                            [
                                'user2' => $uidHash
                            ]
                        ]
                    ],
                    [
                        'sort' => [
                            'time' => -1
                        ]
                    ]
                );
                foreach ($rooms as $room) {
                    $room['unread'] = 0;
                    $db = $client->selectDatabase(\Democontent2\Pi\Utils::mid())->selectCollection('messages');
                    $unreadCounter = $db->aggregate(
                        [
                            [
                                '$match' => [
                                    'roomId' => (string)$room->_id,
                                    'userId' => (($room->user1 == $uidHash) ? $room->user2 : $room->user1),
                                    'status' => 0
                                ]
                            ],
                            [
                                '$group' => [
                                    '_id' => 1,
                                    'count' => [
                                        '$sum' => 1
                                    ]
                                ]
                            ]
                        ]
                    );
                    foreach ($unreadCounter as $unread) {
                        $result += intval($unread->count);
                    }
                }
            }
        }
    }
}

echo \Bitrix\Main\Web\Json::encode(
    [
        'result' => $result
    ]
);