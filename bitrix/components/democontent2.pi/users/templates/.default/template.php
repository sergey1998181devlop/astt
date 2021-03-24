<?php
/**
 * User: Nazarkin Sergey
 * Email: nazarkin2017@mail.ru
 * Date: 07.10.2020
 * Time: 13:07
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}
$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$cityId = $arResult['CITY_ID'];
$verification = 0;
if ($request->get('city')) {
    $cityId = intval($request->get('city'));
}
if ($request->get('verification')) {
    if (intval($request->get('verification')) > 0) {
        $verification = 1;
    }
}

$chatEnabled = false;
if (intval(\Bitrix\Main\Config\Option::get(DSPI, 'chatEnabled'))) {
    $chatEnabled = true;
}

$authorized = $USER->IsAuthorized();
?>
<div class="wrapper">
    <div class="row">
        <div class="new-blockDez-list">
            <div class="col-sm-4 col-md-3 col-xxs-12">

                <div class="list-comp">
                    <div class="list-comp-podlog">
                        <p>СПИСОК ИСПОЛНИТЕЛЕЙ</p>
                    </div>
                </div>
                <div class="location-users-filter">
                    <p>Ваш город:</p>
                    <div class="block-location-users-f">
                        <img src="<?=SITE_TEMPLATE_PATH?>/images/users/city.png">
                    </div>
                    <?php
                    $APPLICATION->IncludeComponent(
                        'democontent2.pi:locationUsersFilter',
                        '',
                        []
                    );
                    ?>
                </div>
                <div class="loation-companies-filter">

                </div>

            </div>
            <div class="col-sm-4 col-md-6 col-xxs-12">
                <div class="new-blockDez-list-desc">
                    Описание раздела демо Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    In quis augue sollicitudin, condimentum lectus nec, lacinia nibh.
                </div>

                <div class="filter-sorty-to-block">

                    <form class="filter-sorty-to-items " method="get">
                        <div class="row">

                            <div class="input-group md-form form-sm form-2 pl-0 col-md-12">
                                <input class="form-control my-0 py-1 amber-border" type="text" placeholder="Выберите тип техники..."  aria-label="Search">
                                <div class="win-to-amber">

                                </div>
                                <div class="input-group-append input-group-append-modal">
                                            <span class="input-group-text amber lighten-3 lighten-3-closed" id="basic-text1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-compact-up" viewBox="0 0 16 16">
                                                      <path fill-rule="evenodd" d="M7.776 5.553a.5.5 0 0 1 .448 0l6 3a.5.5 0 1 1-.448.894L8 6.56 2.224 9.447a.5.5 0 1 1-.448-.894l6-3z"/>
                                                    </svg>


                                            </span>

                                    <span class="input-group-text amber lighten-3 lighten-3-opened" id="basic-text2" style="display: none;">

                                                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-compact-down" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd" d="M1.553 6.776a.5.5 0 0 1 .67-.223L8 9.44l5.776-2.888a.5.5 0 1 1 .448.894l-6 3a.5.5 0 0 1-.448 0l-6-3a.5.5 0 0 1-.223-.67z"/>
                                                    </svg>

                                            </span>
                                </div>
                                </input>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-12 col-sm-12 col-xxs-12">

                                <div class="sorty-panel sorty-panel-alltasks">
                                    <ul>
                                        <li targetCode="new" class="ativeSrtAlltasks" >
                                            <?= Loc::getMessage('LIST_SORT_DATE_UP') ?>

                                        </li>
                                        <li targetCode="old" >
                                            <?= Loc::getMessage('LIST_SORT_DATE_DOWN') ?>
                                        </li>
                                        <?/*   <li targetCode="priseDown" >
                                                <?= Loc::getMessage('LIST_SORT_PRICE_DOWN') ?>
                                            </li>
                                            <li targetCode="priseUp" >
                                                <?= Loc::getMessage('LIST_SORT_PRICE_UP') ?>
                                            </li>
                                            */?>



                                    </ul>
                                </div>



                            </div>
                        </div>



                        <div class="row row-modal " style="display: none;">
                            <div class="container-modal  container-fluid">

                                <div class="modal-filter">




                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12 col-xxs-12">
                                                <div class="cont-check-reset">

                                                    <div class="check-all">
                                                        Выбрать все
                                                    </div>
                                                    <div class="reset-all active">
                                                        Сбросить все
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="block-sections">
                                                <?//Верхние меню  - подключить готовый компонент catalog.menu?>

                                                <?$APPLICATION->IncludeComponent("democontent2.pi:catalog.menuUsers", "");?>


                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 col-xxs-12">
                                                    <div class="block-button-set-options">
                                                        <div class="btn ">Подтвердить выбор</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>


                                </div>

                            </div>
                        </div>




                    </form>
                </div>

            </div>
            <div class="col-sm-4 col-md-3 col-xxs-12">
                <div class="right-title-img">
                    <img src="<?=SITE_TEMPLATE_PATH?>/images/users/smart-men-users.png">
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="page-content block-users-bg">
        <div class="wrapper">

        <?/*
            <div class="page-title">
                <h1>
                    <?= Loc::getMessage('USERS_COMPONENT_TITLE') ?>
                    <?= $arResult['H1_POSTFIX'] ?>
                </h1>
            </div>
        */?>
            <div class="row">
                <div class="col-sm-12 col-md-3 col-xxs-12">

                    <nav class="category-nav white-block" id="category-nav">
                        <div class="row">
                            <div class="filterleft-r">
                                <div class="col-md-6 col-sm-12">
                                    <div class="filterleft-r-ico">
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/images/users/filterleft-r-ico.png">
                                    </div>
                                    <p>Фильтр</p>
                                </div>
                                <div class="col-md-6 col-sm-2">
                                    <div class="filterright-r">
                                        <p>сбросить все</p>
                                        <div class="close-all-check">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-10">
                                    <div class="title-filterLeft">
                                        <p>Рейтинг</p>
                                        <input type="hidden" value="emptyRating" name="rating">
                                        <input type="hidden" value="emptyCity" name="nameCity">
                                        <div class="list-stars-preload" style="display:none">
                                            <svg class="icon icon_star-full">
                                                <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#star-full"></use>
                                            </svg>
                                            <svg class="icon icon_star-empty">
                                                <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#star-empty"></use>
                                            </svg>
                                        </div>
                                        <div class="list-stars">
                                            <div class="assessment-box assessment-box-users">
                                                <div class="star-elem empty-star-sv sv-star-ev">
                                                    <svg class="icon icon_star-empty">
                                                        <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#star-empty"></use>
                                                    </svg>
                                                </div>
                                                <div class="empty-star-sv sv-star-ev">
                                                    <svg class="icon icon_star-empty">
                                                        <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#star-empty"></use>
                                                    </svg>
                                                </div>
                                                <div class="star-elem empty-star-sv sv-star-ev">
                                                    <svg class="icon icon_star-empty">
                                                        <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#star-empty"></use>
                                                    </svg>
                                                </div>
                                                <div class="star-elem empty-star-sv sv-star-ev">
                                                    <svg class="icon icon_star-empty">
                                                        <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#star-empty"></use>
                                                    </svg>
                                                </div>
                                                <div class="star-elem empty-star-sv sv-star-ev">
                                                    <svg class="icon icon_star-empty">
                                                        <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#star-empty"></use>
                                                    </svg>
                                                </div>
                                                <div class="assessment-in" style="width:0%">
                                                    <svg class="icon icon_star-full">
                                                        <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#star-full"></use>
                                                    </svg>
                                                    <svg class="icon icon_star-full">
                                                        <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#star-full"></use>
                                                    </svg>
                                                    <svg class="icon icon_star-full">
                                                        <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#star-full"></use>
                                                    </svg>
                                                    <svg class="icon icon_star-full">
                                                        <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#star-full"></use>
                                                    </svg>
                                                    <svg class="icon icon_star-full">
                                                        <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#star-full"></use>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>


                                    <div class="col-sm-12 col-xxs-12">
                                        <div class="form-group block-all-stars">
                                            <input class="checkbox contract-inp" type="checkbox" checked disabled name="setallstars"
                                                   value="0" id="create-inpD3"/>
                                            <label class="form-checkbox" for="create-inpD3">
                                            <span class="icon-wrap">
                                                <svg class="icon icon_checkmark">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                                </svg>
                                            </span>
                                                Показать все
                                            </label>
                                        </div>
                                    </div>
                                <div class="col-md-12" style="display: none">
                                    <div class="block-btn-users-leftF">
                                        <p>ПРИМЕНИТЬ</p>
                                    </div>
                                </div>



                            </div>
                        </div>
                    </nav>

                    <div class="vertical-banner-users  right-block-banner">

                    </div>
                </div>
                <div class="col-sm-12 col-md-9 col-xxs-12">
                    <nav class="category-nav " id="category-nav">
                        <div class="sorty-panel sorty-executors">
                            <ul>
                                <li class="active" data-target-filter="po-date-height">
                                    <a class="user-sortleft">
                                        <span class="icon-wrap">
                                            <svg class="icon icon_arrow-right">
                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#arrow-right"></use>
                                            </svg>
                                        </span>
                                        <span>
                                            <?= Loc::getMessage('USERS_COMPONENT_FILTER_SORT_DATE_REG') ?>
                                        </span>
                                    </a>
                                </li>
                                <li data-target-filter="po-rate-down">
                                    <a class="user-sortleft">
                                        <span class="icon-wrap">
                                            <svg class="icon icon_arrow-right">
                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#arrow-right"></use>
                                            </svg>
                                        </span>
                                        <span>
                                            <?= Loc::getMessage('USERS_COMPONENT_FILTER_SORT_RATING') ?>
                                        </span>
                                    </a>
                                </li>
                                <span class="sorty-limit dop-list-users">
                                    <span class="sorty-limit-p">Показывать по:</span>
                                    <ul class="sorty-limit-ul sorty-limit-ul-dop">
                                        <div class="list-li active-li-dop">5</div>
                                        <div class="list-li ">15</div>
                                        <div class="list-li ">25</div>
                                        <div class="list-li ">50</div>
                                    </ul>
                                </span>

                            </ul>

                        </div>



                    </nav>

