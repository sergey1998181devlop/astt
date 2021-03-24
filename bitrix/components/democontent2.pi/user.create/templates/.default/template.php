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
                        <form method="post" class="new-task-create" enctype="multipart/form-data" action="<?= $APPLICATION->GetCurPage() ?>">
                            <div class="form-group changeNameBl ">
                                <?/*<label for="create-inp1">
                                    <?= Loc::getMessage('CREATE_COMPONENT_LBL_TTL') ?>
                                </label>*/?>
<!--                                <input required id="create-inp1" class="form-control required" type="text"-->
<!--                                       name="name" placeholder="--><?//= Loc::getMessage('CREATE_COMPONENT_PL_TTL') ?><!--"-->
<!--                                       value="--><?//=$arResult['CATEGORIES']['gruzopodemnaya-tekhnika']['name']?><!--&nbsp;">-->
                                <div id="create-inp1" class="form-control  titleNewOrder" type="text">
                                    <?/*<p class="titleNewTask category">Грузоподъемная техника</p>*/?>


                                    <?if(empty($_GET['category'])):?>
                                        <p class="titleNewTask Subcategory"></p>
                                    <?else:?>
                                        <p class="titleNewTask Subcategory"><?=$arResult['CATEGORIES'][$categoryType]['items'][$categoryId]['name']?></p>
                                    <?endif;?>
                                    <?/*<p class="titleNewTask city">Москва</p>*/?>
                                    <input hidden="hidden" class="autoloadName" name="name" value="">
                                    <input type="hidden" class="situHideProp" name="city" value="">
                                    



                                </div>
                                <div class="form-control-new">
                                    <p class="titleNewTask Subcategory5 "></p>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-sm-12 col-xxs-12">
                                    <div class="form-group">
                                        <label for="create-inp-category">
                                            <?= Loc::getMessage('CREATE_COMPONENT_LBL_CATEGORY') ?>
                                        </label>

                                        <select id="create-inp-category"  class="js-select required " style="width: 100%">
                                            <?if(empty($_GET['category'])):?> <option  class="notInp notInpCategory"  targCode="" value="">&nbsp;</option> <?endif;?>
                                            <?php
                                            foreach ($arResult['CATEGORIES'] as $key => $item) {
                                                $selected = '';
                                                if ($item['code'] == $categoryType) {
                                                    $selected = ' selected';
                                                }
                                                ?>

                                                <option targCode="<?=$item['code']?>" value="<?= $key ?>"<?= $selected ?>><?= $item['name'] ?></option>
                                                <?
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-xxs-12">
                                    <div class="form-group">
                                        <label for="create-inp-subcategory">
                                            <?= Loc::getMessage('CREATE_COMPONENT_LBL_SUBCATEGORY') ?>
                                        </label>
                                        <input name="iblock_code" value="" class="iblock_class_set" type="hidden">
                                        <select id="create-inp-subcategory"  <?if(empty($_GET['category'])):?>disabled="disabled"<?endif;?> class="js-select required" name="iblock"
                                                style="width: 100%">
                                            <?if(empty($_GET['category'])):?> <option targCode=""  class="notInp notInpSubCategory" value="">&nbsp;</option><?endif;?>
                                            <?php
                                            if (strlen($categoryType) > 0 && $categoryId > 0) {
                                                foreach ($arResult['CATEGORIES'][$categoryType]['items'] as $key => $item) {
                                                    $selected = '';
                                                    if ($item['id'] == $categoryId) {
                                                        $selected = ' selected';
                                                    }
                                                    ?>
                                                    <option targCode="<?=$item['code']?>"  value="<?= $key ?>"<?= $selected ?>><?= $item['name'] ?></option>
                                                    <?php
                                                }
                                            } else {
                                                foreach (current($arResult['CATEGORIES'])['items'] as $key => $item) {
                                                    ?>
                                                    <option targCode="<?=$item['code']?>" value="<?= $key ?>"><?= $item['name'] ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                             <?/*   <div class="col-sm-4 col-xxs-12">
                                    <div class="form-group">
                                        <label for="create-inp-city">
                                            <?= Loc::getMessage('CREATE_COMPONENT_LBL_CITY') ?>
                                        </label>
                                        <select id="create-inp-city" disabled="disabled"
                                                class="js-select <?= (count($arResult['CITIES']) > 5) ? 'has-search' : '' ?>"
                                                name="city" required="" style="width: 100%">
                                            <option class="notInp" value="">&nbsp;</option>
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
                                                echo '<option value="' . $city['id'] .'">' . $city['name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div> */?>
                            </div>
                            <div class="row" id="properties"></div>
                            <div class="row">
                                <div class="col-sm-6 col-xxs-12">
                                    <div class="form-group ">
                                        <label for="create-inp2">
                                           Колличество требуемой техники
                                        </label>
                                        <input  id="create-inpCount" class="form-control " type="text"
                                                name="nameCountTehn" maxlength="2" placeholder="Кол-во"
                                                value="">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="nameCountTehn_hidden" value="">
                            <div class="row">
                                <div class="col-sm-6 col-xxs-12">
                                    <div class="form-group dataCountday">
                                        <label for="date-start">
                                            <?= Loc::getMessage('CREATE_COMPONENT_LBL_DATE') ?>
                                        </label>
                                        <div class="datepicker-wrap">
                                            <input name="dateStart" required readonly="true" id="date-start"
                                                   class="form-control ui-datepicker"
                                                   type="text"
                                                   placeholder="<?= Loc::getMessage('CREATE_COMPONENT_PL_DATE_START') ?>">
                                            <input name="timeStart"   placeholder="__:__" autocomplete="off" type="text"
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
                                            <input name="dateEnd"  required id="date-end" disabled readonly="true"
                                                   class="form-control ui-datepicker"
                                                   type="text"
                                                   placeholder="<?= Loc::getMessage('CREATE_COMPONENT_PL_DATE_END') ?>">
                                         <input id="time-end"  type="hidden"  disabled name="timeEnd" placeholder="__:__" autocomplete="off"
                                                   type="text"
                                                   class="time form-control">

                                        </div>
                                   <?/*
                                        <div class="tips">
                                            <a href="#" class="js-lnk presently">
                                                <?= Loc::getMessage('CREATE_COMPONENT_TODAY') ?>
                                            </a>,
                                            <a href="#" class="js-lnk tomorrow">
                                                <?= Loc::getMessage('CREATE_COMPONENT_TOMORROW') ?>
                                            </a>
                                        </div>
                                    */?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="addressStreet" value="">
                            <div class="row" id="coordinates">
                                <div id="coordinates-fields"></div>
                                <div id="map-area" class="error"></div>
                            </div>
                            <?
                            //город / в script подбираем точку а как основной город заявки берем первый элемент
                            ?>
                            <input type="text" class="form-control required" name="hiddenIssetMap" value="" style="opacity: 0;position: absolute" required>


                            <label for="create-inp-countmoney">
                               <h3> <?= Loc::getMessage('CREATE_COMPONENT_LBL_BUDGET') ?></h3>
                            </label>
                            <div class="row" id="total-budget">

                                <div class="moneyBlock">
                                    <div class="col-sm-12 col-xxs-12">
                                        <div class="col-sm-6 col-xxs-12" style="padding-left: 0px">
                                            <div class="form-group">
                                                <label for="create-inp2">
                                                    <?= Loc::getMessage('COUNT_MONEY_ZA') ?>
                                                </label>
                                                <br>
                                                <select id="create-inp-countmoney" required  class="js-select" name="COUNT_MONEY_ZA"
                                                        style="width: 100%">
                                                    <option  class="countmoneyInpl " value="">&nbsp;</option>

                                                    <?if(!empty($arResult['COUNT_MONEY_ZA'][0]['ID']) ):?>
                                                        <?foreach ($arResult['COUNT_MONEY_ZA'] as $idCountMoney => $valueCountMon):?>
                                                            <option  class="countmoneyInpl " value="<?=$valueCountMon['NAME']?>"><?=$valueCountMon['NAME']?></option>

                                                        <?endforeach;?>
                                                    <?endif;?>

                                                </select>
                                            </div>
                                        </div>
                                    </div>




                                    <div class="col-sm-12 col-xxs-12">
                                            <div class="col-sm-6 col-xxs-12">
                                                <div class="form-group">
                                                    <label for="create-inp2">
                                                        <?= Loc::getMessage('CREATE_COMPONENT_LBL_BEZNAL') ?>
                                                    </label>
                                                    <input  id="create-inp1" class="form-control " type="text"
                                                           name="nameBeznal" placeholder="<?= Loc::getMessage('CREATE_COMPONENT_LBL_BEZNAL_SUM') ?>"
                                                           value="">
                                                </div>
                                            </div>
                                    </div>
                                    <div class="col-sm-12 col-xxs-12">
                                        <div class="col-sm-6 col-xxs-12">
                                            <div class="form-group">
                                                <label for="create-inp1">
                                                    <?= Loc::getMessage('CREATE_COMPONENT_LBL_BUDGET_BEZ_NDS') ?>
                                                </label>
                                                <input  id="create-inp1" class="form-control " type="text"
                                                       name="contractPrice" placeholder="<?= Loc::getMessage('CREATE_COMPONENT_LBL_BEZNAL_SUM') ?>"
                                                       value="">
                                            </div>
                                        </div>
                                    </div>




                                <div class="col-sm-6 col-xxs-12">
                                    <div class="form-group">
                                        <label for="create-inp2">
                                            <?= Loc::getMessage('CREATE_COMPONENT_LBL_NAL') ?>
                                        </label>
                                        <input  id="create-inp1" class="form-control " type="text"
                                               name="nameNal" placeholder="<?= Loc::getMessage('CREATE_COMPONENT_LBL_BEZNAL_SUM') ?>"
                                               value="">
                                    </div>
                                </div>

                                </div>

                                <div class="col-sm-12 col-xxs-12">
                                    <div class="form-group">
                                        <input class="checkbox contract-inp" type="checkbox" name="contractPriceDOGOVOR"
                                               value="0" id="create-inpD3"/>
                                        <label class="form-checkbox" for="create-inpD3">
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

                                <textarea  id="create-inp-desc"   minlength="15" name="description" class="form-control required crt-inpt-get"
                                          placeholder="<?= Loc::getMessage('CREATE_COMPONENT_PL_MES') ?>"
                                          rows="5"></textarea>
                            </div>
                            <h5><?= Loc::getMessage('CREATE_COMPONENT_PUBLIC_FILES_TITLE') ?></h5>
                            <div class="form-group">
                                <label>
                                    <?= Loc::getMessage('CREATE_COMPONENT_PUBLIC_FILES_LABEL') ?>
                                </label>
                                <div id="files-list" class="files"></div>
                                <div id="btn-file" class="btn btn-sm btn-file">
                                    <span><?= Loc::getMessage('CREATE_COMPONENT_ADD_FILE') ?></span>
                                    <input type="file" name="__files[]">
                                </div>
                            </div>
                     <?/*       <h5><?= Loc::getMessage('CREATE_COMPONENT_HIDDEN_FILES_TITLE') ?></h5>
                            <div class="form-group">
                                <label>
                                    <?= Loc::getMessage('CREATE_COMPONENT_HIDDEN_FILES') ?>
                                </label>
                                <div class="files"></div>
                                <div class="btn btn-sm btn-file">
                                    <span><?= Loc::getMessage('CREATE_COMPONENT_ADD_FILE') ?></span>
                                    <input type="file" name="__hiddenFiles[]">
                                </div>
                            </div>
                    */?>
                            <?php
                            if ($arResult['ALLOW_CHECKLISTS']) {
                                ?>
                                <h5><?= Loc::getMessage('CREATE_COMPONENT_RESPONSE_CHECKLIST_TITLE') ?></h5>
                                <div class="form-group">
                                    <label>
                                        <?= Loc::getMessage('CREATE_COMPONENT_RESPONSE_CHECKLIST_LABEL') ?>
                                    </label>
                                    <div id="response-checklists">
                                        <?php
                                        $i = 0;
                                        while ($i++ < 5) {
                                            ?>
                                            <br>
                                            <input type="text" class="form-control" name="response-checklist[]"
                                                   placeholder="<?= Loc::getMessage('CREATE_COMPONENT_RESPONSE_CHECKLIST_LABEL_EXAMPLE') ?>">
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

                            <?if ($USER->IsAuthorized()):?>
                                <input hidden="hidden"  name="AuthCheck" value="AuthYes">
                            <?else:?>
                                <input hidden="hidden" name="AuthCheck" value="AuthNot">
                            <?endif;?>

                            <input hidden="hidden" name="assetFormTask" value="Y">
                            <div class="btn-wrap ">

                                <div  class="block-btn-send btn-send  btn-green "  >
                                    <?= Loc::getMessage('CREATE_COMPONENT_BTN') ?>
                                </div>

<!--                                <div class=" btn-send  btn-green block-btn-send " >-->
<!--                                    --><?//= Loc::getMessage('CREATE_COMPONENT_BTN') ?>
<!--                                </div>-->

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
            datepickerLoc = '<?=Loc::getMessage('EDIT_COMPONENT_DATEPICKER')?>',
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