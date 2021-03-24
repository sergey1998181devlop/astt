<?php
/**
 * Date: 28.08.2019
 * Time: 08:00
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
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Type\ParameterDictionary;
use Democontent2\Pi\Hl;

class ResponseValues
{
    const TABLE_NAME = 'Democontentpiresponsevalues';

    private $taskId = 0;
    private $userId = 0;
    private $offerId = 0;
    private $obj = null;

    /**
     * ResponseValues constructor.
     * @param int $taskId
     * @param int $userId
     */
    public function __construct(int $taskId, int $userId)
    {
        $this->taskId = intval($taskId);
        $this->userId = intval($userId);

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
                    'UF_USER_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
                        ]
                    ],
                    'UF_VALUE_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_VALUE_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_VALUE_ID')],
                        ]
                    ],
                    'UF_OFFER_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_OFFER_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_OFFER_ID')],
                        ]
                    ]
                ],
                [],
                [
                    ['UF_CREATED_AT'],
                    ['UF_TASK_ID'],
                    ['UF_USER_ID'],
                    ['UF_OFFER_ID'],
                    ['UF_TASK_ID', 'UF_USER_ID', 'UF_OFFER_ID']
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );
            if ($add) {
                $this->__construct($taskId, $userId);
            }
        }
    }

    /**
     * @param int $offerId
     */
    public function setOfferId(int $offerId): void
    {
        $this->offerId = intval($offerId);
    }

    /**
     * @param ParameterDictionary $parameterDictionary
     * @return array
     */
    public function add(ParameterDictionary $parameterDictionary)
    {
        $result = [];
        if ($this->taskId > 0 && $this->userId > 0 && $this->offerId && !is_null($this->obj)) {
            $lambda = function ($a) {
                return intval($a['ID']);
            };

            $responseCheckList = new Response($this->taskId);
            $checkListValues = array_map($lambda, $responseCheckList->getList());
            if (count($checkListValues)) {
                $obj = $this->obj;

                foreach ($parameterDictionary->get('response-values') as $value) {
                    if (intval($value)) {
                        if (in_array(intval($value), $checkListValues)) {
                            try {
                                $add = $obj::add(
                                    [
                                        'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                                        'UF_TASK_ID' => $this->taskId,
                                        'UF_USER_ID' => $this->userId,
                                        'UF_VALUE_ID' => intval($value),
                                        'UF_OFFER_ID' => $this->offerId
                                    ]
                                );

                                if ($add->isSuccess()) {
                                    $result[] = [
                                        'id' => $add->getId()
                                    ];
                                }
                            } catch (\Exception $e) {
                            }
                        }
                    }
                }
            }

            unset($responseCheckList, $checkListValues);
        }

        return $result;
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
                        'select' => ['ID', 'UF_CREATED_AT', 'UF_USER_ID', 'UF_VALUE_ID', 'UF_OFFER_ID'],
                        'filter' => [
                            '=UF_TASK_ID' => $this->taskId
                        ],
                        'order' => [
                            'ID',
                            'UF_USER_ID'
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
            } catch (ObjectPropertyException $e) {
            } catch (ArgumentException $e) {
            } catch (SystemException $e) {
            } catch (\Exception $e) {
            }
        }
    }
}
