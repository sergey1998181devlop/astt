<?php
$arParams = array("replace_space"=>"-","replace_other"=>"-");
$trans = \Cutil::translit($_FILES['__fileLOGO']['name'][0],"ru",$arParams);

$fileArray = array(
    "name" => $_FILES['__fileLOGO']['name'][0],
    "size" => $_FILES['__fileLOGO']['size'][0],
    "tmp_name" => $_FILES['__fileLOGO']['tmp_name'][0],
    "type" => $_FILES['__fileLOGO']['type'][0],

);
$fid = \CFile::SaveFile( $fileArray , false  , false , false , 'list');
$arFile = \CFile::GetFileArray($fid);