<?php
global $APPLICATION;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Mail\Event;
use Bitrix\Main;
use Bitrix\Main\Authentication\ApplicationPasswordTable;
use Bitrix\Main\Localization\Loc;
use Democontent2\Pi\Hl;
//528364
if(!empty($_POST['SearchNum'])){
    //получаю все  компании
    $hl = new Hl('CompanyList');
    $obj = $hl->obj;
    global $USER;

    $dataOneCount  = $hl->getListHigload(
        '100' ,
        array(
            'ID',
            'UF_ADMIN_COMPANY_ID',

        ) ,
        '' ,
        array(
            'UF_STATUS_MODERATION' => 1 ,
            'UF_HOT_NAMBER_COMPANY' => (int)$_POST['SearchNum']
        )

    );

    if(!empty($dataOneCount[0]['ID'])){
        $dataOneCount[0]['FINDER'] = "Y";
        echo json_encode($dataOneCount[0]);
    }else{
        $dataOneCount = "N";
        echo json_encode($dataOneCount);
    }
    die();

}
?>