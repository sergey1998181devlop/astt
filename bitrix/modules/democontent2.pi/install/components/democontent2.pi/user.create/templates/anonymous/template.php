<?php
/**
 * User: Aleksandr Miranovich
 * Email: miranovich664@gmail.com
 * Date: 11.01.2019
 * Time: 14:24
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}

\Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/jquery-ui.min.css');
\Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery-ui.min.js');
\Bitrix\Main\Page\Asset::getInstance()->addJs(
    'https://api-maps.yandex.ru/2.1/?load=package.full&lang=ru_RU&apikey=' . \Bitrix\Main\Config\Option::get(DSPI, 'yandex_maps_api_key')
);

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$safeCrowEnabled = false;
if (strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiKey')) > 0
    && strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
    $safeCrowEnabled = true;
}

$categoryType = '';
$categoryId = 0;
$need = '';

if ($request->get('need')) {
    $need = \Democontent2\Pi\Utils::clearString($request->get('need'));
}

if ($request->get('category')) {
    $ex = explode('~', $request->get('category'));
    if (count($ex) == 2) {
        if (strlen($ex[0]) > 0 && intval($ex[1]) > 0) {
            if (isset($arResult['CATEGORIES'][$ex[0]])) {
                if (isset($arResult['CATEGORIES'][$ex[0]]['items'][intval($ex[1])])) {
                    $categoryType = $ex[0];
                    $categoryId = intval($ex[1]);
                }
            }
        }
    }
}
?>
    <div class="page-content">
        <div class="wrapper">
            <div class="page-title">
                <h1>
                    <?= Loc::getMessage('CREATE_COMPONENT_TITLE') ?>
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
                        <form method="post" enctype="multipart/form-data"
                              action="<?= $APPLICATION->GetCurPage(false) ?>">
                            <div class="form-group">
                                <label for="create-inp1">
                                    <?= Loc::getMessage('CREATE_COMPONENT_LBL_TTL') ?>
                                </label>
                                <input required id="create-inp1" class="form-control required" type="text" name="name"
                                       placeholder="<?= Loc::getMessage('CREATE_COMPONENT_PL_TTL') ?>"
                                       value="<?= (strlen($need)) ? $need : '' ?>">
                            </div>
                            <div class="row">
                                <div class="col-sm-4 col-xxs-12">
                                    <div class="form-group">
                                        <label for="create-inp-category">
                                            <?= Loc::getMessage('CREATE_COMPONENT_LBL_CATEGORY') ?>
                                        </label>
                                        <select id="create-inp-category" class="js-select" style="width: 100%">
                                            <?php
                                            foreach ($arResult['CATEGORIES'] as $key => $item) {
                                                $selected = '';
                                                if ($item['code'] == $categoryType) {
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
                                            <?= Loc::getMessage('CREATE_COMPONENT_LBL_SUBCATEGORY') ?>
                                        </label>
                                        <select id="create-inp-subcategory" class="js-select" name="iblock"
                                                style="width: 100%">
                                            <?php
                                            if (strlen($categoryType) > 0 && $categoryId > 0) {
                                                foreach ($arResult['CATEGORIES'][$categoryType]['items'] as $key => $item) {
                                                    $selected = '';
                                                    if ($item['id'] == $categoryId) {
                                                        $selected = ' selected';
                                                    }
                                                    ?>
                                                    <option value="<?= $key ?>"<?= $selected ?>><?= $item['name'] ?></option>
                                                    <?php
                                                }
                                            } else {
                                                foreach (current($arResult['CATEGORIES'])['items'] as $key => $item) {
                                                    ?>
                                                    <option value="<?= $key ?>"><?= $item['name'] ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-xxs-12">
                                    <div class="form-group">
                                        <label for="create-inp-city">
                                            <?= Loc::getMessage('CREATE_COMPONENT_LBL_CITY') ?>
                                        </label>
                                        <select id="create-inp-city"
                                                class="js-select <?= (count($arResult['CITIES']) > 5) ? 'has-search' : '' ?>"
                                                name="city" required="" style="width: 100%">
                                            <?php
                                            foreach ($arResult['CITIES'] as $city) {
                                                $selected = '';
                                                if ($arResult['CITY_ID']) {
                                                    if ($city['id'] == $arResult['CITY_ID']) {
                                                        $selected = ' selected';
                                                    }
                                                } else {
                                                    if ($city['default'] > 0) {
                                                        $selected = ' selected';
                                                    }
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
                                            <?= Loc::getMessage('CREATE_COMPONENT_LBL_DATE') ?>
                                        </label>
                                        <div class="datepicker-wrap">
                                            <input name="dateStart" readonly="true" id="date-start"
                                                   class="form-control ui-datepicker"
                                                   type="text"
                                                   placeholder="<?= Loc::getMessage('CREATE_COMPONENT_PL_DATE_START') ?>">
                                            <input name="timeStart" autocomplete="off" placeholder="__:__" type="text"
                                                   class="time form-control">
                                        </div>
                                        <div class="tips">
                                            <a href="#" class="js-lnk presently">
                                                <?= Loc::getMessage('CREATE_COMPONENT_TODAY') ?>
                                            </a>,
                                            <a href="#" class="js-lnk tomorrow">
                                                <?= Loc::getMessage('CREATE_COMPONENT_TOMORROW') ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xxs-12">
                                    <div class="form-group">
                                        <label class="hidden-xxs hidden-xs">&nbsp;</label>
                                        <div class="datepicker-wrap">
                                            <input id="date-end" name="dateEnd" readonly="true"
                                                   class="form-control ui-datepicker"
                                                   type="text"
                                                   placeholder="<?= Loc::getMessage('CREATE_COMPONENT_PL_DATE_END') ?>">
                                            <input id="time-end" name="timeEnd" autocomplete="off" placeholder="__:__" type="text"
                                                   class="time form-control">
                                        </div>
                                        <div class="tips">
                                            <a href="#" class="js-lnk presently">
                                                <?= Loc::getMessage('CREATE_COMPONENT_TODAY') ?>
                                            </a>,
                                            <a href="#" class="js-lnk tomorrow">
                                                <?= Loc::getMessage('CREATE_COMPONENT_TOMORROW') ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="coordinates">
                                <div id="coordinates-fields"></div>
                                <div id="map-area"></div>
                            </div>
                            <div class="row" id="properties"></div>
                            <div class="ttl">
                                <?= Loc::getMessage('CREATE_COMPONENT_STAGE_TTL') ?>
                                <span class="hint-icon" data-tooltip="data-tooltip" data-html="true"
                                      data-container="body"
                                      data-title="<?= Loc::getMessage('CREATE_COMPONENT_STAGE_DESC') ?>"
                                      data-original-title="" title="">?</span>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 col-xxs-12">
                                    <div class="form-group">
                                        <input class="radio" type="radio" name="radioStage" id="radioStage1" checked
                                               value="0">
                                        <label class="form-radio" for="radioStage1">
                                            <span class="icon-wrap"></span>
                                            <?= Loc::getMessage('CREATE_COMPONENT_STAGE_1') ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xxs-12">
                                    <div class="form-group">
                                        <input class="radio" type="radio" name="radioStage" id="radioStage2" value="1">
                                        <label class="form-radio" for="radioStage2">
                                            <span class="icon-wrap"></span>
                                            <?= Loc::getMessage('CREATE_COMPONENT_STAGE_2') ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="total-budget">
                                <div class="col-sm-6 col-xxs-12">
                                    <div class="form-group">
                                        <label for="create-inp2">
                                            <?= Loc::getMessage('CREATE_COMPONENT_LBL_BUDGET') ?>
                                        </label>
                                        <input id="create-inp2" class="budget-inp form-control number-float" type="text"
                                               name="price">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xxs-12">
                                    <div class="form-group">
                                        <input class="checkbox contract-inp" type="checkbox" name="contractPrice"
                                               value="0" id="create-inp3"/>
                                        <label class="form-checkbox" for="create-inp3">
                                            <span class="icon-wrap">
                                                <svg class="icon icon_checkmark">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                                </svg>
                                            </span>
                                            <?= Loc::getMessage('CREATE_COMPONENT_LBL_BUDGET_AGREEMENT') ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div id="stages" class="stages form-group">
                                <div class="stages-list">
                                    <div class="itm">
                                        <div class="ttl bold">
                                            <?= Loc::getMessage('CREATE_COMPONENT_STAGE_STEP_TTL') ?>
                                            # 1
                                        </div>
                                        <div class="form-group">
                                            <label for="stage-name1">
                                                <?= Loc::getMessage('CREATE_COMPONENT_STAGE_LBL_TTL') ?>
                                            </label>
                                            <input required id="stage-name1" class="form-control required" type="text"
                                                   name="stages[1][name]">
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6 col-xxs-12">
                                                <div class="form-group">
                                                    <label for="stage-price1">
                                                        <?= Loc::getMessage('CREATE_COMPONENT_LBL_BUDGET') ?>
                                                    </label>
                                                    <input id="stage-price1"
                                                           class="budget-inp  form-control number-float" type="text"
                                                           name="stages[1][price]">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xxs-12">
                                                <div class="form-group">
                                                    <input class="contract-inp checkbox" type="checkbox"
                                                           name="stages[1][contractPrice]" value="0"
                                                           id="stage-contract1"/>
                                                    <label class="form-checkbox" for="stage-contract1">
                                            <span class="icon-wrap">
                                                <svg class="icon icon_checkmark">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                                </svg>
                                            </span>
                                                        <?= Loc::getMessage('CREATE_COMPONENT_LBL_BUDGET_AGREEMENT') ?>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-wrap text-right">
                                    <a href="#" class="btn btn-sm btn-orange">
                                        <?= Loc::getMessage('CREATE_COMPONENT_STAGE_ADD_BTN') ?>
                                    </a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="create-inp-desc">
                                    <?= Loc::getMessage('CREATE_COMPONENT_LBL_TTL_MES') ?>
                                </label>
                                <textarea required id="create-inp-desc" name="description" class="form-control required"
                                          placeholder="<?= Loc::getMessage('CREATE_COMPONENT_PL_MES') ?>"
                                          rows="5"></textarea>
                            </div>
                            <div class="form-group">
                                <div id="files-list" class="files">

                                </div>
                                <div id="btn-file" class="btn btn-sm btn-file">
                                    <span><?= Loc::getMessage('CREATE_COMPONENT_ADD_FILE') ?></span>
                                    <input type="file" name="__files[]">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>
                                    <?= Loc::getMessage('CREATE_COMPONENT_HIDDEN_FILES') ?>
                                </label>
                                <div class="files">

                                </div>
                                <div class="btn btn-sm btn-file">
                                    <span><?= Loc::getMessage('CREATE_COMPONENT_ADD_FILE') ?></span>
                                    <input type="file" name="__hiddenFiles[]">
                                </div>
                            </div>
                            <?php
                            if ($safeCrowEnabled) {
                                ?>
                                <div class="form-group">
                                    <input id="checkbox-security" class="checkbox checkbox-security" type="checkbox"
                                           name="security"/>
                                    <label class="form-checkbox" for="checkbox-security"><span class="icon-wrap">
                                        <svg class="icon icon_checkmark">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                        </svg>
                                    </span>
                                        <?= Loc::getMessage('CREATE_COMPONENT_SECURE_TRANSACTION') ?>
                                    </label>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="form-group">
                                <input class="checkbox required" required checked type="checkbox" name=""
                                       id="checkbox2"/>
                                <label class="form-checkbox" for="checkbox2"><span class="icon-wrap">
                                        <svg class="icon icon_checkmark">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                        </svg>
                                    </span>
                                    <?php
                                    echo Loc::getMessage(
                                        'CREATE_COMPONENT_POLICE',
                                        [
                                            '#SITE_DIR#' => SITE_DIR
                                        ]
                                    );
                                    ?>
                                </label>
                            </div>
                            <div class="btn-wrap">
                                <button class="btn btn-send  btn-green">
                                    <?= Loc::getMessage('CREATE_COMPONENT_BTN') ?>
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
            categories = JSON.parse('<?= \Bitrix\Main\Web\Json::encode($arResult['CATEGORIES'])?>'),
            selectVariant = '<?=Loc::getMessage('CREATE_COMPONENT_SELECT_VARIANT')?>',
            stageStepTTl = '<?=Loc::getMessage('CREATE_COMPONENT_STAGE_STEP_TTL')?>',
            stageNameLbl = '<?=Loc::getMessage('CREATE_COMPONENT_STAGE_LBL_TTL')?>',
            siteTemplatePach = '<?=SITE_TEMPLATE_PATH?>',
            contractor = '<?=Loc::getMessage('CREATE_COMPONENT_LBL_BUDGET_AGREEMENT')?>',
            stagePriceLbl = '<?=Loc::getMessage('CREATE_COMPONENT_LBL_BUDGET')?>',
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
            courierIblocks = JSON.parse('<?=\Bitrix\Main\Web\Json::encode($arResult['COURIER_IBLOCKS'])?>');
    </script>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}