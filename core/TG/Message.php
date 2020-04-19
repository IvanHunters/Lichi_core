<?php
namespace Lichi\TG;

trait Message
{

    public function message_send($message = "", $other_param = array(), $flag_user = false){
        
        if(is_array($message) || is_object($message)){
          $message = var_export($message, true);
        }
        
        if(!isset($other_param['chat_id'])){
            $other_param['chat_id'] = $this->user_id;
        }
        
        if(isset($other_param['keyboard'])){
            $other_param['reply_markup'] = $other_param['keyboard'];
            unset($other_param['keyboard']);
        }
        
        $other_param['text'] = $message;
        $other_param['disable_web_page_preview'] = true;
        
        if($this->file_upload){
            switch($this->type_upload){
                case 'photo':
                    $array_parametrs['photo'] =  new \CURLFile($this->file_upload);
                    $response = $this->photo_send($array_parametrs);
                break;
                
                case 'file':
                    $array_parametrs['document'] =  new \CURLFile($this->file_upload);
                    $response = $this->document_send($array_parametrs);
                break;
            }
        }else{
            if(!is_null($this->message_id)){
                $other_param["message_id"] = $this->message_id;
                $response = $this->callApi("editMessageText", $other_param);
            }else{
                $response = $this->callApi("sendMessage", $other_param); 
            }
        }
        return $response;
    }

}


 ?>
