<?php
/**
 * Date: 04.09.2019
 * Time: 13:14
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
use Democontent2\Pi\Iblock\Item;
use Democontent2\Pi\Iblock\Response;
use Democontent2\Pi\Utils;

class TaskResponses extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * SendMessage constructor.
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

    public function run(HttpRequest $request)
    {
        if ($request->get('id') && intval($request->get('id'))) {
            if ($request->get('iBlockId') && intval($request->get('iBlockId'))) {
                if ($this->checkKey($request)) {
                    if ($this->getId()) {
                        $item = new Item();
                        $item->setUserId($this->getId());
                        $item->setItemId($request->get('id'));
                        $item->setIBlockId($request->get('iBlockId'));
                        if ($item->checkOwner()) {
                            $city = new City();
                            $cities = $city->getList();
                            $response = new Response();
                            $response->setTaskId($request->get('id'));
                            $results = $response->getList(true);

                            $this->errorCode = 0;

                            foreach ($results as $item) {
                                $photo = null;
                                $_files = null;
                                $files = unserialize($item['UF_FILES']);
                                if (count($files) > 0) {
                                    foreach ($files as $key => $file) {
                                        $getFile = \CFile::GetFromCache($file);
                                        $_files[] = [
                                            'name' => $getFile[$file]['FILE_NAME'],
                                            'src' => '/upload/' . $getFile[$file]['SUBDIR'] . '/' . $getFile[$file]['FILE_NAME']
                                        ];
                                    }
                                }

                                if (intval($item['USER_DATA']['PERSONAL_PHOTO'])) {
                                    $img = \CFile::ResizeImageGet(
                                        intval($item['USER_DATA']['PERSONAL_PHOTO']),
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

                                $this->result[] = [
                                    'id' => intval($item['ID']),
                                    'taskId' => intval($request->get('id')),
                                    'categoryId' => intval($request->get('iBlockId')),
                                    'createdAt' => Utils::formatDate($item['UF_CREATED_AT']),
                                    'isExecutor' => (intval($item['UF_EXECUTOR'])) ? 1 : 0,
                                    'isCandidate' => (intval($item['UF_CANDIDATE'])) ? 1 : 0,
                                    'isDenied' => (intval($item['UF_DENIED'])) ? 1 : 0,
                                    'text' => $item['UF_TEXT'],
                                    'files' => $_files,
                                    'user' => [
                                        'id' => intval($item['UF_USER_ID']),
                                        'c' => Utils::getChatId($item['UF_USER_ID']),
                                        'name' => $item['USER_DATA']['NAME'],
                                        'registeredAt' => strtotime($item['USER_DATA']['DATE_REGISTER']),
                                        'formattedRegisteredAt' => date('d.m.Y', strtotime($item['USER_DATA']['DATE_REGISTER'])),
                                        'photo' => $photo,
                                        'isDocumentsChecked' => intval($item['USER_DATA']['UF_DSPI_DOCUMENTS']),
                                        'isSafe' => (intval($item['USER_DATA']['CARD'])) ? true : false,
                                        'cityId' => intval($item['USER_DATA']['UF_DSPI_CITY']),
                                        'cityName' => (isset($cities[intval($item['USER_DATA']['UF_DSPI_CITY'])])) ? $cities[intval($item['USER_DATA']['UF_DSPI_CITY'])]['name'] : null,
                                        'totalRating' => intval($item['USER_DATA']['CURRENT_RATING']['percent']),
                                        'ratingDetails' => [
                                            'positive' => intval($item['USER_DATA']['CURRENT_RATING']['positive']),
                                            'negative' => intval($item['USER_DATA']['CURRENT_RATING']['negative']),
                                            'neutral' => intval($item['USER_DATA']['CURRENT_RATING']['neutral'])
                                        ]
                                    ]
                                ];
                            }
                        } else {
                            $this->errorMessage = 'Access denied';
                        }
                    } else {
                        $this->errorMessage = 'User Not Found';
                    }
                } else {
                    $this->errorMessage = 'Invalid Key';
                }
            } else {
                $this->errorMessage = 'Invalid category id';
            }
        } else {
            $this->errorMessage = 'Invalid Id';
        }
    }
}
