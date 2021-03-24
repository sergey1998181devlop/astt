<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 25.03.2019
 * Time: 00:39
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Events;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\IO\Path;

class Panel
{
    public static function handler()
    {
        try {
            if (defined('SITE_ID') && defined('SITE_DIR')) {
                if (SITE_ID == Option::get('democontent2.pi', 'siteId') && SITE_DIR == Option::get('democontent2.pi', 'siteDir')) {
                    global $APPLICATION;

                    if (!Directory::isDirectoryExists(
                        Path::normalize(Application::getDocumentRoot() . $APPLICATION->GetCurPage(false))
                    )) {
                        $arr = [
                            'create',
                            'create_section',
                            'edit',
                            'edit_section'
                        ];
                        foreach ($arr as $item) {
                            if (isset($APPLICATION->arPanelButtons[$item])) {
                                unset($APPLICATION->arPanelButtons[$item]);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
        }
    }
}