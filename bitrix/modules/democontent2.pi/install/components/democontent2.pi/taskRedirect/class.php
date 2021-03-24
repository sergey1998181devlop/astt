<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 17.01.2019
 * Time: 20:26
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class TaskRedirectComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $item = new \Democontent2\Pi\Iblock\Item();
        $item->setItemId(intval($this->arParams['itemId']));
        $item->setIBlockId(intval($this->arParams['iBlockId']));
        $redirect = $item->getItemRedirect();

        if (strlen($redirect) > 0) {
            if ($request->get('roomId')) {
                $redirect .= '#!' . $request->get('roomId');
            }

            LocalRedirect($redirect, true, '301 Moved Permanently');
        } else {
            LocalRedirect(SITE_DIR, true, '301 Moved Permanently');
        }
    }
}