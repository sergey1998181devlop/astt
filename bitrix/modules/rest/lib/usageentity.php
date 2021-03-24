<?php
namespace Bitrix\Rest;

use Bitrix\Main;
use \Bitrix\Main\DB\SqlQueryException;

/**
 * Class UsageEntityTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> ENTITY_TYPE string(1) mandatory
 * <li> ENTITY_ID int mandatory
 * <li> ENTITY_CODE string(255) mandatory
 * <li> SUB_ENTITY_TYPE string(1) optional
 * <li> SUB_ENTITY_NAME string(255) optional
 * </ul>
 *
 * @package Bitrix\Rest
 **/
class UsageEntityTable extends Main\Entity\DataManager
{

	const ENTITY_TYPE_APPLICATION = 'A';
	const ENTITY_TYPE_WEBHOOK = 'W';

	const SUB_ENTITY_TYPE_METHOD = 'M';
	const SUB_ENTITY_TYPE_EVENT = 'E';
	const SUB_ENTITY_TYPE_PLACEMENT = 'P';
	const SUB_ENTITY_TYPE_ROBOT = 'R';
	const SUB_ENTITY_TYPE_ACTIVITY = 'A';
	const SUB_ENTITY_TYPE_CONFIGURATION = 'C';

	protected static $info = array();

	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_rest_usage_entity';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true
			),
			'ENTITY_TYPE' => array(
				'data_type' => 'string',
				'required' => true,
				'values' => array(
					self::ENTITY_TYPE_APPLICATION,
					self::ENTITY_TYPE_WEBHOOK
				),
				'validation' => array(
					__CLASS__,
					'validateEntityType'
				)
			),
			'ENTITY_ID' => array(
				'data_type' => 'integer',
				'required' => true
			),
			'ENTITY_CODE' => array(
				'data_type' => 'string',
				'validation' => array(
					__CLASS__,
					'validateEntityCode'
				)
			),
			'SUB_ENTITY_TYPE' => array(
				'data_type' => 'string',
				'values' => array(
					self::SUB_ENTITY_TYPE_METHOD,
					self::SUB_ENTITY_TYPE_EVENT,
					self::SUB_ENTITY_TYPE_PLACEMENT,
					self::SUB_ENTITY_TYPE_ROBOT,
					self::SUB_ENTITY_TYPE_ACTIVITY,
					self::SUB_ENTITY_TYPE_CONFIGURATION,
				),
				'validation' => array(
					__CLASS__,
					'validateSubEntityType'
				)
			),
			'SUB_ENTITY_NAME' => array(
				'data_type' => 'string',
				'validation' => array(
					__CLASS__,
					'validateSubEntityName'
				)
			),
		);
	}

	/**
	 * @param string $entityType
	 * @param string $entityId
	 * @return array
	 */
	protected static function getEntityInfo($entityType, $entityId)
	{
		$key = $entityType.'|'.$entityId;
		if (!isset(static::$info[$key]))
		{
			if ($entityType == self::ENTITY_TYPE_APPLICATION)
			{
				$appInfo = AppTable::getByClientId($entityId);
				static::$info[$key] = array(
					'ENTITY_ID' => $appInfo['ID'],
					'ENTITY_CODE' => $appInfo['CLIENT_ID'],
				);
			}
			else
			{
				static::$info[$key] = array(
					'ENTITY_ID' => $entityId,
					'ENTITY_CODE' => '',
				);
			}
		}
		return static::$info[$key];
	}

	/**
	 * Returns entity autogenerated ID.
	 *
	 * @param string $entityType
	 * @param string $entityId
	 * @param string $subEntityType
	 * @param string $subEntityName
	 * @return int|false
	 * @throws SqlQueryException
	 */
	public static function register($entityType, $entityId, $subEntityType, $subEntityName)
	{
		$entity = static::getEntityInfo($entityType, $entityId);

		$res = static::getList(array(
			'filter' => array(
				'ENTITY_TYPE' => $entityType,
				'ENTITY_ID' => $entity['ENTITY_ID'],
				'SUB_ENTITY_TYPE' => $subEntityType,
				'SUB_ENTITY_NAME' => $subEntityName,
			),
			'select' => array('ID'),
			'limit' => 1,
		));

		if ($element = $res->fetch())
		{
			return $element['ID'];
		}

		$newEntity = array(
			'ENTITY_TYPE' => $entityType,
			'ENTITY_ID' => $entity['ENTITY_ID'],
			'ENTITY_CODE' => $entity['ENTITY_CODE'],
			'SUB_ENTITY_TYPE' => $subEntityType,
			'SUB_ENTITY_NAME' => $subEntityName
		);

		try
		{
			$res = static::add($newEntity);
			return $res->isSuccess() ? $res->getId() : false;
		}
		catch (SqlQueryException $e)
		{
			if (strpos($e->getMessage(), 'Duplicate entry') !== false)
			{
				//Try one more time
				$res = static::add($newEntity);
				return $res->isSuccess() ? $res->getId() : false;
			}
			throw $e;
		}
	}

	/**
	 * Returns validators for ENTITY_TYPE field.
	 *
	 * @return array
	 */
	public static function validateEntityType()
	{
		return array(new Main\Entity\Validator\Length(null, 1),);
	}

	/**
	 * Returns validators for ENTITY_CODE field.
	 *
	 * @return array
	 */
	public static function validateEntityCode()
	{
		return array(new Main\Entity\Validator\Length(null, 255),);
	}

	/**
	 * Returns validators for SUB_ENTITY_TYPE field.
	 *
	 * @return array
	 */
	public static function validateSubEntityType()
	{
		return array(new Main\Entity\Validator\Length(null, 1),);
	}

	/**
	 * Returns validators for SUB_ENTITY_NAME field.
	 *
	 * @return array
	 */
	public static function validateSubEntityName()
	{
		return array(new Main\Entity\Validator\Length(null, 255),);
	}
}
