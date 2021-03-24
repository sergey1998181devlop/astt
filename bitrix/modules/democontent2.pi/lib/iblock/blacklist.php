<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 16.01.2019
 * Time: 13:31
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Iblock;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Democontent2\Pi\Hl;
use Democontent2\Pi\Profile\Profile;
use Democontent2\Pi\User;

class BlackList
{
    const TABLE_NAME = 'Democontentpiblacklist';

    protected $userId = 0;
    protected $blockedId = 0;
    private $obj = null;

    /**
     * BlackList constructor.
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
                    'UF_CREATED_AT' => [
                        'Y',
                        'datetime',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => ''],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')]
                        ]
                    ],
                    'UF_USER_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')]
                        ]
                    ],
                    'UF_BLOCKED_USER_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_BLOCKED_USER_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_BLOCKED_USER_ID')]
                        ]
                    ]
                ],
                [],
                [
                    ['UF_CREATED_AT'],
                    ['UF_USER_ID'],
                    ['UF_BLOCKED_USER_ID']
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
     * @param int $blockedId
     */
    public function setBlockedId($blockedId)
    {
        $this->blockedId = intval($blockedId);
    }

    public function check()
    {
        $result = 0;

        if ($this->obj !== null && $this->userId > 0 && $this->blockedId) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => ['ID'],
                        'filter' => [
                            '=UF_USER_ID' => $this->userId,
                            '=UF_BLOCKED_USER_ID' => $this->blockedId
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $result = intval($res['ID']);
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    public function add()
    {
        $result = false;

        if ($this->obj !== null && $this->userId > 0 && $this->blockedId) {
            if (!$this->check()) {
                try {
                    $obj = $this->obj;
                    $add = $obj::add(
                        [
                            'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                            'UF_USER_ID' => $this->userId,
                            'UF_BLOCKED_USER_ID' => $this->blockedId
                        ]
                    );

                    if ($add->isSuccess()) {
                        $result = true;
                    }
                } catch (\Exception $e) {

                }
            }
        }

        return $result;
    }

    public function remove()
    {
        $result = false;

        if ($this->obj !== null && $this->userId > 0 && $this->blockedId) {
            $id = $this->check();
            if ($id > 0) {
                try {
                    $obj = $this->obj;
                    $remove = $obj::delete($id);

                    if ($remove->isSuccess()) {
                        $result = true;
                    }
                } catch (\Exception $e) {

                }
            }
        }

        return $result;
    }

    public function getList()
    {
        $result = [];

        if ($this->obj !== null && $this->userId > 0) {
            try {
                $profile = new Profile();
                $reviews = new Reviews();
                $us = new User(0);
                $userTable = new UserTable();
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            'UF_BLOCKED_USER_ID'
                        ],
                        'runtime' => [
                            'USER' => [
                                'data_type' => $userTable::getEntity(),
                                'reference' => [
                                    '=this.UF_BLOCKED_USER_ID' => 'ref.ID'
                                ],
                                'join_type' => 'inner'
                            ]
                        ],
                        'filter' => [
                            '=UF_USER_ID' => $this->userId
                        ]
                    ]
                );

                $i = 0;
                while ($res = $get->fetch()) {
                    $us->setId($res['UF_BLOCKED_USER_ID']);
                    $reviews->setUserId($res['UF_BLOCKED_USER_ID']);
                    $profile->setUserId($res['UF_BLOCKED_USER_ID']);
                    $result[$i] = $us->get();
                    $result[$i]['CURRENT_RATING'] = $reviews->rating();
                    $result[$i]['PROFILE'] = $profile->get();
                    $i++;
                }
            } catch (\Exception $e) {
            }
        }

        return $result;
    }
}