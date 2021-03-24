<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 14.01.2019
 * Time: 19:16
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}

use Bitrix\Main\Localization\Loc;

\Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/jquery-ui.min.css');
\Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery-ui.min.js');

$route = [];
if (strlen($arResult['ITEM']['UF_PROPERTIES']) > 0) {
    $characteristics = unserialize($arResult['ITEM']['UF_PROPERTIES']);
    foreach ($characteristics as $k => $v) {
        if (isset($v['route'])) {
            $route = $v['route'];
            break;
        }
    }
}

if (count($route)) {
    \Bitrix\Main\Page\Asset::getInstance()->addJs('https://api-maps.yandex.ru/2.1/?load=package.full&lang=ru_RU');
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

$safeCrowEnabled = false;
if (strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiKey')) > 0
    && strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
    $safeCrowEnabled = true;
}
?>
    <div class="page-content">
        <div class="wrapper">
            <div class="page-title">
                <h1>
                    <?= Loc::getMessage('EDIT_COMPONENT_TITLE') ?>
                </h1>
            </div>
            <div class="row">
                <div class="col-md-3 col-lg-4 col-xxs-12 pull-right">
                    <div class="white-block">
                        <?php
                        $APPLICATION->IncludeComponent(
                            "bitrix:main.include", "",
                            array(
                                "AREA_FILE_SHOW" => "file",
                                "PATH" => SITE_TEMPLATE_PATH . "/inc/create/steps-create.php",
                                "EDIT_TEMPLATE" => "include_areas_template.php",
                                'MODE' => 'html'
                            ),
                            false
                        );
                        ?>
                    </div>
                </div>
                <div class="col-sm-12 col-md-9 col-lg-8 col-xxs-12">
                    <div class="white-block adding-job">
                        <?php
                        if (intval($arResult['ITEM']['UF_MODERATION'])) {
                            ?>
                            <div class="alert alert-info">
                                <?= Loc::getMessage('EDIT_COMPONENT_TASK_ON_MODERATION') ?>
                            </div>
                            <?php
                        } else {
                            ?>
                            <a href="<?= SITE_DIR ?>task<?= $arResult['ITEM']['UF_ID'] ?>-<?= $arResult['ITEM']['UF_IBLOCK_ID'] ?>/"
                               class="btn btn-green btn-fluid">
                                <?= Loc::getMessage('EDIT_COMPONENT_LOOK_TASK') ?>
                            </a>
                            <?php
                        }
                        ?>
                        <br>
                        <form method="post" enctype="multipart/form-data"
                              action="<?= $APPLICATION->GetCurPage(false) ?>">
                            <div class="form-group">
                                <label for="create-inp1">
                                    <?= Loc::getMessage('EDIT_COMPONENT_LBL_TTL') ?>
                                </label>
                                <input required id="create-inp1" class="form-control required" type="text"
                                       name="name" placeholder="<?= Loc::getMessage('EDIT_COMPONENT_PL_TTL') ?>"
                                       value="<?= $arResult['ITEM']['UF_NAME'] ?>">
                            </div>
                            <div class="row">
                                <div class="col-sm-4 col-xxs-12">
                                    <div class="form-group">
                                        <label for="create-inp-category">
                                            <?= Loc::getMessage('EDIT_COMPONENT_LBL_CATEGORY') ?>
                                        </label>
                                        <select disabled id="create-inp-category" class="js-select" style="width: 100%">
                                            <?php
                                            foreach ($arResult['CATEGORIES'] as $key => $item) {
                                                $selected = '';
                                                if ($item['code'] == $arResult['ITEM']['UF_IBLOCK_TYPE']) {
                                                    $selected = ' selected';
                                                }
                                                ?>
                                                <option value="<?= $key ?>"<?= $selected ?>><?= $item['name'] ?></option>
                                                <?
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-xxs-12">
                                    <div class="form-group">
                                        <label for="create-inp-subcategory">
                                            <?= Loc::getMessage('EDIT_COMPONENT_LBL_SUBCATEGORY') ?>
                                        </label>
                                        <select disabled id="create-inp-subcategory" class="js-select" name="iblock"
                                                style="width: 100%">
                                            <?php
                                            foreach ($arResult['CATEGORIES'][$arResult['ITEM']['UF_IBLOCK_TYPE']]['items'] as $key => $item) {
                                                $selected = '';
                                                if ($item['code'] == $arResult['ITEM']['UF_IBLOCK_CODE']) {
                                                    $selected = ' selected';
                                                }
                                                ?>
                                                <option value="<?= $key ?>" <?= $selected ?>><?= $item['name'] ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-xxs-12">
                                    <div class="form-group">
                                        <label for="create-inp-city">
                                            <?= Loc::getMessage('EDIT_COMPONENT_LBL_CITY') ?>
                                        </label>
                                        <select disabled id="create-inp-city"
                                                class="js-select <?= (count($arResult['CITIES']) > 5) ? 'has-search' : '' ?>"
                                                name="city" required="" style="width: 100%">
                                            <option value="0" <?= (!$arResult['ITEM']['UF_CITY']) ? 'selected' : ''; ?>>
                                                <?= Loc::getMessage('EDIT_COMPONENT_CITY_EMPTY') ?>
                                            </option>
                                            <?php

                                            foreach ($arResult['CITIES'] as $city) {
                                                $selected = '';
                                                if ($city['id'] == $arResult['ITEM']['UF_CITY']) {
                                                    $selected = ' selected';
                                                }
                                                echo '<option value="' . $city['id'] . '"' . $selected . '>' . $city['name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 col-xxs-12">
                                    <div class="form-group">
                                        <label for="date-start">
                                            <?= Loc::getMessage('EDIT_COMPONENT_LBL_DATE') ?>
                                        </label>
                                        <div class="datepicker-wrap">
                                            <input name="dateStart" readonly="true" id="date-start"
                                                   class="form-control ui-datepicker"
                                                   type="text"
                                                   value="<?= ($arResult['ITEM']['UF_BEGIN_WITH']) ? date('d.m.Y', strtotime($arResult['ITEM']['UF_BEGIN_WITH'])) : ''; ?>"
                                                   placeholder="<?= Loc::getMessage('EDIT_COMPONENT_PL_DATE_START') ?>">
                                            <input value="<?= ($arResult['ITEM']['UF_BEGIN_WITH']) ? date('H:i', strtotime($arResult['ITEM']['UF_BEGIN_WITH'])) : ''; ?>"
                                                   name="timeStart" placeholder="__:__" type="text"
                                                   class="time form-control">
                                        </div>
                                        <div class="tips">
                                            <a href="#" class="js-lnk presently">
                                                <?= Loc::getMessage('EDIT_COMPONENT_TODAY') ?>
                                            </a>,
                                            <a href="#" class="js-lnk tomorrow">
                                                <?= Loc::getMessage('EDIT_COMPONENT_TOMORROW') ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xxs-12">
                                    <div class="form-group">
                                        <label class="hidden-xxs hidden-xs">&nbsp;</label>
                                        <div class="datepicker-wrap">
                                            <input name="dateEnd" id="date-end" readonly="true"
                                                   class="form-control ui-datepicker"
                                                   value="<?= ($arResult['ITEM']['UF_RUN_UP']) ? date('d.m.Y', strtotime($arResult['ITEM']['UF_RUN_UP'])) : ''; ?>"
                                                   type="text"
                                                   placeholder="<?= Loc::getMessage('EDIT_COMPONENT_PL_DATE_END') ?>">
                                            <input value="<?= ($arResult['ITEM']['UF_RUN_UP']) ? date('H:i', strtotime($arResult['ITEM']['UF_RUN_UP'])) : ''; ?>"
                                                   name="timeEnd" placeholder="__:__" type="text"
                                                   class="time form-control">
                                        </div>
                                        <div class="tips">
                                            <a href="#" class="js-lnk presently">
                                                <?= Loc::getMessage('EDIT_COMPONENT_TODAY') ?>
                                            </a>,
                                            <a href="#" class="js-lnk tomorrow">
                                                <?= Loc::getMessage('EDIT_COMPONENT_TOMORROW') ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="coordinates">
                                <div id="map-area"></div>
                            </div>
                            <div class="row" id="properties"></div>
                            <div class="row" id="total-budget">
                                <div class="col-sm-6 col-xxs-12">
                                    <div class="form-group">
                                        <label for="create-inp2">
                                            <?= Loc::getMessage('EDIT_COMPONENT_LBL_BUDGET') ?>
                                        </label>
                                        <input id="create-inp2" class="budget-inp form-control number-float" type="text"
                                               name="price" <?= (!$arResult['ITEM']['UF_PRICE']) ? 'disabled' : '' ?>
                                               value="<?= ($arResult['ITEM']['UF_PRICE']) ? $arResult['ITEM']['UF_PRICE'] : '' ?>">
                                    </div>
                                </div>
                                <? if (!$arResult['ITEM']['UF_SAFE']): ?>
                                    <div class="col-sm-6 col-xxs-12">
                                        <div class="form-group">
                                            <input class="checkbox contract-inp" type="checkbox" name="contractPrice"
                                                   value="0"
                                                   id="create-inp3" <?= (!$arResult['ITEM']['UF_PRICE']) ? 'checked' : '' ?>/>
                                            <label class="form-checkbox" for="create-inp3">
                                            <span class="icon-wrap">
                                                <svg class="icon icon_checkmark">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                                </svg>
                                            </span>
                                                <?= Loc::getMessage('EDIT_COMPONENT_LBL_BUDGET_AGREEMENT') ?>
                                            </label>
                                        </div>
                                    </div>
                                <? endif ?>
                            </div>

                            <div class="form-group">
                                <label for="create-inp-desc">
                                    <?= Loc::getMessage('EDIT_COMPONENT_LBL_TTL_MES') ?>
                                </label>
                                <textarea required id="create-inp-desc" name="description" class="form-control required"
                                          placeholder="<?= Loc::getMessage('EDIT_COMPONENT_PL_MES') ?>"
                                          rows="5"><?= $arResult['ITEM']['UF_DESCRIPTION'] ?></textarea>
                            </div>

                            <h5><?= Loc::getMessage('EDIT_COMPONENT_PUBLIC_FILES_TITLE') ?></h5>
                            <div class="form-group">
                                <label>
                                    <?= Loc::getMessage('EDIT_COMPONENT_PUBLIC_FILES_LABEL') ?>
                                </label>
                                <div id="files-list" class="files">
                                    <?php
                                    $files = unserialize($arResult['ITEM']['UF_FILES']);
                                    foreach ($files as $key => $file) {
                                        $getFile = CFile::GetFromCache($file);
                                        ?>
                                        <div class="itm existing">
                                            <?= $getFile['ID'] ?>
                                            <a class="filename" target="_blank"
                                               href="/upload/<?= $getFile[$file]['SUBDIR'] ?>/<?= $getFile[$file]['FILE_NAME'] ?>">
                                                <span><?= $getFile[$file]['DESCRIPTION'] ?></span>
                                            </a>
                                            <a data-id="<?= $getFile[$file]['ID'] ?>" class="remove" href="#">
                                                <svg class="icon icon_close">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>images/sprite-svg.svg#close"></use>
                                                </svg>
                                            </a>
                                        </div>
                                        <?
                                    }
                                    ?>
                                </div>
                                <div id="btn-file" class="btn btn-sm btn-file">
                                    <span><?= Loc::getMessage('EDIT_COMPONENT_ADD_FILE') ?></span>
                                    <input type="file" name="__files[]">
                                </div>
                            </div>

                            <h5><?= Loc::getMessage('EDIT_COMPONENT_HIDDEN_FILES_TITLE') ?></h5>
                            <div class="form-group">
                                <label>
                                    <?= Loc::getMessage('EDIT_COMPONENT_HIDDEN_FILES') ?>
                                </label>
                                <div class="files">
                                    <?php
                                    $hiddenFiles = unserialize($arResult['ITEM']['UF_HIDDEN_FILES']);
                                    foreach ($hiddenFiles as $key => $file) {
                                        $getFile = CFile::GetFromCache($file);
                                        ?>
                                        <div class="itm existing">
                                            <a class="filename" target="_blank"
                                               href="/upload/<?= $getFile[$file]['SUBDIR'] ?>/<?= $getFile[$file]['FILE_NAME'] ?>">
                                                <span><?= $getFile[$file]['DESCRIPTION'] ?></span>
                                            </a>
                                            <a data-id="<?= $getFile[$file]['ID'] ?>" class="remove" href="#">
                                                <svg class="icon icon_close">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>images/sprite-svg.svg#close"></use>
                                                </svg>
                                            </a>
                                        </div>
                                        <?
                                    }
                                    ?>
                                </div>
                                <div class="btn btn-sm btn-file">
                                    <span><?= Loc::getMessage('EDIT_COMPONENT_ADD_FILE') ?></span>
                                    <input type="file" name="__hiddenFiles[]">
                                </div>
                            </div>

                            <?php
                            if ($arResult['ALLOW_CHECKLISTS'] && count($arResult['RESPONSE_CHECKLIST'])) {
                                ?>
                                <h5><?= Loc::getMessage('EDIT_COMPONENT_RESPONSE_CHECKLIST_TITLE') ?></h5>
                                <div class="form-group">
                                    <label>
                                        <?= Loc::getMessage('EDIT_COMPONENT_RESPONSE_CHECKLIST_LABEL') ?>
                                    </label>
                                    <div id="response-checklists">
                                        <?php
                                        foreach ($arResult['RESPONSE_CHECKLIST'] as $item) {
                                            ?>
                                            <br>
                                            <input type="text" class="form-control"
                                                   name="response-checklist[<?= $item['ID'] ?>]"
                                                   value="<?= $item['UF_NAME'] ?>"
                                                   placeholder="<?= Loc::getMessage('EDIT_COMPONENT_RESPONSE_CHECKLIST_LABEL_EXAMPLE') ?>">
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }

                            if ($safeCrowEnabled) {
                                ?>
                                <div class="form-group">
                                    <input <?= ($arResult['ITEM']['UF_SAFE']) ? 'checked disabled' : '' ?>
                                            id="checkbox-security" class="checkbox checkbox-security" type="checkbox"
                                            name="security"/>
                                    <label class="form-checkbox" for="checkbox-security"><span class="icon-wrap">
                                        <svg class="icon icon_checkmark">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                        </svg>
                                    </span>
                                        <?= Loc::getMessage('EDIT_COMPONENT_SECURE_TRANSACTION') ?>
                                    </label>
                                </div>
                                <?php
                            }
                            ?>

                            <div class="btn-wrap">
                                <button class="btn btn-send  btn-green">
                                    <?= Loc::getMessage('EDIT_COMPONENT_BTN') ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var maxFiles = parseInt('<?=intval(\Bitrix\Main\Config\Option::get(DSPI, 'max_files'))?>'),
            ajaxPath = '<?=$this->GetFolder()?>/ajax.php',
            templatePath = '<?=SITE_TEMPLATE_PATH?>',
            selectVariant = '<?=Loc::getMessage('EDIT_COMPONENT_SELECT_VARIANT')?>',
            stageStepTTl = '<?=Loc::getMessage('EDIT_COMPONENT_STAGE_STEP_TTL')?>',
            stageNameLbl = '<?=Loc::getMessage('EDIT_COMPONENT_STAGE_LBL_TTL')?>',
            siteTemplatePach = '<?=SITE_TEMPLATE_PATH?>',
            contractor = '<?=Loc::getMessage('EDIT_COMPONENT_LBL_BUDGET_AGREEMENT')?>',
            stagePriceLbl = '<?=Loc::getMessage('EDIT_COMPONENT_LBL_BUDGET')?>',
            datepicerCloseTxt = "<?=Loc::getMessage('DATEPICKER_CLOSE_TEXT')?>",
            datepicerPrevTxt = "<?=Loc::getMessage('DATEPICKER_PREV_TEXT')?>",
            datepicerNextTxt = "<?=Loc::getMessage('DATEPICKER_NEXT_TEXT')?>",
            datepicerCurrentTxt = "<?=Loc::getMessage('DATEPICKER_CURENT_TEXT')?>",
            datepicerMonthName = [<?=Loc::getMessage('DATEPICKER_MONTH_NAME')?>],
            datepicerMonthNameShort = [<?=Loc::getMessage('DATEPICKER_MONTH_NAME_SHORT')?>],
            datepicerDaysName = [<?=Loc::getMessage('DATEPICKER_DAYS_NAME')?>],
            datepicerDaysNameShort = [<?=Loc::getMessage('DATEPICKER_DAYS_NAME_SHORT')?>],
            datepicerDaysNameMin = [<?=Loc::getMessage('DATEPICKER_DAYS_NAME_MIN')?>],
            datepicerWeekTxt = "<?=Loc::getMessage('DATEPICKER_WEEK_TEXT')?>",
            route = JSON.parse('<?=\Bitrix\Main\Web\Json::encode($route)?>');
    </script>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}