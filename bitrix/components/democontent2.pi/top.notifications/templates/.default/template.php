<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.01.2019
 * Time: 09:38
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}

?>
    <li class="itm-notification">
        <a href="<?= SITE_DIR . 'user/notifications/' ?>">
            <? if ($arResult['COUNT'] > 0): ?>
                <span class="count">
                    <?=$arResult['COUNT']?>
                </span>
            <? endif ?>
            <svg class="icon icon_alarmclock">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#alarmclock"></use>
            </svg>
            <span class="">
            <?= Loc::getMessage('TOP_NOTIFICATION_COMPONENT_TTL') ?>
        </span>
        </a>
    </li>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}
