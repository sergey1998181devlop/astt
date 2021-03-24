<?php
/**
 * User: Sergey nazarkin
 * Email: nazarkin2017@mail.ru
 * Date: 08.01.2021
 * Time: 13:09
 */

namespace Democontent2\Pi\Payments\SberBank\Exceptions;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Democontent2\Pi\Hl;

class RegisterOrderSuccessException extends \Exception
{
    const TABLE_NAME = 'Democontentpisberbankorders';

    protected $userId = 0;
    protected $orderId = 0;
    protected $sessionTimeoutSecs = 0;
    protected $resultOrderId = '';
    protected $resultOrderFormURL = '';

    /**
     * @param int $sessionTimeoutSecs
     */
    public function setSessionTimeoutSecs($sessionTimeoutSecs)
    {
        $this->sessionTimeoutSecs = $sessionTimeoutSecs;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @param string $resultOrderFormURL
     */
    public function setResultOrderFormURL($resultOrderFormURL)
    {
        $this->resultOrderFormURL = $resultOrderFormURL;
    }

    /**
     * @param string $resultOrderId
     */
    public function setResultOrderId($resultOrderId)
    {
        $this->resultOrderId = $resultOrderId;
    }

    public function log()
    {
        if ($this->orderId) {
            $className = ToUpper(end(explode('\\', __CLASS__)));
            $hl = new Hl(static::TABLE_NAME, 0);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                $obj::add(
                    [
                        'UF_ORDER_ID' => $this->orderId,
                        'UF_SBERBANK_ORDER_ID' => $this->resultOrderId,
                        'UF_FORM_URL' => $this->resultOrderFormURL,
                        'UF_STATUS' => 0,
                        'UF_SESSION_TIMEOUT' => $this->sessionTimeoutSecs,
                        'UF_ERROR_CODE' => 0,
                        'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                        'UF_USER_ID' => $this->userId
                    ]
                );
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
                        'UF_SBERBANK_ORDER_ID' => [
                            'N',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_SBERBANK_ORDER_ID')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_SBERBANK_ORDER_ID')],
                            ]
                        ],
                        'UF_FORM_URL' => [
                            'N',
                            'string',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_FORM_URL')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_FORM_URL')],
                            ]
                        ],
                        'UF_STATUS' => [
                            'N',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_STATUS')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_STATUS')],
                            ]
                        ],
                        'UF_SESSION_TIMEOUT' => [
                            'N',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_SESSION_TIMEOUT')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_SESSION_TIMEOUT')],
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
                        'UF_CREATED_AT' => [
                            'N',
                            'datetime',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                            ]
                        ],
                        'UF_USER_ID' => [
                            'N',
                            'integer',
                            [
                                'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
                                'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
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
                    $this->log();
                }
            }
        }
    }
} 