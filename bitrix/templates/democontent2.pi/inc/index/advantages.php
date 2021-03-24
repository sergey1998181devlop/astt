<?
$this->addExternalCss(SITE_TEMPLATE_PATH."/js/swiper-slider/swiper-bundle.min.css");
$this->addExternalJS(SITE_TEMPLATE_PATH."/js/swiper-slider/swiper-bundle.min.js");

?>


<div class="section advantages-container">
    <div class="advantages-container-podlog">

    </div>


    <div class="swiper-hear-block">

        <div class="swiper-hear-block-inner">

            <div class="wrapper  ">

                <div class="play-prev ">
                    <img src="<?= SITE_TEMPLATE_PATH ?>/images/play-prev.png">
                </div>

                <div class="row wrapper2 ">


                    <div class="swiper-container1">
                        <div class="swiper-wrapper">
                            <div class="col-sm-12 col-md-4 col-xxs-12 swiper-slide">
                                <div class="block">
                                    <div class="img-item-slide">
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/images/slide1.svg">
                                    </div>
                                    <div class="title-item-slide">
                                        <div class="ttl upper medium">Безопасность</div>
                                        <div class="txt">
                                            Реальные компании, проверенные поставщики и заказчики.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xxs-12 swiper-slide">
                                <div class="block">
                                    <div class="img-item-slide">
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/images/slide3.svg" >
                                    </div>
                                    <div class="title-item-slide">
                                        <div class="ttl upper medium">База поставщиков</div>
                                        <div class="txt">
                                            Несколько сотен компаний с собственном парком техники.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xxs-12 swiper-slide">
                                <div class="block">
                                    <div class="img-item-slide">
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/images/slide4.svg" >
                                    </div>
                                    <div class="title-item-slide">
                                        <div class="ttl upper medium">Реальный рейтинг</div>
                                        <div class="txt">
                                            Без накруток, все претензии рассматриваются публично.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xxs-12 swiper-slide">
                                <div class="block">
                                    <div class="img-item-slide">
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/images/slide2.svg" >
                                    </div>
                                    <div class="title-item-slide">
                                        <div class="ttl upper medium">Постоянные заказы</div>
                                        <div class="txt">
                                            Ежедневные обновления, актуальные заявки на спецтехнику.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xxs-12 swiper-slide">
                                <div class="block">
                                    <div class="img-item-slide">
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/images/slide5.svg" >
                                    </div>
                                    <div class="title-item-slide">
                                        <div class="ttl upper medium">Без посредников</div>
                                        <div class="txt">
                                            Работа напрямую, заказчики и собственники техники.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>








                </div>

                <div class="play-next ">
                    <img src="<?= SITE_TEMPLATE_PATH ?>/images/play-next.png">
                </div>

            </div>

        </div>

    </div>
</div>
<div class="container-fluid container-fluid-dostig">

    <div class="wrapper wrapper-dostidje ">
        <div class="row">
            <div class="row-dostidje-left">
                <div class="block-btn-allt">
                    <p>заявки</p>
                    <div class="innerBlockallt">

                    </div>
                </div>
            </div>
            <div class="row-dostidje">

                <div class="row-dostidje-de">
                    <p>1000</p>
                    <p>компаний</p>
                </div>
                <div class="row-dostidje-de">
                    <p>2000</p>
                    <p>заявок</p>
                </div>
                <div class="row-dostidje-de">
                    <p>500</p>
                    <p>поставщиков</p>
                </div>

            </div>

        </div>
    </div>
</div>






<script>
    var swiper = new Swiper('.swiper-container1', {
        loop: true,


        breakpoints: {
            320: {
                slidesPerView: 'auto',
                spaceBetween: 0,
            },
            480: {
                slidesPerView: 'auto',
                spaceBetween: 0,
            },
            650: {
                // slidesOffsetAfter:30,
                slidesPerView: 'auto',
                spaceBetween: 0,
            },
            // when window width is >= 768px
            768: {
                // slidesOffsetAfter:30,
                slidesPerView: 'auto',
                spaceBetween: 0,

            },
            // when window width is >= 480px
            992: {
                // slidesOffsetBefore:30,
                slidesPerView: 'auto',
                spaceBetween: 0,
            },
            // when window width is >= 640px
            1200: {
                slidesPerView: 'auto',
                spaceBetween: 5
            }
        },
        spaceBetween:10,
        direction: 'horizontal',
        // paginationClickable: true,
        // centeredSlides: true,
        slidesPerView: 'auto',
        // spaceBetween : '',
        autoHeight: true,
        slidesOffsetBefore:50,
        slidesOffsetAfter:50


    });
    $('.play-next').on('click' , function () {
        swiper.slideNext();
    })
    $('.play-prev').on('click' , function () {
        swiper.slidePrev();
    })
</script>
