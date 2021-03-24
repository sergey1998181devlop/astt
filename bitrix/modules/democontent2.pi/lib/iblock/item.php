<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 08.01.2019
 * Time: 11:39
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Iblock;

use Bitrix\Iblock\InheritedProperty\ElementValues;
use Bitrix\Iblock\PropertyIndex\Manager;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\IO\File;
use Bitrix\Main\IO\Path;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Type\ParameterDictionary;
use Bitrix\Main\Web\Json;
use CModule;
use Democontent2\Pi\Cron\Services;
use Democontent2\Pi\EventManager;
use Democontent2\Pi\FireBase;
use Democontent2\Pi\Hl;
use Democontent2\Pi\Logger;
use Democontent2\Pi\Notifications;
use Democontent2\Pi\Order;
use Democontent2\Pi\User;
use Democontent2\Pi\Utils;

class Item
{
    const ALL_TABLE_NAME = 'Democontentpiall';
    const STAGES_TABLE_NAME = 'Democontentpistages';

    protected $ttl = 0;
    protected $cityCode = '';
    protected $iBlockType = '';
    protected $iBlockCode = '';
    protected $iBlockId = 0;
    protected $itemCode = '';
    protected $itemId = 0;
    protected $userId = 0;
    protected $filteredUserId = 0;
    protected $defaultRedirect = false;
    protected $cityRedirect = '';
    protected $cityParams = [];
    protected $paymentRedirect = '';
    protected $allClass = null;
    protected $stagesClass = null;
    private $errors = [];
    private $error = '';

    /**
     * Item constructor.
     * @param int $ttl
     */
    public function __construct($ttl = 86400)
    {
        $this->ttl = intval($ttl);
    }

