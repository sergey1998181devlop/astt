<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 25.09.2018
 * Time: 10:34
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class UsersTariffClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        global $APPLICATION;

        if ($USER->IsAuthorized()) {
            $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
            $us = new \Democontent2\Pi\User(intval($USER->GetID()));
            $userParams = $us->get();
            if (intval($userParams['UF_DSPI_EXECUTOR'])) {
                $menu = new \Democontent2\Pi\Iblock\Menu();
                $prices = new \Democontent2\Pi\Iblock\Prices();
                $profilePrices = new \Democontent2\Pi\Profile\Prices();
                $profilePrices->setUserId($USER->GetID());

                $this->arResult['MENU'] = $menu->get();
                $this->arResult['PRICES'] = $prices->get();
                $this->arResult['PROFILE_PRICES'] = $profilePrices->get();

                if ($request->isPost()) {
                    if ($request->getPost('package')) {
                        $packages = [];
                        $sum = 0;

                        if (count($this->arResult['PRICES']) > 0) {
                            $prices = unserialize($this->arResult['PRICES']['UF_DATA']);

                            foreach ($request->getPost('package') as $k => $v) {
                                if (!isset($prices[$k])) {
                                    continue;
                                }

                                if (isset($v[0])) {
                                    if (isset($prices[$k]['package'][0])) {
                                        if (intval($prices[$k]['package'][0]) > 0) {
                                            $sum += intval($prices[$k]['package'][0]);
                                            $packages[$k][0] = intval($prices[$k]['package'][0]);
                                        }
                                    }
                                } else {
                                    foreach ($v as $k_ => $v_) {
                                        if (isset($prices[$k]['package'][$k_])) {
                                            if (intval($prices[$k]['package'][$k_]) > 0) {
                                                $sum += intval($prices[$k]['package'][$k_]);
                                                $packages[$k][$k_] = intval($prices[$k]['package'][$k_]);
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if ($sum > 0 && count($packages) > 0) {
                            $account = new \Democontent2\Pi\Balance\Account($userParams['ID']);
                            $balance = $account->getAmount();

                            $order = new \Democontent2\Pi\Order($userParams['ID']);
                            $order->setType('packages');
                            $order->setSum($sum);
                            $order->setAdditionalParams($packages);

                            if ($balance > 0 && $balance >= $sum) {
                                $order->setDescription(\Bitrix\Main\Localization\Loc::getMessage('USER_SUBSCRIBE_PAYMENT_DESCRIPTION_BALANCE'));
                                if ($order->make(true)) {
                                    $order->setPayed(true);
                                }
                            } else {
                                $order->setDescription(\Bitrix\Main\Localization\Loc::getMessage('USER_SUBSCRIBE_PAYMENT_DESCRIPTION'));
                                $order->make();
                                if ($order->getRedirect()) {
                                    LocalRedirect($order->getRedirect(), true);
                                }
                            }
                        }
                    }

                    LocalRedirect($APPLICATION->GetCurPage(false), true);
                }

                $this->includeComponentTemplate();
            } else {
                //TODO возможно тут нужно подключить другой шаблон и пригласить пользователя стать исполнителем
                LocalRedirect(SITE_DIR, true);
            }
        } else {
            LocalRedirect(SITE_DIR, true);
        }
    }
}