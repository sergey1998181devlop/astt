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

use Bitrix\Main\UserTable;
use Democontent2\Pi\I\ISberBank;
use Democontent2\Pi\Payments\SberBank\CallBack;
use Democontent2\Pi\Payments\SberBank\OrderStatus;
use Democontent2\Pi\Payments\SberBank\RegisterOrder;
use Democontent2\Pi\User;

class SberBank implements ISberBank
{
    protected $redirect = '';
    private $request = [];
    private $result = '200 OK';
    private $transactionId = '';
    private $sum = 0;

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
        $this->redirect = '';

        $orderStatus = new OrderStatus($data['orderId']);
        $orderStatus->checkUrl();
        if (!$orderStatus->isSessionExpired() && !$orderStatus->getStatus()) {
            if ($orderStatus->getFormUrl()) {
                $this->redirect = $orderStatus->getFormUrl();
                return;
            }

            $us = new User(intval($data['userId']), 0);
            $userParams = $us->get();
            
            $registerOrder = new RegisterOrder($data['orderId']);
            $registerOrder->setOrderParams('amount', ($data['cost'] * 100));
            $registerOrder->setOrderParams('description', $data['description']);
            $registerOrder->setOrderParams('clientId', $data['userId']);
            $registerOrder->setUserEmail($userParams['EMAIL']);
            $registerOrder->setUserPhone($userParams['PERSONAL_PHONE']);

            $orderBundle = [];
            $orderBundle['orderCreationDate'] = implode('T', explode(' ', date('Y-m-d H:i:s')));

            if ($registerOrder->getUserEmail()) {
                $orderBundle['customerDetails']['email'] = $registerOrder->getUserEmail();
            }

            if ($registerOrder->getUserEmail()) {
                $orderBundle['customerDetails']['phone'] = $registerOrder->getUserPhone();
            }

            $registerOrder->setOrderBundle($orderBundle);

            $registerOrder->result();

            if ($registerOrder->getSberBankOrderId() && $registerOrder->getSberBankOrderFormURL()) {
                $this->redirect = $registerOrder->getSberBankOrderFormURL();
            }
        }
    }

    public function verify()
    {
        $callBack = new CallBack();
        $callBack->setParams($this->request);
        if ($callBack->checkSum()) {
            if ($callBack->getOrderId()) {
                if ($callBack->setPayed()) {
                    $this->sum = $callBack->getSum();
                    $this->transactionId = $callBack->getTransactionId();
                    \CHTTP::SetStatus('200 OK');
                } else {
                    \CHTTP::SetStatus('404 Not Found');
                    $this->result = '404 Not Found';
                }
            } else {
                \CHTTP::SetStatus('404 Not Found');
                $this->result = '404 Not Found';
            }
        }
    }
}