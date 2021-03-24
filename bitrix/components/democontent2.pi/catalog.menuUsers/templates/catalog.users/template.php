<?php
/**
 * User: Nazarkin Sergey
 * Email: nazarkin2017@mail.ru
 * Date: 07.10.2020
 * Time: 13:07
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}

?>
    <div class="section categories-list-container section-white">



        <div class="wrapper">

            <nav class="categories">
                <ul>
                    <?
                    foreach ($arResult['MENU'] as $key => $item) {
                        ?>
                        <li>
                            <div class="submenu submenu-newDez">
                                <div class="img-submenu">
                                    <img src="<?=SITE_TEMPLATE_PATH?>/images/<?=$item['code']?>.png">

                                </div>

                                <a href="<?= SITE_DIR ?>users/<?= $item['code'] ?>/">
                                    <span><?= $item['name'] ?></span>
                                </a>
                                <ul>
                                    <?
                                    $i = 0;
                                    foreach ($item['items'] as $key => $subitem) {
                                        $i++;
                                        if ($i < 5) {
                                            ?>
                                            <li>
                                                <a href="<?= SITE_DIR ?>users/<?= $item['code'] ?>/<?= $subitem['code'] ?>/"><?= $subitem['name'] ?></a>
                                            </li>
                                            <?
                                        }
                                    }
                                    ?>
                                    <div class="all-section">
                                        <a class="btn btn-sm btn-gray" href="<?= SITE_DIR ?>users/<?= $item['code'] ?>/">
                                            <?=Loc::getMessage('CATALOG_MENU_COMPONENT_BTN')?>
                                        </a>
                                    </div>
                                </ul>


                            </div>
                        </li>
                        <?
                    }
                    ?>
                </ul>
            </nav>
        </div>
    </div>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}