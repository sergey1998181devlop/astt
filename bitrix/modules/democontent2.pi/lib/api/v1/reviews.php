<?php
/**
 * Date: 15.07.2019
 * Time: 12:30
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
use Democontent2\Pi\Utils;

class Reviews extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * Reviews constructor.
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

    public function run(HttpRequest $request)
    {
        if ($request->get('id')) {
            $this->setId($request->get('id'));
            $userParams = $this->get();

            if (count($userParams)) {
                $type = '';
                $reviews = new \Democontent2\Pi\Iblock\Reviews();
                $reviews->setUserId($this->getId());

                if ($request->get('type')) {
                    switch ($request->get('type')) {
                        case 'negative':
                        case 'positive':
                        case 'neutral':
                            $type = $request->get('type');
                            break;
                    }
                }

                $items = $reviews->getListByUser($type);

                $i = 0;
                foreach ($items as $item) {
                    $this->result['reviews'][$i] = [
                        'id' => intval($item['ID']),
                        'categoryId' => intval($item['UF_IBLOCK_ID']),
                        'taskId' => intval($item['UF_TASK_ID']),
                        'userId' => intval($item['UF_FROM']),
                        'rating' => intval($item['UF_RATING']),
                        'text' => $item['UF_TEXT'],
                        'answer' => $item['UF_ANSWER'],
                        'textTime' => ($item['UF_TEXT_TIME'] !== null) ? Utils::formatDate($item['UF_TEXT_TIME']) : null,
                        'answerTime' => ($item['UF_ANSWER_TIME'] !== null) ? Utils::formatDate($item['UF_ANSWER_TIME']) : null,
                        'name' => $item['USER_FROM_NAME'],
                        'lastName' => $item['USER_FROM_LAST_NAME'],
                        'photo' => null
                    ];

                    if (intval($item['USER_FROM_PHOTO'])) {
                        $img = \CFile::ResizeImageGet(
                            intval($item['USER_FROM_PHOTO']),
                            [
                                'width' => 50,
                                'height' => 50
                            ],
                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                            true
                        );

                        if (isset($img['src'])) {
                            $this->result['reviews'][$i]['photo'] = $img['src'];
                        }
                    }

                    $i++;
                }

                if (count($this->result['reviews'])) {
                    $photo = null;
                    $cityName = null;
                    $rating = $reviews->rating();

                    if (intval($userParams['UF_DSPI_CITY'])) {
                        $city = new City();
                        $cityParams = $city->getById(intval($userParams['UF_DSPI_CITY']));

                        if (isset($cityParams['name'])) {
                            $cityName = $cityParams['name'];
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
                    $this->errorCode = 0;
                } else {
                    $this->errorCode = 0;
                    $this->errorMessage = 'No Results';
                }
            } else {
                $this->errorMessage = 'User Not Found';
            }
        } else {
            $this->errorMessage = 'Missing required field `id`';
        }
    }
}
