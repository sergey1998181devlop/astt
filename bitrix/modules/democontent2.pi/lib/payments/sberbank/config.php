<?php
/**
 * User: Sergey nazarkin
 * Email: nazarkin2017@mail.ru
 * Date: 08.01.2021
 * Time: 13:09
 */

namespace Democontent2\Pi\Payments\SberBank;

use Bitrix\Main\Config\Option;

class Config
{
    private static $_instance = null;
    protected $registerURL = 'https://securepayments.sberbank.ru/payment/rest/register.do';
    protected $preAuthURL = 'https://securepayments.sberbank.ru/payment/rest/registerPreAuth.do';
    protected $depositURL = 'https://securepayments.sberbank.ru/payment/rest/deposit.do';
    protected $reverseURL = 'https://securepayments.sberbank.ru/payment/rest/reverse.do';
    protected $refundURL = 'https://securepayments.sberbank.ru/payment/rest/refund.do';
    protected $statusURL = 'https://securepayments.sberbank.ru/payment/rest/getOrderStatus.do';
    protected $statusExtendedURL = 'https://securepayments.sberbank.ru/payment/rest/getOrderStatusExtended.do';
    protected $statisticsURL = 'https://securepayments.sberbank.ru/payment/rest/getLastOrdersForMerchants.do';
    protected $userName = '';
    protected $password = '';
    protected $secretKey = '';
    protected $sessionTimeoutSecs = 21600;

    private function __construct()
    {
        $this->userName = Option::get(DSPI, 'sberBankUserName');
        $this->password = Option::get(DSPI, 'sberBankPassword');
        $this->secretKey = Option::get(DSPI, 'sberBankSecretKey');
    }

    protected function __clone()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * @return int
     */
    public function getSessionTimeoutSecs()
    {
        return $this->sessionTimeoutSecs;
    }

    /**
     * @return string
     */
    public function getDepositURL()
    {
        return $this->depositURL;
    }

    /**
     * @return string
     */
    public function getPreAuthURL()
    {
        return $this->preAuthURL;
    }

    /**
     * @return string
     */
    public function getRefundURL()
    {
        return $this->refundURL;
    }

    /**
     * @return string
     */
    public function getRegisterURL()
    {
        return $this->registerURL;
    }

    /**
     * @return string
     */
    public function getReverseURL()
    {
        return $this->reverseURL;
    }

    /**
     * @return string
     */
    public function getStatisticsURL()
    {
        return $this->statisticsURL;
    }

    /**
     * @return string
     */
    public function getStatusExtendedURL()
    {
        return $this->statusExtendedURL;
    }

    /**
     * @return string
     */
    public function getStatusURL()
    {
        return $this->statusURL;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

} 