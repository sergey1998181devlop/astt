<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 25.09.2018
 * Time: 10:34
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class UserMenu extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;

        $cards = new \Democontent2\Pi\Payments\SafeCrow\Cards();
        $us = new \Democontent2\Pi\User(intval($USER->GetID()));
        $account = new \Democontent2\Pi\Balance\Account($USER->GetID());
        $cards->setUserId($USER->GetID());
        $this->arResult['USER'] = $us->get();
        $this->arResult['COMPANY'] = $us->getCompanyManager();
        $this->arResult['CARD'] = $cards->getUserCard();
        $this->arResult['BALANCE'] = $account->getAmount();


        $this->includeComponentTemplate();
    }
}