<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.01.2019
 * Time: 09:29
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
?>
    <div class="right-nav right">
        <nav>
            <ul class="clearfix">
                <?php
                $APPLICATION->IncludeComponent(
                    'democontent2.pi:top.auth',
                    '',
                    array()
                );
                $APPLICATION->IncludeComponent(
                    'democontent2.pi:top.balance',
                    '',
                    array()
                );

                if (method_exists($this, 'createFrame')) {
                    $frame = $this->createFrame()->begin();
                }
                ?>
                <? if ($arResult['AUTHORIZED']): ?>

                    <?php
                        $APPLICATION->IncludeComponent(
                            'democontent2.pi:top.notifications',
                            '',
                            array()
                        );
                        $APPLICATION->IncludeComponent(
                            'democontent2.pi:top.chat',
                            '',
                            array()
                        );
                    ?>
                <? endif ?>
                <li class="add-ads">
                    <a href="<?=SITE_DIR?>user/create/">
                        <span class="icon icon_plus"></span>
                        <span class="hidden-sm hidden-xs hidden-xxs">
                            <?= Loc::getMessage('TOP_MENU_COMPONENT_CREATE') ?>
                        </span>
                    </a>
                </li>
                <?
                if (method_exists($this, 'createFrame')) {
                    $frame->end();
                }
                ?>
            </ul>
        </nav>
    </div>
<?
