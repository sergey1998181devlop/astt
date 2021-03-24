<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 08.01.2019
 * Time: 18:41
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Iblock;

use Bitrix\Main\Application;

class Menu
{
    private $ttl = 0;

    /**
     * Menu constructor.
     * @param int $ttl
     */
    public function __construct($ttl = 86400)
    {
        $this->ttl = intval($ttl);
    }
    public function getCountEd(){

//print_r($arrayID);
        /*Формируем массив */
        $arSelect = Array("ID", "IBLOCK_ID", "NAME", "PROPERTY_*");
        $arFilter = Array("IBLOCK_ID"=>103, "SECTION_ID"=> 41);
        $res = \CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);
        $arCountMoneyZa = [];
        while($ob = $res->GetNextElement()){
            $arProps = $ob->GetProperties();
            $arFields = $ob->GetFields();

            $arCountMoneyZa[] = $arFields;

        }


     return $arCountMoneyZa;

    }
    public function get()
    {
        $result = [];

        $cache = Application::getInstance()->getCache();
        $cache_time = $this->ttl;
        $cache_id = md5('menu');
        $cache_path = '/' . DSPI . '/menu';

        $taggedCache = Application::getInstance()->getTaggedCache();




        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();

            if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                $result = $res[$cache_id];
            }


        } else {
            $taggedCache->startTagCache($cache_path);
            $taggedCache->registerTag($cache_id);

            $re = '/democontent2_pi_([a-z_]+)/m';

            $types = \CIBlockType::GetList(
                [
                    'SORT' => 'ASC'
                ]
            );
            while ($typesRes = $types->Fetch()) {
                preg_match_all($re, $typesRes['ID'], $matches, PREG_SET_ORDER, 0);
                if (count($matches) > 0 && isset($matches[0][1])) {
                    $code = str_replace('_', '-', $matches[0][1]);
                    $result[$code] = [];

                    if ($arIBType = \CIBlockType::GetByIDLang($typesRes['ID'], 'ru')) {
                        $result[$code]['name'] = $arIBType["NAME"];
                        $result[$code]['code'] = $code;
                    }

                    if (!count($result[$code])) {
                        unset($result[$code]);
                    } else {
                        $iblocks = \CIBlock::GetList(
                            [
                                'SORT' => 'ASC',
                                'NAME' => 'ASC'
                            ],
                            [
                                'TYPE' => $typesRes['ID'],
                                'ACTIVE' => 'Y'
                            ],
                            false
                        );


                        while ($iblocksRes = $iblocks->Fetch()) {
                            $result[$code]['items'][$iblocksRes['ID']] = [
                                'id' => intval($iblocksRes['ID']),
                                'name' => $iblocksRes['NAME'],
                                'code' => $iblocksRes['CODE'],
                            ];
                        }
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