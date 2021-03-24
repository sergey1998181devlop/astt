<?php
/**
 * Date: 07.08.2019
 * Time: 10:30
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

use Bitrix\Main\HttpRequest;
use Bitrix\Main\Localization\Loc;
use Democontent2\Pi\Order;

class OrderStatus extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * OrderStatus constructor.
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

    public function run(HttpRequest $request)
    {
        if ($request->get('id') && intval($request->get('id'))) {
            if ($this->checkKey($request)) {
                $order = new Order($this->id);
                $order->setOrderId(intval($request->get('id')));
                $orderParams = $order->get();

                if (count($orderParams)) {
                    $this->errorCode = 0;
                    $this->result = [
                        'id' => $order->getOrderId(),
                        'status' => intval($orderParams['UF_PAYED']),
                        'sum' => floatval($orderParams['UF_SUM']),
                        'createdAt' => date('d.m.Y H:i:s', strtotime($orderParams['UF_CREATED_AT'])),
                        'payedAt' => ($orderParams['UF_PAYMENT_DATETIME']) ? date('d.m.Y H:i:s', strtotime($orderParams['UF_PAYMENT_DATETIME'])) : null,
                        'type' => Loc::getMessage('ORDER_STATUS_' . $orderParams['UF_TYPE'])
                    ];
                } else {
                    $this->errorMessage = 'Order Not Found';
                }
            } else {
                $this->errorMessage = 'Invalid Key';
            }
        } else {
            $this->errorMessage = 'id is required field';
        }
    }
}
