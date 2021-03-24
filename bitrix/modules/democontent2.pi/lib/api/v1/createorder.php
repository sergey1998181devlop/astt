<?php
/**
 * Date: 07.08.2019
 * Time: 09:52
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

namespace Democontent2\Pi\Api\V1;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\ParameterDictionary;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\Balance\Account;
use Democontent2\Pi\Order;

class CreateOrder extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * CreateOrder constructor.
     */
    public function __construct()
    {
        parent::__construct(0);
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @param HttpRequest $request
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Democontent2\Pi\Exceptions\OrderSetPayedFailException
     */
    public function run(HttpRequest $request)
    {
        $params = $request->getPostList()->toArray();
        if (!count($params)) {
            try {
                $params = Json::decode(file_get_contents('php://input'));
            } catch (ArgumentException $e) {
            }
        }

        $dict = new ParameterDictionary($params);

        if ($dict->get('amount') && $dict->get('type')) {
            if (intval($dict->get('amount'))) {
                switch ($dict->get('type')) {
                    case 'balance':
                    case 'packages':
                        if ($this->checkKey($request)) {
                            $order = new Order($this->getId());
                            $order->setSum(intval($dict->get('amount')));
                            $order->setType($dict->get('type'));
                            $order->setDescription(Loc::getMessage('CREATE_ORDER_' . $dict->get('type')));

                            if ($dict->get('type') == 'balance') {
                                if ($order->make()) {
                                    if ($order->getRedirect()) {
                                        $this->errorCode = 0;
                                        $this->result['id'] = $order->getOrderId();
                                        $this->result['status'] = 0;
                                        $this->result['redirect'] = $order->getRedirect();
                                    } else {
                                        $this->extra[] = 'Unknown error';
                                    }
                                } else {
                                    $this->extra[] = 'Cannot create order';
                                }
                            } else {
                                if ($dict->get('type') == 'packages') {
                                    //TODO check `itemId` param, and set this to $order object
                                }

                                $account = new Account($this->id);
                                $balance = $account->getAmount();

                                if ($balance > 0 && $balance >= $order->getSum()) {
                                    if ($order->make(true)) {
                                        if ($order->setPayed(true)) {
                                            $this->errorCode = 0;
                                            $this->result['id'] = $order->getOrderId();
                                            $this->result['status'] = 1;
                                            $this->result['redirect'] = null;
                                        } else {
                                            $this->extra[] = 'Cannot pay order';
                                        }
                                    } else {
                                        $this->extra[] = 'Cannot create order';
                                    }
                                } else {
                                    if ($order->make()) {
                                        if ($order->getRedirect()) {
                                            $this->errorCode = 0;
                                            $this->result['id'] = $order->getOrderId();
                                            $this->result['status'] = 0;
                                            $this->result['redirect'] = $order->getRedirect();
                                        } else {
                                            $this->extra[] = 'Unknown error';
                                        }
                                    } else {
                                        $this->extra[] = 'Cannot create order';
                                    }
                                }
                            }
                        } else {
                            $this->errorMessage = 'Invalid Key';
                        }
                        break;
                    default:
                        $this->extra[] = 'Invalid type';
                }
            } else {
                $this->extra[] = 'Amount must be greater than zero';
            }
        } else {
            if (!$dict->get('amount')) {
                $this->extra[] = 'amount is required field';
            }

            if (!$dict->get('type')) {
                $this->extra[] = 'type is required field';
            }
        }
    }
}
