<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 08.01.2019
 * Time: 20:07
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Balance;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\Type\DateTime;
use Democontent2\Pi\EventManager;
use Democontent2\Pi\Hl;

class Account
{
    const TABLE_NAME = 'Democontentpibalance';

    private $userId = 0;
    private $amount = 0;
    private $description = '';
    private $obj = null;

    /**
     * Balance constructor.
     * @param int $userId
     */
    public function __construct($userId)
    {
        $this->userId = intval($userId);

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
                ],
                [],
                [
                    ['UF_CREATED_AT'],
                    ['UF_USER_ID'],
                    ['UF_AMOUNT']
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );

            if ($add) {
                $this->__construct($userId);
            }
        }
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
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

    public function create()
    {
        $return = false;
        if ($this->obj !== null && $this->userId > 0) {
            try {
                $id = 0;
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID'
                        ],
                        'filter' => [
                            '=UF_USER_ID' => $this->userId
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $id = intval($res['ID']);
                }

                if (!$id) {
                    $time = time();
                    $startBalance = intval(Option::get(DSPI, 'startBalance'));

                    $add = $obj::add(
                        [
                            'UF_CREATED_AT' => DateTime::createFromTimestamp($time),
                            'UF_USER_ID' => $this->userId,
                            'UF_AMOUNT' => $startBalance
                        ]
                    );
                    if ($add->isSuccess()) {
                        $return = true;

                        $event = new EventManager(
                            'accountCreated',
                            [
                                'time' => $time,
                                'userId' => $this->userId,
                                'amount' => $startBalance
                            ]
                        );
                        $event->execute();

                        if ($startBalance > 0) {
                            $transaction = new Transaction($this->userId);
                            $transaction->setAmount($startBalance);
                            $transaction->setTime($time);
                            $transaction->create();
                        }
                    }
                }
            } catch (\Exception $e) {

            }
        }

        return $return;
    }

    public function getAmount()
    {
        $result = 0;
        if ($this->obj !== null) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => ['UF_AMOUNT'],
                        'filter' => [
                            '=UF_USER_ID' => $this->userId
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $result = intval($res['UF_AMOUNT']);
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    public function accrual()
    {
        if ($this->obj !== null && $this->amount > 0 && $this->userId > 0) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID',
                            'UF_AMOUNT'
                        ],
                        'filter' => [
                            '=UF_USER_ID' => $this->userId
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $update = $obj::update(
                        $res['ID'],
                        [
                            'UF_AMOUNT' => (intval($res['UF_AMOUNT']) + $this->amount)
                        ]
                    );
                    if ($update->isSuccess()) {
                        $transaction = new Transaction($this->userId);
                        $transaction->setAmount($this->amount);
                        $transaction->setDescription($this->description);
                        $transaction->create();
                    }
                }
            } catch (\Exception $e) {

            }
        }
    }

    public function withdrawal()
    {
        $result = false;
        if ($this->obj !== null && $this->amount > 0 && $this->userId > 0) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID',
                            'UF_AMOUNT'
                        ],
                        'filter' => [
                            '=UF_USER_ID' => $this->userId
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    if (intval($res['UF_AMOUNT']) >= $this->amount) {
                        $update = $obj::update(
                            $res['ID'],
                            [
                                'UF_AMOUNT' => (intval($res['UF_AMOUNT']) - $this->amount)
                            ]
                        );
                        if ($update->isSuccess()) {
                            $result = true;
                            $transaction = new Transaction($this->userId);
                            $transaction->setType(1);
                            $transaction->setAmount($this->amount);
                            $transaction->setDescription($this->description);
                            $transaction->create();
                        }
                    }
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }
}