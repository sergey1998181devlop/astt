<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "#FAQ#");
$APPLICATION->SetTitle("#FAQ#");

$APPLICATION->IncludeComponent(
    'democontent2.pi:faq',
    '',
    array(
        'IBLOCK_ID' => '#IBLOCK_ID#',
        'CACHE_TIME' => 86400000
    )
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>