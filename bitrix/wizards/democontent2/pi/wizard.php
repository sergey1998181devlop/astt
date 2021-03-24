<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/install/wizard_sol/wizard.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
    function InitStep()
    {
        parent::InitStep();

        $this->SetNextStep("site_settings");
        $wizard =& $this->GetWizard();
        $wizard->solutionName = "democontent2.pi";
    }
}

class SiteSettingsStep extends CSiteSettingsWizardStep
{
    function InitStep()
    {
        $wizard =& $this->GetWizard();
        $wizard->solutionName = "democontent2.pi";

        $siteID = $wizard->GetVar("siteID");
        if (COption::GetOptionString("democontent2.pi", "wizard_installed", "N", $siteID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
            $this->SetNextStep("data_install");

        parent::InitStep();

        $templateID = $wizard->GetVar("templateID");
        $themeID = $wizard->GetVar($templateID . "_themeID");
    }

    function ShowStep()
    {
        $wizard =& $this->GetWizard();

        $firstStep = COption::GetOptionString("main", "wizard_first" . substr($wizard->GetID(), 7) . "_" . $wizard->GetVar("siteID"), false, $wizard->GetVar("siteID"));

        $styleMeta = 'style="display:block"';
        if ($firstStep == "Y") $styleMeta = 'style="display:none"';

        $this->content .= $this->ShowCheckboxField(
            "installDemoData",
            "Y",
            array(
                "id" => "install-demo-data",
                "checked" => true,
                "disabled" => true,
                "onClick" => "if(this.checked == true){document.getElementById('bx_metadata').style.display='block';}else{document.getElementById('bx_metadata').style.display='none';}"
            )
        );
        $this->content .= '<label for="install-demo-data">' . GetMessage("WIZARD_DEMODATA") . '</label><br />';

        $formName = $wizard->GetFormName();
        $installCaption = $this->GetNextCaption();
        $nextCaption = GetMessage("NEXT_BUTTON");
    }

    function OnPostForm()
    {
        $wizard =& $this->GetWizard();
        $res = $this->SaveFile("siteLogo", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 210, "max_width" => 60, "make_preview" => "Y"));
    }
}

class DataInstallStep extends CDataInstallWizardStep
{
    function CorrectServices(&$arServices)
    {
        $wizard =& $this->GetWizard();
        if ($wizard->GetVar("installDemoData") != "Y") {
        }
    }
}

class FinishStep extends CFinishWizardStep
{
    function InitStep()
    {
        $this->SetStepID("finish");
        $this->SetNextStep("finish");
        $this->SetTitle(GetMessage("FINISH_STEP_TITLE"));
        $this->SetNextCaption(GetMessage("wiz_go"));
    }

    function ShowStep()
    {
        $wizard =& $this->GetWizard();

        $siteID = WizardServices::GetCurrentSiteID($wizard->GetVar("siteID"));
        $rsSites = CSite::GetByID($siteID);
        $siteDir = "/";
        if ($arSite = $rsSites->Fetch())
            $siteDir = $arSite["DIR"];

        $wizard->SetFormActionScript(str_replace("//", "/", $siteDir . "/?finish"));

        $this->CreateNewIndex();

        COption::SetOptionString("main", "wizard_solution", $wizard->solutionName, false, $siteID);

        $this->content .= GetMessage("FINISH_STEP_CONTENT");

        if ($wizard->GetVar("installDemoData") == "Y")
            $this->content .= GetMessage("FINISH_STEP_REINDEX");

    }
}