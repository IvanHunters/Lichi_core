<?php
namespace Lichi\TG;

trait Message
{
    public $file_upload = false;
    public function message_send($message = "", $other_param = array(), $flag_user = false){

        $response = null;
        if(is_array($message) || is_object($message)){
          $message = var_export($message, true);
        }

        if(!isset($other_param['chat_id'])){
            $other_param['chat_id'] = $this->user_id;
        }

        if(isset($other_param['keyboard']) && !$this->not_keyboard){
            $other_param['reply_markup'] = $other_param['keyboard'];
            unset($other_param['keyboard']);
        }

        $other_param['text'] = $message;
        if (!isset($other_param['parse_mode'])) {
            $other_param['parse_mode'] = 'html';
        }
        $other_param['disable_web_page_preview'] = true;
        if(isset($other_param['attachment'])){
            switch($this->type_upload){
                case 'photo':
                    $other_param['photo'] =  new \CURLFile($other_param['attachment']);
                    unset($other_param['attachment']);
                    $response = $this->photo_send($other_param);
                break;
                case 'video':
                    $other_param['video'] =  new \CURLFile($other_param['attachment']);
                    unset($other_param['attachment']);
                    $response = $this->video_send($other_param);
                break;

                case 'file':
                    $other_param['document'] =  new \CURLFile($other_param['attachment']);
                    unset($other_param['attachment']);
                    $response = $this->document_send($other_param);
                break;
            }
                $response = $this->callApi("sendMessage", $other_param);
        }
        return $response;
    }

}


 ?>
