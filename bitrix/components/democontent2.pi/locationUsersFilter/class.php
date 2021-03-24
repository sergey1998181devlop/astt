<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.01.2019
 * Time: 13:18
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class LocationFilterClass extends CBitrixComponent
{
    public function executeComponent()
    {
        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $city = new \Democontent2\Pi\Iblock\City();
        $this->arResult['ITEMS'] = $city->getList(1);
        $this->arResult['REGIONS'] = $city->getRegions();
        $this->arResult['CURRENT_CITY_ID'] = 0;
        $this->arResult['CURRENT_CITY_NAME'] = '';
        $this->arResult['CURRENT_CITY_CODE'] = '';
        $this->arResult['ANY_CITY'] = false;
        $this->arResult['ALL_ITEMS'] = $city->getList();

        if ($request->get('skipCity')) {
            $host = explode(':', $context->getServer()->getHttpHost());

            $cookie = new \Bitrix\Main\Web\Cookie('current_city', 0);
            $cookie->setDomain($host[0]);
            $context->getResponse()->addCookie($cookie);
            $context->getResponse()->flush('');

            $cookie = new \Bitrix\Main\Web\Cookie('skipCity', 1);
            $cookie->setDomain($host[0]);
            $context->getResponse()->addCookie($cookie);
            $context->getResponse()->flush('');

            LocalRedirect(SITE_DIR, true);
        } else {
            if (count($this->arResult['ALL_ITEMS']) > 1) {
                if (intval($request->getCookie('current_city'))) {
                    if (isset($this->arResult['ALL_ITEMS'][intval($request->getCookie('current_city'))])) {
                        $this->arResult['CURRENT_CITY_ID'] = intval($request->getCookie('current_city'));
                        $this->arResult['CURRENT_CITY_NAME'] = $this->arResult['ALL_ITEMS'][intval($request->getCookie('current_city'))]['name'];

                        if (!$this->arResult['ALL_ITEMS'][intval($request->getCookie('current_city'))]['default']) {
                            $this->arResult['CURRENT_CITY_CODE'] = $this->arResult['ALL_ITEMS'][intval($request->getCookie('current_city'))]['code'];
                        }
                    }
                }

                if ($request->get('changeCity')) {
                    if (intval($request->get('changeCity')) > 0) {
                        if (isset($this->arResult['ALL_ITEMS'][intval($request->get('changeCity'))])) {
                            $host = explode(':', $context->getServer()->getHttpHost());

                            $cookie = new \Bitrix\Main\Web\Cookie('current_city', intval($request->get('changeCity')));
                            $cookie->setDomain($host[0]);
                            $context->getResponse()->addCookie($cookie);
                            $context->getResponse()->flush('');

                            $cookie = new \Bitrix\Main\Web\Cookie('skipCity', 0);
                            $cookie->setDomain($host[0]);
                            $context->getResponse()->addCookie($cookie);
                            $context->getResponse()->flush('');
                        }
                    }

                    LocalRedirect(SITE_DIR, true);
                }

                if (!intval($request->getCookie('skipCity'))) {
                    if (!strlen($this->arResult['CURRENT_CITY_NAME'])) {
                        foreach ($this->arResult['ALL_ITEMS'] as $item) {
                            if ($item['default']) {
                                $this->arResult['CURRENT_CITY_ID'] = $item['id'];
                                $this->arResult['CURRENT_CITY_NAME'] = $item['name'];
                                $this->arResult['CURRENT_CITY_CODE'] = $item['code'];
                                break;
                            }
                        }
                    }
                } else {
                    $this->arResult['ANY_CITY'] = true;
                }
            } else {
                $this->arResult['ANY_CITY'] = true;
            }

            if (strlen($this->arResult['CURRENT_CITY_CODE']) > 0) {
                define('DSPI_CITY', $this->arResult['CURRENT_CITY_CODE']);
            }
        }

        $this->IncludeComponentTemplate();
    }
}