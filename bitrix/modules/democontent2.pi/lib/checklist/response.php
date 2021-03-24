<?php
/**
 * Date: 27.08.2019
 * Time: 18:39
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

namespace Democontent2\Pi\CheckList;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Type\ParameterDictionary;
use Democontent2\Pi\Hl;

class Response
{
    const TABLE_NAME = 'Democontentpiresponsechecklist';

    private $taskId = 0;
    private $obj = null;

    /**
     * Response constructor.
     * @param int $taskId
     */
    public function __construct(int $taskId)
    {
        $this->taskId = intval($taskId);

        $hl = new Hl(static::TABLE_NAME, 0);
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
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                        ]
                    ],
                    'UF_TASK_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TASK_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TASK_ID')],
                        ]
                    ],
                    'UF_NAME' => [
                        'Y',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => ''],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_NAME')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_NAME')],
                        ]
                    ],
                    'UF_REQUIRED' => [
                        'N',
                        'boolean',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => false],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_REQUIRED')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_REQUIRED')],
                        ]
                    ]
                ],
                [
                    'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_NAME` VARCHAR(255);',
                ],
                [
                    ['UF_CREATED_AT'],
                    ['UF_TASK_ID'],
                    ['UF_REQUIRED']
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );
            if ($add) {
                $this->__construct($taskId);
            }
        }
    }

    /**
     * @param ParameterDictionary $parameterDictionary
     * @return array
     */
    public function add(ParameterDictionary $parameterDictionary)
    {
        $result = [];
        if ($this->taskId > 0 && !is_null($this->obj)) {
            $obj = $this->obj;
            foreach ($parameterDictionary->get('response-checklist') as $item) {
                $name = trim(strip_tags(HTMLToTxt($item)));
                if (strlen($name)) {
                    try {
                        $add = $obj::add(
                            [
                                'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                                'UF_TASK_ID' => $this->taskId,
                                'UF_NAME' => $name,
                                'UF_REQUIRED' => false
                            ]
                        );

                        if ($add->isSuccess()) {
                            $result[] = [
                                'id' => $add->getId(),
                                'name' => $name
                            ];
                        }
                    } catch (\Exception $e) {
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param ParameterDictionary $parameterDictionary
     */
    public function update(ParameterDictionary $parameterDictionary)
    {
        if ($this->taskId > 0 && !is_null($this->obj)) {
            $obj = $this->obj;
            foreach ($parameterDictionary->get('response-checklist') as $key => $value) {
                $name = trim(strip_tags(HTMLToTxt($value)));
                if (intval($key)) {
                    try {
                        $get = $obj::getList(
                            [
                                'select' => ['ID', 'UF_NAME'],
                                'filter' => [
                                    '=ID' => intval($key),
                                    '=UF_TASK_ID' => $this->taskId
                                ]
                            ]
                        );
                        while ($res = $get->fetch()) {
                            if (strlen($name) > 0) {
                                $obj::update($res['ID'], ['UF_NAME' => $name]);
                            } else {
                                $obj::delete($res);
                            }
                        }
                    } catch (\Exception $e) {
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getList()
    {
        $result = [];
        if ($this->taskId > 0 && !is_null($this->obj)) {
            $obj = $this->obj;
            try {
                $get = $obj::getList(
                    [
                        'select' => ['ID', 'UF_CREATED_AT', 'UF_TASK_ID', 'UF_NAME'],
                        'filter' => [
                            '=UF_TASK_ID' => $this->taskId
                        ],
                        'order' => ['ID' => 'ASC']
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

        return $result;
    }

    public function deleteAll()
    {
        if ($this->taskId > 0 && !is_null($this->obj)) {
            $obj = $this->obj;
            try {
                $get = $obj::getList(
                    [
                        'select' => ['ID'],
                        'filter' => [
                            '=UF_TASK_ID' => $this->taskId
                        ]
                    ]
                );
                while ($res = $get->fetch()) {
                    $delete = $obj::delete($res['ID']);
                    if (!$delete->isSuccess()) {
                        //TODO handle errors
                    }
                }

                $responseValues = new ResponseValues($this->taskId, 0);
                $responseValues->deleteAll();
            } catch (ObjectPropertyException $e) {
            } catch (ArgumentException $e) {
            } catch (SystemException $e) {
            } catch (\Exception $e) {
            }
        }
    }
}
