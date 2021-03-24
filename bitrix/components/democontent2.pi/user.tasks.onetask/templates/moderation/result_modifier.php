<?php
$el = new \CIBlockElement();

$arSelect = Array("ID", "NAME", "PROPERTY___hidden_moderation" , "PROPERTY___hidden_moderation_reason");
$arFilter = Array("IBLOCK_ID"=>$arResult['UF_IBLOCK_ID'], "ID" => $arResult['UF_ID'],  "ACTIVE"=>"Y");
$res = $el->GetList(Array(), $arFilter, false, false, $arSelect);
while($ob = $res->GetNextElement())
{
    $arFields = $ob->GetFields();

    $arResult['MODERATION'] = $arFields['PROPERTY___HIDDEN_MODERATION_VALUE'];
    $arResult['MODERATION_REASON'] = $arFields['~PROPERTY___HIDDEN_MODERATION_REASON_VALUE']['TEXT'];
}

