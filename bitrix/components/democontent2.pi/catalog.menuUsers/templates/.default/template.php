<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 09.01.2019
 * Time: 13:40
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'createFrame')) {
    $frame = $this->createFrame()->begin();
}

?>

            <?foreach ($arResult['MENU'] as $keyMenuTitle => $itemTitle) {?>

                  <li class="col-md-6 col-sm-6 col-xxs-6 check-list-tasks">
                        <div class="form-group">
                            <input class="checkbox contract-inp CategoryTitle" type="checkbox" name="1_Work" value="Причина отклонения 1" id="<?=$itemTitle['code']?>">
                            <label class="form-checkbox" for="<?=$itemTitle['code']?>">
                                <b><?=$itemTitle['name']?></b>
                                <span class="icon-wrap ">
                                    <svg class="icon icon_checkmark">
                                          <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#checkmark"></use>
                                   </svg>
                               </span>
                            </label>
                        </div>

                    <? foreach ($itemTitle['items'] as $keyItem => $item){?>
                            <ul>

                                <li class="">
                                    <div class="form-group">
                                        <input class="checkbox contract-inp subCategoryFilter" type="checkbox" name="SubCategories" data-id-iblock="<?=$item['id']?>" value="<?=$item['id']?>" id="<?=$item['code']?>">
                                        <label class="form-checkbox" for="<?=$item['code']?>">
                                            <?=$item['name']?>
                                            <span class="icon-wrap ">
                                                  <svg class="icon icon_checkmark">
                                                       <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#checkmark"></use>
                                                  </svg>
                                            </span>
                                        </label>
                                    </div>
                                </li>
                            </ul>
                    <?}?>
                  </li>
            <?}?>
<?php
if (method_exists($this, 'createFrame')) {
    $frame->end();
}