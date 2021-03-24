<?php
/**
 * Author: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Skype: pixel365
 * WebSite: semagin.com
 * Date: 20.04.2016
 * Time: 10:20
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
if (!CModule::IncludeModule("iblock"))
    return;

$moduleId = 'democontent2.pi';
$mManager = new \Bitrix\Main\ModuleManager();

if (!$mManager->isModuleInstalled($moduleId)) {
    CopyDirFiles(
        $_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/democontent2.pi/install/local',
        $_SERVER["DOCUMENT_ROOT"] . "/local",
        true,
        true,
        false
    );

    $mManager->registerModule($moduleId);
    RegisterModuleDependences(
        'main',
        'OnAfterUserAuthorize',
        $moduleId,
        'Democontent2\Pi\Events\Auth',
        'handler'
    );
    RegisterModuleDependences(
        'iblock',
        'OnAfterIBlockElementAdd',
        $moduleId,
        'Democontent2\Pi\Events\Elements\Add',
        "handler"
    );
    RegisterModuleDependences(
        'iblock',
        'OnAfterIBlockElementDelete',
        $moduleId,
        'Democontent2\Pi\Events\Elements\Delete',
        "handler"
    );
    RegisterModuleDependences(
        'iblock',
        'OnAfterIBlockElementUpdate',
        $moduleId,
        'Democontent2\Pi\Events\Elements\Update',
        "handler"
    );
    RegisterModuleDependences(
        'search',
        'BeforeIndex',
        $moduleId,
        'Democontent2\Pi\Events\BeforeIndex',
        "handler"
    );
    RegisterModuleDependences(
        'search',
        'OnSearchGetURL',
        $moduleId,
        'Democontent2\Pi\Events\Search',
        "handler"
    );
    RegisterModuleDependences(
        'main',
        'OnBeforeProlog',
        $moduleId,
        'Democontent2\Pi\Events\BeforeProlog',
        'handler'
    );
    RegisterModuleDependences(
        'iblock',
        'OnBeforeIBlockAdd',
        $moduleId,
        'Democontent2\Pi\Events\Iblocks\BeforeAdd',
        "handler"
    );
    RegisterModuleDependences(
        'iblock',
        'OnAfterIBlockAdd',
        $moduleId,
        'Democontent2\Pi\Events\Iblocks\Add',
        "handler"
    );

    RegisterModuleDependences(
        'iblock',
        'OnIBlockDelete',
        $moduleId,
        'Democontent2\Pi\Events\Iblocks\Delete',
        "handler"
    );
    RegisterModuleDependences(
        'iblock',
        'OnAfterIBlockUpdate',
        $moduleId,
        'Democontent2\Pi\Events\Iblocks\Update',
        "handler"
    );
    RegisterModuleDependences(
        'iblock',
        'OnAfterIBlockSectionAdd',
        $moduleId,
        'Democontent2\Pi\Events\Sections\Add',
        "handler"
    );
    RegisterModuleDependences(
        'iblock',
        'OnAfterIBlockSectionDelete',
        $moduleId,
        'Democontent2\Pi\Events\Sections\Delete',
        "handler"
    );
    RegisterModuleDependences(
        'iblock',
        'OnAfterIBlockSectionUpdate',
        $moduleId,
        'Democontent2\Pi\Events\Sections\Update',
        "handler"
    );
}

$types = array(
    '__democontent2_pi' => array(
        'cities',
        'faq'
    ),
    'democontent2_pi_remont' => array(
        'izolyatsionnye-raboty',
        'plitochnye-raboty',
        'sborka-i-remont-mebeli',
    )
);

\Bitrix\Main\Loader::includeModule($moduleId);
\Bitrix\Main\Config\Option::set($moduleId, 'moderation_new', 0);
\Bitrix\Main\Config\Option::set($moduleId, 'free_limit', 1000);

$iblockClass = new CIBlock();
$item = new \Democontent2\Pi\Iblock\Item(0);
$item->setUserId(1);

$cityId = 0;

foreach ($types as $k => $v) {
    switch ($k) {
        case '__democontent2_pi':
            foreach ($v as $k_) {
                $iblockId = WizardServices::ImportIBlockFromXML(
                    WIZARD_SERVICE_RELATIVE_PATH . "/xml/ru/" . $k_ . ".xml",
                    $k_,
                    $k,
                    WIZARD_SITE_ID,
                    $permissions = Array(
                        "1" => "X",
                        "2" => "R",
                    )
                );
                if (intval($iblockId) > 0) {
                    \CIBlock::SetFields(
                        intval($iblockId),
                        array(
                            'CODE' => array(
                                'IS_REQUIRED' => 'Y',
                                'DEFAULT_VALUE' => array(
                                    'TRANSLITERATION' => 'Y',
                                    'TRANS_LEN' => 100,
                                    'TRANS_CASE' => 'L',
                                    'TRANS_SPACE' => '-',
                                    'TRANS_OTHER' => '-',
                                    'TRANS_EAT' => 'Y'
                                )
                            ),
                            'SECTION_CODE' => array(
                                'IS_REQUIRED' => 'Y',
                                'DEFAULT_VALUE' => array(
                                    'TRANSLITERATION' => 'Y',
                                    'TRANS_LEN' => 100,
                                    'TRANS_CASE' => 'L',
                                    'TRANS_SPACE' => '-',
                                    'TRANS_OTHER' => '-',
                                    'TRANS_EAT' => 'Y'
                                )
                            )
                        )
                    );
                }
            }
            break;
        default:
            if (!$cityId) {
                $cityIblockId = \Democontent2\Pi\Utils::getIBlockIdByType('__democontent2_pi', 'cities');
                if ($cityIblockId > 0) {
                    $get = CIBlockElement::GetList(
                        array(
                            'SORT' => 'ASC'
                        ),
                        array(
                            'IBLOCK_ID' => $cityIblockId
                        ),
                        false,
                        false,
                        array(
                            'ID',
                            'CODE',
                            'IBLOCK_ID'
                        )
                    );
                    while ($res = $get->GetNextElement()) {
                        $f = $res->GetFields();
                        $cityId = intval($f['ID']);
                        break;
                    }
                }
            }

            foreach ($v as $k_) {
                try {
                    $arFields = array(
                        "ACTIVE" => 'Y',
                        "NAME" => GetMessage($k_),
                        "CODE" => $k_,
                        "IBLOCK_TYPE_ID" => $k,
                        "SITE_ID" => array(WIZARD_SITE_ID),
                        "SORT" => 500,
                        "GROUP_ID" => array(
                            "1" => "X",
                            "2" => "R",
                        )
                    );
                    $iBlockId = $iblockClass->Add($arFields);

                    if (intval($iBlockId) > 0) {
                        $x = 0;
                        while ($x++ < rand(3, 6)) {
                            $data = [
                                'iblock' => $iBlockId,
                                'price' => rand(100, 10000),
                                'name' => \Bitrix\Main\Localization\Loc::getMessage('GENERATE_NAMES_' . (rand(1, 21) - 1)),
                                'description' => \Bitrix\Main\Localization\Loc::getMessage('GENERATE_DESCRIPTIONS_' . (rand(1, 21) - 1)),
                                'city' => $cityId
                            ];

                            $item->create($data, []);
                        }
                    }
                } catch (\Exception $e) {

                }
            }
    }
}

\Bitrix\Main\Config\Option::set($moduleId, 'moderation_new', 1);
