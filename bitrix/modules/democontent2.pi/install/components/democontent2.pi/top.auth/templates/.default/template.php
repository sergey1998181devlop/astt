<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.01.2019
 * Time: 09:38
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}
$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
if ($arResult['AUTHORIZED']) {
    ?>
    <li class="login">
        <a href="<?= SITE_DIR . 'user/settings/' ?>">
            <svg class="icon icon_profile">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#profile"></use>
            </svg>
            <span>
                <?= $arResult['USER']['EMAIL'] ?>
            </span>
        </a>
    </li>
    <?
} else {
    ?>
    <?php
    if (\Bitrix\Main\Config\Option::get(DSPI, 'reCaptchaPublic')
        && \Bitrix\Main\Config\Option::get(DSPI, 'reCaptchaSecret')) {
        ?>
        <script src="https://www.google.com/recaptcha/api.js?onload=onLoadReCaptcha&render=explicit" async
                defer></script>
        <?php
    }
    ?>
    <li class="login">
        <a href="#popup-registration" data-fancybox>
            <svg class="icon icon_profile">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#profile"></use>
            </svg>
            <span>
                 <?= Loc::getMessage('TOP_AUTH_ENTERS') ?>
            </span>
        </a>
        <div class="popup popup-registration" id="popup-registration">
            <div class="tabs-head">
                <ul>
                    <li class="active">
                        <a href="#tab1">
                            <?= Loc::getMessage('TOP_AUTH_REG') ?>
                        </a>
                    </li>
                    <li>
                        <a href="#tab2">
                            <?= Loc::getMessage('TOP_AUTH_ENTERS') ?>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="popup-body">
                <div class="tabs-wrap">
                    <div class="tab active" id="tab1">
                        <form class="ajax-form" id="registration">
                            <div class="alert alert-info text-center">
                                <?= Loc::getMessage('TOP_AUTH_REG_MESSAGE') ?>
                            </div>
                            <div class="form-group">
                                <input class="form-control required" type="text" name="name"
                                       placeholder="<?= Loc::getMessage('TOP_AUTH_PL_NAME') ?>"
                                       autocomplete="off">
                            </div>
                            <div class="form-group">
                                <input class="form-control required" type="text" name="email"
                                       placeholder="<?= Loc::getMessage('TOP_AUTH_PL_EMAIL') ?>"
                                       autocomplete="off">
                            </div>
                            <?php
                            if (intval(\Bitrix\Main\Config\Option::get(DSPI, 'register_phone_required')) > 0) {
                                ?>
                                <div class="form-group">
                                    <input class="form-control required" type="phone" name="phone"
                                           data-mask="+<?= \Bitrix\Main\Config\Option::get(DSPI, 'phone_code_mask') ?> (999) 999-99-99"
                                           placeholder="<?= Loc::getMessage('TOP_AUTH_PL_PHONE') ?>"
                                           autocomplete="off">
                                </div>
                                <?php
                            }
                            ?>
                            <div class="form-group">
                                <input class="checkbox" type="checkbox" name="" id="checkbox1" checked="checked"/>
                                <label class="form-checkbox" for="checkbox1">
                                    <span class="icon-wrap">
                                        <svg class="icon icon_checkmark">
                                          <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                        </svg>
                                    </span>
                                    <?php
                                    echo Loc::getMessage(
                                        'TOP_AUTH_AGREEMENT',
                                        array(
                                            '#SITE_DIR#' => SITE_DIR
                                        )
                                    )
                                    ?>
                                </label>
                            </div>
                            <div class="form-group">
                                <div id="registerReCaptcha"></div>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-blue btn-submit">
                                    <?= Loc::getMessage('TOP_AUTH_BTN_REG') ?>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="tab" id="tab2">
                        <div class="tabs-wrap">
                            <div class="tab active" id="tab3">
                                <form method="post" action="<?= $APPLICATION->GetCurPage() ?>">
                                    <div class="form-group">
                                        <input class="form-control required" type="text" name="authEmail"
                                               placeholder="<?= Loc::getMessage('TOP_AUTH_PL_EMAIL') ?>"
                                               autocomplete="off">
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control required" type="password" name="authPassword"
                                               placeholder="<?= Loc::getMessage('TOP_AUTH_PL_PAS') ?>"
                                               autocomplete="off">
                                    </div>
                                    <div class="form-group text-right forget-password-lnk">
                                        <a href="#tab4">
                                            <?= Loc::getMessage('TOP_AUTH_FORGET') ?>
                                        </a>
                                    </div>
                                    <div class="text-center">
                                        <button class="btn btn-blue btn-submit">
                                            <?= Loc::getMessage('TOP_AUTH_BTN_ENTERS') ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab" id="tab4">
                                <form class="restore-password ajax-form" action="" method="post">
                                    <div class="form-group">
                                        <input class="form-control required" type="email" name="restorePasswordEmail"
                                               placeholder="<?= Loc::getMessage('TOP_AUTH_PL_EMAIL') ?>"
                                               autocomplete="off">
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control required" type="phone" name="restorePasswordPhone"
                                               data-mask="+<?= \Bitrix\Main\Config\Option::get(DSPI, 'phone_code_mask') ?> (999) 999-99-99"
                                               placeholder="<?= Loc::getMessage('TOP_AUTH_PL_PHONE') ?>"
                                               autocomplete="off">
                                    </div>
                                    <div class="form-group text-right forget-password-lnk">
                                        <a href="#tab3">
                                            <?= Loc::getMessage('TOP_AUTH_BACK') ?>
                                        </a>
                                    </div>
                                    <div class="text-center">
                                        <button class="btn btn-blue btn-submit">
                                            <?= Loc::getMessage('TOP_AUTH_BTN_RECOVER') ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </li>
    <script>
        var __authAjaxPath = '<?=$this->GetFolder()?>/ajax.php',
            __registerSuccessMessage = '<?=Loc::getMessage('TOP_AUTH_SEND_' . ((strlen(\Bitrix\Main\Loader::includeModule(DSPI, 'defaultSmsGate')) > 0 && intval(\Bitrix\Main\Config\Option::get(DSPI, 'register_phone_required')) > 0) ? 'SMS' : 'EMAIL'))?>',
            __registerErrorMessage = '<?=Loc::getMessage('TOP_AUTH_REGISTER_ERROR')?>',
            __restorePasswordSuccessMessage = '<?=Loc::getMessage('TOP_AUTH_RESTORE_PASSWORD_SUCCESS')?>',
            __restorePasswordErrorMessage = '<?=Loc::getMessage('TOP_AUTH_RESTORE_PASSWORD_ERROR')?>',
            __authErrorReason = '<?=((isset($arResult['ERROR'])) ? $arResult['ERROR'] : '')?>',
            __reCaptchaInit = 0,
            __needAuth = parseInt(<?=($request->get('needAuth') && intval($request->get('needAuth')) == 1) ? 1 : 0?>),
            __phoneRequired = parseInt(<?=intval(\Bitrix\Main\Config\Option::get(DSPI, 'register_phone_required'))?>);
        <?php
        if (\Bitrix\Main\Config\Option::get(DSPI, 'reCaptchaPublic')
        && \Bitrix\Main\Config\Option::get(DSPI, 'reCaptchaSecret')) {
        ?>
        __reCaptchaInit = 1;
        var reCaptchaPublicKey = '<?=\Bitrix\Main\Config\Option::get(DSPI, 'reCaptchaPublic')?>';
        var registerReCaptcha;
        var onLoadReCaptcha = function () {
            registerReCaptcha = grecaptcha.render('registerReCaptcha', {
                'sitekey': reCaptchaPublicKey,
                'theme': 'light'
            });
        };
        <?php
        }
        ?>
    </script>
    <?
}

if (method_exists($this, 'createFrame')) {
    $frame->end();
}