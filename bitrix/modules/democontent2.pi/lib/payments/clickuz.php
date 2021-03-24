<?php
/**
 * Date: 19.12.2019
 * Time: 11:06
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

namespace Democontent2\Pi\Payments;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\Hl;
use Democontent2\Pi\I\IClickUZ;
use Democontent2\Pi\Order;

class ClickUZ implements IClickUZ
{
    private const ENDPOINT = 'https://my.click.uz/services/pay';
    private $redirect = '';
    private $sum = 0;
    private $transactionId = '';
    private $request = [];
    private $result = '';
    private $serviceId = '';
    private $merchantId = '';
    private $secretKey = '';

    /**
     * ClickUZ constructor.
     */
    public function __construct()
    {
        $this->serviceId = Option::get(DSPI, 'click_uz_service_id');
        $this->merchantId = Option::get(DSPI, 'click_uz_merchant_id');
        $this->secretKey = Option::get(DSPI, 'click_uz_secret_key');
    }

    /**
     * @return string
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
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
    public function getResult()
    {
        return $this->result;
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
        if ($data['orderId'] && $data['cost']) {
            $returnUrl = '';

            try {
                $returnUrl = ((Application::getInstance()->getContext()->getRequest()->isHttps()) ? 'https://' : 'http://')
                    . Application::getInstance()->getContext()->getRequest()->getHttpHost() . SITE_DIR . 'user/settings/';
            } catch (SystemException $e) {
            }

            $params = [
                'service_id' => $this->serviceId,
                'merchant_id' => $this->merchantId,
                'amount' => $data['cost'] . '.00',
                'transaction_param' => $data['orderId']
            ];

            if (strlen($returnUrl)) {
                $params['return_url'] = $returnUrl;
            }

            $this->redirect = self::ENDPOINT . '?' . http_build_query($params);
        }
    }

    public function verify()
    {
        $result = [
            'error' => '-3',
            'error_note' => 'Action not found'
        ];

        if (!isset($this->request['action'])) {
            header('Content-Type: application/json');
            $this->result = Json::encode($result);
            return;
        }

        if (intval($this->request['action']) == 1) {
            if (isset($this->request['click_trans_id']) && isset($this->request['service_id'])
                && isset($this->request['click_paydoc_id']) && isset($this->request['merchant_trans_id'])
                && isset($this->request['merchant_prepare_id']) && isset($this->request['amount'])
                && isset($this->request['error']) && isset($this->request['sign_time'])
                && isset($this->request['sign_string'])) {
                $sign = md5($this->request['click_trans_id'] . $this->serviceId . $this->secretKey
                    . $this->request['merchant_trans_id'] . $this->request['merchant_prepare_id']
                    . $this->request['amount'] . $this->request['action'] . $this->request['sign_time']);

                if (intval($this->request['error']) == 0 && ToUpper($this->request['sign_string']) == ToUpper($sign)) {
                    $hl = new Hl('Democontentpiorders', 0);
                    if ($hl->obj !== null) {
                        $obj = $hl->obj;
                        try {
                            $orderId = 0;
                            $get = $obj::getList(
                                [
                                    'select' => [
                                        'ID',
                                        'UF_USER_ID',
                                        'UF_SUM'
                                    ],
                                    'filter' => [
                                        '=ID' => intval($this->request['merchant_prepare_id']),
                                        '=UF_PAYED' => 0
                                    ],
                                    'limit' => 1
                                ]
                            );
                            while ($res = $get->fetch()) {
                                $orderId = intval($res['ID']);
                                if (intval($this->request['amount']) == intval($res['UF_SUM'])) {
                                    $order = new Order(intval($res['UF_USER_ID']));
                                    $order->setOrderId(intval($res['ID']));
                                    if ($order->setPayed()) {
                                        $this->sum = $res['UF_SUM'];
                                        $this->transactionId = $this->request['click_trans_id'];
                                        $result = [
                                            'click_trans_id' => $this->request['click_trans_id'],
                                            'merchant_trans_id' => $this->request['merchant_trans_id'],
                                            'merchant_confirm_id' => null,
                                            'error' => 0,
                                            'error_note' => 'Success'
                                        ];
                                    } else {
                                        $result['error'] = '-7';
                                        $result['error_note'] = 'Failed to update user';
                                    }
                                } else {
                                    $result['error'] = '-2';
                                    $result['error_note'] = 'Incorrect parameter amount';
                                }
                            }

                            if (!$orderId) {
                                $result['error'] = '-5';
                                $result['error_note'] = 'User does not exist';
                            }
                        } catch (\Exception $e) {
                        }
                    }
                } else {
                    if (ToUpper($this->request['sign_string']) !== ToUpper($sign)) {
                        $result['error'] = '-1';
                        $result['error_note'] = 'SIGN CHECK FAILED!';
                    } else {
                        if (intval($this->request['error']) < 0) {
                            $result['error'] = '-9';
                            $result['error_note'] = 'Transaction cancelled';
                        }
                    }
                }
            } else {
                $result['error'] = '-8';
                $result['error_note'] = 'Error in request from click';
            }
        } else {
            if (intval($this->request['action']) == 0) {
                if (isset($this->request['click_trans_id']) && isset($this->request['service_id'])
                    && isset($this->request['click_paydoc_id']) && isset($this->request['merchant_trans_id'])
                    && isset($this->request['amount']) && isset($this->request['error'])
                    && isset($this->request['sign_time']) && isset($this->request['sign_string'])) {
                    $sign = md5($this->request['click_trans_id'] . $this->serviceId . $this->secretKey
                        . $this->request['merchant_trans_id'] . $this->request['amount'] . $this->request['action']
                        . $this->request['sign_time']);

                    if (intval($this->request['error']) == 0 && ToUpper($this->request['sign_string']) == ToUpper($sign)) {
                        $hl = new Hl('Democontentpiorders', 0);
                        if ($hl->obj !== null) {
                            $obj = $hl->obj;
                            try {
                                $orderId = 0;
                                $get = $obj::getList(
                                    [
                                        'select' => [
                                            'ID',
                                            'UF_USER_ID',
                                            'UF_SUM'
                                        ],
                                        'filter' => [
                                            '=ID' => intval($this->request['merchant_trans_id']),
                                            '=UF_PAYED' => 0
                                        ],
                                        'limit' => 1
                                    ]
                                );
                                while ($res = $get->fetch()) {
                                    $orderId = intval($res['ID']);
                                    if (intval($this->request['amount']) == intval($res['UF_SUM'])) {
                                        $this->sum = $res['UF_SUM'];
                                        $this->transactionId = $this->request['click_trans_id'];
                                        $result = [
                                            'click_trans_id' => $this->request['click_trans_id'],
                                            'merchant_trans_id' => $this->request['merchant_trans_id'],
                                            'merchant_prepare_id' => intval($res['ID']),
                                            'error' => 0,
                                            'error_note' => 'Success'
                                        ];
                                    } else {
                                        $result['error'] = '-2';
                                        $result['error_note'] = 'Incorrect parameter amount';
                                    }
                                }

                                if (!$orderId) {
                                    $result['error'] = '-5';
                                    $result['error_note'] = 'User does not exist';
                                }
                            } catch (\Exception $e) {
                            }
                        }
                    } else {
                        if (ToUpper($this->request['sign_string']) !== ToUpper($sign)) {
                            $result['error'] = '-1';
                            $result['error_note'] = 'SIGN CHECK FAILED!';
                        } else {
                            if (intval($this->request['error']) < 0) {
                                $result['error'] = '-9';
                                $result['error_note'] = 'Transaction cancelled';
                            }
                        }
                    }
                } else {
                    $result['error'] = '-8';
                    $result['error_note'] = 'Error in request from click';
                }
            } else {
                $result['error'] = '-3';
                $result['error_note'] = 'Action not found';
            }
        }

        header('Content-Type: application/json');
        $this->result = Json::encode($result);
    }
}
