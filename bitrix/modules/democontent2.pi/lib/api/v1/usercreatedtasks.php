<?php
/**
 * Date: 11.09.2019
 * Time: 09:32
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
use Democontent2\Pi\Iblock\Items;
use Democontent2\Pi\Utils;

class UserCreatedTasks extends Items implements IApi
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
     * @throws \Bitrix\Main\SystemException
     */
    public function run(HttpRequest $request)
    {
        if ($request->get('id')) {
            $us = new \Democontent2\Pi\User($request->get('id'));
            $userParams = $us->get();
            if (count($userParams)) {
                $this->setUserId($userParams['ID']);
                $result = $this->getByUser(true);

                if (count($result)) {
                    $this->errorCode = 0;

                    $reviews = new \Democontent2\Pi\Iblock\Reviews();
                    $reviews->setUserId($userParams['ID']);
                    $rating = $reviews->rating();

                    $city = new City();
                    $cities = $city->getList();

                    $i = 0;
                    foreach ($result as $item) {
                        $photo = null;
                        $cityName = null;

                        if (intval($item['UF_CITY'])) {
                            if (isset($cities[intval($item['UF_CITY'])])) {
                                $cityName = $cities[intval($item['UF_CITY'])]['name'];
                            }
                        }

                        if (intval($userParams['PERSONAL_PHOTO'])) {
                            $img = \CFile::ResizeImageGet(
                                intval($userParams['PERSONAL_PHOTO']),
                                [
                                    'width' => 100,
                                    'height' => 100
                                ],
                                BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                true
                            );
                            if (isset($img['src'])) {
                                $photo = $img['src'];
                            }
                        }

                        $this->result['items'][$i] = [
                            'id' => intval($item['UF_ITEM_ID']),
                            'createdAt' => (isset($item['UF_CREATED_AT'])) ? Utils::formatDate($item['UF_CREATED_AT']) : Utils::formatDate($item['UF_DATE_CREATE']),
                            'code' => str_replace('-' . $item['UF_ITEM_ID'], '', $item['UF_CODE']),
                            'categoryCode' => $item['UF_IBLOCK_CODE'],
                            'categoryType' => $item['UF_IBLOCK_TYPE'],
                            'categoryId' => intval($item['UF_IBLOCK_ID']),
                            'cityId' => intval($item['UF_CITY']),
                            'cityName' => $cityName,
                            'name' => $item['UF_NAME'],
                            'status' => intval($item['UF_STATUS']),
                            'description' => $item['UF_DESCRIPTION'],
                            'price' => floatval($item['UF_PRICE']),
                            'formattedPrice' => Utils::price(floatval($item['UF_PRICE'])),
                            'responses' => intval($item['UF_RESPONSE_COUNT']),
                            'views' => intval($item['UF_COUNTER']),
                            'safe' => intval($item['UF_SAFE']),
                            'quickly' => (strtotime($item['UF_QUICKLY_END']) >= time()) ? 1 : 0,
                            'user' => [
                                'id' => intval($userParams['ID']),
                                'name' => $userParams['NAME'],
                                'photo' => $photo,
                                'cityId' => intval($userParams['UF_DSPI_CITY']),
                                'reviewsCount' => intval($item['REVIEWS_COUNT']),
                                'totalRating' => ($userParams['UF_DSPI_RATING'] && !is_null($userParams['UF_DSPI_RATING'])) ? floatval($userParams['UF_DSPI_RATING']) : 0,
                                'ratingDetails' => [
                                    'positive' => intval($rating['positive']),
                                    'negative' => intval($rating['negative']),
                                    'neutral' => intval($rating['neutral'])
                                ]
                            ]
                        ];

                        $i++;
                    }

                    $photo = null;
                    $cityName = null;

                    if (intval($userParams['UF_DSPI_CITY'])) {
                        if (isset($cities[intval($userParams['UF_DSPI_CITY'])])) {
                            $cityName = $cities[intval($userParams['UF_DSPI_CITY'])]['name'];
                        }
                    }

                    if (isset($userParams['AVATAR']['src'])) {
                        $photo = $userParams['AVATAR']['src'];
                    }

                    $this->result['user'] = [
                        'id' => intval($userParams['ID']),
                        'c' => Utils::getChatId($userParams['ID']),
                        'registeredAt' => strtotime($userParams['DATE_REGISTER']),
                        'formattedRegisteredAt' => date('d.m.Y', strtotime($userParams['DATE_REGISTER'])),
                        'name' => $userParams['NAME'],
                        'lastName' => (strlen($userParams['LAST_NAME'])) ? $userParams['LAST_NAME'] : null,
                        'photo' => $photo,
                        'isExecutor' => (intval($userParams['UF_DSPI_EXECUTOR'])) ? true : false,
                        'isDocumentsChecked' => (intval($userParams['UF_DSPI_DOCUMENTS'])) ? true : false,
                        'isSafe' => (intval($userParams['CARD'])) ? true : false,
                        'cityId' => (intval($userParams['UF_DSPI_CITY'])) ? intval($userParams['UF_DSPI_CITY']) : 0,
                        'cityName' => $cityName,
                        'totalRating' => (floatval($userParams['UF_DSPI_RATING'])) ? floatval($userParams['UF_DSPI_RATING']) : 0,
                        'ratingDetails' => [
                            'positive' => intval($rating['positive']),
                            'negative' => intval($rating['negative']),
                            'neutral' => intval($rating['neutral'])
                        ]
                    ];
                }
            } else {
                $this->errorMessage = 'User Not Found';
            }
        } else {
            $this->errorMessage = 'Missing required parameter `id`';
        }
    }
}
