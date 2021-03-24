<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.01.2019
 * Time: 16:40
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'setFrameMode')) {
    $this->setFrameMode(true);
}

$route = [];
$characteristics = [];
if (strlen($arResult['UF_PROPERTIES']) > 0) {
    $characteristics = unserialize($arResult['UF_PROPERTIES']);

    foreach ($characteristics as $k => $v) {
        if (isset($v['route'])) {
            $route = $v['route'];
            unset($characteristics[$k]);
            break;
        }
    }
}

if (count($route)) {
    \Bitrix\Main\Page\Asset::getInstance()->addJs(
        'https://api-maps.yandex.ru/2.1/?load=package.full&lang=ru_RU&apikey=' . \Bitrix\Main\Config\Option::get(DSPI, 'yandex_maps_api_key')
    );
    $_route = [];
    foreach ($route as $item) {
        $ex = explode(',', $item);
        $_route[] = [
            floatval($ex[0]),
            floatval($ex[1])
        ];
    }
    $route = $_route;
    unset($_route);
}

$currencyName = \Bitrix\Main\Config\Option::get(DSPI, 'currency_name');

$quickly = 0;

if (count($arResult['UF_QUICKLY_END']) > 0 && strtotime($arResult['UF_QUICKLY_END']) > strtotime(date('Y-m-d H:i'))) {
    $quickly = 1;
}

$stagesL = count($arResult['STAGES']);
$myOfferCount = count($arResult['MY_OFFER']);
$safeCrowOrdersCount = count($arResult['SAFECROW_ORDERS']);

$blockClass = '';
switch ($arResult['UF_STATUS']) {
    case 6:
        $blockClass = 'arbitration';
        break;
    default:
        if ($quickly) {
            $blockClass = 'urgent';
        }
}

