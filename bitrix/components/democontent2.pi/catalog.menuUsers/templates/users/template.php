<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.01.2019
 * Time: 13:40
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}

?>
    <a class="btn-filter hidden-md hidden-lg" href="#">
        <svg class="icon icon_filter">
            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#filter"></use>
        </svg>
    </a>
    <nav class="category-nav white-block" id="category-nav">
        <div class="head hidden-md hidden-lg">
            <?= Loc::getMessage('CATALOG_MENU_COMPONENT_TITLE') ?>
            <a class="close" href="#">
                <svg class="icon icon_close">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#close"></use>
                </svg>
            </a>
        </div>
        <ul>
            <?
            foreach ($arResult['MENU'] as $key => $item) {
                ?>
                <li class="<?= (strlen($arResult['IBLOCK_TYPE']) > 0 && $arResult['IBLOCK_TYPE'] === $item['code']) ? 'active' : '' ?>">
                    <a href="<?= SITE_DIR ?>users/<?=$item['code'] ?>/">
                        <?= $item['name'] ?>
                    </a>
                    <? if (strlen($arResult['IBLOCK_TYPE']) > 0 && $arResult['IBLOCK_TYPE'] === $item['code']): ?>
                        <ul>
                            <?
                            foreach ($item['items'] as $key => $subitem) {
                                ?>
                                <li class="<?= (strlen($arResult['IBLOCK_CODE']) > 0 && $arResult['IBLOCK_CODE'] ===  $subitem['code']) ? 'active' : '' ?>">
                                    <a href="<?= SITE_DIR ?>users/<?= $item['code']?>/<?=$subitem['code'] ?>/"><?=$subitem['name']?></a>
                                </li>
                                <?
                            }
                            ?>
                        </ul>
                    <? endif ?>
                </li>
                <?
            }
            ?>
        </ul>
    </nav>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}