<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 07.01.2019
 * Time: 15:49
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Events\Elements;

use Bitrix\Main\Application;
use Democontent2\Pi\Iblock\Item;
use Democontent2\Pi\Logger;
use Democontent2\Pi\Utils;

class Delete
{
    public function handler($element)
    {
        global $USER;

        preg_match_all('/democontent2_pi_([a-z_]+)/m', Utils::getIBlockType($element['IBLOCK_ID']), $matches, PREG_SET_ORDER, 0);
        if (count($matches) > 0 && isset($matches[0][1])) {
            $item = new Item();
            $item->delete(intval($element['ID']), intval($element['IBLOCK_ID']));

            if ($USER->IsAuthorized()) {
                Logger::add(
                    intval($USER->GetID()),
                    'deleteItem',
                    [
                        'id' => intval($element['ID']),
                        'iBlockId' => intval($element['IBLOCK_ID'])
                    ]
                );
            }

            $taggedCache = Application::getInstance()->getTaggedCache();
            $taggedCache->clearByTag('iblock_id_' . $element['IBLOCK_ID']);
        }
    }
}