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
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\IO\Path;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\Exceptions\OrderNotFoundException;
use Democontent2\Pi\I\IYandex;
use Democontent2\Pi\Order;
use Democontent2\Pi\User;
use Democontent2\Pi\Utils;
use YandexCheckout\Client;

class Yandex implements IYandex
{
    protected $invId = 0;
    protected $invDescription = '';
    protected $outSum = 0.0;
    protected $redirect = '';
    protected $request = [];
    protected $resultMessage = '';
    private $transactionId = '';
    private $sum = 0;

    private $shopId = '';
    private $scid = '';

    function __construct()
    {
        try {
            $this->shopId = Option::get(DSPI, 'yandex_kassa_shopid');
            $this->scid = Option::get(DSPI, 'yandex_kassa_scid');
        } catch (ArgumentNullException $e) {
        } catch (ArgumentOutOfRangeException $e) {
        }
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

    /**
     * @return string
     */
    public function getResultMessage()
    {
        return $this->resultMessage;
    }

    /**
     * @param string $resultMessage
     */
    public function setResultMessage($resultMessage)
    {
        $this->resultMessage = $resultMessage;
    }

    /**
     * @return array
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param array $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
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
    public function getInvDescription()
    {
        return $this->invDescription;
    }

    /**
     * @param string $invDescription
     */
    public function setInvDescription($invDescription)
    {
        $this->invDescription = $invDescription;
    }

    /**
     * @return int
     */
    public function getInvId()
    {
        return $this->invId;
    }

    /**
     * @param int $invId
     */
    public function setInvId($invId)
    {
        $this->invId = $invId;
    }

    /**
     * @return float
     */
    public function getOutSum()
    {
        return $this->outSum;
    }

    /**
     * @param float $outSum
     */
    public function setOutSum($outSum)
    {
        $this->outSum = $outSum;
    }

    /**
     * @param $data
     */
    public function make($data)
    {
        $this->redirect = '';

        $this->setInvId($data['orderId']);

        if (!isset($data['userEmail'])) {
            $us = new User(intval($data['userId']), 0);
            $userData = $us->get();

            if (isset($userData['EMAIL'])) {
                if ($userData['EMAIL']) {
                    $data['userEmail'] = $userData['EMAIL'];
                }
            }
        }

        $client = new Client();
        $client->setAuth($this->shopId, $this->scid);

        try {
            $context = Application::getInstance()->getContext();

            $paymentData = [
                'amount' => [
                    'value' => $data['cost'] . '.00',
                    'currency' => Utils::getCurrencyCode(),
                ],
                'description' => $data['description'],
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => 'https://' . Path::normalize(Utils::getSiteUrl($context->getSite()) . SITE_DIR . 'user/balance/'),
                ],
                'metadata' => [
                    'orderId' => $data['orderId'],
                    'userId' => $data['userId'],
                    'userEmail' => ((isset($data['userEmail'])) ? $data['userEmail'] : ''),
                    'sum' => $data['cost']
                ]
            ];

            if ($context->getServer()->get('REMOTE_ADDR')) {
                $paymentData['client_ip'] = $context->getServer()->get('REMOTE_ADDR');
            }

            $taxSystemCode = intval(Option::get(DSPI, 'yandex_kassa_tax_code'));
            $nds = intval(Option::get(DSPI, 'yandex_kassa_nds'));
            $ofd = intval(Option::get(DSPI, 'yandex_kassa_ofd'));

            if ($taxSystemCode && $ofd && $nds) {
                $paymentData['receipt'] = [
                    'email' => $data['userEmail'],
                    'tax_system_code' => $taxSystemCode,
                ];

                $paymentData['receipt']['items'][] = [
                    'description' => $data['description'],
                    'quantity' => '1.00',
                    'amount' => [
                        'value' => $data['cost'],
                        'currency' => Utils::getCurrencyCode()
                    ],
                    'vat_code' => $nds
                ];
            }

            $response = $client->createPayment(
                $paymentData,
                uniqid('', true)
            );

            if (isset($response['id']) && isset($response['confirmation']['confirmation_url'])) {
                $this->redirect = $response['confirmation']['confirmation_url'];
            }
        } catch (SystemException $e) {
        }
    }

    /**
     * @return bool
     * @throws OrderNotFoundException
     * @throws \Democontent2\Pi\Exceptions\OrderSetPayedFailException
     */
    public function verify()
    {
        $result = false;
        $request = $this->getRequest();

        switch ($request['object']['status']) {
            case 'waiting_for_capture':
                $client = new Client();
                try {
                    $client->setAuth(
                        intval(Option::get(DSPI, 'yandex_kassa_shopid')),
                        Option::get(DSPI, 'yandex_kassa_scid')
                    );

                    $order = new Order(intval($request['object']['metadata']['userId']));
                    $order->setOrderId(intval($request['object']['metadata']['orderId']));
                    $getOrder = $order->get();
                    if ($getOrder !== null) {
                        $this->sum = intval($request['object']['metadata']['sum']);
                        $this->transactionId = $request['object']['id'];

                        if (!$order->getPayed()) {
                            $capture = $client->capturePayment(
                                [
                                    'amount' => $request['object']['amount'],
                                ],
                                $request['object']['id'],
                                uniqid('', true)
                            );

                            switch ($capture->getStatus()) {
                                case 'succeeded':
                                    $order->setPayed();
                                    break;
                                default:
                                    $client->cancelPayment(
                                        $request['object']['id'],
                                        uniqid('', true)
                                    );
                            }
                        }
                    } else {
                        $client->cancelPayment(
                            $request['object']['id'],
                            uniqid('', true)
                        );

                        throw new OrderNotFoundException(
                            Json::encode(
                                [
                                    'paymentProvider' => 'Yandex',
                                    'request' => $request
                                ]
                            )
                        );
                    }
                } catch (ArgumentNullException $e) {
                } catch (ArgumentOutOfRangeException $e) {
                } catch (ArgumentException $e) {
                }

                break;
        }

        return $result;
    }
}