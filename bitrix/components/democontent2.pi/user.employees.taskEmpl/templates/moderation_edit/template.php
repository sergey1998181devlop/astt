<?php
/**
 * User: Nazarkin Sergey
 * Email: nazarkin2017@mail.ru
 * Date: 07.10.2020
 * Time: 13:07
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
global $APPLICATION;
$route = [];
$characteristics = [];
if (strlen($arResult['UF_PROPERTIES']) > 0) {
    $characteristics = unserialize($arResult['UF_PROPERTIES']);

    foreach ($characteristics as $k => $v) {
        if (isset($v['route'])) {
            $route = $v['route'];
            unset($characteristics[$k]);
            break;
        }
    }
}
//debmes($arResult);

if (count($route)) {
    \Bitrix\Main\Page\Asset::getInstance()->addJs(
        'https://api-maps.yandex.ru/2.1/?load=package.full&lang=ru_RU&apikey=' . \Bitrix\Main\Config\Option::get(DSPI, 'yandex_maps_api_key')
    );
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

$currencyName = \Bitrix\Main\Config\Option::get(DSPI, 'currency_name');

$quicklyCost = \Bitrix\Main\Config\Option::get(DSPI, 'quickly_option_cost');
$quickly = 0;

if (count($arResult['UF_QUICKLY_END']) > 0 && strtotime($arResult['UF_QUICKLY_END']) > strtotime(date('Y-m-d H:i'))) {
    $quickly = 1;
}

$executor = 0;
foreach ($arResult['RESPONSES'] as $key => $response) {
    if ($response['UF_EXECUTOR']) {
        $executor = $response;
    }
}

$safeCrowOrdersCount = count($arResult['SAFECROW_ORDERS']);

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}

$blockClass = '';
switch ($arResult['UF_STATUS']) {
    case 6:
        $blockClass = 'arbitration';
        break;
    default:
        if ($quickly) {
            $blockClass = 'urgent';
        }
}
$stagesL = count($arResult['STAGES']);


$chatEnabled = false;
if (intval(\Bitrix\Main\Config\Option::get(DSPI, 'chatEnabled'))) {
    $chatEnabled = true;
}
$APPLICATION->SetAdditionalCss("style.css");
$string = $arResult['MODERATION_REASON'];
preg_match_all('/[^\W\d][\w]*/u', $string, $result);

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


if(!empty($arResult['c']['UF_DATA_START_STRING']) && $arResult['c']['UF_DATA_START_STRING'] !== ''){
    $dateStartObj = \Bitrix\Main\Type\DateTime::createFromTimestamp(MakeTimeStamp($arResult['c']['UF_DATA_START_STRING'], "DD.MM.YYYY HH:MI:SS"));
    //переделываю в старый формат после unix
    $dateStartStringWithTime = $dateStartObj->format("d.m.Y H:i:s");
    $dateStartString = $dateStartObj->format("d.m.Y");


}else{
    $dateStartString = '';
}

