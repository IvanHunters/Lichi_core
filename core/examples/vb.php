<?php
ini_set('log_errors', 'On');
ini_set('error_log', 'php_errors.log');

require "vendor/autoload.php";

$config["VB_TOKEN"]    = "";

$event = new Lichi\VB\Callback($config);
//$event->set_webhook("https://video.bot-os.ru/Lichi-soc/index.php");
$event->handler(function($event_data){
    switch($event_data->type_event){
        case 'message_new':
            $event_data->message_send("good");
        break;
    }
});
