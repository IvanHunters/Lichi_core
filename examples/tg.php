<?php
ini_set('log_errors', 'On');
ini_set('error_log', 'php_errors.log');

require "vendor/autoload.php";

$config["TG_TOKEN"]    = "";
$config["TG_PROXY"]   = "51.158.68.26:8811";

$event = new Lichi\TG\Callback($config);
//$event->set_webhook("https://..."); --set callback url
$event->handler(function($event_data){
    switch($event_data->type_event){
        case 'message_new':
            $event_data->message_send("good", ["keyboard"=>$event_data->keyboard_construct(["ok"])]);
        break;
    }
});
