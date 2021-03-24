<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.01.2019
 * Time: 16:40
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
global $USER;
use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'setFrameMode')) {
    $this->setFrameMode(true);
}

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
$quickly = 0;

if (count($arResult['UF_QUICKLY_END']) > 0 && strtotime($arResult['UF_QUICKLY_END']) > strtotime(date('Y-m-d H:i'))) {
    $quickly = 1;
}
$stagesL = count($arResult['STAGES']);

$chatEnabled = false;
if (intval(\Bitrix\Main\Config\Option::get(DSPI, 'chatEnabled'))) {
    $chatEnabled = true;
}
//debmes($arResult);

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
        <div class="row task-container " id="sticky-container">
            <div class="col-sm-4 col-md-3 col-xxs-12">
                <?php
                $APPLICATION->IncludeComponent(
                    'democontent2.pi:user.menu',
                    ''
                );
                ?>
            </div>
            <div class="col-md-3 col-lg-4 col-xxs-12 pull-right" style="display: none">
                <div class="user-card-block white-block sticky-block" data-container="#sticky-container">
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
                                    <?if($USER->IsAuthorized() && is_string($arResult['MODERATION_CURRENT_USER'])  ):?>
                                        <?if(!empty($arResult['CURRENT_USER']['ID'])):?>
                                            <div class="company-phone-detail">
                                                <div class="btn btn-green see-phone-manager" >
                                                    Показать телефон
                                                </div>
                                                <div class="phone-non" style="display:none;">
                                                    <?if(!empty($arResult['CURRENT_USER']['PERSONAL_PHONE'])):?>
                                                    Телефон:<p><?=$arResult['CURRENT_USER']['PERSONAL_PHONE']?></p>
                                                </div>
                                                <?endif;?>
                                                <?if(!empty($arResult['CURRENT_USER']['NAME'])):?>
                                                    Менеджер:<p><?=$arResult['CURRENT_USER']['NAME']?>&nbsp;<?=$arResult['CURRENT_USER']['LAST_NAME']?></p>
                                                <?endif;?>
                                            </div>
                                        <?endif;?>
                                    <?else:?>
                                        <div class="company-phone-detail">
                                            <div class="btn btn-green  without-auth" style="margin: 10px 0px">
                                                Показать телефон
                                            </div>
                                        </div>
                                    <?endif;?>
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
                            <?if($arResult['MODERATION'] === "1" || $arResult['MODERATION'] === "0"):?>
                                <form action="<?= $APPLICATION->GetCurPage(false) ?>"
                                      method="post">
                                    <input type="hidden" name="moderation" value="approve">
                                    <button type="submit" class="moderation-approve">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_APPROVE') ?>
                                    </button>
                                </form>
                            <?endif;?>


                            <?if($arResult['MODERATION'] === "1" || $arResult['MODERATION'] === "0"):?>
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
                            <?endif;?>

                        </div>
                        <?php
                    }
                    ?>
                    <?
                    //если модератор / то свечу ему кнопки  - снять с публикации и редактировать
                    ?>

                </div>
            </div>
            <div class="col-sm-12 col-md-9 col-lg-8 col-xxs-12">

                <div class="white-block">
                    <div class="complain">
                        <div class="complain-item" style="display: none" data-id="<?= $arResult['UF_ID'] ?>" data-path="<?=$this->GetFolder().'/ajaxFavorites.php'?>"
                             data-category="<?= $arResult['UF_IBLOCK_ID'] ?>">
                            <span class="span-star">
<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
     viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
<path style="fill:#FFDC64;" d="M492.757,190.241l-160.969-14.929L267.847,26.833c-4.477-10.396-19.218-10.396-23.694,0
	l-63.941,148.478L19.243,190.24c-11.271,1.045-15.826,15.064-7.322,22.534l121.452,106.694L97.828,477.174
	c-2.489,11.042,9.436,19.707,19.169,13.927l139.001-82.537l139.002,82.537c9.732,5.78,21.659-2.885,19.17-13.927l-35.544-157.705
	l121.452-106.694C508.583,205.305,504.029,191.286,492.757,190.241z"/>
