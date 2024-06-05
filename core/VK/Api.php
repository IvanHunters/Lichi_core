<?php
namespace Lichi\VK;

class Api{
    use Message, Keyboard, Photo, Document;

    protected $messages;

    public function __construct($config) {
        $this->token_group      = $config["VK_TOKEN_GROUP"];
        $this->token_user       = $config["VK_TOKEN_USER"];
        $this->confirm_token    = @$config["VK_TOKEN_CONFIRM"]? $config["VK_TOKEN_CONFIRM"]: false;
        $this->secret_key       = @$config["VK_SECRET_KEY"]? $config["VK_SECRET_KEY"]: false;
    }

    public function set_token($token){
      $this->token_group      = $token;
    }

    function curl($link, $param, $flag=false){
      usleep(334000);
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $link);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
    	if($flag)
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch,CURLOPT_TIMEOUT,40);
    	curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3');
    	curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Expect:'));
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
    	$data = curl_exec($ch);
    	curl_close($ch);
    	$json = json_decode($data,true);
        if (is_null($json)) {
            return $data;
        }
        return $json;
    }

    function provider($link, $param, $flag){
      $param = json_decode($param, true);
      usleep(36000);
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



    function CallHowGroup($method,$param, $retry=0){

        $param['access_token']= $this->token_group;
        $data = $this->curl("https://api.vk.com/method/$method?v=5.199", $param);
        $i = 0;
        if(isset($data['error'])){
            if ($retry === 3) {
                throw new \Exception(json_encode($data['error'], JSON_UNESCAPED_UNICODE)."\nТокен:".$this->token_group);
            } else {
                sleep(3);
                return $this->CallHowGroup($method,$param, ($retry++));
            }
        }
        return is_array($data['response']) ? $data['response'] : [$data['response']];
    }

    function ExecHowGroup($method, $param){

      $access_token = $this->token_group;
      $execute['code'] = "API.{$method}(".json_encode($param, JSON_UNESCAPED_UNICODE).");";
      $data = $this->curl("https://api.vk.com/method/execute?v=5.199&access_token={$access_token}", $execute);
      $i = 0;
      while(isset($data['error'])){
        throw new \Exception(json_encode($data['error'], JSON_UNESCAPED_UNICODE)."\nТокен:".$this->token_group);
        usleep(500000);
        if($i == 10)
          return false;
        $data = $this->curl("https://api.vk.com/method/execute?v=5.199&access_token={$access_token}", $execute);
        $i++;
      }
      return $data;
    }

    function CallHowUser($method,$param){
        $param['access_token']= $this->token_user;
        return $this->curl("https://api.vk.com/method/$method?v=5.199", $param);
    }
}
?>
