<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 18.01.2019
 * Time: 20:08
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$currencyName = \Bitrix\Main\Config\Option::get(DSPI, 'currency_name');

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}

$profileData = [];
if (isset($arResult['PROFILE']['UF_DATA'])) {
    if (strlen($arResult['PROFILE']['UF_DATA'])) {
        $profileData = unserialize($arResult['PROFILE']['UF_DATA']);
    }
}

$chatEnabled = false;
if (intval(\Bitrix\Main\Config\Option::get(DSPI, 'chatEnabled'))) {
    $chatEnabled = true;
}
function num2word($num, $words)
{
    $num = $num % 100;
    if ($num > 19) {
        $num = $num % 10;
    }
    switch ($num) {
        case 1: {
            return($words[0]);
        }
        case 2: case 3: case 4: {
        return($words[1]);
    }
        default: {
            return($words[2]);
        }
    }
}
global $USER;
$id = $USER->GetID();
$rsUserForId = CUser::GetByID($id);
$arUserForID = $rsUserForId->Fetch();

?>
<script>
    function showPopup(messege , classNa){
        var popup = $(document).find('#popup-notification-account')
        $(popup).find('.classNotific').addClass(classNa);
        $(document).find('#popup-notification-account').find('.classNotific').children('.textStrond').html(messege);
        $.fancybox.open([
            {
                src: '#popup-notification-account'
            }]);
        function funcs(){
            $(document).find('#popup-notification-account').find('strong').text('');
            $(popup).find('.classNotific').removeClass(classNa);
            $.fancybox.close([
                {
                    src: '#popup-notification-account'
                }]);
        }
        // setTimeout(funcs, 2000);
    }
</script>
<?//debmes($arResult);?>
    <div class="page-content">
        <div class="wrapper">
            <div class="profile-container">
                <div class="row" id="sticky-container">
                    <div class="col-md-3 col-lg-4 col-xxs-12 pull-right">
                        <div class="user-card-block white-block text-center sticky-block user-card-block-new-DZ"
                             data-container="#sticky-container">
                            <div class="ava">
                                <?php
                                if ($chatEnabled) {
                                    ?>
                                    <div class="status-indicator uid<?= \Democontent2\Pi\Utils::getChatId($arResult['USER']['ID']) ?>"></div>
                                    <?php
                                }
                                ?>
                                <?
                                $test = $arResult['USER']['PERSONAL_PHOTO'];
//                                pre($arResult);
                                ?>
                                <?if(!empty($arUserForID['PERSONAL_PHONE'])  ):?>
                                    <?

                                    $rsUser = CUser::GetByID($arResult['COMPANY'][0]['UF_ADMIN_COMPANY_ID']);
                                    $arUser = $rsUser->Fetch();

                                    $src = CFile::GetPath($arUser['PERSONAL_PHOTO']);
                                 

                                    ?>

                                            <? if ($src !== '' &&  !empty($src ) ): ?>
                                                <div class="object-fit-container">
                                                    <img src="<?= $src ?>"
                                                         alt="" data-object-fit="cover"
                                                         data-object-position="50% 50%"/>
                                                </div>
                                            <? else: ?>
                                                <div class="object-fit-container">
                                                    <div class="name-prefix"
                                                         style="background: <?= strlen($item['USER']['NAME']) ? \Democontent2\Pi\Utils::userBgColor($item['USER']['NAME']) : '#ffffff' ?>">
                                                        <?= strlen($arResult['USER']['NAME'])?\Democontent2\Pi\Utils::userNamePrefix($arResult['USER']['NAME'], $arResult['USER']['LAST_NAME']):'' ?>
                                                    </div>
                                                </div>
                                            <? endif ?>
                                    <?else:?>

                                    <div class="object-fit-container ">
                                        <img src="<?= SITE_TEMPLATE_PATH.'/images/2.png'?>"
                                             alt="" data-object-fit="cover"
                                             data-object-position="50% 50%"/>
                                    </div>
                                <?endif;?>



                            </div>
                            <div class="feedback text-center">
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
                                         style="width:<?= $arResult['CURRENT_RATING']['percent'] * 1 ?>%">
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
                                        <?= $arResult['CURRENT_RATING']['positive'] * 1 ?>
                                    </span>
                                    <span class="dislike">
                                        <svg class="icon icon_thumbs-o-down">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#thumbs-o-down"></use>
                                        </svg>
                                        <?= $arResult['CURRENT_RATING']['negative'] * 1 ?>
                                    </span>
                                </div>
                            </div>
<??>

                            <?if(!empty($arUserForID['PERSONAL_PHONE'])  ):?>

                                <?if(!empty($arResult['COMPANY'][0])):?>

<?//debmes($arResult);?>
                                    <div class="name medium name-profile-newDz">
                                        <?=$arResult['COMPANY'][0]['UF_COMPANY_NAME_MIN']?>
                                    </div>
                                <?else:?>
                                    <div class="name medium name-profile-newDz">
                                        <?= $arResult['USER']['NAME'] ?>
                                        <?= $arResult['USER']['LAST_NAME'] ?>
                                    </div>
                                <?endif;?>
                            <?else:?>
                                <div class="name medium rebue_notAutorize name-profile-newDz">
                                    Название компании
                                </div>
                            <?endif;?>

                            <?if(!empty($arResult['COMPANY'][0]['UF_HOT_NAMBER_COMPANY'])):?>
                                <div ><p class="namber-company"><b>Номер компании: </b><?=$arResult['COMPANY'][0]['UF_HOT_NAMBER_COMPANY']?></p></div>

                            <?endif;?>
                            <?php
                            if ($arResult['USER']['UF_DSPI_EXECUTOR']) {
                                ?>
                                <div class="busy-status busy-status-<?= intval($arResult['USER']['UF_DSPI_BUSY']) ?>">
                                    <?= Loc::getMessage('USER_PUBLIC_PROFILE_BUSY_' . intval($arResult['USER']['UF_DSPI_BUSY'])) ?>
                                </div>
                                <?php
                            }
                            ?>
