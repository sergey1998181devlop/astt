<?php
/**
 * User: Sergey nazarkin
 * Email: nazarkin2017@mail.ru
 * Date: 08.01.2021
 * Time: 13:09
 */

namespace Democontent2\Pi;

use Bitrix\Main\Application;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Democontent2\Pi\Exceptions\NotificationsException;

class Notifications
{
    const TABLE_NAME = 'Democontentpinotifications';

    public static function getLast($userId, $qty = 500, $unRead = false)
    {
        $result = [];

        try {
            $hl = new Hl(static::TABLE_NAME, 0);
            if ($hl->obj !== null) {
                $obj = $hl->obj;

                $filter = [
                    '=UF_USER_ID' => $userId
                ];
                if ($unRead) {
                    $filter['=UF_READ'] = 0;
                }

                $get = $obj::getList(
                    [
                        'select' => [
                            'ID',
                            'UF_CREATED_AT',
                            'UF_TYPE',
                            'UF_DATA'
                        ],
                        'filter' => $filter,
                        'order' => ['ID' => 'DESC'],
                        'limit' => $qty
                    ]
                );

                while ($res = $get->fetch()) {
                    $result[] = $res;
                }
            }
        } catch (\Exception $e) {

        }

        return $result;
    }

    public static function getLastEdit($userId, $qty = 500, $unRead = false)
    {
        $result = [];

        try {
            $hl = new Hl(static::TABLE_NAME, 0);
            if ($hl->obj !== null) {
                $obj = $hl->obj;

                $filter = [
                    '=UF_USER_ID' => $userId
                ];
                if ($unRead) {
                    $filter['=UF_READ'] = 0;
                }

                $get = $obj::getList(
                    [
                        'select' => [
                            'ID',
                            'UF_CREATED_AT',
                            'UF_TYPE',
                            'UF_DATA'
                        ],
                        'filter' => $filter,
                        'order' => ['ID' => 'DESC'],
                        'limit' => $qty
                    ]
                );

                while ($res = $get->fetch()) {
                    $result[] = $res;
                }
            }
        } catch (\Exception $e) {

        }

        return $result;
    }

    public static function getUnreadCount($userId)
    {
        $result = 0;

        try {
            $hl = new Hl(static::TABLE_NAME, 0);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                $get = $obj::getList(
                    [
                        'select' => ['CNT'],
                        'runtime' => [
                            new ExpressionField('CNT', 'COUNT(`ID`)')
                        ],
                        'filter' => [
                            '=UF_USER_ID' => $userId,
                            '=UF_READ' => 0
                        ]
                    ]
                );
                while ($res = $get->fetch()) {
                    $result = intval($res['CNT']);
                }
            }
        } catch (\Exception $e) {

        }

