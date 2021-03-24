<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 28.09.2018
 * Time: 15:25
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Payments;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\Hl;
use Democontent2\Pi\I\ITinkoff;
use Democontent2\Pi\Order;
use Democontent2\Pi\User;

class Tinkoff implements ITinkoff
{
    const BASE_URL = 'https://securepay.tinkoff.ru/v2/';
    const ORDERS_TABLE = 'Democontentpiorders';
    const TINKOFF_ORDERS_TABLE = 'Democontentpitinkofforders';
    const TINKOFF_ERRORS_TABLE = 'Democontentpitinkofferrors';

    private $terminalKey = '';
    private $secretKey = '';
    private $redirect = '';
    private $request = [];
    private $error = '';
    private $response = '';
    private $orderId = 0;
    private $transactionId = '';
    private $sum = 0;
    private $result = '';

    /**
     * Tinkoff constructor.
     */
    public function __construct()
    {
        $this->terminalKey = Option::get(DSPI, 'tinkoffTerminalKey');
        $this->secretKey = Option::get(DSPI, 'tinkoffSecretKey');
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return int
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * @param array $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function make($data)
    {
        $this->checkRedirect($data['orderId']);
        if ($this->redirect) {
            return;
        }

        $this->redirect = '';

        if (!isset($data['userEmail'])) {
            $us = new User(intval($data['userId']), 0);
            $userData = $us->get();

            if (isset($userData['EMAIL'])) {
                if ($userData['EMAIL']) {
                    $data['userEmail'] = $userData['EMAIL'];
                }
            }
        }

        $params = [
            'TerminalKey' => $this->terminalKey,
            'OrderId' => $data['orderId'],
            'Amount' => ($data['cost'] * 100),
            'IP' => Application::getInstance()->getContext()->getServer()->get('REMOTE_ADDR'),
            'Description' => $data['description'],
            'Language' => 'ru',
            'CustomerKey' => intval($data['userId']),
            'Email' => $data['userEmail'],
            //'Phone' => $data['phone'],
            'DATA' => [
                'orderId' => intval($data['orderId']),
                'userId' => intval($data['userId']),
                'userEmail' => $data['userEmail'],
                //'userPhone' => $data['phone'],
                'time' => time()
            ],
        ];

        if (intval(Option::get(DSPI, 'tinkoffOfd')) > 0) {
            $taxCode = Option::get(DSPI, 'tinkoffTaxCode');
            $receipt = [
                'Email' => $data['userEmail'],
                'Taxation' => $taxCode
            ];

            $receipt['Items'][] = [
                'Name' => $data['description'],
                'Price' => ($data['cost'] * 100),
                'Quantity' => 1,
                'Amount' => ($data['cost'] * 100),
                'Tax' => 'none'
            ];

            $params['Receipt'] = $receipt;
        }

        $params['Token'] = $this->signature($params);

        $result = $this->request('Init', $params);
        $this->initPayment($result, $params);
    }

    public function verify()
    {
        $this->result = 'ERROR';
        if (is_array($this->request) && count($this->request) > 0) {
            if (isset($this->request['Token']) && isset($this->request['OrderId'])
                && isset($this->request['Success']) && isset($this->request['Status'])) {
                $token = $this->request['Token'];
                unset($this->request['Token']);

                $signature = $this->checkToken($this->request);
                if ($token == $signature) {
                    switch ($this->request['Status']) {
                        case 'CONFIRMED':
                            $hl = new Hl(static::ORDERS_TABLE, 0);
                            if ($hl->obj !== null) {
                                $obj = $hl->obj;
                                try {
                                    $get = $obj::getList(
                                        [
                                            'select' => [
                                                'ID',
                                                'UF_USER_ID'
                                            ],
                                            'filter' => [
                                                '=ID' => intval($this->request['OrderId']),
                                                '=UF_PAYED' => 0
                                            ],
                                            'limit' => 1
                                        ]
                                    );
                                    while ($res = $get->fetch()) {
                                        $order = new Order(intval($res['UF_USER_ID']));
                                        $order->setOrderId(intval($res['ID']));
                                        if ($order->setPayed()) {
                                            $this->sum = ($this->request['Amount'] / 100);
                                            $this->transactionId = $this->request['PaymentId'];
                                            $this->setPaymentSuccess(
                                                intval($res['ID']),
                                                $this->request['PaymentId']
                                            );
                                            $this->result = 'OK';
                                        }
                                    }
                                } catch (\Exception $e) {
                                }
                            }
                            break;
                        case 'AUTHORIZED':
                            $this->result = 'OK';
                            break;
                    }
                } else {
                    $this->error(
                        [
                            'invalid token' => 1,
                            'original' => $token,
                            'custom' => $signature
                        ],
                        $this->request
                    );
                }
            } else {
                $this->error(
                    [
                        'invalid params'
                    ],
                    $this->request
                );
            }
        }

        if ($this->result == 'OK') {
            \CHTTP::SetStatus('200 OK');
        } else {
            \CHTTP::SetStatus('404 Not Found');
        }
    }

    private function checkToken($request)
    {
        $arr = [];
        $arr_ = [];
        foreach ($request as $k => $v) {
            $arr[$k] = $v;
        }
        $arr['Password'] = $this->secretKey;

        if ($arr['Success']) {
            $arr['Success'] = 'true';
        } else {
            $arr['Success'] = 'false';
        }

        ksort($arr);
        reset($arr);

        foreach ($arr as $k => $v) {
            $arr_[] = $v;
        }

        unset($arr);

        return hash('sha256', implode('', $arr_));
    }

    private function setPaymentSuccess($orderId, $paymentId)
    {
        $hl = new Hl(static::TINKOFF_ORDERS_TABLE, 0);
        if ($hl->obj !== null) {
            $obj = $hl->obj;
            try {
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID'
                        ],
                        'filter' => [
                            '=UF_ORDER_ID' => $orderId,
                            '=UF_PAYMENT_ID' => $paymentId,
                            '=UF_STATUS' => 'NEW'
                        ]
                    ]
                );
                while ($res = $get->fetch()) {
                    $obj::update(
                        $res['ID'],
                        [
                            'UF_STATUS' => 'SUCCESS'
                        ]
                    );
                }
            } catch (\Exception $e) {
            }
        } else {
            if ($this->createOrdersTable()) {
                $this->setPaymentSuccess($orderId, $paymentId);
            }
        }
    }

    private function initPayment($result, $params)
    {
        if (intval($result['Success']) == 1 && !intval($result['ErrorCode'])) {
            if ($result['Status'] == 'NEW') {
                $hl = new Hl(static::TINKOFF_ORDERS_TABLE, 0);
                if ($hl->obj !== null) {
                    $obj = $hl->obj;
                    try {
                        $add = $obj::add(
                            [
                                'UF_ORDER_ID' => $this->orderId,
                                'UF_PAYMENT_ID' => $result['PaymentId'],
                                'UF_STATUS' => $result['Status'],
                                'UF_FORM_URL' => $result['PaymentURL'],
                                'UF_JSON_DATA' => Json::encode(
                                    [
                                        'result' => $result,
                                        'params' => $params
                                    ]
                                ),
                                'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                                'UF_UPDATED_AT' => DateTime::createFromTimestamp(time())
                            ]
                        );
                        if ($add->isSuccess()) {
                            $this->redirect = $result['PaymentURL'];
                        }
                    } catch (\Exception $e) {
                    }
                } else {
                    if ($this->createOrdersTable()) {
                        $this->initPayment($result, $params);
                    }
                }
            } else {
                $this->error($result, $params);
            }
        } else {
            $this->error($result, $params);
        }
    }

    private function error($result, $params)
    {
        $hl = new Hl(static::TINKOFF_ERRORS_TABLE, 0);
        if ($hl->obj !== null) {
            $obj = $hl->obj;
            try {
                $obj::add(
                    [
                        'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                        'UF_ERROR_CODE' => $result['ErrorCode'],
                        'UF_MESSAGE' => $result['Message'],
                        'UF_DETAILS' => $result['Details'],
                        'UF_JSON_DATA' => Json::encode(
                            [
                                'result' => $result,
                                'params' => $params
                            ]
                        )
                    ]
                );
            } catch (\Exception $e) {
            }
        } else {
            if ($this->createErrorsTable()) {
                $this->error($result, $params);
            }
        }
    }

    private function checkRedirect($orderId)
    {
        $this->orderId = intval($orderId);
        $this->redirect = '';

        $hl = new Hl(static::TINKOFF_ORDERS_TABLE, 0);
        if ($hl->obj !== null) {
            $obj = $hl->obj;
            try {
                $get = $obj::getList(
                    [
                        'select' => [
                            'UF_FORM_URL'
                        ],
                        'filter' => [
                            '=UF_ORDER_ID' => $this->orderId,
                            '=UF_STATUS' => 'NEW'
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $this->redirect = $res['UF_FORM_URL'];
                }
            } catch (\Exception $e) {
            }
        } else {
            if ($this->createOrdersTable()) {
                $this->checkRedirect($orderId);
            }
        }
    }

    private function request($path, $args)
    {
        $json = null;
        $this->error = '';
        if (is_array($args)) {
            $args = Json::encode($args);
        }

        if (function_exists('curl_init')) {
            if ($curl = curl_init()) {
                curl_setopt($curl, CURLOPT_URL, self::BASE_URL . $path);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $args);
                curl_setopt($curl, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                ]);

                $out = curl_exec($curl);
                $this->response = $out;

                $json = Json::decode($out);

                curl_close($curl);
            }
        }

        return $json;
    }

    private function signature($args)
    {
        $token = '';
        $args['Password'] = $this->secretKey;
        ksort($args);

        foreach ($args as $arg) {
            if (!is_array($arg)) {
                $token .= $arg;
            }
        }
        $token = hash('sha256', $token);

        return $token;
    }

    private function createOrdersTable()
    {
        $result = false;

        $className = ToUpper(end(explode('\\', __CLASS__)));
        $add = Hl::create(
            ToLower(static::TINKOFF_ORDERS_TABLE),
            [
                'UF_ORDER_ID' => [
                    'N',
                    'integer',
                    [
                        'SETTINGS' => ['DEFAULT_VALUE' => '',],
                        'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ORDER_ID')],
                        'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ORDER_ID')],
                    ]
                ],
                'UF_PAYMENT_ID' => [
                    'N',
                    'string',
                    [
                        'SETTINGS' => ['DEFAULT_VALUE' => '',],
                        'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PAYMENT_ID')],
                        'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PAYMENT_ID')],
                    ]
                ],
                'UF_STATUS' => [
                    'N',
                    'string',
                    [
                        'SETTINGS' => ['DEFAULT_VALUE' => '',],
                        'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_STATUS')],
                        'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_STATUS')],
                    ]
                ],
                'UF_FORM_URL' => [
                    'N',
                    'string',
                    [
                        'SETTINGS' => ['DEFAULT_VALUE' => '',],
                        'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_FORM_URL')],
                        'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_FORM_URL')],
                    ]
                ],
                'UF_JSON_DATA' => [
                    'N',
                    'string',
                    [
                        'SETTINGS' => ['DEFAULT_VALUE' => '',],
                        'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_JSON_DATA')],
                        'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_JSON_DATA')],
                    ]
                ],
                'UF_CREATED_AT' => [
                    'N',
                    'datetime',
                    [
                        'SETTINGS' => ['DEFAULT_VALUE' => '',],
                        'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                        'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                    ]
                ],
                'UF_UPDATED_AT' => [
                    'N',
                    'datetime',
                    [
                        'SETTINGS' => ['DEFAULT_VALUE' => '',],
                        'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_UPDATED_AT')],
                        'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_UPDATED_AT')],
                    ]
                ],
            ],
            [
                'ALTER TABLE `' . ToLower(static::TINKOFF_ORDERS_TABLE) . '` MODIFY `UF_PAYMENT_ID` VARCHAR(255);',
                'ALTER TABLE `' . ToLower(static::TINKOFF_ORDERS_TABLE) . '` MODIFY `UF_STATUS` VARCHAR(255);',
                'ALTER TABLE `' . ToLower(static::TINKOFF_ORDERS_TABLE) . '` MODIFY `UF_JSON_DATA` LONGTEXT;',
            ],
            [
                ['UF_ORDER_ID'],
                ['UF_PAYMENT_ID'],
                ['UF_STATUS'],
                ['UF_CREATED_AT'],
            ],
            Loc::getMessage($className . '_IBLOCK_NAME')
        );
        if ($add) {
            $result = true;
        }

        return $result;
    }

    private function createErrorsTable()
    {
        $result = false;

        $className = ToUpper(end(explode('\\', __CLASS__)));
        $add = Hl::create(
            ToLower(static::TINKOFF_ERRORS_TABLE),
            [
                'UF_CREATED_AT' => [
                    'N',
                    'datetime',
                    [
                        'SETTINGS' => ['DEFAULT_VALUE' => '',],
                        'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                        'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                    ]
                ],
                'UF_ERROR_CODE' => [
                    'N',
                    'string',
                    [
                        'SETTINGS' => ['DEFAULT_VALUE' => '',],
                        'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ERROR_CODE')],
                        'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ERROR_CODE')],
                    ]
                ],
                'UF_MESSAGE' => [
                    'N',
                    'string',
                    [
                        'SETTINGS' => ['DEFAULT_VALUE' => '',],
                        'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_MESSAGE')],
                        'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_MESSAGE')],
                    ]
                ],
                'UF_DETAILS' => [
                    'N',
                    'string',
                    [
                        'SETTINGS' => ['DEFAULT_VALUE' => '',],
                        'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DETAILS')],
                        'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DETAILS')],
                    ]
                ],
                'UF_JSON_DATA' => [
                    'N',
                    'string',
                    [
                        'SETTINGS' => ['DEFAULT_VALUE' => '',],
                        'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_JSON_DATA')],
                        'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_JSON_DATA')],
                    ]
                ],
            ],
            [
                'ALTER TABLE `' . ToLower(static::TINKOFF_ERRORS_TABLE) . '` MODIFY `UF_ERROR_CODE` VARCHAR(255);',
                'ALTER TABLE `' . ToLower(static::TINKOFF_ERRORS_TABLE) . '` MODIFY `UF_JSON_DATA` LONGTEXT;',
            ],
            [
                ['UF_ERROR_CODE'],
                ['UF_CREATED_AT'],
            ],
            Loc::getMessage($className . '_ERRORS_IBLOCK_NAME')
        );
        if ($add) {
            $result = true;
        }

        return $result;
    }
}