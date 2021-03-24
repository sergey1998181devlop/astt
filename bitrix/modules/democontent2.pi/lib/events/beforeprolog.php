<?php
/**
 * User: Sergey nazarkin
 * Email: nazarkin2017@mail.ru
 * Date: 08.01.2021
 * Time: 13:09
 */

namespace Democontent2\Pi\Events;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Cookie;

class BeforeProlog
{
    /**
     * @throws SystemException
     */
    public function handler()
    {
        if (defined('SITE_ID')) {
            if (SITE_ID == Option::get('democontent2.pi', 'siteId')) {
                global $USER;

                if (!$USER->IsAuthorized()) {
                    $request = Application::getInstance()->getContext()->getRequest();
                    if (!$request->getCookie('nonameUser') || strlen($request->getCookie('nonameUser')) !== 32) {
                        self::setCookie();
                    }
                }
            }
        }

        return;
    }

    private static function setCookie()
    {
        try {
            $host = explode(':', Application::getInstance()->getContext()->getServer()->getHttpHost());
            $cookie = new Cookie('nonameUser', md5(microtime(true) . randString()));
            $cookie->setDomain($host[0]);
            Application::getInstance()->getContext()->getResponse()->addCookie($cookie);
            Application::getInstance()->getContext()->getResponse()->flush('');
        } catch (SystemException $e) {
        }
    }
}