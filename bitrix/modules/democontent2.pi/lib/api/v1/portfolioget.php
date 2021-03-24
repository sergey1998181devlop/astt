<?php
/**
 * Date: 05.08.2019
 * Time: 16:13
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
use Democontent2\Pi\Profile\Portfolio\Category;
use Democontent2\Pi\Utils;

class PortfolioGet extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * PortfolioGet constructor.
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
        if ($request->get('id') && intval($request->get('id'))) {
            if ($this->checkKey($request)) {
                if ($this->getId()) {
                    $portfolioCategory = new Category($this->getId());
                    $get = $portfolioCategory->get(intval($request->get('id')));

                    if (count($get)) {
                        $this->errorCode = 0;
                        $this->result = [
                            'id' => intval($get['id']),
                            'name' => $get['name'],
                            'files' => []
                        ];

                        if (count($get['files'])) {
                            foreach ($get['files'] as $file) {
                                $image = \CFile::ResizeImageGet(
                                    $file['UF_FILE_ID'],
                                    [
                                        'width' => 800,
                                        'height' => 800
                                    ],
                                    BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                    true
                                );
                                if (isset($image['src'])) {
                                    $this->result['files'][] = [
                                        'id' => intval($file['ID']),
                                        'path' => $image['src'],
                                        'description' => (strlen($file['UF_DESCRIPTION'])) ? $file['UF_DESCRIPTION'] : null
                                    ];
                                }
                            }
                        }
                    } else {
                        $this->errorMessage = 'Portfolio Not found';
                    }
                    unset($portfolioCategory);
                } else {
                    $this->errorMessage = 'User Not Found';
                }
            } else {
                if ($request->get('userId') && intval($request->get('userId'))) {
                    $portfolioCategory = new Category(intval($request->get('userId')));
                    $get = $portfolioCategory->get(intval($request->get('id')));

                    if (count($get)) {
                        $this->errorCode = 0;
                        $this->result = [
                            'id' => intval($get['id']),
                            'name' => $get['name'],
                            'user' => null,
                            'files' => []
                        ];

                        $this->setId($request->get('userId'));
                        $userParams = $this->get();

                        $reviews = new \Democontent2\Pi\Iblock\Reviews();
                        $reviews->setUserId($userParams['ID']);
                        $rating = $reviews->rating();

                        $photo = null;
                        $cityName = null;

                        if (intval($userParams['UF_DSPI_CITY'])) {
                            $city = new City();
                            $cityParams = $city->getById(intval($userParams['UF_DSPI_CITY']));
                            if (isset($cityParams[intval($userParams['UF_DSPI_CITY'])])) {
                                $cityName = $cityParams[intval($userParams['UF_DSPI_CITY'])]['name'];
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

                        if (count($get['files'])) {
                            foreach ($get['files'] as $file) {
                                $image = \CFile::ResizeImageGet(
                                    $file['UF_FILE_ID'],
                                    [
                                        'width' => 800,
                                        'height' => 800
                                    ],
                                    BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                    true
                                );
                                if (isset($image['src'])) {
                                    $this->result['files'][] = [
                                        'id' => intval($file['ID']),
                                        'path' => $image['src'],
                                        'description' => $file['UF_DESCRIPTION']
                                    ];
                                }
                            }
                        }
                    } else {
                        $this->errorMessage = 'Portfolio Not found';
                    }
                    unset($portfolioCategory);
                } else {
                    $this->errorMessage = 'Invalid User Id';
                }
            }
        } else {
            $this->errorMessage = 'Invalid Id';
        }
    }
}
