<?php
global $APPLICATION;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(!empty($_POST['file'])){
    $result = \CFile::Delete($_POST['file']);
    echo  json_encode($result);
}