<?php
/**
 * User: Sergey nazarkin
 * Email: nazarkin2017@mail.ru
 * Date: 08.01.2021
 * Time: 13:09
 */

namespace Democontent2\Pi\Payments\SberBank;

use Bitrix\Main\Localization\Loc;
use Democontent2\Pi\Hl;
use Democontent2\Pi\Order;
use Democontent2\Pi\Payments\SberBank\Exceptions\HashException;
use Democontent2\Pi\Payments\SberBank\Exceptions\OperationException;

class CallBack
{
    const TABLE_NAME = 'Democontentpisberbankorders';

    protected $userId = 0;
    protected $orderId = 0;
    protected $params = [];
    private $sum = 0;
    private $transactionId = '';

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
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    public function checkSum()
    {
        $return = false;

        $params = $this->params;

        try {
            if ($params['operation'] == 'deposited') {
                switch (intval($params['status'])) {
                    case 1:
                        $config = Config::getInstance();
                        $checkSum = $params['checksum'];
                        unset($params['checksum']);

                        ksort($params);
                        reset($params);

                        $tmp = [];
                        foreach ($params as $k => $v) {
                            $tmp[] = $k . ';' . $v;
                        }

                        $hash = strtoupper(hash_hmac('sha256', implode(';', $tmp) . ';', $config->getSecretKey()));

                        if ($hash == $checkSum) {
                            $this->userId = ((isset($params['clientId'])) ? intval($params['clientId']) : intval($params['userId']));
                            $this->orderId = intval($params['orderNumber']);
                            $this->transactionId = $params['orderId'];
                            $this->sum = (intval($params['amount']) / 100);
                            $return = true;
                        } else {
                            throw new HashException(Loc::getMessage('SB_ERROR_INVALID_SIGNATURE') . ' ' . $checkSum);
                        }

                        break;
                }
            } else {
                throw new OperationException(Loc::getMessage('SB_ERROR_OPERATION_TYPE') . ' ' . $params['operation']);
            }
        } catch (OperationException $e) {
            $e->setOrderId(intval($params['orderNumber']));
            $e->setOperationType('operation');
            $e->log();
        } catch (HashException $e) {
            $e->setOrderId(intval($params['orderNumber']));
            $e->setOperationType('checksum');
            $e->log();
        }

        return $return;
    }

    public function setPayed()
    {
        $return = false;
        if (!$this->orderId || !$this->userId) {
            return $return;
        }

        $orderStatus = new OrderStatus($this->orderId);
        if ($orderStatus->getStatus()) {
            $hl = new Hl(static::TABLE_NAME, 0);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                try {
                    $get = $obj::getList(
                        [
                            'select' => [
                                'ID'
                            ],
                            'filter' => [
                                '=UF_ORDER_ID' => $this->orderId,
                                '=UF_STATUS' => 0
                            ],
                            'limit' => 1
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $update = $obj::update(
                            $res['ID'],
                            [
                                'UF_STATUS' => 1
                            ]
                        );
                        if ($update->isSuccess()) {
                            $return = true;
                            $order = new Order($this->userId);
                            $order->setOrderId($this->orderId);
                            $order->setPayed();
                        }
                    }
                } catch (\Exception $e) {
                }
            }
        }

        return $return;
    }
}