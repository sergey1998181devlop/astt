<?php

/**
 * User: Nazarkin Sergey
 * Email: nazarkin2017@mail.ru
 * Date: 07.10.2020
 * Time: 13:07
 */
class TopNotifications extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;

        if ($USER->IsAuthorized()) {
            $this->arResult['COUNT'] = \Democontent2\Pi\Notifications::getUnreadCount(intval($USER->GetID()));

            $this->includeComponentTemplate();
        } else {
            return;
        }
    }
}