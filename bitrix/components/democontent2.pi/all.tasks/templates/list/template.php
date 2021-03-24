<?php
/**
 * User: Nazarkin Sergey
 * Email: nazarkin2017@mail.ru
 * Date: 07.10.2020
 * Time: 13:07
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
global $USER;
$currencyName = \Bitrix\Main\Config\Option::get(DSPI, 'currency_name');

if (method_exists($this, 'setFrameMode')) {
    $this->setFrameMode(true);
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

$route = [];




//$characteristics = [];
//if (strlen($arResult['UF_PROPERTIES']) > 0) {
//    $characteristics = unserialize($arResult['UF_PROPERTIES']);
//
//    foreach ($characteristics as $k => $v) {
//        if (isset($v['route'])) {
//            $route = $v['route'];
//            unset($characteristics[$k]);
//            break;
//        }
//    }
//}


?>
<?
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
<?
\Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/jquery-ui.min.css');
\Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery-ui.min.js');
?>

<div class="wrapper">
    <div class="row">
        <div class="new-blockDez-list">
            <div class="col-sm-12 col-md-3 col-xxs-12">

                <div class="list-comp">
                    <div class="list-comp-podlog">
                        <p>СПИСОК ЗАЯВОК</p>
                    </div>
                </div>
                <div class="location-users-filter">
                    <p>Ваш город:</p>
                    <div class="block-location-users-f">
                        <img src="<?=SITE_TEMPLATE_PATH?>/images/users/city.png">
                    </div>
                    <input type="hidden" name="nameCity" value="cityEmpty">
                   

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
            <div class="col-sm-12 col-md-6 col-xxs-12">
                <div class="new-blockDez-list-desc">
                    В этом разделе располагаются все заявки от организаций и частных лиц.
                    Вы можете откликнуться на заявку. Для вашего удобство а странице предусмотренны удобные фильтры для быстрого поиска релевантных заявок.
                </div>

                <div class="filter-sorty-to-block">

                    <form class="filter-sorty-to-items " method="get">
                        <div class="row">

                            <div class="input-group md-form form-sm form-2 pl-0 col-md-12 col-md-12">
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

                                                <?$APPLICATION->IncludeComponent("democontent2.pi:catalog.menu", "");?>


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
                    <img src="<?=SITE_TEMPLATE_PATH?>/images/smart-tasks.png">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content page-content-tasks-newDz">
    <div class="wrapper">
<?/*        <div class="page-title">
            <h1>
                <?= Loc::getMessage('TASKS_LIST_TITLE') ?>
                <?php

                ?>
            </h1>
        </div>
*/?>
        <div class="row">
            <div class="col-sm-12 col-md-3 col-xxs-12">
                <form class="props-nav-filter" method="post" >

                     <nav class="category-nav white-block" id="category-nav">
                    <div class="head hidden-md hidden-lg">
                        Категории
                        <a class="close" href="#">
                            <svg class="icon icon_close">
                                <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#close"></use>
                            </svg>
                        </a>
                    </div>

                    <div class="row filterleft-r">
                        <div class="col-md-6 col-sm-2">
                            <div class="filterleft-r-ico">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/users/filterleft-r-ico.png">
                            </div>
                            <p>Фильтр</p>
                        </div>
                        <div class="col-md-6 col-sm-10">
                            <div class="filterright-r">
                                <p>сбросить все</p>
                                <div class="close-all-check">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="city-your">
                                <span class="img-city-f" >
                                     <div class="col-md-12 col-sm-12 col-xxs-12">
                                            <div class="form-group dataCountday">
                                                <label for="date-start">
                                                   Сроки заказа
                                                </label>
                                                 <select class="js-select change-update-prop change-update-prop-day-year" name="state"   style="width: 100%">
                                                        <option selected value="" >&nbsp;</option>
                                                        <option value="1">На одну смену</option>
                                                        <option value="2">На две смены</option>
                                                        <option value="do5">До 5 смен</option>
                                                        <option value="more5">Более 5 смен</option>
                                                 </select>

                                            </div>
                                    </div>
                                </span>
                                <span class="img-city-f" >
                                     <div class="col-md-12 col-sm-12 col-xxs-12">
                                              <div class="form-group">
                                        <label for="date-start-work">
                                           Начало работ
                                        </label>
                                        <div class="datepicker-wrap">
                                            <input name="dateStart" readonly="true" id="date-start-work"
                                                   class="form-control ui-datepicker change-update-prop"
                                                   type="text"
                                                   placeholder="<?= Loc::getMessage('CREATE_COMPONENT_PL_DATE_START') ?>">
                                            <div class="del-count-day-tasks-list">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
                                    </svg>
                                            </div>

                                        </div>

                                    </div>
                                    </div>
                                </span>
                                <span class="chekers">
                                     <div class="form-group">
                                         <input class="checkbox contract-inp props-nav-filter-item block-money-ch" type="checkbox" name="propsLeftFilter" value="priceDogovor" id="priceDogovor">
                                            <label class="form-checkbox" for="priceDogovor">
                                                Договорная стоимость
                                            <span class="icon-wrap ">
                                         <svg class="icon icon_checkmark">
                                           <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#checkmark"></use>
                                         </svg>
                                        </span>
                                      </label>
                                    </div>
                                    <div class="form-group">
                                         <input class="checkbox contract-inp props-nav-filter-item block-money-ch" type="checkbox" name="propsLeftFilter" value="whithNds" id="whithNds">
                                            <label class="form-checkbox" for="whithNds">
                                                с НДС
                                            <span class="icon-wrap ">
                                         <svg class="icon icon_checkmark">
                                           <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#checkmark"></use>
                                         </svg>
                                        </span>
                                      </label>
                                    </div>
                                    <div class="form-group">
                                         <input class="checkbox contract-inp props-nav-filter-item block-money-ch" type="checkbox" name="propsLeftFilter" value="nalMoney" id="nalMoney">
                                            <label class="form-checkbox" for="nalMoney">
                                                Наличные
                                            <span class="icon-wrap ">
                                         <svg class="icon icon_checkmark">
                                           <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#checkmark"></use>
                                         </svg>
                                        </span>
                                      </label>
                                    </div>

                                    <div class="hr-sort-filter"></div>

                                    <div class="form-group">
                                         <input class="checkbox contract-inp props-nav-filter-item" type="checkbox" name="propsLeftFilter" value="nameCompany" id="nameCompany">
                                            <label class="form-checkbox" for="nameCompany">
                                                Компания
                                            <span class="icon-wrap ">
                                         <svg class="icon icon_checkmark">
                                           <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#checkmark"></use>
                                         </svg>
                                        </span>
                                      </label>
                                    </div>
                                     <div class="form-group">
                                         <input class="checkbox contract-inp props-nav-filter-item" type="checkbox" name="propsLeftFilter" value="hotPuts" id="hotPuts">
                                            <label class="form-checkbox" for="hotPuts">
                                                Избранное
                                            <span class="icon-wrap ">
                                         <svg class="icon icon_checkmark">
                                           <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#checkmark"></use>
                                         </svg>
                                        </span>
                                      </label>
                                    </div>


                                </span>
                            </div>
                        </div>
                    </div>
                </nav>

                </form>
                <div class="vertical-banner-users  right-block-banner">

                </div>
            </div>
            <div class="col-sm-12 col-md-9 col-xxs-12">
                <nav class="category-nav" id="category-nav">
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

                            <div class="sorty-panel sorty-panel-maplist">

                                <p class="activeInp" data-targ="tasks-list-varView">Список</p>
                                <p  data-targ="task-list-block-map" >Карта</p>

                            </div>

                            <span class="sorty-limit dop-list-items">
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
                <div class="tasks-newDz">

                    <? if (empty($arResult['ITEMS'])): ?>
                        <div class="alert alert-info">
                            <?= Loc::getMessage('TASKS_LIST_EMPTY') ?>
                        </div>
                    <? else: ?>
                        <div class="tasks-list-varView block-short-menu">
                            <div class="tasks-list" style="display: block">
                                <?php
                                $i = 0;

                                foreach ($arResult['ITEMS'] as $key => $item) {

                                    $route['items'][$key]['item_code'] = $item['UF_CODE'];
                                    $route['items'][$key]['typeEl'] = $item['UF_IBLOCK_TYPE'];
                                    $route['items'][$key]['subTypeEl'] = $item['UF_IBLOCK_CODE'];
//
                                    $route['items'][$key]['name'] = str_replace(array("\r","\n"),"", $item['UF_NAME']);

                                    $route['items'][$key]['center'] = $item['UF_MAP_COORDINATES'];
//
                                    $string = strip_tags($item['UF_DESCRIPTION']);
                                    $string = substr($string, 0, 138);

                                    $string = rtrim($string, "!,.-");
                                    $item['UF_DESCRIPTION'] = $string."...";
//
//
                                    $desc = str_replace('"', '',  $item['UF_DESCRIPTION'] );
                                    $desc = str_replace(array("\r","\n"),"",$desc);
                                    $route['items'][$key]['description'] =  str_replace("\r\n", " ", $desc );
//
//                                    //ессли нету номера в компании  /  беру номер у менеджера
                                    if(!empty($item['COMPANY']['UF_PHONE'])){
                                        $route['items'][$key]['phone'] = $item['COMPANY']['UF_PHONE'];
                                    }else{
                                        $route['items'][$key]['phone'] = $item['USER']['PERSONAL_PHONE'];
                                    }

                                    $route['items'][$key]['start_date'] = $item['UF_DATA_START_STRING'];
                                    $route['items'][$key]['count_day'] = $item['UF_COUNT_DAY_SET'];
//
                                    if(!empty($item['UF_BEZNAL_NUMBER'])){
                                        $route['items'][$key]['beznal'] = $item['UF_BEZNAL_NUMBER'];
                                    }
                                    if(!empty($item['UF_NALL_NUMBER'])){
                                        $route['items'][$key]['nall'] = $item['UF_NALL_NUMBER'];
                                    }
                                    if(!empty($item['UF_NDS_NUMBER'])){
                                        $route['items'][$key]['nds'] = $item['UF_NDS_NUMBER'];
                                    }
                                    if(!empty($item['COMPANY']['UF_COMPANY_NAME_MIN'])){
                                        $route['items'][$key]['nameCompany'] = str_replace('"', '', $item['COMPANY']['UF_COMPANY_NAME_MIN']);
                                    }
//                                    //если есть компания / беру имя компании /иначе беру имя менеджера или физ лица
                                    if(!empty($item['COMPANY']['UF_COMPANY_NAME_MIN'])){
                                        $route['items'][$key]['nameCreatedAuthor'] = str_replace('"', '', $item['COMPANY']['UF_COMPANY_NAME_MIN']);
                                    }else{
                                        $route['items'][$key]['nameCreatedAuthor'] = $item['USER']['NAME']." ".$item['USER']['LAST_NAME'];
                                    }
//                                    //менеджер физ лицо по факту
                                    $route['items'][$key]['managerTask'] = $item['USER']['NAME'].' '.$item['USER']['LAST_NAME'];
//
//
//                                    //currentUserTskBlk
                                    if($USER->IsAuthorized() && !empty($arUserForID['PERSONAL_PHONE'])){
                                        $route['items'][$key]['classButton'] = "see-phone-manager";
                                    }
                                    if(!$USER->IsAuthorized()){
                                        $route['items'][$key]['classButton'] = "without-auth";
                                        $route['items'][$key]['nameCompany'] = "Название компании";
                                        $route['items'][$key]['nameCreatedAuthorClassDop'] = "rebue_notAutorize";
                                        $itemMess['phone'] = '';

                                    }
//
                                    if($USER->IsAuthorized() && empty($arUserForID['PERSONAL_PHONE'])){
                                        $route['items'][$key]['classButton'] = "";
                                        $route['items'][$key]['dopClassButton'] = "see-phone-manager-notPhone";
                                        $route['items'][$key]['nameCompany'] = "Название компании";
                                        $route['items'][$key]['nameCreatedAuthorClassDop'] = "rebue_notAutorize";
                                        $itemMess['phone'] = '';
                                    }


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
                                    $route['items'][$key]['countDayWord'] = $word;

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
                                    <div class="task-preview task-preview-newDez <?= ($quickly) ? 'urgent' : '' ?>"
                                         id="<?= $this->GetEditAreaId($item['UF_ITEM_ID']) ?>">
                                        <div class="image-for-task">
                                            <img src="<?=SITE_TEMPLATE_PATH?>/images/itemsImages/<?=$item['UF_IBLOCK_TYPE']?>.png">
                                        </div>
                                        <div class="tbl tbl-fixed">
                                            <div class="tbc">
                                                <?if($USER->IsAuthorized()):?>
                                                    <div class="complain">
                                                        <div class="complain-item" data-id="<?= $item['UF_ITEM_ID'] ?>" data-path="<?=$this->GetFolder().'/ajaxFavorites.php'?>"
                                                             data-category="<?= $item['UF_IBLOCK_ID'] ?>">

                                                            <span class="span-star twe start-first <?if(empty($item['status_executor'])):?>active<?endif;?>" <?if(empty($item['status_executor'])):?> style="display: block;" <?else:?>style="display: none;" <?endif;?> ">
                                                            <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                                 viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
<path style="fill:rgba(100,60,37,0.39);" d="M492.757,190.241l-160.969-14.929L267.847,26.833c-4.477-10.396-19.218-10.396-23.694,0
	l-63.941,148.478L19.243,190.24c-11.271,1.045-15.826,15.064-7.322,22.534l121.452,106.694L97.828,477.174
	c-2.489,11.042,9.436,19.707,19.169,13.927l139.001-82.537l139.002,82.537c9.732,5.78,21.659-2.885,19.17-13.927l-35.544-157.705
	l121.452-106.694C508.583,205.305,504.029,191.286,492.757,190.241z"/></svg>


                                                            </span>

                                                            <span class="span-star twe-te <?if(!empty($item['status_executor'])):?>active<?endif;?>" <?if(!empty($item['status_executor'])):?> style="display: block;" <?else:?>style="display: none;" <?endif;?>>
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
                                                <?else:?>
                                                    <a href="#popup-registration" data-fancybox="">
                                                        <div class="complain">
                                                            <div class="complain-item-not-auth" data-id="<?= $item['UF_ITEM_ID'] ?>" data-path="<?=$this->GetFolder().'/ajaxFavorites.php'?>"
                                                                 data-category="<?= $item['UF_IBLOCK_ID'] ?>">

                                                            </div>
                                                        </div>
                                                    </a>

                                                <?endif;?>
                                                <div class="ttl medium">
                                                    <? if ($quickly): ?>
                                                        <span class="fire">
                                                <svg class="icon icon_fire">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#fire"></use>
                                                </svg>
                                            </span>
                                                    <? endif ?>
<!--                                                    --><?//= $item['UF_NAME'] ?>
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

                                                                <?if(!empty($arUserForID['PERSONAL_PHONE'])):?>
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
                                                                        <div class="object-fit-container">1
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
                                                        <?//pre($arUserForID);?>
                                                        <div class="tbc">

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
                            <?/*
                        $APPLICATION->IncludeComponent(
                            'democontent2.pi:pagination',
                            '',
                            array(
                                'TOTAL' => $arResult['TOTAL'],
                                'LIMIT' => $arResult['LIMIT'],
                                'CURRENT_PAGE' => $arResult['CURRENT_PAGE'],
                                'URL' => $APPLICATION->GetCurPage(false)
                            )
                        );
                        */?>
                            <?
                            $last = count($arResult['ITEMS'])-1;
                            $idOfset = $arResult['ITEMS'][$last]['ID'];

                            ?>
                            <div class=" block-btn-load-more">

                                <div class="load-more-tasks btn btn-green" data-ofset="<?=$last + 1?>" >
                                    Показать еще
                                </div>

                            </div>
                            <? endif ?>
                        </div>
                        <div class="task-list-block-map block-short-menu" style="display: none">
                            <div class="block-map-list">
                                <div class="map-area" id="map-area">

                                </div>
                                <div class="block-map-input-item" style="display: none">

                                </div>
                            </div>
                        </div>


                </div>
            </div>
        </div>
    </div>
    <div class="scrolltop-ac" id="scroller" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/images/arrows3.png');">

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

<?

if($route['items'][0]['name']){
    ?>
<style>
    .menu {
        list-style: none;
        padding: 5px;

        margin: 0;
    }
    .submenu {
        list-style: none;

        margin: 0 0 0 20px;
        padding: 0;
    }
    .submenu li {
        font-size: 90%;
    }
</style>
<?
    \Bitrix\Main\Page\Asset::getInstance()->addJs(
        'https://api-maps.yandex.ru/2.1/?load=package.full&lang=ru_RU&apikey=' . \Bitrix\Main\Config\Option::get(DSPI, 'yandex_maps_api_key')
    );
    $route['name'] = "Все заявки";
    $route['style'] = "islands#blueIcon";
}
?>

<script>
    BX.message({
        group: JSON.parse('<?=\Bitrix\Main\Web\Json::encode($route)?>'),
    });
</script>