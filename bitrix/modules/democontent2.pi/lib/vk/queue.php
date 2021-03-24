<?php
/**
 * User: Sergey nazarkin
 * Email: nazarkin2017@mail.ru
 * Date: 08.01.2021
 * Time: 13:09
 */

namespace Democontent2\Pi\VK;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\IO\Path;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\Encoding;
use Bitrix\Main\Type\DateTime;
use Democontent2\Pi\EventManager;
use Democontent2\Pi\Hl;

class Queue
{
    const TABLE_NAME = 'Democontentpivk';

    private $class = null;

    /**
     * Queue constructor.
     */
    public function __construct()
    {
        $hl = new Hl(static::TABLE_NAME);
        if ($hl->obj !== null) {
            $this->class = $hl->obj;
        } else {
            $className = ToUpper(end(explode('\\', __CLASS__)));
            $add = Hl::create(
                ToLower(static::TABLE_NAME),
                [
                    'UF_CITY_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CITY_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CITY_ID')],
                        ]
                    ],
                    'UF_IBLOCK_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_ID')],
                        ]
                    ],
                    'UF_APP_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_APP_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_APP_ID')],
                        ]
                    ],
                    'UF_PUBLIC_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PUBLIC_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PUBLIC_ID')],
                        ]
                    ],
                    'UF_ACCESS_KEY' => [
                        'Y',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ACCESS_KEY')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ACCESS_KEY')],
                        ]
                    ],
                    'UF_DESCRIPTION' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DESCRIPTION')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DESCRIPTION')],
                        ]
                    ],
                    'UF_HASH_TAGS' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_HASH_TAGS')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_HASH_TAGS')],
                        ]
                    ],
                    'UF_UTM' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_UTM')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_UTM')],
                        ]
                    ],
                    'UF_LAST_POST_OBJECT' => [
                        'N',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_LAST_POST_OBJECT')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_LAST_POST_OBJECT')],
                        ]
                    ],
                    'UF_LAST_POST_TIME' => [
                        'N',
                        'datetime',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_LAST_POST_TIME')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_LAST_POST_TIME')],
                        ]
                    ],
                ],
                [],
                [
                    ['UF_CITY_ID'],
                    ['UF_IBLOCK_ID'],
                    ['UF_APP_ID'],
                    ['UF_PUBLIC_ID'],
                    ['UF_LAST_POST_OBJECT'],
                    ['UF_LAST_POST_TIME'],
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );
            if ($add) {
                $this->__construct();
            }
        }
    }

    private function getGroups()
    {
        $result = [];

        if ($this->class != null) {
            try {
                $obj = $this->class;
                $get = $obj::getList(
                    [
                        'select' => [
                            '*'
                        ],
                        'filter' => [
                            '>UF_CITY_ID' => 0,
                            '>UF_APP_ID' => 0,
                            '>UF_PUBLIC_ID' => 0,
                            '>UF_IBLOCK_ID' => 0,
                            '!UF_ACCESS_KEY' => false
                        ]
                    ]
                );

                while ($res = $get->Fetch()) {
                    $result[intval($res['ID'])] = $res;
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    private function groupLog($id, $objectId)
    {
        $result = [];

        if ($this->class != null) {
            try {
                $obj = $this->class;
                $obj::update(
                    $id,
                    [
                        'UF_LAST_POST_OBJECT' => $objectId,
                        'UF_LAST_POST_TIME' => DateTime::createFromTimestamp(time())
                    ]
                );
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    public function processing()
    {
        $groups = $this->getGroups();

        if (count($groups) > 0) {
            $siteParams = \CSite::GetList(
                $by = 'sort',
                $order = 'desc',
                [
                    'ID' => Option::get('democontent2.pi', 'siteId')
                ]
            )->Fetch();

            foreach ($groups as $group) {
                $hashTags = [];

                if ($group['UF_HASH_TAGS']) {
                    $ex = explode(',', $group['UF_HASH_TAGS']);
                    if (!Application::isUtfMode()) {
                        foreach ($ex as $e) {
                            $hashTags[] = Encoding::convertEncoding($e, 'windows-1251', 'UTF-8');
                        }
                    } else {
                        $hashTags = $ex;
                    }
                }

                if (intval($group['UF_CITY_ID']) > 0 && intval($group['UF_IBLOCK_ID']) > 0 && intval($group['UF_APP_ID']) > 0
                    && intval($group['UF_PUBLIC_ID']) > 0 && strlen($group['UF_ACCESS_KEY']) > 0) {

                    $vkGroups = new Posting(
                        [
                            'access_token' => $group['UF_ACCESS_KEY'],
                            'app_id' => $group['UF_APP_ID']
                        ]
                    );

                    $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5(intval($group['UF_IBLOCK_ID'])))));
                    $hl = new Hl($tableName);
                    if ($hl->obj !== null) {
                        try {
                            $obj = $hl->obj;
                            $get = $obj::getList(
                                [
                                    'select' => [
                                        'ID',
                                        'UF_ID',
                                        'UF_IBLOCK_TYPE',
                                        'UF_IBLOCK_CODE',
                                        'UF_NAME',
                                        'UF_CODE',
                                        'UF_IMAGE_ID',
                                        'UF_DESCRIPTION',
                                        'UF_PRICE',
                                        'UF_PROPERTIES'
                                    ],
                                    'filter' => [
                                        '!UF_ID' => intval($group['UF_LAST_POST_OBJECT']),
                                        '>UF_ID' => intval($group['UF_LAST_POST_OBJECT']),
                                        '>UF_IMAGE_ID' => 0,
                                        '=UF_CITY' => intval($group['UF_CITY_ID']),
                                        '=UF_ACTIVE' => 1,
                                        '=UF_MODERATION' => 0,
                                        '<=UF_ACTIVE_FROM' => DateTime::createFromTimestamp(time()),
                                        '>=UF_ACTIVE_TO' => DateTime::createFromTimestamp(time()),
                                        '>UF_PAYED' => 0,
                                        '>UF_PRICE' => 0
                                    ],
                                    'order' => [
                                        'ID' => 'DESC'
                                    ],
                                    'limit' => 1
                                ]
                            );
                            while ($arData = $get->fetch()) {
                                $props = [];
                                $macros = [
                                    'PUBLIC_ID' => $group['UF_PUBLIC_ID'],
                                    'OBJECT_ID' => intval($arData['UF_ID'])
                                ];

                                if ($group['UF_UTM']) {
                                    foreach ($macros as $macrosKey => $macrosValue) {
                                        $group['UF_UTM'] = str_replace('#' . $macrosKey . '#', $macrosValue, $group['UF_UTM']);
                                    }
                                }

                                if (strlen($arData['UF_PROPERTIES']) > 0) {
                                    $params = unserialize($arData['UF_PROPERTIES']);
                                    if (count($params) > 0) {
                                        foreach ($params as $k => $v) {
                                            $props[] = $v['name'] . ': ' . $v['value'] . chr(10);
                                        }
                                    }
                                }

                                $post = 0;

                                if (Application::isUtfMode()) {
                                    $post = $vkGroups->postToPublic(
                                        intval($group['UF_PUBLIC_ID']),
                                        Loc::getMessage('VK_SHOW_CONTACTS')
                                        . chr(10) . 'http://' . $siteParams['SERVER_NAME'] . $siteParams['DIR'] . $arData['UF_IBLOCK_TYPE'] . '/' . $arData['UF_IBLOCK_CODE'] . '/' . $arData['UF_CODE'] . '/'
                                        . (($group['UF_UTM']) ? '?' . $group['UF_UTM'] : '') . chr(10) . chr(10)
                                        . Loc::getMessage('VK_PRICE') . $arData['UF_PRICE'] . chr(10) . str_replace('<br />', chr(10), $arData['UF_DESCRIPTION']) . chr(10) . chr(10)
                                        . ((count($props) > 0) ? implode('', $props) : ''),
                                        Path::normalize(Application::getDocumentRoot() . \CFile::GetPath(intval($arData['UF_IMAGE_ID']))),
                                        $hashTags
                                    );
                                } else {
                                    $post = $vkGroups->postToPublic(
                                        intval($group['UF_PUBLIC_ID']),
                                        Encoding::convertEncoding(
                                            Loc::getMessage('VK_SHOW_CONTACTS')
                                            . chr(10) . 'http://' . $siteParams['SERVER_NAME'] . $siteParams['DIR'] . $arData['UF_IBLOCK_TYPE'] . '/' . $arData['UF_IBLOCK_CODE'] . '/' . $arData['UF_CODE'] . '/'
                                            . (($group['UF_UTM']) ? '?' . $group['UF_UTM'] : '') . chr(10) . chr(10)
                                            . Loc::getMessage('VK_PRICE') . $arData['UF_PRICE'] . chr(10) . str_replace('<br />', chr(10), $arData['UF_DESCRIPTION']) . chr(10) . chr(10)
                                            . ((count($props) > 0) ? implode('', $props) : ''),
                                            'windows-1251',
                                            'UTF-8'
                                        ),
                                        Path::normalize(Application::getDocumentRoot() . \CFile::GetPath(intval($arData['UF_IMAGE_ID']))),
                                        $hashTags
                                    );
                                }

                                if (intval($post)) {
                                    $eventManager = new EventManager(
                                        'vkGroupPostSuccess',
                                        [
                                            'publicId' => intval($group['UF_PUBLIC_ID']),
                                            'objectId' => intval($arData['UF_ID']),
                                            'postId' => intval($post)
                                        ]
                                    );
                                    $eventManager->execute();

                                    $this->groupLog(intval($group['ID']), intval($arData['UF_ID']));
                                } else {
                                    $eventManager = new EventManager(
                                        'vkGroupPostFailed',
                                        [
                                            'publicId' => intval($group['UF_PUBLIC_ID']),
                                            'objectId' => intval($arData['UF_ID'])
                                        ]
                                    );
                                    $eventManager->execute();
                                }
                            }
                        } catch (\Exception $e) {

                        }
                    }

                    unset($hl);
                }
            }
        }

        return;
    }
}