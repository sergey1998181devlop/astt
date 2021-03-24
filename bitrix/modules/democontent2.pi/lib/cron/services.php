<?php
/**
 * User: Sergey nazarkin
 * Email: nazarkin2017@mail.ru
 * Date: 08.01.2021
 * Time: 13:09
 */

namespace Democontent2\Pi\Cron;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Democontent2\Pi\Hl;

class Services
{
    const QUICKLY_TABLE = 'Democontentpiquickly';

    private $type = '';
    private $quicklyObj = null;

    function __construct($type)
    {
        $this->type = $type;
        $hl = new Hl(static::QUICKLY_TABLE);
        if ($hl->obj !== null) {
            $this->quicklyObj = $hl->obj;
        } else {
            $className = ToUpper(end(explode('\\', __CLASS__)));
            $add = Hl::create(
                ToLower(static::QUICKLY_TABLE),
                [
                    'UF_IBLOCK_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => 'UF_IBLOCK_ID'],
                            'LIST_COLUMN_LABEL' => ['ru' => 'UF_IBLOCK_ID'],
                        ]
                    ],
                    'UF_ITEM_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => 'UF_ITEM_ID'],
                            'LIST_COLUMN_LABEL' => ['ru' => 'UF_ITEM_ID'],
                        ]
                    ],
                    'UF_CITY_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => 'UF_CITY_ID'],
                            'LIST_COLUMN_LABEL' => ['ru' => 'UF_CITY_ID'],
                        ]
                    ],
                    'UF_DATA' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => 'UF_DATA'],
                            'LIST_COLUMN_LABEL' => ['ru' => 'UF_DATA'],
                        ]
                    ],
                ],
                [],
                [
                    ['UF_IBLOCK_ID'],
                    ['UF_ITEM_ID'],
                    ['UF_ITEM_ID'],
                    ['UF_IBLOCK_ID', 'UF_ITEM_ID'],
                    ['UF_IBLOCK_ID', 'UF_ITEM_ID', 'UF_CITY_ID'],
                ],
                Loc::getMessage($className . '_QUICKLY_IBLOCK_NAME')
            );
            if ($add) {
                $this->__construct($type);
            }
        }
    }

    public function delete($iBlockId, $itemId)
    {
        switch ($this->type) {
            case 'quickly':
                if ($this->quicklyObj !== null) {
                    $obj = $this->quicklyObj;
                    try {
                        $get = $obj::getList(
                            [
                                'select' => [
                                    'ID'
                                ],
                                'filter' => [
                                    '=UF_IBLOCK_ID' => intval($iBlockId),
                                    '=UF_ITEM_ID' => intval($itemId)
                                ]
                            ]
                        );
                        while ($res = $get->fetch()) {
                            $obj::delete($res['ID']);
                        }
                    } catch (\Exception $e) {
                    }
                }
                break;
        }
    }

    public function quickly($iBlockId, $itemId, $cityId, $data)
    {
        if ($this->quicklyObj !== null) {
            $obj = $this->quicklyObj;
            try {
                $id = 0;
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID'
                        ],
                        'filter' => [
                            '=UF_IBLOCK_ID' => intval($iBlockId),
                            '=UF_ITEM_ID' => intval($itemId),
                            '=UF_CITY_ID' => intval($cityId)
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $id = intval($res['ID']);
                }

                if (!$id) {
                    $obj::add(
                        [
                            'UF_IBLOCK_ID' => intval($iBlockId),
                            'UF_ITEM_ID' => intval($itemId),
                            'UF_CITY_ID' => intval($cityId),
                            'UF_DATA' => serialize($data)
                        ]
                    );
                } else {
                    $obj::update(
                        $id,
                        [
                            'UF_DATA' => serialize($data)
                        ]
                    );
                }
                $taggedCache = Application::getInstance()->getTaggedCache();
                $taggedCache->clearByTag(md5('quickly' . $cityId));
            } catch (ObjectPropertyException $e) {
            } catch (ArgumentException $e) {
            } catch (SystemException $e) {
            } catch (\Exception $e) {
            }
        }
    }

    public function end()
    {
        $iblocks = [];
        $el = new \CIBlockElement();
        $getEl = $el->GetList(
            [],
            [
                'IBLOCK_TYPE' => 'democontent2_pi_%',
                '!PROPERTY___hidden_' . $this->type . '_end' => false,
                '<=PROPERTY___hidden_' . $this->type . '_end' => DateTime::createFromTimestamp(time())
            ],
            false,
            false,
            [
                'ID',
                'IBLOCK_ID',
                'PROPERTY___hidden_user'
            ]
        );
        while ($res = $getEl->GetNext()) {
            $iblocks[intval($res['IBLOCK_ID'])] = intval($res['IBLOCK_ID']);

            $el->SetPropertyValuesEx(
                intval($res['ID']),
                intval($res['IBLOCK_ID']),
                [
                    'PROPERTY___hidden_' . $this->type . '_start' => false,
                    'PROPERTY___hidden_' . $this->type . '_end' => false
                ]
            );

            if ($this->quicklyObj !== null) {
                $obj = $this->quicklyObj;
                switch ($this->type) {
                    case 'premium':
                        try {
                            $getPremium = $obj::getList(
                                [
                                    'select' => [
                                        'ID'
                                    ],
                                    'filter' => [
                                        '=UF_IBLOCK_ID' => intval($res['IBLOCK_ID']),
                                        '=UF_ITEM_ID' => intval($res['ID'])
                                    ],
                                    'limit' => 1
                                ]
                            );
                            while ($getPremiumRes = $getPremium->fetch()) {
                                $obj::delete($getPremiumRes['ID']);
                            }
                        } catch (ObjectPropertyException $e) {
                        } catch (ArgumentException $e) {
                        } catch (SystemException $e) {
                        } catch (\Exception $e) {
                        }
                        break;
                }
            }

            if (intval($res['PROPERTY___HIDDEN_USER_VALUE']) > 0) {
                /*
                 * send notification
                 */
            }
        }

        $taggedCache = Application::getInstance()->getTaggedCache();

        if (count($iblocks)) {
            foreach ($iblocks as $iblockId) {
                $taggedCache->clearByTag('iblock_id_' . $iblockId);
            }
        }

        switch ($this->type) {
            case 'quickly':
                $taggedCache->clearByTag($this->type);
                break;
        }

        return;
    }
}