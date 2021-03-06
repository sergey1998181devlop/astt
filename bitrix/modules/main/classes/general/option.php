<?
##############################################
# Bitrix: SiteManager                        #
# Copyright (c) 2002-2006 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

global $MAIN_OPTIONS;
$MAIN_OPTIONS = array();
class CAllOption
{
	public static function err_mess()
	{
		return "<br>Class: CAllOption<br>File: ".__FILE__;
	}

	public static function GetOptionString($module_id, $name, $def="", $site=false, $bExactSite=false)
	{
		$v = null;

		try
		{
			if ($bExactSite)
			{
				$v = \Bitrix\Main\Config\Option::getRealValue($module_id, $name, $site);
				return $v === null ? false : $v;
			}

			$v = \Bitrix\Main\Config\Option::get($module_id, $name, $def, $site);
		}
		catch (\Bitrix\Main\ArgumentNullException $e)
		{

		}

		return $v;
	}

	public static function SetOptionString($module_id, $name, $value="", $desc=false, $site="")
	{
		\Bitrix\Main\Config\Option::set($module_id, $name, $value, $site);
		return true;
	}

	public static function RemoveOption($module_id, $name="", $site=false)
	{
		$filter = array();
		if (strlen($name) > 0)
			$filter["name"] = $name;
		if (strlen($site) > 0)
			$filter["site_id"] = $site;
		\Bitrix\Main\Config\Option::delete($module_id, $filter);
	}

	public static function GetOptionInt($module_id, $name, $def="", $site=false)
	{
		return intval(COption::GetOptionString($module_id, $name, $def, $site));
	}

	public static function SetOptionInt($module_id, $name, $value="", $desc="", $site="")
	{
		return COption::SetOptionString($module_id, $name, IntVal($value), $desc, $site);
	}
}

global $MAIN_PAGE_OPTIONS;
$MAIN_PAGE_OPTIONS = array();
class CAllPageOption
{
	public static function GetOptionString($module_id, $name, $def="", $site=false)
	{
		global $MAIN_PAGE_OPTIONS;

		if($site===false)
			$site = SITE_ID;

		if(isset($MAIN_PAGE_OPTIONS[$site][$module_id][$name]))
			return $MAIN_PAGE_OPTIONS[$site][$module_id][$name];
		elseif(isset($MAIN_PAGE_OPTIONS["-"][$module_id][$name]))
			return $MAIN_PAGE_OPTIONS["-"][$module_id][$name];
		return $def;
	}

	public static function SetOptionString($module_id, $name, $value="", $desc=false, $site="")
	{
		global $MAIN_PAGE_OPTIONS;

		if($site===false)
			$site = SITE_ID;
		if(strlen($site)<=0)
			$site = "-";

		$MAIN_PAGE_OPTIONS[$site][$module_id][$name] = $value;
		return true;
	}

	public static function RemoveOption($module_id, $name="", $site=false)
	{
		global $MAIN_PAGE_OPTIONS;

		if ($site === false)
		{
			foreach ($MAIN_PAGE_OPTIONS as $site => $temp)
			{
				if ($name == "")
					unset($MAIN_PAGE_OPTIONS[$site][$module_id]);
				else
					unset($MAIN_PAGE_OPTIONS[$site][$module_id][$name]);
			}
		}
		else
		{
			if ($name == "")
				unset($MAIN_PAGE_OPTIONS[$site][$module_id]);
			else
				unset($MAIN_PAGE_OPTIONS[$site][$module_id][$name]);
		}
	}

	public static function GetOptionInt($module_id, $name, $def="", $site=false)
	{
		return CPageOption::GetOptionString($module_id, $name, $def, $site);
	}

	public static function SetOptionInt($module_id, $name, $value="", $desc="", $site="")
	{
		return CPageOption::SetOptionString($module_id, $name, IntVal($value), $desc, $site);
	}
}
?>