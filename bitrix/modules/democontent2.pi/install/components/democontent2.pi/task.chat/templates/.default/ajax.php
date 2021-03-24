<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.03.2019
 * Time: 17:39
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$error = 1;
$result = [];

global $USER;

if ($request->isAjaxRequest() && $request->isPost() && $USER->IsAuthorized()) {
    if (\Bitrix\Main\Loader::includeModule('democontent2.pi')) {
        $uidHash = \Democontent2\Pi\Utils::getChatId($USER->GetID());
        $login = \Bitrix\Main\Config\Option::get(DSPI, 'mongoAdminLogin');
        $password = \Bitrix\Main\Config\Option::get(DSPI, 'mongoAdminPassword');

        if ($login && $password) {
            switch ($request->getPost('type')) {
                case 'message':
                    if ($request->getPost('text') && $request->getPost('taskId') && $request->getPost('iBlockId')) {
                        if (intval($request->getPost('taskId')) && intval($request->getPost('iBlockId'))) {
                            $client = new \MongoDB\Client(\Democontent2\Pi\Utils::__mongoDbConnectString());
                            $db = $client->selectDatabase(\Democontent2\Pi\Utils::mid())->selectCollection('rooms');
                            $rooms = $db->find(
                                [
                                    'taskId' => intval($request->getPost('taskId')),
                                    'iBlockId' => intval($request->getPost('iBlockId')),
                                    '$and' => [
                                        [
                                            '$or' => [
                                                [
                                                    'user1' => $uidHash
                                                ],
                                                [
                                                    'user2' => $uidHash
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'limit' => 1
                                ]
                            );
                            foreach ($rooms as $room) {
                                $roomId = (string)$room->_id;
                                $time = (time() * 1000);

                                $us = new \Democontent2\Pi\User($USER->GetID());
                                $userParams = $us->get();

                                $db = $client->selectDatabase(\Democontent2\Pi\Utils::mid())->selectCollection('messages');
                                $add = $db->insertOne(
                                    [
                                        'roomId' => $roomId,
                                        'taskId' => intval($request->getPost('taskId')),
                                        'iBlockId' => intval($request->getPost('iBlockId')),
                                        'type' => 'text',
                                        'time' => $time,
                                        'status' => 0,
                                        'userId' => $uidHash,
                                        'to' => ($room->user1 == $uidHash) ? $room->user2 : $room->user1,
                                        'userRealId' => $us->getId(),
                                        'userName' => $userParams['NAME'],
                                        'userEmail' => $userParams['EMAIL'],
                                        'message' => HTMLToTxt(str_replace("\n", '<br>', strip_tags($request->getPost('text')))),
                                        'ip' => \Bitrix\Main\Application::getInstance()->getContext()->getServer()->get('REMOTE_ADDR')
                                    ]
                                );
                                if ($add->getInsertedId()) {
                                    $error = 0;
                                    $result = [
                                        'id' => (string)$add->getInsertedId(),
                                        'roomId' => $roomId,
                                        'taskId' => intval($request->getPost('taskId')),
                                        'iBlockId' => intval($request->getPost('iBlockId')),
                                        'message' => HTMLToTxt(str_replace("\n", '<br>', strip_tags($request->getPost('text')))),
                                        'from' => $uidHash,
                                        'userId' => $uidHash,
                                        'to' => ($room->user1 == $uidHash) ? $room->user2 : $room->user1,
                                        'userName' => $userParams['NAME'],
                                        'time' => $time,
                                        'date' => date('Y-m-d H:i:s', ($time / 1000)),
                                        'type' => 'text'
                                    ];

                                    $chat = new \Democontent2\Pi\Chat();
                                    $chat->setUserHash($uidHash);
                                    $chat->setRoomId($room->_id);
                                    $chat->sendNotification($room);
                                }

                                unset($us, $userParams);
                            }
                        }
                    }
                    break;
                case 'file':
                    if ($request->getPost('taskId') && $request->getPost('iBlockId') && isset($_FILES['__file'])) {
                        if (intval($request->getPost('taskId')) && intval($request->getPost('iBlockId'))) {
                            $client = new \MongoDB\Client(\Democontent2\Pi\Utils::__mongoDbConnectString());
                            $db = $client->selectDatabase(\Democontent2\Pi\Utils::mid())->selectCollection('rooms');
                            $rooms = $db->find(
                                [
                                    'taskId' => intval($request->getPost('taskId')),
                                    'iBlockId' => intval($request->getPost('iBlockId')),
                                    '$and' => [
                                        [
                                            '$or' => [
                                                [
                                                    'user1' => $uidHash
                                                ],
                                                [
                                                    'user2' => $uidHash
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'limit' => 1
                                ]
                            );
                            foreach ($rooms as $room) {
                                $roomId = (string)$room->_id;
                                $time = (time() * 1000);

                                $extension = \Democontent2\Pi\Utils::getExtension($_FILES['__file']['name']);
                                if (strlen($extension) > 0) {
                                    $fileArray = CFile::MakeFileArray($_FILES['__file']['tmp_name']);
                                    $fileArray['MODULE_ID'] = DSPI;
                                    $fileArray['description'] = $_FILES['__file']['name'];
                                    $fileArray['name'] = md5(microtime(true)) . ToLower(randString(rand(1, 10))) . $extension;

                                    $fileId = CFile::SaveFile($fileArray, DSPI);

                                    if ($fileId) {
                                        $us = new \Democontent2\Pi\User($USER->GetID());
                                        $userParams = $us->get();

                                        $db = $client->selectDatabase(\Democontent2\Pi\Utils::mid())->selectCollection('messages');
                                        $add = $db->insertOne(
                                            [
                                                'roomId' => $roomId,
                                                'taskId' => intval($request->getPost('taskId')),
                                                'iBlockId' => intval($request->getPost('iBlockId')),
                                                'type' => 'file',
                                                'time' => $time,
                                                'status' => 0,
                                                'userId' => $uidHash,
                                                'from' => $uidHash,
                                                'to' => ($room->user1 == $uidHash) ? $room->user2 : $room->user1,
                                                'userRealId' => $us->getId(),
                                                'userName' => $userParams['NAME'],
                                                'userEmail' => $userParams['EMAIL'],
                                                'message' => '',
                                                'ip' => \Bitrix\Main\Application::getInstance()->getContext()->getServer()->get('REMOTE_ADDR'),
                                                'file' => [
                                                    'id' => intval($fileId),
                                                    'size' => intval($fileArray['size']),
                                                    'name' => $fileArray['name'],
                                                    'description' => $fileArray['description'],
                                                    'type' => $fileArray['type'],
                                                    'path' => CFile::GetPath($fileId)
                                                ]
                                            ]
                                        );
                                        if ($add->getInsertedId()) {
                                            $error = 0;
                                            $result = [
                                                'id' => (string)$add->getInsertedId(),
                                                'roomId' => $roomId,
                                                'taskId' => intval($request->getPost('taskId')),
                                                'iBlockId' => intval($request->getPost('iBlockId')),
                                                'message' => strip_tags(HTMLToTxt($request->getPost('text'))),
                                                'userId' => $uidHash,
                                                'to' => ($room->user1 == $uidHash) ? $room->user2 : $room->user1,
                                                'userName' => $userParams['NAME'],
                                                'time' => $time,
                                                'date' => date('Y-m-d H:i:s', ($time / 1000)),
                                                'type' => 'file',
                                                'file' => [
                                                    'id' => intval($fileId),
                                                    'size' => intval($fileArray['size']),
                                                    'name' => $fileArray['name'],
                                                    'description' => $fileArray['description'],
                                                    'type' => $fileArray['type'],
                                                    'path' => ((\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->isHttps()) ? 'https://' : 'http://')
                                                        . \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getHttpHost()
                                                        . CFile::GetPath($fileId)
                                                ]
                                            ];

                                            $chat = new \Democontent2\Pi\Chat();
                                            $chat->setUserHash($uidHash);
                                            $chat->setRoomId($room->_id);
                                            $chat->sendNotification($room);
                                        }

                                        unset($us, $userParams);
                                    }
                                }
                            }
                        }
                    }
                    break;
                case 'read':
                    if ($request->getPost('taskId')) {
                        if (intval($request->getPost('taskId'))) {
                            $client = new \MongoDB\Client(\Democontent2\Pi\Utils::__mongoDbConnectString());
                            $db = $client->selectDatabase(\Democontent2\Pi\Utils::mid())->selectCollection('rooms');
                            $rooms = $db->find(
                                [
                                    'taskId' => intval($request->getPost('taskId')),
                                    '$and' => [
                                        [
                                            '$or' => [
                                                [
                                                    'user1' => $uidHash
                                                ],
                                                [
                                                    'user2' => $uidHash
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'limit' => 1
                                ]
                            );
                            foreach ($rooms as $room) {
//                                $db = $client->selectDatabase(\Democontent2\Pi\Utils::mid())->selectCollection('messages');
//                                $db->updateMany(
//                                    [
//                                        'roomId' => (string)$room->_id,
//                                        'taskId' => intval($request->getPost('taskId')),
//                                        'status' => 0,
//                                        '$and' => [
//                                            [
//                                                'userId' => [
//                                                    '$ne' => $uidHash,
//                                                    '$exists' => true
//                                                ]
//                                            ]
//                                        ]
//                                    ],
//                                    [
//                                        '$set' => [
//                                            'status' => 1
//                                        ]
//                                    ]
//                                );

                                $error = 0;

                                $chat = new \Democontent2\Pi\Chat();
                                $chat->read($room->_id, intval($request->getPost('taskId')), $uidHash);
                                $chat->delete($room->_id, $uidHash);
                            }
                        }
                    }
                    break;
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