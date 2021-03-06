<?
/*
##############################################
# Bitrix: SiteManager                        #
# Copyright (c) 2004 Bitrix                  #
# http://www.bitrix.ru                       #
# mailto:admin@bitrix.ru                     #
##############################################
*/
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$FORM_RIGHT = $APPLICATION->GetGroupRight("form");
if($FORM_RIGHT<="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

CModule::IncludeModule("form");

ClearVars();

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/form/include.php");

$err_mess = "File: ".__FILE__."<br>Line: ";

/***************************************************************************
						   GET | POST processing
****************************************************************************/


$F_RIGHT = CForm::GetPermission($WEB_FORM_ID);

if ($F_RIGHT < 30 || !check_bitrix_sessid()) 
{
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
	die();
}

if ($_REQUEST['action'] == 'delete')
{
	if ($USER->CanDoOperation('edit_other_settings'))
	{
		$ID = intval($_REQUEST['ID']);
		
		$emessage = new CEventMessage();

		$DB->StartTransaction();
		if(!$emessage->Delete($ID))
		{
			$DB->Rollback();
		}
		else
			$DB->Commit();
	}
	
	die();
}

//$dbRes = CFormStatus::GetByID($STATUS_ID);
//$q = CForm::GetByID($WEB_FORM_ID);
//$arrForm = $q->Fetch();

$arTemplates = CFormStatus::SetMailTemplate($WEB_FORM_ID, $STATUS_ID, "Y", '', true);

IncludeModuleLangFile(__FILE__);
$strNote .= GetMessage("FORM_GENERATING_FINISHED")."<br>";

$arReturn = array(
	'NOTE' => $strNote,
	'TEMPLATES' => $arTemplates
);

//$APPLICATION->SetTitle(GetMessage("FORM_PAGE_TITLE"));
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php")
?>
<script>
_processData(<?=CUtil::PhpToJsObject($arReturn)?>);
</script>
<?
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php")
?>