<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 15.01.2019
 * Time: 10:57
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}
\Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/lib/dropzone/dropzone.css');
\Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/dropzone/dropzone.js');
\Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery-ui.min.js');

$currencyName = \Bitrix\Main\Config\Option::get(DSPI, 'currency_name');
$registrationFee = intval(\Bitrix\Main\Config\Option::get(DSPI, 'registration_fee'));
$maxImageSize = intval(\Bitrix\Main\Config\Option::get(DSPI, 'max_file_size'));
$maxFiles = intval(\Bitrix\Main\Config\Option::get(DSPI, 'max_file_size'));
$safeCrowEnabled = false;
$profile = [];

if (count($arResult['PROFILE']) > 0) {
    if (strlen($arResult['PROFILE']['UF_DATA']) > 0) {
        $profile = unserialize($arResult['PROFILE']['UF_DATA']);
    }
}

if (strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiKey')) > 0
    && strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
    $safeCrowEnabled = true;
}

?>
    <div class="page-content">
        <div class="wrapper">
            <div class="page-title">
                <h1>
                    <?= Loc::getMessage('USER_SETTINGS_COMPONENT_H1') ?>
                </h1>
            </div>
            <div class="row">
                <div class="col-sm-4 col-md-3 col-xxs-12">
                    <?php
                    $APPLICATION->IncludeComponent(
                        'democontent2.pi:user.menu',
                        ''
                    );
                    ?>
                </div>
                <div class="col-sm-8 col-xxs-12">
                    <div class="white-block profile-content">

                        <? if (!intval($arResult['USER']['UF_DSPI_EXECUTOR'])): ?>
                            <?if($arResult['MODERATION_CURRENT_USER'] == "true" && $arResult['COMPANY'][0]['UF_STATUS_MODERATION'] == 2):?>
                                <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="post" class="become-executor">
                                <input type="hidden" name="setExecutor" value="1">
                                <div class="tbl">
                                    <div class="tbc">
                                        <div class="alert alert-info">
                                            <?php
                                            if ($registrationFee) {
                                                echo Loc::getMessage(
                                                    'USER_SETTINGS_COMPONENT_BECOME_EXECUTOR_PAY',
                                                    [
                                                        '#SUM#' => $registrationFee,
                                                        '#CURRENCY#' => $currencyName
                                                    ]
                                                );
                                            } else {
                                                echo Loc::getMessage('USER_SETTINGS_COMPONENT_BECOME_EXECUTOR');
                                            }
                                            ?>
                                        </div>
                                    </div>


                                    <div class="tbc">
                                        <button class="btn-submit btn btn-green right">
                                            <?= Loc::getMessage('USER_SETTINGS_COMPONENT_BTN_EXECUTOR') ?>
                                        </button>
                                    </div>

                                </div>
                            </form>
                            <?endif;?>
                        <? endif ?>

                        <div class="tabs-wrap">
                            <div class="tab active" id="tab-profile1">
                                <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="post"
                                      enctype="multipart/form-data">
                                    <?if( $arResult['USER']['UF_MODERATION_ACCESS'] == "true" || $arResult['USER']['UF_MODERATION_ACCESS'] == "false"):?>
                                    <div class="h4">
                                        <?= Loc::getMessage('USER_SETTINGS_COMPONENT_AVA_TITLE') ?>
                                    </div>
