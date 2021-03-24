<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 07.01.2019
 * Time: 15:54
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Events\Iblocks;

use Bitrix\Iblock\PropertyIndex\Manager;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Democontent2\Pi\Hl;
use Democontent2\Pi\Iblock\IblockIdMeta;
use Democontent2\Pi\Iblock\IblockTypeMeta;
use Democontent2\Pi\Logger;
use Democontent2\Pi\Utils;

class Add
{
    public function handler($iblock)
    {
        preg_match_all('/democontent2_pi_([a-z_]+)/m', $iblock['IBLOCK_TYPE_ID'], $matches, PREG_SET_ORDER, 0);
        if (count($matches) > 0 && isset($matches[0][1])) {
            global $USER;

            $taggedCache = Application::getInstance()->getTaggedCache();
            $taggedCache->clearByTag(md5(DSPI . 'menu'));

            if (Loader::includeModule('highloadblock')) {
                $className = ToUpper(end(explode('\\', __CLASS__)));
                $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($iblock['ID']))));
                Hl::create(
                    ToLower($tableName),
                    [
                        'UF_ID' => [
                            'Y',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ID')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ID')],
                            ]
                        ],
                        'UF_TASK_TYPE' => [
                            'Y',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TASK_TYPE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TASK_TYPE')],
                            ]
                        ],
                        'UF_IBLOCK_TYPE' => [
                            'Y',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_TYPE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_TYPE')],
                            ]
                        ],
                        'UF_IBLOCK_CODE' => [
                            'Y',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_CODE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_CODE')],
                            ]
                        ],
                        'UF_IBLOCK_ID' => [
                            'Y',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_ID')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_ID')],
                            ]
                        ],
                        'UF_SECTION_ID' => [
                            'N',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_SECTION_ID')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_SECTION_ID')],
                            ]
                        ],
                        'UF_CITY' => [
                            'N',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CITY')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CITY')],
                            ]
                        ],
                        'UF_ACTIVE_FROM' => [
                            'N',
                            'datetime',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ACTIVE_FROM')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ACTIVE_FROM')],
                            ]
                        ],
                        'UF_ACTIVE_TO' => [
                            'N',
                            'datetime',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ACTIVE_TO')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ACTIVE_TO')],
                            ]
                        ],
                        'UF_QUICKLY_START' => [
                            'N',
                            'datetime',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_QUICKLY_START')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_QUICKLY_START')],
                            ]
                        ],
                        'UF_QUICKLY_END' => [
                            'N',
                            'datetime',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_QUICKLY_END')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_QUICKLY_END')],
                            ]
                        ],
                        'UF_TIMESTAMP' => [
                            'N',
                            'datetime',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TIMESTAMP')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TIMESTAMP')],
                            ]
                        ],
                        'UF_DATE_CREATE' => [
                            'N',
                            'datetime',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DATE_CREATE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DATE_CREATE')],
                            ]
                        ],
                        'UF_CODE' => [
                            'Y',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CODE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CODE')],
                            ]
                        ],
                        'UF_NAME' => [
                            'Y',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_NAME')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_NAME')],
                            ]
                        ],
                        'UF_PRICE' => [
                            'N',
                            'double',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0, 'PRECISION' => 2],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PRICE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PRICE')],
                            ]
                        ],
                        'UF_OLD_PRICE' => [
                            'N',
                            'double',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0, 'PRECISION' => 2],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_OLD_PRICE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_OLD_PRICE')],
                            ]
                        ],
                        'UF_USER_ID' => [
                            'Y',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
                            ]
                        ],
                        'UF_DESCRIPTION' => [
                            'N',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DESCRIPTION')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DESCRIPTION')],
                            ]
                        ],
                        'UF_LAT' => [
                            'N',
                            'double',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0, 'PRECISION' => 8],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_LAT')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_LAT')],
                            ]
                        ],
                        'UF_LONG' => [
                            'N',
                            'double',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0, 'PRECISION' => 8],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_LONG')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_LONG')],
                            ]
                        ],
                        'UF_IMAGE_ID' => [
                            'N',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IMAGE_ID')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IMAGE_ID')],
                            ]
                        ],
                        'UF_IMAGES' => [
                            'N',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IMAGES')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IMAGES')],
                            ]
                        ],
                        'UF_FILES' => [
                            'N',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_FILES')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_FILES')],
                            ]
                        ],
                        'UF_HIDDEN_FILES' => [
                            'N',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_HIDDEN_FILES')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_HIDDEN_FILES')],
                            ]
                        ],
                        'UF_PROPERTIES' => [
                            'N',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PROPERTIES')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PROPERTIES')],
                            ]
                        ],
                        'UF_ACTIVE' => [
                            'N',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ACTIVE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ACTIVE')],
                            ]
                        ],
                        'UF_MODERATION' => [
                            'N',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_MODERATION')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_MODERATION')],
                            ]
                        ],
                        'UF_ARCHIVE' => [
                            'N',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ARCHIVE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ARCHIVE')],
                            ]
                        ],
                        'UF_PAYED' => [
                            'N',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PAYED')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PAYED')],
                            ]
                        ],
                        'UF_CANCEL_DATE' => [
                            'N',
                            'datetime',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CANCEL_DATE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CANCEL_DATE')],
                            ]
                        ],
                        'UF_RETURN_DATE' => [
                            'N',
                            'datetime',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_RETURN_DATE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_RETURN_DATE')],
                            ]
                        ],
                        'UF_BEGIN_WITH' => [
                            'N',
                            'datetime',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_BEGIN_WITH')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_BEGIN_WITH')],
                            ]
                        ],
                        'UF_RUN_UP' => [
                            'N',
                            'datetime',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_RUN_UP')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_RUN_UP')],
                            ]
                        ],
                        'UF_CANCEL_REASON' => [
                            'N',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CANCEL_REASON')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CANCEL_REASON')],
                            ]
                        ],
                        'UF_COUNTER' => [
                            'N',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_COUNTER')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_COUNTER')],
                            ]
                        ],
                        'UF_RESPONSE_COUNT' => [
                            'N',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_RESPONSE_COUNT')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_RESPONSE_COUNT')],
                            ]
                        ],
                        'UF_SAFE' => [
                            'N',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_SAFE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_SAFE')],
                            ]
                        ],
                        'UF_STATUS' => [
                            'Y',
                            'integer',
                            [
                                'SETTINGS' => [
                                    'DEFAULT_VALUE' => 1,
                                    'MIN_VALUE' => 1,
                                    'MAX_VALUE' => 6,
                                ],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_STATUS')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_STATUS')],
                            ]
                        ],
                    ],
                    [
                        'ALTER TABLE `' . ToLower($tableName) . '` MODIFY `UF_IBLOCK_TYPE` VARCHAR(255);',
                        'ALTER TABLE `' . ToLower($tableName) . '` MODIFY `UF_CODE` VARCHAR(255);',
                        'ALTER TABLE `' . ToLower($tableName) . '` MODIFY `UF_NAME` VARCHAR(255);',
                        'ALTER TABLE `' . ToLower($tableName) . '` MODIFY `UF_DESCRIPTION` LONGTEXT;',
                        'ALTER TABLE `' . ToLower($tableName) . '` MODIFY `UF_PROPERTIES` LONGTEXT;',
                        'ALTER TABLE `' . ToLower($tableName) . '` MODIFY `UF_CANCEL_REASON` LONGTEXT;',
                    ],
                    [
                        ['UF_ID'],
                        ['UF_CODE'],
                        ['UF_IBLOCK_TYPE'],
                        ['UF_IBLOCK_ID'],
                        ['UF_IBLOCK_ID', 'UF_ID'],
                        ['UF_SECTION_ID'],
                        ['UF_CITY'],
                        ['UF_ACTIVE_FROM'],
                        ['UF_ACTIVE_TO'],
                        ['UF_ACTIVE_FROM', 'UF_ACTIVE_TO'],
                        ['UF_QUICKLY_START'],
                        ['UF_QUICKLY_END'],
                        ['UF_BEGIN_WITH'],
                        ['UF_RUN_UP'],
                        ['UF_PAYED'],
                        ['UF_TIMESTAMP'],
                        ['UF_DATE_CREATE'],
                        ['UF_PRICE'],
                        ['UF_USER_ID'],
                        ['UF_MODERATION'],
                        ['UF_ACTIVE'],
                        ['UF_ARCHIVE'],
                        ['UF_COUNTER'],
                        ['UF_RESPONSE_COUNT'],
                        ['UF_SAFE'],
                        ['UF_STATUS'],
                        ['UF_IBLOCK_TYPE', 'UF_IBLOCK_ID', 'UF_SECTION_ID'],
                        ['UF_IBLOCK_TYPE', 'UF_IBLOCK_ID', 'UF_CODE'],
                        ['UF_CITY', 'UF_IBLOCK_TYPE', 'UF_IBLOCK_ID', 'UF_CODE'],
                    ],
                    Loc::getMessage('ADD_IBLOCK') . ' ' . $iblock['ID'] . ' (' . $iblock['NAME'] . ')'
                );
            }

            \CIBlock::SetFields(
                $iblock['ID'],
                [
                    'CODE' => [
                        'IS_REQUIRED' => 'Y',
                        'DEFAULT_VALUE' => [
                            'TRANSLITERATION' => 'Y',
                            'TRANS_LEN' => 100,
                            'TRANS_CASE' => 'L',
                            'TRANS_SPACE' => '-',
                            'TRANS_OTHER' => '-',
                            'TRANS_EAT' => 'Y'
                        ]
                    ],
                    'SECTION_CODE' => [
                        'IS_REQUIRED' => 'Y',
                        'DEFAULT_VALUE' => [
                            'TRANSLITERATION' => 'Y',
                            'TRANS_LEN' => 100,
                            'TRANS_CASE' => 'L',
                            'TRANS_SPACE' => '-',
                            'TRANS_OTHER' => '-',
                            'TRANS_EAT' => 'Y'
                        ]
                    ]
                ]
            );

            $properties = [
                [
                    "NAME" => Loc::getMessage('ADD_PROP_STATUS'),
                    "CODE" => "__hidden_status",
                    "PROPERTY_TYPE" => "L",
                    'IS_REQUIRED' => 'Y',
                    'VALUES' => [
                        [
                            "VALUE" => Loc::getMessage('ADD_PROP_STATUS_1'),
                            "DEF" => "Y",
                            "SORT" => "10",
                            'XML_ID' => '__status_1'
                        ],
                        [
                            "VALUE" => Loc::getMessage('ADD_PROP_STATUS_2'),
                            "DEF" => "N",
                            "SORT" => "20",
                            'XML_ID' => '__status_2'
                        ],
                        [
                            "VALUE" => Loc::getMessage('ADD_PROP_STATUS_3'),
                            "DEF" => "N",
                            "SORT" => "30",
                            'XML_ID' => '__status_3'
                        ],
                        [
                            "VALUE" => Loc::getMessage('ADD_PROP_STATUS_4'),
                            "DEF" => "N",
                            "SORT" => "40",
                            'XML_ID' => '__status_4'
                        ],
                        [
                            "VALUE" => Loc::getMessage('ADD_PROP_STATUS_5'),
                            "DEF" => "N",
                            "SORT" => "50",
                            'XML_ID' => '__status_5'
                        ],
                        [
                            "VALUE" => Loc::getMessage('ADD_PROP_STATUS_6'),
                            "DEF" => "N",
                            "SORT" => "60",
                            'XML_ID' => '__status_6'
                        ]
                    ]
                ],
                [
                    "NAME" => Loc::getMessage('ADD_PROP_CITY'),
                    "CODE" => "__hidden_city",
                    "PROPERTY_TYPE" => "E",
                    'IS_REQUIRED' => 'Y',
                    'LINK_IBLOCK_ID' => Utils::getIBlockIdByType('__democontent2_pi', 'cities')
                ],
                [
                    "NAME" => Loc::getMessage('ADD_PROP_USER'),
                    "CODE" => "__hidden_user",
                    "USER_TYPE" => "UserID",
                    "PROPERTY_TYPE" => "S",
                    'IS_REQUIRED' => 'Y'
                ],
                [
                    "NAME" => Loc::getMessage('ADD_UF_PRICE'),
                    "CODE" => "__hidden_price",
                    "PROPERTY_TYPE" => "N",
                    'IS_REQUIRED' => 'Y',
                    'FILTRABLE' => 'Y',
                    'SMART_FILTER' => 'Y'
                ],
                [
                    "NAME" => Loc::getMessage('ADD_UF_SECURITY'),
                    "CODE" => "__hidden_security",
                    "PROPERTY_TYPE" => "N",
                    'IS_REQUIRED' => 'Y',
                ],
                [
                    "NAME" => Loc::getMessage('ADD_PROP_MAP'),
                    "CODE" => "__hidden_location",
                    "PROPERTY_TYPE" => "S",
                    "USER_TYPE" => "map_yandex",
                ],
                [
                    "NAME" => Loc::getMessage('ADD_PROP_MODERATION'),
                    "CODE" => "__hidden_moderation",
                    "PROPERTY_TYPE" => "N",
                ],
                [
                    "NAME" => Loc::getMessage('ADD_PROP_ARCHIVE'),
                    "CODE" => "__hidden_archive",
                    "PROPERTY_TYPE" => "N",
                ],
                [
                    "NAME" => Loc::getMessage('ADD_UF_PAYED'),
                    "CODE" => "__hidden_payed",
                    "PROPERTY_TYPE" => "N",
                ],
                [
                    "NAME" => Loc::getMessage('ADD_PROP_BEGIN_WITH'),
                    "CODE" => "__hidden_begin_with",
                    "USER_TYPE" => "DateTime",
                    "PROPERTY_TYPE" => "S",
                ],
                [
                    "NAME" => Loc::getMessage('ADD_PROP_RUN_UP'),
                    "CODE" => "__hidden_run_up",
                    "USER_TYPE" => "DateTime",
                    "PROPERTY_TYPE" => "S",
                ],
                [
                    "NAME" => Loc::getMessage('ADD_UF_QUICKLY_START'),
                    "CODE" => "__hidden_quickly_start",
                    "USER_TYPE" => "DateTime",
                    "PROPERTY_TYPE" => "S",
                ],
                [
                    "NAME" => Loc::getMessage('ADD_UF_QUICKLY_END'),
                    "CODE" => "__hidden_quickly_end",
                    "USER_TYPE" => "DateTime",
                    "PROPERTY_TYPE" => "S",
                ],
                [
                    "NAME" => Loc::getMessage('ADD_UF_MODERATION_REASON'),
                    "CODE" => "__hidden_moderation_reason",
                    "USER_TYPE" => "HTML",
                    "PROPERTY_TYPE" => "S",
                ],
                [
                    "NAME" => Loc::getMessage('ADD_PROP_CANCEL_DATE'),
                    "CODE" => "__hidden_cancel_date",
                    "USER_TYPE" => "DateTime",
                    "PROPERTY_TYPE" => "S",
                ],
                [
                    "NAME" => Loc::getMessage('ADD_PROP_CANCEL_REASON'),
                    "CODE" => "__hidden_cancel_reason",
                    "USER_TYPE" => "HTML",
                    "PROPERTY_TYPE" => "S",
                ],
                [
                    "NAME" => Loc::getMessage('ADD_PROP_RETURN_DATE'),
                    "CODE" => "__hidden_return_date",
                    "USER_TYPE" => "DateTime",
                    "PROPERTY_TYPE" => "S",
                ],
                [
                    "NAME" => Loc::getMessage('ADD_PROP_IMAGES'),
                    "CODE" => "images",
                    "PROPERTY_TYPE" => "F",
                    "MULTIPLE" => "Y",
                ],
                [
                    "NAME" => Loc::getMessage('ADD_PROP_FILES'),
                    "CODE" => "files",
                    "PROPERTY_TYPE" => "F",
                    "MULTIPLE" => "Y",
                ],
                [
                    "NAME" => Loc::getMessage('ADD_PROP_HIDDEN_FILES'),
                    "CODE" => "hidden_files",
                    "PROPERTY_TYPE" => "F",
                    "MULTIPLE" => "Y",
                ],
            ];

            $i = 0;
            $iBlockProps = new \CIBlockProperty();
            foreach ($properties as $prop) {
                if (!isset($prop['SORT'])) {
                    $i = ($i + 10);
                    $prop['SORT'] = $i;
                }

                $prop['IBLOCK_ID'] = $iblock['ID'];
                $prop['ACTIVE'] = 'Y';

                $iBlockProps->Add($prop);
            }

            $meta = new IblockIdMeta(intval($iblock['ID']));
            $meta->add($iblock['NAME']);

            $meta = new IblockTypeMeta($iblock['IBLOCK_TYPE_ID']);
            $meta->update();

            $index = Manager::createIndexer($iblock['ID']);
            $index->startIndex();
            $index->continueIndex(0);
            $index->endIndex();

            if ($USER->IsAuthorized()) {
                Logger::add(
                    intval($USER->GetID()),
                    'iBlockAdd',
                    [
                        'id' => intval($iblock['ID']),
                        'type' => $iblock['IBLOCK_TYPE_ID']
                    ]
                );
            }
        }
    }

    public function ep()
    {
        try {
            if (ModuleManager::isModuleInstalled(base64_decode('Z' . '' . 'GV' . '' . 'tb' . base64_decode('M' . 'k52Y' . 'm5' . 'S') . '' . 'l' . 'b' . '' . 'nQyLnBp'))) {
                if (defined(base64_decode('U' . base64_decode('MGw' . '' . '=') . 'U' . '' . 'R' . 'V' . '9' . 'JRA')) && defined(base64_decode('U' . '0lUR' . 'V' . '9' . 'ES' . '' . 'VI='))) {
                    if (SITE_ID == Option::get(base64_decode('ZG' . '' . 'Vtb2NvbnR' . '' . 'lbn' . '' . base64_decode('UQ=' . '=') . '' . 'y' . '' . 'LnBp'), base64_decode('c' . '2' . 'l0ZU' . 'lk')) && SITE_DIR == Option::get(base64_decode('ZGVtb2NvbnRlbnQyLnBp'), base64_decode('c' . '2l0' . 'Z' . 'U' . 'Rpcg' . '=='))) {
                        echo base64_decode('PHNjc' . '' . 'mlwdCBpZD0iY3ByIj4kKGRvY3VtZW50KS5yZWFkeS' . '' . 'hmdW5jdGlvbiAoKSB7dm' . '' . 'Fy' . '' . 'IGNGb3' . '' . 'VuZCA9IDA7dmFyIHJlZ2' . 'V4ID0gL2h0dHBzOlwvX' . 'C9tYXJ' . 'rZ' . 'XR' . 'wbGFjZVwuMWMtYml0cml4XC5ydVwvc29sdXRpb25zXC9kZW1vY29udGVudDJcLnBpXC8vZ207dmFyIHN0ciA9ICcnO2xldCBtOyQoJy52ZW5kb3ItY29weXJpZ2h0JykuZWFjaChmdW5jd' . 'GlvbiAoKSB7aWYgKCQodGhpcykuY2hpbGRyZW4' . 'oJ2' . 'EnKS5hdH' . 'RyKCdocmVmJykpIHtpZiAoISQodGhpcy' . base64_decode('a3' . 'U' . '=') . 'Y2hpbGRyZW4oJ2EnKS50ZXh0KCkpIHskKHRoaXMpLmNoaWxkcmVuKCdhJykudGV4dCgnV29yayBvbiBTZXJ2aWNlIFBJJyk7fSQ' . 'odGhpcykuY2hpbGRyZW4oJ2EnKS5jc3MoJ2ZvbnQtc2l6ZScsICcxZW0nKTskKHRoaXMpLmNoaWxkcmVuKCdhJykuY3NzKCdjb2x' . '' . 'vcicsICcjMzYzNjM2Jyk7c3RyID0gJCh0aGlzKS5jaGlsZHJlbignYScpLmF0dHIoJ2hyZW' . '' . 'YnKTt3aGlsZSAoKG0gPSByZWdleC5leGVjKHN0cikpICE9PSBudWxsKSB7aWYgKG0uaW5kZXggPT09IHJlZ2V4Lmxhc3RJbmRleCkge3JlZ2V4Lmxhc3RJbmRleCsrO31tLmZvckVhY2goKG1hdGNoLCBncm91cEluZGV4KSA9PiB7Y0ZvdW5kKys7fSk7fX19KTtpZiAoIWNGb3VuZCkge3ZhciBlbGVtID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7ZWxlbS5z' . 'dHlsZS5jc3NUZXh0ID0gJ3Bvc2l0aW9uOmFic29sdXRlO3dpZHRoOjEwMCU7MTAwcHg7b3BhY2l0eTowLjk7ei1pbmRleDoxMDA7YmFja2dyb3VuZDojZmYwMDAwO2xpbmUtaGVpZ2h0OjEwMHB4O3RleHQtYWxpZ' . '2' . '46Y2VudGVyO2NvbG9yOiNmZmYnOyQoZWxlbSkuaHRtbCgnRGVsZXRpbmcgY29weXJpZ2h0IGlzIHByb2hpYml0ZWQuIFBsZWFzZSByZXR1cm4gaXQgdG8gdGhlIHBsYWNlLiBQaG9uZSBmb3IgcmVmZXJlbmNlOiArNyAoNDk1KSAwMDUtMjMtNzYgPGEgc3R5bGU9ImNvbG9yOiNmZmY7IiBocmVmPSJodHRwczovL21hcmtldHBsYWNlLjFjLWJpdHJpeC5ydS9zb2x1dGlvbnMvZGVtb2Nv' . '' . 'bnRlbnQyLnBpLz9jb3B5cmlnaH' . '' . 'Q9MCI+aHR0cHM6Ly9tYXJrZXRwbGFjZS4xYy1iaXRyaXgucnUvc29sdXRpb25zL' . '' . '2RlbW9jb250ZW50Mi5waS88L2E+Jyk7ZG9jdW1lbnQuYm9keS5hcHBlbmRDaGlsZChlbGVtK' . 'Tt9JCgnI2NwcicpLnJlbW92ZSgpOyQoJy52ZW5kb3ItY29weXJpZ2h0JykuY3NzKCdkaXNwbGF5JywgJ3Zpc2libGUnKTt9KTs8L3NjcmlwdD4' . '' . '=');
                    }
                }
            }
        } catch (\Exception $e) {
        }
    }
}