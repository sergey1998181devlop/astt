<?php
global $APPLICATION;
global $USER;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

//собираю все элементы

$items = new \Democontent2\Pi\Iblock\Items();
$us = new \Democontent2\Pi\User(intval($USER->GetID()));
$favouritesTask = new \Democontent2\Pi\FavouritesTask(intval($USER->GetID()));
$favouritesTaskList = $favouritesTask->getList();




$result['ITEMS'] = $items->getAllTasks();

$idUserCurrent = $USER->GetID();
$rsUser = CUser::GetByID($idUserCurrent);
$arUser = $rsUser->Fetch();



//собираю список id пользователей и достаю список компаний по ним
$arIdes = [];
foreach ($result['ITEMS'] as $idUserItems => $item){
    $arIdes[] = $item['UF_USER_ID'];
}
//достаю список компаний
$result['COMPANY']['ITEMS'] = $us->getListCompanyToTask($arIdes);


//раскидываю компании по элементам
foreach ($arIdes as $id => $valueIdCompany){
    $result['ITEMS'][$id]['COMPANY'] = $result['COMPANY']['ITEMS'][$id];
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


$itemsForAjax = [];
foreach ($result['ITEMS'] as $itemTask => $taskItem){
    $itemMess = array(
        "NAME" => $taskItem['UF_NAME'],
        "DESC" => $taskItem['UF_DESCRIPTION'],
        "START_DATA" => $taskItem['UF_DATA_START_STRING'],
        "CITY_NAME" => $taskItem['UF_CITY_NAME'],
        "MAP_COORD" => $taskItem['UF_MAP_COORDINATES'],

        "NALL" => $taskItem['UF_NALL'],
        "BEZNALL" => $taskItem['UF_BEZNAL'],
        "NDS" => $taskItem['UF_NDS'],
        "ID_ITEM" => $taskItem['ID'],

        "UF_IBLOCK_CODE" => $taskItem['UF_IBLOCK_CODE'],
        "UF_IBLOCK_TYPE" => $taskItem['UF_IBLOCK_TYPE'],
        "UF_CODE" => $taskItem['UF_CODE'],
    );

    $itemMess['item_code'] = $taskItem['UF_CODE'];
    $itemMess['typeEl'] = $taskItem['UF_IBLOCK_TYPE'];
    $itemMess['subTypeEl'] = $taskItem['UF_IBLOCK_CODE'];
//
    $itemMess['name'] = str_replace(array("\r","\n"),"", $taskItem['UF_NAME']);

    $itemMess['center'] = $taskItem['UF_MAP_COORDINATES'];
//
    $string = strip_tags($taskItem['UF_DESCRIPTION']);
    $string = substr($string, 0, 138);

    $string = rtrim($string, "!,.-");
    $itemMess['description']  = $string."...";
//
//
//    $desc = str_replace('"', '',  $taskItem['UF_DESCRIPTION'] );
//    $desc = str_replace(array("\r","\n"),"",$desc);
//    $itemMess['items'][$itemTask]['description'] =  str_replace("\r\n", " ", $desc );
//
//                                    //ессли нету номера в компании  /  беру номер у менеджера
    if(!empty($taskItem['COMPANY']['UF_PHONE'])){
        $itemMess['phone'] = $taskItem['COMPANY']['UF_PHONE'];
    }else{
        $itemMess['phone'] = $taskItem['USER']['PERSONAL_PHONE'];
    }

    $itemMess['start_date'] = $taskItem['UF_DATA_START_STRING'];
    $itemMess['count_day'] = $taskItem['UF_COUNT_DAY_SET'];
//
    if(!empty($taskItem['UF_BEZNAL_NUMBER'])){
        $itemMess['beznal'] = $taskItem['UF_BEZNAL_NUMBER'];
    }
    if(!empty($taskItem['UF_NALL_NUMBER'])){
        $itemMess['nall'] = $taskItem['UF_NALL_NUMBER'];
    }
    if(!empty($taskItem['UF_NDS_NUMBER'])){
        $itemMess['nds'] = $taskItem['UF_NDS_NUMBER'];
    }
    if(!empty($taskItem['COMPANY']['UF_COMPANY_NAME_MIN'])){
        $itemMess['nameCompany'] = str_replace('"', '', $taskItem['COMPANY']['UF_COMPANY_NAME_MIN']);
    }
//                                    //если есть компания / беру имя компании /иначе беру имя менеджера или физ лица
    if(!empty($taskItem['COMPANY']['UF_COMPANY_NAME_MIN'])){
        $itemMess['nameCreatedAuthor'] = str_replace('"', '', $taskItem['COMPANY']['UF_COMPANY_NAME_MIN']);
    }else{
        $itemMess['nameCreatedAuthor'] = $item['USER']['NAME']." ".$taskItem['USER']['LAST_NAME'];
    }
//                                    //менеджер физ лицо по факту
    $itemMess['managerTask'] = $item['USER']['NAME'].' '.$taskItem['USER']['LAST_NAME'];
//
//
//                                    //currentUserTskBlk
    if($USER->IsAuthorized() && !empty($arUser['PERSONAL_PHONE'])){
        $itemMess['classButton'] = "see-phone-manager";
    }
    if(!$USER->IsAuthorized()){
        $itemMess['classButton'] = "without-auth";
        $itemMess['nameCompany'] = "Название компании";
        $itemMess['nameCreatedAuthorClassDop'] = "rebue_notAutorize";
        $itemMess['phone'] = '';

    }
//
    if($USER->IsAuthorized() && empty($arUser['PERSONAL_PHONE'])){
        $itemMess['classButton'] = "";
        $itemMess['dopClassButton'] = "see-phone-manager-notPhone";
        $itemMess['nameCompany'] = "Название компании";
        $itemMess['nameCreatedAuthorClassDop'] = "rebue_notAutorize";
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
    $itemMess['countDayWord'] = $word;


    $favouritesTaskRes = "notIsetF";
    $favouritesTaskRes = array_search($taskItem['UF_ITEM_ID'] , $favouritesTaskList['UF_ITEM_ID']) ;

    if( $favouritesTaskRes !== "notIsetF" && is_int($favouritesTaskRes)){
        $itemMess['CUR_FAVOURITE'] = "Y";

    }



    //кол-во смен считаю тут / заранее, и выбираю название и аватарки и решаю светить или нет кнопку  - показать номер
    if(!empty($taskItem['UF_COUNT_DAY_SET'] ) ){



        if(!empty($taskItem['UF_COUNT_DAY_SET']) ){
            $dateCount = $taskItem['UF_COUNT_DAY_SET'] ;
            $word = num2word((int)$dateCount, array('смену', 'смены', 'смен'));
            $itemMess['COUNT_DAY'] = $dateCount;
            $itemMess['WORD_COUNT_DAY'] = $word;
        }else{
            $dateCount = '1';
            $itemMess['COUNT_DAY'] = 1;
            $itemMess['WORD_COUNT_DAY'] = 'смену';
        }
    }

    //если не пуст и не авторизован
    if(!empty($taskItem['USER']['ID']) && $USER->IsAuthorized()){

        //если не заполнен профиль ставлю ключ
            if(empty($arUser['PERSONAL_PHONE'])){
                $itemMess['CUR_USER_EMPTY_ACCOUNT'] = "Y";
            }
        //если заполнен  / проставляю аватарку имя и кнопку   / не подставляю ключ
            else{
                if(!empty($taskItem['USER']['NAME'])){
                    $itemMess['MANAGER_NAME'] = $taskItem['USER']['NAME'];
                }
                if(!empty($taskItem['USER']['LAST_NAME'])){
                    $itemMess['MANAGER_LASTNAME'] = $taskItem['USER']['LAST_NAME'];
                }
                if(!empty($taskItem['USER']['PERSONAL_PHONE'])){
                    //телефон менеджера
                    $itemMess['PERSONAL_PHONE'] = $taskItem['USER']['PERSONAL_PHONE'];
                }else{
                    //телефон компании
                    $itemMess['PERSONAL_PHONE'] = $taskItem['COMPANY']['UF_PHONE'];
                }
                $itemMess['USER_MANAGER'] = $taskItem['USER']['ID'];
                $userAva = CFile::GetPath($taskItem['USER']["PERSONAL_PHOTO"]);

                $itemMess['CURRENT_AVA'] = $userAva;
//                $itemMess['MANAGER_PHOTO'] =
                $itemMess['CUR_USER_EMPTY_ACCOUNT'] = "N";



            }

    }else{
        $itemMess['NOT_AUTHORIZE'] = "Y";
    }
    $itemMess['UF_ITEM_ID'] = $taskItem['UF_ITEM_ID'];


    $tbsStars = '<div class="feedback">
                                                            <div class="assessment-box">
                                                                <svg class="icon icon_star-empty">
                                                                    <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#star-empty"></use>
                                                                </svg>
                                                                <svg class="icon icon_star-empty">
                                                                    <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#star-empty"></use>
                                                                </svg>
                                                                <svg class="icon icon_star-empty">
                                                                    <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#star-empty"></use>
                                                                </svg>
                                                                <svg class="icon icon_star-empty">
                                                                    <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#star-empty"></use>
                                                                </svg>
                                                                <svg class="icon icon_star-empty">
                                                                    <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#star-empty"></use>
                                                                </svg>
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
                                                            <div class="likes">
                                                                <span class="like">
                                                                    <svg class="icon icon_thumbs-o-up">
                                                                      <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#thumbs-o-up"></use>
                                                                    </svg>
                                                                    0                                                                </span>
                                                                <span class="dislike">
                                                                    <svg class="icon icon_thumbs-o-down">
                                                                        <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#thumbs-o-down"></use>
                                                                    </svg>
                                                                    0                                                                </span>
                                                            </div>
                                                        </div>';
    $blockRight = '<div class="tbc tbc-info">';

        $blockRight .= '<div class="price-box">';
            if(!empty( $taskItem['UF_NDS'])){
                $blockRight .=  '<span class="nds" >C НДС:</span><span class="nds_price">'.(string)$taskItem['UF_NDS'].'₽</span><br>';
            }
            if(!empty( $taskItem['UF_BEZNAL'])){
                $blockRight .= '  <span class="bezznal" >Без НДС: </span><span class="bezznal_price">'.(string)$taskItem['UF_BEZNAL'].'₽</span><br>';
            }
            if(!empty( $taskItem['UF_NALL'])){
                $blockRight .= '<span class="nall" >Нал: </span><span class="nall_price" >'.(string)$taskItem['UF_NALL'].'₽</span><br>';
            }
            if(empty( $taskItem['UF_NDS']) && empty( $taskItem['UF_BEZNAL']) && empty( $taskItem['UF_NALL'])){
                $blockRight .= '<span class="" >По договоренности</span><br>';
            }

        $blockRight .= '</div>';
            //price-box end
        $blockRight .= '<div class="user-box"><div class="tbl tbl-fixed">';

            if(!$USER->IsAuthorized() || empty($arUser['PERSONAL_PHONE'])){


                //если не авторизован илине подтвержден акк
                $blockRight .= '<div class="tbc"><div class="ava"><div class="object-fit-container ">
                    <img src="/bitrix/templates/democontent2.pi/images/2.png" alt="" data-object-fit=""  data-object-position="50% 50%">
                </div> </div> </div>';
                $blockRight .= '<div class="tbc updateRightblsf"><div class="name medium rebue_notAutorize">Название компании</div> ';
                $blockRight .= $tbsStars;

            }else{
                $blockRight .= '<div class="tbc"><div class="ava"><div class="object-fit-container ">
                    <img src="'.$itemMess['CURRENT_AVA'].'" alt="" data-object-fit=""  data-object-position="50% 50%">
                </div> </div> </div>';
                if(!empty($taskItem['COMPANY']['UF_COMPANY_NAME_MIN'])){
                    $blockRight .= '<div class="tbc updateRightblsf"><div class="name medium ">'.$taskItem['COMPANY']['UF_COMPANY_NAME_MIN'].'</div> ';

                }else{
                    $blockRight .= '<div class="tbc updateRightblsf"><div class="name medium ">'.$itemMess['MANAGER_NAME'].'&nbsp'.$itemMess['MANAGER_LASTNAME'].'</div>';

                }


                $blockRight .= $tbsStars;
            }
        $blockRight .= '</div><a class="lnk-abs" href="/user/'.$itemMess['USER_MANAGER'].'/"></a> </div>';
    $blockRight .= '</div>';
    //user-box end
    $blockRight .= '<div class="info-phone-task"><div class="company-phone-detail">';
    //если не авторизован
    if(!$USER->IsAuthorized()){
        $blockRight .= '<div class="btn btn-green  without-auth" style="margin: 10px 0px" >Показать телефон</div>';
    }
    //если авторизован но не подтвержден
    if($USER->IsAuthorized() && empty($arUser['PERSONAL_PHONE']) ){
        $blockRight .= '<div class="btn btn-green see-phone-manager" onclick="showPopup(&quot;Для просмотра содержимого подтвердите аккаунт на странице <br><a >Настройки профиля</a>&quot; , &quot;alert alert-success&quot;);">Показать телефон</div>';
    }
    //если авторизован и подтверден
    if($USER->IsAuthorized() && !empty($arUser['PERSONAL_PHONE']) ){
        $blockRight .= '<div class="btn btn-green see-phone-manager">Показать телефон</div>';
        $blockRight .= '<div class="phone-non" style="display:none;">Телефон:<p>'.$itemMess['PERSONAL_PHONE'].'</p></div>';
        $blockRight .= '<div class="menedged-is">Менеджер:<p>'.$itemMess['MANAGER_NAME'].'&nbsp;'.$itemMess['MANAGER_LASTNAME'].'</p></div>';
    }
    $blockRight .= '</div></div>';
    $blockRight .= '</div>';
    $itemMess['RIGHT_BLOCK'] = $blockRight;

    $itemsForAjax["MESSITEMS"][] = $itemMess;
//    $itemsForAjax[]  = $itemMessForAjax;
}
$endElForOffset = count($result['ITEMS']) -1;

$itemsForAjax['END_ELL'] = $endElForOffset;
if(!empty($itemsForAjax['MESSITEMS'][0])){
    $itemsForAjax['NOTEMPTY'] = 'Y';
}


$APPLICATION->RestartBuffer();

echo json_encode($itemsForAjax);

die();