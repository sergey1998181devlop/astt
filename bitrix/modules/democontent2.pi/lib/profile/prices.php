<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 31.01.2019
 * Time: 14:04
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Profile;

use Bitrix\Main\Localization\Loc;
use Democontent2\Pi\FireBase;
use Democontent2\Pi\Hl;

class Prices
{
    const TABLE_NAME = 'Democontentpiuserprices';

    protected $userId = 0;

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
                    'UF_USER_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')]
                        ]
                    ],
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
                [
                    ['UF_USER_ID']
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );
            if ($add) {
                $this->__construct();
            }
        }
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = intval($userId);
    }

    public function get()
    {
        $result = [];

        if ($this->obj !== null && $this->userId > 0) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => ['ID', 'UF_DATA'],
                        'filter' => ['UF_USER_ID' => $this->userId],
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
        if ($this->obj !== null && $this->userId > 0) {
            try {
                $obj = $this->obj;
                $get = $this->get();
                if (count($get) > 0) {
                    $obj::update($get['ID'], ['UF_DATA' => serialize($data)]);
                } else {
                    $obj::add(
                        [
                            'UF_USER_ID' => $this->userId,
                            'UF_DATA' => serialize($data)
                        ]
                    );
                }

                $fireBase = new FireBase($this->userId);
                $fireBase->webPush([
                    'title' => Loc::getMessage('SERVICE_APPLIED'),
                    'body' => Loc::getMessage('SERVICE_PRICES_APPLIED'),
                ]);
                unset($fireBase);
            } catch (\Exception $e) {

            }
        }
    }
}