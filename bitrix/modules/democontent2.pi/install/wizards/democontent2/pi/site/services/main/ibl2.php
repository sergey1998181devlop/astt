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

$types = array(
    'democontent2_pi_virtual_assistant' => array(
        'reklama-i-prodvizhenie-v-internete',
        'razmeshchenie-obyavleniy',
        //'poisk-i-obrabotka-informatsii',
    ),
    'democontent2_pi_web_development' => array(
        'razrabotka-mobilnykh-prilozheniy',
        'verstka',
    ),
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
