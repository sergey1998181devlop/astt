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

class SmsRu implements ISmsGate
{
    const BASE_URL = 'https://sms.ru/sms/send';

    private $text = '';
    private $phone = '';
    private $error = 'no';
    private $hasError = false;
    private $log = null;
    private $apiKey = '';
    private $smsId = '';
    private $smsCost = '';
    private $smsCount = 0;

    public function __construct()
    {
        $this->apiKey = Option::get(DSPI, 'smsRuApiKey');
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

    private function error()
    {
        switch ($this->error) {
            case 'no':
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
        $params = [
            'api_id' => $this->apiKey,
            'to' => $this->phone,
            'msg' => $this->text,
            'json' => 1
        ];

        try {
            $send = Json::decode(file_get_contents(self::BASE_URL . '?' . http_build_query($params)));
            if (isset($send['STATUS'])) {
                if ($send['STATUS'] == 'OK') {
                    if (isset($send['status_code'])) {
                        if ($send['status_code'] == 100) {
                            if (isset($send['sms'][$this->phone])) {
                                switch ($send['sms'][$this->phone]['status']) {
                                    case 'OK':
                                        $this->smsId = $send['sms'][$this->phone]['sms_id'];
                                        break;
                                    case 'ERROR':
                                        $this->error = $send['sms'][$this->phone]['status_code'] . '-' . $send['sms'][$this->phone]['status_text'];
                                        break;
                                }
                            }
                        }
                    }
                }
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
                'sms.ru'
            );
            $this->log = $log->getLog();
        } catch (ArgumentException $e) {
        }
    }
}