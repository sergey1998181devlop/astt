<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.10.2018
 * Time: 18:30
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
if (!count($arResult)) {
    return;
}

if (method_exists($this, 'setFrameMode')) {
    $this->setFrameMode(true);
}

$menu = array();

foreach ($arResult as $menuItem) {
    switch (intval($menuItem['DEPTH_LEVEL'])) {
        case 1:
            $menu[$menuItem['CHAIN'][0]] = array(
                'url' => $menuItem['LINK'],
                'items' => array()
            );
            break;
        case 2:
            $menu[$menuItem['CHAIN'][0]]['items'][$menuItem['TEXT']] = $menuItem['LINK'];
            break;
    }
}

$i = 0;
foreach ($menu as $k => $v) {
    $i++;
    ?>
    <div class="item col-sm-3">
        <div class="title footer-title-newDez"><?= $k ?>
            <div class="arrow hidden-sm hidden-md hidden-lg">
                <svg class="icon icon_angle">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#angle"></use>
                </svg>
            </div>
        </div>
        <div class="mobile-dropdown">
            <nav>
                <ul>
                    <?php
                    foreach ($v['items'] as $k_ => $v_) {
                        ?>
                        <li><a href="<?= $v_ ?>"><?= $k_ ?></a></li>
                        <?php
                    }
                    ?>
                </ul>
            </nav>
        </div>
    </div>
    <?php
    if ($i == 3) {
        break;
    }
}