<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 25.09.2018
 * Time: 10:34
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class UserChatClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;

        if (!$USER->IsAuthorized() || !intval(\Bitrix\Main\Config\Option::get(DSPI, 'chatEnabled'))) {
            LocalRedirect(SITE_DIR, true);
        }

        $us = new \Democontent2\Pi\User(intval($USER->GetID()));
        $this->arResult['USER'] = $us->get();
        $login = \Bitrix\Main\Config\Option::get(DSPI, 'mongoAdminLogin');
        $password = \Bitrix\Main\Config\Option::get(DSPI, 'mongoAdminPassword');

        if ($login && $password) {
            $client = new \MongoDB\Client(\Democontent2\Pi\Utils::__mongoDbConnectString());
            $db = $client->selectDatabase(\Democontent2\Pi\Utils::mid())->selectCollection('rooms');

            $rooms = $db->find(
                [
                    '$and' => [
                        [
                            'taskId' => [
                                '$gt' => 0,
                                '$exists' => true
                            ]
                        ],
                        [
                            '$or' => [
                                [
                                    'user1' => \Democontent2\Pi\Utils::getChatId($USER->GetID())
                                ],
                                [
                                    'user2' => \Democontent2\Pi\Utils::getChatId($USER->GetID())
                                ]
                            ]
                        ]
                    ]
                ]
            );
            foreach ($rooms as $room) {
                $roomId = (string)$room->_id;
                $roomParams = [
                    'user1' => $room->user1,
                    'user2' => $room->user2
                ];

                $this->arResult['CHATS'][$room->taskId] = [
                    'roomId' => $roomId,
                    'iBlockId' => $room->iBlockId,
                ];

                $db = $client->selectDatabase(\Democontent2\Pi\Utils::mid())->selectCollection('messages');

                $this->arResult['CHATS'][$room->taskId]['unreadMessages'] = intval($db->countDocuments(
                    [
                        'roomId' => $roomId,
                        'userId' => (($roomParams['user1'] == \Democontent2\Pi\Utils::getChatId($USER->GetID())) ? $roomParams['user2'] : $roomParams['user1']),
                        'status' => 0
                    ])
                );

                $item = new \Democontent2\Pi\Iblock\Item();
                $item->setItemId(intval($room->taskId));
                $item->setIBlockId(intval($room->iBlockId));
                $this->arResult['CHATS'][$room->taskId]['params'] = $item->getNameAndCode();
            }
        }

        $this->includeComponentTemplate();
    }
}