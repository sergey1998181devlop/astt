<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 08.01.2019
 * Time: 13:19
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Iblock;

use Bitrix\Main\Application;
use Bitrix\Main\IO\Path;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Democontent2\Pi\Hl;
use Democontent2\Pi\User;

class Complain
{
    const TABLE_NAME = 'Democontentpicomplains';

    private $userId = 0;
    private $taskId = 0;
    private $stageId = 0;
    private $iBlockId = 0;
    private $text = '';
    private $obj = null;

    /**
     * Complain constructor.
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
                    'UF_STATUS' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_STATUS')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_STATUS')]
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
                    'UF_IBLOCK_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IBLOCK_ID')]
                        ]
                    ],
                    'UF_TASK_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TASK_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TASK_ID')]
                        ]
                    ],
                    'UF_STAGE_ID' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_STAGE_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_STAGE_ID')]
                        ]
                    ],
                    'UF_TEXT' => [
                        'Y',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => ''],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TEXT')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TEXT')]
                        ]
                    ],
                    'UF_FILES' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => ''],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_FILES')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_FILES')]
                        ]
                    ]
                ],
                [
                    'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_TEXT` LONGTEXT;',
                    'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_FILES` LONGTEXT;',
                ],
                [
                    ['UF_CREATED_AT'],
                    ['UF_STATUS'],
                    ['UF_USER_ID'],
                    ['UF_TASK_ID'],
                    ['UF_STAGE_ID'],
                    ['UF_TASK_ID', 'UF_STATUS'],
                    ['UF_TASK_ID', 'UF_STAGE_ID'],
                    ['UF_USER_ID', 'UF_TASK_ID']
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );

            if ($add) {
                $this->__construct();
            }
        }
    }

    /**
     * @param int $stageId
     */
    public function setStageId($stageId)
    {
        $this->stageId = intval($stageId);
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = intval($userId);
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        if (strlen(trim(strip_tags($text))) > 0) {
            $this->text = strip_tags(HTMLToTxt($text));
        }
    }

    /**
     * @param int $taskId
     */
    public function setTaskId($taskId)
    {
        $this->taskId = intval($taskId);
    }

    /**
     * @param int $iBlockId
     */
    public function setIBlockId($iBlockId)
    {
        $this->iBlockId = intval($iBlockId);
    }

    /**
     * @param $ownerId
     * @param $executorId
     * @param array $filesData
     */
    public function add($ownerId, $executorId, $filesData = [])
    {
        if ($this->obj !== null && $this->userId > 0 && $this->taskId > 0 && $this->iBlockId > 0 && strlen($this->text) > 0) {
            try {
                $obj = $this->obj;
                $add = $obj::add(
                    [
                        'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                        'UF_STATUS' => 0,
                        'UF_USER_ID' => $this->userId,
                        'UF_IBLOCK_ID' => $this->iBlockId,
                        'UF_TASK_ID' => $this->taskId,
                        'UF_STAGE_ID' => $this->stageId,
                        'UF_TEXT' => $this->text,
                        'UF_FILES' => serialize([])
                    ]
                );

                if ($add->isSuccess()) {
                    $us = new User(0);

                    $us->setId($ownerId);
                    $ownerParams = $us->get();

                    $us->setId($executorId);
                    $executorParams = $us->get();

                    //TODO Перенести интеграцию с SafeCrow сюда

                }
            } catch (\Exception $e) {

            }
        }
    }

    /**
     * @return array|false
     */
    public function get()
    {
        $result = [];

        if ($this->obj !== null && $this->taskId > 0) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => ['*'],
                        'filter' => [
                            '=UF_TASK_ID' => $this->taskId,
                            '=UF_STATUS' => 0
                        ],
                        'limit' => 1,
                        'order' => [
                            'ID' => 'DESC'
                        ]
                    ]
                );
                while ($res = $get->fetch()) {
                    $result = $res;
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }
}