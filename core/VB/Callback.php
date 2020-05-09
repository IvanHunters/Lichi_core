<?php
namespace Lichi\VB;

class Callback extends Api
{
    public function handler($callback){
        
        $data = $this->data = json_decode(file_get_contents('php://input'), true);
        
        @$this->message                     = $this->data["message"];
        @$this->message_type                = $this->message["type"];
        @$this->text                        = $this->message["text"];
        @$this->text_lower                  = preg_replace("/\//","",mb_strtolower($this->text));
        
        $this->user_id                      = $this->data["sender"]["id"];
        $this->user_name                      = $this->data["sender"]["name"];
        
        if($this->data["event"] == 'message'){
            $this->type_event                   = "message_new";
        }else{
            $this->type_event                   = "message_reply";
        }
        
        $callback($this);
    }
    
    public function set_webhook($url){
        return $this->callApi("set_webhook", array("url"=>$url));
    }
}