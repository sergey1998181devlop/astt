<?php
/**
 * Date: 12.08.2019
 * Time: 13:44
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
use Bitrix\Main\Type\ParameterDictionary;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\Iblock\City;
use Democontent2\Pi\Iblock\Item;
use Democontent2\Pi\Iblock\Menu;

class CreateTask extends \Democontent2\Pi\User implements IApi
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

    /**
     * @param HttpRequest $request
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
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

                $city = new City();
                $cities = $city->getList();

                if ($dict->get('cityId') && isset($cities[intval($dict->get('cityId'))])) {
                    $menu = new Menu();
                    $menuList = $menu->get();
                    $categories = [];
                    foreach ($menuList as $k => $v) {
                        if (!count($v['items'])) {
                            continue;
                        }

                        foreach ($v['items'] as $item) {
                            $categories[] = intval($item['id']);
                        }
                    }

                    if ($dict->get('categoryId') && in_array(intval($dict->get('categoryId')), $categories)) {
                        if ($dict->get('name')) {
                            $location = [];
                            $price = 0;
                            $route = [];
                            $prop = [];
                            $files = [];

                            if ($dict->get('location')) {
                                if (is_array($dict->get('location'))) {
                                    if (count($dict->get('location')) == 2) {
                                        $location = $dict->get('location');
                                    }
                                }
                            }

                            if ($dict->get('price')) {
                                $price = floatval($dict->get('price'));
                            }

                            if ($dict->get('route')) {
                                if (is_array($dict->get('route'))) {
                                    $route = $dict->get('route');
                                }
                            }

                            if ($dict->get('prop')) {
                                if (is_array($dict->get('prop'))) {
                                    $prop = $dict->get('prop');
                                }
                            }

                            $data = [
                                'iblock' => intval($dict->get('categoryId')),
                                'city' => intval($dict->get('cityId')),
                                'price' => ($price > 0) ? $price : 0,
                                'location' => (count($location) == 2) ? implode(',', $location) : '',
                                'route' => $route,
                                'name' => $dict->get('name'),
                                'description' => '',
                                'prop' => $prop,
                                'security' => ''
                            ];

                            if ($dict->get('description')) {
                                $data['description'] = $dict->get('description');
                            }

                            if ($dict->get('dateStart')) {
                                $data['dateStart'] = $dict->get('dateStart');
                            }

                            if ($dict->get('timeStart')) {
                                $data['timeStart'] = $dict->get('timeStart');
                            }

                            if ($dict->get('dateEnd')) {
                                $data['dateEnd'] = $dict->get('dateEnd');
                            }

                            if ($dict->get('timeEnd')) {
                                $data['timeEnd'] = $dict->get('timeEnd');
                            }

                            if ($dict->get('stages')) {
                                if (is_array($dict->get('stages'))) {
                                    $data['stages'] = $dict->get('stages');
                                }
                            }

                            if ($dict->get('security')) {
                                if (intval($dict->get('security'))) {
                                    $data['security'] = 'on';
                                }
                            }

                            if ($request->getFileList()->get('files')) {
                                $files['__files'] = $request->getFileList()->get('files');
                            }

                            if ($request->getFileList()->get('hiddenFiles')) {
                                $files['__hiddenFiles'] = $request->getFileList()->get('hiddenFiles');
                            }

                            if ($dict->get('response-checklist')) {
                                $data['response-checklist'] = $dict->get('response-checklist');
                            }

                            $item = new Item();
                            $item->setUserId($this->getId());
                            $item->create($data, $files);

                            if ($item->getItemId()) {
                                $this->errorCode = 0;
                                $this->result = [
                                    'id' => $item->getItemId(),
                                    'redirect' => (strlen($item->getPaymentRedirect()) > 0) ? $item->getPaymentRedirect() : null,
                                    'moderation' => intval(Option::get(DSPI, 'moderation_new'))
                                ];

                                if (count($item->getErrors())) {
                                    $this->extra = $item->getErrors();
                                }
                            } else {
                                if (strlen($item->getError())) {
                                    $this->errorMessage = strip_tags($item->getError());
                                }

                                if (count($item->getErrors())) {
                                    $this->extra = $item->getErrors();
                                }
                            }
                        } else {
                            $this->errorMessage = 'Invalid name';
                        }
                    } else {
                        $this->errorMessage = 'Invalid category id';
                    }
                } else {
                    $this->errorMessage = 'Invalid city id';
                }
            } else {
                $this->errorMessage = 'User Not Found';
            }
        } else {
            $this->errorMessage = 'Invalid Key';
        }
    }
}
