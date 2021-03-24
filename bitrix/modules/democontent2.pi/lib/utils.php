<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 07.01.2019
 * Time: 15:38
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\IO\File;
use Bitrix\Main\IO\Path;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\Text\Encoding;
use Democontent2\Pi\Iblock\Iblock;
use Democontent2\Pi\Iblock\Properties;

class Utils
{
    public static function userBgColor(string $name)
    {
        $_name = substr(trim($name), 0, 1);

        return '#' . substr(md5(ToUpper($_name)), 0, 6);
    }

    public static function userNamePrefix(string $name, $lastName = '')
    {
        $_name = substr(trim($name), 0, 1);
        if (strlen($lastName)) {
            $_name .= substr(trim($lastName), 0, 1);
        }

        return ToUpper($_name);
    }

    public static function mid()
    {
        return str_replace('.', '', DSPI);
    }

    public static function clearString($str)
    {
        $str = preg_replace('/[^' . Loc::getMessage('CYRILLIC_SYMBOLS_PATTERN') . ']/u', ' ', $str);
        $str = preg_replace('/(\s{2,})/m', ' ', $str);

        return trim($str);
    }

    public static function status($status)
    {
        switch ($status) {
            case '__status_2':
                $result = 2; //задание выполняется
                break;
            case '__status_3':
                $result = 3; //задание закрыто
                break;
            case '__status_4':
                $result = 4; //исполнитель выбран
                break;
            case '__status_5':
                $result = 5; //задание выполнено исполнителем
                break;
            case '__status_6':
                $result = 6; //открыта претензия
                break;
            default:
                $result = 1; //задание открыто
        }

        return $result;
    }

    public static function formatDate($date)
    {
        $date = strtotime($date);
        $delta = (time() - $date);
        if (date('d.m.Y', $date) == date('d.m.Y')) {
            return Loc::getMessage('TODAY_IN') . date('H:i', $date);
        } elseif (date('d.m.Y', $date) == date('d.m.Y', strtotime('-1 day'))) {
            return Loc::getMessage('YESTERDAY_IN') . date('H:i', $date);
        } elseif (date('d.m.Y', $date) == date('d.m.Y', strtotime('-2 day'))) {
            return Loc::getMessage('LAST_YESTERDAY_IN') . date('H:i', $date);
        } elseif (date('d.m.Y', $date) == date('d.m.Y', strtotime('-7 day'))) {
            return Loc::getMessage('WEEK_AGO');
        } else {
            if ($delta >= (86400 * 3) && $delta <= (86400 * 7)) {
                return Loc::getMessage('FEW_DAYS_AGO');
            } else {
                return date('d.m.Y H:i', $date);
            }
        }
    }

    public static function stringToFloat($float)
    {
        $result = 0;
        $float = str_replace(' ', '', $float);
        $float = str_replace(',', '.', $float);
        $float = preg_replace('/(\.{2,})/m', '.', $float);
        $float = preg_replace('/([^\d\.])/m', '', $float);

        $temp = [];
        $ex = explode('.', $float);
        foreach ($ex as $e) {
            if (strlen($e) > 0) {
                $i = 0;
                while ($i++ < strlen($e)) {
                    $first = substr($e, ($i - 1), 1);
                    if (count($temp) > 0) {
                        $temp[] = substr($e, ($i - 1), (strlen($e) - ($i - 1)));
                        break;
                    } else {
                        if (intval($first) > 0) {
                            $temp[] = substr($e, ($i - 1), (strlen($e) - ($i - 1)));
                            break;
                        }
                    }
                }
            }

            if (count($temp) == 2) {
                break;
            }
        }

        if (count($temp) > 0) {
            if (floatval(round(implode('.', $temp), 2)) > 0) {
                $result = floatval(round(implode('.', $temp), 2));
            }
        }

        return $result;
    }

    public static function price($str)
    {
        return number_format($str, 0, '.', ' ');
    }

    public static function getIBlockIdByType($type, $code)
    {
        $iBlockId = 0;

        if ($type && $code) {
            $getIBlock = \CIBlock::GetList(
                [],
                [
                    'TYPE' => $type,
                    'ACTIVE' => 'Y',
                    'CODE' => $code
                ],
                true
            );
            while ($ibl = $getIBlock->Fetch()) {
                $iBlockId = $ibl['ID'];
                break;
            }
        }

        return $iBlockId;
    }

