<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'default.css',
	'js' => 'default.js',
	'rel' => [
		'main.polyfill.core',
	],
	'skip_core' => true,
];