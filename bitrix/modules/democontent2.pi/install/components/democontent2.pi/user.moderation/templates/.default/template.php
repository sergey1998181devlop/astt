<?php
/**
 * Date: 01.10.2019
 * Time: 14:40
 * User: Ruslan Semagin
 * Company: PIXEL365
 * Web: https://pixel365.ru
 * Email: pixel.365.24@gmail.com
 * Phone: +7 (495) 005-23-76
 * Skype: pixel365
 * Product Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 * Use of this code is allowed only under the condition of full compliance with the terms of the license agreement,
 * and only as part of the product.
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}
$chatEnabled = false;
if (intval(\Bitrix\Main\Config\Option::get(DSPI, 'chatEnabled'))) {
    $chatEnabled = true;
}
$currencyName = \Bitrix\Main\Config\Option::get(DSPI, 'currency_name');
?>
    <div class="page-content">
        <div class="wrapper">
            <div class="page-title">
                <h1>
                    <?= Loc::getMessage('MODERATION_COMPONENT_TITLE') ?>
                </h1>
            </div>
            <div class="row">
                <div class="col-sm-4 col-md-3 col-xxs-12">
                    <?php
                    $APPLICATION->IncludeComponent(
                        'democontent2.pi:user.menu',
                        ''
                    );
                    ?>
                </div>
                <div class="col-sm-8 col-xxs-12">
                    <div class="white-block">
                        <div class="tasks-list">
                            <?php
                            $i = 0;

                            foreach ($arResult['ITEMS'] as $key => $item) {
                                if (!isset($item['USER']['NAME']) && !strlen($item['USER']['NAME'])) {
                                    continue;
                                }

                                $i++;
                                $arButtons = \CIBlock::GetPanelButtons(
                                    $item['UF_IBLOCK_ID'],
                                    $item['UF_ITEM_ID'],
                                    0,
                                    ["SECTION_BUTTONS" => false, "SESSID" => false]
                                );

                                $arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
                                $arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

                                $this->AddEditAction(
                                    $item['UF_ITEM_ID'],
                                    $arItem['EDIT_LINK'],
                                    CIBlock::GetArrayByID(
                                        $item['UF_IBLOCK_ID'],
                                        "ELEMENT_EDIT"
                                    )
                                );
                                $this->AddDeleteAction(
                                    $item['UF_ITEM_ID'],
                                    $arItem['DELETE_LINK'],
                                    CIBlock::GetArrayByID(
                                        $item['UF_IBLOCK_ID'],
                                        "ELEMENT_DELETE"
                                    ),
                                    [
                                        "CONFIRM" => Loc::getMessage('CONFIRM_DELETE')
                                    ]
                                );

                                if ($item['USER']['PERSONAL_PHOTO'] > 0) {
                                    $ava = CFile::ResizeImageGet($item['USER']['PERSONAL_PHOTO'],
                                        [
                                            'width' => 50,
                                            'height' => 50
                                        ],
                                        BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                        true
                                    );
                                }
                                $quickly = 0;
                                if (count($item['UF_QUICKLY_END']) > 0 && strtotime($item['UF_QUICKLY_END']) > strtotime(date('Y-m-d H:i'))) {
                                    $quickly = 1;
                                }
                                ?>
                                <div class="task-preview <?= ($quickly) ? 'urgent' : '' ?>"
                                     id="<?= $this->GetEditAreaId($item['UF_ITEM_ID']) ?>">
                                    <div class="tbl tbl-fixed">
                                        <div class="tbc">
                                            <div class="ttl medium">
                                                <? if ($quickly): ?>
                                                    <span class="fire">
                                                <svg class="icon icon_fire">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#fire"></use>
                                                </svg>
                                            </span>
                                                <? endif ?>
                                                <?= $item['UF_NAME'] ?>
                                            </div>
                                            <div class="desc">
                                                <?= substr($item['UF_DESCRIPTION'], 0, 215) ?>
                                                <? if (strlen($item['UF_DESCRIPTION']) > 215): ?>
                                                    ...
                                                <? endif ?>
                                            </div>
                                            <div class="btm clearfix">
                                                <div class="left">
                                                    <div class="date-box">
                                                        <svg class="icon icon_time">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#time"></use>
                                                        </svg>
                                                        <span title="<?= date('Y-m-d H:i', strtotime($item['UF_CREATED_AT'])) ?>"
                                                              class="timestamp"></span>
                                                    </div>
                                                    <div class="location-box">
                                                        <svg class="icon icon_location">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#location"></use>
                                                        </svg>
                                                        <?= $item['CITY_NAME'] ?>
                                                    </div>
                                                </div>
                                                <div class="right">
                                                    <div class="responses-box left">
                                                        <svg class="icon icon_comment">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#comment"></use>
                                                        </svg>
                                                        <?php
                                                        echo \Democontent2\Pi\Utils::declension(
                                                            $item['UF_RESPONSE_COUNT'],
                                                            array(
                                                                Loc::getMessage('TASKS_LIST_RESPONSE_1'),
                                                                Loc::getMessage('TASKS_LIST_RESPONSE_2'),
                                                                Loc::getMessage('TASKS_LIST_RESPONSE_3')
                                                            )
                                                        );
                                                        ?>
                                                    </div>
                                                    <div class="views-box left">
                                                        <svg class="icon icon_eye">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#eye"></use>
                                                        </svg>
                                                        <?= $item['UF_COUNTER'] ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tbc tbc-info">
                                            <div class="price-box">
                                                <? if ($item['UF_PRICE'] > 0): ?>
                                                    <?= \Democontent2\Pi\Utils::price($item['UF_PRICE']) ?>
                                                    <div class="currency"><?= $currencyName ?></div>
                                                <? else: ?>
                                                    <span>
                                                        <?= Loc::getMessage('TASKS_LIST_PRICE_CONTRACT') ?>
                                                    </span>
                                                <? endif ?>

                                            </div>
                                            <div class="user-box">
                                                <div class="tbl tbl-fixed">
                                                    <div class="tbc">
                                                        <div class="ava">
                                                            <?php
                                                            if ($chatEnabled) {
                                                                ?>
                                                                <div class="status-indicator uid<?= \Democontent2\Pi\Utils::getChatId($item['USER']['ID']) ?>"></div>
                                                                <?php
                                                            }
                                                            ?>
                                                            <? if ($item['USER']['PERSONAL_PHOTO'] > 0): ?>
                                                                <div class="object-fit-container">
                                                                    <img src="<?= $ava['src'] ?>" alt=""
                                                                         data-object-fit="cover"
                                                                         data-object-position="50% 50%"/>
                                                                </div>
                                                            <? else: ?>
                                                                <div class="object-fit-container">
                                                                    <div class="name-prefix"
                                                                         style="background: <?= strlen($item['USER']['NAME']) ? \Democontent2\Pi\Utils::userBgColor($item['USER']['NAME']) : '#ffffff' ?>">
                                                                        <?= strlen($item['USER']['NAME'])?\Democontent2\Pi\Utils::userNamePrefix($item['USER']['NAME'], $item['USER']['LAST_NAME']):'' ?>
                                                                    </div>
                                                                </div>
                                                            <? endif ?>
                                                        </div>
                                                    </div>
                                                    <div class="tbc">
                                                        <div class="name medium">
                                                            <?= $item['USER']['NAME'] ?>
                                                            <?= $item['USER']['LAST_NAME'] ?>
                                                        </div>
                                                        <div class="feedback">
                                                            <div class="assessment-box">
                                                                <svg class="icon icon_star-empty">
                                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-empty"></use>
                                                                </svg>
                                                                <svg class="icon icon_star-empty">
                                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-empty"></use>
                                                                </svg>
                                                                <svg class="icon icon_star-empty">
                                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-empty"></use>
                                                                </svg>
                                                                <svg class="icon icon_star-empty">
                                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-empty"></use>
                                                                </svg>
                                                                <svg class="icon icon_star-empty">
                                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-empty"></use>
                                                                </svg>
                                                                <div class="assessment-in"
                                                                     style="width:<?= $item['CURRENT_RATING']['percent'] * 1 ?>%">
                                                                    <svg class="icon icon_star-full">
                                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-full"></use>
                                                                    </svg>
                                                                    <svg class="icon icon_star-full">
                                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-full"></use>
                                                                    </svg>
                                                                    <svg class="icon icon_star-full">
                                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-full"></use>
                                                                    </svg>
                                                                    <svg class="icon icon_star-full">
                                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-full"></use>
                                                                    </svg>
                                                                    <svg class="icon icon_star-full">
                                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-full"></use>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                            <div class="likes">
                                                        <span class="like">
                                                            <svg class="icon icon_thumbs-o-up">
                                                              <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#thumbs-o-up"></use>
                                                            </svg>
                                                            <?= $item['CURRENT_RATING']['positive'] * 1 ?>
                                                        </span>
                                                                <span class="dislike">
                                                            <svg class="icon icon_thumbs-o-down">
                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#thumbs-o-down"></use>

                                                            </svg>
                                                            <?= $item['CURRENT_RATING']['negative'] * 1 ?>
                                                        </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <a class="lnk-abs"
                                                   href="<?= SITE_DIR ?>user/<?= $item['USER']['ID'] ?>/"></a>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="lnk-abs"
                                       href="<?= SITE_DIR . '' . $item['UF_IBLOCK_TYPE'] . '/' . $item['UF_IBLOCK_CODE'] . '/' . $item['UF_CODE'] . '/' ?>"></a>
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
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}

