<?php
/**
 * Date: 16.07.2019
 * Time: 19:06
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
use Democontent2\Pi\Utils;

class ChatRooms extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * ChatRooms constructor.
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
        if ($this->checkKey($request)) {
            if ($this->getId()) {
                try {
                    $login = Option::get(DSPI, 'mongoAdminLogin');
                    $password = Option::get(DSPI, 'mongoAdminPassword');

                    if ($login && $password) {
                        $item = new \Democontent2\Pi\Iblock\Item();
                        $client = new \MongoDB\Client(Utils::__mongoDbConnectString());
                        $db = $client->selectDatabase(Utils::mid())->selectCollection('rooms');

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
                                                'user1' => Utils::getChatId($this->getId())
                                            ],
                                            [
                                                'user2' => Utils::getChatId($this->getId())
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        );
                        foreach ($rooms as $room) {
                            $item->setItemId(intval($room->taskId));
                            $item->setIBlockId(intval($room->iBlockId));

                            $this->result[intval($room->taskId)] = [
                                'id' => (string)$room->_id,
                                'name' => $item->getNameAndCode(),
                                'taskId' => intval($room->taskId),
                                'unread' => intval($db->countDocuments(
                                    [
                                        'roomId' => (string)$room->_id,
                                        'userId' => ($room->user1 == Utils::getChatId($this->getId())) ? $room->user2 : $room->user1,
                                        'status' => 0
                                    ]))
                            ];
                        }

                        if (count($this->result)) {
                            $this->errorCode = 0;
                        } else {
                            $this->errorCode = 0;
                            $this->errorMessage = 'No Results';
                        }

                        unset($item, $client, $db, $rooms);
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
    }
}
