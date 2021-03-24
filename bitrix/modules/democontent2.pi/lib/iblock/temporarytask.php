<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 28.01.2019
 * Time: 17:10
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Iblock;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\Type\DateTime;
use Democontent2\Pi\EventManager;
use Democontent2\Pi\Hl;
use Democontent2\Pi\Logger;
use Democontent2\Pi\Order;
use Democontent2\Pi\User;

class TemporaryTask
{
    const TABLE_NAME = 'Democontentpitemporary';

    protected $userId = 0;
    protected $hash = '';
    protected $data = [];
    protected $paymentRedirect = '';

    private $obj = null;

    /**
     * TemporaryTask constructor.
     */
    public function __construct()
    {
        $hl = new Hl(static::TABLE_NAME);
        if ($hl->obj !== null) {
            $this->obj = $hl->obj;
        } else {
            $className = ToUpper(end(explode('\\', __CLASS__)));
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
                    'UF_HASH' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => ''],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_HASH')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_HASH')]
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
                    ],
                    'UF_IP' => [
                        'Y',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => ''],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IP')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_IP')]
                        ]
                    ],
                    'UF_CREATED_AT' => [
                        'Y',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => ''],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')]
                        ]
                    ]
                ],
                [
                    'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_IP` VARCHAR(32);',
                    'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_HASH` VARCHAR(32);',
                    'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_DATA` LONGTEXT;',
                ],
                [
                    ['UF_USER_ID'],
                    ['UF_HASH'],
                    ['UF_IP'],
                    ['UF_CREATED_AT']
                ],
                Loc::getMessage($className . '_IBLOCK_NAME')
            );
            if ($add) {
                $this->__construct();
            }
        }
    }

    /**
     * @return string
     */
    public function getPaymentRedirect()
    {
        return $this->paymentRedirect;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
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

        if ($this->obj !== null) {
            try {
                $obj = $this->obj;
                $filter = [];

                if ($this->userId > 0) {
                    $filter['=UF_USER_ID'] = $this->userId;
                }

                if (strlen($this->hash) == 32) {
                    $filter['=UF_HASH'] = $this->hash;
                }

                if (count($filter) > 0) {
                    $get = $obj::getList(
                        [
                            'select' => ['*'],
                            'filter' => $filter,
                            'limit' => 1
                        ]
                    );
                    while ($res = $get->fetch()) {
                        $result = $res;
                    }
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    public function add()
    {
        if ($this->obj !== null) {
            try {
                $get = $this->get();
                if (count($get) > 0) {
                    $data = unserialize($get['UF_DATA']);
                    if (isset($data['files']) && count($data['files']) > 0) {
                        foreach ($data['files'] as $file) {
                            \CFile::Delete($file);
                        }
                    }

                    if (isset($data['hidden_files']) && count($data['hidden_files']) > 0) {
                        foreach ($data['hidden_files'] as $file) {
                            \CFile::Delete($file);
                        }
                    }
                    $this->remove($get['ID']);
                }

                $ip = Application::getInstance()->getContext()->getServer()->get('REMOTE_ADDR');
                $obj = $this->obj;
                $add = $obj::add(
                    [
                        'UF_USER_ID' => $this->userId,
                        'UF_HASH' => $this->hash,
                        'UF_DATA' => serialize($this->data),
                        'UF_IP' => (strlen($ip)) ? $ip : '0.0.0.0',
                        'UF_CREATED_AT' => DateTime::createFromTimestamp(time())
                    ]
                );

                if ($add->isSuccess()) {

                }
            } catch (\Exception $e) {

            }
        }
    }

    public function restore($userId)
    {
        $result = 0;
        if ($this->obj !== null) {
            try {
                $get = $this->get();
                if (count($get) > 0) {
                    $el = new \CIBlockElement();
                    $us = new User($userId, 0);
                    $userParams = $us->get();

                    $data = unserialize($get['UF_DATA']);
                    $data['ACTIVE_FROM'] = DateTime::createFromTimestamp(time());
                    $data['ACTIVE_TO'] = DateTime::createFromTimestamp((time() + (86400 * intval(Option::get(DSPI, 'item_period')))));
                    $data['PROPERTY_VALUES']['__hidden_user'] = intval($userParams['ID']);

                    if (intval($userParams['UF_DSPI_MOD_OFF']) > 0) {
                        $data['PROPERTY_VALUES']['__hidden_moderation'] = 0;
                    }

                    $files = [];
                    $hiddenFiles = [];
                    $tempFiles = $data['files'];
                    $tempHiddenFiles = $data['hidden_files'];
                    $stages = $data['stages'];

                    unset($data['files'], $data['hidden_files'], $data['stages']);

                    if (count($tempFiles) > 0) {
                        $i = 0;
                        foreach ($tempFiles as $fileId => $fileDescription) {
                            $fileArray = \CFile::MakeFileArray($fileId);
                            $fileArray['description'] = $fileDescription;

                            $files['n' . $i] = [
                                'VALUE' => $fileArray,
                                'DESCRIPTION' => $fileDescription
                            ];
                            $i++;
                        }
                    }

                    if (count($tempHiddenFiles) > 0) {
                        $i = 0;
                        foreach ($tempHiddenFiles as $fileId => $fileDescription) {
                            $fileArray = \CFile::MakeFileArray($fileId);
                            $fileArray['description'] = $fileDescription;

                            $hiddenFiles['n' . $i] = [
                                'VALUE' => $fileArray,
                                'DESCRIPTION' => $fileDescription
                            ];
                            $i++;
                        }
                    }

                    if (count($files) > 0) {
                        $data['PROPERTY_VALUES']['files'] = $files;
                    }

                    if (count($hiddenFiles) > 0) {
                        $data['PROPERTY_VALUES']['hidden_files'] = $hiddenFiles;
                    }

                    $add = $el->Add($data);
                    if ($add) {
                        $result = intval($add);

                        if (count($stages) > 0) {
                            $item = new Item();
                            $item->setItemId(intval($add));
                            $item->addStages($stages);

                            $userItemsCount = 0;
                            $iblock = new Iblock();
                            $iblocksList = $iblock->getAllIds();
                            foreach ($iblocksList as $iblockId) {
                                $tableName = ucfirst(ToLower(preg_replace('/[^a-zA-Z]/', '', md5($iblockId))));
                                $hl = new Hl($tableName);
                                if ($hl->obj !== null) {
                                    $obj = $hl->obj;
                                    try {
                                        $get = $obj::getList(
                                            [
                                                'select' => ['CNT'],
                                                'runtime' => [
                                                    new ExpressionField('CNT', 'COUNT(`ID`)')
                                                ],
                                                'filter' => [
                                                    '=UF_IBLOCK_ID' => $iblockId,
                                                    '=UF_USER_ID' => intval($userParams['ID'])
                                                ]
                                            ]
                                        );
                                        while ($res = $get->fetch()) {
                                            $userItemsCount += intval($res['CNT']);
                                        }
                                    } catch (\Exception $e) {
                                    }
                                }
                            }

                            if (isset($userParams['UF_DSPI_LIMIT']) && intval($userParams['UF_DSPI_LIMIT']) > 0) {
                                if (($userItemsCount + 1) > intval(Option::get(DSPI, 'free_limit'))
                                    && intval(Option::get(DSPI, 'upper_limit_cost')) > 0) {
                                    \CIBlockElement::SetPropertyValuesEx(
                                        intval($add),
                                        intval($data['IBLOCK_ID']),
                                        [
                                            '__hidden_payed' => 0
                                        ]
                                    );
                                    $el->Update(
                                        intval($add),
                                        [
                                            'TIMESTAMP_X' => DateTime::createFromTimestamp(time())
                                        ]
                                    );

                                    $order = new Order(intval($userParams['ID']));
                                    $order->setType('new_item');
                                    $order->setSum(intval(Option::get(DSPI, 'upper_limit_cost')));
                                    $order->setDescription(
                                        Loc::getMessage(
                                            'ITEM_ORDER_DESCRIPTION',
                                            [
                                                '#ID#' => intval($add)
                                            ]
                                        )
                                    );
                                    $order->setAdditionalParams(
                                        [
                                            'iBlockId' => intval($data['IBLOCK_ID']),
                                            'itemId' => intval($add),
                                            'userId' => intval($userParams['ID']),
                                            'period' => intval(Option::get(DSPI, 'item_period'))
                                        ]
                                    );
                                    $order->make();
                                    if ($order->getRedirect()) {
                                        $this->paymentRedirect = $order->getRedirect();
                                    }
                                }
                            } else {
                                if (($userItemsCount + 1) > intval(Option::get(DSPI, 'free_limit'))
                                    && intval(Option::get(DSPI, 'upper_limit_cost')) > 0) {
                                    \CIBlockElement::SetPropertyValuesEx(
                                        intval($add),
                                        intval($data['IBLOCK_ID']),
                                        [
                                            '__hidden_payed' => 0
                                        ]
                                    );
                                    $el->Update(
                                        intval($add),
                                        [
                                            'TIMESTAMP_X' => DateTime::createFromTimestamp(time())
                                        ]
                                    );

                                    $order = new Order(intval($userParams['ID']));
                                    $order->setType('new_item');
                                    $order->setSum(intval(Option::get(DSPI, 'upper_limit_cost')));
                                    $order->setDescription(
                                        Loc::getMessage(
                                            'ITEM_ORDER_DESCRIPTION',
                                            [
                                                '#ID#' => intval($add)
                                            ]
                                        )
                                    );
                                    $order->setAdditionalParams(
                                        [
                                            'iBlockId' => intval($data['IBLOCK_ID']),
                                            'itemId' => intval($add),
                                            'userId' => intval($userParams['ID']),
                                            'period' => intval(Option::get(DSPI, 'item_period'))
                                        ]
                                    );
                                    $order->make();
                                    if ($order->getRedirect()) {
                                        $this->paymentRedirect = $order->getRedirect();
                                    }
                                }
                            }

                            try {
                                Logger::add(
                                    intval($userParams['ID']),
                                    'addItemSuccess',
                                    [
                                        'userId' => intval($userParams['ID']),
                                        'iBlockId' => intval($data['IBLOCK_ID']),
                                        'id' => intval($add),
                                        'userItemsCount' => ($userItemsCount + 1)
                                    ]
                                );

                                $event = new EventManager(
                                    'addItemSuccess',
                                    [
                                        'userId' => intval($userParams['ID']),
                                        'iBlockId' => intval($data['IBLOCK_ID']),
                                        'id' => intval($add),
                                        'userItemsCount' => ($userItemsCount + 1)
                                    ]
                                );
                                $event->execute();
                            } catch (\Exception $e) {
                            }
                        }
                    }

                    /*if (count($tempFiles) > 0) {
                        foreach ($tempFiles as $fileId => $fileDescription) {
                            \CFile::Delete($fileId);
                        }
                    }

                    if (count($tempHiddenFiles) > 0) {
                        foreach ($tempHiddenFiles as $fileId => $fileDescription) {
                            \CFile::Delete($fileId);
                        }
                    }*/

                    $this->remove($get['ID']);
                }
            } catch (\Exception $e) {

            }
        }

        return $result;
    }

    public function remove($id)
    {
        if ($this->obj !== null) {
            try {
                $obj = $this->obj;
                $remove = $obj::delete($id);

                if ($remove->isSuccess()) {

                }
            } catch (\Exception $e) {

            }
        }
    }

    public function removeExpires($days = 30)
    {
        if ($this->obj !== null) {
            try {
                $obj = $this->obj;
                $get = $obj::getList(
                    [
                        'select' => ['ID', 'UF_DATA'],
                        'filter' => [
                            '<=UF_CREATED_AT' => DateTime::createFromTimestamp((time() - (86400 * $days)))
                        ]
                    ]
                );
                while ($res = $get->fetch()) {
                    $data = unserialize($res['UF_DATA']);
                    $this->remove($res['ID']);

                    if (isset($data['files']) && count($data['files']) > 0) {
                        foreach ($data['files'] as $file) {
                            \CFile::Delete($file);
                        }
                    }

                    if (isset($data['hidden_files']) && count($data['hidden_files']) > 0) {
                        foreach ($data['hidden_files'] as $file) {
                            \CFile::Delete($file);
                        }
                    }
                }
            } catch (\Exception $e) {

            }
        }
    }
}