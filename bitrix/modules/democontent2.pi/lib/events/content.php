<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 25.03.2019
 * Time: 08:44
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Events;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\IO\Path;

class Content
{
    public function handler(&$content)
    {
        global $APPLICATION;
        global $USER;

        if (strpos($APPLICATION->GetCurDir(), "/bitrix/") !== false) return;
        if ($APPLICATION->GetProperty("save_kernel") == "Y") return;

        if ($USER->IsAuthorized()) {
            if (defined('SITE_ID') && defined('SITE_DIR')) {
                if (SITE_ID == Option::get('democontent2.pi', 'siteId') && SITE_DIR == Option::get('democontent2.pi', 'siteDir')) {
                    $arPatternsToRemove = [
                        '/<link.+?href=".+?kernel_main\/kernel_main_v1\.css\?\d+"[^>]+>/',
                        '/<span class="bx-panel-small-button-icon bx-panel-seo-icon"><\/span>/m',
                    ];

                    try {
                        if (!Directory::isDirectoryExists(
                            Path::normalize(Application::getDocumentRoot() . $APPLICATION->GetCurPage(false))
                        )) {
                            $arPatternsToRemove[] = '/<span class="bx-panel-small-button-text">SEO<\/span>/m';
                        }
                    } catch (\Bitrix\Main\IO\InvalidPathException $e) {
                    }

                    $content = preg_replace($arPatternsToRemove, "", $content);
                }
            }
        }
    }
}