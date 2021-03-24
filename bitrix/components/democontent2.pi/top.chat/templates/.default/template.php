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

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}
?>
    <li class="itm-message">
        <a href="<?= SITE_DIR ?>user/chat/">
            <svg class="icon icon_chat">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#chat"></use>
            </svg>

            <span class="hidden-sm hidden-xs hidden-xxs">
                <?= Loc::getMessage('TOP_CHAT_COMPONENT_MESSAGES') ?>
            </span>
        </a>
    </li>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}