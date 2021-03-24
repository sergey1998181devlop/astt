<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 25.09.2018
 * Time: 09:34
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
if (\Bitrix\Main\IO\File::isFileExists(\Bitrix\Main\Application::getDocumentRoot() . SITE_TEMPLATE_PATH . '/inc/404/default.php')) {
  $APPLICATION->IncludeComponent(
    "bitrix:main.include", "",
    array(
      "AREA_FILE_SHOW" => "file",
      "PATH" => SITE_TEMPLATE_PATH . '/inc/404/default.php',
      "EDIT_TEMPLATE" => "include_areas_template.php",
        'MODE' => 'html'
    ),
    false
  );
} else {
  ?>
    <div class="page-404 text-center">
        <div class="wrapper">
          <div class="white-block">
            <div class="h1"><?= \Bitrix\Main\Localization\Loc::getMessage('404_COMPONENT_ERORR')  ?></div>
            <div class="numbers ultrabold">404</div>
            <div class="text">
              <?= \Bitrix\Main\Localization\Loc::getMessage('404_COMPONENT_CONTENT')?>
            </div>
          </div>
        </div>
    </div>
  <?php
}