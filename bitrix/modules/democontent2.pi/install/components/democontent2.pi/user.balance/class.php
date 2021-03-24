<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 12.01.2019
 * Time: 17:31
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class UserBalanceComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        global $APPLICATION;

        if ($USER->IsAuthorized()) {
            $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

            if ($request->isPost()) {
                if (strlen(\Bitrix\Main\Config\Option::get(DSPI, 'paymentProvider'))) {
                    if ($request->getPost('amount') && intval($request->getPost('amount')) > 0) {
                        $order = new \Democontent2\Pi\Order(intval($USER->GetID()));
                        $order->setType('balance');
                        $order->setSum(intval($request->getPost('amount')));
                        $order->setDescription(
                            \Bitrix\Main\Localization\Loc::getMessage('INTERNAL_ACCOUNT')
                        );
                        $order->make();
                        if ($order->getRedirect()) {
                            LocalRedirect($order->getRedirect(), true);
                        }
                    }
                }

                LocalRedirect($APPLICATION->GetCurPage(false), true);
            }

            $transaction = new \Democontent2\Pi\Balance\Transaction(intval($USER->GetID()));
            $this->arResult['ITEMS'] = $transaction->getList();

            $this->includeComponentTemplate();
        } else {
            LocalRedirect(SITE_DIR, true);
        }
    }
}