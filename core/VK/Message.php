<?php
namespace Lichi\VK;

trait Message
{

  public function message_send($message = "", $other_param = array(), $flag_user = false){

      if(is_array($message) || is_object($message)){
          $message = var_export($message, true);
      }

      if(!isset($other_param['user_id'])){
        $other_param['user_id'] = $this->user_id;
      }

      if(!isset($other_param['keyboard'])){
        $other_param['keyboard'] = $this->keyboard_hide();
      }

      $params = $other_param;
      $params['message'] = $message;
      $status = $flag_user ? $this->message_sendHowUser($params) : $this->message_sendHowGroup($params);
      return $status;
  }

  protected function message_sendHowUser($params = false){
    if(!$params) return false;

    $params['random_id'] = rand(1, 99999999);
    return $this->CallHowUser("messages.send", $params);
  }

  protected function message_sendHowGroup($params = false){
    if(!$params) return false;

    $params['random_id'] = rand(1, 99999999);
    return $this->CallHowGroup("messages.send", $params);
  }

}


 ?>
