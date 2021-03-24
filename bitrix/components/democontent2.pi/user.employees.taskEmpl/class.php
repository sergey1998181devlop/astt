<?php

use Democontent2\Pi\CheckList\ResponseValues;


/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.01.2019
 * Time: 16:40
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

class DetailComponentClass extends CBitrixComponent
{
    private $stageId = 0;

    public function executeComponent()
    {
        global $USER;
        global $APPLICATION;

        $cities = new \Democontent2\Pi\Iblock\City();
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $item = new \Democontent2\Pi\Iblock\Item();
        $item->setIBlockType($this->arParams['iBlockType']);
        $item->setIBlockCode($this->arParams['iBlockCode']);
        $item->setItemCode($this->arParams['itemCode']);
        $item->setItemId(intval($this->arParams['itemId']));
        if (isset($this->arParams['cityCode'])) {
            $item->setCityCode($this->arParams['cityCode']);
        }

        if($USER->IsAuthorized()){
            if ($request->getPostList()->get('publicUserSet')) {
//Опубликовать
                $item->publicUserSet($_POST);
//                                    LocalRedirect(SITE_DIR . 'user/moderation/', true);
            }
            if ($request->getPostList()->get('remove-from-the-public')) {
//отклонить
                $item->Yremove($_POST);
//                                    LocalRedirect(SITE_DIR . 'user/moderation/', true);
            }
            if ($request->getPostList()->get('remove-task-end')) {
//отклонить
                $item->YremoveTaskEnd($_POST);
                LocalRedirect(SITE_DIR . 'user/tasks/', true);

            }
            if ($request->getPostList()->get('moderation')) {
                switch ($request->getPostList()->get('moderation')) {
                    case 'repeatModeration':
                        $item->repeatModeration($_POST, $_FILES);

                        if(in_array(8, $USER->GetUserGroupArray()) ||  in_array(1, $USER->GetUserGroupArray())){
                            LocalRedirect(SITE_DIR . 'user/moderation/', true);
                        }else{
                            LocalRedirect(SITE_DIR . 'user/tasks/', true);
                        }
                        break;
                }

//                LocalRedirect(SITE_DIR . 'user/moderation/', true);
            }

            $this->arResult = $item->gatItemDetail();


            $this->arResult['c'] = $item->getDemoContent( $this->arResult['UF_ITEM_ID']);

            $db_props = CIBlockElement::GetProperty($this->arResult['UF_IBLOCK_ID'], $this->arResult['UF_ITEM_ID'], array("sort" => "asc"), Array("CODE"=>"files"));

            while ($ob = $db_props->GetNext())
            {
                $this->arResult['FILES_USER_MODERATION_EDIT'][] = $ob['VALUE'];


            }
            global $USER;
            $us = new \Democontent2\Pi\User(intval($USER->GetID()));
            //получаю компанию пользователя создавшего заявку
            $arIdes[0] = $this->arResult['UF_USER_ID'];

            $this->arResult['COMPANY'] = $us->getCompanyManager($arIdes);
//получаю текущего пользователя и узнаю прошел ли он модерацию
            $idUserCurrent = $USER->GetID();
            $rsUser = CUser::GetByID($idUserCurrent);
            $arUser = $rsUser->Fetch();
            //получаю статус подерации текущего пользователя
            $this->arResult['MODERATION_CURRENT_USER'] = $arUser['UF_MODERATION_ACCESS'];
//        debmes(  $this->arResult);
//            if(!empty( $this->arResult['FILES_USER_MODERATION_EDIT'][0])){
//                foreach ($this->arResult['FILES_USER_MODERATION_EDIT'] as $idFiles => $valueFiles){
//
//                }
//            }

        }else{
            LocalRedirect(SITE_DIR . '/', true);
        }



//        debmes( $this->arResult);
        $page = $APPLICATION->GetCurPage(); // результат - /ru/index.php
        $messUrl = explode('/' , $page);

        if($messUrl[2] == 'employees'){
            $APPLICATION->AddChainItem('Сотрудники' , '/user/employees/');
            if(!empty($this->arResult['USER_NAME'])){
                $APPLICATION->AddChainItem($this->arResult['USER_NAME'] , '/user/employees/update/?DetailNum='.$this->arResult['USER_NUM']);
            }
        }
     if($messUrl[2] == 'tasks'){
         $APPLICATION->AddChainItem('Задания' , '/user/tasks/');

     }

//        if(!empty($this->arResult['UF_NAME'])){
//            $APPLICATION->AddChainItem($this->arResult['UF_NAME']);
//        }

        $isAuthorized = $USER->IsAuthorized();
        $userId = intval($USER->GetID());

        if (isset($this->arResult['UF_ITEM_ID']) && intval($this->arResult['UF_ITEM_ID']) > 0) {
            if (intval($this->arResult['UF_MODERATION'])) {


                if (!$USER->IsAdmin()) {
                    CHTTP::SetStatus('404 Not Found');

                    if($isAuthorized){


                        if($this->arResult['UF_MODERATION'] == 2){
                            $this->arResult['USER'] = [];
                            $response = new \Democontent2\Pi\Iblock\Response();
                            $us = new \Democontent2\Pi\User(0);
                            $account = new \Democontent2\Pi\Balance\Account($this->arResult['UF_USER_ID']);

                            if (intval($this->arResult['UF_MODERATION']) == 1 || intval($this->arResult['UF_MODERATION']) == 4) {
                                if (isset($this->arResult['CURRENT_USER']['UF_DSPI_EXECUTOR'])
                                    && intval($this->arResult['CURRENT_USER']['UF_DSPI_EXECUTOR'])
                                    && intval($this->arResult['UF_USER_ID']) !== intval($this->arResult['CURRENT_USER']['ID'])) {
                                    if ($request->getPost('text')) {
                                        if (!$this->arResult['RESPONSE_COST']) {
                                            $response->setText($request->getPost('text'));
                                            $response->setFiles($_FILES);
                                            $response->add($this->arResult['UF_USER_ID'], $request);
                                        } else {
                                            if ($this->arResult['BALANCE'] >= $this->arResult['RESPONSE_COST']) {
                                                $response->setText($request->getPost('text'));
                                                $response->setFiles($_FILES);
                                                if ($response->add($this->arResult['UF_USER_ID'], $request) > 0) {
                                                    $account->setAmount($this->arResult['RESPONSE_COST']);
                                                    $account->setDescription(
                                                        \Bitrix\Main\Localization\Loc::getMessage(
                                                            'CLASS_DETAIL_ORDER_RESPONSE_PAY',
                                                            [
                                                                '#ID#' => $this->arResult['UF_ID']
                                                            ]
                                                        )
                                                    );
                                                    $account->withdrawal();
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            if (isset($this->arResult['UF_USER_ID']) && $this->arResult['UF_USER_ID'] > 0) {
                                $us->setId($this->arResult['UF_USER_ID']);
                                $this->arResult['USER'] = $us->get();

                                $reviews = new \Democontent2\Pi\Iblock\Reviews();
                                $reviews->setUserId(intval($this->arResult['UF_USER_ID']));
                                $this->arResult['CURRENT_RATING'] = $reviews->rating();
                                $this->arResult['REVIEWS_COUNT'] = $reviews->getCountByUser();
//                            pre( $this->arResult);
//                            die();
//                            $photo = $reviews->getListByTask();
                            }

                            $this->setTemplateName('moderation_edit');
                        };
                        if($this->arResult['MODERATION'] == 1 ){
                            $this->setTemplateName('moderation');
                        }


                    }else{
                        $us = new \Democontent2\Pi\User(0);
                        $iblock = new \Democontent2\Pi\Iblock\Iblock();
                        $this->arResult['FAVOURITES'] = [];
                        $this->arResult['CITY'] = $item->getCityParams();
                        $this->arResult['IBLOCK_TYPE_NAME'] = $iblock->getTypeName('democontent2_pi_' . str_replace('-', '_', $this->arResult['UF_IBLOCK_TYPE']));
                        $this->arResult['IBLOCK_NAME'] = $iblock->getIblockName($this->arResult['UF_IBLOCK_ID']);
                        if (isset($this->arResult['UF_USER_ID']) && $this->arResult['UF_USER_ID'] > 0) {
                            $us->setId($this->arResult['UF_USER_ID']);
                            $this->arResult['USER'] = $us->get();

                            $reviews = new \Democontent2\Pi\Iblock\Reviews();
                            $reviews->setUserId(intval($this->arResult['UF_USER_ID']));
                            $this->arResult['CURRENT_RATING'] = $reviews->rating();
                            $this->arResult['REVIEWS_COUNT'] = $reviews->getCountByUser();
//                            pre( $this->arResult);
//                            die();
//                            $photo = $reviews->getListByTask();
                        }
                    }



                    if ($request->isPost()) {
                        if ($request->getPostList()->get('moderation')) {
                            switch ($request->getPostList()->get('moderation')) {
                                case 'repeatModeration':
                                    $item->repeatModeration($_POST, $_FILES);
                                    break;
                            }

                            LocalRedirect(SITE_DIR . 'user/moderation/', true);
                        }

                        if ($request->getPostList()->get('remove-from-the-public')) {

                            $item->Yremove($_POST);
//                                    LocalRedirect(SITE_DIR . 'user/moderation/', true);
                        }
                    }

                } else {

                    if ($request->isPost()) {
                        if ($request->getPostList()->get('remove-from-the-public')) {

                            $item->Yremove($_POST);
//                                    LocalRedirect(SITE_DIR . 'user/moderation/', true);
                        }

                        if ($request->getPostList()->get('moderation')) {
                            switch ($request->getPostList()->get('moderation')) {
                                case 'reject':
                                    $item->refusal_app($item);
                                    break;
                                case 'approve':
                                    $item->approve();
                                    break;
                                case 'repeatModeration':
                                    $item->repeatModeration();
                                    break;
                            }

                            LocalRedirect(SITE_DIR . 'user/moderation/', true);
                        }
                    }
                }

            }

            if (!intval($this->arResult['UF_MODERATION']) || $USER->IsAdmin()) {

                if ($item->isDefaultRedirect()) {
                    LocalRedirect(
                        SITE_DIR . $this->arParams['iBlockType'] . '/' . $this->arParams['iBlockCode'] . '/'
                        . $this->arParams['itemCode'] . '-' . $this->arParams['itemId'] . '/',
                        true,
                        '301 Moved Permanently'
                    );
                }

                /*    if ($item->getCityRedirect()) {
                        LocalRedirect(
                            SITE_DIR . $item->getCityRedirect() . '/' . $this->arParams['iBlockType'] . '/'
                            . $this->arParams['iBlockCode'] . '/' . $this->arParams['itemCode'] . '-' . $this->arParams['itemId'] . '/',
                            true,
                            '301 Moved Permanently'
                        );
                    }
                */

                $item->setIBlockId(intval($this->arResult['UF_IBLOCK_ID']));

                $us = new \Democontent2\Pi\User(0);
                $IBLOCK_ID = '';
                $PROPS = [];
                $Res = CIBlockElement::GetByID($this->arResult['UF_ID']);
                if ($arItem = $Res->GetNext()) {
                    $IBLOCK_ID = $arItem['IBLOCK_ID'];
                }

                $resElement = \CIBlockElement::GetList(
                    [],
                    [
                        'IBLOCK_ID' => $IBLOCK_ID,
                        'ID' => $this->arResult['UF_ID'],
                    ],
                    false,
                    false,
                    [
                        'ID',
                        'IBLOCK_ID',
                        'PROPERTY_CHARACTERISTIC_1',
                        'PROPERTY_CHARACTERISTIC_2',
                        'PROPERTY_CHARACTERISTIC_3',
                        'PROPERTY_BEZNAL',
                        'PROPERTY_NAL',
                        'PROPERTY_BEZ_NDS',
                        'PROPERTY_FOR_DOGOVOR',



                    ]
                );

                if ( !($PROPS = $resElement->getNext() ) )
                {

                }else{
                    foreach ($PROPS as $id => $val){
//                        debmes($id);
                        if($id == 'PROPERTY_CHARACTERISTIC_1_VALUE' || $id == 'PROPERTY_CHARACTERISTIC_2_VALUE' || $id == 'PROPERTY_CHARACTERISTIC_3_VALUE'){
                            $PROPS['CHARACTERISTIC_NAMES'][] = $val;
                        }
                        if($id == 'PROPERTY_CHARACTERISTIC_1_DESCRIPTION' || $id == 'PROPERTY_CHARACTERISTIC_2_DESCRIPTION' || $id == 'PROPERTY_CHARACTERISTIC_3_DESCRIPTION'){
                            $PROPS['CHARACTERISTIC_DESCRIPTION'][] = $val;
                        }
                    }
                }
//                debmes($PROPS);
//                debmes(unserialize($this->arResult['UF_PROPERTIES']));
                $this->arResult['PROPS'] = $PROPS;



                $this->arResult['USER_CARD'] = 0;
                $this->arResult['EXECUTOR_ID'] = 0;
                $this->arResult['IS_EXECUTOR'] = false;
                $this->arResult['IS_OWNER'] = false;
                $this->arResult['CURRENT_USER'] = [];
                $this->arResult['USER'] = [];
                $this->arResult['SAFECROW_ORDERS'] = [];
                $this->arResult['EXECUTOR_CARD'] = [];
                $this->arResult['STAGES'] = $item->getStages();
                $this->arResult['COMPLAIN'] = [];
                $this->arResult['ALLOW_CHECKLISTS'] = intval(\Bitrix\Main\Config\Option::get(DSPI, 'response_checklists'));
                $this->arResult['RESPONSE_CHECKLIST'] = [];
                $this->arResult['RESPONSE_CHECKLIST_VALUES'] = [];



                if ($isAuthorized) {
                    $responseCheckList = new \Democontent2\Pi\CheckList\Response(intval($this->arResult['UF_ID']));
                    $this->arResult['RESPONSE_CHECKLIST'] = $responseCheckList->getList();

                    $us->setId($userId);
                    $this->arResult['CURRENT_USER'] = $us->get();

                    if (strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiKey')) > 0
                        && strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
                        $safeCrow = new \Democontent2\Pi\Payments\SafeCrow\SafeCrow();
                        $safeCrow->setTaskId(intval($this->arResult['UF_ID']));

                        $this->arResult['SAFECROW_ORDERS'] = $safeCrow->getLocalOrders();
                    }

                    $complain = new \Democontent2\Pi\Iblock\Complain();
                    $complain->setTaskId($this->arResult['UF_ID']);
                    $this->arResult['COMPLAIN'] = $complain->get();
                }

                $stagesCount = count($this->arResult['STAGES']);
                $safeCrowOrdersCount = count($this->arResult['SAFECROW_ORDERS']);

                if ($isAuthorized && $this->arResult['UF_SAFE']) {
                    $cards = new \Democontent2\Pi\Payments\SafeCrow\Cards();
                    $cards->setUserId($userId);
                    if (count($cards->getUserCard())) {
                        $this->arResult['USER_CARD'] = 1;
                    }
                    //��������� GET ���������� ������������� ������ ������ SafeCrow
                    if ($request->get('orderId') && $request->get('status') && $request->get('type')) {
                        if ($request->get('type') == 'pay' && $request->get('status') == 'success') {
                            $this->checkSafeCrowOrder($request);
                        }
                    }
                }

                if (isset($this->arResult['UF_USER_ID']) && $this->arResult['UF_USER_ID'] > 0) {
                    if ($isAuthorized && ($userId == intval($this->arResult['UF_USER_ID']))) {
                        $this->arResult['IS_OWNER'] = true;
                    }
                }



                $iblock = new \Democontent2\Pi\Iblock\Iblock();
                $this->arResult['FAVOURITES'] = [];
                $this->arResult['CITY'] = $item->getCityParams();
                $this->arResult['IBLOCK_TYPE_NAME'] = $iblock->getTypeName('democontent2_pi_' . str_replace('-', '_', $this->arResult['UF_IBLOCK_TYPE']));
                $this->arResult['IBLOCK_NAME'] = $iblock->getIblockName($this->arResult['UF_IBLOCK_ID']);

                $response = new \Democontent2\Pi\Iblock\Response();
                $response->setIBlockId(intval($this->arResult['UF_IBLOCK_ID']));
                $response->setTaskId(intval($this->arResult['UF_ID']));

                $this->arResult['RESPONSES'] = $response->getList();

                foreach ($this->arResult['RESPONSES'] as $responseItem) {
                    if (intval($responseItem['UF_EXECUTOR'])) {
                        $this->arResult['EXECUTOR_ID'] = intval($responseItem['UF_USER_ID']);

                        if (intval($responseItem['UF_USER_ID']) == $userId) {
                            $this->arResult['IS_EXECUTOR'] = true;
                        }
                        break;
                    }
                }

                if ($this->arResult['IS_EXECUTOR']) {
                    $cards = new \Democontent2\Pi\Payments\SafeCrow\Cards();
                    $cards->setUserId($this->arResult['EXECUTOR_ID']);
                    $this->arResult['EXECUTOR_CARD'] = $cards->getUserCard();
                }

                $reviews = new \Democontent2\Pi\Iblock\Reviews();
                $reviews->setTaskId(intval($this->arResult['UF_ID']));

                $this->arResult['REVIEWS'] = $reviews->getListByTask();

                if ($this->arResult['IS_OWNER']) {
                    if ($request->isPost()) {
                        switch (intval($this->arResult['UF_STATUS'])) {
                            case 1: //task open
                                if ($request->getPost('close')) {
                                    $item->completed($this->arResult['UF_USER_ID']);

                                    LocalRedirect($APPLICATION->GetCurPage(false), true);
                                }

                                if ($request->getPost('setOption')) {
                                    switch ($request->getPost('setOption')) {
                                        case 'quickly':
                                            $order = new \Democontent2\Pi\Order($this->arResult['UF_USER_ID']);
                                            $order->setItemId($this->arResult['UF_ID']);
                                            $order->setAdditionalParams(
                                                [
                                                    $this->arResult['UF_IBLOCK_ID'] => [$this->arResult['UF_ID']]
                                                ]
                                            );
                                            $order->setType($request->getPost('setOption'));
                                            $order->setSum(intval(\Bitrix\Main\Config\Option::get(
                                                    DSPI,
                                                    $request->getPost('setOption') . '_option_cost'))
                                            );
                                            $order->setDescription(
                                                \Bitrix\Main\Localization\Loc::getMessage(
                                                    'CLASS_SERVICE_' . ToUpper($request->getPost('setOption')) . '_DESCRIPTION',
                                                    [
                                                        '#ID#' => $this->arResult['UF_ID']
                                                    ]
                                                )
                                            );

                                            $account = new \Democontent2\Pi\Balance\Account($this->arResult['UF_USER_ID']);
                                            $balance = $account->getAmount();
                                            if ($balance > 0 && $balance >= $order->getSum()) {
                                                if ($order->make(true)) {
                                                    $order->setPayed(true);

                                                    $account->setAmount($order->getSum());
                                                    $account->setDescription(
                                                        \Bitrix\Main\Localization\Loc::getMessage(
                                                            'CLASS_SERVICE_' . ToUpper($request->getPost('setOption')) . '_DESCRIPTION',
                                                            [
                                                                '#ID#' => $this->arResult['UF_ID']
                                                            ]
                                                        )
                                                    );
                                                    $account->withdrawal();
                                                }
                                            } else {
                                                $order->make();
                                                if ($order->getRedirect()) {
                                                    LocalRedirect($order->getRedirect(), true);
                                                }
                                            }
                                            break;
                                    }
                                }
                                break;
                            case 2: //task running
                                //case 3: //task closed
                                if ($stagesCount > 0) {
                                    if ($this->arResult['UF_SAFE']) {
                                        if ($request->getPost('stageId') && $request->getPost('pay')) {
                                            //������ �����
                                            foreach ($this->arResult['STAGES'] as $stage) {
                                                if (intval($stage['UF_STATUS']) == 0 && intval($request->getPost('stageId')) == intval($stage['ID'])) { //stage completed
                                                    $this->stageId = intval($stage['ID']);
                                                    $this->createSafeCrowOrder($request, intval($stage['UF_PRICE']));
                                                }
                                            }
                                        }
                                    }

                                    $stagesCompleted = 0;
                                    foreach ($this->arResult['STAGES'] as $stage) {
                                        if ($stage['UF_STATUS'] == 3) { //stage completed
                                            $stagesCompleted++;
                                        }
                                    }

                                    if ($request->getPost('setComplain') && $request->getPost('stageId')
                                        && $request->getPost('setComplainMessage') && strlen($request->getPost('setComplainMessage')) > 0) {
                                        //������ �� ����
                                        foreach ($this->arResult['STAGES'] as $stage) {
                                            if (intval($stage['ID']) == intval($request->getPost('stageId'))) {
                                                $executor = 0;
                                                foreach ($this->arResult['RESPONSES'] as $responseItem) {
                                                    if (intval($responseItem['UF_EXECUTOR'])) {
                                                        $executor = intval($responseItem['UF_USER_ID']);
                                                        break;
                                                    }
                                                }

                                                $item->setStageComplain(
                                                    intval($request->getPost('stageId')),
                                                    $request->getPost('setComplainMessage'),
                                                    $this->arResult['UF_USER_ID'],
                                                    $executor
                                                );

                                                $complain = new \Democontent2\Pi\Iblock\Complain();
                                                $complain->setTaskId($this->arResult['UF_ID']);
                                                $complain->setIBlockId($this->arResult['UF_IBLOCK_ID']);
                                                $complain->setUserId($userId);
                                                $complain->setStageId($stage['ID']);
                                                $complain->setText($request->getPost('setComplainMessage'));
                                                $complain->add($this->arResult['UF_USER_ID'], $executor, $_FILES);

                                                if ($this->arResult['UF_SAFE']) {
                                                    $this->escalateOrder(
                                                        strip_tags(HTMLToTxt($request->getPost('setComplainMessage'))),
                                                        intval($stage['ID'])
                                                    );
                                                }
                                                break;
                                            }
                                        }
                                        LocalRedirect($APPLICATION->GetCurPage(false), true);
                                    }

                                    if (($stagesCount - $stagesCompleted) == 1) {
                                        //�������� ������� � ����� � ��� ������, ���� ����������� ��������� ����
                                        if ($request->getPost('feedbackMessage') && $request->getPost('stageId')
                                            && $request->getPost('setCompleted')) {
                                            if (strlen($request->getPost('feedbackMessage')) > 0 && intval($request->getPost('stageId')) > 0) {
                                                $executor = 0;
                                                foreach ($this->arResult['RESPONSES'] as $responseItem) {
                                                    if (intval($responseItem['UF_EXECUTOR'])) {
                                                        $executor = intval($responseItem['UF_USER_ID']);
                                                        break;
                                                    }
                                                }

                                                if ($executor > 0) {
                                                    if ($this->arResult['UF_SAFE'] && $safeCrowOrdersCount > 0) {
                                                        if (strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiKey')) > 0
                                                            && strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
                                                            foreach ($this->arResult['SAFECROW_ORDERS'] as $safeCrowOrder) {
                                                                if (($safeCrowOrder['UF_TASK_ID'] == $this->arResult['UF_ID'])
                                                                    && (intval($safeCrowOrder['UF_STAGE_ID']) == intval($request->getPost('stageId')))) {
                                                                    $safeCrow = new \Democontent2\Pi\Payments\SafeCrow\SafeCrow();
                                                                    $safeCrow->setTaskId($this->arResult['UF_ID']);
                                                                    $closeSafeCrowOrder = $safeCrow->closeOrder(
                                                                        $safeCrowOrder['UF_ORDER_ID'],
                                                                        \Bitrix\Main\Localization\Loc::getMessage('CLASS_DETAIL_SET_COMPLETE')
                                                                    );

                                                                    if (!count($closeSafeCrowOrder)) {
                                                                        //�� ������� ������� ����� � SafeCrow, ��. ����
                                                                    }

                                                                    $item->closeStage(intval($request->getPost('stageId')), $executor);
                                                                    $item->completed($this->arResult['UF_USER_ID'], $executor);

                                                                    $reviews = new \Democontent2\Pi\Iblock\Reviews();
                                                                    $reviews->setIBlockId($this->arResult['UF_IBLOCK_ID']);
                                                                    $reviews->setTaskId($this->arResult['UF_ID']);
                                                                    $reviews->setUserId($this->arResult['UF_USER_ID']);
                                                                    $reviews->setTo($executor);
                                                                    $reviews->setText($request->getPost('feedbackMessage'));
                                                                    $reviews->setRating(intval($request->getPost('feedbackType')));
                                                                    $reviews->add();
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        $item->closeStage(intval($request->getPost('stageId')), $executor);
                                                        $item->completed($this->arResult['UF_USER_ID'], $executor);

                                                        $reviews = new \Democontent2\Pi\Iblock\Reviews();
                                                        $reviews->setIBlockId($this->arResult['UF_IBLOCK_ID']);
                                                        $reviews->setTaskId($this->arResult['UF_ID']);
                                                        $reviews->setUserId($this->arResult['UF_USER_ID']);
                                                        $reviews->setTo($executor);
                                                        $reviews->setText($request->getPost('feedbackMessage'));
                                                        $reviews->setRating(intval($request->getPost('feedbackType')));
                                                        $reviews->add();
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        //�������� ���������� �����
                                        if (($stagesCount - $stagesCompleted) > 1) {
                                            if ($request->getPost('stageId') && $request->getPost('setCompleted')) {
                                                if (intval($request->getPost('stageId')) > 0) {
                                                    $executor = 0;
                                                    foreach ($this->arResult['RESPONSES'] as $responseItem) {
                                                        if (intval($responseItem['UF_EXECUTOR'])) {
                                                            $executor = intval($responseItem['UF_USER_ID']);
                                                            break;
                                                        }
                                                    }

                                                    if ($executor > 0) {
                                                        foreach ($this->arResult['STAGES'] as $stage) {
                                                            if (intval($stage['ID']) == intval($request->getPost('stageId'))) {
                                                                if (intval($stage['UF_STATUS']) == 1) {
                                                                    //1 - ������ "�������� ������������" ��� �����
                                                                    if ($this->arResult['UF_SAFE'] && $safeCrowOrdersCount > 0) {
                                                                        if (strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiKey')) > 0
                                                                            && strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
                                                                            foreach ($this->arResult['SAFECROW_ORDERS'] as $safeCrowOrder) {
                                                                                if (($safeCrowOrder['UF_TASK_ID'] == $this->arResult['UF_ID'])
                                                                                    && (intval($safeCrowOrder['UF_STAGE_ID']) == intval($request->getPost('stageId')))) {
                                                                                    $safeCrow = new \Democontent2\Pi\Payments\SafeCrow\SafeCrow();
                                                                                    $safeCrow->setTaskId($this->arResult['UF_ID']);
                                                                                    $closeSafeCrowOrder = $safeCrow->closeOrder(
                                                                                        $safeCrowOrder['UF_ORDER_ID'],
                                                                                        \Bitrix\Main\Localization\Loc::getMessage('CLASS_DETAIL_SET_COMPLETE')
                                                                                    );

                                                                                    if (!count($closeSafeCrowOrder)) {
                                                                                        //�� ������� ������� �����, ��. ���
                                                                                    } else {
                                                                                        $item->closeStage(intval($request->getPost('stageId')), $executor);
                                                                                    }
                                                                                    break;
                                                                                }
                                                                            }
                                                                        }
                                                                    } else {
                                                                        $item->closeStage(intval($request->getPost('stageId')), $executor);
                                                                    }
                                                                }
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    if ($this->arResult['UF_SAFE']) {
                                        //������ ������
                                        if ($request->getPost('pay')) {
                                            $this->createSafeCrowOrder($request, intval($this->arResult['UF_PRICE']));
                                        }
                                    }

                                    if ($request->getPost('setComplain') && $request->getPost('setComplainMessage')
                                        && strlen($request->getPost('setComplainMessage')) > 0) {
                                        $executor = 0;
                                        foreach ($this->arResult['RESPONSES'] as $responseItem) {
                                            if (intval($responseItem['UF_EXECUTOR'])) {
                                                $executor = intval($responseItem['UF_USER_ID']);
                                                break;
                                            }
                                        }

                                        //������ �� �������
                                        $item->setTaskComplain(
                                            $request->getPost('setComplainMessage'),
                                            $this->arResult['UF_USER_ID'],
                                            $executor
                                        );

                                        $complain = new \Democontent2\Pi\Iblock\Complain();
                                        $complain->setTaskId($this->arResult['UF_ID']);
                                        $complain->setIBlockId($this->arResult['UF_IBLOCK_ID']);
                                        $complain->setUserId($userId);
                                        $complain->setText($request->getPost('setComplainMessage'));
                                        $complain->add($this->arResult['UF_USER_ID'], $executor, $_FILES);

                                        if ($this->arResult['UF_SAFE']) {
                                            $this->escalateOrder(strip_tags(HTMLToTxt($request->getPost('setComplainMessage'))), 0);
                                        }
                                        LocalRedirect($APPLICATION->GetCurPage(false), true);
                                    }
                                }
                                break;
                            case 3: //task is closed
                                if ($request->getPost('feedbackMessage-owner')) {
                                    $reviews = new \Democontent2\Pi\Iblock\Reviews();
                                    $reviews->setIBlockId($this->arResult['UF_IBLOCK_ID']);
                                    $reviews->setTaskId($this->arResult['UF_ID']);
                                    $reviews->setUserId($userId);
                                    $reviews->setText($request->getPost('feedbackMessage-owner'));
                                    $reviews->setRating(intval($request->getPost('feedbackType')));
                                    $reviews->editReview();

                                    LocalRedirect($APPLICATION->GetCurPage(false), true);
                                }
                                break;
                            case 4: //task executor exists
                                if ($request->getPost('unSetExecutor')) {
                                    foreach ($this->arResult['RESPONSES'] as $item) {
                                        if (intval($item['UF_EXECUTOR']) > 0) {
                                            $response->setExecutorId(intval($item['UF_USER_ID']));
                                            $response->unSetExecutor(intval($item['ID']));
                                            break;
                                        }
                                    }
                                }
                                break;
                            case 5: //task executor complete
                                if ($request->getPost('feedbackMessage') && $request->getPost('setCompleted')) {
                                    $executor = 0;
                                    foreach ($this->arResult['RESPONSES'] as $responseItem) {
                                        if (intval($responseItem['UF_EXECUTOR'])) {
                                            $executor = intval($responseItem['UF_USER_ID']);
                                            break;
                                        }
                                    }

                                    if ($executor > 0) {
                                        $item->completed($this->arResult['UF_USER_ID'], $executor);

                                        if ($this->arResult['UF_SAFE'] && $safeCrowOrdersCount > 0) {
                                            if (strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiKey')) > 0
                                                && strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
                                                foreach ($this->arResult['SAFECROW_ORDERS'] as $safeCrowOrder) {
                                                    if ($safeCrowOrder['UF_TASK_ID'] == $this->arResult['UF_ID'] && !intval($safeCrowOrder['UF_STAGE_ID'])) {
                                                        $safeCrow = new \Democontent2\Pi\Payments\SafeCrow\SafeCrow();
                                                        $safeCrow->setTaskId($this->arResult['UF_ID']);
                                                        $closeSafeCrowOrder = $safeCrow->closeOrder(
                                                            $safeCrowOrder['UF_ORDER_ID'],
                                                            \Bitrix\Main\Localization\Loc::getMessage('CLASS_DETAIL_SET_COMPLETE')
                                                        );

                                                        if (!count($closeSafeCrowOrder)) {
                                                            //�� ������� ������� �����, ��. ���
                                                        }
                                                        break;
                                                    }
                                                }
                                            }
                                        }

                                        $reviews = new \Democontent2\Pi\Iblock\Reviews();
                                        $reviews->setIBlockId($this->arResult['UF_IBLOCK_ID']);
                                        $reviews->setTaskId($this->arResult['UF_ID']);
                                        $reviews->setUserId($this->arResult['UF_USER_ID']);
                                        $reviews->setTo($executor);
                                        $reviews->setText($request->getPost('feedbackMessage'));
                                        $reviews->setRating(intval($request->getPost('feedbackType')));
                                        $reviews->add();
                                    }
                                }

                                if ($request->getPost('setComplain') && $request->getPost('setComplainMessage')
                                    && strlen($request->getPost('setComplainMessage')) > 0) {
                                    $executor = 0;
                                    foreach ($this->arResult['RESPONSES'] as $responseItem) {
                                        if (intval($responseItem['UF_EXECUTOR'])) {
                                            $executor = intval($responseItem['UF_USER_ID']);
                                            break;
                                        }
                                    }

                                    //������ �� �������
                                    $item->setTaskComplain(
                                        $request->getPost('setComplainMessage'),
                                        $this->arResult['UF_USER_ID'],
                                        $executor
                                    );

                                    $complain = new \Democontent2\Pi\Iblock\Complain();
                                    $complain->setTaskId($this->arResult['UF_ID']);
                                    $complain->setIBlockId($this->arResult['UF_IBLOCK_ID']);
                                    $complain->setUserId($userId);
                                    $complain->setText($request->getPost('setComplainMessage'));
                                    $complain->add($this->arResult['UF_USER_ID'], $executor, $_FILES);

                                    if ($this->arResult['UF_SAFE']) {
                                        $this->escalateOrder(strip_tags(HTMLToTxt($request->getPost('setComplainMessage'))), 0);
                                    }
                                    LocalRedirect($APPLICATION->GetCurPage(false), true);
                                }
                                break;
                            case 6: //complain is open
                                if ($request->getPost('feedbackMessage')) {
                                    if (strlen($request->getPost('feedbackMessage')) > 0) {
                                        $executor = 0;
                                        foreach ($this->arResult['RESPONSES'] as $responseItem) {
                                            if (intval($responseItem['UF_EXECUTOR'])) {
                                                $executor = intval($responseItem['UF_USER_ID']);
                                                break;
                                            }
                                        }

                                        if ($executor > 0) {
                                            $reviews = new \Democontent2\Pi\Iblock\Reviews();
                                            $reviews->setIBlockId($this->arResult['UF_IBLOCK_ID']);
                                            $reviews->setTaskId($this->arResult['UF_ID']);
                                            $reviews->setUserId($this->arResult['UF_USER_ID']);
                                            $reviews->setTo($executor);
                                            $reviews->setText($request->getPost('feedbackMessage'));
                                            $reviews->setRating(intval($request->getPost('feedbackType')));
                                            $reviews->add();
                                        }
                                    }
                                }
                                break;
                        }

                        LocalRedirect($APPLICATION->GetCurPage(false), true);
                    }

                    if ($this->arResult['ALLOW_CHECKLISTS']) {
                        $responseCheckListValues = new ResponseValues(intval($this->arResult['UF_ID']), 0);
                        $values = $responseCheckListValues->getList();

                        foreach ($values as $value) {
                            foreach ($this->arResult['RESPONSE_CHECKLIST'] as $item) {
                                if ($item['ID'] == $value['UF_VALUE_ID']) {
                                    $this->arResult['RESPONSE_CHECKLIST_VALUES'][$value['UF_OFFER_ID']][] = [
                                        'id' => $value['UF_VALUE_ID'],
                                        'name' => $item['UF_NAME']
                                    ];
                                    break;
                                }
                            }
                        }
                    }
                    if (isset($this->arResult['UF_USER_ID']) && $this->arResult['UF_USER_ID'] > 0) {
                        $us->setId($this->arResult['UF_USER_ID']);
                        $this->arResult['USER'] = $us->get();

                        $reviews = new \Democontent2\Pi\Iblock\Reviews();
                        $reviews->setUserId(intval($this->arResult['UF_USER_ID']));
                        $this->arResult['CURRENT_RATING'] = $reviews->rating();
                        $this->arResult['REVIEWS_COUNT'] = $reviews->getCountByUser();
                    }

                    if (intval($this->arResult['UF_STATUS']) == 3) {
                        $this->setTemplateName('completed');
                    } else {
                        $this->setTemplateName('.default');
                    }
                } else {
                    $this->arResult['BLOCKED'] = 0;
                    $this->arResult['MY_OFFER'] = [];
                    $this->arResult['BALANCE'] = 0;
                    $this->arResult['RESPONSE_COST'] = 0;

                    if ($isAuthorized) {
                        $response->setUserId($userId);
                        $this->arResult['MY_OFFER'] = $response->myOffer();

                        $account = new \Democontent2\Pi\Balance\Account($userId);
                        $blackList = new \Democontent2\Pi\Iblock\BlackList();
                        $blackList->setUserId($this->arResult['UF_USER_ID']);
                        $blackList->setBlockedId($userId);
                        $this->arResult['BLOCKED'] = $blackList->check();

                        if (!$this->arResult['BLOCKED']) {
                            $prices = new \Democontent2\Pi\Iblock\Prices();
                            $profilePrices = new \Democontent2\Pi\Profile\Prices();
                            $profilePrices->setUserId($userId);
                            $this->arResult['BALANCE'] = $account->getAmount();

                            $getPrices = $prices->get();
                            $getProfilePrices = $profilePrices->get();

                            if (count($getPrices) > 0) {
                                $getPrices = unserialize($getPrices['UF_DATA']);
                                if (isset($getPrices[$this->arResult['UF_IBLOCK_TYPE']]['item'][$this->arResult['UF_IBLOCK_ID']])) {
                                    $this->arResult['RESPONSE_COST'] = intval($getPrices[$this->arResult['UF_IBLOCK_TYPE']]['item'][$this->arResult['UF_IBLOCK_ID']]);
                                }
                            }

                            if (count($getProfilePrices) > 0) {
                                $getProfilePrices = unserialize($getProfilePrices['UF_DATA']);
                                if (isset($getProfilePrices[$this->arResult['UF_IBLOCK_TYPE']][0])) {
                                    if (strtotime($getProfilePrices[$this->arResult['UF_IBLOCK_TYPE']][0]) > time()) {
                                        $this->arResult['RESPONSE_COST'] = 0;
                                    } else {
                                        if (isset($getProfilePrices[$this->arResult['UF_IBLOCK_TYPE']][$this->arResult['UF_IBLOCK_ID']])) {
                                            if (strtotime($getProfilePrices[$this->arResult['UF_IBLOCK_TYPE']][$this->arResult['UF_IBLOCK_ID']]) > time()) {
                                                $this->arResult['RESPONSE_COST'] = 0;
                                            }
                                        }
                                    }
                                } else {
                                    if (isset($getProfilePrices[$this->arResult['UF_IBLOCK_TYPE']][$this->arResult['UF_IBLOCK_ID']])) {
                                        if (strtotime($getProfilePrices[$this->arResult['UF_IBLOCK_TYPE']][$this->arResult['UF_IBLOCK_ID']]) > time()) {
                                            $this->arResult['RESPONSE_COST'] = 0;
                                        }
                                    }
                                }
                            }
                        }

                        if ($request->isPost()) {
                            switch (intval($this->arResult['UF_STATUS'])) {
                                case 2:
                                    if ($this->arResult['IS_EXECUTOR']) {
                                        if ($stagesCount > 0) {
                                            if ($request->getPost('stageId')) {
                                                if ($request->getPost('setCompleted')) {
                                                    if (intval($request->getPost('stageId')) > 0) {
                                                        //����������� �������� ����
                                                        foreach ($this->arResult['STAGES'] as $stage) {
                                                            if (intval($stage['ID']) == intval($request->getPost('stageId'))) {
                                                                $item->closeStageExecutor(
                                                                    intval($request->getPost('stageId')),
                                                                    $this->arResult['UF_USER_ID']
                                                                );
                                                                break;
                                                            }
                                                        }
                                                    }

                                                    LocalRedirect($APPLICATION->GetCurPage(false), true);
                                                }

                                                if ($request->getPost('setComplain') && $request->getPost('setComplainMessage')
                                                    && strlen($request->getPost('setComplainMessage')) > 0) {
                                                    //������ �� ����
                                                    foreach ($this->arResult['STAGES'] as $stage) {
                                                        if (intval($stage['ID']) == intval($request->getPost('stageId'))) {
                                                            $item->setStageComplain(
                                                                intval($request->getPost('stageId')),
                                                                $request->getPost('setComplainMessage'),
                                                                $this->arResult['UF_USER_ID'],
                                                                $userId
                                                            );

                                                            $complain = new \Democontent2\Pi\Iblock\Complain();
                                                            $complain->setTaskId($this->arResult['UF_ID']);
                                                            $complain->setIBlockId($this->arResult['UF_IBLOCK_ID']);
                                                            $complain->setUserId($userId);
                                                            $complain->setStageId($stage['ID']);
                                                            $complain->setText($request->getPost('setComplainMessage'));
                                                            $complain->add($this->arResult['UF_USER_ID'], $userId, $_FILES);

                                                            if ($this->arResult['UF_SAFE']) {
                                                                $this->escalateOrder(
                                                                    strip_tags(HTMLToTxt($request->getPost('setComplainMessage'))),
                                                                    intval($stage['ID'])
                                                                );
                                                            }
                                                            break;
                                                        }
                                                    }
                                                    LocalRedirect($APPLICATION->GetCurPage(false), true);
                                                }
                                            }
                                        } else {
                                            if ($request->getPost('setCompleted')) {
                                                //������� ��������� ������������
                                                $item->executorCompleted($this->arResult['UF_USER_ID']);
                                                LocalRedirect($APPLICATION->GetCurPage(false), true);
                                            }

                                            if ($request->getPost('setComplain') && $request->getPost('setComplainMessage')
                                                && strlen($request->getPost('setComplainMessage')) > 0) {
                                                //������ �� �������
                                                $item->setTaskComplain(
                                                    $request->getPost('setComplainMessage'),
                                                    $this->arResult['UF_USER_ID'],
                                                    $userId
                                                );

                                                $complain = new \Democontent2\Pi\Iblock\Complain();
                                                $complain->setTaskId($this->arResult['UF_ID']);
                                                $complain->setIBlockId($this->arResult['UF_IBLOCK_ID']);
                                                $complain->setUserId($userId);
                                                $complain->setText($request->getPost('setComplainMessage'));
                                                $complain->add($this->arResult['UF_USER_ID'], $userId, $_FILES);

                                                if ($this->arResult['UF_SAFE']) {
                                                    $this->escalateOrder(strip_tags(HTMLToTxt($request->getPost('setComplainMessage'))), 0);
                                                }
                                                LocalRedirect($APPLICATION->GetCurPage(false), true);
                                            }
                                        }
                                    }
                                    break;
                                case 3:
                                    if ($this->arResult['IS_EXECUTOR']) {
                                        if ($request->getPost('feedbackMessage-executor')) {
                                            $reviews = new \Democontent2\Pi\Iblock\Reviews();
                                            $reviews->setIBlockId($this->arResult['UF_IBLOCK_ID']);
                                            $reviews->setTaskId($this->arResult['UF_ID']);
                                            $reviews->setUserId($userId);
                                            $reviews->setText($request->getPost('feedbackMessage-executor'));
                                            $reviews->setRating(intval($request->getPost('feedbackType')));
                                            $reviews->editReview();

                                            LocalRedirect($APPLICATION->GetCurPage(false), true);
                                        }
                                    }

                                    //����� �����������
                                    if ($request->getPost('feedbackMessage') && $this->arResult['IS_EXECUTOR']) {
                                        if (strlen($request->getPost('feedbackMessage')) > 0) {
                                            $executor = 0;
                                            foreach ($this->arResult['RESPONSES'] as $responseItem) {
                                                if (intval($responseItem['UF_EXECUTOR'])) {
                                                    $executor = intval($responseItem['UF_USER_ID']);
                                                    break;
                                                }
                                            }

                                            if ($this->arResult['IS_EXECUTOR']) {
                                                $reviews = new \Democontent2\Pi\Iblock\Reviews();
                                                $reviews->setIBlockId($this->arResult['UF_IBLOCK_ID']);
                                                $reviews->setTaskId($this->arResult['UF_ID']);
                                                $reviews->setUserId($executor);
                                                $reviews->setTo($this->arResult['UF_USER_ID']);
                                                $reviews->setText($request->getPost('feedbackMessage'));
                                                $reviews->setRating(intval($request->getPost('feedbackType')));
                                                $reviews->add();
                                            }
                                        }
                                    }
                                    break;
                            }

                            if (!$this->arResult['BLOCKED']) {
                                $response->setUserId($userId);

                                if (intval($this->arResult['UF_STATUS']) == 4) {
                                    if (count($this->arResult['MY_OFFER']) > 0) {
                                        if (intval($this->arResult['MY_OFFER']['UF_EXECUTOR']) > 0) {
                                            $response->setExecutorId(intval($this->arResult['MY_OFFER']['UF_USER_ID']));

                                            if ($request->getPost('unSetExecutor')) {
                                                $response->unSetExecutor($this->arResult['MY_OFFER']['ID']);
                                            }

                                            if ($request->getPost('confirmExecutor')) {
                                                $response->confirmExecutor($this->arResult['UF_USER_ID']);
                                            }
                                        }
                                    }
                                }

                                if (intval($this->arResult['UF_STATUS']) == 1 || intval($this->arResult['UF_STATUS']) == 4) {
                                    if (isset($this->arResult['CURRENT_USER']['UF_DSPI_EXECUTOR'])
                                        && intval($this->arResult['CURRENT_USER']['UF_DSPI_EXECUTOR'])
                                        && intval($this->arResult['UF_USER_ID']) !== intval($this->arResult['CURRENT_USER']['ID'])) {
                                        if ($request->getPost('text')) {
                                            if (!$this->arResult['RESPONSE_COST']) {
                                                $response->setText($request->getPost('text'));
                                                $response->setFiles($_FILES);
                                                $response->add($this->arResult['UF_USER_ID'], $request);
                                            } else {
                                                if ($this->arResult['BALANCE'] >= $this->arResult['RESPONSE_COST']) {
                                                    $response->setText($request->getPost('text'));
                                                    $response->setFiles($_FILES);
                                                    if ($response->add($this->arResult['UF_USER_ID'], $request) > 0) {
                                                        $account->setAmount($this->arResult['RESPONSE_COST']);
                                                        $account->setDescription(
                                                            \Bitrix\Main\Localization\Loc::getMessage(
                                                                'CLASS_DETAIL_ORDER_RESPONSE_PAY',
                                                                [
                                                                    '#ID#' => $this->arResult['UF_ID']
                                                                ]
                                                            )
                                                        );
                                                        $account->withdrawal();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            LocalRedirect($APPLICATION->GetCurPage(false), true);
                        }
                    }

                    if (isset($this->arResult['UF_USER_ID']) && $this->arResult['UF_USER_ID'] > 0) {
                        $us->setId($this->arResult['UF_USER_ID']);
                        $this->arResult['USER'] = $us->get();

                        $reviews = new \Democontent2\Pi\Iblock\Reviews();
                        $reviews->setUserId(intval($this->arResult['UF_USER_ID']));
                        $this->arResult['CURRENT_RATING'] = $reviews->rating();
                        $this->arResult['REVIEWS_COUNT'] = $reviews->getCountByUser();
                    }

                    if (intval($this->arResult['UF_STATUS']) == 3) {
                        $this->setTemplateName('completed');
                    } else {
                        if ($this->arResult['IS_EXECUTOR']) {
                            $this->setTemplateName('executor');
                        }
                    }

                    $this->stat();
                }
            }
        } else {

            CHTTP::SetStatus('404 Not Found');
            $this->setTemplateName('404');
        }
        $this->arResult['CITIES'] = $cities->getList();
        $this->arResult['CONTACTS_OPEN_COST'] = intval(\Bitrix\Main\Config\Option::get(DSPI, 'contacts_open'));
        $this->arResult['CURRENCY'] = \Bitrix\Main\Config\Option::get(DSPI, 'currency_name');


        $this->includeComponentTemplate();
    }

    private function createSafeCrowOrder(\Bitrix\Main\HttpRequest $request, $price)
    {
        if ($this->arResult['IS_OWNER'] && $this->arResult['EXECUTOR_ID'] > 0 && $this->arResult['UF_USER_ID'] > 0 && $price > 0) {
            if (strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiKey')) > 0
                && strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
                $us = new \Democontent2\Pi\User(0);

                $us->setId($this->arResult['EXECUTOR_ID']);
                $supplier = $us->get();

                $us->setId($this->arResult['UF_USER_ID']);
                $consumer = $us->get();

                if (count($consumer) > 0 && count($supplier) > 0) {
                    if (intval($consumer['UF_DSPI_SAFECROW_ID']) > 0 && intval($supplier['UF_DSPI_SAFECROW_ID']) > 0) {
                        $safeCrow = new \Democontent2\Pi\Payments\SafeCrow\SafeCrow();
                        $safeCrow->setTaskId($this->arResult['UF_ID']);
                        $safeCrow->setStageId($this->stageId);

                        $orderId = 0;
                        $orderStatus = '';

                        foreach ($this->arResult['SAFECROW_ORDERS'] as $safeCrowOrder) {
                            if ($safeCrowOrder['UF_TASK_ID'] == $this->arResult['UF_ID']) {
                                if ($this->stageId > 0) {
                                    if (intval($safeCrowOrder['UF_STAGE_ID']) == $this->stageId) {
                                        $orderId = intval($safeCrowOrder['UF_ORDER_ID']);
                                        $orderStatus = $safeCrowOrder['UF_STATUS'];
                                        break;
                                    }
                                } else {
                                    $orderId = intval($safeCrowOrder['UF_ORDER_ID']);
                                    $orderStatus = $safeCrowOrder['UF_STATUS'];
                                    break;
                                }
                            }
                        }

                        if ($orderId > 0) {
                            if ($orderStatus == 'pending') {
                                $orderParams = $safeCrow->getOrder($orderId);
                                if (count($orderParams) > 0 && isset($orderParams['id'])) {
                                    if ($orderParams['status'] == 'pending') {
                                        $payOrder = $safeCrow->payOrder(
                                            $orderId,
                                            (($request->isHttps()) ? 'https://' : 'http://') . $request->getHttpHost() . $request->getDecodedUri()
                                        );

                                        if (count($payOrder) > 0 && isset($payOrder['payment_url'])) {
                                            LocalRedirect($payOrder['payment_url'], true);
                                        }
                                    }
                                }
                            }
                        } else {
                            $cards = new \Democontent2\Pi\Payments\SafeCrow\Cards();
                            $cards->setUserId($supplier['ID']);
                            $executorCard = $cards->getUserCard();

                            //�������� �������� ���������� ����� �����������
                            if (count($executorCard) > 0) {
                                $safeCrow->setSupplierId($supplier['UF_DSPI_SAFECROW_ID']);
                                $safeCrow->setConsumerId($consumer['UF_DSPI_SAFECROW_ID']);
                                $safeCrow->setPrice($price);

                                if ($this->stageId > 0) {
                                    $safeCrow->setDescription(
                                        \Bitrix\Main\Localization\Loc::getMessage(
                                            'CLASS_DETAIL_ORDER_STAGE_DESCRIPTION',
                                            [
                                                '#ID#' => $this->arResult['UF_ID'],
                                                '#STAGE#' => $this->stageId
                                            ]
                                        )
                                    );
                                } else {
                                    $safeCrow->setDescription(
                                        \Bitrix\Main\Localization\Loc::getMessage(
                                            'CLASS_DETAIL_ORDER_DESCRIPTION',
                                            [
                                                '#ID#' => $this->arResult['UF_ID']
                                            ]
                                        )
                                    );
                                }

                                $safeCrow->setServiceCostPayer('consumer');
                                $safeCrow->setExtra(
                                    [
                                        'taskId' => intval($this->arResult['UF_ID']),
                                        'stageId' => $this->stageId,
                                        'iBlockId' => intval($this->arResult['UF_IBLOCK_ID']),
                                        'owner' => intval($consumer['ID']),
                                        'executor' => intval($supplier['ID'])
                                    ]
                                );
                                $order = $safeCrow->createOrder();

                                if (count($order) > 0 && isset($order['id']) && intval($order['id']) > 0) {
                                    $addCardToOrder = $safeCrow->addCardToOrder(
                                        intval($supplier['UF_DSPI_SAFECROW_ID']),
                                        intval($order['id']),
                                        intval($executorCard['UF_CARD_ID'])
                                    );

                                    if (count($addCardToOrder) > 0) {
                                        if (isset($addCardToOrder['supplier_payout_method_id']) && intval($addCardToOrder['supplier_payout_method_id']) > 0) {
                                            $payOrder = $safeCrow->payOrder(
                                                intval($order['id']),
                                                (($request->isHttps()) ? 'https://' : 'http://') . $request->getHttpHost() . $request->getDecodedUri()
                                            );

                                            if (count($payOrder) > 0 && isset($payOrder['redirect_url'])) {
                                                LocalRedirect($payOrder['redirect_url'], true);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function checkSafeCrowOrder(\Bitrix\Main\HttpRequest $request)
    {
        global $APPLICATION;

        if (strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiKey')) > 0
            && strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
            $orderId = explode('_', $request->get('orderId'));
            if (count($orderId) == 2 && intval($orderId[0]) > 0) {
                $safeCrow = new \Democontent2\Pi\Payments\SafeCrow\SafeCrow();
                $checkSafeCrowOrder = $safeCrow->getOrder(intval($orderId[0]));

                if (isset($checkSafeCrowOrder['id']) && intval($checkSafeCrowOrder['id'])) {
                    switch ($checkSafeCrowOrder['status']) {
                        case 'paid':
                            if (isset($checkSafeCrowOrder['extra']['taskId']) && intval($checkSafeCrowOrder['extra']['taskId']) == intval($this->arResult['UF_ID'])) {
                                if (count($this->arResult['SAFECROW_ORDERS']) > 0) {
                                    foreach ($this->arResult['SAFECROW_ORDERS'] as $safeCrowOrder) {
                                        if ($safeCrowOrder['UF_TASK_ID'] == $this->arResult['UF_ID']) {
                                            if (isset($checkSafeCrowOrder['extra']['stageId'])
                                                && intval($checkSafeCrowOrder['extra']['stageId']) == intval($safeCrowOrder['UF_STAGE_ID'])) {
                                                if ($safeCrow->setPaid($safeCrowOrder['ID'])) {
                                                    \Democontent2\Pi\Notifications::add(
                                                        $checkSafeCrowOrder['extra']['owner'],
                                                        'safeCrowOrderPaid',
                                                        [
                                                            'taskId' => $checkSafeCrowOrder['extra']['taskId'],
                                                            'iBlockId' => $checkSafeCrowOrder['extra']['iBlockId'],
                                                            'sum' => ($checkSafeCrowOrder['price'] / 100)
                                                        ]
                                                    );

                                                    \Democontent2\Pi\Notifications::add(
                                                        $checkSafeCrowOrder['extra']['executor'],
                                                        'safeCrowOrderPaid',
                                                        [
                                                            'taskId' => $checkSafeCrowOrder['extra']['taskId'],
                                                            'iBlockId' => $checkSafeCrowOrder['extra']['iBlockId'],
                                                            'sum' => ($checkSafeCrowOrder['price'] / 100)
                                                        ]
                                                    );

                                                    LocalRedirect($APPLICATION->GetCurPage(false), true);
                                                }
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                            break;
                    }
                }
            }
        }
    }

    private function escalateOrder($reason, $stageId = 0)
    {
        if (strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiKey')) > 0
            && strlen(\Bitrix\Main\Config\Option::get(DSPI, 'safeCrowApiSecret')) > 0) {
            if (count($this->arResult['SAFECROW_ORDERS']) > 0) {
                foreach ($this->arResult['SAFECROW_ORDERS'] as $safeCrowOrder) {
                    if ($safeCrowOrder['UF_TASK_ID'] == $this->arResult['UF_ID']) {
                        if ($stageId) {
                            if ($safeCrowOrder['UF_STAGE_ID'] == $stageId) {
                                $safeCrow = new \Democontent2\Pi\Payments\SafeCrow\SafeCrow();
                                $safeCrow->escalateOrder(intval($safeCrowOrder['UF_ORDER_ID']), $reason);
                                break;
                            }
                        } else {
                            $safeCrow = new \Democontent2\Pi\Payments\SafeCrow\SafeCrow();
                            $safeCrow->escalateOrder(intval($safeCrowOrder['UF_ORDER_ID']), $reason);
                            break;
                        }
                    }
                }
            }
        }
    }

    private function stat()
    {
        if (isset($this->arResult['UF_ID'])) {
            global $USER;
            $stat = new \Democontent2\Pi\Iblock\Stat(intval($this->arResult['UF_ID']), intval($this->arResult['UF_IBLOCK_ID']));
            $stat->setHlItemId(intval($this->arResult['ID']));
            if ($USER->IsAuthorized()) {
                $stat->setUserId(intval($USER->GetID()));
            }

            $stat->set();
            $stat->setTtl(3600);

            $this->arResult['STAT'] = [
                'TOTAL' => $stat->total(),
                'TODAY' => $stat->today()
            ];

            if ($USER->IsAuthorized()) {
                if ((intval($this->arResult['UF_USER_ID']) == intval($USER->GetID())) || $USER->IsAdmin()) {
                    $stat->setTablePostfix('phone');
                    $this->arResult['SHOW_PHONE_STAT'] = [
                        'TOTAL' => $stat->total(),
                        'TODAY' => $stat->today()
                    ];
                }

            }
        }
    }
}