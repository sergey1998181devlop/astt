<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 15.01.2019
 * Time: 13:30
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Iblock;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\IO\Path;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Democontent2\Pi\Chat;
use Democontent2\Pi\CheckList\ResponseValues;
use Democontent2\Pi\FireBase;
use Democontent2\Pi\Hl;
use Democontent2\Pi\Notifications;
use Democontent2\Pi\Profile\Profile;
use Democontent2\Pi\User;
use Democontent2\Pi\Utils;

class Response
{
    const TABLE_NAME = 'Democontentpiresponses';

    protected $offerId = 0;
    protected $taskId = 0;
    protected $iBlockId = 0;
    protected $userId = 0;
    protected $executorId = 0;
    protected $text = '';
    protected $files = [];

    private $error = 0;
    private $obj = null;

    /**
     * Response constructor.
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
                    'UF_EXECUTOR' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_EXECUTOR')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_EXECUTOR')]
                        ]
                    ],
                    'UF_CANDIDATE' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CANDIDATE')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CANDIDATE')]
                        ]
                    ],
                    'UF_DENIED' => [
                        'Y',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DENIED')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DENIED')]
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
                    ],
                    'UF_READ' => [
                        'N',
                        'boolean',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => false],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_READ')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_READ')]
                        ]
                    ],
                ],
                [],
                [
                    ['UF_CREATED_AT'],
                    ['UF_USER_ID'],
                    ['UF_TASK_ID'],
                    ['UF_EXECUTOR'],
                    ['UF_CANDIDATE'],
                    ['UF_DENIED'],
                    ['UF_USER_ID', 'UF_TASK_ID'],
                    ['UF_TASK_ID', 'UF_READ']
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );

            if ($add) {
                $this->__construct();
            }
        }
    }

    /**
     * @param int $offerId
     */
    public function setOfferId(int $offerId): void
    {
        $this->offerId = intval($offerId);
    }

    /**
     * @param int $executorId
     */
    public function setExecutorId($executorId)
    {
        $this->executorId = intval($executorId);
    }

    /**
     * @param int $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @return int
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param int $iBlockId
     */
    public function setIBlockId($iBlockId)
    {
        $this->iBlockId = intval($iBlockId);
    }

    /**
     * @param int $taskId
     */
    public function setTaskId($taskId)
    {
        $this->taskId = intval($taskId);
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
        $this->text = $text;
    }

