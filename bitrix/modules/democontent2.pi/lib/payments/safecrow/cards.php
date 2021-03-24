<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 21.01.2019
 * Time: 17:52
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Payments\SafeCrow;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Democontent2\Pi\Hl;

class Cards
{
    const TABLE_NAME = 'Democontentpisafecrowcards';

    protected $userId = 0;
    protected $safeCrowUserId = 0;

    private $obj = null;

    /**
     * Cards constructor.
     */
    public function __construct()
    {
        $className = ToUpper(end(explode('\\', __CLASS__)));
        $hl = new Hl(static::TABLE_NAME);
        if ($hl->obj !== null) {
            $this->obj = $hl->obj;
        } else {
            $add = Hl::create(
                ToLower(static::TABLE_NAME),
                [
                    'UF_CREATED_AT' => [
                        'Y',
                        'datetime',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => ''],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')]
                        ]
                    ],
                    'UF_USER_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')]
                        ]
                    ],
                    'UF_CARD_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CARD_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CARD_ID')]
                        ]
                    ],
                    'UF_PARAMS' => [
                        'Y',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => ''],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PARAMS')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PARAMS')]
                        ]
                    ]
                ],
                [
                    'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_PARAMS` LONGTEXT;'
                ],
                [
                    ['UF_CREATED_AT'],
                    ['UF_USER_ID'],
                    ['UF_CARD_ID']
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );

            if ($add) {
                $this->__construct();
            }
        }
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = intval($userId);
    }

    /**
     * @param int $safeCrowUserId
     */
    public function setSafeCrowUserId($safeCrowUserId)
    {
        $this->safeCrowUserId = intval($safeCrowUserId);
    }

    public function getUserCard()
    {
        $result = [];

        $cache = Application::getInstance()->getCache();
        $cache_time = 86400 * 365;
        $cache_id = md5('card' . $this->userId);
        $cache_path = '/' . DSPI . '/cards';
        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();

            if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                $result = $res[$cache_id];
            }
        } else {
            $taggedCache->startTagCache($cache_path);
            $taggedCache->registerTag('cards');

            if ($this->obj !== null && $this->userId > 0) {
                try {
                    $obj = $this->obj;
                    $get = $obj::getList(
                        [
                            'select' => ['*'],
                            'filter' => [
                                '=UF_USER_ID' => $this->userId
                            ],
                            'limit' => 1
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $result = $res;
                    }
                } catch (\Exception $e) {

                }
            }

            if ($cache_time > 0) {
                $cache->startDataCache($cache_time, $cache_id, $cache_path);
                if (!count($result)) {
                    $cache->abortDataCache();
                    $taggedCache->abortTagCache();
                }
                $cache->endDataCache(
                    [
                        $cache_id => $result
                    ]
                );
                $taggedCache->endTagCache();
            }
        }

        return $result;
    }

    public function saveCard($userCard)
    {
        if ($this->obj !== null && $this->userId > 0) {
            try {
                $id = 0;
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => ['ID'],
                        'filter' => [
                            '=UF_USER_ID' => $this->userId
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $id = intval($res['ID']);
                }

                if (!$id) {
                    $add = $obj::add(
                        [
                            'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                            'UF_USER_ID' => $this->userId,
                            'UF_CARD_ID' => $userCard['id'],
                            'UF_PARAMS' => serialize($userCard)
                        ]
                    );

                    if ($add->isSuccess()) {
                        Application::getInstance()->getTaggedCache()->clearByTag('cards');
                    }
                }
            } catch (\Exception $e) {

            }
        }
    }

    public function addCard(\Bitrix\Main\HttpRequest $request)
    {
        $result = [];

        if ($this->obj !== null && $this->userId > 0 && $this->safeCrowUserId > 0) {
            $safeCrow = new SafeCrow();
            $result = $safeCrow->addUserCard(
                $this->safeCrowUserId,
                (($request->isHttps()) ? 'https://' : 'http://') . $request->getHttpHost() . $request->getDecodedUri()
            );
        }

        return $result;
    }
}