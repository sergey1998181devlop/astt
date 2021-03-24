<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 18.01.2019
 * Time: 17:28
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}
$arrNamesType = array(
    'democontent2_pi_gruzopodemnaya_tekhnika' => 'gruzopodemnaya-tekhnika',
    'democontent2_pi_zemlerojnaya_tekhnika' => 'zemlerojnaya-tekhnika',
    'democontent2_pi_dorozhnaya_tekhnika' => 'dorozhnaya-tekhnika',
    'democontent2_pi_gruzovoj_transport' => 'gruzovoj-transport'
);

//debmes($arResult['ITEMS']);
?>
<div class="page-content">
    <div class="wrapper">
        <div class="page-title">
            <h1>
                <?= Loc::getMessage('USER_TASKS_COMPONENT_TITLE') ?>
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
                    <?if( $arResult['CURRENT_USER']['UF_MODERATION_ACCESS'] == "true"  || $arResult['CURRENT_USER']['UF_MODERATION_ACCESS'] == "false" ){?>
                        <div class="h4 prevH4" >Мои заявки</div>
                    <?}?>
                    <?/*
                    <div class="sorty-panel tabs-head">
                        <ul>
                            <li class="active">
                                <a href="#task-list1">
                                    <?= Loc::getMessage('USER_TASKS_COMPONENT_TAB_TTL_1') ?>
                                </a>
                            </li>
                            <li>
                                <a href="#task-list2">
                                    <?= Loc::getMessage('USER_TASKS_COMPONENT_TAB_TTL_2') ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                    */?>
                    <div class="tabs-wrap">
                        <div class="tab active" id="task-list1">
                            <div class="user-tasks-list">
                                <?php
                                if (count($arResult['ITEMS']) > 0) {
                                    ?>
                                    <table class="table">
                                        <thead>
                                       <th>
                                            <?= Loc::getMessage('USER_TASKS_COMPONENT_table_TH_1') ?>
                                        </th>

                                        <th>
                                            <?= Loc::getMessage('USER_TASKS_COMPONENT_table_TH_2') ?>
                                        </th>
                                        <th>
                                            <?= Loc::getMessage('USER_TASKS_COMPONENT_table_TH_3') ?>
                                        </th>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($arResult['ITEMS'] as $key => $item) {

                                            $urlAll =  $item['UF_IBLOCK_TYPE'].'/'.$item['UF_IBLOCK_CODE'].'/'. $item['UF_CODE'].'/';


                                            $arButtons = \CIBlock::GetPanelButtons(
                                                $item['UF_IBLOCK_ID'],
                                                $item['UF_ITEM_ID'],
                                                0,
                                                array("SECTION_BUTTONS" => false, "SESSID" => false)
                                            );
                                            $arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
                                            $arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
                                            $this->AddEditAction(
                                                $item['UF_ITEM_ID'],
                                                $arItem['EDIT_LINK'],
                                                CIBlock::GetArrayByID(
                                                    $item['UF_IBLOCK_ID'],
                                                    "ELEMENT_EDIT"
                                                )
                                            );
                                            $this->AddDeleteAction(
                                                $item['UF_ITEM_ID'],
                                                $arItem['DELETE_LINK'],
                                                CIBlock::GetArrayByID(
                                                    $item['UF_IBLOCK_ID'],
                                                    "ELEMENT_DELETE"
                                                ),
                                                array(
                                                    "CONFIRM" => Loc::getMessage('CONFIRM_DELETE')
                                                )
                                            );
                                            ?>
                                       <?/*     <a
                                                    data-href="<?= ($item['UF_STATUS'] == 1) ? SITE_DIR . 'user/items/' . $item['UF_IBLOCK_ID'] . '-' . $item['UF_ITEM_ID'] . '/' : SITE_DIR . $item['UF_IBLOCK_TYPE'] . '/' . $item['UF_IBLOCK_CODE'] . '/' . $item['UF_CODE'] . '/' ?>"
                                                class="item" id="<?= $this->GetEditAreaId($item['UF_ITEM_ID']) ?>"> */?>
                                                <?
//                                                    echo $urlAll;
                                                ?>

                                              <td>  <?= $key + 1?></td>
                                                <td class="tdStat">
                                                    <?

                                                    switch ($item['UF_MODERATION']) {
                                                        case 0:
                                                            ?>
                                                            <div class="status  ">
                                                                <?= Loc::getMessage('USER_TASKS_STATUS_1') ?>
                                                            </div>
                                                            <?
                                                            break;
                                                        case 1:
                                                            ?>
                                                            <div class="status hold-true">
                                                                <?= Loc::getMessage('USER_TASKS_STATUS_2') ?>
                                                            </div>
                                                            <?
                                                            break;
                                                        case 2:
                                                            ?>
                                                            <div class="status  completed">
                                                                <?= Loc::getMessage('USER_TASKS_STATUS_3') ?>
                                                            </div>
                                                            <?
                                                            break;


                                                        case 5:
                                                            ?>
                                                            <div class="status hold-true">
                                                                <?= Loc::getMessage('USER_TASKS_STATUS_5') ?>
                                                            </div>
                                                            <?
                                                            break;
                                                        case 6:
                                                            ?>
                                                            <div class="status completed">
                                                                <?= Loc::getMessage('USER_TASKS_STATUS_6') ?>
                                                            </div>
                                                            <?
                                                            break;
                                                    }
                                                    ?>
                                                </td>

                                                <td class="ttl medium">
                                                    <a href="/user/tasks/detail/<?=$item['UF_ITEM_ID']?>/?UserNum=<?=$item['UF_USER_ID']?>">

                                                        <?if(!empty($item['UF_COUNT_TECH'])):?>
                                                            <?= $item['UF_NAME'] ?> <?=$item['UF_COUNT_TECH']?> шт
                                                        <?else:?>
                                                            <?= $item['UF_NAME'] ?>
                                                        <?endif;?>
                                                    </a>
                                                </td>


                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                    <?php
                                } else {
                                    ?>
                                    <div class="alert alert-info">
                                        <?= Loc::getMessage('USER_TASKS_COMPONENT_LIST_EMPTY') ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="tab" id="task-list2">
                            <div class="user-tasks-list">
                                <?php
                                if (count($arResult['EXECUTOR_ITEMS']) > 0) {
                                    ?>
                                    <table class="table">
                                        <thead>
                                        <th>
                                            <?= Loc::getMessage('USER_TASKS_COMPONENT_table_TH_1') ?>
                                        </th>
                                        <th>
                                            <?= Loc::getMessage('USER_TASKS_COMPONENT_table_TH_2') ?>
                                        </th>
                                        <th>
                                            <?= Loc::getMessage('USER_TASKS_COMPONENT_table_TH_3') ?>
                                        </th>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($arResult['EXECUTOR_ITEMS'] as $key => $item) {
                                            $arButtons = \CIBlock::GetPanelButtons(
                                                $item['IBLOCK_ID'],
                                                $item['UF_ITEM_ID'],
                                                0,
                                                array("SECTION_BUTTONS" => false, "SESSID" => false)
                                            );
                                            $arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
                                            $arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
                                            $this->AddEditAction(
                                                $item['UF_ITEM_ID'],
                                                $arItem['EDIT_LINK'],
                                                CIBlock::GetArrayByID(
                                                    $item['IBLOCK_ID'],
                                                    "ELEMENT_EDIT"
                                                )
                                            );
                                            $this->AddDeleteAction(
                                                $item['UF_ITEM_ID'],
                                                $arItem['DELETE_LINK'],
                                                CIBlock::GetArrayByID(
                                                    $item['IBLOCK_ID'],
                                                    "ELEMENT_DELETE"
                                                ),
                                                array(
                                                    "CONFIRM" => Loc::getMessage('CONFIRM_DELETE')
                                                )
                                            );
                                            ?>

                                            <tr data-href="<?= SITE_DIR . '' . $item['UF_IBLOCK_TYPE'] . '/' . $item['UF_IBLOCK_CODE'] . '/' . $item['UF_CODE'] . '/' ?>"
                                                class="item" id="<?= $this->GetEditAreaId($item['UF_ITEM_ID']) ?>">
                                                <td><?= $item['UF_ITEM_ID'] ?></td>
                                                <td class="tdStat">
                                                    <?
                                                    switch ($item['UF_STATUS']) {
                                                        case 1:
                                                        case 4:
                                                            ?>
                                                            <div class="status">
                                                                <?= Loc::getMessage('USER_TASKS_STATUS_1') ?>
                                                            </div>
                                                            <?
                                                            break;
                                                        case 2:
                                                            ?>
                                                            <div class="status hold-true">
                                                                <?= Loc::getMessage('USER_TASKS_STATUS_2') ?>
                                                            </div>
                                                            <?
                                                            break;
                                                        case 3:
                                                            ?>
                                                            <div class="status completed">
                                                                <?= Loc::getMessage('USER_TASKS_STATUS_3') ?>
                                                            </div>
                                                            <?
                                                            break;
                                                        case 5:
                                                            ?>
                                                            <div class="status hold-true">
                                                                <?= Loc::getMessage('USER_TASKS_STATUS_5') ?>
                                                            </div>
                                                            <?
                                                            break;
                                                        case 6:
                                                            ?>
                                                            <div class="status completed">
                                                                <?= Loc::getMessage('USER_TASKS_STATUS_6') ?>
                                                            </div>
                                                            <?
                                                            break;
                                                    }
                                                    ?>
                                                </td>
                                                <td class="ttl medium">
                                                    <?= $item['UF_NAME'] ?>
                                                </td>

                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                    <?php
                                } else {
                                    ?>
                                    <div class="alert alert-info">
                                        <?= Loc::getMessage('USER_TASKS_COMPONENT_LIST_EMPTY') ?>
                                    </div>
                                    <?php
                                }

                                ?>
                            </div>
                        </div>
                    </div>

                </div>

                <?if( $arResult['CURRENT_USER']['UF_MODERATION_ACCESS'] == "true"  || $arResult['CURRENT_USER']['UF_MODERATION_ACCESS'] == "false" ){?>
                    <div class="white-block">
                        <div class="h4 prevH4" >Заявки сотрудников</div>
                        <?/*
                    <div class="sorty-panel tabs-head">
                        <ul>
                            <li class="active">
                                <a href="#task-list1">
                                    <?= Loc::getMessage('USER_TASKS_COMPONENT_TAB_TTL_1') ?>
                                </a>
                            </li>
                            <li>
                                <a href="#task-list2">
                                    <?= Loc::getMessage('USER_TASKS_COMPONENT_TAB_TTL_2') ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                    */?>
                        <div class="tabs-wrap">
                            <div class="tab active" id="task-list1">
                                <div class="user-tasks-list">
                                    <?php
                                    if (count($arResult['TASKS_EMPLOYEES']) > 0) {
                                        ?>
                                        <table class="table">
                                            <thead>
                                            <th>
                                                <?= Loc::getMessage('USER_TASKS_COMPONENT_table_TH_1') ?>
                                            </th>

                                            <th>
                                                <?= Loc::getMessage('USER_TASKS_COMPONENT_table_TH_2') ?>
                                            </th>
                                            <th>
                                                <?= Loc::getMessage('USER_TASKS_COMPONENT_table_TH_3') ?>
                                            </th>
                                            </thead>
                                            <tbody>
                                            <?php
                                            foreach ($arResult['TASKS_EMPLOYEES'] as $key => $item) {

                                                $urlAll =  $item['UF_IBLOCK_TYPE'].'/'.$item['UF_IBLOCK_CODE'].'/'. $item['UF_CODE'].'/';


                                                $arButtons = \CIBlock::GetPanelButtons(
                                                    $item['UF_IBLOCK_ID'],
                                                    $item['UF_ITEM_ID'],
                                                    0,
                                                    array("SECTION_BUTTONS" => false, "SESSID" => false)
                                                );
                                                $arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
                                                $arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
                                                $this->AddEditAction(
                                                    $item['UF_ITEM_ID'],
                                                    $arItem['EDIT_LINK'],
                                                    CIBlock::GetArrayByID(
                                                        $item['UF_IBLOCK_ID'],
                                                        "ELEMENT_EDIT"
                                                    )
                                                );
                                                $this->AddDeleteAction(
                                                    $item['UF_ITEM_ID'],
                                                    $arItem['DELETE_LINK'],
                                                    CIBlock::GetArrayByID(
                                                        $item['UF_IBLOCK_ID'],
                                                        "ELEMENT_DELETE"
                                                    ),
                                                    array(
                                                        "CONFIRM" => Loc::getMessage('CONFIRM_DELETE')
                                                    )
                                                );
                                                ?>
                                                <?/*     <a
                                                    data-href="<?= ($item['UF_STATUS'] == 1) ? SITE_DIR . 'user/items/' . $item['UF_IBLOCK_ID'] . '-' . $item['UF_ITEM_ID'] . '/' : SITE_DIR . $item['UF_IBLOCK_TYPE'] . '/' . $item['UF_IBLOCK_CODE'] . '/' . $item['UF_CODE'] . '/' ?>"
                                                class="item" id="<?= $this->GetEditAreaId($item['UF_ITEM_ID']) ?>"> */?>
                                                <?
//                                                    echo $urlAll;
                                                ?>

                                                <td>  <?= $key + 1?></td>
                                                <td class="tdStat">
                                                    <?

                                                    switch ($item['UF_MODERATION']) {
                                                        case 0:
                                                            ?>
                                                            <div class="status  ">
                                                                <?= Loc::getMessage('USER_TASKS_STATUS_1') ?>
                                                            </div>
                                                            <?
                                                            break;
                                                        case 1:
                                                            ?>
                                                            <div class="status hold-true">
                                                                <?= Loc::getMessage('USER_TASKS_STATUS_2') ?>
                                                            </div>
                                                            <?
                                                            break;
                                                        case 2:
                                                            ?>
                                                            <div class="status  completed">
                                                                <?= Loc::getMessage('USER_TASKS_STATUS_3') ?>
                                                            </div>
                                                            <?
                                                            break;


                                                        case 5:
                                                            ?>
                                                            <div class="status hold-true">
                                                                <?= Loc::getMessage('USER_TASKS_STATUS_5') ?>
                                                            </div>
                                                            <?
                                                            break;
                                                        case 6:
                                                            ?>
                                                            <div class="status completed">
                                                                <?= Loc::getMessage('USER_TASKS_STATUS_6') ?>
                                                            </div>
                                                            <?
                                                            break;
                                                    }
                                                    ?>
                                                </td>

                                                <td class="ttl medium">
                                                    <a href="/user/tasks/detail/<?=$item['UF_ITEM_ID']?>/?UserNum=<?=$item['UF_USER_ID']?>">
                                                        <?= $item['UF_NAME'] ?>
                                                    </a>
                                                </td>


                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="alert alert-info">
                                            <?= Loc::getMessage('USER_TASKS_COMPONENT_LIST_EMPTY') ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="tab" id="task-list2">
                                <div class="user-tasks-list">
                                    <?php
                                    if (count($arResult['EXECUTOR_ITEMS']) > 0) {
                                        ?>
                                        <table class="table">
                                            <thead>
                                            <th>
                                                <?= Loc::getMessage('USER_TASKS_COMPONENT_table_TH_1') ?>
                                            </th>
                                            <th>
                                                <?= Loc::getMessage('USER_TASKS_COMPONENT_table_TH_2') ?>
                                            </th>
                                            <th>
                                                <?= Loc::getMessage('USER_TASKS_COMPONENT_table_TH_3') ?>
                                            </th>
                                            </thead>
                                            <tbody>
                                            <?php
                                            foreach ($arResult['EXECUTOR_ITEMS'] as $key => $item) {
                                                $arButtons = \CIBlock::GetPanelButtons(
                                                    $item['IBLOCK_ID'],
                                                    $item['UF_ITEM_ID'],
                                                    0,
                                                    array("SECTION_BUTTONS" => false, "SESSID" => false)
                                                );
                                                $arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
                                                $arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
                                                $this->AddEditAction(
                                                    $item['UF_ITEM_ID'],
                                                    $arItem['EDIT_LINK'],
                                                    CIBlock::GetArrayByID(
                                                        $item['IBLOCK_ID'],
                                                        "ELEMENT_EDIT"
                                                    )
                                                );
                                                $this->AddDeleteAction(
                                                    $item['UF_ITEM_ID'],
                                                    $arItem['DELETE_LINK'],
                                                    CIBlock::GetArrayByID(
                                                        $item['IBLOCK_ID'],
                                                        "ELEMENT_DELETE"
                                                    ),
                                                    array(
                                                        "CONFIRM" => Loc::getMessage('CONFIRM_DELETE')
                                                    )
                                                );
                                                ?>

                                                <tr data-href="<?= SITE_DIR . '' . $item['UF_IBLOCK_TYPE'] . '/' . $item['UF_IBLOCK_CODE'] . '/' . $item['UF_CODE'] . '/' ?>"
                                                    class="item" id="<?= $this->GetEditAreaId($item['UF_ITEM_ID']) ?>">
                                                    <td><?= $item['UF_ITEM_ID'] ?></td>
                                                    <td class="tdStat">
                                                        <?
                                                        switch ($item['UF_STATUS']) {
                                                            case 1:
                                                            case 4:
                                                                ?>
                                                                <div class="status">
                                                                    <?= Loc::getMessage('USER_TASKS_STATUS_1') ?>
                                                                </div>
                                                                <?
                                                                break;
                                                            case 2:
                                                                ?>
                                                                <div class="status hold-true">
                                                                    <?= Loc::getMessage('USER_TASKS_STATUS_2') ?>
                                                                </div>
                                                                <?
                                                                break;
                                                            case 3:
                                                                ?>
                                                                <div class="status completed">
                                                                    <?= Loc::getMessage('USER_TASKS_STATUS_3') ?>
                                                                </div>
                                                                <?
                                                                break;
                                                            case 5:
                                                                ?>
                                                                <div class="status hold-true">
                                                                    <?= Loc::getMessage('USER_TASKS_STATUS_5') ?>
                                                                </div>
                                                                <?
                                                                break;
                                                            case 6:
                                                                ?>
                                                                <div class="status completed">
                                                                    <?= Loc::getMessage('USER_TASKS_STATUS_6') ?>
                                                                </div>
                                                                <?
                                                                break;
                                                        }
                                                        ?>
                                                    </td>
                                                    <td class="ttl medium">
                                                        <?= $item['UF_NAME'] ?>
                                                    </td>

                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="alert alert-info">
                                            <?= Loc::getMessage('USER_TASKS_COMPONENT_LIST_EMPTY') ?>
                                        </div>
                                        <?php
                                    }

                                    ?>
                                </div>
                            </div>
                        </div>

                    </div>
                <?}?>
            </div>
        </div>
    </div>
</div>
<?php

if (method_exists($this, 'createFrame')) {
    $frame->end();
}
?>

