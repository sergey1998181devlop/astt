<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 08.01.2019
 * Time: 12:00
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Iblock;

use Bitrix\Iblock\InheritedProperty\ElementValues;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Democontent2\Pi\Utils;

class City
{
    private $ttl = 86400;

    /**
     * @param int $ttl
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;
    }

    /**
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getRegions()
    {
        $result = [];

        $cache = Application::getInstance()->getCache();
        $cache_time = $this->ttl;
        $cache_id = md5('all' . $bigCity);
        $cache_path = '/' . DSPI . '/cities';

        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();

            if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                $result = $res[$cache_id];
            }
        } else {
            $iblockId = Utils::getIBlockIdByType('__democontent2_pi', 'cities');
            $taggedCache->startTagCache($cache_path);
            $taggedCache->registerTag('iblock_id_' . $iblockId);

            $sectionTable = new SectionTable();
            $get = $sectionTable::getList(
                [
                    'select' => [
                        'ID',
                        'NAME'
                    ],
                    'filter' => [
                        '=IBLOCK_ID' => $iblockId,
                        '=ACTIVE' => 'Y'
                    ],
                    'order' => [
                        'SORT' => 'ASC',
                        'NAME' => 'ASC'
                    ]
                ]
            );
            while ($res = $get->fetch()) {
                $result[] = $res;
            }

            if ($cache_time > 0) {
                $cache->startDataCache($cache_time, $cache_id, $cache_path);
                if (!count($result)) {
                    $cache->abortDataCache();
                    $taggedCache->abortTagCache();
                }
                $cache->endDataCache([$cache_id => $result]);
                $taggedCache->endTagCache();
            }
        }

        return $result;
    }

    public function getList($bigCity = 0, $regionId = 0)
    {
        $result = [];

        $cache = Application::getInstance()->getCache();
        $cache_time = $this->ttl;
        $cache_id = md5('all' . $bigCity . $regionId);
        $cache_path = '/' . DSPI . '/cities';

        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();

            if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                $result = $res[$cache_id];
            }
        } else {
            $iblockId = Utils::getIBlockIdByType('__democontent2_pi', 'cities');
            $taggedCache->startTagCache($cache_path);
            $taggedCache->registerTag('iblock_id_' . $iblockId);

            $filter = [
                '=IBLOCK_ID' => $iblockId,
                '=ACTIVE' => 'Y'
            ];

            if ($bigCity > 0) {
                $filter['=PROPERTY_hidden_big'] = 1;
            }

            if ($regionId > 0) {
                $filter['=IBLOCK_SECTION_ID'] = $regionId;
            }

            $el = new \CIBlockElement();
            $get = $el->GetList(
                [
                    'SORT' => 'ASC',
                    'NAME' => 'ASC'
                ],
                $filter,
                false,
                false,
                [
                    '*',
                    'PROPERTY_*'
                ]
            );

            while ($res = $get->GetNextElement()) {
                $fields = $res->GetFields();
                $properties = $res->GetProperties();

                $coords = [];
                if ($properties['hidden_coordinates']['VALUE']) {
                    $ex = explode(',', $properties['hidden_coordinates']['VALUE']);
                    $coords[] = floatval($ex[0]);
                    $coords[] = floatval($ex[1]);
                }
                $result[$fields['ID']] = [
                    'id' => $fields['ID'],
                    'name' => $fields['NAME'],
                    'code' => $fields['CODE'],
                    'declension' => $properties['hidden_declension']['VALUE'],
                    'default' => ($properties['hidden_default']['VALUE_XML_ID'] == '__default_yes') ? 1 : 0,
                    'coordinates' => $coords
                ];
            }

            if ($cache_time > 0) {
                $cache->startDataCache($cache_time, $cache_id, $cache_path);
                if (!count($result)) {
                    $cache->abortDataCache();
                    $taggedCache->abortTagCache();
                }
                $cache->endDataCache([$cache_id => $result]);
                $taggedCache->endTagCache();
            }
        }

        return $result;
    }

    public function getDefault()
    {
        $result = [];

        $cache = Application::getInstance()->getCache();
        $cache_time = $this->ttl;
        $cache_id = md5('default');
        $cache_path = '/' . DSPI . '/cities';

        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();

            if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                $result = $res[$cache_id];
            }
        } else {
            $iblockId = Utils::getIBlockIdByType('__democontent2_pi', 'cities');
            $taggedCache->startTagCache($cache_path);
            $taggedCache->registerTag('iblock_id_' . $iblockId);

            $el = new \CIBlockElement();
            $get = $el->GetList(
                [],
                [
                    '=IBLOCK_ID' => $iblockId,
                    '=ACTIVE' => 'Y',
                    '=PROPERTY_hidden_default_VALUE' => Loc::getMessage('CITY_DEFAULT_YES')
                ],
                false,
                false,
                [
                    '*',
                    'PROPERTY_*'
                ]
            );

            while ($res = $get->GetNextElement()) {
                $fields = $res->GetFields();
                $properties = $res->GetProperties();
                $meta = new ElementValues($fields['IBLOCK_ID'], $fields['ID']);

                $result = [
                    'id' => $fields['ID'],
                    'name' => $fields['NAME'],
                    'code' => $fields['CODE'],
                    'coordinates' => $properties['hidden_coordinates']['VALUE'],
                    'declension' => $properties['hidden_declension']['VALUE'],
                    'default' => ($properties['hidden_default']['VALUE_XML_ID'] == '__default_yes') ? 1 : 0,
                    'meta' => $meta->getValues()
                ];
            }

            if ($cache_time > 0) {
                $cache->startDataCache($cache_time, $cache_id, $cache_path);
                if (!count($result)) {
                    $cache->abortDataCache();
                    $taggedCache->abortTagCache();
                }
                $cache->endDataCache([$cache_id => $result]);
                $taggedCache->endTagCache();
            }
        }

        return $result;
    }

    public function getByCode($code)
    {
        $result = [];

        $cache = Application::getInstance()->getCache();
        $cache_time = $this->ttl;
        $cache_id = md5($code);
        $cache_path = '/' . DSPI . '/cities';

        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();

            if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                $result = $res[$cache_id];
            }
        } else {
            $iblockId = Utils::getIBlockIdByType('__democontent2_pi', 'cities');
            $taggedCache->startTagCache($cache_path);
            $taggedCache->registerTag('iblock_id_' . $iblockId);

            $el = new \CIBlockElement();
            $get = $el->GetList(
                [],
                [
                    '=CODE' => $code,
                    '=ACTIVE' => 'Y',
                    '=IBLOCK_ID' => $iblockId
                ],
                false,
                false,
                [
                    '*',
                    'PROPERTY_*'
                ]
            );
            while ($res = $get->GetNextElement()) {
                $fields = $res->GetFields();
                $properties = $res->GetProperties();

                $meta = new ElementValues($fields['IBLOCK_ID'], $fields['ID']);

                $result = [
                    'id' => $fields['ID'],
                    'name' => $fields['NAME'],
                    'code' => $fields['CODE'],
                    'coordinates' => $properties['hidden_coordinates']['VALUE'],
                    'declension' => $properties['hidden_declension']['VALUE'],
                    'default' => ($properties['hidden_default']['VALUE_XML_ID'] == '__default_yes') ? 1 : 0,
                    'meta' => $meta->getValues()
                ];
            }

            if ($cache_time > 0) {
                $cache->startDataCache($cache_time, $cache_id, $cache_path);
                if (!count($result)) {
                    $cache->abortDataCache();
                    $taggedCache->abortTagCache();
                }
                $cache->endDataCache([$cache_id => $result]);
                $taggedCache->endTagCache();
            }
        }

        return $result;
    }

    public function getById($id)
    {
        $result = [];

        $cache = Application::getInstance()->getCache();
        $cache_time = $this->ttl;
        $cache_id = md5($id);
        $cache_path = '/' . DSPI . '/cities';

        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();

            if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                $result = $res[$cache_id];
            }
        } else {
            $iblockId = Utils::getIBlockIdByType('__democontent2_pi', 'cities');
            $taggedCache->startTagCache($cache_path);
            $taggedCache->registerTag($cache_id);
            $taggedCache->registerTag('iblock_id_' . $iblockId);

            $el = new \CIBlockElement();
            $get = $el->GetList(
                [],
                [
                    '=ID' => $id,
                    '=ACTIVE' => 'Y',
                    '=IBLOCK_ID' => $iblockId
                ],
                false,
                false,
                [
                    '*',
                    'PROPERTY_*'
                ]
            );
            while ($res = $get->GetNextElement()) {
                $fields = $res->GetFields();
                $properties = $res->GetProperties();

                $meta = new ElementValues($fields['IBLOCK_ID'], $fields['ID']);

                $result = [
                    'id' => $fields['ID'],
                    'name' => $fields['NAME'],
                    'code' => $fields['CODE'],
                    'declension' => $properties['hidden_declension']['VALUE'],
                    'default' => ($properties['hidden_default']['VALUE_XML_ID'] == '__default_yes') ? 1 : 0,
                    'meta' => $meta->getValues()
                ];
            }

            if ($cache_time > 0) {
                $cache->startDataCache($cache_time, $cache_id, $cache_path);
                if (!count($result)) {
                    $cache->abortDataCache();
                    $taggedCache->abortTagCache();
                }
                $cache->endDataCache([$cache_id => $result]);
                $taggedCache->endTagCache();
            }
        }

        return $result;
    }
}