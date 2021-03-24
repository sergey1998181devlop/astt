<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 08.01.2019
 * Time: 13:04
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Iblock;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;
use Democontent2\Pi\Hl;

class Stat
{
    const TABLE_NAME = 'Democontentpistat';

    private $ttl = 86400;
    private $userId = 0;
    private $itemId = 0;
    private $hlItemId = 0;
    private $iBlockId = 0;
    private $tablePostfix = 'stat';

    /**
     * Stat constructor.
     * @param int $itemId
     * @param int $iBlockId
     */
    public function __construct($itemId, $iBlockId)
    {
        $this->itemId = intval($itemId);
        $this->iBlockId = intval($iBlockId);
    }

    /**
     * @param int $hlItemId
     */
    public function setHlItemId($hlItemId)
    {
        $this->hlItemId = $hlItemId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @param int $ttl
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;
    }

    /**
     * @param string $tablePostfix
     */
    public function setTablePostfix($tablePostfix)
    {
        if (strlen($tablePostfix) > 0) {
            $this->tablePostfix = $tablePostfix;
        }
    }

    public function remove()
    {
        if ($this->iBlockId > 0 && $this->itemId > 0) {
            $hl = new Hl(static::TABLE_NAME . $this->tablePostfix, $this->ttl);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                try {
                    $get = $obj::getList(
                        [
                            'select' => [
                                'ID'
                            ],
                            'filter' => [
                                '=UF_ITEM_ID' => $this->itemId,
                                '=UF_IBLOCK_ID' => $this->iBlockId
                            ]
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $obj::delete($res['ID']);
                    }
                } catch (\Exception $e) {
                }
            }
        }
    }

