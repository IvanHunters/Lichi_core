<?php
namespace Lichi\TG;

class Callback extends Api
{
    public function handler($callback){
        
        $data = $this->data = json_decode(file_get_contents('php://input'), true);
        
        if(isset($data["callback_query"])) $this->method_require = "keyboard_message";
        elseif(isset($data['message'])) $this->method_require = "message";
        else{
            print_r($this);
            die("\nFor Debug");
        }
        
        if($this->method_require == "message"){
            @$this->message                  = $this->data["message"];
            @$this->message_id               = $this->message['message']['message_id'];
            @$this->text                     = $this->message["text"];
            @$this->text_lower               = preg_replace("/\//","",mb_strtolower($this->text));
            
        }elseif ($this->method_require == "keyboard_message") {
            @$this->message                  = $this->data["callback_query"];
            @$this->message_id               = $this->message['message']['message_id'];
            @$this->message["chat"]["id"]    = $this->message["from"]["id"];
            @$this->text                     = $this->message["data"];
            @$this->text_lower               = preg_replace("/\//","",mb_strtolower($this->text));
        }
        
        $this->user_id                      = $this->message["from"]["id"];
        $this->chat_id                      = $this->message["chat"]["id"];
        
        if($this->user_id == $this->chat_id){
            $this->type_event                   = "message_new";
        }else{
            $this->type_event                   = "message_reply";
            $this->chat_id                      = $this->message["chat"]["id"];
        }
        
        $callback($this);
    }
    
    public function set_webhook($url){
        var_dump($this->curl("https://api.telegram.org/bot{$this->token}/setWebhook", array("url"=>$url)));
        die();
    }
}