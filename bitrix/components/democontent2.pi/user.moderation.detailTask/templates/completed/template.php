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

$executor = 0;

foreach ($arResult['RESPONSES'] as $key => $response) {
    if ($response['UF_EXECUTOR']) {
        $executor = $response;
        break;
    }
}
$hasFeedback = 0;
foreach ($arResult['REVIEWS'] as $key => $item) {
    if ($item['UF_FROM'] == $USER->GetID()) {
        $hasFeedback = 1;
        break;
    }
}
$stagesL = count($arResult['STAGES']);

$chatEnabled = false;
if (intval(\Bitrix\Main\Config\Option::get(DSPI, 'chatEnabled'))) {
    $chatEnabled = true;
}
$characteristics = [];
if (strlen($arResult['UF_PROPERTIES']) > 0) {
    $characteristics = unserialize($arResult['UF_PROPERTIES']);
}
?>
    <div class="page-content">
        <div class="wrapper">
            <div class="row task-container " id="sticky-container">
                <div class="col-md-3 col-lg-4 col-xxs-12 pull-right">
                    <div class="user-card-block white-block sticky-block"
                         data-container="#sticky-container">
                        <?php
                        if ($executor) {
                            ?>
                            <div class="title">
                                <?= Loc::getMessage('DETAIL_COMPONENT_BTN_EXECUTOR_TTL') ?>:
                            </div>
                            <div class="user-card-block-in">
                                <div class="tbl tbl-fixed">
                                    <div class="tbc">
                                        <div class="ava">
                                            <?php
                                            if ($chatEnabled) {
                                                ?>
                                                <div class="status-indicator uid<?= \Democontent2\Pi\Utils::getChatId($executor['USER_DATA']['ID']) ?>"></div>
                                                <?php
                                            }
                                            ?>
                                            <? if ($executor['USER_DATA']['PERSONAL_PHOTO'] > 0): ?>
                                                <?

                                                $ava = CFile::ResizeImageGet($executor['USER_DATA']['PERSONAL_PHOTO'],
                                                    array(
                                                        'width' => 150,
                                                        'height' => 150
                                                    ),
                                                    BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                                    true
                                                );
                                                ?>
                                                <div class="object-fit-container">
                                                    <img src="<?= $ava['src'] ?>" alt="" data-object-fit="cover"
                                                         data-object-position="50% 50%"/>
                                                </div>
                                            <? else: ?>
                                                <div class="object-fit-container">
                                                    <div class="name-prefix"
                                                         style="background: <?= strlen($executor['USER']['NAME']) ? \Democontent2\Pi\Utils::userBgColor($executor['USER']['NAME']) : '#ffffff' ?>">
                                                        <?= strlen($executor['USER']['NAME'])?\Democontent2\Pi\Utils::userNamePrefix($executor['USER']['NAME'], $executor['USER']['LAST_NAME']):'' ?>
                                                    </div>
                                                </div>
                                            <? endif ?>
                                        </div>
                                    </div>
                                    <div class="tbc">
                                        <div class="name medium">
                                            <?= $executor['USER_DATA']['NAME'] ?>
                                            <?= $executor['USER_DATA']['LAST_NAME'] ?>
                                        </div>
                                        <div class="user-info">
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
                                                         style="width:<?= $executor['CURRENT_RATING']['percent'] * 1 ?>%">
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
                                                            <?= $executor['CURRENT_RATING']['positive'] * 1 ?>
                                                        </span>
                                                    <span class="dislike">
                                                            <svg class="icon icon_thumbs-o-down">
                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#thumbs-o-down"></use>
                                                            </svg>
                                                        <?= $executor['CURRENT_RATING']['negative'] * 1 ?>
                                                        </span>
                                                </div>
                                            </div>
                                            <? if (count($executor['USER_DATA']['UF_DSPI_CITY'])): ?>
                                                <div class="location-box">
                                                    <svg class="icon icon_location">
                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#location"></use>
                                                    </svg>
                                                    <?= $arResult['CITIES'][$executor['USER_DATA']['UF_DSPI_CITY']]['name'] ?>
                                                </div>
                                            <? endif ?>
                                            <div class="date-box">
                                                <svg class="icon icon_time">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#time"></use>
                                                </svg>
                                                <?= Loc::getMessage('DETAIL_COMPONENT_USER_REG') ?>:
                                                <span title="<?= date('Y-m-d H:i', strtotime($executor['USER_DATA']['DATE_REGISTER'])) ?>"
                                                      class="timestamp"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a class="lnk-abs" href="<?= SITE_DIR ?>user/<?= $executor['USER_DATA']['ID'] ?>/"></a>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="alert alert-danger">
                                <?= Loc::getMessage('DETAIL_COMPONENT_STATUS_3') ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="col-sm-12 col-md-9 col-lg-8 col-xxs-12">
                    <div class="white-block <?= ($quickly) ? 'urgent' : '' ?>">
                        <? if (!$arResult['IS_OWNER']): ?>
                            <div class="ttl-customer hidden-md hidden-lg">
                                <?= Loc::getMessage('DETAIL_COMPONENT_BTN_CUSTOMER_TTL') ?>:
                            </div>
                            <div class="customer">
                                <div class="user-block">
                                    <div class="tbl tbl-fixed">
                                        <div class="tbc">
                                            <div class="ava">
                                                <?php
                                                if ($chatEnabled) {
                                                    ?>
                                                    <div class="status-indicator uid<?= \Democontent2\Pi\Utils::getChatId($arResult['USER']['ID']) ?>"></div>
                                                    <?php
                                                }
                                                ?>
                                                <? if ($arResult['USER']['PERSONAL_PHOTO'] > 0):
                                                    $ava = CFile::ResizeImageGet($arResult['USER']['PERSONAL_PHOTO'],
                                                        array(
                                                            'width' => 50,
                                                            'height' => 50
                                                        ),
                                                        BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                                        true
                                                    );
                                                    ?>
                                                    <div class="object-fit-container">
                                                        <img src="<?= $ava['src'] ?>" alt="" data-object-fit="cover"
                                                                data-object-position="50% 50%"/>
                                                    </div>
                                                <? else: ?>
                                                    <div class="object-fit-container">
                                                        <div class="name-prefix"
                                                             style="background: <?= strlen($arResult['USER']['NAME']) ? \Democontent2\Pi\Utils::userBgColor($arResult['USER']['NAME']) : '#ffffff' ?>">
                                                            <?= strlen($arResult['USER']['NAME'])?\Democontent2\Pi\Utils::userNamePrefix($arResult['USER']['NAME'], $arResult['USER']['LAST_NAME']):'' ?>
                                                        </div>
                                                    </div>
                                                <? endif ?>
                                            </div>
                                        </div>
                                        <div class="tbc">
                                            <div class="name medium">
                                                <?= $arResult['USER']['NAME'] ?>
                                                <?= $arResult['USER']['LAST_NAME'] ?>
                                            </div>
                                            <div class="user-info">
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
                                                             style="width: <?= $arResult['CURRENT_RATING']['percent'] * 1 ?>%">
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
                                            <span class="like" href="#">
                                                <svg class="icon icon_thumbs-o-up">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#thumbs-o-up"></use>
                                                </svg><?= $arResult['CURRENT_RATING']['positive'] * 1 ?></span>
                                                        <span class="dislike" href="#">
                                                <svg class="icon icon_thumbs-o-down">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#thumbs-o-down"></use>
                                                </svg><?= $arResult['CURRENT_RATING']['negative'] * 1 ?></span>
                                                    </div>
                                                </div>
                                                <? if (count($arResult['USER']['UF_DSPI_CITY'])): ?>
                                                    <div class="location-box">
                                                        <svg class="icon icon_location">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#location"></use>
                                                        </svg>
                                                        <?= $arResult['CITIES'][$arResult['USER']['UF_DSPI_CITY']]['name'] ?>
                                                    </div>
                                                <? endif ?>
                                                <div class="date-box">
                                                    <svg class="icon icon_time">
                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#time"></use>
                                                    </svg>
                                                    <span title="<?= date('Y-m-d H:i', strtotime($item['USER_DATA']['DATE_REGISTER'])) ?>"
                                                          class="timestamp"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="<?= SITE_DIR . 'user/' . $arResult['USER']['ID'] ?>/"
                                       class="lnk-abs"></a>
                                </div>
                            </div>
                        <? endif ?>
                        <div class="top-panel">
                            <div class="tbl">
                                <div class="tbc">
                                    <div class="h2 title">
                                        <? if ($arResult['UF_SAFE']): ?>
                                            <span class="security" data-tooltip=""
                                                  data-title="<?= Loc::getMessage('DETAIL_COMPONENT_STAGES_SECURITY_TOOLTIP') ?>">
                                            <svg class="icon icon_shield_doc">
                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#shield-doc"></use>
                                            </svg>
                                        </span>
                                        <? endif ?>
                                        <? if ($quickly): ?>
                                            <span class="fire">
                                                <svg class="icon icon_fire">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#fire"></use>
                                                </svg>
                                            </span>
                                        <? endif ?>
                                        <h1><?= $arResult['UF_NAME'] ?></h1>
                                    </div>
                                </div>
                                <div class="tbc">
                                    <div class="price-box">
                                        <div class="price-ttl">
                                            <?= Loc::getMessage('DETAIL_COMPONENT_PRICE_TTL') ?>
                                        </div>
                                        <? if ($arResult['UF_PRICE'] > 0): ?>
                                            <?= \Democontent2\Pi\Utils::price($arResult['UF_PRICE']) ?>
                                            <div class="currency"><?= $currencyName ?></div>
                                        <? else: ?>
                                            <?php
                                            if (count($arResult['STAGES']) > 0) {
                                                ?>
                                                <span><?= Loc::getMessage('DETAIL_COMPONENT_BUDGET_LONG') ?></span>
                                                <?php
                                            } else {
                                                ?>
                                                <span><?= Loc::getMessage('DETAIL_COMPONENT_PRICE_CONTRACT') ?></span>
                                                <?php
                                            }
                                            ?>
                                        <? endif ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="task-indicators">
                            <div class="itm">
                                <div class="status completed">
                                    <?= Loc::getMessage('DETAIL_COMPONENT_STATUS_3') ?>
                                </div>
                            </div>
                            <? if (!empty($arResult['CITY'])): ?>
                                <div class="itm">
                                    <div class="location-box">
                                        <svg class="icon icon_location">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#location"></use>
                                        </svg>
                                        <?= $arResult['CITY']['name'] ?>
                                    </div>
                                </div>
                            <? endif ?>
                            <div class="itm">
                                <div class="date-box">
                                    <svg class="icon icon_time">
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#time"></use>
                                    </svg>
                                    <span title="<?= date('Y-m-d H:i', strtotime($arResult['UF_TIMESTAMP'])) ?>"
                                          class="timestamp"></span>
                                </div>
                            </div>
                            <div class="itm">
                                <div class="views-box">
                                    <svg class="icon icon_eye">
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#eye"></use>
                                    </svg>
                                    <?php
                                    echo \Democontent2\Pi\Utils::declension(
                                        $arResult['UF_COUNTER'],
                                        array(
                                            Loc::getMessage('DETAIL_COMPONENT_VIEWS_1'),
                                            Loc::getMessage('DETAIL_COMPONENT_VIEWS_2'),
                                            Loc::getMessage('DETAIL_COMPONENT_VIEWS_3')
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                            <div class="itm">
                                <div class="responses-box left">
                                    <svg class="icon icon_comment">
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#comment"></use>
                                    </svg>
                                    <?php
                                    echo \Democontent2\Pi\Utils::declension(
                                        $arResult['UF_RESPONSE_COUNT'],
                                        array(
                                            Loc::getMessage('DETAIL_COMPONENT_RESPONSE_1'),
                                            Loc::getMessage('DETAIL_COMPONENT_RESPONSE_2'),
                                            Loc::getMessage('DETAIL_COMPONENT_RESPONSE_3')
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                        </div>
                        <? if (!empty($arResult['UF_BEGIN_WITH'])): ?>
                            <p>
                        <span class="ttl bold">
                            <?= Loc::getMessage('DETAIL_COMPONENT_DATE_START') ?>:
                        </span>
                                <?= date('d.m.Y H:i', strtotime($arResult['UF_BEGIN_WITH'])) ?>
                            </p>
                        <? endif ?>
                        <? if (!empty($arResult['UF_RUN_UP'])): ?>
                            <p>
                                <span class="ttl bold"><?= Loc::getMessage('DETAIL_COMPONENT_DATE_END') ?>:</span>
                                <?= date('d.m.Y H:i', strtotime($arResult['UF_RUN_UP'])) ?>
                            </p>
                        <? endif ?>
                        <? if ($stagesL > 0): ?>
                            <div class="ttl bold">
                                <?= Loc::getMessage('DETAIL_COMPONENT_STAGES_TITLE') ?>:
                            </div>
                            <table class="table stages-list">
                                <thead>
                                <tr>
                                    <th class="text-left">�</th>
                                    <th class="text-left">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_STAGES_TBL_TH1') ?>
                                    </th>
                                    <th class="text-left">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_STAGES_TBL_TH2') ?>
                                    </th>
                                </tr>
                                </thead>
                                <?php
                                $i = 0;
                                foreach ($arResult['STAGES'] as $STAGE) {
                                    $i++;
                                    ?>
                                    <tr>
                                        <td>
                                            <?= $i ?>
                                        </td>
                                        <td>
                                            <?= $STAGE['UF_NAME'] ?>
                                        </td>
                                        <td class="price">
                                            <? if ($STAGE['UF_PRICE'] > 0): ?>
                                                <?= \Democontent2\Pi\Utils::price($STAGE['UF_PRICE']) ?>
                                                <span class="currency"><?= $currencyName ?></span>
                                            <? else: ?>
                                                <span>
                                            <?= Loc::getMessage('DETAIL_COMPONENT_PRICE_CONTRACT') ?>
                                        </span>
                                            <? endif ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                        <? endif ?>
                        <? if ($arResult['UF_DESCRIPTION']): ?>
                            <div class="ttl bold">
                                <?= Loc::getMessage('DETAIL_COMPONENT_DESC_TITLE') ?>:
                            </div>
                            <div class="descript">
                                <?= TxtToHTML($arResult['UF_DESCRIPTION'], false) ?>
                            </div>
                        <? endif ?>
                        <? if (strlen($arResult['UF_PROPERTIES']) > 0): ?>
                            <div class="characteristics">
                                <table>
                                    <?
                                    foreach ($characteristics as $key => $val) {
                                        ?>
                                        <tr class="leftParams">
                                            <td><span><?= $val['name'] ?></span></td>
                                            <td><?= $val['value'] ?></td>
                                        </tr>
                                        <?
                                    }
                                    ?>
                                </table>
                            </div>
                        <? endif ?>
                        <?php
                        $files = unserialize($arResult['UF_FILES']);
                        $hiddenFiles = unserialize($arResult['UF_HIDDEN_FILES']);
                        if (count($files) > 0) {
                            $images = [];
                            $otherFiles = [];

                            foreach ($files as $key => $file) {
                                $getFile = CFile::GetFromCache($file);

                                if (\Democontent2\Pi\Utils::checkImage($getFile[$file]['FILE_NAME'])) {
                                    $images[$file] = $getFile[$file];
                                } else {
                                    $otherFiles[$file] = $getFile[$file];
                                }
                            }
                            ?>
                            <div class="ttl bold">
                                <?= Loc::getMessage('DETAIL_COMPONENT_FILES_TITLE') ?>:
                            </div>
                            <?php
                            if (count($images) > 0) {
                                ?>
                                <div class="attachment-picts row">
                                    <?php
                                    foreach ($images as $image) {
                                        $imageThumb = CFile::ResizeImageGet(
                                            $image['ID'],
                                            [
                                                'width' => 90,
                                                'height' => 90
                                            ],
                                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                            true
                                        );
                                        ?>
                                        <div class="item">
                                            <a href="/upload/<?= $image['SUBDIR'] ?>/<?= $image['FILE_NAME'] ?>"
                                               data-fancybox="gallery-attachment">
                                                <div class="in">
                                                    <div class="object-fit-container">
                                                        <img src="<?= $imageThumb['src'] ?>" alt="" data-object-fit="cover"
                                                             data-object-position="50% 50%"/>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }

                            if (count($otherFiles) > 0) {
                                ?>
                                <ul class="list-attachment">
                                    <?
                                    foreach ($otherFiles as $file) {
                                        ?>
                                        <li>
                                            <a download target="_blank"
                                               href="/upload/<?= $file['SUBDIR'] ?>/<?= $file['FILE_NAME'] ?>">
                                                <svg class="icon icon_clip">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#clip"></use>
                                                </svg>
                                                <span><?= $file['DESCRIPTION'] ?></span>
                                            </a>
                                        </li>
                                        <?
                                    }
                                    ?>
                                </ul>
                                <?php
                            }
                        }

                        if ((count($hiddenFiles) > 0 && $arResult['UF_USER_ID'] == $USER->GetID())
                            || (count($hiddenFiles) > 0 && count($arResult['MY_OFFER']) && $executor['UF_USER_ID'] == $USER->GetID())) {
                            $images = [];
                            $otherFiles = [];

                            foreach ($hiddenFiles as $key => $file) {
                                $getFile = CFile::GetFromCache($file);

                                if (\Democontent2\Pi\Utils::checkImage($getFile[$file]['FILE_NAME'])) {
                                    $images[$file] = $getFile[$file];
                                } else {
                                    $otherFiles[$file] = $getFile[$file];
                                }
                            }
                            ?>
                            <div class="ttl bold">
                                <?= Loc::getMessage('DETAIL_COMPONENT_HIDDEN_FILES_TITLE') ?>:
                            </div>
                            <?php
                            if (count($images) > 0) {
                                ?>
                                <div class="attachment-picts row">
                                    <?php
                                    foreach ($images as $image) {
                                        $imageThumb = CFile::ResizeImageGet(
                                            $image['ID'],
                                            [
                                                'width' => 90,
                                                'height' => 90
                                            ],
                                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                            true
                                        );
                                        ?>
                                        <div class="item">
                                            <a href="/upload/<?= $image['SUBDIR'] ?>/<?= $image['FILE_NAME'] ?>"
                                               data-fancybox="gallery-attachment">
                                                <div class="in">
                                                    <div class="object-fit-container">
                                                        <img src="<?= $imageThumb['src'] ?>"
                                                             alt="" data-object-fit="cover"
                                                             data-object-position="50% 50%"/>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }

                            if (count($otherFiles) > 0) {
                                ?>
                                <ul class="list-attachment">
                                    <?
                                    foreach ($otherFiles as $file) {
                                        ?>
                                        <li>
                                            <a download target="_blank"
                                               href="/upload/<?= $file['SUBDIR'] ?>/<?= $file['FILE_NAME'] ?>">
                                                <svg class="icon icon_clip">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#clip"></use>
                                                </svg>
                                                <span><?= $file['DESCRIPTION'] ?></span>
                                            </a>
                                        </li>
                                        <?
                                    }
                                    ?>
                                </ul>
                                <?php
                            }
                        }
                        ?>
                        <? if ($executor &&
                            (
                                (!$hasFeedback && $USER->IsAuthorized() && $executor['UF_USER_ID'] == $USER->GetID())
                                || (!$hasFeedback && $USER->IsAuthorized() && $arResult['UF_USER_ID'] == $USER->GetID())
                            )
                        ): ?>
                            <br>
                            <a class="btn btn-green btn-completed" href="#popup-feedback" data-fancybox="">
                                <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_BTN') ?>
                            </a>
                            <div class="popup popup-feedback" id="popup-feedback">
                                <div class="popup-head">
                                    <div class="h2">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_TTL') ?>
                                    </div>
                                </div>
                                <div class="popup-body">
                                    <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="post">
                                        <div class="row">
                                            <div class="col-xxs-12 col-sm-4">
                                                <div class="form-group">
                                                    <input class="radio" type="radio" name="feedbackType"
                                                           id="popup-feedbackType1" checked="checked" value="1"/>
                                                    <label class="form-radio" for="popup-feedbackType1">
                                                        <span class="icon-wrap"></span>
                                                        <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_POSITIVE') ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-xxs-12 col-sm-4">
                                                <div class="form-group">
                                                    <input class="radio" type="radio" name="feedbackType"
                                                           id="popup-feedbackType2" checked="checked" value="2"/>
                                                    <label class="form-radio" for="popup-feedbackType2">
                                                        <span class="icon-wrap"></span>
                                                        <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_NEUTRAL') ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-xxs-12 col-sm-4">
                                                <div class="form-group">
                                                    <input class="radio" type="radio" name="feedbackType"
                                                           id="popup-feedbackType3" value="0"/>
                                                    <label class="form-radio" for="popup-feedbackType3">
                                                        <span class="icon-wrap"></span>
                                                        <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_NEGATIVE') ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="feedback-message">
                                                    <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_MES_LBL') ?>
                                                </label>
                                                <textarea class="form-control required" rows="8" required
                                                          id="feedback-message" name="feedbackMessage"></textarea>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button class="btn-submit btn btn-orange">
                                                <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_BTN') ?>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <? endif ?>
                    </div>
                    <br>
                    <? if (count($arResult['REVIEWS']) > 0): ?>
                        <div class="comments-container">
                            <?php
                            foreach ($arResult['REVIEWS'] as $key => $item) {
                                $who = ($arResult['UF_USER_ID'] == $item['UF_FROM']) ? 'owner' : 'executor';
                                ?>
                                <div class="item  white-block">
                                    <div class="head clearfix bold">
                                        <? if ($who === 'owner'): ?>
                                            <?= Loc::getMessage('DETAIL_COMPONENT_FEEDBACK_CUSTOMER') ?>
                                        <? else: ?>
                                            <?= Loc::getMessage('DETAIL_COMPONENT_FEEDBACK_CONTRACTOR') ?>
                                        <? endif ?>
                                    </div>
                                    <div class="comment-block">
                                        <div class="comment-top">
                                            <?php
                                            switch ($item['UF_RATING']) {
                                                case 0:

                                                    ?>
                                                    <svg class="icon icon_sad">
                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#sad"></use>
                                                    </svg>
                                                    <?php
                                                    break;
                                                case 1:
                                                    ?>
                                                    <svg class="icon icon_smile">
                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#smile"></use>
                                                    </svg>
                                                    <?php
                                                    break;
                                                default:
                                                    ?>
                                                    <svg class="icon icon_neutral">
                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#neutral"></use>
                                                    </svg>
                                                <?php
                                            }
                                            ?>
                                            <div class="date-box">
                                                <svg class="icon icon_time">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#time"></use>
                                                </svg>
                                                <span title="<?= date('Y-m-d H:i', strtotime($item['UF_TEXT_TIME'])) ?>"
                                                      class="timestamp"></span>
                                            </div>

                                        </div>
                                        <div class="descript">
                                            <?= TxtToHTML($item['UF_TEXT'], false) ?>
                                        </div>
                                        <? if ($item['UF_FROM'] == $USER->GetID()): ?>
                                            <form id="feedback-<?= $who ?>" class="hidden" method="post"
                                                  action="<?= $APPLICATION->GetCurPage() ?>">
                                                <div class="row">
                                                    <div class="col-xxs-12 col-sm-4">
                                                        <div class="form-group">
                                                            <input class="radio" type="radio" name="feedbackType"
                                                                   id="popup-<?= $who ?>" checked="checked" value="1"/>
                                                            <label class="form-radio" for="popup-<?= $who ?>">
                                                                <span class="icon-wrap"></span>
                                                                <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_POSITIVE') ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-xxs-12 col-sm-4">
                                                        <div class="form-group">
                                                            <input class="radio" type="radio" name="feedbackType"
                                                                   id="popup-<?= $who ?>2" checked="checked" value="2"/>
                                                            <label class="form-radio" for="popup-<?= $who ?>2">
                                                                <span class="icon-wrap"></span>
                                                                <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_NEUTRAL') ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-xxs-12 col-sm-4">
                                                        <div class="form-group">
                                                            <input class="radio" type="radio" name="feedbackType"
                                                                   id="popup-<?= $who ?>3" value="0"/>
                                                            <label class="form-radio" for="popup-<?= $who ?>3">
                                                                <span class="icon-wrap"></span>
                                                                <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_NEGATIVE') ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="feedback-message-<?= $who ?>">
                                                        <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_MES_LBL') ?>
                                                    </label>
                                                    <textarea class="form-control required" rows="8" required
                                                              id="feedback-message-<?= $who ?>"
                                                              name="feedbackMessage-<?= $who ?>"><?= $item['UF_TEXT'] ?></textarea>
                                                </div>
                                            </form>
                                        <? endif; ?>
                                    </div>
                                    <? if ($item['UF_FROM'] == $USER->GetID()): ?>
                                        <div class="btn-wrap text-right">
                                            <a href="#feedback-<?= $who ?>" class=" btn-edit btn btn-red btn-sm">
                                                <?= Loc::getMessage('DETAIL_COMPONENT_BTN_EDIT') ?>
                                            </a>
                                            <a href="#feedback-<?= $who ?>"
                                               class=" btn-save btn btn-green btn-sm hidden">
                                                <?= Loc::getMessage('DETAIL_COMPONENT_BTN_SAVE') ?>
                                            </a>
                                        </div>
                                    <? endif ?>
                                </div>
                                <br>
                                <?php
                            }
                            ?>
                        </div>
                    <? endif ?>
                </div>
            </div>
        </div>
    </div>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}