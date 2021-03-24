<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 19.01.2019
 * Time: 15:27
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Profile;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Democontent2\Pi\Hl;

class Profile
{
    const TABLE_NAME = 'Democontentpiprofiles';

    protected $userId = 0;
    protected $data = [];
    protected $ttl = 86400;
    private $obj = null;

    /**
     * Profile constructor.
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
                    'UF_USER_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')]
                        ]
                    ],
                    'UF_DATA' => [
                        'Y',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => ''],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DATA')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DATA')]
                        ]
                    ]
                ],
                [
                    'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_DATA` LONGTEXT;'
                ],
                [
                    ['UF_USER_ID']
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );

            if ($add) {
                $this->__construct();
            }
        }
    }

    /**
     * @param int $ttl
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = intval($userId);
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function get()
    {
        $result = [];

        $cache = Application::getInstance()->getCache();
        $cache_time = $this->ttl;
        $cache_id = md5('profile_' . $this->userId);
        $cache_path = '/' . DSPI . '/profiles';

        $taggedCache = Application::getInstance()->getTaggedCache();

        if ($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();

            if (is_array($res[$cache_id]) && (count($res[$cache_id]) > 0)) {
                $result = $res[$cache_id];
            }
        } else {
            $taggedCache->startTagCache($cache_path);
            $taggedCache->registerTag($cache_id);

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
                $cache->endDataCache([$cache_id => $result]);
                $taggedCache->endTagCache();
            }
        }

        return $result;
    }

    public function add()
    {
        if (!count($this->get())) {
            if ($this->obj !== null && $this->userId > 0) {
                try {
                    $obj = $this->obj;
                    $add = $obj::add(
                        [
                            'UF_USER_ID' => $this->userId,
                            'UF_DATA' => serialize($this->data)
                        ]
                    );

                    if ($add->isSuccess()) {

                    }
                } catch (\Exception $e) {

                }
            }
        }
    }

    public function update()
    {
        $data = $this->get();
        if (count($data) > 0) {
            try {
                $obj = $this->obj;
                $update = $obj::update(
                    $data['ID'],
                    [
                        'UF_DATA' => serialize($this->data)
                    ]
                );

                if ($update->isSuccess()) {
                    Application::getInstance()->getTaggedCache()->clearByTag(md5('profile_' . $this->userId));
                }
            } catch (\Exception $e) {

            }
        } else {
            $this->add();
        }
    }
}