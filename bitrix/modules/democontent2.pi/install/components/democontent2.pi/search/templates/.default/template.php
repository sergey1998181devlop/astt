<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 24.09.2018
 * Time: 14:17
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}
?>
    <form class="main-search" action="<?= SITE_DIR ?>user/create/" method="get">
        <?php
        $APPLICATION->IncludeComponent(
            "bitrix:main.include", "",
            array(
                "AREA_FILE_SHOW" => "file",
                "PATH" => SITE_TEMPLATE_PATH . "/inc/index/search.php",
                "EDIT_TEMPLATE" => "include_areas_template.php",
                'MODE' => 'html'
            ),
            false
        );
        ?>
        <div class="tbl tbl-fixed">
            <div class="tbc">
                <select name="category" class="js-select" style="width: 100%">
                    <?php
                    foreach ($arResult['CATEGORIES'] as $k => $v) {
                        if (!isset($v['items']) || !count($v['items'])) {
                            continue;
                        }

                        ?>
                        <optgroup label="<?= $v['name'] ?>">
                            <?php
                            foreach ($v['items'] as $k_ => $v_) {
                                ?>
                                <option value="<?=$k?>~<?= $v_['id'] ?>"><?= $v_['name'] ?></option>
                                <?php
                            }
                            ?>
                        </optgroup>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <div class="tbc">
                <input class="form-control" name="need"
                       placeholder="<?= Loc::getMessage('SEARCH_COMPONENT_PLACEHOLDER') ?>">

                <div class="exemple">
<!--                    --><?//= Loc::getMessage('SEARCH_COMPONENT_EXAMPLE') ?>
                    <?php
                    //TODO кирилица. чтото придумать с подсказвами в принципе
                    ?>
<!--                    <a class="js-lnk" href="#">ѕоклеить обои в квартире</a>-->
                </div>
            </div>
            <div class="tbc">
                <button class="btn btn-blue btn-fluid">
                    <?= Loc::getMessage('SEARCH_COMPONENT_BUTTON') ?>
                </button>
            </div>
        </div>
    </form>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}