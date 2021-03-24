<?php
/**
 * Date: 15.07.2019
 * Time: 11:59
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
use Democontent2\Pi\Iblock\Menu;

class Categories implements IApi
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
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
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

    public function run(HttpRequest $request)
    {
        $menu = new Menu();
        $menuList = $menu->get();

        $i = 0;
        foreach ($menuList as $item) {
            if (!isset($item['items']) || !count($item['items'])) {
                continue;
            }

            $this->result[$i] = [
                'name' => $item['name'],
                'code' => $item['code'],
                'subCategories' => []
            ];

            foreach ($item['items'] as $_item) {
                $this->result[$i]['subCategories'][] = $_item;
            }
            $i++;
        }

        unset($menuList);

        if (count($this->result)) {
            $this->errorCode = 0;
        } else {
            $this->errorCode = 0;
            $this->errorMessage = 'No Results';
        }
    }
}