    /**
     * @param array $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    public function block($offerId)
    {
        $this->error = 1;

        if ($this->obj !== null && $this->taskId > 0 && $this->iBlockId > 0 && $this->userId > 0 && intval($offerId)) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID',
                            'UF_IBLOCK_ID',
                            'UF_USER_ID',
                            'UF_FILES'
                        ],
                        'filter' => [
                            '=ID' => intval($offerId),
                            '=UF_TASK_ID' => $this->taskId,
                            '=UF_IBLOCK_ID' => $this->iBlockId,
                            '=UF_DENIED' => 1
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $delete = $obj::delete(intval($res['ID']));
                    if ($delete->isSuccess()) {
                        $this->error = 0;

                        $blackList = new BlackList();
                        $blackList->setUserId($this->userId);
                        $blackList->setBlockedId($res['UF_USER_ID']);
                        $blackList->add();

                        $item = new Item();
                        $item->setIBlockId($this->iBlockId);
                        $item->setItemId($this->taskId);

                        $responseCounter = $item->getResponseCounter();
                        if (count($responseCounter) == 2) {
                            if (intval($responseCounter['ID']) && intval($responseCounter['UF_RESPONSE_COUNT'])) {
                                $item->updateResponseCounter(
                                    intval($responseCounter['ID']),
                                    (intval($responseCounter['UF_RESPONSE_COUNT']) - 1)
                                );
                            }
                        }

                        $files = unserialize($res['UF_FILES']);
                        foreach ($files as $file) {
                            \CFile::Delete($file);
                        }
                    }
                }
            } catch (\Exception $e) {

            }
        }
    }

    public function unSetDenied($offerId)
    {
        $this->error = 1;

        if ($this->obj !== null && $this->taskId > 0 && $this->iBlockId > 0) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID',
                            'UF_USER_ID'
                        ],
                        'filter' => [
                            '=ID' => $offerId,
                            '=UF_TASK_ID' => $this->taskId,
                            '=UF_IBLOCK_ID' => $this->iBlockId,
                            '=UF_DENIED' => 1
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $update = $obj::update(
                        intval($res['ID']),
                        [
                            'UF_EXECUTOR' => 0,
                            'UF_CANDIDATE' => 0,
                            'UF_DENIED' => 0
                        ]
                    );

                    if ($update->isSuccess()) {
                        $this->error = 0;
                    }
                }
            } catch (\Exception $e) {

            }
        }
    }

    public function setDenied($offerId)
    {
        $this->error = 1;

        if ($this->obj !== null && $this->taskId > 0 && $this->iBlockId > 0 && intval($offerId)) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID',
                            'UF_USER_ID',
                            'UF_IBLOCK_ID'
                        ],
                        'filter' => [
                            '=ID' => intval($offerId),
                            '=UF_TASK_ID' => $this->taskId,
                            '=UF_IBLOCK_ID' => $this->iBlockId,
                            '=UF_EXECUTOR' => 0,
                            '=UF_DENIED' => 0
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $update = $obj::update(
                        intval($res['ID']),
                        [
                            'UF_EXECUTOR' => 0,
                            'UF_CANDIDATE' => 0,
                            'UF_DENIED' => 1
                        ]
                    );

                    if ($update->isSuccess()) {
                        $this->error = 0;

                        Notifications::add(
                            $res['UF_USER_ID'],
                            'responseDenied',
                            [
                                'taskId' => $this->taskId,
                                'iBlockId' => $this->iBlockId
                            ]
                        );

                        $request = Application::getInstance()->getContext()->getRequest();
                        $fb = new FireBase($res['UF_USER_ID']);
                        $fb->webPush([
                            'title' => Loc::getMessage('PUSH_RESPONSE_DENIED_TITLE'),
                            'body' => Loc::getMessage('PUSH_RESPONSE_DENIED_BODY', ['{{TASK_ID}}' => $this->taskId]),
                            'url' => (($request->isHttps()) ? 'https://' : 'http://')
                                . Path::normalize($request->getHttpHost() . SITE_DIR
                                    . 'task' . $this->taskId . '-' . $this->iBlockId) . '/'
                        ]);
                        unset($fb);
                    }
                }
            } catch (\Exception $e) {

            }
        }
    }

    public function unSetCandidate($offerId)
    {
        $this->error = 1;

        if ($this->obj !== null && $this->taskId > 0 && $this->iBlockId > 0) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID',
                            'UF_USER_ID'
                        ],
                        'filter' => [
                            '=ID' => $offerId,
                            '=UF_TASK_ID' => $this->taskId,
                            '=UF_IBLOCK_ID' => $this->iBlockId,
                            '=UF_EXECUTOR' => 0,
                            '=UF_CANDIDATE' => 1,
                            '=UF_DENIED' => 0
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $update = $obj::update(
                        intval($res['ID']),
                        [
                            'UF_CANDIDATE' => 0
                        ]
                    );

                    if ($update->isSuccess()) {
                        $this->error = 0;
                    }
                }
            } catch (\Exception $e) {

            }
        }
    }

    public function setCandidate($offerId)
    {
        $this->error = 1;

        if ($this->obj !== null && $this->taskId > 0 && $this->iBlockId > 0 && intval($offerId) > 0) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID',
                            'UF_USER_ID',
                            'UF_IBLOCK_ID'
                        ],
                        'filter' => [
                            '=ID' => intval($offerId),
                            '=UF_TASK_ID' => $this->taskId,
                            '=UF_IBLOCK_ID' => $this->iBlockId,
                            '=UF_EXECUTOR' => 0,
                            '=UF_CANDIDATE' => 0,
                            '=UF_DENIED' => 0
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $update = $obj::update(
                        intval($res['ID']),
                        [
                            'UF_CANDIDATE' => 1
                        ]
                    );

                    if ($update->isSuccess()) {
                        $this->error = 0;

                        Notifications::add(
                            $res['UF_USER_ID'],
                            'responseCandidate',
                            [
                                'taskId' => $this->taskId,
                                'iBlockId' => $this->iBlockId
                            ]
                        );

                        $request = Application::getInstance()->getContext()->getRequest();
                        $fb = new FireBase($res['UF_USER_ID']);
                        $fb->webPush([
                            'title' => Loc::getMessage('PUSH_RESPONSE_CANDIDATE_TITLE'),
                            'body' => Loc::getMessage('PUSH_RESPONSE_CANDIDATE_BODY', ['{{TASK_ID}}' => $this->taskId]),
                            'url' => (($request->isHttps()) ? 'https://' : 'http://')
                                . Path::normalize($request->getHttpHost() . SITE_DIR
                                    . 'task' . $this->taskId . '-' . $this->iBlockId) . '/'
                        ]);
                        unset($fb);
                    }
                }
            } catch (\Exception $e) {

            }
        }
    }

    /**
     * @return array
     */
    public function checkExecutor($returnUser = false)
    {
        $result = [
            'id' => 0,
            'userId' => 0,
            'userParams' => null
        ];

        if ($this->obj !== null && $this->taskId > 0 && $this->iBlockId > 0) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID',
                            'UF_USER_ID'
                        ],
                        'filter' => [
                            '=UF_TASK_ID' => $this->taskId,
                            '=UF_IBLOCK_ID' => $this->iBlockId,
                            '=UF_EXECUTOR' => 1
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $result['id'] = intval($res['ID']);
                    $result['userId'] = intval($res['UF_USER_ID']);

                    if ($returnUser) {
                        $us = new User($result['userId']);
                        $reviews = new \Democontent2\Pi\Iblock\Reviews();
                        $reviews->setUserId(intval($result['userId']));

                        $result['userParams'] = $us->get();
                        $result['userParams']['ratingDetails'] = $reviews->rating();
                        $result['userParams']['reviewsCount'] = $reviews->getCountByUser();

                    }
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    /**
     * @param $ownerId
     */
    public function confirmExecutor($ownerId)
    {
        $this->error = 1;
        if ($this->obj !== null && $this->taskId > 0 && $this->iBlockId > 0 && $this->executorId > 0) {
            try {
                $item = new Item();
                $item->setIBlockId($this->iBlockId);
                $item->setItemId($this->taskId);

                if ($item->getCurrentStatus() == 4) {
                    $request = Application::getInstance()->getContext()->getRequest();
                    $obj = $this->obj;
                    $get = $obj::getList(
                        [
                            'select' => ['ID'],
                            'filter' => [
                                '=UF_TASK_ID' => $this->taskId,
                                '=UF_IBLOCK_ID' => $this->iBlockId,
                                '=UF_USER_ID' => $this->executorId,
                                '=UF_EXECUTOR' => 1
                            ],
                            'limit' => 1
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $this->error = 0;
                        $statusEnums = \CIBlockPropertyEnum::GetList(
                            [
                                'SORT' => 'ASC'
                            ],
                            [
                                'IBLOCK_ID' => $this->iBlockId,
                                'CODE' => '__hidden_status'
                            ]
                        );
                        while ($statusEnumsRes = $statusEnums->GetNext()) {
                            if ($statusEnumsRes['XML_ID'] == '__status_2') {
                                \CIBlockElement::SetPropertyValuesEx(
                                    $this->taskId,
                                    $this->iBlockId,
                                    [
                                        '__hidden_status' => $statusEnumsRes['ID']
                                    ]
                                );

                                $el = new \CIBlockElement();
                                $el->Update(
                                    $this->taskId,
                                    [
                                        'TIMESTAMP_X' => DateTime::createFromTimestamp(time())
                                    ]
                                );

                                $us = new User($ownerId);
                                $userParams = $us->get();
                                if (count($userParams) > 0) {
                                    Event::send(
                                        [
                                            'EVENT_NAME' => 'DSPI_APPLY_EXECUTOR',
                                            'LID' => Application::getInstance()->getContext()->getSite(),
                                            'C_FIELDS' => [
                                                'EMAIL_TO' => $userParams['EMAIL'],
                                                'ITEM_ID' => $this->taskId,
                                                'FULL_URL' => (($request->isHttps()) ? 'https://' : 'http://')
                                                    . Path::normalize($request->getHttpHost() . SITE_DIR
                                                        . 'task' . $this->taskId . '-' . $this->iBlockId) . '/'
                                            ]
                                        ]
                                    );

                                    $fb = new FireBase($ownerId);
                                    $fb->webPush([
                                        'title' => Loc::getMessage('PUSH_CONFIRM_EXECUTOR_TITLE'),
                                        'body' => Loc::getMessage('PUSH_CONFIRM_EXECUTOR_BODY', ['{{TASK_ID}}' => $this->taskId]),
                                        'url' => (($request->isHttps()) ? 'https://' : 'http://')
                                            . Path::normalize($request->getHttpHost() . SITE_DIR
                                                . 'task' . $this->taskId . '-' . $this->iBlockId) . '/'
                                    ]);
                                    unset($fb);
                                }
                                break;
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
            }
        }
    }

    public function unSetExecutor($offerId)
    {
        $this->error = 1;
        if ($this->obj !== null && $this->taskId > 0 && $this->iBlockId > 0 && $this->executorId > 0) {
            $item = new Item();
            $item->setIBlockId($this->iBlockId);
            $item->setItemId($this->taskId);

            if ($item->getCurrentStatus() == 4) {
                try {
                    $request = Application::getInstance()->getContext()->getRequest();
                    $obj = $this->obj;
                    $get = $obj::getList(
                        [
                            'select' => [
                                'ID',
                                'UF_USER_ID',
                            ],
                            'filter' => [
                                '=ID' => intval($offerId),
                                '=UF_TASK_ID' => $this->taskId,
                                '=UF_IBLOCK_ID' => $this->iBlockId,
                                '=UF_EXECUTOR' => 1
                            ],
                            'limit' => 1
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $this->error = 0;
                        $update = $obj::update(
                            $res['ID'],
                            [
                                'UF_EXECUTOR' => 0,
                                'UF_CANDIDATE' => 0
                            ]
                        );

                        if ($update->isSuccess()) {
                            $statusEnums = \CIBlockPropertyEnum::GetList(
                                [
                                    'SORT' => 'ASC'
                                ],
                                [
                                    'IBLOCK_ID' => $this->iBlockId,
                                    'CODE' => '__hidden_status'
                                ]
                            );
                            while ($statusEnumsRes = $statusEnums->GetNext()) {
                                if ($statusEnumsRes['XML_ID'] == '__status_1') {
                                    \CIBlockElement::SetPropertyValuesEx(
                                        $this->taskId,
                                        $this->iBlockId,
                                        [
                                            '__hidden_status' => $statusEnumsRes['ID']
                                        ]
                                    );

                                    $el = new \CIBlockElement();
                                    $el->Update(
                                        $this->taskId,
                                        [
                                            'TIMESTAMP_X' => DateTime::createFromTimestamp(time())
                                        ]
                                    );
                                    break;
                                }
                            }

                            Notifications::add(
                                $this->executorId,
                                'executorRemoved',
                                [
                                    'taskId' => $this->taskId,
                                    'iBlockId' => $this->iBlockId
                                ]
                            );

                            $us = new User($this->executorId);
                            $userParams = $us->get();
                            if (count($userParams) > 0) {
                                Event::send(
                                    [
                                        'EVENT_NAME' => 'DSPI_EXECUTOR_CANCEL',
                                        'LID' => Application::getInstance()->getContext()->getSite(),
                                        'C_FIELDS' => [
                                            'EMAIL_TO' => $userParams['EMAIL'],
                                            'ITEM_ID' => $this->taskId,
                                            'FULL_URL' => (($request->isHttps()) ? 'https://' : 'http://')
                                                . Path::normalize($request->getHttpHost() . SITE_DIR
                                                    . 'task' . $this->taskId . '-' . $this->iBlockId) . '/'
                                        ]
                                    ]
                                );
                            }

                            $login = Option::get(DSPI, 'mongoAdminLogin');
                            $password = Option::get(DSPI, 'mongoAdminPassword');

                            if ($login && $password) {
                                $client = new \MongoDB\Client(Utils::__mongoDbConnectString());
                                $db = $client->selectDatabase(Utils::mid())->selectCollection('rooms');

                                $rooms = $db->find(
                                    [
                                        'taskId' => intval($this->taskId)
                                    ]
                                );
                                foreach ($rooms as $room) {
                                    $db = $client->selectDatabase(Utils::mid())->selectCollection('messages');
                                    $roomId = (string)$room->_id;
                                    $messages = $db->find(
                                        [
                                            'roomId' => $roomId
                                        ],
                                        [
                                            'sort' => [
                                                'time' => 1
                                            ]
                                        ]
                                    );
                                    foreach ($messages as $message) {
                                        switch ($message->type) {
                                            case 'file':
                                                \CFile::Delete($message->file->id);
                                                break;
                                        }
                                    }

                                    $db->deleteMany(
                                        [
                                            'taskId' => intval($this->taskId)
                                        ]
                                    );
                                }

                                $db = $client->selectDatabase(Utils::mid())->selectCollection('rooms');
                                $db->deleteMany(
                                    [
                                        'taskId' => intval($this->taskId)
                                    ]
                                );

                                $chat = new Chat();
                                $chat->deleteAll(intval($this->taskId));
                            }
                        }
                    }
                } catch (\Exception $e) {

                }
            }
        }
    }

    public function setExecutor($offerId)
    {
        $this->error = 1;

        if ($this->obj !== null && $this->taskId > 0 && $this->iBlockId > 0 && intval($offerId) > 0) {
            try {
                $request = Application::getInstance()->getContext()->getRequest();
                $id = 0;
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => [
                            'ID',
                            'UF_USER_ID'
                        ],
                        'filter' => [
                            '=UF_TASK_ID' => $this->taskId,
                            '=UF_IBLOCK_ID' => $this->iBlockId,
                            '=UF_EXECUTOR' => 1
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $id = intval($res['ID']);
                }

                if (!$id) {
                    $get = $obj::getList(
                        [
                            'select' => ['ID', 'UF_USER_ID'],
                            'filter' => [
                                '=ID' => intval($offerId),
                                '=UF_TASK_ID' => $this->taskId,
                                '=UF_IBLOCK_ID' => $this->iBlockId
                            ]
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $update = $obj::update(
                            intval($offerId),
                            [
                                'UF_EXECUTOR' => 1
                            ]
                        );

                        if ($update->isSuccess()) {
                            $this->error = 0;

                            Notifications::add(
                                $res['UF_USER_ID'],
                                'responseExecutor',
                                [
                                    'taskId' => $this->taskId,
                                    'iBlockId' => $this->iBlockId
                                ]
                            );

                            try {
                                $statusEnums = \CIBlockPropertyEnum::GetList(
                                    [
                                        'SORT' => 'ASC'
                                    ],
                                    [
                                        'IBLOCK_ID' => $this->iBlockId,
                                        'CODE' => '__hidden_status'
                                    ]
                                );
                                while ($statusEnumsRes = $statusEnums->GetNext()) {
                                    if ($statusEnumsRes['XML_ID'] == '__status_4') {
                                        \CIBlockElement::SetPropertyValuesEx(
                                            $this->taskId,
                                            $this->iBlockId,
                                            [
                                                '__hidden_status' => $statusEnumsRes['ID']
                                            ]
                                        );

                                        $el = new \CIBlockElement();
                                        $el->Update(
                                            $this->taskId,
                                            [
                                                'TIMESTAMP_X' => DateTime::createFromTimestamp(time())
                                            ]
                                        );
                                        break;
                                    }
                                }
                            } catch (\Exception $e) {

                            }

                            $us = new User($res['UF_USER_ID']);
                            $userParams = $us->get();
                            if (count($userParams) > 0) {
                                Event::send(
                                    [
                                        'EVENT_NAME' => 'DSPI_SET_EXECUTOR',
                                        'LID' => Application::getInstance()->getContext()->getSite(),
                                        'C_FIELDS' => [
                                            'EMAIL_TO' => $userParams['EMAIL'],
                                            'ITEM_ID' => $this->taskId,
                                            'FULL_URL' => (($request->isHttps()) ? 'https://' : 'http://')
                                                . Path::normalize($request->getHttpHost() . SITE_DIR
                                                    . 'task' . $this->taskId . '-' . $this->iBlockId) . '/'
                                        ]
                                    ]
                                );

                                $fb = new FireBase($res['UF_USER_ID']);
                                $fb->webPush([
                                    'title' => Loc::getMessage('PUSH_RESPONSE_SET_EXECUTOR_TITLE'),
                                    'body' => Loc::getMessage('PUSH_RESPONSE_SET_EXECUTOR_BODY', ['{{TASK_ID}}' => $this->taskId]),
                                    'url' => (($request->isHttps()) ? 'https://' : 'http://')
                                        . Path::normalize($request->getHttpHost() . SITE_DIR
                                            . 'task' . $this->taskId . '-' . $this->iBlockId) . '/'
                                ]);
                                unset($fb);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {

            }
        }
    }

    public function readAll()
    {
        $result = 0;
        if ($this->obj !== null && $this->taskId > 0) {
            $obj = $this->obj;
            try {
                $get = $obj::getList(
                    [
                        'select' => ['ID'],
                        'filter' => [
                            '=UF_TASK_ID' => $this->taskId,
                            '=UF_READ' => false
                        ]
                    ]
                );
                while ($res = $get->fetch()) {
                    $obj::update($res['ID'], ['UF_READ' => true]);
                }
            } catch (ObjectPropertyException $e) {
            } catch (ArgumentException $e) {
            } catch (SystemException $e) {
            } catch (\Exception $e) {
            }
        }

        return $result;
    }

    public function getUnreadCount()
    {
        $result = 0;
        if ($this->obj !== null && $this->taskId > 0) {
            $obj = $this->obj;
            try {
                $result = intval($obj::getCount(
                    [
                        '=UF_TASK_ID' => $this->taskId,
                        '=UF_READ' => false
                    ]
                ));
            } catch (ObjectPropertyException $e) {
            } catch (SystemException $e) {
            }
        }

        return $result;
    }

    public function getList($setRead = false)
    {
        $result = [];

        if ($this->obj !== null && $this->taskId > 0) {
            try {
                $filter = [
                    '=UF_TASK_ID' => $this->taskId
                ];

                if ($this->offerId > 0) {
                    $filter['=ID'] = $this->offerId;
                }

                $profile = new Profile();
                $reviews = new Reviews();
                $us = new User(0);
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => ['*'],
                        'filter' => $filter,
                        'order' => [
                            'UF_CREATED_AT' => 'DESC'
                        ]
                    ]
                );
                while ($res = $get->fetch()) {
                    $reviews->setUserId(intval($res['UF_USER_ID']));
                    $us->setId(intval($res['UF_USER_ID']));
                    $profile->setUserId(intval($res['UF_USER_ID']));
                    $res['USER_DATA'] = $us->get();
                    $res['CURRENT_RATING'] = $reviews->rating();
                    $res['PROFILE'] = $profile->get();

                    $result[] = $res;

                    if ($setRead) {
                        if (!$res['UF_READ']) {
                            $obj::update($res['ID'], ['UF_READ' => true]);
                        }
                    }
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    public function checkResponse()
    {
        $result = true;

        if ($this->obj !== null && $this->taskId > 0 && $this->userId > 0) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => ['ID'],
                        'filter' => [
                            '=UF_USER_ID' => $this->userId,
                            '=UF_TASK_ID' => $this->taskId
                        ],
                        'limit' => 1
                    ]
                );
                while ($res = $get->fetch()) {
                    $result = false;
                }
            } catch (\Exception $e) {
                $result = false;
            }
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * @param $ownerId
     * @param HttpRequest $request
     * @return array|int
     */
    public function add($ownerId, HttpRequest $request)
    {
        $result = 0;

        if ($this->checkResponse()) {
            if (strlen($this->text) > 0) {
                $files = [];

                try {
                    $obj = $this->obj;
                    if (isset($this->files['__files'])) {
                        if (count($this->files['__files']['name']) > 0) {
                            foreach ($this->files['__files']['name'] as $fileKey => $fileValue) {
                                $extension = Utils::getExtension($fileValue);
                                if (strlen($extension) > 0) {
                                    $fileArray = \CFile::MakeFileArray($this->files['__files']['tmp_name'][$fileKey]);

                                    $fileArray['MODULE_ID'] = DSPI;
                                    $fileArray['description'] = $fileValue;
                                    $fileArray['name'] = md5(microtime(true)) . '-' . ToLower(randString(rand(5, 10))) . $extension;

                                    $fileId = \CFile::SaveFile($fileArray, DSPI);
                                    if (intval($fileId) > 0) {
                                        $files[] = intval($fileId);
                                    }
                                } else {
                                    unlink($this->files['__files']['tmp_name'][$fileKey]);
                                }
                            }
                        }
                    }

                    $add = $obj::add(
                        [
                            'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                            'UF_USER_ID' => $this->userId,
                            'UF_IBLOCK_ID' => $this->iBlockId,
                            'UF_TASK_ID' => $this->taskId,
                            'UF_EXECUTOR' => 0,
                            'UF_TEXT' => strip_tags(HTMLToTxt($this->text)),
                            'UF_FILES' => serialize($files),
                            'UF_CANDIDATE' => 0,
                            'UF_DENIED' => 0
                        ]
                    );
                    if (!$add->isSuccess()) {
                        if (count($files) > 0) {
                            foreach ($files as $k => $file) {
                                \CFile::Delete($file);
                                unset($files[$k]);
                            }
                        }
                    } else {
                        $result = $add->getId();

                        if (intval(Option::get(DSPI, 'response_checklists'))) {
                            $responseCheckListValues = new ResponseValues($this->taskId, $this->userId);
                            $responseCheckListValues->setOfferId($add->getId());
                            $responseCheckListValues->add($request->getPostList());
                        }

                        Notifications::add(
                            $this->userId,
                            'responseAdd',
                            [
                                'taskId' => $this->taskId,
                                'iBlockId' => $this->iBlockId
                            ]
                        );

                        Notifications::add(
                            $ownerId,
                            'incomingResponse',
                            [
                                'taskId' => $this->taskId,
                                'iBlockId' => $this->iBlockId
                            ]
                        );

                        $item = new Item();
                        $item->setIBlockId($this->iBlockId);
                        $item->setItemId($this->taskId);

                        $responseCounter = $item->getResponseCounter();
                        if (count($responseCounter) == 2) {
                            if (intval($responseCounter['ID'])) {
                                $item->updateResponseCounter(
                                    intval($responseCounter['ID']),
                                    (intval($responseCounter['UF_RESPONSE_COUNT']) + 1)
                                );
                            }
                        }

                        $us = new User($ownerId);
                        $userParams = $us->get();
                        if (count($userParams) > 0) {
                            Event::send(
                                [
                                    'EVENT_NAME' => 'DSPI_TASK_ADD_RESPONSE',
                                    'LID' => Application::getInstance()->getContext()->getSite(),
                                    'C_FIELDS' => [
                                        'EMAIL_TO' => $userParams['EMAIL'],
                                        'ITEM_ID' => $this->taskId,
                                        'FULL_URL' => (($request->isHttps()) ? 'https://' : 'http://')
                                            . Path::normalize($request->getHttpHost() . SITE_DIR
                                                . 'task' . $this->taskId . '-' . $this->iBlockId) . '/'
                                    ]
                                ]
                            );

                            $fb = new FireBase($ownerId);
                            $fb->webPush([
                                'title' => Loc::getMessage('PUSH_ADD_TITLE'),
                                'body' => Loc::getMessage('PUSH_ADD_BODY', ['{{TASK_ID}}' => $this->taskId]),
                                'url' => (($request->isHttps()) ? 'https://' : 'http://')
                                    . Path::normalize($request->getHttpHost() . SITE_DIR
                                        . 'task' . $this->taskId . '-' . $this->iBlockId) . '/'
                            ]);
                            unset($fb);
                        }
                    }
                } catch (\Exception $e) {
                    if (count($files) > 0) {
                        foreach ($files as $k => $file) {
                            \CFile::Delete($file);
                            unset($files[$k]);
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function myOffer()
    {
        $result = [];

        if ($this->obj !== null && $this->taskId > 0 && $this->userId > 0) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => ['*'],
                        'filter' => [
                            '=UF_USER_ID' => $this->userId,
                            '=UF_TASK_ID' => $this->taskId
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

        return $result;
    }
}