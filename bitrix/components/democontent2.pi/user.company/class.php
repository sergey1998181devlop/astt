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
    private   function specilization(  $request ,  $subscriptions , $profile){

        if ($request->getPostList()->get('specialization') ) {
            $data = [
                'description' => HTMLToTxt($request->getPostList()->get('executorDescription')),
                'specializations' => [],
                'subSpecializations' => []
            ];
            $specializations = [];
            $subSpecializations = [];
            $found = [];

            foreach ($request->getPostList()->get('specialization') as $k => $v) {
                if (isset($this->arResult['MENU'][$k])) {
                    foreach ($v as $k_ => $v_) {
                        if (isset($this->arResult['MENU'][$k]['items'][$v_])) {
                            $specializations[$k] = $this->arResult['MENU'][$k]['name'];
                            $subSpecializations[$k][$v_] = $this->arResult['MENU'][$k]['items'][$v_]['name'];
                            $found[$k][$v_] = $v_;
                            $subscriptions->setIBlockType($k);
                            $subscriptions->setIBlockId($v_);
                            $subscriptions->add();
                        }
                    }
                }
            }

            foreach ($this->arResult['MENU'] as $k => $v) {
                if (!isset($found[$k])) {
                    if (isset($v['items'])) {
                        foreach ($v['items'] as $item) {
                            $subscriptions->setIBlockType($k);
                            $subscriptions->setIBlockId($item['id']);
                            $subscriptions->remove();
                        }
                    }
                } else {
                    if (isset($v['items'])) {
                        foreach ($v['items'] as $item) {
                            if (!isset($found[$k][$item['id']])) {
                                $subscriptions->setIBlockType($k);
                                $subscriptions->setIBlockId($item['id']);
                                $subscriptions->remove();
                            }
                        }
                    }
                }
            }

            $data['specializations'] = $specializations;
            $data['subSpecializations'] = $subSpecializations;

            $profile->setTtl(0);
            $profile->setData($data);
            $profile->update();
        }
    }
    public function executeComponent()
    {
        global $USER;
        global $APPLICATION;

        if ($USER->IsAuthorized()) {
            $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
            $city = new \Democontent2\Pi\Iblock\City();
            $us = new \Democontent2\Pi\User(intval($USER->GetID()), 0);
            $profile = new \Democontent2\Pi\Profile\Profile();
            $cards = new \Democontent2\Pi\Payments\SafeCrow\Cards();
            $menu = new \Democontent2\Pi\Iblock\Menu();
            $subscriptions = new \Democontent2\Pi\Profile\Subscriptions();

            $subscriptions->setUserId($USER->GetID());
            $cards->setUserId($USER->GetID());
            $profile->setUserId($USER->GetID());

            $this->arResult['MENU'] = $menu->get();
            $this->arResult['USER'] = $us->get();
            $this->arResult['PROFILE'] = $profile->get();
            $this->arResult['DATA'] = $profile->get();
            $this->arResult['CARDS'] = $cards->getUserCard();
            $this->arResult['CITIES'] = $city->getList();
            $this->arResult['SUBSCRIPTIONS'] = $subscriptions->getList();
            $this->arResult['COMPANY'] =  $us->getCompanyManager();


//если сотрудник  - у него значит нету компании / значит посылаем нахер
            if( $this->arResult['USER']['UF_MODERATION_ACCESS'] == ""){
                LocalRedirect( "/user/settings/" , true);
            }
//            pre($_POST);
//            die();
            $safeCrowEnabled = false;
            if (strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiKey')) > 0
                && strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
                $safeCrowEnabled = true;
            }


            if ($request->get('type') == 'card-binding' && $request->get('status') == 'success') {
                if (strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiKey')) > 0
                    && strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
                    if (!count($this->arResult['CARDS'])) {
                        if ($safeCrowEnabled) {
                            $safeCrow = new \Democontent2\Pi\Payments\SafeCrow\SafeCrow();
                            $userCards = $safeCrow->userCards($this->arResult['USER']['UF_DSPI_SAFECROW_ID']);
                            if (count($userCards) > 0) {
                                $cards->saveCard($userCards[0]);
                            }
                        }
                    }
                }

                LocalRedirect($APPLICATION->GetCurPage(false), true);
            }

            if ($request->isPost()) {

                if ($request->getPost('type')) {
                    switch ($request->getPost('type')) {
                        case 'verification':
                            if (!$this->arResult['USER']['UF_DSPI_DOCUMENTS']) {
                                $us->attachDocuments($_FILES, (($request->isHttps()) ? 'https://' : 'http://'), $request->getHttpHost());
                            }
                            break;
                    }
                } else {

                    if ($request->getPost('addCard')) {

                        if ($safeCrowEnabled) {
                            if (intval($this->arResult['USER']['UF_DSPI_SAFECROW_ID']) && !count($this->arResult['CARDS'])) {
                                $cards->setSafeCrowUserId($this->arResult['USER']['UF_DSPI_SAFECROW_ID']);
                                $addCard = $cards->addCard($request);

                                if (count($addCard) > 0 && isset($addCard['redirect_url'])) {
                                    LocalRedirect($addCard['redirect_url'], true);
                                }
                            }
                        }
                    } else {


                        if ($request->getPost('company_add')) {

                            $us->setCompany();
                            $this->specilization( $request ,  $subscriptions , $profile);


                            if (strlen($us->getRedirect())) {
                                LocalRedirect($us->getRedirect(), true);
                            }
                        }
                        if ($request->getPost('company_update')) {

                            $us->updateCompany();
                            $this->specilization( $request ,  $subscriptions , $profile);
                            if (strlen($us->getRedirect())) {
                                LocalRedirect($us->getRedirect(), true);
                            }
                        }
                        if ($request->getPost('company_update_repeat')) {

                            $us->updateCompanyRepeat();
                            $this->specilization( $request ,  $subscriptions , $profile);

                            if (strlen($us->getRedirect())) {
                                LocalRedirect($us->getRedirect(), true);
                            }
                        }
                        if ($request->getPost('company_moderation')) {

                            $us->company_moderation();
                            if (strlen($us->getRedirect())) {
                                LocalRedirect($us->getRedirect(), true);
                            }
                        }



                        if ($request->getPost('setExecutor')) {
                            $us->setExecutor();
                            $this->specilization( $request ,  $subscriptions , $profile);
                            if (strlen($us->getRedirect())) {
                                LocalRedirect($us->getRedirect(), true);
                            }
                        } else {

                        }
                    }
                }

                LocalRedirect($APPLICATION->GetCurPage(false), true);
            }
            $APPLICATION->AddChainItem('Компания');
            $this->includeComponentTemplate();
        } else {
            LocalRedirect(SITE_DIR, true);
        }
    }
}