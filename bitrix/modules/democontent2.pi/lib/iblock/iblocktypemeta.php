<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 11.01.2019
 * Time: 08:54
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Iblock;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Democontent2\Pi\Hl;

class IblockTypeMeta
{
    const TABLE_NAME = 'Democontentpiiblocktypemeta';

    private $code = '';
    private $obj = null;

    public function __construct($code)
    {
        $this->code = 'democontent2_pi_' . str_replace('-', '_', $code);
        $hl = new Hl(static::TABLE_NAME);
        if ($hl->obj !== null) {
            $this->obj = $hl->obj;
        } else {
            $className = ToUpper(end(explode('\\', __CLASS__)));
            $add = Hl::create(
                ToLower(static::TABLE_NAME),
                [
                    'UF_IBLOCK_TYPE' => [
                        'Y',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_TYPE')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_TYPE')],
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
                    ['UF_IBLOCK_TYPE']
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );
            if ($add) {
                $this->__construct($code);
            }
        }
    }

    public function get()
    {
        $result = [];

        if (strlen($this->code) > 0 && $this->obj !== null) {
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
                            '=UF_IBLOCK_TYPE' => $this->code
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
        if (strlen($this->code) > 0 && $this->obj !== null) {
            $obj = $this->obj;
            try {
                $obj::add(
                    [
                        'UF_IBLOCK_TYPE' => $this->code,
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

    public function update()
    {
        if (strlen($this->code) > 0 && $this->obj !== null) {
            $this->code = 'democontent2_pi_' . str_replace('democontent2_pi_', '', $this->code);
            $get = $this->get();
            if (!count($get)) {
                $type = \CIBlockType::GetList(
                    [],
                    [
                        '=ID' => $this->code
                    ]
                );

                while ($typeRes = $type->Fetch()) {
                    if ($arIBType = \CIBlockType::GetByIDLang($typeRes['ID'], Application::getInstance()->getContext()->getSite())) {
                        $this->add($arIBType['NAME']);
                    }
                }
            }
        }

        return;
    }
}