<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 08.01.2019
 * Time: 13:03
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
namespace Democontent2\Pi\Iblock;

use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Application;

class Section
{
    private $ttl = 0;

    /**
     * Section constructor.
     * @param int $ttl
     */
    public function __construct($ttl = 86400)
    {
        $this->ttl = intval($ttl);
    }

    public function getByCode($iblockId, $code, $depthLevel = 0)
    {
        $result = [];

        $cache = Application::getInstance()->getCache();
        $cache_time = $this->ttl;
        $cache_id = md5('path_' . $code . $depthLevel);
        $cache_path = '/' . DSPI . '/sections';

        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();

            if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                $result = $res[$cache_id];
            }
        } else {
            $taggedCache->startTagCache($cache_path);
            $taggedCache->registerTag('iblock_id_' . $iblockId);

            try {
                $sectionTable = new SectionTable();
                $get = $sectionTable::getList(
                    [
                        'select' => [
                            'ID',
                            'NAME',
                            'CODE',
                            'IBLOCK_SECTION_ID'
                        ],
                        'filter' => [
                            '=CODE' => $code,
                            '=IBLOCK_ID' => $iblockId,
                            '=DEPTH_LEVEL' => $depthLevel
                        ]
                    ]
                );

                while ($res = $get->fetch()) {
                    $result = $res;
                    break;
                }
            } catch (\Exception $e) {
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

    /**
     * @param $iblockId
     * @param $id
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    public function depthLevels($iblockId, $id)
    {
        $result = [];

        $cache = Application::getInstance()->getCache();
        $cache_time = $this->ttl;
        $cache_id = md5('path_' . $id);
        $cache_path = '/' . DSPI . '/sections';

        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();

            if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                $result = $res[$cache_id];
            }
        } else {
            $taggedCache->startTagCache($cache_path);
            $taggedCache->registerTag('iblock_id_' . $iblockId);

            $sectionTable = new SectionTable();
            $break = false;
            do {
                try {
                    $get = $sectionTable::getList(
                        [
                            'select' => [
                                'ID',
                                'NAME',
                                'CODE',
                                'IBLOCK_SECTION_ID'
                            ],
                            'filter' => [
                                '=ID' => $id,
                                '=IBLOCK_ID' => $iblockId
                            ]
                        ]
                    );

                    while ($res = $get->fetch()) {
                        if (!intval($res['IBLOCK_SECTION_ID'])) {
                            $break = true;
                        } else {
                            $id = intval($res['IBLOCK_SECTION_ID']);
                        }

                        unset($res['IBLOCK_SECTION_ID']);
                        $result[] = $res;
                        break;
                    }
                } catch (\Exception $e) {
                    $break = true;
                }
            } while (!$break);

            if (count($result) > 0) {
                asort($result);
                reset($result);
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

    public function getSections($iblockId, $depthLevel = 1, $parentId = 0)
    {
        $result = [];

        if (intval($iblockId) > 0 && intval($depthLevel) > 0) {
            $sectionTable = new SectionTable();
            $filter = [
                '=IBLOCK_ID' => intval($iblockId),
                '=ACTIVE' => 'Y',
                '=DEPTH_LEVEL' => intval($depthLevel)

            ];
            if (intval($parentId) > 0) {
                $filter['=IBLOCK_SECTION_ID'] = intval($parentId);
            }

            $get = $sectionTable::getList(
                [
                    'select' => [
                        'ID',
                        'NAME',
                        'CODE'
                    ],
                    'filter' => $filter,
                    'order' => [
                        'SORT' => 'ASC',
                        'NAME' => 'ASC'
                    ]
                ]
            );
            while ($res = $get->fetch()) {
                $result[] = $res;
            }
        }

        return $result;
    }
}