<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/loader.bundle.css',
	'js' => 'dist/loader.bundle.js',
	'rel' => [
		'main.core',
	],
	'skip_core' => false,
];