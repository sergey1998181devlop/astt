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
                    <?= Loc::getMessage('EMPLOYEES_H1') ?>
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


                        <div class="tabs-wrap">
                            <div class="tab active" id="tab-profile1">

                                <div class="tasks-list">
                                <?if($arResult['NOTMODERATIONCOMPAANY'] !== "Y"):?>
                                    <?if(!empty($arResult['COMPANY_EMPLOYEES']['ITEMS_USERS'])):?>
                                        <?foreach ($arResult['COMPANY_EMPLOYEES']['ITEMS_USERS'] as $id => $value):?>
                                                <div class="task-preview task-preview-employees-fir">

                                                    <div class="tbl tbl-fixed">
                                                        <div class="tbc">


                                                                <div class="row">

                                                                    <div class="col-lg-6 col-sm-6 col-xxs-6">
                                                                        <div class="form-group ">
                                                                            <p   class="name_employees"> <?if(!empty($value['NAME'])):?> <?=$value['NAME']?> <?else:?> <?endif;?></p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6 col-sm-6 col-xxs-6">
                                                                        <div class="form-group ">
                                                                            <p   class="surname_employees"> <?if(!empty($value['LAST_NAME'])):?> <?=$value['LAST_NAME']?> <?else:?> <?endif;?></p>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-lg-6 col-sm-6 col-xxs-6">
                                                                        <div class="form-group ">
                                                                            <p   class="phone_employees"> <?if(!empty($value['PERSONAL_PHONE'])):?> <?=$value['PERSONAL_PHONE']?> <?else:?> <?endif;?></p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6 col-sm-6 col-xxs-6">
                                                                        <div class="form-group ">
                                                                            <p   class="email_employees"> <?if(!empty($value['EMAIL'])):?> <?=$value['EMAIL']?> <?else:?> <?endif;?></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row rowBtn">
                                                                    <a href="/user/employees/update/?DetailNum=<?=$value['ID']?>">

                                                                        <div class=" btn btn-green  btnEmployeesNewFor " >
                                                                            <?= Loc::getMessage('UPDATE_AMPLOYEES') ?>
                                                                        </div>

                                                                    </a>
                                                                </div>


                                                        </div>
                                                        <div class="tbc tbc-info ourBlockEmplo">
                                                            <div class="price-box info-cardEmployees">
                                                                <span >
                                                                   Сводка
                                                                </span>
                                                            </div>
                                                            <div class="list-info">
                                                                <ul>
                                                                    <li>
                                                                        <p>
                                                                            Кол-во заявок: <b><?if(!empty($value['COUNTS']['COUNT_ELEMENTS'])):?><?=$value['COUNTS']['COUNT_ELEMENTS']?><?else:?>0<?endif;?></b>
                                                                        </p>
                                                                    </li>
                                                                    <li>
                                                                        <p>
                                                                            Активные заявки: <b><?if(!empty($value['COUNTS']['COUNT_ELEMENTS_ACTIVE'])):?><?=$value['COUNTS']['COUNT_ELEMENTS_ACTIVE']?><?else:?>0<?endif;?></b>
                                                                        </p>
                                                                    </li>
                                                                    <li>
                                                                            <p>Статус</p>
                                                                        <?if(!empty($value['UF_CONFIRMED']) && $value['UF_CONFIRMED'] == "true"):?>
                                                                            <div class="alert alert-success" role="alert">
                                                                                <strong>Подтвержден</strong>
                                                                            </div>
                                                                        <?else:?>
                                                                            <div div class="alert alert-danger" role="alert">
                                                                                <strong>Не подтвержден</strong>
                                                                            </div>
                                                                        <?endif;?>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                        <?endforeach;?>
                                    <?endif;?>


                                    <div class="task-preview task-preview-employees">

                                        <div class="tbl tbl-fixed">
                                            <div class="tbc">

                                                <form action="<?= $APPLICATION->GetCurPage(false) ?>" method="post" enctype="multipart/form-data" id="EmplyeesNew" >
                                                    <div class="row">

                                                        <div class="col-lg-6 col-sm-6 col-xxs-6">
                                                            <div class="form-group ">

                                                                <input type="text" name="NAME_NEW_EMPLOYEES" required  placeholder="<?= Loc::getMessage('NAME_NEW_EMPLOYEES') ?>"  class="name_employees" value="">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-6 col-xxs-6">
                                                            <div class="form-group ">

                                                                <input type="text" name="SURNAME_EMPLOYEES"   required placeholder="<?= Loc::getMessage('SURNAME_EMPLOYEES') ?>" class="surname_employees" value="">
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-6 col-xxs-6">
                                                            <div class="form-group ">

                                                                <input type="text" name="PHONE_EMPLOYEES" required  placeholder="<?= Loc::getMessage('PHONE_EMPLOYEES') ?>"  class="phone_employees" value="">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-6 col-xxs-6">
                                                            <div class="form-group ">

                                                                <input type="text" name="EMAIL_EMPLOYEES" required placeholder="<?= Loc::getMessage('EMAIL_EMPLOYEES') ?>"  class="email_employees" value="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row rowBtn">
                                                        <div class=" btn btn-green  btnEmployeesNew " >
                                                            <?= Loc::getMessage('ADD_AMPLOYEES') ?>
                                                        </div>
                                                    </div>
                                                </form>

                                            </div>



                                        </div>

                                    </div>
                                    <?else:?>
                                    <div class="alert alert-info" role="alert">
                                        У вас нет подтвержденной компании
                                    </div>
                                    <?endif;?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                                </div>

                            </div>
                    </div>
                </div>
            </div>




        </div>
    </div>

    <div class="popup popup-registration" id="popup-notification">

        <div class="popup-body">
            <div class="tabs-wrap">
                <div div class="alert alert-danger danger-notific " role="alert">
                    <strong>  Такой пользователь уже существует </strong>
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