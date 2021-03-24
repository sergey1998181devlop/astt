<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 22.10.2018
 * Time: 14:15
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

\Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/socket.io/socket.io.js');
\Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/timeago/jquery.timeago.js');

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}
?>
    <script>
        var username = '<?=\Democontent2\Pi\Utils::getChatId($USER->GetID())?>',
            __siteDir = '<?= SITE_DIR ?>',
            __socketIoTemplatePath = '<?= $this->GetFolder() ?>',
            __siteTemplatePath = '<?=SITE_TEMPLATE_PATH ?>',
            __nodeJsHost = '<?=\Bitrix\Main\Config\Option::get(DSPI, 'nodeJsHost')?>';

        (function () {
            function numpf(n, f, s, t) {
                var n10 = n % 10;
                if ((n10 === 1) && ((n === 1) || (n > 20))) {
                    return f;
                } else if ((n10 > 1) && (n10 < 5) && ((n > 20) || (n < 10))) {
                    return s;
                } else {
                    return t;
                }
            }

            jQuery.timeago.settings.strings = {
                prefixAgo: null,
                prefixFromNow: "<?=Loc::getMessage('TIME_ACROSS')?>",
                suffixAgo: "<?=Loc::getMessage('TIME_BACK')?>",
                suffixFromNow: null,
                seconds: "<?=Loc::getMessage('TIME_LESS_MINUTE')?>",
                minute: "<?=Loc::getMessage('TIME_MINUTE_4')?>",
                minutes: function (value) {
                    return numpf(value, "%d <?=Loc::getMessage('TIME_MINUTE_1')?>", "%d <?=Loc::getMessage('TIME_MINUTE_2')?>", "%d <?=Loc::getMessage('TIME_MINUTE_3')?>");
                },
                hour: "<?=Loc::getMessage('TIME_HOUR_1')?>",
                hours: function (value) {
                    return numpf(value, "%d <?=Loc::getMessage('TIME_HOUR_1')?>", "%d <?=Loc::getMessage('TIME_HOUR_2')?>", "%d <?=Loc::getMessage('TIME_HOUR_3')?>");
                },
                day: "<?=Loc::getMessage('TIME_DAY_1')?>",
                days: function (value) {
                    return numpf(value, "%d <?=Loc::getMessage('TIME_DAY_1')?>", "%d <?=Loc::getMessage('TIME_DAY_2')?>", "%d <?=Loc::getMessage('TIME_DAY_3')?>");
                },
                month: "<?=Loc::getMessage('TIME_MONTH_1')?>",
                months: function (value) {
                    return numpf(value, "%d <?=Loc::getMessage('TIME_MONTH_1')?>", "%d <?=Loc::getMessage('TIME_MONTH_2')?>", "%d <?=Loc::getMessage('TIME_MONTH_3')?>");
                },
                year: "<?=Loc::getMessage('TIME_YEAR_1')?>",
                years: function (value) {
                    return numpf(value, "%d <?=Loc::getMessage('TIME_YEAR_1')?>", "%d <?=Loc::getMessage('TIME_YEAR_2')?>", "%d <?=Loc::getMessage('TIME_YEAR_3')?>");
                }
            };
        })();
    </script>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}