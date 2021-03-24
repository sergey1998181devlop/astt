<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 07.01.2019
 * Time: 15:37
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi;

use Bitrix\Highloadblock\HighloadBlockLangTable;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
use Bitrix\Main\Application;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\SystemException;

use Bitrix\Highloadblock\HighloadBlockTable ;

class Hl
{
    public $obj = null;

    /**
     * Hl constructor.
     * @param $hl
     * @param int $ttl
     */
    public function __construct($hl, $ttl = 86400)
    {
        $hl = (string)$hl;
        $hlBlockId = Utils::hlBlockId($hl, $ttl);
        if ($hlBlockId) {
            try {
                $hlblock = HighloadBlockTable::getById($hlBlockId)->fetch();
                $entity = HighloadBlockTable::compileEntity($hlblock);
                $this->obj = $entity->getDataClass();
            } catch (SystemException $e) {
            }
        }
    }
    function GetEntityDataClass($HlBlockId) {
        if (empty($HlBlockId) || $HlBlockId < 1)
        {
            return false;
        }
        $hlblock = HLBT::getById($HlBlockId)->fetch();
        $entity = HLBT::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        return $entity_data_class;
    }
     function getListHigload( $idHigloadBlock , $insertProp = array() , $limit , $filter = array() ){

        \CModule::IncludeModule('highloadblock');

        $entity_data_class = Hl::GetEntityDataClass($idHigloadBlock);
        $rsData = $entity_data_class::getList(array(
            'select' => $insertProp,
            'order' => array('ID' => 'ASC'),
            'limit' => $limit,//ограничиваем выборку 10-ю элементами
            'filter' => $filter
        ));
        $data = [];
        while($el = $rsData->fetch()){

            $data[] = $el;
        }
        return $data;
    }
    public static function create($code, $fields, $alters = [], $indexes = [], $name = '')
    {
        $result = false;
        $params = [
            $code => [
                'fields' => $fields,
                'alter' => $alters,
                'indexes' => $indexes
            ]
        ];

        foreach ($params as $hlTableName => $paramsValue) {
            try {
                $add = HighloadBlockTable::add(
                    [
                        'NAME' => ucfirst($hlTableName),
                        'TABLE_NAME' => $hlTableName,
                    ]
                );
                if ($add->isSuccess()) {
                    if ($name && strlen($name) > 0) {
                        HighloadBlockLangTable::delete($add->getId());
                        HighloadBlockLangTable::add(
                            [
                                'ID' => $add->getId(),
                                'LID' => 'ru',
                                'NAME' => $name
                            ]
                        );
                    }

                    $result = true;
                    $userTypeEntity = new \CUserTypeEntity();
                    foreach ($paramsValue['fields'] as $fieldName => $fieldValue) {
                        $aUserField = [
                            'ENTITY_ID' => 'HLBLOCK_' . $add->getId(),
                            'FIELD_NAME' => $fieldName,
                            'USER_TYPE_ID' => $fieldValue[1],
                            'SORT' => 500,
                            'MULTIPLE' => 'N',
                            'MANDATORY' => $fieldValue[0],
                            'SHOW_FILTER' => 'N',
                            'SHOW_IN_LIST' => 'Y',
                            'EDIT_IN_LIST' => 'Y',
                            'IS_SEARCHABLE' => 'N',
                            'SETTINGS' => [],
                        ];

                        $aUserField = array_merge($aUserField, $fieldValue[2]);

                        $userTypeEntity->Add($aUserField);
                    }

                    $connection = Application::getConnection();

                    if (isset($paramsValue['alter'])) {
                        if (count($paramsValue['alter'])) {
                            foreach ($paramsValue['alter'] as $alter) {
                                try {
                                    $connection->query($alter);
                                } catch (SqlQueryException $e) {

                                }
                            }
                        }
                    }

                    if (isset($paramsValue['indexes'])) {
                        if (count($paramsValue['indexes'])) {
                            foreach ($paramsValue['indexes'] as $index) {
                                try {
                                    $connection->createIndex($hlTableName, ToLower(implode('_', $index)), $index);
                                } catch (SqlQueryException $e) {

                                }
                            }
                        }
                    }
                } else {
                    ErrorLog::add(0, end(explode('\\', __METHOD__)), $add->getErrorMessages());
                }
            } catch (\Exception $e) {
                ErrorLog::add(
                    0,
                    end(explode('\\', __METHOD__)),
                    [
                        $e->getCode(),
                        $e->getMessage(),
                        $e->getTraceAsString()
                    ]
                );
            }
        }

        return $result;
    }
}