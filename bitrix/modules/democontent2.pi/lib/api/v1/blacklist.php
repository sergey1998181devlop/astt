<?php
/**
 * Date: 07.08.2019
 * Time: 12:50
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

class BlackList extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * BlackList constructor.
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
     */
    public function run(HttpRequest $request)
    {
        if ($this->checkKey($request)) {
            $blackList = new \Democontent2\Pi\Iblock\BlackList();
            $blackList->setUserId($this->id);
            $get = $blackList->getList();
            if (count($get)) {
                $i = 0;
                $this->errorCode = 0;

                $city = new City();
                $cities = $city->getList();

                foreach ($get as $item) {
                    $photo = null;
                    $description = null;
                    $specializations = null;
                    $cityName = null;

                    if (isset($cities[intval($item['UF_DSPI_CITY'])])) {
                        $cityName = $cities[intval($item['UF_DSPI_CITY'])]['name'];
                    }

                    if (isset($item['AVATAR']['src'])) {
                        $photo = $item['AVATAR']['src'];
                    }

//                    if (isset($item['PROFILE']['UF_DATA'])) {
//                        if (strlen($item['PROFILE']['UF_DATA'])) {
//                            $data = unserialize($item['PROFILE']['UF_DATA']);
//
//                            if (isset($data['description'])) {
//                                if (strlen($data['description'])) {
//                                    $description = $data['description'];
//                                }
//                            }
//
//                            if (isset($data['subSpecializations'])) {
//                                foreach ($data['subSpecializations'] as $k => $v) {
//                                    foreach ($v as $_k => $_v) {
//                                        $specializations[] = [
//                                            'id' => intval($_k),
//                                            'name' => $_v
//                                        ];
//                                    }
//                                }
//                            }
//                        }
//                    }

                    $this->result[] = [
                        'id' => intval($item['ID']),
                        'c' => Utils::getChatId($item['ID']),
                        'registeredAt' => strtotime($item['DATE_REGISTER']),
                        'formattedRegisteredAt' => date('d.m.Y', strtotime($item['DATE_REGISTER'])),
                        'name' => $item['NAME'],
                        'lastName' => (strlen($item['LAST_NAME'])) ? $item['LAST_NAME'] : null,
                        'description' => $description,
                        'photo' => $photo,
                        'isExecutor' => (intval($item['UF_DSPI_EXECUTOR'])) ? true : false,
                        'isDocumentsChecked' => (intval($item['UF_DSPI_DOCUMENTS'])) ? true : false,
                        'isSafe' => (intval($item['CARD'])) ? true : false,
                        'cityId' => (intval($item['UF_DSPI_CITY'])) ? intval($item['UF_DSPI_CITY']) : 0,
                        'cityName' => $cityName,
                        'totalRating' => (floatval($item['UF_DSPI_RATING'])) ? floatval($item['UF_DSPI_RATING']) : 0,
                        'ratingDetails' => [
                            'positive' => intval($item['CURRENT_RATING']['positive']),
                            'negative' => intval($item['CURRENT_RATING']['negative']),
                            'neutral' => intval($item['CURRENT_RATING']['neutral'])
                        ],
                        'specializations' => $specializations
                    ];
                }
            } else {
                $this->errorCode = 0;
                $this->errorMessage = 'No Results';
            }

            unset($blackList, $get);
        } else {
            $this->errorMessage = 'Invalid Key';
        }
    }
}
