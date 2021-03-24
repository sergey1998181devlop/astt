<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 07.01.2019
 * Time: 15:52
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Events\Iblocks;

use Bitrix\Main\Application;
use Democontent2\Pi\Iblock\IblockIdMeta;
use Democontent2\Pi\Iblock\IblockTypeMeta;
use Democontent2\Pi\Logger;

class Update
{
    public function handler($iblock)
    {
        preg_match_all('/democontent2_pi_([a-z_]+)/m', $iblock['IBLOCK_TYPE_ID'], $matches, PREG_SET_ORDER, 0);
        if (count($matches) > 0 && isset($matches[0][1])) {
            global $USER;

            $meta = new IblockIdMeta(intval($iblock['ID']));
            $meta->update($iblock['NAME']);

            $meta = new IblockTypeMeta($iblock['IBLOCK_TYPE_ID']);
            $meta->update();

            $taggedCache = Application::getInstance()->getTaggedCache();
            $taggedCache->clearByTag(md5(DSPI . 'menu'));

            if ($USER->IsAuthorized()) {
                Logger::add(
                    intval($USER->GetID()),
                    'iBlockUpdate',
                    [
                        'id' => intval($iblock['ID']),
                        'type' => $iblock['IBLOCK_TYPE_ID']
                    ]
                );
            }

            try {
                if (strlen($iblock['CODE']) > 0) {
                    $tableName = ToLower(preg_replace('/[^a-zA-Z]/', '', md5(intval($iblock['ID']))));
                    $connection = Application::getConnection();
                    $connection->query(
                        'UPDATE `' . $tableName . '` SET `UF_IBLOCK_CODE`="' . $iblock['CODE'] . '" 
                    WHERE `UF_IBLOCK_TYPE`="' . $matches[0][1] . '" AND `UF_IBLOCK_ID`="' . $iblock['ID'] . '" AND `UF_IBLOCK_CODE` != "' . $iblock['CODE'] . '"'
                    );

                    $connection->query(
                        'UPDATE `democontentpiall` SET `UF_IBLOCK_CODE`="' . $iblock['CODE'] . '" 
                    WHERE `UF_IBLOCK_TYPE`="' . $matches[0][1] . '" AND `UF_IBLOCK_ID`="' . $iblock['ID'] . '" AND `UF_IBLOCK_CODE` != "' . $iblock['CODE'] . '"'
                    );
                }
            } catch (\Exception $e) {
            }
        }
    }
}