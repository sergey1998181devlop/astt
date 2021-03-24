<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 17.02.2019
 * Time: 17:01
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Iblock;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\IO\Path;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Democontent2\Pi\FireBase;
use Democontent2\Pi\Hl;
use Democontent2\Pi\Logger;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;

class SendNotificationsForSubscribers
{
    const SUBSCRIPTIONS_TABLE_NAME = 'Democontentpisubscriptions';

    private $minutes = 15;

    /**
     * SendNotificationsForSubscribers constructor.
     * @param int $minutes
     */
    public function __construct($minutes)
    {
        if (intval($minutes)) {
            $this->minutes = intval($minutes);
        }
    }

    public function send()
    {
        if ($this->minutes) {
            $hl = new Hl('Democontentpiall');
            if ($hl->obj !== null) {
                try {
                    $push = [];
                    $users = [];
                    $tasks = [];
                    $filter = [
                        '>=UF_CREATED_AT' => DateTime::createFromTimestamp(time() - (60 * $this->minutes)),
                        '=UF_MODERATION' => 0,
                        '=UF_STATUS' => 1
                    ];

                    $filter[] = [
                        'LOGIC' => 'AND',
                        [
                            'LOGIC' => 'OR',
                            [
                                '=UF_QUICKLY_END' => false
                            ],
                            [
                                '>=UF_QUICKLY_END' => DateTime::createFromTimestamp(time() - (60 * $this->minutes))
                            ]
                        ]
                    ];

                    $obj = $hl->obj;
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
                            ]
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $tasks[$res['UF_IBLOCK_TYPE']][$res['UF_IBLOCK_ID']][$res['UF_ITEM_ID']] = $res;
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
                                                            '>=UF_PAID_TO' => DateTime::createFromTimestamp(time() - (60 * $this->minutes))
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
                                                'UF_CODE' => $v['UF_CODE'],
                                                'UF_ITEM_ID' => $v['UF_ITEM_ID'],
                                            ];
                                        }
                                    }
                                }
                            }

                            unset($userTable, $obj_, $subscriptions);
                        }


                        if (count($users) > 0) {
                            $site = \CSite::GetByID(Option::get(DSPI, 'siteId'))->Fetch();
                            if (isset($site['SERVER_NAME']) && strlen($site['SERVER_NAME'])) {
                                foreach ($users as $userId => $userData) {
                                    $links = [];
                                    foreach ($userData['TASKS'] as $task) {
                                        $links[] = '<a href="http://'
                                            . Path::normalize($site['SERVER_NAME']
                                                . $site['DIR'] . '/task' . $task['UF_ITEM_ID'] . '-' . $task['UF_IBLOCK_ID']) . '/" target="_blank">'
                                            . '#' . $task['UF_ITEM_ID'] . ' - ' . $task['UF_NAME'] . '</a>';

                                        if (!isset($push[$task['UF_ITEM_ID']])) {
                                            $push[$task['UF_ITEM_ID']] = [];
                                        }

                                        foreach ($task['PARAMS'] as $pK => $pV) {
                                            $push[$task['UF_ITEM_ID']][$pK] = $pV;
                                        }

                                        $push[$task['UF_ITEM_ID']]['USERS'][$userId] = $userId;
                                    }

                                    Event::send(
                                        [
                                            'EVENT_NAME' => 'DSPI_SUBSCRIBE_SEND',
                                            'LID' => $site['LID'],
                                            'C_FIELDS' => [
                                                'NAME' => $userData['NAME'],
                                                'EMAIL_TO' => $userData['EMAIL'],
                                                'TASKS_LIST' => implode('<br>', $links)
                                            ]
                                        ]
                                    );
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
                                    }
                                }
                            } else {
                                Logger::add(
                                    0,
                                    'siteServerUrlEmpty',
                                    []
                                );
                            }
                        }

                        unset($tasks, $users, $push);
                    }
                } catch (\Exception $e) {
                }
            }
        }

        return;
    }
}