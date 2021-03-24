<?php

/**
 * User: Mydak Seva
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


            //беру текущего выбранного сотрудника и подгружаю у него все заявки
            $this->arResult['COMPANY_EMPLOYEES_DETAIL'] = $us->getEmployeesDetail();
            $APPLICATION->AddChainItem('Сотрудники компании ' , '/user/employees/');
            if(!empty( $this->arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['ID'])){
                $APPLICATION->AddChainItem($this->arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['NAME']);
//                $this->arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['NAME']
            }



//если модератор запросил удаление , удаляю по id ,  но с начала ищу его в текущей компании
            //если пользователь не найден в этой компании , получаю сообщение в    $result['message'] = 'errorDelete';
            if(!empty($_POST['UpdateUsEmployeesID'])){
                $result = $us->UpdateUsEmployees();
            }
            if (!empty($_POST['DeleteNum'])) {
                $result = $us->DeleteNum();
            }


            $this->includeComponentTemplate();


            //если пользователя не существует , редиректю на стр сотрудников
            if(empty($this->arResult['COMPANY_EMPLOYEES_DETAIL']['USER_DETAIL'][0]['ID'])){
                LocalRedirect("/user/employees/");
            }
        }else{
            LocalRedirect(SITE_DIR . '/', true);
        }
    }
}