    public function total()
    {
        $result = 0;
        if ($this->iBlockId > 0 && $this->itemId > 0) {
            $cache = Application::getInstance()->getCache();
            $cache_time = $this->ttl;
            $cache_id = md5('total_' . $this->itemId . $this->iBlockId);
            $cache_path = '/' . DSPI . '/' . $this->tablePostfix;

            $taggedCache = Application::getInstance()->getTaggedCache();

            if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
                $res = $cache->getVars();

                if (intval($res[$cache_id]) > 0) {
                    $result = $res[$cache_id];
                }
            } else {
                $taggedCache->startTagCache($cache_path);
                $taggedCache->registerTag('iblock_id_' . $this->iBlockId);
                $taggedCache->registerTag('element_id_' . $this->itemId);

                $hl = new Hl(static::TABLE_NAME . $this->tablePostfix, $this->ttl);
                if ($hl->obj !== null) {
                    $obj = $hl->obj;
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
                                    '=UF_ITEM_ID' => $this->itemId,
                                    '=UF_IBLOCK_ID' => $this->iBlockId
                                ]
                            ]
                        );
                        while ($res = $get->fetch()) {
                            $result = intval($res['CNT']);
                        }
                    } catch (ObjectPropertyException $e) {
                    } catch (ArgumentException $e) {
                    } catch (SystemException $e) {
                    }
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

    public function today()
    {
        $result = 0;
        if ($this->iBlockId > 0 && $this->itemId > 0) {
            $cache = Application::getInstance()->getCache();
            $cache_time = $this->ttl;
            $cache_id = md5('today_' . $this->itemId . $this->iBlockId);
            $cache_path = '/' . DSPI . '/' . $this->tablePostfix;

            $taggedCache = Application::getInstance()->getTaggedCache();

            if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
                $res = $cache->getVars();

                if (intval($res[$cache_id]) > 0) {
                    $result = $res[$cache_id];
                }
            } else {
                $taggedCache->startTagCache($cache_path);
                $taggedCache->registerTag('iblock_id_' . $this->iBlockId);
                $taggedCache->registerTag('element_id_' . $this->itemId);

                $hl = new Hl(static::TABLE_NAME . $this->tablePostfix, $this->ttl);
                if ($hl->obj !== null) {
                    $obj = $hl->obj;
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
                                    '=UF_ITEM_ID' => $this->itemId,
                                    '=UF_IBLOCK_ID' => $this->iBlockId,
                                    '>=UF_DATE' => Date::createFromTimestamp(time())
                                ],
                                'group' => [
                                    'UF_DATE'
                                ]
                            ]
                        );
                        while ($res = $get->fetch()) {
                            $result = intval($res['CNT']);
                        }
                    } catch (ObjectPropertyException $e) {
                    } catch (ArgumentException $e) {
                    } catch (SystemException $e) {
                    }
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

    public function month()
    {
        $result = [];
        if ($this->iBlockId > 0 && $this->itemId > 0) {
            $cache = Application::getInstance()->getCache();
            $cache_time = $this->ttl;
            $cache_id = md5('month_' . $this->itemId . $this->iBlockId);
            $cache_path = '/' . DSPI . '/' . $this->tablePostfix;

            $taggedCache = Application::getInstance()->getTaggedCache();

            if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
                $res = $cache->getVars();

                if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                    $result = $res[$cache_id];
                }
            } else {
                $taggedCache->startTagCache($cache_path);
                $taggedCache->registerTag('iblock_id_' . $this->iBlockId);
                $taggedCache->registerTag('element_id_' . $this->itemId);

                $hl = new Hl(static::TABLE_NAME . $this->tablePostfix, $this->ttl);
                if ($hl->obj !== null) {
                    $obj = $hl->obj;
                    try {
                        $get = $obj::getList(
                            [
                                'select' => [
                                    'UF_DATE',
                                    'CNT'
                                ],
                                'runtime' => [
                                    new ExpressionField('CNT', 'COUNT(`ID`)')
                                ],
                                'filter' => [
                                    '=UF_ITEM_ID' => $this->itemId,
                                    '=UF_IBLOCK_ID' => $this->iBlockId,
                                    '>=UF_DATE' => Date::createFromTimestamp((time() - (86400 * 30)))
                                ],
                                'group' => [
                                    'UF_DATE'
                                ],
                                'order' => [
                                    'UF_DATE' => 'ASC'
                                ]
                            ]
                        );
                        while ($res = $get->fetch()) {
                            $result[] = $res;
                        }
                    } catch (ObjectPropertyException $e) {
                    } catch (ArgumentException $e) {
                    } catch (SystemException $e) {
                    }
                }

                if ($cache_time > 0) {
                    $cache->startDataCache($cache_time, $cache_id, $cache_path);
                    if (!count($result)) {
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

    public function set()
    {
        if ($this->iBlockId > 0 && $this->itemId > 0) {
            $className = ToUpper(end(explode('\\', __CLASS__)));
            $hl = new Hl(static::TABLE_NAME . $this->tablePostfix, $this->ttl);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                try {
                    $add = $obj::add(
                        [
                            'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                            'UF_DATE' => Date::createFromTimestamp(time()),
                            'UF_IP' => Application::getInstance()->getContext()->getServer()->get('REMOTE_ADDR'),
                            'UF_USER_ID' => $this->userId,
                            'UF_IBLOCK_ID' => $this->iBlockId,
                            'UF_ITEM_ID' => $this->itemId
                        ]
                    );
                    if ($add->isSuccess()) {
                        if ($this->hlItemId > 0) {
                            $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($this->iBlockId))));
                            $hl_ = new Hl($tableName);
                            if ($hl_->obj !== null) {
                                $obj_ = $hl_->obj;
                                try {
                                    $get = $obj_::getList(
                                        [
                                            'select' => [
                                                'ID',
                                                'UF_COUNTER'
                                            ],
                                            'filter' => [
                                                '=ID' => $this->hlItemId
                                            ]
                                        ]
                                    )->fetch();
                                    if (isset($get['ID'])) {
                                        $update = $obj_::update(
                                            $this->hlItemId,
                                            [
                                                'UF_COUNTER' => (intval($get['UF_COUNTER']) + 1)
                                            ]
                                        );

                                        if ($update->isSuccess()) {
                                            $item = new Item();
                                            $item->setCounter($this->itemId, (intval($get['UF_COUNTER']) + 1));
                                        }
                                    }
                                } catch (\Exception $e) {
                                }
                            }
                        }
                    }
                } catch (SystemException $e) {
                } catch (\Exception $e) {
                }
            } else {
                $add = Hl::create(
                    ToLower(static::TABLE_NAME . $this->tablePostfix),
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
                        'UF_DATE' => [
                            'Y',
                            'date',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DATE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DATE')],
                            ]
                        ],
                        'UF_IP' => [
                            'N',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IP')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IP')],
                            ]
                        ],
                        'UF_USER_ID' => [
                            'N',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
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
                        'UF_ITEM_ID' => [
                            'Y',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ITEM_ID')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ITEM_ID')],
                            ]
                        ],
                    ],
                    [
                        'ALTER TABLE `' . ToLower(static::TABLE_NAME . $this->tablePostfix) . '` MODIFY `UF_IP` VARCHAR(255);'
                    ],
                    [
                        ['UF_CREATED_AT'],
                        ['UF_IP'],
                        ['UF_USER_ID'],
                        ['UF_IBLOCK_ID'],
                        ['UF_ITEM_ID'],
                        ['UF_ITEM_ID', 'UF_IBLOCK_ID'],
                    ],
                    Loc::getMessage(ToUpper($this->tablePostfix) . '_IBLOCK_NAME')
                );
                if ($add) {
                    $this->set();
                }
            }
        }
    }
}