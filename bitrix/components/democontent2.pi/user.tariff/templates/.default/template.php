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

$currencyName = \Bitrix\Main\Config\Option::get(DSPI, 'currency_name');

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}
$prices = [];
$profilePrices = [];
if (count($arResult['PRICES']) > 0) {
    $prices = unserialize($arResult['PRICES']['UF_DATA']);
}
if (count($arResult['PROFILE_PRICES']) > 0) {
    $profilePrices = unserialize($arResult['PROFILE_PRICES']['UF_DATA']);
}
?>
    <div class="page-content">
        <div class="wrapper">
            <div class="page-title">
                <h1>
                    <?= Loc::getMessage('USER_TARIFF_COMPONENT_TITLE') ?>
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
                        <div class="alert alert-info">
                            <?= Loc::getMessage('USER_TARIFF_COMPONENT_DESC') ?>
                        </div>
                        <br>
                        <form class="tariff-plan" action="<?= $APPLICATION->GetCurPage() ?>" method="post">
                            <?
                            $i = 0;
                            foreach ($arResult['MENU'] as $key => $item) {
                                $i++;
                                $checkedAll = false;
                                $checkedPackage = false;

                                if (isset($profilePrices[$key][0])) {
                                    if (strtotime($profilePrices[$key][0]) >= time()) {
                                        $checkedPackage = true;
                                    }
                                }
                                ?>
                                <div class="package">
                                    <div class="package-root">
                                        <div class="package-root__label">
                                            <input data-price="<?= $prices[$key]['package'][0] ?>"
                                                   class="checkbox package-root-checkbox"
                                                   data-package="<?= $key ?>"
                                                   type="checkbox" id="catalog-root-<?= $key ?>"
                                                   name="package[<?= $key ?>][0]"<?= ($checkedPackage) ? ' checked disabled' : '' ?>>
                                            <label class="form-checkbox" for="catalog-root-<?= $key ?>">
                                                    <span class="icon-wrap">
                                                        <svg class="icon icon_checkmark">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                                        </svg>
                                                    </span>
                                                <b><?= $item['name'] ?></b>
                                                <?php
                                                if (isset($profilePrices[$key][0])) {
                                                    if (strtotime($profilePrices[$key][0]) >= time()) {
                                                        $checkedAll = true;
                                                        ?>
                                                        <div class="package-payment">
                                                            <?= Loc::getMessage('USER_TARIFF_COMPONENT_PAYMENT_TO') ?>
                                                            <?= date('d.m.Y H:i', strtotime($profilePrices[$key][0])) ?>
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </label>
                                        </div>
                                        <div class="package-root__price">
                                            <b>
                                                <?= \Democontent2\Pi\Utils::price($prices[$key]['package'][0]) ?>
                                                <?= $currencyName ?>
                                            </b>
                                        </div>
                                    </div>
                                    <?php
                                    foreach ($item['items'] as $subitem) {
                                        $checkedThis = false;
                                        if (isset($profilePrices[$key][$subitem['id']])) {
                                            if (strtotime($profilePrices[$key][$subitem['id']]) >= time()) {
                                                $checkedThis = true;
                                            }
                                        }
                                        ?>
                                        <div class="package-sub">
                                            <div class="package-sub__label">
                                                <input class="checkbox<?= ($checkedAll || $checkedThis) ? '' : ' package-item-checkbox package-item-' . $key ?>"
                                                       type="checkbox"
                                                       name="package[<?= $key ?>][<?= $subitem['id'] ?>]"
                                                       data-price="<?= $prices[$key]['package'][$subitem['id']] ?>"
                                                       id="catalog-sub-<?= $subitem['id'] ?>"<?= ($checkedAll || $checkedThis) ? ' checked disabled' : '' ?>>
                                                <label class="form-checkbox<?= ($checkedAll || $checkedThis) ? ' package-label_disabled' : '' ?>"
                                                       for="catalog-sub-<?= $subitem['id'] ?>">
                                                                <span class="icon-wrap">
                                                                    <svg class="icon icon_checkmark">
                                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                                                    </svg>
                                                                </span>
                                                    <?= $subitem['name'] ?>
                                                    <?php
                                                    if (!$checkedAll) {
                                                        if (isset($profilePrices[$key][$subitem['id']])) {
                                                            if (strtotime($profilePrices[$key][$subitem['id']]) >= time()) {
                                                                ?>
                                                                <div class="package-payment">
                                                                    <?= Loc::getMessage('USER_TARIFF_COMPONENT_PAYMENT_TO') ?>
                                                                    <?= date('d.m.Y H:i', strtotime($profilePrices[$key][$subitem['id']])) ?>
                                                                </div>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <div class="package-payment package-payment--danger">
                                                                    <?= Loc::getMessage('USER_TARIFF_COMPONENT_PAYMENT_EXPIRED') ?>
                                                                    <span title="<?= date('Y-m-d H:i:s', strtotime($profilePrices[$key][$subitem['id']])) ?>"
                                                                          class="timestamp"></span>
                                                                </div>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </label>
                                            </div>
                                            <div class="package-sub__price">
                                                <?= \Democontent2\Pi\Utils::price($prices[$key]['package'][$subitem['id']]) ?>
                                                <?= $currencyName ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?
                            }
                            ?>
                            <div class="total tbl">
                                <div class="tbc text">
                                    <?= Loc::getMessage('USER_TARIFF_COMPONENT_TOTAL_TXT') ?>:
                                    <span id="tariff-sum" class="sum">0</span><?= $currencyName ?>
                                </div>

                                <div class="tbc btn-wrap">
                                    <button disabled type="submit" class="btn btn-green">
                                        <?= Loc::getMessage('USER_TARIFF_COMPONENT_BTN') ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}