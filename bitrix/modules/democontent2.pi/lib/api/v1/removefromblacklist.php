<?php
/**
 * Date: 07.08.2019
 * Time: 13:00
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

class RemoveFromBlackList extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * RemoveFromBlackList constructor.
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
        if ($request->get('id') && intval($request->get('id'))) {
            if ($this->checkKey($request)) {
                $blackList = new \Democontent2\Pi\Iblock\BlackList();
                $blackList->setUserId($this->id);
                $blackList->setBlockedId($request->get('id'));

                if ($blackList->remove()) {
                    $this->errorCode = 0;
                } else {
                    $this->errorMessage = 'Unknown Error';
                }

                unset($blackList);
            } else {
                $this->errorMessage = 'Invalid Key';
            }
        } else {
            $this->errorMessage = 'id is required field';
        }
    }
}
