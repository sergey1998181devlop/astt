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

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\I\IPayment;
use Democontent2\Pi\Payments\ClickUZ;
use Democontent2\Pi\Payments\PayPal;
use Democontent2\Pi\Payments\PayTrail;
use Democontent2\Pi\Payments\RoboKassa;
use Democontent2\Pi\Payments\SberBank;
use Democontent2\Pi\Payments\Skrill;
use Democontent2\Pi\Payments\Tinkoff;
use Democontent2\Pi\Payments\Yandex;

class Payment implements IPayment
{
    const LOG_TABLE = 'Democontentpipaymentrequestlog';
    const TRANSACTIONS_TABLE = 'Democontentpipaymentstransactions';

    private $redirect = '';
    private $orderId = 0;
    private $paymentProvider = null;
    private $params = [];
    private $result = '';
    private $source = null;
    private $sum = 0;
    private $transactionId = '';

    /**
     * Payment constructor.
     */
    public function __construct()
    {
        try {
            $provider = Option::get(DSPI, 'paymentProvider');
            switch ($provider) {
                case 'yandex':
                case 'robokassa':
                case 'sberbank':
                case 'tinkoff':
                case 'paytrail':
                case 'skrill':
                case 'paypal':
                case 'click.uz':
                    $this->paymentProvider = $provider;
                    break;
            }
        } catch (ArgumentNullException $e) {
        } catch (ArgumentOutOfRangeException $e) {
        }
    }

    public function setSource($source)
    {
        if ($source) {
            try {
                $this->source = Json::decode($source);
            } catch (\Exception $e) {
            }
        }
    }

    public function getResult()
    {
        echo $this->result;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        if (count($this->params)) {
            $this->params['orderId'] = $this->orderId;
        }
    }

    /**
     * @param null|string $paymentProvider
     */
    public function setPaymentProvider($paymentProvider)
    {
        $this->paymentProvider = $paymentProvider;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        if (count($params) > 0) {
            $this->params = $params;

            if ($this->orderId) {
                $this->params['orderId'] = $this->orderId;
            }
        }
    }

    public function getRedirect()
    {
        return $this->redirect;
    }

    public function make()
    {
        if ($this->paymentProvider !== null) {
            switch ($this->paymentProvider) {
                case 'yandex':
                    $yandex = new Yandex();
                    $yandex->make($this->params);
                    $this->redirect = $yandex->getRedirect();
                    break;
                case 'sberbank':
                    $sberBank = new SberBank();
                    $sberBank->make($this->params);
                    $this->redirect = $sberBank->getRedirect();
                    break;
                case 'tinkoff':
                    $tinkoff = new Tinkoff();
                    $tinkoff->make($this->params);
                    $this->redirect = $tinkoff->getRedirect();
                    break;
                case 'robokassa':
                    $roboKassa = new RoboKassa();
                    $roboKassa->make($this->params);
                    $this->redirect = $roboKassa->getRedirect();
                    break;
                case 'paytrail':
                    $payTrail = new PayTrail();
                    $payTrail->make($this->params);
                    $this->redirect = $payTrail->getRedirect();
                    break;
                case 'skrill':
                    $skrill = new Skrill();
                    $skrill->make($this->params);
                    $this->redirect = $skrill->getRedirect();
                    break;
                case 'paypal':
                    $payPal = new PayPal();
                    $payPal->make($this->params);
                    $this->redirect = $payPal->getRedirect();
                    break;
                case 'click.uz':
                    $clickUz = new ClickUZ();
                    $clickUz->make($this->params);
                    $this->redirect = $clickUz->getRedirect();
                    break;
            }
        }
    }

