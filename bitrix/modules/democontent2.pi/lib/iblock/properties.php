<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 08.01.2019
 * Time: 12:16
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Iblock;

use Bitrix\Main\Application;

class Properties
{
    protected $ttl = 86400;
    protected $iBlockId = 0;

    /**
     * Properties constructor.
     * @param int $iBlockId
     */
    public function __construct($iBlockId)
    {
        $this->iBlockId = $iBlockId;
    }

    /**
     * @param int $ttl
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;
    }

    /**
     * @param int $iBlockId
     */
    public function setIBlockId(int $iBlockId): void
    {
        $this->iBlockId = intval($iBlockId);
    }

    /**
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    public function all()
    {
        $result = [];

        $cache = Application::getInstance()->getCache();
        $cache_time = $this->ttl;
        $cache_id = md5('all_' . $this->iBlockId);
        $cache_path = '/' . DSPI . '/properties';

        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();

            if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                $result = $res[$cache_id];
            }
        } else {
            $taggedCache->startTagCache($cache_path);
            $taggedCache->registerTag('iblock_id_' . $this->iBlockId);

            $get = \CIBlockProperty::GetList(
                [
                    'SORT' => 'ASC',
                    'NAME' => 'ASC'
                ],
                [
                    'IBLOCK_ID' => $this->iBlockId,
                    'VERSION' => 2
                ]
            );
            while ($res = $get->Fetch()) {
                preg_match_all('/__hidden_([a-zA-Z_]+)/m', $res['CODE'], $matches, PREG_SET_ORDER, 0);
                if (!count($matches)) {
                    switch ($res['PROPERTY_TYPE']) {
                        case 'S':
                            $result[$res['ID']] = [
                                'type' => 'string',
                                'id' => intval($res['ID']),
                                'name' => $res['NAME'],
                                'code' => $res['CODE'],
                                'isRequired' => ($res['IS_REQUIRED'] == 'Y') ? 1 : 0
                            ];
                            break;
                        case 'N':
                            $result[$res['ID']] = [
                                'type' => 'integer',
                                'id' => intval($res['ID']),
                                'name' => $res['NAME'],
                                'code' => $res['CODE'],
                                'isRequired' => ($res['IS_REQUIRED'] == 'Y') ? 1 : 0,
                            ];
                            break;
                        case 'L':
                            $result[$res['ID']] = [
                                'type' => 'list',
                                'id' => intval($res['ID']),
                                'name' => $res['NAME'],
                                'code' => $res['CODE'],
                                'isRequired' => ($res['IS_REQUIRED'] == 'Y') ? 1 : 0,
                                'values' => []
                            ];

                            $values = \CIBlockProperty::GetPropertyEnum(
                                $res['ID'],
                                [
                                    'SORT' => 'ASC'
                                ],
                                [
                                    'IBLOCK_ID' => $this->iBlockId
                                ]
                            );
                            while ($valuesResult = $values->Fetch()) {
                                $result[$res['ID']]['values'][intval($valuesResult['ID'])] = [
                                    'id' => intval($valuesResult['ID']),
                                    'name' => $valuesResult['VALUE'],
                                    'isDefault' => ($valuesResult['DEF'] == 'Y') ? 1 : 0,
                                    'xmlId' => $valuesResult['XML_ID']
                                ];
                            }
                            break;
                        case 'E':
                            if (intval($res['LINK_IBLOCK_ID']) > 0) {
                                $result[$res['ID']] = [
                                    'type' => 'list',
                                    'id' => intval($res['ID']),
                                    'name' => $res['NAME'],
                                    'code' => $res['CODE'],
                                    'isRequired' => ($res['IS_REQUIRED'] == 'Y') ? 1 : 0,
                                    'values' => []
                                ];

                                $getLinkedElements = \CIBlockElement::GetList(
                                    [
                                        'SORT' => 'ASC'
                                    ],
                                    [
                                        '=IBLOCK_ID' => intval($res['LINK_IBLOCK_ID']),
                                        '=ACTIVE' => 'Y'
                                    ],
                                    false,
                                    false,
                                    [
                                        'ID',
                                        'IBLOCK_ID',
                                        'NAME',
                                        'XML_ID'
                                    ]
                                );
                                while ($resLinkedElements = $getLinkedElements->GetNextElement()) {
                                    $fields = $resLinkedElements->GetFields();
                                    $result[$res['ID']]['values'][intval($fields['ID'])] = [
                                        'id' => intval($fields['ID']),
                                        'name' => $fields['NAME'],
                                        'isDefault' => 0,
                                        'xmlId' => $fields['XML_ID']
                                    ];
                                }
                            }
                            break;
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

    /**
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    public function filterable()
    {
        $result = [];

        $cache = Application::getInstance()->getCache();
        $cache_time = $this->ttl;
        $cache_id = md5('filterable_' . $this->iBlockId);
        $cache_path = '/' . DSPI . '/properties';

        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();

            if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                $result = $res[$cache_id];
            }
        } else {
            $taggedCache->startTagCache($cache_path);
            $taggedCache->registerTag('iblock_id_' . $this->iBlockId);

            $connection = Application::getConnection();
            $get = \CIBlockProperty::GetList(
                [
                    'SORT' => 'ASC',
                    'NAME' => 'ASC'
                ],
                [
                    'IBLOCK_ID' => $this->iBlockId,
                    'FILTRABLE' => 'Y',
                    'VERSION' => 2
                ]
            );
            while ($res = $get->Fetch()) {
                preg_match_all('/__hidden_([a-zA-Z_]+)/m', $res['CODE'], $matches, PREG_SET_ORDER, 0);
                if (!count($matches) || $res['CODE'] == '__hidden_price') {
                    switch ($res['PROPERTY_TYPE']) {
                        case 'S':
                            $result[$res['ID']] = [
                                'type' => 'string',
                                'id' => intval($res['ID']),
                                'name' => $res['NAME'],
                                'code' => $res['CODE'],
                                'required' => ($res['IS_REQUIRED'] == 'Y') ? 1 : 0
                            ];
                            break;
                        case 'N':
                            $result[$res['ID']] = [
                                'type' => 'integer',
                                'id' => intval($res['ID']),
                                'name' => $res['NAME'],
                                'code' => $res['CODE'],
                                'required' => ($res['IS_REQUIRED'] == 'Y') ? 1 : 0,
                                'min' => 0,
                                'max' => 0
                            ];

                            try {
                                $query = $connection->query(
                                    'SELECT `PROPERTY_' . $res['ID'] . '` 
                                        FROM `b_iblock_element_prop_s' . $this->iBlockId . '` 
                                        ORDER BY `PROPERTY_' . $res['ID'] . '` ASC 
                                        LIMIT 1'
                                );
                                while ($queryResult = $query->fetch()) {
                                    $result[$res['ID']]['min'] = round($queryResult['PROPERTY_' . $res['ID']], 0);
                                }

                                $query = $connection->query(
                                    'SELECT `PROPERTY_' . $res['ID'] . '` 
                                        FROM `b_iblock_element_prop_s' . $this->iBlockId . '` 
                                        ORDER BY `PROPERTY_' . $res['ID'] . '` DESC 
                                        LIMIT 1'
                                );
                                while ($queryResult = $query->fetch()) {
                                    $result[$res['ID']]['max'] = round($queryResult['PROPERTY_' . $res['ID']], 0);
                                }
                            } catch (\Exception $e) {
                            }
                            break;
                        case 'L':
                            $result[$res['ID']] = [
                                'type' => 'list',
                                'id' => intval($res['ID']),
                                'name' => $res['NAME'],
                                'code' => $res['CODE'],
                                'isRequired' => ($res['IS_REQUIRED'] == 'Y') ? 1 : 0,
                                'values' => []
                            ];

                            $values = \CIBlockProperty::GetPropertyEnum(
                                $res['ID'],
                                [
                                    'SORT' => 'ASC'
                                ],
                                [
                                    'IBLOCK_ID' => $this->iBlockId
                                ]
                            );
                            while ($valuesResult = $values->Fetch()) {
                                $result[$res['ID']]['values'][] = [
                                    'id' => intval($valuesResult['ID']),
                                    'name' => $valuesResult['VALUE'],
                                    'isDefault' => ($valuesResult['DEF'] == 'Y') ? 1 : 0,
                                    'xmlId' => $valuesResult['XML_ID']
                                ];
                            }
                            break;
                        case 'E':
                            break;
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

    public function hidden()
    {
        $result = [];

        $cache = Application::getInstance()->getCache();
        $cache_time = $this->ttl;
        $cache_id = md5('hidden_' . $this->iBlockId);
        $cache_path = '/' . DSPI . '/properties';

        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();

            if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                $result = $res[$cache_id];
            }
        } else {
            $taggedCache->startTagCache($cache_path);
            $taggedCache->registerTag('iblock_id_' . $this->iBlockId);

            $get = \CIBlockProperty::GetList(
                [
                    'SORT' => 'ASC',
                    'NAME' => 'ASC'
                ],
                [
                    'IBLOCK_ID' => $this->iBlockId,
                    'VERSION' => 2
                ]
            );
            while ($res = $get->Fetch()) {
                preg_match_all('/__hidden_([a-zA-Z_]+)/m', $res['CODE'], $matches, PREG_SET_ORDER, 0);
                if (count($matches) > 0) {
                    switch ($res['PROPERTY_TYPE']) {
                        case 'S':
                            $result[$res['CODE']] = [
                                'type' => 'string',
                                'id' => intval($res['ID']),
                                'name' => $res['NAME'],
                                'code' => $res['CODE'],
                                'required' => ($res['IS_REQUIRED'] == 'Y') ? 1 : 0
                            ];
                            break;
                        case 'N':
                            $result[$res['CODE']] = [
                                'type' => 'integer',
                                'id' => intval($res['ID']),
                                'name' => $res['NAME'],
                                'code' => $res['CODE'],
                                'required' => ($res['IS_REQUIRED'] == 'Y') ? 1 : 0,
                                'min' => 0,
                                'max' => 0
                            ];
                            break;
                        case 'L':
                        case 'E':
                            $result[$res['CODE']] = [
                                'type' => 'list',
                                'id' => intval($res['ID']),
                                'name' => $res['NAME'],
                                'code' => $res['CODE'],
                                'required' => ($res['IS_REQUIRED'] == 'Y') ? 1 : 0
                            ];
                            break;
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