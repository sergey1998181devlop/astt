<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.03.2019
 * Time: 16:54
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class TaskChatComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        if (!$USER->IsAuthorized()) {
            return;
        } else {
            $this->arResult['USER'] = [];
            $this->arResult['ITEMS'] = [];
            $this->arResult['ROOM_ID'] = '';
            $this->arResult['UNREAD_MESSAGES'] = 0;

            $us = new \Democontent2\Pi\User($USER->GetID());
            $this->arResult['USER'] = $us->get();

            $login = \Bitrix\Main\Config\Option::get(DSPI, 'mongoAdminLogin');
            $password = \Bitrix\Main\Config\Option::get(DSPI, 'mongoAdminPassword');

            if ($login && $password) {
                $uidHash = \Democontent2\Pi\Utils::getChatId($USER->GetID());
                $roomParams = [];
                $client = new \MongoDB\Client(\Democontent2\Pi\Utils::__mongoDbConnectString());
                $db = $client->selectDatabase(\Democontent2\Pi\Utils::mid())->selectCollection('rooms');

                $rooms = $db->find(
                    [
                        'taskId' => intval($this->arParams['taskId']),
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
                    $db = $client->selectDatabase(\Democontent2\Pi\Utils::mid())->selectCollection('messages');
                    if ($room->user1 == $uidHash || $room->user2 == $uidHash) {
                        $this->arResult['ROOM_ID'] = (string)$room->_id;
                        $roomParams = [
                            'user1' => $room->user1,
                            'user2' => $room->user2
                        ];
                        $messages = $db->find(
                            [
                                'roomId' => $this->arResult['ROOM_ID']
                            ],
                            [
                                'sort' => [
                                    'time' => 1
                                ]
                            ]
                        );
                        foreach ($messages as $message) {
                            $this->arResult['ITEMS'][] = $message;
                        }
                    }
                }

                if (!strlen($this->arResult['ROOM_ID'])) {
                    $insert = $db->insertOne(
                        [
                            'taskId' => intval($this->arParams['taskId']),
                            'iBlockId' => intval($this->arParams['iBlockId']),
                            'time' => (time() * 1000),
                            'user1' => \Democontent2\Pi\Utils::getChatId($this->arParams['ownerId']),
                            'user2' => \Democontent2\Pi\Utils::getChatId($this->arParams['executorId']),
                            'creatorIp' => \Bitrix\Main\Application::getInstance()->getContext()->getServer()->get('REMOTE_ADDR')
                        ]
                    );
                    if ($insert->getInsertedId()) {
                        $this->arResult['ROOM_ID'] = (string)$insert->getInsertedId();
                    }
                }

                if (count($this->arResult['ITEMS']) > 0 && strlen($this->arResult['ROOM_ID'])) {
                    if (count($roomParams) > 0) {
                        $this->arResult['UNREAD_MESSAGES'] = intval($db->countDocuments(
                            [
                                'roomId' => $this->arResult['ROOM_ID'],
                                'userId' => (($roomParams['user1'] == $uidHash) ? $roomParams['user2'] : $roomParams['user1']),
                                'status' => 0
                            ])
                        );
                    }
                }
            } else {
                //return;
            }

            $this->includeComponentTemplate();
        }
    }
}