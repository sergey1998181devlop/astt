<?php
/**
 * Author: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Skype: pixel365
 * WebSite: semagin.com
 * Date: 18.04.2016
 * Time: 13:23
 */

if( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die();
if( !defined( "WIZARD_DEFAULT_SITE_ID" ) && !empty( $_REQUEST[ "wizardSiteID" ] ) )
    define( "WIZARD_DEFAULT_SITE_ID", $_REQUEST[ "wizardSiteID" ] );

$arWizardDescription = array(
    "NAME"        => GetMessage( "PI_WIZARD_NAME" ),
    "DESCRIPTION" => GetMessage( "PI_WIZARD_DESC" ),
    "VERSION"     => "1.0",
    "START_TYPE"  => "WINDOW",
    "WIZARD_TYPE" => "INSTALL",
    "IMAGE"       => "/images/" . LANGUAGE_ID . "/solution.png",
    "PARENT"      => "wizard_sol",
    "TEMPLATES"   => array(
        array( "SCRIPT" => "wizard_sol" )
    ),
    "STEPS"       => array(),
);

if( !defined( "WIZARD_DEFAULT_SITE_ID" ) ) {
    $arWizardDescription[ "STEPS" ][ ] = "SelectSiteStep";
}
//$arWizardDescription[ "STEPS" ][ ] = "SelectTemplateStep";
//$arWizardDescription[ "STEPS" ][ ] = "SelectThemeStep";
$arWizardDescription[ "STEPS" ][ ] = "SiteSettingsStep";
$arWizardDescription[ "STEPS" ][ ] = "DataInstallStep";
$arWizardDescription[ "STEPS" ][ ] = "FinishStep";