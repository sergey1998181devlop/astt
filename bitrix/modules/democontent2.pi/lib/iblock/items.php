<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.01.2019
 * Time: 10:36
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Iblock;

use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\InheritedProperty\SectionValues;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Democontent2\Pi\Hl;
use Democontent2\Pi\Order;
use Democontent2\Pi\User;
use Democontent2\Pi\Utils;

class Items
{
    const ALL_TABLE_NAME = 'Democontentpiall';

    protected $ttl = 86400;
    protected $userId = 0;
    protected $cityCode = '';
    protected $iBlockType = '';
    protected $iBlockCode = '';
    protected $iBlockId = 0;
    protected $limit = 24;
    protected $offset = 0;
    protected $sectionOne = '';
    protected $sectionTwo = '';
    protected $cityParams = [];
    protected $cityId = 0;
    protected $sectionsParams = [];
    protected $meta = [];
    protected $total = 0;
    protected $withPhotosOnly = 0;
    protected $order = ['ID' => 'DESC'];
    protected $filterProperties = [];
    protected $skipCity = false;
    protected $unsetUserId = false;
    protected $withCoords = false;
    protected $quickly = false;
    protected $noResponses = false;
    protected $min10Responses = false;
    protected $safe = false;
    protected $filteredUserId = 0;
    protected $status = 0;
    protected $allClass = null;

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        switch (intval($status)) {
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
                $this->status = intval($status);
                break;
        }
    }

    /**
     * @return int
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * @param int $cityId
     */
    public function setCityId($cityId)
    {
        $this->cityId = intval($cityId);
    }

    /**
     * @return int
     */
    public function getFilteredUserId(): int
    {
        return $this->filteredUserId;
    }

    /**
     * @param int $filteredUserId
     */
    public function setFilteredUserId(int $filteredUserId): void
    {
        $this->filteredUserId = $filteredUserId;
    }

    /**
     * @param bool $safe
     */
    public function setSafe($safe)
    {
        $this->safe = $safe;
    }

    /**
     * @param bool $noResponses
     */
    public function setNoResponses($noResponses)
    {
        $this->noResponses = $noResponses;
    }

    /**
     * @param bool $min10Responses
     */
    public function setMin10Responses($min10Responses)
    {
        $this->min10Responses = $min10Responses;
    }

    /**
     * @param bool $quickly
     */
    public function setQuickly($quickly)
    {
        $this->quickly = $quickly;
    }

    /**
     * @param bool $withCoords
     */
    public function setWithCoords($withCoords)
    {
        $this->withCoords = $withCoords;
    }

    /**
     * @param bool $unsetUserId
     */
    public function setUnsetUserId($unsetUserId)
    {
        $this->unsetUserId = $unsetUserId;
    }

    /**
     * @param bool $skipCity
     */
    public function setSkipCity($skipCity)
    {
        $this->skipCity = $skipCity;
    }

    /**
     * @param int $withPhotosOnly
     */
    public function setWithPhotosOnly($withPhotosOnly)
    {
        $this->withPhotosOnly = intval($withPhotosOnly);
    }

    /**
     * @return bool
     */
    public function isSkipCity()
    {
        return $this->skipCity;
    }

    /**
     * @param $filterProperties
     * @throws \Bitrix\Main\SystemException
     */
    public function setFilterProperties($filterProperties)
    {
        if ($this->iBlockId > 0) {
            $i = 0;
            $tempFilter = [];
            $props = new Properties($this->iBlockId);
            $filterableProperties = $props->filterable();
            if (count($filterableProperties) > 0) {
                foreach ($filterProperties as $k => $v) {
                    if (!isset($filterableProperties[$k])) {
                        continue;
                    }

                    switch ($filterableProperties[$k]['type']) {
                        case 'integer':
                            if (is_array($v) && isset($v[0]) && isset($v[1])) {
                                $i++;
                                $tempFilter[($i - 1)] = [
                                    'LOGIC' => 'AND'
                                ];

                                if (intval($v[0]) >= $filterableProperties[$k]['min']
                                    && intval($v[0]) <= $filterableProperties[$k]['max']) {
                                    $tempFilter[($i - 1)][] = [
                                        '>=PROPERTY_' . $k => intval($v[0])
                                    ];
                                } else {
                                    $tempFilter[($i - 1)][] = [
                                        '>=PROPERTY_' . $k => 0
                                    ];
                                }

                                if (intval($v[1]) >= $filterableProperties[$k]['min']
                                    && intval($v[1]) <= $filterableProperties[$k]['max']) {
                                    $tempFilter[($i - 1)][] = [
                                        '<=PROPERTY_' . $k => intval($v[1])
                                    ];
                                } else {
                                    $tempFilter[($i - 1)][] = [
                                        '<=PROPERTY_' . $k => $filterableProperties[$k]['max']
                                    ];
                                }
                            }
                            break;
                        case 'list':
                            if (is_array($v) && count($v) > 0) {
                                $i++;
                                $tempFilter[($i - 1)] = [
                                    'LOGIC' => 'AND'
                                ];
                                $tempFilter[($i - 1)][0] = [
                                    'LOGIC' => 'OR'
                                ];
                                foreach ($v as $_v) {
                                    if (intval($_v) > 0) {
                                        $tempFilter[($i - 1)][0][] = [
                                            '=PROPERTY_' . $k => intval($_v)
                                        ];
                                    }
                                }
                            }
                            break;
                    }
                }
            }

            if (count($tempFilter) > 0) {
                $this->filterProperties = [
                    'LOGIC' => 'AND'
                ];
                $this->filterProperties[] = $tempFilter;
            }
        } else {
            if (!$this->iBlockId && $this->iBlockType && $this->iBlockCode) {
                $this->iBlockId = Utils::getIBlockIdByType(
                    str_replace('-', '_', 'democontent2_pi_' . $this->iBlockType), $this->iBlockCode
                );
                if ($this->iBlockId > 0) {
                    $i = 0;
                    $tempFilter = [];
                    $props = new Properties($this->iBlockId);
                    $filterableProperties = $props->filterable();
                    if (count($filterableProperties) > 0) {
                        foreach ($filterProperties as $k => $v) {
                            if (!isset($filterableProperties[$k])) {
                                continue;
                            }

                            switch ($filterableProperties[$k]['type']) {
                                case 'integer':
                                    if (is_array($v) && isset($v[0]) && isset($v[1])) {
                                        $i++;
                                        $tempFilter[($i - 1)] = [
                                            'LOGIC' => 'AND'
                                        ];

                                        if (intval($v[0]) >= $filterableProperties[$k]['min']
                                            && intval($v[0]) <= $filterableProperties[$k]['max']) {
                                            $tempFilter[($i - 1)][] = [
                                                '>=PROPERTY_' . $k => intval($v[0])
                                            ];
                                        } else {
                                            $tempFilter[($i - 1)][] = [
                                                '>=PROPERTY_' . $k => 0
                                            ];
                                        }

                                        if (intval($v[1]) >= $filterableProperties[$k]['min']
                                            && intval($v[1]) <= $filterableProperties[$k]['max']) {
                                            $tempFilter[($i - 1)][] = [
                                                '<=PROPERTY_' . $k => intval($v[1])
                                            ];
                                        } else {
                                            $tempFilter[($i - 1)][] = [
                                                '<=PROPERTY_' . $k => $filterableProperties[$k]['max']
                                            ];
                                        }
                                    }
                                    break;
                                case 'list':
                                    if (is_array($v) && count($v) > 0) {
                                        $i++;
                                        $tempFilter[($i - 1)] = [
                                            'LOGIC' => 'AND'
                                        ];
                                        $tempFilter[($i - 1)][0] = [
                                            'LOGIC' => 'OR'
                                        ];
                                        foreach ($v as $_v) {
                                            if (intval($_v) > 0) {
                                                $tempFilter[($i - 1)][0][] = [
                                                    '=PROPERTY_' . $k => intval($_v)
                                                ];
                                            }
                                        }
                                    }
                                    break;
                            }
                        }
                    }

                    if (count($tempFilter) > 0) {
                        $this->filterProperties = [
                            'LOGIC' => 'AND'
                        ];
                        $this->filterProperties[] = $tempFilter;
                    }
                }
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
     * @param array $cityParams
     */
    public function setCityParams($cityParams)
    {
        $this->cityParams = $cityParams;
    }

    /**
     * @param int $iBlockId
     */
    public function setIBlockId($iBlockId)
    {
        $this->iBlockId = intval($iBlockId);
    }

    /**
     * @param int $ttl
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @return array
     */
    public function getSectionsParams()
    {
        return $this->sectionsParams;
    }

    /**
     * @param int $sectionOne
     */
    public function setSectionOne($sectionOne)
    {
        $this->sectionOne = $sectionOne;
    }

    /**
     * @param int $sectionTwo
     */
    public function setSectionTwo($sectionTwo)
    {
        $this->sectionTwo = $sectionTwo;
    }

    /**
     * @param string $cityCode
     */
    public function setCityCode($cityCode)
    {
        $this->cityCode = $cityCode;
    }

    /**
     * @param string $iBlockType
     */
    public function setIBlockType($iBlockType)
    {
        $this->iBlockType = str_replace('-', '_', $iBlockType);
    }

    /**
     * @param string $iBlockCode
     */
    public function setIBlockCode($iBlockCode)
    {
        $this->iBlockCode = $iBlockCode;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getIBlockId()
    {
        return $this->iBlockId;
    }

    /**
     * @return string
     */
    public function getIBlockType()
    {
        return $this->iBlockType;
    }

    /**
     * @return array
     */
    public function getCityParams()
    {
        return $this->cityParams;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return string
     */
    public function getIBlockCode()
    {
        return $this->iBlockCode;
    }

    /**
     * @param array $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    private function setAllClass()
    {
        if ($this->allClass == null) {
            $hl = new Hl(static::ALL_TABLE_NAME, 0);

            if ($hl->obj !== null) {
                $this->allClass = $hl->obj;
            } else {
                $className = ToUpper(end(explode('\\', __CLASS__)));
                $add = Hl::create(
                    ToLower(static::ALL_TABLE_NAME),
                    [
                        'UF_USER_ID' => [
                            'Y',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
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
                        'UF_IBLOCK_TYPE' => [
                            'Y',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_TYPE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_TYPE')],
                            ]
                        ],
                        'UF_IBLOCK_CODE' => [
                            'Y',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_CODE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_CODE')],
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
                        'UF_CITY' => [
                            'Y',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CITY')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CITY')],
                            ]
                        ],
                        'UF_MODERATION' => [
                            'Y',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_MODERATION')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_MODERATION')],
                            ]
                        ],
                        'UF_CREATED_AT' => [
                            'Y',
                            'datetime',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                            ]
                        ],
                        'UF_BEGIN_WITH' => [
                            'N',
                            'datetime',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_BEGIN_WITH')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_BEGIN_WITH')],
                            ]
                        ],
                        'UF_RUN_UP' => [
                            'N',
                            'datetime',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_RUN_UP')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_RUN_UP')],
                            ]
                        ],
                        'UF_CODE' => [
                            'Y',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CODE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CODE')],
                            ]
                        ],
                        'UF_NAME' => [
                            'Y',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_NAME')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_NAME')],
                            ]
                        ],
                        'UF_PRICE' => [
                            'N',
                            'double',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0, 'PRECISION' => 2],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PRICE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PRICE')],
                            ]
                        ],
                        'UF_DESCRIPTION' => [
                            'Y',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DESCRIPTION')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DESCRIPTION')],
                            ]
                        ],
                        'UF_RESPONSE_COUNT' => [
                            'N',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_RESPONSE_COUNT')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_RESPONSE_COUNT')],
                            ]
                        ],
                        'UF_COUNTER' => [
                            'N',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_COUNTER')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_COUNTER')],
                            ]
                        ],
                        'UF_SAFE' => [
                            'N',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_SAFE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_SAFE')],
                            ]
                        ],
                        'UF_QUICKLY_START' => [
                            'N',
                            'datetime',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_QUICKLY_START')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_QUICKLY_START')],
                            ]
                        ],
                        'UF_QUICKLY_END' => [
                            'N',
                            'datetime',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_QUICKLY_END')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_QUICKLY_END')],
                            ]
                        ],
                    ],
                    [
                        'ALTER TABLE `' . ToLower(static::ALL_TABLE_NAME) . '` MODIFY `UF_IBLOCK_TYPE` VARCHAR(255);',
                        'ALTER TABLE `' . ToLower(static::ALL_TABLE_NAME) . '` MODIFY `UF_IBLOCK_CODE` VARCHAR(255);',
                        'ALTER TABLE `' . ToLower(static::ALL_TABLE_NAME) . '` MODIFY `UF_CODE` VARCHAR(255);',
                        'ALTER TABLE `' . ToLower(static::ALL_TABLE_NAME) . '` MODIFY `UF_NAME` VARCHAR(255);',
                        'ALTER TABLE `' . ToLower(static::ALL_TABLE_NAME) . '` MODIFY `UF_DESCRIPTION` LONGTEXT;',
                    ],
                    [
                        ['UF_CITY'],
                        ['UF_CREATED_AT'],
                        ['UF_PRICE'],
                        ['UF_RESPONSE_COUNT'],
                        ['UF_BEGIN_WITH'],
                        ['UF_RUN_UP'],
                        ['UF_MODERATION'],
                        ['UF_RESPONSE_COUNT'],
                        ['UF_COUNTER'],
                        ['UF_SAFE'],
                        ['UF_QUICKLY_END'],
                    ],
                    Loc::getMessage($className . '_ALL_IBLOCK_NAME')
                );

                if ($add) {
                    $hl = new Hl(static::ALL_TABLE_NAME);
                    if ($hl->obj !== null) {
                        $this->allClass = $hl->obj;
                    }
                }
            }
        }
    }

    public function getByIblockType($cityId = 0)
    {
        $result = [];

        $hl = new Hl('Democontentpiall');
        if ($hl->obj !== null) {
            $city = new City();
            $cities = $city->getList();

            try {
                $filter = [
                    '=UF_IBLOCK_TYPE' => str_replace('_', '-', $this->iBlockType)
                ];
                if (!$this->filteredUserId) {
                    $filter = [
                        '=UF_IBLOCK_TYPE' => str_replace('_', '-', $this->iBlockType),
                        '=UF_MODERATION' => 0
                    ];
                }

                if ($cityId > 0 && !$this->skipCity) {
                    $filter['=UF_CITY'] = $cityId;
                } else {
                    if ($this->cityId > 0 && !$this->skipCity) {
                        $filter['=UF_CITY'] = $this->cityId;
                    }
                }

                if ($this->noResponses) {
                    $filter['=UF_RESPONSE_COUNT'] = 0;
                }

                if ($this->safe) {
                    $filter['=UF_SAFE'] = 1;
                }

                if ($this->min10Responses) {
                    $filter['<UF_RESPONSE_COUNT'] = 10;
                }

                if ($this->quickly) {
                    $filter['!UF_QUICKLY_END'] = false;
                    $filter['>=UF_QUICKLY_END'] = DateTime::createFromTimestamp(time());
                }

                if ($this->filteredUserId > 0) {
                    $filter['=UF_USER_ID'] = $this->filteredUserId;

                    if ($this->status > 0) {
                        $filter['=UF_STATUS'] = $this->status;
                    }
                } else {

                    $filter[] = [
                        'LOGIC' => 'AND',
                        [
                            'LOGIC' => 'OR',
                            [
                                '=UF_STATUS' => 1
                            ],
                            [
                                '=UF_STATUS' => 4
                            ]
                        ]
                    ];
                }

                $obj = $hl->obj;

                $total = $obj::getList(
                    [
                        'select' => [
                            'CNT'
                        ],
                        'runtime' => [
                            new ExpressionField('CNT', 'COUNT(`ID`)')
                        ],
                        'filter' => $filter
                    ]
                )->fetch();

                $this->total = intval($total['CNT']);

                $get = $obj::getList(
                    [
                        'select' => [
                            '*'
                        ],
                        'filter' => $filter,
                        'limit' => $this->limit,
                        'offset' => $this->offset,
                        'order' => $this->order
                    ]
                );

                while ($res = $get->fetch()) {
                    $result[] = $res;
                }
            } catch (\Exception $e) {

            }
        }

        if (count($result) > 0) {
            $reviews = new Reviews();
            $us = new User(0);
            foreach ($result as $k => $item) {
                $us->setId(intval($item['UF_USER_ID']));
                $reviews->setUserId(intval($item['UF_USER_ID']));

                $result[$k]['USER'] = $us->get();
                $result[$k]['RATING'] = $reviews->rating();
                $result[$k]['REVIEWS_COUNT'] = $reviews->getCountByUser();

                $stat = new Stat(intval($item['UF_ITEM_ID']), intval($item['UF_IBLOCK_ID']));

                $result[$k]['UF_COUNTER'] = $stat->total();

                $result[$k]['CITY_NAME'] = '';
                if (isset($cities[$result[$k]['UF_CITY']])) {
                    $result[$k]['CITY_NAME'] = $cities[$result[$k]['UF_CITY']]['name'];
                }

                unset($stat);
            }
        }

        return $result;
    }
    public function allTasksWithIdList($cityId = 0 , $listId = false)
    {
        $result = [];

        $hl = new Hl('Democontentpiall');
        if ($hl->obj !== null) {
            $city = new City();
            $cities = $city->getList();

            try {
                $filter = [];
                if (!$this->filteredUserId) {
                    $filter = [
                      
                        '=UF_ITEM_ID' => $listId['UF_ITEM_ID'],
//                        '=UF_USER_ID' => $listId['UF_USER_ID'],
                    ];
                }





                $obj = $hl->obj;

                $total = $obj::getList(
                    [
                        'select' => ['CNT'],
                        'runtime' => [
                            new ExpressionField('CNT', 'COUNT(`ID`)')
                        ],
                        'filter' => $filter
                    ]
                )->fetch();

                $this->total = intval($total['CNT']);

                $get = $obj::getList(
                    [
                        'select' => ['*'],
                        'filter' => $filter,
                        'limit' => $this->limit,
                        'offset' => $this->offset,
                        'order' => $this->order
                    ]
                );

                while ($res = $get->fetch()) {
                    $res['CITY_NAME'] = '';
                    if (isset($cities[$res['UF_CITY']])) {
                        $res['CITY_NAME'] = $cities[$res['UF_CITY']]['name'];
                    }

                    $result[] = $res;
                }

            } catch (\Exception $e) {

            }
        }

        if (count($result) > 0) {
            $reviews = new Reviews();
            $us = new User(0);
            foreach ($result as $k => $item) {
                $us->setId(intval($item['UF_USER_ID']));
                $reviews->setUserId(intval($item['UF_USER_ID']));

                $result[$k]['USER'] = $us->get();
                $result[$k]['CURRENT_RATING'] = $reviews->rating();
                $result[$k]['REVIEWS_COUNT'] = $reviews->getCountByUser();

                $stat = new Stat(intval($item['UF_ITEM_ID']), intval($item['UF_IBLOCK_ID']));

                $result[$k]['UF_COUNTER'] = $stat->total();

                unset($stat);
            }
        }

        return $result;
    }
    public function getCities($idIblock){
        $arFilter = array(
            'IBLOCK_ID' => $idIblock, // выборка элементов из инфоблока с ИД равным «5»
            'ACTIVE' => 'Y',  // выборка только активных элементов
        );

        $res = \CIBlockElement::GetList(array(), $arFilter);

        $messEl = [];
        while ($element = $res->GetNext()) {
            // $element['NAME'];
            // и другие свойства элемента
            $messEl[] = $element;
        }
        return $messEl;
    }
    public function  getAllTasks( ){
        global $USER;
        if($_POST['Ajax'] == "isset"){
            global $APPLICATION;
            $APPLICATION->RestartBuffer();
            //чищу буфер для чистого ответа при аяксе и собираю филтр и проставляю ofsset и limit
        }
        $hl = new Hl('Democontentpiall');
        $filter = [];




        if ($USER->IsAuthorized()){
            $idUserCurrent = $USER->GetID();
            $rsUser = \CUser::GetByID($idUserCurrent);
            $arUser = $rsUser->Fetch();
        }else{
            $arUser['UF_DSPI_CITY'] = 1;
        }
        //если есть аякс - значит это фильтр или кнопка "показать еще " - формируем фильтр


        $filter = [
            '=UF_CITY' => (int) $arUser['UF_DSPI_CITY']
        ];
        if($_POST['Ajax'] == "isset"){
            if(!empty($_POST['CityId']) && $_POST['CityId'] !==  "cityEmpty" && $_POST['CityId'] !==  "notcity"){
                $filter = [
                     '=UF_CITY' => (int) $_POST['CityId']
                ];
            }else{
                unset($filter['=UF_CITY']);
            }
            if(!empty($_POST['propLeftFilterDopParams']['CountDayOrder'])){

                //кол-во смен
                if($_POST['propLeftFilterDopParams']['CountDayOrder'] == 1){
//                    UF_COUNT_DAY_SET
                    $filter['=UF_COUNT_DAY_SET'] = (int) 1;
                }
                if($_POST['propLeftFilterDopParams']['CountDayOrder'] == 2){
                    $filter['=UF_COUNT_DAY_SET'] = (int) 2;
                }
                if($_POST['propLeftFilterDopParams']['CountDayOrder'] == "do5"){
                    $filter['<=UF_COUNT_DAY_SET'] = (int) 5;
                }
                if($_POST['propLeftFilterDopParams']['CountDayOrder'] == "more5"){
                    $filter['>UF_COUNT_DAY_SET'] = (int) 5;
                }

//                $filter = [
//                    '=UF_COUNT_DAY_SET' => $_POST['propLeftFilterDopParams']['CityId']
//                ];
            }
            if(!empty($_POST['propLeftFilterDopParams']['Date'])){

                $filter['>=UF_BEGIN_WITH'] = ConvertDateTime($_POST['propLeftFilterDopParams']['Date'], "DD.MM.YYYY")." 00:00:00";
            }

            $runtime = [];
            //КОЛ-Во дней
            if(!empty($_POST['propsLeftFilterCheckbox'][0])){
                foreach ($_POST['propsLeftFilterCheckbox'] as $idProp => $propChecked){
                    if( $propChecked == "priceDogovor"){
                        //договорная стоимость
                        //договорная когда все поля пустые

                        $filter['UF_BEZNAL_NUMBER'] = false;
                        $filter['UF_NALL_NUMBER'] = false;
                        $filter['UF_NDS_NUMBER'] = false;
                    }
                    if( $propChecked == "whithNds"){
                        //c ндс
                        //выбрать все элементы у которых заполнено свойство с ндс
                        $filter['!UF_NDS_NUMBER'] = false;
                    }
                    if( $propChecked == "nalMoney"){
                        //Наличные
                        //когда поле наличных не пустое
                        $filter['!UF_NALL_NUMBER'] = false;
                    }
                    if( $propChecked == "nameCompany"){
                        //Компания
                        //когда поле компании не пустое
                        $filter['!UF_COMPANY_ID'] = false;
                    }
                    // избраное филтруем
                    if( $propChecked == "hotPuts"){
                        //Избраное
                        global $USER;
                        $favouritesTask = new \Democontent2\Pi\FavouritesTask(intval($USER->GetID()));
                        $favouritesTaskList = $favouritesTask->getList();

                        $filter['=UF_ITEM_ID'] = $favouritesTaskList['UF_ITEM_ID'];
                    }
                }

            }

            if(!empty($_POST['popsRightSpecialization'][0])){
//                $filter['!UF_NALL_NUMBER'] = false;
                $filterSpecialization  = [];
                foreach ($_POST['popsRightSpecialization'] as $idSpecialization => $ItemSpecialization){
                    $filterSpecialization[] = $ItemSpecialization;
                }
//                UF_IBLOCK_CODE
                $filter['=UF_IBLOCK_CODE'] = $filterSpecialization;
            }

        }
        if(!empty($_POST['propsRnj'])){
            if($_POST['propsRnj'] == 'new'){
                //Новые
                $this->order['UF_CREATED_AT'] = 'DESC';
                unset($this->order['ID']);
            }
            if($_POST['propsRnj'] == 'old'){
                //Старые
                $this->order['UF_CREATED_AT'] = 'ASC';
                unset($this->order['ID']);
            }
            if($_POST['propsRnj'] == 'priseDown'){
                //Дешевле

            }
            if($_POST['propsRnj'] == 'priseUp'){
                //Дороже

            }
        }
        if(!empty($_POST['offset'])){

            $this->offset  = $_POST['offset'];
        }else{
            $this->offset  = 0;
        }


        if (!$this->filteredUserId) {
            $filter['=UF_MODERATION'] = 0;
        }
        if(empty($_POST['propsRnj'])){
            $this->order['UF_CREATED_AT'] = 'DESC';
            unset($this->order['ID']);
        }

        $obj = $hl->obj;

        $limit = 5;
        if(!empty($_POST['limitMap'])){
            if($_POST['limitMap'] == "LIST"){
                if($_POST['currentlimit'] == "all"){
                    $limit = $limit;
                }else{
                    $newlimit = (integer)$_POST['currentlimit'];
                    $limit = $newlimit;
                }
            }
            if($_POST['limitMap'] == "MAP"){
                $limit = '';
            }
        }
        if(!empty($_POST['limitMainScreen'])){
            $limit = $_POST['limitMainScreen'];
        }


        $get = $obj::getList(
            [
                'select' => ['*'],
                'filter' => $filter,
                'limit' => $limit,
                'offset' => $this->offset,
                'order' => $this->order,
                'runtime' => $runtime
            ]
        );
        while ($res = $get->fetch()) {
            $res['CITY_NAME'] = '';
            if (isset($cities[$res['UF_CITY']])) {
                $res['CITY_NAME'] = $cities[$res['UF_CITY']]['name'];
            }

            $result[] = $res;
        }

        if (count($result) > 0) {
            $reviews = new Reviews();
            $us = new User(0);
            foreach ($result as $k => $item) {
                $us->setId(intval($item['UF_USER_ID']));
                $reviews->setUserId(intval($item['UF_USER_ID']));

                $result[$k]['USER'] = $us->get();
                $result[$k]['CURRENT_RATING'] = $reviews->rating();
                $result[$k]['REVIEWS_COUNT'] = $reviews->getCountByUser();

                $stat = new Stat(intval($item['UF_ITEM_ID']), intval($item['UF_IBLOCK_ID']));

                $result[$k]['UF_COUNTER'] = $stat->total();

                unset($stat);
            }
        }


        return $result;
        if($_POST['Ajax'] == "isset"){
           die();
        }
    }
    public function  getAllTasksFilter(){
        $hl = new Hl('Democontentpiall');
        $filter = [];
        if (!$this->filteredUserId) {
            $filter = [
                '=UF_MODERATION' => 0
            ];
        }

        $obj = $hl->obj;


        $get = $obj::getList(
            [
                'select' => ['*'],
                'filter' => $filter,
                'limit' => $this->limit,
                'offset' => $this->offset,
                'order' => $this->order
            ]
        );
        while ($res = $get->fetch()) {
            $res['CITY_NAME'] = '';
            if (isset($cities[$res['UF_CITY']])) {
                $res['CITY_NAME'] = $cities[$res['UF_CITY']]['name'];
            }

            $result[] = $res;
        }

        if (count($result) > 0) {
            $reviews = new Reviews();
            $us = new User(0);
            foreach ($result as $k => $item) {
                $us->setId(intval($item['UF_USER_ID']));
                $reviews->setUserId(intval($item['UF_USER_ID']));

                $result[$k]['USER'] = $us->get();
                $result[$k]['CURRENT_RATING'] = $reviews->rating();
                $result[$k]['REVIEWS_COUNT'] = $reviews->getCountByUser();

                $stat = new Stat(intval($item['UF_ITEM_ID']), intval($item['UF_IBLOCK_ID']));

                $result[$k]['UF_COUNTER'] = $stat->total();

                unset($stat);
            }
        }


        return $result;
    }
    public function allTasks($cityId = 0)
    {
        $result = [];

        $hl = new Hl('Democontentpiall');
        if ($hl->obj !== null) {
          /*  $city = new City();
            $cities = $city->getList();
*/
            try {
                $filter = [];
                if (!$this->filteredUserId) {
                    $filter = [
                        '=UF_MODERATION' => 0
                    ];
                }
                /*

                if ($cityId > 0 && !$this->skipCity) {
                    $filter['=UF_CITY'] = $cityId;
                } else {
                    if ($this->cityId > 0 && !$this->skipCity) {
                        $filter['=UF_CITY'] = $this->cityId;
                    }
                }

                if ($this->noResponses) {
                    $filter['=UF_RESPONSE_COUNT'] = 0;
                }

                if ($this->safe) {
                    $filter['=UF_SAFE'] = 1;
                }

                if ($this->min10Responses) {
                    $filter['<UF_RESPONSE_COUNT'] = 10;
                }

                if ($this->quickly) {
                    $filter['!UF_QUICKLY_END'] = false;
                    $filter['>=UF_QUICKLY_END'] = DateTime::createFromTimestamp(time());
                }

                if ($this->filteredUserId > 0) {
                    $filter['=UF_USER_ID'] = $this->filteredUserId;

                    if ($this->status > 0) {
                        $filter['=UF_STATUS'] = $this->status;
                    }
                } else {
                    $filter[] = [
                        'LOGIC' => 'AND',
                        [
                            'LOGIC' => 'OR',
                            [
                                '=UF_STATUS' => 1
                            ],
                            [
                                '=UF_STATUS' => 4
                            ]
                        ]
                    ];

                }*/

                $obj = $hl->obj;

                $total = $obj::getList(
                    [
                        'select' => ['CNT'],
                        'runtime' => [
                            new ExpressionField('CNT', 'COUNT(`ID`)')
                        ],
                        'filter' => $filter
                    ]
                )->fetch();

                $this->total = intval($total['CNT']);
               
                $get = $obj::getList(
                    [
                        'select' => ['*'],
                        'filter' => $filter,
                        'limit' => $this->limit,
                        'offset' => $this->offset,
                        'order' => $this->order
                    ]
                );

                while ($res = $get->fetch()) {
                    $res['CITY_NAME'] = '';
                    if (isset($cities[$res['UF_CITY']])) {
                        $res['CITY_NAME'] = $cities[$res['UF_CITY']]['name'];
                    }

                    $result[] = $res;
                }
            } catch (\Exception $e) {

            }
        }

        if (count($result) > 0) {
            $reviews = new Reviews();
            $us = new User(0);
            foreach ($result as $k => $item) {
                $us->setId(intval($item['UF_USER_ID']));
                $reviews->setUserId(intval($item['UF_USER_ID']));

                $result[$k]['USER'] = $us->get();
                $result[$k]['CURRENT_RATING'] = $reviews->rating();
                $result[$k]['REVIEWS_COUNT'] = $reviews->getCountByUser();

                $stat = new Stat(intval($item['UF_ITEM_ID']), intval($item['UF_IBLOCK_ID']));

                $result[$k]['UF_COUNTER'] = $stat->total();

                unset($stat);
            }
        }

        return $result;
    }

    public function tasksStat()
    {
        $result = [
            'owner' => 0,
            'executor' => 0
        ];

        if ($this->userId > 0) {
            $cache = Application::getInstance()->getCache();
            $cache_time = $this->ttl;
            $cache_id = md5('user_' . $this->userId);
            $cache_path = '/' . DSPI . '/users/tasks/stat';

            $taggedCache = Application::getInstance()->getTaggedCache();

            if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
                $res = $cache->getVars();

                if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                    $result = $res[$cache_id];
                }
            } else {
                $taggedCache->startTagCache($cache_path);
                $taggedCache->registerTag($cache_id);

                $hl = new Hl('Democontentpiresponses');
                $hl__ = new Hl('Democontentpiall');
                if ($hl->obj !== null && $hl__->obj !== null) {
                    $obj = $hl->obj;
                    $obj__ = $hl__->obj;

                    try {
                        $get = $obj::getList(
                            [
                                'select' => [
                                    'CNT'
                                ],
                                'runtime' => [
                                    new ExpressionField('CNT', 'COUNT(`UF_TASK_ID`)'),
                                    'ITEM' => [
                                        'data_type' => $hl__->obj,
                                        'reference' => [
                                            '=this.UF_TASK_ID' => 'ref.UF_ITEM_ID'
                                        ],
                                        'join_type' => 'inner'
                                    ]
                                ],
                                'filter' => [
                                    '=UF_USER_ID' => $this->userId,
                                    '>UF_EXECUTOR' => 0,
                                    '=ITEM.UF_STATUS' => 3
                                ]
                            ]
                        );
                        while ($res = $get->fetch()) {
                            $result['executor'] = intval($res['CNT']);
                        }

                        $get = $obj__::getList(
                            [
                                'select' => [
                                    'CNT'
                                ],
                                'runtime' => [
                                    new ExpressionField('CNT', 'COUNT(`ID`)')
                                ],
                                'filter' => [
                                    '=UF_USER_ID' => $this->userId
                                ]
                            ]
                        );
                        while ($res = $get->fetch()) {
                            $result['owner'] = intval($res['CNT']);
                        }
                    } catch (ObjectPropertyException $e) {
                    } catch (ArgumentException $e) {
                    } catch (SystemException $e) {
                    }
                }

                if ($cache_time > 0) {
                    $cache->startDataCache($cache_time, $cache_id, $cache_path);
                    $cache->endDataCache([$cache_id => $result]);
                    $taggedCache->endTagCache();
                }
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getWhereExecutor(bool $isCompleted = false)
    {
        $result = [];

        if ($this->userId > 0) {
            $hl = new Hl('Democontentpiresponses');
            $hl__ = new Hl('Democontentpiall');
            if ($hl->obj !== null && $hl__->obj !== null) {
                $obj = $hl->obj;
                try {
                    $filter = [
                        '=UF_USER_ID' => $this->userId,
                        '=UF_EXECUTOR' => 1
                    ];

                    if ($isCompleted) {
                        $filter['=UF_STATUS'] = 3;
                    } else {
                        if ($this->status > 0) {
                            $filter['=UF_STATUS'] = $this->status;
                        }
                    }

                    $get = $obj::getList(
                        [
                            'select' => [
                                'UF_IBLOCK_TYPE' => 'ITEM.UF_IBLOCK_TYPE',
                                'UF_IBLOCK_CODE' => 'ITEM.UF_IBLOCK_CODE',
                                'UF_DESCRIPTION' => 'ITEM.UF_DESCRIPTION',
                                'UF_RESPONSE_COUNT' => 'ITEM.UF_RESPONSE_COUNT',
                                'UF_COUNTER' => 'ITEM.UF_COUNTER',
                                'CREATED_AT' => 'ITEM.UF_CREATED_AT',
                                'IBLOCK_ID' => 'ITEM.UF_IBLOCK_ID',
                                'UF_ITEM_ID' => 'ITEM.UF_ITEM_ID',
                                'UF_NAME' => 'ITEM.UF_NAME',
                                'UF_CODE' => 'ITEM.UF_CODE',
                                'UF_STATUS' => 'ITEM.UF_STATUS',
                                'UF_CITY' => 'ITEM.UF_CITY',
                                'UF_PRICE' => 'ITEM.UF_PRICE',
                                'UF_SAFE' => 'ITEM.UF_SAFE',
                                'UF_QUICKLY_END' => 'ITEM.UF_QUICKLY_END',
                                'UF_OWNER_ID' => 'ITEM.UF_USER_ID',
                            ],
                            'runtime' => [
                                'ITEM' => [
                                    'data_type' => $hl__->obj,
                                    'reference' => [
                                        '=this.UF_TASK_ID' => 'ref.UF_ITEM_ID'
                                    ],
                                    'join_type' => 'inner'
                                ]
                            ],
                            'filter' => $filter,
                            'order' => [
                                'UF_ITEM_ID' => 'DESC'
                            ],
                            'limit' => 1000
                        ]
                    );

                    while ($res = $get->fetch()) {
                        $res['UF_CREATED_AT'] = $res['CREATED_AT'];
                        unset($res['CREATED_AT']);
                        $result[] = $res;
                    }
                } catch (ObjectPropertyException $e) {
                } catch (ArgumentException $e) {
                } catch (SystemException $e) {
                }
            }
        }

        return $result;
    }
    public function getByUserModeration($moderation = false)
    {
        $result = [];

        if ($this->userId > 0) {
            $cache = Application::getInstance()->getCache();
            $cache_time = $this->ttl;
            $cache_id = md5('items_' . $this->userId . $moderation);
            $cache_path = '/' . DSPI . '/items';
            $taggedCache = Application::getInstance()->getTaggedCache();

            if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
                $res = $cache->getVars();

                if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                    $result = $res[$cache_id];
                }
            } else {
                $taggedCache->registerTag('items_' . $this->userId);
                $taggedCache->startTagCache($cache_path);


                $hl = new Hl('Democontentpiall');
                if ($hl->obj !== null) {
                    try {
                        $filter = [
                            '=UF_USER_ID' => $this->userId,
                            '=UF_MODERATION' => 0
                        ];




                        $obj = $hl->obj;
                        $get = $obj::getList(
                            [
                                'select' => [
                                    '*'
                                ],
                                'filter' => $filter,
                                'order' => [
                                    'ID' => 'DESC'
                                ],
                                'limit' => 1000
                            ]
                        );

                        while ($res = $get->fetch()) {

                            $result[] = $res;

                        }


                    } catch (\Exception $e) {

                    }
                }
                if ($cache_time > 0) {
                    $cache->startDataCache($cache_time, $cache_id, $cache_path);
                    if (!count($result)) {
                        $cache->abortDataCache();
                        $taggedCache->abortTagCache();
                    }
                    $cache->endDataCache(
                        [
                            $cache_id => $result
                        ]
                    );
                    $taggedCache->endTagCache();
                }
            }
        }

        return $result;
    }
    /**
     * @param bool $moderation
     * @return array
     * @throws SystemException
     */
    public function getByUser($moderation = false)
    {
        $result = [];

        if ($this->userId > 0) {
            $cache = Application::getInstance()->getCache();
            $cache_time = $this->ttl;
            $cache_id = md5('items_' . $this->userId . $moderation);
            $cache_path = '/' . DSPI . '/items';
            $taggedCache = Application::getInstance()->getTaggedCache();

            if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
                $res = $cache->getVars();

                if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                    $result = $res[$cache_id];
                }
            } else {
                $taggedCache->registerTag('items_' . $this->userId);
                $taggedCache->startTagCache($cache_path);


                $hl = new Hl('Democontentpiall');
                if ($hl->obj !== null) {
                    try {
                        $filter = [
                            '=UF_USER_ID' => $this->userId,
                            '=UF_MODERATION' => 0
                        ];




                        $obj = $hl->obj;
                        $get = $obj::getList(
                            [
                                'select' => [
                                    '*'
                                ],
                                'filter' => $filter,
                                'order' => [
                                    'ID' => 'DESC'
                                ],
                                'limit' => 1000
                            ]
                        );

                        while ($res = $get->fetch()) {

                            $result[] = $res;

                        }


                    } catch (\Exception $e) {

                    }
                }
                if ($cache_time > 0) {
                    $cache->startDataCache($cache_time, $cache_id, $cache_path);
                    if (!count($result)) {
                        $cache->abortDataCache();
                        $taggedCache->abortTagCache();
                    }
                    $cache->endDataCache(
                        [
                            $cache_id => $result
                        ]
                    );
                    $taggedCache->endTagCache();
                }
            }
        }

        return $result;
    }
    public function getByUserTasks($moderation = false)
    {
        $result = [];

        if ($this->userId > 0) {
            $cache = Application::getInstance()->getCache();
            $cache_time = $this->ttl;
            $cache_id = md5('items_' . $this->userId . $moderation);
            $cache_path = '/' . DSPI . '/items';
            $taggedCache = Application::getInstance()->getTaggedCache();

            if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
                $res = $cache->getVars();

                if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                    $result = $res[$cache_id];
                }
            } else {
                $taggedCache->registerTag('items_' . $this->userId);
                $taggedCache->startTagCache($cache_path);


                $hl = new Hl('Democontentpiall');
                if ($hl->obj !== null) {
                    try {
                        $filter = [
                            '=UF_USER_ID' => $this->userId,
                        ];




                        $obj = $hl->obj;
                        $get = $obj::getList(
                            [
                                'select' => [
                                    '*'
                                ],
                                'filter' => $filter,
                                'order' => [
                                    'ID' => 'DESC'
                                ],
                                'limit' => 1000
                            ]
                        );

                        while ($res = $get->fetch()) {

                            $result[] = $res;

                        }


                    } catch (\Exception $e) {

                    }
                }
                if ($cache_time > 0) {
                    $cache->startDataCache($cache_time, $cache_id, $cache_path);
                    if (!count($result)) {
                        $cache->abortDataCache();
                        $taggedCache->abortTagCache();
                    }
                    $cache->endDataCache(
                        [
                            $cache_id => $result
                        ]
                    );
                    $taggedCache->endTagCache();
                }
            }
        }

        return $result;
    }

    public function getByUserTasksMessEmpl($moderation = false ,  $messTasksEmployees)
    {
        $result = [];

        if ($this->userId > 0) {
            $cache = Application::getInstance()->getCache();
            $cache_time = $this->ttl;
            $cache_id = md5('items_' . $this->userId . $moderation);
            $cache_path = '/' . DSPI . '/items';
            $taggedCache = Application::getInstance()->getTaggedCache();

            if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
                $res = $cache->getVars();

                if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                    $result = $res[$cache_id];
                }
            } else {
                $taggedCache->registerTag('items_' . $this->userId);
                $taggedCache->startTagCache($cache_path);


                $hl = new Hl('Democontentpiall');
                if ($hl->obj !== null) {
                    try {
                        $filter = [
                            '=UF_USER_ID' => $messTasksEmployees,
                        ];




                        $obj = $hl->obj;
                        $get = $obj::getList(
                            [
                                'select' => [
                                    '*'
                                ],
                                'filter' => $filter,
                                'order' => [
                                    'ID' => 'DESC'
                                ],
                                'limit' => 1000
                            ]
                        );

                        while ($res = $get->fetch()) {

                            $result[] = $res;

                        }


                    } catch (\Exception $e) {

                    }
                }
                if ($cache_time > 0) {
                    $cache->startDataCache($cache_time, $cache_id, $cache_path);
                    if (!count($result)) {
                        $cache->abortDataCache();
                        $taggedCache->abortTagCache();
                    }
                    $cache->endDataCache(
                        [
                            $cache_id => $result
                        ]
                    );
                    $taggedCache->endTagCache();
                }
            }
        }

        return $result;
    }

    public function get()
    {
        $result = [];

        if (!$this->skipCity) {
            $city = new City();
            if ($this->cityCode) {
                $this->cityParams = $city->getByCode($this->cityCode);
            } else {
                if ($this->cityId > 0) {
                    $this->cityParams = $city->getById($this->cityId);
                } else {
                    $this->cityParams = $city->getDefault();
                }
            }
        }

        $cache = Application::getInstance()->getCache();
        $cache_time = $this->ttl;
        $cache_id = md5($this->cityCode . $this->iBlockType . $this->iBlockCode
            . $this->sectionOne . $this->sectionTwo . $this->limit . $this->offset
            . serialize($this->order) . serialize($this->cityParams) . serialize($this->filterProperties)
            . $this->withPhotosOnly . $this->skipCity . $this->iBlockId . $this->unsetUserId . $this->withCoords
            . $this->quickly . $this->safe . $this->noResponses . $this->min10Responses . $this->filteredUserId . $this->status);
        $cache_path = '/' . DSPI . '/items';
        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($this->quickly || $this->safe || $this->noResponses || $this->min10Responses) {
            $cache_time = 3600;
        }

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();

            if (is_array($res[$cache_id]['result']) && (count($res[$cache_id]['result']) > 0)) {
                $result = $res[$cache_id]['result'];
                $this->meta = $res[$cache_id]['meta'];
                $this->total = $res[$cache_id]['total'];
                $this->sectionsParams = $res[$cache_id]['sectionParams'];
                $this->iBlockId = $res[$cache_id]['iBlockId'];
            }
        } else {
            $taggedCache->startTagCache($cache_path);
            $taggedCache->registerTag('iblock_id_' . $this->iBlockId);
            $taggedCache->registerTag('iblock_id_' . $this->iBlockId . '_city_id_' . $this->cityParams['id']);

            if (count($this->filterProperties) > 0) {
                $taggedCache->registerTag('iblock_filter_' . $this->iBlockId);
            }

            if (!$this->iBlockId) {
                $this->iBlockId = Utils::getIBlockIdByType(
                    str_replace('-', '_', 'democontent2_pi_' . $this->iBlockType), $this->iBlockCode
                );
            }

            if ($this->iBlockId) {
                $allow = true;

                if ($allow) {
                    $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($this->iBlockId))));
                    $hl = new Hl($tableName);
                    if ($hl->obj !== null) {
                        $obj = $hl->obj;
                        try {
                            $filter = [
                                '=UF_ACTIVE' => 1,
                                '>UF_PAYED' => 0
                            ];

                            if (!$this->filteredUserId) {
                                $filter = [
                                    '=UF_ACTIVE' => 1,
                                    '=UF_MODERATION' => 0,
                                    '>UF_PAYED' => 0
                                ];
                            }

                            if ($this->noResponses) {
                                $filter['=UF_RESPONSE_COUNT'] = 0;
                            }

                            if ($this->safe) {
                                $filter['=UF_SAFE'] = 1;
                            }

                            if ($this->min10Responses) {
                                $filter['<UF_RESPONSE_COUNT'] = 10;
                            }

                            if ($this->quickly) {
                                $filter['!UF_QUICKLY_END'] = false;
                                $filter['>=UF_QUICKLY_END'] = DateTime::createFromTimestamp(time());
                            }

                            if ($this->withCoords) {
                                $filter['!UF_LAT'] = false;
                                $filter['!UF_LAT'] = 0;

                                $filter['!UF_LONG'] = false;
                                $filter['!UF_LONG'] = 0;
                            }

                            if ($this->iBlockId > 0) {
                                $filter['=UF_IBLOCK_ID'] = $this->iBlockId;
                            } else {
                                $filter['=UF_IBLOCK_TYPE'] = str_replace('_', '-', $this->iBlockType);
                                $filter['=UF_IBLOCK_CODE'] = $this->iBlockCode;
                            }

                            if (!$this->skipCity) {
                                $filter['=UF_CITY'] = $this->cityParams['id'];
                            }

                            if ($this->withPhotosOnly > 0) {
                                $filter['>UF_IMAGE_ID'] = 0;
                            }

                            if ($this->filteredUserId >= 0) {
                                $filter['=UF_USER_ID'] = $this->filteredUserId;

                                if(!empty($this->status))  {
                                    $filter['=UF_STATUS'] = $this->status;
                                }
                            }

                            if ($this->skipCity) {
                                $city = new City();
                                $cities = $city->getList();
                            }

                            $params = [
                                'select' => [
                                    '*'
                                ],
                                'filter' => $filter,
                                'limit' => $this->limit,
                                'offset' => $this->offset,
                                'order' => $this->order
                            ];

                            $get = $obj::getList($params);

                            while ($res = $get->fetch()) {
                                $quickly = 0;

                                if ($res['UF_QUICKLY_END'] && strtotime($res['UF_QUICKLY_END']) > time()) {
                                    $quickly = 1;
                                }

                                $res['QUICKLY'] = $quickly;

                                if (!$this->skipCity) {
                                    $res['URL'] = SITE_DIR . (($this->cityParams['default']) ? '' : 'city-' . $this->cityParams['code'] . '/')
                                        . $res['UF_IBLOCK_TYPE'] . '/' . $res['UF_IBLOCK_CODE'] . '/' . $res['UF_CODE'] . '/';
                                } else {
                                    $res['URL'] = SITE_DIR . (($cities[$res['UF_CITY']]['default']) ? '' : 'city-' . $cities[$res['UF_CITY']]['code'] . '/')
                                        . $res['UF_IBLOCK_TYPE'] . '/' . $res['UF_IBLOCK_CODE'] . '/' . $res['UF_CODE'] . '/';
                                }

                                $res['UF_NAME'] = strip_tags($res['UF_NAME']);
                                $res['UF_DESCRIPTION'] = strip_tags($res['UF_DESCRIPTION']);

                                if ($this->unsetUserId) {
                                    unset($res['UF_USER_ID']);
                                }

                                $result[] = $res;
                            }

                            $get = $obj::getList(
                                [
                                    'select' => [
                                        'CNT'
                                    ],
                                    'runtime' => [
                                        new ExpressionField('CNT', 'COUNT(`ID`)')
                                    ],
                                    'filter' => $filter
                                ]
                            );


                            while ($res = $get->fetch()) {
                                $this->total = intval($res['CNT']);
                            }
                        } catch (\Exception $e) {
                        }
                    }
                }
            }

            if ($cache_time > 0) {
                $cache->startDataCache($cache_time, $cache_id, $cache_path);
                if (!count($result)) {
                    $cache->abortDataCache();
                    $taggedCache->abortTagCache();
                }
                $cache->endDataCache(
                    [
                        $cache_id => [
                            'result' => $result,
                            'meta' => $this->meta,
                            'total' => $this->total,
                            'sectionParams' => $this->sectionsParams,
                            'iBlockId' => $this->iBlockId,
                        ]
                    ]
                );
                $taggedCache->endTagCache();
            }
        }

        return $result;
    }

    public function applyOptions($option, $ids)
    {
        $result = [];
        switch ($option) {
            case 'vip':
            case 'color':
            case 'premium':
            case 'up':
            case 'turbo':
                $sum = 0;
                $cost = intval(Option::get(DSPI, $option . '_option_cost'));
                if ($cost > 0) {
                    foreach ($ids as $id) {
                        $explode = explode('-', $id);
                        $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($explode[0]))));
                        $hl = new Hl($tableName);
                        if ($hl->obj !== null) {
                            $obj = $hl->obj;
                            try {
                                $get = $obj::getList(
                                    [
                                        'select' => [
                                            'ID',
                                            'UF_ID'
                                        ],
                                        'filter' => [
                                            '=UF_ID' => $explode[1],
                                            '=UF_IBLOCK_ID' => $explode[0],
                                            '=UF_USER_ID' => $this->userId,
                                            '=UF_ACTIVE' => 1,
                                            '<=UF_ACTIVE_FROM' => DateTime::createFromTimestamp(time()),
                                            '>=UF_ACTIVE_TO' => DateTime::createFromTimestamp(time()),
                                        ],
                                        'limit' => 1
                                    ]
                                );
                                while ($res = $get->fetch()) {
                                    $result[$explode[0]][$res['UF_ID']] = $res['UF_ID'];
                                    $sum += $cost;
                                }
                            } catch (\Exception $e) {
                            }
                        }
                    }

                    if (count($result) > 0 && $sum > 0) {
                        $order = new Order($this->userId);
                        $order->setType($option);
                        $order->setSum($sum);
                        $order->setDescription(
                            Loc::getMessage(
                                'ITEMS_ORDER_DESCRIPTION',
                                [
                                    '#OPTION_NAME#' => Loc::getMessage('ITEMS_OPTION_' . ToUpper($option))
                                ]
                            )
                        );
                        $order->setAdditionalParams($result);
                        $order->make();
                        if ($order->getRedirect()) {
                            LocalRedirect($order->getRedirect(), true);
                        }
                    }
                }
                break;
            case 'activation':
                $sum = 0;
                $cost = intval(Option::get(DSPI, $option . '_option_cost'));
                if ($cost > 0) {
                    foreach ($ids as $id) {
                        $explode = explode('-', $id);
                        $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($explode[0]))));
                        $hl = new Hl($tableName);
                        if ($hl->obj !== null) {
                            $obj = $hl->obj;
                            try {
                                $filter = [
                                    '=UF_ID' => $explode[1],
                                    '=UF_IBLOCK_ID' => $explode[0],
                                    '=UF_USER_ID' => $this->userId,
                                    [
                                        'LOGIC' => 'OR',
                                        [
                                            'LOGIC' => 'AND',
                                            [
                                                '<=UF_ACTIVE_FROM' => DateTime::createFromTimestamp(time())
                                            ],
                                            [
                                                '<=UF_ACTIVE_TO' => DateTime::createFromTimestamp(time())
                                            ]
                                        ],
                                        [
                                            'LOGIC' => 'AND',
                                            [
                                                '!UF_CANCEL_DATE' => false
                                            ]
                                        ]
                                    ]
                                ];
                                $get = $obj::getList(
                                    [
                                        'select' => [
                                            'ID',
                                            'UF_ID'
                                        ],
                                        'filter' => $filter,
                                        'limit' => 1
                                    ]
                                );
                                while ($res = $get->fetch()) {
                                    $result[$explode[0]][$res['UF_ID']] = $res['UF_ID'];
                                    $sum += $cost;
                                }
                            } catch (\Exception $e) {
                            }
                        }
                    }

                    if (count($result) > 0 && $sum > 0) {
                        $order = new Order($this->userId);
                        $order->setType($option);
                        $order->setSum($sum);
                        $order->setDescription(
                            Loc::getMessage(
                                'ITEMS_ORDER_DESCRIPTION',
                                [
                                    '#OPTION_NAME#' => Loc::getMessage('ITEMS_OPTION_' . ToUpper($option))
                                ]
                            )
                        );
                        $order->setAdditionalParams($result);
                        $order->make();
                        if ($order->getRedirect()) {
                            LocalRedirect($order->getRedirect(), true);
                        }
                    }
                } else {
                    foreach ($ids as $id) {
                        $explode = explode('-', $id);
                        $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($explode[0]))));
                        $hl = new Hl($tableName);
                        if ($hl->obj !== null) {
                            $el = new \CIBlockElement();
                            $obj = $hl->obj;
                            try {
                                $filter = [
                                    '=UF_ID' => $explode[1],
                                    '=UF_IBLOCK_ID' => $explode[0],
                                    '=UF_USER_ID' => $this->userId,
                                    [
                                        'LOGIC' => 'OR',
                                        [
                                            'LOGIC' => 'AND',
                                            [
                                                '<=UF_ACTIVE_FROM' => DateTime::createFromTimestamp(time())
                                            ],
                                            [
                                                '<=UF_ACTIVE_TO' => DateTime::createFromTimestamp(time())
                                            ]
                                        ],
                                        [
                                            'LOGIC' => 'AND',
                                            [
                                                '!UF_CANCEL_DATE' => false
                                            ]
                                        ]
                                    ]
                                ];
                                $get = $obj::getList(
                                    [
                                        'select' => [
                                            'ID',
                                            'UF_IBLOCK_ID',
                                            'UF_ID'
                                        ],
                                        'filter' => $filter,
                                        'limit' => 1
                                    ]
                                );
                                while ($res = $get->fetch()) {
                                    \CIBlockElement::SetPropertyValuesEx(
                                        $res['UF_ID'],
                                        $res['UF_IBLOCK_ID'],
                                        [
                                            '__hidden_cancel_date' => false,
                                            '__hidden_return_date' => DateTime::createFromTimestamp(time())
                                        ]
                                    );
                                    $el->Update(
                                        $res['UF_ID'],
                                        [
                                            'ACTIVE_TO' => DateTime::createFromTimestamp(
                                                time() + (86400 * Option::get(DSPI, 'item_period'))
                                            )
                                        ]
                                    );
                                }
                            } catch (\Exception $e) {
                            }
                        }
                    }
                }
                break;
            case 'activation_up':
                $sum = 0;
                $cost = intval(Option::get(DSPI, $option . '_option_cost'));
                if ($cost > 0) {
                    foreach ($ids as $id) {
                        $explode = explode('-', $id);
                        $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($explode[0]))));
                        $hl = new Hl($tableName);
                        if ($hl->obj !== null) {
                            $obj = $hl->obj;
                            try {
                                $filter = [
                                    '=UF_ID' => $explode[1],
                                    '=UF_IBLOCK_ID' => $explode[0],
                                    '=UF_USER_ID' => $this->userId,
                                    [
                                        'LOGIC' => 'OR',
                                        [
                                            'LOGIC' => 'AND',
                                            [
                                                '<=UF_ACTIVE_FROM' => DateTime::createFromTimestamp(time())
                                            ],
                                            [
                                                '<=UF_ACTIVE_TO' => DateTime::createFromTimestamp(time())
                                            ]
                                        ],
                                        [
                                            'LOGIC' => 'AND',
                                            [
                                                '!UF_CANCEL_DATE' => false
                                            ]
                                        ]
                                    ]
                                ];
                                $get = $obj::getList(
                                    [
                                        'select' => [
                                            'ID',
                                            'UF_ID'
                                        ],
                                        'filter' => $filter,
                                        'limit' => 1
                                    ]
                                );
                                while ($res = $get->fetch()) {
                                    $result[$explode[0]][$res['UF_ID']] = $res['UF_ID'];
                                    $sum += $cost;
                                }
                            } catch (\Exception $e) {
                            }
                        }
                    }

                    if (count($result) > 0 && $sum > 0) {
                        $order = new Order($this->userId);
                        $order->setType($option);
                        $order->setSum($sum);
                        $order->setDescription(
                            Loc::getMessage(
                                'ITEMS_ORDER_DESCRIPTION',
                                [
                                    '#OPTION_NAME#' => Loc::getMessage('ITEMS_OPTION_' . ToUpper($option))
                                ]
                            )
                        );
                        $order->setAdditionalParams($result);
                        $order->make();
                        if ($order->getRedirect()) {
                            LocalRedirect($order->getRedirect(), true);
                        }
                    }
                }
                break;
        }
    }

    public function my()
    {
        $result = [];

        $iblock = new Iblock();
        $iblocks = $iblock->getAllIds();

        if (count($iblocks) > 0) {
            foreach ($iblocks as $iblockId) {
                $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($iblockId))));
                $hl = new Hl($tableName);
                if ($hl->obj !== null) {
                    $obj = $hl->obj;
                    try {
                        $get = $obj::getList(
                            [
                                'select' => [
                                    'ID',
                                    'UF_ID',
                                    'UF_IBLOCK_ID',
                                    'UF_CITY',
                                    'UF_ACTIVE_FROM',
                                    'UF_ACTIVE_TO',
                                    'UF_VIP_START',
                                    'UF_VIP_END',
                                    'UF_PREMIUM_START',
                                    'UF_PREMIUM_END',
                                    'UF_COLOR_START',
                                    'UF_COLOR_END',
                                    'UF_TIMESTAMP',
                                    'UF_DATE_CREATE',
                                    'UF_NAME',
                                    'UF_PRICE',
                                    'UF_IMAGE_ID',
                                    'UF_IMAGES',
                                    'UF_ACTIVE',
                                    'UF_ARCHIVE',
                                    'UF_CANCEL_DATE',
                                    'UF_RETURN_DATE',
                                ],
                                'filter' => [
                                    '=UF_IBLOCK_ID' => $iblockId,
                                    '=UF_USER_ID' => $this->userId
                                ],
                                'order' => [
                                    'ID' => 'DESC'
                                ]
                            ]
                        );
                        while ($res = $get->fetch()) {
                            $active = 0;
                            $vip = 0;
                            $premium = 0;
                            $color = 0;

                            if ($res['UF_VIP_END'] && strtotime($res['UF_VIP_END']) > time()) {
                                $vip = 1;
                            }

                            if ($res['UF_PREMIUM_END'] && strtotime($res['UF_PREMIUM_END']) > time()) {
                                $premium = 1;
                            }

                            if ($res['UF_COLOR_END'] && strtotime($res['UF_COLOR_END']) > time()) {
                                $color = 1;
                            }

                            if ($res['UF_ACTIVE_TO'] && strtotime($res['UF_ACTIVE_TO']) > time()) {
                                $active = 1;
                            }

                            if ($res['UF_CANCEL_DATE']) {
                                $active = 0;
                            }

                            $result[] = [
                                'ID' => $res['ID'],
                                'UF_ID' => $res['UF_ID'],
                                'UF_IBLOCK_ID' => $res['UF_IBLOCK_ID'],
                                'UF_CITY' => $res['UF_CITY'],
                                'UF_TIMESTAMP' => $res['UF_TIMESTAMP'],
                                'UF_DATE_CREATE' => $res['UF_DATE_CREATE'],
                                'UF_NAME' => $res['UF_NAME'],
                                'UF_PRICE' => $res['UF_PRICE'],
                                'UF_IMAGE_ID' => $res['UF_IMAGE_ID'],
                                'UF_IMAGES' => $res['UF_IMAGES'],
                                'UF_CANCEL_DATE' => $res['UF_CANCEL_DATE'],
                                'UF_RETURN_DATE' => $res['UF_RETURN_DATE'],
                                'URL' => SITE_DIR . 'user/items/' . $res['UF_IBLOCK_ID'] . '-' . $res['UF_ID'] . '/',
                                'ACTIVE' => $active,
                                'VIP' => $vip,
                                'PREMIUM' => $premium,
                                'COLOR' => $color,
                                'VIP_END' => ($vip > 0) ? $res['UF_VIP_END'] : '',
                                'PREMIUM_END' => ($premium > 0) ? $res['UF_PREMIUM_END'] : '',
                                'COLOR_END' => ($color > 0) ? $res['UF_COLOR_END'] : ''
                            ];
                        }
                    } catch (\Exception $e) {
                    }
                }
            }
        }

        return $result;
    }

    public function moderation()
    {
        $result = [];

        $hl = new Hl('Democontentpiall');
        if ($hl->obj !== null) {
            $obj = $hl->obj;
            $city = new City();
            $cities = $city->getList();

            try {
                $filter = [
                    '=UF_MODERATION' => 1
                ];

                $get = $obj::getList(
                    [
                        'select' => ['*'],
                        'filter' => $filter,
                        'order' => ['ID' => 'DESC']
                    ]
                );

                while ($res = $get->fetch()) {
                    $res['CITY_NAME'] = '';
                    if (isset($cities[$res['UF_CITY']])) {
                        $res['CITY_NAME'] = $cities[$res['UF_CITY']]['name'];
                    }

                    $result[] = $res;
                }
            } catch (\Exception $e) {

            }
        }

        if (count($result) > 0) {
            $reviews = new Reviews();
            $us = new User(0);
            foreach ($result as $k => $item) {
                $us->setId(intval($item['UF_USER_ID']));
                $reviews->setUserId(intval($item['UF_USER_ID']));

                $result[$k]['USER'] = $us->get();
                $result[$k]['CURRENT_RATING'] = $reviews->rating();
                $result[$k]['REVIEWS_COUNT'] = $reviews->getCountByUser();

                $stat = new Stat(intval($item['UF_ITEM_ID']), intval($item['UF_IBLOCK_ID']));

                $result[$k]['UF_COUNTER'] = $stat->total();

                unset($stat);
            }
        }

        return $result;
    }
}