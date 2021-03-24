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

if(!empty($arResult['COMPANY'][0]['UF_STATUS_MODERATION']) && $arResult['COMPANY'][0]['UF_STATUS_MODERATION'] != "0"){
    $statusCompany = (integer) $arResult['COMPANY'][0]['UF_STATUS_MODERATION'];
}else{


    if($arResult['COMPANY'][0]['UF_STATUS_MODERATION'] == ''){
        $statusCompany = '';
    }else{
        $statusCompany = "0";
    }
}



?>

    <div class="page-content">
        <div class="wrapper">
            <div class="page-title">
                <h1>
                    <?= Loc::getMessage('HEADER_H_1') ?>
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
                    <div class="sorty-panel tabs-head">
                        <ul>
                            <li class="active">
                                <a href="#user-company">
                                    Карточка компании
                                </a>

                            </li>
                            <li style="display:none;">
                                <a href="#user-profile">
                                    Специлизации
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="tabs-wrap">

<?if(!empty($arResult['COMPANY'][0]['UF_STATUS_FALSE_MODERATION']) || !empty($arResult['COMPANY'][0]['UF_STATUS_FALSE_MODERATION2']) || !empty($arResult['COMPANY'][0]['UF_STATUS_FALSE_MODERATION3'])):?>
                        <div class="sticky-contaier-new sticky-block" data-container="#sticky-container" >
                            <div class="user-card-block white-block " data-container="#sticky-container">


                            <div class="user-card-block white-block " data-container="#sticky-container">
                                <div class="alert-success-error  ">
                                    <div class="listReason">
                                        <ul class="refusal_app_form">
                                            <?if(!empty($arResult['COMPANY'][0]['UF_STATUS_FALSE_MODERATION'])):?>
                                                <li><?=$arResult['COMPANY'][0]['UF_STATUS_FALSE_MODERATION']?></li>
                                            <?endif;?>
                                            <?if(!empty($arResult['COMPANY'][0]['UF_STATUS_FALSE_MODERATION2'])):?>
                                                <li><?=$arResult['COMPANY'][0]['UF_STATUS_FALSE_MODERATION2']?></li>
                                            <?endif;?>
                                            <?if(!empty($arResult['COMPANY'][0]['UF_STATUS_FALSE_MODERATION3'])):?>
                                                <li><?=$arResult['COMPANY'][0]['UF_STATUS_FALSE_MODERATION3']?></li>
                                            <?endif;?>
                                        </ul>
                                    </div>

                                </div>


                                <br>
                                <div class="text-center" id="chatButton"></div>
                            </div>

                         </div>
                        </div>
