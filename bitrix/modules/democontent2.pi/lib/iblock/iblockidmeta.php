<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 11.01.2019
 * Time: 08:53
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Iblock;

use Bitrix\Main\Localization\Loc;
use Democontent2\Pi\Hl;

class IblockIdMeta
{
    const TABLE_NAME = 'Democontentpiiblockidmeta';

    private $id = 0;
    private $obj = null;

    /**
     * IblockIdMeta constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = intval($id);
        $hl = new Hl(static::TABLE_NAME);
        if ($hl->obj !== null) {
            $this->obj = $hl->obj;
        } else {
            $className = ToUpper(end(explode('\\', __CLASS__)));
            $add = Hl::create(
                ToLower(static::TABLE_NAME),
                [
                    'UF_IBLOCK_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_ID')],
                        ]
                    ],
                    'UF_H1' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => 'H1'],
                            'LIST_COLUMN_LABEL' => ['ru' => 'H1'],
                        ]
                    ],
                    'UF_TITLE' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => 'TITLE'],
                            'LIST_COLUMN_LABEL' => ['ru' => 'TITLE'],
                        ]
                    ],
                    'UF_DESCRIPTION' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => 'DESCRIPTION'],
                            'LIST_COLUMN_LABEL' => ['ru' => 'DESCRIPTION'],
                        ]
                    ],
                    'UF_KEYWORDS' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => 'KEYWORDS'],
                            'LIST_COLUMN_LABEL' => ['ru' => 'KEYWORDS'],
                        ]
                    ],
                    'UF_FULL_DESCRIPTION' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_FULL_DESCRIPTION')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_FULL_DESCRIPTION')],
                        ]
                    ],
                ],
                [
                    'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_FULL_DESCRIPTION` LONGTEXT;',
                ],
                [
                    ['UF_IBLOCK_ID']
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );
            if ($add) {
                $this->__construct($id);
            }
        }
    }

    public function get()
    {
        $result = [];

        if ($this->id > 0 && $this->obj !== null) {
            $obj = $this->obj;
            try {
                $get = $obj::getList(
                    [
                        'select' => [
                            'UF_H1',
                            'UF_TITLE',
                            'UF_DESCRIPTION',
                            'UF_KEYWORDS',
                            'UF_FULL_DESCRIPTION'
                        ],
                        'filter' => [
                            '=UF_IBLOCK_ID' => $this->id
                        ],
                        'order' => [
                            'ID' => 'DESC'
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $result = $res;
                }
            } catch (\Exception $e) {
            }
        }

        return $result;
    }

    public function add($name)
    {
        if ($this->id > 0 && $this->obj !== null) {
            $obj = $this->obj;
            try {
                $obj::add(
                    [
                        'UF_IBLOCK_ID' => $this->id,
                        'UF_H1' => $name . ' #CITY_DECLENSION#',
                        'UF_TITLE' => $name . ' #CITY_DECLENSION#',
                        'UF_DESCRIPTION' => '',
                        'UF_KEYWORDS' => '',
                        'UF_FULL_DESCRIPTION' => ''
                    ]
                );
            } catch (\Exception $e) {
            }
        }

        return;
    }

    public function update($name)
    {
        if ($this->id > 0 && $this->obj !== null) {
            $get = $this->get();
            if (!count($get)) {
                $this->add($name);
            }
        }

        return;
    }
}