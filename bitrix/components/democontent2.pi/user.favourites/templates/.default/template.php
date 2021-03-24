<?php
/**
 * Date: 08.07.2019
 * Time: 16:52
 * User: Ruslan Semagin
 * Company: PIXEL365
 * Web: https://pixel365.ru
 * Email: pixel.365.24@gmail.com
 * Phone: +7 (495) 005-23-76
 * Skype: pixel365
 * Product Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 * Use of this code is allowed only under the condition of full compliance with the terms of the license agreement,
 * and only as part of the product.
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
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
//pre($arResult);
?>

<?//debmes($arResult);?>
    <div class="page-content">
        <div class="wrapper">
            <div class="page-title">
                <h1>
                    <?= Loc::getMessage('NOTIFICATIONS_COMPONENT_TITLE') ?>
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
                    <div class="white-block notifications-container">
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
                            <div id="user-feedbacks1" class="tab active">
                                <div class="white-block notifications-container">

                                    <?php
                                    if (!count($arResult['ITEMS'])) {
                                        ?>
                                        <div class="alert alert-info">
                                            <?= Loc::getMessage('NO_RESULTS') ?>
                                        </div>
                                        <?php
                                    } else {
                                        $i = 0;
                                        foreach ($arResult['ITEMS'] as $item) {

                                            $i++;
                                            if (!empty($item['ID'])) {
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
                                                <div id="executor-<?= $item['ID'] ?>" class="profile-preview profile-preview-newDz">
                                                    <div class="remove-item" data-id="<?= $item['ID'] ?>">
                                                        <?= Loc::getMessage('REMOVE') ?>
                                                    </div>
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
                                                            </div>
                                                        </div>
                                                        <div class="tbc desc">
                                                            <?if(!empty($arUserForID['PERSONAL_PHONE'])):?>
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

                                                            <?if(!empty($item['COMPANY']['UF_DESCRIPTION'])):?>
                                                                <div class="desc-company-newDez">
                                                                    <?=$item['COMPANY']['UF_DESCRIPTION']?>
                                                                </div>
                                                            <?endif;?>


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
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <div id="user-feedbacks2" class="tab">
                                <div class="white-block notifications-container">

                                    <?php
                                    $i = 0;
                                    foreach ($arResult['ITEMS_TASKS'] as $key => $item) {

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
                                        <div class="task-preview  task-preview-newDez task-preview-newDez-moderation  <?= ($quickly) ? 'urgent' : '' ?>"
                                             id="<?= $this->GetEditAreaId($item['UF_ITEM_ID']) ?>">
                                            <div class="image-for-task">
                                                <img src="<?=SITE_TEMPLATE_PATH?>/images/itemsImages/<?=$item['UF_IBLOCK_TYPE']?>.png">
                                            </div>
                                            <div class="tbl tbl-fixed">
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
                                                            <div class="location-box">
                                                                <svg class="icon icon_location">
                                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#location"></use>
                                                                </svg>
                                                                <?= $item['CITY_NAME'] ?>
                                                            </div>
                                                        </div>
                                                        <?/*    <div class="right">
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
 */?>
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
                                                                    <?
                                                                    $test = $item['USER']['PERSONAL_PHOTO'];
                                                                    ?>

                                                                    <?
                                                                    $rsUser = CUser::GetByID($item['UF_USER_ID']);
                                                                    $arUser = $rsUser->Fetch();

                                                                    $src = CFile::GetPath($arUser['PERSONAL_PHOTO']);


                                                                    ?>

                                                                    <?if($USER->IsAuthorized() && is_string($arResult['MODERATION_CURRENT_USER'])  ):?>


                                                                        <? if ($src !== '' &&  !empty($src ) ): ?>
                                                                            <div class="object-fit-container">
                                                                                <img src="<?= $src ?>"
                                                                                     alt="" data-object-fit="cover"
                                                                                     data-object-position="50% 50%"/>
                                                                            </div>
                                                                        <? else: ?>
                                                                            <div class="object-fit-container">
                                                                                <div class="icon icon_user">
                                                                                    <svg class="icon icon_user">
                                                                                        <use xlink:href="<?=SITE_TEMPLATE_PATH?>/images/sprite-svg.svg#user"></use>
                                                                                    </svg>
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
                                                                        <a class=""  href="<?= SITE_DIR ?>user/<?= $item['USER']['ID'] ?>/">
                                                                            <div class="name medium" style="color:#222">
                                                                                <?=$item['COMPANY']['UF_COMPANY_NAME_MIN']?>
                                                                            </div>
                                                                        </a>                                    <?else:?>
                                                                        <a class=""  href="<?= SITE_DIR ?>user/<?= $item['USER']['ID'] ?>/">
                                                                            <div class="name medium" style="color:#222">
                                                                                <?= $item['USER']['NAME'] ?>
                                                                                <?= $item['USER']['LAST_NAME'] ?>
                                                                            </div>
                                                                        </a>
                                                                    <?endif;?>
                                                                <?else:?>
                                                                    <div class="name medium rebue_notAutorize">
                                                                        Название компании
                                                                    </div>
                                                                <?endif;?>

                                                            </div>
                                                        </div>
                                                        <?/*   <a class="lnk-abs"
                                                   href="<?= SITE_DIR ?>user/<?= $item['USER']['ID'] ?>/"></a>
*/?>
                                                        <a class="lnk-abs"
                                                           href="<?= SITE_DIR ?>user/moderation/detailModeration/<?=$item['UF_ITEM_ID']?>/?UserNum=<?=$item['USER']['ID']?>"></a>

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
                                            <?/*    <a class="lnk-abs"
                                       href="<?= SITE_DIR . '' . $item['UF_IBLOCK_TYPE'] . '/' . $item['UF_IBLOCK_CODE'] . '/' . $item['UF_CODE'] . '/' ?>"></a> */?>
                                            <a class="lnk-abs"
                                               href="<?= SITE_DIR ?>user/moderation/detailModeration/<?=$item['UF_ITEM_ID']?>/?UserNum=<?=$item['USER']['ID']?>"></a>
                                        </div>
                                        <?php

                                        if ($i == 1) {
                                            ?>
                                            <br>
                                            <?
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
    <script>
        BX.message({userFavouritesPath: '<?=$this->GetFolder()?>/ajax.php'});
    </script>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}
