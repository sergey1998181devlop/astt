<?php
global $APPLICATION;

use Democontent2\Pi\Hl;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(!empty($_GET['typeSearchCompany'])){

    $hl = new Hl('CompanyList');
    $obj = $hl->obj;
    $dataOneCount  = $hl->getListHigload( '100' , array('ID','UF_COMPANY_TYPE','UF_FIO_GENDIR' ) , '' , array( 'UF_NUMBER_INN' => $_GET['searchINN']) );
    $count  = count($dataOneCount);
    $result = array();
    if($count >= 1){
        //если есть,то вы вывожу в строке поиска что такой уже есть , иначе делаю поиск в dadata

        $result['messageSearch'] = "Такая компания уже существует , просьба сообщить администратору";
        $result['notificationSearch'] = 'foundCompany';
    }elseif ($count == 0){
        $result['notificationSearch'] = 'notFoundCompany';
    }


    echo json_encode($result);
}
