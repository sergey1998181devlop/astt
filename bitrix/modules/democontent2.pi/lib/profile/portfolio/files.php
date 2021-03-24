<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.04.2019
 * Time: 18:16
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Profile\Portfolio;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Democontent2\Pi\Hl;

class Files
{
    const TABLE_NAME = 'Democontentpiportfoliofiles';

    private $userId = 0;
    private $categoryId = 0;
    private $obj = null;

    /**
     * Files constructor.
     * @param int $userId
     * @param int $categoryId
     */
    public function __construct($userId, $categoryId)
    {
        $this->userId = intval($userId);
        $this->categoryId = intval($categoryId);

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
                    'UF_CATEGORY_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CATEGORY_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CATEGORY_ID')]
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
                    'UF_FILE_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_FILE_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_FILE_ID')]
                        ]
                    ],
                    'UF_DESCRIPTION' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => ''],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DESCRIPTION')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DESCRIPTION')]
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
                    'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_DESCRIPTION` VARCHAR(255);',
                ],
                [
                    ['UF_CREATED_AT'],
                    ['UF_CATEGORY_ID'],
                    ['UF_USER_ID'],
                    ['UF_FILE_ID'],
                    ['UF_SORT'],
                    ['UF_USER_ID', 'UF_CATEGORY_ID'],
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );
            if ($add) {
                $this->__construct($userId, $categoryId);
            }
        }
    }


    public function add($fileId)
    {
        $result = 0;

        if ($this->obj !== null && $this->userId > 0 && $this->categoryId > 0 && intval($fileId) > 0) {
            try {
                $obj = $this->obj;
                $add = $obj::add(
                    [
                        'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                        'UF_CATEGORY_ID' => $this->categoryId,
                        'UF_USER_ID' => $this->userId,
                        'UF_FILE_ID' => intval($fileId),
                        'UF_DESCRIPTION' => '',
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

        if ($this->obj !== null && $this->userId > 0 && $this->categoryId > 0 && intval($id) > 0) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID',
                            'UF_FILE_ID'
                        ],
                        'filter' => [
                            '=ID' => intval($id),
                            '=UF_USER_ID' => $this->userId,
                            '=UF_CATEGORY_ID' => $this->categoryId
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $delete = $obj::delete($res['ID']);
                    if ($delete->isSuccess()) {
                        $result = true;
                        \CFile::Delete($res['UF_FILE_ID']);
                        Application::getInstance()->getTaggedCache()->clearByTag('portfolio_' . $this->userId);
                    }
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    public function removeAll()
    {
        if ($this->obj !== null && $this->userId > 0 && $this->categoryId > 0) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID',
                            'UF_FILE_ID'
                        ],
                        'filter' => [
                            '=UF_USER_ID' => $this->userId,
                            '=UF_CATEGORY_ID' => $this->categoryId
                        ]
                    ]
                );
                while ($res = $get->fetch()) {
                    $delete = $obj::delete($res['ID']);
                    if ($delete->isSuccess()) {
                        \CFile::Delete($res['UF_FILE_ID']);
                    }
                }
            } catch (\Exception $e) {

            }
        }
    }

    public function getList()
    {
        $result = [];

        if ($this->obj !== null && $this->userId > 0 && $this->categoryId > 0) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID',
                            'UF_FILE_ID',
                            'UF_DESCRIPTION'
                        ],
                        'filter' => [
                            '=UF_USER_ID' => $this->userId,
                            '=UF_CATEGORY_ID' => $this->categoryId
                        ],
                        'order' => [
                            'UF_SORT' => 'ASC',
                            'ID' => 'ASC'
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

    public function changeDescription($id, $description)
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
                    $update = $obj::update(
                        $res['ID'],
                        [
                            'UF_DESCRIPTION' => substr(trim(strip_tags(HTMLToTxt($description))), 0, 255)
                        ]
                    );
                    if ($update->isSuccess()) {
                        $result = true;
                        Application::getInstance()->getTaggedCache()->clearByTag('portfolio_' . $this->userId);
                    }
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    public function sort($ids)
    {
        $data = explode(',', $ids);
        if (count($data) > 0) {
            if ($this->obj !== null && $this->userId > 0 && $this->categoryId > 0) {
                try {
                    $sort = 0;
                    $obj = $this->obj;
                    foreach ($data as $id) {
                        if (intval($id) > 0) {
                            $get = $obj::getList(
                                [
                                    'select' => [
                                        'ID'
                                    ],
                                    'filter' => [
                                        '=ID' => intval($id),
                                        '=UF_USER_ID' => $this->userId,
                                        '=UF_CATEGORY_ID' => $this->categoryId
                                    ],
                                    'limit' => 1
                                ]
                            );
                            while ($res = $get->fetch()) {
                                $update = $obj::update(
                                    $res['ID'],
                                    [
                                        'UF_SORT' => $sort
                                    ]
                                );
                                if ($update->isSuccess()) {
                                    $sort++;
                                    Application::getInstance()->getTaggedCache()->clearByTag('portfolio_' . $this->userId);
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {

                }
            }
        }

        return;
    }
}