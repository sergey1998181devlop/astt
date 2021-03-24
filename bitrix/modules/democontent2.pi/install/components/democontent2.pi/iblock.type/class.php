<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.01.2019
 * Time: 13:17
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class IblockTypeComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        if (\Bitrix\Main\Loader::includeModule('democontent2.pi')) {
            $iblock = new \Democontent2\Pi\Iblock\Iblock();
            $iblocks = $iblock->getAllList($this->arParams['iBlockType']);

            if (count($iblocks) > 0) {
                $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
                $iblockMeta = new \Democontent2\Pi\Iblock\IblockTypeMeta($this->arParams['iBlockType']);
                $city = new \Democontent2\Pi\Iblock\City();
                $items = new \Democontent2\Pi\Iblock\Items();
                $cities = $city->getList();

                $items->setIBlockType($this->arParams['iBlockType']);

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

                $cityId = 0;
                $cityCode = '';
                $cityParams = array();

                if (isset($this->arParams['cityCode'])) {
                    $cityCode = $this->arParams['cityCode'];
                }

                foreach ($cities as $c) {
                    if (intval($c['default']) > 0) {
                        $cityId = intval($c['id']);
                        $cityParams = $c;
                        break;
                    }
                }

                foreach ($cities as $c) {
                    if ($c['code'] == $cityCode) {
                        if (intval($c['default']) > 0) {
                            LocalRedirect(SITE_DIR . $this->arParams['iBlockType'] . '/', true);
                        } else {
                            $cityId = intval($c['id']);
                            $cityParams = $c;
                        }
                        break;
                    }
                }

                $this->arResult['CITY'] = $cityParams;

                $this->arResult['META'] = $iblockMeta->get();
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

                if (isset($this->arParams['LIMIT']) && intval($this->arParams['LIMIT']) > 0) {
                    $items->setLimit(intval($this->arParams['LIMIT']));
                } elsE {
                    $items->setLimit(30);
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
                            $items->setOrder(['UF_CREATED_AT' => 'ASC']);
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
                            $items->setOrder(['UF_CREATED_AT' => 'DESC']);
                            $this->arResult['ORDER'] = 'new';
                    }
                }

                $this->arResult['IBLOCKS'] = $iblocks;
                $this->arResult['ITEMS'] = $items->getByIblockType();
                $this->arResult['TOTAL'] = $items->getTotal();
                $this->arResult['LIMIT'] = $items->getLimit();
                $this->arResult['OFFSET'] = $items->getOffset();
                $this->arResult['CURRENT_PAGE'] = $currentPage;
                $this->arResult['CURRENT_URL'] = SITE_DIR . $this->arParams['iBlockType'] . '/';

                $this->includeComponentTemplate();
            } else {
                CHTTP::SetStatus('404 Not Found');
            }
        } else {
            CHTTP::SetStatus('404 Not Found');
        }
    }
}