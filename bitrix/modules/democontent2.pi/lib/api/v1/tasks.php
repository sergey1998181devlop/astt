<?php
/**
 * Date: 14.08.2019
 * Time: 08:18
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
use Democontent2\Pi\Iblock\Items;
use Democontent2\Pi\Iblock\Menu;
use Democontent2\Pi\Iblock\Response;
use Democontent2\Pi\Utils;

class Tasks extends Items implements IApi
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
        $result = [];

        $us = new \Democontent2\Pi\User(0);
        $us->checkKey($request);

        if ($us->getId()) {
            $this->setFilteredUserId($us->getId());
        }

        if ($request->get('page')) {
            if (intval($request->get('page')) > 1) {
                $this->setOffset((intval($request->get('page')) - 1) * $this->getLimit());
            }
        }

        $this->setOrder(['UF_CREATED_AT' => 'DESC']);

        if ($request->get('order')) {
            switch ($request->get('order')) {
                case 'old':
                    $this->setOrder(['UF_CREATED_AT' => 'ASC']);
                    break;
                case 'expensive':
                    $this->setOrder(['UF_PRICE' => 'DESC']);
                    break;
                case 'cheap':
                    $this->setOrder(['UF_PRICE' => 'ASC']);
                    break;
                case 'popular':
                    $this->setOrder(['UF_COUNTER' => 'DESC']);
                    break;
                case 'moreFeedback':
                    $this->setOrder(
                        [
                            'UF_RESPONSE_COUNT' => 'DESC',
                            'ID' => 'DESC'
                        ]
                    );
                    break;
                case 'lessFeedback':
                    $this->setOrder(
                        [
                            'UF_RESPONSE_COUNT' => 'ASC',
                            'ID' => 'DESC'
                        ]
                    );
                    break;
            }
        }

        if ($request->get('quickly')) {
            $this->setQuickly(true);
        }

        if ($request->get('safe')) {
            $this->setSafe(true);
        }

        if ($request->get('noResponses')) {
            $this->setNoResponses(true);
        }

        if ($request->get('lessThan10')) {
            $this->setMin10Responses(true);
        }

        if ($request->get('cityId')) {
            $city = new \Democontent2\Pi\Iblock\City();
            $cities = $city->getList();
            if (count($cities)) {
                if (isset($cities[intval($request->get('cityId'))])) {
                    $this->setCityId(intval($request->get('cityId')));
                } else {
                    $this->extra['cityId'] = 'Not Found';
                }
            }
        }

        if ($request->get('categoryType') || $request->get('categoryId')) {
            $menu = new Menu();
            $menuList = $menu->get();

            if ($request->get('categoryType')) {
                if (isset($menuList[$request->get('categoryType')])) {
                    $this->setIBlockType($request->get('categoryType'));
                } else {
                    $this->extra['categoryType'] = 'Not Found';
                }
            }

            if ($request->get('categoryId') && intval($request->get('categoryId'))) {
                $break = false;
                foreach ($menuList as $item) {
                    if (!isset($item['items']) || !count($item['items'])) {
                        continue;
                    }

                    foreach ($item['items'] as $_item) {
                        if (intval($_item['id']) == intval($request->get('categoryId'))) {
                            $this->setIBlockId(intval($_item['id']));
                            $break = true;
                            break;
                        }
                    }

                    if ($break) {
                        break;
                    }
                }
            }
        }

        if ($request->get('status')) {
            $this->setStatus(intval($request->get('status')));
        }

        if (!strlen($this->iBlockType) && !$this->iBlockId) {
            $result = $this->allTasks();
        } else {
            if (strlen($this->iBlockType)) {
                $result = $this->getByIblockType();
            } else {
                $result = $this->get();
            }
        }
        $this->errorCode = 0;

        if (count($result)) {
            if ($us->getId()) {
                $response = new Response();
            }
            $i = 0;
            foreach ($result as $item) {
                $photo = null;

                if (intval($item['USER']['PERSONAL_PHOTO'])) {
                    $img = \CFile::ResizeImageGet(
                        intval($item['USER']['PERSONAL_PHOTO']),
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

                $this->result[$i] = [
                    'id' => intval($item['UF_ITEM_ID']),
                    'createdAt' => (isset($item['UF_CREATED_AT'])) ? Utils::formatDate($item['UF_CREATED_AT']) : Utils::formatDate($item['UF_DATE_CREATE']),
                    'code' => str_replace('-' . $item['UF_ITEM_ID'], '', $item['UF_CODE']),
                    'categoryCode' => $item['UF_IBLOCK_CODE'],
                    'categoryType' => $item['UF_IBLOCK_TYPE'],
                    'categoryId' => intval($item['UF_IBLOCK_ID']),
                    'cityId' => intval($item['UF_CITY']),
                    'cityName' => $item['CITY_NAME'],
                    'name' => $item['UF_NAME'],
                    'status' => intval($item['UF_STATUS']),
                    'description' => $item['UF_DESCRIPTION'],
                    'price' => floatval($item['UF_PRICE']),
                    'formattedPrice' => Utils::price(floatval($item['UF_PRICE'])),
                    'responses' => intval($item['UF_RESPONSE_COUNT']),
                    'views' => intval($item['UF_COUNTER']),
                    'safe' => intval($item['UF_SAFE']),
                    'moderation' => intval($item['UF_MODERATION']),
                    'quickly' => (strtotime($item['UF_QUICKLY_END']) >= time()) ? 1 : 0,
                    'user' => [
                        'id' => intval($item['USER']['ID']),
                        'c' => Utils::getChatId($item['USER']['ID']),
                        'name' => $item['USER']['NAME'],
                        'photo' => $photo,
                        'cityId' => intval($item['USER']['UF_DSPI_CITY']),
                        'reviewsCount' => intval($item['REVIEWS_COUNT']),
                        'totalRating' => ($item['USER']['UF_DSPI_RATING'] && !is_null($item['USER']['UF_DSPI_RATING'])) ? floatval($item['USER']['UF_DSPI_RATING']) : 0,
                        'ratingDetails' => [
                            'positive' => intval($item['CURRENT_RATING']['positive']),
                            'negative' => intval($item['CURRENT_RATING']['negative']),
                            'neutral' => intval($item['CURRENT_RATING']['neutral'])
                        ]
                    ]
                ];

                if ($us->getId()) {
                    $response->setTaskId(intval($item['UF_ITEM_ID']));
                    $this->result[$i]['unRead'] = $response->getUnreadCount();
                }

                $i++;
            }
        }
    }
}
