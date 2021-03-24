<?php
/**
 * Date: 26.09.2019
 * Time: 11:09
 * User: Ruslan Semagin
 * Company: PIXEL365
 * Web: https://pixel365.ru
 * Email: pixel.365.24@gmail.com
 * Phone: +7 (495) 005-23-76
 * Skype: pixel365
 * Product Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: https://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 * Use of this code is allowed only under the condition of full compliance with the terms of the license agreement,
 * and only as part of the product.
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}
\Bitrix\Main\Page\Asset::getInstance()->addJs('https://www.gstatic.com/firebasejs/7.0.0/firebase.js');
?>
    <script>
        BX.message({
            pushAjaxPath: '<?=$this->GetFolder()?>/ajax.php',
            vapidKey: '<?=\Bitrix\Main\Config\Option::get(DSPI, 'firebase_web_push_key')?>',
            senderId: '<?=\Bitrix\Main\Config\Option::get(DSPI, 'firebase_sender_id')?>'
        });
    </script>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}