<?//debmes($arResult['USER']);?>
                                    <div class="form-group">
                                        <div class="user-ava ava-inp-file">
                                            <svg class="icon icon_user">
                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#user"></use>
                                            </svg>
                                            <input type="file" accept="image/x-png,image/jpeg,image/png,image/gif"
                                                   name="photo">
                                            <?php
                                            if (isset($arResult['USER']['AVATAR'])) {
                                                ?>
                                                <div class="object-fit-container image_preResult">
                                                    <?$new_password = randString(7);?>
                                                    <img id="ava-image" src="<?= $arResult['USER']['AVATAR']['src'] ?>?rand=<?=$new_password?>"
                                                    alt=""
                                                    data-object-fit="cover"
                                                    data-object-position="50% 50%">
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="error-message">
                                            <?= Loc::getMessage(
                                                'USER_SETTINGS_COMPONENT_AVA_ERROR',
                                                array(
                                                    '#SIZE#' => $maxImageSize
                                                )
                                            ) ?>
                                        </div>
                                    </div>
                                    <?endif;?>

                                    <div class="h4">
                                        <?= Loc::getMessage('USER_SETTINGS_COMPONENT_PERSONAL_DATA') ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-6 col-xxs-12">
                                            <div class="form-group">
                                                <label class="bold">
                                                    <?= Loc::getMessage('USER_SETTINGS_COMPONENT_PERSONAL_NAME') ?>
                                                    <span class="required">*</span>
                                                </label>
                                                <input class="form-control required" type="text" name="name" required
                                                       <?if( $arResult['USER']['UF_MODERATION_ACCESS'] == "true" || $arResult['USER']['UF_MODERATION_ACCESS'] == "false"):?>

                                                       <?else:;?>
                                                            disabled
                                                       <?endif;?>

                                                       value="<?= $arResult['USER']['NAME'] ?>"
                                                       placeholder="<?= Loc::getMessage('USER_SETTINGS_COMPONENT_PERSONAL_NAME') ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-xxs-12">
                                            <div class="form-group">
                                                <label class="bold"><?= Loc::getMessage('USER_SETTINGS_COMPONENT_PERSONAL_LASTNAME') ?></label>
                                                <input class="form-control" type="text" name="lastName"
                                                    <?if( $arResult['USER']['UF_MODERATION_ACCESS'] == "true" || $arResult['USER']['UF_MODERATION_ACCESS'] == "false"):?>

                                                    <?else:;?>
                                                        disabled
                                                    <?endif;?>
                                                       value="<?= $arResult['USER']['LAST_NAME'] ?>"
                                                       placeholder="<?= Loc::getMessage('USER_SETTINGS_COMPONENT_PERSONAL_LASTNAME') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="reedit_user_data">
                                        <?if( $arResult['USER']['UF_MODERATION_ACCESS'] == "true" || $arResult['USER']['UF_MODERATION_ACCESS'] == "false"):?>
                                           <?/* <div class="h4"><?= Loc::getMessage('USER_SETTINGS_COMPONENT_CONTACTS') ?></div> */?>
                                        <?endif;?>
                                        <div class="row">


                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label class="bold"><?= Loc::getMessage('USER_SETTINGS_COMPONENT_CONTACTS_PHONE') ?></label>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <input class="form-control required emailUpAc-phone" type="phone" name="phone" required
                                                                   data-mask="+<?= \Bitrix\Main\Config\Option::get(DSPI, 'phone_code_mask') ?> (999) 999-99-99"
                                                                   value="<?= \Democontent2\Pi\Utils::formatPhone($arResult['USER']['PERSONAL_PHONE']) ?>"
                                                                   placeholder="<?= Loc::getMessage('USER_SETTINGS_COMPONENT_CONTACTS_PHONE') ?>">
                                                        </div>





                                                        <?if($arResult['USER']['UF_MODERATION_ACCESS'] == "true" || $arResult['USER']['UF_MODERATION_ACCESS'] == "false"):?>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <?//модератор  - может обновить номер и мыло / значит ставим на кнопки обновить и обновить?>
                                                                    <?
                                                                    //если пуст номер / тогда скрываю и жду пока не введут 10 символов
                                                                    ?>
                                                                    <?if(empty($arResult['USER']['PERSONAL_PHONE'])):?>
                                                                        <button class="btn-submit btn btn-green left  pers_data update__account_data" style="display: none" data-type-check="phone" type="submit">
                                                                            Подтвердить номер
                                                                        </button>
                                                                    <?else:?>
                                                                        <button class="btn-submit btn btn-green left  pers_data update__account_data" data-type-check="phone" type="submit">
                                                                            Обновить номер
                                                                        </button>
                                                                    <?endif;?>

                                                                </div>
                                                            </div>
                                                        <?else:?>
                                                            <?if( $arResult['USER']['UF_CONFIRMED'] == "false"):?>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">

                                                                        <button class="btn-submit btn btn-green left  pers_data update__account_data" data-type-check="phone" type="submit">
                                                                            <?= Loc::getMessage('USER_SETTINGS_SAVE_PHONE') ?>
                                                                        </button>

                                                                    </div>
                                                                </div>
                                                            <?endif;?>
                                                        <?endif;?>

                                                    </div>

                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label class="bold"><?= Loc::getMessage('USER_SETTINGS_COMPONENT_CONTACTS_EMAIL') ?></label>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <input class="form-control emailUpAc-mail" type="email" name="authEmail"
                                                                   value="<?= $arResult['USER']['EMAIL'] ?>"  required
                                                                   placeholder="<?= Loc::getMessage('USER_SETTINGS_COMPONENT_CONTACTS_EMAIL') ?>"
                                                            >
                                                        </div>
                                                        <div class="col-sm-6" style="display: none">
                                                            <div class="form-group">

                                                                <button class="btn-submit btn btn-green left  pers_data update__account_data" data-type-check="mail" type="submit">
                                                                    <?= Loc::getMessage('USER_SETTINGS_SAVE_MAIL') ?>
                                                                </button>

                                                            </div>
                                                        </div>
                                                        <?if($arResult['USER']['UF_MODERATION_ACCESS'] == "true" || $arResult['USER']['UF_MODERATION_ACCESS'] == "false"):?>
                                                            <div class="col-sm-6" >
                                                                <div class="form-group">

                                                                    <button class="btn-submit btn btn-green left  pers_data update__account_data" data-type-check="mail" type="submit">
                                                                        Обновить email
                                                                    </button>

                                                                </div>
                                                            </div>
                                                        <?endif;?>
                                                    </div>

                                                </div>
                                            </div>


                                        </div>
                                    </div>




                                <?if( $arResult['USER']['UF_MODERATION_ACCESS'] == "true" || $arResult['USER']['UF_MODERATION_ACCESS'] == "false"):?>
                                    <div class="row">
                                        <input type="hidden" name="update_personal_dataNameLastN" value="Y">
                                        <button class="btn-submit btn btn-green left  pers_data" type="submit">
                                            <?= Loc::getMessage('USER_SETTINGS_COMPONENT_BTN_SUBMIT') ?>
                                        </button>
                                    </div>
                                <?endif;?>



                                </form>
                                <div class="row ">
                                    <?/*   <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="bold">
                                                    <?= Loc::getMessage('USER_SETTINGS_COMPONENT_CITY') ?>
                                                </label>
                                                <select name="city" style="width: 100%;" class="js-select">
                                                    <?php
                                                    foreach ($arResult['CITIES'] as $city) {
                                                        $selected = '';
                                                        if ($city['id'] == $arResult['USER']['UF_DSPI_CITY']) {
                                                            $selected = ' selected';
                                                        }
                                                        ?>
                                                        <option value="<?= $city['id'] ?>"<?= $selected ?>><?= $city['name'] ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div> */?>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <a href="#" class="js-lnk btn-transform-pas" style="display: block;margin-top: 30px;text-align: center">
                                                <?= Loc::getMessage('USER_SETTINGS_COMPONENT_BTN_TRANSFORM_PAS') ?>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                                <div class="new_pass" style="display:none;padding-bottom: 15px;padding-top: 20px" >
                                    <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="post"
                                          id="transform-password" style="display: block"
                                          class="transform-password">
                                        <div class="row">


                                            <div class="col-sm-12 col-xxs-12">
                                                <div class="col-sm-6 col-xxs-12">
                                                    <div class="form-group">
                                                        <label class="bold"><?= Loc::getMessage('USER_SETTINGS_COMPONENT_PASSWORD_OLD') ?></label>
                                                        <input type="password" name="OldPass"
                                                               class="  pass3 form-control required authPassForUser">
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
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xxs-12">
                                                <div class="form-group">
                                                    <label class="bold"><?= Loc::getMessage('USER_SETTINGS_COMPONENT_PASSWORD_NEW') ?></label>
                                                    <input type="password" name="NewPass"
                                                           class="  pass3 form-control required authPassForUser">
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
                                            </div>
                                            <div class="col-sm-6 col-xxs-12">
                                                <div class="form-group">
                                                    <label class="bold"><?= Loc::getMessage('USER_SETTINGS_COMPONENT_PASSWORD_REPIT') ?></label>
                                                    <input type="password" name="NewPass_REPEAT"
                                                           class="  pass3 form-control required authPassForUser">
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
                                            </div>
                                            <input type="hidden" name="changePass" value="Y">
                                        </div>
                                        <div class="text-right clearfix">

                                            <a href="#" class="js-lnk">
                                                <?= Loc::getMessage('USER_SETTINGS_COMPONENT_PASSWORD_HIDE') ?>
                                            </a>
                                            <button class="btn-submit btn btn-green left" type="submit">
                                                <?= Loc::getMessage('USER_SETTINGS_COMPONENT_PASSWORD_SAVE') ?>
                                            </button>

                                        </div>
                                    </form>
                                </div>






                            <? if ($arResult['USER']['UF_DSPI_EXECUTOR']): ?>
                                <div class="tab" id="tab-profile2">
                                    <form method="post" action="<?= $APPLICATION->GetCurPage(false) ?>">
                                        <div class="h4">
                                            <?= Loc::getMessage('USER_SETTINGS_COMPONENT_SPECIALIZATION_TTL') ?>
                                        </div>
                                        <div class="alert alert-info">
                                            <?= Loc::getMessage('USER_SETTINGS_COMPONENT_SUBSCRIBE_DESCRIPTION') ?>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <div class="specialization-list">
                                                <ul>
                                                    <?php
                                                    $i = 0;
                                                    foreach ($arResult['MENU'] as $k => $item) {
                                                        if (!count($item['items'])) {
                                                            continue;
                                                        }
                                                        $i++;
                                                        ?>
                                                        <li>
                                                            <input class="checkbox" type="checkbox"
                                                                   id="catalog-<?= $i ?>">
                                                            <label class="form-checkbox" for="catalog-<?= $i ?>">
                                                                <span class="icon-wrap">
                                                                    <svg class="icon icon_checkmark">
                                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                                                    </svg>
                                                                </span>
                                                                <?= $item['name'] ?>
                                                            </label>
                                                            <span class="icon-angle-wrap">
                                                                 <svg class="icon icon_angle">
                                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#angle"></use>
                                                                    </svg>
                                                            </span>
                                                            <ul>
                                                                <?php
                                                                foreach ($item['items'] as $subitem) {
                                                                    $i++;
                                                                    $checked = '';

                                                                    if (isset($arResult['SUBSCRIPTIONS'][$subitem['id']])) {
                                                                        $checked = ' checked';
                                                                    }
                                                                    ?>
                                                                    <li>
                                                                        <input class="checkbox" type="checkbox"
                                                                               name="specialization[<?= $k ?>][]"
                                                                               value="<?= $subitem['id'] ?>"
                                                                               id="catalog-<?= $i ?>"<?= $checked ?>>
                                                                        <label class="form-checkbox"
                                                                               for="catalog-<?= $i ?>">
                                                                                <span class="icon-wrap">
                                                                                    <svg class="icon icon_checkmark">
                                                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                                                                    </svg>
                                                                                </span>
                                                                            <?= $subitem['name'] ?>
                                                                        </label>
                                                                    </li>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </ul>
                                                        </li>
                                                        <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="pr-desc" class="bold">
                                                <?= Loc::getMessage('USER_SETTINGS_COMPONENT_DESCRIPTION') ?>
                                                <span class="required">*</span>
                                            </label>
                                            <textarea id="pr-desc" class="form-control required" required
                                                      name="executorDescription" cols="30"
                                                      rows="10"><?= (isset($profile['description']) ? $profile['description'] : '') ?></textarea>
                                        </div>
                                        <div class="btn-wrap text-right">
                                            <button class="btn btn-green btn-submit" type="submit">
                                                <?= Loc::getMessage('USER_SETTINGS_COMPONENT_BTN_SAVE') ?>
                                            </button>
                                        </div>
                                    </form>
                                    <? if (!count($arResult['CARDS']) && $safeCrowEnabled): ?>
                                        <form action="<?= $APPLICATION->GetCurPage() ?>" method="post">
                                            <div class="h4">
                                                <?= Loc::getMessage('USER_SETTINGS_COMPONENT_SECURITY_TTL') ?>
                                            </div>
                                            <div class="form-group">
                                                <div class="alert alert-info">
                                                    <?= Loc::getMessage('USER_SETTINGS_COMPONENT_SECURITY_INFO') ?>
                                                </div>
                                            </div>
                                            <input type="hidden" name="addCard" value="1">
                                            <div class="form-group">
                                                <button class="btn btn-green btn-security">
                                                    <?= Loc::getMessage('USER_SETTINGS_COMPONENT_BTN_ADD_CARD') ?>
                                                </button>
                                            </div>
                                        </form>
                                    <? endif ?>
                                    <? if (!$arResult['USER']['UF_DSPI_DOCUMENTS']): ?>
                                        <form class="form-group" enctype="multipart/form-data"
                                              action="<?= $APPLICATION->GetCurPage(false) ?>" method="post">
                                            <input type="hidden" name="type" value="verification">
                                            <div class="h4">
                                                <?= Loc::getMessage('USER_SETTINGS_COMPONENT_VERIFICATION_TTL') ?>
                                            </div>
                                            <div class="error-message">
                                                <?= Loc::getMessage('USER_SETTINGS_COMPONENT_VERIFICATION_ERROR') ?>
                                            </div>
                                            <div class="form-group">
                                                <div id="files-list" class="files"></div>
                                                <div id="btn-file" class="btn btn-sm btn-file ">
                                                    <span><?= Loc::getMessage('USER_SETTINGS_COMPONENT_BTN_ADD_FILE') ?></span>
                                                    <input accept="image/x-png,image/jpeg" type="file"
                                                           name="__documents[]">
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-green">
                                                <?= Loc::getMessage('USER_SETTINGS_COMPONENT_VERIFICATION_BTN') ?>
                                            </button>
                                        </form>
                                    <? endif ?>
                                </div>
                            <? endif ?>
                        </div>
                    </div>

                    <?if( $arResult['USER']['UF_MODERATION_ACCESS'] == "true" || $arResult['USER']['UF_MODERATION_ACCESS'] == "false"):?>
                        <?//если это модератор и у него заполнен номер и имя , но нету компании?>
                        <?if(!empty($arResult['USER']['PERSONAL_PHONE']) && !empty($arResult['USER']['NAME'])):?>
                            <?if(empty($arResult['COMPANY'][0]['ID'])):?>
                                <div class="alert alert-info" role="alert">
                                    Вы можете заполнить информацию о своей компании и получить дополнительные возможности на портале
                                </div>
                            <?endif;?>
                        <?endif;?>
                    <?endif;?>
                </div>
            </div>
        </div>
    </div>

    <?if(!empty($_REQUEST['changePass'])):?>
    <?
        $class = 'alert alert-danger danger-notific';
        $message = '';

        switch ($_REQUEST['changePass']) {
            case 'OldInputError':
                $message = 'Старый пароль введен не верно';
                break;
            case 'SuccessUpdatePass':
                $message = 'Пароль успешно обновлен';
                $class = 'alert alert-success danger-notific';
                break;
            case 'ERROR_UPDATE':
                $message = 'Ошибка обновления';
                break;
        }
    ?>
        <div class="popup popup-registration" id="popup-notification-setting">

            <div class="popup-body">
                <div class="tabs-wrap">
                    <div  class="<?=$class?>" role="alert">
                        <strong><?=$message?></strong>
                    </div>
                </div>
            </div>
        </div>
    <script>
        $.fancybox.open([
            {
                src: '#popup-notification-setting'
            }]);

        const func = () => {
            window.history.replaceState({}, document.title, "/user/" + "settings/");
            $.fancybox.close([
                {
                    src: '#popup-notification-setting'
                }]);
        };
        setTimeout(func, 4000);
    </script>

    <?endif;?>
    <script>

        var maxSizeAva = parseInt(<?=($maxImageSize)?>),
            maxFiles = parseInt('<?=$maxFiles?>'),
            getCurPath = '<?=$APPLICATION->GetCurPage(false)?>',
            dropzoneMainImage = '<?=Loc::getMessage('USER_SETTINGS_COMPONENT_DROPZONE_MAIN_IMAGE')?>',
            templatePath = '<?=SITE_TEMPLATE_PATH?>';
    </script>
    </script>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}