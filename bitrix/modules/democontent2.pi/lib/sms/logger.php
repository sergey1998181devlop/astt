<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 17.09.2018
 * Time: 17:42
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\Sms;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\Encoding;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\Json;
use Democontent2\Pi\Hl;

class Logger
{
    const TABLE_NAME = 'Democontentpismslog';

    private $hlClass = null;
    private $params = [];
    private $error = '';
    private $gate = '';
    private $smsId = '';
    private $smsCost = '';
    private $smsCount = 0;
    private $text = '';
    private $phone = '';
    private $log = null;

    /**
     * Log constructor.
     * @param $params
     * @param $error
     * @param $gate
     */
    public function __construct($params, $error, $gate)
    {
        $this->params = $params;
        $this->error = $error;
        $this->gate = $gate;
        $this->smsId = $params['smsId'];
        $this->smsCount = $params['smsCount'];
        $this->smsCost = $params['smsCost'];
        $this->phone = $params['phone'];
        $this->text = $params['text'];

        $hl = new Hl(static::TABLE_NAME);
        $this->hlClass = $hl->obj;

        $this->save();
    }

    /**
     * @return null
     */
    public function getLog()
    {
        return $this->log;
    }

    private function save()
    {
        if ($this->hlClass !== null) {
            $obj = $this->hlClass;

            try {
                if (Application::isUtfMode()) {
                    $this->text = urldecode($this->text);
                } else {
                    $this->text = Encoding::convertEncodingToCurrent(urldecode($this->text));
                }

                $add = $obj::add(
                    [
                        'UF_ID' => $this->smsId,
                        'UF_TEXT' => $this->text,
                        'UF_QTY' => $this->smsCount,
                        'UF_COST' => $this->smsCost,
                        'UF_PHONE' => $this->phone,
                        'UF_ERROR' => $this->error,
                        'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                        'UF_PARAMS' => Json::encode($this->params),
                        'UF_GATE' => $this->gate
                    ]
                );
                if ($add->isSuccess()) {
                    $this->log = [
                        'id' => $add,
                        'params' => [
                            'UF_ID' => $this->smsId,
                            'UF_TEXT' => $this->text,
                            'UF_QTY' => $this->smsCount,
                            'UF_COST' => $this->smsCost,
                            'UF_PHONE' => $this->phone,
                            'UF_ERROR' => $this->error,
                            'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                            'UF_PARAMS' => Json::encode($this->params),
                            'UF_GATE' => $this->gate
                        ]
                    ];
                }
            } catch (ArgumentException $e) {
            } catch (\Exception $e) {
            }
        } else {
            $className = ToUpper(end(explode('\\', __CLASS__)));
            $add = Hl::create(
                ToLower(static::TABLE_NAME),
                [
                    'UF_ID' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ID')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ID')],
                        ]
                    ],
                    'UF_TEXT' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TEXT')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TEXT')],
                        ]
                    ],
                    'UF_QTY' => [
                        'N',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_QTY')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_QTY')],
                        ]
                    ],
                    'UF_COST' => [
                        'N',
                        'integer',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_COST')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_COST')],
                        ]
                    ],
                    'UF_PHONE' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PHONE')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PHONE')],
                        ]
                    ],
                    'UF_ERROR' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ERROR')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_ERROR')],
                        ]
                    ],
                    'UF_CREATED_AT' => [
                        'N',
                        'datetime',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_CREATED_AT')],
                        ]
                    ],
                    'UF_PARAMS' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PARAMS')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_PARAMS')],
                        ]
                    ],
                    'UF_GATE' => [
                        'N',
                        'string',
                        [
                            'SETTINGS' => ['DEFAULT_VALUE' => '',],
                            'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_GATE')],
                            'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_GATE')],
                        ]
                    ],
                ],
                [
                    'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_ID` VARCHAR(255);',
                    'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_PHONE` VARCHAR(32);',
                    'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_GATE` VARCHAR(32);',
                    'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_ERROR` LONGTEXT;',
                    'ALTER TABLE `' . ToLower(static::TABLE_NAME) . '` MODIFY `UF_PARAMS` LONGTEXT;',
                ],
                [
                    ['UF_ID'],
                    ['UF_GATE'],
                    ['UF_PHONE'],
                    ['UF_CREATED_AT'],
                ],
                Loc::getMessage('LOGGER_IBLOCK_NAME')
            );
            if ($add) {
                $this->save();
            }
        }
    }
}