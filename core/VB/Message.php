<?php
namespace Lichi\VB;

trait Message
{

    public function message_send($message = "", $other_param = array(), $flag_user = false){
        
        if(is_array($message) || is_object($message)){
          $message = var_export($message, true);
        }
        
        if(!isset($other_param['receiver'])){
            $other_param['receiver'] = $this->user_id;
        }
        
        $other_param['text'] = $message;
        $other_param['type'] = "text";
        
        $response = $this->callApi("send_message", $other_param);
        return $response;
    }

}


 ?>
