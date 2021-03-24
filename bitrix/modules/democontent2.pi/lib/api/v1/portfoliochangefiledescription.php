<?php
/**
 * Date: 06.08.2019
 * Time: 08:31
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
use Democontent2\Pi\Profile\Portfolio\Files;

class PortfolioChangeFileDescription extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * PortfolioChangeFileDescription constructor.
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

        if ($dict->get('id') && intval($dict->get('id'))) {
            if ($dict->get('fileId') && intval($dict->get('fileId'))) {
                if ($dict->get('description')) {
                    if ($this->checkKey($request)) {
                        $files = new Files($this->id, intval($dict->get('id')));
                        if ($files->changeDescription($dict->get('fileId'), $dict->get('description'))) {
                            $this->errorCode = 0;
                        } else {
                            $this->errorMessage = 'Failed to change file description';
                        }
                        unset($files);
                    } else {
                        $this->errorMessage = 'Invalid Key';
                    }
                } else {
                    $this->errorMessage = 'Invalid Description';
                }
            } else {
                $this->errorMessage = 'Invalid File Id';
            }
        } else {
            $this->errorMessage = 'Invalid Id';
        }
    }
}
