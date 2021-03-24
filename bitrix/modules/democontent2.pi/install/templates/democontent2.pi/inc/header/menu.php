<nav class="menu">
    <ul>
        <li>
            <a href="<?= SITE_DIR ?>users/">#EXECUTORS#</a>
        </li>
        <li>
            <a href="<?= SITE_DIR ?>tasks/">#TASKS#</a>
        </li>
        <?php
        $APPLICATION->IncludeComponent(
            "bitrix:menu",
            "top",
            array(
                "ROOT_MENU_TYPE" => "top",
                "MENU_CACHE_TYPE" => "Y",
                "MENU_CACHE_TIME" => "360000000000",
                "MENU_CACHE_USE_GROUPS" => "Y",
                "MENU_CACHE_GET_VARS" => array(),
                "MAX_LEVEL" => "1",
                "CHILD_MENU_TYPE" => "left",
                "USE_EXT" => "Y",
                "ALLOW_MULTI_SELECT" => "N"
            ),
            false
        );
        ?>
    </ul>
</nav>