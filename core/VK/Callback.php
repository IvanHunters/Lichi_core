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
        $this->username     = @$this->CallHowGroup("users.get", array("user_ids"=>$this->user_id))['response'][0]['first_name'];
        $this->group_id     = @$data->group_id;
        $this->message_id   = @$data->object->message->id;
        $this->text         = @$data->object->message->text;
        $this->text_lower   = @preg_replace("/\[(.*)\]\s/","",mb_strtolower($this->text));
        $this->publish_date = @$data->object->message->date;
	    
        $callback($this);
    }
    
    public function set_webhook($url, $secret_key){
        $group_id = $this->CallHowGroup('groups.getById', ['']);
        if(isset($group_id['response'])){
            $group_id = $group_id['response'][0]['id'];
            $server_id = $this->CallHowGroup('groups.addCallbackServer', ["url"=>$url, "title"=>"Lichi", 'group_id'=>$group_id, 'secret_key'=>$secret_key])['response']['server_id'];
            $code_connect = $this->CallHowGroup('groups.getCallbackConfirmationCode', ["group_id"=>$group_id])['response']['code'];
            return ['group_id'=>$group_id, 'server_id'=>$server_id, 'code_connect'=>$code_connect];
        }
        else
        {
            return false;
        }
    }
    
    public function enable_bot($group_id, $server_id){
        var_dump($this->CallHowGroup('groups.setCallbackSettings', ["group_id"=>$group_id, "server_id"=>$server_id, "api_version"=>"5.103", "message_new"=>1, "message_reply"=>1, "message_allow"=>1, "message_typing_state"=>1]));
    }
}