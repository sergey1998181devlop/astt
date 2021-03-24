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

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
?>
    <form class="filter-sorty" action="<?= $APPLICATION->GetCurPage() ?>" method="get">
        <div class="row">
            <div class="col-sm-9 col-xxs-12">
                <div class="row">
                    <div class="col-sm-6 col-xxs-12">
                        <div class="form-group">
                            <div class="form-group">
                                <input class="checkbox" type="checkbox" name="param1"
                                       id="filter-sorty-checkbox1"<?= ($request->get('param1')) ? ' checked' : '' ?>/>
                                <label class="form-checkbox" for="filter-sorty-checkbox1">
                                    <span class="icon-wrap">
                                        <svg class="icon icon_checkmark">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                        </svg>
                                    </span>
                                    <?= Loc::getMessage('TASKS_LIST_FILTER_SORTY_1') ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xxs-12">
                        <div class="form-group">
                            <div class="form-group">
                                <input class="checkbox" type="checkbox" name="param2"
                                       id="filter-sorty-checkbox2" <?= ($request->get('param2')) ? ' checked' : '' ?>/>
                                <label class="form-checkbox" for="filter-sorty-checkbox2">
                                    <span class="icon-wrap">
                                        <svg class="icon icon_checkmark">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                        </svg>
                                    </span>
                                    <?= Loc::getMessage('TASKS_LIST_FILTER_SORTY_2') ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xxs-12">
                        <div class="form-group">
                            <input class="checkbox" type="checkbox" name="param3"
                                   id="filter-sorty-checkbox3"<?= ($request->get('param3')) ? ' checked' : '' ?>/>
                            <label class="form-checkbox" for="filter-sorty-checkbox3">
                                <span class="icon-wrap">
                                    <svg class="icon icon_checkmark">
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                    </svg>
                                </span>
                                <?= Loc::getMessage('TASKS_LIST_FILTER_SORTY_3') ?>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xxs-12">
                        <div class="form-group">
                            <input class="checkbox" type="checkbox" name="param4"
                                   id="filter-sorty-checkbox4"<?= ($request->get('param4')) ? ' checked' : '' ?>/>
                            <label class="form-checkbox" for="filter-sorty-checkbox4">
                                <span class="icon-wrap">
                                    <svg class="icon icon_checkmark">
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                    </svg>
                                </span>
                                <?= Loc::getMessage('TASKS_LIST_FILTER_SORTY_4') ?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 col-xxs-12">
                <button type="submit" class="btn btn-fluid btn-green">
                    <?= Loc::getMessage('TASKS_FILTER_SHOW_ALL') ?>
                </button>
            </div>
        </div>
    </form>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}