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

$currencyName = \Bitrix\Main\Config\Option::get(DSPI, 'currency_name');
$registrationFee = intval(\Bitrix\Main\Config\Option::get(DSPI, 'registration_fee'));
$maxImageSize = intval(\Bitrix\Main\Config\Option::get(DSPI, 'max_file_size'));
$maxFiles = intval(\Bitrix\Main\Config\Option::get(DSPI, 'max_file_size'));
$safeCrowEnabled = false;
$profile = [];

if (count($arResult['PROFILE']) > 0) {
    if (strlen($arResult['PROFILE']['UF_DATA']) > 0) {
        $profile = unserialize($arResult['PROFILE']['UF_DATA']);
    }
}

if (strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiKey')) > 0
    && strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
    $safeCrowEnabled = true;
}

?>
    <div class="page-content">
    <div class="wrapper">
        <div class="page-title">
            <h1>
                <?= Loc::getMessage('USER_SETTINGS_COMPONENT_H1') ?>
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
<!--            --><?//debmes($arResult['COMPANY_EMPLOYEES']);?>
            <div class="col-sm-8 col-xxs-12">
                <div class="white-block profile-content">
                    <div class="tasks-list">
                        <!--                                   --><?// debmes($arResult['COMPANY_EMPLOYEES']); ?>
                        <?if(!empty($arResult['COMPANY_EMPLOYEES_DETAIL'])):?>

                                <div class="task-preview task-preview-employees-fir">

                                    <div class="tbl tbl-fixed">
                                        <div class="tbc">

                                         <form id="formUpdateDataEmployees" action="<?= $APPLICATION->GetCurPage(false) ?>" >

                                             <input type="hidden" name="UpdateUsEmployeesID" value="<?=$arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['ID']?>">

                                            <div class="row">
                                                <div class="col-lg-6 col-sm-6 col-xxs-6">
                                                    <div class="form-group ">
                                                        <p   class="updateParam name_employees"><?if(!empty($arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['NAME'])):?> <?=$arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['NAME']?> <?else:?> <?endif;?></p>
                                                        <input required  type="text"
                                                               value="<?if(!empty($arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['NAME'])):?> <?=$arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['NAME']?> <?else:?> <?endif;?>"
                                                               placeholder="<?= Loc::getMessage('NAME_NEW_EMPLOYEES') ?>"
                                                               name="NAME_RMPLOYEES"
                                                               class="form-control required inpUpdateParam"
                                                        >
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-sm-6 col-xxs-6">
                                                    <div class="form-group ">
                                                        <p   class="updateParam surname_employees"><?if(!empty($arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['LAST_NAME'])):?> <?=$arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['LAST_NAME']?> <?else:?> <?endif;?></p>
                                                        <input required type="text"
                                                               value="<?if(!empty($arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['LAST_NAME'])):?> <?=$arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['LAST_NAME']?> <?else:?> <?endif;?>"
                                                               placeholder="<?= Loc::getMessage('SURNAME_EMPLOYEES') ?>"
                                                               name="SURNAME_RMPLOYEES"
                                                               class="form-control required inpUpdateParam"
                                                        >
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6 col-sm-6 col-xxs-6">
                                                    <div class="form-group ">
                                                        <p   class="updateParam phone_employees"><?if(!empty($arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['PERSONAL_PHONE'])):?> <?=$arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['PERSONAL_PHONE']?> <?else:?> <?endif;?> </p>
                                                        <?
                                                        $sub = substr($arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['PERSONAL_PHONE'] , 2);
                                                        ?>
                                                        <input required type="text"
                                                               value="<?if(!empty($arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['PERSONAL_PHONE'])):?> <?=$sub?> <?else:?> <?endif;?>"
                                                               placeholder="<?= Loc::getMessage('PHONE_EMPLOYEES') ?>"
                                                               name="PHONE_RMPLOYEES"
                                                               class="form-control required inpUpdateParam"
                                                        >
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-sm-6 col-xxs-6">
                                                    <div class="form-group ">
                                                        <p   class="updateParam email_employees"><?if(!empty($arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['EMAIL'])):?> <?=$arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['EMAIL']?> <?else:?> <?endif;?></p>
                                                        <input required type="email"
                                                               value=" <?if(!empty($arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['EMAIL'])):?> <?=$arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['EMAIL']?> <?else:?> <?endif;?>"
                                                               placeholder="<?= Loc::getMessage('EMAIL_EMPLOYEES') ?>"
                                                               name="EMAIL_RMPLOYEES"
                                                               class="form-control required inpUpdateParam"
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                             <p class="pass_encorect"></p>
                                            <div class="row rowBtn">

                                                    <div class=" btn btn-green  btnEmployeesNewFor updateParam  changeButton" >
                                                        <?= Loc::getMessage('UPDATE_AMPLOYEES') ?>
                                                    </div>
                                                <div  class=" btn btn-green  btnEmployeesNewFor inpUpdateParam  saveDataUp" >
                                                    <?= Loc::getMessage('UPDATE_AMPLOYEES_CLICK') ?>
                                                </div>
                                                <div class=" btn btn-blue  btnEmployeesNewFor inpUpdateParam inpBackParam" >
                                                    <?= Loc::getMessage('UPDATE_AMPLOYEES_CLICK_BACK') ?>
                                                </div>


