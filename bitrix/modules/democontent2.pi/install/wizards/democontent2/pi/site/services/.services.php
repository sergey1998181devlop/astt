<?php
/**
 * Author: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Skype: pixel365
 * WebSite: semagin.com
 * Date: 18.04.2016
 * Time: 13:22
 */
if( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die();

$arServices = array(
    "main" => array(
        "NAME"        => GetMessage( "SERVICE_MAIN_SETTINGS" ),
        "DESCRIPTION" => GetMessage( "SERVICE_MAIN_SETTINGS" ),
        "STAGES"      => array(),
    )
);
$arServices[ 'main' ][ 'STAGES' ][ ] = 'site_create.php';
$arServices[ 'main' ][ 'STAGES' ][ ] = 'types.php';
$arServices[ 'main' ][ 'STAGES' ][ ] = 'files.php';
$arServices[ 'main' ][ 'STAGES' ][ ] = 'hl.php';
$arServices[ 'main' ][ 'STAGES' ][ ] = 'ibl.php';
$arServices[ 'main' ][ 'STAGES' ][ ] = 'ibl2.php';
$arServices[ 'main' ][ 'STAGES' ][ ] = 'template.php';
//$arServices[ 'main' ][ 'STAGES' ][ ] = 'finish.php';
