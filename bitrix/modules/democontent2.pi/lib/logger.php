<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 07.01.2019
 * Time: 15:32
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
namespace Democontent2\Pi;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Democontent2\Pi\Exceptions\LoggerException;

class Logger
{
    const TABLE_NAME = 'Democontentpiactionlog';

    /**
     * @param int $userId
     * @param string $type
     * @param array $data
     */
    public static function add($userId = 0, $type = '', $data = [])
    {
        $className = ToUpper(end(explode('\\', __CLASS__)));
        try {
            $hl = new Hl(static::TABLE_NAME);
            if ($hl->obj !== null) {
                $obj = $hl->obj;
                $add = $obj::add(
                    [
                        'UF_CREATED_AT' => DateTime::createFromTimestamp(time()),
                        'UF_IP' => Application::getInstance()->getContext()->getServer()->get('REMOTE_ADDR'),
                        'UF_USER_ID' => $userId,
                        'UF_TYPE' => $type,
                        'UF_DATA' => serialize($data)
                    ]
                );
                if (!$add->isSuccess() && !$add->getId()) {
                    throw new LoggerException('Insert Error');
                }
            } else {
                throw new LoggerException('Object is NULL');
            }
        } catch (LoggerException $e) {
            switch ($e->getMessage()) {
                case 'Object is NULL':
                    $add = Hl::create(
                        ToLower(static::TABLE_NAME),
                        [
                            'UF_CREATED_AT' => [
                                'N',
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
                                'N',
                                'integer',
                                [
                                    'SETTINGS' => ['DEFAULT_VALUE' => 0,],
                                    'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
                                    'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_USER_ID')],
                                ]
                            ],
                            'UF_TYPE' => [
                                'N',
                                'string',
                                [
                                    'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                    'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TYPE')],
                                    'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_TYPE')],
                                ]
                            ],
                            'UF_DATA' => [
                                'N',
                                'string',
                                [
                                    'SETTINGS' => ['DEFAULT_VALUE' => '',],
                                    'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DATA')],
                                    'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage($className . '_UF_DATA')],
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
                            ['UF_TYPE']
                        ],
                        Loc::getMessage('LOGGER_IBLOCK_NAME')
                    );
                    if ($add) {
                        Logger::add($userId, $type, $data);
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