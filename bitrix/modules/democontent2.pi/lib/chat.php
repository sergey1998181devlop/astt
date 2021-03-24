<?php
/**
 * Date: 13.08.2019
 * Time: 09:02
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

namespace Democontent2\Pi;

use MongoDB\BSON\ObjectId;
use MongoDB\Client;

class Chat
{
    const COLLECTION_UNREAD_COUNTERS = 'unread_counters';
    const COLLECTION_ROOMS = 'rooms';
    const COLLECTION_MESSAGES = 'messages';
    private $client = null;
    private $roomId = null;
    private $userHash = '';

    /**
     * Chat constructor.
     */
    public function __construct()
    {
        $this->client = new Client(Utils::__mongoDbConnectString());
    }

    /**
     * @param string $userHash
     */
    public function setUserHash(string $userHash): void
    {
        $this->userHash = $userHash;
    }

    /**
     * @param string $roomId
     */
    public function setRoomId(string $roomId): void
    {
        if (strlen(trim($roomId)) == 24) {
            $this->roomId = new ObjectId(trim($roomId));
        }
    }

    public function getNotifications()
    {
        $result = [];
        $db = $this->client->selectDatabase(Utils::mid())->selectCollection(static::COLLECTION_UNREAD_COUNTERS);
        $find = $db->find(
            [
                '$and' => [
                    [
                        'userHash' => [
                            '$exists' => true
                        ]
                    ],
                    [
                        'sendTime' => [
                            '$lte' => time(),
                            '$exists' => true
                        ]
                    ],
                    [
                        'sent' => [
                            '$exists' => true,
                            '$eq' => 0
                        ]
                    ]
                ]
            ]
        );

        foreach ($find as $item) {
            $result[] = $item;
        }

        return $result;
    }

    public function sent(ObjectId $id)
    {
        $db = $this->client->selectDatabase(Utils::mid())->selectCollection(static::COLLECTION_UNREAD_COUNTERS);
        $db->updateMany(['_id' => $id], ['$set' => ['sent' => 1]]);
    }

    public function sendNotification($room)
    {
        if (!is_null($this->client) && strlen($this->userHash) == 32 && $this->roomId !== null) {
            $db = $this->client->selectDatabase(Utils::mid())->selectCollection(static::COLLECTION_UNREAD_COUNTERS);
            $id = null;
            $find = $db->find(
                [
                    'roomId' => (string)$this->roomId,
                    'userHash' => (($room->user1 == $this->userHash) ? $room->user2 : $room->user1)
                ]
            );
            foreach ($find as $item) {
                $id = $item->_id;
                break;
            }

            if (is_null($id)) {
                $insert = $db->insertOne(
                    [
                        'roomId' => (string)$this->roomId,
                        'taskId' => intval($room->taskId),
                        'iBlockId' => intval($room->iBlockId),
                        'userHash' => (($room->user1 == $this->userHash) ? $room->user2 : $room->user1),
                        'sendTime' => (time() + 600),
                        'sent' => 0
                    ]
                );

                if ($insert->getInsertedId()) {
                    $db->createIndex(['roomId' => 1, 'userHash' => 1]);
                    $db->createIndex(['roomId' => -1, 'userHash' => -1]);
                    $db->createIndex(['sendTime' => 1]);
                    $db->createIndex(['sendTime' => -1]);
                    $db->createIndex(['taskId' => 1]);
                    $db->createIndex(['taskId' => -1]);
                    $db->createIndex(['iBlockId' => 1]);
                    $db->createIndex(['iBlockId' => -1]);
                    $db->createIndex(['userHash' => 1]);
                    $db->createIndex(['userHash' => -1]);
                    $db->createIndex(['sent' => 1]);
                    $db->createIndex(['sent' => -1]);
                }
            }
        }
    }

    public function getRoom(ObjectId $roomId)
    {
        $result = [];
        $db = $this->client->selectDatabase(Utils::mid())->selectCollection(static::COLLECTION_ROOMS);
        $find = $db->find(
            [
                '_id' => $roomId
            ]
        );
        foreach ($find as $item) {
            $result = $item;
            break;
        }

        return $result;
    }

    public function delete(ObjectId $roomId, string $userHash)
    {
        if (!is_null($this->client)) {
            $db = $this->client->selectDatabase(Utils::mid())->selectCollection(static::COLLECTION_UNREAD_COUNTERS);
            $db->deleteMany(
                [
                    'roomId' => (string)$roomId,
                    'userHash' => $userHash
                ]
            );
        }
    }

    public function deleteAll(int $taskId)
    {
        if (!is_null($this->client)) {
            $db = $this->client->selectDatabase(Utils::mid())->selectCollection(static::COLLECTION_UNREAD_COUNTERS);
            $db->deleteMany(['taskId' => intval($taskId)]);
        }
    }

    public function read(ObjectId $roomId, int $taskId, string $userHash)
    {
        if (!is_null($this->client)) {
            $db = $this->client->selectDatabase(Utils::mid())->selectCollection(static::COLLECTION_MESSAGES);
            $db->updateMany(
                [
                    'roomId' => (string)$roomId,
                    'taskId' => intval($taskId),
                    'status' => 0,
                    '$and' => [
                        [
                            'userId' => [
                                '$ne' => $userHash,
                                '$exists' => true
                            ]
                        ]
                    ]
                ],
                [
                    '$set' => [
                        'status' => 1
                    ]
                ]
            );
        }
    }

    public function unread()
    {
        $result = [
            'total' => 0,
            'rooms' => null
        ];

        if (!is_null($this->client)) {
            $db = $this->client->selectDatabase(Utils::mid())->selectCollection(static::COLLECTION_ROOMS);
            $rooms = $db->find(
                [
                    'taskId' => [
                        '$gt' => 0,
                        '$exists' => true
                    ],
                    '$or' => [
                        [
                            'user1' => $this->userHash
                        ],
                        [
                            'user2' => $this->userHash
                        ]
                    ]
                ]
            );
            foreach ($rooms as $room) {
                $room['unread'] = 0;
                $db = $this->client->selectDatabase(Utils::mid())->selectCollection(static::COLLECTION_MESSAGES);
                $unreadCounter = $db->aggregate(
                    [
                        [
                            '$match' => [
                                'roomId' => (string)$room->_id,
                                'userId' => (($room->user1 == $this->userHash) ? $room->user2 : $room->user1),
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
                    $result['total'] += intval($unread->count);
                    $result['rooms'][(string)$room->_id] = intval($unread->count);
                }
            }
        }

        return $result;
    }
}
