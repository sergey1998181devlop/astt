<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 14.01.2019
 * Time: 15:59
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi;

use Bitrix\Highloadblock\HighloadBlockLangTable;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Application;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Democontent2\Pi\Exceptions\NotificationsException;

class Company
{


    const TABLE_NAME = 'company';



    public static function addCompanyForModeration($userId = 0, $rows )
    {
//        $className = ToUpper(end(explode('\\', __CLASS__)));
            $userId = $userId;
            $hl = new Hl(static::TABLE_NAME, 0);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                $add = $obj::add($rows);
                if (!$add->isSuccess() && !$add->getId()) {
                    throw new NotificationsException('Insert Error');
                }
            } else {
                throw new NotificationsException('Object is NULL');
            }


    }









}