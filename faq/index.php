<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Помощь");
$APPLICATION->SetTitle("Помощь");

$APPLICATION->IncludeComponent(
    'democontent2.pi:faq',
    '',
    array(
        'IBLOCK_ID' => '2',
        'CACHE_TIME' => 86400000
    )
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>