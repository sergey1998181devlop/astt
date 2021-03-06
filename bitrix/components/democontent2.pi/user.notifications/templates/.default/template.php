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
                                <?= Loc::getMessage('NOTIFICATIONS_COMPONENT_EMPTY') ?>
                            </div>
                            <?php
                        } else {
                            $idUser = $USER->getID();

                            foreach ($arResult['ITEMS'] as $key => $item) {


                                $data = unserialize($item['UF_DATA']);

                                ?>
                                <div class="item">
                                    <div class="date">
                                        <div class="date-box">
                                            <svg class="icon icon_time">
                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite-svg.svg#time"></use>
                                            </svg>
                                            <span title="<?= date('Y-m-d H:i', strtotime($item['UF_CREATED_AT'])) ?>"
                                                  class="timestamp"></span>
                                        </div>
                                    </div>

                                    <? if (array_key_exists('text', $data)): ?>
                                        <?= $data['text'] ?>
                                    <? else: ?>
                                        <?
                                        $data['SITE_DIR'] = SITE_DIR;
                                        $data['SITE_TEMPLATE_PATH'] = SITE_TEMPLATE_PATH;
                                        $companyId = [];
                                        if($item['UF_TYPE'] == "CompanyAdd"){
                                           $mess = unserialize($item['UF_DATA']);
                                            $companyId['companyId'] = $mess['CompanyId'];
                                            $companyId['userId'] = $mess['userId'];

                                        }
                                        if($item['UF_TYPE'] == "CompanyFalseModeration"){
                                            $mess = unserialize($item['UF_DATA']);
                                            $companyId['companyId'] = $mess['CompanyId'];
                                            $companyId['userId'] = $mess['userId'];

                                        }

                                        switch ($item['UF_TYPE']) {
                                            case 'CompanyAdd':
                                                echo '???????????????? ??? <a href="/user/moderataion/company/'. $companyId['companyId'].'/?us='. $companyId['userId'].' ">'. $companyId['companyId']. '</a> '.  '?????????????????? ?? ?????????????? ??????????????????';
                                             break;
                                             case 'CompanyFalseModeration':
                                                echo '<a href="/user/company/"> ????????????????  </a> ?????????????????? ?????????????????????? ';
                                             break;
                                             case 'CompanyTrueModeration':
                                                echo '<a href="/user/company/"> ????????????????  </a> ???????????????????????? ?????????????????????? ';
                                             break;


                                            case 'responseCandidate':
                                            case 'moderationRefusal':
//                                                echo Loc::getMessage('NOTIFICATION_' . $item['UF_TYPE'], $data);
                                            echo '?????????????? ??? <a href="/user/tasks/detail/'.$data['taskId'].'/">'.$data['taskId']. '</a> '. Loc::getMessage('NOTIFICATION_' . $item['UF_TYPE'], $data);
                                                break;
                                            case 'responseAdd':
                                            case 'incomingResponse':
                                            case 'taskIsClosed':
                                            case 'executorRemoved':
                                            case 'safeCrowOrderPaid':
                                            case 'closeTaskStage':
                                            case 'stageComplainOpen':
                                            case 'taskComplainOpen':
                                            case 'closeStageExecutor':
                                            case 'executorCompleted':
                                            case 'responseDenied':
                                            case 'responseExecutor':
                                            case 'moderationCompleted':
                                            echo '?????????????? ??? <a href="/user/tasks/detail/'.$data['taskId'].'/">'.$data['taskId']. '</a> '. Loc::getMessage('NOTIFICATION_' . $item['UF_TYPE'], $data);

                                            break;
                                            case 'addTask':
                                                echo '?????????????? ??? <a href="/user/tasks/detail/'.$data['taskId'].'/">'.$data['taskId']. '</a> '. Loc::getMessage('NOTIFICATION_' . $item['UF_TYPE'], $data);

                                             break;
                                             case 'SuccessEditTask':
                                                 echo '?????????????? ??? <a href="/user/tasks/detail/'.$data['taskId'].'/">'.$data['taskId']. '</a> '.  Loc::getMessage('NOTIFICATION_' . $item['UF_TYPE'], $data);



                                             break;

                                            default:

                                        }
                                        ?>
                                    <? endif ?>
                                </div>
                                <?
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}
