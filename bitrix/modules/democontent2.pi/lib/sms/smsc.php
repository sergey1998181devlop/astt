<?php
/**
 * Date: 19.08.2019
 * Time: 13:04
 * User: Ruslan Semagin
 * Company: PIXEL365
 * Web: https://pixel365.ru
 * Email: pixel.365.24@gmail.com
 * Phone: +7 (495) 005-23-76
 * Skype: pixel365
 * Product Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 * Use of this code is allowed only under the condition of full compliance with the terms of the license agreement,
 * and only as part of the product.
 */

namespace Democontent2\Pi\Sms;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\I\ISmsGate;

class SmsC implements ISmsGate
{
    const BASE_URL = 'https://smsc.ru/sys/send.php';

    private $login = '';
    private $password = '';
    private $error = 0;
    private $hasError = false;
    private $log = null;
    private $text = '';
    private $phone = '';

    public function __construct()
    {
        $this->login = Option::get(DSPI, 'smsCLogin');
        $this->password = Option::get(DSPI, 'smsCPassword');
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
        if ($this->error > 0) {
            $this->hasError = true;
        }

        return $this->error;
    }

    public function make()
    {
        $smsCost = '';
        $smsCount = 0;
        $smsId = md5(microtime(true) . randString() . uniqid());
        $params = [
            'login' => $this->login,
            'psw' => $this->password,
            'phones' => $this->phone,
            'mes' => $this->text,
            'id' => $smsId,
            'fmt' => 3
        ];

        if (Application::isUtfMode()) {
            $params['charset'] = 'utf-8';
        }

        try {
            $send = Json::decode(file_get_contents(self::BASE_URL . '?' . http_build_query($params)));

            if (isset($send['error_code'])) {
                $this->error = intval($send['error_code']);
            }

            if (isset($send['id'])) {
                if (strlen($send['id'])) {
                    if ($send['id'] !== $smsId) {
                        $smsId = $send['id'];
                    }
                }
            }

            if (isset($send['cost'])) {
                $smsCost = $send['cost'];
            }

            if (isset($send['cnt'])) {
                $smsCount = intval($send['cnt']);
            }

            $params_ = [
                'smsId' => $smsId,
                'smsCount' => $smsCount,
                'smsCost' => $smsCost,
                'text' => $this->text,
                'phone' => $this->phone
            ];

            $log = new Logger(
                array_merge($params, $params_),
                $this->error(),
                'smsc.ru'
            );
            $this->log = $log->getLog();
        } catch (ArgumentException $e) {
        }
    }
}
