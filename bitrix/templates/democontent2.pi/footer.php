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

<!--<div class="footer-empty"></div>-->
<div class="popup popup-notification-account" id="popup-notification-account">

    <div class="popup-body">
        <div class="tabs-wrap">
            <div  class="classNotific" role="alert">
                <strong class="textStrond"> </strong>
            </div>
        </div>
    </div>
</div>

<div class="popup popup-code-inp" id="popup-code-inp">
    <div class="tabs-head">
        <ul>
            <li class="active">
                <a href="#tab1">Введите полученный код подтверждения</a>
            </li>
        </ul>
    </div>
    <div class="popup-body" >
        <div class="tabs-wrap">
            <div  class="" role="alert">

                <form class="form-check-code" method="post">
                    <div class="col-sm-12 col-xxs-12">
                        <div class="form-group ">
                            <input class="form-control required " type="phone" name="CodeAccountuser" required
                                   value=""
                                   placeholder="Ваш код">
                        </div>
                        <div class="mesageNotigic" style="display: none"></div>
                        <?/*<div class="text-center">
                            <button class="btn btn-green btn-submit" type="submit">
                                Обновить данные
                            </button>
                        </div>
                        */?>

                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<footer>
    <div class="footer-top">
        <div class="wrapper">
            <div class="row">

                <div class="item col-sm-3">
                    <?php
                    $APPLICATION->IncludeComponent(
                        "bitrix:main.include", "",
                        array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => SITE_TEMPLATE_PATH . "/inc/footer/social.php",
                            "EDIT_TEMPLATE" => "include_areas_template.php",
                            'MODE' => 'html'
                        ),
                        false
                    );
                    ?>
                </div>
                <?php
                $APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "",
                    array(
                        "ROOT_MENU_TYPE" => "bottom",
                        "MENU_CACHE_TYPE" => "Y",
                        "MENU_CACHE_TIME" => "360000000000",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "MENU_CACHE_GET_VARS" => array(),
                        "MAX_LEVEL" => "2",
                        "CHILD_MENU_TYPE" => "left",
                        "USE_EXT" => "Y",
                        "ALLOW_MULTI_SELECT" => "N"
                    ),
                    false
                );
                ?>
                <div class="item col-sm-5">
                    <div class="title footer-title-newDez">
                        Скачать приложение
                    </div>
                    <div class="obj-images-app">
                        <a href="#">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/social-shere-ico/app/gplay.png">
                        </a>
                        <a href="#">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/social-shere-ico/app/aple.png">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom" style="display: none;">
        <div class="wrapper text-center">
            <?php
            $APPLICATION->IncludeComponent("bitrix:main.include", "template1", Array(
	        "AREA_FILE_SHOW" => "file",	// Показывать включаемую область
            "PATH" => SITE_TEMPLATE_PATH."/inc/footer/copyright.php",	// Путь к файлу области
            "EDIT_TEMPLATE" => "include_areas_template.php",	// Шаблон области по умолчанию
            "MODE" => "php"
	),
	false
);
            ?>
        </div>
    </div>
</footer>
<script>
    window.onload = function () {
        document.body.classList.add('loaded_hiding');
        window.setTimeout(function () {
            document.body.classList.add('loaded');
            document.body.classList.remove('loaded_hiding');
        }, 500);
    }


</script>

</body>
</html>
