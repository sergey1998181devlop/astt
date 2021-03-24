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

$uid = intval($USER->GetID());
$userHash = \Democontent2\Pi\Utils::getChatId($USER->GetID());

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}
?>
    <div class="page-content">
        <div class="wrapper">
            <div class="page-title">
                <h1>
                    <?= Loc::getMessage('USER_CHAT_COMPONENT_TITLE') ?>
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
                        <div class="chat-container">
                            <?php
                            if (count($arResult['CHATS'])) {
                                ?>
                                <div id="list-message" class="dialogs-window">
                                    <?php
                                    foreach ($arResult['CHATS'] as $chat) {
                                        ?>
                                        <div class="item">
                                            <div class="left">
                                            <span class="new<?= ($chat['unreadMessages'] > 0) ? ' active' : '' ?>">
                                                <?= $chat['unreadMessages'] ?>
                                            </span>
                                                <div class="ttl semibold">
                                                    <?= $chat['params']['UF_NAME'] ?>
                                                </div>
                                            </div>
                                            <a href="<?= SITE_DIR ?><?= $chat['params']['UF_IBLOCK_TYPE'] ?>/<?= $chat['params']['UF_IBLOCK_CODE'] ?>/<?= $chat['params']['UF_CODE'] ?>/#!<?= $chat['roomId'] ?>"
                                               class="lnk-abs"></a>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="alert alert-info">
                                    <?= Loc::getMessage('USER_DIRECT_COMPONENT_MESSAGE_EMPTY') ?>
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