if(!empty($arResult['c']['UF_DATA_END_STRING']) && $arResult['c']['UF_DATA_END_STRING'] !== ''){
    $dateEndObj = \Bitrix\Main\Type\DateTime::createFromTimestamp(MakeTimeStamp($arResult['c']['UF_DATA_END_STRING'], "DD.MM.YYYY"));
    $dateEndString = $dateEndObj->format("d.m.Y");
}else{
    $dateEndString = '';
}
if(!empty($arResult['c']['UF_DATA_START_STRING']) && !empty($arResult['c']['UF_DATA_END_STRING'])){
    $date1 = new DateTime($dateStartString);
    $date2 = new DateTime($dateEndString);
    $interval = $date1->diff($date2);
    $dateCount = $interval->d + 1;
    $word = num2word((int)$dateCount, array('смену', 'смены', 'смен'));

}else{
    $dateCount = '1';
    $word = 'смену';
}
?>
    <div class="page-content">
        <div class="wrapper">
            <div class="row task-container" id="sticky-container">

                <div class="col-md-3 col-lg-4 col-xxs-12 pull-right">
                    <div class="sticky-contaier-new sticky-block" data-container="#sticky-container">
                        <div class="user-card-block white-block " data-container="#sticky-container">
                            <?
                            if (method_exists($this, 'createFrame')) {
                                $frame = $this->createFrame()->begin();
                            }
                            ?>
                            <? if (count($arResult['MY_OFFER']) && $arResult['MY_OFFER']['UF_DENIED']): ?>
                                <div class="alert alert-danger">
                                    <?= Loc::getMessage('DETAIL_COMPONENT_YOUR_DENIED') ?>
                                </div>
                                <br>
                            <? endif ?>
                            <?
                            if (method_exists($this, 'createFrame')) {
                                $frame->end();
                            }
                            ?>
                            <div class="user-card-block-in">
                                <div class="tbl tbl-fixed">
                                    <div class="tbc">
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
                                            ?>
                                            <?if($USER->IsAuthorized() && is_string($arResult['MODERATION_CURRENT_USER'])  ):?>
                                                <?
                                                $rsUser = CUser::GetByID($arResult['UF_USER_ID']);
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
                                    </div>
                                    <div class="tbc">
                                        <?if($USER->IsAuthorized() && is_string($arResult['MODERATION_CURRENT_USER'])):?>
                                            <?if(!empty($arResult['COMPANY'][0]['ID'])):?>
                                                <a class=""  href="<?= SITE_DIR ?>user/<?= $arResult['USER']['ID'] ?>/">
                                                    <div class="name medium" style="color:#222">
                                                        <?=$arResult['COMPANY'][0]['UF_COMPANY_NAME_MIN']?>
                                                    </div>
                                                </a>                                    <?else:?>
                                                <a class=""  href="<?= SITE_DIR ?>user/<?= $arResult['USER']['ID'] ?>/">
                                                    <div class="name medium" style="color:#222">
                                                        <?= $arResult['USER']['NAME'] ?>
                                                        <?= $arResult['USER']['LAST_NAME'] ?>
                                                    </div>
                                                </a>
                                            <?endif;?>
                                        <?else:?>
                                            <div class="name medium rebue_notAutorize">
                                                Название компании
                                            </div>
                                        <?endif;?>
                                        <div class="user-info">
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
                                                         style="width: <?= $arResult['CURRENT_RATING']['percent'] * 1 ?>%">
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
                                            <span class="like" href="#">
                                                <svg class="icon icon_thumbs-o-up">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#thumbs-o-up"></use>
                                                </svg><?= $arResult['CURRENT_RATING']['positive'] * 1 ?></span>
                                                    <span class="dislike" href="#">
                                                <svg class="icon icon_thumbs-o-down">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#thumbs-o-down"></use>
                                                </svg><?= $arResult['CURRENT_RATING']['negative'] * 1 ?></span>
                                                </div>
                                            </div>

                                            <? if (count($arResult['USER']['UF_DSPI_CITY'])): ?>
                                                <div class="location-box">
                                                    <svg class="icon icon_location">
                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#location"></use>
                                                    </svg>
                                                    <?= $arResult['CITIES'][$arResult['USER']['UF_DSPI_CITY']]['name'] ?>
                                                </div>
                                            <? endif ?>
                                            <div class="date-box">
                                                <svg class="icon icon_time">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#time"></use>
                                                </svg>
                                                <?= Loc::getMessage('DETAIL_COMPONENT_USER_REG') ?>:
                                                <span title="<?= date('Y-m-d H:i', strtotime($arResult['USER']['DATE_REGISTER'])) ?>"
                                                      class="timestamp"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a class="lnk-abs" href="<?= SITE_DIR ?>user/<?= $arResult['USER']['ID'] ?>/"></a>
                            </div>
                            <?php
                            if ($arResult['CONTACTS_OPEN_COST']) {
                                ?>
                                <div class="btn btn-blue contacts-open" data-id="<?= $arResult['UF_USER_ID'] ?>"
                                     data-hash="<?= md5($arResult['UF_USER_ID'] . \Democontent2\Pi\Sign::getInstance()->get()) ?>">
                                    ���������� ������� - <?= $arResult['CONTACTS_OPEN_COST'] ?> <?= $arResult['CURRENCY'] ?>
                                </div>
                                <div class="contacts-view"></div>
                                <div class="contacts-error dn">
                                    ������������ ������� ��� ��������� ��������� ���������.<br>
                                    <a href="<?= SITE_DIR ?>user/balance/">��������� ������</a>
                                </div>
                                <?php
                            }
                            ?>
                            <?php
                            if ($USER->IsAdmin() && intval($arResult['UF_MODERATION'])) {
                                ?>
                                <div class="moderation">
                                    <form action="<?= $APPLICATION->GetCurPage(false) ?>"
                                          method="post">
                                        <input type="hidden" name="moderation" value="approve">
                                        <button type="submit" class="moderation-approve">
                                            <?= Loc::getMessage('DETAIL_COMPONENT_APPROVE') ?>
                                        </button>
                                    </form>
                                  <?/*  <form action="<?= $APPLICATION->GetCurPage(false) ?>"
                                          method="post">
                                        <input type="hidden" name="moderation" value="reject">
                                    */?>
                                        <a href="#popup-back-task" data-fancybox="">

                                            <button type="submit" class="moderation-rejected">
                                                <?= Loc::getMessage('DETAIL_COMPONENT_REMOVE') ?>
                                            </button>

                                        </a>
                                    <div id="popup-back-task" class="popup popup-back-task" style="display: none">

                                        <div class="tabs-head">
                                            <ul>
                                                <li class="active">
                                                    <a href="#tab1">
                                                        <?= Loc::getMessage('DETAIL_COMPONENT_BACK_TASK_TITLE') ?>
                                                    </a>

                                                </li>

                                            </ul>
                                        </div>
                                        <div class="popup-body">
                                            <div class="tabs-wrap">


                                                <div class="tabs-wrap">
                                                    <div class="tab active" id="tab3">

                                                        <form class="refusal_app_form ajax-form" action="<?= $APPLICATION->GetCurPage(false) ?>" method="post" >
                                                            <input type="hidden" name="ELEMENT_ID" value="<?=$arResult['UF_ID']?>">
                                                            <input type="hidden" name="moderation" value="reject">
                                                            <div class="col-sm-12 col-xxs-12">
                                                                <div class="form-group">
                                                                    <input class="checkbox contract-inp" type="checkbox" name="notCorrect"
                                                                           value="<?= Loc::getMessage('NOTCORRECT') ?>" id="notCorrect"/>
                                                                    <label class="form-checkbox" for="notCorrect">
                                                                        <?= Loc::getMessage('NOTCORRECT') ?>
                                                                        <span class="icon-wrap">
                                                                                            <svg class="icon icon_checkmark">
                                                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                                                                            </svg>
                                                                                        </span>

                                                                    </label>
                                                                </div>
                                                            </div>

                                                            <div class="col-sm-12 col-xxs-12">
                                                                <div class="form-group">
                                                                    <input class="checkbox contract-inp" type="checkbox" name="notCorrectFile"
                                                                           value="<?= Loc::getMessage('NOTCORRECTFILE') ?>" id="notCorrectFile"/>
                                                                    <label class="form-checkbox" for="notCorrectFile">
                                                                        <?= Loc::getMessage('NOTCORRECTFILE') ?>
                                                                        <span class="icon-wrap">
                                                                                            <svg class="icon icon_checkmark">
                                                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                                                                            </svg>
                                                                                        </span>

                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 col-xxs-12">
                                                                <div class="form-group">
                                                                    <input class="checkbox contract-inp" type="checkbox" name="notCorrectBadFiles"
                                                                           value="<?= Loc::getMessage('BADFILES') ?>" id="notCorrectBadFiles"/>
                                                                    <label class="form-checkbox" for="notCorrectBadFiles">
                                                                        <?= Loc::getMessage('BADFILES') ?>
                                                                        <span class="icon-wrap">
                                                                                            <svg class="icon icon_checkmark">
                                                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                                                                            </svg>
                                                                                        </span>

                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 col-xxs-12">
                                                                <div class="form-group">
                                                                    <input class="checkbox contract-inp" type="checkbox" name="notCorrectMoneySmall"
                                                                           value="<?= Loc::getMessage('MONEYSMALL') ?>" id="notCorrectMoneySmall"/>
                                                                    <label class="form-checkbox" for="notCorrectMoneySmall">
                                                                        <?= Loc::getMessage('MONEYSMALL') ?>
                                                                        <span class="icon-wrap">
                                                                                            <svg class="icon icon_checkmark">
                                                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                                                                            </svg>
                                                                                        </span>

                                                                    </label>
                                                                </div>
                                                            </div>




                                                            <div class="text-center">
                                                                <button class="moderation-rejected refusal_app-button btn-submit " type="submit">
                                                                    <?= Loc::getMessage('DETAIL_COMPONENT_BACK_TASK_BUTTON') ?>
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>

                                    </div>

                                 <?/*   </form> */?>
                                </div>
                                <?php
                            }
                            ?>

                        </div>

                        <div class="user-card-block white-block "
                             data-container="#sticky-container">
                            <? if ($executor > 0): ?>
                                <div class="h4">
                                    <?= Loc::getMessage('DETAIL_EXUDOR_MESSAGE') ?>:
                                </div>
                                <div class="user-card-block-in">
                                    <div class="tbl tbl-fixed">
                                        <div class="tbc">
                                            <div class="ava">
                                                <?php
                                                if ($chatEnabled) {
                                                    ?>
                                                    <div class="status-indicator uid<?= \Democontent2\Pi\Utils::getChatId($executor['USER_DATA']['ID']) ?>"></div>
                                                    <?php
                                                }
                                                ?>
                                                <? if ($executor['USER_DATA']['PERSONAL_PHOTO'] > 0): ?>
                                                    <?php
                                                    $ava = CFile::ResizeImageGet($executor['USER_DATA']['PERSONAL_PHOTO'],
                                                        array(
                                                            'width' => 150,
                                                            'height' => 150
                                                        ),
                                                        BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                                        true
                                                    );
                                                    ?>
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
                                        <div class="tbc">
                                            <div class="name medium">
                                                <?= $executor['USER_DATA']['NAME'] ?>
                                                <?= $executor['USER_DATA']['LAST_NAME'] ?>
                                            </div>
                                            <div class="user-info">
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
                                                             style="width:<?= $executor['CURRENT_RATING']['percent'] * 1 ?>%">
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
                                                            <?= $executor['CURRENT_RATING']['positive'] * 1 ?>
                                                        </span>
                                                        <span class="dislike">
                                                            <svg class="icon icon_thumbs-o-down">
                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#thumbs-o-down"></use>
                                                            </svg>
                                                        <?= $executor['CURRENT_RATING']['negative'] * 1 ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <? if (count($executor['USER_DATA']['UF_DSPI_CITY'])): ?>
                                                    <div class="location-box">
                                                        <svg class="icon icon_location">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#location"></use>
                                                        </svg>
                                                        <?= $arResult['CITIES'][$executor['USER_DATA']['UF_DSPI_CITY']]['name'] ?>
                                                    </div>
                                                <? endif ?>
                                                <div class="date-box">
                                                    <svg class="icon icon_time">
                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#time"></use>
                                                    </svg>
                                                    <?= Loc::getMessage('DETAIL_COMPONENT_USER_REG') ?>:
                                                    <span title="<?= date('Y-m-d H:i', strtotime($executor['USER_DATA']['DATE_REGISTER'])) ?>"
                                                          class="timestamp"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="lnk-abs" href="<?= SITE_DIR ?>user/<?= $executor['USER_DATA']['ID'] ?>/"></a>
                                </div>
                                <? if ($arResult['UF_STATUS'] == 4): ?>
                                    <div class="alert alert-warning text-center">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_STATUS_4') ?>
                                        <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="post">
                                            <input type="hidden" name="unSetExecutor" value="1">
                                            <button class="btn btn-xs btn-red">
                                                <?= Loc::getMessage('DETAIL_COMPONENT_EXECUTOR_REMOVE') ?>
                                            </button>
                                        </form>
                                    </div>
                                <? endif ?>
                            <? else: ?>
                                <div class="alert-success-error  ">
                                    <?= Loc::getMessage('DETAIL_EXUDOR_MESSAGE') ?>

                                    <?if(!empty($arResult['MODERATION_REASON'])):?>
                                        <div class="listReason">
                                            <?=$arResult['MODERATION_REASON']?>
                                        </div>
                                    <?endif;?>
                                </div>

                            <? endif ?>

                            <br>
                            <div class="text-center" id="chatButton"></div>
                        </div>

                    </div>

                </div>

                <div class="col-sm-12 col-md-9 col-lg-8 col-xxs-12">
                    <div class="white-block <?= $blockClass ?>"
                         id="task"
                         data-iblockid="<?= $arResult['UF_IBLOCK_ID'] ?>"
                         data-taskid="<?= $arResult['UF_ID'] ?>">
                        <div class="top-panel">
                            <div class="tbl">
                                <div class="tbc">
                                    <div class="h2 title">
                                        <? if ($arResult['UF_SAFE']): ?>
                                            <span class="security" data-tooltip=""
                                                  data-title="<?= Loc::getMessage('DETAIL_COMPONENT_STAGES_SECURITY_TOOLTIP') ?>">
                                            <svg class="icon icon_shield_doc">
                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#shield-doc"></use>
                                            </svg>
                                        </span>
                                        <? endif ?>
                                        <? if ($quickly): ?>
                                            <span class="fire">
                                                <svg class="icon icon_fire">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#fire"></use>
                                                </svg>
                                            </span>
                                        <? endif ?>
                                        <h1><?= $arResult['UF_NAME'] ?></h1>
                                    </div>
                                </div>
                         
                                <div class="tbc">
                                    <div class="price-box">
                                        <?if(!empty($arResult['c']['UF_NDS'])):?>
                                            <?if(!empty($arResult['c']['UF_NDS'])):?>
                                                <span class="nds" >Безнал с НДС: <?=(string)$arResult['c']['UF_NDS']?>₽</span><br>
                                            <?endif;?>
                                        <?endif;?>
                                        <?if(!empty($arResult['c']['UF_BEZNAL']) ):?>
                                            <?if(!empty($arResult['c']['UF_BEZNAL'])):?>
                                                <span class="bezznal" >Безнал без НДС: <?=(string)$arResult['c']['UF_BEZNAL']?>₽</span><br>
                                            <?endif;?>
                                        <?endif;?>

                                        <?if(!empty($arResult['c']['UF_NALL']) ):?>
                                            <?if(!empty($arResult['c']['UF_NALL'])):?>
                                                <span class="nall" >Нал: <?=(string)$arResult['c']['UF_NALL']?>₽</span><br>
                                            <?endif;?>
                                        <?endif;?>

                                        <?if(empty($arResult['c']['UF_BEZNAL']) && empty($arResult['c']['UF_NDS']) && empty($arResult['c']['UF_NALL'])):?>
                                            <span class="nall" >По договоренности</span><br>
                                        <?endif;?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="task-indicators">
                            <div class="itm">
                                <?
                                switch ($arResult['MODERATION']) {

                                    case 2:
                                        ?>
                                        <div class="status completed">
                                            <?= Loc::getMessage('DETAIL_COMPONENT_STATUS_2') ?>
                                        </div>
                                        <?
                                        break;


                                }
                                ?>
                            </div>
                            <? if (!empty($arResult['CITY'])): ?>
                                <div class="itm">
                                    <div class="location-box">
                                        <svg class="icon icon_location">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#location"></use>
                                        </svg>
                                        <?= $arResult['CITY']['name'] ?>
                                    </div>
                                </div>
                            <? endif ?>
                            <div class="itm">
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
                           <?/* <div class="itm">
                                <div class="views-box">
                                    <svg class="icon icon_eye">
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#eye"></use>
                                    </svg>
                                    <?php
                                    echo \Democontent2\Pi\Utils::declension(
                                        $arResult['UF_COUNTER'],
                                        array(
                                            Loc::getMessage('DETAIL_COMPONENT_VIEWS_1'),
                                            Loc::getMessage('DETAIL_COMPONENT_VIEWS_2'),
                                            Loc::getMessage('DETAIL_COMPONENT_VIEWS_3')
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
*/?>

                <?/*            <div class="itm">
                                <div class="responses-box left">
                                    <svg class="icon icon_comment">
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#comment"></use>
                                    </svg>
                                    <?php
                                    echo \Democontent2\Pi\Utils::declension(
                                        $arResult['UF_RESPONSE_COUNT'],
                                        array(
                                            Loc::getMessage('DETAIL_COMPONENT_RESPONSE_1'),
                                            Loc::getMessage('DETAIL_COMPONENT_RESPONSE_2'),
                                            Loc::getMessage('DETAIL_COMPONENT_RESPONSE_3')
                                        )
                                    );
                                    ?>
                                </div>
                            </div> */?>
                        </div>


                        <form method="post" class="formEdit" enctype="multipart/form-data" action="<?= $APPLICATION->GetCurPage() ?>">
                            <?php
                            if (count($route)) {
                                ?>
                                <div id="route-area">
                                    <div id="map-area"></div>
                                </div>
                                <?php
                            }
                            ?>
                            <?if(array_search('notIssetCompany' , $result[0])):?>