<path style="fill:#FFC850;" d="M267.847,26.833c-4.477-10.396-19.218-10.396-23.694,0l-63.941,148.478L19.243,190.24
	c-11.271,1.045-15.826,15.064-7.322,22.534l121.452,106.694L97.828,477.174c-2.489,11.042,9.436,19.707,19.169,13.927l31.024-18.422
	c4.294-176.754,86.42-301.225,151.441-372.431L267.847,26.833z"/>
<path d="M510.967,196.781c-2.56-7.875-9.271-13.243-17.518-14.008l-156.535-14.518l-31.029-72.054
	c-1.639-3.804-6.049-5.562-9.854-3.922c-3.804,1.638-5.56,6.05-3.922,9.853l32.791,76.144c1.086,2.521,3.463,4.248,6.196,4.501
	l160.969,14.929c3.194,0.296,4.307,2.692,4.638,3.708c0.33,1.016,0.838,3.608-1.572,5.725L373.678,313.835
	c-2.063,1.812-2.97,4.605-2.366,7.283l35.545,157.703c0.705,3.13-1.229,4.929-2.095,5.557c-0.864,0.628-3.17,1.915-5.931,0.274
	l-139.003-82.537c-2.359-1.4-5.299-1.4-7.657,0l-139.003,82.537c-2.76,1.642-5.066,0.354-5.931-0.274
	c-0.865-0.628-2.8-2.427-2.095-5.556l18.348-81.406c0.911-4.041-1.627-8.055-5.667-8.965c-4.047-0.91-8.054,1.627-8.965,5.667
	l-18.348,81.407c-1.82,8.078,1.211,16.12,7.91,20.988c6.699,4.866,15.285,5.265,22.403,1.037l135.174-80.264l135.174,80.264
	c3.28,1.947,6.87,2.913,10.443,2.913c4.185,0,8.347-1.325,11.96-3.95c6.7-4.868,9.73-12.909,7.91-20.989l-34.565-153.36
	L505.029,218.41C511.251,212.944,513.525,204.657,510.967,196.781z"/>
