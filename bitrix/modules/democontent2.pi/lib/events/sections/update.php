<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 07.01.2019
 * Time: 15:28
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Events\Sections;

use Bitrix\Main\Application;
use Democontent2\Pi\Logger;

class Update
{
    public function handler($section)
    {
        global $USER;

        $taggedCache = Application::getInstance()->getTaggedCache();
        $taggedCache->clearByTag(md5(DSPI . 'menu'));
        $taggedCache->clearByTag('iblock_id_' . $section['IBLOCK_ID']);

        if ($USER->IsAuthorized()) {
            Logger::add(
                intval($USER->GetID()),
                'sectionDelete',
                [
                    'id' => intval($section['ID']),
                    'iBlockId' => intval($section['IBLOCK_ID'])
                ]
            );
        }
    }
}