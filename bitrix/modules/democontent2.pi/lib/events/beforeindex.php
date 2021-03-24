<?php
/**
 * User: Sergey nazarkin
 * Email: nazarkin2017@mail.ru
 * Date: 08.01.2021
 * Time: 13:09
 */

namespace Democontent2\Pi\Events;

use Democontent2\Pi\Hl;
use Democontent2\Pi\Iblock\City;

class BeforeIndex
{
    public function handler($element)
    {
        $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($element['PARAM2']))));
        $hl = new Hl($tableName, 0);
        if ($hl->obj !== null) {
            $obj = $hl->obj;
            try {
                $get = $obj::getList(
                    [
                        'select' => [
                            'UF_ID',
                            'UF_MODERATION',
                            'UF_PAYED',
                            'UF_IBLOCK_TYPE',
                            'UF_IBLOCK_CODE',
                            'UF_IBLOCK_ID',
                            'UF_CITY',
                            'UF_ACTIVE_TO',
                            'UF_TIMESTAMP',
                            'UF_CODE',
                            'UF_STATUS',
                        ],
                        'filter' => [
                            '=UF_ID' => $element['ITEM_ID']
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    if (intval($res['UF_MODERATION']) > 0 || !intval($res['UF_PAYED'])
                        || (intval($res['UF_STATUS']) !== 1 && intval($res['UF_STATUS']) !== 3)) {
                        $element['TITLE'] = '';
                        $element['BODY'] = '';
                    } else {
                        $url = [];
                        $city = new City();
                        $cityParams = $city->getById(intval($res['UF_CITY']));
                        if (!$cityParams['default']) {
                            $url[] = 'city-' . $cityParams['code'];
                        }

                        $url[] = $res['UF_IBLOCK_TYPE'];
                        $url[] = $res['UF_IBLOCK_CODE'];
                        $url[] = $res['UF_CODE'];

                        $element['PARAM1'] = $cityParams['id'];
                    }
                }
            } catch (\Exception $e) {

            }
        }

        return $element;
    }
}