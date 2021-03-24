<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.01.2019
 * Time: 10:34
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
global $USER;
use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'setFrameMode')) {
    $this->setFrameMode(true);
}

$currencyName = \Bitrix\Main\Config\Option::get(DSPI, 'currency_name');
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
<div class="section section-tasks  section-tasks-newD">
    <div class="wrapper">
        <h2 class="title h2 upper  last-tasks-title">

            <div class="text-block">
                <?= Loc::getMessage('TASKS_LIST_TITLE') ?>
            </div>
            <div class="orange-block-tasks-title">

            </div>

        </h2>
        <div class="row sticky-container-main-lasts-tasks" id="sticky-container" >
            <div class="col-sm-12 col-md-9 col-xxs-12">
                <div class="tasks-list">
                    <?php
                    $i = 0;
                    foreach ($arResult['ITEMS'] as $key => $item) {
//                        pre($item['UF_IBLOCK_CODE']);
                        $i++;
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

                        <div class="task-preview <?= ($quickly) ? 'urgent' : '' ?> task-preview-newDez"
                             id="<?= $this->GetEditAreaId($item['UF_ITEM_ID']) ?>">
                            <div class="image-for-task">
                                <img src="<?=SITE_TEMPLATE_PATH?>/images/itemsImages/<?=$item['UF_IBLOCK_TYPE']?>.png">
                            </div>
                            <div class="tbl tbl-fixed">

                                <div class="tbc">
                                    <div class="ttl medium medium-new-title">
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
                                        <div class="left">

                                            <div class="date-box" <?if(empty($dateCount)):?>style="display: none" <?endif;?>>

                                                <?if(!empty($dateStartString) && $dateStartString !== ''):?>
                                                    <svg class="icon icon_time">
                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#time"></use>
                                                    </svg>
                                                    <span title="<?=$dateStartString?>" class="timestamp">C <?=$dateStartString?> на <?=$dateCount?> <?=$word?></span>
                                                <?else:?>

                                                <?endif;?>
                                            </div>

                                            <div class="location-box location-box-new-dez">
                                                <svg class="icon icon_location">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#location"></use>
                                                </svg>
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
                                                        Loc::getMessage('TASKS_LIST_RESPONSE_1'),
                                                        Loc::getMessage('TASKS_LIST_RESPONSE_2'),
                                                        Loc::getMessage('TASKS_LIST_RESPONSE_3')
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
                                            <span class="" >По договоренности</span><br>
                                        <?endif;?>



                                    </div>
                                    <div class="user-box">
                                        <div class="tbl tbl-fixed">
                                            <div class="tbc">
                                                <div class="ava">
                                                    <?php
                                                    if ($chatEnabled) {
                                                        ?>
                                                        <div class="status-indicator uid<?= \Democontent2\Pi\Utils::getChatId($item['USER']['ID']) ?>"></div>
                                                        <?php
                                                    }
                                                    ?>

                                                    <?if($USER->IsAuthorized() && is_string($arResult['MODERATION_CURRENT_USER'])  ):?>
                                                        <?
                                                        $rsUser = CUser::GetByID($item['UF_USER_ID']);
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
                                                                    <?= strlen($item['USER']['NAME'])?\Democontent2\Pi\Utils::userNamePrefix($item['USER']['NAME'], $item['USER']['LAST_NAME']):'' ?>
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
                                            </div>

                                            <div class="tbc">

                                                <?if($USER->IsAuthorized() && is_string($arResult['MODERATION_CURRENT_USER'])):?>
                                                    <?if(!empty($item['COMPANY']['ID'])):?>

                                                        <div class="name medium">
                                                            <?=$item['COMPANY']['UF_COMPANY_NAME_MIN']?>
                                                        </div>
                                                    <?else:?>
                                                        <div class="name medium">
                                                            <?= $item['USER']['NAME'] ?>
                                                            <?= $item['USER']['LAST_NAME'] ?>
                                                        </div>
                                                    <?endif;?>
                                                <?else:?>
                                                    <div class="name medium rebue_notAutorize">
                                                        Название компании
                                                    </div>
                                                <?endif;?>

                                                <div class="info-phone-task">
                                                    <?if(!empty($arUserForID['PERSONAL_PHONE']) ):?>
                                                        <?if(!empty($item['USER']['ID'])):?>
                                                            <div class="company-phone-detail">
                                                                <div class="btn btn-green see-phone-manager" >
                                                                    Показать телефон
                                                                </div>
                                                                <div class="phone-non" style="display:none;">
                                                                    <?if(!empty($item['USER']['PERSONAL_PHONE'])):?>
                                                                    Телефон:<p><?=$item['USER']['PERSONAL_PHONE']?></p>
                                                                </div>
                                                                <?endif;?>
                                                                <?if(!empty($item['USER']['NAME'])):?>
                                                                    <div class="menedged-is">
                                                                        <p><?=$item['USER']['NAME']?>&nbsp;<?=$item['USER']['LAST_NAME']?></p>
                                                                    </div>
                                                                <?endif;?>
                                                            </div>
                                                        <?endif;?>
                                                    <?else:?>
                                                        <?if($USER->IsAuthorized()):?>
                                                            <div class="company-phone-detail">
                                                                <div class="btn btn-green see-phone-manager"  onclick='showPopup("Для просмотра содержимого подтвердите аккаунт на странице <br><a >Настройки профиля</a>" , "alert alert-success");'>
                                                                    Показать телефон
                                                                </div>
                                                                <?/*
                                                <div class="phone-non" style="display:none;">
                                                    <?if(!empty($arResult['USER']['PERSONAL_PHONE'])):?>
                                                    Телефон:<p><?=$arResult['USER']['PERSONAL_PHONE']?></p>
                                                    <?endif;?>
                                                </div>

                                                <?if(!empty($arResult['USER']['NAME'])):?>
                                                    Менеджер:<p><?=$arResult['USER']['NAME']?>&nbsp;<?=$arResult['USER']['LAST_NAME']?></p>
                                                <?endif;?>
                                                */?>
                                                            </div>
                                                        <?else:?>
                                                            <div class="company-phone-detail">
                                                                <div class="btn btn-green  without-auth" style="margin: 10px 0px">
                                                                    Показать телефон
                                                                </div>
                                                            </div>
                                                        <?endif;?>


                                                    <?endif;?>
                                                </div>

                                            </div>
                                        </div>
                                        <a class="lnk-abs"
                                           href="<?= SITE_DIR ?>user/<?= $item['USER']['ID'] ?>/"></a>
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
                            <a class="lnk-abs"
                               href="<?= SITE_DIR . '' . $item['UF_IBLOCK_TYPE'] . '/' . $item['UF_IBLOCK_CODE'] . '/' . $item['UF_CODE'] . '/' ?>"></a>
                        </div>
                        <?php


                    }
                    ?>
                </div>

                <div class="show-all-list">
                    <a href="/tasks/" >ПОСМОТРЕТЬ ВСЕ ЗАДАНИЯ</a>
                </div>

            </div>
            <div class="col-sm-4 col-md-3 col-xxs-12">
                <div class="vertical-banner  right-block-banner" >

                </div>
            </div>
        </div>
    </div>
</div>
