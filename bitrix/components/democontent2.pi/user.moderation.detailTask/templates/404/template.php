<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.01.2019
 * Time: 16:46
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
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
