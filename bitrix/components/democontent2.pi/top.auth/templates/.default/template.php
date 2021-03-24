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

            <div class="logProfilStyle">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/logProfil.png">
            </div>
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
            <div class="logProfilStyle">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/logProfil.png">
            </div>
            <span>
                 <?= Loc::getMessage('TOP_AUTH_ENTERS') ?>
            </span>
        </a>
        <div class="popup popup-registration" id="popup-registration">
            <div class="tabs-head">
                <ul>
                    <li >
                        <a class="tab-lnk" href="#tab_reg1">
                            <?= Loc::getMessage('TOP_AUTH_REG') ?>
                        </a>
                    </li>
                    <li class="active">
                        <a class="tab-lnk" href="#tab_auth2">
                            <?= Loc::getMessage('TOP_AUTH_ENTERS') ?>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="popup-body">
                <div class="tabs-wrap">
                    <div class="tab " id="tab_reg1">
                        <form class="ajax-form" id="registration" method="post" enctype="multipart/form-data">
                            <div class="alert alert-info text-center">
                                <?= Loc::getMessage('TOP_AUTH_REG_MESSAGE') ?>
                            </div>

                           <?/* <div class="form-group">
                                <input class="form-control required password_reg" type="text" name="pass"
                                       placeholder="<?= Loc::getMessage('TOP_AUTH_PL_PASS') ?>"
                                       autocomplete="off">
                            </div>
                            */?>
                            <p class="pass_encorect"><?= Loc::getMessage('INPUT_PASS_REPEAT_NOT_CORRECT') ?></p>


                            <div class="block_more_reg_type-init">

                             <?/*<span class="type_init_reg">
                                 <?= Loc::getMessage('PLEASE_INTER_TYPE_INIT') ?>
                             </span>
                                */?>
                                <?/*
                                <div class="tabs-head-type">
                                    <ul>
                                        <li class="active">
                                            <a href="#typeTab1">
                                                <?= Loc::getMessage('EMAIL_INIT') ?>
                                            </a>
                                        </li>
                                       <li>
                                            <a href="#typeTab2">
                                                <?= Loc::getMessage('SMS_INIT') ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                */?>
                                <div class="tabsTypes">
                                    <div class="tabType active" id="typeTab1">
                                        <div class="form-group">
                                            <input class="form-control required code_se_email" type="text" name="email"
                                                   placeholder="<?= Loc::getMessage('TOP_AUTH_PL_EMAIL') ?>"
                                                   autocomplete="off">
                                        </div>
                                        <p class="phone_encorect"> </p>
                                    </div>
                                    <div class="tabType " id="typeTab2">

                                        <div class="form-group">
                                        <?php
                                        if (intval(\Bitrix\Main\Config\Option::get(DSPI, 'register_phone_required')) > 0) {
                                            ?>
                                            <div class="form-group">
                                                <input class="form-control required code_se_phone" type="phone" name="phone"
                                                       data-mask="+<?= \Bitrix\Main\Config\Option::get(DSPI, 'phone_code_mask') ?> (999) 999-99-99"
                                                       placeholder="<?= Loc::getMessage('TOP_AUTH_PL_PHONE') ?>"
                                                       autocomplete="off">
                                            </div>
                                            <p class="phone_encorect"> </p>
                                            <?php
                                        }
                                        ?>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="inp_code">
                                <div class="form-group">
                                    <input class="form-control required"
                                           type="text"
                                           name="code"
                                           placeholder="<?= Loc::getMessage('INPUT_CODE') ?>"

                                    >
                                </div>

                            </div>
                            <p class="repeat_send_code"><?= Loc::getMessage('INPUT_CODE_REPEAT') ?></p>
                            <p class="repeat_send_code_not_correct"><?= Loc::getMessage('INPUT_CODE_REPEAT_NOT_CORRECT') ?></p>



                            <div class="form-group">
                                <input class="checkbox" type="checkbox" name="" id="checkbox1" required checked="checked"/>
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
                            <div class="text-center registerBlockB">
                                <button class="btn btn-blue btn-submit" type="submit" >
                                    <?= Loc::getMessage('TOP_AUTH_BTN_REG') ?>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="active tab" id="tab_auth2">
                        <div class="tabs-wrap">
                            <div class="tab active" id="tab3">
                                <form  id="auth-form-b">

                                    <div class="tabs-head-type-auth" style="display: none;">
                                        <ul>
                                            <li class="active" data-typeOld="authEmail"  data-type="restorePasswordEmail" data-name="адрес электронной почты">
                                                <a href="#typeTabAuth1">
                                                    <?= Loc::getMessage('TOP_AUTH_PL_EMAIL_AUTH') ?>
                                                </a>
                                            </li>
                                            <li data-typeOld="authPhone" data-type="restorePasswordPhone" data-name="ваш телефонный номер" >
                                                <a href="#typeTabAuth2">
                                                    <?= Loc::getMessage('TOP_AUTH_PL_PHONE_AUTH') ?>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="tabsTypes">
                                        <div class="tabType active" id="typeTabAuth1">
                                            <div class="form-group">
                                                <input class="form-control required" type="text" name="authEmail"
                                                       placeholder="<?= Loc::getMessage('TOP_AUTH_PL_EMAIL') ?>"
                                                       autocomplete="off">
                                            </div>

                                        </div>
                                        <div class="tabType " id="typeTabAuth2">
                                            <div class="form-group">
                                                <input class="form-control required" type="text" name="authPhone"
                                                       placeholder="<?= Loc::getMessage('TOP_AUTH_PL_PHONE') ?>"
                                                       autocomplete="off">
                                            </div>
                                        </div>

                                    </div>


                                    <div class="form-group ">
                                        <input class="form-control required pass_see_more authPassForUser " type="password" name="authPassword"
                                               placeholder="<?= Loc::getMessage('TOP_AUTH_PL_PAS') ?>"
                                               autocomplete="off">

                                        <span id="eye-see-pass" class="eye-position-modal">
                                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                              <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                              <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                            </svg>
                                                <svg style="display: none;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                  <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                                  <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
                                                  <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                                                </svg>
                                        </span>
                                    </div>
                                    <p class="errorMailOrPass" style="display: none;color: red;"></p>
                                    <div class="form-group text-right forget-password-lnk">
                                        <a href="#tab4"  class="iForrgotPass" >
                                            <?= Loc::getMessage('TOP_AUTH_FORGET') ?>
                                        </a>
                                    </div>
                                    <div class="text-center">
                                        <div class="btn btn-blue clickAuth">
                                            <?= Loc::getMessage('TOP_AUTH_BTN_ENTERS') ?>
                                        </div>
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
                                    <p class="forgot_auth" style="text-align: center"></p>
                                    <div class="form-group text-right forget-password-lnk">
                                        <a href="#tab3">
                                            <?= Loc::getMessage('TOP_AUTH_BACK') ?>
                                        </a>
                                    </div>

                                    <div class="text-center">
                                        <button class="btn btn-blue btn-submit btn-submit-restore">
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