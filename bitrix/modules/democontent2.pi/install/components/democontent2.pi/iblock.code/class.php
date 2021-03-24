<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.01.2019
 * Time: 12:23
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class ListComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        if (\Bitrix\Main\Loader::includeModule('democontent2.pi')) {
            $iblock = new \Democontent2\Pi\Iblock\Iblock();
            $iblocks = $iblock->getAllList($this->arParams['iBlockType']);

            if (count($iblocks) > 0) {
                $iblockId = 0;
                $allow = false;
                $ids = array();
                foreach ($iblocks as $k => $v) {
                    foreach ($v as $item) {
                        if ($item['code'] == $this->arParams['iBlockCode']) {
                            $ids[] = $item['id'];
                            $iblockId = $item['id'];
                            $allow = true;
                            break;
                        }
                    }
                }

                if (!$allow) {
                    CHTTP::SetStatus('404 Not Found');
                } else {
                    $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
                    $items = new \Democontent2\Pi\Iblock\Items();
                    $iblockTypeMeta = new \Democontent2\Pi\Iblock\IblockTypeMeta($this->arParams['iBlockType']);
                    $iblockMeta = new \Democontent2\Pi\Iblock\IblockIdMeta($iblockId);
                    $city = new \Democontent2\Pi\Iblock\City();
                    $cities = $city->getList();

                    if (count($cities) < 2 || intval($request->getCookie('skipCity'))) {
                        $items->setSkipCity(true);
                    }

                    if (intval($request->getCookie('current_city')) > 0) {
                        if (!$items->isSkipCity()) {
                            if (isset($cities[intval($request->getCookie('current_city'))])) {
                                $items->setCityId(intval($request->getCookie('current_city')));
                            }
                        }
                    }

                    $cityCode = '';

                    if (isset($this->arParams['cityCode'])) {
                        $cityCode = $this->arParams['cityCode'];
                    }

                    foreach ($cities as $c) {
                        if (intval($c['default']) > 0) {
                            $cityParams = $c;
                            break;
                        }
                    }

                    foreach ($cities as $c) {
                        if ($c['code'] == $cityCode) {
                            if (intval($c['default']) > 0) {
                                LocalRedirect(SITE_DIR . $this->arParams['iBlockType'] . '/', true);
                            } else {
                                $cityParams = $c;
                            }
                            break;
                        }
                    }

                    if (isset($this->arParams['LIMIT']) && intval($this->arParams['LIMIT']) > 0) {
                        $items->setLimit(intval($this->arParams['LIMIT']));
                    }

                    if (isset($this->arParams['iBlockType'])) {
                        $items->setIBlockType($this->arParams['iBlockType']);
                    }

                    if (isset($this->arParams['iBlockCode'])) {
                        $items->setIBlockCode($this->arParams['iBlockCode']);
                    }

                    $currentPage = 1;
                    if ($request->get('page')) {
                        if (intval($request->get('page')) > 1) {
                            $currentPage = intval($request->get('page'));
                            $items->setOffset((intval($request->get('page')) - 1) * $items->getLimit());
                        }
                    }

                    if ($request->get('param1')) {
                        $items->setQuickly(true);
                    }

                    if ($request->get('param2')) {
                        $items->setNoResponses(true);
                    }

                    if ($request->get('param3')) {
                        $items->setSafe(true);
                    }

                    if ($request->get('param4')) {
                        $items->setMin10Responses(true);
                    }

                    $this->arResult['ORDER'] = 'new';

                    if ($request->get('order')) {
                        switch ($request->get('order')) {
                            case 'old':
                                $items->setOrder(['UF_DATE_CREATE' => 'ASC']);
                                $this->arResult['ORDER'] = $request->get('order');
                                break;
                            case 'expensive':
                                $items->setOrder(['UF_PRICE' => 'DESC']);
                                $this->arResult['ORDER'] = $request->get('order');
                                break;
                            case 'cheap':
                                $items->setOrder(['UF_PRICE' => 'ASC']);
                                $this->arResult['ORDER'] = $request->get('order');
                                break;
                            case 'popular':
                                $items->setOrder(['UF_COUNTER' => 'DESC']);
                                $this->arResult['ORDER'] = $request->get('order');
                                break;
                            case 'moreFeedback':
                                $items->setOrder(
                                    [
                                        'UF_RESPONSE_COUNT' => 'DESC',
                                        'ID' => 'DESC'
                                    ]
                                );
                                $this->arResult['ORDER'] = $request->get('order');
                                break;
                            case 'lessFeedback':
                                $items->setOrder(
                                    [
                                        'UF_RESPONSE_COUNT' => 'ASC',
                                        'ID' => 'DESC'
                                    ]
                                );
                                $this->arResult['ORDER'] = $request->get('order');
                                break;
                            default:
                                $items->setOrder(['UF_DATE_CREATE' => 'DESC']);
                                $this->arResult['ORDER'] = 'new';
                        }
                    }

                    $this->arResult['IBLOCK_ID'] = $iblockId;
                    $this->arResult['ITEMS'] = $items->get();
                    $this->arResult['TOTAL'] = $items->getTotal();
                    $this->arResult['LIMIT'] = $items->getLimit();
                    $this->arResult['OFFSET'] = $items->getOffset();
                    $this->arResult['CURRENT_PAGE'] = $currentPage;
                    $this->arResult['CITY'] = $items->getCityParams();
                    $this->arResult['META'] = $items->getMeta();
                    $this->arResult['CURRENT_URL'] = SITE_DIR . $this->arParams['iBlockType'] . '/' . $items->getIBlockCode() . '/';
                    $this->arResult['META'] = $iblockMeta->get();
                    $this->arResult['TYPE_META'] = $iblockTypeMeta->get();

                    foreach ($this->arResult['META'] as $metaKey => $metaValue) {
                        $tempMeta = str_replace('#CITY_DECLENSION#', $cityParams['declension'], $metaValue);
                        $tempMetaExplode = explode(' ', $tempMeta);
                        $tempMeta = array();
                        foreach ($tempMetaExplode as $t) {
                            if (strlen($t) > 0) {
                                $tempMeta[] = $t;
                            }
                        }

                        $this->arResult['META'][$metaKey] = implode(' ', $tempMeta);
                    }

                    foreach ($this->arResult['TYPE_META'] as $metaKey => $metaValue) {
                        $tempMeta = str_replace('#CITY_DECLENSION#', $cityParams['declension'], $metaValue);
                        $tempMetaExplode = explode(' ', $tempMeta);
                        $tempMeta = array();
                        foreach ($tempMetaExplode as $t) {
                            if (strlen($t) > 0) {
                                $tempMeta[] = $t;
                            }
                        }

                        $this->arResult['TYPE_META'][$metaKey] = implode(' ', $tempMeta);
                    }

                    if (count($this->arResult['ITEMS']) > 0) {
                        $reviews = new \Democontent2\Pi\Iblock\Reviews();
                        $us = new \Democontent2\Pi\User(0);
                        foreach ($this->arResult['ITEMS'] as $k => $item) {
                            $us->setId(intval($item['UF_USER_ID']));
                            $reviews->setUserId(intval($item['UF_USER_ID']));

                            $this->arResult['ITEMS'][$k]['USER'] = $us->get();
                            $this->arResult['ITEMS'][$k]['RATING'] = $reviews->rating();
                            $this->arResult['ITEMS'][$k]['REVIEWS_COUNT'] = $reviews->getCountByUser();

                            $this->arResult['ITEMS'][$k]['CITY_NAME'] = '';
                            if (isset($cities[$this->arResult['ITEMS'][$k]['UF_CITY']])) {
                                $this->arResult['ITEMS'][$k]['CITY_NAME'] = $cities[$this->arResult['ITEMS'][$k]['UF_CITY']]['name'];
                            }
                        }
                    }

                    $this->includeComponentTemplate();
                }
            }
        }
    }
}