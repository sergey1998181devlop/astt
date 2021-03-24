<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Event;
use Bitrix\Main;
use Bitrix\Main\Authentication\ApplicationPasswordTable;

//проверяю пользователя и его контрольную строку
$rsUser = CUser::GetByLogin($arParams['USER_LOGIN']);
$arUser = $rsUser->Fetch();

if(!empty($_GET['change_password'])){
    if(empty($arParams['CHECKWORD']) || empty($arParams['USER_LOGIN']) || empty($_GET['change_password'])){
        LocalRedirect('/', true);
    }

    if( trim((string) $arUser['CHECKWORD']) === trim((string)$arParams['CHECKWORD'])){

        $this->IncludeComponentTemplate();
    }else{
        LocalRedirect('/', true);

    }
}


if(!empty($_POST['change_password_user'])){
    if($_POST['change_password_user'] == "yes" && !empty($_POST['USER_LOGIN']) && !empty($_POST['NEWPASS_USER'] && !empty($_POST['NEWPASS_USER_REPEAT'])) ) {
        $salt = randString(8);
        $checkword = md5(CMain::GetServerUniqID().uniqid());
        $rsUser = CUser::GetByLogin($_POST['USER_LOGIN']);
        $arUser = $rsUser->Fetch();
        $user = new CUser;
        $fields = array(
            "PASSWORD" => trim($_POST['NEWPASS_USER']),
            "CONFIRM_PASSWORD" => trim($_POST['NEWPASS_USER_REPEAT']),
            "CHECKWORD" => $salt.md5($salt.$checkword)
        );
        $r = $user->Update($arUser['ID']  ,$fields);//обновляю пароль пользователя  / редеректю в личныйй кабинет

        $USER->Authorize($arUser['ID']);
        LocalRedirect('/user/settings/', true);
    }else{

    LocalRedirect('/', true);

    }
}






