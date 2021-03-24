<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 25.09.2018
 * Time: 10:35
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}

?>
    <div class="account-block">
        <div class="user-box">
            <div class="tbl tbl-fixed" >
                <div class="tbc">
                    <div class="user-ava">
                        <?php
                        if (isset($arResult['USER']['AVATAR']) && !empty($arResult['USER']['AVATAR']['src']) ) {
                            ?>
                            <?$new_password = randString(7);?>
                            <div class="object-fit-container">
                                <img src="<?= $arResult['USER']['AVATAR']['src'] ?>?rand=<?=$new_password?>"
                                     alt="" data-object-fit="cover"
                                     data-object-position="50% 50%"/>
                            </div>
                            <?php
                        } else {
                            ?>
                            <svg class="icon icon_user">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#user"></use>
                            </svg>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="tbc semibold">
                    <?if(!empty($arResult['COMPANY'][0]['ID'])):?>
                        <?=$arResult['COMPANY'][0]['UF_COMPANY_NAME_MIN']?>
                    <?else:?>
                        <?= $arResult['USER']['NAME'] . ((strlen($arResult['USER']['LAST_NAME'])) ? ' ' . $arResult['USER']['LAST_NAME'] : '') ?>
                    <?endif;?>

                    <div class="balance-block">
                        <?= \Democontent2\Pi\Utils::price($arResult['BALANCE']) ?>
                        <?= \Bitrix\Main\Config\Option::get(DSPI, 'currency_name') ?>
                    </div>
                    <? if ($arResult['USER']['UF_DSPI_DOCUMENTS']): ?>
                        <div class="verification-box">
                            <svg class="icon icon_checkmark">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                            </svg>
                            <?= Loc::getMessage('USER_MENU_COMPONENT_VERIFICATION') ?>
                        </div>
                    <? endif ?>
                    <? if (count($arResult['CARD'])): ?>
                        <div class="security-box">
                            <svg class="icon icon_shield_doc">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#shield-doc"></use>
                            </svg>
                            <?= Loc::getMessage('USER_MENU_COMPONENT_SECURITY') ?>
                        </div>
                    <? endif ?>
                </div>
            </div>

            <?php
            if ($arResult['USER']['UF_DSPI_EXECUTOR']) {
                ?>
                <div class="status-controller-area">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn <?= (!intval($arResult['USER']['UF_DSPI_BUSY'])) ? 'btn-green' : 'btn-secondary' ?> status-control active"
                               data-type="free">
                            <?= Loc::getMessage('USER_MENU_COMPONENT_IS_FREE') ?>
                        </label>
                        <label class="btn <?= (intval($arResult['USER']['UF_DSPI_BUSY'])) ? 'btn-green' : 'btn-secondary' ?> status-control active"
                               data-type="busy">
                            <?= Loc::getMessage('USER_MENU_COMPONENT_BUSY') ?>
                        </label>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <nav>
            <ul>
                <?//светить раздел модерация только для модераторов и для админа !?>
                <?  if(in_array(8, $USER->GetUserGroupArray()) ||  in_array(1, $USER->GetUserGroupArray())): ?>
                    <li>
                        <a href="<?= SITE_DIR . 'user/categories/' ?>">
                            <?= Loc::getMessage('USER_MENU_COMPONENT_CATEGORIES') ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_DIR . 'user/moderation/' ?>">
                            <?= Loc::getMessage('USER_MENU_COMPONENT_MODERATION') ?>
                        </a>
                    </li>
                <? endif ?>




                <? if ($arResult['USER']['UF_DSPI_EXECUTOR']): ?>
                    <li>
                        <a href="<?= SITE_DIR . 'user/portfolio/' ?>">
                            <?= Loc::getMessage('USER_MENU_COMPONENT_PORTFOLIO') ?>
                            <div class="icon-wrap">
                                <svg class="icon icon_briefcase">
                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#briefcase"></use>
                                </svg>
                            </div>
                        </a>
                    </li>
                <? endif ?>
                <li>
                    <a href="<?= SITE_DIR . 'user/settings/' ?>">
                        <?= Loc::getMessage('USER_MENU_COMPONENT_PROFILE') ?>
                        <div class="icon-wrap">
                            <svg class="icon icon_profile">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#profile"></use>
                            </svg>
                        </div>
                    </a>
                </li>

                <?if( $arResult['USER']['UF_MODERATION_ACCESS'] == "true" || $arResult['USER']['UF_MODERATION_ACCESS'] == "false"):?>
                <li>
                    <a href="<?= SITE_DIR . 'user/company/' ?>">
                        <?= Loc::getMessage('USER_MENU_COMPONENT_COMPANY') ?>
                        <div class="icon-wrap">
                            <svg class="icon icon_profile">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#profile"></use>
                            </svg>
                        </div>
                    </a>
                </li>
                <?endif;?>
                <?if( $arResult['USER']['UF_MODERATION_ACCESS'] == "true"  || $arResult['USER']['UF_MODERATION_ACCESS'] == "false" ):?>
                <li>
                    <a href="<?= SITE_DIR . 'user/employees/' ?>">
                        <?= Loc::getMessage('USER_MENU_COMPONENT_EMPLOYEES') ?>
                        <div class="icon-wrap">
                            <svg class="icon icon_profile">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#profile"></use>
                            </svg>
                        </div>
                    </a>
                </li>
                <?endif;?>
                <?php
                if (intval($arResult['USER']['UF_DSPI_EXECUTOR'])) {
                    ?>
                    <li>
                        <a href="<?= SITE_DIR . 'user/tariff/' ?>">
                            <?= Loc::getMessage('USER_MENU_COMPONENT_TARIFF') ?>
                            <div class="icon-wrap">
                                <svg class="icon icon_profile">
                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#profile"></use>
                                </svg>
                            </div>
                        </a>
                    </li>
                    <?php
                }
                ?>
                <?if( $arResult['USER']['UF_MODERATION_ACCESS'] == "true"  || $arResult['USER']['UF_MODERATION_ACCESS'] == "false"):?>
                <li>
                    <a href="<?= SITE_DIR . 'user/balance/' ?>">
                        <?= Loc::getMessage('USER_MENU_COMPONENT_BALANCE') ?>
                        <div class="icon-wrap">
                            <svg class="icon icon_wallet">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#wallet"></use>
                            </svg>
                        </div>
                    </a>
                </li>
                <?endif;?>
                <li>
                    <a href="<?= SITE_DIR . 'user/notifications/' ?>">
                        <?= Loc::getMessage('USER_MENU_COMPONENT_NOTIFICATIONS') ?>
                        <div class="icon-wrap">
                            <svg class="icon icon_alarmclock">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#alarmclock"></use>
                            </svg>
                        </div>
                    </a>
                </li>
                <?php
                if (intval(\Bitrix\Main\Config\Option::get(DSPI, 'chatEnabled'))) {
                    ?>
                    <li>
                        <a href="<?= SITE_DIR . 'user/chat/' ?>">
                            <?= Loc::getMessage('USER_MENU_COMPONENT_CHAT') ?>
                            <div class="icon-wrap">
                                <svg class="icon icon_alarmclock">
                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#chat"></use>
                                </svg>
                            </div>
                        </a>
                    </li>
                    <?php
                }
                ?>
                <li>
                    <a href="<?= SITE_DIR . 'user/tasks/' ?>">
                        <?= Loc::getMessage('USER_MENU_COMPONENT_TASKS') ?>
                        <div class="icon-wrap">
                            <svg class="icon icon_files">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#files"></use>
                            </svg>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="<?= SITE_DIR . 'user/blocked/' ?>">
                        <?= Loc::getMessage('USER_MENU_COMPONENT_BLOCKED') ?>
                        <div class="icon-wrap icon-wrap-blocked">
                            <svg class="icon icon_profile">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#profile"></use>
                            </svg>
                            <svg class="icon icon_blocked">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#blocked"></use>
                            </svg>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="<?= SITE_DIR . 'user/feedbacks/' ?>">
                        <?= Loc::getMessage('USER_MENU_COMPONENT_FEEDBACK') ?>
                        <div class="icon-wrap">
                            <svg class="icon icon_comment">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#comment"></use>
                            </svg>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="<?= SITE_DIR . 'user/favourites/' ?>">
                        <?= Loc::getMessage('USER_MENU_COMPONENT_FAVOURITES') ?>
                        <div class="icon-wrap">
                            <img src="<?= $this->GetFolder() ?>/-favourite-star.svg">
                        </div>
                    </a>
                </li>
                <li>
                    <a href="<?= SITE_DIR . 'user/exit/' ?>">
                        <?= Loc::getMessage('USER_MENU_COMPONENT_EXIT') ?>
                        <div class="icon-wrap">
                            <svg class="icon icon_exit">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#exit"></use>
                            </svg>
                        </div>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <script>
        BX.message({userMenuPath: '<?=$this->GetFolder()?>/ajax.php'});
    </script>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}