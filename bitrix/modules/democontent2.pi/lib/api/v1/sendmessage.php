<?php
/**
 * Date: 30.07.2019
 * Time: 17:50
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

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Type\ParameterDictionary;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\Chat;
use Democontent2\Pi\Utils;
use MongoDB\BSON\ObjectId;
use MongoDB\Client;

class SendMessage extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * SendMessage constructor.
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
     * @throws \Bitrix\Main\SystemException
     */
    public function run(HttpRequest $request)
    {
        $params = $request->getPostList()->toArray();
        if (!count($params)) {
            try {
                $params = Json::decode(file_get_contents('php://input'));
            } catch (ArgumentException $e) {
            }
        }

        $dict = new ParameterDictionary($params);

        if ($dict->get('id') && strlen($dict->get('id')) == 24
            && $dict->get('taskId') && intval($dict->get('taskId'))) {
            if ($this->checkKey($request)) {
                if ($this->getId()) {
                    if (strlen(HTMLToTxt(str_replace("\n", '<br>', strip_tags($dict->get('text')))))) {
                        try {
                            if (intval(Option::get(DSPI, 'chatEnabled'))) {
                                $id = null;
                                $find = false;
                                $uidHash = Utils::getChatId($this->getId());
                                $client = new Client(Utils::__mongoDbConnectString());
                                $db = $client->selectDatabase(Utils::mid())->selectCollection('rooms');
                                $rooms = $db->find(
                                    [
                                        '_id' => new ObjectId($dict->get('id')),
                                        '$and' => [
                                            [
                                                'taskId' => intval($dict->get('taskId'))
                                            ],
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
                                    $find = true;
                                    $roomId = (string)$room->_id;
                                    $time = (time() * 1000);

                                    $userParams = $this->get();

                                    $db = $client->selectDatabase(Utils::mid())->selectCollection('messages');
                                    $add = $db->insertOne(
                                        [
                                            'roomId' => $roomId,
                                            'taskId' => $room->taskId,
                                            'iBlockId' => $room->iBlockId,
                                            'type' => 'text',
                                            'time' => $time,
                                            'status' => 0,
                                            'userId' => $uidHash,
                                            'to' => ($room->user1 == $uidHash) ? $room->user2 : $room->user1,
                                            'userRealId' => $this->getId(),
                                            'userName' => $userParams['NAME'],
                                            'userEmail' => $userParams['EMAIL'],
                                            'message' => HTMLToTxt(str_replace("\n", '<br>', strip_tags($dict->get('text')))),
                                            'ip' => Application::getInstance()->getContext()->getServer()->get('REMOTE_ADDR')
                                        ]
                                    );
                                    if ($add->getInsertedId()) {
                                        $this->result = [
                                            'id' => (string)$add->getInsertedId(),
                                            'roomId' => $roomId,
                                            'taskId' => $room->taskId,
                                            'iBlockId' => $room->iBlockId,
                                            'message' => HTMLToTxt(str_replace("\n", '<br>', strip_tags($dict->get('text')))),
                                            'userId' => $uidHash,
                                            'from' => $uidHash,
                                            'to' => ($room->user1 == $uidHash) ? $room->user2 : $room->user1,
                                            'userName' => $userParams['NAME'],
                                            'time' => $time,
                                            'date' => date('d.m.Y H:i', ($time / 1000)),
                                            'type' => 'text',
                                            'status' => 0,
                                            'file' => null
                                        ];

                                        $chat = new Chat();
                                        $chat->setUserHash($uidHash);
                                        $chat->setRoomId($room->_id);
                                        $chat->sendNotification($room);
                                    }
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
                            $this->errorMessage = 'Service is unavailable';
                        }
                    } else {
                        $this->errorMessage = 'Empty Message';
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
