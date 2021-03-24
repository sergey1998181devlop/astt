<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

use \Bitrix\Main\Localization\Loc;

return array(
	'block' => array(
		'name' => Loc::getMessage('LANDING_BLOCK_26_3_SEPARATOR_NAME'),
		'section' => array('separator'),
	),
	'cards' => array(),
	'nodes' => array(),
	'style' => array(
		'block' => [
			'type' => ['display', 'height-vh--md', 'fill-first', 'fill-second'],
		],
		'nodes' => [
		],
	),
);