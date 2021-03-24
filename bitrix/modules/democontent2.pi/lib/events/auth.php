<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 07.01.2019
 * Time: 16:09
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Events;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Cookie;
use Democontent2\Pi\Balance\Account;
use Democontent2\Pi\Iblock\TemporaryTask;

class Auth
{
    /**
     * @param $user
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function handler($user)
    {
        if (isset($user['user_fields']['ID'])) {
            $balance = new Account(intval($user['user_fields']['ID']));
            $balance->create();

            $request = Application::getInstance()->getContext()->getRequest();
            if ($request->getCookie('nonameUser') && strlen($request->getCookie('nonameUser')) == 32) {
                $hash = $request->getCookie('nonameUser');
                self::clearCookie();

                $temporaryTask = new TemporaryTask();
                $temporaryTask->setHash($hash);
                $task = $temporaryTask->get();

                if (count($task) > 0) {
                    $restoreId = $temporaryTask->restore(intval($user['user_fields']['ID']));
                    if ($restoreId > 0) {
                        if ($temporaryTask->getPaymentRedirect()) {
                            LocalRedirect($temporaryTask->getPaymentRedirect(), true);
                        }
                    }
                }
            }
        } else {
            if (intval($user['ID']) > 0) {
                $balance = new Account(intval($user['ID']));
                $balance->create();

                $request = Application::getInstance()->getContext()->getRequest();
                if ($request->getCookie('nonameUser') && strlen($request->getCookie('nonameUser')) == 32) {
                    $hash = $request->getCookie('nonameUser');
                    self::clearCookie();

                    $temporaryTask = new TemporaryTask();
                    $temporaryTask->setHash($hash);
                    $task = $temporaryTask->get();

                    if (count($task) > 0) {
                        $restoreId = $temporaryTask->restore(intval($user['ID']));
                        if ($restoreId > 0) {
                            if ($temporaryTask->getPaymentRedirect()) {
                                LocalRedirect($temporaryTask->getPaymentRedirect(), true);
                            }
                        }
                    }
                }
            }
        }
    }

    private static function clearCookie()
    {
        try {
            $host = explode(':', Application::getInstance()->getContext()->getServer()->getHttpHost());
            $cookie = new Cookie('nonameUser', '');
            $cookie->setDomain($host[0]);
            Application::getInstance()->getContext()->getResponse()->addCookie($cookie);
            Application::getInstance()->getContext()->getResponse()->flush('');
        } catch (SystemException $e) {
        }
    }
}