<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 31.01.2019
 * Time: 12:33
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Iblock;

use Bitrix\Main\Localization\Loc;
use Democontent2\Pi\Hl;

class Prices
{
    const TABLE_NAME = 'Democontentpiprices';

    private $obj = null;

    /**
     * Prices constructor.
     */
    public function __construct()
    {
        $hl = new Hl(static::TABLE_NAME);
        if ($hl->obj !== null) {
            $this->obj = $hl->obj;
        } else {
            $className = ToUpper(end(explode('\\', __CLASS__)));
            $add = Hl::create(
                ToLower(static::TABLE_NAME),
                [
                    'UF_DATA' => [
                        'Y',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => ''],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DATA')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DATA')]
                        ]
                    ]
                ],
                [
                    'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_DATA` LONGTEXT;',
                ],
                [],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );
            if ($add) {
                $this->__construct();
            }
        }
    }

    public function get()
    {
        $result = [];

        if ($this->obj !== null) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => ['ID', 'UF_DATA'],
                        'order' => ['ID' => 'ASC'],
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

    public function save($data)
    {
        if ($this->obj !== null) {
            try {
                $obj = $this->obj;
                $get = $this->get();
                if (count($get) > 0) {
                    $obj::update($get['ID'], ['UF_DATA' => serialize($data)]);
                } else {
                    $obj::add(['UF_DATA' => serialize($data)]);
                }
            } catch (\Exception $e) {

            }
        }
    }
}