<?php

use Bitrix\Main\Type\DateTime;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $APPLICATION;
global $USER;

$iblokId = 1;
$element = 897;
$arFilter = array(
    "CREATED_BY" => $USER->GetID(),
    "IBLOCK_ID" => $iblokId,
    "NAME" => $data['city'],
);

$rsItems = \CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter, false, false, Array());
$element = [];
while($res = $rsItems->GetNextElement())
{
    $arFields = $res->GetFields();
    if(!empty($arFields['ID'])){
        $element = $arFields;
    }

}
//если есть город такой ) то сохраняю старый айдишник / иначе добавля элемент (город) и добавляю айди с новым городом
if(!empty($element['ID'])){
    $idCiti = $element['ID'];
}else{

    $el = new CIBlockElement;

    $PROP = array();
    $PROP[12] = "Белый";  // свойству с кодом 12 присваиваем значение "Белый"
    $PROP[3] = 38;        // свойству с кодом 3 присваиваем значение 38

    $arLoadProductArray = Array(
        "NAME"    => $data['city'] , // элемент изменен текущим пользователем
    );

    if($PRODUCT_ID = $el->Add($arLoadProductArray)){

    }


}



