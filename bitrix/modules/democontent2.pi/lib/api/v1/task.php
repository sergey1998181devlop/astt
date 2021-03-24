<?php
/**
 * Date: 21.08.2019
 * Time: 18:53
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
use Democontent2\Pi\Iblock\Complain;
use Democontent2\Pi\Iblock\Item;
use Democontent2\Pi\Iblock\Response;
use Democontent2\Pi\Iblock\Stat;
use Democontent2\Pi\Utils;

class Task extends Item implements IApi
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
     */
    public function run(HttpRequest $request)
    {
        if ($request->get('id')) {
            if ($request->get('categoryType')) {
                if ($request->get('categoryCode')) {
                    if ($request->get('itemCode')) {
                        $this->setIBlockType($request->get('categoryType'));
                        $this->setIBlockCode($request->get('categoryCode'));
                        $this->setItemCode($request->get('itemCode'));
                        $this->setItemId(intval($request->get('id')));

                        $us = new \Democontent2\Pi\User(0);
                        $us->checkKey($request);
                        if ($us->getId()) {
                            $this->setFilteredUserId($us->getId());
                        }

                        $result = $this->get();

                        if (count($result)) {
                            $city = new City();

                            $photo = null;
                            $cityName = null;
                            $stages = null;

                            $us->setId(intval($result['UF_USER_ID']));
                            $reviews = new \Democontent2\Pi\Iblock\Reviews();
                            $reviews->setUserId(intval($result['UF_USER_ID']));
                            $userParams = $us->get();
                            $userRating = $reviews->rating();

                            if (intval($result['UF_CITY'])) {
                                $getCity = $city->getById(intval($result['UF_CITY']));
                                if (isset($getCity['name'])) {
                                    $cityName = $getCity['name'];
                                }
                            }

                            $getStages = $this->getStages();
                            if (count($getStages)) {
                                foreach ($getStages as $stage) {
                                    $stageFiles = null;

                                    if (strlen($stage['UF_FILES'])) {
                                        $_stageFiles = unserialize($stage['UF_FILES']);
                                        if (count($_stageFiles)) {
                                            $stageFiles = $_stageFiles;
                                        }
                                    }
                                    $stages[] = [
                                        'id' => intval($stage['ID']),
                                        'status' => intval($stage['UF_STATUS']),
                                        'sort' => intval($stage['UF_SORT']),
                                        'name' => $stage['UF_NAME'],
                                        'description' => (strlen($stage['UF_DESCRIPTION'])) ? $stage['UF_DESCRIPTION'] : null,
                                        'price' => (floatval($stage['UF_PRICE']) > 0) ? Utils::price($stage['UF_PRICE']) : null,
                                        'files' => $stageFiles
                                    ];
                                }
                            }

                            if (intval($userParams['PERSONAL_PHOTO'])) {
                                $img = \CFile::ResizeImageGet(
                                    intval($userParams['PERSONAL_PHOTO']),
                                    [
                                        'width' => 50,
                                        'height' => 50
                                    ],
                                    BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                    true
                                );
                                if (isset($img['src'])) {
                                    $photo = $img['src'];
                                }
                            }

                            $this->errorCode = 0;
                            $this->result = [
                                'id' => intval($result['UF_ID']),
                                'createdAt' => Utils::formatDate($result['UF_DATE_CREATE']),
                                'begin' => ($result['UF_BEGIN_WITH']) ? date('d.m.Y H:i', strtotime($result['UF_BEGIN_WITH'])) : null,
                                'end' => ($result['UF_RUN_UP']) ? date('d.m.Y H:i', strtotime($result['UF_RUN_UP'])) : null,
                                'categoryId' => intval($result['UF_IBLOCK_ID']),
                                'categoryType' => $result['UF_IBLOCK_TYPE'],
                                'categoryCode' => $result['UF_IBLOCK_CODE'],
                                'code' => $result['UF_CODE'],
                                'name' => $result['UF_NAME'],
                                'cityId' => intval($result['UF_CITY']),
                                'cityName' => $cityName,
                                'price' => floatval($result['UF_PRICE']),
                                'oldPrice' => floatval($result['UF_OLD_PRICE']),
                                'formattedPrice' => Utils::price(floatval($result['UF_PRICE'])),
                                'formattedOldPrice' => Utils::price(floatval($result['UF_OLD_PRICE'])),
                                'userId' => intval($result['UF_USER_ID']),
                                'description' => $result['UF_DESCRIPTION'],
                                'lat' => floatval($result['UF_LAT']),
                                'long' => floatval($result['UF_LONG']),
                                'images' => null,
                                'files' => null,
                                'hiddenImages' => null,
                                'hiddenFiles' => null,
                                'properties' => null,
                                'route' => null,
                                'views' => intval($result['UF_COUNTER']),
                                'responses' => intval($result['UF_RESPONSE_COUNT']),
                                'quickly' => (strtotime($result['UF_QUICKLY_END']) >= time()) ? 1 : 0,
                                'safe' => intval($result['UF_SAFE']),
                                'moderation' => intval($result['UF_MODERATION']),
                                'status' => intval($result['UF_STATUS']),
                                'stages' => $stages,
                                'user' => [
                                    'id' => intval($result['UF_USER_ID']),
                                    'c' => Utils::getChatId($result['UF_USER_ID']),
                                    'name' => $userParams['NAME'],
                                    'registeredAt' => strtotime($userParams['DATE_REGISTER']),
                                    'formattedRegisteredAt' => Utils::formatDate($userParams['DATE_REGISTER']),
                                    'photo' => $photo,
                                    'cityId' => intval($userParams['UF_DSPI_CITY']),
                                    'cityName' => null,
                                    'reviewsCount' => $reviews->getCountByUser(),
                                    'totalRating' => ($userParams['UF_DSPI_RATING'] && !is_null($userParams['UF_DSPI_RATING'])) ? floatval($userParams['UF_DSPI_RATING']) : 0,
                                    'ratingDetails' => [
                                        'positive' => intval($userRating['positive']),
                                        'negative' => intval($userRating['negative']),
                                        'neutral' => intval($userRating['neutral'])
                                    ]
                                ]
                            ];

                            $cityName = null;
                            if (intval($userParams['UF_DSPI_CITY'])) {
                                $getCity = $city->getById(intval($userParams['UF_DSPI_CITY']));
                                if (isset($getCity['name'])) {
                                    $this->result['user']['cityName'] = $getCity['name'];
                                }
                            }

                            if ($this->filteredUserId == intval($result['UF_USER_ID'])) {
                                $this->result['executor'] = null;

                                $response = new Response();
                                $response->setTaskId($result['UF_ID']);
                                $response->setIBlockId($result['UF_IBLOCK_ID']);
                                $this->result['unRead'] = $response->getUnreadCount();

                                $executor = $response->checkExecutor(true);
                                if ($executor['userId']) {
                                    $photo = null;
                                    if (intval($executor['userParams']['PERSONAL_PHOTO'])) {
                                        $img = \CFile::ResizeImageGet(
                                            intval($executor['userParams']['PERSONAL_PHOTO']),
                                            [
                                                'width' => 50,
                                                'height' => 50
                                            ],
                                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                            true
                                        );
                                        if (isset($img['src'])) {
                                            $photo = $img['src'];
                                        }
                                    }

                                    $cityName = null;
                                    if (intval($executor['userParams']['UF_DSPI_CITY'])) {
                                        $getCity = $city->getById(intval($executor['userParams']['UF_DSPI_CITY']));
                                        if (isset($getCity['name'])) {
                                            $cityName = $getCity['name'];
                                        }
                                    }

                                    $this->result['executor'] = [
                                        'id' => intval($executor['userParams']['ID']),
                                        'c' => Utils::getChatId($executor['userParams']['ID']),
                                        'name' => $executor['userParams']['NAME'],
                                        'registeredAt' => strtotime($executor['userParams']['DATE_REGISTER']),
                                        'formattedRegisteredAt' => Utils::formatDate($executor['userParams']['DATE_REGISTER']),
                                        'photo' => $photo,
                                        'cityId' => intval($executor['userParams']['UF_DSPI_CITY']),
                                        'cityName' => $cityName,
                                        'reviewsCount' => intval($executor['userParams']['reviewsCount']),
                                        'totalRating' => ($executor['userParams']['UF_DSPI_RATING'] && !is_null($executor['userParams']['UF_DSPI_RATING'])) ? floatval($executor['userParams']['UF_DSPI_RATING']) : 0,
                                        'ratingDetails' => [
                                            'positive' => intval($executor['userParams']['ratingDetails']['positive']),
                                            'negative' => intval($executor['userParams']['ratingDetails']['negative']),
                                            'neutral' => intval($executor['userParams']['ratingDetails']['neutral'])
                                        ]
                                    ];
                                }
                            } else {
                                if (intval($result['UF_SAFE'])) {
                                    $response = new Response();
                                    $response->setTaskId($result['UF_ID']);
                                    $response->setIBlockId($result['UF_IBLOCK_ID']);
                                    $executor = $response->checkExecutor();
                                    if ($executor['userId'] > 0) {

                                    }
                                }
                            }

                            if (strlen($result['UF_PROPERTIES']) > 0) {
                                $properties = unserialize($result['UF_PROPERTIES']);

                                foreach ($properties as $k => $v) {
                                    if (isset($v['route'])) {
                                        $_route = [];
                                        foreach ($v['route'] as $item) {
                                            $ex = explode(',', $item);
                                            $_route[] = [
                                                floatval($ex[0]),
                                                floatval($ex[1])
                                            ];
                                        }

                                        $this->result['route'] = $_route;
                                        unset($properties[$k], $_route);
                                    } else {
                                        $this->result['properties'][] = [
                                            'name' => $v['name'],
                                            'value' => $v['value']
                                        ];
                                    }
                                }
                            }

                            $files = unserialize($result['UF_FILES']);
                            if (count($files) > 0) {
                                $images = [];
                                $otherFiles = [];

                                foreach ($files as $file) {
                                    $getFile = \CFile::GetFromCache($file);

                                    if (Utils::checkImage($getFile[$file]['FILE_NAME'])) {
                                        $images[] = [
                                            'id' => intval($file),
                                            'name' => $getFile[$file]['FILE_NAME'],
                                            'src' => '/upload/' . $getFile[$file]['SUBDIR'] . '/' . $getFile[$file]['FILE_NAME']
                                        ];
                                    } else {
                                        $otherFiles[] = [
                                            'id' => intval($file),
                                            'name' => Utils::shortString($getFile[$file]['FILE_NAME']),
                                            'src' => '/upload/' . $getFile[$file]['SUBDIR'] . '/' . $getFile[$file]['FILE_NAME'],
                                            'size' => Utils::formatBytes($getFile[$file]['FILE_SIZE'])
                                        ];
                                    }
                                }

                                if (count($images) > 0) {
                                    foreach ($images as $image) {
                                        $imageThumb = \CFile::ResizeImageGet(
                                            $image['id'],
                                            [
                                                'width' => 200,
                                                'height' => 200
                                            ],
                                            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                            true
                                        );
                                        if (isset($imageThumb['src'])) {
                                            $image['thumb'] = $imageThumb['src'];
                                            $this->result['images'][] = $image;
                                        }
                                    }
                                }

                                if (count($otherFiles) > 0) {
                                    $this->result['files'] = $otherFiles;
                                }
                            }

                            //TODO исполнитель
                            if ($this->filteredUserId) {
                                $this->result['complain'] = null;

                                $files = unserialize($result['UF_HIDDEN_FILES']);
                                if (count($files) > 0) {
                                    $images = [];
                                    $otherFiles = [];

                                    foreach ($files as $file) {
                                        $getFile = \CFile::GetFromCache($file);

                                        if (Utils::checkImage($getFile[$file]['FILE_NAME'])) {
                                            $images[] = [
                                                'id' => intval($file),
                                                'name' => $getFile[$file]['FILE_NAME'],
                                                'src' => '/upload/' . $getFile[$file]['SUBDIR'] . '/' . $getFile[$file]['FILE_NAME']
                                            ];
                                        } else {
                                            $otherFiles[] = [
                                                'id' => intval($file),
                                                'name' => Utils::shortString($getFile[$file]['FILE_NAME']),
                                                'src' => '/upload/' . $getFile[$file]['SUBDIR'] . '/' . $getFile[$file]['FILE_NAME'],
                                                'size' => Utils::formatBytes($getFile[$file]['FILE_SIZE'])
                                            ];
                                        }
                                    }

                                    if (count($images) > 0) {
                                        foreach ($images as $image) {
                                            $imageThumb = \CFile::ResizeImageGet(
                                                $image['id'],
                                                [
                                                    'width' => 200,
                                                    'height' => 200
                                                ],
                                                BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                                                true
                                            );
                                            if (isset($imageThumb['src'])) {
                                                $image['thumb'] = $imageThumb['src'];
                                                $this->result['hiddenImages'][] = $image;
                                            }
                                        }
                                    }

                                    if (count($otherFiles) > 0) {
                                        $this->result['hiddenFiles'] = $otherFiles;
                                    }
                                }

                                if ($this->result['status'] == 6) {
                                    $complain = new Complain();
                                    $complain->setTaskId($result['UF_ID']);
                                    $getComplain = $complain->get();

                                    $this->result['complain'] = [
                                        'id' => intval($getComplain['ID']),
                                        'createdAt' => date('d.m.Y H:i', strtotime($getComplain['UF_CREATED_AT'])),
                                        'formattedCreatedAt' => Utils::formatDate($getComplain['UF_CREATED_AT']),
                                        'stage' => intval($getComplain['UF_STAGE_ID']),
                                        'status' => intval($getComplain['UF_STATUS']),
                                        'text' => $getComplain['UF_TEXT'],
                                        'userId' => intval($getComplain['UF_USER_ID'])
                                    ];
                                }
                            }

                            $stat = new Stat(intval($result['UF_ID']), intval($result['UF_IBLOCK_ID']));
                            if ($us->getId()) {
                                $stat->setUserId($us->getId());
                            }
                            $stat->set();
                        } else {
                            $this->errorMessage = 'Not Found';
                        }
                    } else {
                        $this->errorMessage = 'Invalid item code';
                    }
                } else {
                    $this->errorMessage = 'Invalid category code';
                }
            } else {
                $this->errorMessage = 'Invalid category type';
            }
        } else {
            $this->errorMessage = 'Invalid Id';
        }
    }
}
