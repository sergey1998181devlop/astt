<?php
/**
 * User: Nazarkin Sergey
 * Email: nazarkin2017@mail.ru
 * Date: 07.10.2020
 * Time: 13:07
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