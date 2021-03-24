<?php
/**
 * User: Sergey nazarkin
 * Email: nazarkin2017@mail.ru
 * Date: 08.01.2021
 * Time: 13:09
 */

namespace Democontent2\Pi\Payments\SberBank\Exceptions;

use Bitrix\Main\Localization\Loc;
use Democontent2\Pi\Hl;

class OrderSuccessStatusException extends \Exception
{
    const TABLE_NAME = 'Democontentpisberbankordersparams';

    protected $orderId = 0;
    protected $statusParams = [];

    /**
     * @param int $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @param array $orderParams
     */
    public function setStatusParams($orderParams)
    {
        $this->statusParams = $orderParams;
    }

    public function save()
    {
        if (count($this->statusParams) > 0) {
            $className = ToUpper(end(explode('\\', __CLASS__)));
            $hl = new Hl(static::TABLE_NAME, 0);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                foreach ($this->statusParams as $k => $v) {
                    try {
                        $obj::add(
                            [
                                'UF_ORDER_ID' => $this->orderId,
                                'UF_PARAM_NAME' => $k,
                                'UF_PARAM_VALUE' => $v
                            ]
                        );
                    } catch (\Exception $e) {
                    }
                }
            } else {
                $add = Hl::create(
                    ToLower(static::TABLE_NAME),
                    [
                        'UF_ORDER_ID' => [
                            'N',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ORDER_ID')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ORDER_ID')],
                            ]
                        ],
                        'UF_PARAM_NAME' => [
                            'N',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PARAM_NAME')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PARAM_NAME')],
                            ]
                        ],
                        'UF_PARAM_VALUE' => [
                            'N',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PARAM_VALUE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PARAM_VALUE')],
                            ]
                        ],
                    ],
                    [
                        'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_ORDER_ID` VARCHAR(255);',
                    ],
                    [
                        ['UF_ORDER_ID'],
                    ],
                    Loc::getMessage($className . '_IBLOCK_NAME')
                );
                if ($add) {
                    $this->save();
                }
            }
        }
    }
} 