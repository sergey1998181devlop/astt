<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 12.01.2019
 * Time: 17:31
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
?>
    <form class="filter-sorty"  method="get">
        <div class="row">
            <div class="col-sm-9 col-xxs-12">
                <div class="row">

                </div>
            </div>
            <div class="sorty-panel">
                <ul>
                    <li>
                            <?= Loc::getMessage('LIST_SORT_DATE_UP') ?>

                    </li>
                    <li>
                            <?= Loc::getMessage('LIST_SORT_DATE_DOWN') ?>
                    </li>
                    <li>
                            <?= Loc::getMessage('LIST_SORT_PRICE_DOWN') ?>
                    </li>
                    <li >
                            <?= Loc::getMessage('LIST_SORT_PRICE_UP') ?>
                    </li>

                </ul>
            </div>

        </div>
    </form>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}