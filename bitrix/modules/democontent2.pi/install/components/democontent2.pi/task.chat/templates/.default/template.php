<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.03.2019
 * Time: 16:54
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

$uidHash = \Democontent2\Pi\Utils::getChatId($USER->GetID());
$url = ((\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->isHttps()) ? 'https://' : 'http://')
    . \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getHttpHost();

?>
    <div id="chat-container" class="chat-individual">
        <div class="close">
            <svg class="icon icon_close">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#close"></use>
            </svg>
        </div>
        <div class="wrapper">
            <div class="chat-in">
                <div class="head">
                    <div class="title"><?= $arParams['title'] ?></div>
                </div>
                <div class="messages-list scroll-block">
                    <div id="messages-items" class="messages-items">
                        <?php
                        foreach ($arResult['ITEMS'] as $item) {
                            if ($item->userId == $uidHash) {
                                ?>
                                <div class="item clearfix readStatus<?= $arResult['ROOM_ID'] ?><?= ($item->status) ? ' read' : '' ?>">
                                    <div class="info">
                                        <span class="bold"><?= Loc::getMessage('TASK_CHAT_YOU') ?></span>:
                                        <span class="timestamp" data-time="<?= ($item->time / 1000) ?>"
                                              title="<?= date('Y-m-d H:i:s', ($item->time / 1000)) ?>"></span>
                                    </div>
                                    <div class="message">
                                        <?php
                                        switch ($item->type) {
                                            case 'text':
                                                ?>
                                                <?= TxtToHTML($item->message) ?>
                                                <?php
                                                break;
                                            case 'file':
                                                ?>
                                                <?= Loc::getMessage('TASK_CHAT_FILE') ?>
                                                <a href="<?= $url ?><?= $item->file->path ?>" target="_blank">
                                                    <?= $item->file->description ?>
                                                </a>
                                                <?php
                                                break;
                                        }
                                        ?>
                                        <span id="<?= (string)$item->_id ?>" class="message-status">
                                            <svg class="icon icon_check">
                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#check"></use>
                                            </svg>
                                            <svg class="icon icon_check">
                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#check"></use>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="item clearfix you">
                                    <div class="info">
                                        <span class="bold"><?= $item->userName ?></span>:
                                        <span class="timestamp" data-time="<?= ($item->time / 1000) ?>"
                                              title="<?= date('Y-m-d H:i:s', ($item->time / 1000)) ?>"></span>
                                    </div>
                                    <div class="message">
                                        <?php
                                        switch ($item->type) {
                                            case 'text':
                                                ?>
                                                <?= TxtToHTML($item->message) ?>
                                                <?php
                                                break;
                                            case 'file':
                                                ?>
                                                <?= Loc::getMessage('TASK_CHAT_FILE') ?>
                                                <a href="<?= $url ?><?= $item->file->path ?>" target="_blank">
                                                    <?= $item->file->description ?>
                                                </a>
                                                <?php
                                                break;
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <div id="typingStatus" class="text-input">
                    <div class="loader">
                        <div class="fountainG" id="fountainG_1"></div>
                        <div class="fountainG" id="fountainG_2"></div>
                        <div class="fountainG" id="fountainG_3"></div>
                        <div class="fountainG" id="fountainG_4"></div>
                        <div class="fountainG" id="fountainG_5"></div>
                        <div class="fountainG" id="fountainG_6"></div>
                        <div class="fountainG" id="fountainG_7"></div>
                        <div class="fountainG" id="fountainG_8"></div>
                    </div>
                    <span id="typingStatusName"></span>
                </div>

                <div class="dropzone dropzone-files__files form-group">
                    <div class="dz-default dz-message">
                        <div class="dz-message-title">
                            <svg class="icon icon_file">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#file"></use>
                            </svg>
                            <?= Loc::getMessage('TASK_CHAT_DRAG_DROP_FILES') ?>
                        </div>
                    </div>
                </div>
                <div class="bottom">
                    <a href="#" class="btn-file">
                        <svg class="icon icon_clip">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#clip"></use>
                        </svg>
                    </a>
                    <div class="footnote-mes">
                        <?= Loc::getMessage('TASK_CHAT_FOOTNOTE_MESSAGE') ?>
                    </div>
                    <textarea id="inputMessage" class="form-control inputMessage"></textarea>
                    <button class="sendMessage">
                        <svg class="icon icon_paper-plane">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#paper-plane"></use>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        BX.message(
            {
                taskChatButton: '<?=Loc::getMessage('TASK_CHAT_BUTTON')?>',
                taskChatPath: '<?=$this->GetFolder()?>/ajax.php',
                taskChatYou: '<?=Loc::getMessage('TASK_CHAT_YOU')?>',
                taskChatFile: '<?=Loc::getMessage('TASK_CHAT_FILE')?>',
                taskChatRoomId: '<?=$arResult['ROOM_ID']?>',
                taskId: '<?=$arParams['taskId']?>',
                taskIBlockId: '<?=$arParams['iBlockId']?>',
                taskUid: '<?=\Democontent2\Pi\Utils::getChatId($USER->GetID())?>',
                taskUnread: parseInt('<?=intval($arResult['UNREAD_MESSAGES'])?>'),
                realName: '<?=$arResult['USER']['NAME']?>',
                userHash: '<?=($arParams['ownerId'] == $USER->GetID()) ? \Democontent2\Pi\Utils::getChatId($arParams['executorId']) : \Democontent2\Pi\Utils::getChatId($arParams['ownerId'])?>',
                siteTemplatePath: '<?=SITE_TEMPLATE_PATH?>'
            }
        );
    </script>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}