<?php
/**
 * Date: 15.08.2019
 * Time: 11:14
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

class Properties extends \Democontent2\Pi\Iblock\Properties implements IApi
{

    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * Properties constructor.
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
     * @throws \Bitrix\Main\SystemException
     */
    public function run(HttpRequest $request)
    {
        if ($request->get('id') && intval($request->get('id'))) {
            $this->setIBlockId($request->get('id'));
            $result = $this->all();

            if (count($result)) {
                $this->errorCode = 0;

                $i = 0;
                foreach ($result as $item) {
                    switch ($item['type']) {
                        case 'list':
                            if (isset($item['values']) && count($item['values'])) {
                                $values = [];
                                foreach ($item['values'] as $value) {
                                    $values[] = $value;
                                }

                                $item['values'] = $values;
                                $this->result[$i] = $item;
                            }
                            break;
                        default:
                            $this->result[$i] = $item;
                    }
                    $i++;
                }
            } else {
                $this->errorMessage = 'Not Found';
            }
        } else {
            $this->errorMessage = 'id is required field';
        }
    }
}
