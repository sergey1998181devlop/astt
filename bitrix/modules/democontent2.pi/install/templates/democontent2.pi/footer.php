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
<div class="footer-empty"></div>
<footer>
    <div class="footer-top">
        <div class="wrapper">
            <div class="row">
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
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="wrapper text-center">
            <?php
            $APPLICATION->IncludeComponent(
                "bitrix:main.include", "",
                array(
                    "AREA_FILE_SHOW" => "file",
                    "PATH" => SITE_TEMPLATE_PATH . "/inc/footer/copyright.php",
                    "EDIT_TEMPLATE" => "include_areas_template.php",
                    'MODE' => 'php'
                ),
                false
            );
            ?>
        </div>
    </div>
</footer>
<div class="btn-up" id="btn-up">
    <svg class="icon icon_angle-up">
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#angle-up"></use>
    </svg>
</div>
<?php
$APPLICATION->IncludeComponent('democontent2.pi:push', '');
?>
</body>
</html>
