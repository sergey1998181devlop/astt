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
    <div class="location-block left">
        <a class="head" href="#popup-location" data-fancybox>
            <svg class="icon icon_compass">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#compass"></use>
            </svg>
            <?php
            if ($arResult['ANY_CITY']) {
                echo Loc::getMessage('ANY_CITY2');
            } else {
                echo $arResult['CURRENT_CITY_NAME'];
            }
            ?>
        </a>
    </div>

    <div class="popup popup-location" id="popup-location">
        <div class="popup-body">
            <div class="choice-list">
                <div class="tabs-wrap">
                    <div class="tab active" id="tab-choice1">
                        <ul>
                            <li>
                                <a href="<?= $APPLICATION->GetCurPage(false) ?>?skipCity=1">
                                    <?= Loc::getMessage('ANY_CITY') ?>
                                </a>
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
                                        <a href="<?= $APPLICATION->GetCurPage(false) ?>?changeCity=<?= $item['id'] ?>"><?= $item['name'] ?></a>
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
                                            <a href="<?= $APPLICATION->GetCurPage(false) ?>?changeCity=<?= $item['id'] ?>"><?= $item['name'] ?></a>
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
                                            <a href="<?= $item['ID'] ?>"><?= $item['NAME'] ?></a>
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
    <script>
        var
            __popupLocationCityPath = '<?= $APPLICATION->GetCurPage(false) ?>?changeCity=',
            __popupLocationAjaxPath = '<?=$this->GetFolder()?>/ajax.php';
    </script>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}