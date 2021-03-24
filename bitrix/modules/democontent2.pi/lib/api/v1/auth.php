<?php
/**
 * Date: 13.07.2019
 * Time: 17:24
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

use Bitrix\Main\HttpRequest;
use Bitrix\Main\UserTable;
use Democontent2\Pi\EventManager;
use Democontent2\Pi\Logger;
use Democontent2\Pi\User;

class Auth extends User implements IApi
{
    private $password = '';
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * Auth constructor.
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        parent::__construct(intval($id));
    }

    /**
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
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
     * @param HttpRequest $request
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function run(HttpRequest $request)
    {
        $newKey = '';
        if ($request->getHeaders()->get('x-pi-email') && $request->getHeaders()->get('x-pi-password')) {
            if (filter_var($request->getHeaders()->get('x-pi-email'), FILTER_VALIDATE_EMAIL)) {
                $this->email = $request->getHeaders()->get('x-pi-email');
                $this->password = $request->getHeaders()->get('x-pi-password');

                $userTable = new UserTable();
                $get = $userTable::getList(
                    [
                        'select' => [
                            'ID',
                            'PASSWORD',
                            'UF_DSPI_API_KEY'
                        ],
                        'filter' => [
                            '=EMAIL' => $this->email
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $this->id = intval($res['ID']);
                    $salt = substr($res['PASSWORD'], 0, (strlen($res['PASSWORD']) - 32));
                    $realPassword = substr($res['PASSWORD'], -32);
                    $password_ = md5($salt . $this->password);

                    if ($realPassword == $password_) {
                        $apiKeyCreated = 0;

                        if (strlen($res['UF_DSPI_API_KEY']) == 32) {
                            $newKey = md5($this->id . md5($res['UF_DSPI_API_KEY']) . randString(rand(3, 10)));

                            $newKey = $res['UF_DSPI_API_KEY'];

//                            $us = new \CUser();
//                            $us->Update(
//                                $this->id,
//                                [
//                                    'UF_DSPI_API_KEY' => $newKey
//                                ]
//                            );
//
//                            $eventManager = new EventManager(
//                                'apiKeyUpdated',
//                                [
//                                    'userId' => $this->id
//                                ]
//                            );
//                            $eventManager->execute();
                        } else {
                            $apiKeyCreated = 1;
                            $newKey = md5(microtime() . $this->id . randString(rand(3, 10)));

                            $us = new \CUser();
                            $us->Update(
                                $this->id,
                                [
                                    'UF_DSPI_API_KEY' => $newKey
                                ]
                            );

                            $eventManager = new EventManager(
                                'apiKeyCreated',
                                [
                                    'userId' => $this->id
                                ]
                            );
                            $eventManager->execute();
                        }

                        $this->errorCode = 0;
                        $this->result = [
                            'id' => intval($res['ID']),
                            'key' => $newKey
                        ];

                        Logger::add(
                            $this->id,
                            'apiAuth',
                            [
                                'apiKeyCreated' => $apiKeyCreated
                            ]
                        );
                    } else {
                        $this->errorMessage = 'Wrong Password';
                    }
                }
            } else {
                $this->errorMessage = 'Invalid Credentials';
            }
        } else {
            $this->errorMessage = 'Invalid Credentials';
        }

        if (strlen($newKey) == 32) {
            header('X-PI-KEY: ' . $newKey);
        }
    }
}