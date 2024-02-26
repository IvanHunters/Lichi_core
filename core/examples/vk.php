<?php
ini_set('log_errors', 'On');
ini_set('error_log', 'php_errors.log');

require "vendor/autoload.php";

$config["VK_TOKEN_USER"]    = "";
$config["VK_TOKEN_GROUP"]   = "";
$config["VK_TOKEN_CONFIRM"] = "";
$config["VK_SECRET_KEY"]    = "";

$event = new Lichi\VK\Callback($config);

$event->handler(function($event_data){
    switch($event_data->type_event){
        case 'message_new':
            $event_data->message_send("good", ["keyboard"=>$event_data->keyboard_construct(["ok"])]);
        break;
    }
});
