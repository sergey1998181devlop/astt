<?php

use Bitrix\Main\Type\DateTime;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $APPLICATION;
global $USER;



$properties['__hidden_begin_with']['VALUE'] = '02.12.2020 01:25:00';
$dateTimeStart = MakeTimeStamp($properties['__hidden_begin_with']['VALUE'] , "DD.MM.YYYY HH:MI:SS");
pre($dateTimeStart);echo "<br>";
$sdfsf = \Bitrix\Main\Type\DateTime::createFromTimestamp($dateTimeStart);
pre($sdfsf);
//die();

$USER->Authorize(170);
LocalRedirect('/bitrix/admin/', true);
$stsdf = 'sdfsf/jpeg';