    /**
     * @param int $filteredUserId
     */
    public function setFilteredUserId(int $filteredUserId): void
    {
        $this->filteredUserId = $filteredUserId;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return int
     */
    public function getIBlockId()
    {
        return $this->iBlockId;
    }

    /**
     * @return int
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * @return string
     */
    public function getPaymentRedirect()
    {
        return $this->paymentRedirect;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return array
     */
    public function getCityParams()
    {
        return $this->cityParams;
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
     * @param int $iBlockId
     */
    public function setIBlockId($iBlockId)
    {
        $this->iBlockId = intval($iBlockId);
    }

    /**
     * @param string $itemCode
     */
    public function setItemCode($itemCode)
    {
        $this->itemCode = $itemCode;
    }

    /**
     * @param int $itemId
     */
    public function setItemId($itemId)
    {
        $this->itemId = intval($itemId);
    }

    /**
     * @return bool
     */
    public function isDefaultRedirect()
    {
        return $this->defaultRedirect;
    }

    /**
     * @return string
     */
    public function getCityRedirect()
    {
        return $this->cityRedirect;
    }

    private function setStagesClass()
    {
        if ($this->stagesClass == null) {
            $hl = new Hl(static::STAGES_TABLE_NAME, 0);

            if ($hl->obj !== null) {
                $this->stagesClass = $hl->obj;
            } else {
                $className = ToUpper(end(explode('\\', __CLASS__)));
                $add = Hl::create(
                    ToLower(static::STAGES_TABLE_NAME),
                    [
                        'UF_TASK_ID' => [
                            'Y',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TASK_ID')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TASK_ID')],
                            ]
                        ],
                        'UF_PRICE' => [
                            'Y',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PRICE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PRICE')],
                            ]
                        ],
                        'UF_STATUS' => [
                            'Y',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_STATUS')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_STATUS')],
                            ]
                        ],
                        'UF_SORT' => [
                            'Y',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_SORT')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_SORT')],
                            ]
                        ],
                        'UF_NAME' => [
                            'N',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_NAME')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_NAME')],
                            ]
                        ],
                        'UF_DESCRIPTION' => [
                            'N',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DESCRIPTION')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DESCRIPTION')],
                            ]
                        ],
                        'UF_FILES' => [
                            'N',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_FILES')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_FILES')],
                            ]
                        ],
                    ],
                    [
                        'ALTER TABLE `' . ToLower(static::STAGES_TABLE_NAME) . '` MODIFY `UF_DESCRIPTION` LONGTEXT;',
                        'ALTER TABLE `' . ToLower(static::STAGES_TABLE_NAME) . '` MODIFY `UF_FILES` LONGTEXT;',
                    ],
                    [
                        ['ID', 'UF_TASK_ID'],
                        ['UF_TASK_ID'],
                        ['UF_PRICE'],
                        ['UF_STATUS'],
                        ['UF_SORT'],
                    ],
                    Loc::getMessage($className . '_STAGES_IBLOCK_NAME')
                );

                if ($add) {
                    $hl = new Hl(static::STAGES_TABLE_NAME);
                    if ($hl->obj !== null) {
                        $this->stagesClass = $hl->obj;
                    }
                }
            }
        }
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
                            'N',
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
                        'UF_STATUS' => [
                            'Y',
                            'integer',
                            [
                                'SETTINGS' => [
                                    'DEFAULT_VALUE' => 1,
                                    'MIN_VALUE' => 1,
                                    'MAX_VALUE' => 6,
                                ],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_STATUS')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_STATUS')],
                            ]
                        ]
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
                        ['UF_STATUS'],
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

    public function getNameAndCode()
    {
        $result = [];

        if ($this->iBlockId > 0 && $this->itemId > 0) {
            $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($this->iBlockId))));
            $hl = new Hl($tableName);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            'UF_ID',
                            'UF_NAME',
                            'UF_IBLOCK_ID',
                            'UF_IBLOCK_TYPE',
                            'UF_IBLOCK_CODE',
                            'UF_CODE'
                        ],
                        'filter' => [
                            '=UF_ID' => $this->itemId,
                            '=UF_IBLOCK_ID' => $this->iBlockId,
                            '=UF_MODERATION' => 0,
                            '=UF_PAYED' => 1,
                            '=UF_ACTIVE' => 1
                        ],
                        'limit' => 1
                    ]
                );

                while ($res = $get->fetch()) {
                    $result = $res;
                }
            }
        }

        return $result;
    }

    public function getItemRedirect()
    {
        $result = '';

        if ($this->iBlockId > 0 && $this->itemId > 0) {
            $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($this->iBlockId))));
            $hl = new Hl($tableName);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            'UF_IBLOCK_TYPE',
                            'UF_IBLOCK_CODE',
                            'UF_CODE'
                        ],
                        'filter' => [
                            '=UF_ID' => $this->itemId,
                            '=UF_IBLOCK_ID' => $this->iBlockId,
                            '=UF_MODERATION' => 0,
                            '=UF_PAYED' => 1,
                            '=UF_ACTIVE' => 1
                        ],
                        'limit' => 1
                    ]
                );

                while ($res = $get->fetch()) {
                    $result = SITE_DIR . $res['UF_IBLOCK_TYPE'] . '/' . $res['UF_IBLOCK_CODE'] . '/' . $res['UF_CODE'] . '/';
                }
            }
        }

        return $result;
    }

    public function getItem()
    {
        $result = [];
        if ($this->iBlockId > 0 && $this->itemId > 0 && $this->userId > 0) {
            $get = \CIBlockElement::GetList(
                [],
                [
                    'ID' => $this->itemId,
                    'IBLOCK_ID' => $this->iBlockId,
                    '=PROPERTY___hidden_user' => $this->userId
                ],
                false,
                false,
                [
                    '*',
                    'PROPERTY_*'
                ]
            );
            while ($res = $get->GetNextElement()) {
                $fields = $res->GetFields();
                $properties = $res->GetProperties(
                    [
                        'SORT' => 'ASC'
                    ]
                );

                $iBlock = new Iblock();

                $result = [
                    'city' => '',
                    'iBlockTypeName' => $iBlock->getTypeName($fields['IBLOCK_TYPE_ID']),
                    'iBlockName' => $fields['IBLOCK_NAME'],
                    'name' => $fields['~NAME'],
                    'code' => $fields['CODE'],
                    'description' => $fields['~DETAIL_TEXT'],
                    'mainImage' => intval($fields['DETAIL_PICTURE']),
                    'dateCreate' => $fields['DATE_CREATE'],
                    'activeFrom' => $fields['ACTIVE_FROM'],
                    'activeTo' => $fields['ACTIVE_TO'],
                    'moderation' => 0,
                    'moderationCancelReason' => '',
                    'payed' => 0,
                    'price' => 0,
                    'quickly' => 0,
                    'quicklyEnd' => '',
                    'location' => [],
                    'files' => [],
                    'images' => [],
                    'properties' => []
                ];

                foreach ($properties as $k => $v) {
                    preg_match_all('/__hidden_([a-zA-Z_]+)/m', $v['CODE'], $matches, PREG_SET_ORDER, 0);
                    if (count($matches)) {
                        switch ($k) {
                            case '__hidden_moderation_reason':
                                if (isset($v['VALUE']['TEXT']) && strlen($v['VALUE']['TEXT']) > 0) {
                                    $result['moderationCancelReason'] = $v['VALUE']['TEXT'];
                                }
                                break;
                            case '__hidden_city':
                                if (intval($v['VALUE']) > 0) {
                                    $city = new City();
                                    $getCity = $city->getById(intval($v['VALUE']));
                                    if (count($getCity) > 0) {
                                        $result['city'] = $getCity['name'];
                                    }
                                }
                                break;
                            case '__hidden_location':
                                if (strlen($v['VALUE']) > 0) {
                                    $result['location'] = explode(',', $v['VALUE']);
                                }
                                break;
                            case '__hidden_moderation':
                                $result['moderation'] = intval($v['VALUE']);
                                break;
                            case '__hidden_payed':
                                $result['payed'] = intval($v['VALUE']);
                                break;
                            case '__hidden_price':
                                $result['price'] = floatval($v['VALUE']);
                                break;
                            case '__hidden_quickly_end':
                                if ($v['VALUE']) {
                                    if (strtotime($v['VALUE']) >= time()) {
                                        $result['quickly'] = 1;
                                        $result['quicklyEnd'] = DateTime::createFromTimestamp(strtotime($v['VALUE']));
                                    }
                                }
                                break;
                        }
                        continue;
                    }

                    switch ($k) {
                        case 'files':
                        case 'images':
                            if (is_array($v['VALUE']) && count($v['VALUE']) > 0) {
                                $result[$k] = $v['VALUE'];
                            }
                            break;
                        default:
                            switch ($v['PROPERTY_TYPE']) {
                                case 'S':
                                    $result['properties'][$v['ID']] = $v['VALUE'];
                                    break;
                                case 'N':
                                    $result['properties'][$v['ID']] = floatval($v['VALUE']);
                                    break;
                                case 'L':
                                    $result['properties'][$v['ID']] = intval($v['VALUE_ENUM_ID']);
                                    break;
                                case 'E':
                                    $result['properties'][$v['ID']] = intval($v['VALUE']);
                                    break;
                            }
                    }
                }
                break;
            }
        }

        return $result;
    }

    public function getOwner()
    {
        $result = 0;

        if ($this->iBlockId > 0 && $this->itemId > 0) {
            $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($this->iBlockId))));
            $hl = new Hl($tableName);
            if ($hl->obj !== null) {
                try {
                    $obj = $hl->obj;
                    $get = $obj::getList(
                        [
                            'select' => [
                                'UF_USER_ID'
                            ],
                            'filter' => [
                                '=UF_ID' => intval($this->itemId)
                            ],
                            'limit' => 1
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $result = intval($res['UF_USER_ID']);
                    }
                } catch (\Exception $e) {
                }
            }
        }

        return $result;
    }

    public function getCurrentStatus()
    {
        $result = 0;

        if ($this->iBlockId && $this->itemId) {
            $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($this->iBlockId))));
            $hl = new Hl($tableName);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                try {
                    $get = $obj::getList(
                        [
                            'select' => [
                                'UF_STATUS'
                            ],
                            'filter' => [
                                '=UF_ID' => $this->itemId,
                                '=UF_IBLOCK_ID' => $this->iBlockId,
                                '=UF_ACTIVE' => 1,
                                //'=UF_MODERATION' => 0,
                                //'=UF_PAYED' => 1
                            ],
                            'limit' => 1
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $result = intval($res['UF_STATUS']);
                    }
                } catch (\Exception $e) {

                }
            }
        }

        return $result;
    }

    public function checkOwner()
    {
        $result = false;

        if ($this->iBlockId && $this->userId && $this->itemId) {
            $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($this->iBlockId))));
            $hl = new Hl($tableName);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                try {
                    $get = $obj::getList(
                        [
                            'select' => [
                                'ID'
                            ],
                            'filter' => [
                                '=UF_ID' => $this->itemId,
                                '=UF_IBLOCK_ID' => $this->iBlockId,
                                '=UF_USER_ID' => $this->userId,
                                '=UF_ACTIVE' => 1,
                                //'=UF_MODERATION' => 0,
                                '=UF_PAYED' => 1
                            ],
                            'limit' => 1
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $result = true;
                    }
                } catch (\Exception $e) {

                }
            }
        }

        return $result;
    }

    public function edit($item, $data, $filesData)
    {
        if ($this->itemId > 0 && $this->iBlockId > 0) {
            if (isset($data['name']) && strlen($data['name']) > 0 && isset($data['description']) && strlen($data['description']) > 0) {
                $el = new \CIBlockElement();
                $us = new User($item['UF_USER_ID'], 0);
                $userParams = $us->get();

                $allow = true;
                $dateStart = '';
                $dateEnd = '';
                $stages = [];
                $deleteStages = [];
                $newStages = [];
                $errors = [];
                $files = [];
                $hiddenFiles = [];
                $removedFiles = [];
                $removedHiddenFiles = [];
                $updateFiles = false;
                $updateHiddenFiles = false;
                $propertyValues = [
                    '__hidden_price' => floatval($data['price']),
                    '__hidden_moderation' => intval(Option::get(DSPI, 'moderation_update'))
                ];

                if (intval($userParams['UF_DSPI_MOD_OFF']) > 0) {
                    $propertyValues['__hidden_moderation'] = 0;
                }

                if (!intval($item['UF_SAFE'])) {
                    $security = false;

                    if (strlen(Option::get(DSPI, 'safeCrowApiKey')) > 0
                        && strlen(Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
                        $security = true;

                        if (isset($data['security']) && strlen($data['security']) > 0) {
                            $propertyValues['__hidden_security'] = 1;

                            if ($propertyValues['__hidden_price'] <= 0) {
                                $propertyValues['__hidden_security'] = 0;
                            }
                        } else {
                            $security = false;
                        }
                    }

                    if (isset($data['deleteStages']) && count($data['deleteStages'])) {
                        $deleteStages = $data['deleteStages'];
                    }

                    if (isset($data['newStages']) && count($data['newStages'])) {
                        $newStages = $data['newStages'];
                    }

                    if (isset($data['stages'])) {
                        foreach ($data['stages'] as $stageKey => $stageValue) {
                            if (in_array($stageKey, $deleteStages)) {
                                continue;
                            }

                            if (isset($stageValue['name']) && strlen($stageValue['name']) > 0) {
                                $stages[$stageKey] = [
                                    'name' => strip_tags(HTMLToTxt($stageValue['name'])),
                                    'price' => (isset($stageValue['price'])) ? intval($stageValue['price']) : 0
                                ];

                                if (!isset($stageValue['price']) || !intval($stageValue['price'])) {
                                    $security = false;
                                }
                            }
                        }

                        if (!count($stages)) {
                            $allow = false;
                            $errors['stages'] = 'empty';
                        }
                    }

                    if (count($stages) > 0) {
                        $propertyValues['__hidden_price'] = 0;

                        if (!$security) {
                            $propertyValues['__hidden_security'] = 0;
                        } else {
                            $propertyValues['__hidden_security'] = 1;
                        }
                    }
                }

                if (isset($data['dateStart'])) {
                    $dateStart = $data['dateStart'] . ' 00:00:00';

                    if (isset($data['timeStart'])) {
                        $tmp = explode(':', $data['timeStart']);
                        if (count($tmp) == 2) {
                            switch ($tmp[0]) {
                                case '00':
                                case '01':
                                case '02':
                                case '03':
                                case '04':
                                case '05':
                                case '06':
                                case '07':
                                case '08':
                                case '09':
                                case '10':
                                case '11':
                                case '12':
                                case '13':
                                case '14':
                                case '15':
                                case '16':
                                case '17':
                                case '18':
                                case '19':
                                case '20':
                                case '21':
                                case '22':
                                case '23':
                                    if (intval($tmp[1]) >= 0 && intval($tmp[1]) <= 59) {
                                        $dateStart = $data['dateStart'] . ' ' . $tmp[0] . ':' . $tmp[1] . ':00';
                                    }
                                    break;
                            }
                        }
                    }
                }

                if (isset($data['dateEnd'])) {
                    $dateEnd = $data['dateEnd'] . ' 00:00:00';

                    if (isset($data['timeEnd'])) {
                        $tmp = explode(':', $data['timeEnd']);
                        if (count($tmp) == 2) {
                            switch ($tmp[0]) {
                                case '00':
                                case '01':
                                case '02':
                                case '03':
                                case '04':
                                case '05':
                                case '06':
                                case '07':
                                case '08':
                                case '09':
                                case '10':
                                case '11':
                                case '12':
                                case '13':
                                case '14':
                                case '15':
                                case '16':
                                case '17':
                                case '18':
                                case '19':
                                case '20':
                                case '21':
                                case '22':
                                case '23':
                                    if (intval($tmp[1]) >= 0 && intval($tmp[1]) <= 59) {
                                        $dateEnd = $data['dateEnd'] . ' ' . $tmp[0] . ':' . $tmp[1] . ':00';
                                    }
                                    break;
                            }
                        }
                    }
                }

                if (strlen($dateStart) > 0 && strlen($dateEnd) > 0) {
                    if (strtotime($dateStart) !== strtotime($dateEnd)) {
                        if (strtotime($dateEnd) > strtotime($dateStart)) {
                            $propertyValues['__hidden_begin_with'] = DateTime::createFromTimestamp(strtotime($dateStart));
                            $propertyValues['__hidden_run_up'] = DateTime::createFromTimestamp(strtotime($dateEnd));
                        } else {
                            $propertyValues['__hidden_begin_with'] = DateTime::createFromTimestamp(strtotime($dateEnd));
                            $propertyValues['__hidden_run_up'] = DateTime::createFromTimestamp(strtotime($dateStart));
                        }
                    } else {
                        $propertyValues['__hidden_begin_with'] = DateTime::createFromTimestamp(strtotime($dateStart));
                    }
                } else {
                    if (strlen($dateStart) > 0) {
                        $propertyValues['__hidden_begin_with'] = DateTime::createFromTimestamp(strtotime($dateStart));
                    }

                    if (strlen($dateEnd) > 0) {
                        $propertyValues['__hidden_run_up'] = DateTime::createFromTimestamp(strtotime($dateEnd));
                    }
                }

                if ($allow) {
                    $currentFiles = [];
                    $currentHiddenFiles = [];

                    if (strlen($item['UF_FILES']) > 0) {
                        $currentFiles = unserialize($item['UF_FILES']);
                    }

                    if (strlen($item['UF_HIDDEN_FILES']) > 0) {
                        $currentHiddenFiles = unserialize($item['UF_HIDDEN_FILES']);
                    }

                    if (isset($data['__removeFiles'])) {
                        if (count($currentFiles) > 0) {
                            foreach ($data['__removeFiles'] as $removeFileKey => $removeFileId) {
                                foreach ($currentFiles as $currentFileKey => $currentFileId) {
                                    if ($removeFileId == $currentFileId) {
                                        $updateFiles = true;
                                        $removedFiles[] = $removeFileId;
                                        unset($data['__removeFiles'][$removeFileKey]);
                                        break;
                                    }
                                }
                            }
                        }

                        if (count($currentHiddenFiles) > 0) {
                            foreach ($data['__removeFiles'] as $removeFileKey => $removeFileId) {
                                foreach ($currentHiddenFiles as $currentHiddenFileKey => $currentHiddenFileId) {
                                    if ($removeFileId == $currentHiddenFileId) {
                                        $updateHiddenFiles = true;
                                        $removedHiddenFiles[] = $removeFileId;
                                        unset($data['__removeFiles'][$removeFileKey]);
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    if (isset($filesData['__files'])) {
                        if (count($filesData['__files']['name']) > 0) {
                            $i = count($files);
                            foreach ($filesData['__files']['name'] as $fileKey => $fileValue) {
                                $extension = Utils::getExtension($fileValue);
                                if (strlen($extension) > 0) {
                                    $updateFiles = true;
                                    $fileArray = \CFile::MakeFileArray($filesData['__files']['tmp_name'][$fileKey]);
                                    $fileArray['description'] = $fileValue;
                                    $fileArray['name'] = md5(microtime(true)) . '-' . ToLower(randString(rand(5, 10))) . $extension;
                                    $files['n' . $i] = [
                                        'VALUE' => $fileArray,
                                        'DESCRIPTION' => $fileArray['description']
                                    ];
                                    $i++;
                                }
                            }
                        }
                    }

                    if (isset($filesData['__hiddenFiles'])) {
                        if (count($filesData['__hiddenFiles']['name']) > 0) {
                            $i = count($hiddenFiles);
                            foreach ($filesData['__hiddenFiles']['name'] as $fileKey => $fileValue) {
                                $extension = Utils::getExtension($fileValue);
                                if (strlen($extension) > 0) {
                                    $updateHiddenFiles = true;
                                    $fileArray = \CFile::MakeFileArray($filesData['__hiddenFiles']['tmp_name'][$fileKey]);
                                    $fileArray['description'] = $fileValue;
                                    $fileArray['name'] = md5(microtime(true)) . '-' . ToLower(randString(rand(5, 10))) . $extension;
                                    $hiddenFiles['n' . $i] = [
                                        'VALUE' => $fileArray,
                                        'DESCRIPTION' => $fileArray['description']
                                    ];
                                    $i++;
                                }
                            }
                        }
                    }

                    if ($updateFiles) {
                        $putFiles = [];
                        $i = 0;
                        if (count($currentFiles) > 0) {
                            foreach ($currentFiles as $fileId) {
                                if (!in_array($fileId, $removedFiles)) {
                                    $fileArray = \CFile::MakeFileArray($fileId);
                                    $putFiles['n' . $i] = [
                                        'VALUE' => $fileArray,
                                        'DESCRIPTION' => $fileArray['description']
                                    ];
                                    $i++;
                                }
                            }
                        }

                        if (count($files) > 0) {
                            foreach ($files as $fileArray) {
                                $putFiles['n' . $i] = $fileArray;
                                $i++;
                            }
                        }

                        $propertyValues['files'] = (count($putFiles) > 0) ? $putFiles : false;
                    }

                    if ($updateHiddenFiles) {
                        $putFiles = [];
                        $i = 0;
                        if (count($currentHiddenFiles) > 0) {
                            foreach ($currentHiddenFiles as $fileId) {
                                if (!in_array($fileId, $removedHiddenFiles)) {
                                    $fileArray = \CFile::MakeFileArray($fileId);
                                    $putFiles['n' . $i] = [
                                        'VALUE' => $fileArray,
                                        'DESCRIPTION' => $fileArray['description']
                                    ];
                                    $i++;
                                }
                            }
                        }

                        if (count($hiddenFiles) > 0) {
                            foreach ($hiddenFiles as $fileArray) {
                                $putFiles['n' . $i] = $fileArray;
                                $i++;
                            }
                        }

                        $propertyValues['hidden_files'] = (count($putFiles) > 0) ? $putFiles : false;
                    }

                    \CIBlockElement::SetPropertyValuesEx($this->itemId, $this->iBlockId, $propertyValues);
                    $update = $el->Update(
                        $this->itemId,
                        [
                            'NAME' => strip_tags(HTMLToTxt($data['name'])),
                            'DETAIL_TEXT' => HTMLToTxt(str_replace("\r\n", '<br>', strip_tags($data['description']))),
                            'TIMESTAMP_X' => DateTime::createFromTimestamp(time()),
                            'DETAIL_PICTURE' => false
                        ]
                    );
                    if ($update) {
                        $responseCheckList = new \Democontent2\Pi\CheckList\Response($this->itemId);
                        $responseCheckList->update(new ParameterDictionary($data));

                        if (count($removedFiles) > 0) {
                            foreach ($removedFiles as $fileId) {
                                \CFile::Delete($fileId);
                            }
                        }

                        if (count($removedHiddenFiles) > 0) {
                            foreach ($removedHiddenFiles as $fileId) {
                                \CFile::Delete($fileId);
                            }
                        }

                        if (count($deleteStages) > 0 || count($newStages) > 0 || count($stages) > 0) {
                            if (count($deleteStages) > 0) {
                                foreach ($deleteStages as $stageId) {
                                    $this->deleteStage($stageId);
                                }
                            }

                            $i = 0;

                            if (count($stages) > 0) {
                                foreach ($stages as $stageId => $stageValue) {
                                    if (in_array($stageId, $deleteStages)) {
                                        continue;
                                    }
                                    $this->updateStage($stageId, $stageValue, $i++);
                                }
                            }

                            if (count($newStages) > 0) {
                                $this->addStages($newStages, $i);
                            }
                        }

                        if ($propertyValues['__hidden_moderation'] > 0) {
                            Event::send(
                                [
                                    'EVENT_NAME' => 'DSPI_UPDATE_MODERATION',
                                    'LID' => Application::getInstance()->getContext()->getSite(),
                                    'C_FIELDS' => [
                                        'ID' => $this->itemId,
                                        'IBLOCK_ID' => $this->iBlockId,
                                        'IBLOCK_TYPE' => $this->iBlockType
                                    ]
                                ]
                            );
                        }

                        Logger::add(
                            $this->userId,
                            'updateItemSuccess',
                            [
                                'userId' => $this->userId,
                                'iBlockId' => $this->iBlockId,
                                'id' => $this->itemId
                            ]
                        );

                        $event = new EventManager(
                            'updateItemSuccess',
                            [
                                'userId' => $this->userId,
                                'iBlockId' => $this->iBlockId,
                                'id' => $this->itemId
                            ]
                        );
                        $event->execute();

                        /*if (count($oldImages) > 0 && (count($detailImage) > 0 || count($images) > 0)) {
                            foreach ($oldImages as $img) {
                                \CFile::Delete($img);
                            }
                        }*/
                    } else {
                        $this->error = $el->LAST_ERROR;
                        Logger::add(
                            $this->userId,
                            'updateItemError',
                            [
                                'userId' => $this->userId,
                                'iBlockId' => $this->iBlockId,
                                'id' => $this->itemId,
                                'data' => $data,
                                'error' => $el->LAST_ERROR
                            ]
                        );

                        $event = new EventManager(
                            'updateItemError',
                            [
                                'userId' => $this->userId,
                                'iBlockId' => $this->iBlockId,
                                'id' => $this->itemId,
                                'data' => $data,
                                'error' => $el->LAST_ERROR
                            ]
                        );
                        $event->execute();
                    }
                } else {
                    if (count($errors) > 0) {
                        $this->errors = $request->getPostList()->get('name');
                        Logger::add(
                            $this->userId,
                            'prepareUpdateItemError',
                            [
                                'data' => $data,
                                'errors' => $errors
                            ]
                        );

                        $event = new EventManager(
                            'prepareUpdateItemError',
                            [
                                'userId' => $this->userId,
                                'iBlockId' => $this->iBlockId,
                                'data' => $data,
                                'errors' => $errors
                            ]
                        );
                        $event->execute();
                    }
                }
            }
        }
    }

    public function getForEdit()
    {
        $result = [];

        if ($this->userId > 0 && $this->iBlockId > 0 && $this->itemId > 0) {
            $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($this->iBlockId))));
            $hl = new Hl($tableName);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                try {
                    $filter = [
                        '=UF_ID' => $this->itemId,
                        '=UF_IBLOCK_ID' => $this->iBlockId,
                        '=UF_USER_ID' => $this->userId,
                    ];

                    $get = $obj::getList(
                        [
                            'select' => ['*'],
                            'filter' => $filter,
                            'limit' => 1
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $result = $res;
                        $result['STAGES'] = $this->getStages();
                    }
                } catch (\Exception $e) {
                }
            }
        }

        return $result;
    }

    public function getItemCustom()
    {
        global $APPLICATION;
        $list = [];
        $result = [];
        $page = $APPLICATION->GetCurPage(); // результат - /ru/index.php
        $pieces = explode("/", $page);

        $res = \CIBlockElement::GetByID($pieces[4]);
        if($ar_res = $res->GetNext()){
            $list = $ar_res;
        }

        $arSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM","PROPERTY_*");//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
        $arFilter = Array("IBLOCK_ID"=>IntVal($list['IBLOCK_ID'] ) , "ID" => $list['ID'], "ACTIVE"=>"Y");
        $res = \CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
        while($ob = $res->GetNextElement()){
            $arFields = $ob->GetFields();

            $arProps = $ob->GetProperties();


            array_push($result , $arFields);
            array_push($result , $arProps);
        }
        return $result;
    }


    public function gatItemDetailForTasks()
    {
        global $APPLICATION;
        $list = [];
        $result = [];
        $page = $APPLICATION->GetCurPage(); // результат - /ru/index.php
        $pieces = explode("/", $page);



        $res = \CIBlockElement::GetByID($pieces[4]);
        if ($ar_res = $res->GetNext()) {
            $list = $ar_res;
        }


        $str = Utils::getIBlockIdByType(
            str_replace('-', '_', 'democontent2_pi_' . $list['IBLOCK_TYPE_ID']), $list['IBLOCK_CODE']
        );
        $type  = str_replace('democontent2_pi_', '',  $list['IBLOCK_TYPE_ID']);
        $list['IBLOCK_TYPE_ID'] = $type;
        $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($list['IBLOCK_ID']))));

        $hl = new Hl($tableName);
        if ($hl->obj !== null) {
            $obj = $hl->obj;
            try {
                $filter = [
                    '=UF_ID' => $list['ID'],
                    '=UF_CODE' => $list['CODE'] . '-' . $list['ID'],
                    '=UF_IBLOCK_TYPE' => str_replace('_', '-', $list['IBLOCK_TYPE_ID']),
                    '=UF_IBLOCK_CODE' => $list['IBLOCK_CODE']
                ];

                if ($this->cityCode) {
                    $filter['=UF_CITY'] = $this->cityParams['id'];
                }


                if ($this->filteredUserId > 0) {
                    $filter['=UF_USER_ID'] = $list['CREATED_BY'];
                } else {

                }

                $get = $obj::getList(
                    [
                        'select' => ['*'],
                        'filter' => $filter,
                        'limit' => 1
                    ]
                );
                $result = $get->fetch();


                $result['UF_NAME'] = strip_tags($result['UF_NAME']);
                $result['UF_DESCRIPTION'] = strip_tags($result['UF_DESCRIPTION']);

                $el = new \CIBlockElement();

//                            "PROPERTY___hidden_moderation", "PROPERTY___hidden_moderation_reason" , "PROPERTY___hidden_user"
                $arSelect = array("ID", "NAME" ,  "PROPERTY___hidden_moderation", "PROPERTY___hidden_moderation_reason" , "PROPERTY___hidden_user");
                $arFilter = array("IBLOCK_ID" => $list['IBLOCK_ID'], "ID" => $list['ID'], "ACTIVE" => "Y");
                $res = $el->GetList(array(), $arFilter, false, false, $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $result['MODERATION'] = $arFields['PROPERTY___HIDDEN_MODERATION_VALUE'];
                    $result['MODERATION_REASON'] = $arFields['~PROPERTY___HIDDEN_MODERATION_REASON_VALUE']['TEXT'];
                    $result['HIDDEN_USER'] = $arFields['~PROPERTY___HIDDEN_USER_VALUE'];
                }
            } catch (\Exception $e) {
            }
            return $result;
        }





    }
    public function gatItemDetailEdit()
    {
        global $APPLICATION;
        $list = [];
        $result = [];
        $page = $APPLICATION->GetCurPage(); // результат - /ru/index.php
        $pieces = explode("/", $page);
        $files = [];

        $res = \CIBlockElement::GetByID($pieces[4]);
        if ($ar_res = $res->GetNext()) {
            $list = $ar_res;

        }

        $res = \CIBlockElement::GetProperty($list['IBLOCK_ID'], $pieces[4], "sort", "asc", array("CODE" => "FILES"));
        if ($ob = $res->GetNext())
        {
            $files[] = $ob['VALUE'];
        }



        $hl = new Hl('Democontentpiall');
        if ($hl->obj !== null) {
            $obj = $hl->obj;
            try {
                $filter = [
                    '=UF_ITEM_ID' => $list['ID'],

                ];

                if ($this->cityCode) {
                    $filter['=UF_CITY'] = $this->cityParams['id'];
                }


                if ($this->filteredUserId > 0) {
                    $filter['=UF_USER_ID'] = $list['CREATED_BY'];
                } else {

                }

                $get = $obj::getList(
                    [
                        'select' => ['*'],
                        'filter' => $filter,
                        'limit' => 1
                    ]
                );
                $result = $get->fetch();


                $result['UF_NAME'] = strip_tags($result['UF_NAME']);
                $result['UF_DESCRIPTION'] = strip_tags($result['UF_DESCRIPTION']);

                $el = new \CIBlockElement();



//                            "PROPERTY___hidden_moderation", "PROPERTY___hidden_moderation_reason" , "PROPERTY___hidden_user"
                $arSelect = array(
                    "ID",
                    "NAME" ,
                    "PROPERTY___hidden_moderation",
                    "PROPERTY___hidden_moderation_reason" ,
                    "PROPERTY___hidden_user" ,
                    "PROPERTY___hidden_price",
                    "PROPERTY_CHARACTERISTIC_1",
                    "PROPERTY_CHARACTERISTIC_2",
                    "PROPERTY_CHARACTERISTIC_3",
                    "PROPERTY_files",
                    "PROPERTY___hidden_coordinates",
                );
                $arFilter = array("IBLOCK_ID" => $list['IBLOCK_ID'], "ID" => $list['ID'], "ACTIVE" => "Y");
                $res = $el->GetList(array(), $arFilter, false, false, $arSelect);
                while ($ob = $res->GetNextElement()) {

                    $arFields = $ob->GetFields();


                    $result['COORDINATION_ITEMS'] = $arFields['PROPERTY___HIDDEN_COORDINATES_VALUE'];
                    $result['FILES'] = $arFields['PROPERTY_FILES_VALUE'];

                    $result['MODERATION'] = $arFields['PROPERTY___HIDDEN_MODERATION_VALUE'];
                    $result['MODERATION_REASON'] = $arFields['~PROPERTY___HIDDEN_MODERATION_REASON_VALUE']['TEXT'];
                    $result['HIDDEN_USER'] = $arFields['~PROPERTY___HIDDEN_USER_VALUE'];
                    $result['HIDDEN_PRICE_VALUE'] = $arFields['PROPERTY___HIDDEN_PRICE_VALUE'];

                    $result['CHARACTERISTIC_1']['VALUE'] = $arFields['PROPERTY_CHARACTERISTIC_1_VALUE'];
                    $result['CHARACTERISTIC_1']['DESCRIPTION'] = $arFields['PROPERTY_CHARACTERISTIC_1_DESCRIPTION'];

                    $result['CHARACTERISTIC_2']['VALUE'] = $arFields['PROPERTY_CHARACTERISTIC_2_VALUE'];
                    $result['CHARACTERISTIC_2']['DESCRIPTION'] = $arFields['PROPERTY_CHARACTERISTIC_2_DESCRIPTION'];

                    $result['CHARACTERISTIC_3']['VALUE'] = $arFields['PROPERTY_CHARACTERISTIC_3_VALUE'];
                    $result['CHARACTERISTIC_3']['DESCRIPTION'] = $arFields['PROPERTY_CHARACTERISTIC_3_DESCRIPTION'];
                }
            } catch (\Exception $e) {
            }
        }




//если id  пользователя не соотвествует айди в свойстве - привязка пользователя - редиректю на общий список
//         if(!empty($_GET['UserNum']) &&  $result['HIDDEN_USER'] == $_GET['UserNum']){
        $rsUser = \CUser::GetByID($_GET['UserNum']);
        $arUser = $rsUser->Fetch();
        $result['USER_NAME'] = $arUser['NAME'];
        $result['USER_NUM'] = $arUser['ID'];
        $result['PROPS_ELEMENT']['PROPERTY_files'] = $files;


        return $result;
//         }else{
//             LocalRedirect("/user/employees/");
//         }


    }
    public function gatItemDetail()
    {
        global $APPLICATION;
        $list = [];
        $result = [];
        $page = $APPLICATION->GetCurPage(); // результат - /ru/index.php
        $pieces = explode("/", $page);
        $files = [];

        $res = \CIBlockElement::GetByID($pieces[4]);
        if ($ar_res = $res->GetNext()) {
            $list = $ar_res;

        }

        $res = \CIBlockElement::GetProperty($list['IBLOCK_ID'], $pieces[4], "sort", "asc", array("CODE" => "FILES"));
        if ($ob = $res->GetNext())
        {
            $files[] = $ob['VALUE'];
        }



                    $hl = new Hl('Democontentpiall');
                    if ($hl->obj !== null) {
                        $obj = $hl->obj;
                        try {
                            $filter = [
                                '=UF_ITEM_ID' => $list['ID'],

                            ];

                            if ($this->cityCode) {
                                $filter['=UF_CITY'] = $this->cityParams['id'];
                            }


                            if ($this->filteredUserId > 0) {
                                $filter['=UF_USER_ID'] = $list['CREATED_BY'];
                            } else {

                            }

                            $get = $obj::getList(
                                [
                                    'select' => ['*'],
                                    'filter' => $filter,
                                    'limit' => 1
                                ]
                            );
                            $result = $get->fetch();


                            $result['UF_NAME'] = strip_tags($result['UF_NAME']);
                            $result['UF_DESCRIPTION'] = strip_tags($result['UF_DESCRIPTION']);

                            $el = new \CIBlockElement();

//                            "PROPERTY___hidden_moderation", "PROPERTY___hidden_moderation_reason" , "PROPERTY___hidden_user"
                            $arSelect = array("ID",
                                "NAME" ,
                                "PROPERTY___hidden_moderation",
                                "PROPERTY___hidden_moderation_reason" ,
                                "PROPERTY___hidden_user",
                                "PROPERTY___hidden_coordinates"
                            );
                            $arFilter = array("IBLOCK_ID" => $list['IBLOCK_ID'], "ID" => $list['ID'], "ACTIVE" => "Y");
                            $res = $el->GetList(array(), $arFilter, false, false, $arSelect);
                            while ($ob = $res->GetNextElement()) {
                                $arFields = $ob->GetFields();

                                $route = array(
                                    0 => array(
                                        'route' => $arFields['PROPERTY___HIDDEN_COORDINATES_VALUE']
                                    )
                                );
                                $result['UF_PROPERTIES'] = serialize($route);
                                $result['MODERATION'] = $arFields['PROPERTY___HIDDEN_MODERATION_VALUE'];
                                $result['MODERATION_REASON'] = $arFields['~PROPERTY___HIDDEN_MODERATION_REASON_VALUE']['TEXT'];
                                $result['HIDDEN_USER'] = $arFields['~PROPERTY___HIDDEN_USER_VALUE'];
                            }
                        } catch (\Exception $e) {
                        }
                    }




//если id  пользователя не соотвествует айди в свойстве - привязка пользователя - редиректю на общий список
//         if(!empty($_GET['UserNum']) &&  $result['HIDDEN_USER'] == $_GET['UserNum']){
             $rsUser = \CUser::GetByID($_GET['UserNum']);
             $arUser = $rsUser->Fetch();
             $result['USER_NAME'] = $arUser['NAME'];
             $result['USER_NUM'] = $arUser['ID'];
             $result['PROPS_ELEMENT']['PROPERTY_files'] = $files;


             return $result;
//         }else{
//             LocalRedirect("/user/employees/");
//         }


    }
    public function  getDemoContent($idElement){

            $hl = new Hl('Democontentpiall');

            if ($hl->obj !== null) {
                $obj = $hl->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID'  , 'UF_DATA_START_STRING','UF_DATA_END_STRING','UF_BEZNAL','UF_NDS','UF_NALL','UF_START_ADDRESS_STREET','UF_COUNT_TECH'
                        ],
                        'filter' => [
                            '=UF_ITEM_ID' => $idElement,
                        ],
                        'limit' => 1
                    ]
                );
                $resultStatus = $get->fetch();


                $result['c'] = $resultStatus;
                return  $result['c'];

            }
        }

    public function get()
    {

        $result = [];

        $city = new City();
        if ($this->cityCode) {
            $this->cityParams = $city->getByCode($this->cityCode);
            if ($this->cityParams['default'] > 0) {
                $this->defaultRedirect = true;
            }
        } else {
            $this->cityParams = $city->getDefault();
        }

        if (!$this->defaultRedirect && $this->iBlockType && $this->iBlockCode && $this->itemCode && $this->itemId) {
            $cache = Application::getInstance()->getCache();
            $cache_time = $this->ttl;
            $cache_id = md5($this->cityCode . $this->iBlockType . $this->iBlockCode . $this->itemCode . $this->itemId . $this->filteredUserId);
            $cache_path = '/' . DSPI . '/items';

            $taggedCache = Application::getInstance()->getTaggedCache();


            if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {

                $res = $cache->getVars();

                if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                    $result = $res[$cache_id];

                    $el = new \CIBlockElement();


                    $arSelect = Array("ID", "NAME", "PROPERTY___hidden_moderation" , "PROPERTY___hidden_moderation_reason");
                    $arFilter = Array("IBLOCK_ID"=>$result['UF_IBLOCK_ID'], "ID" => $result['UF_ID'],  "ACTIVE"=>"Y");
                    $res = $el->GetList(Array(), $arFilter, false, false, $arSelect);
                    while($ob = $res->GetNextElement())
                    {
                        $arFields = $ob->GetFields();

                        $result['MODERATION'] = $arFields['PROPERTY___HIDDEN_MODERATION_VALUE'];
                        $result['MODERATION_REASON'] = $arFields['~PROPERTY___HIDDEN_MODERATION_REASON_VALUE']['TEXT'];
                    }




                }
            } else {


                $this->iBlockId = Utils::getIBlockIdByType(
                    str_replace('-', '_', 'democontent2_pi_' . $this->iBlockType), $this->iBlockCode
                );
                if ($this->iBlockId) {
                    $taggedCache->startTagCache($cache_path);
                    $taggedCache->registerTag('iblock_id_' . $this->iBlockId);
                    $taggedCache->registerTag('element_id_' . $this->itemId);
                    $taggedCache->registerTag('iblock_id_' . $this->iBlockId . '_city_id_' . $this->cityParams['id']);

                    $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($this->iBlockId))));

                    $hl = new Hl($tableName);


                    if ($hl->obj !== null) {
                        $obj = $hl->obj;
                        try {
                            $filter = [
                                '=UF_ID' => $this->itemId,
                                '=UF_CODE' => $this->itemCode . '-' . $this->itemId,
                                '=UF_IBLOCK_TYPE' => str_replace('_', '-', $this->iBlockType),
                                '=UF_IBLOCK_CODE' => $this->iBlockCode
                            ];

                            if ($this->cityCode) {
                                $filter['=UF_CITY'] = $this->cityParams['id'];
                            }

                            if ($this->filteredUserId > 0) {
                                $filter['=UF_USER_ID'] = $this->filteredUserId;
                            } else {
//                                $filter = array_merge(
//                                    $filter,
//                                    [
//                                        '=UF_ACTIVE' => 1,
//                                        '=UF_MODERATION' => 0,
//                                        '>UF_PAYED' => 0
//                                    ]
//                                );
//
//                                $filter[] = [
//                                    'LOGIC' => 'AND',
//                                    [
//                                        'LOGIC' => 'OR',
//                                        [
//                                            '=UF_STATUS' => 1
//                                        ],
//                                        [
//                                            '=UF_STATUS' => 4
//                                        ]
//                                    ]
//                                ];
                            }

                            $get = $obj::getList(
                                [
                                    'select' => ['*'],
                                    'filter' => $filter,
                                    'limit' => 1
                                ]
                            );
                            $result = $get->fetch();


                            if(!empty($_POST['CODETABLE'])){

                                $tableName_State = $_POST['CODETABLE'];
                                $hl = new Hl($tableName_State);

                                if ($hl->obj !== null) {
                                    $obj = $hl->obj;
                                    $get = $obj::getList(
                                        [
                                            'select' => [
                                                'ID'  , 'UF_DATA_START_STRING','UF_DATA_END_STRING','UF_BEZNAL','UF_NDS','UF_NALL'
                                            ],
                                            'filter' => [
                                                '=UF_ITEM_ID' => $this->arResult['UF_ITEM_ID'],
                                            ],
                                            'limit' => 1
                                        ]
                                    );
                                    $resultStatus = $get->fetch();


                                    $result['c'] = $resultStatus;

                                }
                            }

                            $result['UF_NAME'] = strip_tags($result['UF_NAME']);
                            $result['UF_DESCRIPTION'] = strip_tags($result['UF_DESCRIPTION']);

                            $el = new \CIBlockElement();


                            $arSelect = Array("ID", "NAME", "PROPERTY___hidden_moderation" , "PROPERTY___hidden_moderation_reason");
                            $arFilter = Array("IBLOCK_ID"=>  $this->iBlockId , "ID" => $this->itemId,  "ACTIVE"=>"Y");
                            $res = $el->GetList(Array(), $arFilter, false, false, $arSelect);
                            while($ob = $res->GetNextElement())
                            {

                                $arFields = $ob->GetFields();

                                $result['MODERATION'] = $arFields['PROPERTY___HIDDEN_MODERATION_VALUE'];
                                $result['MODERATION_REASON'] = $arFields['~PROPERTY___HIDDEN_MODERATION_REASON_VALUE']['TEXT'];
//                                pre($arFields);
                            }



                            if (isset($result['UF_ID'])) {
                                $taggedCache->registerTag(md5('user_' . intval($result['UF_USER_ID'])));
                                $meta = new ElementValues(intval($result['UF_IBLOCK_ID']), intval($result['UF_ID']));
                                $result['META'] = $meta->getValues();

                                foreach ($result['META'] as $metaKey => $metaValue) {
                                    $tempMeta = str_replace('#CITY_DECLENSION#', $this->cityParams['declension'], $metaValue);
                                    $tempMetaExplode = explode(' ', $tempMeta);
                                    $tempMeta = [];
                                    foreach ($tempMetaExplode as $t) {
                                        if (strlen($t) > 0) {
                                            $tempMeta[] = $t;
                                        }
                                    }

                                    $result['META'][$metaKey] = implode(' ', $tempMeta);
                                }
                            }

                            if (intval($result['UF_CITY']) !== intval($this->cityParams['id'])) {
                                $this->cityParams = $city->getById(intval($result['UF_CITY']));
                                $this->cityRedirect = 'city-' . $this->cityParams['code'];
                            }
                        } catch (\Exception $e) {
                        }
                    }

                    if ($cache_time > 0) {
                        $cache->startDataCache($cache_time, $cache_id, $cache_path);
                        if (!count($result) || strlen($this->cityRedirect) > 0) {
                            $cache->abortDataCache();
                            $taggedCache->abortTagCache();
                        }
                        $cache->endDataCache([$cache_id => $result]);
                        $taggedCache->endTagCache();
                    }
                }
            }

        }

        return $result;
    }

    public function approve()
    {
        if ($this->itemId > 0) {
            \CIBlockElement::SetPropertyValuesEx($this->itemId, 0, ['__hidden_moderation' => 0]);
            $el = new \CIBlockElement();
            $el->Update($this->itemId, ['TIMESTAMP_X' => DateTime::createFromTimestamp(time())]);
            unset($el);
        }
    }
    public function publicUserSet(){
        $el = new \CIBlockElement;
        $PRODUCT_ID = $_POST['UF_ITEM_ID'];  // изменяем элемент с кодом (ID)
        $IBLOCK_ID = $_POST['IBLOCK_ID_EL_HL'];
        $PROP = [];
        $PROP['__hidden_moderation'] = "0";
        $PROP['__hidden_status'] = "0";


        $res = $el->SetPropertyValuesEx($PRODUCT_ID, $IBLOCK_ID , $PROP);




        $hl = new Hl('democontentpiall');
        $obj = $hl->obj;
        $rows = array(
            "UF_MODERATION" => 0,
            "UF_STATUS" => 0
        );
        $result = $obj::update($_POST['ID_EL_HL'] , $rows);
    }
    public function approveModeration($itemId , $idStatusPial , $iblockId , $userItemId)
    {

        $this->itemId = $itemId;
        if ($itemId > 0) {
            \CIBlockElement::SetPropertyValuesEx($itemId, 0, ['__hidden_moderation' => (string) 0]);
            $el = new \CIBlockElement();
            $el->Update($this->itemId, ['TIMESTAMP_X' => DateTime::createFromTimestamp(time())]);
            unset($el);

            $hl = new Hl('democontentpiall');
            $obj = $hl->obj;
            $rows = array(
                "UF_STATUS" => 0,
                "UF_MODERATION" => 0
            );
            $result = $obj::update($idStatusPial , $rows);

            $arrItem = [
                "taskId" => $itemId,
                "iBlockId" =>  $iblockId
            ];
            $userItemIdMess  = array(
              0 => $userItemId
            );
            //удаляю у всех админов уведомления по заявк этой
            \Democontent2\Pi\Notifications::deleteForAdmin($itemId);
            //  текущему пользователю отправляю что он прошел модерацию
            $allItems[] =  \Democontent2\Pi\Notifications::addNewNotificMess( $userItemIdMess  , 'moderationCompleted' , $arrItem ,  $itemId );
            LocalRedirect(SITE_DIR . 'user/moderation/', true);
        }
    }
    public function Yremove(){
        $el = new \CIBlockElement;
        $PRODUCT_ID = $_POST['UF_ITEM_ID'];  // изменяем элемент с кодом (ID)
        $IBLOCK_ID = $_POST['IBLOCK_ID_EL_HL'];
        $PROP = [];
        $PROP['__hidden_moderation'] = 5;
        $PROP['__hidden_status'] = 5;


        $res = $el->SetPropertyValuesEx($PRODUCT_ID, $IBLOCK_ID , $PROP);




        $hl = new Hl('democontentpiall');
        $obj = $hl->obj;
        $rows = array(
            "UF_MODERATION" => 5,
            "UF_STATUS" => 5
        );
        $result = $obj::update($_POST['ID_EL_HL'] , $rows);
    }

    public function YremoveTaskEnd(){

        \CIBlockElement::Delete($_POST['UF_ITEM_ID']);
        $hl = new Hl('democontentpiall');
        $obj = $hl->obj;
        $result = $obj::delete($_POST['ID_EL_HL'] );
    }

    public function repeatModeration( $data, $filesData )
    {
        //подбираю поля , обновляю заявку/элемент в базе , ставлю значение модерации 1
        $this->itemId = $_POST['itemId'];
        $el = new \CIBlockElement;

        $PROP = array();
        if(!empty($_POST['description'])) {


            $res = $el -> Update( $this->itemId ,array("DETAIL_TEXT" => $_POST['description']));

        }
        if(!empty($_POST['nameBeznal'])){
            $PROP['BEZNAL'] = $_POST['nameNal'];
        }
        if(!empty($_POST['nameNal'])){
            $PROP['NAL'] = $_POST['nameBeznal'];

        }
        if(!empty($_POST['contractPrice'])){
            $PROP['BEZ_NDS'] = $_POST['contractPrice'];

        }
       /* if($_POST['contractPrice'] == 0) {

            $PROP['BEZ_NDS'] = 343;

        };
       */
        $PROP['__hidden_moderation'] = 1;
        $files = [];
        if (isset($filesData['__files'])) {
            if (count($filesData['__files']['name']) > 0) {

                $i = 0;
                foreach ($filesData['__files']['name'] as $fileKey => $fileValue) {
                    $extension = Utils::getExtension($fileValue);
                    if (strlen($extension) > 0) {
                        $fileArray = \CFile::MakeFileArray($filesData['__files']['tmp_name'][$fileKey]);

                        $fileArray['description'] = $fileValue;
                        $fileArray['name'] = md5(microtime(true)) . '-' . ToLower(randString(rand(5, 10))) . $extension;
                        $files['n' . $i] = [
                            'VALUE' => $fileArray,
                            'DESCRIPTION' => $fileArray['description']
                        ];
                        $i++;
                    }
                }
            }
        }
        $PROP['files'] = $files;



        $PRODUCT_ID = $this->itemId;  // изменяем элемент с кодом (ID)

        $res = $el->SetPropertyValuesEx($PRODUCT_ID, $this->iBlockId , $PROP);

        $hl = new Hl('democontentpiall');
        $obj = $hl->obj;
        $rows = array(
            "UF_STATUS" => 1,
            "UF_MODERATION" => 1
        );
        $result = $obj::update($_POST['uf_id_item'] , $rows);




        $arrItem = [
            "taskId" => $PRODUCT_ID,
            "iBlockId" =>    $this->iBlockId
        ];
        $itemId = $PRODUCT_ID;
        $result = \Bitrix\Main\UserGroupTable::getList(array(
            'filter' => array('GROUP_ID'=>1,'USER.ACTIVE'=>'Y'),
            'select' => array('USER_ID','NAME'=>'USER.NAME','LAST_NAME'=>'USER.LAST_NAME'), // выбираем идентификатор п-ля, имя и фамилию
            'order' => array('USER.ID'=>'DESC'), // сортируем по идентификатору пользователя
        ));

        $arGroupUsers  = [];
        while ($arGroup = $result->fetch())
        {
            $arGroupUsers[] = $arGroup['USER_ID'];
        }
        $allItems[] =  \Democontent2\Pi\Notifications::addNewNotificMess( $arGroupUsers  , 'SuccessEditTask' , $arrItem , $itemId );


        unset($el);




    }



    public function remove()
    {

        if ($this->itemId > 0) {
            try {
                \CIBlockElement::Delete($this->itemId);
            } catch (\Exception $e) {
            }
        }
    }
    public  function refusal_app($item , $id_status){

        global $USER;
        if ($this->itemId > 0) {
            $html = "<ul class='refusal_app_form'>";

            if(!empty($_POST['notCorrect'])){
                $html = $html ."<li tag='notCorrect'>".$_POST['notCorrect']."</li>";

            }
            if(!empty($_POST['notCorrectFile'])){
                $html = $html ."<li tag='notCorrectFile'>".$_POST['notCorrectFile']."</li>";

            }
            if(!empty($_POST['notCorrectBadFiles'])){
                $html = $html . "<li tag='notCorrectBadFiles'>".$_POST['notCorrectBadFiles']."</li>";
            }
            if(!empty($_POST['notCorrectMoneySmall'])){
                $html = $html . "<li tag='notCorrectMoneySmall'>".$_POST['notCorrectMoneySmall']."</li>";
            }
            $html = $html . "</ul>";

            $value = array('VALUE'=>array('TYPE'=>'html', 'TEXT'=>$html));


            \CIBlockElement::SetPropertyValuesEx($this->itemId, false, array(
                '__hidden_moderation_reason' => $value ,
                '__hidden_moderation' => 2
            ));
            $el = new \CIBlockElement();
            $el->Update($this->itemId, ['TIMESTAMP_X' => DateTime::createFromTimestamp(time())]);
//            Notifications
            //при отклонении создаю новую запись с 0


            $arrItem = [
                "taskId" =>  $this->itemId,
                "iBlockId" => $this->iBlockId
            ];
            $USER_ID = '';
            $db_props = $el->GetProperty($this->iBlockId, $this->itemId, array("sort" => "asc"), Array("CODE"=>"__hidden_user"));
            if($ar_props = $db_props->Fetch()){
                $USER_ID = IntVal($ar_props["VALUE"]);
            }

            $hl = new Hl('democontentpiall');
            $obj = $hl->obj;
            $rows = array(
                "UF_STATUS" => 2
            );
            $result = $obj::update($id_status , $rows);


            $allItems[] =  \Democontent2\Pi\Notifications::add($USER_ID , 'moderationRefusal' , $arrItem );

            unset($el);
        }

    }
    public  function refusal_app_moderation($itemId , $idIblock , $idStatus , $userId){
        $this->itemId = $itemId;
        $this->iBlockId = $idIblock;
        $id_status = $idStatus;
        global $USER;
        if ($this->itemId > 0) {
            $html = "<ul class='refusal_app_form'>";

            if(!empty($_POST['notCorrect'])){
                $html = $html ."<li tag='notCorrect'>".$_POST['notCorrect']."</li>";

            }
            if(!empty($_POST['notCorrectFile'])){
                $html = $html ."<li tag='notCorrectFile'>".$_POST['notCorrectFile']."</li>";

            }
            if(!empty($_POST['notCorrectBadFiles'])){
                $html = $html . "<li tag='notCorrectBadFiles'>".$_POST['notCorrectBadFiles']."</li>";
            }
            if(!empty($_POST['notCorrectMoneySmall'])){
                $html = $html . "<li tag='notCorrectMoneySmall'>".$_POST['notCorrectMoneySmall']."</li>";
            }
            $html = $html . "</ul>";

            $value = array('VALUE'=>array('TYPE'=>'html', 'TEXT'=>$html));


            \CIBlockElement::SetPropertyValuesEx($this->itemId, false, array(
                '__hidden_moderation_reason' => $value ,
                '__hidden_moderation' => 2
            ));
            $el = new \CIBlockElement();
            $el->Update($this->itemId, ['TIMESTAMP_X' => DateTime::createFromTimestamp(time())]);
//            Notifications
            //при отклонении создаю новую запись с 0


            $arrItem = [
                "taskId" =>  $this->itemId,
                "iBlockId" => $this->iBlockId
            ];
            $USER_ID = '';
            $db_props = $el->GetProperty($this->iBlockId, $this->itemId, array("sort" => "asc"), Array("CODE"=>"__hidden_user"));
            if($ar_props = $db_props->Fetch()){
                $USER_ID = IntVal($ar_props["VALUE"]);
            }

            $hl = new Hl('democontentpiall');
            $obj = $hl->obj;
            $rows = array(
                "UF_STATUS" => 2,
                "UF_MODERATION" => 2
            );
            $result = $obj::update($id_status , $rows);

            $userItemIdMess = array(
                0 => $userId
            );


            //удаляю у всех админов уведомления по заявк этой
            \Democontent2\Pi\Notifications::deleteForAdmin($itemId);
            //  текущему пользователю отправляю что он прошел модерацию
            $allItems[] =  \Democontent2\Pi\Notifications::addNewNotificMess( $userItemIdMess  , 'moderationRefusal' , $arrItem ,  $itemId );


//            $allItems[] =  \Democontent2\Pi\Notifications::add($userId , 'moderationRefusal' , $arrItem );

            unset($el);
        }

    }
    public function setCounter($id, $counter)
    {
        $this->setAllClass();
        if ($this->allClass !== null) {
            try {
                $obj = $this->allClass;
                $get = $obj::getList(
                    [
                        'select' => ['ID'],
                        'filter' => [
                            '=UF_ITEM_ID' => $id
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $obj::update(
                        $res['ID'],
                        [
                            'UF_COUNTER' => $counter
                        ]
                    );
                }
            } catch (\Exception $e) {

            }
        }
    }

    public function getMainParams()
    {
        $result = [];
        if ($this->iBlockId > 0 && $this->itemId > 0) {
            try {
                $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($this->iBlockId))));
                $hl = new Hl($tableName);
                if ($hl->obj !== null) {
                    $obj = $hl->obj;
                    $get = $obj::getList(
                        [
                            'select' => [
                                'UF_NAME',
                                'UF_USER_ID',
                                'UF_IBLOCK_TYPE',
                                'UF_IBLOCK_CODE',
                                'UF_CODE',
                            ],
                            'filter' => [
                                '=UF_ID' => $this->itemId,
                                '=UF_IBLOCK_ID' => $this->iBlockId
                            ],
                            'limit' => 1
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $result = $res;
                    }
                }

            } catch (\Exception $e) {
            }
        }

        return $result;
    }

    public function getTaskNameAndOwner()
    {
        $result = [];
        if ($this->iBlockId > 0 && $this->itemId > 0) {
            try {
                $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($this->iBlockId))));
                $hl = new Hl($tableName);
                if ($hl->obj !== null) {
                    $obj = $hl->obj;
                    $get = $obj::getList(
                        [
                            'select' => ['UF_NAME', 'UF_USER_ID'],
                            'filter' => [
                                '=UF_ID' => $this->itemId,
                                '=UF_IBLOCK_ID' => $this->iBlockId
                            ],
                            'limit' => 1
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $result = [
                            'name' => $res['UF_NAME'],
                            'userId' => intval($res['UF_USER_ID'])
                        ];
                    }
                }

            } catch (\Exception $e) {
            }
        }

        return $result;
    }

    public function delete($id, $iBlockId)
    {
        try {
            $taggedCache = Application::getInstance()->getTaggedCache();
            $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($iBlockId))));
            $hl = new Hl($tableName);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                $get = $obj::getList(
                    [
                        'select' => ['ID'],
                        'filter' => [
                            '=UF_ID' => $id,
                            '=UF_IBLOCK_ID' => $iBlockId
                        ]
                    ]
                );
                while ($res = $get->fetch()) {
                    $delete = $obj::delete(intval($res['ID']));
                    if ($delete->isSuccess()) {
                        $stat = new Stat(intval($id), intval($iBlockId));
                        $stat->remove();

                        $responseCheckList = new \Democontent2\Pi\CheckList\Response(intval($id));
                        $responseCheckList->deleteAll();

                        $taggedCache->clearByTag('element_id_' . $id);
                        Manager::deleteElementIndex($iBlockId, $id);
                    }
                }
            }

            try {
                $this->setAllClass();
                if ($this->allClass !== null) {
                    $obj = $this->allClass;
                    $get = $obj::getList(
                        [
                            'select' => ['ID'],
                            'filter' => [
                                '=UF_ITEM_ID' => $id,
                                '=UF_IBLOCK_ID' => $iBlockId
                            ]
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $obj::delete(intval($res['ID']));
                    }
                }
            } catch (\Exception $e) {

            }

            try {
                $this->setStagesClass();
                if ($this->stagesClass !== null) {
                    $obj = $this->stagesClass;
                    $get = $obj::getList(
                        [
                            'select' => ['ID'],
                            'filter' => [
                                '=UF_TASK_ID' => $id
                            ]
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $obj::delete(intval($res['ID']));
                    }
                }
            } catch (\Exception $e) {

            }
        } catch (\Exception $e) {
        }
    }

    public function update($id, $iBlockId)
    {
        try {
            $taggedCache = Application::getInstance()->getTaggedCache();
            $el = new \CIBlockElement();
            $get = $el->GetList(
                [],
                [
                    '=ID' => $id,
                    '=IBLOCK_ID' => $iBlockId
                ],
                false,
                false,
                [
                    '*',
                    'PROPERTY_*'
                ]
            );

            while ($res = $get->GetNextElement()) {
                $fields = $res->GetFields();
                $properties = $res->GetProperties();

                if (!isset($properties['__hidden_status']) || !isset($properties['__hidden_status']['VALUE_XML_ID'])
                    || !strlen($properties['__hidden_status']['VALUE_XML_ID'])) {
                    $properties['__hidden_status']['VALUE_XML_ID'] = '__status_1';

                    try {
                        $statusEnums = \CIBlockPropertyEnum::GetList(
                            [
                                'SORT' => 'ASC'
                            ],
                            [
                                'IBLOCK_ID' => $this->iBlockId,
                                'CODE' => '__hidden_status'
                            ]
                        );
                        while ($statusEnumsRes = $statusEnums->GetNext()) {
                            if ($statusEnumsRes['XML_ID'] == '__status_1') {
                                \CIBlockElement::SetPropertyValuesEx(
                                    $fields['ID'],
                                    $fields['IBLOCK_ID'],
                                    [
                                        '__hidden_status' => $statusEnumsRes['ID']
                                    ]
                                );
                                break;
                            }
                        }
                    } catch (\Exception $e) {

                    }
                }

                $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($fields['IBLOCK_ID']))));
                $hl = new Hl($tableName);
                if ($hl->obj !== null) {
                    $obj = $hl->obj;
                    $getFromHl = $obj::getList(
                        [
                            'select' => [
                                'ID',
                                'UF_OLD_PRICE',
                                'UF_PRICE',
                                'UF_MODERATION'
                            ],
                            'filter' => [
                                '=UF_ID' => intval($fields['ID']),
                                '=UF_IBLOCK_ID' => intval($fields['IBLOCK_ID'])
                            ]
                        ]
                    );
                    while ($getFromHlRes = $getFromHl->fetch()) {
                        $location = [];
                        if ($properties['__hidden_location']['VALUE']) {
                            $ex = explode(',', $properties['__hidden_location']['VALUE']);
                            $location[] = floatval(trim($ex[0]));
                            $location[] = floatval(trim($ex[1]));
                        }

                        $disallowProperties = ['images'];
                        $formattedProperties = [];

                        if (isset($properties['__hidden_coordinates']['VALUE'])) {
                            if (is_array($properties['__hidden_coordinates']['VALUE']) && count($properties['__hidden_coordinates']['VALUE'])) {
                                $formattedProperties[] = [
                                    'route' => $properties['__hidden_coordinates']['VALUE']
                                ];
                            }
                        }

                        foreach ($properties as $k => $v) {
                            if (in_array($k, $disallowProperties)) {
                                continue;
                            }

                            preg_match_all('/__hidden_([a-zA-Z_]+)/m', $k, $matches, PREG_SET_ORDER, 0);
                            if (count($matches) > 0 && isset($matches[0][1])) {
                                continue;
                            }

                            switch ($v['PROPERTY_TYPE']) {
                                case 'N':
                                case 'S':
                                case 'L':
                                    if ($v['VALUE']) {
                                        $formattedProperties[] = [
                                            'name' => $v['NAME'],
                                            'value' => $v['VALUE']
                                        ];
                                    }
                                    break;
                            }
                        }

                        if (!strlen(trim($fields['CODE']))) {
                            $code = ToLower(\CUtil::translit($fields['NAME'], 'ru', Utils::tr())) . '-' . $fields['ID'];
                        } else {
                            $code = trim($fields['CODE']) . '-' . $fields['ID'];
                        }

                        $oldPrice = floatval($getFromHlRes['UF_OLD_PRICE']);
                        if ($oldPrice > 0) {
                            if (floatval($properties['__hidden_price']['VALUE']) >= $oldPrice) {
                                $oldPrice = 0;
                            }
                        } else {
                            if (floatval($getFromHlRes['UF_PRICE']) > floatval($properties['__hidden_price']['VALUE'])) {
                                $oldPrice = floatval($getFromHlRes['UF_PRICE']);
                            }
                        }

                        $obj::update(
                            $getFromHlRes['ID'],
                            [
                                'UF_ID' => intval($fields['ID']),
                                'UF_IBLOCK_TYPE' => str_replace('_', '-', str_replace('democontent2_pi_', '', $fields['IBLOCK_TYPE_ID'])),
                                'UF_IBLOCK_CODE' => $fields['IBLOCK_CODE'],
                                'UF_IBLOCK_ID' => intval($fields['IBLOCK_ID']),
                                'UF_CITY' => intval($properties['__hidden_city']['VALUE']),
                                'UF_ACTIVE_FROM' => ($fields['ACTIVE_FROM']) ? DateTime::createFromTimestamp(strtotime($fields['ACTIVE_FROM'])) : false,
                                'UF_ACTIVE_TO' => ($fields['ACTIVE_TO']) ? DateTime::createFromTimestamp(strtotime($fields['ACTIVE_TO'])) : false,
                                'UF_CODE' => $code,
                                'UF_NAME' => trim($fields['NAME']),
                                'UF_PRICE' => floatval($properties['__hidden_price']['VALUE']),
                                'UF_OLD_PRICE' => $oldPrice,
                                'UF_USER_ID' => intval($properties['__hidden_user']['VALUE']),
                                'UF_DESCRIPTION' => $fields['DETAIL_TEXT'],
                                'UF_LAT' => (count($location) > 0) ? $location[0] : 0,
                                'UF_LONG' => (count($location) > 0) ? $location[1] : 0,
                                'UF_IMAGE_ID' => (intval($fields['DETAIL_PICTURE']) > 0) ? intval($fields['DETAIL_PICTURE']) : 0,
                                'UF_IMAGES' => (is_array($properties['images']['VALUE']) && count($properties['images']['VALUE']) > 0) ? serialize($properties['images']['VALUE']) : serialize([]),
                                'UF_FILES' => (is_array($properties['files']['VALUE']) && count($properties['files']['VALUE']) > 0) ? serialize($properties['files']['VALUE']) : serialize([]),
                                'UF_HIDDEN_FILES' => (is_array($properties['hidden_files']['VALUE']) && count($properties['hidden_files']['VALUE']) > 0) ? serialize($properties['hidden_files']['VALUE']) : serialize([]),
                                'UF_PROPERTIES' => (count($formattedProperties) > 0) ? serialize($formattedProperties) : '',
                                'UF_MODERATION' => $properties['__hidden_moderation']['VALUE'],
                                'UF_ACTIVE' => ($fields['ACTIVE'] == 'Y') ? 1 : 0,
                                'UF_ARCHIVE' => (intval($properties['__hidden_archive']['VALUE']) > 0) ? 1 : 0,
                                'UF_PAYED' => (intval($properties['__hidden_payed']['VALUE']) > 0) ? 1 : 0,
                                'UF_QUICKLY_START' => ($properties['__hidden_quickly_start']['VALUE']) ? DateTime::createFromTimestamp(strtotime($properties['__hidden_quickly_start']['VALUE'])) : false,
                                'UF_QUICKLY_END' => ($properties['__hidden_quickly_end']['VALUE']) ? DateTime::createFromTimestamp(strtotime($properties['__hidden_quickly_end']['VALUE'])) : false,
                                'UF_BEGIN_WITH' => ($properties['__hidden_begin_with']['VALUE']) ? DateTime::createFromTimestamp(strtotime($properties['__hidden_begin_with']['VALUE'])) : false,
                                'UF_RUN_UP' => ($properties['__hidden_run_up']['VALUE']) ? DateTime::createFromTimestamp(strtotime($properties['__hidden_run_up']['VALUE'])) : false,
                                'UF_CANCEL_DATE' => ($properties['__hidden_cancel_date']['VALUE']) ? DateTime::createFromTimestamp(strtotime($properties['__hidden_cancel_date']['VALUE'])) : false,
                                'UF_RETURN_DATE' => ($properties['__hidden_return_date']['VALUE']) ? DateTime::createFromTimestamp(strtotime($properties['__hidden_return_date']['VALUE'])) : false,
                                'UF_CANCEL_REASON' => (isset($properties['__hidden_cancel_reason']['VALUE']['TEXT'])) ? htmlspecialcharsbx(trim($properties['__hidden_cancel_reason']['VALUE']['TEXT'])) : '',
                                'UF_STATUS' => $properties['__hidden_moderation']['VALUE']

                            ]
                        );

                        if (strtotime($fields['ACTIVE_TO']) <= time()) {
                            $services = new Services('quickly');
                            $services->delete(
                                intval($fields['IBLOCK_ID']),
                                intval($fields['ID'])
                            );
                        } else {
                            if ($properties['__hidden_quickly_end']['VALUE']) {
                                if (strtotime($properties['__hidden_quickly_end']['VALUE']) > time()) {
                                    $services = new Services('quickly');
                                    $services->quickly(
                                        intval($fields['IBLOCK_ID']),
                                        intval($fields['ID']),
                                        intval($properties['__hidden_city']['VALUE']),
                                        [
                                            'name' => trim($fields['NAME']),
                                            'code' => $code,
                                            'price' => floatval($properties['__hidden_price']['VALUE']),
                                            'iBlockType' => str_replace(
                                                '_',
                                                '-',
                                                str_replace('democontent2_pi_', '', $fields['IBLOCK_TYPE_ID'])
                                            ),
                                            'iBlockCode' => $fields['IBLOCK_CODE']
                                        ]
                                    );
                                } else {
                                    $services = new Services('quickly');
                                    $services->delete(
                                        intval($fields['IBLOCK_ID']),
                                        intval($fields['ID'])
                                    );
                                }
                            } else {
                                $services = new Services('quickly');
                                $services->delete(
                                    intval($fields['IBLOCK_ID']),
                                    intval($fields['ID'])
                                );
                            }
                        }

                        Manager::updateElementIndex($fields['IBLOCK_ID'], $fields['ID']);
                        $taggedCache->clearByTag('element_id_' . $fields['ID']);
                        $taggedCache->clearByTag('iblock_id_' . $fields['IBLOCK_ID'] . '_city_id_' . intval($properties['__hidden_city']['VALUE']));

                        if (ModuleManager::isModuleInstalled('search')) {
                            if (Loader::includeModule('search')) {
                                if (intval($properties['__hidden_moderation']['VALUE']) > 0) {
                                    \CSearch::DeleteIndex('iblock', intval($fields['ID']));
                                }
                            }
                        }

                        try {
                            $this->setAllClass();
                            if ($this->allClass !== null) {
                                $obj = $this->allClass;
                                $get_ = $obj::getList(
                                    [
                                        'select' => ['ID'],
                                        'filter' => [
                                            '=UF_ITEM_ID' => $fields['ID'],
                                            '=UF_IBLOCK_ID' => $fields['IBLOCK_ID']
                                        ]
                                    ]
                                );
                                while ($res_ = $get_->fetch()) {
                                    $obj::update(
                                        $res_['ID'],
                                        [
                                            'UF_USER_ID' => intval($properties['__hidden_user']['VALUE']),
                                            'UF_ITEM_ID' => intval($fields['ID']),
                                            'UF_IBLOCK_TYPE' => str_replace('_', '-', str_replace('democontent2_pi_', '', $fields['IBLOCK_TYPE_ID'])),
                                            'UF_IBLOCK_CODE' => $fields['IBLOCK_CODE'],
                                            'UF_IBLOCK_ID' => intval($fields['IBLOCK_ID']),
                                            'UF_CITY' => intval($properties['__hidden_city']['VALUE']),
                                            'UF_BEGIN_WITH' => false,
                                            'UF_RUN_UP' => false,
                                            'UF_CODE' => $code,
                                            'UF_NAME' => trim($fields['NAME']),
                                            'UF_PRICE' => floatval($properties['__hidden_price']['VALUE']),
                                            'UF_DESCRIPTION' => $fields['DETAIL_TEXT'],
                                            'UF_MODERATION' => $properties['__hidden_moderation']['VALUE'],
                                            'UF_QUICKLY_START' => ($properties['__hidden_quickly_start']['VALUE']) ? DateTime::createFromTimestamp(strtotime($properties['__hidden_quickly_start']['VALUE'])) : false,
                                            'UF_QUICKLY_END' => ($properties['__hidden_quickly_end']['VALUE']) ? DateTime::createFromTimestamp(strtotime($properties['__hidden_quickly_end']['VALUE'])) : false,
                                            'UF_STATUS' => $properties['__hidden_moderation']['VALUE']
                                        ]
                                    );
                                }
                            }
                        } catch (\Exception $e) {

                        }

                        if (!intval($properties['__hidden_moderation']['VALUE'])) {
                            if (intval($getFromHlRes['UF_MODERATION'])) {
//                                Notifications::add(
//                                    intval($properties['__hidden_user']['VALUE']),
//                                    'moderationCompleted',
//                                    [
//                                        'taskId' => intval($fields['ID']),
//                                        'iBlockId' => intval($fields['IBLOCK_ID']),
//                                    ]
//                                );
                                $fb = new FireBase(intval($properties['__hidden_user']['VALUE']));
                                $fb->webPush([
                                    'title' => Loc::getMessage('PUSH_MODERATION_COMPLETED_TITLE'),
                                    'body' => Loc::getMessage('PUSH_MODERATION_COMPLETED_BODY', ['{{TASK_ID}}' => intval($fields['ID'])])
                                ]);
                                unset($fb);

                                if ((time() - strtotime($fields['DATE_CREATE'])) >= (15 * 6)) {
                                    $pushQueue = new PushQueue();
                                    $pushQueue->put(intval($fields['ID']), intval($fields['IBLOCK_ID']));
                                    unset($pushQueue);
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
        }
    }

    public function add($id, $iBlockId)
    {

        try {
            $el = new \CIBlockElement();
            $get = $el->GetList(
                [],
                [
                    '=ID' => $id,
                    '=IBLOCK_ID' => $iBlockId
                ],
                false,
                false,
                [
                    '*',
                    'PROPERTY_*'
                ]
            );
            while ($res = $get->GetNextElement()) {
                $fields = $res->GetFields();
                $properties = $res->GetProperties();
                $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($fields['IBLOCK_ID']))));
                $hl = new Hl($tableName);
                if ($hl->obj !== null) {
                    $location = [];
                    if ($properties['__hidden_location']['VALUE']) {
                        $ex = explode(',', $properties['__hidden_location']['VALUE']);
                        $location[] = floatval(trim($ex[0]));
                        $location[] = floatval(trim($ex[1]));
                    }

                    $disallowProperties = [
                        'images',
                        'files'
                    ];
                    $formattedProperties = [];

                    if (isset($properties['__hidden_coordinates']['VALUE'])) {
                        if (is_array($properties['__hidden_coordinates']['VALUE']) && count($properties['__hidden_coordinates']['VALUE'])) {
                            $formattedProperties[] = [
                                'route' => $properties['__hidden_coordinates']['VALUE']
                            ];
                        }
                    }

                    foreach ($properties as $k => $v) {
                        if (in_array($k, $disallowProperties)) {
                            continue;
                        }

                        preg_match_all('/__hidden_([a-zA-Z_]+)/m', $k, $matches, PREG_SET_ORDER, 0);
                        if (count($matches) > 0 && isset($matches[0][1])) {
                            continue;
                        }

                        switch ($v['PROPERTY_TYPE']) {
                            case 'N':
                            case 'S':
                            case 'L':
                                if ($v['VALUE']) {
                                    $formattedProperties[] = array(
                                        'name' => $v['NAME'],
                                        'value' => $v['VALUE']
                                    );
                                }
                                break;
                        }
                    }

                    if (!isset($properties['__hidden_status']) || !isset($properties['__hidden_status']['VALUE_XML_ID'])
                        || !strlen($properties['__hidden_status']['VALUE_XML_ID'])) {
                        $properties['__hidden_status']['VALUE_XML_ID'] = '__status_1';

                        try {
                            $statusEnums = \CIBlockPropertyEnum::GetList(
                                [
                                    'SORT' => 'ASC'
                                ],
                                [
                                    'IBLOCK_ID' => $this->iBlockId,
                                    'CODE' => '__hidden_status'
                                ]
                            );
                            while ($statusEnumsRes = $statusEnums->GetNext()) {
                                if ($statusEnumsRes['XML_ID'] == '__status_1') {
                                    \CIBlockElement::SetPropertyValuesEx(
                                        $fields['ID'],
                                        $fields['IBLOCK_ID'],
                                        [
                                            '__hidden_status' => $statusEnumsRes['ID']
                                        ]
                                    );
                                    break;
                                }
                            }
                        } catch (\Exception $e) {

                        }
                    }
                    $dateTimeStart = MakeTimeStamp($properties['__hidden_begin_with']['VALUE'] , "DD.MM.YYYY HH:MI:SS");
                    $dateTimeEnd = MakeTimeStamp($properties['__hidden_run_up']['VALUE'] , "DD.MM.YYYY");

                    $hiddenUser = $properties['__hidden_user']['VALUE'];
                    $getUserPoId = \CUser::GetByID($hiddenUser);
                    $getUserPoIdMess = $getUserPoId->Fetch();
                    $resCIty = \CIBlockElement::GetByID($properties['__hidden_city']['VALUE']);
                    global $cityName;
                    $cityName = '';
                    if($resCItyMess = $resCIty->GetNext()){
                        $cityName = $resCItyMess['NAME'];
                    }
                    global $companyId;
                    $companyId = '';
                    if(!empty($properties['__hidden_user']['VALUE'])){



                        if(!empty($getUserPoIdMess['UF_ID_COMPANY'])){
                            $companyId = $getUserPoIdMess['UF_ID_COMPANY'];
                        }else{
                            $companyId = "";
                        }

                    }

                    global $countDayBl;
                    $countDayBl = '';
                    if(!empty($properties['__hidden_begin_with']['VALUE']) && !empty($properties['__hidden_run_up']['VALUE'] ) ){
                        $dateStartObj = \Bitrix\Main\Type\DateTime::createFromTimestamp(MakeTimeStamp($properties['__hidden_begin_with']['VALUE'], "DD.MM.YYYY HH:MI:SS"));
                        $dateEndObj = \Bitrix\Main\Type\DateTime::createFromTimestamp(MakeTimeStamp($properties['__hidden_run_up']['VALUE'], "DD.MM.YYYY"));


                        $dateStartString = $dateStartObj->format("d.m.Y");
                        $dateEndString = $dateEndObj->format("d.m.Y");



                        $date1 = new \DateTime($dateStartString);
                        $date2 = new \DateTime($dateEndString);

                        $interval = $date1->diff($date2);

                        $countDayBl = $interval->d + 1;


                    }else{
                        $countDayBl = '';
                    }

                    global $coordsMap;
                    $coordsMap = $properties['__hidden_coordinates']['VALUE'][0];


                    //DemocontentpiallГЫ
                    $code = ToLower(\CUtil::translit($fields['NAME'], 'ru', Utils::tr())) . '-' . $fields['ID'];
                    $obj = $hl->obj;
                    $obj::add(
                        [
                            'UF_START_ADDRESS_STREET' => $_POST['addressStreet'],
                            'UF_MAP_COORDINATES' => $coordsMap,
                            'UF_COUNT_DAY_SET' => $countDayBl,
                            'UF_COMPANY_ID' => $companyId,
                            'UF_ID' => intval($fields['ID']),
                            'UF_IBLOCK_TYPE' => str_replace('_', '-', str_replace('democontent2_pi_', '', $fields['IBLOCK_TYPE_ID'])),
                            'UF_IBLOCK_CODE' => $fields['IBLOCK_CODE'],
                            'UF_IBLOCK_ID' => intval($fields['IBLOCK_ID']),
                            'UF_SECTION_ID' => 0,
                            'UF_CITY' => intval($properties['__hidden_city']['VALUE']),
                            'UF_CITY_NAME' => $cityName,
                            'UF_ACTIVE_FROM' => ($fields['ACTIVE_FROM']) ? DateTime::createFromTimestamp(strtotime($fields['ACTIVE_FROM'])) : false,
                            'UF_ACTIVE_TO' => ($fields['ACTIVE_TO']) ? DateTime::createFromTimestamp(strtotime($fields['ACTIVE_TO'])) : false,
                            'UF_TIMESTAMP' => DateTime::createFromTimestamp(strtotime($fields['TIMESTAMP_X'])),
                            'UF_DATE_CREATE' => DateTime::createFromTimestamp(strtotime($fields['DATE_CREATE'])),
                            'UF_CODE' => $code,
                            'UF_NAME' => trim($fields['NAME']),
                            'UF_PRICE' => floatval($properties['__hidden_price']['VALUE']),
                            'UF_OLD_PRICE' => 0,
                            'UF_USER_ID' => intval($properties['__hidden_user']['VALUE']),
                            'UF_DESCRIPTION' => $fields['DETAIL_TEXT'],
                            'UF_LAT' => (count($location) > 0) ? $location[0] : 0,
                            'UF_LONG' => (count($location) > 0) ? $location[1] : 0,
                            'UF_IMAGE_ID' => (intval($fields['DETAIL_PICTURE']) > 0) ? intval($fields['DETAIL_PICTURE']) : 0,
                            'UF_IMAGES' => (is_array($properties['images']['VALUE']) && count($properties['images']['VALUE']) > 0) ? serialize($properties['images']['VALUE']) : serialize([]),
                            'UF_FILES' => (is_array($properties['files']['VALUE']) && count($properties['files']['VALUE']) > 0) ? serialize($properties['files']['VALUE']) : serialize([]),
                            'UF_HIDDEN_FILES' => (is_array($properties['hidden_files']['VALUE']) && count($properties['hidden_files']['VALUE']) > 0) ? serialize($properties['hidden_files']['VALUE']) : serialize([]),
                            'UF_PROPERTIES' => (count($formattedProperties) > 0) ? serialize($formattedProperties) : '',
                            'UF_MODERATION' => intval($properties['__hidden_moderation']['VALUE']) ,
                            'UF_ACTIVE' => ($fields['ACTIVE'] == 'Y') ? 1 : 0,
                            'UF_ARCHIVE' => 0,
                            'UF_PAYED' => (intval($properties['__hidden_payed']['VALUE']) > 0) ? 1 : 0,

                            'UF_BEGIN_WITH' => ConvertDateTime($properties['__hidden_begin_with']['VALUE'], "DD.MM.YYYY")." 00:00:00",
                            'UF_RUN_UP' => ConvertDateTime($properties['__hidden_run_up']['VALUE'], "DD.MM.YYYY")." 23:59:59",

                            'UF_DATA_START_STRING' => $properties['__hidden_begin_with']['VALUE'],
                            'UF_DATA_END_STRING' => $properties['__hidden_run_up']['VALUE'],

                            'UF_COUNTER' => 0,
                            'UF_RESPONSE_COUNT' => 0,
                            'UF_SAFE' => (intval($properties['__hidden_security']['VALUE']) > 0) ? 1 : 0,
                            'UF_STATUS' => Utils::status($properties['__hidden_status']['VALUE_XML_ID']),
                            'UF_BEZNAL' => (string) $properties['BEZNAL']['VALUE'],
                            'UF_NALL' => (string) $properties['NAL']['VALUE'],
                            'UF_NDS' => (string)$properties['BEZ_NDS']['VALUE'],
                            'UF_NAME_CREATED' => $getUserPoIdMess['NAME'],
                            'UF_FEMALE_CREATED' => $getUserPoIdMess['LAST_NAME'],

                            'UF_BEZNAL_NUMBER' => (int) $properties['BEZNAL']['VALUE'],
                            'UF_NALL_NUMBER' => (int) $properties['NAL']['VALUE'],
                            'UF_NDS_NUMBER' =>  (int) $properties['BEZ_NDS']['VALUE'],
                            'UF_COUNT_TECH' =>  (int)  $GLOBALS['COUNT_TECH'],

                        ]
                    );

                    Application::getInstance()->getTaggedCache()
                        ->clearByTag('iblock_id_' . $fields['IBLOCK_ID'] . '_city_id_' . intval($properties['__hidden_city']['VALUE']));

                    if (ModuleManager::isModuleInstalled('search')) {
                        if (Loader::includeModule('search')) {
                            if (intval($properties['__hidden_moderation']['VALUE']) > 0) {
                                \CSearch::DeleteIndex('iblock', intval($fields['ID']));
                            }
                        }
                    }
                }

                Manager::updateElementIndex($fields['IBLOCK_ID'], $fields['ID']);

                $dateTimeStart = MakeTimeStamp($properties['__hidden_begin_with']['VALUE'] , "DD.MM.YYYY HH:MI:SS");
                $dateTimeEnd = MakeTimeStamp($properties['__hidden_run_up']['VALUE'] , "DD.MM.YYYY");

                $hiddenUser = $properties['__hidden_user']['VALUE'];
                $getUserPoId = \CUser::GetByID($hiddenUser);
                $getUserPoIdMess = $getUserPoId->Fetch();
                global $cityName;

                try {
                    $this->setAllClass();
                    if ($this->allClass !== null) {
                        $obj = $this->allClass;
                        global $companyId;
                        global $countDayBl;
                        global $coordsMap;
                        $obj::add(
                            [
                                'UF_START_ADDRESS_STREET' => $_POST['addressStreet'],
                                'UF_MAP_COORDINATES' => $coordsMap,
                                'UF_COUNT_DAY_SET' => $countDayBl,
                                'UF_COMPANY_ID' => $companyId,
                                'UF_USER_ID' => intval($properties['__hidden_user']['VALUE']),
                                'UF_ITEM_ID' => intval($fields['ID']),
                                'UF_IBLOCK_TYPE' => str_replace('_', '-', str_replace('democontent2_pi_', '', $fields['IBLOCK_TYPE_ID'])),
                                'UF_IBLOCK_CODE' => $fields['IBLOCK_CODE'],
                                'UF_IBLOCK_ID' => intval($fields['IBLOCK_ID']),
                                'UF_CITY' => intval($properties['__hidden_city']['VALUE']),
                                'UF_CITY_NAME' => $cityName,
                                'UF_CREATED_AT' => DateTime::createFromTimestamp(strtotime($fields['TIMESTAMP_X'])),


                                'UF_DATA_START_STRING' => $properties['__hidden_begin_with']['VALUE'],
                                'UF_DATA_END_STRING' => $properties['__hidden_run_up']['VALUE'],
                                'UF_BEGIN_WITH' => ConvertDateTime($properties['__hidden_begin_with']['VALUE'], "DD.MM.YYYY")." 00:00:00",
                                'UF_RUN_UP' => ConvertDateTime($properties['__hidden_run_up']['VALUE'], "DD.MM.YYYY")." 23:59:59",


                                'UF_CODE' => ToLower(\CUtil::translit($fields['NAME'], 'ru', Utils::tr())) . '-' . $fields['ID'],
                                'UF_NAME' => trim($fields['NAME']),
                                'UF_PRICE' => floatval($properties['__hidden_price']['VALUE']),
                                'UF_DESCRIPTION' => $fields['DETAIL_TEXT'],
                                'UF_RESPONSE_COUNT' => 0,
                                'UF_COUNTER' => 0,
                                'UF_SAFE' => (intval($properties['__hidden_security']['VALUE']) > 0) ? 1 : 0,
                                'UF_MODERATION' => intval($properties['__hidden_moderation']['VALUE']),
                                'UF_QUICKLY_START' => DateTime::createFromTimestamp(strtotime($properties['__hidden_begin_with']['VALUE'])),
                                'UF_QUICKLY_END' => DateTime::createFromTimestamp(strtotime($properties['__hidden_run_up']['VALUE'])),
                                'UF_STATUS' => Utils::status($properties['__hidden_status']['VALUE_XML_ID']),
                                'UF_BEZNAL' => (string) $properties['BEZNAL']['VALUE'],
                                'UF_NALL' => (string) $properties['NAL']['VALUE'],
                                'UF_NDS' => (string)$properties['BEZ_NDS']['VALUE'],
                                'UF_NAME_CREATED' => $getUserPoIdMess['NAME'],
                                'UF_FEMALE_CREATED' => $getUserPoIdMess['LAST_NAME'],

                                'UF_BEZNAL_NUMBER' => (int) $properties['BEZNAL']['VALUE'],
                                'UF_NALL_NUMBER' => (int) $properties['NAL']['VALUE'],
                                'UF_NDS_NUMBER' =>  (int) $properties['BEZ_NDS']['VALUE'],

                                'UF_COUNT_TECH' =>  (int)  $GLOBALS['COUNT_TECH'],
                            ]
                        );
                    }
                } catch (\Exception $e) {

                }
            }
        } catch (\Exception $e) {
        }
    }

    public function updateResponseCounter($id, $counter)
    {
        $result = false;

        if ($this->itemId > 0 && $this->iBlockId) {
            $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($this->iBlockId))));
            $hl = new Hl($tableName);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                try {
                    $update = $obj::update(
                        $id,
                        [
                            'UF_RESPONSE_COUNT' => $counter
                        ]
                    );

                    if ($update->isSuccess()) {
                        $hl_ = new Hl(static::ALL_TABLE_NAME);
                        if ($hl_->obj !== null) {
                            $obj_ = $hl_->obj;
                            $get = $obj_::getList(
                                [
                                    'select' => ['ID'],
                                    'filter' => [
                                        '=UF_ITEM_ID' => $this->itemId,
                                        '=UF_IBLOCK_ID' => $this->iBlockId
                                    ],
                                    'limit' => 1
                                ]
                            );
                            while ($res = $get->fetch()) {
                                $update_ = $obj_::update(
                                    $res['ID'],
                                    [
                                        'UF_RESPONSE_COUNT' => $counter
                                    ]
                                );

                                if ($update_->isSuccess()) {

                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                }
            }
        }

        return $result;
    }

    public function getResponseCounter()
    {
        $result = [];

        if ($this->itemId > 0 && $this->iBlockId) {
            $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($this->iBlockId))));
            $hl = new Hl($tableName);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                try {
                    $get = $obj::getList(
                        [
                            'select' => ['ID', 'UF_RESPONSE_COUNT'],
                            'filter' => [
                                '=UF_IBLOCK_ID' => $this->iBlockId,
                                '=UF_ID' => $this->itemId
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
        }

        return $result;
    }

    public function cancel()
    {
        if ($this->itemId > 0 && $this->iBlockId > 0 && $this->userId > 0) {
            $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($this->iBlockId))));
            $hl = new Hl($tableName);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                try {
                    $get = $obj::getList(
                        [
                            'select' => ['ID'],
                            'filter' => [
                                '=UF_IBLOCK_ID' => $this->iBlockId,
                                '=UF_ID' => $this->itemId,
                                '=UF_USER_ID' => $this->userId,
                                '=UF_ACTIVE' => 1,
                                '<=UF_ACTIVE_FROM' => DateTime::createFromTimestamp(time()),
                                '>=UF_ACTIVE_TO' => DateTime::createFromTimestamp(time()),
                                '=UF_MODERATION' => 0
                            ],
                            'limit' => 1
                        ]
                    );
                    while ($res = $get->fetch()) {
                        \CIBlockElement::SetPropertyValuesEx(
                            $this->itemId,
                            $this->iBlockId,
                            [
                                '__hidden_cancel_date' => DateTime::createFromTimestamp(time()),
                                '__hidden_quickly_start' => false,
                                '__hidden_quickly_end' => false
                            ]
                        );

                        $el = new \CIBlockElement();
                        $el->Update(
                            $this->itemId,
                            [
                                'ACTIVE_TO' => DateTime::createFromTimestamp(time())
                            ]
                        );

                        $event = new EventManager(
                            'cancel',
                            [
                                'iBlockId' => $this->iBlockId,
                                'itemId' => $this->itemId,
                                'userId' => $this->userId
                            ]
                        );
                        $event->execute();

                        Logger::add(
                            $this->userId,
                            'cancel',
                            [
                                'iBlockId' => $this->iBlockId,
                                'itemId' => $this->itemId
                            ]
                        );
                    }
                } catch (\Exception $e) {
                }
            }
        }
    }

    public function save($data)
    {
        if ($this->itemId > 0 && $this->iBlockId > 0 && $this->userId > 0) {
            $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($this->iBlockId))));
            $hl = new Hl($tableName);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                try {
                    $get = $obj::getList(
                        [
                            'select' => [
                                'ID',
                                'UF_IMAGE_ID',
                                'UF_IMAGES',
                                'UF_FILES',
                                'UF_HIDDEN_FILES',
                            ],
                            'filter' => [
                                '=UF_IBLOCK_ID' => $this->iBlockId,
                                '=UF_ID' => $this->itemId,
                                '=UF_USER_ID' => $this->userId,
                                '=UF_ACTIVE' => 1,
                                '<=UF_ACTIVE_FROM' => DateTime::createFromTimestamp(time()),
                                '>=UF_ACTIVE_TO' => DateTime::createFromTimestamp(time()),
                                '=UF_MODERATION' => 0
                            ],
                            'limit' => 1
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $tempPath = [];
                        $errors = [];
                        $detailImage = [];
                        $images = [];
                        $oldImages = [];
                        $allow = true;
                        $us = new User($this->userId, 0);                        $allow = true;

                        $el = new \CIBlockElement();
                        $properties = new Properties($this->iBlockId);
                        $properties->setTtl(0);
                        $propertiesList = $properties->all();
                        $userParams = $us->get();

                        if (intval($res['UF_IMAGE_ID']) > 0) {
                            $oldImages[intval($res['UF_IMAGE_ID'])] = intval($res['UF_IMAGE_ID']);
                        }

                        if (strlen($res['UF_IMAGES'])) {
                            foreach (unserialize($res['UF_IMAGES']) as $img) {
                                $oldImages[$img] = $img;
                            }
                        }

                        //TODO ������� ��������� ���������� ������

                        $location = [];
                        if ($data['location'] && strlen($data['location']) > 0) {
                            $ex = explode(',', $data['location']);
                            if (count($ex) == 2) {
                                if (floatval(trim($ex[0])) > 0 && floatval(trim($ex[1])) > 0) {
                                    $location[] = floatval(trim($ex[0]));
                                    $location[] = floatval(trim($ex[1]));
                                }
                            }
                        }

                        $propertyValues = [
                            '__hidden_price' => floatval($data['price']),
                            '__hidden_moderation' => intval(Option::get(DSPI, 'moderation_update')),
                            '__hidden_location' => (count($location) == 2) ? implode(',', $location) : ''
                        ];

                        if (intval($userParams['UF_DSPI_MOD_OFF']) > 0) {
                            $propertyValues['__hidden_moderation'] = 0;
                        }

                        if (!isset($data['name']) || !strlen($data['name'])) {
                            $allow = false;
                            $errors['properties'][] = [
                                'id' => 'name',
                                'type' => 'isset property'
                            ];
                        }

                        foreach ($propertiesList as $propertyId => $propertyValue) {
                            if ($propertyValue['isRequired'] > 0) {
                                if (!isset($data['prop'][$propertyId])) {
                                    $allow = false;
                                    $errors['properties'][] = [
                                        'id' => $propertyId,
                                        'type' => 'isset property'
                                    ];
                                }
                            }

                            switch ($propertyValue['type']) {
                                case 'string':
                                    if ($propertyValue['isRequired'] > 0) {
                                        if (strlen($data['prop'][$propertyId]) > 0) {
                                            $propertyValues[$propertyId] = htmlspecialcharsbx($data['prop'][$propertyId]);
                                        } else {
                                            $allow = false;
                                            $errors['properties'][] = [
                                                'id' => $propertyId,
                                                'type' => 'empty'
                                            ];
                                        }
                                    } else {
                                        if (strlen($data['prop'][$propertyId]) > 0) {
                                            $propertyValues[$propertyId] = htmlspecialcharsbx($data['prop'][$propertyId]);
                                        }
                                    }
                                    break;
                                case 'integer':
                                    if ($propertyValue['isRequired'] > 0) {
                                        if (floatval($data['prop'][$propertyId]) > 0) {
                                            $propertyValues[$propertyId] = floatval($data['prop'][$propertyId]);
                                        } else {
                                            $allow = false;
                                            $errors['properties'][] = [
                                                'id' => $propertyId,
                                                'type' => 'float'
                                            ];
                                        }
                                    } else {
                                        if (floatval($data['prop'][$propertyId]) > 0) {
                                            $propertyValues[$propertyId] = floatval($data['prop'][$propertyId]);
                                        }
                                    }
                                    break;
                                case 'list':
                                    if ($propertyValue['isRequired'] > 0) {
                                        if (intval($data['prop'][$propertyId]) > 0) {
                                            if (isset($propertyValue['values'][intval($data['prop'][$propertyId])])) {
                                                $propertyValues[$propertyId] = intval($data['prop'][$propertyId]);
                                            } else {
                                                $allow = false;
                                                $errors['properties'][] = [
                                                    'id' => $propertyId,
                                                    'type' => 'isset value'
                                                ];
                                            }
                                        } else {
                                            $allow = false;
                                            $errors['properties'][] = [
                                                'id' => $propertyId,
                                                'type' => 'int'
                                            ];
                                        }
                                    } else {
                                        if (intval($data['prop'][$propertyId]) > 0) {
                                            if (isset($propertyValue['values'][intval($data['prop'][$propertyId])])) {
                                                $propertyValues[$propertyId] = intval($data['prop'][$propertyId]);
                                            }
                                        }
                                    }
                                    break;
                            }
                        }

                        //TODO ������� ��������� ���������� ������
                        if (isset($data['__files'])) {
                            ksort($data['__files']);
                            reset($data['__files']);

                            if (count($data['__files']) > 0) {
                                $i = 0;
                                foreach ($data['__files'] as $file) {
                                    if (intval($file) > 0) {
                                        if (!count($detailImage)) {
                                            $detailImage = \CFile::MakeFileArray($file);
                                        } else {
                                            $images['n' . $i] = [
                                                'VALUE' => \CFile::MakeFileArray($file),
                                                'DESCRIPTION' => md5(microtime(true))
                                            ];
                                            $i++;
                                        }
                                    } else {
                                        if (!count($detailImage)) {
                                            $temp = Utils::createTempImageFromBase64($file);
                                            if ($temp && File::isFileExists($temp)) {
                                                $tempPath[] = $temp;
                                                $detailImage = \CFile::MakeFileArray($temp);
                                            }
                                        } else {
                                            $temp = Utils::createTempImageFromBase64($file);
                                            if ($temp && File::isFileExists($temp)) {
                                                $tempPath[] = $temp;
                                                $images['n' . $i] = [
                                                    'VALUE' => \CFile::MakeFileArray($temp),
                                                    'DESCRIPTION' => md5(microtime(true))
                                                ];
                                                $i++;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (count($images) > 0) {
                            $propertyValues['images'] = $images;
                        }

                        if ($allow) {
                            \CIBlockElement::SetPropertyValuesEx($this->itemId, $this->iBlockId, $propertyValues);
                            $update = $el->Update(
                                $this->itemId,
                                [
                                    'NAME' => strip_tags(HTMLToTxt($data['name'])),
                                    'DETAIL_TEXT' => HTMLToTxt(str_replace("\r\n", '<br>', strip_tags($data['description']))),
                                    'TIMESTAMP_X' => DateTime::createFromTimestamp(time()),
                                    'DETAIL_PICTURE' => (count($detailImage) > 0) ? $detailImage : false
                                ]
                            );
                            if ($update) {
                                if ($propertyValues['__hidden_moderation'] > 0) {
                                    Event::send(
                                        [
                                            'EVENT_NAME' => 'DSPI_UPDATE_MODERATION',
                                            'LID' => Application::getInstance()->getContext()->getSite(),
                                            'C_FIELDS' => [
                                                'ID' => $this->itemId,
                                                'IBLOCK_ID' => $this->iBlockId,
                                                'IBLOCK_TYPE' => $this->iBlockType
                                            ]
                                        ]
                                    );
                                }

                                try {
                                    Logger::add(
                                        $this->userId,
                                        'updateItemSuccess',
                                        [
                                            'userId' => $this->userId,
                                            'iBlockId' => $this->iBlockId,
                                            'id' => $this->itemId
                                        ]
                                    );

                                    $event = new EventManager(
                                        'updateItemSuccess',
                                        [
                                            'userId' => $this->userId,
                                            'iBlockId' => $this->iBlockId,
                                            'id' => $this->itemId
                                        ]
                                    );
                                    $event->execute();
                                } catch (LoaderException $e) {
                                }

                                if (count($oldImages) > 0 && (count($detailImage) > 0 || count($images) > 0)) {
                                    foreach ($oldImages as $img) {
                                        \CFile::Delete($img);
                                    }
                                }
                            } else {
                                try {
                                    Logger::add(
                                        $this->userId,
                                        'updateItemError',
                                        [
                                            'userId' => $this->userId,
                                            'iBlockId' => $this->iBlockId,
                                            'id' => $this->itemId,
                                            'data' => $data,
                                            'error' => $el->LAST_ERROR
                                        ]
                                    );

                                    $event = new EventManager(
                                        'updateItemError',
                                        [
                                            'userId' => $this->userId,
                                            'iBlockId' => $this->iBlockId,
                                            'id' => $this->itemId,
                                            'data' => $data,
                                            'error' => $el->LAST_ERROR
                                        ]
                                    );
                                    $event->execute();
                                } catch (LoaderException $e) {
                                }
                            }
                        } else {
                            if (count($errors) > 0) {
                                try {
                                    Logger::add(
                                        $this->userId,
                                        'prepareUpdateItemError',
                                        [
                                            'data' => $data,
                                            'errors' => $errors
                                        ]
                                    );

                                    $event = new EventManager(
                                        'prepareUpdateItemError',
                                        [
                                            'userId' => $this->userId,
                                            'iBlockId' => $this->iBlockId,
                                            'data' => $data,
                                            'errors' => $errors
                                        ]
                                    );
                                    $event->execute();
                                } catch (LoaderException $e) {
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                }
            }
        }
    }

    public function addStages($stages, $i = 0)
    {
        if (!count($stages)) {
            return;
        }

        if ($this->itemId > 0) {
            $this->setStagesClass();
            if ($this->stagesClass !== null) {
                try {
                    $obj = $this->stagesClass;
                    foreach ($stages as $stage) {
                        $i++;
                        $add = $obj::add(
                            [
                                'UF_TASK_ID' => $this->itemId,
                                'UF_PRICE' => intval($stage['price']),
                                'UF_STATUS' => 0,
                                'UF_SORT' => $i,
                                'UF_NAME' => strip_tags(HTMLToTxt($stage['name'])),
                                'UF_DESCRIPTION' => '',
                                'UF_FILES' => serialize([])
                            ]
                        );
                        if ($add->isSuccess()) {

                        }
                    }
                } catch (\Exception $e) {

                }
            }
        }
    }

    private function updateStage($id, $stage, $sort)
    {
        if ($this->itemId > 0) {
            $this->setStagesClass();
            if ($this->stagesClass !== null) {
                try {
                    $obj = $this->stagesClass;
                    $get = $obj::getList(
                        [
                            'select' => ['ID'],
                            'filter' => [
                                'ID' => $id,
                                'UF_TASK_ID' => $this->itemId
                            ],
                            'limit' => 1
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $obj::update(
                            $res['ID'],
                            [
                                'UF_PRICE' => intval($stage['price']),
                                'UF_NAME' => strip_tags(HTMLToTxt($stage['name'])),
                                'UF_SORT' => $sort,
                            ]
                        );
                    }
                } catch (\Exception $e) {

                }
            }
        }
    }

    private function deleteStage($id)
    {
        if ($this->itemId > 0) {
            $this->setStagesClass();
            if ($this->stagesClass !== null) {
                try {
                    $obj = $this->stagesClass;
                    $get = $obj::getList(
                        [
                            'select' => ['ID'],
                            'filter' => [
                                'ID' => $id,
                                'UF_TASK_ID' => $this->itemId
                            ],
                            'limit' => 1
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

    public function closeStage($id, $executorId = 0)
    {
        /*
         * 0 - ���� ������
         * 1 - ���� �������� ������������
         * 2 - ������� ���������
         * 3 - ���� ������
         */
        $result = false;
        if ($this->itemId > 0) {
            $this->setStagesClass();
            if ($this->stagesClass !== null) {
                try {
                    $request = Application::getInstance()->getContext()->getRequest();
                    $obj = $this->stagesClass;
                    $get = $obj::getList(
                        [
                            'select' => [
                                'ID',
                                'UF_SORT',
                                'UF_NAME',
                                'UF_DESCRIPTION'
                            ],
                            'filter' => [
                                '=ID' => $id,
                                '=UF_TASK_ID' => $this->itemId,
                                '=UF_STATUS' => 1
                            ],
                            'limit' => 1
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $update = $obj::update(
                            $res['ID'],
                            [
                                'UF_STATUS' => 3
                            ]
                        );

                        if ($update->isSuccess()) {
                            $result = true;

                            if (intval($executorId) > 0) {
                                Notifications::add(
                                    $executorId,
                                    'closeTaskStage',
                                    [
                                        'taskId' => $this->itemId,
                                        'iBlockId' => $this->iBlockId,
                                        'stageId' => $id
                                    ]
                                );

                                $fb = new FireBase($executorId);
                                $fb->webPush([
                                    'title' => Loc::getMessage('PUSH_CLOSE_STAGE_TITLE'),
                                    'body' => Loc::getMessage('PUSH_CLOSE_STAGE_BODY', ['{{STAGE_ID}}' => $id, '{{TASK_ID}}' => $this->itemId])
                                ]);
                                unset($fb);

                                $us = new User($executorId);
                                $userParams = $us->get();
                                if (count($userParams) > 0) {
                                    Event::send(
                                        [
                                            'EVENT_NAME' => 'DSPI_OWNER_STAGE_END',
                                            'LID' => Application::getInstance()->getContext()->getSite(),
                                            'C_FIELDS' => [
                                                'EMAIL_TO' => $userParams['EMAIL'],
                                                'ITEM_ID' => $this->itemId,
                                                'FULL_URL' => (($request->isHttps()) ? 'https://' : 'http://')
                                                    . Path::normalize($request->getHttpHost() . SITE_DIR
                                                        . 'task' . $this->itemId . '-' . $this->iBlockId) . '/'
                                            ]
                                        ]
                                    );
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {

                }
            }
        }

        return $result;
    }

    public function closeStageExecutor($id, $user1 = 0)
    {
        /*
         * 0 - ���� ������
         * 1 - ���� �������� ������������
         * 2 - ������� ���������
         * 3 - ���� ������
         */
        $result = false;
        if ($this->itemId > 0) {
            $this->setStagesClass();
            if ($this->stagesClass !== null) {
                try {
                    $request = Application::getInstance()->getContext()->getRequest();
                    $obj = $this->stagesClass;
                    $get = $obj::getList(
                        [
                            'select' => [
                                'ID',
                                'UF_SORT',
                                'UF_NAME',
                                'UF_DESCRIPTION'
                            ],
                            'filter' => [
                                '=ID' => $id,
                                '=UF_TASK_ID' => $this->itemId,
                                '=UF_STATUS' => 0
                            ],
                            'limit' => 1
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $update = $obj::update(
                            $res['ID'],
                            [
                                'UF_STATUS' => 1
                            ]
                        );

                        if ($update->isSuccess()) {
                            $result = true;

                            if (intval($user1) > 0) {
                                Notifications::add(
                                    $user1,
                                    'closeStageExecutor',
                                    [
                                        'taskId' => $this->itemId,
                                        'iBlockId' => $this->iBlockId
                                    ]
                                );

                                $fb = new FireBase($user1);
                                $fb->webPush([
                                    'title' => Loc::getMessage('PUSH_CLOSE_STAGE_TITLE'),
                                    'body' => Loc::getMessage('PUSH_CLOSE_STAGE_EXECUTOR_BODY', ['{{STAGE_ID}}' => $id, '{{TASK_ID}}' => $this->itemId])
                                ]);
                                unset($fb);

                                $us = new User($user1);
                                $userParams = $us->get();
                                if (count($userParams) > 0) {
                                    Event::send(
                                        [
                                            'EVENT_NAME' => 'DSPI_EXECUTOR_STAGE_END',
                                            'LID' => Application::getInstance()->getContext()->getSite(),
                                            'C_FIELDS' => [
                                                'EMAIL_TO' => $userParams['EMAIL'],
                                                'ITEM_ID' => $this->itemId,
                                                'FULL_URL' => (($request->isHttps()) ? 'https://' : 'http://')
                                                    . Path::normalize($request->getHttpHost() . SITE_DIR
                                                        . 'task' . $this->itemId . '-' . $this->iBlockId) . '/'
                                            ]
                                        ]
                                    );
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {

                }
            }
        }

        return $result;
    }

    public function getStages()
    {
        $result = [];
        if ($this->itemId > 0) {
            $this->setStagesClass();
            if ($this->stagesClass !== null) {
                try {
                    $obj = $this->stagesClass;
                    $get = $obj::getList(
                        [
                            'select' => ['*'],
                            'filter' => [
                                '=UF_TASK_ID' => $this->itemId
                            ],
                            'order' => [
                                'UF_SORT' => 'ASC'
                            ]
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $result[] = $res;
                    }
                } catch (\Exception $e) {

                }
            }
        }

        return $result;
    }

    /**
     * @param $data
     * @param $filesData
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws \Bitrix\Main\SystemException
     */
    public function translit_custom($s) {
        $s = (string) $s; // преобразуем в строковое значение
        $s = strip_tags($s); // убираем HTML-теги
        $s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
        $s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
        $s = trim($s); // убираем пробелы в начале и конце строки
        $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
        $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
        $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
        $s = str_replace(" ", "-", $s); // заменяем пробелы знаком минус
        return $s; // возвращаем результат
    }
    public function create($data, $filesData)
    {


        global $USER;
        $issetCompany = '';
        $us = new \Democontent2\Pi\User(intval($USER->GetID()));
        //получаю компанию пользователя создавшего заявку
        $arIdes[0] = $this->userId;
        $issetCompany = $us->getCompanyManager($arIdes);
        $GLOBALS['ISSETCOMPANYCURRENT'] = $issetCompany;
        $GLOBALS['COUNT_TECH'] = $data['nameCountTehn_hidden'];

        if (isset($data['iblock_code']) && intval($data['iblock_code']) > 0) {
            $iBlock = new Iblock(0);
            $ibl = $iBlock->getAllIds();
            if (in_array(intval($data['iblock_code']), $ibl)) {
                $this->iBlockId = intval($data['iblock_code']);
            }
        }



        if ( $this->userId > 0) {

            $this->iBlockType = Utils::getIBlockType($this->iBlockId);



            $stages = [];
            $errors = [];
            $files = [];
            $hiddenFiles = [];
            $allow = true;
            $us = new User($this->userId, 0);
            $el = new \CIBlockElement();
            $city = new City();
            $city->setTtl(0);
            $properties = new Properties($this->iBlockId);
            $properties->setTtl(0);
            $propertiesList = $properties->all();
            $userParams = $us->get();
            $cityParams = $city->getDefault();

            if (isset($data['city']) && intval($data['city']) > 0) {
                $getCity = $city->getById(intval($data['city']));
                if (count($getCity) > 0) {
                    $cityParams = $getCity;
                }
                unset($getCity);
            }

            $location = [];
            if ($data['location'] && strlen($data['location']) > 0) {
                $ex = explode(',', $data['location']);
                if (count($ex) == 2) {
                    if (floatval(trim($ex[0])) > 0 && floatval(trim($ex[1])) > 0) {
                        $location[] = floatval(trim($ex[0]));
                        $location[] = floatval(trim($ex[1]));
                    }
                }
            }

            $dateStart = '';
            $dateEnd = '';
            if(!empty($data['COUNT_MONEY_ZA'])){
                $priseZaEd = $data['COUNT_MONEY_ZA'];
            }else{
                $priseZaEd = '';
            }


            $nameTrans = $data['city'];
            $arParams = array("replace_space"=>"-","replace_other"=>"-");
            $trans = $this->translit_custom($nameTrans);



            $arFilter = array(
                "IBLOCK_ID" => 1,
                "NAME" => $data['city'],
                "CODE" => $trans
            );
            $idCiti = '';
            $rsItems = \CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter, false, false, Array());
            $element = [];
            while($res = $rsItems->GetNextElement())
            {
                $arFields = $res->GetFields();
                if(!empty($arFields['ID'])){
                    $element = $arFields;
                }

            }

            if(!empty($element['ID'])){
                $idCiti = $element['ID'];
            }else{
                $el = new \CIBlockElement;
                $arParams = array("replace_space"=>"-","replace_other"=>"-");

                $arLoadProductArray = Array(
                    "NAME"    => $data['city'] , // элемент изменен текущим пользователем
                    "IBLOCK_ID"      => 1,
                    'CODE' => $trans
                );

                $idCiti = $el->Add($arLoadProductArray);

            }




            $propertyValues = [
                '__hidden_city' => intval($idCiti),
                '__hidden_user' => $this->userId,
                '__hidden_price' => $priseZaEd,
                '__hidden_moderation' => intval(Option::get(DSPI, 'moderation_new')),
                '__hidden_archive' => 0,
                '__hidden_payed' => 1,
                '__hidden_location' => (count($location) == 2) ? implode(',', $location) : '',
                '__hidden_begin_with' => false,
                '__hidden_run_up' => false,
                '__hidden_security' => 0,

            ];



            if (isset($data['route'])) {
                if (is_array($data['route']) && count($data['route'])) {
                    $courierIblocks = [];
                    try {
                        $courierIblocks = Json::decode(Option::get(DSPI, 'courierIblocks'));
                    } catch (ArgumentNullException $e) {
                    } catch (ArgumentOutOfRangeException $e) {
                    } catch (ArgumentException $e) {
                    }

                    if (count($courierIblocks)) {
                        if (in_array($this->iBlockId, $courierIblocks)) {
                            Utils::checkCourierIblock($this->iBlockId);

                            $coords = [];
                            foreach ($data['route'] as $route) {
                                $ex = explode(',', $route);
                                if (is_float(floatval($ex[0])) && is_float(floatval($ex[1]))) {
                                    $coords[] = floatval($ex[0]) . ',' . floatval($ex[1]);
                                }
                            }

                            if (count($coords)) {
                                $propertyValues['__hidden_coordinates'] = $coords;
                            }
                        }
                    }
                }
            }


            $statusEnums = \CIBlockPropertyEnum::GetList(
                [
                    'SORT' => 'ASC'
                ],
                [
                    'IBLOCK_ID' => $this->iBlockId,
                    'CODE' => '__hidden_status'
                ]
            );
            while ($statusEnumsRes = $statusEnums->GetNext()) {
                if ($statusEnumsRes['XML_ID'] == '__status_1') {
                    $propertyValues['__hidden_status'] = $statusEnumsRes['ID'];
                    break;
                }
            }

            if (isset($data['dateStart'])) {
                $dateStart = $data['dateStart'] . ' 00:00:00';
//эnj пиздцец от старых чуваков ) я несколько мес смотрю на этот свич который нехера не делает кроме 23/59 и не понимаю нах это надо
                if (isset($data['timeStart'])) {
                    $tmp = explode(':', $data['timeStart']);
                    if (count($tmp) == 2) {
                        switch ($tmp[0]) {
                            case '00':
                            case '01':
                            case '02':
                            case '03':
                            case '04':
                            case '05':
                            case '06':
                            case '07':
                            case '08':
                            case '09':
                            case '10':
                            case '11':
                            case '12':
                            case '13':
                            case '14':
                            case '15':
                            case '16':
                            case '17':
                            case '18':
                            case '19':
                            case '20':
                            case '21':
                            case '22':
                            case '23':
                                if (intval($tmp[1]) >= 0 && intval($tmp[1]) <= 59) {
                                    $dateStart = $data['dateStart'] . ' ' . $tmp[0] . ':' . $tmp[1] . ':00';
                                }
                                break;
                        }
                    }
                }
            }

            if (isset($data['dateEnd'])) {
                $dateEnd = $data['dateEnd'] . ' 00:00:00';

                if (isset($data['timeEnd'])) {
                    $tmp = explode(':', $data['timeEnd']);
                    if (count($tmp) == 2) {
                        switch ($tmp[0]) {
                            case '00':
                            case '01':
                            case '02':
                            case '03':
                            case '04':
                            case '05':
                            case '06':
                            case '07':
                            case '08':
                            case '09':
                            case '10':
                            case '11':
                            case '12':
                            case '13':
                            case '14':
                            case '15':
                            case '16':
                            case '17':
                            case '18':
                            case '19':
                            case '20':
                            case '21':
                            case '22':
                            case '23':
                                if (intval($tmp[1]) >= 0 && intval($tmp[1]) <= 59) {
                                    $dateEnd = $data['dateEnd'] . ' ' . $tmp[0] . ':' . $tmp[1] . ':00';
                                }
                                break;
                        }
                    }
                }
            }

            if (strlen($dateStart) > 0 && strlen($dateEnd) > 0) {
                if (strtotime($dateStart) !== strtotime($dateEnd)) {
                    if (strtotime($dateEnd) > strtotime($dateStart)) {
                        $propertyValues['__hidden_begin_with'] = DateTime::createFromTimestamp(strtotime($dateStart));
                        $propertyValues['__hidden_run_up'] = DateTime::createFromTimestamp(strtotime($dateEnd));
                    } else {
                        $propertyValues['__hidden_begin_with'] = DateTime::createFromTimestamp(strtotime($dateEnd));
                        $propertyValues['__hidden_run_up'] = DateTime::createFromTimestamp(strtotime($dateStart));
                    }
                } else {
                    $propertyValues['__hidden_begin_with'] = DateTime::createFromTimestamp(strtotime($dateStart));
                    $propertyValues['__hidden_run_up'] = DateTime::createFromTimestamp(strtotime($dateEnd));
                }
            } else {
                if (strlen($dateStart) > 0) {
                    $propertyValues['__hidden_begin_with'] = DateTime::createFromTimestamp(strtotime($dateStart));
                }

                if (strlen($dateEnd) > 0) {
                    $propertyValues['__hidden_run_up'] = DateTime::createFromTimestamp(strtotime($dateEnd));
                }
            }


            if (intval($userParams['UF_DSPI_MOD_OFF']) > 0) {
                $propertyValues['__hidden_moderation'] = 0;
            }

            if (!isset($data['name']) || !strlen($data['name'])) {
                $allow = false;
                $errors['properties'][] = [
                    'id' => 'name',
                    'type' => 'isset property'
                ];
            }

            foreach ($propertiesList as $propertyId => $propertyValue) {
                if ($propertyValue['isRequired'] > 0) {
                    if (!isset($data['prop'][$propertyId])) {
                        $allow = false;
                        $errors['properties'][] = [
                            'id' => $propertyId,
                            'type' => 'isset property'
                        ];
                    }
                }

                switch ($propertyValue['type']) {
                    case 'string':
                        if ($propertyValue['isRequired'] > 0) {
                            if (strlen($data['prop'][$propertyId]) > 0) {
                                $propertyValues[$propertyId] = htmlspecialcharsbx($data['prop'][$propertyId]);
                            } else {
                                $allow = false;
                                $errors['properties'][] = [
                                    'id' => $propertyId,
                                    'type' => 'empty'
                                ];
                            }
                        } else {
                            if (strlen($data['prop'][$propertyId]) > 0) {
                                $propertyValues[$propertyId] = htmlspecialcharsbx($data['prop'][$propertyId]);
                            }
                        }
                        break;
                    case 'integer':
                        if ($propertyValue['isRequired'] > 0) {
                            if (floatval($data['prop'][$propertyId]) > 0) {
                                $propertyValues[$propertyId] = floatval($data['prop'][$propertyId]);
                            } else {
                                $allow = false;
                                $errors['properties'][] = [
                                    'id' => $propertyId,
                                    'type' => 'float'
                                ];
                            }
                        } else {
                            if (floatval($data['prop'][$propertyId]) > 0) {
                                $propertyValues[$propertyId] = floatval($data['prop'][$propertyId]);
                            }
                        }
                        break;
                    case 'list':
                        if ($propertyValue['isRequired'] > 0) {
                            if (intval($data['prop'][$propertyId]) > 0) {
                                if (isset($propertyValue['values'][intval($data['prop'][$propertyId])])) {
                                    $propertyValues[$propertyId] = intval($data['prop'][$propertyId]);
                                } else {
                                    $allow = false;
                                    $errors['properties'][] = [
                                        'id' => $propertyId,
                                        'type' => 'isset value'
                                    ];
                                }
                            } else {
                                $allow = false;
                                $errors['properties'][] = [
                                    'id' => $propertyId,
                                    'type' => 'int'
                                ];
                            }
                        } else {
                            if (intval($data['prop'][$propertyId]) > 0) {
                                if (isset($propertyValue['values'][intval($data['prop'][$propertyId])])) {
                                    $propertyValues[$propertyId] = intval($data['prop'][$propertyId]);
                                }
                            }
                        }
                        break;
                }
            }

        /*  if (isset($filesData['__files'])) {
                if (count($filesData['__files']['name']) > 0) {
                    $i = 0;
                    foreach ($filesData['__files']['name'] as $fileKey => $fileValue) {

                        $extension = Utils::getExtension($fileValue);
                        if (strlen($extension) > 0) {
                            $fileArray = \CFile::MakeFileArray($filesData['__files']['tmp_name'][$fileKey]);

                            $fileArray['description'] = $fileValue;
                            $fileArray['name'] = md5(microtime(true)) . '-' . ToLower(randString(rand(5, 10))) . $extension;
                            $files['n' . $i] = [
                                'VALUE' => $fileArray,
                                'DESCRIPTION' => $fileArray['description']
                            ];

                            $i++;
                        }
                    }
                }
            }
        */

            if(isset($filesData['__files'])){
                if (count($filesData['__files']['name']) > 0) {
                    $i = 0;

                    $arMessFiles = [];

                    foreach ($filesData['__files']['name'] as $idFile => $valueFile){
                        $extension = Utils::getExtension($valueFile);
                        $randStr = randString(15);
                        $typeFilecolon = explode('/' ,  $filesData['__files']['type'][$idFile]);
                        $fileArray = array(
                            "name" => $randStr.'.'.$typeFilecolon[1],
                            "size" => $filesData['__files']['size'][$idFile],
                            "tmp_name" => $filesData['__files']['tmp_name'][$idFile],
                            "type" => $filesData['__files']['type'][$idFile],

                        );
                        $fid = \CFile::SaveFile( $fileArray , false  , false , false , 'listTask');
                        $arFile = \CFile::GetFileArray($fid);

                        $fileChache = \CFile::ResizeImageGet(
                            $arFile ,
                            array(
                                'width'=>1024,
                                'height'=>1024),
                            BX_RESIZE_IMAGE_PROPORTIONAL,
                            true,
                                    array(),
                                    false ,
                            70
                        );

                        if(!empty($fileChache['src'])){


                    /*        $messAll = [];

                            $messAll['description'] = $arFile['FILE_NAME'];
                            $messAll['name'] = $arFile['FILE_NAME'];
                            $messAll['tmp_name'] = $fileChache['src'];
                            $messAll['type'] = $filesData['__files']['type'][$idFile];
                            $messAll['size'] = $fileChache['size'];
                    */
                            $files['n' . $i] = [
                                'VALUE' => \CFile::MakeFileArray($fileChache['src']),
                                'DESCRIPTION' => $arFile['FILE_NAME']
                            ];


//                            $files['n' . $i]['VALUE']['description'] = $arFile['tmp_name'];
                            $i++;
                        }


                    }


                }
            }

            if (isset($filesData['__hiddenFiles'])) {
                if (count($filesData['__hiddenFiles']['name']) > 0) {
                    $i = 0;
                    foreach ($filesData['__hiddenFiles']['name'] as $fileKey => $fileValue) {
                        if(!empty($fileValue)){
                            $extension = Utils::getExtension($fileValue);
                            if (strlen($extension) > 0) {
                                $fileArray = \CFile::MakeFileArray($filesData['__hiddenFiles']['tmp_name'][$fileKey]);

                                $fileArray['description'] = $fileValue;
                                $fileArray['name'] = md5(microtime(true)) . '-' . ToLower(randString(rand(5, 10))) . $extension;
                                $hiddenFiles['n' . $i] = [
                                    'VALUE' => $fileArray,
                                    'DESCRIPTION' => $fileArray['description']
                                ];
                                $i++;
                            }

                        }
                    }
                }
            }

            if (count($files) > 0) {
                $propertyValues['files'] = $files;


            }

            if (count($hiddenFiles) > 0) {
                $propertyValues['hidden_files'] = $hiddenFiles;
            }

            $security = false;

            if (strlen(Option::get(DSPI, 'safeCrowApiKey')) > 0
                && strlen(Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
                $security = true;

                if (isset($data['security']) && strlen($data['security']) > 0) {
                    $propertyValues['__hidden_security'] = 1;

                    if ($propertyValues['__hidden_price'] <= 0) {
                        $propertyValues['__hidden_security'] = 0;
                    }
                } else {
                    $security = false;
                }
            }

            if (isset($data['stages'])) {
                foreach ($data['stages'] as $stageKey => $stageValue) {
                    if (isset($stageValue['name']) && strlen($stageValue['name']) > 0) {
                        $stages[] = [
                            'name' => strip_tags(HTMLToTxt($stageValue['name'])),
                            'price' => (isset($stageValue['price'])) ? intval($stageValue['price']) : 0
                        ];

                        if (!isset($stageValue['price']) || !intval($stageValue['price'])) {
                            $security = false;
                        }
                    }
                }

                if (!count($stages)) {
                    $allow = false;
                    $errors['stages'] = 'empty';
                }
            }

            if (count($stages) > 0) {
                $propertyValues['__hidden_price'] = 0;

                if (!$security) {
                    $propertyValues['__hidden_security'] = 0;
                } else {
                    $propertyValues['__hidden_security'] = 1;
                }
            }


            $allow = 1;
            if ($allow == 1) {

                $userItemsCount = 0;
                $iblock = new Iblock();
                $iblocksList = $iblock->getAllIds();
                foreach ($iblocksList as $iblockId) {
                    $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($iblockId))));
                    $hl = new Hl($tableName);
                    if ($hl->obj !== null) {
                        $obj = $hl->obj;
                        try {
                            $get = $obj::getList(
                                [
                                    'select' => ['CNT'],
                                    'runtime' => [
                                        new ExpressionField('CNT', 'COUNT(`ID`)')
                                    ],
                                    'filter' => [
                                        '=UF_IBLOCK_ID' => $this->iblockId,
                                        '=UF_USER_ID' => $this->userId
                                    ]
                                ]
                            );
                            while ($res = $get->fetch()) {
                                $userItemsCount += intval($res['CNT']);
                            }
                        } catch (\Exception $e) {
                        }
                    }
                }


//                if($data['contractPriceDOGOVOR'] == 0){
//                    $propertyValues['FOR_DOGOVOR'] = 344;
//
//                };

                if(!empty($data['contractPrice'])) {

                    $propertyValues['BEZ_NDS'] = $data['contractPrice'];

                };

                $props = [];
                if(!empty( $data['nameBeznal'])){
                    $props['PROPS']['BEZZNAL'] = $data['nameBeznal'];
                }

                if(!empty($data['nameNal'])){
                    $props['PROPS']['NAL'] = $data['nameNal'];
                }
                if($data['contractPrice'] == 0) {
                    $props['PROPS']['BEZ_NDS'] = $data['contractPrice'];
                };


/*
                $arIblock = \Bitrix\Iblock\IblockTable::getList(array(
                    'filter' => array('CODE' => $data['iblock_code']) // параметры фильтра
                ))->fetch();

                pre($data);
                die();
*/

//                var_dump($data);
//                die();

//                pre($data['iblock']);echo "<br>";
//                $data['iblock'] = (int) $data['iblock'];
//                echo  gettype($data['iblock']);
//                die();
                if(empty($data['iblock']) || $data['iblock'] == ''){
                    $arIblock['ID'] = \Bitrix\Iblock\IblockTable::getList(array(
                        'filter' => array('CODE' => $data['iblock_code']) // параметры фильтра
                    ))->fetch();
                    $arIblock['ID'] = $arIblock['ID']['ID'];

                }else{
                    $arIblock['ID'] = $data['iblock'];

                }

                //нуно получить свойства инфоблока чтобы узнать айди их и по кодам впихнуть ) вытрахиваемся как можем  /  никто не винован в говно проекте
                //суть в том что при каждом добавлении когда я выбриаю подраздел  -  тоесть второе поле  - подкатегория ) в админке на каждую покатегорию инфоблок )
                // и вот перед кадым добавленем  / мне надо выдернуть

                //из инфоблока свойства и узнать их айди ) чтобы заявка создалась ) но если вы подуали что заявка хранится в этом инф блоке
                //то это не так   / она далее сохранится в хайлоуд democontentpial
                $arOrder = Array();      // сортируем по свойству ID_MSSQL по возрастанию
                $arFilter = Array("IBLOCK_ID"=> $arIblock['ID']);   // указываем из какого Инфоблока брать данные
                $arSelectFields = Array("PROPERTY_NAL" , "PROPERTY_BEZ_NDS" , "PROPERTY_BEZNAL" , "PROPERTY_CHARACTERISTIC_1" , "PROPERTY_CHARACTERISTIC_2" , "PROPERTY_CHARACTERISTIC_3");      // указываем что нам нужно
                $res = \CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelectFields);
                $arProps = [];
                while($ar_fields = $res->GetNext())
                {

                    $arProps = $ar_fields;
                }

                $messIds = [];
                foreach ($arProps as $id => $valProp){
                    if(!empty($valProp)){
                        $idEl  = explode(':' , $valProp);

                        $messIds[$id][] = $idEl[1];


                    }
                }



                $propertyValues[$messIds['PROPERTY_BEZNAL_VALUE_ID'][0]] = $data['nameBeznal'];
                $propertyValues[$messIds['PROPERTY_NAL_VALUE_ID'][0]] = $data['nameNal'];

                $propertyValues[$messIds['PROPERTY_CHARACTERISTIC_1_VALUE_ID'][0]] =  Array(
                    "n0" => Array(
                        "VALUE" => $data['harectic_name_0'],
                        "DESCRIPTION" => $data['harectic_0'] )
                );
                $propertyValues[$messIds['PROPERTY_CHARACTERISTIC_2_VALUE_ID'][0]] =  Array(
                    "n0" => Array(
                        "VALUE" => $data['harectic_name_1'],
                        "DESCRIPTION" => $data['harectic_1'] )
                );
                $propertyValues[$messIds['PROPERTY_CHARACTERISTIC_3_VALUE_ID'][0]] =  Array(
                    "n0" => Array(
                        "VALUE" => $data['harectic_name_2'],
                        "DESCRIPTION" => $data['harectic_2'] )
                );


                $coord = explode("," , $data['route'][0]);
                $comma_separated = implode(",", $coord);

//                $propertyValues[1181]['n0'] =  Array(
//                        "VALUE" => $comma_separated
//
//                );
//                pre($propertyValues[1181]);
//
//
//                die();
                $mapCoord = [];
                foreach($data['route'] as $id => $valMap){
                    $coord = explode("," , $valMap);
                    $comma_separated = implode(",", $coord);
                    $mapCoord['n'.$id] = array(
                        "VALUE" => $comma_separated
                    );
                }


                $arFilter = array(
                    'IBLOCK_ID' => $arIblock['ID'],
                    'CODE' => '__hidden_coordinates',
                );
                $res = \CIBlockProperty::GetList(array(), $arFilter);
                $field = $res->Fetch();


                $propertyValues[$field['ID']] = $mapCoord;
//                pre($propertyValues[1181]);
//                die();



                $rsUserNk = \CUser::GetByID($this->userId);
                $arUserNNk = $rsUserNk->Fetch();

//если у текущего пользователя не заполнен профиль / тогда не даю пройти модерацию
                if(empty($arUserNNk['PERSONAL_PHONE'])){

                    $propertyValues['__hidden_moderation'] = 2;


                    $html = "<ul class='refusal_app_form'>";

                    $html = $html . "<li tag='notIssetCompany'>Не заполнен личный профиль пользователя. Для публикации заполните необходимые поля и подтвердите контактные данные.</li>";

                    $html = $html . "</ul>";


                    $value = array('VALUE'=>array('TYPE'=>'html', 'TEXT'=>$html));

                    $propertyValues['__hidden_moderation_reason'] = $value;


                    \CIBlockElement::SetPropertyValuesEx($this->itemId, false, array(
                        '__hidden_moderation_reason' => $value ,
                        '__hidden_moderation' => 2
                    ));


                }

                $add = $el->Add(
                    [
                        'IBLOCK_ID' => $arIblock['ID'],
                        'IBLOCK_SECTION_ID' => false,
                        'NAME' => strip_tags(HTMLToTxt($data['name'])),
                        'CODE' => ToLower(\CUtil::translit(htmlspecialcharsbx(trim($data['name'])), 'ru', Utils::tr())),
                        'ACTIVE_FROM' => DateTime::createFromTimestamp(time()),
                        'ACTIVE_TO' => DateTime::createFromTimestamp((time() + (86400 * intval(Option::get(DSPI, 'item_period'))))),
                        'ACTIVE' => 'Y',
                        'DETAIL_TEXT' => HTMLToTxt(str_replace("\r\n", '<br>', strip_tags($data['description']))),
                        'DETAIL_PICTURE' => false,
                        'PROPERTY_VALUES' => $propertyValues,

                    ]
                );

//добавляю уведомление


                $arrItem = [
                    "taskId" => $add,
                    "iBlockId" =>  $arIblock['ID']
                ];
                $itemId = $add;

                $result = \Bitrix\Main\UserGroupTable::getList(array(
                    'filter' => array('GROUP_ID'=>8,'USER.ACTIVE'=>'Y'),
                    'select' => array('USER_ID','NAME'=>'USER.NAME','LAST_NAME'=>'USER.LAST_NAME'), // выбираем идентификатор п-ля, имя и фамилию
                    'order' => array('USER.ID'=>'DESC'), // сортируем по идентификатору пользователя
                ));

                $arGroupUsers  = [];
                while ($arGroup = $result->fetch())
                {
                    $arGroupUsers[] = $arGroup['USER_ID'];
                }

//если не заполнен профиль  / добавляю заявку с статусом отклонен модератором  -  и отправляю сообщение пользователю что заявка отклонена
                if(empty($arUserNNk['PERSONAL_PHONE'])){
                    $arGroupUsers  = [];
                    $arGroupUsers[] = $this->userId;
                    $allItems[] =  \Democontent2\Pi\Notifications::addNewNotificMess( $arGroupUsers  , 'moderationRefusal' , $arrItem , $itemId );
                }else{
                    //иначе отправляю модераторам уведомление и сохраняю  завявку с статусом 1


                    $allItems[] =  \Democontent2\Pi\Notifications::addNewNotificMess( $arGroupUsers  , 'addTask' , $arrItem , $itemId );
                }




                if (!intval($add)) {

                    try {
                        Logger::add(
                            $this->userId,
                            'addItemError',
                            [
                                'userId' => $this->userId,
                                'iBlockId' => $this->iBlockId,
                                'data' => $data,
                                'error' => $el->LAST_ERROR
                            ]
                        );

                        $event = new EventManager(
                            'addItemError',
                            [
                                'userId' => $this->userId,
                                'iBlockId' => $this->iBlockId,
                                'data' => $data,
                                'error' => $el->LAST_ERROR
                            ]
                        );
                        $event->execute();
                    } catch (\Exception $e) {
                    }

                } else {
                    $this->itemId = intval($add);

                    $this->addStages($stages);

                    if (intval(Option::get(DSPI, 'response_checklists'))) {
                        $responseCheckList = new \Democontent2\Pi\CheckList\Response(intval($add));
                        $responseCheckList->add(new ParameterDictionary($data));
                    }

                    if (isset($userParams['UF_DSPI_LIMIT']) && intval($userParams['UF_DSPI_LIMIT']) > 0) {
                        if (($userItemsCount + 1) > intval(Option::get(DSPI, 'free_limit'))
                            && intval(Option::get(DSPI, 'upper_limit_cost')) > 0) {
                            \CIBlockElement::SetPropertyValuesEx(
                                intval($add),
                                $this->iBlockId,
                                [
                                    '__hidden_payed' => 0
                                ]
                            );
                            $el->Update(
                                intval($add),
                                [
                                    'TIMESTAMP_X' => DateTime::createFromTimestamp(time())
                                ]
                            );

                            $order = new Order($this->userId);
                            $order->setType('new_item');
                            $order->setSum(intval(Option::get(DSPI, 'upper_limit_cost')));
                            $order->setDescription(
                                Loc::getMessage(
                                    'ITEM_ORDER_DESCRIPTION',
                                    [
                                        '#ID#' => intval($add)
                                    ]
                                )
                            );
                            $order->setAdditionalParams(
                                [
                                    'iBlockId' => $this->iBlockId,
                                    'itemId' => $this->itemId,
                                    'userId' => $this->userId,
                                    'period' => intval(Option::get(DSPI, 'item_period'))
                                ]
                            );
                            $order->make();
                            if ($order->getRedirect()) {
                                $this->paymentRedirect = $order->getRedirect();
                            }
                        }
                    } else {
                        if (($userItemsCount + 1) > intval(Option::get(DSPI, 'free_limit'))
                            && intval(Option::get(DSPI, 'upper_limit_cost')) > 0) {
                            \CIBlockElement::SetPropertyValuesEx(
                                intval($add),
                                $this->iBlockId,
                                [
                                    '__hidden_payed' => 0
                                ]
                            );
                            $el->Update(
                                intval($add),
                                [
                                    'TIMESTAMP_X' => DateTime::createFromTimestamp(time())
                                ]
                            );

                            $order = new Order($this->userId);
                            $order->setType('new_item');
                            $order->setSum(intval(Option::get(DSPI, 'upper_limit_cost')));
                            $order->setDescription(
                                Loc::getMessage(
                                    'ITEM_ORDER_DESCRIPTION',
                                    [
                                        '#ID#' => intval($add)
                                    ]
                                )
                            );
                            $order->setAdditionalParams(
                                [
                                    'iBlockId' => $this->iBlockId,
                                    'itemId' => $this->itemId,
                                    'userId' => $this->userId,
                                    'period' => intval(Option::get(DSPI, 'item_period'))
                                ]
                            );
                            $order->make(false , $props);
                            if ($order->getRedirect()) {
                                $this->paymentRedirect = $order->getRedirect();
                            }
                        }
                    }

                    if ($propertyValues['__hidden_moderation'] > 0) {
                        Event::send(
                            [
                                'EVENT_NAME' => 'DSPI_NEW_MODERATION',
                                'LID' => Application::getInstance()->getContext()->getSite(),
                                'C_FIELDS' => [
                                    'ID' => intval($add),
                                    'IBLOCK_ID' => $this->iBlockId,
                                    'IBLOCK_TYPE' => $this->iBlockType
                                ]
                            ]
                        );
                    }

                    try {
                        Logger::add(
                            $this->userId,
                            'addItemSuccess',
                            [
                                'userId' => $this->userId,
                                'iBlockId' => $this->iBlockId,
                                'id' => intval($add),
                                'userItemsCount' => ($userItemsCount + 1)
                            ]
                        );

                        $event = new EventManager(
                            'addItemSuccess',
                            [
                                'userId' => $this->userId,
                                'iBlockId' => $this->iBlockId,
                                'id' => intval($add),
                                'userItemsCount' => ($userItemsCount + 1)
                            ]
                        );
                        $event->execute();
                    } catch (\Exception $e) {
                    }
                }

            } else {

                if (count($errors) > 0) {

                    $this->errors = $errors;
                    try {
                        Logger::add(
                            $this->userId,
                            'prepareAddItemError',
                            [
                                'data' => $data,
                                'errors' => $errors
                            ]
                        );

                        $event = new EventManager(
                            'prepareAddItemError',
                            [
                                'userId' => $this->userId,
                                'iBlockId' => $this->iBlockId,
                                'data' => $data,
                                'errors' => $errors
                            ]
                        );
                        $event->execute();
                    } catch (\Exception $e) {
                    }
                }
            }
        }
    }

    public function addToTemp($data, $filesData, $hash)
    {
        if (isset($data['iblock']) && intval($data['iblock']) > 0) {
            $iBlock = new Iblock(0);
            $ibl = $iBlock->getAllIds();
            if (in_array(intval($data['iblock']), $ibl)) {
                $this->iBlockId = intval($data['iblock']);
            }
        }

        if ($this->iBlockId > 0) {
            $stages = [];
            $errors = [];
            $files = [];
            $hiddenFiles = [];
            $allow = true;
            $city = new City();
            $city->setTtl(0);
            $properties = new Properties($this->iBlockId);
            $properties->setTtl(0);
            $propertiesList = $properties->all();
            $cityParams = $city->getDefault();

            if (isset($data['city']) && intval($data['city']) > 0) {
                $getCity = $city->getById(intval($data['city']));
                if (count($getCity) > 0) {
                    $cityParams = $getCity;
                }
                unset($getCity);
            }

            $location = [];
            if ($data['location'] && strlen($data['location']) > 0) {
                $ex = explode(',', $data['location']);
                if (count($ex) == 2) {
                    if (floatval(trim($ex[0])) > 0 && floatval(trim($ex[1])) > 0) {
                        $location[] = floatval(trim($ex[0]));
                        $location[] = floatval(trim($ex[1]));
                    }
                }
            }

            $dateStart = '';
            $dateEnd = '';

            $propertyValues = [
                '__hidden_city' => intval($cityParams['id']),
                '__hidden_user' => 0,
                '__hidden_price' => (isset($data['price']) && floatval($data['price']) > 0) ? floatval($data['price']) : 0,
                '__hidden_moderation' => intval(Option::get(DSPI, 'moderation_new')),
                '__hidden_archive' => 0,
                '__hidden_payed' => 1,
                '__hidden_location' => (count($location) == 2) ? implode(',', $location) : '',
                '__hidden_begin_with' => false,
                '__hidden_run_up' => false,
                '__hidden_security' => 0
            ];

            $statusEnums = \CIBlockPropertyEnum::GetList(
                [
                    'SORT' => 'ASC'
                ],
                [
                    'IBLOCK_ID' => $this->iBlockId,
                    'CODE' => '__hidden_status'
                ]
            );
            while ($statusEnumsRes = $statusEnums->GetNext()) {
                if ($statusEnumsRes['XML_ID'] == '__status_1') {
                    $propertyValues['__hidden_status'] = $statusEnumsRes['ID'];
                    break;
                }
            }

            if (isset($data['dateStart'])) {
                $dateStart = $data['dateStart'] . ' 00:00:00';

                if (isset($data['timeStart'])) {
                    $tmp = explode(':', $data['timeStart']);
                    if (count($tmp) == 2) {
                        switch ($tmp[0]) {
                            case '00':
                            case '01':
                            case '02':
                            case '03':
                            case '04':
                            case '05':
                            case '06':
                            case '07':
                            case '08':
                            case '09':
                            case '10':
                            case '11':
                            case '12':
                            case '13':
                            case '14':
                            case '15':
                            case '16':
                            case '17':
                            case '18':
                            case '19':
                            case '20':
                            case '21':
                            case '22':
                            case '23':
                                if (intval($tmp[1]) >= 0 && intval($tmp[1]) <= 59) {
                                    $dateStart = $data['dateStart'] . ' ' . $tmp[0] . ':' . $tmp[1] . ':00';
                                }
                                break;
                        }
                    }
                }
            }

            if (isset($data['dateEnd'])) {
                $dateEnd = $data['dateEnd'] . ' 00:00:00';

                if (isset($data['timeEnd'])) {
                    $tmp = explode(':', $data['timeEnd']);
                    if (count($tmp) == 2) {
                        switch ($tmp[0]) {
                            case '00':
                            case '01':
                            case '02':
                            case '03':
                            case '04':
                            case '05':
                            case '06':
                            case '07':
                            case '08':
                            case '09':
                            case '10':
                            case '11':
                            case '12':
                            case '13':
                            case '14':
                            case '15':
                            case '16':
                            case '17':
                            case '18':
                            case '19':
                            case '20':
                            case '21':
                            case '22':
                            case '23':
                                if (intval($tmp[1]) >= 0 && intval($tmp[1]) <= 59) {
                                    $dateEnd = $data['dateEnd'] . ' ' . $tmp[0] . ':' . $tmp[1] . ':00';
                                }
                                break;
                        }
                    }
                }
            }

            if (strlen($dateStart) > 0 && strlen($dateEnd) > 0) {
                if (strtotime($dateStart) !== strtotime($dateEnd)) {
                    if (strtotime($dateEnd) > strtotime($dateStart)) {
                        $propertyValues['__hidden_begin_with'] = DateTime::createFromTimestamp(strtotime($dateStart));
                        $propertyValues['__hidden_run_up'] = DateTime::createFromTimestamp(strtotime($dateEnd));
                    } else {
                        $propertyValues['__hidden_begin_with'] = DateTime::createFromTimestamp(strtotime($dateEnd));
                        $propertyValues['__hidden_run_up'] = DateTime::createFromTimestamp(strtotime($dateStart));
                    }
                } else {
                    $propertyValues['__hidden_begin_with'] = DateTime::createFromTimestamp(strtotime($dateStart));
                }
            } else {
                if (strlen($dateStart) > 0) {
                    $propertyValues['__hidden_begin_with'] = DateTime::createFromTimestamp(strtotime($dateStart));
                }

                if (strlen($dateEnd) > 0) {
                    $propertyValues['__hidden_run_up'] = DateTime::createFromTimestamp(strtotime($dateEnd));
                }
            }

            if (!isset($data['name']) || !strlen($data['name'])) {
                $allow = false;
                $errors['properties'][] = [
                    'id' => 'name',
                    'type' => 'isset property'
                ];
            }

            foreach ($propertiesList as $propertyId => $propertyValue) {
                if ($propertyValue['isRequired'] > 0) {
                    if (!isset($data['prop'][$propertyId])) {
                        $allow = false;
                        $errors['properties'][] = [
                            'id' => $propertyId,
                            'type' => 'isset property'
                        ];
                    }
                }

                switch ($propertyValue['type']) {
                    case 'string':
                        if ($propertyValue['isRequired'] > 0) {
                            if (strlen($data['prop'][$propertyId]) > 0) {
                                $propertyValues[$propertyId] = htmlspecialcharsbx($data['prop'][$propertyId]);
                            } else {
                                $allow = false;
                                $errors['properties'][] = [
                                    'id' => $propertyId,
                                    'type' => 'empty'
                                ];
                            }
                        } else {
                            if (strlen($data['prop'][$propertyId]) > 0) {
                                $propertyValues[$propertyId] = htmlspecialcharsbx($data['prop'][$propertyId]);
                            }
                        }
                        break;
                    case 'integer':
                        if ($propertyValue['isRequired'] > 0) {
                            if (floatval($data['prop'][$propertyId]) > 0) {
                                $propertyValues[$propertyId] = floatval($data['prop'][$propertyId]);
                            } else {
                                $allow = false;
                                $errors['properties'][] = [
                                    'id' => $propertyId,
                                    'type' => 'float'
                                ];
                            }
                        } else {
                            if (floatval($data['prop'][$propertyId]) > 0) {
                                $propertyValues[$propertyId] = floatval($data['prop'][$propertyId]);
                            }
                        }
                        break;
                    case 'list':
                        if ($propertyValue['isRequired'] > 0) {
                            if (intval($data['prop'][$propertyId]) > 0) {
                                if (isset($propertyValue['values'][intval($data['prop'][$propertyId])])) {
                                    $propertyValues[$propertyId] = intval($data['prop'][$propertyId]);
                                } else {
                                    $allow = false;
                                    $errors['properties'][] = [
                                        'id' => $propertyId,
                                        'type' => 'isset value'
                                    ];
                                }
                            } else {
                                $allow = false;
                                $errors['properties'][] = [
                                    'id' => $propertyId,
                                    'type' => 'int'
                                ];
                            }
                        } else {
                            if (intval($data['prop'][$propertyId]) > 0) {
                                if (isset($propertyValue['values'][intval($data['prop'][$propertyId])])) {
                                    $propertyValues[$propertyId] = intval($data['prop'][$propertyId]);
                                }
                            }
                        }
                        break;
                }
            }

            if (isset($filesData['__files'])) {
                if (count($filesData['__files']['name']) > 0) {
                    foreach ($filesData['__files']['name'] as $fileKey => $fileValue) {
                        $extension = Utils::getExtension($fileValue);
                        if (strlen($extension) > 0) {
                            $fileArray = \CFile::MakeFileArray($filesData['__files']['tmp_name'][$fileKey]);
                            $fileArray['description'] = $fileValue;
                            $fileArray['name'] = md5(microtime(true)) . '-' . ToLower(randString(rand(5, 10))) . $extension;

                            $fileId = \CFile::SaveFile($fileArray, DSPI);
                            if (intval($fileId) > 0) {
                                $files[intval($fileId)] = $fileValue;
                            }
                        }
                    }
                }
            }

            if (isset($filesData['__hiddenFiles'])) {
                if (count($filesData['__hiddenFiles']['name']) > 0) {
                    foreach ($filesData['__hiddenFiles']['name'] as $fileKey => $fileValue) {
                        $extension = Utils::getExtension($fileValue);
                        if (strlen($extension) > 0) {
                            $fileArray = \CFile::MakeFileArray($filesData['__hiddenFiles']['tmp_name'][$fileKey]);
                            $fileArray['description'] = $fileValue;
                            $fileArray['name'] = md5(microtime(true)) . '-' . ToLower(randString(rand(5, 10))) . $extension;

                            $fileId = \CFile::SaveFile($fileArray, DSPI);
                            if (intval($fileId) > 0) {
                                $hiddenFiles[intval($fileId)] = $fileValue;
                            }
                        }
                    }
                }
            }

            /*if (count($files) > 0) {
                $propertyValues['files'] = $files;
            }

            if (count($hiddenFiles) > 0) {
                $propertyValues['hidden_files'] = $hiddenFiles;
            }*/

            $security = false;

            if (strlen(Option::get(DSPI, 'safeCrowApiKey')) > 0
                && strlen(Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
                $security = true;

                if (isset($data['security']) && strlen($data['security']) > 0) {
                    $propertyValues['__hidden_security'] = 1;

                    if ($propertyValues['__hidden_price'] <= 0) {
                        $propertyValues['__hidden_security'] = 0;
                    }
                } else {
                    $security = false;
                }
            }

            if (isset($data['stages'])) {
                foreach ($data['stages'] as $stageKey => $stageValue) {
                    if (isset($stageValue['name']) && strlen($stageValue['name']) > 0) {
                        $stages[] = [
                            'name' => strip_tags(HTMLToTxt($stageValue['name'])),
                            'price' => (isset($stageValue['price'])) ? intval($stageValue['price']) : 0
                        ];

                        if (!isset($stageValue['price']) || !intval($stageValue['price'])) {
                            $security = false;
                        }
                    }
                }

                if (!count($stages)) {
                    $allow = false;
                    $errors['stages'] = 'empty';
                }
            }

            if (count($stages) > 0) {
                $propertyValues['__hidden_price'] = 0;

                if (!$security) {
                    $propertyValues['__hidden_security'] = 0;
                } else {
                    $propertyValues['__hidden_security'] = 1;
                }
            }

            if ($allow) {
                $tempData = [
                    'IBLOCK_ID' => $this->iBlockId,
                    'IBLOCK_SECTION_ID' => false,
                    'NAME' => strip_tags(HTMLToTxt($data['name'])),
                    'CODE' => ToLower(\CUtil::translit(htmlspecialcharsbx(trim($data['name'])), 'ru', Utils::tr())),
                    'ACTIVE_FROM' => false,
                    'ACTIVE_TO' => false,
                    'ACTIVE' => 'Y',
                    'DETAIL_TEXT' => HTMLToTxt(str_replace("\r\n", '<br>', strip_tags($data['description']))),
                    'DETAIL_PICTURE' => false,
                    'PROPERTY_VALUES' => $propertyValues,
                    'stages' => $stages, //TODO ������� ��� ��������� ��� ������������ ���������� � ��������
                    'files' => $files,
                    'hidden_files' => $hiddenFiles
                ];

                $temporaryTask = new TemporaryTask();
                $temporaryTask->setHash($hash);
                $temporaryTask->setData($tempData);
                $temporaryTask->add();
            }
        }
    }

    public function completed($user1 = 0, $user2 = 0)
    {
        if ($this->iBlockId > 0 && $this->itemId > 0) {
            try {
                $request = Application::getInstance()->getContext()->getRequest();
                $statusEnums = \CIBlockPropertyEnum::GetList(
                    [
                        'SORT' => 'ASC'
                    ],
                    [
                        'IBLOCK_ID' => $this->iBlockId,
                        'CODE' => '__hidden_status'
                    ]
                );
                while ($statusEnumsRes = $statusEnums->GetNext()) {
                    if ($statusEnumsRes['XML_ID'] == '__status_3') {
                        \CIBlockElement::SetPropertyValuesEx(
                            $this->itemId,
                            $this->iBlockId,
                            [
                                '__hidden_status' => $statusEnumsRes['ID']
                            ]
                        );

                        $el = new \CIBlockElement();
                        $update = $el->Update(
                            $this->itemId,
                            [
                                'TIMESTAMP_X' => DateTime::createFromTimestamp(time())
                            ]
                        );

                        $us = new User(0);

                        if ($update) {
                            if (intval($user1) > 0) {
                                Notifications::add(
                                    $user1,
                                    'taskIsClosed',
                                    [
                                        'taskId' => $this->itemId,
                                        'iBlockId' => $this->iBlockId
                                    ]
                                );

                                $fb = new FireBase($user1);
                                $fb->webPush([
                                    'title' => Loc::getMessage('PUSH_CLOSE_TASK_TITLE'),
                                    'body' => Loc::getMessage('PUSH_CLOSE_TASK_BODY', ['{{TASK_ID}}' => $this->itemId])
                                ]);
                                unset($fb);

                                $us->setId($user1);
                                $userParams = $us->get();
                                if (count($userParams) > 0) {
                                    Event::send(
                                        [
                                            'EVENT_NAME' => 'DSPI_TASK_CLOSED',
                                            'LID' => Application::getInstance()->getContext()->getSite(),
                                            'C_FIELDS' => [
                                                'EMAIL_TO' => $userParams['EMAIL'],
                                                'ITEM_ID' => $this->itemId,
                                                'FULL_URL' => (($request->isHttps()) ? 'https://' : 'http://')
                                                    . Path::normalize($request->getHttpHost() . SITE_DIR
                                                        . 'task' . $this->itemId . '-' . $this->iBlockId) . '/'
                                            ]
                                        ]
                                    );
                                }
                            }

                            if (intval($user2) > 0) {
                                Notifications::add(
                                    $user2,
                                    'taskIsClosed',
                                    [
                                        'taskId' => $this->itemId,
                                        'iBlockId' => $this->iBlockId
                                    ]
                                );

                                $fb = new FireBase($user2);
                                $fb->webPush([
                                    'title' => Loc::getMessage('PUSH_CLOSE_TASK_TITLE'),
                                    'body' => Loc::getMessage('PUSH_CLOSE_TASK_BODY', ['{{TASK_ID}}' => $this->itemId])
                                ]);
                                unset($fb);

                                $us->setId($user2);
                                $userParams = $us->get();
                                if (count($userParams) > 0) {
                                    Event::send(
                                        [
                                            'EVENT_NAME' => 'DSPI_TASK_CLOSED',
                                            'LID' => Application::getInstance()->getContext()->getSite(),
                                            'C_FIELDS' => [
                                                'EMAIL_TO' => $userParams['EMAIL'],
                                                'ITEM_ID' => $this->itemId,
                                                'FULL_URL' => (($request->isHttps()) ? 'https://' : 'http://')
                                                    . Path::normalize($request->getHttpHost() . SITE_DIR
                                                        . 'task' . $this->itemId . '-' . $this->iBlockId) . '/'
                                            ]
                                        ]
                                    );
                                }
                            }
                        }
                        break;
                    }
                }
            } catch (\Exception $e) {

            }
        }
    }

    /**
     * @param int $user1
     */
    public function executorCompleted($user1 = 0)
    {
        if ($this->iBlockId > 0 && $this->itemId > 0) {
            if ($this->getCurrentStatus() == 2) {
                try {
                    $request = Application::getInstance()->getContext()->getRequest();
                    $statusEnums = \CIBlockPropertyEnum::GetList(
                        [
                            'SORT' => 'ASC'
                        ],
                        [
                            'IBLOCK_ID' => $this->iBlockId,
                            'CODE' => '__hidden_status'
                        ]
                    );
                    while ($statusEnumsRes = $statusEnums->GetNext()) {
                        if ($statusEnumsRes['XML_ID'] == '__status_5') {
                            \CIBlockElement::SetPropertyValuesEx(
                                $this->itemId,
                                $this->iBlockId,
                                [
                                    '__hidden_status' => $statusEnumsRes['ID']
                                ]
                            );

                            $el = new \CIBlockElement();
                            $update = $el->Update(
                                $this->itemId,
                                [
                                    'TIMESTAMP_X' => DateTime::createFromTimestamp(time())
                                ]
                            );

                            if ($update) {
                                if (intval($user1) > 0) {
                                    Notifications::add(
                                        $user1,
                                        'executorCompleted',
                                        [
                                            'taskId' => $this->itemId,
                                            'iBlockId' => $this->iBlockId
                                        ]
                                    );

                                    $fb = new FireBase($user1);
                                    $fb->webPush([
                                        'title' => Loc::getMessage('PUSH_EXECUTOR_COMPLETE_TASK_TITLE'),
                                        'body' => Loc::getMessage('PUSH_EXECUTOR_COMPLETE_TASK_BODY', ['{{TASK_ID}}' => $this->itemId])
                                    ]);
                                    unset($fb);

                                    $us = new User($user1);
                                    $userParams = $us->get();
                                    if (count($userParams) > 0) {
                                        Event::send(
                                            [
                                                'EVENT_NAME' => 'DSPI_EXECUTOR_TASK_END',
                                                'LID' => Application::getInstance()->getContext()->getSite(),
                                                'C_FIELDS' => [
                                                    'EMAIL_TO' => $userParams['EMAIL'],
                                                    'ITEM_ID' => $this->itemId,
                                                    'FULL_URL' => (($request->isHttps()) ? 'https://' : 'http://')
                                                        . Path::normalize($request->getHttpHost() . SITE_DIR
                                                            . 'task' . $this->itemId . '-' . $this->iBlockId) . '/'
                                                ]
                                            ]
                                        );
                                    }
                                }
                            }
                            break;
                        }
                    }
                } catch (\Exception $e) {

                }
            }
        }
    }

    public function setTaskComplain($text, $user1 = 0, $user2 = 0)
    {
        if ($this->iBlockId > 0 && $this->itemId > 0 && strlen($text) > 0) {
            $currentStatus = $this->getCurrentStatus();
            switch ($currentStatus) {
                case 2:
                case 4:
                    try {
                        $request = Application::getInstance()->getContext()->getRequest();
                        $statusEnums = \CIBlockPropertyEnum::GetList(
                            [
                                'SORT' => 'ASC'
                            ],
                            [
                                'IBLOCK_ID' => $this->iBlockId,
                                'CODE' => '__hidden_status'
                            ]
                        );
                        while ($statusEnumsRes = $statusEnums->GetNext()) {
                            if ($statusEnumsRes['XML_ID'] == '__status_6') {
                                \CIBlockElement::SetPropertyValuesEx(
                                    $this->itemId,
                                    $this->iBlockId,
                                    [
                                        '__hidden_status' => $statusEnumsRes['ID']
                                    ]
                                );

                                $el = new \CIBlockElement();
                                $update = $el->Update(
                                    $this->itemId,
                                    [
                                        'TIMESTAMP_X' => DateTime::createFromTimestamp(time())
                                    ]
                                );

                                if ($update) {
                                    $us = new User(0);

                                    if (intval($user1) > 0) {
                                        Notifications::add(
                                            $user1,
                                            'taskComplainOpen',
                                            [
                                                'taskId' => $this->itemId,
                                                'iBlockId' => $this->iBlockId
                                            ]
                                        );

                                        $fb = new FireBase($user1);
                                        $fb->webPush([
                                            'title' => Loc::getMessage('PUSH_COMPLAIN_TASK_TITLE'),
                                            'body' => Loc::getMessage('PUSH_COMPLAIN_TASK_BODY', ['{{TASK_ID}}' => $this->itemId])
                                        ]);
                                        unset($fb);

                                        $us->setId($user1);
                                        $userParams = $us->get();
                                        if (count($userParams) > 0) {
                                            Event::send(
                                                [
                                                    'EVENT_NAME' => 'DSPI_TASK_COMPLAIN',
                                                    'LID' => Application::getInstance()->getContext()->getSite(),
                                                    'C_FIELDS' => [
                                                        'EMAIL_TO' => $userParams['EMAIL'],
                                                        'ITEM_ID' => $this->itemId,
                                                        'TEXT' => strip_tags(HTMLToTxt($text)),
                                                        'FULL_URL' => (($request->isHttps()) ? 'https://' : 'http://')
                                                            . Path::normalize($request->getHttpHost() . SITE_DIR
                                                                . 'task' . $this->itemId . '-' . $this->iBlockId) . '/'
                                                    ]
                                                ]
                                            );
                                        }
                                    }

                                    if (intval($user2) > 0) {
                                        Notifications::add(
                                            $user2,
                                            'taskComplainOpen',
                                            [
                                                'taskId' => $this->itemId,
                                                'iBlockId' => $this->iBlockId
                                            ]
                                        );

                                        $fb = new FireBase($user2);
                                        $fb->webPush([
                                            'title' => Loc::getMessage('PUSH_COMPLAIN_TASK_TITLE'),
                                            'body' => Loc::getMessage('PUSH_COMPLAIN_TASK_BODY', ['{{TASK_ID}}' => $this->itemId])
                                        ]);
                                        unset($fb);

                                        $us->setId($user2);
                                        $userParams = $us->get();
                                        if (count($userParams) > 0) {
                                            Event::send(
                                                [
                                                    'EVENT_NAME' => 'DSPI_TASK_COMPLAIN',
                                                    'LID' => Application::getInstance()->getContext()->getSite(),
                                                    'C_FIELDS' => [
                                                        'EMAIL_TO' => $userParams['EMAIL'],
                                                        'ITEM_ID' => $this->itemId,
                                                        'TEXT' => strip_tags(HTMLToTxt($text)),
                                                        'FULL_URL' => (($request->isHttps()) ? 'https://' : 'http://')
                                                            . Path::normalize($request->getHttpHost() . SITE_DIR
                                                                . 'task' . $this->itemId . '-' . $this->iBlockId) . '/'
                                                    ]
                                                ]
                                            );
                                        }
                                    }
                                }
                                break;
                            }
                        }
                    } catch (\Exception $e) {

                    }
                    break;
            }
        }
    }

    public function setStageComplain($stageId, $text, $user1 = 0, $user2 = 0)
    {
        if ($this->iBlockId > 0 && $this->itemId > 0 && strlen($text) > 0) {
            $currentStatus = $this->getCurrentStatus();
            switch ($currentStatus) {
                case 2:
                    try {
                        $request = Application::getInstance()->getContext()->getRequest();
                        $statusEnums = \CIBlockPropertyEnum::GetList(
                            [
                                'SORT' => 'ASC'
                            ],
                            [
                                'IBLOCK_ID' => $this->iBlockId,
                                'CODE' => '__hidden_status'
                            ]
                        );
                        while ($statusEnumsRes = $statusEnums->GetNext()) {
                            if ($statusEnumsRes['XML_ID'] == '__status_6') {
                                \CIBlockElement::SetPropertyValuesEx(
                                    $this->itemId,
                                    $this->iBlockId,
                                    [
                                        '__hidden_status' => $statusEnumsRes['ID']
                                    ]
                                );

                                $el = new \CIBlockElement();
                                $el->Update(
                                    $this->itemId,
                                    [
                                        'TIMESTAMP_X' => DateTime::createFromTimestamp(time())
                                    ]
                                );

                                $this->setStagesClass();
                                if ($this->stagesClass !== null) {
                                    try {
                                        $obj = $this->stagesClass;
                                        $get = $obj::getList(
                                            [
                                                'select' => ['ID'],
                                                'filter' => [
                                                    '=ID' => $stageId,
                                                    '=UF_TASK_ID' => $this->itemId
                                                ],
                                                'limit' => 1
                                            ]
                                        );
                                        while ($res = $get->fetch()) {
                                            $obj::update(
                                                $res['ID'],
                                                [
                                                    'UF_STATUS' => 2
                                                ]
                                            );
                                        }
                                    } catch (\Exception $e) {

                                    }
                                }

                                $us = new User(0);

                                if (intval($user1) > 0) {
                                    Notifications::add(
                                        $user1,
                                        'stageComplainOpen',
                                        [
                                            'taskId' => $this->itemId,
                                            'iBlockId' => $this->iBlockId,
                                            'stageId' => $stageId
                                        ]
                                    );

                                    $fb = new FireBase($user1);
                                    $fb->webPush([
                                        'title' => Loc::getMessage('PUSH_COMPLAIN_STAGE_TITLE'),
                                        'body' => Loc::getMessage('PUSH_COMPLAIN_STAGE_BODY', ['{{STAGE_ID}}' => $stageId, '{{TASK_ID}}' => $this->itemId])
                                    ]);
                                    unset($fb);

                                    $us->setId($user1);
                                    $userParams = $us->get();
                                    if (count($userParams) > 0) {
                                        Event::send(
                                            [
                                                'EVENT_NAME' => 'DSPI_STAGE_COMPLAIN',
                                                'LID' => Application::getInstance()->getContext()->getSite(),
                                                'C_FIELDS' => [
                                                    'EMAIL_TO' => $userParams['EMAIL'],
                                                    'ITEM_ID' => $this->itemId,
                                                    'TEXT' => strip_tags(HTMLToTxt($text)),
                                                    'FULL_URL' => (($request->isHttps()) ? 'https://' : 'http://')
                                                        . Path::normalize($request->getHttpHost() . SITE_DIR
                                                            . 'task' . $this->itemId . '-' . $this->iBlockId) . '/'
                                                ]
                                            ]
                                        );
                                    }
                                }

                                if (intval($user2) > 0) {
                                    Notifications::add(
                                        $user2,
                                        'stageComplainOpen',
                                        [
                                            'taskId' => $this->itemId,
                                            'iBlockId' => $this->iBlockId,
                                            'stageId' => $stageId
                                        ]
                                    );

                                    $fb = new FireBase($user2);
                                    $fb->webPush([
                                        'title' => Loc::getMessage('PUSH_COMPLAIN_STAGE_TITLE'),
                                        'body' => Loc::getMessage('PUSH_COMPLAIN_STAGE_BODY', ['{{STAGE_ID}}' => $stageId, '{{TASK_ID}}' => $this->itemId])
                                    ]);
                                    unset($fb);

                                    $us->setId($user2);
                                    $userParams = $us->get();
                                    if (count($userParams) > 0) {
                                        Event::send(
                                            [
                                                'EVENT_NAME' => 'DSPI_STAGE_COMPLAIN',
                                                'LID' => Application::getInstance()->getContext()->getSite(),
                                                'C_FIELDS' => [
                                                    'EMAIL_TO' => $userParams['EMAIL'],
                                                    'ITEM_ID' => $this->itemId,
                                                    'TEXT' => strip_tags(HTMLToTxt($text)),
                                                    'FULL_URL' => (($request->isHttps()) ? 'https://' : 'http://')
                                                        . Path::normalize($request->getHttpHost() . SITE_DIR
                                                            . 'task' . $this->itemId . '-' . $this->iBlockId) . '/'
                                                ]
                                            ]
                                        );
                                    }
                                }
                                break;
                            }
                        }
                    } catch (\Exception $e) {

                    }
                    break;
            }
        }
    }
}