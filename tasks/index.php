<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.01.2019
 * Time: 11:58
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Все задания");
$APPLICATION->SetTitle("Все задания");

$APPLICATION->IncludeComponent(
    'democontent2.pi:all.tasks',
    'list',
    array(
        'LIMIT' => 30
    )
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>