<path d="M116.085,362.057c-0.911,4.041,1.627,8.055,5.667,8.965c0.556,0.125,1.11,0.186,1.656,0.186c3.43,0,6.524-2.367,7.309-5.853
	l9.97-44.237c0.604-2.679-0.304-5.473-2.366-7.283L16.87,207.141c-2.41-2.117-1.902-4.709-1.571-5.725
	c0.33-1.016,1.442-3.412,4.637-3.708l160.968-14.929c2.733-0.253,5.11-1.98,6.196-4.501L251.04,29.801
	c1.269-2.946,3.891-3.265,4.959-3.265c1.069,0,3.691,0.318,4.96,3.264l17.367,40.327c1.64,3.804,6.05,5.561,9.854,3.922
	c3.804-1.638,5.56-6.05,3.922-9.853l-17.367-40.328c-3.276-7.605-10.454-12.33-18.736-12.33c-8.28,0-15.459,4.725-18.735,12.331
	l-62.18,144.388L18.551,182.773c-8.245,0.765-14.958,6.132-17.518,14.008c-2.559,7.875-0.284,16.163,5.938,21.629l118.106,103.755
	L116.085,362.057z"/>                            </span>
                        </div>
                    </div>

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
                        <?/*<div class="itm">
                            <?
                            switch ($arResult['UF_STATUS']) {
                                case 1:
                                case 4:
                                    ?>
                                    <div class="status">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_STATUS_1') ?>
                                    </div>
                                    <?
                                    break;
                                case 2:
                                    ?>
                                    <div class="status hold-true">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_STATUS_2') ?>
                                    </div>
                                    <?
                                    break;
                                case 3:
                                    ?>
                                    <div class="status completed">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_STATUS_3') ?>
                                    </div>
                                    <?
                                    break;
                                case 6:
                                    ?>
                                    <div class="status completed">
                                        <?= Loc::getMessage('DETAIL_COMPONENT_STATUS_6') ?>
                                    </div>
                                    <?
                                    break;
                            }
                            ?>
                        </div>
*/?>
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

                        <?/*  <div class="itm">
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
                        <?/*
                        <div class="itm">
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
                        </div>
*/?>
                    </div>
                    <? if (!empty($arResult['UF_BEGIN_WITH'])): ?>
                        <p>
                        <span class="ttl bold">
                            <?= Loc::getMessage('DETAIL_COMPONENT_DATE_START') ?>:
                        </span>
                            <?= date('d.m.Y H:i', strtotime($arResult['UF_BEGIN_WITH'])) ?>
                        </p>
                    <? endif ?>

                    <? if (!empty($arResult['UF_RUN_UP'])): ?>
                        <p>
                            <span class="ttl bold"><?= Loc::getMessage('DETAIL_COMPONENT_DATE_END') ?>:</span>
                            <?= date('d.m.Y H:i', strtotime($arResult['UF_RUN_UP'])) ?>
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

                    <?php
                    if (count($route)) {
                        ?>
                        <div id="route-area">
                            <div id="map-area"></div>
                        </div>
                        <?php
                    }
                    ?>

                    <? if (strlen($arResult['UF_PROPERTIES']) > 0): ?>
                        <div class="characteristics" style="display: none">
                            <table>
                                <?
                                foreach ($characteristics as $key => $val) {
                                    $name = '';

                                    ?>
                                    <tr class="leftParams">
                                        <td><span><?= $val['name'] ?></span></td>
                                        <td><?= $val['value'] ?></td>
                                    </tr>
                                    <?
                                }
                                ?>
                            </table>
                        </div>
                    <? endif ?>

                    <?php
                    $files = $arResult['PROPS_ELEMENT']['PROPERTY_files'];

                    if (!empty($files[0])) {
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
                        <div class="ttl bold">
                            <?= Loc::getMessage('DETAIL_COMPONENT_FILES_TITLE') ?>:
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
                    <?php
                    switch ($arResult['UF_STATUS']) {
                        case 1:
                        case 4:
                            if ($USER->IsAuthorized() && isset($arResult['CURRENT_USER']['UF_DSPI_EXECUTOR'])
                                && intval($arResult['CURRENT_USER']['UF_DSPI_EXECUTOR']) > 0) {
                                if (!count($arResult['MY_OFFER']) && !$arResult['BLOCKED']) {
                                    if ($arResult['UF_SAFE']) {
                                        if (!$arResult['USER_CARD']) {
                                            ?>
                                            <div class="btn-wrap">
                                                <a class="btn btn-green" data-fancybox=""
                                                   href="#popup-attach-card">
                                                    <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL') ?>
                                                </a>
                                            </div>
                                            <?php
                                        } else {
                                            if (!$arResult['RESPONSE_COST']) {
                                                ?>
                                                <div class="btn-wrap" style="display: none">
                                                    <a class="btn btn-green" data-fancybox=""
                                                       href="#popup-proposal">
                                                        <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL') ?>
                                                    </a>
                                                </div>
                                                <?php
                                            } else {
                                                if ($arResult['BALANCE'] >= $arResult['RESPONSE_COST']) {
                                                    ?>
                                                    <div class="btn-wrap" style="display: none">
                                                        <a class="btn btn-green" data-fancybox=""
                                                           href="#popup-proposal">
                                                            <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL') ?>
                                                            -
                                                            <?= \Democontent2\Pi\Utils::price($arResult['RESPONSE_COST']) ?>
                                                            <?= $currencyName ?>
                                                        </a>
                                                    </div>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <div class="btn-wrap">
                                                        <a class="btn btn-green" data-fancybox=""
                                                           href="#top-up-balance">
                                                            <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL') ?>
                                                            -
                                                            <?= \Democontent2\Pi\Utils::price($arResult['RESPONSE_COST']) ?>
                                                            <?= $currencyName ?>
                                                        </a>
                                                    </div>
                                                    <div class="popup popup-top-up-balance" id="top-up-balance">
                                                        <div class="popup-head">
                                                            <div class="h2">
                                                                <?= Loc::getMessage('DETAIL_COMPONENT_POP_TOP_UP_BALANCE') ?>
                                                            </div>
                                                            <div class="alert alert-warning">
                                                                <?= Loc::getMessage('DETAIL_COMPONENT_POP_TOP_UP_BALANCE_INFO') ?>
                                                            </div>
                                                            <form action="<?= SITE_DIR ?>user/balance/" method="post">
                                                                <div class="row">
                                                                    <div class="col-sm-8 col-xxs-12">
                                                                        <div class="form-group">
                                                                            <input id="deposit-amount" required
                                                                                   name="amount" type="text"
                                                                                   class="form-control required"
                                                                                   placeholder="<?= Loc::getMessage('DETAIL_COMPONENT_BALANCE_INPUT_PL') ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4 col-xxs-12">
                                                                        <div class="form-group">
                                                                            <button class="btn-submit btn btn-green">
                                                                                <?= Loc::getMessage('DETAIL_COMPONENT_BALANCE_BTN') ?>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                        }
                                    } else {
                                        if (!$arResult['RESPONSE_COST']) {
                                            ?>
                                            <div class="btn-wrap" style="display: none">
                                                <a class="btn btn-green" data-fancybox=""
                                                   href="#popup-proposal">
                                                    <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL') ?>
                                                </a>
                                            </div>
                                            <?php
                                        } else {
                                            if ($arResult['BALANCE'] >= $arResult['RESPONSE_COST']) {
                                                ?>
                                                <div class="btn-wrap" style="display: none">
                                                    <a class="btn btn-green" data-fancybox=""
                                                       href="#popup-proposal">
                                                        <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL') ?>
                                                        -
                                                        <?= \Democontent2\Pi\Utils::price($arResult['RESPONSE_COST']) ?>
                                                        <?= $currencyName ?>
                                                    </a>
                                                </div>
                                                <?php
                                            } else {
                                                ?>
                                                <div class="btn-wrap">
                                                    <a class="btn btn-green" data-fancybox=""
                                                       href="#top-up-balance">
                                                        <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL') ?>
                                                        -
                                                        <?= \Democontent2\Pi\Utils::price($arResult['RESPONSE_COST']) ?>
                                                        <?= $currencyName ?>
                                                    </a>
                                                </div>
                                                <div class="popup popup-top-up-balance" id="top-up-balance">
                                                    <div class="popup-head">
                                                        <div class="h2">
                                                            <?= Loc::getMessage('DETAIL_COMPONENT_POP_TOP_UP_BALANCE') ?>
                                                        </div>
                                                        <div class="alert alert-warning">
                                                            <?= Loc::getMessage('DETAIL_COMPONENT_POP_TOP_UP_BALANCE_INFO') ?>
                                                        </div>
                                                        <form action="<?= SITE_DIR ?>user/balance/" method="post">
                                                            <div class="row">
                                                                <div class="col-sm-8 col-xxs-12">
                                                                    <div class="form-group">
                                                                        <input id="deposit-amount" required
                                                                               name="amount" type="text"
                                                                               class="form-control required"
                                                                               placeholder="<?= Loc::getMessage('DETAIL_COMPONENT_BALANCE_INPUT_PL') ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-4 col-xxs-12">
                                                                    <div class="form-group">
                                                                        <button class="btn-submit btn btn-green">
                                                                            <?= Loc::getMessage('DETAIL_COMPONENT_BALANCE_BTN') ?>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        }
                                    }

                                }
                            } else {
                                if (!$USER->IsAuthorized()) {
                                    ?>
                                    <div class="btn-wrap hidden-sm hidden-xxs hidden-xs">
                                        <a class="btn btn-green" data-fancybox=""
                                           href="#popup-registration">
                                            <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL') ?>
                                        </a>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="alert alert-success" style="display: none">
                                        <?php
                                        echo Loc::getMessage(
                                            'DETAIL_COMPONENT_MAKE_EXECUTOR',
                                            [
                                                '#SITE_DIR#' => SITE_DIR
                                            ]
                                        );
                                        ?>
                                    </div>
                                    <?php
                                }
                            }
                            break;
                    }

                    ?>
                    <? if (count($arResult['MY_OFFER']) > 0 && !$arResult['BLOCKED'] && $arResult['UF_STATUS'] == 2): ?>
                        <br>
                        <a class="btn btn-green btn-completed" href="#popup-feedback" data-fancybox="">
                            <?= Loc::getMessage('DETAIL_COMPONENT_BTN_COMPLETED') ?>
                        </a>
                        <div class="popup popup-feedback" id="popup-feedback">
                            <div class="popup-head">
                                <div class="h2">
                                    <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_TTL') ?>
                                </div>
                            </div>
                            <div class="popup-body">
                                <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="post">
                                    <div class="row">
                                        <div class="col-xxs-12 col-sm-4">
                                            <div class="form-group">
                                                <input class="radio" type="radio" name="feedbackType"
                                                       id="popup-feedbackType1" checked="checked" value="1"/>
                                                <label class="form-radio" for="popup-feedbackType1">
                                                    <span class="icon-wrap"></span>
                                                    <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_POSITIVE') ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-xxs-12 col-sm-4">
                                            <div class="form-group">
                                                <input class="radio" type="radio" name="feedbackType"
                                                       id="popup-feedbackType2" checked="checked" value="2"/>
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
                                        <div class="form-group">
                                            <label for="feedback-message">
                                                <?= Loc::getMessage('DETAIL_COMPONENT_POP_FEEDBACK_MES_LBL') ?>
                                            </label>
                                            <textarea class="form-control required" rows="8" required
                                                      id="feedback-message" name="feedbackMessage"></textarea>
                                        </div>
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

                    <?if( empty($arResult['CURRENT_USER']['UF_MODERATION_ACCESS'])):?>
                        <div class="row">
                            <div class="col-sm-6 col-md-6 col-xxs-12">
                                <?if($arResult['UF_MODERATION'] == 5){?>
                                    <!--                                снять с публикации если статус 0 -->

                                <?}else{?>
                                    <!--                                снять с публикации если статус 0 -->

                                <?}?>


<!--                                опубликовать  - сменить 5 на 0  / если статус 5-->

                            </div>
                            <div class="col-sm-6 col-md-6 col-xxs-12" style="display: none;">
                                <?
                                $page = $APPLICATION->GetCurPage(); // результат - /ru/index.php
                                ?>
                                    <a href="<?=$page?>edit/">
                                        <button type="submit" class="edit-task-my">
                                            Редактировать
                                        </button>
                                    </a>
                            </div>

                        </div>
                    <?endif;?>

                </div>
                <?php
                try {
                    if (\Bitrix\Main\IO\File::isFileExists(
                        \Bitrix\Main\IO\Path::normalize(
                            \Bitrix\Main\Application::getDocumentRoot() . SITE_TEMPLATE_PATH . '/inc/adv/list.php'
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
                ?>
                <br>
                <? if (count($arResult['MY_OFFER'])): ?>
                    <h3>
                        <?= Loc::getMessage('DETAIL_COMPONENT_YOUR_OFFER') ?>:
                    </h3>
                    <div class="white-block">
                        <div class="date-box">
                            <svg class="icon icon_time">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#time"></use>
                            </svg>
                            <span title="<?= date('Y-m-d H:i', strtotime($arResult['MY_OFFER']['UF_CREATED_AT'])) ?>"
                                  class="timestamp"></span>
                        </div>
                        <div class="desc">
                            <?= TxtToHTML($arResult['MY_OFFER']['UF_TEXT'], false) ?>
                        </div>
                        <?
                        $files = unserialize($arResult['MY_OFFER']['UF_FILES']);
                        if (count($files) > 0) {
                            ?>
                            <div class="ttl bold">
                                <?= Loc::getMessage('DETAIL_COMPONENT_FILES_TITLE') ?>:
                            </div>
                            <ul class="list-attachment">
                                <?
                                foreach ($files as $key => $file) {
                                    $getFile = CFile::GetFromCache($file);
                                    ?>
                                    <li>
                                        <a download target="_blank"
                                           href="/upload/<?= $getFile[$file]['SUBDIR'] ?>/<?= $getFile[$file]['FILE_NAME'] ?>">
                                            <svg class="icon icon_clip">
                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#clip"></use>
                                            </svg>
                                            <span><?= $getFile[$file]['DESCRIPTION'] ?></span>
                                        </a>
                                    </li>
                                    <?
                                }
                                ?>
                            </ul>
                            <?
                        }
                        ?>
                    </div>
                <? endif ?>
            </div>
        </div>
    </div>
</div>
<?php
if ($USER->IsAuthorized() && !count($arResult['MY_OFFER']) && !$arResult['BLOCKED']
    && isset($arResult['CURRENT_USER']['UF_DSPI_EXECUTOR']) && intval($arResult['CURRENT_USER']['UF_DSPI_EXECUTOR'])) {
    ?>
    <div class="popup popup-proposal" id="popup-proposal">
        <div class="popup-head">
            <div class="h2">
                <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL_TTL') ?>
            </div>
        </div>
        <div class="popup-body">
            <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="popup-proposal-message">
                        <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL_MES_LBL') ?>:
                    </label>
                    <textarea class="form-control required" name="text" required rows="8"
                              id="popup-proposal-message"></textarea>
                </div>
                <div class="form-group">
                    <div id="files-list" class="files"></div>
                    <div id="btn-file" class="btn btn-sm btn-file">
                        <span><?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL_BTN_FILE') ?></span>
                        <input type="file" name="__files[]">
                    </div>
                </div>

                <?php
                if ($arResult['ALLOW_CHECKLISTS'] && count($arResult['RESPONSE_CHECKLIST'])) {
                    foreach ($arResult['RESPONSE_CHECKLIST'] as $item) {
                        ?>
                        <div class="form-group">
                            <input id="checklist<?= $item['ID'] ?>" class="checkbox checkbox-security" type="checkbox"
                                   name="response-values[]" value="<?= $item['ID'] ?>">
                            <label class="form-checkbox" for="checklist<?= $item['ID'] ?>">
                                <span class="icon-wrap">
                                        <svg class="icon icon_checkmark">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                        </svg>
                                    </span>
                                <?= $item['UF_NAME'] ?>
                            </label>
                        </div>
                        <?php
                    }
                }
                ?>

                <div class="text-center">
                    <button class="btn-submit btn btn-green">
                        <?= Loc::getMessage('DETAIL_COMPONENT_BTN_PROPOSAL_BTN') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php
}
?>
<? if ($USER->IsAuthorized() && !$arResult['USER_CARD'] && $arResult['UF_SAFE']): ?>
    <div id="popup-attach-card" class="popup popup-attach-card text-center">
        <div class="popup-head">
            <div class="h2">
                <?= Loc::getMessage('DETAIL_COMPONENT_POPUP_ATTACH_CARD_TTL') ?>
            </div>
        </div>
        <div class="popup-body">
            <?= Loc::getMessage('DETAIL_COMPONENT_POPUP_ATTACH_CARD_DESC') ?>
            <br>
            <br>
            <a href="<?= SITE_DIR ?>user/settings/" class="btn btn-orange">
                <?= Loc::getMessage('DETAIL_COMPONENT_POPUP_ATTACH_CARD_BTN') ?>
            </a>
        </div>
    </div>
<? endif ?>

<script>
    BX.message({
        usersPath: '<?=$this->GetFolder()?>/ajaxFavorites.php',
        addToFavourites: '<?=Loc::getMessage('ADD_TO_FAVOURITES')?>',
        inFavourites: '<?=Loc::getMessage('IN_FAVOURITES')?>',
        maxFiles: parseInt('<?=intval(\Bitrix\Main\Config\Option::get(DSPI, 'max_files'))?>'),
        templatePath: '<?=SITE_TEMPLATE_PATH?>',
        route: JSON.parse('<?=\Bitrix\Main\Web\Json::encode($route)?>'),
        detailTemplateAjaxPath: '<?=$this->GetFolder()?>/ajax.php',
        isAuthorized: <?=$USER->IsAuthorized() ? 1 : 0?>,
        pleaseAuth: '<?=Loc::getMessage('DETAIL_COMPONENT_PLEASE_AUTH')?>',
        complainSuccess: '<?=Loc::getMessage('DETAIL_COMPONENT_COMPLAIN_SUCCESS')?>',
    });
</script>