<?php
/**
 * Date: 30.07.2019
 * Time: 16:47
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

class RestorePassword extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * RestorePassword constructor.
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
            if ($this->getId() > 1) {
                $userGroups = [];
                $groups = \CUser::GetUserGroupList($this->getId());
                while ($group = $groups->Fetch()) {
                    $groups[] = intval($group['GROUP_ID']);
                }

                if (in_array(1, $userGroups)) {
                    $this->errorMessage = 'User Not Found';
                    return;
                }

                $currentParams = $this->get();
                $this->setEmail($currentParams['EMAIL']);
                $this->setPhone($currentParams['PERSONAL_PHONE']);

                if ($this->restorePassword()) {
                    $this->errorCode = 0;
                } else {
                    $this->errorMessage = 'Could not recover password';
                }
            } else {
                $this->errorMessage = 'User Not Found';
            }
        } else {
            $this->errorMessage = 'Invalid Key';
        }
    }
}
