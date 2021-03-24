<?php
/**
 * Date: 30.07.2019
 * Time: 17:38
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

class ChangeFavourite extends \Democontent2\Pi\Favourites implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

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
     */
    public function run(HttpRequest $request)
    {
        if ($request->getHeaders()->get('x-pi-key') && strlen($request->getHeaders()->get('x-pi-key')) == 32) {
            $us = new User();
            $us->setApiKey($request->getHeaders()->get('x-pi-key'));
            $us->getIdByApiKey();
            if ($us->getId()) {
                parent::__construct($us->getId());

                if ($request->get('id') && intval($request->get('id'))) {
                    $this->setExecutorId(intval($request->get('id')));
                    $this->change();

                    $this->errorCode = 0;
                } else {
                    $this->errorMessage = 'Invalid Id';
                }
            } else {
                $this->errorMessage = 'User Not Found';
            }
        } else {
            $this->errorMessage = 'Invalid Key';
        }
    }
}
