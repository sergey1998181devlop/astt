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

$chatEnabled = false;
if (intval(\Bitrix\Main\Config\Option::get(DSPI, 'chatEnabled'))) {
    $chatEnabled = true;
}
?>
    <div class="page-content">
        <div class="wrapper">
            <div class="page-title">
                <h1>
                    <?= Loc::getMessage('USER_FEEDBACKS_COMPONENT_TITLE') ?>
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
                        <div class="sorty-panel tabs-head">
                            <ul>
                                <li class="active">
                                    <a href="#user-feedbacks1">
                                        <?= Loc::getMessage('USER_FEEDBACKS_COMPONENT_TAB_TTL_1') ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#user-feedbacks2">
                                        <?= Loc::getMessage('USER_FEEDBACKS_COMPONENT_TAB_TTL_2') ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="tabs-wrap">
                            <div class="tab active" id="user-feedbacks1">
                                <div class="comments-container">
                                    <?php
                                    $myFeedback = 0;
                                    foreach ($arResult['ITEMS'] as $key => $item) {
                                        if ($item['UF_FROM'] == $USER->GetID()) {
                                            $myFeedback++;

                                            ?>
                                            <div class="item">
                                                <div class="head clearfix">
                                                    <div class="user-box">
                                                        <div class="tbl tbl-fixed">
                                                            <div class="tbc">
                                                                <div class="ava">
                                                                    <? if ($item['USER_TO_PHOTO'] > 0): ?>
                                                                        <?
                                                                        $ava = CFile::ResizeImageGet($item['USER_TO_PHOTO'],
                                                                            array(
                                                                                'width' => 50,
                                                                                'height' => 50
                                                                            ),
                                                                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                                                            true
                                                                        );
                                                                        ?>
                                                                        <div class="object-fit-container">
                                                                            <img
                                                                                    src="<?= $ava['src'] ?>"
                                                                                    alt="" data-object-fit="cover"
                                                                                    data-object-position="50% 50%"/>
                                                                        </div>
                                                                    <? else: ?>
                                                                        <svg class="icon icon_user">
                                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#user"></use>
                                                                        </svg>
                                                                    <? endif ?>
                                                                    <?php
                                                                    if ($chatEnabled) {
                                                                        ?>
                                                                        <div class="status-indicator uid<?= \Democontent2\Pi\Utils::getChatId($item['UF_TO']) ?>"></div>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                            <div class="tbc">
                                                                <div class="name bold">
                                                                    <?= $item['USER_TO_NAME'] ?>
                                                                    <?= $item['USER_TO_LAST_NAME'] ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <a href="<?= SITE_DIR . 'user/' . $item['UF_TO'] . '/' ?>"
                                                           class="lnk-abs"></a>
                                                    </div>
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
                                                        <?= Loc::getMessage('USER_FEEDBACKS_COMPONENT_TTL_TASK', [
                                                            'SITE_DIR' => SITE_DIR,
                                                            'iBlockId' => $item['UF_IBLOCK_ID'],
                                                            'taskId' => $item['UF_TASK_ID']
                                                        ]) ?>
                                                    </div>
                                                    <div class="descript"><?= TxtToHTML($item['UF_TEXT'], false) ?></div>
                                                    <div class="comment-btm clearfix">
                                                        <div class="date-box">
                                                            <svg class="icon icon_time">
                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#time"></use>
                                                            </svg>
                                                            <span title="<?= date('Y-m-d H:i', strtotime($item['UF_TEXT_TIME'])) ?>"
                                                                  class="timestamp"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php


                                        }
                                    }
                                    if ($myFeedback == 0) {
                                        ?>
                                        <div class="alert alert-info">
                                            <?= Loc::getMessage('USER_FEEDBACKS_COMPONENT_LIST_EMPTY') ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>

                            </div>
                            <div class="tab" id="user-feedbacks2">
                                <div class="comments-container">
                                    <?php
                                    $aboutMeFeedback = 0;
                                    foreach ($arResult['ITEMS'] as $key => $item) {
                                        if ($item['UF_FROM'] != $USER->GetID()) {
                                            $aboutMeFeedback++;
                                            ?>
                                            <div class="item">
                                                <div class="head clearfix">
                                                    <div class="user-box">
                                                        <div class="tbl tbl-fixed">
                                                            <div class="tbc">
                                                                <div class="ava">
                                                                    <? if ($item['USER_FROM_PHOTO'] > 0): ?>
                                                                        <?
                                                                        $ava = CFile::ResizeImageGet($item['USER_FROM_PHOTO'],
                                                                            array(
                                                                                'width' => 50,
                                                                                'height' => 50
                                                                            ),
                                                                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                                                            true
                                                                        );
                                                                        ?>
                                                                        <div class="object-fit-container">
                                                                            <img
                                                                                    src="<?= $ava['src'] ?>"
                                                                                    alt="" data-object-fit="cover"
                                                                                    data-object-position="50% 50%"/>
                                                                        </div>
                                                                    <? else: ?>
                                                                        <svg class="icon icon_user">
                                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#user"></use>
                                                                        </svg>
                                                                    <? endif ?>
                                                                    <?php
                                                                    if ($chatEnabled) {
                                                                        ?>
                                                                        <div class="status-indicator uid<?= \Democontent2\Pi\Utils::getChatId($item['UF_FROM']) ?>"></div>
                                                                        <?php
                                                                    }
                                                                    ?>

                                                                </div>
                                                            </div>
                                                            <div class="tbc">
                                                                <div class="name bold">
                                                                    <?= $item['USER_FROM_NAME'] ?>
                                                                    <?= $item['USER_FROM_LAST_NAME'] ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <a href="<?= SITE_DIR . 'user/' . $item['UF_FROM'] . '/' ?>"
                                                           class="lnk-abs"></a>
                                                    </div>
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
                                                        <?= Loc::getMessage('USER_FEEDBACKS_COMPONENT_TTL_TASK', [
                                                            'SITE_DIR' => SITE_DIR,
                                                            'taskId' => $item['UF_IBLOCK_ID'],
                                                            'iBlockId' => $item['UF_TASK_ID']
                                                        ]) ?>
                                                    </div>
                                                    <div class="descript"><?= TxtToHTML($item['UF_TEXT'], false) ?></div>
                                                    <div class="comment-btm clearfix">
                                                        <div class="date-box">
                                                            <svg class="icon icon_time">
                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#time"></use>
                                                            </svg>
                                                            <span title="<?= date('Y-m-d H:i', strtotime($item['UF_TEXT_TIME'])) ?>"
                                                                  class="timestamp"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                    if ($aboutMeFeedback == 0) {
                                        ?>
                                        <div class="alert alert-info">
                                            <?= Loc::getMessage('USER_FEEDBACKS_COMPONENT_LIST_EMPTY') ?>
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
        </div>
    </div>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}