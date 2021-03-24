<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 08.01.2019
 * Time: 13:21
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Iblock;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\IO\Path;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Democontent2\Pi\Hl;
use Democontent2\Pi\Logger;
use Democontent2\Pi\User;

class Reviews
{
    const TABLE_NAME = 'Democontentpireviews';

    protected $id = 0;
    protected $ttl = 3600;
    protected $userId = 0;
    protected $to = 0;
    protected $taskId = 0;
    protected $iBlockId = 0;
    protected $rating = 5;
    protected $text = '';

    private $class = null;

    /**
     * Reviews constructor.
     */
    public function __construct()
    {
        $hl = new Hl(static::TABLE_NAME);
        if ($hl->obj !== null) {
            $this->class = $hl->obj;
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
                    'UF_TASK_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TASK_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TASK_ID')],
                        ]
                    ],
                    'UF_FROM' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_FROM')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_FROM')],
                        ]
                    ],
                    'UF_TO' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TO')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TO')],
                        ]
                    ],
                    'UF_RATING' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => [
                                'DEFAULT_VALUE' => 0,
                                'MIN_VALUE' => 0,
                                'MAX_VALUE' => 2,
                            ],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_RATING')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_RATING')],
                        ]
                    ],
                    'UF_TEXT' => [
                        'Y',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TEXT')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TEXT')],
                        ]
                    ],
                    'UF_ANSWER' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ANSWER')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ANSWER')],
                        ]
                    ],
                    'UF_TEXT_TIME' => [
                        'Y',
                        'datetime',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TEXT_TIME')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TEXT_TIME')],
                        ]
                    ],
                    'UF_ANSWER_TIME' => [
                        'N',
                        'datetime',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ANSWER_TIME')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ANSWER_TIME')],
                        ]
                    ],
                ],
                [],
                [
                    ['UF_IBLOCK_ID'],
                    ['UF_TASK_ID'],
                    ['UF_FROM'],
                    ['UF_TO'],
                    ['UF_RATING'],
                    ['UF_TEXT_TIME'],
                    ['UF_ANSWER_TIME'],
                    ['UF_IBLOCK_ID', 'UF_TASK_ID', 'UF_FROM', 'UF_TO']
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );
            if ($add) {
                $this->__construct();
            }
        }
    }

    /**
     * @param int $iBlockId
     */
    public function setIBlockId($iBlockId)
    {
        $this->iBlockId = $iBlockId;
    }

    /**
     * @return int
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * @param int $taskId
     */
    public function setTaskId($taskId)
    {
        $this->taskId = $taskId;
    }

    /**
     * @param int $ttl
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @param int $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @param int $rating
     */
    public function setRating($rating)
    {
        switch (intval($rating)) {
            case 0:
            case 1:
            case 2:
                $this->rating = intval($rating);
                break;
            default:
                $this->rating = 1;
        }
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    public function editReview()
    {
        $result = false;

        if ($this->class != null && $this->userId && $this->taskId && $this->iBlockId > 0 && strlen($this->text) > 0) {
            $obj = $this->class;
            try {
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID',
                            'UF_TO'
                        ],
                        'filter' => [
                            '=UF_TASK_ID' => $this->taskId,
                            '=UF_IBLOCK_ID' => $this->iBlockId,
                            '=UF_FROM' => $this->userId
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $update = $obj::update(
                        $res['ID'],
                        [
                            'UF_RATING' => $this->rating,
                            'UF_TEXT' => HTMLToTxt(str_replace("\r\n", '<br>', strip_tags($this->text)))
                        ]
                    );
                    if ($update->isSuccess()) {
                        $result = true;

                        Logger::add(
                            $this->userId,
                            'editReview',
                            [
                                'id' => intval($res['ID'])
                            ]
                        );

                        $us = new User($res['UF_TO']);
                        $userParams = $us->get();
                        if (count($userParams) > 0) {
                            Event::send(
                                [
                                    'EVENT_NAME' => 'DSPI_EDIT_REVIEW',
                                    'LID' => Application::getInstance()->getContext()->getSite(),
                                    'C_FIELDS' => [
                                        'EMAIL_TO' => $userParams['EMAIL'],
                                        'ITEM_ID' => $this->taskId,
                                        'FULL_URL' => ((Application::getInstance()->getContext()->getRequest()->isHttps()) ? 'https://' : 'http://')
                                            . Path::normalize(Application::getInstance()->getContext()->getRequest()->getHttpHost() . SITE_DIR
                                                . 'task' . $this->taskId . '-' . $this->iBlockId) . '/'
                                    ]
                                ]
                            );
                        }

                        $this->userId = intval($res['UF_TO']);
                        $this->ttl = 0;
                        $currentRating = $this->rating();
                        $us = new User(intval($res['UF_TO']));
                        $us->get();
                        $us->updateCurrentRating($currentRating['percent']);
                    }
                }
            } catch (\Exception $e) {
            }
        }

        return $result;
    }

    public function editAnswer()
    {
        $result = false;

        if ($this->class != null && $this->userId && $this->id && strlen($this->text) > 0) {
            $obj = $this->class;
            try {
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID'
                        ],
                        'filter' => [
                            '=ID' => $this->id,
                            '=UF_TO' => $this->userId
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $update = $obj::update(
                        $this->id,
                        [
                            'UF_ANSWER' => HTMLToTxt(str_replace("\r\n", '<br>', strip_tags($this->text)))
                        ]
                    );
                    if ($update->isSuccess()) {
                        $result = true;

                        //TODO уведомление автору отзыва об изменении ответа на него
                    }
                }
            } catch (\Exception $e) {
            }
        }

        return $result;
    }

    public function checkReview()
    {
        $result = true;

        if ($this->class != null && $this->userId && $this->to) {
            $obj = $this->class;
            try {
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID'
                        ],
                        'filter' => [
                            '=UF_FROM' => $this->userId,
                            '=UF_TO' => $this->to
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $result = false;
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    public function add()
    {
        $result = false;

        if ($this->class != null && $this->userId && $this->to && $this->taskId && $this->iBlockId && strlen($this->text) > 0) {
            $obj = $this->class;
            try {
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID'
                        ],
                        'filter' => [
                            '=UF_IBLOCK_ID' => $this->iBlockId,
                            '=UF_TASK_ID' => $this->taskId,
                            '=UF_FROM' => $this->userId,
                            '=UF_TO' => $this->to
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $this->id = intval($res['ID']);
                }

                if (!$this->id) {
                    $add = $obj::add(
                        [
                            'UF_IBLOCK_ID' => $this->iBlockId,
                            'UF_TASK_ID' => $this->taskId,
                            'UF_FROM' => $this->userId,
                            'UF_TO' => $this->to,
                            'UF_RATING' => $this->rating,
                            'UF_TEXT' => HTMLToTxt(str_replace("\r\n", '<br>', strip_tags($this->text))),
                            'UF_TEXT_TIME' => DateTime::createFromTimestamp(time())
                        ]
                    );
                    if ($add->isSuccess() && $add->getId()) {
                        $result = true;

                        $us = new User($this->to);
                        $userParams = $us->get();
                        if (count($userParams) > 0) {
                            Event::send(
                                [
                                    'EVENT_NAME' => 'DSPI_REVIEW_ADD',
                                    'LID' => Application::getInstance()->getContext()->getSite(),
                                    'C_FIELDS' => [
                                        'EMAIL_TO' => $userParams['EMAIL'],
                                        'ITEM_ID' => $this->taskId,
                                        'FULL_URL' => ((Application::getInstance()->getContext()->getRequest()->isHttps()) ? 'https://' : 'http://')
                                            . Path::normalize(Application::getInstance()->getContext()->getRequest()->getHttpHost() . SITE_DIR
                                                . 'task' . $this->taskId . '-' . $this->iBlockId) . '/'
                                    ]
                                ]
                            );
                        }

                        Logger::add(
                            $this->userId,
                            'addReview',
                            [
                                'id' => $add->getId(),
                                'taskId' => $this->taskId,
                                'from' => $this->userId,
                                'to' => $this->to,
                                'rating' => $this->rating,
                                'text' => HTMLToTxt(str_replace("\r\n", '<br>', strip_tags($this->text)))
                            ]
                        );

                        Application::getInstance()->getTaggedCache()->clearByTag('userRating_' . $this->to);

                        $this->userId = $this->to;
                        $this->ttl = 0;
                        $currentRating = $this->rating();
                        $us = new User($this->to);
                        $us->get();
                        $us->updateCurrentRating($currentRating['percent']);
                    }
                }
            } catch (ObjectPropertyException $e) {
            } catch (ArgumentException $e) {
            } catch (SystemException $e) {
            } catch (\Exception $e) {
            }
        }

        return $result;
    }

    public function answer()
    {
        $result = false;

        if ($this->class != null && $this->userId && $this->id && strlen($this->text) > 0) {
            $obj = $this->class;
            try {
                $get = $obj::getList(
                    [
                        'select' => [
                            'UF_FROM'
                        ],
                        'filter' => [
                            '=ID' => $this->id,
                            '=UF_TO' => $this->userId,
                            'UF_ANSWER_TIME' => false
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $update = $obj::update(
                        $this->id,
                        [
                            'UF_ANSWER' => HTMLToTxt(str_replace("\r\n", '<br>', strip_tags($this->text))),
                            'UF_ANSWER_TIME' => DateTime::createFromTimestamp(time())
                        ]
                    );
                    if ($update->isSuccess()) {
                        $result = true;

                        //TODO уведомление автору об ответе на отзыв, ID: $res['UF_FROM']

                        Logger::add(
                            $this->userId,
                            'answerToReview',
                            [
                                'id' => $this->id,
                                'text' => HTMLToTxt(str_replace("\r\n", '<br>', strip_tags($this->text)))
                            ]
                        );
                    }
                }
            } catch (ObjectPropertyException $e) {
            } catch (ArgumentException $e) {
            } catch (SystemException $e) {
            } catch (\Exception $e) {
            }
        }

        return $result;
    }

    public function rating()
    {
        $result = [
            'positive' => 0,
            'negative' => 0,
            'neutral' => 0,
            'percent' => 0.0
        ];

        if ($this->class != null && $this->userId) {
            $cache = Application::getInstance()->getCache();
            $cache_time = $this->ttl;
            $cache_id = md5('userRating_' . $this->userId);
            $cache_path = '/' . DSPI . '/ratings';

            $taggedCache = Application::getInstance()->getTaggedCache();

            if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
                $res = $cache->getVars();

                if ($res[$cache_id] > 0) {
                    $result = $res[$cache_id];
                }
            } else {
                $taggedCache->startTagCache($cache_path);
                $taggedCache->registerTag('userRating_' . $this->userId);

                $rating = [];

                $obj = $this->class;
                try {
                    $get = $obj::getList(
                        [
                            'select' => [
                                'UF_RATING',
                                'CNT'
                            ],
                            'runtime' => [
                                new ExpressionField('CNT', 'COUNT(`ID`)')
                            ],
                            'filter' => [
                                '=UF_TO' => $this->userId
                            ],
                            'group' => [
                                'UF_RATING'
                            ]
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $rating[$res['UF_RATING']] = $res['CNT'];
                    }
                } catch (\Exception $e) {

                }

                $positive = 0;
                $negative = 0;
                $neutral = 0;

                foreach ($rating as $k => $v) {
                    switch (intval($k)) {
                        case 0:
                            $negative = intval($v);
                            break;
                        case 1:
                            $positive = intval($v);
                            break;
                        case 2:
                            $neutral = intval($v);
                            break;
                    }
                }

                if ($positive > 0) {
                    if (!$negative) {
                        $result['percent'] = 100;
                    } else {
                        $result['percent'] = round((($positive / ($positive + $negative)) * 5) * 20, 2);
                    }
                }

                $result['positive'] = $positive;
                $result['negative'] = $negative;
                $result['neutral'] = $neutral;

                if ($cache_time > 0) {
                    $cache->startDataCache($cache_time, $cache_id, $cache_path);
                    /*if (!$result) {
                        $cache->abortDataCache();
                        $taggedCache->abortTagCache();
                    }*/
                    $cache->endDataCache([$cache_id => $result]);
                    $taggedCache->endTagCache();
                }
            }
        }

        return $result;
    }

    public function getCountByUser()
    {
        $result = 0;

        if ($this->class != null && $this->userId) {
            $cache = Application::getInstance()->getCache();
            $cache_time = $this->ttl;
            $cache_id = md5('userReviewsCount_' . $this->userId);
            $cache_path = '/' . DSPI . '/ratings';

            $taggedCache = Application::getInstance()->getTaggedCache();

            if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
                $res = $cache->getVars();

                if ($res[$cache_id] > 0) {
                    $result = $res[$cache_id];
                }
            } else {
                $taggedCache->startTagCache($cache_path);
                $taggedCache->registerTag('userRating_' . $this->userId);

                $obj = $this->class;

                try {
                    $get = $obj::getList(
                        [
                            'select' => [
                                'CNT'
                            ],
                            'runtime' => [
                                new ExpressionField('CNT', 'COUNT(`ID`)')
                            ],
                            'filter' => [
                                '=UF_TO' => $this->userId
                            ]
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $result = intval($res['CNT']);
                    }
                } catch (\Exception $e) {

                }

                if ($cache_time > 0) {
                    $cache->startDataCache($cache_time, $cache_id, $cache_path);
                    if (!$result) {
                        $cache->abortDataCache();
                        $taggedCache->abortTagCache();
                    }
                    $cache->endDataCache([$cache_id => $result]);
                    $taggedCache->endTagCache();
                }
            }
        }

        return $result;
    }

    public function getListByUser(string $type = '')
    {
        $result = [];

        if ($this->class != null && $this->userId) {
            $obj = $this->class;

            $filterByType = null;
            switch ($type) {
                case 'negative':
                    $filterByType = 0;
                    break;
                case 'positive':
                    $filterByType = 1;
                    break;
                case 'neutral':
                    $filterByType = 2;
                    break;
            }

            try {
                $filter = [
                    '=UF_TO' => $this->userId
                ];

                if (!is_null($filterByType)) {
                    $filter['=UF_RATING'] = $filterByType;
                }

                $userTable = new UserTable();
                $get = $obj::getList(
                    [
                        'select' => [
                            '*',
                            'USER_FROM_NAME' => 'USER_FROM.NAME',
                            'USER_FROM_LAST_NAME' => 'USER_FROM.LAST_NAME',
                            'USER_FROM_PHOTO' => 'USER_FROM.PERSONAL_PHOTO'
                        ],
                        'runtime' => [
                            'USER_FROM' => [
                                'data_type' => $userTable::getEntity(),
                                'reference' => [
                                    '=this.UF_FROM' => 'ref.ID'
                                ],
                                'join_type' => 'inner'
                            ]
                        ],
                        'filter' => $filter
                    ]
                );
                while ($res = $get->fetch()) {
                    $result[] = $res;
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    public function getListByTask()
    {
        $result = [];

        if ($this->class != null && $this->taskId) {
            $obj = $this->class;

            try {
                $userTable = new UserTable();
                $get = $obj::getList(
                    [
                        'select' => [
                            '*',
                            'USER_FROM_NAME' => 'USER_FROM.NAME',
                            'USER_FROM_LAST_NAME' => 'USER_FROM.LAST_NAME',
                            'USER_FROM_PHOTO' => 'USER_FROM.PERSONAL_PHOTO',
                            'USER_TO_NAME' => 'USER_TO.NAME',
                            'USER_TO_LAST_NAME' => 'USER_TO.LAST_NAME',
                            'USER_TO_PHOTO' => 'USER_TO.PERSONAL_PHOTO',
                        ],
                        'runtime' => [
                            'USER_FROM' => [
                                'data_type' => $userTable::getEntity(),
                                'reference' => [
                                    '=this.UF_FROM' => 'ref.ID'
                                ],
                                'join_type' => 'inner'
                            ],
                            'USER_TO' => [
                                'data_type' => $userTable::getEntity(),
                                'reference' => [
                                    '=this.UF_TO' => 'ref.ID'
                                ],
                                'join_type' => 'inner'
                            ]
                        ],
                        'filter' => [
                            '=UF_TASK_ID' => $this->taskId
                        ]
                    ]
                );
                while ($res = $get->fetch()) {
                    $result[] = $res;
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    public function getList()
    {
        $result = [];

        if ($this->class != null && $this->userId) {
            $obj = $this->class;
            try {
                $userTable = new UserTable();
                $get = $obj::getList(
                    [
                        'select' => [
                            '*',
                            'USER_FROM_NAME' => 'USER_FROM.NAME',
                            'USER_FROM_LAST_NAME' => 'USER_FROM.LAST_NAME',
                            'USER_FROM_PHOTO' => 'USER_FROM.PERSONAL_PHOTO',
                            'USER_TO_NAME' => 'USER_TO.NAME',
                            'USER_TO_LAST_NAME' => 'USER_TO.LAST_NAME',
                            'USER_TO_PHOTO' => 'USER_TO.PERSONAL_PHOTO',
                        ],
                        'runtime' => [
                            'USER_FROM' => [
                                'data_type' => $userTable::getEntity(),
                                'reference' => [
                                    '=this.UF_FROM' => 'ref.ID'
                                ],
                                'join_type' => 'inner'
                            ],
                            'USER_TO' => [
                                'data_type' => $userTable::getEntity(),
                                'reference' => [
                                    '=this.UF_TO' => 'ref.ID'
                                ],
                                'join_type' => 'inner'
                            ]
                        ],
                        'filter' => [
                            'LOGIC' => 'OR',
                            [
                                '=UF_FROM' => $this->userId
                            ],
                            [
                                '=UF_TO' => $this->userId
                            ]
                        ],
                        'order' => [
                            'UF_TEXT_TIME' => 'DESC'
                        ]
                    ]
                );
                while ($res = $get->fetch()) {
                    $result[] = $res;
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }
}