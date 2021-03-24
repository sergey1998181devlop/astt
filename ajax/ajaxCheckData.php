<?php
global $APPLICATION;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Mail\Event;
use Bitrix\Main;
use Bitrix\Main\Authentication\ApplicationPasswordTable;
use Bitrix\Main\Localization\Loc;
function getTemplateMessege($code){
    $template = file_get_contents('templateMessageAjax.php');
    $str = ' <h2 style="font-size: 36px; font-family: Helvetica, Arial, sans-serif; color: #333 !important; margin: 0px;">Код подтверждения</h2>
    <p style="margin-top: 30px">Внимание! При смене почты поменяется и ваш логин для входа.Ваш код подтверждения -  '.$code.'</p><br>
    ';
    $message =  str_replace('#WORK_AREA#' , $str , $template);
    return $message;
}
function smsSender($codeBase64 , $phone){
    require_once 'sms.ru.php';
    $codeBase64 = base64_decode($codeBase64);;
    $smsru = new SMSRU('A0167F15-F7C8-1183-CFB5-AED38CFDC70E'); // Ваш уникальный программный ключ, который можно получить на главной странице
    $data = new stdClass();
    $data->to =  $phone;
    if(!empty($_POST['NumberAdmin'])){
        //если отправляет сотрудник / то беру адйи админа и отправляю код ему
        $idAdminComany = (int) $_POST['NumberAdmin'];
        $rsUser = CUser::GetByID($idAdminComany);
        $arUser = $rsUser->Fetch();
        $data->to =  $arUser['PERSONAL_PHONE'];
    }
    $data->text = 'Ваш код подтверждения - '.$codeBase64; // Текст сообщения
    if(!empty($_POST['NumberAdmin'])){
        $data->text = 'Сотрудник '.$_POST['UserNameEmpl'].' '.$_POST['UserFemaleEmpl'].' изменил номер телефона.Его код подтверждения - '.$codeBase64; // Текст сообщения
    }
    $data->json = 1;
    $data->daytime = 1;
    $data->from = 'astt.su';
    $sms = $smsru->send_one($data); // Отправка сообщения и возврат данных в переменную
    if ($sms->status == "OK") {
        $status = [
            'message' => "OK"
        ];
    }else{
        $status = [
//            'message' => $sms->status,
//            'status_error' => $sms->status_code,
            'message' => $sms->status_text
        ];
    }
    $status =  json_encode($status);
    return $status;
}
function mailSender($codeBase64 , $mail){
    $codeBase64 = base64_decode($codeBase64);;
    $message = getTemplateMessege($codeBase64);
    $status = custom_mail( $mail , 'Ваш код подтверждения на сайте '.$_SERVER['SERVER_NAME'], $message);
    if($status == 1){
        $status = [
            'message' => "OK"
        ];
    }else{
        $status = [
            'message' => "Ошибка отправки"
        ];
    }
    return json_encode($status);
}
function updateDataUser($type , $value){
    $fields = [];
    $messegetext = '';
    if($type == 'mail'){
        $messegetext = 'Ваш email и логин успешно обновлены';
        $fields["EMAIL"] =  $value;
        $fields["LOGIN"] =  $value;
    }elseif ($type == 'phone'){
        $messegetext = 'Ваш телефон успешно обновлен';
        $fields["PERSONAL_PHONE"] =  $value;
        $fields["UF_CONFIRMED"] =  'true';
    }
    $user = new CUser;

    $r = $user->Update( $user->GetID()  ,$fields);
    if($r === true){
        $status = [
            'status' => "OK",
            'messageText' => $messegetext
        ];
    }else{
        $status = [
            'message' => "Ошибка обновления",
        ];
    }
    return json_encode($status);
}

//если отправили тип маил  - отправляю код на маил
if($_POST['typeCheck'] == 'mail'){
    $status = mailSender($_POST['codeBase64'] , $_POST['dataCheck'] );
    echo $status;
}
//если отправили тип sms  - отправляю код по sms
if($_POST['typeCheck'] == 'phone'){
    $status = smsSender($_POST['codeBase64'] , $_POST['dataCheck'] );
    echo $status;
}
//если отправили - подтверждение проверки кода  -  обновляю по типу
if($_POST['codeSuccess'] == 'Y'){
    $status = updateDataUser($_POST['typeCheckAll'] , $_POST['typeValueAll']);
    echo $status;
}
//! Если обновляет данные модератор / то данные просто обновляются / если обновляет данные сотрудник  -  мы это узнаем по доп параметрам  - то код уйдет на номер модератора для безопасности
//все решает один параметр