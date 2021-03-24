<?php
/**
 * Date: 13.07.2019
 * Time: 17:36
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
use Bitrix\Main\Config\Option;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use Democontent2\Pi\Balance\Account;
use Democontent2\Pi\Chat;
use Democontent2\Pi\Iblock\City;
use Democontent2\Pi\Iblock\Items;
use Democontent2\Pi\Profile\Portfolio\Category;
use Democontent2\Pi\Utils;

class User extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * User constructor.
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
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    public function run(HttpRequest $request)
    {
        if ($this->checkKey($request)) {
            if ($this->getId()) {
                $userParams = $this->get();

                if (count($userParams)) {
                    $chat = new Chat();
                    $chat->setUserHash(Utils::getChatId($this->getId()));

                    $city = new City();
                    $cityParams = $city->getById(intval($userParams['UF_DSPI_CITY']));

                    $reviews = new \Democontent2\Pi\Iblock\Reviews();
                    $reviews->setUserId(intval($userParams['ID']));
                    $userRating = $reviews->rating();

                    $account = new Account($this->getId());
                    $balance = $account->getAmount();
                    $this->result = [
                        'id' => intval($userParams['ID']),
                        'c' => Utils::getChatId($userParams['ID']),
                        'registeredAt' => strtotime($userParams['DATE_REGISTER']),
                        'name' => $userParams['NAME'],
                        'lastName' => (strlen($userParams['LAST_NAME'])) ? $userParams['LAST_NAME'] : null,
                        'email' => $userParams['EMAIL'],
                        'balance' => [
                            'amount' => $balance,
                            'formattedAmount' => Utils::price($balance),
                            'currency' => Option::get(DSPI, 'currency_name')
                        ],
                        'description' => '',
                        'phone' => Utils::validatePhone($userParams['PERSONAL_PHONE']),
                        'formattedPhone' => Utils::formatPhone($userParams['PERSONAL_PHONE']),
                        'photo' => null,
                        'isModerationOff' => (intval($userParams['UF_DSPI_MOD_OFF'])) ? true : false,
                        'isExecutor' => (intval($userParams['UF_DSPI_EXECUTOR'])) ? true : false,
                        'isDocumentsChecked' => (intval($userParams['UF_DSPI_DOCUMENTS'])) ? true : false,
                        'isSafe' => (intval($userParams['CARD'])) ? true : false,
                        'personalLimit' => (intval($userParams['UF_DSPI_LIMIT'])) ? intval($userParams['UF_DSPI_LIMIT']) : 0,
                        'safeCrowId' => (intval($userParams['CARD'])) ? intval($userParams['CARD']) : 0,
                        'cityId' => (intval($userParams['UF_DSPI_CITY'])) ? intval($userParams['UF_DSPI_CITY']) : 0,
                        'cityName' => isset($cityParams['name']) ? $cityParams['name'] : null,
                        'totalRating' => (floatval($userParams['UF_DSPI_RATING'])) ? floatval($userParams['UF_DSPI_RATING']) : 0,
                        'ratingDetails' => [
                            'positive' => intval($userRating['positive']),
                            'negative' => intval($userRating['negative']),
                            'neutral' => intval($userRating['neutral'])
                        ],
                        'subscriptions' => null,
                        'specializations' => null,
                        'unread' => $chat->unread()
                    ];

                    if (intval($userParams['PERSONAL_PHOTO'])) {
                        $this->result['photo'] = \CFile::ResizeImageGet(
                            intval($userParams['PERSONAL_PHOTO']),
                            [
                                'width' => 50,
                                'height' => 50
                            ],
                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                            true
                        );
                    }

                    $profile = new \Democontent2\Pi\Profile\Profile();
                    $profile->setUserId(intval($userParams['ID']));
                    $profileData = $profile->get();
                    if (isset($profileData['UF_DATA'])) {
                        $profileData['UF_DATA'] = unserialize($profileData['UF_DATA']);

                        $this->result['description'] = $profileData['UF_DATA']['description'];

                        if (isset($profileData['UF_DATA']['subSpecializations'])) {
                            foreach ($profileData['UF_DATA']['subSpecializations'] as $k => $v) {
                                foreach ($v as $_k => $_v) {
                                    $this->result['specializations'][] = [
                                        'id' => intval($_k),
                                        'name' => $_v
                                    ];
                                }
                            }
                        }
                    }

                    $subscriptions = new \Democontent2\Pi\Profile\Subscriptions();
                    $subscriptions->setUserId(intval($userParams['ID']));
                    $subscriptionsList = $subscriptions->getList();
                    foreach ($subscriptionsList as $item) {
                        $this->result['subscriptions'][] = [
                            'categoryId' => intval($item['UF_IBLOCK_ID']),
                            'paidTo' => ($item['UF_PAID_TO']) ? strtotime($item['UF_PAID_TO']) : 0
                        ];
                    }

                    unset($profile, $subscriptions, $profileData, $subscriptionsList);

                    $this->errorCode = 0;
                } else {
                    $this->errorMessage = 'User Not Found';
                }
            } else {
                $this->errorMessage = 'User Not Found';
            }
        } else {
            if ($request->get('id')) {
                $this->setId(intval($request->get('id')));
                $userParams = $this->get();

                if (count($userParams)) {
                    if (intval($userParams['UF_DSPI_EXECUTOR'])) {
                        $city = new City();
                        $cityParams = $city->getById(intval($userParams['UF_DSPI_CITY']));

                        $reviews = new \Democontent2\Pi\Iblock\Reviews();
                        $reviews->setUserId(intval($userParams['ID']));
                        $userRating = $reviews->rating();

                        $this->result = [
                            'id' => intval($userParams['ID']),
                            'c' => Utils::getChatId($userParams['ID']),
                            'registeredAt' => date('d.m.Y', strtotime($userParams['DATE_REGISTER'])),
                            'formattedRegisteredAt' => Utils::formatDate($userParams['DATE_REGISTER']),
                            'name' => $userParams['NAME'],
                            'lastName' => (strlen($userParams['LAST_NAME'])) ? $userParams['LAST_NAME'] : null,
                            'description' => null,
                            'photo' => null,
                            'isExecutor' => (intval($userParams['UF_DSPI_EXECUTOR'])) ? true : false,
                            'isDocumentsChecked' => (intval($userParams['UF_DSPI_DOCUMENTS'])) ? true : false,
                            'isSafe' => (intval($userParams['CARD'])) ? true : false,
                            'cityId' => (intval($userParams['UF_DSPI_CITY'])) ? intval($userParams['UF_DSPI_CITY']) : 0,
                            'cityName' => isset($cityParams['name']) ? $cityParams['name'] : null,
                            'totalRating' => (floatval($userParams['UF_DSPI_RATING'])) ? floatval($userParams['UF_DSPI_RATING']) : 0,
                            'ratingDetails' => [
                                'positive' => intval($userRating['positive']),
                                'negative' => intval($userRating['negative']),
                                'neutral' => intval($userRating['neutral'])
                            ],
                            'specializations' => null,
                            'tasksStat' => null,
                            'portfolio' => null
                        ];

                        if (intval($userParams['PERSONAL_PHOTO'])) {
                            $image = \CFile::ResizeImageGet(
                                intval($userParams['PERSONAL_PHOTO']),
                                [
                                    'width' => 100,
                                    'height' => 100
                                ],
                                BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                true
                            );

                            if (isset($image['src'])) {
                                $this->result['photo'] = $image['src'];
                            }
                        }

                        $profile = new \Democontent2\Pi\Profile\Profile();
                        $profile->setUserId(intval($userParams['ID']));
                        $profileData = $profile->get();
                        if (isset($profileData['UF_DATA'])) {
                            $profileData['UF_DATA'] = unserialize($profileData['UF_DATA']);

                            $this->result['description'] = strlen($profileData['UF_DATA']['description']) ? $profileData['UF_DATA']['description'] : null;

                            if (isset($profileData['UF_DATA']['subSpecializations'])) {
                                foreach ($profileData['UF_DATA']['subSpecializations'] as $k => $v) {
                                    foreach ($v as $_k => $_v) {
                                        $this->result['specializations'][] = [
                                            'id' => intval($_k),
                                            'name' => $_v
                                        ];
                                    }
                                }
                            }
                        }

                        unset($profile, $profileData);

                        $items = new Items();
                        $items->setUserId($userParams['ID']);
                        $this->result['tasksStat'] = $items->tasksStat();

                        $portfolioCategory = new Category($userParams['ID'], 86400);
                        $portfolio = $portfolioCategory->getList();
                        if (count($portfolio)) {
                            foreach ($portfolio as $item) {
                                $this->result['portfolio'][] = [
                                    'id' => intval($item['ID']),
                                    'name' => $item['UF_NAME']
                                ];
                            }
                        }

                        $this->errorCode = 0;
                    } else {
                        $this->errorMessage = 'User Not Found';
                    }
                } else {
                    $this->errorMessage = 'User Not Found';
                }
            } else {
                $this->errorMessage = 'Missing `x-pi-key` header or field `id`';
            }
        }
    }
}