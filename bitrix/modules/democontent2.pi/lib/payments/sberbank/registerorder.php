<?php
/**
 * User: Sergey nazarkin
 * Email: nazarkin2017@mail.ru
 * Date: 08.01.2021
 * Time: 13:09
 */

namespace Democontent2\Pi\Payments\SberBank;

use Bitrix\Main\Application;
use Bitrix\Main\IO\Path;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\Payments\SberBank\Exceptions\ErrorException;
use Democontent2\Pi\Payments\SberBank\Exceptions\RegisterOrderSuccessException;
use Democontent2\Pi\Utils;

class RegisterOrder
{
    protected $operationType = 'register';
    protected $orderId = 0;
    protected $sberBankOrderId = '';
    protected $sberBankOrderFormURL = '';
    protected $errorCode = 0;
    protected $sessionTimeoutSecs = 0;
    protected $bonusValue = 0;
    protected $userEmail = '';
    protected $userPhone = '';
    protected $cartItems = [];
    protected $orderBundle = [];
    protected $orderParams = [
        'orderNumber' => 0,
        'amount' => 0,
        'currency' => 643,
        'description' => '',
        'pageView' => 'DESKTOP',
        'clientId' => 0,
        //'jsonParams'  => ''
    ];

    function __construct($orderNumber)
    {
        $this->orderId = $orderNumber;
        $this->setOrderParams('orderNumber', $orderNumber);
    }

    /**
     * @return array
     */
    public function getOrderBundle()
    {
        return $this->orderBundle;
    }

    /**
     * @param array $orderBundle
     */
    public function setOrderBundle($orderBundle)
    {
        $this->orderBundle = $orderBundle;
    }

    /**
     * @return string
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * @param string $userEmail
     */
    public function setUserEmail($userEmail)
    {
        $this->userEmail = $userEmail;
    }

    /**
     * @return string
     */
    public function getUserPhone()
    {
        return $this->userPhone;
    }

    /**
     * @param string $userPhone
     */
    public function setUserPhone($userPhone)
    {
        $this->userPhone = $userPhone;
    }

    /**
     * @return string
     */
    public function getSberBankOrderFormURL()
    {
        return $this->sberBankOrderFormURL;
    }

    /**
     * @return string
     */
    public function getSberBankOrderId()
    {
        return $this->sberBankOrderId;
    }

    /**
     * @param string $paramName
     * @param mixed $paramValue
     */
    public function setOrderParams($paramName, $paramValue)
    {
        $this->orderParams[$paramName] = $paramValue;
    }

    /**
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\IO\InvalidPathException
     * @throws \Bitrix\Main\SystemException
     */
    private function request()
    {
        $config = Config::getInstance();
        $this->sessionTimeoutSecs = $config->getSessionTimeoutSecs();
        $this->setOrderParams('userName', $config->getUserName());
        $this->setOrderParams('password', $config->getPassword());
        $this->setOrderParams('sessionTimeoutSecs', $config->getSessionTimeoutSecs());
        $this->setOrderParams(
            'returnUrl',
            'http://' . Path::normalize(Utils::getSiteUrl(Application::getInstance()->getContext()->getSite()) . SITE_DIR . 'user/balance/')
        );
        $this->setOrderParams(
            'failUrl',
            'http://' . Path::normalize(Utils::getSiteUrl(Application::getInstance()->getContext()->getSite()) . SITE_DIR . 'user/balance/')
        );

        $orderBundle = $this->getOrderBundle();
        if (!empty($orderBundle)) {
            $this->setOrderParams('orderBundle', Json::encode($orderBundle));
        }

        $request = Json::decode(file_get_contents($config->getRegisterURL() . '?' . http_build_query($this->orderParams)));

        return $request;
    }

    public function result()
    {
        try {
            $getResult = $this->request();
            if (intval($getResult['errorCode'])) {
                $this->errorCode = intval($getResult['errorCode']);
                throw new ErrorException($getResult['errorMessage']);
            } else {
                if ($getResult['orderId'] && $getResult['formUrl']) {
                    $this->sberBankOrderId = $getResult['orderId'];
                    $this->sberBankOrderFormURL = $getResult['formUrl'];
                    throw new RegisterOrderSuccessException();
                }
            }
        } catch (ErrorException $e) {
            $e->setOrderId($this->orderId);
            $e->setErrorCode($this->errorCode);
            $e->setOperationType($this->operationType);
            $e->log();
        } catch (RegisterOrderSuccessException $e) {
            $e->setUserId($this->orderParams['clientId']);
            $e->setOrderId($this->orderId);
            $e->setResultOrderId($this->sberBankOrderId);
            $e->setResultOrderFormURL($this->sberBankOrderFormURL);
            $e->setSessionTimeoutSecs($this->sessionTimeoutSecs);
            $e->log();
        }
    }
} 