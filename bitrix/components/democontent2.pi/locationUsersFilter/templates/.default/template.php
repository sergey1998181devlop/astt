<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.01.2019
 * Time: 13:18
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
if (count($arResult['ALL_ITEMS']) < 2) {
    return;
}

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}

use Bitrix\Main\Localization\Loc;

?>
    <div class="location-block ">
        <a class="head" href="#popup-location-users" data-fancybox>

            <img src="<?= SITE_TEMPLATE_PATH ?>/images/location.png#compass" class="icon icon_compass">
            <?php

                echo Loc::getMessage('ANY_CITY2');

            ?>
        </a>
    </div>

    <div class="popup popup-location popup-location-users-filter" id="popup-location-users">
        <div class="popup-body">
            <div class="choice-list">
                <div class="tabs-wrap">
                    <div class="tab active" id="tab-choice1">
                        <ul>
                            <li>
                                <p class="putcityow" target-city-code="notcity">
                                    <?= Loc::getMessage('ANY_CITY') ?>
                                </p>
                            </li>
                        </ul>
                        <? if (count($arResult['ALL_ITEMS']) > 0 && (!count($arResult['ITEMS']) && !count($arResult['REGIONS']))): ?>
                            <div class="title bold">
                                <?= Loc::getMessage('LOCATION_POPUP_CITY_SELECTION') ?>
                            </div>
                            <ul id="cities">
                                <?
                                foreach ($arResult['ALL_ITEMS'] as $item) {
                                    ?>
                                    <li>
                                        <p  class="putcityow"   target-city-code="<?= $item['id']?>"><?= $item['name'] ?></p>
                                    </li>
                                    <?
                                }
                                ?>
                            </ul>
                        <? else: ?>
                            <? if (count($arResult['ITEMS']) > 0): ?>
                                <div class="title bold">
                                    <?= Loc::getMessage('LOCATION_POPUP_POPULAR_CITIES') ?>
                                </div>
                                <ul id="cities">
                                    <?
                                    foreach ($arResult['ITEMS'] as $item) {
                                        ?>
                                        <li>
                                            <p  class="putcityow"  target-city-code="<?= $item['id']?>"><?= $item['name'] ?></p>
                                        </li>
                                        <?
                                    }
                                    ?>
                                </ul>
                            <? endif ?>

                            <? if (count($arResult['REGIONS']) > 0): ?>
                                <div class="title bold">
                                    <?= Loc::getMessage('LOCATION_POPUP_REGION_SELECTION') ?></div>
                                <ul id="regions">
                                    <?
                                    foreach ($arResult['REGIONS'] as $item) {
                                        ?>
                                        <li>
                                            <p  class="putcityow"  target-city-code="<?= $item['id']?>"  href="<?= $item['ID'] ?>"><?= $item['NAME'] ?></p>
                                        </li>
                                        <?
                                    }
                                    ?>
                                </ul>
                            <? endif ?>
                        <? endif; ?>
                    </div>
                    <div class="tab" id="tab-choice2">
                        <div class="title bold">
                            <a class="back" href="#tab-choice1">
                                <svg class="icon icon_arrow-left">
                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#arrow-left"></use>
                                </svg>
                                <?= Loc::getMessage('LOCATION_POPUP_CITY_SELECTION_BACK') ?>
                            </a>
                        </div>
                        <ul id="cities-regions"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}