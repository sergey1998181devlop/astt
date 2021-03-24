<?php
/**
 * Author: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Skype: pixel365
 * WebSite: semagin.com
 * Date: 18.04.2016
 * Time: 13:54
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!defined("WIZARD_SITE_ID"))
    return;

if (!CModule::IncludeModule("iblock"))
    return;

$moduleId = 'democontent2.pi';
$mManager = new \Bitrix\Main\ModuleManager();
$eventType = new CEventType();
$eventMessage = new CEventMessage();
$wUtil = new CWizardUtil();

function getIBlockIdByType($type, $code)
{
    $result = 0;

    $getIBlock = CIBlock::GetList(
        array(),
        array(
            'TYPE' => $type,
            'ACTIVE' => 'Y',
            "CODE" => $code
        ), true
    );
    while ($ibl = $getIBlock->Fetch()) {
        $result = intval($ibl['ID']);
        break;
    }

    return $result;
}

CopyDirFiles(
    $_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/democontent2.pi/install/public',
    WIZARD_SITE_PATH,
    true,
    true,
    false
);

CopyDirFiles(
    $_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/democontent2.pi/install/components',
    $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components",
    true,
    true,
    false
);

CopyDirFiles(
    $_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/democontent2.pi/install/templates',
    $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates",
    true,
    true,
    false
);

if (\Bitrix\Main\IO\Directory::isDirectoryExists(WIZARD_SITE_PATH . 'search/')) {
    \Bitrix\Main\IO\Directory::deleteDirectory(WIZARD_SITE_PATH . 'search/');
}

$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "index.php", array("INDEX_TITLE" => GetMessage('INDEX_TITLE')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . ".bottom.menu.php", array("SITE_DIR" => WIZARD_SITE_DIR));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . ".bottom.menu.php", array("PARTNERS" => GetMessage('PARTNERS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . ".bottom.menu.php", array("SELLERS" => GetMessage('SELLERS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . ".bottom.menu.php", array("BUYERS" => GetMessage('BUYERS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . ".top.menu.php", array("SITE_DIR" => WIZARD_SITE_DIR));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . ".top.menu.php", array("ABOUT" => GetMessage('ABOUT')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . ".top.menu.php", array("FAQ" => GetMessage('FAQ')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . ".top.menu.php", array("AGREEMENT" => GetMessage('AGREEMENT')));

/*$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "about/index.php", array("LOREM_IPSUM" => GetMessage('LOREM_IPSUM')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "about/index.php", array("ABOUT" => GetMessage('ABOUT')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "about/.section.php", array("ABOUT" => GetMessage('ABOUT')));

$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "agreement/index.php", array("LOREM_IPSUM" => GetMessage('LOREM_IPSUM')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "agreement/index.php", array("AGREEMENT" => GetMessage('AGREEMENT')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "agreement/.section.php", array("AGREEMENT" => GetMessage('AGREEMENT')));*/

