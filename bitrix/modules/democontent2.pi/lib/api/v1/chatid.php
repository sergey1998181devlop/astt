<?php
/**
 * Date: 16.07.2019
 * Time: 18:55
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

namespace Democontent2\Pi\Api\V1;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\HttpRequest;
use Democontent2\Pi\Chat;
use Democontent2\Pi\Utils;
use MongoDB\BSON\ObjectId;
use MongoDB\Client;

class ChatId extends \Democontent2\Pi\User implements IApi
{

    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * ChatId constructor.
     */
    public function __construct()
    {
        parent::__construct(0);
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @param HttpRequest $request
     */
    public function run(HttpRequest $request)
    {
        if ($request->get('id') && strlen($request->get('id')) == 24 && $request->get('taskId') && intval($request->get('taskId'))) {
            if ($this->checkKey($request)) {
                if ($this->getId()) {
                    try {
                        $find = false;

                        if (intval(Option::get(DSPI, 'chatEnabled'))) {
                            $client = new Client(Utils::__mongoDbConnectString());
                            $db = $client->selectDatabase(Utils::mid())->selectCollection('rooms');
                            $rooms = $db->find(
                                [
                                    '_id' => new ObjectId($request->get('id')),
                                    '$and' => [
                                        [
                                            'taskId' => intval($request->get('taskId'))
                                        ],
                                        [
                                            '$or' => [
                                                [
                                                    'user1' => Utils::getChatId($this->getId())
                                                ],
                                                [
                                                    'user2' => Utils::getChatId($this->getId())
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
                                $find = true;
                                $db = $client->selectDatabase(Utils::mid())->selectCollection('messages');
                                $messages = $db->find(
                                    [
                                        'taskId' => intval($request->get('taskId'))
                                    ],
                                    [
                                        'sort' => [
                                            'time' => 1
                                        ]
                                    ]
                                );

                                $i = 0;
                                foreach ($messages as $message) {
                                    $this->result[$i] = [
                                        'id' => (string)$message->_id,
                                        'type' => $message->type,
                                        'status' => $message->status,
                                        'taskId' => $message->taskId,
                                        'iBlockId' => $message->iBlockId,
                                        'message' => $message->message,
                                        'from' => $message->userId,
                                        'to' => $message->to,
                                        'time' => $message->time,
                                        'date' => date('d.m.Y H:i', ($message->time / 1000)),
                                        'file' => ($message->type == 'file') ? $message->file : null
                                    ];

                                    $i++;

//                                    if ((string)$room->_id == '5d73e5a9a4e8547ee009c8c4' && intval($message->taskId) == 211) {
//                                        $db->deleteOne(['_id' => $message->_id]);
//                                    }
                                }

                                $chat = new Chat();
                                $chat->read($room->_id, intval($request->get('taskId')), Utils::getChatId($this->getId()));
                                $chat->delete($room->_id, Utils::getChatId($this->getId()));
                            }

                            if ($find) {
                                $this->errorCode = 0;
                            } else {
                                $this->errorMessage = 'Chat Not Found';
                            }
                        } else {
                            $this->errorMessage = 'Service is unavailable';
                        }
                    } catch (ArgumentNullException $e) {
                    } catch (ArgumentOutOfRangeException $e) {
                        $this->errorMessage = 'Unknown Error';
                    }
                } else {
                    $this->errorMessage = 'User Not Found';
                }
            } else {
                $this->errorMessage = 'Invalid Key';
            }
        } else {
            $this->errorMessage = 'Invalid Id';
        }
    }
}