<!--                                                href="/user/employees/update/?DeleteNum=--><?//=$arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['ID']?><!--"-->

                                                    <a  href="#popup-deleteEmployees" data-fancybox="" >
                                                        <div class=" btn btn-red  btnEmployeesNewFor DeleteEmployees " data-deleteEmployees="<?=$arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['ID']?>">
                                                            <?= Loc::getMessage('DELETE_AMPLOYEES') ?>
                                                        </div>
                                                    </a>


                                                </a>
                                            </div>

                                            </form>
                                        </div>

                                        <div class="popup popup-deleteEmployees" id="popup-deleteEmployees">
                                            <div class="popup-body">
                                                <div class="tabs-wrap">

                                                        <form class="ajax-form" id="formDelEmployees" action="<?= $APPLICATION->GetCurPage(false) ?>" >
                                                            <input type="hidden" name="DeleteNum" value="<?=$arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['ID']?>">
                                                            <div class="alert alert-info text-center">
                                                                <?= Loc::getMessage('TOP_DEL_EMPLOEES') ?>
                                                            </div>
                                                            <div class="inp-elEmployees">
                                                                <div class="form-group">
                                                                    <input class="form-control required " type="text" name="passConfirm"
                                                                           placeholder="<?= Loc::getMessage('TOP_AUTH_PL_PASS') ?>"
                                                                           autocomplete="off">
                                                                </div>
                                                                <p class="pass_encorect"></p>
                                                                <div class="buttonPadd">
                                                                    <div class="btn btn-blue buttonPadd-bt" >
                                                                        <?= Loc::getMessage('TOP_AUTH_BTN_REG') ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="tbc tbc-info ourBlockEmplo">
                                            <div class="price-box info-cardEmployees">
                                                                <span >
                                                                   Сводка
                                                                </span>
                                            </div>
<!--                                            --><?//debmes($arResult['COMPANY_EMPLOYEES_DETAIL']);?>
                                            <div class="list-info">
                                                <ul>
                                                    <li>
                                                        <p>
                                                            Кол-во заявок: <b><?if($arResult['COMPANY_EMPLOYEES_DETAIL']['COUNT'] > 0):?><?=$arResult['COMPANY_EMPLOYEES_DETAIL']['COUNT']?><?endif;?></b>
                                                        </p>
                                                    </li>
                                                    <li>
                                                        <p>
                                                            Активные заявки: <b><?if($arResult['COMPANY_EMPLOYEES_DETAIL']['COUNT_ACTIVE'] > 0):?><?=$arResult['COMPANY_EMPLOYEES_DETAIL']['COUNT_ACTIVE']?><?endif;?></b>
                                                        </p>
                                                    </li>
                                                    <li>
                                                        <p>Статус</p>

                                                        <?if(!empty($arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['UF_CONFIRMED']) && $arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['UF_CONFIRMED'] == "true"):?>
                                                            <div class="alert alert-success" role="alert">

                                                                <strong>Подтвержден</strong>
                                                            </div>
                                                        <?else:?>
                                                            <div  class="alert alert-danger" role="alert">
                                                                <strong>Не подтвержден</strong>
                                                            </div>
                                                        <?endif;?>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                        <?endif;?>
                </div>
                    <div class="h4"> <?= Loc::getMessage('TASK_EMPLOYEES') ?></div>
                    <div class="user-tasks-list">
                        <?php
                        if (count($arResult['COMPANY_EMPLOYEES_DETAIL']['ITEMS']) > 0) {
                            ?>
                            <table class="table">
                                <thead>
                              <?/*  <th>
                                    <?= Loc::getMessage('USER_TASKS_COMPONENT_table_TH_1') ?>
                                </th>
                                */?>
                                <th>
                                    <?= Loc::getMessage('USER_TASKS_COMPONENT_table_TH_2') ?>
                                </th>
                                <th>
                                    <?= Loc::getMessage('USER_TASKS_COMPONENT_table_TH_3') ?>
                                </th>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($arResult['COMPANY_EMPLOYEES_DETAIL']['ITEMS'] as $key => $item) {
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
<!--                                        <a href="--><?//= SITE_DIR . '' . $item['UF_IBLOCK_TYPE'] . '/' . $item['UF_IBLOCK_CODE'] . '/' . $item['UF_CODE'] . '/' ?><!--" >-->
                                       <?/*     <td><?= $item['UF_ITEM_ID'] ?></td>    */?>
<!--                                        </a>-->
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

                                        <td class="ttl medium dopListZ">


                                            <a href="/user/employees/taskEmpl/<?=$item['UF_ITEM_ID']?>/?UserNum=<?=$arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['ID']?>">
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
        </div>
    </div>

    <script>
        var maxSizeAva = parseInt(<?=($maxImageSize)?>),
            maxFiles = parseInt('<?=$maxFiles?>'),
            getCurPath = '<?=$APPLICATION->GetCurPage(false)?>',
            dropzoneMainImage = '<?=Loc::getMessage('USER_SETTINGS_COMPONENT_DROPZONE_MAIN_IMAGE')?>',
            templatePath = '<?=SITE_TEMPLATE_PATH?>';
    </script>
    </script>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}