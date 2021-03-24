<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 25.03.2019
 * Time: 19:29
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Payments;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\Hl;
use Democontent2\Pi\I\IPayTrail;
use Democontent2\Pi\Order;
use Democontent2\Pi\Sign;

class PayTrail implements IPayTrail
{
    const URL = 'https://payment.paytrail.com/api-payment/create';

    private $merchantId = '';
    private $merchantSecret = '';
    private $redirect = '';
    private $sum = 0;
    private $transactionId = '';
    private $request = [];
    private $result = '';

    /**
     * PayTrail constructor.
     */
    public function __construct()
    {
        try {
            $this->merchantId = Option::get(DSPI, 'paytrail_merchant_id');
            $this->merchantSecret = Option::get(DSPI, 'paytrail_merchant_secret');
        } catch (ArgumentNullException $e) {
        } catch (ArgumentOutOfRangeException $e) {
        }
    }

    public function make($data)
    {
        if ($data['orderId']) {
            $host = ((Application::getInstance()->getContext()->getRequest()->isHttps()) ? 'https://' : 'http://')
                . Application::getInstance()->getContext()->getRequest()->getHttpHost() . SITE_DIR;

            $requestParams = [
                "orderNumber" => $data['orderId'],
                "description" => $data['description'],
                "currency" => 'EUR',
                "locale" => 'en_US',
                "urlSet" => [
                    "success" => $host,
                    "failure" => $host,
                    "pending" => $host,
                    "notification" => $host . 'payments/' . Sign::getInstance()->get() . '/',
                ],
                'price' => $data['cost']
            ];

            if (function_exists("curl_init")) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, static::URL);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
                curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Content-Type: application/json",
                    "Accept: application/json",
                    "X-Verkkomaksut-Api-Version: 1"
                ]);
                curl_setopt($ch, CURLOPT_USERPWD, $this->merchantId . ":" . $this->merchantSecret);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($requestParams));
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);


                $result = Json::decode(curl_exec($ch));

                if (isset($result['url'])) {
                    if (strlen($result['url'])) {
                        $this->redirect = $result['url'];
                    }
                }

                curl_close($ch);
            }
        }
    }

    public function verify()
    {
        if (isset($this->request['RETURN_AUTHCODE']) && isset($this->request['PAID'])
            && isset($this->request['ORDER_NUMBER']) && isset($this->request['TIMESTAMP']) && isset($this->request['METHOD'])) {
            $arr = [
                $this->request['ORDER_NUMBER'],
                $this->request['TIMESTAMP'],
                $this->request['PAID'],
                $this->request['METHOD'],
                $this->merchantSecret
            ];
            if ($this->request['RETURN_AUTHCODE'] == ToUpper(md5(implode('|', $arr)))) {
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
                                    '=ID' => intval($this->request['ORDER_NUMBER']),
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
                                $this->transactionId = $this->request['PAID'];
                                $this->result = 'true';
                            }
                        }
                    } catch (\Exception $e) {
                    }
                }
            }
        }
    }

    /**
     * @param array $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
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

}