$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "buyers/index.php", array("LOREM_IPSUM" => GetMessage('LOREM_IPSUM')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "buyers/index.php", array("BUYERS" => GetMessage('BUYERS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "buyers/.section.php", array("BUYERS" => GetMessage('BUYERS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "buyers/.left.menu.php", array("SAFETY" => GetMessage('SAFETY')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "buyers/.left.menu.php", array("RECOMENDS" => GetMessage('RECOMENDS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "buyers/.left.menu.php", array("HOWTO" => GetMessage('HOWTO')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "buyers/howto/index.php", array("LOREM_IPSUM" => GetMessage('LOREM_IPSUM')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "buyers/howto/index.php", array("HOWTO" => GetMessage('HOWTO')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "buyers/howto/.section.php", array("HOWTO" => GetMessage('HOWTO')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "buyers/recomends/index.php", array("LOREM_IPSUM" => GetMessage('LOREM_IPSUM')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "buyers/recomends/index.php", array("RECOMENDS" => GetMessage('RECOMENDS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "buyers/recomends/.section.php", array("RECOMENDS" => GetMessage('RECOMENDS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "buyers/safety/index.php", array("LOREM_IPSUM" => GetMessage('LOREM_IPSUM')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "buyers/safety/index.php", array("SAFETY" => GetMessage('SAFETY')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "buyers/safety/.section.php", array("SAFETY" => GetMessage('SAFETY')));

$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "partners/index.php", array("LOREM_IPSUM" => GetMessage('LOREM_IPSUM')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "partners/index.php", array("PARTNERS" => GetMessage('PARTNERS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "partners/.section.php", array("PARTNERS" => GetMessage('PARTNERS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "partners/.left.menu.php", array("SAFETY" => GetMessage('SAFETY')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "partners/.left.menu.php", array("RECOMENDS" => GetMessage('RECOMENDS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "partners/.left.menu.php", array("HOWTO" => GetMessage('HOWTO')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "partners/howto/index.php", array("LOREM_IPSUM" => GetMessage('LOREM_IPSUM')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "partners/howto/index.php", array("HOWTO" => GetMessage('HOWTO')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "partners/howto/.section.php", array("HOWTO" => GetMessage('HOWTO')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "partners/recomends/index.php", array("LOREM_IPSUM" => GetMessage('LOREM_IPSUM')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "partners/recomends/index.php", array("RECOMENDS" => GetMessage('RECOMENDS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "partners/recomends/.section.php", array("RECOMENDS" => GetMessage('RECOMENDS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "partners/safety/index.php", array("LOREM_IPSUM" => GetMessage('LOREM_IPSUM')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "partners/safety/index.php", array("SAFETY" => GetMessage('SAFETY')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "partners/safety/.section.php", array("SAFETY" => GetMessage('SAFETY')));

$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "sellers/index.php", array("LOREM_IPSUM" => GetMessage('LOREM_IPSUM')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "sellers/index.php", array("SELLERS" => GetMessage('SELLERS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "sellers/.section.php", array("SELLERS" => GetMessage('SELLERS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "sellers/.left.menu.php", array("SAFETY" => GetMessage('SAFETY')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "sellers/.left.menu.php", array("RECOMENDS" => GetMessage('RECOMENDS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "sellers/.left.menu.php", array("HOWTO" => GetMessage('HOWTO')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "sellers/howto/index.php", array("LOREM_IPSUM" => GetMessage('LOREM_IPSUM')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "sellers/howto/index.php", array("HOWTO" => GetMessage('HOWTO')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "sellers/howto/.section.php", array("HOWTO" => GetMessage('HOWTO')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "sellers/recomends/index.php", array("LOREM_IPSUM" => GetMessage('LOREM_IPSUM')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "sellers/recomends/index.php", array("RECOMENDS" => GetMessage('RECOMENDS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "sellers/recomends/.section.php", array("RECOMENDS" => GetMessage('RECOMENDS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "sellers/safety/index.php", array("LOREM_IPSUM" => GetMessage('LOREM_IPSUM')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "sellers/safety/index.php", array("SAFETY" => GetMessage('SAFETY')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "sellers/safety/.section.php", array("SAFETY" => GetMessage('SAFETY')));

try {
    $wUtil->ReplaceMacros(WIZARD_SITE_PATH . "user/index.php", array("MY_PROFILE" => GetMessage('MY_PROFILE')));
    $wUtil->ReplaceMacros(WIZARD_SITE_PATH . "shares/index.php", array("SHARES" => GetMessage('SHARES')));
    $wUtil->ReplaceMacros(WIZARD_SITE_PATH . "order/index.php", array("ORDER" => GetMessage('ORDER')));
    $wUtil->ReplaceMacros(WIZARD_SITE_PATH . "news/index.php", array("NEWS" => GetMessage('NEWS')));
    $wUtil->ReplaceMacros(WIZARD_SITE_PATH . "favorites/index.php", array("FAVORITES" => GetMessage('FAVORITES')));
    $wUtil->ReplaceMacros(WIZARD_SITE_PATH . "cart/index.php", array("CART" => GetMessage('CART')));
    $wUtil->ReplaceMacros(WIZARD_SITE_PATH . "delivery/index.php", array("DELIVERY" => GetMessage('DELIVERY')));
    $wUtil->ReplaceMacros(WIZARD_SITE_PATH . "delivery/index.php", array("DELIVERY_CONTENT" => GetMessage('DELIVERY_CONTENT')));
    $wUtil->ReplaceMacros(WIZARD_SITE_PATH . "privacy/index.php", array("PRIVACY_TITLE" => GetMessage('PRIVACY_TITLE')));
    $wUtil->ReplaceMacros(WIZARD_SITE_PATH . "privacy/index.php", array("PRIVACY_CONTENT" => GetMessage('PRIVACY_CONTENT')));
} catch (\Exception $e) {

}

$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "tasks/index.php", array("ALL_TASKS" => GetMessage('ALL_TASKS')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "tasks/.section.php", array("ALL_TASKS" => GetMessage('ALL_TASKS')));

$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "faq/index.php", array("FAQ" => GetMessage('FAQ')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "faq/index.php", array("IBLOCK_ID" => getIBlockIdByType('__democontent2_pi', 'faq')));
$wUtil->ReplaceMacros(WIZARD_SITE_PATH . "faq/.section.php", array("FAQ" => GetMessage('FAQ')));

$wUtil->ReplaceMacros(
    $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/democontent2.pi/inc/404/default.php",
    array(
        "INC_404_ERROR" => GetMessage('INC_404_ERROR'),
        "INC_404_ERROR_1" => GetMessage('INC_404_ERROR_1'),
        "INC_404_ERROR_2" => GetMessage('INC_404_ERROR_2'),
    )
);

$wUtil->ReplaceMacros(
    $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/democontent2.pi/inc/footer/social.php",
    array(
        "social" => GetMessage('social'),
    )
);

$wUtil->ReplaceMacros(
    $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/democontent2.pi/inc/header/menu.php",
    array(
        "EXECUTORS" => GetMessage('EXECUTORS'),
        "TASKS" => GetMessage('TASKS'),
    )
);

$wUtil->ReplaceMacros(
    $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/democontent2.pi/inc/footer/copyright.php",
    array(
        "INC_FOOTER_COPYRIGHT_1" => GetMessage('INC_FOOTER_COPYRIGHT_1'),
        "INC_FOOTER_COPYRIGHT_2" => GetMessage('INC_FOOTER_COPYRIGHT_2'),
        "INC_FOOTER_COPYRIGHT_3" => GetMessage('INC_FOOTER_COPYRIGHT_3'),
        "INC_FOOTER_COPYRIGHT_4" => GetMessage('INC_FOOTER_COPYRIGHT_4'),
        "INC_FOOTER_COPYRIGHT_5" => GetMessage('INC_FOOTER_COPYRIGHT_5'),
        "INC_FOOTER_COPYRIGHT_6" => GetMessage('INC_FOOTER_COPYRIGHT_6'),
    )
);
$wUtil->ReplaceMacros(
    $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/democontent2.pi/inc/index/advantages.php",
    array(
        "advatnages0" => GetMessage('advatnages0'),
        "advatnages1" => GetMessage('advatnages1'),
        "advatnages2" => GetMessage('advatnages2'),
        "advatnages3" => GetMessage('advatnages3'),
        "advatnages4" => GetMessage('advatnages4'),
    )
);
$wUtil->ReplaceMacros(
    $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/democontent2.pi/inc/index/search.php",
    array(
        "index_search1" => GetMessage('index_search1'),
        "index_search2" => GetMessage('index_search2'),
    )
);

$wUtil->ReplaceMacros(
    $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/democontent2.pi/inc/create/steps-create.php",
    array(
        "steps_create1" => GetMessage('steps_create1'),
        "steps_create2" => GetMessage('steps_create2'),
        "steps_create3" => GetMessage('steps_create3'),
        "steps_create4" => GetMessage('steps_create4'),
        "steps_create5" => GetMessage('steps_create5'),
    )
);

$cSite = new CSite();
$siteUpd = $cSite->Update(WIZARD_SITE_ID, array(
        'TEMPLATE' => array(
            array(
                "CONDITION" => "",
                "SORT" => 150,
                "TEMPLATE" => "democontent2.pi"
            )
        )
    )
);

COption::SetOptionString("main", "wizard_template_id", $moduleId, false, WIZARD_SITE_ID);

if ($mManager->isModuleInstalled($moduleId)) {
    //$mManager->registerModule($moduleId);

    RegisterModuleDependences(
        'main',
        'OnEpilog',
        $moduleId,
        'Democontent2\Pi\Events\Iblocks\Add',
        'ep'
    );

    \Bitrix\Main\Config\Option::set($moduleId, 'siteId', WIZARD_SITE_ID);
    \Bitrix\Main\Config\Option::set($moduleId, 'siteDir', WIZARD_SITE_DIR);
    \Bitrix\Main\Config\Option::set($moduleId, 'sign', md5(microtime(true) . randString()));
    \Bitrix\Main\Config\Option::set($moduleId, 'currency_name', GetMessage('CURRENCY_NAME'));
    \Bitrix\Main\Config\Option::set($moduleId, 'phone_code_mask', 7);
    \Bitrix\Main\Config\Option::set($moduleId, 'max_files', 10);
    \Bitrix\Main\Config\Option::set($moduleId, 'max_file_size', 10);
    \Bitrix\Main\Config\Option::set($moduleId, 'item_period', 30);
    \Bitrix\Main\Config\Option::set($moduleId, 'free_limit', 1000);
    \Bitrix\Main\Config\Option::set($moduleId, 'moderation_new', 1);
    \Bitrix\Main\Config\Option::set($moduleId, 'moderation_update', 1);
    \Bitrix\Main\Config\Option::set($moduleId, 'upper_limit_cost', 50);
    \Bitrix\Main\Config\Option::set($moduleId, 'quickly_option_cost', 100);
    \Bitrix\Main\Config\Option::set($moduleId, 'quickly_option_period', 7);
    \Bitrix\Main\Config\Option::set($moduleId, 'defaultMap', 'yandex');
    \Bitrix\Main\Config\Option::set($moduleId, 'chatEnabled', 0);
    \Bitrix\Main\Config\Option::set($moduleId, 'startBalance', 100);
}

try {
    $types = [
        'DSPI_NEW_OFFER' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#EMAIL#'
        ),
        'DSPI_NEW_PAYMENT' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#DEFAULT_EMAIL_FROM#'
        ),
        'DSPI_NEW_USER' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#LOGIN#'
        ),
        'DSPI_RESTORE_PASSWORD' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#EMAIL#'
        ),
        'DSPI_NEW_MODERATION' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#DEFAULT_EMAIL_FROM#'
        ),
        'DSPI_UPDATE_MODERATION' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#DEFAULT_EMAIL_FROM#'
        ),
        'DSPI_NEW_COMPLAIN' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#DEFAULT_EMAIL_FROM#'
        ),
        'DSPI_OWNER_STAGE_END' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#EMAIL_TO#'
        ),
        'DSPI_EXECUTOR_STAGE_END' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#EMAIL_TO#'
        ),
        'DSPI_EXECUTOR_TASK_END' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#EMAIL_TO#'
        ),
        'DSPI_TASK_COMPLAIN' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#EMAIL_TO#'
        ),
        'DSPI_STAGE_COMPLAIN' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#EMAIL_TO#'
        ),
        'DSPI_TASK_CLOSED' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#EMAIL_TO#'
        ),
        'DSPI_APPLY_EXECUTOR' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#EMAIL_TO#'
        ),
        'DSPI_EXECUTOR_CANCEL' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#EMAIL_TO#'
        ),
        'DSPI_SET_EXECUTOR' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#EMAIL_TO#'
        ),
        'DSPI_TASK_ADD_RESPONSE' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#EMAIL_TO#'
        ),
        'DSPI_EDIT_REVIEW' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#EMAIL_TO#'
        ),
        'DSPI_REVIEW_ADD' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#EMAIL_TO#'
        ),
        'DSPI_SUBSCRIBE_SEND' => array
        (
            'FROM' => '#DEFAULT_EMAIL_FROM#',
            'TO' => '#EMAIL_TO#'
        ),
    ];
    $findTypes = array();

    foreach ($types as $typeId => $typeValue) {
        $eventTypeList = $eventType->GetList(
            array(
                'TYPE_ID' => $typeId
            )
        );
        while ($res = $eventTypeList->Fetch()) {
            if ($res['NAME']) {
                $findTypes[$typeId] = $res['ID'];
            }
        }

        if (!isset($findTypes[$typeId])) {
            $add = $eventType->Add(
                array(
                    'LID' => 'ru',
                    'EVENT_NAME' => $typeId,
                    'NAME' => GetMessage($typeId),
                    'DESCRIPTION' => GetMessage($typeId . '_MACROS'),
                )
            );

            if (intval($add)) {
                $site = new CSite();
                $siteList = $site->GetList(
                    $by = "sort", $order = "desc"
                );
                while ($res = $siteList->Fetch()) {
                    $eventMessage->Add(
                        array(
                            'ACTIVE' => 'Y',
                            'EVENT_NAME' => $typeId,
                            'LID' => $res['LID'],
                            'EMAIL_FROM' => $typeValue['FROM'],
                            'EMAIL_TO' => $typeValue['TO'],
                            'SUBJECT' => GetMessage($typeId . '_SUBJECT'),
                            'BODY_TYPE' => 'html',
                            'MESSAGE' => GetMessage($typeId . '_MESSAGE')
                        )
                    );
                }
            }
        }
    }
} catch (\Exception $e) {

}

BXClearCache(true, '/democontent2.pi');

if (\Bitrix\Main\Loader::includeModule($moduleId)) {
    $u = new CUser();
    $us = new \Democontent2\Pi\User(1, 0);
    $userParams = $us->get();

    if (!strlen($userParams['NAME'])) {
        $u->Update(
            1,
            array(
                'NAME' => 'Admin',
                'UF_DSPI_EXECUTOR' => 1,
                'UF_DSPI_DOCUMENTS' => 1,
            )
        );
    } else {
        $u->Update(
            1,
            array(
                'UF_DSPI_EXECUTOR' => 1,
                'UF_DSPI_DOCUMENTS' => 1,
            )
        );
    }

    try {
        new \Democontent2\Pi\VK\Queue();
    } catch (\Exception $e) {

    }

    $iBlockId = \Democontent2\Pi\Utils::getIBlockIdByType('__democontent2_pi', 'cities');
    if ($iBlockId > 0) {
        try {
            $prop = new CIBlockProperty();
            $prop->Add(
                array(
                    "NAME" => \Bitrix\Main\Localization\Loc::getMessage('BIG_CITY_PROP_NAME'),
                    "ACTIVE" => "Y",
                    "SORT" => "500",
                    "CODE" => "hidden_big",
                    "PROPERTY_TYPE" => "N",
                    "IBLOCK_ID" => $iBlockId
                )
            );
        } catch (\Exception $e) {
        }

        $get = CIBlockElement::GetList(
            array(
                'SORT' => 'ASC'
            ),
            array(
                'IBLOCK_ID' => $iBlockId
            ),
            false,
            false,
            array(
                'ID',
                'CODE',
                'IBLOCK_ID'
            )
        );
        while ($res = $get->GetNextElement()) {
            $f = $res->GetFields();
            switch (ToLower(trim($f['CODE']))) {
                case 'moscow':
                case 'moskva':
                    CIBlockElement::SetPropertyValuesEx(
                        $f['ID'],
                        $iBlockId,
                        array(
                            'hidden_big' => 1
                        )
                    );

                    try {
                        $u->Update(
                            1,
                            array(
                                'UF_DSPI_CITY' => intval($f['ID'])
                            )
                        );
                    } catch (\Exception $e) {

                    }
                    break;
            }
        }
    }

    try {
        $subscriptions = new \Democontent2\Pi\Profile\Subscriptions();
        $menu = new \Democontent2\Pi\Iblock\Menu();

        $subscriptions->setUserId(1);

        $getMenu = $menu->get();
        foreach ($getMenu as $k => $v) {
            if (isset($v['items'])) {
                foreach ($v['items'] as $item) {
                    $subscriptions->setIBlockType($k);
                    $subscriptions->setIBlockId($item['id']);
                    $subscriptions->add();
                }
            }
        }

        $account = new \Democontent2\Pi\Balance\Account(1);
        $account->create();
    } catch (\Exception $e) {

    }
}