<?php
/**
 * User: Aleksandr Miranovich
 * Email: miranovich664@gmail.com
 * Date: 09.01.2019
 * Time: 12:49
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if (function_exists('setFrameMode')) {
    $this->setFrameMode(true);
}

$strReturn .= '<div class="breadcrumbs">';
$strReturn .= '<div class="wrapper">';
$strReturn .= '<ul class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">';

$i = 0;
$c = count($arResult);
foreach ($arResult as $k => $v) {
    $i++;

    if ($i == $c) {
        $strReturn .= '<li><span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="item"><span itemprop="name">' . $v['TITLE'] . '</span></span><meta itemprop="position" content="' . $i . '" /></span></span></li>';
    } else {
        $strReturn .= '<li><span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . $v['LINK'] . '" itemprop="item"><span itemprop="name">' . $v['TITLE'] . '</span></a><meta itemprop="position" content="' . $i . '" /></span></span></li>';
    }
}
$strReturn .= '</ul>';
$strReturn .= '</div>';
$strReturn .= '</div>';

return $strReturn;