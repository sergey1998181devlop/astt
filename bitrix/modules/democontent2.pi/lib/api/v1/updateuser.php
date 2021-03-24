<?php
/**
 * Date: 16.07.2019
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

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\IO\File;
use Bitrix\Main\Type\ParameterDictionary;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\Iblock\City;
use Democontent2\Pi\Utils;

class UpdateUser extends \Democontent2\Pi\User implements IApi
{
    private $errorCode = 1;
    private $errorMessage = '';
    private $result = [];
    private $extra = [];

    /**
     * UpdateUser constructor.
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
     * @throws \Bitrix\Main\SystemException
     */
    public function run(HttpRequest $request)
    {
        if ($this->checkKey($request)) {
            if ($this->getId()) {
                $params = $request->getPostList()->toArray();
                if (!count($params)) {
                    try {
                        $params = Json::decode(file_get_contents('php://input'));
                    } catch (ArgumentException $e) {
                    }
                }

                $dict = new ParameterDictionary($params);

                $userGroups = [];
                $groups = \CUser::GetUserGroupList($this->getId());
                while ($group = $groups->Fetch()) {
                    $userGroups[] = intval($group['GROUP_ID']);
                }

//                if (in_array(1, $userGroups)) {
//                    $this->errorMessage = 'User Not Found';
//                    return;
//                }

                $currentParams = $this->get();
                $newParams = [];

                if ($dict->get('name') && strlen($dict->get('name'))) {
                    if (trim($dict->get('name')) !== $currentParams['NAME']) {
                        $newParams['NAME'] = trim(htmlspecialcharsbx(HTMLToTxt($dict->get('name'))));
                        $this->result[] = 'name';
                    } else {
                        $this->extra['errors'][] = [
                            'name',
                            'Unmodified value'
                        ];
                    }
                }

                if ($dict->get('lastName') && strlen($dict->get('lastName'))) {
                    if (trim($dict->get('lastName')) !== $currentParams['LAST_NAME']) {
                        $newParams['LAST_NAME'] = trim(htmlspecialcharsbx(HTMLToTxt($dict->get('lastName'))));
                        $this->result[] = 'lastName';
                    } else {
                        $this->extra['errors'][] = [
                            'lastName',
                            'Unmodified value'
                        ];
                    }
                }

                if ($dict->get('cityId') && intval($dict->get('cityId'))) {
                    if (intval($dict->get('cityId')) !== intval($currentParams['UF_DSPI_CITY'])) {
                        $city = new City();
                        $cities = $city->getList();
                        if (count($cities)) {
                            foreach ($cities as $city) {
                                if (intval($city['id']) == intval($dict->get('cityId'))) {
                                    $newParams['UF_DSPI_CITY'] = intval($city['id']);
                                    $this->result[] = 'city';
                                    break;
                                }
                            }
                        }

                        unset($city, $cities);
                    } else {
                        $this->extra['errors'][] = [
                            'city',
                            'Unmodified value'
                        ];
                    }
                }

                if ($dict->get('password') && !is_null($dict->get('password')) && strlen(trim($dict->get('password'))) >= 6) {
                    $newParams['PASSWORD'] = trim(HTMLToTxt($dict->get('password')));
                    $newParams['CONFIRM_PASSWORD'] = trim(HTMLToTxt($dict->get('password')));
                    $this->result[] = 'password';
                }

                if ($dict->get('photo') && !is_null($dict->get('photo'))) {
                    $__image = Utils::createTempImageFromBase64($dict->get('photo'), 'users');
                    if (strlen($__image)) {
                        $newParams['PERSONAL_PHOTO'] = \CFile::MakeFileArray($__image);
                        if (intval($currentParams['PERSONAL_PHOTO']) > 0) {
                            $newParams['PERSONAL_PHOTO']['MODULE_ID'] = 'main';
                            $newParams['PERSONAL_PHOTO']['del'] = 'Y';
                            $newParams['PERSONAL_PHOTO']['old_file'] = intval($currentParams['PERSONAL_PHOTO']);
                        }

                        $this->result[] = 'photo';

                        if (File::isFileExists($__image)) {
                            //File::deleteFile($__image);
                        }
                    }
                }

//                if ($request->getFile('photo')) {
//                    $photo = $request->getFile('photo');
//                    switch ($photo['type']) {
//                        case 'image/jpeg':
//                            if (!intval($photo['error'])) {
//                                if (File::isFileExists($photo['tmp_name'])) {
//                                    $newParams['PERSONAL_PHOTO'] = \CFile::MakeFileArray($photo['tmp_name']);
//                                    $newParams['PERSONAL_PHOTO']['name'] = md5(microtime(true)) . '.jpg';
//                                    if (intval($currentParams['PERSONAL_PHOTO']) > 0) {
//                                        $newParams['PERSONAL_PHOTO']['MODULE_ID'] = 'main';
//                                        $newParams['PERSONAL_PHOTO']['del'] = 'Y';
//                                        $newParams['PERSONAL_PHOTO']['old_file'] = intval($currentParams['PERSONAL_PHOTO']);
//                                    }
//
//                                    $this->result[] = 'photo';
//                                }
//                            } else {
//                                $this->extra['errors'][] = [
//                                    'photo',
//                                    'Error Code: ' . intval($photo['error'])
//                                ];
//                            }
//                            break;
//                        case 'image/png':
//                            if (!intval($photo['error'])) {
//                                if (File::isFileExists($photo['tmp_name'])) {
//                                    $newParams['PERSONAL_PHOTO'] = \CFile::MakeFileArray($photo['tmp_name']);
//                                    $newParams['PERSONAL_PHOTO']['name'] = md5(microtime(true)) . '.png';
//                                    if (intval($currentParams['PERSONAL_PHOTO']) > 0) {
//                                        $newParams['PERSONAL_PHOTO']['MODULE_ID'] = 'main';
//                                        $newParams['PERSONAL_PHOTO']['del'] = 'Y';
//                                        $newParams['PERSONAL_PHOTO']['old_file'] = intval($currentParams['PERSONAL_PHOTO']);
//                                    }
//
//                                    $this->result[] = 'photo';
//                                }
//                            } else {
//                                $this->extra['errors'][] = [
//                                    'photo',
//                                    'Error Code: ' . intval($photo['error'])
//                                ];
//                            }
//                            break;
//                        default:
//                            $this->extra['errors'][] = [
//                                'photo',
//                                'Invalid file type. Allowed only image/jpeg or image/png.'
//                            ];
//                    }
//                }

                if (count($newParams)) {
                    $us = new \CUser();
                    $update = $us->Update($this->getId(), $newParams);
                    if ($update) {
                        $this->errorCode = 0;
                        Application::getInstance()->getTaggedCache()->clearByTag(md5('user_' . $this->getId()));
                    } else {
                        $this->errorMessage = 'Failed to update user account';
                        $this->extra['errors'][] = [
                            'update',
                            $us->LAST_ERROR
                        ];
                    }

                    unset($us);
                }

                unset($currentParams);
            } else {
                $this->errorMessage = 'User Not Found';
            }
        } else {
            $this->errorMessage = 'Invalid Key';
        }
    }
}
