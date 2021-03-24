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
<!-- Yandex.Metrika counter --> <script type="text/javascript" > (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)}; m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)}) (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym"); ym(65983300, "init", { clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true }); </script> <noscript><div><img src="https://mc.yandex.ru/watch/65983300" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-88296434-5"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-88296434-5');




</script>

    <meta charset="<?= SITE_CHARSET ?>">
    <title><? $APPLICATION->ShowTitle() ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <?php
    $APPLICATION->ShowHead();
    \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/dropzone.css');
    \Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/theme-dropzone/jquery.filer-dragdropbox-theme.css');
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
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery.maskMoney.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/inputmask.js');
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
    $page = $APPLICATION->GetCurPage();

    if($page == '/user/company/' || !empty($_GET['us'])):

        \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/dropzone.js');
        \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/custom-dropzone.js');
    endif;
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/main.js');
    \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/moment.js');
    ?>

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
</head>
<div class="preloader preloaderForPlacehopld">
    <div class="preloader__row">
        <div class="preloader__item"></div>
        <div class="preloader__item"></div>
    </div>
</div>
<body class="<?= ($USER->IsAuthorized()) ? 'user-authorized' : '' ?>">

<script>
    window.onload = function () {
        document.body.classList.add('loaded');
    }

</script>
<?php
global $USER;
$idCurUser  = $USER->GetID();
if ($USER->IsAuthorized() &&  $idCurUser == 1 ) {
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
        <div class="new-blockheader">
            <div class="wrapper">

                <?php
                $APPLICATION->IncludeComponent(
                    'democontent2.pi:top.menuAuthadd',
                    '',
                    array()
                );
                ?>

            </div>
        </div>
        <?php
        $APPLICATION->IncludeComponent(
            'democontent2.pi:cookie',
            '',
            array()
        );
        ?>

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
            );?>
            <div class="burger-mobil">
                <div class="burger-mobil-block">
                    <span class="navbar-toggler-icon"></span>
                    <span class="navbar-toggler-icon"></span>
                    <span class="navbar-toggler-icon"></span>
                </div>
            </div>
            <?

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
