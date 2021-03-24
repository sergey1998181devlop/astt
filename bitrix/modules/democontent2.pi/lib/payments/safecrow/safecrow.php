<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 21.01.2019
 * Time: 14:02
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Payments\SafeCrow;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\Hl;
use Democontent2\Pi\Logger;
use Democontent2\Pi\User;

class SafeCrow
{
    const ORDERS_TABLE_NAME = 'Democontentpisafecroworders';

    private $apiKey = '';
    private $apiSecret = '';
    private $server = '';
    private $prefix = '/api/v3';
    private $obj = null;
    protected $taskId = 0;
    protected $stageId = 0;
    protected $userId = 0;
    protected $consumerId = 0;
    protected $supplierId = 0;
    protected $price = 0;
    protected $extra = [];
    protected $description = '';
    protected $serviceCostPayer = '';
    protected $phone = '';
    protected $email = '';
    protected $name = '';

    /**
     * SafeCrow constructor.
     */
    public function __construct()
    {
        $this->server = 'https://' . Option::get(DSPI, 'safeCrowServer') . '.safecrow.ru/api/v3';
        $this->apiKey = Option::get(DSPI, 'safeCrowApiKey');
        $this->apiSecret = Option::get(DSPI, 'safeCrowApiSecret');

        $className = ToUpper(end(explode('\\', __CLASS__)));
        $hl = new Hl(static::ORDERS_TABLE_NAME);
        if ($hl->obj !== null) {
            $this->obj = $hl->obj;
        } else {
            $add = Hl::create(
                ToLower(static::ORDERS_TABLE_NAME),
                [
                    'UF_CREATED_AT' => [
                        'Y',
                        'datetime',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => ''],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')]
                        ]
                    ],
                    'UF_UPDATED_AT' => [
                        'Y',
                        'datetime',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => ''],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_UPDATED_AT')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_UPDATED_AT')]
                        ]
                    ],
                    'UF_TASK_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TASK_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TASK_ID')]
                        ]
                    ],
                    'UF_STAGE_ID' => [
                        'N',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_STAGE_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_STAGE_ID')]
                        ]
                    ],
                    'UF_ORDER_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ORDER_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ORDER_ID')]
                        ]
                    ],
                    'UF_PRICE' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PRICE')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PRICE')]
                        ]
                    ],
                    'UF_STATUS' => [
                        'Y',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => ''],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_STATUS')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_STATUS')]
                        ]
                    ],
                    'UF_DATA' => [
                        'Y',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => ''],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DATA')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DATA')]
                        ]
                    ],
                    'UF_PARAMS' => [
                        'Y',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => ''],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PARAMS')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PARAMS')]
                        ]
                    ]
                ],
                [
                    'ALTER TABLE `' . ToLower(static::ORDERS_TABLE_NAME) . '` MODIFY `UF_STATUS` VARCHAR(255);',
                    'ALTER TABLE `' . ToLower(static::ORDERS_TABLE_NAME) . '` MODIFY `UF_DATA` LONGTEXT;',
                    'ALTER TABLE `' . ToLower(static::ORDERS_TABLE_NAME) . '` MODIFY `UF_PARAMS` LONGTEXT;'
                ],
                [
                    ['UF_CREATED_AT'],
                    ['UF_UPDATED_AT'],
                    ['UF_TASK_ID'],
                    ['UF_STAGE_ID'],
                    ['UF_ORDER_ID'],
                    ['UF_PRICE'],
                    ['UF_STATUS'],
                    ['UF_TASK_ID', 'UF_STAGE_ID']
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );

            if ($add) {
                $this->__construct();
            }
        }
    }

    /**
     * @param int $taskId
     */
    public function setTaskId($taskId)
    {
        $this->taskId = intval($taskId);
    }

    /**
     * @param int $stageId
     */
    public function setStageId($stageId)
    {
        $this->stageId = intval($stageId);
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = intval($userId);
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param int $consumerId
     */
    public function setConsumerId($consumerId)
    {
        $this->consumerId = intval($consumerId);
    }

    /**
     * @param int $supplierId
     */
    public function setSupplierId($supplierId)
    {
        $this->supplierId = intval($supplierId);
    }

    /**
     * @param int $price
     */
    public function setPrice($price)
    {
        $this->price = intval($price);
    }

    /**
     * @param array $extra
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param string $serviceCostPayer
     */
    public function setServiceCostPayer($serviceCostPayer)
    {
        $this->serviceCostPayer = $serviceCostPayer;
    }

    public function getLocalOrders()
    {
        $result = [];

        if ($this->obj !== null && $this->taskId > 0) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => ['*'],
                        'filter' => [
                            '=UF_TASK_ID' => $this->taskId
                        ]
                    ]
                );
                while ($res = $get->fetch()) {
                    $result[] = $res;
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    public function setPaid($id)
    {
        $result = false;

        if ($this->obj !== null && intval($id) > 0) {
            try {
                $obj = $this->obj;
                $update = $obj::update(
                    intval($id),
                    [
                        'UF_STATUS' => 'paid'
                    ]
                );
                if ($update->isSuccess()) {
                    $result = true;
                    //TODO уведомления о внесении денег
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    public function addUser()
    {
        $returnId = 0;
        if ($this->userId > 0) {
            $params = [
                'email' => $this->email,
                'phone' => $this->phone,
                'name' => $this->name
            ];

            $endpoint = '/users';
            $data = $this->apiKey . 'POST' . $this->prefix . $endpoint . Json::encode($params);
            $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($params));
            curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            $response = curl_exec($ch) . "\n";
            curl_close($ch);

            if (strlen($response) > 0) {
                $response = Json::decode($response);
                if (isset($response['id']) && intval($response['id']) > 0) {
                    $returnId = intval($response['id']);

                    $us = new User($this->userId);
                    $us->setSafeCrowId(intval($response['id']));
                } else {
                    if (isset($response['errors'])) {
                        $params['userId'] = $this->userId;
                        $params['response'] = $response;
                        Logger::add($this->userId, 'safeCrowAddUserFailed', $params);
                    }
                }
            }
        }

        return $returnId;
    }

    public function getUsers()
    {
        $result = [];
        $endpoint = '/users';
        $data = $this->apiKey . 'GET' . $this->prefix . $endpoint;
        $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch) . "\n";
        curl_close($ch);

        if (strlen($response) > 0) {
            $result = Json::decode($response);
            if (isset($result['errors'])) {
                $params['response'] = $result;
                Logger::add($this->userId, 'safeCrowGetUsersFailed', $params);
            }
        }

        return $result;
    }

    public function getUser($id)
    {
        $result = [];
        $endpoint = '/users/' . $id;
        $data = $this->apiKey . 'GET' . $this->prefix . $endpoint;
        $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch) . "\n";
        curl_close($ch);

        if (strlen($response) > 0) {
            $response = Json::decode($response);
            if (isset($response['id']) && intval($response['id']) > 0) {
                $result = $response;
            } else {
                if (isset($response['errors'])) {
                    $params['userId'] = $this->userId;
                    $params['safeCrowId'] = intval($id);
                    $params['response'] = $response;
                    Logger::add($this->userId, 'safeCrowGetUserFailed', $params);
                }
            }
        }

        return $result;
    }

    public function updateUser($id)
    {
        $params = [
            'phone' => $this->phone,
            'name' => $this->name
        ];

        $endpoint = '/users/' . $id;
        $data = $this->apiKey . 'POST' . $this->prefix . $endpoint . Json::encode($params);
        $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($params));
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch) . "\n";
        curl_close($ch);

        if (strlen($response) > 0) {
            $response = Json::decode($response);
            if (isset($response['id']) && intval($response['id']) > 0) {
                $params['userId'] = $this->userId;
                $params['safeCrowId'] = intval($id);
                $params['response'] = $response;
                Logger::add($this->userId, 'safeCrowUpdateUserSuccess', $params);
            } else {
                if (isset($response['errors'])) {
                    $params['userId'] = $this->userId;
                    $params['safeCrowId'] = intval($id);
                    $params['response'] = $response;
                    Logger::add($this->userId, 'safeCrowUpdateUserFailed', $params);
                }
            }
        }
    }

    public function calculate($price)
    {
        $result = [];

        $params = [
            'price' => $price,
            'service_cost_payer' => '50/50'
        ];

        $endpoint = '/calculate';
        $data = $this->apiKey . 'POST' . $this->prefix . $endpoint . Json::encode($params);
        $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($params));
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch) . "\n";
        curl_close($ch);

        if (strlen($response) > 0) {
            $response = Json::decode($response);
            if (isset($response['id']) && intval($response['id']) > 0) {
                $result = $response;
            } else {
                if (isset($response['errors'])) {
                    $params['response'] = $response;
                    Logger::add($this->userId, 'safeCrowCalculateFailed', $params);
                }
            }
        }

        return $result;
    }

    public function createOrder()
    {
        $result = [];
        $params = [
            'consumer_id' => $this->consumerId,
            'supplier_id' => $this->supplierId,
            'price' => $this->price * 100,
            'description' => $this->description,
            'service_cost_payer' => $this->serviceCostPayer,
            'extra' => $this->extra
        ];

        $endpoint = '/orders';
        $data = $this->apiKey . 'POST' . $this->prefix . $endpoint . Json::encode($params);
        $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($params));
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch) . "\n";
        curl_close($ch);

        if (strlen($response) > 0) {
            $response = Json::decode($response);
            if (isset($response['id']) && intval($response['id']) > 0) {
                $result = $response;

                try {
                    if ($this->obj !== null) {
                        $obj = $this->obj;
                        $add = $obj::add(
                            [
                                'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                                'UF_UPDATED_AT' => DateTime::createFromTimestamp(time()),
                                'UF_TASK_ID' => $this->taskId,
                                'UF_STAGE_ID' => $this->stageId,
                                'UF_ORDER_ID' => intval($response['id']),
                                'UF_PRICE' => $this->price,
                                'UF_STATUS' => $response['status'],
                                'UF_DATA' => serialize($response),
                                'UF_PARAMS' => serialize($params)
                            ]
                        );

                        if ($add->isSuccess()) {

                        }
                    }
                } catch (\Exception $e) {

                }
            } else {
                if (isset($response['errors'])) {
                    $params['response'] = $response;
                    Logger::add($this->userId, 'safeCrowCreateOrderFailed', $params);
                }
            }
        }

        return $result;
    }

    public function getOrders()
    {
        $result = [];
        $endpoint = '/orders';
        $data = $this->apiKey . 'GET' . $this->prefix . $endpoint;
        $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch) . "\n";
        curl_close($ch);

        if (strlen($response) > 0) {
            $result = Json::decode($response);
            if (isset($result['errors'])) {
                $params['response'] = $result;
                Logger::add($this->userId, 'safeCrowGetOrdersFailed', $params);
            }
        }

        return $result;
    }

    public function getOrder($id)
    {
        $result = [];
        $endpoint = '/orders/' . $id;
        $data = $this->apiKey . 'GET' . $this->prefix . $endpoint;
        $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch) . "\n";
        curl_close($ch);

        if (strlen($response) > 0) {
            $response = Json::decode($response);
            if (isset($response['id']) && intval($response['id']) > 0) {
                $result = $response;
            } else {
                if (isset($response['errors'])) {
                    $params['response'] = $response;
                    Logger::add($this->userId, 'safeCrowGetOrderFailed', $params);
                }
            }
        }

        return $result;
    }

    public function getUserOrders($safeCrowId)
    {
        $result = [];
        $endpoint = '/users/' . $safeCrowId . '/orders';
        $data = $this->apiKey . 'GET' . $this->prefix . $endpoint;
        $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch) . "\n";
        curl_close($ch);

        if (strlen($response) > 0) {
            $result = Json::decode($response);
            if (isset($result['errors'])) {
                $params['response'] = $result;
                Logger::add($this->userId, 'safeCrowGetUserOrdersFailed', $params);
            }
        }

        return $result;
    }

    public function annulOrder($id, $reason)
    {
        $result = [];
        $params = [
            'reason' => $reason
        ];
        $endpoint = '/orders/' . $id . '/annul';
        $data = $this->apiKey . 'POST' . $this->prefix . $endpoint . Json::encode($params);
        $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($params));
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch) . "\n";
        curl_close($ch);

        if (strlen($response) > 0) {
            $result = Json::decode($response);
            if (isset($result['errors'])) {
                $params['id'] = $id;
                $params['response'] = $result;
                Logger::add($this->userId, 'safeCrowAnnulOrderFailed', $params);
            } else {
                if (isset($result['id']) && intval($result['id']) > 0) {
                    try {
                        if ($this->obj !== null) {
                            $obj = $this->obj;
                            $get = $obj::getList(
                                [
                                    'select' => ['ID', 'UF_DATA'],
                                    'filter' => [
                                        '=UF_ORDER_ID' => $id
                                    ],
                                    'limit' => 1
                                ]
                            );
                            while ($res = $get->fetch()) {
                                $data = unserialize($res['UF_DATA']);
                                $result['reason'] = $reason;
                                $data[] = $result;
                                $update = $obj::update(
                                    $res['ID'],
                                    [
                                        'UF_UPDATED_AT' => DateTime::createFromTimestamp(time()),
                                        'UF_STATUS' => $result['status'],
                                        'UF_DATA' => serialize($data)
                                    ]
                                );

                                if ($update->isSuccess()) {
                                    //TODO обработка успешного изменения статуса
                                }
                            }
                        }
                    } catch (\Exception $e) {

                    }
                }
            }
        }

        return $result;
    }

    public function cancelOrder($id, $reason)
    {
        $result = [];
        $params = [
            'reason' => $reason
        ];
        $endpoint = '/orders/' . $id . '/cancel';
        $data = $this->apiKey . 'POST' . $this->prefix . $endpoint . Json::encode($params);
        $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($params));
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch) . "\n";
        curl_close($ch);

        if (strlen($response) > 0) {
            $result = Json::decode($response);
            if (isset($result['errors'])) {
                $params['id'] = $id;
                $params['response'] = $result;
                Logger::add($this->userId, 'safeCrowCancelOrderFailed', $params);
            } else {
                if (isset($result['id']) && intval($result['id']) > 0) {
                    try {
                        if ($this->obj !== null) {
                            $obj = $this->obj;
                            $get = $obj::getList(
                                [
                                    'select' => ['ID', 'UF_DATA'],
                                    'filter' => [
                                        '=UF_ORDER_ID' => $id
                                    ],
                                    'limit' => 1
                                ]
                            );
                            while ($res = $get->fetch()) {
                                $data = unserialize($res['UF_DATA']);
                                $result['reason'] = $reason;
                                $data[] = $result;
                                $update = $obj::update(
                                    $res['ID'],
                                    [
                                        'UF_UPDATED_AT' => DateTime::createFromTimestamp(time()),
                                        'UF_STATUS' => $result['status'],
                                        'UF_DATA' => serialize($data)
                                    ]
                                );

                                if ($update->isSuccess()) {
                                    //TODO обработка успешного изменения статуса
                                }
                            }
                        }
                    } catch (\Exception $e) {

                    }
                }
            }
        }

        return $result;
    }

    public function closeOrder($id, $reason, $discount = 0)
    {
        $result = [];
        $params = [
            'reason' => $reason,
            'discount' => $discount
        ];
        $endpoint = '/orders/' . $id . '/close';
        $data = $this->apiKey . 'POST' . $this->prefix . $endpoint . Json::encode($params);
        $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($params));
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch) . "\n";
        curl_close($ch);

        if (strlen($response) > 0) {
            $response = Json::decode($response);

            if (isset($response['id']) && intval($response['id']) > 0) {
                $result = $response;
                $params['id'] = $id;
                $params['response'] = $response;

                try {
                    if ($this->obj !== null) {
                        $obj = $this->obj;
                        $get = $obj::getList(
                            [
                                'select' => ['ID', 'UF_DATA'],
                                'filter' => [
                                    '=UF_ORDER_ID' => $id
                                ],
                                'limit' => 1
                            ]
                        );
                        while ($res = $get->fetch()) {
                            $data = unserialize($res['UF_DATA']);
                            $result['reason'] = $reason;
                            $data[] = $result;
                            $update = $obj::update(
                                $res['ID'],
                                [
                                    'UF_UPDATED_AT' => DateTime::createFromTimestamp(time()),
                                    'UF_STATUS' => $result['status'],
                                    'UF_DATA' => serialize($data)
                                ]
                            );

                            if ($update->isSuccess()) {
                                //TODO обработка успешного изменения статуса
                            }
                        }
                    }
                } catch (\Exception $e) {

                }

                Logger::add($this->userId, 'safeCrowCloseOrderSuccess', $params);
            } else {
                if (isset($response['errors'])) {
                    $params['response'] = $response;
                    Logger::add($this->userId, 'safeCrowCloseOrderFailed', $params);
                }
            }
        }

        return $result;
    }

    public function escalateOrder($id, $reason)
    {
        $result = [];
        $params = [
            'reason' => $reason
        ];
        $endpoint = '/orders/' . $id . '/escalate';
        $data = $this->apiKey . 'POST' . $this->prefix . $endpoint . Json::encode($params);
        $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($params));
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch) . "\n";
        curl_close($ch);

        if (strlen($response) > 0) {
            $response = Json::decode($response);

            if (isset($response['errors'])) {
                $params['id'] = $id;
                $params['response'] = $response;
                Logger::add($this->userId, 'safeCrowEscalateOrderFailed', $params);
            } else {
                $result = $response;
            }
        }

        return $result;
    }

    public function payOrder($id, $redirectUrl)
    {
        $result = [];
        $params = [
            'redirect_url' => $redirectUrl
        ];
        $endpoint = '/orders/' . $id . '/pay';
        $data = $this->apiKey . 'POST' . $this->prefix . $endpoint . Json::encode($params);
        $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($params));
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch) . "\n";
        curl_close($ch);

        if (strlen($response) > 0) {
            $response = Json::decode($response);

            if (isset($response['errors'])) {
                $params['id'] = $id;
                $params['response'] = $response;
                Logger::add($this->userId, 'safeCrowPayOrderFailed', $params);
            } else {
                $result = $response;
            }
        }

        return $result;
    }

    public function attachmentToOrder()
    {
        //TODO сделать вложение файлов к сделке
    }

    public function orderAttachments()
    {
        //TODO получить файлы связанные со сделкой
    }

    public function addUserCard($userId, $redirectUrl)
    {
        $result = [];
        $params = [
            'redirect_url' => $redirectUrl
        ];
        $endpoint = '/users/' . $userId . '/cards';
        $data = $this->apiKey . 'POST' . $this->prefix . $endpoint . Json::encode($params);
        $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($params));
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch) . "\n";
        curl_close($ch);

        if (strlen($response) > 0) {
            $response = Json::decode($response);

            if (isset($response['errors'])) {
                $params['response'] = $response;
                Logger::add($this->userId, 'safeCrowAddUserCardFailed', $params);
            } else {
                $result = $response;
            }
        }

        return $result;
    }

    public function userCards($userId)
    {
        $result = [];
        $endpoint = '/users/' . $userId . '/cards';
        $data = $this->apiKey . 'GET' . $this->prefix . $endpoint;
        $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch) . "\n";
        curl_close($ch);

        if (strlen($response) > 0) {
            $response = Json::decode($response);

            if (isset($response['errors'])) {
                Logger::add($this->userId, 'safeCrowGetUserCardsFailed', $response);
            } else {
                $result = $response;
            }
        }

        return $result;
    }

    public function addCardToOrder($userId, $orderId, $cardId)
    {
        $result = [];
        $params = [
            'supplier_payout_card_id' => $cardId
        ];
        $endpoint = '/users/' . $userId . '/orders/' . $orderId;
        $data = $this->apiKey . 'POST' . $this->prefix . $endpoint . Json::encode($params);
        $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($params));
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch) . "\n";
        curl_close($ch);

        if (strlen($response) > 0) {
            $response = Json::decode($response);

            if (isset($response['error'])) {
                $params['response'] = $response;
                Logger::add($this->userId, 'safeCrowAddCardToOrderFailed', $params);
            } else {
                $result = $response;
            }
        }

        return $result;
    }

    public function setCallbackUrl($callbackUrl)
    {
        $result = [];
        $params = [
            'callback_url' => $callbackUrl
        ];
        $endpoint = '/settings';
        $data = $this->apiKey . 'POST' . $this->prefix . $endpoint . Json::encode($params);
        $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($params));
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch) . "\n";
        curl_close($ch);

        if (strlen($response) > 0) {
            $response = Json::decode($response);

            if (isset($response['errors'])) {
                $params['response'] = $response;
                Logger::add($this->userId, 'safeCrowSetCallbackUrlFailed', $params);
            } else {
                $result = $response;
            }
        }

        return $result;
    }

    public function getCallbackUrl()
    {
        $result = [];
        $endpoint = '/settings';
        $data = $this->apiKey . 'GET' . $this->prefix . $endpoint;
        $hmac = hash_hmac('SHA256', $data, $this->apiSecret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$hmac}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch) . "\n";
        curl_close($ch);

        if (strlen($response) > 0) {
            $response = Json::decode($response);

            if (isset($response['errors'])) {
                Logger::add($this->userId, 'safeCrowGetCallbackUrlFailed', $response);
            } else {
                $result = $response;
            }
        }

        return $result;
    }
}
