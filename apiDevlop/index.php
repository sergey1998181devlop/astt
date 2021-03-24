<?php
require_once 'debugFunctions/functions.php';
include 'Classes/MysqlClass.php';
function array_key_first(array $arr) {
    foreach($arr as $key => $unused) {
        return $key;
    }
    return NULL;
}
$class_name = array_key_first($_GET);

require_once 'Classes/'.'Mysql'. 'Class.php';
require_once 'Classes/'.$class_name. 'Class.php';


if($_GET['News'] == 'Y'){
    $Object = new NewsClass();



    $result = $Object->sort($_GET['section'] , $_GET['subSection']);
    $mess = [];
    foreach ($result as $record) {
        $mess['items'][] = $record;
    }
    $mess = json_encode($mess);
    echo $mess;




}




