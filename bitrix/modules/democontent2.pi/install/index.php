<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 07.01.2019
 * Time: 14:54
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
global $MESS;
IncludeModuleLangFile(__FILE__);

Class democontent2_pi extends CModule
{
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $PARTNER_NAME;
    var $PARTNER_URI;
    var $MODULE_GROUP_RIGHTS = "Y";
    var $MODULE_ID = "democontent2.pi";

    function democontent2_pi()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage('DSPI_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('DSPI_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = 'pixel365';
        $this->PARTNER_URI = 'https://pixel365.ru';
    }


    function InstallDB($install_wizard = true)
    {

    }

    function printArray($arr)
    {
        $output = "\$arUrlRewrite = array(\n";

        foreach ($arr as $val) {
            $output .= "\tarray(\n";
            foreach ($val as $key1 => $val1)
                $output .= "\t\t\"" . EscapePHPString($key1) . "\" => \"" . EscapePHPString($val1) . "\",\n";
            $output .= "\t),\n";
        }

        $output .= ");\n";

        return $output;
    }

    function UnInstallDB($arParams = Array())
    {
        UnRegisterModule($this->MODULE_ID);

        UnRegisterModuleDependences(
            'main',
            'OnAfterUserAuthorize',
            $this->MODULE_ID,
            'Democontent2\Pi\Events\Auth',
            'handler'
        );
        UnRegisterModuleDependences(
            'iblock',
            'OnAfterIBlockElementAdd',
            $this->MODULE_ID,
            'Democontent2\Pi\Events\Elements\Add',
            "handler"
        );
        UnRegisterModuleDependences(
            'iblock',
            'OnAfterIBlockElementDelete',
            $this->MODULE_ID,
            'Democontent2\Pi\Events\Elements\Delete',
            "handler"
        );
        UnRegisterModuleDependences(
            'iblock',
            'OnAfterIBlockElementUpdate',
            $this->MODULE_ID,
            'Democontent2\Pi\Events\Elements\Update',
            "handler"
        );
        UnRegisterModuleDependences(
            'search',
            'BeforeIndex',
            $this->MODULE_ID,
            'Democontent2\Pi\Events\BeforeIndex',
            "handler"
        );
        UnRegisterModuleDependences(
            'search',
            'OnSearchGetURL',
            $this->MODULE_ID,
            'Democontent2\Pi\Events\Search',
            "handler"
        );
        UnRegisterModuleDependences(
            'main',
            'OnBeforeProlog',
            $this->MODULE_ID,
            'Democontent2\Pi\Events\BeforeProlog',
            'handler'
        );
        UnRegisterModuleDependences(
            'iblock',
            'OnBeforeIBlockAdd',
            $this->MODULE_ID,
            'Democontent2\Pi\Events\Iblocks\BeforeAdd',
            "handler"
        );
        UnRegisterModuleDependences(
            'iblock',
            'OnAfterIBlockAdd',
            $this->MODULE_ID,
            'Democontent2\Pi\Events\Iblocks\Add',
            "handler"
        );
        UnRegisterModuleDependences(
            'main',
            'OnEpilog',
            $this->MODULE_ID,
            'Democontent2\Pi\Events\Iblocks\Add',
            'ep'
        );
        UnRegisterModuleDependences(
            'iblock',
            'OnIBlockDelete',
            $this->MODULE_ID,
            'Democontent2\Pi\Events\Iblocks\Delete',
            "handler"
        );
        UnRegisterModuleDependences(
            'iblock',
            'OnAfterIBlockUpdate',
            $this->MODULE_ID,
            'Democontent2\Pi\Events\Iblocks\Update',
            "handler"
        );
        UnRegisterModuleDependences(
            'iblock',
            'OnAfterIBlockSectionAdd',
            $this->MODULE_ID,
            'Democontent2\Pi\Events\Sections\Add',
            "handler"
        );
        UnRegisterModuleDependences(
            'iblock',
            'OnAfterIBlockSectionDelete',
            $this->MODULE_ID,
            'Democontent2\Pi\Events\Sections\Delete',
            "handler"
        );
        UnRegisterModuleDependences(
            'iblock',
            'OnAfterIBlockSectionUpdate',
            $this->MODULE_ID,
            'Democontent2\Pi\Events\Sections\Update',
            "handler"
        );

        UnRegisterModuleDependences(
            'main',
            'OnPanelCreate',
            $this->MODULE_ID,
            'Democontent2\Pi\Events\Panel',
            "handler"
        );
        UnRegisterModuleDependences(
            'main',
            'OnEndBufferContent',
            $this->MODULE_ID,
            'Democontent2\Pi\Events\Content',
            "handler"
        );

        try {
            \Bitrix\Main\Config\Option::delete($this->MODULE_ID);
        } catch (\Bitrix\Main\ArgumentNullException $e) {
        }

        try {
            $types = \CEventType::GetList();
            while ($typesRes = $types->Fetch()) {
                switch ($typesRes['EVENT_NAME']) {
                    case 'DSPI_NEW_OFFER':
                    case 'DSPI_NEW_PAYMENT':
                    case 'DSPI_NEW_USER':
                    case 'DSPI_RESTORE_PASSWORD':
                    case 'DSPI_NEW_MODERATION':
                    case 'DSPI_UPDATE_MODERATION':
                    case 'DSPI_NEW_COMPLAIN':
                    case 'DSPI_OWNER_STAGE_END':
                    case 'DSPI_EXECUTOR_STAGE_END':
                    case 'DSPI_EXECUTOR_TASK_END':
                    case 'DSPI_TASK_COMPLAIN':
                    case 'DSPI_STAGE_COMPLAIN':
                    case 'DSPI_TASK_CLOSED':
                    case 'DSPI_APPLY_EXECUTOR':
                    case 'DSPI_EXECUTOR_CANCEL':
                    case 'DSPI_SET_EXECUTOR':
                    case 'DSPI_TASK_ADD_RESPONSE':
                    case 'DSPI_EDIT_REVIEW':
                    case 'DSPI_REVIEW_ADD':
                        $msg = \CEventMessage::GetList($by = 'ID', $order = 'desc', ['TYPE_ID' => $typesRes['EVENT_NAME']]);
                        while ($msgRes = $msg->Fetch()) {
                            \CEventMessage::Delete($msgRes['ID']);
                        }
                        \CEventType::Delete($typesRes['EVENT_NAME']);
                        break;
                }
            }
        } catch (\Exception $e) {

        }

        try {
            $userTypeEntity = new \CUserTypeEntity();
            $ufTypes = [
                'UF_DSPI_DOCUMENTS',
                'UF_DSPI_EXECUTOR',
                'UF_DSPI_MOD_OFF',
                'UF_DSPI_CITY',
                'UF_DSPI_SAFECROW_ID',
                'UF_DSPI_LIMIT',
                'UF_DSPI_API_KEY',
                'UF_DSPI_RATING'
            ];
            foreach ($ufTypes as $type) {
                $getType = \CUserTypeEntity::GetList(
                    [],
                    [
                        'FIELD_NAME' => $type
                    ]
                )->Fetch();
                if (isset($getType['ID'])) {
                    if (intval($getType['ID']) > 0) {
                        $userTypeEntity->Delete($getType['ID']);
                    }
                }
            }
        } catch (\Exception $e) {

        }

        try {
            if (\Bitrix\Main\IO\Directory::isDirectoryExists(
                \Bitrix\Main\IO\Path::normalize(
                    \Bitrix\Main\Application::getDocumentRoot() . '/bitrix/wizards/democontent2/pi'
                )
            )) {
                \Bitrix\Main\IO\Directory::deleteDirectory(\Bitrix\Main\IO\Path::normalize(
                    \Bitrix\Main\Application::getDocumentRoot() . '/bitrix/wizards/democontent2/pi'
                ));
            }

            if (\Bitrix\Main\IO\Directory::isDirectoryExists(
                \Bitrix\Main\IO\Path::normalize(
                    \Bitrix\Main\Application::getDocumentRoot() . '/bitrix/components/democontent2.pi'
                )
            )) {
                \Bitrix\Main\IO\Directory::deleteDirectory(\Bitrix\Main\IO\Path::normalize(
                    \Bitrix\Main\Application::getDocumentRoot() . '/bitrix/components/democontent2.pi'
                ));
            }

            if (\Bitrix\Main\IO\Directory::isDirectoryExists(
                \Bitrix\Main\IO\Path::normalize(
                    \Bitrix\Main\Application::getDocumentRoot() . '/bitrix/templates/democontent2.pi'
                )
            )) {
                \Bitrix\Main\IO\Directory::deleteDirectory(\Bitrix\Main\IO\Path::normalize(
                    \Bitrix\Main\Application::getDocumentRoot() . '/bitrix/templates/democontent2.pi'
                ));
            }

            if (\Bitrix\Main\IO\Directory::isDirectoryExists(
                \Bitrix\Main\IO\Path::normalize(
                    \Bitrix\Main\Application::getDocumentRoot() . '/local/dspi'
                )
            )) {
                \Bitrix\Main\IO\Directory::deleteDirectory(\Bitrix\Main\IO\Path::normalize(
                    \Bitrix\Main\Application::getDocumentRoot() . '/local/dspi'
                ));
            }

            if (\Bitrix\Main\IO\File::isFileExists(
                \Bitrix\Main\IO\Path::normalize(
                    \Bitrix\Main\Application::getDocumentRoot() . '/local/democontent2_pi_prolog.php'
                )
            )) {
                \Bitrix\Main\IO\File::deleteFile(
                    \Bitrix\Main\IO\Path::normalize(
                        \Bitrix\Main\Application::getDocumentRoot() . '/local/democontent2_pi_prolog.php'
                    )
                );
            }

            if (\Bitrix\Main\IO\File::isFileExists(
                \Bitrix\Main\IO\Path::normalize(
                    \Bitrix\Main\Application::getDocumentRoot() . '/local/democontent2_pi_template.php'
                )
            )) {
                \Bitrix\Main\IO\File::deleteFile(
                    \Bitrix\Main\IO\Path::normalize(
                        \Bitrix\Main\Application::getDocumentRoot() . '/local/democontent2_pi_template.php'
                    )
                );
            }

            if (\Bitrix\Main\IO\File::isFileExists(
                \Bitrix\Main\IO\Path::normalize(
                    \Bitrix\Main\Application::getDocumentRoot() . '/local/cron/remove_temp_tasks.php'
                )
            )) {
                \Bitrix\Main\IO\File::deleteFile(
                    \Bitrix\Main\IO\Path::normalize(
                        \Bitrix\Main\Application::getDocumentRoot() . '/local/cron/remove_temp_tasks.php'
                    )
                );
            }

            if (\Bitrix\Main\IO\File::isFileExists(Bitrix\Main\IO\Path::normalize(\Bitrix\Main\Application::getDocumentRoot() . "/urlrewrite.php"))) {
                $arUrlRewrite = [];
                include(\Bitrix\Main\IO\Path::normalize(\Bitrix\Main\Application::getDocumentRoot() . "/urlrewrite.php"));

                if (is_array($arUrlRewrite) && !empty($arUrlRewrite) && count($arUrlRewrite) > 0) {
                    foreach ($arUrlRewrite as $k => $v) {
                        if (isset($v['SOLUTION'])) {
                            if ($v['SOLUTION'] == 'democontent2.pi') {
                                unset($arUrlRewrite[$k]);
                            }
                        }
                    }

                    $f = fopen(\Bitrix\Main\IO\Path::normalize(\Bitrix\Main\Application::getDocumentRoot() . "/urlrewrite.php"), "w");
                    fwrite($f, "<" . "?\n" . $this->printArray($arUrlRewrite) . "\n?" . ">");
                    fclose($f);
                    bx_accelerator_reset();
                }
            }
        } catch (\Exception $e) {

        }

        BXClearCache(true, '/democontent2.pi');

        return true;
    }

    function InstallEvents()
    {
        return true;
    }

    function UnInstallEvents()
    {
        return true;
    }

    function InstallFiles()
    {
        return true;
    }

    function InstallPublic()
    {
        return true;
    }

    function UnInstallFiles()
    {
        return true;
    }

    function DoInstall()
    {
        $this->InstallDB(true);
        $this->InstallFiles();
        $this->InstallEvents();
        $this->InstallPublic();
    }

    function DoUninstall()
    {
        $this->UnInstallDB();
        $this->UnInstallFiles();
        $this->UnInstallEvents();
    }
}