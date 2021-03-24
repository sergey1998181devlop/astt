<?php
/**
 * User: Aleksandr Miranovich
 * Email: miranovich664@gmail.com
 * Date: 13.01.2019
 * Time: 09:48
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery.cookie.js');

use Bitrix\Main\Localization\Loc;
if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}
?>
<div id="message-cookie" style="display: none;" class="message-cookie">
    <div class="wrapper text-center">
        <a href="#" class="close">
            <svg class="icon icon_compass">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#close"></use>
            </svg>
        </a>
        <?=Loc::getMessage('COOKIE_COMPONENT_TEXT')?>
    </div>
</div>
<?
if (method_exists($this, 'createFrame')) {
    $frame->end();
}
?>