<?endif;?>




                        <div id="user-company" class="tab active">


                                <?if(!empty($arResult['USER']['NAME']) && !empty($arResult['USER']['PERSONAL_PHONE']) ):?>
                                    <div class="white-block profile-content">


                                        <div class="tabs-wrap">
                                            <div class="tab active" id="tab-profile1">


                                                <div class="h4">
                                                    <?= Loc::getMessage('USER_SETTINGS_COMPONENT_AVA_TITLE_COMPANY') ?>
                                                </div>

                                                <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="post" enctype="multipart/form-data" id="CompanyNew" >
                                                    <div class="row">




                                                        <div class="col-lg-12 col-sm-12 col-xxs-12">
                                                            <div class="form-group <?if($arResult['COMPANY']['MODERATION_IS_ACTIVE'] == "ACTIVE"):?>issetPrevInn<?endif;?> ">
                                                                <label class="bold">
                                                                    <?= Loc::getMessage('COMPANY_INN') ?>
                                                                </label>


                                                                <select data-path="/ajax/ajaxIssetCompany.php"
                                                                        name="INN"
                                                                        id="searchCompany"
                                                                        class=" form-control js-data-example-ajax"
                                                                    <?if($statusCompany == 1 || $statusCompany == 2):?>
                                                                        disabled
                                                                    <?endif;?>
                                                                    <?if($arResult['COMPANY']['MODERATION_IS_ACTIVE'] == "ACTIVE"):?>
                                                                        data-placeholder='&nbsp;<?=$arResult['COMPANY'][0]['UF_NUMBER_INN']?>&nbsp;'
                                                                    <?endif;?>>
                                                                </select>
                                                                <div class="error-text searchCompanyErrorText"></div>
                                                                <input type="hidden" name="INN" class="INNPASS" required value="<?=$arResult['COMPANY'][0]['UF_NUMBER_INN']?>">



                                                            </div>


                                                        </div>


                                                        <div class="more-properties-company"
                                                            <?if($arResult['COMPANY']['MODERATION_IS_ACTIVE'] == "ACTIVE"):?>
                                                                style="display: block"
                                                            <?else:?>
                                                                style="display: none"
                                                            <?endif;?>
                                                        >



                                                            <div class="col-lg-12 col-sm-12 col-xxs-12">
                                                                <div class="form-group">
                                                                    <label class="bold">
                                                                        <?= Loc::getMessage('COMPANY_NAME_COMPANY') ?>
                                                                    </label>
                                                                    <?if($arResult['COMPANY']['MODERATION_IS_ACTIVE'] == "ACTIVE"):?>
                                                                        <input type="text"  class="form-control" required name="UF_COMPANY_NAME_MIN"
                                                                            <?if($statusCompany == 1 || $statusCompany == 2):?>
                                                                                disabled
                                                                            <?endif;?>
                                                                            <?if(!empty($arResult['COMPANY'][0]['UF_COMPANY_NAME_MIN'])):?>
                                                                                value='<?=$arResult['COMPANY'][0]['UF_COMPANY_NAME_MIN']?>'
                                                                            <?endif;?>
                                                                        >
                                                                    <?else:?>
                                                                        <input type="text" name="UF_COMPANY_NAME_MIN" class=" form-control " value="">
                                                                    <?endif;?>
                                                                </div>
                                                            </div>


                                                            <div class="col-lg-12 col-sm-12 col-xxs-12">
                                                                <div class="form-group ">
                                                                    <label class="bold">
                                                                        <?= Loc::getMessage('COMPANY_NAME_BIG_NAME') ?>
                                                                    </label>
                                                                    <?if($arResult['COMPANY']['MODERATION_IS_ACTIVE'] == "ACTIVE"):?>
                                                                        <input type="text"  class="form-control" required name="UF_BIG_NAME"
                                                                            <?if($statusCompany == 1 || $statusCompany == 2):?>
                                                                                disabled
                                                                            <?endif;?>
                                                                            <?if(!empty($arResult['COMPANY'][0]['UF_BIG_NAME'])):?>
                                                                                value='<?=$arResult['COMPANY'][0]['UF_BIG_NAME']?>'
                                                                            <?endif;?>
                                                                        >
                                                                    <?else:?>
                                                                        <input type="text" name="UF_BIG_NAME"  class=" form-control " value="">
                                                                    <?endif;?>
                                                                </div>
                                                            </div>




                                                            <div class="col-lg-6 col-sm-6 col-xxs-12" style="display: none">
                                                                <div class="form-group">
                                                                    <label class="bold">
                                                                        <?= Loc::getMessage('TYPE_COMPANY') ?>
                                                                    </label>

                                                                    <select name="type_company"
                                                                        <?if($statusCompany == 1 || $statusCompany == 2):?>
                                                                            disabled
                                                                        <?endif;?>
                                                                        <?if($arResult['COMPANY'][0]['UF_COMPANY_TYPE'] == "UR" || $arResult['COMPANY'][0]['UF_COMPANY_TYPE'] == "IP" ): ?>
                                                                            disabled
                                                                        <?endif;?>
                                                                            style="width: 100%;" class="js-select">
                                                                        <option value="UR" <?if($arResult['COMPANY'][0]['UF_COMPANY_TYPE'] == "UR"):?>selected<?endif;?> >Юр. Лицо</option>
                                                                        <option value="IP" <?if($arResult['COMPANY'][0]['UF_COMPANY_TYPE'] == "IP"):?>selected<?endif;?> >ИП</option>
                                                                        <!--                                                        <option name="SAMOZANAD_P" value="SAMOZANAD">Самозанятый</option>-->
                                                                    </select>

                                                                </div>
                                                            </div>

                                                            <div class="col-lg-6 col-sm-6 col-xxs-12">
                                                                <div class="form-group">
                                                                    <label class="bold">
                                                                        <?= Loc::getMessage('COMPANY_OGRN') ?>
                                                                    </label>
                                                                    <input class="form-control kolvo_type_num" required type="text"
                                                                        <?if($statusCompany == 1 || $statusCompany == 2):?>
                                                                            disabled
                                                                        <?endif;?>
                                                                        <?if(!empty($arResult['COMPANY'][0]['UF_NUMBER_OGRN'])):?>
                                                                            value="<?=$arResult['COMPANY'][0]['UF_NUMBER_OGRN']?>"
                                                                        <?endif;?>
                                                                           name="OGRN"
                                                                           placeholder="<?= Loc::getMessage('COMPANY_OGRN_OPTION') ?>"
                                                                    >
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-sm-6 col-xxs-12">
                                                                <div class="form-group">
                                                                    <label class="bold">
                                                                        <?= Loc::getMessage('COMPANY_KPP') ?>
                                                                    </label>
                                                                    <input class="form-control kolvo_type_num" required type="text"
                                                                        <?if($statusCompany == 1 || $statusCompany == 2):?>
                                                                            disabled
                                                                        <?endif;?>
                                                                        <?if(!empty($arResult['COMPANY'][0]['UF_KOD_KPP'])):?>
                                                                            value="<?=$arResult['COMPANY'][0]['UF_KOD_KPP']?>"
                                                                        <?endif;?> name="KPP"
                                                                           placeholder="<?= Loc::getMessage('COMPANY_KPP_OPTION') ?>"
                                                                    >
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-sm-6 col-xxs-12">
                                                                <div class="form-group">
                                                                    <label class="bold">
                                                                        <?= Loc::getMessage('COMPANY_OKPO') ?>
                                                                    </label>
                                                                    <input class="form-control kolvo_type_num"  required type="text"
                                                                        <?if($statusCompany == 1 || $statusCompany == 2):?>
                                                                            disabled
                                                                        <?endif;?>
                                                                        <?if(!empty($arResult['COMPANY'][0]['UF_KOD_OKPO'])):?>
                                                                            value="<?=$arResult['COMPANY'][0]['UF_KOD_OKPO']?>"
                                                                        <?endif;?>  name="OKPO"
                                                                           placeholder="<?= Loc::getMessage('COMPANY_OKPO_OPTION') ?>"
                                                                    >
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-sm-6 col-xxs-12">
                                                                <div class="form-group">
                                                                    <label class="bold">
                                                                        <?= Loc::getMessage('COMPANY_GEN_DIR') ?>
                                                                    </label>
                                                                    <input class="form-control" required type="text"
                                                                        <?if($statusCompany == 1 || $statusCompany == 2):?>
                                                                            disabled
                                                                        <?endif;?>
                                                                        <?if(!empty($arResult['COMPANY'][0]['UF_FIO_GENDIR'])):?>
                                                                            value="<?=$arResult['COMPANY'][0]['UF_FIO_GENDIR']?>"
                                                                        <?endif;?>  name="GEN_DERECTOR"
                                                                           placeholder="<?= Loc::getMessage('COMPANY_GEN_DIR_OPTION') ?>"
                                                                    >
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12 col-sm-12 col-xxs-12">
                                                                <div class="form-group">
                                                                    <label class="bold">
                                                                        <?= Loc::getMessage('COMPANY_UR_ADDRESS') ?>
                                                                    </label>
                                                                    <input class="form-control" required type="text"
                                                                        <?if($statusCompany == 1 || $statusCompany == 2):?>
                                                                            disabled
                                                                        <?endif;?>
                                                                        <?if(!empty($arResult['COMPANY'][0]['UF_UR_ADDRESS'])):?>
                                                                            value="<?=$arResult['COMPANY'][0]['UF_UR_ADDRESS']?>"
                                                                        <?else:?>
                                                                            value=""
                                                                        <?endif;?>
                                                                           name="UR_ADDRESS"
                                                                           placeholder="<?= Loc::getMessage('COMPANY_UR_ADDRESS_OPTION') ?>"
                                                                    >
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12 col-sm-12 col-xxs-12">
                                                                <div class="form-group">
                                                                    <label class="bold">
                                                                        <?= Loc::getMessage('COMPANY_FACT_ADDRESS') ?>
                                                                    </label>
                                                                    <input class="form-control" type="text"
                                                                        <?if($statusCompany == 1 ):?>
                                                                            disabled
                                                                        <?endif;?>
                                                                        <?if(!empty($arResult['COMPANY'][0]['UF_REAL_ADDRESS'])):?>
                                                                            value="<?=$arResult['COMPANY'][0]['UF_REAL_ADDRESS']?>"
                                                                        <?else:?>
                                                                            value=""
                                                                        <?endif;?>
                                                                           name="FACT_ADDRESS"
                                                                           placeholder="<?= Loc::getMessage('COMPANY_FACT_ADDRESS_OPTION') ?>"
                                                                    >
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-sm-6 col-xxs-12">
                                                                <div class="form-group">
                                                                    <label class="bold">
                                                                        <?= Loc::getMessage('COMPANY_PHONE_ADDRESS') ?>
                                                                    </label>
                                                                    <input class="form-control" required  type="text"
                                                                        <?if($statusCompany == 1 ):?>
                                                                            disabled
                                                                        <?endif;?>
                                                                        <?if(!empty($arResult['COMPANY'][0]['UF_PHONE'])):?>
                                                                            value="<?=$arResult['COMPANY'][0]['UF_PHONE']?>"
                                                                        <?endif;?> name="authPhone"
                                                                           placeholder="<?= Loc::getMessage('COMPANY_FACT_PHONE_OPTION') ?>"
                                                                    >
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-sm-6 col-xxs-12">
                                                                <div class="form-group">
                                                                    <label class="bold">
                                                                        <?= Loc::getMessage('COMPANY_EMAIL') ?>
                                                                    </label>
                                                                    <input class="form-control" required type="email"
                                                                        <?if($statusCompany == 1 ):?>
                                                                            disabled
                                                                        <?endif;?>
                                                                        <?if(!empty($arResult['COMPANY'][0]['UF_EMAIL'])):?>
                                                                            value="<?=$arResult['COMPANY'][0]['UF_EMAIL']?>"
                                                                        <?else:?>
                                                                            value=""
                                                                        <?endif;?>
                                                                           name="EMAIL"
                                                                           placeholder="<?= Loc::getMessage('COMPANY_EMAIL_OPTION') ?>"
                                                                    >
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12 col-sm-12 col-xxs-12">
                                                                <div class="form-group">
                                                                    <label class="bold">
                                                                        Сайт компании
                                                                    </label>
                                                                    <input class="form-control" required type="text"
                                                                        <?if($statusCompany == 1 ):?>
                                                                            disabled
                                                                        <?endif;?>
                                                                        <?if(!empty($arResult['COMPANY'][0]['UF_SITE_LINK'])):?>
                                                                            value="<?=$arResult['COMPANY'][0]['UF_SITE_LINK']?>"
                                                                        <?else:?>
                                                                            value=""
                                                                        <?endif;?>
                                                                           name="UF_SITE_LINK"
                                                                           placeholder="Ссылка на сайт компании"
                                                                    >
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12 col-sm-12 col-xxs-12">
                                                                <div class="form-group">
                                                                    <label class="bold">
                                                                        <?= Loc::getMessage('COMPANY_DETAIL_OPTION') ?>
                                                                    </label>
                                                                    <textarea  id="create-inp-desc" required name="COMPANY_DESCRIPTION" class="form-control "
                                                                               placeholder="<?= Loc::getMessage('COMPANY_DETAIL_OPTION_M') ?>"
                                                                <?if($statusCompany == 1 ):?>
                                                                    disabled
                                                                <?endif;?>
                                                                        <?if(!empty($arResult['COMPANY'][0]['UF_DESCRIPTION'])):?>
                                                                value="<?=$arResult['COMPANY'][0]['UF_DESCRIPTION']?>"
                                                                               <?endif;?>rows="5"><?if(!empty($arResult['COMPANY'][0]['UF_DESCRIPTION'])):?><?=$arResult['COMPANY'][0]['UF_DESCRIPTION']?><?endif;?></textarea>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-12 col-sm-12 col-xxs-12">
                                                                <div class="form-group">
                                                                    <label for="create-inp-category">
                                                                        Тип Компании
                                                                    </label>
                                                                    <?
                                                                    $ar_executor = array(
                                                                           "customer" =>  "Заказчик",
                                                                           "supplier" =>  "Поставщик",
                                                                           "cu-supplier" =>  "Заказчик-поставщик"
                                                                    );
                                                                    ?>
                                                                    <select id="" name="UF_EXECUTOR" class="js-select  " style="width: 100%"
                                                                        <?if($statusCompany == 1 ):?>
                                                                            disabled
                                                                        <?endif;?>
                                                                    >
                                                                        <?$supplier = false;?>

                                                                        <?foreach ($ar_executor as $idExec => $valExec){?>
                                                                            <option value="<?=$idExec?>" <?if($arResult['COMPANY'][0]['UF_EXECUTOR'] == $idExec):?>selected<?endif;?>><?=$valExec?></option>
                                                                        <?}?>
                                                                    </select>
                                                                </div>
                                                            </div>
