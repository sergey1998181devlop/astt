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
                        <? endif ?>
                        <div class="sorty-panel tabs-head">
                            <ul>
                                <li class="active">
                                    <a href="#tab-profile1">
                                        <?= Loc::getMessage('USER_SETTINGS_COMPONENT_TABS_1') ?>
                                    </a>
                                </li>
                                <? if ($arResult['USER']['UF_DSPI_EXECUTOR']): ?>
                                    <li>
                                        <a href="#tab-profile2">
                                            <?= Loc::getMessage('USER_SETTINGS_COMPONENT_TABS_2') ?>
                                        </a>
                                    </li>
                                <? endif ?>
                            </ul>
                        </div>
                        <div class="tabs-wrap">
                            <div class="tab active" id="tab-profile1">
                                <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="post"
                                      enctype="multipart/form-data">
                                    <div class="h4">
                                        <?= Loc::getMessage('USER_SETTINGS_COMPONENT_AVA_TITLE') ?>
                                    </div>
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
                                                <div class="object-fit-container">
                                                    <img id="ava-image" src="<?= $arResult['USER']['AVATAR']['src'] ?>""
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
                                                <input class="form-control required" type="text" name="name"
                                                       value="<?= $arResult['USER']['NAME'] ?>"
                                                       placeholder="<?= Loc::getMessage('USER_SETTINGS_COMPONENT_PERSONAL_NAME') ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-xxs-12">
                                            <div class="form-group">
                                                <label class="bold"><?= Loc::getMessage('USER_SETTINGS_COMPONENT_PERSONAL_LASTNAME') ?></label>
                                                <input class="form-control" type="text" name="lastName"
                                                       value="<?= $arResult['USER']['LAST_NAME'] ?>"
                                                       placeholder="<?= Loc::getMessage('USER_SETTINGS_COMPONENT_PERSONAL_LASTNAME') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="h4"><?= Loc::getMessage('USER_SETTINGS_COMPONENT_CONTACTS') ?></div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="bold"><?= Loc::getMessage('USER_SETTINGS_COMPONENT_CONTACTS_PHONE') ?></label>
                                                <input class="form-control required" type="phone" name="phone"
                                                       data-mask="+<?= \Bitrix\Main\Config\Option::get(DSPI, 'phone_code_mask') ?> (999) 999-99-99"
                                                       value="<?= \Democontent2\Pi\Utils::formatPhone($arResult['USER']['PERSONAL_PHONE']) ?>"
                                                       placeholder="<?= Loc::getMessage('USER_SETTINGS_COMPONENT_CONTACTS_PHONE') ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="bold"><?= Loc::getMessage('USER_SETTINGS_COMPONENT_CONTACTS_EMAIL') ?></label>
                                                <input class="form-control" type="email"
                                                       value="<?= $arResult['USER']['EMAIL'] ?>"
                                                       placeholder="<?= Loc::getMessage('USER_SETTINGS_COMPONENT_CONTACTS_EMAIL') ?>"
                                                       disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
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
                                        </div>
                                        <div class="col-sm-6">

                                        </div>
                                    </div>
                                    <div class="form-group btn-wrap clearfix">
                                        <button class="btn-submit btn btn-green left">
                                            <?= Loc::getMessage('USER_SETTINGS_COMPONENT_BTN_SUBMIT') ?>
                                        </button>

                                    </div>
                                </form>
                                <div class="form-group">
                                    <a href="#" class="js-lnk btn-transform-pas">
                                        <?= Loc::getMessage('USER_SETTINGS_COMPONENT_BTN_TRANSFORM_PAS') ?>
                                    </a>
                                </div>

                                <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="post"
                                      id="transform-password"
                                      class="transform-password">
                                    <div class="row">
                                        <div class="col-sm-6 col-xxs-12">
                                            <div class="form-group">
                                                <label class="bold"><?= Loc::getMessage('USER_SETTINGS_COMPONENT_PASSWORD_NEW') ?></label>
                                                <input type="password" name="password" required
                                                       class="password required pass1 form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xxs-12">
                                            <div class="form-group">
                                                <label class="bold"><?= Loc::getMessage('USER_SETTINGS_COMPONENT_PASSWORD_REPIT') ?></label>
                                                <input type="password" name="confirmPassword" required
                                                       class="password required pass2 form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right clearfix">
                                        <button class="btn-submit btn btn-green left">
                                            <?= Loc::getMessage('USER_SETTINGS_COMPONENT_PASSWORD_SAVE') ?>
                                        </button>
                                        <a href="#" class="js-lnk">
                                            <?= Loc::getMessage('USER_SETTINGS_COMPONENT_PASSWORD_HIDE') ?>
                                        </a>
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
                </div>
            </div>
        </div>
    </div>

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