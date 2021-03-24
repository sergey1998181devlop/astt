<?php
global $APPLICATION;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once 'sms.ru.php';
use Bitrix\Main\Mail\Event;
use Bitrix\Main;
use Bitrix\Main\Authentication\ApplicationPasswordTable;
use Bitrix\Main\Localization\Loc;
$smsru = new SMSRU('A0167F15-F7C8-1183-CFB5-AED38CFDC70E'); // Ваш уникальный программный ключ, который можно получить на главной странице
if($_POST['Forrgot'] == 'Y' && !empty($_POST['login'])){
    if($_POST['type'] == 'restorePasswordPhone'){
        $_POST['login'] = preg_replace('![^0-9]+!', '', $_POST['login']);
    }
    $rsUser = CUser::GetByLogin($_POST['login']);
    $arUser = $rsUser->Fetch();

    if(!empty($arUser['ID'])){

        //если такой пользователь существует , обновляю ему пароль и кидаю на тел или мыло

        global $USER;
        $pass = randString(8);
        $fields = array(
            "PASSWORD"=> $pass,//пароль
            "CONFIRM_PASSWORD"=> $pass //подтверждение пароля
        );

        $r = $USER->Update( $arUser['ID'] ,$fields);//$USER->GetID() означает что изменение производится у текущего пользователя


        if($r === true){
            if($_POST['type'] == 'restorePasswordEmail'){
                //отправля новый пароль на мыло
                $template = file_get_contents('templateMessageAjax.php');
                $str = ' <h2 style="font-size: 36px; font-family: Helvetica, Arial, sans-serif; color: #333 !important; margin: 0px;">Востановление пароля</h2>
                        Ваш новый пароль на сайте - '.$pass;


                $message =  str_replace('#WORK_AREA#' , $str , $template);
                $status = custom_mail( $_POST['login'] , 'Новый  пароль  для сайта - '.$_SERVER['SERVER_NAME'], $message);

                if($status == 1){
                    $status = [
                        'message' => "FORRGOT_SUCCESS",
                        'message_text' => 'Ваш новый пароль отправлен вам на почту'
                    ];
                    echo json_encode($status);
                }else{
                    $status = [
                        'message' => "FORRGOT_ERROR",

                    ];
                    echo json_encode($status);
                }

            }
            if($_POST['type'] == 'restorePasswordPhone'){
                $_POST['login'] = preg_replace('![^0-9]+!', '', $_POST['login']);
                //отправля новый пароль на телефон
                $data = new stdClass();
                $data->to =  $_POST['login'];
                $data->text = 'Ваш новый пароль на сайте - '.$pass; // Текст сообщения
                $data->json = 1;
                $data->daytime = 1;
                $data->from = 'astt.su';
// $data->from = ''; // Если у вас уже одобрен буквенный отправитель, его можно указать здесь, в противном случае будет использоваться ваш отправитель по умолчанию
// $data->time = time() + 7*60*60; // Отложить отправку на 7 часов
// $data->translit = 1; // Перевести все русские символы в латиницу (позволяет сэкономить на длине СМС)
//                $data->test = 1; // Позволяет выполнить запрос в тестовом режиме без реальной отправки сообщения
// $data->partner_id = '1'; // Можно указать ваш ID партнера, если вы интегрируете код в чужую систему
                $sms = $smsru->send_one($data); // Отправка сообщения и возврат данных в переменную

                if ($sms->status == "OK") { // Запрос выполнен успешно
                    $status = [
                        'message' => "FORRGOT_SUCCESS",
                        'message_text' => 'Ваш новый пароль отправлен вам на почту'
                    ];
                    echo json_encode($status);
                } else {
                    $status = [
                        'message' => 'FORRGOT_ERROR',
                        'status_error' => $sms->status_code,
                        'message_text' => $sms->status_text
                    ];
                    echo json_encode($status);
                }


            }
        }



    }else{
        $status = [
            'message' => "FORRGOT_ERROR",

        ];
        if($_POST['type'] == 'restorePasswordEmail'){
            $status['message_text'] =  "Пользователь с данным email не существует";
        }
        if($_POST['type'] == 'restorePasswordPhone'){
            $status['message_text'] =  "Пользователь с данным номером не существует";
        }
        echo json_encode($status);
    }


}
if($_POST['Auth'] == 'Y'){

    global $USER;
    $login = '';
    if(!empty($_POST['authEmail'] )){
        $login = $_POST['authEmail'];
    }
    if(!empty($_POST['authPhone'] )){
        $login = $_POST['authPhone'];
        $login = preg_replace('![^0-9]+!', '', $login);;
    }

    $rsUser = CUser::GetByLogin( $login );
    $arUser = $rsUser->Fetch();


    if (!is_object($USER)) $USER = new CUser;


    $passN = md5($_POST['authPassword']);

    $arAuthResult = '';
    $ErMessage = '';

    if(isset($arUser['ID'])){
        if($passN == $arUser['PASSWORD']){

            $arAuthResult = $USER->Authorize($arUser['ID']);
            $arAuthResult = 'OKAUTHUSER';

        }else{
            $ErMessage = 'Вы ввели не правильный логин или пароль';
            $arAuthResult = "ERROR";

        }
    }else{
        $ErMessage = 'Пользователя не существует';
        $arAuthResult = 'ERROR';
    }



    if($arAuthResult == 'ERROR'){
        $status = [
            'message' => "ERROR_AUTH",
            'status_text' => $ErMessage
        ];
        echo json_encode($status);
    }else{

        $status = [
            'message' => "OKAUTH",

        ];
        echo json_encode($status);
    }
}

