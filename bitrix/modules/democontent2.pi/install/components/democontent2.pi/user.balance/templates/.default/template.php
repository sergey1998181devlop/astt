<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 12.01.2019
 * Time: 17:31
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}

$paymentProvider = strlen(\Bitrix\Main\Config\Option::get(DSPI, 'paymentProvider'));
?>
    <div class="page-content">
        <div class="wrapper">
            <div class="page-title">
                <h1>
                    <?= Loc::getMessage('BALANCE_COMPONENT_TITLE') ?>
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
                    <div class="white-block balance-container">
                        <div class="sorty-panel tabs-head">
                            <ul>
                                <li class="active">
                                    <a href="#balance-1">
                                        <?= Loc::getMessage('BALANCE_COMPONENT_TABS_HEAD_1') ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#balance-2">
                                        <?= Loc::getMessage('BALANCE_COMPONENT_TABS_HEAD_2') ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#balance-3">
                                        <?= Loc::getMessage('BALANCE_COMPONENT_TABS_HEAD_3') ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="tabs-wrap">
                            <div id="balance-1" class="tab active">
                                <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="post">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6 col-xxs12">
                                            <div class="form-group">
                                                <input id="deposit-amount" required name="amount" type="text"
                                                       class="form-control required"
                                                       placeholder="<?= Loc::getMessage('BALANCE_COMPONENT_INPUT_PL') ?>"
                                                    <?= (!$paymentProvider) ? ' disabled' : '' ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 col-xxs12">
                                            <div class="form-group">
                                                <button class="btn-submit btn btn-green"<?= (!$paymentProvider) ? ' disabled' : '' ?>>
                                                    <?= Loc::getMessage('BALANCE_COMPONENT_BTN') ?>
                                                </button>
                                            </div>
                                        </div>
                                        <?php
                                        if(!$paymentProvider){
                                            ?>
                                            <div class="col-xs-12">
                                                <div class="alert alert-info">
                                                    <?=Loc::getMessage('BALANCE_COMPONENT_PAYMENT_DISABLED')?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </form>
                                <?php
                                $APPLICATION->IncludeComponent(
                                    "bitrix:main.include", "",
                                    array(
                                        "AREA_FILE_SHOW" => "file",
                                        "PATH" => SITE_TEMPLATE_PATH . "/inc/balance/logo.php",
                                        "EDIT_TEMPLATE" => "include_areas_template.php",
                                        'MODE' => 'html'
                                    ),
                                    false
                                );
                                ?>
                            </div>
                            <div id="balance-2" class="tab">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th class="text-left">
                                            <?= Loc::getMessage('BALANCE_COMPONENT_REPORT_TTL_1') ?>
                                        </th>
                                        <th class="text-left">
                                            <?= Loc::getMessage('BALANCE_COMPONENT_REPORT_TTL_2') ?>
                                        </th>
                                        <th class="text-left">
                                            <?= Loc::getMessage('BALANCE_COMPONENT_REPORT_TTL_3') ?>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($arResult['ITEMS'] as $item) {
                                        if (intval($item['UF_TYPE']) > 0) {
                                            continue;
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $item['ID'] ?></td>
                                            <td><?= date('d.m.Y H:i:s', strtotime($item['UF_CREATED_AT'])) ?></td>
                                            <td><?= \Democontent2\Pi\Utils::price($item['UF_AMOUNT']) ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <div id="balance-3" class="tab">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th class="text-left">
                                            <?= Loc::getMessage('BALANCE_COMPONENT_REPORT_TTL_1') ?>
                                        </th>
                                        <th class="text-left">
                                            <?= Loc::getMessage('BALANCE_COMPONENT_REPORT_TTL_2') ?>
                                        </th>
                                        <th class="text-left">
                                            <?= Loc::getMessage('BALANCE_COMPONENT_REPORT_TTL_3') ?>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($arResult['ITEMS'] as $item) {
                                        if (!intval($item['UF_TYPE'])) {
                                            continue;
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $item['ID'] ?></td>
                                            <td><?= date('d.m.Y H:i:s', strtotime($item['UF_CREATED_AT'])) ?></td>
                                            <td><?= \Democontent2\Pi\Utils::price($item['UF_AMOUNT']) ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}
