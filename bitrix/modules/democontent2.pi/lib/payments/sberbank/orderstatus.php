<?php
/**
 * User: Sergey nazarkin
 * Email: nazarkin2017@mail.ru
 * Date: 08.01.2021
 * Time: 13:09
 */

namespace Democontent2\Pi\Payments\SberBank;

use Bitrix\Main\Web\Json;
use Democontent2\Pi\Hl;
use Democontent2\Pi\Payments\SberBank\Exceptions\ErrorException;
use Democontent2\Pi\Payments\SberBank\Exceptions\OrderSuccessStatusException;

class OrderStatus
{
    const TABLE_NAME = 'Democontentpisberbankorders';

    protected $operationType = 'status';
    protected $orderId = 0;
    protected $status = 0;
    protected $sberBankOrderId = '';
    protected $formUrl = '';
    protected $errorCode = 0;
    protected $orderParams = [];
    protected $statusParams = [];
    protected $sessionExpired = false;
    protected $sessionCreated = 0;
    protected $sessionTimeout = 0;

    function __construct($orderNumber)
    {
        $this->orderId = $orderNumber;
    }

    /**
     * @return string
     */
    public function getFormUrl()
    {
        return $this->formUrl;
    }

    /**
     * @return boolean
     */
    public function isSessionExpired()
    {
        return $this->sessionExpired;
    }

    /**
     * @param string $paramName
     * @param mixed $paramValue
     */
    public function setOrderParams($paramName, $paramValue)
    {
        $this->orderParams[$paramName] = $paramValue;
    }

    private function sberBankOrderNumber()
    {
        $hl = new Hl(static::TABLE_NAME, 0);
        if ($hl->obj !== null) {
            $obj = $hl->obj;
            try {
                $get = $obj::getList(
                    [
                        'select' => [
                            'UF_SBERBANK_ORDER_ID'
                        ],
                        'filter' => [
                            '=UF_ORDER_ID' => $this->orderId
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $this->sberBankOrderId = $res['UF_SBERBANK_ORDER_ID'];
                }
            } catch (\Exception $e) {
            }
        }
    }

    private function checkStatus()
    {
        $result = null;

        $this->sberBankOrderNumber();

        if ($this->sberBankOrderId) {
            $config = Config::getInstance();

            $this->setOrderParams('userName', $config->getUserName());
            $this->setOrderParams('password', $config->getPassword());
            $this->setOrderParams('orderId', $this->sberBankOrderId);

            $result = Json::decode(file_get_contents($config->getStatusURL() . '?' . http_build_query($this->orderParams)));
        }

        return $result;
    }

    public function checkUrl()
    {
        $hl = new Hl(static::TABLE_NAME, 0);
        if ($hl->obj !== null) {
            $obj = $hl->obj;
            try {
                $get = $obj::getList(
                    [
                        'select' => [
                            '*'
                        ],
                        'filter' => [
                            '=UF_ORDER_ID' => $this->orderId
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    if ((time() - strtotime($res['UF_CREATED_AT']) >= intval($res['UF_SESSION_TIMEOUT']))) {
                        $this->sessionExpired = true;
                    }

                    $this->sessionCreated = intval(strtotime($res['UF_CREATED_AT']));
                    $this->sessionTimeout = intval($res['UF_SESSION_TIMEOUT']);
                    $this->formUrl = $res['UF_FORM_URL'];
                    $this->status = intval($res['UF_STATUS']);
                }
            } catch (\Exception $e) {
            }
        }

        return;
    }

    public function getStatus()
    {
        try {
            $getStatus = $this->checkStatus();

            if (intval($getStatus['ErrorCode'])) {
                $this->errorCode = intval($getStatus['ErrorCode']);
                throw new ErrorException($getStatus['ErrorMessage']);
            } else {
                switch ($getStatus['OrderStatus']) {
                    case 2:
                        $this->status = 1;
                        $this->statusParams = $getStatus;
                        throw new OrderSuccessStatusException();
                        break;
                }
            }
        } catch (ErrorException $e) {
            $e->setOrderId($this->orderId);
            $e->setErrorCode($this->errorCode);
            $e->setOperationType($this->operationType);
            $e->log();
        } catch (OrderSuccessStatusException $e) {
            $e->setOrderId($this->orderId);
            $e->setStatusParams($this->statusParams);
            $e->save();
        }

        return $this->status;
    }
} 