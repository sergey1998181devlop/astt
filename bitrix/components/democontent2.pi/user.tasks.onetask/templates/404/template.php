<?php
/**
 * User: Nazarkin Sergey
 * Email: nazarkin2017@mail.ru
 * Date: 07.10.2020
 * Time: 13:07
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}
?>
<div class="wrapper">
    <div class="white-block page-404 text-center">
        <div class="h1"><?= Loc::getMessage('DETAIL_COMPONENT_404_TITLE') ?></div>
        <div class="numbers ultrabold">404</div>
        <div class="text">
            <?= Loc::getMessage('DETAIL_COMPONENT_404_MESSAGE') ?>
        </div>
    </div>
</div>
