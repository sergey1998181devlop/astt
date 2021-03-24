<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 12.01.2019
 * Time: 17:34
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class UserExitClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        if ($USER->IsAuthorized()) {
            $USER->Logout();
        }

        LocalRedirect(SITE_DIR, true);
    }
}