<?
//$firstKey = array_key_first($my_array);

?>
                                                        <div class="col-lg-12 col-sm-12 col-xxs-12 specialization-list-owr">

                                                            <div class="specialization-list">
                                                                <ul>
                                                                    <?php
                                                                    $i = 0;
                                                                    $curKey = 1;
                                                                    foreach ($arResult['MENU'] as $k => $item) {
                                                                        if (!count($item['items'])) {
                                                                            continue;
                                                                        }
                                                                        $i++;
                                                                        ?>
                                                                            <?
                                                                                if($curKey % 2 == 1){
                                                                            ?>
                                                                                    <div class="spec-block-list-twe col-lg-6 col-sm-6 col-xxs-6">
                                                                                        <?}
                                                                                ?>
                                                                        <li class="">
                                                                            <?
                                                                            //если есть один хотя бы чекнутый подпункт  / то ставлю бекраграунд / чтобы отличие было инпутов / если отмечены все то проставляю
                                                                            //галочку на заглавный пункт
                                                                            $checked = false ;
                                                                            $checkedCount = '';
                                                                            $point = false;
                                                                            $countAllEll = count($item['items']);
                                                                            foreach ($item['items'] as $subitem) {
                                                                                $i++;
                                                                                //если один хотя бы чекнут  -> тогда проставляю что он чекед и у заглавного будет бекграунд и ставлю метку чтобы не попадать на след
                                                                                //итерациях на это условие
                                                                                if (isset($arResult['SUBSCRIPTIONS'][$subitem['id']]) && $point == false) {
                                                                                    $checked = 'checked';
                                                                                    $point = true;
//                                                                                    break;
                                                                                }
                                                                                if (isset($arResult['SUBSCRIPTIONS'][$subitem['id']])){
                                                                                    $checkedCount = $checkedCount + 1;
                                                                                }
                                                                            }
                                                                            $checkedInputReal = false;
                                                                            if($countAllEll == $checkedCount){
                                                                                $checkedInputReal = true;
                                                                                $checked == false;
                                                                            }
                                                                            ?>
                                                                                <input class="checkbox" type="checkbox"  <?= $checked ?>
                                                                                       id="catalog-<?= $i ?>">
                                                                            <?
                                                                            if($checked == false){
                                                                                $checkedClass = '';
                                                                            }else{
                                                                                if($countAllEll == $checkedCount){
                                                                                    $checkedClass = '';
                                                                                }else{
                                                                                    $checkedClass = ' style="background:#2b8bf0" ';
                                                                                }
                                                                            }
                                                                            ?>


                                                                            <label class="form-checkbox" for="catalog-<?= $i ?>">
                                                                                <span class="icon-wrap icon-wrap_subIn" <?=$checkedClass?> >
                                                                                    <svg class="icon icon_checkmark icon_checkmark_sub " >
                                                                                     <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                                                                    </svg>
                                                                                </span>
                                                                                <?= $item['name'] ?>
                                                                            </label>
                                                                            <span class="icon-angle-wrap">
                                                                                <svg class="icon icon_angle">
                                                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#angle"></use>
                                                                                </svg>
                                                                            </span>
                                                                            <ul>
                                                                                <?php
                                                                                foreach ($item['items'] as $subitem) {
                                                                                    $i++;
                                                                                    $checked = '';

                                                                                    if (isset($arResult['SUBSCRIPTIONS'][$subitem['id']])) {
                                                                                        $checked = ' checked';
                                                                                    }
                                                                                    ?>
                                                                                    <li>
                                                                                        <input class="checkbox" type="checkbox"
                                                                                               name="specialization[<?= $k ?>][]"
                                                                                               value="<?= $subitem['id'] ?>"
                                                                                               id="catalog-<?= $i ?>"<?= $checked ?>>
                                                                                        <label class="form-checkbox"
                                                                                               for="catalog-<?= $i ?>">
                                                                                            <?if(!empty($subitem['name'])){?>
                                                                                                <span class="icon-wrap icon-wrap-not-empty" >
                                                                                            <?}else{?>
                                                                                                <span class="icon-wrap" >
                                                                                            <?}?>



                                                                                    <svg class="icon icon_checkmark">
                                                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                                                                    </svg>
                                                                                </span>
                                                                                            <?= $subitem['name'] ?>
                                                                                        </label>
                                                                                    </li>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </ul>
                                                                        </li>

                                                                        <?php




                                                                        if($curKey % 2 == 0  ){
                                                                            ?>
                                                                            </div>
                                                                            <?
                                                                        }
                                                                        $curKey++;
                                                                    }
                                                                    ?>

                                                                </ul>
                                                            </div>
                                                        </div>


                                                            <div class="col-lg-12 col-sm-12 col-xxs-12" <?if($statusCompany == 1 || $statusCompany == 2):?>style="pointer-events: none;opacity: 0.4;"<?endif;?> >
                                                                <?if($statusCompany == 1 || $statusCompany == 2):?>
                                                                    <div class="podlogJFiller"></div>
                                                                <?endif;?>
                                                                <div class="form-group">
                                                                    <label class="bold">
                                                                        <?= Loc::getMessage('ASSEPT_COMPANY') ?>
                                                                    </label>


                                                                    <?if(!empty($arResult['COMPANY']['MODERATION_FILES']['UF_FILE0']['ID']) || !empty($arResult['COMPANY']['MODERATION_FILES']['UF_FILE1']['ID'])
                                                                        || !empty($arResult['COMPANY']['MODERATION_FILES']['UF_FILE2']['ID'] || !empty($arResult['COMPANY']['MODERATION_FILES']['UF_FILE3']['ID']))
                                                                    ):?>

                                                                        <?

//загружаю предварительно документы 4 штуки
                                                                        $arFilesMess = [];
                                                                        $strIdFiles = [];
//массив id файлов при загрузки обновления компании




                                                                        $arIdFilesForUpdate = [];

                                                                        $arFiles = [];
                                                                        if(!empty($arResult['COMPANY']['MODERATION_FILES']['UF_FILE0']['ID'])){
                                                                            array_push($arFiles , $arResult['COMPANY']['MODERATION_FILES']['UF_FILE0']);
                                                                        }
                                                                        if(!empty($arResult['COMPANY']['MODERATION_FILES']['UF_FILE1']['ID'])){
                                                                            array_push($arFiles , $arResult['COMPANY']['MODERATION_FILES']['UF_FILE1']);
                                                                        }
                                                                        if(!empty($arResult['COMPANY']['MODERATION_FILES']['UF_FILE2']['ID'])){
                                                                            array_push($arFiles , $arResult['COMPANY']['MODERATION_FILES']['UF_FILE2']);
                                                                        }
                                                                        if(!empty($arResult['COMPANY']['MODERATION_FILES']['UF_FILE3']['ID'])){
                                                                            array_push($arFiles , $arResult['COMPANY']['MODERATION_FILES']['UF_FILE3']);
                                                                        }

                                                                        foreach ($arFiles as $id => $value){



                                                                            if($id !== 'UF_LOGO'){
                                                                                $preData = [];

                                                                                if(!empty($value['ID'])){
                                                                                    $strIdFiles[] =  $value['ID'];
                                                                                    $arIdFilesForUpdate[] = $value['ID'];

                                                                                    $preData['size'] = $value['FILE_SIZE'];
                                                                                    $preDataType = explode( '/' ,  $value['CONTENT_TYPE'] );

                                                                                    $preData['type'] =  $value['CONTENT_TYPE'];
                                                                                    $preData['name'] = $value['FILE_NAME'].'.'.$preDataType[1];
                                                                                    $preData['file'] = $value['SRC'];
                                                                                }else{
                                                                                    break;
                                                                                }


                                                                                $arFilesMess[] = $preData;
                                                                            }
                                                                        }



                                                                        $arFilesMess = $arFilesMess;
                                                                        $strIdFiles = implode(",", $strIdFiles);





//    $arFilesMess[0]['name'] = 'hnrxXchTYp.pdf';



                                                                        ?>

                                                                        <input type="file" name="files[]" id="filer_input2" multiple="multiple" data-jfiler-extensions='jpg , pdf , jpeg '

                                                                               data-jfiler-files='<?=json_encode($arFilesMess)?>'>
                                                                        <!--    --><?//debmes($arResult['MODERATION_FILES']);?>
                                                                        <input type="hidden" name="idElements" value="<?=$strIdFiles?>">



                                                                    <?else:?>
                                                                        <input type="file" name="files[]" id="filer_input2" multiple="multiple">
                                                                        <input type="hidden" name="idElements" value="">

                                                                    <?endif;?>
                                                                    <input type="hidden" name="idCompany" value="<?=$arResult['COMPANY'][0]['ID']?>">
                                                                </div>

                                                            </div>




                                                        </div>


                                                    </div>


                                                    <?if($statusCompany === "0"):?>
                                                        <input type="hidden" name="company_update" value="Y">
                                                    <?elseif ($statusCompany === 1):?>
                                                        <input type="hidden" name="company_desabled" value="Y">
                                                    <?elseif ($statusCompany === 2):?>
                                                        <input type="hidden" name="company_update_repeat" value="Y">
                                                    <?elseif(empty($statusCompany)):?>
                                                        <input type="hidden" name="company_add" value="Y">
                                                    <?endif;?>




                                                    <?if(!empty($arResult['COMPANY'][0]['UF_NUMBER_INN'])):?>

                                                        <button class="btn-submit btn btn-green left btnCompanyNewUpdate "<?if($statusCompany == 1):?>disabled<?endif;?>   type="submit">
                                                            <?= Loc::getMessage('USER_SETTINGS_COMPONENT_BTN_SUBMIT_COMPANY_UPDATE') ?>
                                                        </button>
                                                    <?else:?>
                                                        <button class="btn-submit btn btn-green left btnCompanyNew "   type="submit">
                                                            <?= Loc::getMessage('USER_SETTINGS_COMPONENT_BTN_SUBMIT_COMPANY') ?>
                                                        </button>
                                                    <?endif;?>







                                                </form>
                                                <?if($arResult['COMPANY'][0]['UF_STATUS_MODERATION'] == 0):?>
                                                <?if($statusCompany !== 2):?>
                                                    <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="post" enctype="multipart/form-data"   >
                                                        <input type="hidden" name="company_moderation" value="Y">
                                                        <input type="hidden" name="idCompany" value="<?=$arResult['COMPANY'][0]['ID']?>">
                                                        <button class="btn-submit btn btn-green left btnCompanyNewModeration " <?if(empty($arResult['COMPANY'][0]['UF_NUMBER_INN'])):?>disabled<?endif;?>  type="submit">
                                                            <?= Loc::getMessage('SEND_MODERATION_COMPANY') ?>
                                                        </button>
                                                    </form>
                                                <?endif;?>
                                                <?endif;?>
                                                <div class="dop_files" style="display:none"></div>


                                                <? if (!count($arResult['CARDS']) && $safeCrowEnabled): ?>
                                                    <form action="<?= $APPLICATION->GetCurPage() ?>" method="post">
                                                        <div class="h4">
                                                            <?= Loc::getMessage('USER_SETTINGS_COMPONENT_SECURITY_TTL') ?>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="alert alert-info">
                                                                <?= Loc::getMessage('USER_SETTINGS_COMPONENT_SECURITY_INFO') ?>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="addCard" value="1">
                                                        <div class="form-group">
                                                            <button class="btn btn-green btn-security">
                                                                <?= Loc::getMessage('USER_SETTINGS_COMPONENT_BTN_ADD_CARD') ?>
                                                            </button>
                                                        </div>
                                                    </form>
                                                <? endif ?>
                                                <? if (!$arResult['USER']['UF_DSPI_DOCUMENTS']): ?>
                                                    <form class="form-group" enctype="multipart/form-data"
                                                          action="<?= $APPLICATION->GetCurPage(false) ?>" method="post">
                                                        <input type="hidden" name="type" value="verification">

                                                        <div class="error-message">
                                                            <?= Loc::getMessage('USER_SETTINGS_COMPONENT_VERIFICATION_ERROR') ?>
                                                        </div>


                                                    </form>
                                                <? endif ?>




                                            </div>
                                            <? if ($arResult['USER']['UF_DSPI_EXECUTOR']): ?>
                                                <div class="tab" id="tab-profile2">

                                                </div>
                                            <? endif ?>
                                        </div>
                                    </div>
                                <?else:?>

                                    <div class="white-block profile-content">


                                        <div class="tabs-wrap">
                                            <div class="tab active" id="tab-profile1">

                                                <div class="tasks-list">
                                                    <div class="alert alert-info" role="alert">
                                                        У  вас не заполнен профиль
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                <?endif;?>


                        </div>
                        <div id="user-profile" class="tab">
                            <div class="white-block profile-content">

                                <form method="post" action="<?= $APPLICATION->GetCurPage(false) ?>">

                                    <div class="h4">
                                        <?= Loc::getMessage('USER_SETTINGS_COMPONENT_SPECIALIZATION_TTL') ?>
                                    </div>
                                    <div class="alert alert-info">
                                        <?= Loc::getMessage('USER_SETTINGS_COMPONENT_SUBSCRIBE_DESCRIPTION') ?>
                                    </div>
                                    <br>
                                    <div class="form-group">

                                    </div>
                                    <?/*  <div class="form-group">
                                            <label for="pr-desc" class="bold">
                                                <?= Loc::getMessage('USER_SETTINGS_COMPONENT_DESCRIPTION') ?>
                                                <span class="required">*</span>
                                            </label>
                                            <textarea id="pr-desc" class="form-control required" required
                                                      name="executorDescription" cols="30"
                                                      rows="10"><?= (isset($profile['description']) ? $profile['description'] : '') ?></textarea>
                                        </div>
                                        */?>
                                    <div class="btn-wrap text-right">
                                        <button class="btn btn-green btn-submit" type="submit">
                                            <?= Loc::getMessage('USER_SETTINGS_COMPONENT_BTN_SAVE') ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
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