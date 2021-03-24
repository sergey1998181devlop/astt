<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.01.2019
 * Time: 08:44
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class TopBalanceComponentClass extends CBitrixComponent
{
    /**
     * @return mixed|void
     * @throws \Bitrix\Main\LoaderException
     */
    public function executeComponent()
    {
        global $USER;

        if ($USER->IsAuthorized()) {
            $balance = new Democontent2\Pi\Balance\Account(intval($USER->GetID()));
            $this->arResult['AMOUNT'] = \Democontent2\Pi\Utils::price($balance->getAmount());

            $this->includeComponentTemplate();
        } else {
            return;
        }
    }
}