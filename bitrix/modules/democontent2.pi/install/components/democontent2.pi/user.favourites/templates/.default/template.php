<?php
/**
 * Date: 08.07.2019
 * Time: 16:52
 * User: Ruslan Semagin
 * Company: PIXEL365
 * Web: https://pixel365.ru
 * Email: pixel.365.24@gmail.com
 * Phone: +7 (495) 005-23-76
 * Skype: pixel365
 * Product Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 * Use of this code is allowed only under the condition of full compliance with the terms of the license agreement,
 * and only as part of the product.
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
                    <?= Loc::getMessage('NOTIFICATIONS_COMPONENT_TITLE') ?>
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
                    <div class="white-block notifications-container">
                        <?php
                        if (!count($arResult['ITEMS'])) {
                            ?>
                            <div class="alert alert-info">
                                <?= Loc::getMessage('NO_RESULTS') ?>
                            </div>
                            <?php
                        } else {
                            $i = 0;
                            foreach ($arResult['ITEMS'] as $item) {
                                $i++;
                                if ($item['PERSONAL_PHOTO'] > 0) {
                                    $ava = CFile::ResizeImageGet($item['PERSONAL_PHOTO'],
                                        array(
                                            'width' => 150,
                                            'height' => 150
                                        ),
                                        BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                        true
                                    );
                                }
                                ?>
                                <div id="executor-<?= $item['ID'] ?>" class="profile-preview">
                                    <div class="remove-item" data-id="<?= $item['ID'] ?>">
                                        <?= Loc::getMessage('REMOVE') ?>
                                    </div>
                                    <div class="tbl tbl-fixed">
                                        <div class="tbc pict">
                                            <div class="pict-wrap">
                                                <?php
                                                if ($chatEnabled) {
                                                    ?>
                                                    <div class="status-indicator uid<?= \Democontent2\Pi\Utils::getChatId($item['ID']) ?>"></div>
                                                    <?php
                                                }
                                                ?>
                                                <? if ($item['PERSONAL_PHOTO'] > 0): ?>
                                                    <div class="object-fit-container">
                                                        <img
                                                                src="<?= $ava['src'] ?>"
                                                                alt="" data-object-fit="cover"
                                                                data-object-position="50% 50%"/>
                                                    </div>
                                                <? else: ?>
                                                    <svg class="icon icon_user">
                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#user"></use>
                                                    </svg>
                                                <? endif ?>
                                            </div>
                                        </div>
                                        <div class="tbc desc">
                                            <div class="name medium">
                                                <?= $item['NAME'] ?>
                                                <?= $item['LAST_NAME'] ?>
                                            </div>

                                            <?php
                                            if (intval($item['UF_DSPI_CITY'])) {
                                                if (isset($arResult['CITIES'][$item['UF_DSPI_CITY']])) {
                                                    ?>
                                                    <div class="location">
                                                        <div class="location-box">
                                                            <svg class="icon icon_location">
                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#location"></use>
                                                            </svg>
                                                            <?= $arResult['CITIES'][$item['UF_DSPI_CITY']]['name'] ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            <div class="indicators">
                                                <?php
                                                if ($item['UF_DSPI_DOCUMENTS']) {
                                                    ?>
                                                    <div class="verification-box">
                                                        <svg class="icon icon_checkmark">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#checkmark"></use>
                                                        </svg>
                                                        <?= Loc::getMessage('USERS_COMPONENT_VERIFICATION') ?>
                                                    </div>
                                                    <?php
                                                }

                                                if ($item['CARD']) {
                                                    ?>
                                                    <div class="security-box">
                                                        <svg class="icon icon_shield_doc">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#shield-doc"></use>
                                                        </svg>
                                                        <?= Loc::getMessage('USERS_COMPONENT_SECURITY') ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="txt">
                                                <?php
                                                if (isset($item['PROFILE']['UF_DATA'])) {
                                                    $profileData = unserialize($item['PROFILE']['UF_DATA']);
                                                    if (isset($profileData['description']) && strlen($profileData['description']) > 0) {
                                                        ?>
                                                        <p><?= $profileData['description'] ?></p>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <div class="info">
                                                <div class="date-box">
                                                    <svg class="icon icon_time">
                                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#time"></use>
                                                    </svg>
                                                    <span title="<?= date('Y-m-d H:i', strtotime($item['DATE_REGISTER'])) ?>"
                                                          class="timestamp"></span>
                                                </div>
                                                <div class="feedback">
                                                    <div class="assessment-box">
                                                        <svg class="icon icon_star-empty">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-empty"></use>
                                                        </svg>
                                                        <svg class="icon icon_star-empty">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-empty"></use>
                                                        </svg>
                                                        <svg class="icon icon_star-empty">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-empty"></use>
                                                        </svg>
                                                        <svg class="icon icon_star-empty">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-empty"></use>
                                                        </svg>
                                                        <svg class="icon icon_star-empty">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-empty"></use>
                                                        </svg>
                                                        <div class="assessment-in"
                                                             style="width:<?= $item['CURRENT_RATING']['percent'] * 1 ?>%">
                                                            <svg class="icon icon_star-full">
                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-full"></use>
                                                            </svg>
                                                            <svg class="icon icon_star-full">
                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-full"></use>
                                                            </svg>
                                                            <svg class="icon icon_star-full">
                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-full"></use>
                                                            </svg>
                                                            <svg class="icon icon_star-full">
                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-full"></use>
                                                            </svg>
                                                            <svg class="icon icon_star-full">
                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#star-full"></use>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="likes">
                                                        <span class="like">
                                                            <svg class="icon icon_thumbs-o-up">
                                                              <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#thumbs-o-up"></use>
                                                            </svg>
                                                            <?= $item['CURRENT_RATING']['positive'] * 1 ?>
                                                        </span>
                                                        <span class="dislike">
                                                            <svg class="icon icon_thumbs-o-down">
                                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#thumbs-o-down"></use>

                                                            </svg>
                                                                    <?= $item['CURRENT_RATING']['negative'] * 1 ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="lnk-abs" href="<?= SITE_DIR ?>user/<?= $item['ID'] ?>/"></a>
                                </div>
                                <?php
                                if ($i == 1) {
                                    try {
                                        if (\Bitrix\Main\IO\File::isFileExists(
                                            \Bitrix\Main\IO\Path::normalize(
                                                \Bitrix\Main\Application::getDocumentRoot() . SITE_TEMPLATE_PATH . "/inc/adv/list.php"
                                            )
                                        )) {
                                            $APPLICATION->IncludeComponent(
                                                "bitrix:main.include", "",
                                                array(
                                                    "AREA_FILE_SHOW" => "file",
                                                    "PATH" => SITE_TEMPLATE_PATH . "/inc/adv/list.php",
                                                    "EDIT_TEMPLATE" => "include_areas_template.php",
                                                    'MODE' => 'html'
                                                ),
                                                false
                                            );
                                        }
                                    } catch (\Bitrix\Main\IO\InvalidPathException $e) {
                                    }
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        BX.message({userFavouritesPath: '<?=$this->GetFolder()?>/ajax.php'});
    </script>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}