<!--                    <div class="sorty-limit">-->
<!--                        <p>Показывать по:</p>-->
<!--                        <ul>-->
<!--                            <li>5</li>-->
<!--                            <li>15</li>-->
<!--                            <li>25</li>-->
<!--                            <li>50</li>-->
<!--                        </ul>-->
<!--                    </div>-->
                    <div class="white-block">


               <?/*       <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="get" class="filter-sorty">
                            <div class="row">
                                <div class="col-sm-4 col-xxs-12">
                                    <div class="form-group">
                                        <select name="city" class="js-select" style="width: 100%">
                                            <option value="0"><?= Loc::getMessage('USERS_COMPONENT_EMPTY_CITY') ?></option>
                                            <?php
                                            foreach ($arResult['CITIES'] as $city) {
                                                $selected = '';
                                                if (intval($city['id']) == $cityId) {
                                                    $selected = ' selected';
                                                }
                                                ?>
                                                <option value="<?= $city['id'] ?>"<?= $selected ?>><?= $city['name'] ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-xxs-12">
                                    <div class="form-group">
                                        <select name="verification" class="js-select" style="width: 100%">
                                            <option value="0"<?= (!$verification) ? ' selected' : '' ?>>
                                                <?= Loc::getMessage('USERS_COMPONENT_FILTER_VERIFICATION_OFF') ?>
                                            </option>
                                            <option value="1"<?= ($verification) ? ' selected' : '' ?>>
                                                <?= Loc::getMessage('USERS_COMPONENT_FILTER_VERIFICATION_ON') ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-xxs-12">
                                    <div class="form-group">
                                        <button class="btn btn-fluid btn-blue" type="submit">
                                            <?= Loc::getMessage('USERS_COMPONENT_FILTER_BTN') ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        */?>

                        <? if (count($arResult['ITEMS'])): ?>

                            <div class="masters-list">
                                <?php
                                $i = 0;
                                $last = $last = count($arResult['ITEMS'])-1;

                                foreach ($arResult['ITEMS'] as $item) {
                                    $i++;
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
                                    <div id="executor-<?= $item['ID'] ?>" class="profile-preview">
                                        <?php
                                        if ($authorized) {
                                            ?>
                                            <div class="remove-item<?= ((in_array($item['ID'], $arResult['FAVOURITES'])) ? ' active' : '') ?>"
                                                 data-id="<?= $item['ID'] ?>">
                                                <?php
                                                if (in_array($item['ID'], $arResult['FAVOURITES'])) {
                                                    echo Loc::getMessage('IN_FAVOURITES');
                                                } else {
                                                    echo Loc::getMessage('ADD_TO_FAVOURITES');
                                                }
                                                ?>
                                            </div>
                                            <a href="<?= SITE_DIR ?>user/<?= $item['ID'] ?>/" class="more-info-company">
                                                <p>Подробнее о компании</p>
                                            </a>
                                            <?php
                                        }else{
                                        ?>
                                            <a href="<?= SITE_DIR ?>user/<?= $item['ID'] ?>/" class="more-info-company">
                                                <p>Подробнее о компании</p>
                                            </a>
                                        <?}?>

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

                                                    <?
                                                    $test = $item['USER']['PERSONAL_PHOTO'];
                                                    ?>

                                                    <?
                                                    $rsUser = CUser::GetByID($item['ID']);
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
                                            <div class="tbc desc">

                                                <?if($USER->IsAuthorized() && is_string($arResult['MODERATION_CURRENT_USER'])):?>

                                                    <?if(!empty($item['COMPANY'][0]['ID'])):?>


                                                        <div class="name medium">
                                                            <?=$item['COMPANY'][0]['UF_COMPANY_NAME_MIN']?>
                                                        </div>
                                                    <?if(!empty($item['COMPANY'][0]['UF_DESCRIPTION'])):?>
                                                        <div class="desc-company-newDez">
                                                            <?=$item['COMPANY'][0]['UF_DESCRIPTION']?>
                                                        </div>
                                                    <?endif;?>
                                                    <?else:?>
                                                        <div class="name medium">
                                                            <?= $item['NAME']?>
                                                            <?= $item['LAST_NAME'] ?>
                                                        </div>
                                                    <?endif;?>
                                                <?else:?>
                                                    <div class="name medium rebue_notAutorize">
                                                        Название компании
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
                                                    <div class="date-box date-box-users">
                                                        <svg class="icon icon_time">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#time"></use>
                                                        </svg>
                                                        <?
                                                        $get_time_ago = diff_time_string( date('Y-m-d H:i', strtotime($item['DATE_REGISTER'])) );

                                                        ?>
                                                        <span title="<?= date('Y-m-d H:i', strtotime($item['DATE_REGISTER'])) ?>"
                                                            class="date_user_ajax"><?=$get_time_ago?> назад</span>
                                                    </div>
                                                    <div class="feedback feedback_users">
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
<!--                                        <a class="lnk-abs" href="--><?//= SITE_DIR ?><!--user/--><?//= $item['ID'] ?><!--/"></a>-->
                                    </div>
                                    <?php

                                }
                                ?>
                            </div>

                            <?
                            $APPLICATION->IncludeComponent(
                                'democontent2.pi:pagination',
                                '',
                                [
                                    'TOTAL' => $arResult['TOTAL'],
                                    'LIMIT' => $arResult['LIMIT'],
                                    'CURRENT_PAGE' => $arResult['CURRENT_PAGE'],
                                    'URL' => $APPLICATION->GetCurPage(false)
                                ]
                            );
                            ?>
                        <? else: ?>
                            <div class="alert alert-info">
                                <?= Loc::getMessage('USERS_COMPONENT_EMPTY') ?>
                            </div>
                        <? endif ?>
                    </div>
                <?if(count($arResult['ITEMS'] > 0)):?>
                    <div class="pagination-block-users">
                       <div class="load-more load-more-users col-md-6" data-ofset="<?=$last + 1?>" >
                           <p>Загрузить еще</p>
                       </div>
                        <div class="col-md-6">

                        </div>
                    </div>
                <?endif;?>

                </div>
            </div>
        </div>
    </div>
    <div class="banner-demo-bottom">
        <!--    <img src="--><?//=SITE_TEMPLATE_PATH?><!--/images/banners/banners-all-tasks-main.png">-->
        <div class="banner-demo-bottom-inner" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/images/banners/banners-all-tasks-main.png')">

        </div>
    </div>
    <script>
        BX.message({
            usersPath: '<?=$this->GetFolder()?>/ajax.php',
            addToFavourites: '<?=Loc::getMessage('ADD_TO_FAVOURITES')?>',
            inFavourites: '<?=Loc::getMessage('IN_FAVOURITES')?>'
        });
    </script>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}