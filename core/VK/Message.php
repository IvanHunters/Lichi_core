<?php
namespace Lichi\VK;

trait Message
{

  public function message_send($message = "", $other_param = array(), $flag_user = false)
  {

      if(is_array($message) || is_object($message)){
          $message = var_export($message, true);
          if (mb_strlen($message) > 1000) {
              $messages = str_split($message, 900);
              foreach ($messages as $message) {
                  $this->message_send($message, $other_param, $flag_user);
              }
              return null;
          }
      }

      if(!isset($other_param['user_id']) && !isset($other_param['peer_ids'])){
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

    $params['random_id'] = rand(435345345345983, 99999999999999999);
    return $this->CallHowUser("messages.send", $params);
  }

  protected function message_sendHowGroup($params = false){
    if(!$params) return false;

    $params['random_id'] = rand(1, 99999999999999999);
    $params['dont_parse_links'] = 1;

    //$params['reply_to'] = $this->message_id;
    return  $this->CallHowGroup("messages.send", $params);
  }

}


 ?>
