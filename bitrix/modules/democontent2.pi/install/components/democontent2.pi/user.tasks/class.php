<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 18.01.2019
 * Time: 17:19
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class UserTasksComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;

        if ($USER->IsAuthorized()) {
            $items = new \Democontent2\Pi\Iblock\Items();
            $items->setUserId(intval($USER->GetID()));
            $items->setTtl(0);

            $this->arResult['ITEMS'] = $items->getByUser();
            $this->arResult['EXECUTOR_ITEMS'] = $items->getWhereExecutor();

            $this->includeComponentTemplate();
        } else {
            LocalRedirect(SITE_DIR, true);
        }
    }
}