<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 08.01.2019
 * Time: 20:08
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Balance;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Democontent2\Pi\EventManager;
use Democontent2\Pi\Hl;

class Transaction
{
    const TABLE_NAME = 'Democontentpibalancetransactions';

    private $userId = 0;
    private $type = 0; //0 - in, 1 - out
    private $orderId = 0;
    private $amount = 0;
    private $description = '';
    private $time = 0;
    private $obj = null;

    /**
     * Transaction constructor.
     * @param $userId
     */
    public function __construct($userId)
    {
        $this->userId = $userId;

        $className = ToUpper(end(explode('\\', __CLASS__)));
        $hl = new Hl(static::TABLE_NAME);
        if ($hl->obj !== null) {
            $this->obj = $hl->obj;
        } else {
            $add = Hl::create(
                ToLower(static::TABLE_NAME),
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
                    'UF_ORDER_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ORDER_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ORDER_ID')],
                        ]
                    ],
                    'UF_USER_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
                        ]
                    ],
                    'UF_AMOUNT' => [
                        'N',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_AMOUNT')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_AMOUNT')],
                        ]
                    ],
                    'UF_TYPE' => [
                        'N',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TYPE')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TYPE')],
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
                ],
                [],
                [
                    ['UF_CREATED_AT'],
                    ['UF_ORDER_ID'],
                    ['UF_USER_ID'],
                    ['UF_AMOUNT'],
                    ['UF_TYPE']
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );

            if ($add) {
                $this->__construct($userId);
            }
        }
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = intval($orderId);
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = intval($amount);
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param int $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    public function create()
    {
        if ($this->obj !== null) {
            try {
                $time = (($this->time > 0) ? $this->time : time());
                $obj = $this->obj;
                $add = $obj::add(
                    [
                        'UF_CREATED_AT' => DateTime::createFromTimestamp($time),
                        'UF_ORDER_ID' => $this->orderId,
                        'UF_USER_ID' => $this->userId,
                        'UF_AMOUNT' => $this->amount,
                        'UF_TYPE' => $this->type,
                        'UF_DESCRIPTION' => $this->description,
                    ]
                );
                if ($add->isSuccess()) {
                    $event = new EventManager(
                        'transactionCreated',
                        [
                            'time' => $time,
                            'userId' => $this->userId,
                            'orderId' => $this->orderId,
                            'type' => $this->type,
                            'amount' => $this->amount,
                            'description' => $this->description
                        ]
                    );
                    $event->execute();
                }
            } catch (\Exception $e) {

            }
        }
    }

    public function getList()
    {
        $result = [];

        if ($this->obj !== null) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            '*'
                        ],
                        'filter' => [
                            'UF_USER_ID' => $this->userId
                        ],
                        'order' => [
                            'ID' => 'DESC'
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
}