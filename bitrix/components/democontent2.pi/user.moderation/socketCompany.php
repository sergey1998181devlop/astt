<?php

global $APPLICATION;


$root_dir = 'develop.su';
$dir = explode($root_dir, __DIR__);
define ('PATH', $dir[0].$root_dir.'/');

require_once PATH.'vendor/autoload.php';
require_once PATH.'/Workerman-master/Autoloader.php';
use Democontent2\Pi\Hl;
use Bitrix\Main\Application;


// Create a Websocket server
$ws_worker = new  \Workerman\Worker('websocket://192.168.1.10:61523');


// 4 processes
$ws_worker->count = 4;

// Emitted when new connection come
$ws_worker->onConnect = function ($connection) {
    $connection->send('This message was sent from Backend(index.php), when server was started.');
    echo "New connection\n";

    //первый раз гружу все записи и отбираю компанию с самой последней датой создания

};



\Workerman\Lib\Timer::add(1  , function () use ($connection) {
    //начиная с второго запроса ищу все что позднее даты созднаия  последней задачи


});


// Emitted when data received
$ws_worker->onMessage = function ($connection, $data) {
    // if, server got message from frontend, server send message to Frontend $data
    $connection->send($data);
};




// Emitted when connection closed
$ws_worker->onClose = function ($connection) {
    echo "Connection closed\n";
};

// Run worker
\Workerman\Worker::runAll();