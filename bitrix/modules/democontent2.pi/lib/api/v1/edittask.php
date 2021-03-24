<?php
/**
 * Date: 12.08.2019
 * Time: 17:24
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
use Democontent2\Pi\Iblock\Item;
use Democontent2\Pi\Iblock\Menu;

class EditTask extends \Democontent2\Pi\User implements IApi
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
     */
    public function run(HttpRequest $request)
    {
        if ($this->checkKey($request)) {
            if ($this->getId()) {
                if ($request->getPostList()->get('id')) {
                    if ($request->getPostList()->get('cityId')) {
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

                        if ($request->getPostList()->get('categoryId') && in_array(intval($request->getPostList()->get('categoryId')), $categories)) {
                            if ($request->getPostList()->get('name') && $request->getPostList()->get('description')) {
                                $item = new Item();
                                $item->setUserId($this->id);
                                $item->setItemId($request->getPostList()->get('id'));
                                $item->setIBlockId($request->getPostList()->get('categoryId'));
                                $currentState = $item->getForEdit();

                                if (count($currentState)) {
                                    $location = [];
                                    $price = 0;
                                    $route = [];
                                    $prop = [];
                                    $files = [];

                                    if ($request->getPostList()->get('location')) {
                                        if (is_array($request->getPostList()->get('location'))) {
                                            if (count($request->getPostList()->get('location')) == 2) {
                                                $location = $request->getPostList()->get('location');
                                            }
                                        }
                                    }

                                    if ($request->getPostList()->get('price')) {
                                        $price = floatval($request->getPostList()->get('price'));
                                    }

                                    if ($request->getPostList()->get('route')) {
                                        if (is_array($request->getPostList()->get('route'))) {
                                            $route = $request->getPostList()->get('route');
                                        }
                                    }

                                    if ($request->getPostList()->get('prop')) {
                                        if (is_array($request->getPostList()->get('prop'))) {
                                            $prop = $request->getPostList()->get('prop');
                                        }
                                    }

                                    $data = [
                                        'iblock' => intval($request->getPostList()->get('categoryId')),
                                        'city' => intval($request->getPostList()->get('cityId')),
                                        'price' => ($price > 0) ? $price : 0,
                                        'location' => (count($location) == 2) ? implode(',', $location) : '',
                                        'route' => $route,
                                        'name' => $request->getPostList()->get('name'),
                                        'description' => '',
                                        'prop' => $prop,
                                        'security' => '',
                                        'deleteStages' => [],
                                        'newStages' => [],
                                    ];

                                    if ($request->getPostList()->get('description')) {
                                        $data['description'] = $request->getPostList()->get('description');
                                    }

                                    if ($request->getPostList()->get('dateStart')) {
                                        $data['dateStart'] = $request->getPostList()->get('dateStart');
                                    }

                                    if ($request->getPostList()->get('timeStart')) {
                                        $data['timeStart'] = $request->getPostList()->get('timeStart');
                                    }

                                    if ($request->getPostList()->get('dateEnd')) {
                                        $data['dateEnd'] = $request->getPostList()->get('dateEnd');
                                    }

                                    if ($request->getPostList()->get('timeEnd')) {
                                        $data['timeEnd'] = $request->getPostList()->get('timeEnd');
                                    }

                                    if ($request->getFileList()->get('stages')) {
                                        if (is_array($request->getPostList()->get('stages'))) {
                                            $data['stages'] = $request->getFileList()->get('stages');
                                        }
                                    }

                                    if ($request->getFileList()->get('deleteStages')) {
                                        if (is_array($request->getPostList()->get('deleteStages'))) {
                                            $data['deleteStages'] = $request->getFileList()->get('deleteStages');
                                        }
                                    }

                                    if ($request->getFileList()->get('removeFiles')) {
                                        if (is_array($request->getPostList()->get('removeFiles'))) {
                                            $data['__removeFiles'] = $request->getFileList()->get('removeFiles');
                                        }
                                    }

                                    if ($request->getFileList()->get('newStages')) {
                                        if (is_array($request->getPostList()->get('newStages'))) {
                                            $data['newStages'] = $request->getFileList()->get('newStages');
                                        }
                                    }

                                    if ($request->getPostList()->get('security')) {
                                        if (intval($request->getPostList()->get('security'))) {
                                            $data['security'] = 'on';
                                        }
                                    }

                                    if ($request->getFileList()->get('files')) {
                                        $files['__files'] = $request->getFileList()->get('files');
                                    }

                                    if ($request->getFileList()->get('hiddenFiles')) {
                                        $files['__hiddenFiles'] = $request->getFileList()->get('hiddenFiles');
                                    }

                                    $item->edit($currentState, $data, $files);

                                    if (strlen($item->getError())) {
                                        $this->errorMessage = strip_tags($item->getError());
                                    } else {
                                        $this->errorCode = 0;
                                    }

                                    if (count($item->getErrors())) {
                                        $this->extra = $item->getErrors();
                                    }
                                } else {
                                    $this->errorMessage = 'Task Not Found';
                                }
                            } else {
                                $this->errorMessage = 'Invalid name or description';
                            }
                        } else {
                            $this->errorMessage = 'Invalid category id';
                        }
                    } else {
                        $this->errorMessage = 'Invalid cityId';
                    }
                } else {
                    $this->errorMessage = 'Invalid Id';
                }
            } else {
                $this->errorMessage = 'User Not Found';
            }
        } else {
            $this->errorMessage = 'Invalid Key';
        }
    }
}
