<?php
/**
 * Date: 13.07.2019
 * Time: 17:39
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
use Democontent2\Pi\Iblock\City;

class Cities extends City implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

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
        $cities = $this->getList();

        if (count($cities)) {
            foreach ($cities as $city) {
                $city['id'] = intval($city['id']);
                $city['isDefault'] = ($city['default']) ? true : false;
                unset($city['default']);
                $this->result[] = $city;
            }
            $this->errorCode = 0;
        } else {
            $this->errorCode = 0;
            $this->errorMessage = 'No Results';
        }

        unset($cities);
    }
}
