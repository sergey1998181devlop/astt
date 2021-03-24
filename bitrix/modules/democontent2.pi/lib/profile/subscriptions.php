<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 26.01.2019
 * Time: 19:09
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Profile;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Democontent2\Pi\Hl;

class Subscriptions
{
    const TABLE_NAME = 'Democontentpisubscriptions';

    protected $userId = 0;
    protected $iBlockType = '';
    protected $iBlockId = 0;

    private $obj = null;

    /**
     * Subscriptions constructor.
     */
    public function __construct()
    {
        $className = ToUpper(end(explode('\\', __CLASS__)));
        $hl = new Hl(static::TABLE_NAME);
        if ($hl->obj !== null) {
            $this->obj = $hl->obj;
        } else {
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
                    'UF_IBLOCK_TYPE' => [
                        'Y',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_TYPE')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_TYPE')]
                        ]
                    ],
                    'UF_IBLOCK_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_ID')]
                        ]
                    ],
                    'UF_PAID_TO' => [
                        'N',
                        'datetime',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PAID_TO')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PAID_TO')]
                        ]
                    ],
                ],
                [
                    'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_IBLOCK_TYPE` VARCHAR(255);'
                ],
                [
                    ['UF_USER_ID'],
                    ['UF_IBLOCK_TYPE'],
                    ['UF_IBLOCK_ID'],
                    ['UF_USER_ID', 'UF_IBLOCK_TYPE', 'UF_IBLOCK_ID'],
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

    /**
     * @param string $iBlockType
     */
    public function setIBlockType($iBlockType)
    {
        $this->iBlockType = $iBlockType;
    }

    /**
     * @param int $iBlockId
     */
    public function setIBlockId($iBlockId)
    {
        $this->iBlockId = intval($iBlockId);
    }

    public function get()
    {
        $result = [];

        if ($this->obj !== null && $this->userId > 0 && $this->iBlockId > 0 && $this->iBlockType) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => ['ID', 'UF_PAID_TO'],
                        'filter' => [
                            '=UF_USER_ID' => $this->userId,
                            '=UF_IBLOCK_TYPE' => $this->iBlockType,
                            '=UF_IBLOCK_ID' => $this->iBlockId,
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

    public function add()
    {
        $result = 0;

        if ($this->obj !== null && $this->userId > 0 && $this->iBlockId > 0 && $this->iBlockType) {
            if (!count($this->get())) {
                try {
                    $obj = $this->obj;
                    $add = $obj::add(
                        [
                            'UF_USER_ID' => $this->userId,
                            'UF_IBLOCK_TYPE' => $this->iBlockType,
                            'UF_IBLOCK_ID' => $this->iBlockId,
                            'UF_PAID_TO' => false
                        ]
                    );

                    if ($add->isSuccess()) {
                        $result = $add->getId();
                    }
                } catch (\Exception $e) {

                }
            }
        }

        return $result;
    }

    public function remove()
    {
        if ($this->obj !== null && $this->userId > 0 && $this->iBlockId > 0 && $this->iBlockType) {
            $get = $this->get();
            if (count($get)) {
                try {
                    $obj = $this->obj;
                    $obj::delete($get['ID']);
                } catch (\Exception $e) {

                }
            }
        }
    }

    public function paidTo($days)
    {
        $result = false;

        if ($this->obj !== null && $this->userId > 0 && $this->iBlockId > 0 && $this->iBlockType) {
            $item = $this->get();

            try {
                $obj = $this->obj;

                if (count($item) > 0 && isset($item['ID'])) {
                    $time = 0;
                    if ($item['UF_PAID_TO']) {
                        if (strtotime($item['UF_PAID_TO']) > time()) {
                            $time = (strtotime($item['UF_PAID_TO']) + (86400 * $days));
                        }
                    }

                    $update = $obj::update(
                        $item['ID'],
                        [
                            'UF_PAID_TO' => DateTime::createFromTimestamp(($time > 0) ? $time : (time() + (86400 * $days)))
                        ]
                    );

                    if ($update->isSuccess()) {
                        $result = true;
                    }
                } else {
                    $add = $obj::add(
                        [
                            'UF_USER_ID' => $this->userId,
                            'UF_IBLOCK_TYPE' => $this->iBlockType,
                            'UF_IBLOCK_ID' => $this->iBlockId,
                            'UF_PAID_TO' => DateTime::createFromTimestamp(time() + (86400 * $days))
                        ]
                    );

                    if ($add->isSuccess()) {
                        $result = true;
                    }
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    public function getList()
    {
        $result = [];

        if ($this->obj !== null && $this->userId > 0) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => ['*'],
                        'filter' => [
                            '=UF_USER_ID' => $this->userId,
                        ]
                    ]
                );
                while ($res = $get->fetch()) {
                    $result[$res['UF_IBLOCK_ID']] = $res;
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }
}