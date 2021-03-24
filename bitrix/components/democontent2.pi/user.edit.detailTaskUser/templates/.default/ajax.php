<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 12.01.2019
 * Time: 17:25
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$error = 1;
$result = array();

global $USER;
if(!empty($_POST['Category']) && empty($_POST['SubCategory'])){
    $arrNamesType = array(
        'gruzopodemnaya-tekhnika' => 'democontent2_pi_gruzopodemnaya_tekhnika',
        'zemlerojnaya-tekhnika' => 'democontent2_pi_zemlerojnaya_tekhnika',
        'dorozhnaya-tekhnika' => 'democontent2_pi_dorozhnaya_tekhnika',
        'gruzovoj-transport' => 'democontent2_pi_gruzovoj_transport'
    );

    $res = CIBlock::GetList(Array(), Array(
        'TYPE'=>$arrNamesType[$_POST['Category']],
        'SITE_ID'=>SITE_ID,
        'ACTIVE'=>'Y',
        "CODE"=>array()), true);
    $arrElementsDrop = array();
    while($arIblock = $res->Fetch()) {

        //здесь выводим информацию о инфоблоке: ID, NAME, CODE и т.д.
        //вывод названия инфоблока
//        print_r($arIblock['NAME']);
        $arrElementsDrop['TYPECAT'] = 'Y';
        $arrElementsDrop['NAME'][] = $arIblock['NAME'];
        $arrElementsDrop['CODE'][] = $arIblock['CODE'];

    }
//    pre($arrElementsDrop);
$result = json_encode($arrElementsDrop);
    echo  $result;



}
if(!empty($_POST['Category']) && !empty($_POST['SubCategory'])){

    $category = $_POST['Category'];
    $SubCategory = $_POST['SubCategory'];
    $arElements = [];

    $arSelect = Array("ID", "NAME" , "IBLOCK_SECTION_ID" );
    $arFilter = Array("IBLOCK_ID"=>IntVal(103),  "ACTIVE"=>"Y" , "SECTION_CODE" => $SubCategory);
    $res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false,  false , $arSelect);
    while($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();
        $arElements[$arFields['IBLOCK_SECTION_ID']][] = $arFields;

    }
    $arrElementsAll = [];
    foreach ($arElements as $id => $value){


        $arFilter = Array('IBLOCK_ID'=> 103, 'ID' => $id );
        $db_list = CIBlockSection::GetList(Array("SORT"=>"ASC"), $arFilter, true);

        while($ar_result = $db_list->GetNext())
        {

            $arrElementsAll[$ar_result['SORT']][$ar_result['NAME']] = $value;
//            debmes($ar_result);
//            return false;
        }




    }

    ksort($arrElementsAll);

    $allList = [];
    foreach ($arrElementsAll as $id => $val ){
      foreach ($val as $idH => $valueS )

        foreach ($valueS as $idV => $valEnd){
            $allList[$idH][] = $valEnd['NAME'];


        }
    }


    echo json_encode($allList);

}
