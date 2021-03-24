<?php
/**
 * User: Sergey nazarkin
 * Email: nazarkin2017@mail.ru
 * Date: 08.01.2021
 * Time: 13:09
 */

namespace Democontent2\Pi;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Democontent2\Pi\I\ISms;
use Democontent2\Pi\Sms\Intis;
use Democontent2\Pi\Sms\SmsC;
use Democontent2\Pi\Sms\SmsRu;
use Democontent2\Pi\Sms\SmsUslugi;

class Sms implements ISms
{
    private $phone = '';
    private $text = '';
    private $messageId = 0;
    private $messageReplace = [];
    private $error = '';

    /**
     * @param $messageReplace
     */
    public function setMessageReplace($messageReplace)
    {
        $this->messageReplace = $messageReplace;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param int $messageId
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = Utils::validatePhone($phone);
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        if (Application::isUtfMode()) {
            $this->text = $text;
        } else {
            $this->text = \CUtil::translit($text, 'ru', Utils::smsTranslitRules());
        }
    }

    public function make()
    {
        if ($this->phone) {
            switch (Option::get(DSPI, 'defaultSmsGate')) {
                case 'sms16.ru':
                    $sms = new Intis();
                    break;
                case 'sms-uslugi.ru':
                    $sms = new SmsUslugi();
                    break;
                case 'sms.ru':
                    $sms = new SmsRu();
                    break;
                case 'smsc.ru':
                    $sms = new SmsC();
                    break;
                default:
                    $sms = null;
            }

            if ($sms !== null && is_object($sms)) {
                $sms->setPhone($this->phone);

                if ($this->messageId > 0) {
                    $message = Loc::getMessage('SMS_' . $this->messageId);

                    if (count($this->messageReplace) > 0) {
                        $message = Loc::getMessage(
                            'SMS_' . $this->messageId,
                            $this->messageReplace
                        );
                    }

                    if (Application::isUtfMode()) {
                        $this->text = $message . $this->text;
                    } else {
                        $this->text = \CUtil::translit($message, 'ru', Utils::smsTranslitRules()) . $this->text;
                    }
                }

                if ($this->text) {
                    $sms->setText($this->text);
                    $sms->make();
                }

                $this->error = $sms->getError();

                if ($sms->getLog() !== null) {
                    $event = new EventManager(
                        'smsLog',
                        $sms->getLog()
                    );
                    $event->execute();
                }
            }
        }
    }
}