        return $result;
    }

    public static function getOneCurrentItem($userId , $idEl)
    {
        $result = 0;

        try {
            $hl = new Hl(static::TABLE_NAME, 0);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                $get = $obj::getList(
                    [
                        'select' => ['CNT'],
                        'runtime' => [
                            new ExpressionField('CNT', 'COUNT(`ID`)')
                        ],
                        'filter' => [
                            '=UF_USER_ID' => $userId,
                            '=UF_READ' => 0,
                            ''
                        ]
                    ]
                );
                while ($res = $get->fetch()) {
                    $result = intval($res['CNT']);
                }
            }
        } catch (\Exception $e) {

        }

        return $result;
    }

    public static function readAll($userId)
    {
        try {
            $hl = new Hl(static::TABLE_NAME, 0);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                $get = $obj::getList(
                    [
                        'select' => ['ID'],
                        'filter' => [
                            '=UF_USER_ID' => $userId,
                            '=UF_READ' => 0,

                        ]
                    ]
                );
                while ($res = $get->fetch()) {
                    $obj::update($res['ID'], ['UF_READ' => 1]);

                }
            }
        } catch (\Exception $e) {

        }
    }
    public static function update($userId)
    {
        try {
            $hl = new Hl(static::TABLE_NAME, 0);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                $get = $obj::getList(
                    [
                        'select' => ['ID'],
                        'filter' => [
                            '=UF_USER_ID' => $userId,
                            '=UF_READ' => 0
                        ]
                    ]
                );
                while ($res = $get->fetch()) {
                    $obj::update($res['ID']);
                }
            }
        } catch (\Exception $e) {

        }
    }
    public static function deleteForAdmin($itemId){
        $sql = "DELETE FROM  `sitemanager`.`democontentpinotifications` WHERE UF_ITEM_ID=$itemId";
        global $DB;
        $res = $DB->Query($sql, false);
    }
    public static function getListIdUsersGroup(){
        $result = \Bitrix\Main\UserGroupTable::getList(array(
            'filter' => array('GROUP_ID'=>1,'USER.ACTIVE'=>'Y'),
            'select' => array('USER_ID','NAME'=>'USER.NAME','LAST_NAME'=>'USER.LAST_NAME'), // выбираем идентификатор п-ля, имя и фамилию
            'order' => array('USER.ID'=>'DESC'), // сортируем по идентификатору пользователя
        ));
        $arGroupUsers  = [];
        while ($arGroup = $result->fetch())
        {
            $arGroupUsers[] = $arGroup['USER_ID'];
        }
        return $arGroupUsers;
    }
    //функция отправки уведомлений любому кол-во айди  / массив айди / или массив айди из группы
    public static function addNewNotificMess($userId = false , $type = '' , $data = []  , $itemId ){
        $connection = \Bitrix\Main\Application::getConnection();
        $messData = '';
        function mres($value)
        {
            $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
            $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
            return str_replace($search, $replace, $value);
        }
        foreach ($userId as $id => $val){
            $exam =  [
                date('Y-m-d H:i:s'),
                Application::getInstance()->getContext()->getServer()->get('REMOTE_ADDR'),
                $val,
                $type,
                mres(serialize($data)),
                 0,
                $itemId
            ];
            $str = "(NULL ,'".implode("','",$exam)."')";
            $messData .= $str;

        }
        $messData  =  str_replace(  ")(",  "),(",  $messData  );

        $sql1 = "INSERT INTO `sitemanager`.`democontentpinotifications` 
                (`ID`, `UF_CREATED_AT`, `UF_IP`, `UF_USER_ID`, `UF_TYPE`, `UF_DATA`, `UF_READ`,`UF_ITEM_ID`)
                VALUES
                       $messData ";
        global $DB;
        $res = $DB->Query($sql1, false);
    }
    public static function add($userId = 0, $type = '', $data = [] , $userItemId = false)
    {


        $className = ToUpper(end(explode('\\', __CLASS__)));
        try {
            $hl = new Hl(static::TABLE_NAME, 0);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                $add = $obj::add(
                    [
                        'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                        'UF_IP' => Application::getInstance()->getContext()->getServer()->get('REMOTE_ADDR'),
                        'UF_USER_ID' => $userId,
                        'UF_TYPE' => $type,
                        'UF_DATA' => serialize($data),
                        'UF_READ' => 0,
                        'UF_ITEM_ID' => $userItemId
                    ]
                );
                if (!$add->isSuccess() && !$add->getId()) {
                    throw new NotificationsException('Insert Error');
                }
            } else {
                throw new NotificationsException('Object is NULL');
            }
        } catch (NotificationsException $e) {
            switch ($e->getMessage()) {
                case 'Object is NULL':
                    $add = Hl::create(
                        ToLower(static::TABLE_NAME),
                        [
                            'UF_CREATED_AT' => [
                                'Y',
                                'datetime',
                                [
                                    'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                    'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                                    'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                                ]
                            ],
                            'UF_IP' => [
                                'N',
                                'string',
                                [
                                    'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                    'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IP')],
                                    'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IP')],
                                ]
                            ],
                            'UF_USER_ID' => [
                                'Y',
                                'integer',
                                [
                                    'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                    'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
                                    'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
                                ]
                            ],
                            'UF_TYPE' => [
                                'Y',
                                'string',
                                [
                                    'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                    'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TYPE')],
                                    'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TYPE')],
                                ]
                            ],
                            'UF_DATA' => [
                                'Y',
                                'string',
                                [
                                    'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                    'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DATA')],
                                    'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DATA')],
                                ]
                            ],
                            'UF_READ' => [
                                'N',
                                'integer',
                                [
                                    'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                    'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_READ')],
                                    'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_READ')],
                                ]
                            ],
                        ],
                        [
                            'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_IP` VARCHAR(255);',
                            'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_DATA` LONGTEXT;'
                        ],
                        [
                            ['UF_CREATED_AT'],
                            ['UF_IP'],
                            ['UF_USER_ID'],
                            ['UF_TYPE'],
                            ['UF_READ'],
                            ['UF_USER_ID', 'UF_READ'],
                        ],
                        Loc::getMessage($className . '_IBLOCK_NAME')
                    );
                    if ($add) {
                        self::add($userId, $type, $data);
                    }
                    break;
                case 'Insert Error':
                    /*
                     * Some handler
                     */
                    break;
            }
        } catch (\Exception $e) {
        }
    }
}