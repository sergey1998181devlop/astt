<?php
/**
 * Author: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Skype: pixel365
 * WebSite: semagin.com
 * Date: 20.04.2016
 * Time: 10:27
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("iblock"))
    return;

$arTypes = array(
    'democontent2_pi_remont' => 10,
    'democontent2_pi_virtual_assistant' => 20,
    'democontent2_pi_web_development' => 30,
    '__democontent2_pi' => 500,
);

$arLanguages = array();
$rsLanguage = CLanguage::GetList($by, $order, array());
while ($arLanguage = $rsLanguage->Fetch())
    $arLanguages[] = $arLanguage["LID"];

$iblockType = new CIBlockType;

foreach ($arTypes as $type => $sort) {
    $arType = array(
        'ID' => $type,
        'SECTIONS' => 'Y',
        'IN_RSS' => 'N',
        'SORT' => $sort,
        'LANG' => array()
    );

    $dbType = CIBlockType::GetList(array(), array("=ID" => $arType["ID"]));

    if ($dbType->Fetch())
        continue;

    foreach ($arLanguages as $languageID) {
        WizardServices::IncludeServiceLang("types.php", $languageID);

        $arType["LANG"][$languageID]["NAME"] = GetMessage($arType["ID"]);
        $arType["LANG"][$languageID]["ELEMENT_NAME"] = GetMessage("CATALOG_ELEMENT_NAME");

        if ($arType["SECTIONS"] == "Y") {
            $arType["LANG"][$languageID]["SECTION_NAME"] = GetMessage("CATALOG_SECTION_NAME");
        }
    }

    $res = $iblockType->Add($arType);
};

try {
    $ufTypes = array(
        'UF_DSPI_DOCUMENTS',
        'UF_DSPI_EXECUTOR',
        'UF_DSPI_MOD_OFF',
        'UF_DSPI_CITY',
        'UF_DSPI_SAFECROW_ID',
        'UF_DSPI_LIMIT',
        'UF_DSPI_API_KEY',
        'UF_DSPI_RATING',
        'UF_DSPI_BUSY'
    );

    foreach ($ufTypes as $ufType) {
        switch ($ufType) {
            case 'UF_DSPI_DOCUMENTS':
            case 'UF_DSPI_EXECUTOR':
            case 'UF_DSPI_MOD_OFF':
            case 'UF_DSPI_BUSY':
                $userType = new \CUserTypeEntity();
                $userType->Add(
                    [
                        "ENTITY_ID" => "USER",
                        "FIELD_NAME" => $ufType,
                        "USER_TYPE_ID" => "boolean",
                        "XML_ID" => "",
                        "MULTIPLE" => "N",
                        "MANDATORY" => "N",
                        "SHOW_FILTER" => "N",
                        "SHOW_IN_LIST" => "Y",
                        "EDIT_IN_LIST" => "Y",
                        "DEFAULT_VALUE" => 0,
                        "IS_SEARCHABLE" => "N",
                        'EDIT_FORM_LABEL' => [
                            'ru' => GetMessage($ufType)
                        ],
                        'LIST_COLUMN_LABEL' => [
                            'ru' => GetMessage($ufType)
                        ],
                        'LIST_FILTER_LABEL' => [
                            'ru' => GetMessage($ufType)
                        ]
                    ]
                );
                break;
            case 'UF_DSPI_CITY':
            case 'UF_DSPI_SAFECROW_ID':
            case 'UF_DSPI_LIMIT':
                $userType = new \CUserTypeEntity();
                $userType->Add(
                    [
                        "ENTITY_ID" => "USER",
                        "FIELD_NAME" => $ufType,
                        "USER_TYPE_ID" => "integer",
                        "XML_ID" => "",
                        "MULTIPLE" => "N",
                        "MANDATORY" => "N",
                        "SHOW_FILTER" => "N",
                        "SHOW_IN_LIST" => "Y",
                        "EDIT_IN_LIST" => "Y",
                        "DEFAULT_VALUE" => "0",
                        "IS_SEARCHABLE" => "N",
                        'EDIT_FORM_LABEL' => [
                            'ru' => GetMessage($ufType)
                        ],
                        'LIST_COLUMN_LABEL' => [
                            'ru' => GetMessage($ufType)
                        ],
                        'LIST_FILTER_LABEL' => [
                            'ru' => GetMessage($ufType)
                        ]
                    ]
                );
                break;
            case 'UF_DSPI_API_KEY':
                $userType = new \CUserTypeEntity();
                $userType->Add(
                    [
                        "ENTITY_ID" => "USER",
                        "FIELD_NAME" => $ufType,
                        "USER_TYPE_ID" => "string",
                        "XML_ID" => "",
                        "MULTIPLE" => "N",
                        "MANDATORY" => "N",
                        "SHOW_FILTER" => "N",
                        "SHOW_IN_LIST" => "Y",
                        "EDIT_IN_LIST" => "Y",
                        "DEFAULT_VALUE" => "",
                        "IS_SEARCHABLE" => "N",
                        'EDIT_FORM_LABEL' => [
                            'ru' => GetMessage($ufType)
                        ],
                        'LIST_COLUMN_LABEL' => [
                            'ru' => GetMessage($ufType)
                        ],
                        'LIST_FILTER_LABEL' => [
                            'ru' => GetMessage($ufType)
                        ]
                    ]
                );
                break;
            case 'UF_DSPI_RATING':
                $userType = new \CUserTypeEntity();
                $userType->Add(
                    [
                        "ENTITY_ID" => "USER",
                        "FIELD_NAME" => $ufType,
                        "USER_TYPE_ID" => "double",
                        "XML_ID" => "",
                        "MULTIPLE" => "N",
                        "MANDATORY" => "N",
                        "SHOW_FILTER" => "N",
                        "SHOW_IN_LIST" => "Y",
                        "EDIT_IN_LIST" => "Y",
                        "DEFAULT_VALUE" => 0,
                        "IS_SEARCHABLE" => "N",
                        'EDIT_FORM_LABEL' => [
                            'ru' => GetMessage($ufType)
                        ],
                        'LIST_COLUMN_LABEL' => [
                            'ru' => GetMessage($ufType)
                        ],
                        'LIST_FILTER_LABEL' => [
                            'ru' => GetMessage($ufType)
                        ],
                        'SETTINGS' => [
                            'DEFAULT_VALUE' => 0,
                            'PRECISION' => 2,
                            'MIN_VALUE' => 0,
                            'MAX_VALUE' => 100,
                        ]
                    ]
                );
                break;
        }
    }
} catch (\Exception $e) {
}
