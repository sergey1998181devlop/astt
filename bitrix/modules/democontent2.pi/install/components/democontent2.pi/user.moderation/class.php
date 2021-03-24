<?php
/**
 * Date: 01.10.2019
 * Time: 14:40
 * User: Ruslan Semagin
 * Company: PIXEL365
 * Web: https://pixel365.ru
 * Email: pixel.365.24@gmail.com
 * Phone: +7 (495) 005-23-76
 * Skype: pixel365
 * Product Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 * Use of this code is allowed only under the condition of full compliance with the terms of the license agreement,
 * and only as part of the product.
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class UserModerationComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        if (!$USER->IsAdmin()) {
            LocalRedirect(SITE_DIR, true);
        }

        $items = new \Democontent2\Pi\Iblock\Items();
        $this->arResult['ITEMS'] = $items->moderation();

        $this->includeComponentTemplate();
    }
}
