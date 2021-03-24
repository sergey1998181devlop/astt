<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Сервис поиска исполнителей");
$APPLICATION->SetTitle("Сервис поиска исполнителей");

\Bitrix\Main\Loader::includeModule('democontent2.pi');
?>

    <div class="main-bannner"
         style="background: url('<?= SITE_TEMPLATE_PATH ?>/images/back.png') center center no-repeat; background-size: cover;">
        <div class="floatEcs flotrigtFlnew" style="background-image: url('<?= SITE_TEMPLATE_PATH ?>/images/floatEcs.png')">
            <img src="<?= SITE_TEMPLATE_PATH ?>/images/floatEcs.png">
        </div>

        <div class="flotrigtFl" style="background-image: url('<?= SITE_TEMPLATE_PATH ?>/images/floatEcsright.png')" >
<!--            <img src="--><?//= SITE_TEMPLATE_PATH ?><!--/images/floatEcsright.png">-->
        </div>
        <div class="titleSearch2-block">
            <div class="titleSearch2">

                <h2 class="title h2 upper text-center">Выберите профессионалов под свои задачи</h2>
                <div class="subtitle text-center">
                    На нашем сервисе несколько сотен проверенных собственников строительной техники готовы помочь вам в решении самых разнообразных задач
                </div>
            </div>
        </div>


        <div class="wrapper">
            <?php
            $APPLICATION->IncludeComponent(
                'democontent2.pi:search',
                ''
            );
            ?>
        </div>
    <div class="container-fluid new-block-float">

        <div class="col-md-6">

            <div class="rect_left">
                <div class="rect_left_backgr">

                </div>
                <div class="titleSearch">
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
                </div>
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/rect_left.png">
                <div class="categories-list-container-leftFloat">
                    <img src="<?= SITE_TEMPLATE_PATH ?>/images/leftFloat.png">
                </div>

            </div>
        </div>
        <div class="col-md-6">
            <div class="peopleInhe" style="background-image: url('<?= SITE_TEMPLATE_PATH ?>/images/peopleInhe.png')">
<!--                <img src="--><?//= SITE_TEMPLATE_PATH ?><!--/images/peopleInhe.png">-->
            </div>
            <div class="rect_right">

                <div class="rect_right_backgr">


                </div>
                <div class="rect_right_backgr-bgr" style='background-image: url("<?= SITE_TEMPLATE_PATH ?>/images/rect_right.png")'>

                </div>
<!--                <img src="--><?//= SITE_TEMPLATE_PATH ?><!--/images/rect_right.png">-->
                        <div class="categories-list-container-rightFloat">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/rightFloat.png">
                        </div>
            </div>
        </div>

    </div>



    </div>

<div class="wrapper wrapper-sm">
    <div class="col-md-6 col-sm-12"></div>
    <div class="col-md-6 col-sm-12 new_upper_prem-het-block">
        <div class="new_upper_prem">
            <p>Наши </p><p>преимущества</p>
        </div>
    </div>
</div>
<?
$test = "SELECT * `b_blog `";
?>
<?php
$APPLICATION->IncludeComponent(
    "bitrix:main.include", "",
    array(
        "AREA_FILE_SHOW" => "file",
        "PATH" => SITE_TEMPLATE_PATH . "/inc/index/advantages.php",
        "EDIT_TEMPLATE" => "include_areas_template.php",
        'MODE' => 'html'
    ),
    false
);

$APPLICATION->IncludeComponent(
    'democontent2.pi:catalog.menu',
    'catalog.users'
);
$_POST['limitMainScreen'] = 5;
$APPLICATION->IncludeComponent(
    'democontent2.pi:all.tasks',
    '',
    array(
        'LIMIT' => 15
    )
);
?>
<div class="what-we-work"  >
    <div class="minus-one-fl-bg" style="">
        <img src="<?=SITE_TEMPLATE_PATH?>/images/back-blocks/4.png">
    </div>
    <div class="one-fl-bg" style="">
        <img src="<?=SITE_TEMPLATE_PATH?>/images/back-blocks/1.png">
    </div>
    <div class="twe-fl-bg" style="">
        <img src="<?=SITE_TEMPLATE_PATH?>/images/back-blocks/2.png">
    </div>
    <div class="tour-fl-bg" style="">
        <img src="<?=SITE_TEMPLATE_PATH?>/images/back-blocks/3.png">
    </div>


    <div class="wrapper wrapper-fot-gl">
        <div class="row">
            <div class="col-md-6 col-sm-12 block-rorate-player" >



                <div class="li-tap-blog-rotate">
                    <!--                    rotateWeWork.png-->
                    <!--                                    style="background-image: url('--><?//=SITE_TEMPLATE_PATH?><!--/images/block_2.png')"-->
                    <div   class="  li-tap li-tap-left" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/images/rotateWeWork.png')">
                            <p class="titile-num">
                                <span>1</span>
                            </p>
                            <div class="what-we-work-reg">
                                <img src="<?=SITE_TEMPLATE_PATH?>/images/what-we-work/regl.png">
                            </div>
                            <p class="titile-text"><span>РЕГИСТРАЦИЯ</span></p>
                    </div>
                    <div   class="  li-tap  li-tap-rotate" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/images/rotateWeWork.png')">

                        <p class="titile-text"><span>ПОДТВЕРЖДЕНИЕ КОМПАНИИ</span></p>
                            <div class="what-we-work-reg">
                                <img src="<?=SITE_TEMPLATE_PATH?>/images/what-we-work/tabl-pens.png">
                            </div>
                        <p class="titile-num">
                            <span>2</span>
                        </p>


                    </div>
                    <div   class="  li-tap li-tap-left" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/images/rotateWeWork.png')">
                        <p class="titile-num"> <span>3</span></p>
                        <div class="what-we-work-reg">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/what-we-work/excavate.png">
                        </div>
                        <p class="titile-text"><span>РАБОТА ПО ЗАЯВКЕ</span></p>
                    </div>
                    <div   class="  li-tap li-tap-rotate" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/images/rotateWeWork.png')">


                        <p class="titile-text"><span>ДОВОЛЬНЫЕ КЛИЕНТЫ И ПОСТАВЩИКИ</span></p>
                        <div class="what-we-work-reg">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/what-we-work/super-b.png">
                        </div>
                        <p class="titile-num">
                            <span>4</span>
                        </p>

                    </div>

                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="title-what-we-work">
                    <p>КАК МЫ РАБОТАЕМ</p>
                </div>
                <iframe class="iframe-y"  src="https://www.youtube.com/embed/FeRO2x-FXzc" frameborder="0" allowfullscreen></iframe>
               <a href="/user/create/" class="create-task-mainstr">

                   <div class="title-what-we-work-send-m">
                       <p>ОСТАВИТЬ ЗАЯВКУ</p>
                   </div>
               </a>

            </div>
        </div>
    </div>
</div>
<div class="banner-demo-bottom">
<!--    <img src="--><?//=SITE_TEMPLATE_PATH?><!--/images/banners/banners-all-tasks-main.png">-->
    <div class="banner-demo-bottom-inner" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/images/banners/banners-all-tasks-main.png')">

    </div>
</div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>