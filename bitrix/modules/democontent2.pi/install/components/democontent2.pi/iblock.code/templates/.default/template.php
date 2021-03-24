<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.01.2019
 * Time: 12:24
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$currencyName = \Bitrix\Main\Config\Option::get(DSPI, 'currency_name');

if (method_exists($this, 'setFrameMode')) {
    $this->setFrameMode(true);
}

$chatEnabled = false;
if (intval(\Bitrix\Main\Config\Option::get(DSPI, 'chatEnabled'))) {
    $chatEnabled = true;
}
?>
<div class="page-content">
    <div class="wrapper">
        <div class="page-title">
            <h1>
                <?= ((strlen($arResult['META']['UF_H1'])) ? $arResult['META']['UF_H1'] : $arResult['META']['UF_TITLE']) ?>
            </h1>
        </div>
        <div class="row">
            <div class="col-sm-4 col-md-3 col-xxs-12">
                <?php
                $APPLICATION->IncludeComponent(
                    'democontent2.pi:catalog.menu',
                    '',
                    array(
                        'IBLOCK_TYPE' => (isset($arParams['iBlockType'])) ? $arParams['iBlockType'] : '',
                        'IBLOCK_CODE' => (isset($arParams['iBlockCode'])) ? $arParams['iBlockCode'] : '',
                    )
                );
                ?>

            </div>
            <div class="col-sm-12 col-md-9 col-xxs-12">
                <div class="white-block">
                    <?php
                    $APPLICATION->IncludeComponent(
                        'democontent2.pi:tasks.filter',
                        '',
                        array()
                    );
                    ?>
                    <? if (empty($arResult['ITEMS'])): ?>
                        <div class="alert alert-info">
                            <?= Loc::getMessage('TASKS_LIST_EMPTY') ?>
                        </div>
                    <? else: ?>
                        <div class="sorty-panel">
                            <ul>
                                <li<?= (($arResult['ORDER'] == 'new') ? ' class="active"' : false) ?>>
                                    <a href="<?= $arResult['CURRENT_URL'] ?>">
                                        <?= Loc::getMessage('LIST_SORT_DATE_UP') ?>
                                    </a>
                                </li>
                                <li<?= (($arResult['ORDER'] == 'old') ? ' class="active"' : false) ?>>
                                    <a href="<?= $arResult['CURRENT_URL'] ?>?order=old">
                                        <?= Loc::getMessage('LIST_SORT_DATE_DOWN') ?>
                                    </a>
                                </li>
                                <li<?= (($arResult['ORDER'] == 'cheap') ? ' class="active"' : false) ?>>
                                    <a href="<?= $arResult['CURRENT_URL'] ?>?order=cheap">
                                        <?= Loc::getMessage('LIST_SORT_PRICE_DOWN') ?>
                                    </a>
                                </li>
                                <li<?= (($arResult['ORDER'] == 'expensive') ? ' class="active"' : false) ?>>
                                    <a href="<?= $arResult['CURRENT_URL'] ?>?order=expensive">
                                        <?= Loc::getMessage('LIST_SORT_PRICE_UP') ?>
                                    </a>
                                </li>
                                <li<?= (($arResult['ORDER'] == 'popular') ? ' class="active"' : false) ?>>
                                    <a href="<?= $arResult['CURRENT_URL'] ?>?order=popular">
                                        <?= Loc::getMessage('LIST_SORT_POPULAR') ?>
                                    </a>
                                </li>
                                <li<?= (($arResult['ORDER'] == 'moreFeedback') ? ' class="active"' : false) ?>>
                                    <a href="<?= $arResult['CURRENT_URL'] ?>?order=moreFeedback">
                                        <?= Loc::getMessage('LIST_SORT_MORE_FEEDBACK') ?>
                                    </a>
                                </li>
                                <li<?= (($arResult['ORDER'] == 'lessFeedback') ? ' class="active"' : false) ?>>
                                    <a href="<?= $arResult['CURRENT_URL'] ?>?order=lessFeedback">
                                        <?= Loc::getMessage('LIST_SORT_LESS_FEEDBACK') ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="tasks-list">
                            <?php
                            $i = 0;
                            foreach ($arResult['ITEMS'] as $key => $item) {
                                $i++;
                                $arButtons = \CIBlock::GetPanelButtons(
                                    $item['UF_IBLOCK_ID'],
                                    $item['UF_ITEM_ID'],
                                    0,
                                    array("SECTION_BUTTONS" => false, "SESSID" => false)
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
                                    array(
                                        "CONFIRM" => Loc::getMessage('CONFIRM_DELETE')
                                    )
                                );

                                if ($item['USER']['PERSONAL_PHOTO'] > 0) {
                                    $ava = CFile::ResizeImageGet($item['USER']['PERSONAL_PHOTO'],
                                        array(
                                            'width' => 50,
                                            'height' => 50
                                        ),
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
                                                        <span title="<?= date('Y-m-d H:i', strtotime($item['UF_DATE_CREATE'])) ?>"
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
                                                                    <img src="<?= $ava['src'] ?>"
                                                                            alt="" data-object-fit="cover"
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
                                                                     style="width:<?= $item['RATING']['percent'] * 1 ?>%">
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
                                                            <?= $item['RATING']['positive'] * 1 ?>
                                                        </span>
                                                                <span class="dislike">
                                                            <svg class="icon icon_thumbs-o-down">
                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#thumbs-o-down"></use>

                                                            </svg>
                                                                    <?= $item['RATING']['negative'] * 1 ?>
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
                                if ($i == 1) {
                                    $APPLICATION->IncludeComponent(
                                        "bitrix:main.include", "",
                                        array(
                                            "AREA_FILE_SHOW" => "file",
                                            "PATH" => SITE_TEMPLATE_PATH . "/inc/adv/list.php",
                                            "EDIT_TEMPLATE" => "include_areas_template.php",
                                            'MODE' => 'html'
                                        ),
                                        false
                                    );
                                }
                            }
                            ?>
                        </div>
                        <?
                        $APPLICATION->IncludeComponent(
                            'democontent2.pi:pagination',
                            '',
                            array(
                                'TOTAL' => $arResult['TOTAL'],
                                'LIMIT' => $arResult['LIMIT'],
                                'CURRENT_PAGE' => $arResult['CURRENT_PAGE'],
                                'URL' => $APPLICATION->GetCurPage(false)
                            )
                        );
                        ?>
                    <? endif ?>

                </div>
            </div>
        </div>
    </div>
</div>
