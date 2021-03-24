<?php

/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 18.01.2019
 * Time: 17:19
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

class UserTasksComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;

        if ($USER->IsAuthorized()) {
            $items = new \Democontent2\Pi\Iblock\Items();
            $items->setUserId(intval($USER->GetID()));
            $items->setTtl(0);

            $this->arResult['ITEMS'] = $items->getByUserTasks();
//            debmes($this->arResult['ITEMS']);
            $this->arResult['EXECUTOR_ITEMS'] = $items->getWhereExecutor();

            $idCurrentUser = $USER->GetID();
            $rsUser = CUser::GetByID($idCurrentUser);
            $arUser = $rsUser->Fetch();
            $us = new \Democontent2\Pi\User( $idCurrentUser, 0);
            $this->arResult['CURRENT_USER'] = $arUser;
            //если модератор  -  собираю его сотрудников и собираю все их заявки
            if( $arUser['UF_MODERATION_ACCESS'] == "true"  || $arUser['UF_MODERATION_ACCESS'] == "false" ){
                $this->arResult['COMPANY_EMPLOYEES'] = $us->getCompanyEmployees();
            }


            $messTasksEmployees = [];
            foreach ($this->arResult['COMPANY_EMPLOYEES']['ITEMS_USERS'] as $idEmpl => $valEmpl){
                $messTasksEmployees[] = $valEmpl['ID'];
            }

//            $messTasksEmployees = array_reverse($messTasksEmployees);
            $this->arResult['TASKS_EMPLOYEES'] = $items->getByUserTasksMessEmpl(false , $messTasksEmployees);

//            debmes( $this->arResult['TASKS_EMPLOYEES']);

            $this->includeComponentTemplate();
        } else {
            LocalRedirect(SITE_DIR, true);
        }
    }
}