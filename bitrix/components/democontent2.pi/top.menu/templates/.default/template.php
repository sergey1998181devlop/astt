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
    <div class="right-nav right right-nav-menu-top">
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
                    <a href="<?=SITE_DIR?>user/create/" class="userCreateBtn">
                        <span class="icon icon_plus"></span>
                        <span class="hidden-sm hidden-xs hidden-xxs">
                            <?= Loc::getMessage('TOP_MENU_COMPONENT_CREATE') ?>
                        </span>
                    </a>
                </li>

                <li class="find-company">




                                    <div class="input-group md-form form-sm form-2 pl-0 col-md-12">
                                        <input class="form-control my-0 py-1 amber-border amber-border-header-search" type="text" name="searchInpHead" placeholder="Введите номер компании"  aria-label="Search">

                                        <div class="input-group-append input-group-append-modal header-search-company">
                                            <span class="input-group-text amber lighten-3 lighten-3-closed" id="basic-text1">
                                                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
  <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
</svg>


                                            </span>

                                            <span class="input-group-text amber lighten-3 lighten-3-opened" id="basic-text2" style="display: none;">

                                                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-compact-down" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd" d="M1.553 6.776a.5.5 0 0 1 .67-.223L8 9.44l5.776-2.888a.5.5 0 1 1 .448.894l-6 3a.5.5 0 0 1-.448 0l-6-3a.5.5 0 0 1-.223-.67z"/>
                                                    </svg>

                                            </span>
                                        </div>
                                        </input>
                                </div>


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
