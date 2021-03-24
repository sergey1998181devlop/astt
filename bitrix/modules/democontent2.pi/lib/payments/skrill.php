<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 26.03.2019
 * Time: 13:44
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Payments;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Democontent2\Pi\Hl;
use Democontent2\Pi\I\ISkrill;
use Democontent2\Pi\Order;
use Democontent2\Pi\Sign;
use Democontent2\Pi\User;

class Skrill implements ISkrill
{
    const URL = 'https://www.moneybookers.com/app/payment.pl';

    private $merchantAccount = '';
    private $mqi = '';
    private $merchantSecretWord = '';
    private $redirect = '';
    private $sum = 0;
    private $transactionId = '';
    private $request = [];
    private $result = '';

    /**
     * Skrill constructor.
     */
    public function __construct()
    {
        try {
            $this->merchantAccount = Option::get(DSPI, 'skrill_merchant_account');
            $this->merchantSecretWord = Option::get(DSPI, 'skrill_merchant_secret_word');
            $this->mqi = Option::get(DSPI, 'skrill_mqi');
        } catch (ArgumentNullException $e) {
        } catch (ArgumentOutOfRangeException $e) {
        }
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

            $host = ((Application::getInstance()->getContext()->getRequest()->isHttps()) ? 'https://' : 'http://')
                . Application::getInstance()->getContext()->getRequest()->getHttpHost() . SITE_DIR;

            $requestParams = [
                "action" => "prepare",
                "email" => $this->merchantAccount,
                "pay_to_email" => $this->merchantAccount,
                "password" => $this->mqi,
                'amount' => $data['cost'],
                'currency' => "EUR",
                'bnf_email' => $data['userEmail'],
                'subject' => $data['description'],
                'note' => $data['description'],
                "transaction_id" => $data['orderId'],
                "return_url" => $host,
                "cancel_url" => $host,
                "return_url_target" => "_self",
                "cancel_url_target" => "_self",
                "status_url" => $host . 'payments/' . Sign::getInstance()->get() . '/',
                'language' => "English",
                'prepare_only' => 1
            ];

            if (function_exists("curl_init")) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, static::URL);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
                curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($requestParams));
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));


                $result = curl_exec($ch);

                curl_close($ch);

                if (strlen($result) == 32) {
                    $this->redirect = static::URL . '?sid=' . $result;
                }
            }
        }
    }

    public function verify()
    {
        if (isset($this->request['md5sig']) && $this->request['merchant_id']
            && isset($this->request['mb_transaction_id']) && isset($this->request['status']) && intval($this->request['status']) == 2
            && isset($this->request['transaction_id']) && intval($this->request['transaction_id'])) {
            $arr = [
                $this->request['merchant_id'],
                $this->request['transaction_id'],
                ToUpper(md5($this->merchantSecretWord)),
                $this->request['mb_amount'],
                $this->request['mb_currency'],
                $this->request['status']
            ];

            if ($this->request['md5sig'] == ToUpper(md5(implode('', $arr)))) {
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
                                    '=ID' => intval($this->request['transaction_id']),
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
                                $this->transactionId = $this->request['mb_transaction_id'];
                                $this->result = 'true';
                            }
                        }
                    } catch (\Exception $e) {
                    }
                }
            }
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


}