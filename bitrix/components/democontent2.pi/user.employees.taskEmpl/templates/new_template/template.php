<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 15.01.2019
 * Time: 10:57
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}
\Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/lib/dropzone/dropzone.css');
\Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/dropzone/dropzone.js');
\Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery-ui.min.js');

$currencyName = \Bitrix\Main\Config\Option::get(DSPI, 'currency_name');
$registrationFee = intval(\Bitrix\Main\Config\Option::get(DSPI, 'registration_fee'));
$maxImageSize = intval(\Bitrix\Main\Config\Option::get(DSPI, 'max_file_size'));
$maxFiles = intval(\Bitrix\Main\Config\Option::get(DSPI, 'max_file_size'));
$safeCrowEnabled = false;
$profile = [];

if (count($arResult['PROFILE']) > 0) {
    if (strlen($arResult['PROFILE']['UF_DATA']) > 0) {
        $profile = unserialize($arResult['PROFILE']['UF_DATA']);
    }
}

if (strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiKey')) > 0
    && strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
    $safeCrowEnabled = true;
}

?>
    <div class="page-content">
    <div class="wrapper">
        <div class="page-title">
            <h1>
                <?= Loc::getMessage('USER_SETTINGS_COMPONENT_H1') ?>
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
                <div class="white-block profile-content">
                    <div class="wrapper">
                        <div class="row task-container " id="sticky-container">

                            <div class="col-sm-12 col-md-12 col-lg-12 col-xxs-12">
                                <? if ($arResult['BLOCKED']): ?>
                                    <div class="alert alert-danger">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_BLOCKED_USER_MES') ?>
                                    </div>
                                    <br>
                                <? endif ?>
                                <div class="white-block<?= ($quickly) ? ($arResult['UF_STATUS'] == 6) ? ' arbitration' : ' urgent' : '' ?>">
                                    <div class="complain">
                                        <div class="complain-item" data-id="<?= $arResult['UF_ID'] ?>"
                                             data-category="<?= $arResult['UF_IBLOCK_ID'] ?>">
                                            <span></span>
                                        </div>
                                    </div>
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
                                            <?
                                            switch ($arResult['UF_STATUS']) {
                                                case 1:
                                                case 4:
                                                    ?>
                                                    <div class="status">
                                                        <?= Loc::getMessage('DETAIL_COMPONENT_STATUS_1') ?>
                                                    </div>
                                                    <?
                                                    break;
                                                case 2:
                                                    ?>
                                                    <div class="status hold-true">
                                                        <?= Loc::getMessage('DETAIL_COMPONENT_STATUS_2') ?>
                                                    </div>
                                                    <?
                                                    break;
                                                case 3:
                                                    ?>
                                                    <div class="status completed">
                                                        <?= Loc::getMessage('DETAIL_COMPONENT_STATUS_3') ?>
                                                    </div>
                                                    <?
                                                    break;
                                                case 6:
                                                    ?>
                                                    <div class="status completed">
                                                        <?= Loc::getMessage('DETAIL_COMPONENT_STATUS_6') ?>
                                                    </div>
                                                    <?
                                                    break;
                                            }
                                            ?>
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
                                                        <?= $i++; ?>
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

                                    <?php
                                    if (count($route)) {
                                        ?>
                                        <div id="route-area">
                                            <div id="map-area"></div>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <? if (strlen($arResult['UF_PROPERTIES']) > 0): ?>
                                        <div class="characteristics">
                                            <table>
                                                <?
                                                foreach ($characteristics as $key => $val) {
                                                    $name = '';

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
                                    <?php
                                    switch ($arResult['UF_STATUS']) {
                                        case 1:
                                        case 4:
                                            if ($USER->IsAuthorized() && isset($arResult['CURRENT_USER']['UF_DSPI_EXECUTOR'])
                                                && intval($arResult['CURRENT_USER']['UF_DSPI_EXECUTOR']) > 0) {
                                                if (!count($arResult['MY_OFFER']) && !$arResult['BLOCKED']) {
                                                    if ($arResult['UF_SAFE']) {
                                                        if (!$arResult['USER_CARD']) {
                                                            ?>
                                                            <div class="btn-wrap">
                                                                <a class="btn btn-green" data-fancybox=""
                                                                   href="#popup-attach-card">
                                                                    <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL') ?>
                                                                </a>
                                                            </div>
                                                            <?php
                                                        } else {
                                                            if (!$arResult['RESPONSE_COST']) {
                                                                ?>
                                                                <div class="btn-wrap">
                                                                    <a class="btn btn-green" data-fancybox=""
                                                                       href="#popup-proposal">
                                                                        <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL') ?>
                                                                    </a>
                                                                </div>
                                                                <?php
                                                            } else {
                                                                if ($arResult['BALANCE'] >= $arResult['RESPONSE_COST']) {
                                                                    ?>
                                                                    <div class="btn-wrap">
                                                                        <a class="btn btn-green" data-fancybox=""
                                                                           href="#popup-proposal">
                                                                            <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL') ?>
                                                                            -
                                                                            <?= \Democontent2\Pi\Utils::price($arResult['RESPONSE_COST']) ?>
                                                                            <?= $currencyName ?>
                                                                        </a>
                                                                    </div>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <div class="btn-wrap">
                                                                        <a class="btn btn-green" data-fancybox=""
                                                                           href="#top-up-balance">
                                                                            <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL') ?>
                                                                            -
                                                                            <?= \Democontent2\Pi\Utils::price($arResult['RESPONSE_COST']) ?>
                                                                            <?= $currencyName ?>
                                                                        </a>
                                                                    </div>
                                                                    <div class="popup popup-top-up-balance" id="top-up-balance">
                                                                        <div class="popup-head">
                                                                            <div class="h2">
                                                                                <?= Loc::getMessage('DETAIL_COMPONENT_POP_TOP_UP_BALANCE') ?>
                                                                            </div>
                                                                            <div class="alert alert-warning">
                                                                                <?= Loc::getMessage('DETAIL_COMPONENT_POP_TOP_UP_BALANCE_INFO') ?>
                                                                            </div>
                                                                            <form action="<?= SITE_DIR ?>user/balance/" method="post">
                                                                                <div class="row">
                                                                                    <div class="col-sm-8 col-xxs-12">
                                                                                        <div class="form-group">
                                                                                            <input id="deposit-amount" required
                                                                                                   name="amount" type="text"
                                                                                                   class="form-control required"
                                                                                                   placeholder="<?= Loc::getMessage('DETAIL_COMPONENT_BALANCE_INPUT_PL') ?>">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-sm-4 col-xxs-12">
                                                                                        <div class="form-group">
                                                                                            <button class="btn-submit btn btn-green">
                                                                                                <?= Loc::getMessage('DETAIL_COMPONENT_BALANCE_BTN') ?>
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        if (!$arResult['RESPONSE_COST']) {
                                                            ?>
                                                            <div class="btn-wrap">
                                                                <a class="btn btn-green" data-fancybox=""
                                                                   href="#popup-proposal">
                                                                    <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL') ?>
                                                                </a>
                                                            </div>
                                                            <?php
                                                        } else {
                                                            if ($arResult['BALANCE'] >= $arResult['RESPONSE_COST']) {
                                                                ?>
                                                                <div class="btn-wrap">
                                                                    <a class="btn btn-green" data-fancybox=""
                                                                       href="#popup-proposal">
                                                                        <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL') ?>
                                                                        -
                                                                        <?= \Democontent2\Pi\Utils::price($arResult['RESPONSE_COST']) ?>
                                                                        <?= $currencyName ?>
                                                                    </a>
                                                                </div>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <div class="btn-wrap">
                                                                    <a class="btn btn-green" data-fancybox=""
                                                                       href="#top-up-balance">
                                                                        <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL') ?>
                                                                        -
                                                                        <?= \Democontent2\Pi\Utils::price($arResult['RESPONSE_COST']) ?>
                                                                        <?= $currencyName ?>
                                                                    </a>
                                                                </div>
                                                                <div class="popup popup-top-up-balance" id="top-up-balance">
                                                                    <div class="popup-head">
                                                                        <div class="h2">
                                                                            <?= Loc::getMessage('DETAIL_COMPONENT_POP_TOP_UP_BALANCE') ?>
                                                                        </div>
                                                                        <div class="alert alert-warning">
                                                                            <?= Loc::getMessage('DETAIL_COMPONENT_POP_TOP_UP_BALANCE_INFO') ?>
                                                                        </div>
                                                                        <form action="<?= SITE_DIR ?>user/balance/" method="post">
                                                                            <div class="row">
                                                                                <div class="col-sm-8 col-xxs-12">
                                                                                    <div class="form-group">
                                                                                        <input id="deposit-amount" required
                                                                                               name="amount" type="text"
                                                                                               class="form-control required"
                                                                                               placeholder="<?= Loc::getMessage('DETAIL_COMPONENT_BALANCE_INPUT_PL') ?>">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-4 col-xxs-12">
                                                                                    <div class="form-group">
                                                                                        <button class="btn-submit btn btn-green">
                                                                                            <?= Loc::getMessage('DETAIL_COMPONENT_BALANCE_BTN') ?>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                            }
                                                        }
                                                    }

                                                }
                                            } else {
                                                if (!$USER->IsAuthorized()) {
                                                    ?>
                                                    <div class="btn-wrap hidden-sm hidden-xxs hidden-xs">
                                                        <a class="btn btn-green" data-fancybox=""
                                                           href="#popup-registration">
                                                            <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL') ?>
                                                        </a>
                                                    </div>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <div class="alert alert-success">
                                                        <?php
                                                        echo Loc::getMessage(
                                                            'DETAIL_COMPONENT_MAKE_EXECUTOR',
                                                            [
                                                                '#SITE_DIR#' => SITE_DIR
                                                            ]
                                                        );
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            break;
                                    }

                                    ?>
                                    <? if (count($arResult['MY_OFFER']) > 0 && !$arResult['BLOCKED'] && $arResult['UF_STATUS'] == 2): ?>
                                        <br>
                                        <a class="btn btn-green btn-completed" href="#popup-feedback" data-fancybox="">
                                            <?= Loc::getMessage('DETAIL_COMPONENT_BTN_COMPLETED') ?>
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

                                    <?php
                                    try {
                                        if (\Bitrix\Main\IO\File::isFileExists(
                                            \Bitrix\Main\IO\Path::normalize(
                                                \Bitrix\Main\Application::getDocumentRoot() . SITE_TEMPLATE_PATH . '/inc/adv/list.php'
                                            )
                                        )) {
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
                                    } catch (\Bitrix\Main\IO\InvalidPathException $e) {
                                    }
                                    ?>
                                </div>
                                <br>
                                <? if (count($arResult['MY_OFFER'])): ?>
                                    <h3>
                                        <?= Loc::getMessage('DETAIL_COMPONENT_YOUR_OFFER') ?>:
                                    </h3>
                                    <div class="white-block">
                                        <div class="date-box">
                                            <svg class="icon icon_time">
                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#time"></use>
                                            </svg>
                                            <span title="<?= date('Y-m-d H:i', strtotime($arResult['MY_OFFER']['UF_CREATED_AT'])) ?>"
                                                  class="timestamp"></span>
                                        </div>
                                        <div class="desc">
                                            <?= TxtToHTML($arResult['MY_OFFER']['UF_TEXT'], false) ?>
                                        </div>
                                        <?
                                        $files = unserialize($arResult['MY_OFFER']['UF_FILES']);
                                        if (count($files) > 0) {
                                            ?>
                                            <div class="ttl bold">
                                                <?= Loc::getMessage('DETAIL_COMPONENT_FILES_TITLE') ?>:
                                            </div>
                                            <ul class="list-attachment">
                                                <?
                                                foreach ($files as $key => $file) {
                                                    $getFile = CFile::GetFromCache($file);
                                                    ?>
                                                    <li>
                                                        <a download target="_blank"
                                                           href="/upload/<?= $getFile[$file]['SUBDIR'] ?>/<?= $getFile[$file]['FILE_NAME'] ?>">
                                                            <svg class="icon icon_clip">
                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#clip"></use>
                                                            </svg>
                                                            <span><?= $getFile[$file]['DESCRIPTION'] ?></span>
                                                        </a>
                                                    </li>
                                                    <?
                                                }
                                                ?>
                                            </ul>
                                            <?
                                        }
                                        ?>
                                    </div>
                                <? endif ?>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>

    <script>
        var maxSizeAva = parseInt(<?=($maxImageSize)?>),
            maxFiles = parseInt('<?=$maxFiles?>'),
            getCurPath = '<?=$APPLICATION->GetCurPage(false)?>',
            dropzoneMainImage = '<?=Loc::getMessage('USER_SETTINGS_COMPONENT_DROPZONE_MAIN_IMAGE')?>',
            templatePath = '<?=SITE_TEMPLATE_PATH?>';
    </script>
    </script>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}