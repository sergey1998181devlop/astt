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

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Democontent2\Pi\Logger;
use Democontent2\PI\Utils;

class Delete
{
    public function handler($iblock)
    {
        global $USER;

        if (Loader::includeModule('highloadblock')) {
            $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($iblock))));
            try {
                $hlId = Utils::hlBlockId($tableName, 0);
                if ($hlId > 0) {
                    $taggedCache = Application::getInstance()->getTaggedCache();
                    $taggedCache->clearByTag(md5(DSPI . 'menu'));
                    $taggedCache->clearByTag('iblock_id_' . $iblock);

                    HighloadBlockTable::delete($hlId);

                    if ($USER->IsAuthorized()) {
                        Logger::add(
                            intval($USER->GetID()),
                            'iBlockDelete',
                            [
                                'id' => intval($iblock)
                            ]
                        );
                    }
                }
            } catch (\Exception $e) {
            }
        }
    }
}