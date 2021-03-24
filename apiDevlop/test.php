<?php
//Scheme: https
//Hostname: api.lenta.ru
//User-Agent: Lenta/1.4.2 (iPhone; iOS 10.3.2; Scale/2.00)
//X-Lenta-Media-Type: 1
//Accept-Language: ru-RU;q=1, en-RU;q=0.9
//Accept: application/json

$messAllTags = [];
$apiStackMess = json_decode(file_get_contents('http://api.lenta.ru/rubrics'));
function pre($array){
    $mess =  "<pre>".print_r($array)."</pre>";
    return $mess;

}

foreach ($apiStackMess as $id => $value){
   print_r($value);
}




//// 1. инициализация
//$ch = curl_init();
//
//// 2. указываем параметры, включая url
//curl_setopt($ch, CURLOPT_URL, "http://api.lenta.ru/rubrics");
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//curl_setopt($ch, CURLOPT_HEADER, 0);
//
//// 3. получаем HTML в качестве результата
//$output = curl_exec($ch);
//
//// 4. закрываем соединение
//curl_close($ch);