<!--                            --><?//pre($arResult);?>
                                <? if (!empty($dateStartString)): ?>
                                    <p>
                                        <span class="ttl bold">
                                            <?= Loc::getMessage('DETAIL_COMPONENT_DATE_START') ?>:
                                        </span>
                                        <?= date('d.m.Y H:i', strtotime($dateStartString)) ?>
                                    </p>
                                <? endif ?>
                                <? if (!empty($dateEndString)): ?>
                                    <p>
                                        <span class="ttl bold"><?= Loc::getMessage('DETAIL_COMPONENT_DATE_END') ?>:</span>
                                        <?= date('d.m.Y H:i', strtotime($dateEndString)) ?>
                                    </p>
                                <? endif ?>

                                <? if ($stagesL > 0): ?>
                                    <div class="ttl bold">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_STAGES_TITLE') ?>:
                                    </div>
                                    <table class="table stages-list">
                                        <thead>
                                        <tr>
                                            <th class="text-left">�</th>
                                            <th class="text-left">
                                                <?= Loc::getMessage('DETAIL_COMPONENT_STAGES_TBL_TH1') ?>
                                            </th>
                                            <th class="text-left">
                                                <?= Loc::getMessage('DETAIL_COMPONENT_STAGES_TBL_TH2') ?>
                                            </th>
                                        </tr>
                                        </thead>
                                        <?php
                                        $i = 0;
                                        foreach ($arResult['STAGES'] as $STAGE) {
                                            $i++;
                                            ?>
                                            <tr>
                                                <td>
                                                    <?= $i++; ?>
                                                </td>
                                                <td>
                                                    <?= $STAGE['UF_NAME'] ?>
                                                </td>
                                                <td class="price">
                                                    <? if ($STAGE['UF_PRICE'] > 0): ?>
                                                        <?= \Democontent2\Pi\Utils::price($STAGE['UF_PRICE']) ?>
                                                        <span class="currency"><?= $currencyName ?></span>
                                                    <? else: ?>
                                                        <span>
                                            <?= Loc::getMessage('DETAIL_COMPONENT_PRICE_CONTRACT') ?>
                                        </span>
                                                    <? endif ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                <? endif ?>
                                <? if ($arResult['UF_DESCRIPTION']): ?>
                                    <div class="ttl bold">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_DESC_TITLE') ?>:
                                    </div>
                                    <div class="descript">
                                        <p>  <?= TxtToHTML($arResult['UF_DESCRIPTION'], false) ?></p>
                                    </div>
                                <? endif ?>

                                <div class="col-sm-12 col-xxs-12">
                                    <div class="forum-group">

                                        <?php
                                        $files = unserialize($arResult['UF_FILES']);
                                        $hiddenFiles = unserialize($arResult['UF_HIDDEN_FILES']);
                                        if (count($files) > 0) {
                                            $images = [];
                                            $otherFiles = [];

                                            foreach ($files as $key => $file) {
                                                $getFile = CFile::GetFromCache($file);

                                                if (\Democontent2\Pi\Utils::checkImage($getFile[$file]['FILE_NAME'])) {
                                                    $images[$file] = $getFile[$file];
                                                } else {
                                                    $otherFiles[$file] = $getFile[$file];
                                                }
                                            }
                                            ?>

                                            <?php
                                            if (count($images) > 0) {
                                                ?>
                                                <label>
                                                    <?= Loc::getMessage('NOT_COLLECT_FILES') ?>
                                                </label>
                                                <div class="attachment-picts row">
                                                    <?php
                                                    foreach ($images as $image) {
                                                        $imageThumb = CFile::ResizeImageGet(
                                                            $image['ID'],
                                                            [
                                                                'width' => 90,
                                                                'height' => 90
                                                            ],
                                                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                                            true
                                                        );
                                                        ?>
                                                        <div class="item">
                                                            <a href="/upload/<?= $image['SUBDIR'] ?>/<?= $image['FILE_NAME'] ?>"
                                                               data-fancybox="gallery-attachment">
                                                                <div class="in">
                                                                    <div class="object-fit-container">
                                                                        <img src="<?= $imageThumb['src'] ?>"
                                                                             alt="" data-object-fit="cover"
                                                                             data-object-position="50% 50%"/>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                            }

                                            if (count($otherFiles) > 0) {
                                                ?>
                                                <ul class="list-attachment">
                                                    <?
                                                    foreach ($otherFiles as $file) {
                                                        ?>
                                                        <li>
                                                            <a download target="_blank"
                                                               href="/upload/<?= $file['SUBDIR'] ?>/<?= $file['FILE_NAME'] ?>">
                                                                <svg class="icon icon_clip">
                                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#clip"></use>
                                                                </svg>
                                                                <span><?= $file['DESCRIPTION'] ?></span>
                                                            </a>
                                                        </li>
                                                        <?
                                                    }
                                                    ?>
                                                </ul>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>

                                <?php

                                if (!empty($arResult['FILES_USER_MODERATION_EDIT'][0])) {
                                    $images = [];


                                    ?>
                                    <div class="ttl bold">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_FILES_TITLE') ?>:
                                    </div>

                                        <div class="attachment-picts row">
                                            <?php
                                            foreach ($arResult['FILES_USER_MODERATION_EDIT'] as $image) {

                                                $imageThumb = CFile::ResizeImageGet(
                                                    $image,
                                                    [
                                                        'width' => 90,
                                                        'height' => 90
                                                    ],
                                                    BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                                    true
                                                );
                                                ?>
                                                <div class="item">
                                                    <a href="/upload/<?= $image['SUBDIR'] ?>/<?= $image['FILE_NAME'] ?>"
                                                       data-fancybox="gallery-attachment">
                                                        <div class="in">
                                                            <div class="object-fit-container">
                                                                <img src="<?= $imageThumb['src'] ?>" alt=""
                                                                     data-object-fit="cover"
                                                                     data-object-position="50% 50%"/>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>





<?}?>
                            <?endif;?>
                            <?if(array_search('notCorrect' , $result[0])):?>

                                <h5><?= Loc::getMessage('CREATE_COMPONENT_PUBLIC_FILES_TITLE') ?></h5>
                                <div class="form-group">
                                    <label for="create-inp-desc">
                                        <?= Loc::getMessage('CREATE_COMPONENT_LBL_TTL_MES') ?>
                                    </label>
                                    <textarea required id="create-inp-desc" name="description" class="form-control required"
                                              placeholder="<?= Loc::getMessage('CREATE_COMPONENT_PL_MES') ?>"
                                              rows="5"></textarea>
                                </div>




                            <?endif;?>
                            <?if(array_search('notCorrectFile' , $result[0])):?>


                                <h5><?= Loc::getMessage('CREATE_COMPONENT_PUBLIC_FILES_TITLE2') ?></h5>
                                <div class="form-group">
                                    <label>
                                        <?= Loc::getMessage('CREATE_COMPONENT_PUBLIC_FILES_LABEL') ?>
                                    </label>
                                    <div id="files-list" class="files"></div>
                                    <div id="btn-file" class="btn btn-sm btn-file">
                                        <span><?= Loc::getMessage('CREATE_COMPONENT_ADD_FILE') ?></span>
                                        <input type="file" name="__files[]"  >
                                    </div>
                                </div>


                                <div class="col-sm-12 col-xxs-12">
                                    <div class="forum-group">

                                        <?php
                                        $files = unserialize($arResult['UF_FILES']);
                                        $hiddenFiles = unserialize($arResult['UF_HIDDEN_FILES']);
                                        if (count($files) > 0) {
                                            $images = [];
                                            $otherFiles = [];

                                            foreach ($files as $key => $file) {
                                                $getFile = CFile::GetFromCache($file);

                                                if (\Democontent2\Pi\Utils::checkImage($getFile[$file]['FILE_NAME'])) {
                                                    $images[$file] = $getFile[$file];
                                                } else {
                                                    $otherFiles[$file] = $getFile[$file];
                                                }
                                            }
                                            ?>

                                            <?php
                                            if (count($images) > 0) {
                                                ?>
                                                <label>
                                                    <?= Loc::getMessage('NOT_COLLECT_FILES') ?>
                                                </label>
                                                <div class="attachment-picts row">
                                                    <?php
                                                    foreach ($images as $image) {
                                                        $imageThumb = CFile::ResizeImageGet(
                                                            $image['ID'],
                                                            [
                                                                'width' => 90,
                                                                'height' => 90
                                                            ],
                                                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                                            true
                                                        );
                                                        ?>
                                                        <div class="item">
                                                            <a href="/upload/<?= $image['SUBDIR'] ?>/<?= $image['FILE_NAME'] ?>"
                                                               data-fancybox="gallery-attachment">
                                                                <div class="in">
                                                                    <div class="object-fit-container">
                                                                        <img src="<?= $imageThumb['src'] ?>"
                                                                             alt="" data-object-fit="cover"
                                                                             data-object-position="50% 50%"/>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                            }

                                            if (count($otherFiles) > 0) {
                                                ?>
                                                <ul class="list-attachment">
                                                    <?
                                                    foreach ($otherFiles as $file) {
                                                        ?>
                                                        <li>
                                                            <a download target="_blank"
                                                               href="/upload/<?= $file['SUBDIR'] ?>/<?= $file['FILE_NAME'] ?>">
                                                                <svg class="icon icon_clip">
                                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#clip"></use>
                                                                </svg>
                                                                <span><?= $file['DESCRIPTION'] ?></span>
                                                            </a>
                                                        </li>
                                                        <?
                                                    }
                                                    ?>
                                                </ul>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>

                            <?endif;?>
                            <?if(array_search('notCorrectMoneySmall' , $result[0])):?>

                                <div class="moneyBlock">
                                    <div class="col-sm-6 col-xxs-12">
                                        <div class="form-group">
                                            <label for="create-inp2">
                                                <?= Loc::getMessage('CREATE_COMPONENT_LBL_BEZNAL') ?>
                                            </label>
                                            <input required id="create-inp1-nal" class="form-control required" type="text"
                                                   name="nameBeznal" placeholder="<?= Loc::getMessage('CREATE_COMPONENT_LBL_BEZNAL_SUM') ?>"
                                                   value="">
                                        </div>
                                    </div>


                                    <div class="col-sm-6 col-xxs-12">

                                            <div class="form-group">
                                                <label for="create-inp1">
                                                    <?= Loc::getMessage('CREATE_COMPONENT_LBL_BUDGET_BEZ_NDS') ?>
                                                </label>
                                                <input  id="create-inp1-nal" class="form-control " type="text"
                                                        name="contractPrice" placeholder="<?= Loc::getMessage('CREATE_COMPONENT_LBL_BEZNAL_SUM') ?>"
                                                        value="">
                                            </div>

                                    </div>

                                    <div class="col-sm-12 col-xxs-12">
                                        <div class="col-sm-6 col-xxs-12" style="padding:0px">

                                            <div class="form-group">
                                                <label for="create-inp2">
                                                    <?= Loc::getMessage('CREATE_COMPONENT_LBL_NAL') ?>
                                                </label>
                                                <input required id="create-inp1-nal" class="form-control required" type="text"
                                                       name="nameNal" placeholder="<?= Loc::getMessage('CREATE_COMPONENT_LBL_BEZNAL_SUM') ?>"
                                                       value="">
                                            </div>

                                        </div>
                                    </div>


                                </div>
                                <div class="col-sm-12 col-xxs-12">
                                    <div class="form-group">
                                        <input class="checkbox contract-inp" type="checkbox" name="contractPriceDOGOVOR"
                                               value="0" id="create-inpD3"/>
                                        <label class="form-checkbox checkbox-edit-form" for="create-inpD3">
                                            <span class="icon-wrap">
                                                <svg class="icon icon_checkmark">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                                </svg>
                                            </span>
                                            <?= Loc::getMessage('CREATE_COMPONENT_LBL_BUDGET_AGREEMENT') ?>
                                        </label>
                                    </div>
                                </div>


                            <?endif;?>

<?
global $USER;
$idUser = $USER->GetID();


$rsUser = CUser::GetByID($idUser); //id пользователя полученного на пред шаге
$arUserCurrent = $rsUser->Fetch();


?>

                            <div class="col-sm-12 col-xxs-12">
                                <div class="form-group">
                            <input type="hidden" name="setOption" value="quickly">
                            <input type="hidden" name="moderation"    value="repeatModeration">
                            <input type="hidden" name="itemId"    value="<?=$arResult['UF_ITEM_ID']?>">
                            <input type="hidden" name="uf_id_item"    value="<?=$arResult['ID']?>">
                                    <?
                                        $messageBtn = Loc::getMessage('DETAIL_COMPONENT_URGENT_TOOLTIP_RESEND');
                                    ?>
                                    <?if(empty($arUserCurrent['PERSONAL_PHONE'])):
                                        $messageBtn = Loc::getMessage('DETAIL_COMPONENT_URGENT_TOOLTIP_RESEND_NOT_COMPANY');
                                    ?>
                                    <a class="btn btn-green btn-urgent" href="/user/settings/"
                                            data-title="<?= $messageBtn ?>">
                                    <?else:?>
                                            <button class="btn btn-green btn-urgent" type="submit" data-tooltip=""
                                                    data-title="<?= $messageBtn ?>">
                                    <?endif;?>

                                     <?= $messageBtn ?>

                                    <?if(empty($arUserCurrent['PERSONAL_PHONE'])):?>
                                        </a>
                                    <?else:?>
    </button>
<?endif;?>
                                </div>
                            </div>

                        </form>
<?/*
                        <? if ($stagesL > 0): ?>
                            <div class="ttl bold">
                                <?= Loc::getMessage('DETAIL_COMPONENT_STAGES_TITLE') ?>:
                            </div>
                            <table class="table stages-list">
                                <thead>
                                <tr>
                                    <th class="text-left">�</th>
                                    <th class="text-left">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_STAGES_TBL_TH1') ?>
                                    </th>
                                    <th class="text-left">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_STAGES_TBL_TH2') ?>
                                    </th>
                                </tr>
                                </thead>
                                <?php
                                $stagesCompleted = 0;
                                foreach ($arResult['STAGES'] as $STAGE) {
                                    if ($STAGE['UF_STATUS'] == 3) { //stage completed
                                        $stagesCompleted++;
                                    }
                                }

                                if ($executor) {
                                    if ($arResult['UF_SAFE']) {
                                        switch (intval($arResult['UF_STATUS'])) {
                                            case 2:
                                                $i = 0;
                                                foreach ($arResult['STAGES'] as $STAGE) {
                                                    $i++;
                                                    $closed = false;
                                                    $completedByOwner = false;
                                                    switch (intval($STAGE['UF_STATUS'])) {
                                                        case 1:
                                                            $completedByOwner = true;
                                                            break;
                                                        case 3:
                                                            $closed = true;
                                                            break;
                                                    }
                                                    ?>
                                                    <tr<?= (($closed) ? ' class="completed"' : '') ?>>
                                                        <td>
                                                            <?= $i ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            if (!$closed) {
                                                                if (!$completedByOwner) {
                                                                    if ($safeCrowOrdersCount > 0) {
                                                                        $safeCrowOrderFound = false;
                                                                        foreach ($arResult['SAFECROW_ORDERS'] as $safeCrowOrder) {
                                                                            if ($safeCrowOrder['UF_STAGE_ID'] == $STAGE['ID']) {
                                                                                if ($safeCrowOrder['UF_STATUS'] == 'paid') {
                                                                                    $safeCrowOrderFound = true;
                                                                                }
                                                                                break;
                                                                            }
                                                                        }

                                                                        if (!$safeCrowOrderFound) {
                                                                            //����� ���, ������ ������
                                                                            ?>
                                                                            <?= $STAGE['UF_NAME'] ?>
                                                                            <div class="stage-btns">
                                                                                <form method="post"
                                                                                      action="<?= $APPLICATION->GetCurPage(false) ?>">
                                                                                    <input type="hidden" name="pay"
                                                                                           value="1">
                                                                                    <input type="hidden" name="stageId"
                                                                                           value="<?= $STAGE['ID'] ?>">
                                                                                    <button type="submit"
                                                                                            class="btn btn-green btn-xs ">
                                                                                        <?= Loc::getMessage('DETAIL_COMPONENT_SAFE_ADD_MONEY') ?>
                                                                                    </button>
                                                                                </form>
                                                                            </div>
                                                                            <?php
                                                                        } else {
                                                                            //������ ������� + ������ ������������
                                                                            ?>
                                                                            <div class="badge badge-success">
                                                                                <?= Loc::getMessage('DETAIL_COMPONENT_SAFE_ADD_MONEY_EXISTS') ?>
                                                                            </div>
                                                                            <?= $STAGE['UF_NAME'] ?>
                                                                            <div class="stage-btns">
                                                                                <a href="#popup-complain"
                                                                                   class="btn btn-red btn-xs btn-complain"
                                                                                   data-id="<?= $STAGE['ID'] ?>">
                                                                                    <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLAIN') ?>
                                                                                </a>
                                                                            </div>
                                                                            <?php
                                                                        }
                                                                    } else {
                                                                        //����� ���, ������ ������
                                                                        ?>

                                                                        <?= $STAGE['UF_NAME'] ?>
                                                                        <div class="stage-btns">
                                                                            <form method="post"
                                                                                  action="<?= $APPLICATION->GetCurPage(false) ?>">
                                                                                <input type="hidden" name="pay"
                                                                                       value="1">
                                                                                <input type="hidden" name="stageId"
                                                                                       value="<?= $STAGE['ID'] ?>">
                                                                                <button type="submit"
                                                                                        class="btn btn-green btn-xs">
                                                                                    <?= Loc::getMessage('DETAIL_COMPONENT_SAFE_ADD_MONEY') ?>
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                        <?php
                                                                    }
                                                                } else {
                                                                    if (($stagesL - $stagesCompleted) == 1) {
                                                                        ?>

                                                                        <div class="badge badge-success">
                                                                            <?= Loc::getMessage('DETAIL_COMPONENT_SAFE_EXECUTOR_END') ?>
                                                                        </div>
                                                                        <?= $STAGE['UF_NAME'] ?>
                                                                        <div class="stage-btns">
                                                                            <a href="#popup-feedback"
                                                                               class="btn btn-green btn-xs btn-completed-all"
                                                                               data-id="<?= $STAGE['ID'] ?>">
                                                                                <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLETED') ?>
                                                                            </a>
                                                                            <a href="#popup-complain"
                                                                               class="btn btn-red btn-xs btn-complain"
                                                                               data-id="<?= $STAGE['ID'] ?>">
                                                                                <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLAIN') ?>
                                                                            </a>
                                                                        </div>
                                                                        <?php
                                                                    } else {
                                                                        if (($stagesL - $stagesCompleted) > 1) {
                                                                            ?>
                                                                            <div class="badge badge-success">
                                                                                <?= Loc::getMessage('DETAIL_COMPONENT_SAFE_EXECUTOR_END') ?>
                                                                            </div>
                                                                            <?= Loc::getMessage('') ?>
                                                                            <?= $STAGE['UF_NAME'] ?>
                                                                            <div class="stage-btns">
                                                                                <a href="#popup-confirmation"
                                                                                   class="btn btn-green btn-xs btn-completed"
                                                                                   data-id="<?= $STAGE['ID'] ?>">
                                                                                    <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLETED') ?>
                                                                                </a>
                                                                                <a href="#popup-complain"
                                                                                   class="btn btn-red btn-xs btn-complain"
                                                                                   data-id="<?= $STAGE['ID'] ?>">
                                                                                    <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLAIN') ?>
                                                                                </a>
                                                                            </div>
                                                                            <?php
                                                                        }
                                                                    }
                                                                }
                                                            } else {
                                                                ?>
                                                                <?= $STAGE['UF_NAME'] ?>
                                                                <?php
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="price">
                                                            <?= \Democontent2\Pi\Utils::price($STAGE['UF_PRICE']) ?>
                                                            <span class="currency"><?= $currencyName ?></span>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                break;
                                            case 3:
                                                $i = 0;
                                                foreach ($arResult['STAGES'] as $STAGE) {
                                                    $i++;
                                                    ?>
                                                    <tr class="completed">
                                                        <td>
                                                            <?= $i ?>
                                                        </td>
                                                        <td>
                                                            <?= $STAGE['UF_NAME'] ?>
                                                        </td>
                                                        <td class="price">
                                                            <?= \Democontent2\Pi\Utils::price($STAGE['UF_PRICE']) ?>
                                                            <span class="currency"><?= $currencyName ?></span>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                break;
                                            case 6:
                                                $i = 0;
                                                foreach ($arResult['STAGES'] as $STAGE) {
                                                    $i++;
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?= $i ?>
                                                        </td>
                                                        <td>
                                                            <? if ($STAGE['UF_STATUS'] == 2): ?>
                                                                <div class="badge badge-danger">
                                                                    <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_COMPLAIN_EXISTS') ?>
                                                                </div>
                                                            <? endif ?>
                                                            <?= $STAGE['UF_NAME'] ?>
                                                        </td>
                                                        <td class="price">
                                                            <?= \Democontent2\Pi\Utils::price($STAGE['UF_PRICE']) ?>
                                                            <span class="currency"><?= $currencyName ?></span>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                break;
                                            default:
                                                $i = 0;
                                                foreach ($arResult['STAGES'] as $STAGE) {
                                                    $i++;
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?= $i ?>
                                                        </td>
                                                        <td>
                                                            <?= $STAGE['UF_NAME'] ?>
                                                        </td>
                                                        <td class="price">
                                                            <?= \Democontent2\Pi\Utils::price($STAGE['UF_PRICE']) ?>
                                                            <span class="currency"><?= $currencyName ?></span>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                        }

                                    } else {
                                        switch (intval($arResult['UF_STATUS'])) {
                                            case 2:
                                                $i = 0;
                                                foreach ($arResult['STAGES'] as $STAGE) {
                                                    $i++;
                                                    $closed = false;
                                                    $completedByOwner = false;
                                                    switch (intval($STAGE['UF_STATUS'])) {
                                                        case 1:
                                                            $completedByOwner = true;
                                                            break;
                                                        case 3:
                                                            $closed = true;
                                                            break;
                                                    }
                                                    ?>
                                                    <tr<?= (($closed) ? ' class="completed"' : '') ?>>
                                                        <td>
                                                            <?= $i ?>
                                                        </td>
                                                        <td>

                                                            <?php
                                                            if (!$closed) {
                                                                if (!$completedByOwner) {
                                                                    ?>
                                                                    <?= $STAGE['UF_NAME'] ?>
                                                                    <div class="stage-btns">
                                                                        <a href="#popup-complain"
                                                                           class="btn btn-red btn-xs btn-complain"
                                                                           data-id="<?= $STAGE['ID'] ?>">
                                                                            <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLAIN') ?>
                                                                        </a>
                                                                    </div>
                                                                    <?php
                                                                } else {
                                                                    //������� ��������� ������������, �������� ��������� + ������������
                                                                    if (($stagesL - $stagesCompleted) == 1) {
                                                                        ?>

                                                                        <div class="badge badge-success">
                                                                            <?= Loc::getMessage('DETAIL_COMPONENT_SAFE_EXECUTOR_END') ?>
                                                                        </div>
                                                                        <?= $STAGE['UF_NAME'] ?>
                                                                        <div class="stage-btns">
                                                                            <a href="#popup-feedback"
                                                                               class="btn btn-green btn-xs btn-completed-all"
                                                                               data-id="<?= $STAGE['ID'] ?>">
                                                                                <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLETED') ?>
                                                                            </a>
                                                                            <a href="#popup-complain"
                                                                               class="btn btn-red btn-xs btn-complain"
                                                                               data-id="<?= $STAGE['ID'] ?>">
                                                                                <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLAIN') ?>
                                                                            </a>
                                                                        </div>
                                                                        <?php
                                                                    } else {
                                                                        if (($stagesL - $stagesCompleted) > 1) {
                                                                            ?>
                                                                            <div class="badge badge-success">
                                                                                <?= Loc::getMessage('DETAIL_COMPONENT_SAFE_EXECUTOR_END') ?>
                                                                            </div>
                                                                            <?= Loc::getMessage('') ?>
                                                                            <?= $STAGE['UF_NAME'] ?>
                                                                            <div class="stage-btns">
                                                                                <a href="#popup-confirmation"
                                                                                   class="btn btn-green btn-xs btn-completed"
                                                                                   data-id="<?= $STAGE['ID'] ?>">
                                                                                    <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLETED') ?>
                                                                                </a>
                                                                                <a href="#popup-complain"
                                                                                   class="btn btn-red btn-xs btn-complain"
                                                                                   data-id="<?= $STAGE['ID'] ?>">
                                                                                    <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLAIN') ?>
                                                                                </a>
                                                                            </div>
                                                                            <?php
                                                                        }
                                                                    }
                                                                }
                                                            } else {
                                                                ?>
                                                                <?= $STAGE['UF_NAME'] ?>
                                                                <?php
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="price">
                                                            <?= \Democontent2\Pi\Utils::price($STAGE['UF_PRICE']) ?>
                                                            <span class="currency"><?= $currencyName ?></span>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                break;
                                            case 3:
                                                $i = 0;
                                                foreach ($arResult['STAGES'] as $STAGE) {
                                                    $i++;
                                                    ?>
                                                    <tr class="completed">
                                                        <td>
                                                            <?= $i ?>
                                                        </td>
                                                        <td>
                                                            <?= $STAGE['UF_NAME'] ?>
                                                        </td>
                                                        <td class="price">
                                                            <? if ($STAGE['UF_PRICE'] > 0): ?>
                                                                <?= \Democontent2\Pi\Utils::price($STAGE['UF_PRICE']) ?>
                                                                <span class="currency"><?= $currencyName ?></span>
                                                            <? else: ?>
                                                                <span><?= Loc::getMessage('DETAIL_COMPONENT_PRICE_CONTRACT') ?></span>
                                                            <? endif ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                break;
                                            case 6:
                                                $i = 0;
                                                foreach ($arResult['STAGES'] as $STAGE) {
                                                    $i++;
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?= $i ?>
                                                        </td>
                                                        <td>
                                                            <? if ($STAGE['UF_STATUS'] == 2): ?>
                                                                <div class="badge badge-danger">
                                                                    <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_COMPLAIN_EXISTS') ?>
                                                                </div>
                                                            <? endif ?>
                                                            <?= $STAGE['UF_NAME'] ?>
                                                        </td>
                                                        <td class="price">
                                                            <?= \Democontent2\Pi\Utils::price($STAGE['UF_PRICE']) ?>
                                                            <span class="currency"><?= $currencyName ?></span>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                break;
                                            default:
                                                $i = 0;
                                                foreach ($arResult['STAGES'] as $STAGE) {
                                                    $i++;
                                                    ?>
                                                    <tr<?= ((intval($STAGE['UF_STATUS']) == 3) ? ' class="completed"' : '') ?>>
                                                        <td>
                                                            <?= $i ?>
                                                        </td>
                                                        <td>
                                                            <?= $STAGE['UF_NAME'] ?>
                                                        </td>
                                                        <td class="price">
                                                            <? if ($STAGE['UF_PRICE'] > 0): ?>
                                                                <?= \Democontent2\Pi\Utils::price($STAGE['UF_PRICE']) ?>
                                                                <span class="currency"><?= $currencyName ?></span>
                                                            <? else: ?>
                                                                <span><?= Loc::getMessage('DETAIL_COMPONENT_PRICE_CONTRACT') ?></span>
                                                            <? endif ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                        }
                                    }
                                } else {
                                    $i = 0;
                                    foreach ($arResult['STAGES'] as $STAGE) {
                                        $i++;
                                        ?>
                                        <tr>
                                            <td>
                                                <?= $i ?>
                                            </td>
                                            <td>
                                                <?= $STAGE['UF_NAME'] ?>
                                            </td>
                                            <td class="price">
                                                <? if ($STAGE['UF_PRICE'] > 0): ?>
                                                    <?= \Democontent2\Pi\Utils::price($STAGE['UF_PRICE']) ?>
                                                    <span class="currency"><?= $currencyName ?></span>
                                                <? else: ?>
                                                    <span><?= Loc::getMessage('DETAIL_COMPONENT_PRICE_CONTRACT') ?></span>
                                                <? endif ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </table>
                        <? endif ?>
*/?>




        <?php/*

                        if (count($hiddenFiles) > 0) {
                            $images = [];
                            $otherFiles = [];

                            foreach ($hiddenFiles as $key => $file) {
                                $getFile = CFile::GetFromCache($file);

                                if (\Democontent2\Pi\Utils::checkImage($getFile[$file]['FILE_NAME'])) {
                                    $images[$file] = $getFile[$file];
                                } else {
                                    $otherFiles[$file] = $getFile[$file];
                                }
                            }
                            ?>
                            <div class="ttl bold">
                                <?= Loc::getMessage('DETAIL_COMPONENT_HIDDEN_FILES_TITLE') ?>:
                            </div>
                            <?php
                            if (count($images) > 0) {
                                ?>
                                <div class="attachment-picts row">
                                    <?php
                                    foreach ($images as $image) {
                                        $imageThumb = CFile::ResizeImageGet(
                                            $image['ID'],
                                            [
                                                'width' => 90,
                                                'height' => 90
                                            ],
                                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                            true
                                        );
                                        ?>
                                        <div class="item">
                                            <a href="/upload/<?= $image['SUBDIR'] ?>/<?= $image['FILE_NAME'] ?>"
                                               data-fancybox="gallery-attachment">
                                                <div class="in">
                                                    <div class="object-fit-container">
                                                        <img src="<?= $imageThumb['src'] ?>"
                                                             alt="" data-object-fit="cover"
                                                             data-object-position="50% 50%"/>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }

                            if (count($otherFiles) > 0) {
                                ?>
                                <ul class="list-attachment">
                                    <?
                                    foreach ($otherFiles as $file) {
                                        ?>
                                        <li>
                                            <a download target="_blank"
                                               href="/upload/<?= $file['SUBDIR'] ?>/<?= $file['FILE_NAME'] ?>">
                                                <svg class="icon icon_clip">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#clip"></use>
                                                </svg>
                                                <span><?= $file['DESCRIPTION'] ?></span>
                                            </a>
                                        </li>
                                        <?
                                    }
                                    ?>
                                </ul>
                                <?php
                            }
                        }
                        ?>
                        <div class="btn-wrap ">




                            <?php
                            if (!$stagesL && $executor) {
                                // ���� ������� ����������
                                if ($arResult['UF_SAFE']) {
                                    switch ($arResult['UF_STATUS']) {
                                        case 2:
                                            $safeCrowOrderFound = false;
                                            foreach ($arResult['SAFECROW_ORDERS'] as $safeCrowOrder) {
                                                if ($safeCrowOrder['UF_STATUS'] == 'paid') {
                                                    $safeCrowOrderFound = true;
                                                }
                                                break;
                                            }

                                            if (!$safeCrowOrdersCount) {
                                                // ������ ��� �� ������
                                                ?>
                                                <form method="post"
                                                      action="<?= $APPLICATION->GetCurPage(false) ?>">
                                                    <input type="hidden" name="pay"
                                                           value="1">
                                                    <button type="submit"
                                                            class="btn btn-green">
                                                        <?= Loc::getMessage('DETAIL_COMPONENT_SAFE_ADD_MONEY') ?>
                                                    </button>
                                                </form>
                                                <?php

                                            } else {
                                                if ($safeCrowOrderFound) {
                                                    // ������ ��������� ����������� ��� �� ����
                                                    ?>
                                                    <div class="badge badge-success">
                                                        <?= Loc::getMessage('DETAIL_COMPONENT_SAFE_ADD_MONEY_EXISTS') ?>
                                                    </div>
                                                    <br>
                                                    <br>
                                                    <a href="#popup-complain"
                                                       class="btn btn-red"
                                                       data-fancybox="">
                                                        <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLAIN') ?>
                                                    </a>
                                                    <?php
                                                }
                                            }
                                            break;
                                        case 5:
                                            ?>
                                            <div class="badge badge-success">
                                                <?= Loc::getMessage('DETAIL_COMPONENT_SAFE_EXECUTOR_END') ?>
                                            </div>
                                            <br>
                                            <br>
                                            <div class="stage-btns">
                                                <a href="#popup-feedback" data-fancybox="" class="btn btn-green">
                                                    <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLETED') ?>
                                                </a>
                                                <a href="#popup-complain" data-fancybox="" class="btn btn-red">
                                                    <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLAIN') ?>
                                                </a>
                                            </div>
                                            <?php
                                            break;
                                        case 6:
                                            ?>
                                            <div class="badge badge-danger">
                                                <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_COMPLAIN_EXISTS') ?>
                                            </div>
                                            <?php
                                            break;
                                    }

                                } else {
                                    // ����������� ��� ���������� ������

                                    switch ($arResult['UF_STATUS']) {
                                        case 2:
                                            ?>
                                            <a href="#popup-complain"
                                               class="btn btn-red"
                                               data-fancybox="">
                                                <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLAIN') ?>
                                            </a>
                                            <?php
                                            break;
                                        case 5:
                                            ?>
                                            <div class="badge badge-success">
                                                <?= Loc::getMessage('DETAIL_COMPONENT_SAFE_EXECUTOR_END') ?>
                                            </div>
                                            <br>
                                            <br>
                                            <div class="stage-btns">
                                                <a href="#popup-feedback" data-fancybox="" class="btn btn-green">
                                                    <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLETED') ?>
                                                </a>
                                                <a href="#popup-complain" data-fancybox="" class="btn btn-red">
                                                    <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_SET_COMPLAIN') ?>
                                                </a>
                                            </div>
                                            <?php
                                            break;
                                        case 6:
                                            ?>
                                            <div class="badge badge-danger">
                                                <?= Loc::getMessage('DETAIL_COMPONENT_STAGE_COMPLAIN_EXISTS') ?>
                                            </div>
                                            <?php
                                            break;
                                    }
                                }
                            }
                            ?>
                            <? if ($executor && $arResult['UF_STATUS'] !== 1 && $arResult['UF_STATUS'] !== 6): ?>
                                <div class="popup popup-feedback" id="popup-feedback">
                                    <div class="popup-head">
                                        <div class="h2">
                                            <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_TTL') ?>
                                        </div>
                                    </div>
                                    <div class="popup-body">
                                        <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="post">
                                            <input type="hidden" name="setCompleted" value="1">
                                            <input type="hidden" name="stageId">
                                            <div class="row">
                                                <div class="col-xxs-12 col-sm-4">
                                                    <div class="form-group">
                                                        <input class="radio" type="radio" name="feedbackType"
                                                               id="popup-feedbackType1" checked="checked"
                                                               value="1"/>
                                                        <label class="form-radio" for="popup-feedbackType1">
                                                            <span class="icon-wrap"></span>
                                                            <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_POSITIVE') ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-xxs-12 col-sm-4">
                                                    <div class="form-group">
                                                        <input class="radio" type="radio" name="feedbackType"
                                                               id="popup-feedbackType2" checked="checked"
                                                               value="2"/>
                                                        <label class="form-radio" for="popup-feedbackType2">
                                                            <span class="icon-wrap"></span>
                                                            <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_NEUTRAL') ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-xxs-12 col-sm-4">
                                                    <div class="form-group">
                                                        <input class="radio" type="radio" name="feedbackType"
                                                               id="popup-feedbackType3" value="0"/>
                                                        <label class="form-radio" for="popup-feedbackType3">
                                                            <span class="icon-wrap"></span>
                                                            <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_NEGATIVE') ?>
                                                        </label>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="form-group">
                                                <label for="feedback-message">
                                                    <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_MES_LBL') ?>
                                                </label>
                                                <textarea class="form-control required" rows="8" required
                                                          id="feedback-message" name="feedbackMessage"></textarea>
                                            </div>
                                            <div class="text-center">
                                                <button class="btn-submit btn btn-orange">
                                                    <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_BTN') ?>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <? endif ?>
                        </div>
                    </div>
                    */?>
                    <br>


                    <?php
                    if ($arResult['UF_STATUS'] == 6) {
                        ?>
                        <div class="white-block">
                            <div class="badge badge-danger">
                                <?php
                                echo Loc::getMessage(
                                    'DETAIL_COMPONENT_COMPLAIN_INFO_TITLE',
                                    [
                                        '#WHO#' => Loc::getMessage('DETAIL_COMPONENT_COMPLAIN_INFO_WHO_' . ((intval($arResult['COMPLAIN']['UF_USER_ID'])) == intval($arResult['UF_USER_ID']) ? 'OWNER' : 'EXECUTOR'))
                                    ]
                                );
                                ?>
                                <span title="<?= date('Y-m-d H:i', strtotime($arResult['COMPLAIN']['UF_CREATED_AT'])) ?>"
                                      class="timestamp"></span>
                            </div>
                            <p>
                                <?= $arResult['COMPLAIN']['UF_TEXT'] ?>
                            </p>
                        </div>
                        <br>
                        <?php
                    } else {
                        if (count($arResult['STAGES']) > 0) {
                            foreach ($arResult['STAGES'] as $STAGE) {
                                if ($STAGE['UF_STATUS'] == 2) {
                                    ?>
                                    <div class="white-block">
                                        <div class="badge badge-danger">
                                            <?php
                                            echo Loc::getMessage(
                                                'DETAIL_COMPONENT_COMPLAIN_INFO_TITLE',
                                                [
                                                    '#WHO#' => Loc::getMessage('DETAIL_COMPONENT_COMPLAIN_INFO_WHO_' . ((intval($arResult['COMPLAIN']['UF_USER_ID'])) == intval($arResult['UF_USER_ID']) ? 'OWNER' : 'EXECUTOR'))
                                                ]
                                            );
                                            ?>
                                            <span title="<?= date('Y-m-d H:i', strtotime($arResult['COMPLAIN']['UF_CREATED_AT'])) ?>"
                                                  class="timestamp"></span>
                                        </div>
                                        <p>
                                            <?= $arResult['COMPLAIN']['UF_TEXT'] ?>
                                        </p>
                                    </div>
                                    <br>
                                    <?php
                                    break;
                                }
                            }
                        }
                    }
                    ?>




                </div>
            </div>
        </div>
    </div>
    <div id="popup-complain"
         class="popup popup-complain">
        <div class="popup-head text-center">
            <div class="h2">
                <?= Loc::getMessage('DETAIL_COMPONENT_POPUP_COMPLAIN_TTL') ?>
            </div>
        </div>
        <div class="popup-body">
            <form method="post"
                  action="<?= $APPLICATION->GetCurPage(false) ?>">
                <input type="hidden"
                       name="setComplain"
                       value="1">
                <input type="hidden"
                       name="stageId">
                <div class="form-group">
                    <textarea class="form-control"
                              placeholder="<?= Loc::getMessage('DETAIL_COMPONENT_POPUP_COMPLAIN_') ?>"
                              name="setComplainMessage" cols="30" rows="10"></textarea>
                </div>
                <div class="text-center">
                    <button type="submit"
                            class="btn">
                        <?= Loc::getMessage('DETAIL_COMPONENT_POPUP_BTN_SEND') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div id="popup-confirmation"
         class="popup popup-confirmation">
        <div class="popup-head text-center">
            <div class="h2">
                <?= Loc::getMessage('DETAIL_COMPONENT_POPUP_CONFIRMATION_TTL') ?>
            </div>
        </div>
        <div class="popup-body text-center">

            <? if ($arResult['UF_SAFE']): ?>
                <?= Loc::getMessage('DETAIL_COMPONENT_POPUP_CONFIRMATION_DESC') ?>
            <? else: ?>
                <?= Loc::getMessage('DETAIL_COMPONENT_POPUP_CONFIRMATION_DESC_2') ?>
            <? endif ?>
            <div class="btn-wrap text-center">
                <a href="#" data-fancybox-close class="btn btn-red">
                    <?= Loc::getMessage('DETAIL_COMPONENT_POPUP_CONFIRMATION_BTN_CANCLE') ?>
                </a>
                <form style="display: inline;" method="post"
                      action="<?= $APPLICATION->GetCurPage(false) ?>">
                    <input type="hidden" name="setCompleted"
                           value="1">
                    <input type="hidden" name="stageId">
                    <button type="submit" class="btn">
                        <?= Loc::getMessage('DETAIL_COMPONENT_POPUP_CONFIRMATION_BTN') ?>
                    </button>
                </form>
            </div>

        </div>
    </div>
<?php
if (intval($arResult['EXECUTOR_ID'])) {
    $APPLICATION->IncludeComponent(
        'democontent2.pi:task.chat',
        '',
        [
            'title' => $arResult['UF_NAME'],
            'taskId' => $arParams['itemId'],
            'ownerId' => intval($arResult['UF_USER_ID']),
            'executorId' => intval($arResult['EXECUTOR_ID']),
            'iBlockId' => intval($arResult['UF_IBLOCK_ID'])
        ]
    );
}
?>
    <script>
        var ajaxPath = '<?=$this->GetFolder()?>/ajax.php',
            route = JSON.parse('<?=\Bitrix\Main\Web\Json::encode($route)?>');
    </script>
<?php
/*
if (method_exists($this, 'createFrame')) {
    $frame->end();
}
*/