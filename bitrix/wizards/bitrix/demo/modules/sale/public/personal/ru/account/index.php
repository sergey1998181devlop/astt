<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Счет пользователя");
?><?$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.account",
	"",
	Array(
		"SET_TITLE" => "Y" 
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>