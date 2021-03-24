<?php
/**
 * Date: 08.07.2019
 * Time: 16:10
 * User: Ruslan Semagin
 * Company: PIXEL365
 * Web: https://pixel365.ru
 * Email: pixel.365.24@gmail.com
 * Phone: +7 (495) 005-23-76
 * Skype: pixel365
 * Product Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 * Use of this code is allowed only under the condition of full compliance with the terms of the license agreement,
 * and only as part of the product.
 */

namespace Democontent2\Pi;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Democontent2\Pi\Iblock\Reviews;
use Democontent2\Pi\Payments\SafeCrow\Cards;
use Democontent2\Pi\Profile\Profile;

class Favourites
{
    const TABLE_NAME = 'Democontentpifavourites';

    private $ttl = 86400 * 365;
    private $userId = 0;
    private $executorId = 0;
    private $class = null;

    /**
     * Favourites constructor.
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = intval($userId);

        if ($this->userId > 0) {
            $hl = new Hl(self::TABLE_NAME);
            if ($hl->obj !== null) {
                $this->class = $hl->obj;
            } else {
                $className = ToUpper(end(explode('\\', __CLASS__)));
                $add = Hl::create(
                    ToLower(self::TABLE_NAME),
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
                        'UF_EXECUTOR_ID' => [
                            'Y',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_EXECUTOR_ID')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_EXECUTOR_ID')]
                            ]
                        ]
                    ],
                    [],
                    [['UF_USER_ID']],
                    Loc::getMessage($className . '_IBLOCK_NAME')
                );
                if ($add) {
                    $this->__construct($this->userId);
                }
            }
        }
    }

    /**
     * @param float|int $ttl
     */
    public function setTtl($ttl): void
    {
        $this->ttl = $ttl;
    }

    /**
     * @param int $executorId
     */
    public function setExecutorId(int $executorId): void
    {
        $this->executorId = intval($executorId);

        if ($this->executorId == $this->userId) {
            $this->executorId = 0;
        }
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = intval($userId);
    }

    public function change(): void
    {

        if ($this->userId && $this->executorId && $this->class !== null) {

            $obj = $this->class;
            try {
                $id = 0;
                $get = $obj::getList(
                    [
                        'select' => ['ID'],
                        'filter' => [
                            '=UF_USER_ID' => $this->userId,
                            '=UF_EXECUTOR_ID' => $this->executorId
                        ],
                        'limit' => 1
                    ]
                );

                while ($res = $get->Fetch()) {
                    $id = intval($res['ID']);
                }

                if (!$id) {
                    $obj::add(
                        [
                            'UF_USER_ID' => $this->userId,
                            'UF_EXECUTOR_ID' => $this->executorId
                        ]
                    );
                } else {
                    $obj::delete($id);
                }

                $taggedCache = Application::getInstance()->getTaggedCache();
                $taggedCache->clearByTag('favourites_' . $this->userId);
            } catch (\Exception $e) {
            }
        }
    }

    public function getIdList()
    {
        $result = [];

        if ($this->userId && $this->class !== null) {
            try {
                $obj = $this->class;
                $get = $obj::getList(
                    [
                        'select' => ['UF_EXECUTOR_ID'],
                        'filter' => [
                            '=UF_USER_ID' => $this->userId
                        ]
                    ]
                );

                while ($res = $get->Fetch()) {
                    $result[] = intval($res['UF_EXECUTOR_ID']);
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getList()
    {
        $result = [];
        if ($this->userId && $this->class !== null) {
            $ids = [];
            $obj = $this->class;

            try {
                $get = $obj::getList(
                    [
                        'select' => ['UF_EXECUTOR_ID'],
                        'filter' => [
                            '=UF_USER_ID' => $this->userId
                        ]
                    ]
                );

                while ($res = $get->Fetch()) {
                    $ids[] = [
                        '=USER_ID' => intval($res['UF_EXECUTOR_ID'])
                    ];
                }
            } catch (\Exception $e) {

            }

            if (count($ids)) {
                try {
                    $us = new User(0);
                    $profile = new Profile();
                    $cards = new Cards();
                    $reviews = new Reviews();
                    $userTable = new UserTable();
                    $subscriptionsTable = new Hl('Democontentpisubscriptions');

                    if ($subscriptionsTable->obj !== null) {
                        $filter = [
                            '=USER_ACTIVE' => 'Y',
                            '=USER_EXECUTOR' => 1,
                            [
                                'LOGIC' => 'AND',
                                [array_merge(['LOGIC' => 'OR'], $ids)]
                            ]
                        ];

                        $obj = $subscriptionsTable->obj;
                        $get = $obj::getList(
                            [
                                'select' => [
                                    'USER_ID' => 'USER.ID',
                                    'USER_ACTIVE' => 'USER.ACTIVE',
                                    'USER_EXECUTOR' => 'USER.UF_DSPI_EXECUTOR',
                                    'USER_CITY' => 'USER.UF_DSPI_CITY',
                                    'USER_DOCUMENTS' => 'USER.UF_DSPI_DOCUMENTS',
                                    'USER_RATING' => 'USER.UF_DSPI_RATING',
                                ],
                                'runtime' => [
                                    'USER' => [
                                        'data_type' => $userTable::getEntity(),
                                        'reference' => [
                                            '=this.UF_USER_ID' => 'ref.ID'
                                        ],
                                        'join_type' => 'inner'
                                    ]
                                ],
                                'filter' => $filter,
                                'group' => ['USER_ID'],
                                'order' => [
                                    'USER_ID' => 'ASC'
                                ]
                            ]
                        );

                        while ($res = $get->fetch()) {
                            $us->setId(intval($res['USER_ID']));
                            $reviews->setUserId(intval($res['USER_ID']));
                            $cards->setUserId(intval($res['USER_ID']));
                            $profile->setUserId(intval($res['USER_ID']));

                            $res = $us->get();
                            $res['CURRENT_RATING'] = $reviews->rating();
                            $res['PROFILE'] = $profile->get();
                            $res['CARD'] = (count($cards->getUserCard()) > 0) ? 1 : 0;

                            $result[] = $res;
                        }
                    }
                } catch (\Exception $e) {

                }
            }
        }

        return $result;
    }
}
