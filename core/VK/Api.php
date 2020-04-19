<?php
namespace Lichi\VK;

class Api{
    use Message, Keyboard, Photo, Document;
    
    protected $token_user, $token_group, $messages;

    public function __construct($config) {
        $this->token_group      = $config["VK_TOKEN_GROUP"];
        $this->token_user       = $config["VK_TOKEN_USER"];
        $this->confirm_token    = @$config["VK_TOKEN_CONFIRM"]? $config["VK_TOKEN_CONFIRM"]: false;
        $this->secret_key       = @$config["VK_SECRET_KEY"]? $config["VK_SECRET_KEY"]: false;
        $this->random_id        = rand(1, 99999999);
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
    	curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3');
    	curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Expect:'));
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
    	$data = curl_exec($ch);
    	curl_close($ch);
    	return json_decode($data,true);

    }

    function CallHowGroup($method,$param){
        $param['access_token']= $this->token_group;
        return $this->curl("https://api.vk.com/method/$method?v=5.103", $param);
    }

    function CallHowUser($method,$param){
        $param['access_token']= $this->token_user;
        return $this->curl("https://api.vk.com/method/$method?v=5.103", $param);
    }
}
?>
