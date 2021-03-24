<?php
/**
 * Author: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Skype: pixel365
 * WebSite: semagin.com
 * Date: 20.04.2016
 * Time: 10:27
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (\Bitrix\Main\Loader::includeModule('iblock') && \Bitrix\Main\Loader::includeModule('highloadblock')) {
    function hlBlockId($type)
    {
        $result = 0;
        try {
            $connection = \Bitrix\Main\Application::getConnection();
            $hlBlockId = $connection->query(
                'SELECT `ID` FROM `' . \Bitrix\Highloadblock\HighloadBlockTable::getTableName() . '` WHERE `NAME`="' . $type . '"'
            )->fetch();

            if (intval($hlBlockId['ID'])) {
                $result = intval($hlBlockId['ID']);
            }
        } catch (\Exception $e) {

        }

        return $result;
    }

    $params = array(
        'democontentpiiblockidmeta' => array(
            'fields' => array(
                'UF_IBLOCK_ID' => array(
                    'Y',
                    'integer'
                ),
                'UF_H1' => array(
                    'N',
                    'string'
                ),
                'UF_TITLE' => array(
                    'N',
                    'string'
                ),
                'UF_DESCRIPTION' => array(
                    'N',
                    'string'
                ),
                'UF_KEYWORDS' => array(
                    'N',
                    'string'
                ),
                'UF_FULL_DESCRIPTION' => array(
                    'N',
                    'string'
                ),
            ),
            'alter' => array(
                'ALTER TABLE `democontentpiiblockidmeta` MODIFY `UF_FULL_DESCRIPTION` LONGTEXT;',
            ),
            'indexes' => array(
                array('UF_IBLOCK_ID')
            )
        ),
        'democontentpiiblocktypemeta' => array(
            'fields' => array(
                'UF_IBLOCK_TYPE' => array(
                    'Y',
                    'string'
                ),
                'UF_H1' => array(
                    'N',
                    'string'
                ),
                'UF_TITLE' => array(
                    'N',
                    'string'
                ),
                'UF_DESCRIPTION' => array(
                    'N',
                    'string'
                ),
                'UF_KEYWORDS' => array(
                    'N',
                    'string'
                ),
                'UF_FULL_DESCRIPTION' => array(
                    'N',
                    'string'
                ),
            ),
            'alter' => array(
                'ALTER TABLE `democontentpiiblocktypemeta` MODIFY `UF_FULL_DESCRIPTION` LONGTEXT;',
            ),
            'indexes' => array(
                array('UF_IBLOCK_TYPE')
            )
        ),
    );

    foreach ($params as $hlTableName => $paramsValue) {
        try {
            $add = \Bitrix\Highloadblock\HighloadBlockTable::add(
                array(
                    'NAME' => ucfirst($hlTableName),
                    'TABLE_NAME' => $hlTableName,
                )
            );
            if ($add->isSuccess()) {
                $userTypeEntity = new \CUserTypeEntity();
                foreach ($paramsValue['fields'] as $fieldName => $fieldValue) {
                    $aUserField = array(
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
                        'SETTINGS' => array(),
                    );

                    $aUserField = array_merge(
                        $aUserField,
                        array(
                            'SETTINGS' => array('DEFAULT_VALUE' => ''),
                            'EDIT_FORM_LABEL' => array('ru' => GetMessage($fieldName)),
                            'LIST_COLUMN_LABEL' => array('ru' => GetMessage($fieldName)),
                        )
                    );

                    $userTypeEntity->Add($aUserField);
                }

                $connection = \Bitrix\Main\Application::getConnection();

                if (isset($paramsValue['alter'])) {
                    if (count($paramsValue['alter'])) {
                        foreach ($paramsValue['alter'] as $alter) {
                            try {
                                $connection->query($alter);
                            } catch (\Bitrix\Main\DB\SqlQueryException $e) {

                            }
                        }
                    }
                }

                if (isset($paramsValue['indexes'])) {
                    if (count($paramsValue['indexes'])) {
                        foreach ($paramsValue['indexes'] as $index) {
                            try {
                                $connection->createIndex($hlTableName, ToLower(implode('_', $index)), $index);
                            } catch (\Bitrix\Main\DB\SqlQueryException $e) {

                            }
                        }
                    }
                }

                if ($add->getId()) {
                    try {
                        $res = \Bitrix\Highloadblock\HighloadBlockLangTable::getList(
                            array(
                                'select' => array(
                                    'ID'
                                ),
                                'filter' => array(
                                    'ID' => intval($add->getId()),
                                    'LID' => 'ru'
                                ),
                                'limit' => 1
                            )
                        );
                        while ($row = $res->fetch()) {
                            \Bitrix\Highloadblock\HighloadBlockLangTable::delete($row['ID']);
                        }

                        \Bitrix\Highloadblock\HighloadBlockLangTable::add(
                            array(
                                'ID' => intval($add->getId()),
                                'LID' => 'ru',
                                'NAME' => GetMessage($hlTableName)
                            )
                        );
                    } catch (\Exception $e) {

                    }
                }
            }
        } catch (Exception $e) {

        }
    }
} else {
    return;
}
