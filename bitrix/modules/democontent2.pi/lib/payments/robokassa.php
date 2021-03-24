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

use Bitrix\Main\Config\Option;
use Democontent2\Pi\Hl;
use Democontent2\Pi\I\irobokassa;
use Democontent2\Pi\Order;
use Democontent2\Pi\User;

class RoboKassa implements IRobokassa
{
    private $request = [];
    private $transactionId = '';
    private $sum = 0;
    private $redirect = '';
    private $result = '';
    private $merchantLogin = '';
    private $password1 = '';
    private $password2 = '';
    private $testPassword1 = '';
    private $testPassword2 = '';

    /**
     * RoboKassa constructor.
     */
    public function __construct()
    {
        $this->merchantLogin = Option::get(DSPI, 'robokassa_login');
        $this->password1 = Option::get(DSPI, 'robokassa_password_1');
        $this->password2 = Option::get(DSPI, 'robokassa_password_2');
        $this->testPassword1 = Option::get(DSPI, 'robokassa_test_password_1');
        $this->testPassword2 = Option::get(DSPI, 'robokassa_test_password_2');
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->result;
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
        if ($data['orderId']) {
            if (!isset($data['userEmail'])) {
                $us = new User(intval($data['userId']), 0);
                $userData = $us->get();

                if (isset($userData['EMAIL'])) {
                    if ($userData['EMAIL']) {
                        $data['userEmail'] = $userData['EMAIL'];
                    }
                }
            }

            $additionalParams = [
                'Shp_orderId' => intval($data['orderId']),
                'Shp_time' => time(),
                'Shp_userEmail' => $data['userEmail'],
                'Shp_userId' => intval($data['userId']),
                //'Shp_userPhone' => $data['phone']
            ];

            ksort($additionalParams);
            reset($additionalParams);

            $tempAdditional = [];
            foreach ($additionalParams as $k => $v) {
                $tempAdditional[] = $k . '=' . $v;
            }

            if (intval(Option::get(DSPI, 'robokassa_mode'))) {
                $params = [
                    'MrchLogin' => $this->merchantLogin,
                    'OutSum' => $data['cost'],
                    'InvId' => $data['orderId'],
                    'Desc' => $data['description'],
                    'IsTest' => 1,
                    'SignatureValue' => md5($this->merchantLogin . ':' . $data['cost'] . ':' . $data['orderId'] . ':' . $this->testPassword1 . ':' . implode(':', $tempAdditional))
                ];
            } else {
                $params = [
                    'MrchLogin' => $this->merchantLogin,
                    'OutSum' => $data['cost'],
                    'InvId' => $data['orderId'],
                    'Desc' => $data['description'],
                    'SignatureValue' => md5($this->merchantLogin . ':' . $data['cost'] . ':' . $data['orderId'] . ':' . $this->password1 . ':' . implode(':', $tempAdditional))
                ];
            }

            foreach ($additionalParams as $k => $v) {
                $params[$k] = $v;
            }

            unset($tempAdditional, $additionalParams);

            $this->redirect = 'https://auth.robokassa.ru/Merchant/Index.aspx?' . http_build_query($params);
        }
    }

    public function verify()
    {
        $this->result = "ERROR\n";
        if (isset($this->request['OutSum']) && isset($this->request['InvId'])
            && isset($this->request['SignatureValue']) && isset($this->request['Shp_orderId'])
            && isset($this->request['Shp_userId']) && isset($this->request['Shp_time'])
            && isset($this->request['Shp_userEmail'])/* && isset($this->request['Shp_userPhone'])*/) {
            if (intval($this->request['Shp_orderId'])) {
                $additionalParams = [
                    'Shp_orderId' => $this->request['Shp_orderId'],
                    'Shp_time' => $this->request['Shp_time'],
                    'Shp_userEmail' => $this->request['Shp_userEmail'],
                    'Shp_userId' => $this->request['Shp_userId'],
                    //'Shp_userPhone' => $this->request['Shp_userPhone']
                ];

                ksort($additionalParams);
                reset($additionalParams);

                $tempAdditional = [];
                foreach ($additionalParams as $k => $v) {
                    $tempAdditional[] = $k . '=' . $v;
                }

                if (intval(Option::get(DSPI, 'robokassa_mode'))) {
                    $mySignature = ToUpper(md5($this->request['OutSum'] . ':' . $this->request['Shp_orderId'] . ':' . $this->testPassword2 . ':' . implode(':', $tempAdditional)));
                } else {
                    $mySignature = ToUpper(md5($this->request['OutSum'] . ':' . $this->request['Shp_orderId'] . ':' . $this->password2 . ':' . implode(':', $tempAdditional)));
                }
                unset($tempAdditional, $additionalParams);

                if ($mySignature == ToUpper($this->request['SignatureValue'])) {
                    $hl = new Hl('Democontentpiorders', 0);
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
                                        '=ID' => intval($this->request['Shp_orderId']),
                                        '=UF_PAYED' => 0
                                    ],
                                    'limit' => 1
                                ]
                            );
                            while ($res = $get->fetch()) {
                                $order = new Order(intval($res['UF_USER_ID']));
                                $order->setOrderId(intval($res['ID']));
                                if ($order->setPayed()) {
                                    $this->sum = $this->request['OutSum'];
                                    $this->transactionId = $mySignature;
                                    $this->result = "OK" . intval($this->request['Shp_orderId']) . "\n";
                                }
                            }
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
        }
    }
}