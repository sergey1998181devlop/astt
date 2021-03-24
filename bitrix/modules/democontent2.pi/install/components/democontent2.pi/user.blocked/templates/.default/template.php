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


?>
    <div class="page-content">
        <div class="wrapper">
            <div class="page-title">
                <h1>
                    <?= Loc::getMessage('BLOCKED_COMPONENT_TITLE') ?>
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
                    <div class="white-block">
                        <div class="list-contractor">
                            <?php
                            if (count($arResult['ITEMS']) > 0) {
                                foreach ($arResult['ITEMS'] as $key => $item) {
                                    ?>
                                    <div class="contractor-preview">
                                        <div class="tbl tbl-fixed">
                                            <div class="tbc">
                                                <div class="ava">
                                                    <? if ($item['PERSONAL_PHOTO'] > 0):
                                                        $ava = CFile::ResizeImageGet($item['PERSONAL_PHOTO'],
                                                            array(
                                                                'width' => 50,
                                                                'height' => 50
                                                            ),
                                                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                                            true
                                                        );
                                                        ?>
                                                        <div class="object-fit-container">
                                                            <img src="<?= $ava['src'] ?>" alt="" data-object-fit="cover"
                                                                 data-object-position="50% 50%"/>
                                                        </div>
                                                    <? else: ?>
                                                        <svg class="icon icon_user">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#user"></use>
                                                        </svg>
                                                    <? endif ?>
                                                </div>
                                            </div>
                                            <div class="tbc">
                                                <div class="name medium">
                                                    <?= $item['NAME'] ?>
                                                    <?= $item['LAST_NAME'] ?>
                                                </div>
                                            </div>
                                            <div class="tbc tbc-btn hidden-xxs hidden-xs">
                                                <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="post">
                                                    <input type="hidden" name="unBlocked"
                                                           value="<?= $item['ID'] ?>">
                                                    <button class="btn btn-xs btn-green btn-unblocked">
                                                        <?= Loc::getMessage('BLOCKED_COMPONENT_BTN_UNBLOCKED') ?>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <form class="visible-xxs visible-xs"
                                              action="<?= $APPLICATION->GetCurPage(false) ?>" method="post">
                                            <input type="hidden" name="unBlocked"
                                                   value="<?= $item['ID'] ?>">
                                            <button class="btn btn-xs btn-green btn-unblocked">
                                                <?= Loc::getMessage('BLOCKED_COMPONENT_BTN_UNBLOCKED') ?>
                                            </button>
                                        </form>
                                        <a href="<?= SITE_DIR . 'user/' . $item['ID'] ?>/"
                                           class="lnk-abs"></a>
                                    </div>
                                    <?
                                }
                            } else {
                                ?>
                                <div class="alert alert-info">
                                    <?= Loc::getMessage('BLOCKED_COMPONENT_EMPTY') ?>
                                </div>
                                <?php
                            }
                            ?>
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