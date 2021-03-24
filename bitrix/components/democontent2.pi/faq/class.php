<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.01.2019
 * Time: 16:40
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class faqComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        $this->arResult['ITEMS'] = array();
        try {
            if (\Bitrix\Main\Loader::includeModule('iblock')) {
                if (isset($this->arParams['IBLOCK_ID']) && intval($this->arParams['IBLOCK_ID']) > 0) {
                    $cache = \Bitrix\Main\Application::getInstance()->getCache();
                    $cache_time = intval($this->arParams['CACHE_TIME']);
                    $cache_id = md5(DSPI . 'faq');
                    $cache_path = '/' . DSPI . '/faq';

                    $taggedCache = \Bitrix\Main\Application::getInstance()->getTaggedCache();

                    if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
                        $res = $cache->getVars();

                        if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                            $this->arResult['ITEMS'] = $res[$cache_id];
                        }
                    } else {
                        $taggedCache->startTagCache($cache_path);
                        $taggedCache->registerTag('iblock_id_' . intval($this->arParams['IBLOCK_ID']));

                        $sectionTable = new \Bitrix\Iblock\SectionTable();
                        $sections = $sectionTable->getList(
                            [
                                'select' => [
                                    'ID',
                                    'NAME'
                                ],
                                'filter' => [
                                    '=IBLOCK_ID' => intval($this->arParams['IBLOCK_ID']),
                                    '=ACTIVE' => 'Y'
                                ],
                                'order' => [
                                    'SORT' => 'ASC',
                                    'NAME' => 'ASC'
                                ]
                            ]
                        );
                        while ($sectionRes = $sections->fetch()) {
                            $get = CIBlockElement::GetList(
                                array(
                                    'SORT' => 'ASC',
                                    'NAME' => 'ASC'
                                ),
                                array(
                                    '=IBLOCK_ID' => intval($this->arParams['IBLOCK_ID']),
                                    '=ACTIVE' => 'Y',
                                    '=SECTION_ID' => $sectionRes['ID']
                                ),
                                false,
                                false,
                                array(
                                    'ID',
                                    'NAME',
                                    'DETAIL_TEXT'
                                )
                            );
                            while ($res = $get->GetNextElement()) {
                                $this->arResult['ITEMS'][$sectionRes['ID']]['ID'] = $sectionRes['ID'];
                                $this->arResult['ITEMS'][$sectionRes['ID']]['NAME'] = $sectionRes['NAME'];
                                $this->arResult['ITEMS'][$sectionRes['ID']]['ITEMS'][] = $res->GetFields();
                            }
                        }

                        if ($cache_time > 0) {
                            $cache->startDataCache($cache_time, $cache_id, $cache_path);
                            if (!count($this->arResult['ITEMS'])) {
                                $cache->abortDataCache();
                                $taggedCache->abortTagCache();
                            }
                            $cache->endDataCache(array($cache_id => $this->arResult['ITEMS']));
                            $taggedCache->endTagCache();
                        }
                    }
                }
            }
        } catch (\Bitrix\Main\LoaderException $e) {
        } catch (\Bitrix\Main\SystemException $e) {
        }

        $this->includeComponentTemplate();
    }
}