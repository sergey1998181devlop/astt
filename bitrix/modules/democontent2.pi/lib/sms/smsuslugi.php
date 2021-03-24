<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 17.09.2018
 * Time: 17:42
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Sms;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\I\ISmsGate;

class SmsUslugi implements ISmsGate
{
    const BASE_URL = 'https://lcab.sms-uslugi.ru/lcabApi/sendSms.php';

    private $text = '';
    private $phone = '';
    private $error = 'no';
    private $hasError = false;
    private $log = null;
    private $login = '';
    private $password = '';
    private $smsId = '';
    private $smsCost = '';
    private $smsCount = 0;
    private $resultDescription = '';

    /**
     * SmsUslugi constructor.
     */
    public function __construct()
    {
        $this->login = Option::get(DSPI, 'smsUslugiLogin');
        $this->password = Option::get(DSPI, 'smsUslugiPassword');
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return null
     */
    public function getLog()
    {
        return $this->log;
    }

    private function error()
    {
        switch ($this->error) {
            case 'no':
            case '1':
                $result = '';
                $this->hasError = false;
                break;
            default:
                $result = $this->error;
                $this->hasError = true;
        }

        return $result;
    }

    public function make()
    {
        $this->smsId = md5(microtime(true) . randString() . uniqid());
        $params = [
            'login' => $this->login,
            'password' => $this->password,
            'to' => $this->phone,
            'smsid' => $this->smsId,
            'txt' => $this->text
        ];

        try {
            $send = Json::decode(file_get_contents(self::BASE_URL . '?' . http_build_query($params)));
            if (isset($send['code'])) {
                $this->error = $send['code'];
            }

            if (isset($send['priceOfSending'])) {
                $this->smsCost = $send['priceOfSending'];
            }

            if (isset($send['colsmsOfSending'])) {
                $this->smsCount = $send['colsmsOfSending'];
            }

            if (isset($send['descr'])) {
                $this->resultDescription = $send['descr'];
            }

            $error = $this->error();
            $this->error = $error;

            $params_ = [
                'smsId' => $this->smsId,
                'smsCount' => $this->smsCount,
                'smsCost' => $this->smsCost,
                'text' => $this->text,
                'phone' => $this->phone,
                'resultDescription' => $this->resultDescription
            ];

            $log = new Logger(
                array_merge($params, $params_),
                $error,
                'sms-uslugi.ru'
            );
            $this->log = $log->getLog();
        } catch (ArgumentException $e) {
        }
    }
}