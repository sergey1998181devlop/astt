<?php
global $APPLICATION;
//define ("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?


//sms with a code was sent. Redirect to the change password form
pre($_SERVER);


//backurl	"/testauthForgot/"
//AUTH_FORM	"Y"
//TYPE	"SEND_PWD"
//USER_LOGIN	"nazarkin2017@mail.ru"
//USER_EMAIL	"nazarkin2017@mail.ru"
//send_account_info	"Выслать"

global $APPLICATION;
$APPLICATION->IncludeComponent("bitrix:system.auth.forgotpasswd", "", Array(
    "SHOW_ERRORS" => "Y"
),
    false
);

?>