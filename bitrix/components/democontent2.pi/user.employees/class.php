<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 15.01.2019
 * Time: 10:57
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
class UserSettings extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        global $APPLICATION;

        if ($USER->IsAuthorized()) {
            $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
            $us = new \Democontent2\Pi\User(intval($USER->GetID()), 0);


            $this->arResult['USER'] = $us->get();

            //если сотрудник  - у него значит нету компании / значит посылаем нахер
            if( $this->arResult['USER']['UF_MODERATION_ACCESS'] == ""){
                LocalRedirect( "/user/settings/" , true);
            }

//            getEmployeesDetail
//            getCompanyEmployees

            global $USER;
            $ar = array(0 => $USER->GetID());
            $currentCompany = $us->getCompanyManager($ar);
            if($currentCompany[0]['UF_STATUS_MODERATION'] == 2) {
                $this->arResult['COMPANY_EMPLOYEES'] = $us->getCompanyEmployees();
            }else{
                $this->arResult['NOTMODERATIONCOMPAANY'] = "Y";
            }

            $APPLICATION->AddChainItem('Сотрудники' , '/user/employees/');

   
            if ($request->get('addUEmployees')) {

                    $us->addUserCompany();
            }else{
                $this->includeComponentTemplate();
            }
//            if ($request->isPost()) {
//
//                if ($request->getPostList()->get('addUEmployees')) {
//
//                    $us->addUserCompany();
//                    return false;
//                }
//
//
//
//
//            }else{
////                $this->includeComponentTemplate();
//            }

        }else{
            LocalRedirect(SITE_DIR . '/', true);
        }
    }
}



