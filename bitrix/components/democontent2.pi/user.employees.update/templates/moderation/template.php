<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 08.10.2018
 * Time: 09:23
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}

use Bitrix\Main\Localization\Loc;


if (method_exists($this, 'createFrame')) {
    $frame->end();
}
?>
<div class="page-content">
    <div class="wrapper">
        <div class="alert alert-success">
            <?=Loc::getMessage('DETAIL_COMPONENT_MODERATION_MESSAGE')?>
        </div>
    </div>
</div>