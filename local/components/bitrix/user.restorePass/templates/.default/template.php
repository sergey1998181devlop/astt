<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.01.2019
 * Time: 10:34
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
global $USER;
use Bitrix\Main\Localization\Loc;?>



<div class="container-fluid d-flex h-100 justify-content-center align-items-center p-0">

    <div class="row bg-white shadow-sm">

        <div class="col border rounded p-4" style="text-align: center">
            <h3 class="text-center mb-4">Активация учетной записи</h3>
            <div class="" style="width: 400px;display: inline-block;margin-top: 70px">

                <form method="post" enctype="multipart/form-data"  id="active-employees" action="<?= $APPLICATION->GetCurPage(false) ?>">

                    <div class="form-group">
                        <label for="exampleInputPassword1">Придумайте пароль</label>
                        <input type="password" name="NEWPASS_USER" value="" class="form-control" id="exampleInputPassword1">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Подтверждение нового пароля</label>
                        <input type="password" class="form-control" name="NEWPASS_USER_REPEAT" value="" id="exampleInputPassword1">
                    </div>
                    <input type="hidden" name="USER_LOGIN" value="<?=$_GET['USER_LOGIN']?>">
                    <input type="hidden" name="change_password_user" value="yes">
                    <button type="" class="btn btn-primary w-100 active-account">Активировать</button>
                </form>
            </div>

        </div>
    </div>
</div>