    public static function getIBlockType($id)
    {
        $result = '';

        $cache = Application::getInstance()->getCache();
        $cache_time = 86400;
        $cache_id = md5('iblock_' . $id);
        $cache_path = '/' . DSPI . '/iblocks';

        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();
            if ($res[$cache_id]) {
                $result = $res[$cache_id];
            }
        } else {
            $taggedCache->startTagCache($cache_path);
            $taggedCache->registerTag($cache_id);
            $taggedCache->registerTag('iblock_id_' . $id);

            $getIBlock = \CIBlock::GetList(
                [],
                [
                    'ID' => $id
                ],
                true
            );
            while ($ibl = $getIBlock->Fetch()) {
                $result = $ibl['IBLOCK_TYPE_ID'];
                break;
            }

            if ($cache_time > 0) {
                $cache->startDataCache($cache_time, $cache_id, $cache_path);
                if (!strlen($result)) {
                    $cache->abortDataCache();
                    $taggedCache->abortTagCache();
                }
                $cache->endDataCache([$cache_id => $result]);
                $taggedCache->endTagCache();
            }
        }

        return $result;
    }

    public static function getIBlockCode($id)
    {
        $iBlockCode = '';

        $getIBlock = \CIBlock::GetList(
            [],
            [
                'ID' => $id
            ],
            true
        );
        while ($ibl = $getIBlock->Fetch()) {
            $iBlockCode = $ibl['CODE'];
            break;
        }

        return $iBlockCode;
    }

    public static function hlBlockId($type, $ttl = 86400)
    {
        $result = 0;

        $cache = Application::getInstance()->getCache();
        $cache_time = $ttl;
        $cache_id = md5('hlBlockId_' . $type);
        $cache_path = '/' . DSPI . '/hl';

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();

            if (intval($res[$cache_id])) {
                $result = intval($res[$cache_id]);
            }
        } else {
            try {
                $connection = Application::getConnection();
                $hlBlockId = $connection->query(
                    'SELECT `ID` FROM `' . HighloadBlockTable::getTableName() . '` WHERE `NAME`="' . $type . '"'
                )->fetch();

                if (intval($hlBlockId['ID'])) {
                    $result = intval($hlBlockId['ID']);
                }
            } catch (\Exception $e) {

            }

            if ($cache_time > 0) {
                $cache->startDataCache($cache_time, $cache_id, $cache_path);
                if (!intval($result)) {
                    $cache->abortDataCache();
                }

                $cache->endDataCache(
                    [
                        $cache_id => intval($result)
                    ]
                );
            }
        }

        return $result;
    }

    public static function tr()
    {
        return [
            'max_len' => 150,
            'change_case' => false,
            'replace_space' => '-',
            'replace_other' => '-',
            'delete_repeat_replace' => true
        ];
    }

    public static function validatePhone($str)
    {
        $str = preg_replace('/[^\d]/', '', $str);
        if (strlen($str) !== 11) {
            $str = '';
        }

        return $str;
    }

    public static function validateCode($str)
    {
        $str = preg_replace('/[^\d]/', '', $str);
        if (strlen($str) !== 4) {
            $str = '';
        }

        return $str;
    }

    public static function checkPhoneFormat($str)
    {
        $str = '+' . trim(str_replace('+', '', $str));
        $re = '/(\+' . ((intval(Option::get(DSPI, 'phone_code_mask'))) ? Option::get(DSPI, 'phone_code_mask') : 7) . '\(([0-9]{3})\)([0-9]{3})-([0-9]{2})-([0-9]{2}))/m';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

        if (!count($matches))
            return false;

        return true;
    }

    public static function convert($str)
    {
        if (Application::getInstance()->isUtfMode()) {
            return $str;
        } else {
            return Encoding::convertEncodingToCurrent($str);
        }
    }

    public static function checkImage($fileName)
    {
        $ex = explode('.', $fileName);
        $extension = '';
        switch (ToUpper(end($ex))) {
            case 'JPG':
            case 'JPEG':
                $extension = '.jpg';
                break;
            case 'PNG':
                $extension = '.png';
                break;
        }

        return $extension;
    }

    /**
     * @param $bytes
     * @return string
     */
    public static function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    public static function shortString($string)
    {
        if (strlen($string) >= 20) {
            return substr($string, 0, 10) . '...' . substr($string, -8);
        } else {
            return $string;
        }
    }

    public static function getExtension($fileName)
    {
        $ex = explode('.', $fileName);
        $extension = '';
        switch (ToUpper(end($ex))) {
            case 'XLS':
                $extension = '.xls';
                break;
            case 'XLSX':
                $extension = '.xlsx';
                break;
            case 'DOC':
                $extension = '.doc';
                break;
            case 'DOCX':
                $extension = '.docx';
                break;
            case 'PDF':
                $extension = '.pdf';
                break;
            case 'JPG':
            case 'JPEG':
                $extension = '.jpg';
                break;
            case 'PNG':
                $extension = '.png';
                break;
        }

        return $extension;
    }

    public static function getExtensionByMime($mime)
    {
        $extension = '';
        switch ($mime) {
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                $extension = '.xls';
                break;
            case 'application/msword':
                $extension = '.doc';
                break;
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                $extension = '.docx';
                break;
            case 'application/pdf':
                $extension = '.pdf';
                break;
            case 'image/jpeg':
                $extension = '.jpg';
                break;
            case 'image/png':
                $extension = '.png';
                break;
        }

        return $extension;
    }

    public static function createTempImageFromBase64API($item)
    {
        $result = '';

        $_tempFilePath = Application::getInstance()->getContext()->getServer()->getDocumentRoot()
            . '/upload/tmp/' . md5(microtime(true) . randString()) . '.jpg';

        if (File::putFileContents($_tempFilePath, base64_decode($item))) {
            $result = $_tempFilePath;
        }

        return $result;
    }

    /**
     * @param $item
     * @param string $folder
     * @return string|string[]|null
     * @throws SystemException
     * @throws \Bitrix\Main\IO\InvalidPathException
     */
    public static function createTempImageFromBase64($item, $folder = 'tmp')
    {
        $result = '';

        if ($folder !== 'tmp') {
            Directory::createDirectory(
                Path::normalize(Application::getInstance()->getContext()->getServer()->getDocumentRoot() . '/upload/' . $folder)
            );
        }

        $ex = explode(';', $item);
        switch ($ex[0]) {
            case 'data:image/jpeg':
                $_ex = explode('base64,', $ex[1]);
                if (count($_ex) == 2) {
                    $_tempFilePath = Path::normalize(
                        Application::getInstance()->getContext()->getServer()->getDocumentRoot()
                        . '/upload/' . $folder . '/' . md5(microtime(true) . randString()) . '.jpg'
                    );

                    if (File::putFileContents($_tempFilePath, base64_decode($_ex[1]))) {
                        $result = $_tempFilePath;
                    }
                }
                break;
            case 'data:image/png':
                $_ex = explode('base64,', $ex[1]);
                if (count($_ex) == 2) {
                    $_tempFilePath = Path::normalize(
                        Application::getInstance()->getContext()->getServer()->getDocumentRoot()
                        . '/upload/' . $folder . '/' . md5(microtime(true) . randString()) . '.png'
                    );

                    if (File::putFileContents($_tempFilePath, base64_decode($_ex[1]))) {
                        $result = $_tempFilePath;
                    }
                }
                break;
        }

        return $result;
    }

    public static function declension($num, $words)
    {
        $num = intval($num);
        $num = $num % 100;
        if ($num > 19) {
            //$num = $num % 10;
        }

        switch ($num) {
            case 0:
                return Loc::getMessage('NO') . ' ' . $words[2];
            case 1:
                return $num . ' ' . $words[0];
            case 2:
            case 3:
            case 4:
                return $num . ' ' . $words[1];
            default:
                return $num . ' ' . $words[2];
        }
    }

    public static function hideDigits($str)
    {
        $str = (string)$str;
        $digits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        foreach ($digits as $d) {
            $str = str_replace($d, 'X', $str);
        }
        return $str;
    }

    public static function formatPhone($str)
    {
        $result = '';
        $str = Utils::validatePhone($str);
        if ($str) {
            $re = '/([' . ((intval(Option::get(DSPI, 'phone_code_mask'))) ? Option::get(DSPI, 'phone_code_mask') : 7) . ']+)([\d]{3}+)([\d]{3}+)([\d]{2}+)([\d]{2}+)/m';
            preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

            if (isset($matches[0]) && count($matches[0]) == 6) {
                $result = '+' . $matches[0][1] . ' (' . $matches[0][2] . ') ' . $matches[0][3] . '-' . $matches[0][4] . '-' . $matches[0][5];
            }
        }

        return $result;
    }

    public static function smsTranslitRules()
    {
        return [
            'max_len' => 150,
            'change_case' => false,
            'replace_space' => ' ',
            'replace_other' => ' ',
            'delete_repeat_replace' => true,
            'safe_chars' => '.,:!?-_#№@'
        ];
    }

    public static function getCurrencyCode()
    {
        switch (intval(Option::get(DSPI, 'currency'))) {
            case 0:
                $result = 'RUB';
                break;
            case 1:
                $result = 'UAH';
                break;
            case 2:
                $result = 'KZT';
                break;
            case 3:
                $result = 'BYN';
                break;
            case '4':
                $result = 'USD';
                break;
            case 5:
                $result = 'EUR';
                break;
            default:
                $result = 'RUB';
        }

        return $result;
    }

    public static function getSiteUrl($id)
    {
        $result = '';

        try {
            $cache = Application::getInstance()->getCache();
            $cache_time = (86400 * 300);
            $cache_id = md5('siteUrl_' . $id);
            $cache_path = '/' . DSPI . '/site';

            if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
                $res = $cache->getVars();

                $result = $res[$cache_id];
            } else {
                $getSite = \CSite::GetByID($id)->Fetch();
                if (isset($getSite['SERVER_NAME'])) {
                    if ($getSite['SERVER_NAME']) {
                        $result = $getSite['SERVER_NAME'];
                    }
                }

                if ($cache_time > 0) {
                    $cache->startDataCache($cache_time, $cache_id, $cache_path);
                    if (!$result) {
                        $cache->abortDataCache();
                    }

                    $cache->endDataCache([$cache_id => $result]);
                }
            }
        } catch (SystemException $e) {
        }

        return $result;
    }

    public static function replaceMacros($filePath, $arReplace, $skipSharp = false)
    {
        clearstatcache();

        if (!is_file($filePath) || !is_writable($filePath) || !is_array($arReplace))
            return;

        @chmod($filePath, BX_FILE_PERMISSIONS);

        if (!$handle = @fopen($filePath, "rb"))
            return;

        $content = @fread($handle, filesize($filePath));
        @fclose($handle);

        if (!($handle = @fopen($filePath, "wb")))
            return;

        if (flock($handle, LOCK_EX)) {
            $arSearch = [];
            $arValue = [];

            foreach ($arReplace as $search => $replace) {
                if ($skipSharp)
                    $arSearch[] = $search;
                else
                    $arSearch[] = "#" . $search . "#";

                $arValue[] = $replace;
            }

            $content = str_replace($arSearch, $arValue, $content);
            @fwrite($handle, $content);
            @flock($handle, LOCK_UN);
        }
        @fclose($handle);
    }

    /**
     * @return string
     */
    public static function __mongoDbConnectString()
    {
        try {
            $login = getenv('PI_MONGODB_ADMIN_LOGIN') ? getenv('PI_MONGODB_LOGIN') : Option::get(DSPI, 'mongoAdminLogin');
            $password = getenv('PI_MONGODB_ADMIN_PASSWORD') ? getenv('PI_MONGODB_ADMIN_PASSWORD') : Option::get(DSPI, 'mongoAdminPassword');
            $host = getenv('PI_MONGODB_HOST') ? getenv('PI_MONGODB_HOST') : Option::get(DSPI, 'mongoHost');
            $port = getenv('PI_MONGODB_PORT') ? getenv('PI_MONGODB_PORT') : Option::get(DSPI, 'mongoPort');
            $atlas = getenv('PI_MONGODB_ATLAS') ? getenv('PI_MONGODB_ATLAS') : Option::get(DSPI, 'mongoAtlasAddress');

            if (strlen($atlas)) {
                return 'mongodb+srv://' . $login . ':' . $password . '@' . $atlas . '/' . Utils::mid() . '?retryWrites=true&w=majority';
            } else {
                if (strlen($host) && strlen($port)) {
                    return 'mongodb://' . $login . ':' . $password . '@' . $host . ':' . $port . '/' . Utils::mid();
                }
            }
        } catch (ArgumentNullException $e) {
        } catch (ArgumentOutOfRangeException $e) {
        }

        return '';
    }

    public static function checkCourierIblock($iblockId)
    {
        if (intval($iblockId)) {
            $ibl = new Iblock();
            $getName = $ibl->getIblockName(intval($iblockId));

            if (strlen($getName)) {
                try {
                    $properties = new Properties(intval($iblockId));
                    $properties->setTtl(0);
                    $iblockProperties = $properties->hidden();

                    if (!isset($iblockProperties['__hidden_coordinates'])) {
                        $p = new \CIBlockProperty();
                        $p->Add([
                            "IBLOCK_ID" => intval($iblockId),
                            "NAME" => Loc::getMessage('COORDINATES'),
                            "CODE" => "__hidden_coordinates",
                            "PROPERTY_TYPE" => "S",
                            "SORT" => 500,
                            "USER_TYPE" => "map_yandex",
                            "MULTIPLE" => "Y"
                        ]);
                    }
                } catch (\Exception $e) {
                }
            }
        }
    }

    /**
     * @param $userId
     * @return string
     */
    public static function getChatId($userId)
    {
        return md5(Sign::getInstance()->get() . md5(intval($userId)));
    }
}