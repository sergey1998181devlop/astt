<?php
$arrId = [];
$arrIblockId = [];
foreach ($arResult['ITEMS'] as $id => $value){
    $data = unserialize($value['UF_DATA']);
    $arrId[] = $data['taskId'];
    $arrIblockId[] = $data['iBlockId'];


    //pre($arResult['ITEMS']);
//echo "<br>";

    $urlAll = '';
    $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
    $arFilter = Array("ID" =>  $arrId, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), array('ID' , 'DETAIL_PAGE_URL','IBLOCK_CODE'));
    while($ob = $res->GetNextElement())
    {
        $fields = $ob->GetFields();
        $urlOne = $fields['IBLOCK_TYPE_ID'];
        $detailPage = $fields['CODE'];


        $arrNamesType = array(
            'democontent2_pi_gruzopodemnaya_tekhnika' => 'gruzopodemnaya-tekhnika',
            'democontent2_pi_zemlerojnaya_tekhnika' => 'zemlerojnaya-tekhnika',
            'democontent2_pi_dorozhnaya_tekhnika' => 'dorozhnaya-tekhnika',
            'democontent2_pi_gruzovoj_transport' => 'gruzovoj-transport'
        );
        $urlAll = $arrNamesType[$urlOne].'/'.$fields['IBLOCK_CODE'].'/'. $detailPage .'-'.$fields['ID'].'/';




    }
    $arResult['ITEMS'][$id]['DETAIL_PAGE_URL'] = $urlAll;

}

//pre($arResult['ITEMS']);
