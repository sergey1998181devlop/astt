<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 28.09.2018
 * Time: 16:02
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi;

use Bitrix\Iblock\PropertyIndex\Manager;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\Balance\Account;
use Democontent2\Pi\Exceptions\OrderSetPayedFailException;
use Democontent2\Pi\I\IOrder;
use Democontent2\Pi\Profile\Prices;

class Order implements IOrder
{
    const ORDER_TABLE = 'Democontentpiorders';

    private $orderId = 0;
    private $userId = 0;
    private $orderClass = null;
    private $sum = 0;
    private $payed = 0;
    private $redirectToSite = true;
    private $redirect = '';
    private $params = [];
    private $error = 0;
    private $errorMessage = '';
    private $eventNamespace = 'order';
    private $description = '';
    private $type = '';
    private $itemId = 0;
    private $additionalParams = [];

    public function __construct($userId)
    {
        $this->userId = intval($userId);

        $hl = new Hl(static::ORDER_TABLE);
        if ($hl->obj !== null) {
            $this->orderClass = $hl->obj;
        } else {
            $className = ToUpper(end(explode('\\', __CLASS__)));
            $add = Hl::create(
                ToLower(static::ORDER_TABLE),
                [
                    'UF_CREATED_AT' => [
                        'N',
                        'datetime',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                        ]
                    ],
                    'UF_PAYMENT_DATETIME' => [
                        'N',
                        'datetime',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PAYMENT_DATETIME')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PAYMENT_DATETIME')],
                        ]
                    ],
                    'UF_PAYED' => [
                        'N',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PAYED')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PAYED')],
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
                    'UF_SUM' => [
                        'N',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_SUM')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_SUM')],
                        ]
                    ],
                    'UF_ITEM_ID' => [
                        'N',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ITEM_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ITEM_ID')],
                        ]
                    ],
                    'UF_TYPE' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TYPE')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TYPE')],
                        ]
                    ],
                    'UF_IP' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => 'IP'],
                            'LIST_COLUMN_LABEL' => ['ru' => 'IP'],
                        ]
                    ],
                    'UF_DATA' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DATA')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DATA')],
                        ]
                    ],
                ],
                [
                    'ALTER TABLE `' . ToLower(static::ORDER_TABLE) . '` MODIFY `UF_DATA` LONGTEXT;',
                    'ALTER TABLE `' . ToLower(static::ORDER_TABLE) . '` MODIFY `UF_TYPE` VARCHAR(255);',
                    'ALTER TABLE `' . ToLower(static::ORDER_TABLE) . '` MODIFY `UF_IP` VARCHAR(255);',
                ],
                [
                    ['UF_CREATED_AT'],
                    ['UF_PAYMENT_DATETIME'],
                    ['UF_PAYED'],
                    ['UF_USER_ID'],
                    ['UF_ITEM_ID'],
                    ['UF_TYPE'],
                    ['UF_IP'],
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );
            if ($add) {
                $this->__construct($userId);
            }
        }
    }

    /**
     * @param string $eventNamespace
     */
    public function setEventNamespace($eventNamespace)
    {
        $this->eventNamespace = $eventNamespace;
    }

    /**
     * @param array $additionalParams
     */
    public function setAdditionalParams($additionalParams)
    {
        $this->additionalParams = $additionalParams;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param int $itemId
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isRedirectToSite()
    {
        return $this->redirectToSite;
    }

    /**
     * @return int
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @return null
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * @return int
     */
    public function getPayed()
    {
        return $this->payed;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @param bool $skipAccrual
     * @return bool
     * @throws ArgumentException
     * @throws OrderSetPayedFailException
     */
    public function setPayed($skipAccrual = false)
    {
        $success = false;
        $obj = $this->orderClass;
        try {
            $upd = $obj::update(
                $this->orderId,
                [
                    'UF_PAYED' => 1,
                    'UF_PAYMENT_DATETIME' => DateTime::createFromTimestamp(time())
                ]
            );

            if ($upd->isSuccess()) {
                $success = true;
                $this->applyOptions($skipAccrual);

                if ($this->sum > 0) {
                    Event::send(
                        [
                            'EVENT_NAME' => 'DSPI_NEW_PAYMENT',
                            'LID' => Application::getInstance()->getContext()->getSite(),
                            'C_FIELDS' => [
                                'SUM' => $this->sum
                            ]
                        ]
                    );
                }

                $event = new EventManager(
                    $this->eventNamespace . '.payed',
                    [
                        'id' => $this->orderId
                    ]
                );
                $event->execute();
            }
        } catch (\Exception $e) {
        }

        if (!$success) {
            throw new OrderSetPayedFailException(
                Json::encode(
                    [
                        'orderId' => $this->orderId
                    ]
                )
            );
        }

        return $success;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return int
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * @param int $sum
     */
    public function setSum($sum)
    {
        $this->sum = $sum;
    }

    /**
     * @param bool $skipAccrual
     * @throws ArgumentException
     * @throws SystemException
     */
    public function applyOptions($skipAccrual = false)
    {
        //TODO ������� �������� � �������
        if ($this->orderId > 0) {
            $order = $this->get();
            if (is_array($order)) {
                $this->sum = intval($order['UF_SUM']);
                $order['UF_TYPE'] = ToLower($order['UF_TYPE']);
                switch ($order['UF_TYPE']) {
                    case 'packages':
                        $data = [];
                        $params = Json::decode($order['UF_DATA']);
                        $account = new Account($params['userId']);
                        $pricesObj = new Prices();
                        $pricesObj->setUserId($params['userId']);

                        $account->setAmount($params['cost']);

                        if (!$skipAccrual) {
                            $account->setDescription(Loc::getMessage('ORDER_PACKAGES_ACCRUAL'));
                            $account->accrual();
                        }

                        $userPrices = $pricesObj->get();
                        if (count($userPrices) > 0) {
                            $prices = unserialize($userPrices['UF_DATA']);

                            foreach ($params['params'] as $k => $v) {
                                foreach ($v as $k_ => $v_) {
                                    if (!isset($prices[$k][$k_])) {
                                        $prices[$k][$k_] = date('Y-m-d H:i:s', time() + (86400 * 30));
                                    } else {
                                        $dateExpire = strtotime($prices[$k][$k_]);
                                        if ($dateExpire <= time()) {
                                            $prices[$k][$k_] = date('Y-m-d H:i:s', time() + (86400 * 30));
                                        } else {
                                            $prices[$k][$k_] = date('Y-m-d H:i:s', $dateExpire + (86400 * 30));
                                        }
                                    }
                                }
                            }

                            $data = $prices;
                        } else {
                            foreach ($params['params'] as $k => $v) {
                                foreach ($v as $k_ => $v_) {
                                    $data[$k][$k_] = date('Y-m-d H:i:s', time() + (86400 * 30));
                                }
                            }
                        }

                        if (count($data) > 0) {
                            $pricesObj->save($data);
                            $account->setDescription(Loc::getMessage('ORDER_PACKAGES_WITHDRAWAL'));
                            $account->withdrawal();
                        }
                        break;
                    case 'executor':
                        $us = new User($this->userId);
                        $us->setExecutor(true);
                        break;
                    case 'balance':
                        try {
                            $params = Json::decode($order['UF_DATA']);
                            $account = new Account($this->userId);
                            $account->setAmount($this->sum);
                            $account->setDescription($params['description']);
                            $account->accrual();

                            $fireBase = new FireBase($this->userId);
                            $fireBase->webPush([
                                'title' => Loc::getMessage('ORDER_SERVICE_APPLIED'),
                                'body' => Loc::getMessage('ORDER_SERVICE_BALANCE', ['{{SUM}}' => $this->sum]),
                            ]);
                            unset($fireBase);
                        } catch (\Exception $e) {
                            Logger::add(
                                $this->userId,
                                'balanceError',
                                [
                                    'amount' => $this->sum
                                ]
                            );
                        }
                        break;
                    case 'new_item':
                        try {
                            $params = Json::decode($order['UF_DATA']);
                            if (isset($params['params'])) {
                                $el = new \CIBlockElement();
                                \CIBlockElement::SetPropertyValuesEx(
                                    intval($params['params']['itemId']),
                                    intval($params['params']['iBlockId']),
                                    [
                                        '__hidden_payed' => 1
                                    ]
                                );
                                $el->Update(
                                    intval($params['params']['itemId']),
                                    [
                                        'TIMESTAMP_X' => DateTime::createFromTimestamp(time())
                                    ]
                                );
                            }
                        } catch (ArgumentException $e) {
                        }
                        break;
                    case 'quickly':
                        try {
                            $params = Json::decode($order['UF_DATA']);
                            if (isset($params['params'])) {
                                $el = new \CIBlockElement();
                                foreach ($params['params'] as $iBlockId => $items) {
                                    $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($iBlockId))));
                                    $hl = new Hl($tableName);
                                    if ($hl->obj !== null) {
                                        $obj = $hl->obj;
                                        $get = $obj::getList(
                                            [
                                                'select' => [
                                                    'UF_ID',
                                                    'UF_' . $order['UF_TYPE'] . '_START',
                                                    'UF_' . $order['UF_TYPE'] . '_END'
                                                ],
                                                'filter' => [
                                                    '=UF_IBLOCK_ID' => $iBlockId,
                                                    'UF_ID' => $items
                                                ]
                                            ]
                                        );

                                        while ($res = $get->fetch()) {
                                            if (!$res['UF_' . $order['UF_TYPE'] . '_START']
                                                && !$res['UF_' . $order['UF_TYPE'] . '_END']) {
                                                \CIBlockElement::SetPropertyValuesEx(
                                                    $res['UF_ID'],
                                                    $iBlockId,
                                                    [
                                                        '__hidden_' . $order['UF_TYPE'] . '_start' => DateTime::createFromTimestamp(time()),
                                                        '__hidden_' . $order['UF_TYPE'] . '_end' => DateTime::createFromTimestamp(
                                                            time() + (86400 * intval(Option::get(DSPI, $order['UF_TYPE'] . '_option_period')))
                                                        )
                                                    ]
                                                );
                                            } else {
                                                if (strtotime($res['UF_' . $order['UF_TYPE'] . '_END']) >= time()) {
                                                    \CIBlockElement::SetPropertyValuesEx(
                                                        $res['UF_ID'],
                                                        $iBlockId,
                                                        [
                                                            '__hidden_' . $order['UF_TYPE'] . '_end' => DateTime::createFromTimestamp(
                                                                strtotime($res['UF_' . $order['UF_TYPE'] . '_END']) + (86400 * intval(Option::get(DSPI, $order['UF_TYPE'] . '_option_period')))
                                                            )
                                                        ]
                                                    );
                                                } else {
                                                    \CIBlockElement::SetPropertyValuesEx(
                                                        $res['UF_ID'],
                                                        $iBlockId,
                                                        [
                                                            '__hidden_' . $order['UF_TYPE'] . '_end' => DateTime::createFromTimestamp(
                                                                time() + (86400 * intval(Option::get(DSPI, $order['UF_TYPE'] . '_option_period')))
                                                            )
                                                        ]
                                                    );
                                                }
                                            }

                                            Manager::updateElementIndex($iBlockId, $res['UF_ID']);
                                            $el->Update(
                                                $res['UF_ID'],
                                                [
                                                    'DATE_CREATE' => DateTime::createFromTimestamp(strtotime($res['UF_DATE_CREATE']))
                                                ]
                                            );

                                            $fireBase = new FireBase($order['UF_USER_ID']);
                                            $fireBase->webPush([
                                                'title' => Loc::getMessage('ORDER_SERVICE_APPLIED'),
                                                'body' => Loc::getMessage('ORDER_SERVICE_APPLIED_QUICKLY', ['{{TASK_ID}}' => $res['UF_ID']]),
                                            ]);
                                            unset($fireBase);
                                        }
                                    }
                                }
                            }
                        } catch (\Exception $e) {

                        }
                        break;
                    case 'activation':
                        try {
                            $params = Json::decode($order['UF_DATA']);
                            if (isset($params['params'])) {
                                $el = new \CIBlockElement();
                                foreach ($params['params'] as $iBlockId => $items) {
                                    $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($iBlockId))));
                                    $hl = new Hl($tableName);
                                    if ($hl->obj !== null) {
                                        $obj = $hl->obj;
                                        $get = $obj::getList(
                                            [
                                                'select' => [
                                                    'ID',
                                                    'UF_ID',
                                                    'UF_ACTIVE_TO'
                                                ],
                                                'filter' => [
                                                    '=IBLOCK_ID' => $iBlockId,
                                                    '<=UF_ACTIVE_TO' => DateTime::createFromTimestamp(time()),
                                                    'UF_ID' => $items
                                                ]
                                            ]
                                        );
                                        while ($res = $get->fetch()) {
                                            $el->Update(
                                                $res['UF_ID'],
                                                [
                                                    'ACTIVE_TO' => DateTime::createFromTimestamp(
                                                        time() + (86400 * intval(Option::get(DSPI, 'item_period')))
                                                    )
                                                ]
                                            );
                                            Manager::updateElementIndex($iBlockId, $res['UF_ID']);
                                        }
                                    }
                                }
                            }
                        } catch (ArgumentException $e) {
                        }
                        break;
                }
            }
        }
    }

    public function make($skipPayment = false , $props = false)
    {

        $this->params['cost'] = $this->sum;
        $this->params['userId'] = $this->userId;
        $this->params['description'] = $this->description;

        if (count($this->additionalParams) > 0) {
            $this->params['params'] = $this->additionalParams;
        }

        if ($this->orderClass !== null && $this->userId && $this->sum > 0
            && strlen($this->description) > 0 && strlen($this->type) > 0) {

            $obj = $this->orderClass;
            try {
                $add = $obj::add(
                    [
                        'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                        'UF_PAYED' => 0,
                        'UF_USER_ID' => $this->userId,
                        'UF_SUM' => $this->sum,
                        'UF_ITEM_ID' => $this->itemId,
                        'UF_TYPE' => $this->type,
                        'UF_IP' => Application::getInstance()->getContext()->getServer()->get('REMOTE_ADDR'),
                        'UF_DATA' => Json::encode($this->params),
                        'UF_NDS' => $props['PROPS']['BEZ_NDS'],
                        'UF_NALL' => $props['PROPS']['NAL'],
                        'UF_BEZNAL' => $props['PROPS']['BEZZNAL'],
                    ]
                );
                if ($add->isSuccess()) {
                    $this->orderId = $add->getId();

                    $event = new EventManager(
                        $this->eventNamespace . '.add',
                        [
                            'id' => $this->orderId
                        ]
                    );
                    $event->execute();
                }
            } catch (\Exception $e) {
            }
        }

        if ($this->orderId && $this->sum > 0) {
            if (!$skipPayment) {
                $payment = new Payment();
                $payment->setOrderId($this->orderId);
                $payment->setParams($this->params);
                $payment->make();

                if ($payment->getRedirect()) {
                    $this->redirectToSite = false;
                    $this->redirect = $payment->getRedirect();
                }
            }
        }

        return $this->orderId;
    }

    public function get()
    {
        $result = null;

        $this->sum = 0;
        if ($this->orderClass !== null) {
            if ($this->checkOrder()) {
                try {
                    $obj = $this->orderClass;
                    $list = $obj::getList(
                        [
                            'select' => [
                                '*'
                            ],
                            'filter' => [
                                '=ID' => $this->orderId
                            ]
                        ]
                    );
                    while ($res = $list->fetch()) {
                        $result = $res;
                        $this->payed = intval($res['UF_PAYED']);
                    }
                } catch (ObjectPropertyException $e) {
                } catch (ArgumentException $e) {
                } catch (SystemException $e) {
                }
            }
        }

        return $result;
    }

    private function checkOrder()
    {
        $result = 0;

        if ($this->orderClass !== null && $this->orderId && $this->userId) {
            try {
                $obj = $this->orderClass;

                $list = $obj::getList(
                    [
                        'select' => [
                            'ID'
                        ],
                        'filter' => [
                            '=ID' => $this->orderId,
                            '=UF_USER_ID' => $this->userId
                        ],
                        'order' => [
                            'UF_CREATED_AT' => 'DESC'
                        ]
                    ]
                );
                while ($res = $list->fetch()) {
                    if (intval($res['ID']) > 0) {
                        $result = intval($res['ID']);
                        break;
                    }
                }
            } catch (ObjectPropertyException $e) {
            } catch (ArgumentException $e) {
            } catch (SystemException $e) {
            } catch (\Exception $e) {
            }
        }

        return $result;
    }
}