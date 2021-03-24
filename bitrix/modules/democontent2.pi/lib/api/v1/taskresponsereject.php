<?php
/**
 * Date: 05.09.2019
 * Time: 12:13
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

class TaskResponseReject extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * SendMessage constructor.
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
        if ($request->get('taskId') && intval($request->get('taskId'))) {
            if ($request->get('iBlockId') && intval($request->get('iBlockId'))) {
                if ($request->get('offerId') && intval($request->get('offerId'))) {
                    if ($this->checkKey($request)) {
                        if ($this->getId()) {
                            $item = new Item();
                            $item->setUserId($this->getId());
                            $item->setItemId($request->get('taskId'));
                            $item->setIBlockId($request->get('iBlockId'));
                            if ($item->checkOwner()) {
                                $response = new Response();
                                $response->setTaskId($request->get('taskId'));
                                $response->setIBlockId($request->get('iBlockId'));
                                $response->setOfferId($request->get('offerId'));
                                $response->setDenied($request->get('offerId'));
                                $this->errorCode = $response->getError();
                            } else {
                                $this->errorMessage = 'Access denied';
                            }
                        } else {
                            $this->errorMessage = 'User Not Found';
                        }
                    } else {
                        $this->errorMessage = 'Invalid Key';
                    }
                } else {
                    $this->errorMessage = 'Invalid offer id';
                }
            } else {
                $this->errorMessage = 'Invalid category id';
            }
        } else {
            $this->errorMessage = 'Invalid Id';
        }
    }
}
