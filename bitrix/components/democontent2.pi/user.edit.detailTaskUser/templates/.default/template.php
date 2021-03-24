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
//pre($arResult);
?>

    <div class="page-content">
        <div class="wrapper">
<!--            <div class="page-title">-->
<!--                <h1>-->
<!--                    Редактирование заявки-->
<!--                </h1>-->
<!--            </div>-->
            <div class="row">
                <div class="col-sm-4 col-md-3 col-xxs-12 pull-left">

                        <?php
                        $APPLICATION->IncludeComponent(
                            'democontent2.pi:user.menu',
                            ''
                        );
                        ?>

                </div>
  
                <div class="col-sm-12 col-md-9 col-lg-8 col-xxs-12">
                    <div class="white-block adding-job">
                                        <h1>
                                            Редактирование заявки
                                        </h1>
                        <form method="post" enctype="multipart/form-data" action="<?= $APPLICATION->GetCurPage() ?>">
                            <div class="form-group changeNameBl ">
                                <?/*<label for="create-inp1">
                                    <?= Loc::getMessage('CREATE_COMPONENT_LBL_TTL') ?>
                                </label>*/?>
<!--                                <input required id="create-inp1" class="form-control required" type="text"-->
<!--                                       name="name" placeholder="--><?//= Loc::getMessage('CREATE_COMPONENT_PL_TTL') ?><!--"-->
<!--                                       value="--><?//=$arResult['CATEGORIES']['gruzopodemnaya-tekhnika']['name']?><!--&nbsp;">-->
                                <div id="create-inp1" class="form-control  titleNewOrder" type="text">
                                    <?/*<p class="titleNewTask category">Грузоподъемная техника</p>*/?>

                                    <?if(!empty($arResult['UF_IBLOCK_CODE'])):?>
                                    <?
