<?php
namespace Lichi\VB;

class Api{
    use Message, Keyboard, Photo, Document;

    protected $token;

    public function __construct($config) {
        $this->token      = $config["VB_TOKEN"];
    }

    public function curl($url,$param){
        usleep(334000);
    	  $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Expect:'));
      	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }

    function provider($link, $param, $flag){
      $param = json_decode($param, true);
      usleep(334000);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $link);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_POST, $flag);
      curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt ($ch, CURLOPT_USERAGENT, 'Lichi-social');
      curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Expect:'));
      curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
      $data = curl_exec($ch);
      curl_close($ch);
      return json_decode($data,true);
    }

    public function callApi($method,$param){
        $url = "https://chatapi.viber.com/pa/{$method}";
        $param['auth_token'] = $this->token;
        $param = json_encode($param);
        $return = $this->curl($url, $param);
        if($return['status'] == '2')
          return false;
        return $return;
    }
}
?>
