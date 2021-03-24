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

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'setFrameMode')) {
    $this->setFrameMode(true);
}
?>

<div class="page-content">
    <div class="wrapper">
        <div class="page-title">
            <h1>
                <?=Loc::getMessage('FAQ_COMPONENT_TTL')?>
            </h1>
        </div>
        <div class="row faq-container" id="sticky-container">
            <div class="col-sm-4 col-md-3 col-xxs-12">
                <div class="white-block sticky-block" data-container="#sticky-container">
                    <div class="filter tabs-head">
                        <ul>
                            <?php
                            $i = 0;
                            foreach ($arResult['ITEMS'] as $key => $item) {
                                $i++;
                                ?>
                                <li class="<?=($i==1)? 'active': ''?>">
                                    <a href="#faq-tab<?=$item['ID']?>">
                                        <?=$item['NAME']?>
                                    </a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-9 col-xxs-12">
                <div class="white-block">
                    <div class="tabs-wrap">
                        <?php
                        $i = 0;
                        foreach ($arResult['ITEMS'] as $key => $item) {
                        $i++;
                            ?>
                                <div class="tab <?=($i==1)? 'active': ''?>" id="faq-tab<?=$item['ID']?>">
                                    <div class="list-questions">
                                    <?php
                                    foreach ($item['ITEMS'] as $key => $itm) {
                                        ?>
                                        <div class="item">
                                            <div class="head semibold">
                                                <div class="icon-wrap">
                                                    <div class="in">
                                                        <svg class="icon icon_angle">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#angle"></use>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <?=$itm['NAME']?>
                                            </div>
                                            <div class="desc">
                                                <?=$itm['DETAIL_TEXT']?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    </div>
                                </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
