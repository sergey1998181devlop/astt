<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 07.01.2019
 * Time: 16:15
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="<?= SITE_CHARSET ?>">
    <title><? $APPLICATION->ShowTitle() ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <?php
    $APPLICATION->ShowHead();
    \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/fancybox.css');
    \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/slick.css');
    \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/select2.css');
    \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/template_styles.css');

    try {
        if (\Bitrix\Main\IO\File::isFileExists(\Bitrix\Main\IO\Path::normalize(
            \Bitrix\Main\Application::getDocumentRoot() . SITE_TEMPLATE_PATH . '/css/custom.css'
        ))) {
            \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/custom.css');
        }
    } catch (\Bitrix\Main\IO\InvalidPathException $e) {
    }

    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/select2.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/tooltip.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/object-fit.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/svg4everybody.min.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/hc-sticky.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/slick-carousel.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/fancybox.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/mask.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery.timeago.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery.timeago.ru.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/app.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/bootstrap-notify/bootstrap-notify.min.js');

    try {
        if (\Bitrix\Main\IO\File::isFileExists(\Bitrix\Main\IO\Path::normalize(
            \Bitrix\Main\Application::getDocumentRoot() . SITE_TEMPLATE_PATH . '/js/custom.js'
        ))) {
            \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/custom.js');
        }
    } catch (\Bitrix\Main\IO\InvalidPathException $e) {
    }

    CJSCore::Init(['fx']);
    ?>
</head>
<body class="<?= ($USER->IsAuthorized()) ? 'user-authorized' : '' ?>">
<?php
if ($USER->IsAuthorized()) {
    $APPLICATION->ShowPanel();
}

if ($USER->IsAuthorized()) {
    $APPLICATION->IncludeComponent(
        'democontent2.pi:socket.io',
        ''
    );
}

$APPLICATION->IncludeComponent(
    "bitrix:main.include", "",
    array(
        "AREA_FILE_SHOW" => "file",
        "PATH" => SITE_TEMPLATE_PATH . "/inc/header/counters.php",
        "EDIT_TEMPLATE" => "include_areas_template.php",
        'MODE' => 'php'
    ),
    false
);
?>
<header>
    <div class="header-top">
        <?php
        $APPLICATION->IncludeComponent(
            'democontent2.pi:cookie',
            '',
            array()
        );
        ?>
        <div class="header-top-in">
            <div class="wrapper">
                <?php
                $APPLICATION->IncludeComponent(
                    'democontent2.pi:top.menu',
                    '',
                    array()
                );
                ?>
                <a class="burger left" href="#"><span></span></a>
                <?php
                $APPLICATION->IncludeComponent(
                    'democontent2.pi:location',
                    '',
                    []
                );
                ?>
            </div>
        </div>
    </div>
    <div class="header">
        <div class="wrapper">
            <?php
            $APPLICATION->IncludeComponent(
                "bitrix:main.include", "",
                array(
                    "AREA_FILE_SHOW" => "file",
                    "PATH" => SITE_TEMPLATE_PATH . "/inc/header/logo.php",
                    "EDIT_TEMPLATE" => "include_areas_template.php",
                    'MODE' => 'html'
                ),
                false
            );
            $APPLICATION->IncludeComponent(
                "bitrix:main.include", "",
                array(
                    "AREA_FILE_SHOW" => "file",
                    "PATH" => SITE_TEMPLATE_PATH . "/inc/header/menu.php",
                    "EDIT_TEMPLATE" => "include_areas_template.php",
                    'MODE' => 'html'
                ),
                false
            );
            ?>

        </div>
    </div>
</header>
<?php
if (
    ($APPLICATION->GetCurPage() !== SITE_DIR)
    && (\Bitrix\Main\Context::getCurrent()->getRequest()->getRequestedPage() !== SITE_DIR . 'index.php')
    && !defined('ERROR_404')
) {
    $APPLICATION->IncludeComponent(
        "bitrix:breadcrumb",
        ".default",
        array(
            "START_FROM" => "0",
            "PATH" => "",
            "SITE_ID" => \Bitrix\Main\Application::getInstance()->getContext()->getSite()
        )
    );
}
?>
