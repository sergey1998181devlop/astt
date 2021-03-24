<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 27.03.2019
 * Time: 16:12
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Payments;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\Hl;
use Democontent2\Pi\I\IPayPal;
use Democontent2\Pi\Order;

class PayPal implements IPayPal
{
    private $clientId = '';
    private $secret = '';
    private $redirect = '';
    private $sum = 0;
    private $transactionId = '';
    private $request = [];
    private $result = '';
    private $url = '';

    /**
     * PayPal constructor.
     */
    public function __construct()
    {
        $this->clientId = Option::get(DSPI, 'paypal_client_id');
        $this->secret = Option::get(DSPI, 'paypal_secret');

        if (intval(Option::get(DSPI, 'paypal_mode'))) {
            $this->url = 'https://api.paypal.com/v1';
        } else {
            $this->url = 'https://api.sandbox.paypal.com/v1';
        }
    }

    public function getRedirect()
    {
        return $this->redirect;
    }

    public function getTransactionId()
    {
        return $this->transactionId;
    }

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

    private function getToken()
    {
        $token = '';

        if (function_exists("curl_init")) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url . '/oauth2/token');
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $this->clientId . ":" . $this->secret);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

            $result = curl_exec($ch);

            curl_close($ch);

            if (strlen($result)) {
                $result = Json::decode($result);
                if (isset($result['access_token'])) {
                    $token = $result['access_token'];
                }
            }
        }

        return $token;
    }

    public function make($data)
    {
        if ($data['orderId']) {
            $host = ((Application::getInstance()->getContext()->getRequest()->isHttps()) ? 'https://' : 'http://')
                . Application::getInstance()->getContext()->getRequest()->getHttpHost() . SITE_DIR;

            $token = $this->getToken();

            if (strlen($token)) {
                $data = [
                    'intent' => 'sale',
                    'payer' => [
                        'payment_method' => 'paypal'
                    ],
                    'transactions' => [
                        [
                            'amount' => [
                                'currency' => 'USD',
                                'total' => $data['cost'],

                            ],
                            'custom' => $data['orderId'],
                            'invoice_number' => $data['orderId'],
                            'description' => $data['description'],
                            'payment_options' => [
                                'allowed_payment_method' => 'IMMEDIATE_PAY'
                            ]
                        ]
                    ],
                    'redirect_urls' => [
                        'return_url' => $host,
                        'cancel_url' => $host
                    ]
                ];

                if (function_exists("curl_init")) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $this->url . '/payments/payment');
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'Content-Type: application/json',
                            'Authorization: Bearer ' . $token
                        ]
                    );
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
                    curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($data));

                    $result = curl_exec($ch);

                    curl_close($ch);

                    if (strlen($result)) {
                        $result = Json::decode($result);

                        if (isset($result['id']) && isset($result['links'])) {
                            if (count($result['links'])) {
                                foreach ($result['links'] as $link) {
                                    switch ($link['method']) {
                                        case 'REDIRECT':
                                            $this->redirect = $link['href'];
                                            break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function verify()
    {
        if (isset($this->request['id']) && isset($this->request['state'])) {
            switch (ToLower($this->request['state'])) {
                case 'completed':
                    $token = $this->getToken();

                    if (strlen($token)) {
                        if (function_exists("curl_init")) {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $this->url . '/payments/payment/' . $this->request['id']);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                    'Content-Type: application/json',
                                    'Authorization: Bearer ' . $token
                                ]
                            );
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                            $result = curl_exec($ch);

                            curl_close($ch);

                            if (strlen($result)) {
                                $result = Json::decode($result);

                                if (isset($result['id']) && isset($result['state'])) {
                                    switch (ToLower($result['state'])) {
                                        case 'completed':
                                            $hl = new Hl('Democontentpiorders', 0);
                                            if ($hl->obj !== null) {
                                                $obj = $hl->obj;
                                                try {
                                                    $get = $obj::getList(
                                                        [
                                                            'select' => [
                                                                'ID',
                                                                'UF_SUM',
                                                                'UF_USER_ID'
                                                            ],
                                                            'filter' => [
                                                                '=ID' => intval($result['transactions'][0]['invoice_number']),
                                                                '=UF_PAYED' => 0
                                                            ],
                                                            'limit' => 1
                                                        ]
                                                    );
                                                    while ($res = $get->fetch()) {
                                                        $order = new Order(intval($res['UF_USER_ID']));
                                                        $order->setOrderId(intval($res['ID']));
                                                        if ($order->setPayed()) {
                                                            $this->sum = $res['UF_SUM'];
                                                            $this->transactionId = $this->request['id'];
                                                            $this->result = 'true';
                                                        }
                                                    }
                                                } catch (\Exception $e) {
                                                }
                                            }
                                            break;
                                    }
                                }
                            }
                        }
                    }
                    break;
            }
        }
    }
}