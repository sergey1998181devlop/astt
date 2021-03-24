<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 07.01.2019
 * Time: 15:51
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Events\Iblocks;

use Democontent2\Pi\Utils;

class BeforeAdd
{
    public function handler(&$iblock)
    {
        preg_match_all('/democontent2_pi_([a-z_]+)/m', $iblock['IBLOCK_TYPE_ID'], $matches, PREG_SET_ORDER, 0);
        if (count($matches) > 0 && isset($matches[0][1])) {
            if (strlen(trim($iblock['NAME'])) > 0) {
                $iblock['LIST_PAGE_URL'] = '';
                $iblock['SECTION_PAGE_URL'] = '';
                $iblock['DETAIL_PAGE_URL'] = '#SITE_DIR##LOCATION_CITY#/#IBLOCK_TYPE_ID#/#IBLOCK_CODE#/#ELEMENT_CODE#/';
                $iblock['VERSION'] = 2;
                $iblock['GROUP_ID'][2] = 'R';

                if (strlen(trim($iblock['CODE'])) < 1) {
                    $iblock['CODE'] = ToLower(\CUtil::translit(trim($iblock['NAME']), 'ru', Utils::tr()));
                } else {
                    $iblock['CODE'] = str_replace('_', '-', ToLower(\CUtil::translit(trim($iblock['CODE']), 'ru', Utils::tr())));
                }

                if (strlen(trim($iblock['CODE'])) < 1) {
                    $iblock['CODE'] = ToLower(\CUtil::translit(trim($iblock['NAME']), 'ru', Utils::tr()));
                }

                if (!isset($iblock['IPROPERTY_TEMPLATES']['ELEMENT_META_TITLE']) || !strlen($iblock['IPROPERTY_TEMPLATES']['ELEMENT_META_TITLE'])) {
                    $iblock['IPROPERTY_TEMPLATES']['ELEMENT_META_TITLE'] = '{=this.Name}';
                }

                if (!isset($iblock['IPROPERTY_TEMPLATES']['ELEMENT_PAGE_TITLE']) || !strlen($iblock['IPROPERTY_TEMPLATES']['ELEMENT_PAGE_TITLE'])) {
                    $iblock['IPROPERTY_TEMPLATES']['ELEMENT_PAGE_TITLE'] = '{=this.Name}';
                }

                if (!isset($iblock['IPROPERTY_TEMPLATES']['SECTION_META_TITLE']) || !strlen($iblock['IPROPERTY_TEMPLATES']['SECTION_META_TITLE'])) {
                    $iblock['IPROPERTY_TEMPLATES']['SECTION_META_TITLE'] = '{=this.Name}';
                }

                if (!isset($iblock['IPROPERTY_TEMPLATES']['SECTION_PAGE_TITLE']) || !strlen($iblock['IPROPERTY_TEMPLATES']['SECTION_PAGE_TITLE'])) {
                    $iblock['IPROPERTY_TEMPLATES']['SECTION_PAGE_TITLE'] = '{=this.Name}';
                }
            }
        }
    }
}