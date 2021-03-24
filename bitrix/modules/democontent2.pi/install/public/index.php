<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "#INDEX_TITLE#");
$APPLICATION->SetTitle("#INDEX_TITLE#");

\Bitrix\Main\Loader::includeModule('democontent2.pi');
?>
    <div class="main-bannner"
         style="background: url('<?= SITE_TEMPLATE_PATH ?>/images/main-banner.jpg') center center no-repeat; background-size: cover;">
        <div class="wrapper">
            <?php
            $APPLICATION->IncludeComponent(
                'democontent2.pi:search',
                ''
            );
            ?>
        </div>
    </div>

<?php
$APPLICATION->IncludeComponent(
    "bitrix:main.include", "",
    array(
        "AREA_FILE_SHOW" => "file",
        "PATH" => SITE_TEMPLATE_PATH . "/inc/index/advantages.php",
        "EDIT_TEMPLATE" => "include_areas_template.php",
        'MODE' => 'html'
    ),
    false
);

$APPLICATION->IncludeComponent(
    'democontent2.pi:catalog.menu',
    'catalog.users'
);

$APPLICATION->IncludeComponent(
    'democontent2.pi:all.tasks',
    '',
    array(
        'LIMIT' => 15
    )
);
?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>