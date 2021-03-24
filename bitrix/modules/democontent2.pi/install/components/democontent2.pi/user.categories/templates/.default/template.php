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
$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$prices = [];
$cityId = 0;
$verification = 0;
if ($request->get('city')) {
    $cityId = intval($request->get('city'));
}
if ($request->get('verification')) {
    if (intval($request->get('verification')) > 0) {
        $verification = 1;
    }
}

if (count($arResult['PRICES']) > 0) {
    $prices = unserialize($arResult['PRICES']['UF_DATA']);
}
?>
    <div class="page-content">
        <div class="wrapper">
            <div class="page-title">
                <h1>
                    <?= Loc::getMessage('USERS_CATEGORIES_TITLE') ?>
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
                        <form class="categories-list" action="<?= $APPLICATION->GetCurPage(false) ?>" method="post">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th class="input-wrap">
                                        <?= Loc::getMessage('USERS_CATEGORIES_PRICE_ONE') ?>
                                    </th>
                                    <th class="input-wrap">
                                        <?= Loc::getMessage('USERS_CATEGORIES_PACKAGE') ?>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($arResult['MENU'] as $key => $item) {
                                    ?>
                                    <tr>
                                        <td>
                                            <b><?= $item['name'] ?></b>
                                        </td>
                                        <td class="input-wrap">
                                            <input name="category[<?= $item['code'] ?>][item][0]" type="text"
                                                   class="form-control input-sm"
                                                   value="<?= (isset($prices[$key]['item'][0])) ? intval($prices[$key]['item'][0]) : 0 ?>">
                                        </td>
                                        <td class="input-wrap">
                                            <input name="category[<?= $item['code'] ?>][package][0]" type="text"
                                                   class="form-control input-sm"
                                                   value="<?= (isset($prices[$key]['package'][0])) ? intval($prices[$key]['package'][0]) : 0 ?>">
                                        </td>
                                        <?php

                                        ?>
                                    </tr>
                                    <?php
                                    foreach ($item['items'] as $subitem) {
                                        ?>
                                        <tr class="subitem">
                                            <td>
                                                <?= $subitem['name'] ?>
                                            </td>
                                            <td class="input-wrap">
                                                <input name="category[<?= $item['code'] ?>][item][<?= $subitem['id'] ?>]"
                                                       type="text" class="form-control input-sm"
                                                       value="<?= (isset($prices[$key]['item'][$subitem['id']])) ? intval($prices[$key]['item'][$subitem['id']]) : 0 ?>">
                                            </td>
                                            <td class="input-wrap">
                                                <input name="category[<?= $item['code'] ?>][package][<?= $subitem['id'] ?>]"
                                                       type="text" class="form-control input-sm"
                                                       value="<?= (isset($prices[$key]['package'][$subitem['id']])) ? intval($prices[$key]['package'][$subitem['id']]) : 0 ?>">
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                            <br>
                            <button class="btn btn-green" type="submit">
                                <?= Loc::getMessage('USERS_CATEGORIES_BTN') ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}