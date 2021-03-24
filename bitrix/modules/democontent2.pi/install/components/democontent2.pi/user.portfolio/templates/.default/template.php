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

$maxImageSize = intval(\Bitrix\Main\Config\Option::get(DSPI, 'max_file_size'));
$maxFiles = intval(\Bitrix\Main\Config\Option::get(DSPI, 'max_file_size'));

if (count($arResult['PROFILE']) > 0) {
    if (strlen($arResult['PROFILE']['UF_DATA']) > 0) {
        $profile = unserialize($arResult['PROFILE']['UF_DATA']);
    }
}
?>
    <div class="page-content">
        <div class="wrapper">
            <div class="page-title">
                <h1>
                    <?= Loc::getMessage('USER_PORTFOLIO_COMPONENT_H1') ?>
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
                        <div class="h4">
                            <?= Loc::getMessage('USER_PORTFOLIO_COMPONENT_H1') ?>
                        </div>
                        <div class="h4">
                            <?= Loc::getMessage('USER_PORTFOLIO_COMPONENT_PORTFOLIO_CATEGORY_TTL') ?>
                        </div>
                        <ol class="portfolio-category">
                            <?php
                            foreach ($arResult['CATEGORIES'] as $category) {
                                ?>
                                <li id="category<?= $category['ID'] ?>">
                                    <a href="<?= SITE_DIR ?>user/portfolio/?id=<?= $category['ID'] ?>">
                                        <?= $category['UF_NAME'] ?>
                                    </a>
                                    <a href="#" class="remove removeCategory" data-id="<?= $category['ID'] ?>">
                                        <svg class="icon icon_close">
                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#close"></use>
                                        </svg>
                                    </a>
                                </li>
                                <?php
                            }
                            ?>
                        </ol>
                        <form method="post" action="<?= $APPLICATION->GetCurPage(false) ?>">
                            <div class="form-group add-category">
                                <label class="bold">
                                    <?= Loc::getMessage('USER_PORTFOLIO_COMPONENT_CATEGORY_LBL') ?>
                                </label>
                                <div class="tbl tbl-fixed">
                                    <div class="tbc">
                                        <input class="form-control required" type="text" name="categoryName">
                                    </div>
                                    <div class="tbc">
                                        <button class="btn btn-fluid btn-submit btn-orange" type="submit">
                                            <?= Loc::getMessage('USER_PORTFOLIO_COMPONENT_CATEGORY_BNT_SAVE') ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <?php
                        if (count($arResult['CATEGORY'])) {
                            ?>
                            <div class="h4">
                                <?= $arResult['CATEGORY']['name'] ?>
                            </div>
                            <div id="dropzone" class="dropzone dropzone-files"
                                 data-id="<?= $arResult['CATEGORY']['id'] ?>">
                                <div class="dz-default dz-message">
                                    <div class="dz-message-title">
                                        <?= Loc::getMessage('USER_PORTFOLIO_COMPONENT_DROPZONE_PLACEHOLDER1') ?>
                                    </div>
                                    <div class="dz-message-limit">
                                        <?= Loc::getMessage('USER_PORTFOLIO_COMPONENT_DROPZONE_PLACEHOLDER2', array('#QTY#' => $maxFiles)) ?>
                                    </div>
                                </div>
                                <div class="limit">
                                    <div class="dz-default dz-message">
                                        <div class="dz-message-title">
                                            <?= Loc::getMessage('USER_PORTFOLIO_COMPONENT_DROPZONE_PLACEHOLDER3', array('#QTY#' => $maxFiles)) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group files-previews clearfix" id="files-previews">
                                <?php
                                if (count($arResult['CATEGORY']['files'])) {
                                    foreach ($arResult['CATEGORY']['files'] as $file) {
                                        $image = CFile::ResizeImageGet(
                                            $file['UF_FILE_ID'],
                                            [
                                                'width' => 200,
                                                'height' => 200
                                            ],
                                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                            true
                                        );
                                        if (isset($image['src'])) {
                                            ?>
                                            <div class="preview-pict" data-id="<?= $file['ID'] ?>">
                                                <div class="uploadedPreview">
                                                    <a href="#" class="remove">
                                                        <svg class="icon icon_bin">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#bin"></use>
                                                        </svg>
                                                    </a>
                                                    <div class="object-fit-container">
                                                        <img data-object-fit="cover" data-object-position="50% 50%"
                                                             src="<?= $image['src'] ?>">
                                                    </div>
                                                    <a href="#" class="preview-pict-ttl">
                                                        <?= (strlen($file['UF_DESCRIPTION']) ? $file['UF_DESCRIPTION'] : Loc::getMessage('USER_PORTFOLIO_COMPONENT_IMAGE_DESCRIPTION')) ?>
                                                        <svg class="icon icon_pen">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#pen"></use>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
if (count($arResult['CATEGORY'])) {
    ?>
    <div id="popupTitleImg" class="popup popup-pict-title">
        <div class="popup-body">
            <div class="ttl h4">
                <?= Loc::getMessage('USER_PORTFOLIO_COMPONENT_POP_IMG_TTL') ?>
            </div>
            <form class="ajax">
                <div class="form-group">
                    <label class="bold"></label>
                    <input type="hidden" id="imageDescriptionId">
                    <input type="text" class="form-control" id="imageDescription">
                </div>
                <div id="imageDescriptionSave" class="btn btn-sumbit">
                    <?= Loc::getMessage('USER_PORTFOLIO_COMPONENT_POP_IMG_BTN_SAVE') ?>
                </div>
            </form>
        </div>
    </div>
    <?php
}
?>
    <script>
        var maxSizeAva = parseInt(<?=($maxImageSize)?>),
            maxFiles = parseInt('<?=$maxFiles?>'),
            getCurPath = '<?=$APPLICATION->GetCurPage(false)?>',
            dropzoneImageTitle = '<?=Loc::getMessage('USER_PORTFOLIO_COMPONENT_DROPZONE_IMAGE_TITLE')?>',
            templatePath = '<?=SITE_TEMPLATE_PATH?>';

        BX.message(
            {
                portfolioTemplatePath: '<?=$this->GetFolder()?>',
                portfolioRemoveCategoryConfirm: '<?=Loc::getMessage('USER_PORTFOLIO_COMPONENT_REMOVE_CATEGORY_CONFIRM')?>'
            }
        );
    </script>
    </script>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}