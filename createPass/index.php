<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Создание пароля");

$APPLICATION->IncludeComponent("bitrix:user.restorePass",".default",Array(
        'CHECKWORD' => $_GET['USER_CHECKWORD'],
        'USER_LOGIN' => $_GET['USER_LOGIN'],

    )
);
?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
