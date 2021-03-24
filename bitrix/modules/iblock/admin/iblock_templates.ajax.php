<?php
define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);
define('BX_SECURITY_SHOW_MESSAGE', true);
define("PUBLIC_AJAX_MODE", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/admin_tools.php");
IncludeModuleLangFile(__FILE__);
header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);

if(!CModule::includeModule("iblock") || !CModule::includeModule('fileman'))
{
	die();
}
CUtil::jSPostUnescape();
if (check_bitrix_sessid())
{
	if ($_REQUEST["ENTITY_TYPE"] === "B")
	{
		$ipropTemplates = new \Bitrix\Iblock\InheritedProperty\IblockTemplates($_REQUEST["ENTITY_ID"]);
		$arFields = array(
			"NAME" => $_POST["NAME"],
			"CODE" => $_POST["CODE"],
			"DESCRIPTION" => $_POST["DESCRIPTION"],
		);
	}
	elseif ($_REQUEST["ENTITY_TYPE"] === "S")
	{
		$ipropTemplates = new \Bitrix\Iblock\InheritedProperty\SectionTemplates($_REQUEST["IBLOCK_ID"], $_REQUEST["ENTITY_ID"]);
		$arFields = array(
			"IBLOCK_ID" => $_REQUEST["IBLOCK_ID"],
			"IBLOCK_SECTION_ID" => $_REQUEST["IBLOCK_SECTION_ID"],
			"NAME" => $_POST["NAME"],
			"CODE" => $_POST["CODE"],
			"DESCRIPTION" => $_POST["DESCRIPTION"],
		);
		foreach ($_POST as $key => $value)
		{
			if (mb_substr($key, 0, 3) === "UF_")
				$arFields[$key] = $value;
		}
	}
	elseif ($_REQUEST["ENTITY_TYPE"] === "E")
	{
		$ipropTemplates = new \Bitrix\Iblock\InheritedProperty\ElementTemplates($_REQUEST["IBLOCK_ID"], $_REQUEST["ENTITY_ID"]);

		$section_id = 0;
		if (isset($_POST["IBLOCK_ELEMENT_SECTION_ID"]) && (int)$_POST["IBLOCK_ELEMENT_SECTION_ID"] > 0)
		{
			$section_id = (int)$_POST["IBLOCK_ELEMENT_SECTION_ID"];
		}
		elseif (!empty($_POST["IBLOCK_SECTION"]) && is_array($_POST["IBLOCK_SECTION"]))
		{
			$postSections = array_filter($_POST["IBLOCK_SECTION"], "strlen");
			if (!empty($postSections))
				$section_id = min($postSections);
			unset($postSections);
		}

		$arFields = array(
			"IBLOCK_ID" => $_REQUEST["IBLOCK_ID"],
			"IBLOCK_SECTION_ID" => $section_id,
			"NAME" => $_POST["NAME"],
			"CODE" => $_POST["CODE"],
			"PREVIEW_TEXT" => $_POST["PREVIEW_TEXT"],
			"DETAIL_TEXT" => $_POST["DETAIL_TEXT"],
		);
	}
	else
	{
		$ipropTemplates = null;
		$arFields = array();
	}

	if ($ipropTemplates)
	{
		$values = $ipropTemplates->getValuesEntity();
		$entity = $values->createTemplateEntity();
		$entity->setFields($arFields);

		$templates = $ipropTemplates->findTemplates();
		if (is_array($_POST["IPROPERTY_TEMPLATES"]))
		{
			foreach ($_POST["IPROPERTY_TEMPLATES"] as $TEMPLATE_NAME => $TEMPLATE_VALUE)
			{
				$templates[$TEMPLATE_NAME] = array(
					"TEMPLATE" => \Bitrix\Iblock\Template\Helper::convertArrayToModifiers($TEMPLATE_VALUE),
				);
			}
		}

		$result = array();
		foreach ($templates as $TEMPLATE_NAME => $templateInfo)
		{
			$result[] = array(
				"id" => $TEMPLATE_NAME,
				"value" => \Bitrix\Main\Text\HtmlFilter::encode(
					\Bitrix\Iblock\Template\Engine::process($entity, $templateInfo["TEMPLATE"])
				),
			);
		}
		
		if ($_REQUEST["ENTITY_TYPE"] === "E")
		{
			$html = ' ';
			$firstSection = 0;
			$inSelect = false;
			$sections = $_POST["IBLOCK_SECTION"]? array_filter($_POST["IBLOCK_SECTION"], "strlen"): array();
			$html .= '<select name="IBLOCK_ELEMENT_SECTION_ID" id="IBLOCK_ELEMENT_SECTION_ID" onchange="InheritedPropertiesTemplates.updateInheritedPropertiesValues(false, true)">';
			if ($sections)
			{
				$sectionList = CIBlockSection::GetList(
					array("left_margin"=>"asc"),
					array("=ID"=>$sections),
					false,
					array("ID", "NAME")
				);
				while ($section = $sectionList->Fetch())
				{
					if (!$firstSection)
						$firstSection = $section["ID"];

					if ($section_id == $section["ID"])
					{
						$inSelect = true;
						$html .= '<option value="'.htmlspecialcharsbx($section["ID"]).'" selected>'.htmlspecialcharsEx($section["NAME"]).'</option>';
					}
					else
					{
						$html .= '<option value="'.htmlspecialcharsbx($section["ID"]).'">'.htmlspecialcharsEx($section["NAME"]).'</option>';
					}
				}
			}
			$html .= '</select><br>';

			$arIBlock = CIBlock::GetArrayById($_REQUEST["IBLOCK_ID"]);

			$arFields = array(
				"LANG_DIR" => "",
				"LID" => $arIBlock["LID"],
				"ID" => $_REQUEST["ID"],
				"IBLOCK_ID" => $_REQUEST["IBLOCK_ID"],
				"CODE" => $_POST["CODE"],
				"EXTERNAL_ID" => $_POST["XML_ID"],
				"IBLOCK_TYPE_ID" => CIBlock::GetArrayById($_REQUEST["IBLOCK_ID"], "IBLOCK_TYPE_ID"),
				"IBLOCK_CODE" => CIBlock::GetArrayById($_REQUEST["IBLOCK_ID"], "CODE"),
				"IBLOCK_EXTERNAL_ID" => CIBlock::GetArrayById($_REQUEST["IBLOCK_ID"], "XML_ID"),
				"IBLOCK_SECTION_ID" => $inSelect? $section_id: $firstSection,
				
			);

			if ($arIBlock["CANONICAL_PAGE_URL"])
			{
				$html .= GetMessage("IB_TA_CANONICAL_PAGE_URL")."<br>";
				$page_url = CIBlock::ReplaceDetailUrl($arIBlock["CANONICAL_PAGE_URL"], $arFields, true, "E");
				$html .= '<a href="'.htmlspecialcharsbx($page_url).'" target="_blank">'.htmlspecialcharsEx($page_url).'</a>';
			}
			else
			{
				$page_url = CIBlock::ReplaceDetailUrl($arIBlock["DETAIL_PAGE_URL"], $arFields, true, "E");
				$html .= htmlspecialcharsEx($page_url);
			}

			$result[] = array(
				"htmlId" => "RESULT_IBLOCK_ELEMENT_SECTION_ID",
				"value" => $html,
				"hiddenId" => "IBLOCK_ELEMENT_SECTION_ID",
				"hiddenValue" => $arFields["IBLOCK_SECTION_ID"],
			);
		}

		echo CUtil::PhpToJSObject($result);
	}
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_after.php");
?>
