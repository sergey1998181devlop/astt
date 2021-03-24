<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.04.2019
 * Time: 08:07
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Profile\Portfolio;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Type\ParameterDictionary;
use Democontent2\Pi\Hl;

class Category
{
    const TABLE_NAME = 'Democontentpiportfoliocat';

    private $ttl = 0;
    private $userId = 0;
    private $obj = null;

    /**
     * Category constructor.
     * @param $userId
     * @param int $ttl
     */
    public function __construct($userId, $ttl = 0)
    {
        $this->userId = intval($userId);
        $this->ttl = intval($ttl);

        $hl = new Hl(static::TABLE_NAME);
        if ($hl->obj !== null) {
            $this->obj = $hl->obj;
        } else {
            $className = ToUpper(end(explode('\\', __CLASS__)));
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
                    'UF_NAME' => [
                        'Y',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => ''],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_NAME')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_NAME')]
                        ]
                    ],
                    'UF_ACTIVE' => [
                        'N',
                        'boolean',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => false],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ACTIVE')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ACTIVE')]
                        ]
                    ],
                    'UF_SORT' => [
                        'N',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 500],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_SORT')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_SORT')]
                        ]
                    ],
                ],
                [
                    'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_NAME` VARCHAR(255);',
                ],
                [
                    ['UF_CREATED_AT'],
                    ['UF_USER_ID'],
                    ['UF_ACTIVE'],
                    ['UF_SORT'],
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );
            if ($add) {
                $this->__construct($userId, $ttl);
            }
        }
    }

    public function add(ParameterDictionary $data)
    {
        $result = 0;
        if ($this->obj !== null && $this->userId > 0 && $data->valid() && $data->get('categoryName')) {
            try {
                $obj = $this->obj;
                $add = $obj::add(
                    [
                        'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                        'UF_USER_ID' => $this->userId,
                        'UF_NAME' => trim(strip_tags(HTMLToTxt($data->get('categoryName')))),
                        'UF_ACTIVE' => true,
                        'UF_SORT' => 500
                    ]
                );
                if ($add->isSuccess()) {
                    $result = $add->getId();
                    Application::getInstance()->getTaggedCache()->clearByTag('portfolio_' . $this->userId);
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    public function remove($id)
    {
        $result = false;

        if ($this->obj !== null && $this->userId > 0 && intval($id) > 0) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID'
                        ],
                        'filter' => [
                            '=ID' => intval($id),
                            '=UF_USER_ID' => $this->userId
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $delete = $obj::delete($res['ID']);
                    if ($delete->isSuccess()) {
                        $files = new Files($this->userId, $res['ID']);
                        $files->removeAll();
                        $result = true;

                        Application::getInstance()->getTaggedCache()->clearByTag('portfolio_' . $this->userId);
                    }
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    public function get($id)
    {
        $result = [];

        if ($this->obj !== null && $this->userId > 0 && intval($id) > 0) {
            $cache = Application::getInstance()->getCache();
            $cache_time = $this->ttl;
            $cache_id = md5('portfolioCategory_' . $this->userId . intval($id));
            $cache_path = '/' . DSPI . '/portfolio';

            $taggedCache = Application::getInstance()->getTaggedCache();

            if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
                $res = $cache->getVars();
                if ($res[$cache_id]) {
                    $result = $res[$cache_id];
                }
            } else {
                $taggedCache->startTagCache($cache_path);
                $taggedCache->registerTag($cache_id);
                $taggedCache->registerTag('portfolio_' . $this->userId);

                try {
                    $obj = $this->obj;
                    $get = $obj::getList(
                        [
                            'select' => [
                                'ID',
                                'UF_NAME'
                            ],
                            'filter' => [
                                '=ID' => intval($id),
                                '=UF_USER_ID' => $this->userId,
                                '=UF_ACTIVE' => true
                            ],
                            'limit' => 1
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $files = new Files($this->userId, $res['ID']);

                        $result['id'] = $res['ID'];
                        $result['name'] = $res['UF_NAME'];
                        $result['files'] = $files->getList();

                        unset($files);
                    }
                } catch (\Exception $e) {

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

    public function getList()
    {
        $result = [];

        if ($this->obj !== null && $this->userId > 0) {
            $cache = Application::getInstance()->getCache();
            $cache_time = $this->ttl;
            $cache_id = md5('portfolioCategories_' . $this->userId);
            $cache_path = '/' . DSPI . '/portfolio';

            $taggedCache = Application::getInstance()->getTaggedCache();

            if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
                $res = $cache->getVars();
                if ($res[$cache_id]) {
                    $result = $res[$cache_id];
                }
            } else {
                $taggedCache->startTagCache($cache_path);
                $taggedCache->registerTag($cache_id);
                $taggedCache->registerTag('portfolio_' . $this->userId);

                try {
                    $obj = $this->obj;
                    $get = $obj::getList(
                        [
                            'select' => [
                                'ID',
                                'UF_CREATED_AT',
                                'UF_NAME'
                            ],
                            'filter' => [
                                '=UF_USER_ID' => $this->userId,
                                '=UF_ACTIVE' => true
                            ],
                            'order' => [
                                'UF_SORT' => 'ASC',
                                'UF_NAME' => 'ASC'
                            ]
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $result[] = $res;
                    }
                } catch (\Exception $e) {

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
}