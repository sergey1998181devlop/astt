<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 10.10.2018
 * Time: 09:09
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");?>
<div class="wrapper exeptionSucces" style="600px">
    <div class="exeptionSucces-block">

        <div class="exeptionSucces-block-center">
            <div class="page-title">
                <h1>Заявка отправлена</h1>
            </div>
            <div class="alert alert-info">
                Заявка будет опубликовано после проверки модератором
            </div>
            <div class="btn-wrap" >
                <a href="/user/create/" class=" col-md-6">
                    <div class="btn btn-send  btn-green" >
                        Подать заявку на другую технику
                    </div>
                </a>
                <a href="/user/tasks/" class=" col-md-6">
                    <div class="btn btn-send  btn-green" >
                        Мои заявки
                    </div>
                </a>
            </div>
        </div>

    </div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>