    public function verify()
    {
        if ($this->paymentProvider !== null) {
            if (count($this->params)) {
                switch ($this->paymentProvider) {
                    case 'yandex':
                        $yandex = new Yandex();

                        if (is_array($this->source)) {
                            $yandex->setRequest($this->source);
                        }

                        try {
                            $yandex->verify();
                            $this->transactionId = $yandex->getTransactionId();
                            $this->sum = $yandex->getSum();
                        } catch (Exceptions\OrderNotFoundException $e) {
                        } catch (Exceptions\OrderSetPayedFailException $e) {
                        }
                        break;
                    case 'sberbank':
                        $sberBank = new SberBank();
                        $sberBank->setRequest($this->params);
                        $sberBank->verify();
                        $this->transactionId = $sberBank->getTransactionId();
                        $this->sum = $sberBank->getSum();
                        $this->result = $sberBank->getResult();
                        break;
                    case 'tinkoff':
                        $tinkoff = new Tinkoff();
                        if (is_array($this->source)) {
                            $tinkoff->setRequest($this->source);
                        } else {
                            $tinkoff->setRequest($this->params);
                        }
                        $tinkoff->verify();
                        $this->transactionId = $tinkoff->getTransactionId();
                        $this->sum = $tinkoff->getSum();
                        $this->result = $tinkoff->getResult();
                        break;
                    case 'robokassa':
                        $roboKassa = new RoboKassa();
                        $roboKassa->setRequest($this->params);
                        $roboKassa->verify();
                        $this->transactionId = $roboKassa->getTransactionId();
                        $this->sum = $roboKassa->getSum();
                        $this->result = $roboKassa->getResult();
                        break;
                    case 'paytrail':
                        $payTrail = new PayTrail();
                        $payTrail->setRequest($this->params);
                        $payTrail->verify();
                        $this->transactionId = $payTrail->getTransactionId();
                        $this->sum = $payTrail->getSum();
                        $this->result = $payTrail->getResult();
                        break;
                    case 'skrill':
                        $skrill = new Skrill();
                        $skrill->setRequest($this->params);
                        $skrill->verify();
                        $this->transactionId = $skrill->getTransactionId();
                        $this->sum = $skrill->getSum();
                        $this->result = $skrill->getResult();
                        break;
                    case 'paypal':
                        $payPal = new PayPal();
                        $payPal->setRequest($this->params);
                        $payPal->verify();
                        $this->transactionId = $payPal->getTransactionId();
                        $this->sum = $payPal->getSum();
                        $this->result = $payPal->getResult();
                        break;
                    case 'click.uz':
                        $clickUz = new ClickUZ();
                        $clickUz->setRequest($this->params);
                        $clickUz->verify();
                        $this->transactionId = $clickUz->getTransactionId();
                        $this->sum = $clickUz->getSum();
                        $this->result = $clickUz->getResult();
                        break;
                }

                if ($this->sum && $this->transactionId) {
                    $this->setTransaction();
                }
            }
        }
    }

    public function log()
    {
        $hl = new Hl(static::LOG_TABLE);
        if ($hl->obj !== null) {
            $obj = $hl->obj;
            try {
                $params = $this->params;
                if (is_array($this->source)) {
                    $params = $this->source;
                }
                $obj::add(
                    [
                        'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                        'UF_DATA' => Json::encode($params)
                    ]
                );
            } catch (\Exception $e) {
            }
        } else {
            $className = ToUpper(end(explode('\\', __CLASS__)));
            $add = Hl::create(
                ToLower(static::LOG_TABLE),
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
                    'ALTER TABLE `' . ToLower(static::LOG_TABLE) . '` MODIFY `UF_DATA` LONGTEXT;',
                ],
                [
                    ['UF_CREATED_AT'],
                ],
                Loc::getMessage($className . '_LOG_IBLOCK_NAME')
            );
            if ($add) {
                $this->log();
            }
        }
    }

    private function setTransaction()
    {
        $hl = new Hl(static::TRANSACTIONS_TABLE, 0);
        if ($hl->obj !== null) {
            $obj = $hl->obj;
            try {
                $params = $this->params;
                if (is_array($this->source)) {
                    $params = $this->source;
                }
                $obj::add(
                    [
                        'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                        'UF_PROVIDER' => $this->paymentProvider,
                        'UF_SUM' => $this->sum,
                        'UF_TRANSACTION_ID' => $this->transactionId,
                        'UF_ORDER_ID' => $this->orderId,
                        'UF_DATA' => Json::encode($params)
                    ]
                );
            } catch (\Exception $e) {
            }
        } else {
            $className = ToUpper(end(explode('\\', __CLASS__)));
            $add = Hl::create(
                ToLower(static::TRANSACTIONS_TABLE),
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
                    'UF_PROVIDER' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PROVIDER')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PROVIDER')],
                        ]
                    ],
                    'UF_SUM' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_SUM')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_SUM')],
                        ]
                    ],
                    'UF_TRANSACTION_ID' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TRANSACTION_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TRANSACTION_ID')],
                        ]
                    ],
                    'UF_ORDER_ID' => [
                        'N',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ORDER_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ORDER_ID')],
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
                    'ALTER TABLE `' . ToLower(static::TRANSACTIONS_TABLE) . '` MODIFY `UF_DATA` LONGTEXT;',
                    'ALTER TABLE `' . ToLower(static::TRANSACTIONS_TABLE) . '` MODIFY `UF_PROVIDER` VARCHAR(255);',
                    'ALTER TABLE `' . ToLower(static::TRANSACTIONS_TABLE) . '` MODIFY `UF_TRANSACTION_ID` VARCHAR(255);',
                ],
                [
                    ['UF_CREATED_AT'],
                    ['UF_ORDER_ID'],
                    ['UF_PROVIDER'],
                    ['UF_TRANSACTION_ID'],
                ],
                Loc::getMessage($className . '_TRANSACTION_IBLOCK_NAME')
            );
            if ($add) {
                $this->setTransaction();
            }
        }
    }
}