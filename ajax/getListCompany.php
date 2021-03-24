<?php
global $APPLICATION;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Mail\Event;
use Bitrix\Main;
use Bitrix\Main\Authentication\ApplicationPasswordTable;
use Bitrix\Main\Localization\Loc;
use Democontent2\Pi\Hl;

if($_POST['updateCompany'] == "Y" ){
    $maxData = $_POST['maxData'];


    //получаю все  компании
    $hl = new Hl('CompanyList');
    $obj = $hl->obj;
    global $USER;

    $dataOneCount  = $hl->getListHigload(
        '100' ,
        array(
            'ID',
            'UF_COMPANY_TYPE',
            'UF_NUMBER_OGRN',
            'UF_KOD_KPP',
            'UF_KOD_OKPO',
            'UF_FIO_GENDIR',
            'UF_UR_ADDRESS',
            'UF_REAL_ADDRESS',
            'UF_PHONE',
            'UF_EMAIL',
            'UF_DESCRIPTION',
            'UF_NUMBER_INN',
            'UF_LOGO',
            'UF_FILE0',
            'UF_FILE1',
            'UF_FILE2',
            'UF_FILE3',
            'UF_COMPANY_NAME_MIN',
            'UF_BIG_NAME',
            'UF_STATUS_MODERATION',
            'UF_ADMIN_COMPANY_ID',
            'UF_DATA_CREATE'
        ) ,
        '' ,
        array(
            'UF_STATUS_MODERATION' => 1 ,
            '>UF_DATA_CREATE' => \Bitrix\Main\Type\DateTime::createFromPhp( new DateTime($maxData) )

        )

    );

    $idMessUsers = [];
    foreach ($dataOneCount as $id => $val){
        $idMessUsers[] = $val['UF_ADMIN_COMPANY_ID'];
    }
    $idMessUsers = implode("||" , $idMessUsers);

    $filter = Array
    (
        "ID"                  => $idMessUsers,

    );
    $order = array('sort' => 'asc');
    $tmp = 'sort'; // параметр проигнорируется методом, но обязан быть
    $rsUsers = \CUser::GetList($order, $tmp , $filter);
    $usersMess = [];
    while($arUser = $rsUsers->Fetch()) {
        $usersMess[] = $arUser;
    }

    foreach ($dataOneCount  as $id => $itemComp) {
        $dataOneCount[$id]['USER'] = $usersMess[$id];
    }

    $arReturnJson = [];
//    $arReturnJson = [
//        'status' => 'OKDATA',
//        'IdCompany' => $dataOneCount['ID'],
//        'NameMinCompany' => $dataOneCount['UF_COMPANY_NAME_MIN'],
//    ];
    if(!empty($dataOneCount[0]['ID'])){
        $arReturnJson['status'] = 'updateSuccess';
    }
    $messNewMaxData = [];
    foreach ($dataOneCount as $idMess => $companyNew){
        $ava = CFile::GetPath($companyNew["USER"]["PERSONAL_PHOTO"]);
        $messNewMaxData[] = $companyNew['UF_DATA_CREATE']->format('U');
        $arReturnJson['COMPANY'][$companyNew['ID']] = [
            'NameMinCompany' => $companyNew['UF_COMPANY_NAME_MIN'],
            'UserId' => $companyNew['USER']['ID'],
            'UserName' => $companyNew['USER']['NAME'],
            'UserFemale' => $companyNew['USER']['LAST_NAME'],
            'UserAvatar' => $ava,
            'DescriptionCompany' => $companyNew['UF_DESCRIPTION'],

        ];
    }
    if(!empty($dataOneCount[0]['ID'])) {
        $maxDate = date('Y-m-d H:i:s', max($messNewMaxData));
        $arReturnJson['MAXDATA'] = $maxDate;
    }

    //возращаю json  собранного массива новых созданных компаний
//    header('Content-Type: application/json');

    echo json_encode($arReturnJson);


}