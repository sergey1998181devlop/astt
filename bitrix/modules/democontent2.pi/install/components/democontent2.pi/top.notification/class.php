<?php

/**
 * User: Nazarkin Sergey
 * Email: nazarkin2017@mail.ru
 * Date: 07.10.2020
 * Time: 13:07
 */
class TopAuthComponentClass extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        global $APPLICATION;

        $this->arResult['AUTHORIZED'] = $USER->IsAuthorized();
        $this->arResult['EMAIL'] = ($this->arResult['AUTHORIZED']) ? $USER->GetEmail() : false;

        if (\Bitrix\Main\Loader::includeModule('democontent2.pi')) {
            if ($this->arResult['AUTHORIZED']) {
                $us = new \Democontent2\Pi\User($USER->GetID());
                $this->arResult['USER'] = $us->get();
            } else {
                $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
                if ($request->isPost()) {
                    if ($request->getPost('authEmail') && $request->getPost('authPassword')) {
                        $USER = new CUser();
                        $APPLICATION->arAuthResult = $USER->Login(
                            trim($request->getPost('authEmail')),
                            trim($request->getPost('authPassword')),
                            'Y'
                        );

                        \Democontent2\Pi\Logger::add(
                            (($USER->IsAuthorized()) ? intval($USER->GetID()) : 0),
                            'auth',
                            array(
                                (is_array($APPLICATION->arAuthResult)) ? array('message' => $APPLICATION->arAuthResult['MESSAGE'], 'login' => htmlspecialcharsbx(strip_tags($request->getPost('authEmail')))) : ''
                            )
                        );

                        if (is_array($APPLICATION->arAuthResult) && isset($APPLICATION->arAuthResult['MESSAGE'])) {
                            $this->arResult['ERROR'] = $APPLICATION->arAuthResult['MESSAGE'];
                        } else {
                            $this->arResult['AUTHORIZED'] = $USER->IsAuthorized();
                            $this->arResult['EMAIL'] = ($this->arResult['AUTHORIZED']) ? $USER->GetEmail() : false;
                        }
                    }
                }
            }
        }

        $this->includeComponentTemplate();
    }
}