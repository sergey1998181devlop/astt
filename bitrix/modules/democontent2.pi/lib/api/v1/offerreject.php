<?php
/**
 * Date: 09.09.2019
 * Time: 13:33
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
use Democontent2\Pi\Iblock\Item;
use Democontent2\Pi\Iblock\Response;

class OfferReject extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * OfferReject constructor.
     */
    public function __construct()
    {
        parent::__construct(0);
    }

    /**
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
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
     * @param HttpRequest $request
     */
    public function run(HttpRequest $request)
    {
        if ($this->checkKey($request)) {
            if ($this->getId()) {
                if ($request->get('taskId')) {
                    if ($request->get('categoryId')) {
                        if ($request->get('offerId')) {
                            $item = new Item();
                            $item->setItemId($request->get('taskId'));
                            $item->setIBlockId($request->get('categoryId'));
                            $ownerId = $item->getOwner();

                            if ($ownerId > 0 && $ownerId !== $this->getId()) {
                                $response = new Response();
                                $response->setTaskId($request->get('taskId'));
                                $response->setExecutorId($this->getId());
                                $response->setIBlockId($request->get('categoryId'));
                                $response->unSetExecutor(intval($request->get('offerId')));
                                $this->errorCode = $response->getError();
                            } else {
                                $this->errorMessage = 'Unknown error';
                            }
                        } else {
                            $this->errorMessage = 'Invalid offer id';
                        }
                    } else {
                        $this->errorMessage = 'Invalid category id';
                    }
                } else {
                    $this->errorMessage = 'Invalid task id';
                }
            } else {
                $this->errorMessage = 'User not found';
            }
        } else {
            $this->errorMessage = 'Invalid key';
        }
    }
}
