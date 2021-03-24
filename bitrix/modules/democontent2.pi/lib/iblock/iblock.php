<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 08.01.2019
 * Time: 11:56
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Iblock;

use Bitrix\Main\Application;

class Iblock
{
    private $ttl = 0;

    /**
     * Iblock constructor.
     * @param int $ttl
     */
    public function __construct($ttl = 86400)
    {
        $this->ttl = intval($ttl);
    }

    public function getTypeName($type)
    {
        $result = '';

        $cache = Application::getInstance()->getCache();
        $cache_time = $this->ttl;
        $cache_id = md5('iblockType_' . $type);
        $cache_path = '/' . DSPI . '/iblocks';
        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();
            if ($res[$cache_id]) {
                $result = $res[$cache_id];
            }
        } else {
            $taggedCache->startTagCache($cache_path);
            $taggedCache->registerTag($cache_id);

            $getIBlock = \CIBlockType::GetList(
                [],
                [
                    '=ID' => $type
                ]
            );
            while ($ibl = $getIBlock->Fetch()) {
                if ($arIBType = \CIBlockType::GetByIDLang($ibl["ID"], LANG)) {
                    $result = $arIBType["NAME"];
                }
                break;
            }

            if ($cache_time > 0) {
                $cache->startDataCache($cache_time, $cache_id, $cache_path);
                if (!strlen($result)) {
                    $cache->abortDataCache();
                    $taggedCache->abortTagCache();
                }
                $cache->endDataCache([$cache_id => $result]);
                $taggedCache->endTagCache();
            }
        }

        return $result;
    }

    public function getIblockName($id)
    {
        $result = '';

        $cache = Application::getInstance()->getCache();
        $cache_time = $this->ttl;
        $cache_id = md5('iblockName_' . $id);
        $cache_path = '/' . DSPI . '/iblocks';
        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();
            if ($res[$cache_id]) {
                $result = $res[$cache_id];
            }
        } else {
            $taggedCache->startTagCache($cache_path);
            $taggedCache->registerTag('iblock_id_' . $id);

            $getIBlock = \CIBlock::GetList(
                [],
                [
                    'ID' => $id
                ],
                true
            );
            while ($ibl = $getIBlock->Fetch()) {
                $result = $ibl['NAME'];
                break;
            }

            if ($cache_time > 0) {
                $cache->startDataCache($cache_time, $cache_id, $cache_path);
                if (!strlen($result)) {
                    $cache->abortDataCache();
                    $taggedCache->abortTagCache();
                }
                $cache->endDataCache([$cache_id => $result]);
                $taggedCache->endTagCache();
            }
        }

        return $result;
    }

    public function getAllIds()
    {
        $result = [];

        $cache = Application::getInstance()->getCache();
        $cache_time = $this->ttl;
        $cache_id = md5('allIds');
        $cache_path = '/' . DSPI . '/iblocks';
        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();
            if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                $result = $res[$cache_id];
            }
        } else {
            $taggedCache->startTagCache($cache_path);

            $get = \CIBlock::GetList(
                [
                    'SORT' => 'ASC',
                    'NAME' => 'ASC'
                ],
                [
                    'TYPE' => 'democontent2_pi_%'
                ]
            );
            while ($res = $get->Fetch()) {
                $result[] = intval($res['ID']);
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

    public function getAllList($type = '')
    {
        $result = [];

        $type = str_replace('-', '_', $type);

        $cache = Application::getInstance()->getCache();
        $cache_time = $this->ttl;
        $cache_id = md5('list' . $type);
        $cache_path = '/' . DSPI . '/iblocks';
        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();
            if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                $result = $res[$cache_id];
            }
        } else {
            $taggedCache->startTagCache($cache_path);

            $get = \CIBlock::GetList(
                [
                    'SORT' => 'ASC',
                    'NAME' => 'ASC'
                ],
                [
                    'TYPE' => (strlen($type) > 0) ? 'democontent2_pi_' . $type : 'democontent2_pi_%'
                ]
            );
            while ($res = $get->Fetch()) {
                $result[$res['IBLOCK_TYPE_ID']][] = [
                    'id' => intval($res['ID']),
                    'code' => str_replace('_', '-', str_replace('democontent2_pi_', '', $res['CODE'])),
                    'name' => $res['NAME']
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

    public function getTypes()
    {
        $result = [];

        $cache = Application::getInstance()->getCache();
        $cache_time = $this->ttl;
        $cache_id = md5('types');
        $cache_path = '/' . DSPI . '/iblocks';
        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();
            if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                $result = $res[$cache_id];
            }
        } else {
            $taggedCache->startTagCache($cache_path);

            $iblocks = $this->getAllList();
            $get = \CIBlockType::GetList(
                [
                    'SORT' => 'ASC',
                    'NAME' => 'ASC'
                ],
                [
                    'LANGUAGE_ID' => Application::getInstance()->getContext()->getLanguage()
                ]
            );
            while ($res = $get->Fetch()) {
                preg_match_all('/democontent2_pi_([a-z_]+)/m', $res['ID'], $matches, PREG_SET_ORDER, 0);
                if (count($matches) > 0 && isset($matches[0][1])) {
                    $result[$res['ID']] = [
                        'name' => $res['NAME'],
                        'iblocks' => []
                    ];

                    if (isset($iblocks[$res['ID']])) {
                        $result[$res['ID']]['iblocks'] = $iblocks[$res['ID']];
                    } else {
                        unset($result[$res['ID']]);
                    }
                }
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