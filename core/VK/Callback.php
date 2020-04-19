<?php
namespace Lichi\VK;

class Callback extends Api
{
    function response($return = true){
        ignore_user_abort(true);
        ob_start();
        if($return) echo ('ok');
        else exit($this->confirm_token);
        $size = ob_get_length();
        header("Content-Encoding: none");
        header("Content-Length: {$size}");
        header("Connection: close");
        ob_end_flush();
        ob_flush();
        flush();
        if (function_exists('fastcgi_finish_request'))  fastcgi_finish_request();
        if(session_id()) session_write_close();
    }
    
    public function handler($callback){
        $this->data = $data = json_decode(file_get_contents('php://input'));
        if($data->type == "confirmation")   $this->response(false);
        else $this->response();
        
        $this->secret_key_request  = @$this->data->secret;
        if($this->secret_key && $this->secret_key_request != $this->secret_key) return false;
        $this->type_event   = $this->data->type;
        $this->client_info  = @$this->data->object->client_info;
        $this->chat_id      = @$data->object->message->peer_id;
        $this->user_id      = @$data->object->message->from_id;
        $this->group_id     = @$data->group_id;
        $this->id_message   = @$data->object->message->id;
        $this->text         = @$data->object->message->text;
        $this->text_lower   = @preg_replace("/\[(.*)\]\s/","",mb_strtolower($this->text));
        $this->publish_date = @$data->object->message->date;
	    
        $callback($this);
    }
}