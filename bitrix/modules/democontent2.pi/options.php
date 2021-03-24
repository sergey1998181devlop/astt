<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 28.09.2018
 * Time: 14:41
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

IncludeModuleLangFile(__FILE__);

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

$module_id = "democontent2.pi";

$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);

if ($POST_RIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

\Bitrix\Main\Loader::includeModule($module_id);

if ($_POST) {
    if ($_POST['settings']) {
        $courierIblocks = \Bitrix\Main\Web\Json::encode([]);

        foreach ($_POST['settings'] as $k => $v) {
            switch ($k) {
                case 'startBalance':
                case 'free_limit':
                case 'upper_limit_cost':
                case 'quickly_option_period':
                case 'quickly_option_cost':
                case 'max_files':
                case 'item_period':
                case 'max_file_size':
                case 'max_image_size':
                case 'moderation_new':
                case 'moderation_update':
                case 'phone_code_mask':
                case 'company_cost':
                case 'chatEnabled':
                case 'paypal_mode':
                case 'apiEnabled':
                case 'apiOnlyHttps':
                case 'registration_fee':
                case 'response_checklists':
                case 'register_phone_required':
                case 'contacts_open':
                    COption::SetOptionString($module_id, $k, intval($v));
                    break;
                case 'defaultMap':
                    switch ($v) {
                        case 'google':
                            COption::SetOptionString($module_id, $k, $v);
                            break;
                        default:
                            COption::SetOptionString($module_id, $k, 'yandex');
                    }
                    break;
                case 'courierIblocks':
                    if (is_array($v)) {
                        if (count($v)) {
                            $courierIblocks = \Bitrix\Main\Web\Json::encode($v);

                            foreach ($v as $iblId) {
                                \Democontent2\Pi\Utils::checkCourierIblock($iblId);
                            }
                        }
                    }
                    break;
                default:
                    COption::SetOptionString($module_id, $k, trim($v));
            }
        }

        COption::SetOptionString($module_id, 'courierIblocks', $courierIblocks);
    }

    LocalRedirect('/bitrix/admin/settings.php?mid=' . $module_id . '&lang=ru');
}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

$tab = 0;
$aTabs = array();
$aTabs[] = array("DIV" => "edit" . $tab++, "TAB" => GetMessage("SYSTEM_SETTINGS"), "ICON" => "", "TITLE" => "");
$aTabs[] = array("DIV" => "edit" . $tab++, "TAB" => GetMessage("PAYMENTS_SETTINGS"), "ICON" => "", "TITLE" => "");
$aTabs[] = array("DIV" => "edit" . $tab++, "TAB" => GetMessage("SMS_SETTINGS"), "ICON" => "", "TITLE" => "");
$aTabs[] = array("DIV" => "edit" . $tab++, "TAB" => GetMessage("CHAT_SETTINGS"), "ICON" => "", "TITLE" => "");
$aTabs[] = array("DIV" => "edit" . $tab++, "TAB" => 'REST API', "ICON" => "", "TITLE" => "");
$aTabs[] = array("DIV" => "edit" . $tab++, "TAB" => GetMessage('PI_APP_SETTINGS'), "ICON" => "", "TITLE" => "");
$aTabs[] = array("DIV" => "edit" . $tab++, "TAB" => GetMessage("HELP_TAB"), "ICON" => "", "TITLE" => "");

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>
    <form method="post"
          action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($module_id) ?>&amp;lang=<?= urlencode(LANGUAGE_ID) ?>"
          autocomplete="off">
        <?
        $tabControl->BeginNextTab();

        if (!function_exists('curl_init')) {
            //ShowError(Loc::getMessage('CURL_IS_NOT_INSTALLED'));
        }
        ?>
        <tr>
            <td>
                <?= GetMessage('PI_DOCS') ?> <a href="http://docs.democontent.ru/pi/" target="_blank">http://docs.democontent.ru/pi/</a>
            </td>
        </tr>
        <tr>
            <td>
                <?php
                ShowNote(GetMessage('SECRET_KEY') . \Democontent2\Pi\Sign::getInstance()->get());
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[startBalance]"
                       value="<?= intval(COption::GetOptionString($module_id, 'startBalance')) ?>">
                <?= Loc::getMessage('START_BALANCE') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[currency_name]"
                       value="<?= COption::GetOptionString($module_id, 'currency_name') ?>">
                <?= Loc::getMessage('CURRENCY_NAME') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[phone_code_mask]"
                       value="<?= ((intval(COption::GetOptionString($module_id, 'phone_code_mask'))) ? COption::GetOptionString($module_id, 'phone_code_mask') : 7) ?>">
                <?= Loc::getMessage('PHONE_CODE_MASK') ?>
            </td>
        </tr>
        <tr>
            <td>
                <select name="settings[register_phone_required]">
                    <option value="0"
                            <?= ((!intval(COption::GetOptionString($module_id, 'register_phone_required'))) ? ' selected' : '') ?>><?= Loc::getMessage('NO') ?></option>
                    <option value="1"
                            <?= ((intval(COption::GetOptionString($module_id, 'register_phone_required'))) ? ' selected' : '') ?>><?= Loc::getMessage('YES') ?></option>
                </select>
                <?= Loc::getMessage('REGISTER_PHONE_REQUIRED') ?>
            </td>
        </tr>

        <tr class="heading">
            <td><?= Loc::getMessage('ITEMS_SETTINGS') ?></td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[max_files]"
                       value="<?= COption::GetOptionString($module_id, 'max_files') ?>">
                <?= Loc::getMessage('ITEMS_SETTINGS_MAX_FILES') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[max_file_size]"
                       value="<?= COption::GetOptionString($module_id, 'max_file_size') ?>">
                <?= Loc::getMessage('ITEMS_SETTINGS_MAX_FILE_SIZE') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[item_period]"
                       value="<?= COption::GetOptionString($module_id, 'item_period') ?>">
                <?= Loc::getMessage('ITEMS_SETTINGS_PERIOD') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[free_limit]"
                       value="<?= COption::GetOptionString($module_id, 'free_limit') ?>">
                <?= Loc::getMessage('ITEMS_SETTINGS_FREE_LIMIT') ?>
            </td>
        </tr>
        <tr>
            <td>
                <select name="settings[moderation_new]">
                    <option value="0"
                            <?= ((!intval(COption::GetOptionString($module_id, 'moderation_new'))) ? ' selected' : '') ?>><?= Loc::getMessage('NO') ?></option>
                    <option value="1"
                            <?= ((intval(COption::GetOptionString($module_id, 'moderation_new'))) ? ' selected' : '') ?>><?= Loc::getMessage('YES') ?></option>
                </select>
                <?= Loc::getMessage('ITEMS_SETTINGS_NEW_ITEM_MODERATION') ?>
            </td>
        </tr>
        <tr>
            <td>
                <select name="settings[moderation_update]">
                    <option value="0"
                            <?= ((!intval(COption::GetOptionString($module_id, 'moderation_update'))) ? ' selected' : '') ?>><?= Loc::getMessage('NO') ?></option>
                    <option value="1"
                            <?= ((intval(COption::GetOptionString($module_id, 'moderation_update'))) ? ' selected' : '') ?>><?= Loc::getMessage('YES') ?></option>
                </select>
                <?= Loc::getMessage('ITEMS_SETTINGS_UPDATE_ITEM_MODERATION') ?>
            </td>
        </tr>
        <tr>
            <td>
                <select name="settings[defaultMap]">
                    <option value="yandex"
                            <?= ((COption::GetOptionString($module_id, 'defaultMap') == 'yandex') ? ' selected' : '') ?>>
                        <?= Loc::getMessage('YANDEX_MAPS') ?>
                    </option>
                    <option value="google"
                            <?= ((COption::GetOptionString($module_id, 'defaultMap') == 'google') ? ' selected' : '') ?>>
                        <?= Loc::getMessage('GOOGLE_MAPS') ?>
                    </option>
                </select>
                <?= Loc::getMessage('ITEMS_SETTINGS_DEFAULT_MAP') ?>
            </td>
        </tr>
        <tr>
            <td>
                <select name="settings[response_checklists]">
                    <option value="0"
                            <?= ((!intval(COption::GetOptionString($module_id, 'response_checklists'))) ? ' selected' : '') ?>><?= Loc::getMessage('NO') ?></option>
                    <option value="1"
                            <?= ((intval(COption::GetOptionString($module_id, 'response_checklists'))) ? ' selected' : '') ?>><?= Loc::getMessage('YES') ?></option>
                </select>
                <?= Loc::getMessage('ITEMS_SETTINGS_CHECKLISTS') ?>
            </td>
        </tr>

        <tr class="heading">
            <td><?= Loc::getMessage('PAYMENT_OPTIONS') ?></td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[upper_limit_cost]"
                       value="<?= COption::GetOptionString($module_id, 'upper_limit_cost') ?>">
                <?= Loc::getMessage('PAYMENT_OPTIONS_UPPER_LIMIT_COST') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[quickly_option_cost]"
                       value="<?= COption::GetOptionString($module_id, 'quickly_option_cost') ?>">
                <?= Loc::getMessage('PAYMENT_OPTIONS_QUICKLY_COST') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[quickly_option_period]"
                       value="<?= COption::GetOptionString($module_id, 'quickly_option_period') ?>">
                <?= Loc::getMessage('PAYMENT_OPTIONS_QUICKLY_PERIOD') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[registration_fee]"
                       value="<?= intval(COption::GetOptionString($module_id, 'registration_fee')) ?>">
                <?= Loc::getMessage('PAYMENT_OPTIONS_REGISTRATION_FEE') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[contacts_open]"
                       value="<?= intval(COption::GetOptionString($module_id, 'contacts_open')) ?>">
                <?= Loc::getMessage('PAYMENT_OPTIONS_CONTACTS_OPEN') ?>
            </td>
        </tr>

        <tr class="heading">
            <td>API</td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[yandex_maps_api_key]"
                       value="<?= COption::GetOptionString($module_id, 'yandex_maps_api_key') ?>">
                <?= GetMessage('YANDEX_MAPS_API_KEY') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[google_maps_api_key]"
                       value="<?= COption::GetOptionString($module_id, 'google_maps_api_key') ?>">
                <?= GetMessage('GOOGLE_MAPS_API_KEY') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[reCaptchaPublic]"
                       value="<?= Option::get($module_id, 'reCaptchaPublic') ?>"> <?= Loc::getMessage('RECAPTCHA_PUBLIC') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[reCaptchaSecret]"
                       value="<?= Option::get($module_id, 'reCaptchaSecret') ?>"> <?= Loc::getMessage('RECAPTCHA_SECRET') ?>
            </td>
        </tr>

        <tr class="heading">
            <td><a href="https://www.safecrow.ru/" target="_blank"><?= Loc::getMessage('SAFECROW_TITLE') ?></a></td>
        </tr>
        <tr>
            <td>
                <select name="settings[safeCrowServer]">
                    <option value="dev" <?= (Option::get($module_id, 'safeCrowServer') == 'dev') ? ' selected' : '' ?>>
                        dev
                    </option>
                    <option value="staging"
                            <?= (Option::get($module_id, 'safeCrowServer') == 'staging') ? ' selected' : '' ?>>
                        staging
                    </option>
                </select>
                <?= GetMessage('SAFECROW_SERVER') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[safeCrowApiKey]"
                       value="<?= Option::get($module_id, 'safeCrowApiKey') ?>"> <?= Loc::getMessage('SAFECROW_API_KEY') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[safeCrowApiSecret]"
                       value="<?= Option::get($module_id, 'safeCrowApiSecret') ?>"> <?= Loc::getMessage('SAFECROW_API_SECRET') ?>
            </td>
        </tr>

        <?php
        $iblockClass = new \Democontent2\Pi\Iblock\Iblock();
        $iblocks = $iblockClass->getTypes();
        $courierIblocksIds = \Bitrix\Main\Web\Json::decode(Option::get($module_id, 'courierIblocks'));
        ?>

        <tr class="heading">
            <td><?= GetMessage("COURIER_IBLOCKS") ?></td>
        </tr>
        <tr>
            <td>
                <select name="settings[courierIblocks][]" multiple>
                    <?php
                    foreach ($iblocks as $k => $v) {
                        if (!isset($v['iblocks']) || !count($v['iblocks'])) {
                            continue;
                        }

                        foreach ($v['iblocks'] as $item) {
                            $selected = '';
                            if (in_array($item['id'], $courierIblocksIds)) {
                                $selected = ' selected';
                            }
                            ?>
                            <option value="<?= $item['id'] ?>" <?= $selected ?>>
                                <?= $item['name'] ?> (<?= $v['name'] ?>)
                            </option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>

        <?php
        $tabControl->BeginNextTab();
        ?>
        <tr class="heading">
            <td><?= GetMessage("PAYMENTS_SETTINGS") ?></td>
        </tr>
        <tr>
            <td>
                <?= GetMessage('PAYMENTS_SETTINGS_DEFAULT') ?> -
                <input type="radio" name="settings[paymentProvider]"
                       value=""
                       <?= ((!COption::GetOptionString($module_id, 'paymentProvider')) ? ' checked' : '') ?>><?= GetMessage('PAYMENTS_SETTINGS_OFF') ?>
                <input type="radio" name="settings[paymentProvider]"
                       value="yandex"
                       <?= ((COption::GetOptionString($module_id, 'paymentProvider') == 'yandex') ? ' checked' : '') ?>><?= GetMessage('YANDEX_KASSA') ?>
                <input type="radio" name="settings[paymentProvider]"
                       value="robokassa"
                       <?= ((COption::GetOptionString($module_id, 'paymentProvider') == 'robokassa') ? ' checked' : '') ?>><?= GetMessage('ROBOKASSA') ?>
                <input type="radio" name="settings[paymentProvider]"
                       value="sberbank"
                       <?= ((COption::GetOptionString($module_id, 'paymentProvider') == 'sberbank') ? ' checked' : '') ?>><?= GetMessage('SBERBANK') ?>
                <input type="radio" name="settings[paymentProvider]"
                       value="tinkoff"
                       <?= ((COption::GetOptionString($module_id, 'paymentProvider') == 'tinkoff') ? ' checked' : '') ?>><?= GetMessage('TINKOFF') ?>
                <input type="radio" name="settings[paymentProvider]"
                       value="paytrail"
                       <?= ((COption::GetOptionString($module_id, 'paymentProvider') == 'paytrail') ? ' checked' : '') ?>>Paytrail
                <input type="radio" name="settings[paymentProvider]"
                       value="paypal"
                       <?= ((COption::GetOptionString($module_id, 'paymentProvider') == 'paypal') ? ' checked' : '') ?>>PayPal
                <input type="radio" name="settings[paymentProvider]"
                       value="skrill"
                       <?= ((COption::GetOptionString($module_id, 'paymentProvider') == 'skrill') ? ' checked' : '') ?>>Skrill
                <input type="radio" name="settings[paymentProvider]"
                       value="click.uz"
                       <?= ((COption::GetOptionString($module_id, 'paymentProvider') == 'click.uz') ? ' checked' : '') ?>>Click.uz
            </td>
        </tr>
        <tr class="heading">
            <td><?= GetMessage("ROBOKASSA") ?></td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[robokassa_login]" id="robokassa_login"
                       value="<?= COption::GetOptionString($module_id, 'robokassa_login') ?>" style="width:300px;">
                <?= GetMessage('ROBOKASSA_1') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[robokassa_password_1]" id="robokassa_password_1"
                       value="<?= COption::GetOptionString($module_id, 'robokassa_password_1') ?>" style="width:300px;">
                <?= GetMessage('ROBOKASSA_2') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[robokassa_password_2]" id="robokassa_password_2"
                       value="<?= COption::GetOptionString($module_id, 'robokassa_password_2') ?>" style="width:300px;">
                <?= GetMessage('ROBOKASSA_3') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[robokassa_test_password_1]" id="robokassa_test_password_1"
                       value="<?= COption::GetOptionString($module_id, 'robokassa_test_password_1') ?>"
                       style="width:300px;">
                <?= GetMessage('ROBOKASSA_4') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[robokassa_test_password_2]" id="robokassa_test_password_2"
                       value="<?= COption::GetOptionString($module_id, 'robokassa_test_password_2') ?>"
                       style="width:300px;">
                <?= GetMessage('ROBOKASSA_5') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= GetMessage('ROBOKASSA_6') ?> -
                <input type="radio" name="settings[robokassa_mode]"
                       value="0"
                       <?= ((!intval(COption::GetOptionString($module_id, 'robokassa_mode'))) ? ' checked' : '') ?>><?= GetMessage('YES') ?>
                <input type="radio" name="settings[robokassa_mode]"
                       value="1"
                       <?= ((intval(COption::GetOptionString($module_id, 'robokassa_mode'))) ? ' checked' : '') ?>><?= GetMessage('NO') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php
                echo \Bitrix\Main\Localization\Loc::getMessage(
                    'ROBOKASSA_SUCCESS_URL',
                    array(
                        '#SITE_DIR#' => COption::GetOptionString($module_id, 'siteDir'),
                        '#HASH#' => \Democontent2\Pi\Sign::getInstance()->get()
                    )
                );
                ?>
            </td>
        </tr>
        <tr class="heading">
            <td><?= GetMessage("YANDEX_KASSA") ?></td>
        </tr>
        <tr>
        <tr>
            <td>
                <input type="text" name="settings[yandex_kassa_shopid]" id="yandex_kassa_shopid"
                       value="<?= COption::GetOptionString($module_id, 'yandex_kassa_shopid') ?>" style="width:300px;">
                <?= GetMessage('YANDEX_KASSA_SHOPID') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[yandex_kassa_scid]" id="yandex_kassa_scid"
                       value="<?= COption::GetOptionString($module_id, 'yandex_kassa_scid') ?>" style="width:300px;">
                <?= GetMessage('YANDEX_KASSA_SCID') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= GetMessage('YANDEX_KASSA_TAX_CODE') ?>
                <?php
                $i = 0;
                echo '<select name="settings[yandex_kassa_tax_code]">';
                echo '<option value="0">---</option>';
                while ($i++ < 6) {
                    $selected = '';
                    if (intval(COption::GetOptionString($module_id, 'yandex_kassa_tax_code')) == $i) {
                        $selected = ' selected';
                    }

                    echo '<option value="' . $i . '"' . $selected . '>' . GetMessage('YANDEX_KASSA_TAX_CODE_' . $i) . '</option>';
                }
                echo '</select>';
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= GetMessage('YANDEX_KASSA_NDS') ?>
                <?php
                $i = 0;
                echo '<select name="settings[yandex_kassa_nds]">';
                echo '<option value="0">---</option>';
                while ($i++ < 6) {
                    $selected = '';
                    if (intval(COption::GetOptionString($module_id, 'yandex_kassa_nds')) == $i) {
                        $selected = ' selected';
                    }

                    echo '<option value="' . $i . '"' . $selected . '>' . GetMessage('YANDEX_KASSA_NDS_' . $i) . '</option>';
                }
                echo '</select>';
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= GetMessage('YANDEX_KASSA_OFD') ?> -
                <input type="radio" name="settings[yandex_kassa_ofd]"
                       value="0"
                       <?= ((!intval(COption::GetOptionString($module_id, 'yandex_kassa_ofd'))) ? ' checked' : '') ?>><?= GetMessage('NO') ?>
                <input type="radio" name="settings[yandex_kassa_ofd]"
                       value="1"
                       <?= ((intval(COption::GetOptionString($module_id, 'yandex_kassa_ofd'))) ? ' checked' : '') ?>><?= GetMessage('YES') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php
                echo \Bitrix\Main\Localization\Loc::getMessage(
                    'YANDEX_KASSA_SUCCESS_URL',
                    array(
                        '#SITE_DIR#' => COption::GetOptionString($module_id, 'siteDir'),
                        '#HASH#' => \Democontent2\Pi\Sign::getInstance()->get()
                    )
                );
                ?>
            </td>
        </tr>

        <tr class="heading">
            <td><?= GetMessage("SBERBANK") ?></td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[sberBankUserName]"
                       value="<?= COption::GetOptionString($module_id, 'sberBankUserName') ?>">
                <?= GetMessage('SBERBANK_USERNAME') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[sberBankPassword]"
                       value="<?= COption::GetOptionString($module_id, 'sberBankPassword') ?>">
                <?= GetMessage('SBERBANK_PASSWORD') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[sberBankSecretKey]"
                       value="<?= COption::GetOptionString($module_id, 'sberBankSecretKey') ?>">
                <?= GetMessage('SBERBANK_SECRET_KEY') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php
                echo \Bitrix\Main\Localization\Loc::getMessage(
                    'TINKOFF_NOTIFICATION_URL',
                    array(
                        '#SITE_DIR#' => COption::GetOptionString($module_id, 'siteDir'),
                        '#HASH#' => \Democontent2\Pi\Sign::getInstance()->get()
                    )
                );
                ?>
            </td>
        </tr>
        <tr class="heading">
            <td><?= GetMessage("TINKOFF") ?></td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[tinkoffTerminalKey]"
                       value="<?= COption::GetOptionString($module_id, 'tinkoffTerminalKey') ?>">
                <?= GetMessage('TINKOFF_TERMINAL_KEY') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[tinkoffSecretKey]"
                       value="<?= COption::GetOptionString($module_id, 'tinkoffSecretKey') ?>">
                <?= GetMessage('TINKOFF_SECRET_KEY') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= GetMessage('TINKOFF_OFD') ?> -
                <input type="radio" name="settings[tinkoffOfd]"
                       value="0"
                       <?= ((!intval(COption::GetOptionString($module_id, 'tinkoffOfd'))) ? ' checked' : '') ?>><?= GetMessage('NO') ?>
                <input type="radio" name="settings[tinkoffOfd]"
                       value="1"
                       <?= ((intval(COption::GetOptionString($module_id, 'tinkoffOfd'))) ? ' checked' : '') ?>><?= GetMessage('YES') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= GetMessage('YANDEX_KASSA_NDS') ?>
                <?php
                $i = 0;
                echo '<select name="settings[tinkoffNds]">';
                echo '<option value="0">---</option>';
                while ($i++ < 4) {
                    $selected = '';
                    if (COption::GetOptionString($module_id, 'tinkoffNds') == GetMessage('TINKOFF_NDS__' . $i)) {
                        $selected = ' selected';
                    }

                    echo '<option value="' . GetMessage('TINKOFF_NDS__' . $i) . '"' . $selected . '>' . GetMessage('TINKOFF_NDS_' . $i) . '</option>';
                }
                echo '</select>';
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= GetMessage('YANDEX_KASSA_TAX_CODE') ?>
                <?php
                $i = 0;
                echo '<select name="settings[tinkoffTaxCode]">';
                echo '<option value="0">---</option>';
                while ($i++ < 6) {
                    $selected = '';
                    if (COption::GetOptionString($module_id, 'tinkoffTaxCode') == GetMessage('TINKOFF_TAX_CODE__' . $i)) {
                        $selected = ' selected';
                    }

                    echo '<option value="' . GetMessage('TINKOFF_TAX_CODE__' . $i) . '"' . $selected . '>' . GetMessage('TINKOFF_TAX_CODE_' . $i) . '</option>';
                }
                echo '</select>';
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php
                echo \Bitrix\Main\Localization\Loc::getMessage(
                    'TINKOFF_NOTIFICATION_URL',
                    array(
                        '#SITE_DIR#' => COption::GetOptionString($module_id, 'siteDir'),
                        '#HASH#' => \Democontent2\Pi\Sign::getInstance()->get()
                    )
                );
                ?>
            </td>
        </tr>

        <tr class="heading">
            <td><a href="https://www.paytrail.com/" target="_blank">Paytrail</a></td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[paytrail_merchant_id]"
                       value="<?= COption::GetOptionString($module_id, 'paytrail_merchant_id') ?>">
                Merchant ID
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[paytrail_merchant_secret]"
                       value="<?= COption::GetOptionString($module_id, 'paytrail_merchant_secret') ?>">
                Merchant Secret
            </td>
        </tr>

        <tr class="heading">
            <td><a href="https://www.paypal.com/" target="_blank">PayPal</a></td>
        </tr>
        <tr>
            <td>
                <select name="settings[paypal_mode]">
                    <option value="0"
                            <?= (!intval(COption::GetOptionString($module_id, 'paypal_mode'))) ? ' selected' : '' ?>><?= GetMessage('PAYPAL_MODE_0') ?></option>
                    <option value="1"
                            <?= (intval(COption::GetOptionString($module_id, 'paypal_mode'))) ? ' selected' : '' ?>><?= GetMessage('PAYPAL_MODE_1') ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[paypal_client_id]"
                       value="<?= COption::GetOptionString($module_id, 'paypal_client_id') ?>">
                Client ID
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[paypal_secret]"
                       value="<?= COption::GetOptionString($module_id, 'paypal_secret') ?>">
                Secret
            </td>
        </tr>

        <tr class="heading">
            <td><a href="https://www.skrill.com/" target="_blank">Skrill</a></td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[skrill_merchant_account]"
                       value="<?= COption::GetOptionString($module_id, 'skrill_merchant_account') ?>">
                Merchant Email
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[skrill_merchant_secret_word]"
                       value="<?= COption::GetOptionString($module_id, 'skrill_merchant_secret_word') ?>">
                Merchant Secret Word
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[skrill_mqi]"
                       value="<?= COption::GetOptionString($module_id, 'skrill_mqi') ?>">
                MQI
            </td>
        </tr>

        <tr class="heading">
            <td><a href="https://click.uz/" target="_blank">Click.uz</a></td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[click_uz_service_id]"
                       value="<?= COption::GetOptionString($module_id, 'click_uz_service_id') ?>">
                SERVICE_ID
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[click_uz_merchant_id]"
                       value="<?= COption::GetOptionString($module_id, 'click_uz_merchant_id') ?>">
                MERCHANT_ID
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[click_uz_secret_key]"
                       value="<?= COption::GetOptionString($module_id, 'click_uz_secret_key') ?>">
                SECRET_KEY
            </td>
        </tr>
        <?php
        $tabControl->BeginNextTab();
        $defaultSmsGate = Option::get($module_id, 'defaultSmsGate');
        ?>
        <tr class="heading">
            <td><?= Loc::getMessage('DEFAULT_SMS_GATE') ?></td>
        </tr>
        <tr>
            <td>
                <input type="radio" name="settings[defaultSmsGate]"
                       value=""
                       <?= (!$defaultSmsGate) ? ' checked' : '' ?>><?= Loc::getMessage('DEFAULT_SMS_GATE_DISABLED') ?>
                <br>
                <input type="radio" name="settings[defaultSmsGate]"
                       value="sms16.ru" <?= ($defaultSmsGate == 'sms16.ru') ? ' checked' : '' ?>>sms16.ru<br>
                <input type="radio" name="settings[defaultSmsGate]"
                       value="sms-uslugi.ru" <?= ($defaultSmsGate == 'sms-uslugi.ru') ? ' checked' : '' ?>>sms-uslugi.ru<br>
                <input type="radio" name="settings[defaultSmsGate]"
                       value="sms.ru" <?= ($defaultSmsGate == 'sms.ru') ? ' checked' : '' ?>>sms.ru<br>
                <input type="radio" name="settings[defaultSmsGate]"
                       value="smsc.ru" <?= ($defaultSmsGate == 'smsc.ru') ? ' checked' : '' ?>>smsc.ru<br>
            </td>
        </tr>

        <tr class="heading">
            <td><a href="https://new.sms16.ru" target="_blank">SMS16.RU</a></td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[intisLogin]"
                       value="<?= Option::get($module_id, 'intisLogin') ?>"> <?= Loc::getMessage('INTIS_LOGIN') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[intisSender]"
                       value="<?= Option::get($module_id, 'intisSender') ?>"> <?= Loc::getMessage('INTIS_SENDER') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[intisApiKey]"
                       value="<?= Option::get($module_id, 'intisApiKey') ?>"> <?= Loc::getMessage('INTIS_API_KEY') ?>
            </td>
        </tr>
        <tr class="heading">
            <td><a href="https://sms-uslugi.ru" target="_blank">SMS-USLUGI.RU</a></td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[smsUslugiLogin]"
                       value="<?= Option::get($module_id, 'smsUslugiLogin') ?>"> <?= Loc::getMessage('SMSUSLUGI_LOGIN') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[smsUslugiPassword]"
                       value="<?= Option::get($module_id, 'smsUslugiPassword') ?>"> <?= Loc::getMessage('SMSUSLUGI_PASSWORD') ?>
            </td>
        </tr>
        <tr class="heading">
            <td><a href="https://sms.ru" target="_blank">SMS.RU</a></td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[smsRuApiKey]"
                       value="<?= Option::get($module_id, 'smsRuApiKey') ?>">
                <a href="https://sms.ru/?panel=api"
                   target="_blank"><?= Loc::getMessage('SMS_RU_API_ID') ?></a>
            </td>
        </tr>
        <tr class="heading">
            <td><a href="https://smsc.ru" target="_blank">SMSC.RU</a></td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[smsCLogin]"
                       value="<?= Option::get($module_id, 'smsCLogin') ?>"> <?= Loc::getMessage('SMSUSLUGI_LOGIN') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[smsCPassword]"
                       value="<?= Option::get($module_id, 'smsCPassword') ?>"> <?= Loc::getMessage('SMSUSLUGI_PASSWORD') ?>
            </td>
        </tr>
        <tr class="heading">
            <td><?= Loc::getMessage('SMS_TEMPLATES') ?></td>
        </tr>
        <tr>
            <td>
                <?= Loc::getMessage('REGISTER_SMS') ?><br><br>
                <textarea rows="5" style="width: 100%;"
                          name="settings[registerSmsText]"><?= ((strlen(Option::get($module_id, 'registerSmsText')) > 0 ? Option::get($module_id, 'registerSmsText') : Loc::getMessage('USER_SEND_PASSWORD'))) ?></textarea>
            </td>
        </tr>
        <tr>
            <td>
                <?= Loc::getMessage('RESTORE_SMS') ?><br><br>
                <textarea rows="5" style="width: 100%;"
                          name="settings[restoreSmsText]"><?= ((strlen(Option::get($module_id, 'restoreSmsText')) > 0 ? Option::get($module_id, 'restoreSmsText') : Loc::getMessage('USER_SEND_NEW_PASSWORD'))) ?></textarea>
            </td>
        </tr>
        <?php
        $tabControl->BeginNextTab();
        ?>
        <tr>
            <td>
                <select name="settings[chatEnabled]">
                    <option value="0"
                            <?= ((!intval(Option::get($module_id, 'chatEnabled'))) ? ' selected' : '') ?>><?= Loc::getMessage('NO') ?></option>
                    <option value="1"
                            <?= ((intval(Option::get($module_id, 'chatEnabled'))) ? ' selected' : '') ?>><?= Loc::getMessage('YES') ?></option>
                </select>
                <?= Loc::getMessage('CHAT_SETTINGS_CHAT_ENABLED') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[nodeJsHost]"
                       value="<?= Option::get($module_id, 'nodeJsHost') ?>"> <?= Loc::getMessage('CHAT_SETTINGS_NODEJS_HOST') ?>
            </td>
        </tr>
        <tr class="heading">
            <td><?= Loc::getMessage('CHAT_SETTINGS_MONGO') ?></td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[mongoHost]"
                       value="<?= Option::get($module_id, 'mongoHost') ?>"> <?= Loc::getMessage('CHAT_SETTINGS_MONGO_HOST') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[mongoPort]"
                       value="<?= Option::get($module_id, 'mongoPort') ?>"> <?= Loc::getMessage('CHAT_SETTINGS_MONGO_PORT') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[mongoAtlasAddress]"
                       value="<?= Option::get($module_id, 'mongoAtlasAddress') ?>"> <?= Loc::getMessage('CHAT_SETTINGS_MONGO_ATLAS') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[mongoAdminLogin]"
                       value="<?= Option::get($module_id, 'mongoAdminLogin') ?>"> <?= Loc::getMessage('CHAT_SETTINGS_MONGO_LOGIN') . \Democontent2\Pi\Utils::mid() ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="password" autocomplete="off" name="settings[mongoAdminPassword]"
                       value="<?= Option::get($module_id, 'mongoAdminPassword') ?>"> <?= Loc::getMessage('CHAT_SETTINGS_MONGO_PASSWORD') . \Democontent2\Pi\Utils::mid() ?>
            </td>
        </tr>

        <tr>
            <td>
                <?php
                echo ShowMessage(['MESSAGE' => Loc::getMessage('MONGO_ENV_NOTE')]);
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <b>PI_MONGODB_ADMIN_LOGIN</b>
                - <?= Loc::getMessage('CHAT_SETTINGS_MONGO_LOGIN') . \Democontent2\Pi\Utils::mid() ?><br>
                <b>PI_MONGODB_ADMIN_PASSWORD</b>
                - <?= Loc::getMessage('CHAT_SETTINGS_MONGO_PASSWORD') . \Democontent2\Pi\Utils::mid() ?><br>
                <b>PI_MONGODB_HOST</b> - <?= Loc::getMessage('CHAT_SETTINGS_MONGO_HOST') ?><br>
                <b>PI_MONGODB_PORT</b> - <?= Loc::getMessage('CHAT_SETTINGS_MONGO_PORT') ?><br>
                <b>PI_MONGODB_ATLAS</b> - <?= Loc::getMessage('CHAT_SETTINGS_MONGO_ATLAS') ?>
            </td>
        </tr>

        <tr class="heading">
            <td><?= Loc::getMessage('CHAT_SETTINGS_MONGO_HOWTO') ?></td>
        </tr>
        <tr>
            <td>
                <p>
                    <?= Loc::getMessage('CHAT_SETTINGS_MONGO_HOWTO_DESCRIPTION') ?>
                </p>
            </td>
        </tr>
        <?php
        $tabControl->BeginNextTab();
        ?>
        <tr>
            <td>
                <select name="settings[apiEnabled]">
                    <option value="0"
                            <?= ((!intval(Option::get($module_id, 'apiEnabled'))) ? ' selected' : '') ?>><?= Loc::getMessage('DISABLED') ?></option>
                    <option value="1"
                            <?= ((intval(Option::get($module_id, 'apiEnabled'))) ? ' selected' : '') ?>><?= Loc::getMessage('ENABLED') ?></option>
                </select>
                <?= Loc::getMessage('API_STATUS') ?>
            </td>
        </tr>
        <tr>
            <td>
                <select name="settings[apiOnlyHttps]">
                    <option value="0"
                            <?= ((!intval(Option::get($module_id, 'apiOnlyHttps'))) ? ' selected' : '') ?>><?= Loc::getMessage('NO') ?></option>
                    <option value="1"
                            <?= ((intval(Option::get($module_id, 'apiOnlyHttps'))) ? ' selected' : '') ?>><?= Loc::getMessage('YES') ?></option>
                </select>
                <?= Loc::getMessage('HTTPS_ONLY') ?>
            </td>
        </tr>
        <tr>
            <td>
                <br>
                <?= Loc::getMessage('API_URL_RULE') ?><br><br>
                <pre>
                    array(
                        "CONDITION" => "#^<?= Option::get($module_id, 'siteDir') ?>api/v1/([a-z-]+)/($|\?.+$)$#",
                        "RULE" => "namespace=democontent2.pi.api.v1&amp;component=handler&amp;params[method]=$1",
                        "ID" => "democontent2.pi:handler",
                        "PATH" => "/local/democontent2_pi_prolog.php",
                        "SOLUTION" => "democontent2.pi",
                        "SORT" => 1
                    )
                </pre>
            </td>
        </tr>
        <?php
        $tabControl->BeginNextTab();
        ?>
        <tr class="heading">
            <td><?= Loc::getMessage('APPS') ?></td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[android_app]"
                       value="<?= Option::get($module_id, 'android_app') ?>">
                Android
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[ios_app]"
                       value="<?= Option::get($module_id, 'ios_app') ?>">
                iOS
            </td>
        </tr>

        <tr class="heading">
            <td>FireBase</td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[firebase_config]"
                       value="<?= Option::get($module_id, 'firebase_config') ?>">
                <?= Loc::getMessage('PI_APP_SETTINGS_FIREBASE_CONFIG') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[firebase_url]" value="<?= Option::get($module_id, 'firebase_url') ?>">
                Database URL
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[firebase_sender_id]"
                       value="<?= Option::get($module_id, 'firebase_sender_id') ?>">
                Sender ID
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[firebase_web_push_key]"
                       value="<?= Option::get($module_id, 'firebase_web_push_key') ?>">
                <?= GetMessage('PI_APP_SETTINGS_FIREBASE_WEB_PUSH_KEYS') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="settings[firebase_web_push_icon]"
                       value="<?= Option::get($module_id, 'firebase_web_push_icon') ?>">
                <?= GetMessage('PI_APP_SETTINGS_FIREBASE_WEB_PUSH_ICON') ?>
            </td>
        </tr>
        <?php
        $tabControl->BeginNextTab();
        ?>
        <tr class="heading">
            <td><?= Loc::getMessage('HELP_SEO_MACROS') ?></td>
        </tr>
        <tr>
            <td>
                <p>
                    <b>#CITY_DECLENSION#</b> - <?= Loc::getMessage('HELP_SEO_MACROS_CITY_DESCRIPTION') ?>
                </p>
            </td>
        </tr>
        <tr class="heading">
            <td><?= Loc::getMessage('HELP_CRON') ?></td>
        </tr>
        <tr>
            <td>
                <?php
                $cron[] = '01 * * * * root /usr/bin/php -f ' . \Bitrix\Main\Application::getDocumentRoot() . '/local/cron/remove_temp_tasks.php ' . \Democontent2\Pi\Sign::getInstance()->get();
                $cron[] = '*/3 * * * * root /usr/bin/php -f ' . \Bitrix\Main\Application::getDocumentRoot() . '/local/cron/send_notifications_for_subscribers.php ' . \Democontent2\Pi\Sign::getInstance()->get();
                $cron[] = '*/10 * * * * root /usr/bin/php -f ' . \Bitrix\Main\Application::getDocumentRoot() . '/local/cron/chat_notifications.php ' . \Democontent2\Pi\Sign::getInstance()->get();
                $cron[] = '* * * * * root /usr/bin/php -f ' . \Bitrix\Main\Application::getDocumentRoot() . '/local/cron/push_queue.php ' . \Democontent2\Pi\Sign::getInstance()->get();

                foreach ($cron as $c) {
                    echo "<pre>";
                    print_r($c);
                    echo "</pre>";
                }
                ?>
            </td>
        </tr>
        <? $tabControl->Buttons(); ?>
        <input type="submit" name="<?= GetMessage("SAVE") ?>" value="<?= GetMessage("SAVE") ?>"
               title="<?= GetMessage("SAVE") ?>">
        <?= bitrix_sessid_post(); ?>
        <? $tabControl->End(); ?>
    </form>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>