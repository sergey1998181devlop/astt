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
    <div class="section categories-list-container section-white">
        <div class="wrapper">
            <h2 class="title h2 upper text-center">
                <?= Loc::getMessage('CATALOG_MENU_COMPONENT_TITLE') ?>
            </h2>
            <nav class="categories">
                <ul>
                    <?
                    foreach ($arResult['MENU'] as $key => $item) {
                        ?>
                        <li>
                            <div class="submenu">
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
                                </ul>
                                <div class="all-section">
                                    <a class="btn btn-sm btn-gray" href="<?= SITE_DIR ?>users/<?= $item['code'] ?>/">
                                        <?=Loc::getMessage('CATALOG_MENU_COMPONENT_BTN')?>
                                    </a>
                                </div>
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