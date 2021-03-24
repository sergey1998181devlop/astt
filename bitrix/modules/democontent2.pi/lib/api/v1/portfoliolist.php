<?php
/**
 * Date: 05.08.2019
 * Time: 16:25
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

class PortfolioList extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * PortfolioList constructor.
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
        if ($this->checkKey($request)) {
            if ($this->getId()) {
                $portfolioCategory = new Category($this->getId());
                $results = $portfolioCategory->getList();

                if (count($results)) {
                    $this->errorCode = 0;

                    foreach ($results as $result) {
                        $this->result[] = [
                            'id' => intval($result['ID']),
                            'name' => $result['UF_NAME']
                        ];
                    }
                } else {
                    $this->errorCode = 0;
                    $this->errorMessage = 'No Results';
                }
                unset($portfolioCategory);
            } else {
                $this->errorMessage = 'User Not Found';
            }
        } else {
            if ($request->get('userId') && intval($request->get('userId'))) {
                $portfolioCategory = new Category(intval($request->get('userId')));
                $results = $portfolioCategory->getList();

                if (count($results)) {
                    $this->errorCode = 0;

                    foreach ($results as $result) {
                        $this->result[] = [
                            'id' => intval($result['ID']),
                            'name' => $result['UF_NAME']
                        ];
                    }
                } else {
                    $this->errorCode = 0;
                    $this->errorMessage = 'No Results';
                }
                unset($portfolioCategory);
            } else {
                $this->errorMessage = 'Invalid User Id';
            }
        }
    }
}
