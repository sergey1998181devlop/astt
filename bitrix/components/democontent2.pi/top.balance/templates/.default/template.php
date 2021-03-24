<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.01.2019
 * Time: 08:45
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}

$currencyName = \Bitrix\Main\Config\Option::get(DSPI, 'currency_name');
?>
    <li class="itm-balance">
        <a href="<?= SITE_DIR . 'user/balance/' ?>">
            <svg class="icon icon_wallet">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#wallet"></use>
            </svg>
            <span id="top-balance" class="">
                <?= $arResult['AMOUNT'] ?>
                <?= $currencyName ?>
            </span>
        </a>
    </li>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}