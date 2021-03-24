<?php
global $APPLICATION;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$arParams = array("replace_space"=>"-","replace_other"=>"-");
$trans = \Cutil::translit($_FILES['files']['name'][0],"ru",$arParams);
$newName = randString(10);
$fileArray = array(
    "name" => $newName,
    "size" => $_FILES['files']['size'][0],
    "tmp_name" => $_FILES['files']['tmp_name'][0],
    "type" => $_FILES['files']['type'][0],

);

$fid = \CFile::SaveFile( $fileArray , false  , false , false , 'list');
$arFile = \CFile::GetFileArray($fid);

if(!empty($arFile['ID'])){
    $statusUp = [
        'ID_NEW_EL' => $arFile['ID'],
        'NEW_FILE_NAME' => $newName
    ];
    echo  json_encode($statusUp);
}