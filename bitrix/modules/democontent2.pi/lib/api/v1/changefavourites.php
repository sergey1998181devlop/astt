<?php
/**
 * Date: 11.09.2019
 * Time: 15:53
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

class ChangeFavourites extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * Favourites constructor.
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
        if ($request->get('id')) {
            if ($this->checkKey($request)) {
                if ($this->getId()) {
                    $this->errorCode = 0;
                    $favourites = new \Democontent2\Pi\Favourites($this->getId());
                    $favourites->setExecutorId($request->get('id'));
                    $favourites->change();
                } else {
                    $this->errorMessage = 'User Not Found';
                }
            } else {
                $this->errorMessage = 'Invalid Key';
            }
        } else {
            $this->errorMessage = 'Missing required parameter `id`';
        }
    }
}
