<?php
/**
 * Date: 06.08.2019
 * Time: 08:52
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
use Democontent2\Pi\Profile\Portfolio\Category;

class PortfolioRemoveCategory extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * PortfolioRemoveCategory constructor.
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
     */
    public function run(HttpRequest $request)
    {
        if ($request->getPostList()->get('id') && intval($request->getPostList()->get('id'))) {
            if ($this->checkKey($request)) {
                $category = new Category($this->id);
                if ($category->remove($request->getPostList()->get('id'))) {
                    $this->errorCode = 0;
                } else {
                    $this->errorMessage = 'Failed to remove portfolio category';
                }
                unset($category);
            } else {
                $this->errorMessage = 'Invalid Key';
            }
        } else {
            $this->errorMessage = 'Invalid Id';
        }
    }
}
