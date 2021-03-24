<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 28.09.2018
 * Time: 15:25
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Payments\SberBank\Exceptions;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Democontent2\Pi\Hl;

class ErrorException extends \Exception
{
    const TABLE_NAME = 'Democontentpisberbankerrors';

    protected $errorCode = 0;
    protected $orderId = 0;
    protected $operationType = '';

    /**
     * @param string $operationType
     */
    public function setOperationType($operationType)
    {
        $this->operationType = $operationType;
    }

    /**
     * @param int $errorCode
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    public function log()
    {
        if ($this->orderId && $this->errorCode) {
            $className = ToUpper(end(explode('\\', __CLASS__)));
            $hl = new Hl(static::TABLE_NAME, 0);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                try {
                    $obj::add(
                        [
                            'UF_ORDER_ID' => $this->orderId,
                            'UF_ERROR_CODE' => $this->errorCode,
                            'UF_ERROR_MESSAGE' => $this->message,
                            'UF_OPERATION_TYPE' => $this->operationType,
                            'UF_CREATED_AT' => DateTime::createFromTimestamp(time())
                        ]
                    );
                } catch (\Exception $e) {
                }
            } else {
                $add = Hl::create(
                    ToLower(static::TABLE_NAME),
                    [
                        'UF_ORDER_ID' => [
                            'N',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ORDER_ID')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ORDER_ID')],
                            ]
                        ],
                        'UF_ERROR_CODE' => [
                            'N',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ERROR_CODE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ERROR_CODE')],
                            ]
                        ],
                        'UF_ERROR_MESSAGE' => [
                            'N',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ERROR_MESSAGE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ERROR_MESSAGE')],
                            ]
                        ],
                        'UF_OPERATION_TYPE' => [
                            'N',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_OPERATION_TYPE')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_OPERATION_TYPE')],
                            ]
                        ],
                        'UF_CREATED_AT' => [
                            'N',
                            'datetime',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                            ]
                        ],
                    ],
                    [
                        'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_ORDER_ID` VARCHAR(255);',
                        'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_ERROR_CODE` VARCHAR(255);',
                    ],
                    [
                        ['UF_CREATED_AT'],
                        ['UF_ORDER_ID'],
                        ['UF_ERROR_CODE'],
                    ],
                    Loc::getMessage($className . '_IBLOCK_NAME')
                );
                if ($add) {
                    $this->log();
                }
            }
        }
    }
} 