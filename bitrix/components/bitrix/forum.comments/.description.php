<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => GetMessage("FORUM_COMMENTS"), 
	"DESCRIPTION" => GetMessage("FORUM_COMMENTS_DESCRIPTION"), 
	"ICON" => "/images/icon.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "communication", 
		"CHILD" => array(
			"ID" => "forum",
			"NAME" => GetMessage("FORUM"),
			"CHILD" => array(
				"ID" => "forum_public",
				"NAME" => GetMessage("FORUM_PUBLIC"),
			)
		)
	),
);
?>