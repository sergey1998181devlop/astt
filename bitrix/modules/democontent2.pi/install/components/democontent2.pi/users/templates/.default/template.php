<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 25.09.2018
 * Time: 10:35
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}
$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$cityId = $arResult['CITY_ID'];
$verification = 0;
if ($request->get('city')) {
    $cityId = intval($request->get('city'));
}
if ($request->get('verification')) {
    if (intval($request->get('verification')) > 0) {
        $verification = 1;
    }
}

$chatEnabled = false;
if (intval(\Bitrix\Main\Config\Option::get(DSPI, 'chatEnabled'))) {
    $chatEnabled = true;
}

$authorized = $USER->IsAuthorized();
?>
    <div class="page-content">
        <div class="wrapper">
            <div class="page-title">
                <h1>
                    <?= Loc::getMessage('USERS_COMPONENT_TITLE') ?>
                    <?= $arResult['H1_POSTFIX'] ?>
                </h1>
            </div>
            <div class="row">
                <div class="col-sm-4 col-md-3 col-xxs-12">
                    <?php
                    $APPLICATION->IncludeComponent(
                        'democontent2.pi:catalog.menu',
                        'users',
                        array(
                            'IBLOCK_TYPE' => (isset($arParams['iBlockType'])) ? $arParams['iBlockType'] : '',
                            'IBLOCK_CODE' => (isset($arParams['iBlockCode'])) ? $arParams['iBlockCode'] : '',
                        )
                    );
                    ?>
                </div>
                <div class="col-sm-12 col-md-9 col-xxs-12">
                    <div class="white-block">
                        <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="get" class="filter-sorty">
                            <div class="row">
                                <div class="col-sm-4 col-xxs-12">
                                    <div class="form-group">
                                        <select name="city" class="js-select" style="width: 100%">
                                            <option value="0"><?= Loc::getMessage('USERS_COMPONENT_EMPTY_CITY') ?></option>
                                            <?php
                                            foreach ($arResult['CITIES'] as $city) {
                                                $selected = '';
                                                if (intval($city['id']) == $cityId) {
                                                    $selected = ' selected';
                                                }
                                                ?>
                                                <option value="<?= $city['id'] ?>"<?= $selected ?>><?= $city['name'] ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-xxs-12">
                                    <div class="form-group">
                                        <select name="verification" class="js-select" style="width: 100%">
                                            <option value="0"<?= (!$verification) ? ' selected' : '' ?>>
                                                <?= Loc::getMessage('USERS_COMPONENT_FILTER_VERIFICATION_OFF') ?>
                                            </option>
                                            <option value="1"<?= ($verification) ? ' selected' : '' ?>>
                                                <?= Loc::getMessage('USERS_COMPONENT_FILTER_VERIFICATION_ON') ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-xxs-12">
                                    <div class="form-group">
                                        <button class="btn btn-fluid btn-blue" type="submit">
                                            <?= Loc::getMessage('USERS_COMPONENT_FILTER_BTN') ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <? if (count($arResult['ITEMS'])): ?>
                            <div class="sorty-panel sorty-executors">
                                <ul>
                                    <li class="active">
                                        <a href="<?= $APPLICATION->GetCurPage(false) ?>">
                                        <span class="icon-wrap">
                                            <svg class="icon icon_arrow-right">
                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#arrow-right"></use>
                                            </svg>
                                        </span>
                                            <span>
                                            <?= Loc::getMessage('USERS_COMPONENT_FILTER_SORT_DATE_REG') ?>
                                        </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?= $APPLICATION->GetCurPage(false) ?>?order=desc">
                                        <span class="icon-wrap">
                                            <svg class="icon icon_arrow-right">
                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#arrow-right"></use>
                                            </svg>
                                        </span>
                                            <span>
                                            <?= Loc::getMessage('USERS_COMPONENT_FILTER_SORT_DATE_REG') ?>
                                        </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?= $APPLICATION->GetCurPage(false) ?>?order=rating">
                                        <span class="icon-wrap">
                                            <svg class="icon icon_arrow-right">
                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#arrow-right"></use>
                                            </svg>
                                        </span>
                                            <span>
                                            <?= Loc::getMessage('USERS_COMPONENT_FILTER_SORT_RATING') ?>
                                        </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="masters-list">
                                <?php
                                $i = 0;
                                foreach ($arResult['ITEMS'] as $item) {
                                    $i++;
                                    if ($item['PERSONAL_PHOTO'] > 0) {
                                        $ava = CFile::ResizeImageGet($item['PERSONAL_PHOTO'],
                                            array(
                                                'width' => 150,
                                                'height' => 150
                                            ),
                                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                            true
                                        );
                                    }
                                    ?>
                                    <div id="executor-<?= $item['ID'] ?>" class="profile-preview">
                                        <?php
                                        if ($authorized) {
                                            ?>
                                            <div class="remove-item<?= ((in_array($item['ID'], $arResult['FAVOURITES'])) ? ' active' : '') ?>"
                                                 data-id="<?= $item['ID'] ?>">
                                                <?php
                                                if (in_array($item['ID'], $arResult['FAVOURITES'])) {
                                                    echo Loc::getMessage('IN_FAVOURITES');
                                                } else {
                                                    echo Loc::getMessage('ADD_TO_FAVOURITES');
                                                }
                                                ?>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        <div class="tbl tbl-fixed">
                                            <div class="tbc pict">
                                                <div class="pict-wrap">
                                                    <?php
                                                    if ($chatEnabled) {
                                                        ?>
                                                        <div class="status-indicator uid<?= \Democontent2\Pi\Utils::getChatId($item['ID']) ?>"></div>
                                                        <?php
                                                    }
                                                    ?>
                                                    <? if ($item['PERSONAL_PHOTO'] > 0): ?>
                                                        <div class="object-fit-container">
                                                            <img src="<?= $ava['src'] ?>"
                                                                 alt="" data-object-fit="cover"
                                                                 data-object-position="50% 50%"/>
                                                        </div>
                                                    <? else: ?>
                                                        <svg class="icon icon_user">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#user"></use>
                                                        </svg>
                                                    <? endif ?>
                                                </div>
                                            </div>
                                            <div class="tbc desc">
                                                <div class="name medium">
                                                    <?= $item['NAME'] ?>
                                                    <?= $item['LAST_NAME'] ?>
                                                </div>

                                                <?php
                                                if (intval($item['UF_DSPI_CITY'])) {
                                                    if (isset($arResult['CITIES'][$item['UF_DSPI_CITY']])) {
                                                        ?>
                                                        <div class="location">
                                                            <div class="location-box">
                                                                <svg class="icon icon_location">
                                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#location"></use>
                                                                </svg>
                                                                <?= $arResult['CITIES'][$item['UF_DSPI_CITY']]['name'] ?>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                                <div class="indicators">
                                                    <?php
                                                    if ($item['UF_DSPI_DOCUMENTS']) {
                                                        ?>
                                                        <div class="verification-box">
                                                            <svg class="icon icon_checkmark">
                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                                            </svg>
                                                            <?= Loc::getMessage('USERS_COMPONENT_VERIFICATION') ?>
                                                        </div>
                                                        <?php
                                                    }

                                                    if ($item['CARD']) {
                                                        ?>
                                                        <div class="security-box">
                                                            <svg class="icon icon_shield_doc">
                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#shield-doc"></use>
                                                            </svg>
                                                            <?= Loc::getMessage('USERS_COMPONENT_SECURITY') ?>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <div class="txt">
                                                    <?php
                                                    if (isset($item['PROFILE']['UF_DATA'])) {
                                                        $profileData = unserialize($item['PROFILE']['UF_DATA']);
                                                        if (isset($profileData['description']) && strlen($profileData['description']) > 0) {
                                                            ?>
                                                            <p><?= $profileData['description'] ?></p>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <div class="info">
                                                    <div class="date-box">
                                                        <svg class="icon icon_time">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#time"></use>
                                                        </svg>
                                                        <span title="<?= date('Y-m-d H:i', strtotime($item['DATE_REGISTER'])) ?>"
                                                              class="timestamp"></span>
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
                                        </div>
                                        <a class="lnk-abs" href="<?= SITE_DIR ?>user/<?= $item['ID'] ?>/"></a>
                                    </div>
                                    <?php
                                    if ($i == 1) {
                                        try {
                                            if (\Bitrix\Main\IO\File::isFileExists(
                                                \Bitrix\Main\IO\Path::normalize(
                                                    \Bitrix\Main\Application::getDocumentRoot() . SITE_TEMPLATE_PATH . "/inc/adv/list.php"
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
                                    }
                                }
                                ?>
                            </div>
                            <?
                            $APPLICATION->IncludeComponent(
                                'democontent2.pi:pagination',
                                '',
                                [
                                    'TOTAL' => $arResult['TOTAL'],
                                    'LIMIT' => $arResult['LIMIT'],
                                    'CURRENT_PAGE' => $arResult['CURRENT_PAGE'],
                                    'URL' => $APPLICATION->GetCurPage(false)
                                ]
                            );
                            ?>
                        <? else: ?>
                            <div class="alert alert-info">
                                <?= Loc::getMessage('USERS_COMPONENT_EMPTY') ?>
                            </div>
                        <? endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        BX.message({
            usersPath: '<?=$this->GetFolder()?>/ajax.php',
            addToFavourites: '<?=Loc::getMessage('ADD_TO_FAVOURITES')?>',
            inFavourites: '<?=Loc::getMessage('IN_FAVOURITES')?>'
        });
    </script>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}