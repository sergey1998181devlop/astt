<?php
global $APPLICATION;
global



$USER;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$hl = new Democontent2\Pi\Hl('Democontentpiall');
$dateStart = "04.02.2021";
$dateEnd = "08.02.2021";
$obj = $hl->obj;
$obj::add(
    [
        "UF_ID" => rand(),
        "UF_NAME" => "test",
        "UF_BEGIN_WITH" => ConvertDateTime($dateStart, "DD.MM.YYYY")." 00:00:00",
        "UF_RUN_UP" => ConvertDateTime($dateEnd, "DD.MM.YYYY")." 23:59:59"
    ]
);
