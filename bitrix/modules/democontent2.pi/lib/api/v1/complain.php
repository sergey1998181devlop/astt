<?php
/**
 * Date: 04.10.2019
 * Time: 13:33
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

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\IO\InvalidPathException;
use Bitrix\Main\IO\Path;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\Type\ParameterDictionary;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\FireBase;
use Democontent2\Pi\Iblock\Item;
use Democontent2\Pi\Utils;

class Complain extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

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
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\IO\InvalidPathException
     * @throws \Bitrix\Main\SystemException
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

        if ($this->checkKey($request)) {
            if ($this->getId()) {
                if ($dict->get('id') && $dict->get('categoryId')) {
                    $item = new Item();
                    $item->setIBlockId($dict->get('categoryId'));
                    $item->setItemId($dict->get('id'));
                    $result = $item->getMainParams();

                    if (count($result)) {
                        $this->errorCode = 0;

                        if (intval($result['UF_USER_ID']) !== $this->getId()) {
                            $userData = $this->get();
                            if (count($userData)) {
                                Event::send(
                                    [
                                        'EVENT_NAME' => 'DSPI_NEW_COMPLAIN',
                                        'LID' => Option::get(DSPI, 'siteId'),
                                        'C_FIELDS' => [
                                            'IP' => Application::getInstance()->getContext()->getServer()->get('REMOTE_ADDR'),
                                            'USER_ID' => $this->getId(),
                                            'USER_NAME' => $userData['NAME'],
                                            'USER_EMAIL' => $userData['EMAIL'],
                                            'USER_PHONE' => Utils::formatPhone($userData['PERSONAL_PHONE']),
                                            'IBLOCK_TYPE' => $result['UF_IBLOCK_TYPE'],
                                            'IBLOCK_ID' => $dict->get('categoryId'),
                                            'ITEM_ID' => $dict->get('id'),
                                            'FULL_URL' => (($request->isHttps()) ? 'https://' : 'http://') . Path::normalize($request->getHttpHost()
                                                    . Option::get(DSPI, 'siteDir')
                                                    . $result['UF_IBLOCK_TYPE'] . '/' . $result['UF_IBLOCK_CODE'] . '/' . $result['UF_CODE']) . '/',
                                            'TEXT' => $dict->get('text') ? HTMLToTxt(strip_tags($dict->get('text'))) : ''
                                        ]
                                    ]
                                );

                                try {
                                    $fireBase = new FireBase($result['UF_USER_ID']);
                                    $fireBase->webPush([
                                        'title' => Loc::getMessage('COMPLAIN_PUSH_TITLE'),
                                        'body' => Loc::getMessage('COMPLAIN_PUSH_BODY', ['{{TASK_ID}}' => $item->getItemId()]),
                                        'url' => (($request->isHttps()) ? 'https://' : 'http://') . Path::normalize($request->getHttpHost() . SITE_DIR
                                                . $result['UF_IBLOCK_TYPE'] . '/' . $result['UF_IBLOCK_CODE'] . '/' . $result['UF_CODE']) . '/'
                                    ]);
                                } catch (ArgumentNullException $e) {
                                } catch (ArgumentOutOfRangeException $e) {
                                } catch (InvalidPathException $e) {
                                }
                            }
                        }
                    } else {
                        $this->errorMessage = 'Task not found';
                    }
                } else {
                    if ($dict->get('userId')) {
                        $us = new \Democontent2\Pi\User($dict->get('userId'));
                        $userParams = $us->get();
                        $userData = $this->get();

                        if (count($userParams)) {
                            if (intval($userParams['ID']) !== intval($userData['ID'])) {
                                $this->errorCode = 0;

                                $blackList = new \Democontent2\Pi\Iblock\BlackList();
                                $blackList->setUserId($userData['ID']);
                                $blackList->setBlockedId($userParams['ID']);
                                $blackList->add();

                                Event::send(
                                    [
                                        'EVENT_NAME' => 'DSPI_NEW_COMPLAIN',
                                        'LID' => Option::get(DSPI, 'siteId'),
                                        'C_FIELDS' => [
                                            'IP' => Application::getInstance()->getContext()->getServer()->get('REMOTE_ADDR'),
                                            'USER_ID' => $userParams['ID'],
                                            'USER_NAME' => $userData['NAME'],
                                            'USER_EMAIL' => $userData['EMAIL'],
                                            'USER_PHONE' => Utils::formatPhone($userData['PERSONAL_PHONE']),
                                            'IBLOCK_TYPE' => '',
                                            'IBLOCK_ID' => 0,
                                            'ITEM_ID' => 0,
                                            'FULL_URL' => (($request->isHttps()) ? 'https://' : 'http://') . Path::normalize($request->getHttpHost()
                                                    . Option::get(DSPI, 'siteDir')) . '/user/' . $userData['ID'] . '/',
                                            'TEXT' => $dict->get('text') ? HTMLToTxt(strip_tags($dict->get('text'))) : ''
                                        ]
                                    ]
                                );
                            }
                        } else {
                            $this->errorMessage = 'User not found';
                        }
                    } else {
                        $this->errorMessage = 'Invalid params';
                    }
                }
            } else {
                $this->errorMessage = 'User not found';
            }
        } else {
            $this->errorMessage = 'Invalid key';
        }
    }
}
