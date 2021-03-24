<?php
/**
 * Author: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Skype: pixel365
 * WebSite: semagin.com
 * Date: 18.04.2016
 * Time: 16:17
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

function printArray($arr)
{
    $output = "\$arUrlRewrite = array(\n";

    foreach ($arr as $val) {
        $output .= "\tarray(\n";
        foreach ($val as $key1 => $val1)
            $output .= "\t\t\"" . EscapePHPString($key1) . "\" => \"" . EscapePHPString($val1) . "\",\n";
        $output .= "\t),\n";
    }

    $output .= ");\n";

    return $output;
}

$arNewUrlRewrite = array(
    array(
        "CONDITION" => "#^" . WIZARD_SITE_DIR . "payments/([\da-zA-Z]{32}+)/($|\?.+$)$#",
        "RULE" => "component=payments&params[hash]=$1",
        "ID" => "democontent2.pi:payments",
        "PATH" => "/local/democontent2_pi_prolog.php",
        "SOLUTION" => "democontent2.pi",
        "SORT" => 1
    ),
    array(
        "CONDITION" => "#^" . WIZARD_SITE_DIR . "task([\d]+)-([\d]+)/($|\?.+$)$#",
        "RULE" => "component=taskRedirect&params[iBlockId]=$2&params[itemId]=$1",
        "ID" => "democontent2.pi:taskRedirect",
        "PATH" => "/local/democontent2_pi_prolog.php",
        "SOLUTION" => "democontent2.pi",
        "SORT" => 2
    ),
    array(
        "CONDITION" => "#^" . WIZARD_SITE_DIR . "users/([a-z-]+)/([\da-z-]+)/($|\?.+$)$#",
        "RULE" => "component=users&params[iBlockType]=$1&params[iBlockCode]=$2",
        "ID" => "democontent2.pi:users",
        "PATH" => "/local/democontent2_pi_template.php",
        "SOLUTION" => "democontent2.pi",
        "SORT" => 3
    ),
    array(
        "CONDITION" => "#^" . WIZARD_SITE_DIR . "users/([a-z-]+)/($|\?.+$)$#",
        "RULE" => "component=users&params[iBlockType]=$1",
        "ID" => "democontent2.pi:users",
        "PATH" => "/local/democontent2_pi_template.php",
        "SOLUTION" => "democontent2.pi",
        "SORT" => 4
    ),
    array(
        "CONDITION" => "#^" . WIZARD_SITE_DIR . "users/($|\?.+$)$#",
        "RULE" => "component=users",
        "ID" => "democontent2.pi:users",
        "PATH" => "/local/democontent2_pi_template.php",
        "SOLUTION" => "democontent2.pi",
        "SORT" => 5
    ),
    array(
        "CONDITION" => "#^" . WIZARD_SITE_DIR . "user/([\d]+)/($|\?.+$)$#",
        "RULE" => "component=user.public.profile&params[id]=$1",
        "ID" => "democontent2.pi:user.public.profile",
        "PATH" => "/local/democontent2_pi_template.php",
        "SOLUTION" => "democontent2.pi",
        "SORT" => 6
    ),
    array(
        "CONDITION" => "#^" . WIZARD_SITE_DIR . "user/items/([\d]+)-([\d]+)/($|\?.+$)$#",
        "RULE" => "component=user.items.detail&params[iBlockId]=$1&params[itemId]=$2",
        "ID" => "democontent2.pi:user.items.detail",
        "PATH" => "/local/democontent2_pi_template.php",
        "SOLUTION" => "democontent2.pi",
        "SORT" => 8
    ),
    array(
        "CONDITION" => "#^" . WIZARD_SITE_DIR . "user/([a-z_-]+)/($|\?.+$)$#",
        "RULE" => "component=user.$1&params[componentName]=$1",
        "ID" => "democontent2.pi:user.$1",
        "PATH" => "/local/democontent2_pi_template.php",
        "SOLUTION" => "democontent2.pi",
        "SORT" => 9
    ),
    array(
        "CONDITION" => "#^" . WIZARD_SITE_DIR . "([a-z-]+)/([\da-z-]+)/([\da-z-]+)-([\d]+)/($|\?.+$)$#",
        "RULE" => "component=detail&params[iBlockType]=$1&params[iBlockCode]=$2&params[itemCode]=$3&params[itemId]=$4",
        "ID" => "democontent2.pi:detail",
        "PATH" => "/local/democontent2_pi_template.php",
        "SOLUTION" => "democontent2.pi",
        "SORT" => 11
    ),
    array(
        "CONDITION" => "#^" . WIZARD_SITE_DIR . "([a-z-]+)/([\da-z-]+)/($|\?.+$)$#",
        "RULE" => "component=iblock.code&params[iBlockType]=$1&params[iBlockCode]=$2",
        "ID" => "democontent2.pi:iblock.code",
        "PATH" => "/local/democontent2_pi_template.php",
        "SOLUTION" => "democontent2.pi",
        "SORT" => 12
    ),
    array(
        "CONDITION" => "#^" . WIZARD_SITE_DIR . "([a-z-]+)/($|\?.+$)$#",
        "RULE" => "component=iblock.type&params[iBlockType]=$1",
        "ID" => "democontent2.pi:iblock.type",
        "PATH" => "/local/democontent2_pi_template.php",
        "SOLUTION" => "democontent2.pi",
        "SORT" => 13
    )
);

if (\Bitrix\Main\IO\File::isFileExists(Bitrix\Main\IO\Path::normalize($_SERVER["DOCUMENT_ROOT"] . "/urlrewrite.php"))) {
    include(\Bitrix\Main\IO\Path::normalize($_SERVER["DOCUMENT_ROOT"] . "/urlrewrite.php"));
}

$_i = 0;
foreach ($arNewUrlRewrite as $key => $arUrl) {
    $_i++;
    $arNewUrlRewrite[$key]['SORT'] = $_i;

    foreach ($arUrlRewrite as $k => $v) {
        $s1 = md5($v['CONDITION'] . $v['RULE'] . $v['ID'] . $v['PATH']);
        $s2 = md5($arUrl['CONDITION'] . $arUrl['RULE'] . $arUrl['ID'] . $arUrl['PATH']);
        if ($s1 == $s2) {
            unset($arUrlRewrite[$k]);
            break;
        }
    }
}

if (is_array($arUrlRewrite) && !empty($arUrlRewrite)) {
    foreach ($arUrlRewrite as $k => $v) {
        if (!isset($arUrlRewrite[$k]['SORT'])) {
            $arUrlRewrite[$k]['SORT'] = 500;
        }
    }
    $arNewUrlRewrite = array_merge($arNewUrlRewrite, $arUrlRewrite);
}

$docRoot = CSite::GetSiteDocRoot(WIZARD_SITE_ID);
$f = fopen(\Bitrix\Main\IO\Path::normalize($_SERVER["DOCUMENT_ROOT"] . "/urlrewrite.php"), "w");
fwrite($f, "<" . "?\n" . printArray($arNewUrlRewrite) . "\n?" . ">");
fclose($f);
bx_accelerator_reset();