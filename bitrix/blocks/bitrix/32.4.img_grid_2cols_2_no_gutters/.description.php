<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

use \Bitrix\Main\Localization\Loc;

return array(
	'block' => array(
		'name' => Loc::getMessage('LANDING_BLOCK_32.4.IMG_GRID_2_COLS_2_NO_GUTTERS_NAME'),
		'section' => array('image'),
		'dynamic' => false,
	),
	'cards' => array(),
	'nodes' => array(
		'.landing-block-node-img-small' => array(
			'name' => Loc::getMessage('LANDING_BLOCK_32.4.IMG_GRID_2_COLS_2_NO_GUTTERS_NODES_LANDINGBLOCKNODEIMG2'),
			'type' => 'img',
			'dimensions' => array('width' => 1080),
			'allowInlineEdit' => false,
		),
		'.landing-block-node-img-big' => array(
			'name' => Loc::getMessage('LANDING_BLOCK_32.4.IMG_GRID_2_COLS_2_NO_GUTTERS_NODES_LANDINGBLOCKNODEIMG1'),
			'type' => 'img',
			'dimensions' => array('width' => 1080),
			'allowInlineEdit' => false,
		),
	),
	'style' => array(
		'.landing-block-node-img-container-left-top' => array(
			'name' => Loc::getMessage('LANDING_BLOCK_32.4.IMG_GRID_2_COLS_2_NO_GUTTERS_NODES_LANDINGBLOCKNODEIMG'),
			'type' => 'animation',
		),
		'.landing-block-node-img-container-left-bottom' => array(
			'name' => Loc::getMessage('LANDING_BLOCK_32.4.IMG_GRID_2_COLS_2_NO_GUTTERS_NODES_LANDINGBLOCKNODEIMG'),
			'type' => 'animation',
		),
		'.landing-block-node-img-container-right' => array(
			'name' => Loc::getMessage('LANDING_BLOCK_32.4.IMG_GRID_2_COLS_2_NO_GUTTERS_NODES_LANDINGBLOCKNODEIMG'),
			'type' => 'animation',
		),
	),
	'assets' => array(
		'ext' => array('landing_gallery_cards'),
	),
);