if(!empty($_POST['email']) && !isset($_POST['registration'])){
    $rsUser = CUser::GetByLogin($_POST['email']);
    $arUser = $rsUser->Fetch();
    if(empty($arUser['ID'])){
        $_POST['message'] = base64_decode($_POST['message']);

        $template = file_get_contents('templateMessageAjax.php');
        $str = ' <h2 style="font-size: 36px; font-family: Helvetica, Arial, sans-serif; color: #333 !important; margin: 0px;">Подтверждение регистрационных данных</h2>
Ваш код для регистрации на сайте - '.$_POST['message'];


        $message =  str_replace('#WORK_AREA#' , $str , $template);
        $status = custom_mail( $_POST['email'] , 'Код подтверждение для регистрации на сайте - '.$_SERVER['SERVER_NAME'], $message);
        if($status == 1){
            $status = [
                'message' => "OKMAIL"
            ];
            echo json_encode($status);
        }else{
            $status = [
                'message' => "ERRORSEND"
            ];
            echo json_encode($status);
        }
    }else{
        $status = [
            'message' => "REPEAT_USER",
            'message_text' => "Пользователь с данным email уже существует"
        ];
        echo json_encode($status);
    }

}
elseif (!empty($_POST['phone'])  && !isset($_POST['registration'] )){
    $_POST['phone'] = preg_replace('![^0-9]+!', '', $_POST['phone']);
    $rsUser = CUser::GetByLogin($_POST['phone']);
    $arUser = $rsUser->Fetch();
    if(empty($arUser['ID'])){
        //    $_POST['daytime'] = 1;
//    $_POST['test'] = 1;
//    $_POST['json'] = 1;

        $_POST['message'] = base64_decode($_POST['message']);
        $data = new stdClass();
        $data->to =  $_POST['phone'];
        $data->text = 'Ваш код для регистрации на сайте - '.$_POST['message']; // Текст сообщения
        $data->json = 1;
        $data->daytime = 1;
        $data->from = 'astt.su';
// $data->from = ''; // Если у вас уже одобрен буквенный отправитель, его можно указать здесь, в противном случае будет использоваться ваш отправитель по умолчанию
// $data->time = time() + 7*60*60; // Отложить отправку на 7 часов
// $data->translit = 1; // Перевести все русские символы в латиницу (позволяет сэкономить на длине СМС)
//        $data->test = 1; // Позволяет выполнить запрос в тестовом режиме без реальной отправки сообщения
// $data->partner_id = '1'; // Можно указать ваш ID партнера, если вы интегрируете код в чужую систему
        $sms = $smsru->send_one($data); // Отправка сообщения и возврат данных в переменную

        if ($sms->status == "OK") { // Запрос выполнен успешно
            $status = [
                'message' => "OKSMS"
            ];
            echo json_encode($status);
        } else {
            $status = [
                'message' => $sms->status,
                'status_error' => $sms->status_code,
                'status_text' => $sms->status_text
            ];
            echo json_encode($status);
        }
    }else{
        $status = [
            'message' => 'REPEAT_USER',
            'message_text' => "Пользователь с данным номером уже существует"
        ];
        echo json_encode($status);
    }


}
//регистрация
elseif ($_POST['registration'] == 'Y'){
    $user = new CUser;
    $_POST['phone'] = preg_replace('![^0-9]+!', '', $_POST['phone']);
    $pass_new_gen = randString(8);
    $arFile = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/democontent2.pi/images/2.png");
    $arFields = array(
        'NAME'             => '',
        'LAST_NAME'        => '',
        'LID'              => 'ru',
        'ACTIVE'           => 'Y',
        'PASSWORD'         => $pass_new_gen, // минимум 8 символов
        'CONFIRM_PASSWORD' => $pass_new_gen,
        'GROUP_ID'         => array(5),
        'PERSONAL_PHOTO'   => $arFile,
        "UF_MODERATION"       => 'Y',
        "UF_CONFIRMED" => 'false',
        "UF_MODERATION_ACCESS" => 'false'

    );

    if(!empty($_POST['email'])){
        $arFields['EMAIL'] = $_POST['email'];
        $arFields['LOGIN'] = $_POST['email'];

    }
    if(!empty($_POST['phone'])){
//        $arFields['EMAIL'] = $_POST['phone'];
        $arFields['LOGIN'] = $_POST['phone'];
        $arFields['PERSONAL_PHONE'] = $_POST['phone'];
    }
//    $count
    //Отправляю регистрационные данные на почту новому пользователю

    $template = file_get_contents('templateMessageAjax.php');
    $str = ' <h2 style="font-size: 36px; font-family: Helvetica, Arial, sans-serif; color: #333 !important; margin: 0px;">Новый пользователь успешно создан</h2>
    <p style="margin-top: 30px">Ваш логин - '.$arFields['LOGIN'].'</p><br>
    <p style="margin-top: 7px">Ваш пароль - '.$pass_new_gen.'</p><br>
    ';


    $message =  str_replace('#WORK_AREA#' , $str , $template);
    $status = custom_mail( $_POST['email'] , 'Вы успешно зарегистрированны на сайте  - '.$_SERVER['SERVER_NAME'], $message);


    $rsUser = CUser::GetByLogin($arFields['LOGIN']);
    $arUser = $rsUser->Fetch();
    if(empty($arUser['ID'])){
        $ID = $user->Add($arFields);

        if ((int)$ID > 0) {
            global $USER;
            $USER->Authorize($ID);
            $status = [
                'message' => "REGOK"
            ];
            echo json_encode($status);
        } else {

            $status = [
                'message' => $user->LAST_ERROR
            ];
            echo json_encode($status);
        }
    }else{
        $status = [
            'message' => 'REPEAT_USER'
        ];
        echo json_encode($status);
    }
}