//                                    pre($arResult['CATEGORIES'][$arResult['UF_IBLOCK_TYPE']]);
                                    $name = '';
                                        foreach ($arResult['CATEGORIES'][$arResult['UF_IBLOCK_TYPE']]['items'] as $id => $valCod){
                                            if($valCod['code'] ==  $arResult['UF_IBLOCK_CODE']){
                                                $name = $valCod['name'];
                                            }
                                        }
                                    ?>
                                        <p class="titleNewTask Subcategory"><?=$name?></p>
                                    <?endif;?>
                                    <?if(!empty($arResult['CHARACTERISTIC_1']['VALUE'])):?>
                                        <p class="titleNewTask SubcategoryType"><?=$arResult['CHARACTERISTIC_1']['DESCRIPTION']?></p>
                                    <?endif;?>
                                    <?if(!empty($arResult['CHARACTERISTIC_2']['VALUE'])):?>
                                        <p class="titleNewTask SubcategoryType2"><?=$arResult['CHARACTERISTIC_2']['DESCRIPTION']?></p>
                                    <?endif;?>
                                    <?if(!empty($arResult['CHARACTERISTIC_3']['VALUE'])):?>
                                        <p class="titleNewTask SubcategoryType3"><?=$arResult['CHARACTERISTIC_3']['DESCRIPTION']?></p>
                                    <?endif;?>

                                    <?if(empty($_GET['category'])):?>

                                    <?else:?>
                                        <p class="titleNewTask Subcategory"><?=$arResult['CATEGORIES'][$categoryType]['items'][$categoryId]['name']?></p>
                                    <?endif;?>
                                    <?/*<p class="titleNewTask city">Москва</p>*/?>
                                    <input hidden="hidden" class="autoloadName" name="name" value="">



                                </div>


                                <?if(!empty($arResult['UF_COUNT_TECH'])):?>
                                    <div class="form-control-new">
                                        <p class="titleNewTask Subcategory5 "><?=$arResult['UF_COUNT_TECH']?> шт</p>
                                    </div>
                                <?endif;?>
                            </div>
                            <div class="row ">
                                <div class="col-sm-12 col-xxs-12">
                                    <div class="form-group">
                                        <label for="create-inp-category">
                                            <?= Loc::getMessage('CREATE_COMPONENT_LBL_CATEGORY') ?>
                                        </label>

                                        <select id="create-inp-category"  class="js-select required " style="width: 100%">
                                            <?
                                            $GLOBALS['codeBlock'] = '';
                                            ?>
                                            <?if(empty($_GET['category'])):?> <option  class="notInp notInpCategory"  targCode="" value="">&nbsp;</option> <?endif;?>
                                            <?php
                                            foreach ($arResult['CATEGORIES'] as $key => $item) {
                                                $selected = '';
                                                if ($item['code'] == $arResult['UF_IBLOCK_TYPE']) {
                                                    $selected = ' selected';
                                                    $GLOBALS['codeBlock'] = $item['code'];
                                                }
                                                ?>

                                                <option targCode="<?=$item['code']?>" value="<?= $key ?>"<?= $selected ?>><?= $item['name'] ?></option>
                                                <?
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <?pre($arResult['CATEGORIES'][$categoryType]['items']);?>
                                <div class="col-sm-12 col-xxs-12">
                                    <div class="form-group">
                                        <label for="create-inp-subcategory">
                                            <?= Loc::getMessage('CREATE_COMPONENT_LBL_SUBCATEGORY') ?>
                                        </label>
                                        <input name="iblock_code" <?if(!empty($arResult['UF_IBLOCK_CODE'])):?>value="<?=$arResult['UF_IBLOCK_CODE']?>"<?endif;?> class="iblock_class_set" type="hidden">


                                        <?

                                        ?>
                                        <select id="create-inp-subcategory"   class="js-select required" name="iblock"
                                                style="width: 100%">



                                            <?php

                                                foreach ($arResult['CATEGORIES'][$GLOBALS['codeBlock']]['items'] as $key => $item) {
                                                    $selected = '';
                                                    if($item == $arResult['UF_IBLOCK_CODE']){
                                                        $selected = ' selected';
                                                    }
                                                    ?>
                                                    <option targCode="<?=$item['code']?>" value="<?= $key ?>" <?= $selected ?> ><?= $item['name'] ?></option>
                                                    <?php
                                                }

                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <?



                                ?>
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
                                        <?if(!empty($arResult['UF_COUNT_TECH'])):?>
                                        <input  id="create-inpCount" class="form-control " type="text"
                                                name="nameCountTehn" maxlength="2" placeholder="Кол-во"
                                                value="<?=$arResult['UF_COUNT_TECH']?>">
                                        <?else:?>
                                            <input  id="create-inpCount" class="form-control " type="text"
                                                    name="nameCountTehn" maxlength="2" placeholder="Кол-во"
                                                    value="">
                                        <?endif;?>


                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 col-xxs-12">
                                    <div class="form-group dataCountday">
                                        <label for="date-start">
                                            <?= Loc::getMessage('CREATE_COMPONENT_LBL_DATE') ?>
                                        </label>
                                        <div class="datepicker-wrap">
                                            <input name="dateStart" <?if(!empty($arResult['UF_DATA_START_STRING'])):?>value="<?=$arResult['UF_DATA_START_STRING']?>"<?endif;?>
                                                   required readonly="true" id="date-start"
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
                                            <input name="dateEnd"
                                                   <?if(!empty($arResult['UF_DATA_END_STRING'])):?>value="<?=$arResult['UF_DATA_END_STRING']?>"<?endif;?>
                                                   required id="date-end" disabled readonly="true"
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
                                <?
                                $checkedDogovor = '';
                                if(empty($arResult['UF_NALL']) && empty($arResult['UF_BEZNAL']) && empty($arResult['UF_NDS']) ){
                                    $checkedDogovor = 'Y';
                                }
                                ?>
                                <div class="moneyBlock" <?if($checkedDogovor == 'Y'):?>  style="display: none" <?endif;?>>
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
                                                            <option  class="countmoneyInpl "
                                                                     <?if($arResult['HIDDEN_PRICE_VALUE'] == $valueCountMon['NAME']):?>
                                                                        selected
                                                                    <?endif;?>
                                                                     value="<?=$valueCountMon['NAME']?>"><?=$valueCountMon['NAME']?></option>

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
                                                        <?if(!empty($arResult['UF_BEZNAL'])):?>
                                                            value="<?=$arResult['UF_BEZNAL']?>"
                                                        <?else:?>
                                                            value=""
                                                        <?endif;?>
                                                    >
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
                                                    <?if(!empty($arResult['UF_NDS'])):?>
                                                        value="<?=$arResult['UF_NDS']?>"
                                                    <?else:?>
                                                        value=""
                                                    <?endif;?>
                                                >
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
                                                <?if(!empty($arResult['UF_NALL'])):?>
                                               value="<?=$arResult['UF_NALL']?>"
                                                <?else:?>
                                                    value=""
                                                <?endif;?>
                                        >
                                    </div>
                                </div>

                                </div>


                                <div class="col-sm-12 col-xxs-12">
                                    <div class="form-group">
                                        <input class="checkbox contract-inp" type="checkbox" <?if($checkedDogovor == 'Y'):?>checked<?endif;?> name="contractPriceDOGOVOR"
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
                                        <div class="ttl bold">
                            <?= Loc::getMessage('DETAIL_COMPONENT_FILES_TITLE') ?>:
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

                                <textarea  id="create-inp-desc"   minlength="60" name="description" class="form-control required crt-inpt-get"
                                          placeholder="<?= Loc::getMessage('CREATE_COMPONENT_PL_MES') ?>"
                                          rows="5"><?if(!empty($arResult['UF_DESCRIPTION'])):?><?=$arResult['UF_DESCRIPTION']?><?endif;?></textarea>
                            </div>
                            <h5><?= Loc::getMessage('CREATE_COMPONENT_PUBLIC_FILES_TITLE') ?></h5>

                            <?php
                            $messImages = [];
                            if (count($arResult['FILES']) > 0) {

                                ?>
                                <div class="attachment-picts row">
                                    <?php
                                    foreach ($arResult['FILES'] as $image) {


                                        $imageThumb = CFile::ResizeImageGet(
                                            $image,
                                            [
                                                'width' => 90,
                                                'height' => 90
                                            ],
                                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                            true
                                        );
                                        $pathOriginal  = CFile::GetPath($image);
                                        if(!empty($pathOriginal)){
//                                            pre($image);
                                            $messImages[] = $image;
                                        ?>
                                        <div class="item">
                                            <a href="<?=$pathOriginal?>"
                                               data-fancybox="gallery-attachment">
                                                <div class="in">
                                                    <div class="object-fit-container">
                                                        <img src="<?= $imageThumb['src'] ?>"
                                                             alt="" data-object-fit="cover"
                                                             data-object-position="50% 50%"/>
                                                    </div>
                                                </div>
                                            </a>
                                            <div class="btn btn-red solution-edit-task event-del-photoNew-edit" data-id-delPhoto="<?=$image?>" >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    <?php
                                        }

                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            ?>
                            <input type="hidden" name="dopPrewiewFiles" value="<?=implode(',' , $messImages)?>">

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
                            <input hidden="hidden" name="assetFormTask" value="Y">
                            <div class="btn-wrap ">
                                <button class="block-btn-send btn-send  btn-green " type="button" >
                                    <?= Loc::getMessage('CREATE_COMPONENT_BTN') ?>
                                </button>

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
<?
$coordination = json_encode($arResult['COORDINATION_ITEMS']);


?>
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
            coordinationS = '<?=$coordination?>',

            courierIblocks = JSON.parse('<?=\Bitrix\Main\Web\Json::encode($arResult['COURIER_IBLOCKS'])?>');
    </script>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}