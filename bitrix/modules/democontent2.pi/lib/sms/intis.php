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
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\I\ISmsGate;

class Intis implements ISmsGate
{
    const BASE_URL = 'https://new.sms16.ru/get/';

    private $apiKey = '';
    private $login = '';
    private $text = '';
    private $phone = '';
    private $timeStamp = '';
    private $sender = '';
    private $return = 'json';
    private $smsId = '';
    private $smsCost = '';
    private $smsCount = 0;
    private $error = 'no';
    private $hasError = false;
    private $log = null;

    /**
     * Intis constructor.
     */
    public function __construct()
    {
        $this->timeStamp = $this->timeStamp();
        $this->sender = Option::get(DSPI, 'intisSender');
        $this->login = Option::get(DSPI, 'intisLogin');
        $this->apiKey = Option::get(DSPI, 'intisApiKey');
    }

    /**
     * @return null
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
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
     * @param string $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @param string $return
     */
    public function setReturn($return)
    {
        $this->return = $return;
    }

    private function signature()
    {
        $params = [
            'sender' => $this->sender,
            'timestamp' => $this->timeStamp,
            'login' => $this->login,
            'phone' => $this->phone,
            'text' => $this->text,
            'return' => $this->return
        ];

        ksort($params);
        reset($params);

        return md5(implode($params) . $this->apiKey);
    }

    private function timeStamp()
    {
        return file_get_contents(self::BASE_URL . 'timestamp.php');
    }

    private function error()
    {
        switch ($this->error) {
            case 'no':
            case '0':
                $result = '';
                $this->hasError = false;
                break;
            case '000':
            case '1':
            case '2':
            case '3':
            case '4':
            case '5':
            case '6':
            case '7':
            case '8':
            case '9':
            case '10':
            case '11':
            case '12':
            case '13':
            case '14':
            case '15':
            case '16':
            case '17':
            case '18':
            case '19':
            case '20':
            case '21':
            case '22':
            case '23':
            case '24':
            case '25':
            case '26':
            case '27':
            case '28':
            case '29':
            case '30':
                $result = $this->error . ': ' . Loc::getMessage('INTIS_ERROR_' . $this->error);
                $this->hasError = true;
                break;
            default:
                $result = Loc::getMessage('INTIS_UNKNOWN_ERROR') . $this->error;
                $this->hasError = true;
        }

        return $result;
    }

    public function make()
    {
        $params = [
            'sender' => $this->sender,
            'timestamp' => $this->timeStamp,
            'login' => $this->login,
            'phone' => $this->phone,
            'text' => $this->text,
            'return' => $this->return,
            'signature' => $this->signature()
        ];

        try {
            $send = Json::decode(file_get_contents(self::BASE_URL . 'send.php?' . http_build_query($params)));
            if (isset($send[0][$this->phone]['error'])) {
                $this->error = $send[0][$this->phone]['error'];
            }

            if (isset($send[0][$this->phone]['id_sms'])) {
                $this->smsId = $send[0][$this->phone]['id_sms'];
            }

            if (isset($send[0][$this->phone]['cost'])) {
                $this->smsCost = $send[0][$this->phone]['cost'];
            }

            if (isset($send[0][$this->phone]['count_sms'])) {
                $this->smsCount = $send[0][$this->phone]['count_sms'];
            }

            $error = $this->error();
            $this->error = $error;

            $params_ = [
                'smsId' => $this->smsId,
                'smsCount' => $this->smsCount,
                'smsCost' => $this->smsCost,
                'text' => $this->text,
                'phone' => $this->phone
            ];

            $log = new Logger(
                array_merge($params, $params_),
                $error,
                'sms16.ru'
            );
            $this->log = $log->getLog();
        } catch (ArgumentException $e) {
        }
    }
}