<?//debmes($arUserForID);?>
                            <div class="company-phone-detail  ">
                                <?//если не авторизован  - прошу авторизоваться ) иначе высвечиваю кнопку с условиями?>
                            <?if(!$USER->IsAuthorized()){?>
                                <a class="company-phone-detail-new-DZ btn btn-green see-phone-manager see-phone-manager-user" href="#popup-registration" data-fancybox="">
                                    Показать телефон
                                </a>
                                <?}else{?>
                                    <?//если всеже авторизван  - то проверяю продтвержден ли аккаунт \? ?>
                                    <?if(!empty($arUserForID['PERSONAL_PHONE'])){?>
                                                <div class=" company-phone-detail-new-DZ btn btn-green see-phone-manager see-phone-manager-user" >
                                                    Показать телефон
                                                </div>

                                                <?if(!empty($arResult['COMPANY'][0]['UF_PHONE'])){?>
                                                    <div class="phone-non" style="display:none;">
                                                        <p><?=$arResult['COMPANY'][0]['UF_PHONE']?></p>
                                                    </div>
                                                <?}else{?>
                                                    <div class="phone-non" style="display:none;">
                                                        <p><?=$arResult['USER']['PERSONAL_PHONE']?></p>
                                                    </div>
                                                <?}?>
                                    <?}else{?>
<!--                                    showPopup(array.messageText , 'alert alert-success');-->

                                                 <div class="company-phone-detail-new-DZ btn btn-green see-phone-manager see-phone-manager-user" onclick='showPopup("Для просмотра содержимого подтвердите аккаунт на странице <br><a >Настройки профиля</a>" , "alert alert-success");'>
                                                    Показать телефон
                                                </div>


                                    <?}?>


                                <?}?>


                            </div>

                            <div class="user-info">
                                <? if ($arResult['USER']['UF_DSPI_DOCUMENTS']): ?>
                                    <div class="verification-box">
                                        <svg class="icon icon_checkmark">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                        </svg>
                                        <?= Loc::getMessage('USER_PUBLIC_PROFILE_VERIFICATION') ?>
                                    </div>
                                <? endif ?>
                                <? if (count($arResult['CARD'])): ?>
                                    <div class="security-box">
                                        <svg class="icon icon_shield_doc">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#shield-doc"></use>
                                        </svg>
                                        <?= Loc::getMessage('USER_PUBLIC_PROFILE_SECURITY') ?>
                                    </div>
                                <? endif ?>
                            </div>
                            <div class="user-info user-info-user-detal-newDz">

                                <div class="date-box">
                                    <?
                                    $get_time_ago = diff_time_string( date('Y-m-d H:i', strtotime($arResult['USER']['DATE_REGISTER'])) );

                                    ?>
                                    <div class="celander">
                                        <img src="/bitrix/templates/democontent2.pi/images/celender-detail-u.png">
                                    </div>
                                    <?= Loc::getMessage('USER_PUBLIC_PROFILE_REG') ?>:
                                    <span
                                          class=""><?=$get_time_ago?></span>
                                </div>
                                <div class="award-box">
                                    <div class="celander">
                                        <img src="/bitrix/templates/democontent2.pi/images/vector-files.png">
                                    </div>
                                    <?= Loc::getMessage('USER_PUBLIC_PROFILE_OWNER') ?>:
                                    <?= $arResult['TASKS_STAT']['owner'] ?>
                                </div>
                                <?php
                                if (intval($arResult['USER']['UF_DSPI_CITY'])) {
                                    ?>
                                    <div class="location-box">
                                        <svg class="icon icon_location">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#location"></use>
                                        </svg>
                                        <?= $arResult['CITY_NAME'] ?>
                                    </div>
                                    <?php
                                }
                                ?>
                                <?/*<div class="award-box">
                                    <svg class="icon icon_award">
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#award"></use>
                                    </svg>
                                    <?= Loc::getMessage('USER_PUBLIC_PROFILE_EXECUTOR') ?>:
                                    <?= $arResult['TASKS_STAT']['executor'] ?>
                                </div>
                                */?>
                            </div>
                            <? if ($arResult['USER']['ID'] != $USER->GetID() && $USER->IsAuthorized()
                                && count($arResult['MY_ITEMS']) && $arResult['USER']['UF_DSPI_EXECUTOR'] && !$arResult['USER']['UF_DSPI_BUSY']): ?>
                                <div class="btn-wrap">
                                    <a href="#popup" data-fancybox="" class="btn btn-green">
                                        <?= Loc::getMessage('USER_PUBLIC_PROFILE_BTN_OFFER_JOB') ?>
                                    </a>
                                </div>
                            <? endif ?>
                        </div>
                        <div class="vertical-banner  right-block-banner">

                        </div>
                    </div>
                    <div class="col-sm-12 col-md-9 col-lg-8 col-xxs-12">
                        <div class="white-block">
                            <div class="profile-tabs-head tabs-head">
                                <div class="head visible-xxs visible-xs">
                                    <?= Loc::getMessage('USER_PUBLIC_PROFILE_TABS_TTL_1') ?>
                                    <div class="icon-wrap">
                                        <svg class="icon icon_angle">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#angle"></use>
                                        </svg>
                                    </div>
                                </div>
                                <ul>
                                    <li class="active">
                                        <a href="#master-tab1">
                                            <?= Loc::getMessage('USER_PUBLIC_PROFILE_TABS_TTL_1') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#master-tab3">
                                            <?= Loc::getMessage('USER_PUBLIC_PROFILE_TABS_TTL_2') ?>
                                        </a>
                                    </li>
                                    <?/*<li>
                                        <a href="#master-tab5">
                                            <?= Loc::getMessage('USER_PUBLIC_PROFILE_TABS_TTL_3') ?>
                                        </a>
                                    </li>
                                    */?>
                                    <?php
                                    if (count($arResult['PORTFOLIO_CATEGORIES'])) {
                                        ?>
                                        <li>
                                            <a href="#master-tab6">
                                                <?= Loc::getMessage('USER_PUBLIC_PROFILE_TABS_TTL_4') ?>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                    <?if(!empty($arResult['COMPANY'][0]['ID'])):?>
<?

                                        ?>
                                            <?if(!$USER->IsAuthorized()):?>
                                            <li>
                                                <a href="#popup-registration"  data-fancybox="" >
                                                    Сотрудники
                                                </a>
                                            </li>
                                            <?else:?>

                                            <?if(!empty($arUserForID['PERSONAL_PHONE'])):?>
                                                <li>
                                                    <a href="#master-employees">
                                                        Сотрудники
                                                    </a>
                                                </li>
                                            <?else:?>
                                                <li onclick='showPopup("Для просмотра содержимого подтвердите аккаунт на странице <br><a >Настройки профиля</a>" , "alert alert-success");' >
                                                    <a style="cursor: pointer">
                                                        Сотрудники
                                                    </a>
                                                </li>
                                            <?endif;?>

                                            <?endif;?>


                                    <?endif;?>
                                </ul>
                            </div>

                            <div class="tabs-wrap">
                                <div class="tab active" id="master-tab1">
                                    <div class="about">
<!--                                        $arResult[0]['UF_NUMBER_INN']-->
                                        <?if(!empty($arUserForID['PERSONAL_PHONE'])){?>
                                            <?if(!empty($arResult['COMPANY'][0]['UF_NUMBER_INN'])):?>
                                                <p class="ttl  ttlInn bold">
                                                    ИНН:
                                                </p>
                                                <p class=""> <?=$arResult['COMPANY'][0]['UF_NUMBER_INN']?></p>
                                            <?endif;?>
                                        <?}?>

                                        <?php
                                        if (isset($arResult['COMPANY'][0]['UF_DESCRIPTION']) && strlen($arResult['COMPANY'][0]['UF_DESCRIPTION'])) {
                                            ?>
                                            <p class="ttl bold ttlInn_about"><?= Loc::getMessage('USER_PUBLIC_PROFILE_ABOUT_ME') ?>
                                                :</p>
                                            <p class="descript">
                                                <?= $arResult['COMPANY'][0]['UF_DESCRIPTION'] ?>
                                            </p>
                                            <?php
                                        } else {
                                            ?>
                                            <div class="alert alert-info">
                                                <?= Loc::getMessage('USER_PUBLIC_PROFILE_ABOUT_ME_EMPTY') ?>
                                            </div>
                                            <br>
                                            <?php
                                        }
                                        if (isset($profileData['specializations']) && count($profileData['specializations'])) {
                                            ?>
                                            <p class="ttl bold specializations-styles">
                                                <?= Loc::getMessage('USER_PUBLIC_PROFILE_SPECIALIZATION') ?>:
                                            </p>


                                            <?foreach ($profileData['specializations'] as $key => $valSpec):?>
                                                <ul class="col-md-6 col-sm-12 specializations-styles-item-user">
                                                    <div class="image-for-task left">
                                                        <img src="/bitrix/templates/democontent2.pi/images/itemsImages/<?=$key?>.png">
                                                    </div>
                                                    <div class="container-specilization-block-user left">
                                                        <p><?=$valSpec?></p>
                                                        <div class="container-specilization-block-user-list">
                                                            <?foreach ($profileData['subSpecializations'][$key] as $k_ => $v_):?>
                                                                <li><?=$v_?></li>
                                                            <?endforeach;?>
                                                        </div>
                                                    </div>
                                                </ul>
                                            <?endforeach;?>
                                            <?php
                                        } else {
                                            ?>
                                            <div class="alert alert-info">
                                                <?= Loc::getMessage('USER_PUBLIC_PROFILE_SPECIALIZATION_EMPTY') ?>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="tab" id="master-tab3">
                                    <div class="sorty-panel tabs-head">
                                        <ul style="display: none">
                                            <li class="active">
                                                <a href="#task-published">
                                                    <?= Loc::getMessage('USER_PUBLIC_PROFILE_TASK_PUBLISHED') ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#task-done">
                                                    <?= Loc::getMessage('USER_PUBLIC_PROFILE_TASK_DONE') ?>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="tabs-wrap">
                                        <div class="tab active" id="task-published">
                                            <div class="tasks-list">
                                                <?
                                                $list = 0;
                                                foreach ($arResult['ITEMS'] as $key => $item) {

                                                    if(!empty($item['UF_DATA_START_STRING']) && $item['UF_DATA_START_STRING'] !== ''){
                                                        $dateStartObj = \Bitrix\Main\Type\DateTime::createFromTimestamp(MakeTimeStamp($item['UF_DATA_START_STRING'], "DD.MM.YYYY HH:MI:SS"));
                                                        //переделываю в старый формат после unix
                                                        $dateStartStringWithTime = $dateStartObj->format("d.m.Y H:i:s");
                                                        $dateStartString = $dateStartObj->format("d.m.Y");


                                                    }else{
                                                        $dateStartString = '';
                                                    }

                                                    if(!empty($item['UF_DATA_END_STRING']) && $item['UF_DATA_END_STRING'] !== ''){
                                                        $dateEndObj = \Bitrix\Main\Type\DateTime::createFromTimestamp(MakeTimeStamp($item['UF_DATA_END_STRING'], "DD.MM.YYYY"));
                                                        $dateEndString = $dateEndObj->format("d.m.Y");
                                                    }else{
                                                        $dateEndString = '';
                                                    }
                                                    if(!empty($item['UF_DATA_START_STRING']) && !empty($item['UF_DATA_END_STRING'])){
                                                        $date1 = new DateTime($dateStartString);
                                                        $date2 = new DateTime($dateEndString);
                                                        $interval = $date1->diff($date2);
                                                        $dateCount = $interval->d + 1;
                                                        $word = num2word((int)$dateCount, array('смену', 'смены', 'смен'));

                                                    }else{
                                                        $dateCount = '1';
                                                        $word = 'смену';
                                                    }

                                                    $list++;
                                                    $arButtons = \CIBlock::GetPanelButtons(
                                                        $item['UF_IBLOCK_ID'],
                                                        $item['UF_ITEM_ID'],
                                                        0,
                                                        array("SECTION_BUTTONS" => false, "SESSID" => false)
                                                    );

                                                    $arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
                                                    $arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

                                                    $this->AddEditAction(
                                                        $item['UF_ITEM_ID'],
                                                        $arItem['EDIT_LINK'],
                                                        CIBlock::GetArrayByID(
                                                            $item['UF_IBLOCK_ID'],
                                                            "ELEMENT_EDIT"
                                                        )
                                                    );
                                                    $this->AddDeleteAction(
                                                        $item['UF_ITEM_ID'],
                                                        $arItem['DELETE_LINK'],
                                                        CIBlock::GetArrayByID(
                                                            $item['UF_IBLOCK_ID'],
                                                            "ELEMENT_DELETE"
                                                        ),
                                                        array(
                                                            "CONFIRM" => Loc::getMessage('CONFIRM_DELETE')
                                                        )
                                                    );

                                                    if ($item['USER']['PERSONAL_PHOTO'] > 0) {
                                                        $ava = CFile::ResizeImageGet($item['USER']['PERSONAL_PHOTO'],
                                                            array(
                                                                'width' => 50,
                                                                'height' => 50
                                                            ),
                                                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                                            true
                                                        );
                                                    }
                                                    $quickly = 0;
                                                    if (count($item['UF_QUICKLY_END']) > 0 && strtotime($item['UF_QUICKLY_END']) > strtotime(date('Y-m-d H:i'))) {
                                                        $quickly = 1;
                                                    }
                                                    ?>
                                                    <div class="task-preview  task-preview-newDez task-preview-newDez-detail-user <?= ($quickly) ? 'urgent' : '' ?>"
                                                         id="<?= $this->GetEditAreaId($item['UF_ITEM_ID']) ?>">
                                                        <div class="image-for-task">
                                                            <img src="<?=SITE_TEMPLATE_PATH?>/images/itemsImages/gruzopodemnaya-tekhnika.png">
                                                        </div>
                                                        <div class="tbl">
                                                            <div class="tbc">
                                                                <div class="ttl medium">
                                                                    <? if ($quickly): ?>
                                                                        <span class="fire">
                                                                            <svg class="icon icon_fire">
                                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#fire"></use>
                                                                            </svg>
                                                                        </span>
                                                                    <? endif ?>
                                                                    <?if(!empty($item['UF_COUNT_TECH'])):?>
                                                                        <?= $item['UF_NAME'] ?> <?=$item['UF_COUNT_TECH']?> шт
                                                                    <?else:?>
                                                                        <?= $item['UF_NAME'] ?>
                                                                    <?endif;?>
                                                                </div>
                                                                <div class="desc">
                                                                    <?= substr($item['UF_DESCRIPTION'], 0, 215) ?>
                                                                    <? if (strlen($item['UF_DESCRIPTION']) > 215): ?>
                                                                        ...
                                                                    <? endif ?>
                                                                </div>
                                                                <div class="btm clearfix">
                                                                    <div class="">
                                                                        <div class="date-box ">
                                                                            <?if(!empty($dateStartString) && $dateStartString !== ''):?>

                                                                                    <svg class="icon icon_time">
                                                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#time"></use>
                                                                                    </svg>
                                                                                <div class="data-box-time"></div>

                                                                                <span title="<?=$dateStartString?>" class="timestamp">C <?=$dateStartString?> на <?=$dateCount?> <?=$word?></span>
                                                                            <?else:?>

                                                                            <?endif;?>
                                                                        </div>
                                                                        <div class="location-box">
                                                                            <svg class="icon icon_location">
                                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#location"></use>
                                                                            </svg>
                                                                            <div class="data-box-time-location"></div>

                                                                            <?= $item['UF_CITY_NAME'] ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="right" style="display: none">
                                                                        <div class="responses-box left">
                                                                            <svg class="icon icon_comment">
                                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#comment"></use>
                                                                            </svg>
                                                                            <?php
                                                                            echo \Democontent2\Pi\Utils::declension(
                                                                                $item['UF_RESPONSE_COUNT'],
                                                                                array(
                                                                                    Loc::getMessage('USER_PUBLIC_PROFILE_RESPONSE_1'),
                                                                                    Loc::getMessage('USER_PUBLIC_PROFILE_RESPONSE_2'),
                                                                                    Loc::getMessage('USER_PUBLIC_PROFILE_RESPONSE_3')
                                                                                )
                                                                            );
                                                                            ?>
                                                                        </div>
                                                                        <div class="views-box left">
                                                                            <svg class="icon icon_eye">
                                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#eye"></use>
                                                                            </svg>
                                                                            <?= $item['UF_COUNTER'] ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="tbc tbc-info">

                                                                <div class="price-box">
                                                                    <?if(!empty($item['UF_NDS'])):?>
                                                                        <?if(!empty($item['UF_NDS'])):?>
                                                                            <span class="nds" >C НДС:</span><span class="nds_price"><?=(string)$item['UF_NDS']?>₽</span><br>
                                                                        <?endif;?>
                                                                    <?endif;?>

                                                                    <?if(!empty($item['UF_BEZNAL']) ):?>
                                                                        <?if(!empty($item['UF_BEZNAL'])):?>
                                                                            <span class="bezznal" >Без НДС: </span><span class="bezznal_price"><?=(string)$item['UF_BEZNAL']?>₽</span><br>
                                                                        <?endif;?>
                                                                    <?endif;?>

                                                                    <?if(!empty($item['UF_NALL']) ):?>
                                                                        <?if(!empty($item['UF_NALL'])):?>
                                                                            <span class="nall" >Нал: </span><span class="nall_price" ><?=(string)$item['UF_NALL']?>₽</span><br>
                                                                        <?endif;?>
                                                                    <?endif;?>

                                                                    <?if(empty($item['UF_BEZNAL']) && empty($item['UF_NDS']) && empty($item['UF_NALL'])):?>
                                                                        <span class="nall" >По договоренности</span><br>
                                                                    <?endif;?>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <a class="lnk-abs"
                                                           href="<?= SITE_DIR . '' . $item['UF_IBLOCK_TYPE'] . '/' . $item['UF_IBLOCK_CODE'] . '/' . $item['UF_CODE'] . '/' ?>"></a>
                                                    </div>
                                                    <?php
                                                }
                                                if ($list == 0) {
                                                    ?>
                                                    <div class="alert alert-info">
                                                        <?= Loc::getMessage('USER_PUBLIC_PROFILE_TASKS_EMPTY') ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="tab" id="task-done">
                                            <div class="tasks-list">
                                                <?
                                                $list = 0;
                                                foreach ($arResult['EXECUTOR_ITEMS'] as $key => $item) {





                                                    $list++;
                                                    $arButtons = \CIBlock::GetPanelButtons(
                                                        $item['UF_IBLOCK_ID'],
                                                        $item['UF_ITEM_ID'],
                                                        0,
                                                        array("SECTION_BUTTONS" => false, "SESSID" => false)
                                                    );

                                                    $arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
                                                    $arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

                                                    $this->AddEditAction(
                                                        $item['UF_ITEM_ID'],
                                                        $arItem['EDIT_LINK'],
                                                        CIBlock::GetArrayByID(
                                                            $item['IBLOCK_ID'],
                                                            "ELEMENT_EDIT"
                                                        )
                                                    );
                                                    $this->AddDeleteAction(
                                                        $item['UF_ITEM_ID'],
                                                        $arItem['DELETE_LINK'],
                                                        CIBlock::GetArrayByID(
                                                            $item['IBLOCK_ID'],
                                                            "ELEMENT_DELETE"
                                                        ),
                                                        array(
                                                            "CONFIRM" => Loc::getMessage('CONFIRM_DELETE')
                                                        )
                                                    );

                                                    if ($item['USER']['PERSONAL_PHOTO'] > 0) {
                                                        $ava = CFile::ResizeImageGet($item['USER']['PERSONAL_PHOTO'],
                                                            array(
                                                                'width' => 50,
                                                                'height' => 50
                                                            ),
                                                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                                            true
                                                        );
                                                    }
                                                    $quickly = 0;
                                                    if (count($item['UF_QUICKLY_END']) > 0 && strtotime($item['UF_QUICKLY_END']) > strtotime(date('Y-m-d H:i'))) {
                                                        $quickly = 1;
                                                    }
                                                    ?>
                                                    <div class="task-preview <?= ($quickly) ? 'urgent' : '' ?>"
                                                         id="<?= $this->GetEditAreaId($item['UF_ITEM_ID']) ?>">
                                                        <div class="tbl">
                                                            <div class="tbc">
                                                                <div class="ttl medium">
                                                                    <? if ($quickly): ?>
                                                                        <span class="fire">
                                                                            <svg class="icon icon_fire">
                                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#fire"></use>
                                                                            </svg>
                                                                        </span>
                                                                    <? endif ?>
                                                                    <?= $item['UF_NAME'] ?>
                                                                </div>
                                                                <div class="desc">
                                                                    <?= substr($item['UF_DESCRIPTION'], 0, 215) ?>
                                                                    <? if (strlen($item['UF_DESCRIPTION']) > 215): ?>
                                                                        ...
                                                                    <? endif ?>
                                                                </div>
                                                                <div class="btm clearfix">
                                                                    <div class="left">
                                                                        <div class="date-box">
                                                                            <?if(!empty($dateStartString) && $dateStartString !== ''):?>
                                                                                <svg class="icon icon_time">
                                                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#time"></use>
                                                                                </svg>
                                                                                <span title="<?=$dateStartString?>" class="timestamp">C <?=$dateStartString?> на <?=$dateCount?> <?=$word?></span>
                                                                            <?else:?>

                                                                            <?endif;?>


                                                                        </div>
                                                                    </div>
                                                                    <div class="right">
                                                                        <div class="responses-box left">
                                                                            <svg class="icon icon_comment">
                                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#comment"></use>
                                                                            </svg>
                                                                            <?php
                                                                            echo \Democontent2\Pi\Utils::declension(
                                                                                $item['UF_RESPONSE_COUNT'],
                                                                                array(
                                                                                    Loc::getMessage('USER_PUBLIC_PROFILE_RESPONSE_1'),
                                                                                    Loc::getMessage('USER_PUBLIC_PROFILE_RESPONSE_2'),
                                                                                    Loc::getMessage('USER_PUBLIC_PROFILE_RESPONSE_3')
                                                                                )
                                                                            );
                                                                            ?>
                                                                        </div>
                                                                        <div class="views-box left">
                                                                            <svg class="icon icon_eye">
                                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#eye"></use>
                                                                            </svg>
                                                                            <?= $item['UF_COUNTER'] ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="tbc tbc-info">
                                                                <?
                                                                switch ($item['UF_STATUS']) {
                                                                    case 1:
                                                                    case 4:
                                                                        ?>
                                                                        <div class="status">
                                                                            <?= Loc::getMessage('USER_PUBLIC_PROFILE_STATUS_1') ?>
                                                                        </div>
                                                                        <?
                                                                        break;
                                                                    case 2:
                                                                        ?>
                                                                        <div class="status hold-true">
                                                                            <?= Loc::getMessage('USER_PUBLIC_PROFILE_STATUS_2') ?>
                                                                        </div>
                                                                        <?
                                                                        break;
                                                                    case 3:
                                                                        ?>
                                                                        <div class="status completed">
                                                                            <?= Loc::getMessage('USER_PUBLIC_PROFILE_STATUS_3') ?>
                                                                        </div>
                                                                        <?
                                                                        break;
                                                                }
                                                                ?>
                                                                <div class="price-box">
                                                                    <? if ($item['UF_PRICE'] > 0): ?>
                                                                        <?= \Democontent2\Pi\Utils::price($item['UF_PRICE']) ?>
                                                                        <div class="currency"><?= $currencyName ?></div>
                                                                    <? else: ?>
                                                                        <span>
                                                        <?= Loc::getMessage('USER_PUBLIC_PROFILE_PRICE_CONTRACT') ?>
                                                    </span>
                                                                    <? endif ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <a class="lnk-abs"
                                                           href="<?= SITE_DIR . '' . $item['UF_IBLOCK_TYPE'] . '/' . $item['UF_IBLOCK_CODE'] . '/' . $item['UF_CODE'] . '/' ?>"></a>
                                                    </div>
                                                    <?php
                                                }
                                                if ($list == 0) {
                                                    ?>
                                                    <div class="alert alert-info">
                                                        <?= Loc::getMessage('USER_PUBLIC_PROFILE_TASKS_EMPTY') ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab" id="master-tab5">
                                    <div class="sorty-panel tabs-head">
                                        <ul>
                                            <li class="active">
                                                <a href="#feedback-tab1">
                                                    <?= Loc::getMessage('USER_PUBLIC_PROFILE_FEEDBACK_TAB_1') ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#feedback-tab2">
                                                    <?= Loc::getMessage('USER_PUBLIC_PROFILE_FEEDBACK_TAB_2') ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#feedback-tab3">
                                                    <?= Loc::getMessage('USER_PUBLIC_PROFILE_FEEDBACK_TAB_3') ?>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="tabs-wrap">
                                        <div class="tab active" id="feedback-tab1">
                                            <div class="comments-container">
                                                <?php
                                                $list = 0;
                                                foreach ($arResult['REVIEWS'] as $key => $item) {
                                                    if ($item['UF_RATING'] == 1) {
                                                        $list++;
                                                        ?>
                                                        <div class="item">
                                                            <div class="head clearfix">
                                                                <div class="user-box">
                                                                    <div class="tbl tbl-fixed">
                                                                        <div class="tbc">
                                                                            <div class="ava">
                                                                                <?php
                                                                                if ($chatEnabled) {
                                                                                    ?>
                                                                                    <div class="status-indicator uid<?= \Democontent2\Pi\Utils::getChatId($item['UF_FROM']) ?>"></div>
                                                                                    <?php
                                                                                }
                                                                                ?>
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
                                                                                        <img src="<?= $ava['src'] ?>"
                                                                                             alt=""
                                                                                             data-object-fit="cover"
                                                                                             data-object-position="50% 50%"/>
                                                                                    </div>
                                                                                <? else: ?>
                                                                                    <div class="object-fit-container">
                                                                                        <div class="name-prefix"
                                                                                             style="background: <?= strlen($item['USER_FROM_NAME']) ? \Democontent2\Pi\Utils::userBgColor($item['USER_FROM_NAME']) : '#ffffff' ?>">
                                                                                            <?= strlen($item['USER_FROM_NAME'])?\Democontent2\Pi\Utils::userNamePrefix($item['USER_FROM_NAME'], $item['USER_FROM_LAST_NAME']):'' ?>
                                                                                        </div>
                                                                                    </div>
                                                                                <? endif ?>
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
                                                                    <svg class="icon icon_smile">
                                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#smile"></use>
                                                                    </svg>
                                                                    <?= Loc::getMessage('USER_PUBLIC_PROFILE_FEEDBACK_TTL_TASK', [
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
                                                if ($list == 0) {
                                                    ?>
                                                    <div class="alert alert-info">
                                                        <?= Loc::getMessage('USER_PUBLIC_PROFILE_FEEDBACK_EMPTY') ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="tab" id="feedback-tab2">
                                            <div class="comments-container">
                                                <?php
                                                $list = 0;
                                                foreach ($arResult['REVIEWS'] as $key => $item) {
                                                    if ($item['UF_RATING'] == 2) {
                                                        $list++;
                                                        ?>
                                                        <div class="item">
                                                            <div class="head clearfix">
                                                                <div class="user-box">
                                                                    <div class="tbl tbl-fixed">
                                                                        <div class="tbc">
                                                                            <div class="ava">
                                                                                <?php
                                                                                if ($chatEnabled) {
                                                                                    ?>
                                                                                    <div class="status-indicator uid<?= \Democontent2\Pi\Utils::getChatId($item['UF_FROM']) ?>"></div>
                                                                                    <?php
                                                                                }
                                                                                ?>
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
                                                                                        <img src="<?= $ava['src'] ?>"
                                                                                             alt=""
                                                                                             data-object-fit="cover"
                                                                                             data-object-position="50% 50%"/>
                                                                                    </div>
                                                                                <? else: ?>
                                                                                    <div class="object-fit-container">
                                                                                        <div class="name-prefix"
                                                                                             style="background: <?= strlen($item['USER_FROM_NAME']) ? \Democontent2\Pi\Utils::userBgColor($item['USER_FROM_NAME']) : '#ffffff' ?>">
                                                                                            <?= strlen($item['USER_FROM_NAME'])?\Democontent2\Pi\Utils::userNamePrefix($item['USER_FROM_NAME'], $item['USER_FROM_LAST_NAME']):'' ?>
                                                                                        </div>
                                                                                    </div>
                                                                                <? endif ?>
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
                                                                    <svg class="icon icon_neutral">
                                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#smile"></use>
                                                                    </svg>
                                                                    <?= Loc::getMessage('USER_PUBLIC_PROFILE_FEEDBACK_TTL_TASK', [
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
                                                if ($list == 0) {
                                                    ?>
                                                    <div class="alert alert-info">
                                                        <?= Loc::getMessage('USER_PUBLIC_PROFILE_FEEDBACK_EMPTY') ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="tab" id="feedback-tab3">
                                            <div class="comments-container">
                                                <?php
                                                $list = 0;
                                                foreach ($arResult['REVIEWS'] as $key => $item) {
                                                    if ($item['UF_RATING'] == 0) {
                                                        $list++;
                                                        ?>
                                                        <div class="item">
                                                            <div class="head clearfix">
                                                                <div class="user-box">
                                                                    <div class="tbl tbl-fixed">
                                                                        <div class="tbc">
                                                                            <div class="ava">
                                                                                <?php
                                                                                if ($chatEnabled) {
                                                                                    ?>
                                                                                    <div class="status-indicator uid<?= \Democontent2\Pi\Utils::getChatId($item['UF_FROM']) ?>"></div>
                                                                                    <?php
                                                                                }
                                                                                ?>
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
                                                                                        <img src="<?= $ava['src'] ?>"
                                                                                             alt=""
                                                                                             data-object-fit="cover"
                                                                                             data-object-position="50% 50%"/>
                                                                                    </div>
                                                                                <? else: ?>
                                                                                    <div class="object-fit-container">
                                                                                        <div class="name-prefix"
                                                                                             style="background: <?= strlen($item['USER_FROM_NAME']) ? \Democontent2\Pi\Utils::userBgColor($item['USER_FROM_NAME']) : '#ffffff' ?>">
                                                                                            <?= strlen($item['USER_FROM_NAME'])?\Democontent2\Pi\Utils::userNamePrefix($item['USER_FROM_NAME'], $item['USER_FROM_LAST_NAME']):'' ?>
                                                                                        </div>
                                                                                    </div>
                                                                                <? endif ?>
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
                                                                    <svg class="icon icon_sad">
                                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#smile"></use>
                                                                    </svg>
                                                                    <?= Loc::getMessage('USER_PUBLIC_PROFILE_FEEDBACK_TTL_TASK', [
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
                                                if ($list == 0) {
                                                    ?>
                                                    <div class="alert alert-info">
                                                        <?= Loc::getMessage('USER_PUBLIC_PROFILE_FEEDBACK_EMPTY') ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab" id="master-employees">
                                    <div class="sorty-panel tabs-head">

                                        <ul>
                                            <li class="active">
                                                <a href="#feedback-tab1">
                                                    Cписок сотрудников
                                                </a>
                                            </li>

                                        </ul>

                                    </div>

                                    <div class="tabs-wrap">

                                        <div class="tab active" id="feedback-tab1">
                                            <?if(!empty($arResult['COMPANY_EMPLOYEES']['ITEMS_USERS']['0']['ID'])):?>
                                                <div class="row row-employe-user">
                                                    <div class="col-md-4 col-sm-4">
                                                       <p>Имя</p>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4">
                                                        <p>Фамилия</p>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4">
                                                        <p>Телефон</p>
                                                    </div>
                                                </div>
                                                <div class="block-employe">
                                                    <div class="row">
                                                        <?foreach ($arResult['COMPANY_EMPLOYEES']['ITEMS_USERS'] as $id => $valEmplo):?>
                                                        <div class="empl-val-foreach">
                                                            <div class="col-md-4 col-sm-4" >
                                                                <p><?=$valEmplo['NAME']?></p>
                                                            </div>
                                                            <div class="col-md-4 col-sm-4" >
                                                                <p><?=$valEmplo['LAST_NAME']?></p>
                                                            </div>
                                                            <div class="col-md-4 col-sm-4" >
                                                                <p><?=$valEmplo['PERSONAL_PHONE']?></p>
                                                            </div>
                                                        </div>
                                                        <?endforeach;?>

                                                    </div>
                                                </div>
                                            <?else:?>
                                                <div class="alert alert-info" role="alert">
                                                    Список сотрудников пуст
                                                </div>
                                            <?endif;?>
                                        </div>
                                    </div>

                                </div>
                                <?php
                                if (count($arResult['PORTFOLIO_CATEGORIES'])) {
                                    ?>
                                    <div class="tab" id="master-tab6">
                                        <div class="row">
                                            <div class="col-xxs-12 col-sm-6">
                                                <select style="width: 100%" id="sorty-portflolio" class="js-select">
                                                    <?php
                                                    foreach ($arResult['PORTFOLIO_CATEGORIES'] as $category) {
                                                        $selected = '';
                                                        if ($category['ID'] == $arResult['PORTFOLIO_CATEGORY_SELECTED']) {
                                                            $selected = ' selected';
                                                        }
                                                        ?>
                                                        <option value="<?= $category['ID'] ?>"<?= $selected ?>>
                                                            <?= $category['UF_NAME'] ?>
                                                        </option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="tabs-wrap">
                                            <div class="tab active" id="portfolio-category1">
                                                <div class="gallery-container row">
                                                    <?php
                                                    foreach ($arResult['PORTFOLIO_CATEGORY_IMAGES']['files'] as $image) {
                                                        $thumb = CFile::ResizeImageGet(
                                                            $image['UF_FILE_ID'],
                                                            [
                                                                'width' => 250,
                                                                'height' => 250
                                                            ],
                                                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                                            true
                                                        );
                                                        if (!isset($thumb['src'])) {
                                                            continue;
                                                        }

                                                        $bigThumb = CFile::ResizeImageGet(
                                                            $image['UF_FILE_ID'],
                                                            [
                                                                'width' => 800,
                                                                'height' => 800
                                                            ],
                                                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                                            true
                                                        );
                                                        ?>
                                                        <div class="col-xxs-12 col-sm-4 col-md-4 col-xs-6">
                                                            <div class="block">
                                                                <a class="lnk-abs"
                                                                   href="<?= $bigThumb['src'] ?>"
                                                                   data-fancybox="gallery"
                                                                   data-caption="<?= $image['UF_DESCRIPTION'] ?>">
                                                                    <div class="object-fit-container">
                                                                        <img src="<?= $thumb['src'] ?>"
                                                                             alt="" data-object-fit="cover"
                                                                             data-object-position="50% 50%">
                                                                    </div>
                                                                </a>
                                                                <?php
                                                                if (strlen($image['UF_DESCRIPTION']) > 0) {
                                                                    ?>
                                                                    <div class="caption">
                                                                        <?= $image['UF_DESCRIPTION'] ?>
                                                                    </div>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>

                                            <?php
                                            foreach ($arResult['PORTFOLIO_CATEGORIES'] as $category) {
                                                ?>
                                                <div class="tab" id="portfolio-category<?= $category['ID'] ?>">
                                                    <?= $category['UF_NAME'] ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <br>
                        <div class="horizont-baner-newDz">
                            <img src="/bitrix/templates/democontent2.pi/images/bg-baner-horizont.png">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="banner-demo-bottom banner-demo-bottom-profile-detail">
        <!--    <img src="--><!--/images/banners/banners-all-tasks-main.png">-->
        <div class="banner-demo-bottom-inner" style="background-image: url('/bitrix/templates/democontent2.pi/images/banners/banners-all-tasks-main.png')">

        </div>
    </div>
<? if ($arResult['USER']['ID'] !== $USER->GetID() && $USER->IsAuthorized() && count($arResult['MY_ITEMS'])): ?>
    <div class="popup popup-offer-job " id="popup">
        <div class="popup-head">
            <div class="h2">
                <?= Loc::getMessage('USER_PUBLIC_PROFILE_POP_OFFER_JOB_TTL') ?>
            </div>
        </div>
        <div class="popup-body">
            <form method="post" action="<?= $APPLICATION->GetCurPage(false) ?>">
                <table class="table">
                    <tbody>
                    <?php
                    foreach ($arResult['MY_ITEMS'] as $key => $item) {
                        if ($item['UF_STATUS'] == 1 || $item['UF_STATUS'] == 4) {
                            ?>
                            <tr data-href="<?= SITE_DIR . '' . $item['UF_IBLOCK_TYPE'] . '/' . $item['UF_IBLOCK_CODE'] . '/' . $item['UF_CODE'] . '/' ?>"
                                class="item" id="<?= $this->GetEditAreaId($item['UF_ITEM_ID']) ?>">
                                <td class="ttl medium">
                                    <input class="radio" id="radio-offer-job-<?= $item['ID'] ?>" type="radio"
                                           name="offer" value="<?= $item['ID'] ?>">
                                    <label class="form-radio" for="radio-offer-job-<?= $item['ID'] ?>">
                                        <span class="icon-wrap"></span>
                                        <?= $item['UF_NAME'] ?>
                                    </label>

                                </td>

                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <br>
                <div class="text-center">
                    <button class="btn btn-green">
                        <?= Loc::getMessage('USER_PUBLIC_PROFILE_BTN_OFFER_JOB') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
<? endif ?>
    <script>
        BX.message(
            {
                curPage: '<?=$APPLICATION->GetCurPage(false)?>'
            }
        );
    </script>
<?php

if (method_exists($this, 'createFrame')) {
    $frame->end();
}