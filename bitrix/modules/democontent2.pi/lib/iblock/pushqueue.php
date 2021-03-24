<?php
/**
 * Date: 07.11.2019
 * Time: 19:42
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

namespace Democontent2\Pi\Iblock;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\IO\InvalidPathException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Democontent2\Pi\FireBase;
use Democontent2\Pi\Hl;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;

class PushQueue
{
    const TABLE_NAME = 'Democontentpipushqueue';
    const SUBSCRIPTIONS_TABLE_NAME = 'Democontentpisubscriptions';

    private $obj = null;

    /**
     * PushQueue constructor.
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
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                        ]
                    ],
                    'UF_ITEM_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ITEM_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ITEM_ID')],
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
                ],
                [],
                [
                    ['UF_CREATED_AT'],
                    ['UF_ITEM_ID'],
                    ['UF_IBLOCK_ID']
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );

            if ($add) {
                $this->__construct();
            }
        }
    }

    public function put(int $itemId, int $iBlockId)
    {
        if (!is_null($this->obj) && $itemId > 0 && $iBlockId > 0) {
            $obj = $this->obj;
            try {
                $obj::add(
                    [
                        'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                        'UF_ITEM_ID' => intval($itemId),
                        'UF_IBLOCK_ID' => intval($iBlockId)
                    ]
                );
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function send()
    {
        switch (date('H')) {
            case '00':
            case '01':
            case '02':
            case '03':
            case '04':
            case '05':
            case '06':
            case '20':
            case '21':
            case '22':
            case '23':
            case '24':
                //return;
                break;
        }

        if (!is_null($this->obj)) {
            $queue = [];
            $obj = $this->obj;
            try {
                $get = $obj::getList(['select' => ['*']]);
                while ($res = $get->fetch()) {
                    $queue[] = $res;
                    $obj::delete($res['ID']);
                }
            } catch (ObjectPropertyException $e) {
            } catch (ArgumentException $e) {
            } catch (SystemException $e) {
            } catch (\Exception $e) {
            }

            if (count($queue)) {
                $hl = new Hl('Democontentpiall');
                if ($hl->obj !== null) {
                    $obj = $hl->obj;
                    $push = [];
                    $users = [];
                    $tasks = [];

                    foreach ($queue as $item) {
                        try {
                            $filter = [
                                '=UF_ITEM_ID' => intval($item['UF_ITEM_ID']),
                                '=UF_IBLOCK_ID' => intval($item['UF_IBLOCK_ID']),
                                '=UF_MODERATION' => 0,
                                '=UF_STATUS' => 1
                            ];

                            $get = $obj::getList(
                                [
                                    'select' => [
                                        'UF_ITEM_ID',
                                        'UF_IBLOCK_TYPE',
                                        'UF_IBLOCK_CODE',
                                        'UF_IBLOCK_ID',
                                        'UF_CODE',
                                        'UF_NAME',
                                        'UF_PRICE',
                                        'UF_DESCRIPTION',
                                        'UF_SAFE',
                                        'UF_CREATED_AT',
                                    ],
                                    'filter' => $filter,
                                    'order' => [
                                        'ID' => 'ASC'
                                    ],
                                    'limit' => 1
                                ]
                            );
                            while ($res = $get->fetch()) {
                                $tasks[$res['UF_IBLOCK_TYPE']][$res['UF_IBLOCK_ID']][$res['UF_ITEM_ID']] = $res;
                            }
                        } catch (\Exception $e) {
                        }
                    }

                    if (count($tasks) > 0) {
                        $subscriptions = new Hl(static::SUBSCRIPTIONS_TABLE_NAME);
                        if ($subscriptions->obj !== null) {
                            $userTable = new UserTable();
                            $obj_ = $subscriptions->obj;

                            foreach ($tasks as $iBlockType => $iBlock) {
                                foreach ($iBlock as $iBlockId => $iBlockTasks) {
                                    $getSubscribers = $obj_::getList(
                                        [
                                            'select' => [
                                                'UF_USER_ID',
                                                'USER_EMAIL' => 'USER.EMAIL',
                                                'USER_NAME' => 'USER.NAME',
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
                                            'filter' => [
                                                '=UF_IBLOCK_TYPE' => $iBlockType,
                                                '=UF_IBLOCK_ID' => $iBlockId,
                                                [
                                                    'LOGIC' => 'AND',
                                                    [
                                                        'LOGIC' => 'OR',
                                                        [
                                                            '=UF_PAID_TO' => false
                                                        ],
                                                        [
                                                            '>=UF_PAID_TO' => DateTime::createFromTimestamp((time() - 60))
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    );
                                    while ($getSubscribersRes = $getSubscribers->fetch()) {
                                        $users[$getSubscribersRes['UF_USER_ID']]['NAME'] = $getSubscribersRes['USER_NAME'];
                                        $users[$getSubscribersRes['UF_USER_ID']]['EMAIL'] = $getSubscribersRes['USER_EMAIL'];
                                        foreach ($iBlockTasks as $k => $v) {
                                            $users[$getSubscribersRes['UF_USER_ID']]['TASKS'][$k] = $v;
                                            $users[$getSubscribersRes['UF_USER_ID']]['TASKS'][$k]['PARAMS'] = [
                                                'UF_NAME' => $v['UF_NAME'],
                                                'UF_IBLOCK_TYPE' => $v['UF_IBLOCK_TYPE'],
                                                'UF_IBLOCK_CODE' => $v['UF_IBLOCK_CODE'],
                                                'UF_CODE' => str_replace('-' . $v['UF_ITEM_ID'], '', $v['UF_CODE']),
                                                'UF_ITEM_ID' => $v['UF_ITEM_ID'],
                                            ];
                                        }
                                    }
                                }
                            }

                            unset($userTable, $obj_, $subscriptions);
                        }

                        if (count($users) > 0) {
                            foreach ($users as $userId => $userData) {
                                foreach ($userData['TASKS'] as $task) {
                                    if (!isset($push[$task['UF_ITEM_ID']])) {
                                        $push[$task['UF_ITEM_ID']] = [];
                                    }

                                    foreach ($task['PARAMS'] as $pK => $pV) {
                                        $push[$task['UF_ITEM_ID']][$pK] = $pV;
                                    }

                                    $push[$task['UF_ITEM_ID']]['USERS'][$userId] = $userId;
                                }
                            }

                            if (count($push)) {
                                try {
                                    $firebase = new FireBase(0);
                                    $firebase->sendNewTasks($push);
                                    unset($firebase);
                                } catch (ArgumentNullException $e) {
                                } catch (ArgumentOutOfRangeException $e) {
                                } catch (MessagingException $e) {
                                } catch (FirebaseException $e) {
                                } catch (InvalidPathException $e) {
                                }
                            }
                        }

                        unset($tasks, $users, $push);
                    }
                }
            }

            unset($queue);
        }
    }
}