$chatEnabled = false;
if (intval(\Bitrix\Main\Config\Option::get(DSPI, 'chatEnabled'))) {
    $chatEnabled = true;
}
?>
<div class="page-content">
    <div class="wrapper">
        <div class="row task-container " id="sticky-container">
            <div class="col-md-3 col-lg-4 col-xxs-12 pull-right">
                <div class="user-card-block white-block sticky-block" data-container="#sticky-container">
                    <?
                    if (method_exists($this, 'createFrame')) {
                        $frame = $this->createFrame()->begin();
                    }
                    ?>
                    <? if ($myOfferCount && $arResult['MY_OFFER']['UF_CANDIDATE'] && !$arResult['MY_OFFER']['UF_EXECUTOR']): ?>
                        <div class="alert alert-warning">
                            <?= Loc::getMessage('DETAIL_COMPONENT_YOUR_CANDIDATE') ?>
                        </div>
                        <br>
                    <? endif ?>
                    <? if ($myOfferCount && $arResult['MY_OFFER']['UF_DENIED']): ?>
                        <div class="alert alert-danger">
                            <?= Loc::getMessage('DETAIL_COMPONENT_YOUR_DENIED') ?>
                        </div>
                        <br>
                    <? endif ?>
                    <? if ($myOfferCount && $arResult['MY_OFFER']['UF_EXECUTOR']): ?>
                        <? if ($arResult['UF_STATUS'] == 4): ?>
                            <div class="alert alert-warning text-center">
                                <?= Loc::getMessage('DETAIL_COMPONENT_YOUR_EXECUTOR') ?>
                                <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="post">
                                    <input type="hidden" name="confirmExecutor" value="1">
                                    <button class="btn btn-xs btn-green">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_EXECUTOR_CONFIRM') ?>
                                    </button>
                                </form>
                                <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="post">
                                    <input type="hidden" name="unSetExecutor" value="1">
                                    <button class="btn btn-xs btn-red">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_EXECUTOR_RESET') ?>
                                    </button>
                                </form>
                            </div>
                        <? else: ?>
                            <div class="alert alert-success">
                                <?= Loc::getMessage('DETAIL_COMPONENT_YOUR_EXECUTOR') ?>
                            </div>
                        <? endif ?>
                        <br>
                    <? endif ?>
                    <?
                    if (method_exists($this, 'createFrame')) {
                        $frame->end();
                    }
                    ?>
                    <div class="ttl-customer">
                        <?= Loc::getMessage('DETAIL_COMPONENT_BTN_CUSTOMER_TTL') ?>:
                    </div>
                    <div class="user-card-block-in">
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
                                    <? if ($arResult['USER']['PERSONAL_PHOTO'] > 0): ?>
                                        <?
                                        $ava = CFile::ResizeImageGet($arResult['USER']['PERSONAL_PHOTO'],
                                            array(
                                                'width' => 150,
                                                'height' => 150
                                            ),
                                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                            true
                                        );
                                        ?>
                                        <div class="object-fit-container">
                                            <img src="<?= $ava['src'] ?>"
                                                 alt="" data-object-fit="cover"
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
                                        <?= Loc::getMessage('DETAIL_COMPONENT_USER_REG') ?>:
                                        <span title="<?= date('Y-m-d H:i', strtotime($arResult['USER']['DATE_REGISTER'])) ?>"
                                              class="timestamp"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a class="lnk-abs" href="<?= SITE_DIR ?>user/<?= $arResult['USER']['ID'] ?>/"></a>
                    </div>
                    <br>
                    <div class="text-center" id="chatButton"></div>
                </div>

            </div>
            <div class="col-sm-12 col-md-9 col-lg-8 col-xxs-12">
                <? if ($arResult['BLOCKED']): ?>
                    <div class="alert alert-danger">
                        <?= Loc::getMessage('DETAIL_COMPONENT_BLOCKED_USER_MES') ?>
                    </div>
                    <br>
                <? endif ?>
                <div class="white-block <?= $blockClass ?>">
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
                                case 5:
                                    ?>
                                    <div class="status hold-true">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_STATUS_5') ?>
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
                            $stagesCompleted = 0;
                            foreach ($arResult['STAGES'] as $STAGE) {
                                if ($STAGE['UF_STATUS'] == 3) { //stage completed
                                    $stagesCompleted++;
                                }
                            }

                            if ($arResult['UF_SAFE']) {
                                switch (intval($arResult['UF_STATUS'])) {
                                    case 2:
                                        $i = 0;
                                        foreach ($arResult['STAGES'] as $STAGE) {
                                            $i++;
                                            $closed = false;
                                            $completedByOwner = false;
                                            switch (intval($STAGE['UF_STATUS'])) {
                                                case 1:
                                                    $completedByOwner = true;
                                                    break;
                                                case 3:
                                                    $closed = true;
                                                    break;
                                            }
                                            ?>
                                            <tr<?= (($closed) ? ' class="completed"' : '') ?>>
                                                <td>
                                                    <?= $i ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if (!$closed) {
                                                        if (!$completedByOwner) {
                                                            if ($safeCrowOrdersCount > 0) {
                                                                $safeCrowOrderFound = false;
                                                                foreach ($arResult['SAFECROW_ORDERS'] as $safeCrowOrder) {
                                                                    if ($safeCrowOrder['UF_STAGE_ID'] == $STAGE['ID']) {
                                                                        if ($safeCrowOrder['UF_STATUS'] == 'paid') {
                                                                            $safeCrowOrderFound = true;
                                                                        }
                                                                        break;
                                                                    }
                                                                }

                                                                if (!$safeCrowOrderFound) {
                                                                    //����� ���
                                                                    ?>
                                                                    <div class="badge  badge-info">
                                                                        <?= Loc::getMessage('DETAIL_COMPONENT_SAFE_ADD_MONEY_WAITING') ?>
                                                                    </div>
                                                                    <?= $STAGE['UF_NAME'] ?>
                                                                    <div class="stage-btns">

                                                                    </div>
                                                                    <?php
                                                                } else {
                                                                    //������ �������
                                                                    ?>
                                                                    <div class="badge badge-success">
                                                                        <?= Loc::getMessage('DETAIL_COMPONENT_SAFE_ADD_MONEY_EXISTS') ?>
                                                                    </div>
                                                                    <?= $STAGE['UF_NAME'] ?>
                                                                    <div class="stage-btns">
                                                                        <a href="#popup-confirmation"
                                                                           class="btn btn-green btn-xs btn-completed"
                                                                           data-id="<?= $STAGE['ID'] ?>">
                                                                            <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLETED') ?>
                                                                        </a>
                                                                        <a href="#popup-complain"
                                                                           class="btn btn-red btn-xs btn-complain"
                                                                           data-id="<?= $STAGE['ID'] ?>">
                                                                            <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLAIN') ?>
                                                                        </a>
                                                                    </div>


                                                                    <?php
                                                                }
                                                            } else {
                                                                //����� ���
                                                                ?>
                                                                <div class="badge badge-info">
                                                                    <?= Loc::getMessage('DETAIL_COMPONENT_SAFE_ADD_MONEY_WAITING') ?>
                                                                </div>
                                                                <?= $STAGE['UF_NAME'] ?>

                                                                <?php
                                                            }
                                                        } else {
                                                            //������� ��������� ������������, �������� ��������� + ������������
                                                            ?>
                                                            <div class="badge badge-info">
                                                                <?= Loc::getMessage('DETAIL_COMPONENT_OWNER_WAITING') ?>
                                                            </div>
                                                            <?= $STAGE['UF_NAME'] ?>
                                                            <div class="stage-btns">
                                                                <a href="#popup-complain"
                                                                   class="btn btn-red btn-xs btn-complain"
                                                                   data-id="<?= $STAGE['ID'] ?>">
                                                                    <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLAIN') ?>
                                                                </a>
                                                            </div>
                                                            <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <?= $STAGE['UF_NAME'] ?>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td class="price">
                                                    <?= \Democontent2\Pi\Utils::price($STAGE['UF_PRICE']) ?>
                                                    <span class="currency"><?= $currencyName ?></span>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        break;
                                    case 3:
                                        $i = 0;
                                        foreach ($arResult['STAGES'] as $STAGE) {
                                            $i++;
                                            ?>
                                            <tr class="completed">
                                                <td>
                                                    <?= $i ?>
                                                </td>
                                                <td>
                                                    <?= $STAGE['UF_NAME'] ?>
                                                </td>
                                                <td class="price">
                                                    <?= \Democontent2\Pi\Utils::price($STAGE['UF_PRICE']) ?>
                                                    <span class="currency"><?= $currencyName ?></span>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        break;
                                    case 6:
                                        $i = 0;
                                        foreach ($arResult['STAGES'] as $STAGE) {
                                            $i++;
                                            ?>
                                            <tr>
                                                <td>
                                                    <?= $i ?>
                                                </td>
                                                <td>
                                                    <? if ($STAGE['UF_STATUS'] == 2): ?>
                                                        <div class="badge badge-danger">
                                                            <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_COMPLAIN_EXISTS') ?>
                                                        </div>
                                                    <? endif ?>
                                                    <?= $STAGE['UF_NAME'] ?>
                                                </td>
                                                <td class="price">
                                                    <?= \Democontent2\Pi\Utils::price($STAGE['UF_PRICE']) ?>
                                                    <span class="currency"><?= $currencyName ?></span>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        break;
                                    default:
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
                                                    <?= \Democontent2\Pi\Utils::price($STAGE['UF_PRICE']) ?>
                                                    <span class="currency"><?= $currencyName ?></span>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                }
                            } else {
                                switch (intval($arResult['UF_STATUS'])) {
                                    case 2:
                                        $i = 0;
                                        foreach ($arResult['STAGES'] as $STAGE) {
                                            $i++;
                                            $closed = false;
                                            $completedByOwner = false;
                                            switch (intval($STAGE['UF_STATUS'])) {
                                                case 1:
                                                    $completedByOwner = true;
                                                    break;
                                                case 3:
                                                    $closed = true;
                                                    break;
                                            }
                                            ?>
                                            <tr<?= (($closed) ? ' class="completed"' : '') ?>>
                                                <td>
                                                    <?= $i ?>
                                                </td>
                                                <td>

                                                    <?php
                                                    if (!$closed) {
                                                        if (!$completedByOwner) {
                                                            ?>
                                                            <?= $STAGE['UF_NAME'] ?>
                                                            <div class="stage-btns">
                                                                <a href="#popup-confirmation"
                                                                   class="btn btn-green btn-xs btn-completed"
                                                                   data-id="<?= $STAGE['ID'] ?>">
                                                                    <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLETED') ?>
                                                                </a>
                                                                <a href="#popup-complain"
                                                                   class="btn btn-red btn-xs btn-complain"
                                                                   data-id="<?= $STAGE['ID'] ?>">
                                                                    <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLAIN') ?>
                                                                </a>
                                                            </div>
                                                            <?php
                                                        } else {
                                                            //������� ��������� ������������, �������� ��������� + ������������
                                                            ?>
                                                            <div class="badge badge-success">
                                                                <?= Loc::getMessage('DETAIL_COMPONENT_OWNER_WAITING') ?>
                                                            </div>
                                                            <?= $STAGE['UF_NAME'] ?>

                                                            <div class="stage-btns">

                                                                <a href="#popup-complain"
                                                                   class="btn btn-red btn-xs btn-complain"
                                                                   data-id="<?= $STAGE['ID'] ?>">
                                                                    <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLAIN') ?>
                                                                </a>
                                                            </div>
                                                            <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <?= $STAGE['UF_NAME'] ?>
                                                        <?php

                                                    }
                                                    ?>
                                                </td>
                                                <td class="price">
                                                    <?= \Democontent2\Pi\Utils::price($STAGE['UF_PRICE']) ?>
                                                    <span class="currency"><?= $currencyName ?></span>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        break;
                                    case 3:
                                        $i = 0;
                                        foreach ($arResult['STAGES'] as $STAGE) {
                                            $i++;
                                            ?>
                                            <tr class="completed">
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
                                                        <span><?= Loc::getMessage('DETAIL_COMPONENT_PRICE_CONTRACT') ?></span>
                                                    <? endif ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        break;
                                    case 6:
                                        $i = 0;
                                        foreach ($arResult['STAGES'] as $STAGE) {
                                            $i++;
                                            ?>
                                            <tr>
                                                <td>
                                                    <?= $i ?>
                                                </td>
                                                <td>
                                                    <? if ($STAGE['UF_STATUS'] == 2): ?>
                                                        <div class="badge badge-danger">
                                                            <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_COMPLAIN_EXISTS') ?>
                                                        </div>
                                                    <? endif ?>
                                                    <?= $STAGE['UF_NAME'] ?>
                                                </td>
                                                <td class="price">
                                                    <?= \Democontent2\Pi\Utils::price($STAGE['UF_PRICE']) ?>
                                                    <span class="currency"><?= $currencyName ?></span>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        break;
                                    default:
                                        $i = 0;
                                        foreach ($arResult['STAGES'] as $STAGE) {
                                            $i++;
                                            ?>
                                            <tr<?= ((intval($STAGE['UF_STATUS']) == 3) ? ' class="completed"' : '') ?>>
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
                                                        <span><?= Loc::getMessage('DETAIL_COMPONENT_PRICE_CONTRACT') ?></span>
                                                    <? endif ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                }
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
                                <?php
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

                    if (count($hiddenFiles) > 0 && $myOfferCount && $arResult['MY_OFFER']['UF_EXECUTOR']) {
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
                                <?php
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
                    if (!$stagesL && $myOfferCount > 0 && !$arResult['BLOCKED']) {
                        // ���� ������� ����������
                        if ($arResult['UF_SAFE']) {
                            switch ($arResult['UF_STATUS']) {
                                case 2:
                                    $safeCrowOrderFound = false;
                                    foreach ($arResult['SAFECROW_ORDERS'] as $safeCrowOrder) {
                                        if ($safeCrowOrder['UF_STATUS'] == 'paid') {
                                            $safeCrowOrderFound = true;
                                        }
                                        break;
                                    }
                                    if (!$safeCrowOrdersCount) {
                                        // ������ �� ������
                                        ?>
                                        <div class="badge  badge-info">
                                            <?= Loc::getMessage('DETAIL_COMPONENT_SAFE_ADD_MONEY_WAITING') ?>
                                        </div>
                                        <?php

                                    } else {
                                        if ($safeCrowOrderFound) {
                                            // ������ ���������
                                            ?>
                                            <div class="badge badge-success">
                                                <?= Loc::getMessage('DETAIL_COMPONENT_SAFE_ADD_MONEY_EXISTS') ?>
                                            </div>
                                            <br>
                                            <br>
                                            <div class="btn-wrap">
                                                <a href="#popup-confirmation"
                                                   class="btn btn-green" data-fancybox="">
                                                    <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLETED') ?>
                                                </a>
                                                <a href="#popup-complain"
                                                   class="btn btn-red" data-fancybox="">
                                                    <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLAIN') ?>
                                                </a>
                                            </div>
                                            <?php
                                        }
                                    }
                                    break;
                                case 5:
                                    ?>
                                    <div class="badge badge-info">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_OWNER_WAITING') ?>
                                    </div>
                                    <div class="btn-wrap">
                                        <a href="#popup-complain"
                                           class="btn btn-red" data-fancybox="">
                                            <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLAIN') ?>
                                        </a>
                                    </div>
                                    <?php
                                    break;
                                case 6:
                                    ?>
                                    <div class="badge badge-danger">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_COMPLAIN_EXISTS') ?>
                                    </div>
                                    <?php
                                    break;
                            }
                        } else {
                            switch ($arResult['UF_STATUS']) {
                                case 2:
                                    ?>
                                    <div class="btn-wrap">
                                        <a href="#popup-confirmation"
                                           class="btn btn-green" data-fancybox="">
                                            <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLETED') ?>
                                        </a>
                                        <a href="#popup-complain" class="btn btn-red" data-fancybox="">
                                            <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLAIN') ?>
                                        </a>
                                    </div>
                                    <?php
                                    break;
                                case 5:
                                    ?>
                                    <div class="badge badge-success">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_SAFE_EXECUTOR_END') ?>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="stage-btns">
                                        <a href="#popup-complain" data-fancybox="" class="btn btn-red">
                                            <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLAIN') ?>
                                        </a>
                                    </div>
                                    <?php
                                    break;
                                case 6:
                                    ?>
                                    <div class="badge badge-danger">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_COMPLAIN_EXISTS') ?>
                                    </div>
                                    <?php
                                    break;
                            }
                        }
                    }
                    ?>
                    <?php
                    if (!$myOfferCount && !$arResult['BLOCKED'] && $arResult['UF_STATUS'] != 3 && $arResult['UF_STATUS'] != 2) {
                        ?>

                        <div class="btn-wrap hidden-sm hidden-xxs hidden-xs">
                            <a class="btn btn-green" data-fancybox=""
                               href="<?= ($USER->IsAuthorized()) ? '#popup-proposal' : '#popup-registration' ?>">
                                <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL') ?>
                            </a>
                        </div>
                        <?php
                    }
                    ?>

                </div>
                <br>

                <?php
                if ($arResult['UF_STATUS'] == 6) {
                    ?>
                    <div class="white-block">
                        <div class="badge badge-danger">
                            <?php
                            echo Loc::getMessage(
                                'DETAIL_COMPONENT_COMPLAIN_INFO_TITLE',
                                [
                                    '#WHO#' => Loc::getMessage('DETAIL_COMPONENT_COMPLAIN_INFO_WHO_' . ((intval($arResult['COMPLAIN']['UF_USER_ID'])) == intval($arResult['UF_USER_ID']) ? 'OWNER' : 'EXECUTOR'))
                                ]
                            );
                            ?>
                            <span title="<?= date('Y-m-d H:i', strtotime($arResult['COMPLAIN']['UF_CREATED_AT'])) ?>"
                                  class="timestamp"></span>
                        </div>
                        <p>
                            <?= $arResult['COMPLAIN']['UF_TEXT'] ?>
                        </p>
                    </div>
                    <br>
                    <?php
                } else {
                    if (count($arResult['STAGES']) > 0) {
                        foreach ($arResult['STAGES'] as $STAGE) {
                            if ($STAGE['UF_STATUS'] == 2) {
                                ?>
                                <div class="white-block">
                                    <div class="badge badge-danger">
                                        <?php
                                        echo Loc::getMessage(
                                            'DETAIL_COMPONENT_COMPLAIN_INFO_TITLE',
                                            [
                                                '#WHO#' => Loc::getMessage('DETAIL_COMPONENT_COMPLAIN_INFO_WHO_' . ((intval($arResult['COMPLAIN']['UF_USER_ID'])) == intval($arResult['UF_USER_ID']) ? 'OWNER' : 'EXECUTOR'))
                                            ]
                                        );
                                        ?>
                                        <span title="<?= date('Y-m-d H:i', strtotime($arResult['COMPLAIN']['UF_CREATED_AT'])) ?>"
                                              class="timestamp"></span>
                                    </div>
                                    <p>
                                        <?= $arResult['COMPLAIN']['UF_TEXT'] ?>
                                    </p>
                                </div>
                                <br>
                                <?php
                                break;
                            }
                        }
                    }
                }
                ?>

                <? if ($myOfferCount): ?>
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
                                <?php
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
<div id="popup-complain" class="popup popup-complain text-center">
    <div class="popup-head text-center">
        <div class="h2">
            <?= Loc::getMessage('DETAIL_COMPONENT_POPUP_COMPLAIN_TTL') ?>
        </div>
    </div>
    <div class="popup-body">
        <form method="post"
              action="<?= $APPLICATION->GetCurPage(false) ?>">
            <input type="hidden"
                   name="setComplain"
                   value="1">
            <input type="hidden"
                   name="stageId">
            <div class="form-group">
                <textarea class="form-control" placeholder="<?= Loc::getMessage('DETAIL_COMPONENT_POPUP_COMPLAIN_') ?>"
                          name="setComplainMessage" cols="30" rows="10"></textarea>
            </div>
            <div class="text-center">
                <button type="submit"
                        class="btn">
                    <?= Loc::getMessage('DETAIL_COMPONENT_POPUP_BTN_SEND') ?>
                </button>
            </div>
        </form>
    </div>
</div>
<div id="popup-confirmation" class="popup popup-confirmation  text-center">
    <div class="popup-head text-center">
        <div class="h2">
            <?= Loc::getMessage('DETAIL_COMPONENT_POPUP_CONFIRMATION_TTL') ?>
        </div>
    </div>
    <div class="popup-body">
        <?= Loc::getMessage('DETAIL_COMPONENT_POPUP_CONFIRMATION_DESC') ?>
        <div class="btn-wrap text-center">
            <a href="#" data-fancybox-close class="btn btn-red">
                <?= Loc::getMessage('DETAIL_COMPONENT_POPUP_CONFIRMATION_BTN_CANCLE') ?>
            </a>
            <form style="display: inline;" method="post"
                  action="<?= $APPLICATION->GetCurPage(false) ?>">
                <input type="hidden" name="setCompleted"
                       value="1">
                <input type="hidden" name="stageId">
                <button type="submit" class="btn">
                    <?= Loc::getMessage('DETAIL_COMPONENT_POPUP_CONFIRMATION_BTN') ?>
                </button>
            </form>
        </div>

    </div>
</div>
<?php
if (intval($arResult['EXECUTOR_ID'])) {
    $APPLICATION->IncludeComponent(
        'democontent2.pi:task.chat',
        '',
        [
            'title' => $arResult['UF_NAME'],
            'taskId' => $arParams['itemId'],
            'ownerId' => intval($arResult['UF_USER_ID']),
            'executorId' => intval($arResult['EXECUTOR_ID']),
            'iBlockId' => intval($arResult['UF_IBLOCK_ID'])
        ]
    );
}
?>
<script>
    var maxFiles = parseInt('<?=intval(\Bitrix\Main\Config\Option::get(DSPI, 'max_files'))?>'),
        templatePath = '<?=SITE_TEMPLATE_PATH?>',
        route = JSON.parse('<?=\Bitrix\Main\Web\Json::encode($route)?>');
</script>