<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

use \Bitrix\Main\Localization\Loc;
Loc::loadLanguageFile(__FILE__);

return array(
	'name' => Loc::getMessage('LANDING_DEMO_SPA_TITLE'),
	'description' => Loc::getMessage('LANDING_DEMO_SPA_DESCRIPTION'),
	'fields' => array(
		'ADDITIONAL_FIELDS' => array(
			'THEME_CODE' => 'spa',

			'METAOG_IMAGE' => 'https://cdn.bitrix24.site/bitrix/images/demo/page/spa/preview.jpg',
			'METAOG_TITLE' => Loc::getMessage('LANDING_DEMO_SPA_TITLE'),
			'METAOG_DESCRIPTION' => Loc::getMessage('LANDING_DEMO_SPA_DESCRIPTION'),
			'METAMAIN_TITLE' => Loc::getMessage('LANDING_DEMO_SPA_TITLE'),
			'METAMAIN_DESCRIPTION' => Loc::getMessage('LANDING_DEMO_SPA_DESCRIPTION')
		)
	),
	'items' => array (
		'0.menu_18_spa' =>
			array (
				'CODE' => '0.menu_18_spa',
				'SORT' => '-100',
				'CONTENT' => '<header class="landing-block landing-block-menu u-header u-header--sticky u-header--float g-z-index-9999">
	<div class="u-header__section g-bg-black-opacity-0_5 g-bg-transparent--lg g-transition-0_3 g-py-6 g-py-14--md"
		 data-header-fix-moment-exclude="g-bg-black-opacity-0_5 g-bg-transparent--lg g-py-14--md"
		 data-header-fix-moment-classes="u-header__section--light u-shadow-v27 g-bg-white g-py-11--md">
		<nav class="navbar navbar-expand-lg g-py-0 g-px-10">
			<div class="container">
				<!-- Logo -->
				<a href="#" class="navbar-brand landing-block-node-menu-logo-link u-header__logo p-0">
					<img class="landing-block-node-menu-logo u-header__logo-img u-header__logo-img--main d-block g-max-width-180" src="https://cdn.bitrix24.site/bitrix/images/landing/logos/spa-logo-light.png" alt="" data-header-fix-moment-exclude="d-block" data-header-fix-moment-classes="d-none" />

					<img class="landing-block-node-menu-logo2 u-header__logo-img u-header__logo-img--main d-none g-max-width-180" src="https://cdn.bitrix24.site/bitrix/images/landing/logos/spa-logo-dark.png" alt="" data-header-fix-moment-exclude="d-none" data-header-fix-moment-classes="d-block" />
				</a>
				<!-- End Logo -->

				<!-- Navigation -->
				<div class="collapse navbar-collapse align-items-center flex-sm-row" id="navBar">
					<ul class="landing-block-node-menu-list js-scroll-nav navbar-nav text-uppercase g-font-weight-700 g-font-size-11 g-pt-20 g-pt-0--lg ml-auto">
						<li class="landing-block-node-menu-list-item nav-item g-mr-12--lg g-mb-7 g-mb-0--lg ">
							<a href="#block@block[01.big_with_text_3]" class="landing-block-node-menu-list-item-link nav-link p-0" target="_self">HOME</a>
						</li>
						<li class="landing-block-node-menu-list-item nav-item g-mx-12--lg g-mb-7 g-mb-0--lg">
							<a href="#block@block[43.1.big_tiles_with_slider]" class="landing-block-node-menu-list-item-link nav-link p-0" target="_self">ABOUT</a>
						</li>
						<li class="landing-block-node-menu-list-item nav-item g-mx-12--lg g-mb-7 g-mb-0--lg">
							<a href="#block@block[04.7.one_col_fix_with_title_and_text_2]" class="landing-block-node-menu-list-item-link nav-link p-0" target="_self">BEST OFFERS</a>
						</li>
						<li class="landing-block-node-menu-list-item nav-item g-mx-12--lg g-mb-7 g-mb-0--lg">
							<a href="#block@block[31.1.two_cols_text_img]" class="landing-block-node-menu-list-item-link nav-link p-0" target="_self">OUR PROCEDURES</a>
						</li>
						<li class="landing-block-node-menu-list-item nav-item g-mx-12--lg g-mb-7 g-mb-0--lg">
							<a href="#block@block[04.1.one_col_fix_with_title]" class="landing-block-node-menu-list-item-link nav-link p-0" target="_self">ADVICES</a>
						</li>
						
						
						<li class="landing-block-node-menu-list-item nav-item g-mx-12--lg g-mb-7 g-mb-0--lg">
							<a href="#block@block[04.1.one_col_fix_with_title@2]" class="landing-block-node-menu-list-item-link nav-link p-0" target="_self">Gallery</a>
						</li><li class="landing-block-node-menu-list-item nav-item g-mx-12--lg g-mb-7 g-mb-0--lg">
							<a href="#block@block[04.1.one_col_fix_with_title@3]" class="landing-block-node-menu-list-item-link nav-link p-0" target="_self">PRODUCTS</a>
						</li>
						<li class="landing-block-node-menu-list-item nav-item g-ml-12--lg">
							<a href="#block@block[04.1.one_col_fix_with_title@4]" class="landing-block-node-menu-list-item-link nav-link p-0" target="_self">CONTACTS</a>
						</li>
					</ul>
				</div>
				<!-- End Navigation -->

				<!-- Responsive Toggle Button -->
				<button class="navbar-toggler btn g-line-height-1 g-brd-none g-pa-0 ml-auto g-flex-centered-item--center" type="button" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navBar" data-toggle="collapse" data-target="#navBar">
                <span class="hamburger hamburger--slider">
                  <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                  </span>
                </span>
				</button>
				<!-- End Responsive Toggle Button -->
			</div>
		</nav>
	</div>
</header>',
			),
		'01.big_with_text_3' =>
			array (
				'CODE' => '01.big_with_text_3',
				'SORT' => '500',
				'CONTENT' => '<section class="landing-block landing-block-node-img u-bg-overlay g-flex-centered g-min-height-70vh g-bg-img-hero g-bg-black-opacity-0_5--after g-py-80" style="background-image: url(\'https://cdn.bitrix24.site/bitrix/images/landing/business/2100x1416/img1.jpg\');" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb">
	<div class="container g-max-width-800 text-center u-bg-overlay__inner g-mx-1 js-animation landing-block-node-container fadeInDown animated">
		<h2 class="landing-block-node-title g-line-height-1 g-font-weight-700 g-color-white g-mb-20 g-text-transform-none g-font-size-60">Relaxing Massage</h2>

		<div class="landing-block-node-text g-mb-35 g-color-white g-font-size-36"><span style="font-weight: bold;">30% OFF</span></div>
		<div class="landing-block-node-button-container">
			<a href="#" class="landing-block-node-button btn btn-xl u-btn-primary text-uppercase g-font-weight-700 g-font-size-12 g-py-15 g-px-40 g-mb-15 rounded-0" target="_self">LEARN MORE</a>
		</div>
	</div>
</section>',
			),
		'43.1.big_tiles_with_slider' =>
			array (
				'CODE' => '43.1.big_tiles_with_slider',
				'SORT' => '1000',
				'CONTENT' => '<section class="landing-block">
	<div class="container-fluid px-0">
		<div class="row no-gutters">
			<div class="landing-block-node-img1 col-md-6 g-bg-img-hero g-min-height-400 js-animation fadeInLeft" style="background-image: url(https://cdn.bitrix24.site/bitrix/images/landing/business/1200x781/img1.jpg);"></div>

			<div class="landing-block-node-block-top js-animation fadeInRight col-md-6 d-flex align-items-center text-center g-pa-50">
				<div class="w-100">
					<div class="g-mb-25">
						<h4 class="landing-block-node-subtitle g-font-weight-700 g-font-size-20 g-color-primary g-mb-25">
							About Spa</h4>
						<h2 class="landing-block-node-title text-uppercase g-font-weight-600 g-font-size-22 mb-0">
							Perfect place for your
							relaxation</h2>
					</div>

					<div class="landing-block-node-text g-mb-35">
						<p>Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim
							justo, rhoncus ut, imperdiet a, venenatis vitae, justo.</p>
					</div>
					<div class="landing-block-node-button-container">
						<a class="landing-block-node-button btn btn-md text-uppercase u-btn-primary g-font-weight-700 g-font-size-11 g-brd-none rounded-0 g-py-10 g-px-25" href="#">View our procedures</a>
					</div>
				</div>
			</div>
		</div>

		<div class="row no-gutters">
			<div class="landing-block-node-block-bottom js-animation fadeInUp col-md-6 d-flex align-items-center g-max-height-300--md g-max-height-625--lg text-center g-overflow-hidden">
				<img class="landing-block-node-img2 w-100 img-fluid" src="https://cdn.bitrix24.site/bitrix/images/landing/business/1200x781/img2.jpg" alt="" />
			</div>

			<div class="col-md-6 landing-block-node-block-bottom js-animation fadeInLeft">
				<div class="js-carousel" data-infinite="true" data-arrows-classes="u-arrow-v1 g-absolute-centered--y g-width-45 g-height-55 g-font-size-12 g-color-gray-dark-v5 g-bg-white g-mt-minus-10" data-arrow-left-classes="fa fa-chevron-left g-left-0" data-arrow-right-classes="fa fa-chevron-right g-right-0">
					<div class="landing-block-node-card js-slide d-flex align-items-center g-max-height-300 g-max-height-625--lg">
						<img class="landing-block-node-card-img w-100 img-fluid" src="https://cdn.bitrix24.site/bitrix/images/landing/business/1200x781/img3.jpg" alt="" />
					</div>

					<div class="landing-block-node-card js-slide d-flex align-items-center g-max-height-300 g-max-height-625--lg">
						<img class="landing-block-node-card-img w-100 img-fluid" src="https://cdn.bitrix24.site/bitrix/images/landing/business/1200x781/img4.jpg" alt="" />
					</div>

					<div class="landing-block-node-card js-slide d-flex align-items-center g-max-height-300 g-max-height-625--lg">
						<img class="landing-block-node-card-img w-100 img-fluid" src="https://cdn.bitrix24.site/bitrix/images/landing/business/1200x781/img5.jpg" alt="" />
					</div>
				</div>
			</div>
		</div>
	</div>
</section>',
			),
		'43.2.three_tiles_with_img_zoom' =>
			array (
				'CODE' => '43.2.three_tiles_with_img_zoom',
				'SORT' => '1500',
				'CONTENT' => '<section class="landing-block g-pt-80 g-pb-80">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-lg-4 g-mb-30 g-mb-0--lg">
				<!-- Article -->
				<article class="text-center u-block-hover u-bg-overlay g-color-white g-bg-img-hero g-bg-black-opacity-0_3--after landing-block-node-block js-animation fadeInUp">
					<!-- Article Image -->
					<img class="landing-block-node-img1 w-100 u-block-hover__main--zoom-v1" src="https://cdn.bitrix24.site/bitrix/images/landing/business/800x867/img1.jpg" alt="" />
					<!-- End Article Image -->

					<!-- Article Content -->
					<div class="u-block-hover__additional u-bg-overlay__inner g-pos-abs g-flex-middle g-brd-around g-brd-2 g-brd-white-opacity-0_3 g-pa-15 g-ma-20">
						<div class="text-uppercase g-flex-middle-item">
							<h3 class="landing-block-node-subtitle1 g-font-weight-700 g-font-size-18 g-color-white g-brd-bottom g-brd-2 g-brd-primary g-mb-20">
								Facial massage</h3>
							<div class="landing-block-node-title1 text-uppercase g-line-height-1 g-font-weight-700 g-font-size-40 g-mb-30">
								<span style="font-weight: bold;">40% Off</span>
							</div>
							<div class="landing-block-node-button1-container">
								<a class="landing-block-node-button1 btn btn-md text-uppercase u-btn-primary g-font-weight-700 g-font-size-11 g-brd-none rounded-0 g-py-10 g-px-25" href="#">Contact us</a>
							</div>
						</div>
					</div>
					<!-- End Article Content -->
				</article>
				<!-- End Article -->
			</div>

			<div class="col-md-6 col-lg-4 g-mb-30 g-mb-0--lg">
				<!-- Article -->
				<article class="text-center u-block-hover u-bg-overlay g-color-white g-bg-img-hero g-bg-black-opacity-0_3--after landing-block-node-block js-animation fadeInUp">
					<!-- Article Image -->
					<img class="landing-block-node-img2 w-100 u-block-hover__main--zoom-v1" src="https://cdn.bitrix24.site/bitrix/images/landing/business/800x867/img2.jpg" alt="" />
					<!-- End Article Image -->

					<!-- Article Content -->
					<div class="u-block-hover__additional u-bg-overlay__inner g-pos-abs g-flex-middle g-brd-around g-brd-2 g-brd-white-opacity-0_3 g-pa-15 g-ma-20">
						<div class="text-uppercase g-flex-middle-item">
							<h3 class="landing-block-node-subtitle2 g-font-weight-700 g-font-size-16 g-color-white g-mb-5">
								20% Discount</h3>
							<div class="landing-block-node-title2 text-uppercase g-line-height-1 g-font-weight-700 g-font-size-28 g-mb-10">
								<span style="font-weight: bold;">Spa products</span>
							</div>
							<h3 class="landing-block-node-text2 g-font-weight-700 g-font-size-16 g-color-white mb-0">
								When you buy over 100$</h3>
						</div>
					</div>
					<!-- End Article Content -->
				</article>
				<!-- End Article -->
			</div>

			<div class="col-lg-4">
				<!-- Article -->
				<article class="landing-block-node-bg-mini text-center u-block-hover g-color-white g-bg-primary g-mb-30">
					<div class="g-brd-around g-brd-2 g-brd-white-opacity-0_3 g-pa-30 g-ma-20">
						<div class="g-flex-middle-item">
							<h3 class="landing-block-node-title-mini text-uppercase g-font-weight-700 g-font-size-18 g-color-white g-mb-10">
								Relaxing
								massage</h3>
							<div class="landing-block-node-text-mini g-font-size-12 g-color-white mb-0">
								<p>Morbi ex urna, porttitor vel consequat non</p>
							</div>
						</div>
					</div>
				</article>
				<!-- End Article -->

				<!-- Article -->
				<article class="text-center u-block-hover u-bg-overlay g-color-white g-bg-img-hero g-bg-black-opacity-0_3--after landing-block-node-block js-animation fadeInUp">
					<!-- Article Image -->
					<img class="landing-block-node-img-mini w-100 u-block-hover__main--zoom-v1" src="https://cdn.bitrix24.site/bitrix/images/landing/business/800x401/img1.jpg" alt="" />
					<!-- End Article Image -->

					<!-- Article Content -->
					<div class="u-block-hover__additional u-bg-overlay__inner g-pos-abs g-flex-middle g-brd-around g-brd-2 g-brd-white-opacity-0_3 g-pa-15 g-ma-20">
						<div class="g-flex-middle-item">
							<h3 class="landing-block-node-title-mini text-uppercase g-font-weight-700 g-font-size-18 g-color-white g-mb-5">
								1+1=1</h3>
							<div class="landing-block-node-text-mini g-font-size-12 g-color-white mb-0">
								<p>Morbi ex urna, porttitor vel consequat non</p>
							</div>
						</div>
					</div>
					<!-- End Article Content -->
				</article>
				<!-- End Article -->
			</div>
		</div>
	</div>
</section>',
			),
		'04.7.one_col_fix_with_title_and_text_2' =>
			array (
				'CODE' => '04.7.one_col_fix_with_title_and_text_2',
				'SORT' => '2000',
				'CONTENT' => '<section class="landing-block g-bg-gray-light-v5 g-py-20 js-animation fadeInUp animated g-pt-60 g-pb-20">

        <div class="container landing-block-node-subcontainer text-center g-max-width-800">

            <div class="landing-block-node-inner text-uppercase u-heading-v2-4--bottom g-brd-primary">
                <h4 class="landing-block-node-subtitle g-font-weight-700 g-color-primary g-mb-15 g-text-transform-none g-line-height-1 g-font-size-20">Best Offers</h4>
                <h2 class="landing-block-node-title u-heading-v2__title g-line-height-1_1 g-font-weight-700 g-color-black g-mb-minus-10 g-font-size-22">IT IS YOUR TIME TO RELAX</h2>
            </div>

			<div class="landing-block-node-text g-color-gray-dark-v5"><p>Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo.</p></div>
        </div>

    </section>',
			),
		'20.1.two_cols_fix_img_title_text' =>
			array (
				'CODE' => '20.1.two_cols_fix_img_title_text',
				'SORT' => '2500',
				'CONTENT' => '<section class="landing-block g-bg-gray-light-v5 g-pt-20 g-pb-20">
        <div class="container">
            <div class="row landing-block-inner">

                <div class="landing-block-card landing-block-node-block col-md-6 g-mb-30 g-mb-0--md g-pt-10 js-animation fadeIn animated ">
                    <img class="landing-block-node-img img-fluid g-mb-30" src="https://cdn.bitrix24.site/bitrix/images/landing/business/800x350/img1.jpg" alt="" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb" />

                    <h3 class="landing-block-node-title text-uppercase g-font-weight-700 g-color-black g-mb-20 text-center g-font-size-14">WINE FUSCE DOLOR LIBERO, EFFICITUR ET LOBORTIS AT, <p>FAUCIBUS NEC NUNC</p></h3>
                    <div class="landing-block-node-text text-center g-font-size-14 g-color-gray-dark-v5"><p>Cras sit amet varius velit. Maecenas porta condimentum<br /><span style="font-weight: bold;color: rgb(175, 180, 43);">$25</span></p></div>
                </div>

                <div class="landing-block-card landing-block-node-block col-md-6 g-mb-30 g-mb-0--md g-pt-10 js-animation fadeIn animated ">
                    <img class="landing-block-node-img img-fluid g-mb-30" src="https://cdn.bitrix24.site/bitrix/images/landing/business/800x350/img2.jpg" alt="" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb" />

                    <h3 class="landing-block-node-title text-uppercase g-font-weight-700 g-color-black g-mb-20 text-center g-font-size-14">WINE FUSCE DOLOR LIBERO, EFFICITUR ET LOBORTIS AT, <p>FAUCIBUS NEC NUNC</p></h3>
                    <div class="landing-block-node-text text-center g-font-size-14 g-color-gray-dark-v5"><p>Cras sit amet varius velit. Maecenas porta condimentum<br /><span style="font-weight: bold;">$25</span></p></div>
                </div>

            </div>
        </div>
    </section>',
			),
		'20.3.four_cols_fix_img_title_text' =>
			array (
				'CODE' => '20.3.four_cols_fix_img_title_text',
				'SORT' => '3000',
				'CONTENT' => '<section class="landing-block g-bg-gray-light-v5 g-pt-20 g-pb-60">
	<div class="container">
		<div class="row landing-block-inner">

			<div class="landing-block-card landing-block-node-block col-md-3 g-mb-30 g-mb-0--md g-pt-10 js-animation fadeInUp animated ">
				<img class="landing-block-node-img img-fluid g-mb-30" src="https://cdn.bitrix24.site/bitrix/images/landing/business/800x652/img1.jpg" alt="" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb" />

				<h3 class="landing-block-node-title text-uppercase g-font-weight-700 g-color-black g-mb-20 g-font-size-14 text-center">VESTIBULUM SEMPER, URNAEU <p>VULPUTATE EGESTAS</p></h3>
				<div class="landing-block-node-text g-font-size-14 g-color-gray-dark-v5 text-center"><p>Proin sollicitudin turpis in massa<br /><span style="font-weight: bold;">$70</span></p></div>
			</div>

			<div class="landing-block-card landing-block-node-block col-md-3 g-mb-30 g-mb-0--md g-pt-10 js-animation fadeInUp animated ">
				<img class="landing-block-node-img img-fluid g-mb-30" src="https://cdn.bitrix24.site/bitrix/images/landing/business/800x652/img2.jpg" alt="" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb" />

				<h3 class="landing-block-node-title text-uppercase g-font-weight-700 g-color-black g-mb-20 g-font-size-14 text-center">VESTIBULUM SEMPER, URNAEU <p>VULPUTATE EGESTAS</p></h3>
				<div class="landing-block-node-text g-font-size-14 g-color-gray-dark-v5 text-center"><p>Proin sollicitudin turpis in massa<br /><span style="font-weight: bold;">$70</span></p></div>
			</div>

			<div class="landing-block-card landing-block-node-block col-md-3 g-mb-30 g-mb-0--md g-pt-10 js-animation fadeInUp animated ">
				<img class="landing-block-node-img img-fluid g-mb-30" src="https://cdn.bitrix24.site/bitrix/images/landing/business/800x652/img3.jpg" alt="" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb" />

				<h3 class="landing-block-node-title text-uppercase g-font-weight-700 g-color-black g-mb-20 g-font-size-14 text-center">VESTIBULUM SEMPER, URNAEU <p>VULPUTATE EGESTAS</p></h3>
				<div class="landing-block-node-text g-font-size-14 g-color-gray-dark-v5 text-center"><p>Proin sollicitudin turpis in massa<br /><span style="font-weight: bold;">$70</span></p></div>
			</div>

			<div class="landing-block-card landing-block-node-block col-md-3 g-mb-30 g-mb-0--md g-pt-10 js-animation fadeInUp animated ">
				<img class="landing-block-node-img img-fluid g-mb-30" src="https://cdn.bitrix24.site/bitrix/images/landing/business/800x652/img4.jpg" alt="" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb" />

				<h3 class="landing-block-node-title text-uppercase g-font-weight-700 g-color-black g-mb-20 g-font-size-14 text-center">VESTIBULUM SEMPER, URNAEU <p>VULPUTATE EGESTAS</p></h3>
				<div class="landing-block-node-text g-font-size-14 g-color-gray-dark-v5 text-center"><p>Proin sollicitudin turpis in massa<br /><span style="font-weight: bold;">$70</span><br /></p></div>
			</div>

		</div>
	</div>
</section>',
			),
		'31.1.two_cols_text_img' =>
			array (
				'CODE' => '31.1.two_cols_text_img',
				'SORT' => '3500',
				'CONTENT' => '<section class="landing-block g-bg-main">
	<div>
		<div class="row mx-0">
			<div class="col-md-6 text-center text-md-left g-py-50 g-py-100--md g-px-15 g-px-50--md">
				<h3 class="landing-block-node-title text-uppercase g-font-weight-700 g-font-size-default g-mb-25 g-color-black g-font-size-22 js-animation fadeInUp animated">BODY CARE</h3>
				<div class="landing-block-node-text g-mb-30 g-color-gray-dark-v5 js-animation fadeInUp animated"><p>Morbi massa justo, gravida sollicitudin tortor vel, dignissim viverra lectus. In varius blandit condimentum. Pellentesque rutrum mauris ornare libero.</p></div>
				<div class="landing-block-node-button-container">
					<a class="landing-block-node-button text-uppercase btn btn-xl g-font-weight-700 g-font-size-12 js-animation fadeInUp animated g-rounded-1 u-btn-outline-primary" href="#" tabindex="0" target="_self">VIEW MORE</a>
				</div>
			</div>

			<div class="landing-block-node-img col-md-6 g-min-height-360 g-bg-img-hero g-px-0 g-bg-size-cover" style="background-image: url(\'https://cdn.bitrix24.site/bitrix/images/landing/business/800x457/img1.jpg\');" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb"></div>
		</div>
	</div>
</section>',
			),
		'31.2.two_cols_img_text' =>
			array (
				'CODE' => '31.2.two_cols_img_text',
				'SORT' => '4000',
				'CONTENT' => '<section class="landing-block g-bg-main">
	<div>
		<div class="row mx-0">
			<div class="landing-block-node-img col-md-6 g-min-height-360 g-bg-img-hero g-px-0 g-bg-size-cover" style="background-image: url(\'https://cdn.bitrix24.site/bitrix/images/landing/business/800x457/img2.jpg\');" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb"></div>
			
			<div class="col-md-6 text-center text-md-left g-py-50 g-py-100--md g-px-15 g-px-50--md">
				<h3 class="landing-block-node-title text-uppercase g-font-weight-700 g-font-size-default g-mb-25 g-color-black g-font-size-22 js-animation fadeInUp animated">Face care</h3>
				<div class="landing-block-node-text g-mb-30 g-color-gray-light-v1 js-animation fadeInUp animated"><p>Morbi massa justo, gravida sollicitudin tortor vel, dignissim viverra lectus. In varius blandit condimentum. Pellentesque rutrum mauris ornare libero.</p></div>
				<div class="landing-block-node-button-container">
					<a class="landing-block-node-button text-uppercase btn btn-xl g-font-weight-700 g-font-size-12 js-animation fadeInUp animated rounded-0 u-btn-outline-primary" href="#" tabindex="0" target="_self">VIEW MORE</a>
				</div>
			</div>
		</div>
	</div>
</section>',
			),
		'31.1.two_cols_text_img@2' =>
			array (
				'CODE' => '31.1.two_cols_text_img',
				'SORT' => '4500',
				'CONTENT' => '<section class="landing-block g-bg-main">
	<div>
		<div class="row mx-0">
			<div class="col-md-6 text-center text-md-left g-py-50 g-py-100--md g-px-15 g-px-50--md">
				<h3 class="landing-block-node-title text-uppercase g-font-weight-700 g-font-size-default g-mb-25 g-color-black g-font-size-22 js-animation fadeInUp animated">FOOT CARE</h3>
				<div class="landing-block-node-text g-mb-30 g-color-gray-dark-v5 js-animation fadeInUp animated"><p>Morbi massa justo, gravida sollicitudin tortor vel, dignissim viverra lectus. In varius blandit condimentum. Pellentesque rutrum mauris ornare libero.</p></div>
				<div class="landing-block-node-button-container">
					<a class="landing-block-node-button text-uppercase btn btn-xl g-font-weight-700 g-font-size-12 js-animation fadeInUp animated rounded-0 u-btn-outline-primary" href="#" tabindex="0" target="_self">VIEW MORE</a>
				</div>
			</div>

			<div class="landing-block-node-img col-md-6 g-min-height-360 g-bg-img-hero g-px-0 g-bg-size-cover" style="background-image: url(\'https://cdn.bitrix24.site/bitrix/images/landing/business/800x457/img3.jpg\');" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb"></div>
		</div>
	</div>
</section>',
			),
		'31.2.two_cols_img_text@2' =>
			array (
				'CODE' => '31.2.two_cols_img_text',
				'SORT' => '5000',
				'CONTENT' => '<section class="landing-block g-bg-main">
	<div>
		<div class="row mx-0">
			<div class="landing-block-node-img col-md-6 g-min-height-300 g-bg-img-hero g-px-0 g-bg-size-cover" style="background-image: url(\'https://cdn.bitrix24.site/bitrix/images/landing/business/800x457/img4.jpg\');" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb"></div>
			
			<div class="col-md-6 text-center text-md-left g-py-50 g-py-100--md g-px-15 g-px-50--md">
				<h3 class="landing-block-node-title text-uppercase g-font-weight-700 g-font-size-default g-mb-25 g-color-black g-font-size-22 js-animation fadeInUp animated">NAILS CARE</h3>
				<div class="landing-block-node-text g-mb-30 g-color-gray-dark-v5 js-animation fadeInUp animated"><p>Morbi massa justo, gravida sollicitudin tortor vel, dignissim viverra lectus. In varius blandit condimentum. Pellentesque rutrum mauris ornare libero.</p></div>
				<div class="landing-block-node-button-container">
					<a class="landing-block-node-button text-uppercase btn btn-xl g-font-weight-700 g-font-size-12 js-animation fadeInUp animated u-btn-outline-primary rounded-0" href="#" tabindex="0" target="_self">VIEW MORE</a>
				</div>
			</div>
		</div>
	</div>
</section>',
			),
		'04.1.one_col_fix_with_title' =>
			array (
				'CODE' => '04.1.one_col_fix_with_title',
				'SORT' => '5500',
				'CONTENT' => '<section class="landing-block g-bg-primary js-animation fadeInUp animated g-pt-60 g-pb-20">
        <div class="container">
            <div class="landing-block-node-inner text-uppercase text-center u-heading-v2-4--bottom g-brd-white">
                <h4 class="landing-block-node-subtitle h6 g-font-weight-800 g-letter-spacing-1 g-mb-20 g-text-transform-none g-font-size-20 g-color-white-opacity-0_7">Some Advices</h4>
                <h2 class="landing-block-node-title h1 u-heading-v2__title g-line-height-1_3 g-font-weight-600 g-mb-minus-10 g-color-white g-font-size-22">WE CARE ABOUT OUR CLIENTS</h2>
            </div>
        </div>
    </section>',
			),
		'20.3.four_cols_fix_img_title_text@2' =>
			array (
				'CODE' => '20.3.four_cols_fix_img_title_text',
				'SORT' => '6000',
				'CONTENT' => '<section class="landing-block g-bg-primary g-pt-20 g-pb-60">
	<div class="container">
		<div class="row landing-block-inner">

			<div class="landing-block-card landing-block-node-block col-md-3 g-mb-30 g-mb-0--md g-pt-10 js-animation fadeInUp animated  g-bg-main">
				<img class="landing-block-node-img img-fluid g-mb-30" src="https://cdn.bitrix24.site/bitrix/images/landing/business/600x333/img1.jpg" alt="" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb" />

				<h3 class="landing-block-node-title text-uppercase g-font-weight-700 g-mb-20 g-font-size-14 text-center g-color-black">FACE CARE</h3>
				<div class="landing-block-node-text g-font-size-14 text-center g-color-gray-dark-v5"><p>Morbi ex urna, porttitor vel consequat non, rhoncus nec nibh efficitur est ut justo viverra dapibus aliquet iaculis</p></div>
			</div>

			<div class="landing-block-card landing-block-node-block col-md-3 g-mb-30 g-mb-0--md g-pt-10 js-animation fadeInUp animated  g-bg-main">
				<img class="landing-block-node-img img-fluid g-mb-30" src="https://cdn.bitrix24.site/bitrix/images/landing/business/600x333/img2.jpg" alt="" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb" />

				<h3 class="landing-block-node-title text-uppercase g-font-weight-700 g-mb-20 g-font-size-14 text-center g-color-black">FOOT CARE</h3>
				<div class="landing-block-node-text g-font-size-14 text-center g-color-gray-dark-v5"><p>Morbi ex urna, porttitor vel consequat non, rhoncus nec nibh efficitur est ut justo viverra dapibus aliquet iaculis</p></div>
			</div>

			<div class="landing-block-card landing-block-node-block col-md-3 g-mb-30 g-mb-0--md g-pt-10 js-animation fadeInUp animated  g-bg-main">
				<img class="landing-block-node-img img-fluid g-mb-30" src="https://cdn.bitrix24.site/bitrix/images/landing/business/600x333/img3.jpg" alt="" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb" />

				<h3 class="landing-block-node-title text-uppercase g-font-weight-700 g-mb-20 g-font-size-14 text-center g-color-black">HANDS CARE</h3>
				<div class="landing-block-node-text g-font-size-14 text-center g-color-gray-dark-v5"><p>Morbi ex urna, porttitor vel consequat non, rhoncus nec nibh efficitur est ut justo viverra dapibus aliquet iaculis</p></div>
			</div>

			<div class="landing-block-card landing-block-node-block col-md-3 g-mb-30 g-mb-0--md g-pt-10 js-animation fadeInUp animated  g-bg-main">
				<img class="landing-block-node-img img-fluid g-mb-30" src="https://cdn.bitrix24.site/bitrix/images/landing/business/600x333/img4.jpg" alt="" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb" />

				<h3 class="landing-block-node-title text-uppercase g-font-weight-700 g-mb-20 g-font-size-14 text-center g-color-black">BODY CARE</h3>
				<div class="landing-block-node-text g-font-size-14 text-center g-color-gray-dark-v5"><p>Morbi ex urna, porttitor vel consequat non, rhoncus nec nibh efficitur est ut justo viverra dapibus aliquet iaculis</p></div>
			</div>

		</div>
	</div>
</section>',
			),
		'04.1.one_col_fix_with_title@2' =>
			array (
				'CODE' => '04.1.one_col_fix_with_title',
				'SORT' => '6500',
				'CONTENT' => '<section class="landing-block g-bg-main js-animation fadeInUp animated g-pt-60 g-pb-20">
        <div class="container">
            <div class="landing-block-node-inner text-uppercase text-center u-heading-v2-4--bottom g-brd-primary">
                <h4 class="landing-block-node-subtitle h6 g-font-weight-800 g-letter-spacing-1 g-color-primary g-mb-20 g-text-transform-none g-font-size-18">Our gallery</h4>
                <h2 class="landing-block-node-title h1 u-heading-v2__title g-line-height-1_3 g-font-weight-600 g-mb-minus-10 g-font-size-22">You will see how we care about out clients</h2>
            </div>
        </div>
    </section>',
			),
		'32.6.img_grid_4cols_1_no_gutters' =>
			array (
				'CODE' => '32.6.img_grid_4cols_1_no_gutters',
				'SORT' => '7000',
				'CONTENT' => '<section class="landing-block g-pt-0 g-pb-0">

	<div class="row no-gutters js-gallery-cards">

		<div class="col-12 col-sm-6 col-md-3">
			<div class="h-100">
				<div class="landing-block-node-img-container landing-block-node-img-container-leftleft js-animation fadeInLeft h-100 g-pos-rel g-parent u-block-hover">
					<img data-fancybox="gallery" class="landing-block-node-img img-fluid g-object-fit-cover h-100 w-100 u-block-hover__main--zoom-v1" src="https://cdn.bitrix24.site/bitrix/images/landing/business/400x400/img1.jpg" alt="" data-fileid="-1" />
				</div>
			</div>
		</div>

		<div class="col-12 col-sm-6 col-md-3">
			<div class="h-100">
				<div class="landing-block-node-img-container landing-block-node-img-container-left js-animation fadeInDown h-100 g-pos-rel g-parent u-block-hover">
					<img data-fancybox="gallery" class="landing-block-node-img img-fluid g-object-fit-cover h-100 w-100 u-block-hover__main--zoom-v1" src="https://cdn.bitrix24.site/bitrix/images/landing/business/400x400/img2.jpg" alt="" data-fileid="-1" />
				</div>
			</div>
		</div>

		<div class="col-12 col-sm-6 col-md-3">
			<div class="h-100">
				<div class="landing-block-node-img-container landing-block-node-img-container-right js-animation fadeInDown h-100 g-pos-rel g-parent u-block-hover">
					<img data-fancybox="gallery" class="landing-block-node-img img-fluid g-object-fit-cover h-100 w-100 u-block-hover__main--zoom-v1" src="https://cdn.bitrix24.site/bitrix/images/landing/business/400x400/img3.jpg" alt="" data-fileid="-1" />
				</div>
			</div>
		</div>

		<div class="col-12 col-sm-6 col-md-3">
			<div class="h-100">
				<div class="landing-block-node-img-container landing-block-node-img-container-rightright js-animation fadeInRight h-100 g-pos-rel g-parent u-block-hover">
					<img data-fancybox="gallery" class="landing-block-node-img img-fluid g-object-fit-cover h-100 w-100 u-block-hover__main--zoom-v1" src="https://cdn.bitrix24.site/bitrix/images/landing/business/400x400/img4.jpg" alt="" data-fileid="-1" />
				</div>
			</div>
		</div>

	</div>

</section>',
			),
		'32.6.img_grid_4cols_1_no_gutters@2' =>
			array (
				'CODE' => '32.6.img_grid_4cols_1_no_gutters',
				'SORT' => '7500',
				'CONTENT' => '<section class="landing-block g-pt-0 g-pb-0">

	<div class="row no-gutters js-gallery-cards">

		<div class="col-12 col-sm-6 col-md-3">
			<div class="h-100">
				<div class="landing-block-node-img-container landing-block-node-img-container-leftleft js-animation fadeInLeft h-100 g-pos-rel g-parent u-block-hover">
					<img data-fancybox="gallery" class="landing-block-node-img img-fluid g-object-fit-cover h-100 w-100 u-block-hover__main--zoom-v1" src="https://cdn.bitrix24.site/bitrix/images/landing/business/400x400/img5.jpg" alt="" data-fileid="-1" />
				</div>
			</div>
		</div>

		<div class="col-12 col-sm-6 col-md-3">
			<div class="h-100">
				<div class="landing-block-node-img-container landing-block-node-img-container-left js-animation fadeInDown h-100 g-pos-rel g-parent u-block-hover">
					<img data-fancybox="gallery" class="landing-block-node-img img-fluid g-object-fit-cover h-100 w-100 u-block-hover__main--zoom-v1" src="https://cdn.bitrix24.site/bitrix/images/landing/business/400x400/img6.jpg" alt="" data-fileid="-1" />
				</div>
			</div>
		</div>

		<div class="col-12 col-sm-6 col-md-3">
			<div class="h-100">
				<div class="landing-block-node-img-container landing-block-node-img-container-right js-animation fadeInDown h-100 g-pos-rel g-parent u-block-hover">
					<img data-fancybox="gallery" class="landing-block-node-img img-fluid g-object-fit-cover h-100 w-100 u-block-hover__main--zoom-v1" src="https://cdn.bitrix24.site/bitrix/images/landing/business/400x400/img7.jpg" alt="" data-fileid="-1" />
				</div>
			</div>
		</div>

		<div class="col-12 col-sm-6 col-md-3">
			<div class="h-100">
				<div class="landing-block-node-img-container landing-block-node-img-container-rightright js-animation fadeInRight h-100 g-pos-rel g-parent u-block-hover">
					<img data-fancybox="gallery" class="landing-block-node-img img-fluid g-object-fit-cover h-100 w-100 u-block-hover__main--zoom-v1" src="https://cdn.bitrix24.site/bitrix/images/landing/business/400x400/img8.jpg" alt="" data-fileid="-1" />
				</div>
			</div>
		</div>

	</div>

</section>',
			),
		'04.1.one_col_fix_with_title@3' =>
			array (
				'CODE' => '04.1.one_col_fix_with_title',
				'SORT' => '8000',
				'CONTENT' => '<section class="landing-block js-animation fadeInUp animated g-bg-main g-pt-60 g-pb-20">
        <div class="container">
            <div class="landing-block-node-inner text-uppercase text-center u-heading-v2-4--bottom g-brd-primary">
                <h4 class="landing-block-node-subtitle h6 g-font-weight-800 g-letter-spacing-1 g-color-primary g-mb-20 g-text-transform-none g-font-size-18">Our products</h4>
                <h2 class="landing-block-node-title h1 u-heading-v2__title g-line-height-1_3 g-font-weight-600 g-mb-minus-10 g-font-size-22">WE CARE ABOUT OUR CLIENTS</h2>
            </div>
        </div>
    </section>',
			),
		'42.1.rest_menu' =>
			array (
				'CODE' => '42.1.rest_menu',
				'SORT' => '8500',
				'CONTENT' => '<section class="landing-block g-pt-20 g-bg-main g-pb-60">
	<div class="container">
		<div class="tab-content g-pt-20">
			<div class="tab-pane fade show active">
				<!-- Products Block -->
				<div class="row landing-block-inner">
					<div class="landing-block-node-card js-animation col-md-6 g-mb-50 fadeInUp animated ">
						<!-- Article -->
						<article class="media">
							<img class="landing-block-node-card-photo g-width-100 img-fluid g-rounded-50x" src="https://cdn.bitrix24.site/bitrix/images/landing/business/180x288/img1.png" alt="" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb" />

							<!-- Article Content -->
							<div class="media-body align-self-center g-pl-10">
								<div class="d-flex justify-content-between u-heading-v1-4 g-bg-main g-brd-gray-light-v4 g-mb-8">
									<h3 class="landing-block-node-card-title align-self-center u-heading-v1__title g-color-black g-font-weight-700 g-font-size-13 text-uppercase mb-0">CORATA</h3>

									<div class="align-self-center g-pos-rel g-bg-main g-pl-15">
										<div class="landing-block-node-card-price g-font-weight-700 g-font-size-13 g-color-white g-bg-primary g-rounded-3 g-py-4 g-px-12">$50</div>
									</div>
								</div>

								<div class="landing-block-node-card-text mb-0"><p>In rutrum tellus vitae blandit lacinia</p></div>
							</div>
							<!-- End Article Content -->
						</article>
						<!-- End Article -->
					</div>

					<div class="landing-block-node-card js-animation col-md-6 g-mb-50 fadeInUp animated ">
						<!-- Article -->
						<article class="media">
							<img class="landing-block-node-card-photo g-width-100 img-fluid g-rounded-50x" src="https://cdn.bitrix24.site/bitrix/images/landing/business/180x288/img2.png" alt="" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb" />

							<!-- Article Content -->
							<div class="media-body align-self-center g-pl-10">
								<div class="d-flex justify-content-between u-heading-v1-4 g-bg-main g-brd-gray-light-v4 g-mb-8">
									<h3 class="landing-block-node-card-title align-self-center u-heading-v1__title g-color-black g-font-weight-700 g-font-size-13 text-uppercase mb-0">MORBI</h3>

									<div class="align-self-center g-pos-rel g-bg-main g-pl-15">
										<div class="landing-block-node-card-price g-font-weight-700 g-font-size-13 g-color-white g-bg-primary g-rounded-3 g-py-4 g-px-12">$50</div>
									</div>
								</div>

								<div class="landing-block-node-card-text mb-0"><p>In rutrum tellus vitae blandit lacinia</p></div>
							</div>
							<!-- End Article Content -->
						</article>
						<!-- End Article -->
					</div>
				
					<div class="landing-block-node-card js-animation col-md-6 g-mb-50 fadeInUp animated ">
						<!-- Article -->
						<article class="media">
							<img class="landing-block-node-card-photo g-width-100 img-fluid g-rounded-50x" src="https://cdn.bitrix24.site/bitrix/images/landing/business/180x288/img3.png" alt="" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb" />

							<!-- Article Content -->
							<div class="media-body align-self-center g-pl-10">
								<div class="d-flex justify-content-between u-heading-v1-4 g-bg-main g-brd-gray-light-v4 g-mb-8">
									<h3 class="landing-block-node-card-title align-self-center u-heading-v1__title g-color-black g-font-weight-700 g-font-size-13 text-uppercase mb-0">PROIN<br /></h3>

									<div class="align-self-center g-pos-rel g-bg-main g-pl-15">
										<div class="landing-block-node-card-price g-font-weight-700 g-font-size-13 g-color-white g-bg-primary g-rounded-3 g-py-4 g-px-12">$50</div>
									</div>
								</div>

								<div class="landing-block-node-card-text mb-0"><p>In rutrum tellus vitae blandit lacinia</p></div>
							</div>
							<!-- End Article Content -->
						</article>
						<!-- End Article -->
					</div>

					<div class="landing-block-node-card js-animation col-md-6 g-mb-50 fadeInUp animated ">
						<!-- Article -->
						<article class="media">
							<img class="landing-block-node-card-photo g-width-100 img-fluid g-rounded-50x" src="https://cdn.bitrix24.site/bitrix/images/landing/business/180x288/img4.png" alt="" data-fileid="-1" data-filehash="9eef207add73028ae50f74a9033c20cb" />

							<!-- Article Content -->
							<div class="media-body align-self-center g-pl-10">
								<div class="d-flex justify-content-between u-heading-v1-4 g-bg-main g-brd-gray-light-v4 g-mb-8">
									<h3 class="landing-block-node-card-title align-self-center u-heading-v1__title g-color-black g-font-weight-700 g-font-size-13 text-uppercase mb-0">ALIQUIM</h3>

									<div class="align-self-center g-pos-rel g-bg-main g-pl-15">
										<div class="landing-block-node-card-price g-font-weight-700 g-font-size-13 g-color-white g-bg-primary g-rounded-3 g-py-4 g-px-12">$50</div>
									</div>
								</div>

								<div class="landing-block-node-card-text mb-0"><p>In rutrum tellus vitae blandit lacinia</p></div>
							</div>
							<!-- End Article Content -->
						</article>
						<!-- End Article -->
					</div>
				</div>
				<!-- End Products Block -->
			</div>
		</div>
	</div>
</section>',
			),
		'04.1.one_col_fix_with_title@4' =>
			array (
				'CODE' => '04.1.one_col_fix_with_title',
				'SORT' => '9000',
				'CONTENT' => '<section class="landing-block js-animation fadeInUp animated g-pt-60 g-pb-20 g-bg-main">
        <div class="container">
            <div class="landing-block-node-inner text-uppercase text-center u-heading-v2-4--bottom g-brd-primary">
                <h4 class="landing-block-node-subtitle h6 g-font-weight-800 g-letter-spacing-1 g-color-primary g-mb-20 g-text-transform-none g-font-size-20">Contact us</h4>
                <h2 class="landing-block-node-title h1 u-heading-v2__title g-line-height-1_3 g-font-weight-600 g-mb-minus-10 g-font-size-22">GET IN TOUCH</h2>
            </div>
        </div>
    </section>',
			),
		'33.12.form_2_light_right_text' =>
			array (
				'CODE' => '33.12.form_2_light_right_text',
				'SORT' => '9500',
				'CONTENT' => '<section class="g-pos-rel landing-block text-center g-py-100 g-bg-main g-pt-20 g-pb-60">

	<div class="container">

		<div class="landing-block-form-styles" hidden="">
			<div class="g-bg-transparent h1 g-color-white g-brd-none g-pa-0" data-form-style-wrapper-padding="1" data-form-style-bg="1" data-form-style-bg-content="1" data-form-style-bg-block="1" data-form-style-header-font-size="1" data-form-style-main-font-weight="1" data-form-style-button-font-color="1" data-form-style-border-block="1">
			</div>
			<div class="g-bg-primary g-color-primary g-brd-primary" data-form-style-main-bg="1" data-form-style-main-border-color="1" data-form-style-main-font-color-hover="1">
			</div>
			<div class="g-bg-gray-light-v5 g-color-gray-dark-v1 g-brd-around g-brd-white rounded-0" data-form-style-input-bg="1" data-form-style-input-select-bg="1" data-form-style-input-border="1" data-form-style-input-border-radius="1" data-form-style-main-font-color="1">
			</div>
			<div class="g-brd-around g-brd-gray-light-v2 g-color-gray-dark-v5 g-brd-bottom g-bg-black-opacity-0_7" data-form-style-input-border-color="1" data-form-style-input-border-hover="1" data-form-style-icon-font-color="1">
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="bitrix24forms g-brd-white-opacity-0_6 u-form-alert-v4" data-b24form="" data-b24form-use-style="Y" data-b24form-show-header="N" data-b24form-original-domain=""></div>
			</div>
			
			<div class="col-md-6">
				<div class="text-center g-overflow-hidden">
					<h3 class="landing-block-node-main-title h3 text-uppercase g-font-weight-700 g-mb-20 g-color-black g-font-size-22"> </h3>
					
					<div class="landing-block-node-text g-line-height-1_5 text-left g-mb-40 g-color-gray-dark-v5" data-form-style-main-font-family="1" data-form-style-main-font-weight="1" data-form-style-header-text-font-size="1"> </div>
					<div class="g-mx-minus-2 g-my-minus-2">
						<div class="row mx-0">
							<div class="landing-block-card-contact js-animation fadeIn col-sm-6 g-brd-left g-brd-bottom g-brd-gray-light-v4 g-px-15 g-py-25">
							<span class="landing-block-card-contact-icon-container g-color-primary">
								<i class="landing-block-card-contact-icon icon-anchor d-inline-block g-font-size-50 g-mb-30"></i>
								</span>
								<h3 class="landing-block-card-contact-title text-uppercase g-font-size-11 g-color-gray-dark-v5 mb-0"
									data-form-style-label-font-weight="1"
									data-form-style-label-font-size="1"
									data-form-style-second-font-color="1"
								>
									Address</h3>
								<div class="landing-block-card-contact-text g-font-size-11 g-color-gray-dark-v1">
									<span style="font-weight: bold;">Sit amet adipiscing</span>
								</div>
							</div>

							<div class="landing-block-card-contact js-animation fadeIn col-sm-6 g-brd-left g-brd-bottom g-brd-gray-light-v4 g-px-15 g-py-25">
							<span class="landing-block-card-contact-icon-container g-color-primary">
								<i class="landing-block-card-contact-icon icon-call-in d-inline-block g-font-size-50 g-mb-30"></i>
								</span>
								<h3 class="landing-block-card-contact-title text-uppercase g-font-size-11 g-color-gray-dark-v5 mb-0"
									data-form-style-label-font-weight="1"
									data-form-style-label-font-size="1"
									data-form-style-second-font-color="1"
								>
									Phone
									number</h3>
								<div class="landing-block-card-contact-text g-font-size-11 g-color-gray-dark-v1">
									<span style="font-weight: bold;"><a href="tel:+4025448569">+402 5448 569</a></span>
								</div>
							</div>

							<div class="landing-block-card-contact js-animation fadeIn col-sm-6 g-brd-left g-brd-bottom g-brd-gray-light-v4 g-px-15 g-py-25">
							<span class="landing-block-card-contact-icon-container g-color-primary">
								<i class="landing-block-card-contact-icon icon-line icon-envelope-letter d-inline-block g-font-size-50 g-mb-30"></i>
								</span>
								<h3 class="landing-block-card-contact-title text-uppercase g-font-size-11 g-color-gray-dark-v5 mb-0"
									data-form-style-label-font-weight="1"
									data-form-style-label-font-size="1"
									data-form-style-second-font-color="1"
								>
									Email</h3>
								<div class="landing-block-card-contact-text g-font-size-11 g-color-gray-dark-v1">
									<span style="font-weight: bold;"><a href="mailto:info@company24.com">info@company24.com</a></span>
								</div>
							</div>

							<div class="landing-block-card-contact js-animation fadeIn col-sm-6 g-brd-left g-brd-bottom g-brd-gray-light-v4 g-px-15 g-py-25">
							<span class="landing-block-card-contact-icon-container g-color-primary">
								<i class="landing-block-card-contact-icon icon-earphones-alt d-inline-block g-font-size-50 g-mb-30"></i>
								</span>
								<h3 class="landing-block-card-contact-title text-uppercase g-font-size-11 g-color-gray-dark-v5 mb-0"
									data-form-style-label-font-weight="1"
									data-form-style-label-font-size="1"
									data-form-style-second-font-color="1"
								>
									Toll free</h3>
								<div class="landing-block-card-contact-text g-font-size-11 g-color-gray-dark-v1">
									<span style="font-weight: bold;"><a href="tel:+402 5897 660">+402 5897 660</a></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>',
			),
		'17.copyright' =>
			array (
				'CODE' => '17.copyright',
				'SORT' => '10000',
				'CONTENT' => '<section class="landing-block js-animation animation-none">
	<div class="text-center g-color-gray-dark-v3 g-pa-10">
		<div class="g-width-600 mx-auto">
			<div class="landing-block-node-text g-font-size-12  js-animation animation-none">
				<p>&copy; 2018 All rights reserved.</p>
			</div>
		</div>
	</div>
</section>',
			),
	)
);