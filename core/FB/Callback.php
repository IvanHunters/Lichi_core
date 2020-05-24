<?php
namespace Lichi\FB;

class Callback extends Api
{
    public function handler($callback){
        
        if(isset($_GET['hub_mode']) && $_GET['hub_mode'] == "subscribe" && $_GET['hub_verify_token'] == $this->confirm_token) 
            die($_GET["hub_challenge"]);
        
        $this->data                         = json_decode(file_get_contents('php://input'), true);

        $this->message          = $this->data['entry'][0]['messaging'];
        $this->user_id          = $this->message[0]['sender']['id'];
        $this->recipient_id     = $this->message[0]['recipient']['id'];
        
        if(isset($this->message[0]['delivery']) || isset($this->message[0]['read'])) 
            die();
        
        if(!isset($this->message[0]['postback']))
            $this->text         = $this->message[0]['message']['text'];
        else
            $this->text         = $this->message[0]['postback']['title'];
            
        $this->text_lower       = mb_strtolower($this->text);
        
        if($this->sender_id != $this->group_id){
            $this->type_event                   = "message_new";
        }else{
            $this->type_event                   = "message_reply";
        }
        
        $this->username                      = "?";

        $callback($this);
    }

    public function set_webhook($url){
        return $this->callApi("set_webhook", array("url"=>$url));
    }
}
