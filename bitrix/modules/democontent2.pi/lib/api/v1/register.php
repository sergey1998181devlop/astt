<?php
/**
 * Date: 30.07.2019
 * Time: 17:01
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

use Bitrix\Main\ArgumentException;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Type\ParameterDictionary;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\Utils;

class Register extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * Register constructor.
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
        $params = $request->getPostList()->toArray();
        if (!count($params)) {
            try {
                $params = Json::decode(file_get_contents('php://input'));
            } catch (ArgumentException $e) {
            }
        }

        $dict = new ParameterDictionary($params);

        if ($dict->get('name') && $dict->get('email') && $dict->get('phone')) {
            if (filter_var($dict->get('email'), FILTER_VALIDATE_EMAIL) && Utils::validatePhone($dict->get('phone'))) {
                $this->setName($dict->get('name'));
                $this->setEmail($dict->get('email'));
                $this->setPhone($dict->get('phone'));

                $id = $this->register();
                if ($id) {
                    $this->setId($id);
                    $key = $this->apiKey();
                    if (strlen($key) == 32) {
                        $this->errorCode = 0;
                        $this->result = [
                            'id' => intval($id),
                            'key' => $key
                        ];
                    } else {
                        $this->errorMessage = 'Unknown Error';
                    }
                } else {
                    $this->extra = $params;
                    $this->errorMessage = 'Failed to register user. Perhaps a user with such data already exists.';
                }
            } else {
                if (!Utils::validatePhone($dict->get('phone'))) {
                    $this->errorMessage = 'Invalid Phone';
                    $this->extra[] = 'phone';
                }

                if (!filter_var($dict->get('email'), FILTER_VALIDATE_EMAIL)) {
                    $this->errorMessage = 'Invalid Email';
                    $this->extra[] = 'email';
                }
            }
        } else {
            $this->errorMessage = 'Invalid Parameters';

            if (!strlen($dict->get('name'))) {
                $this->extra[] = 'name';
            }

            if (!strlen($dict->get('email')) || !filter_var($dict->get('email'), FILTER_VALIDATE_EMAIL)) {
                $this->extra[] = 'email';
            }

            if (!strlen($dict->get('phone')) || !Utils::validatePhone($dict->get('phone'))) {
                $this->extra[] = 'phone';
            }
        }
    }
}
