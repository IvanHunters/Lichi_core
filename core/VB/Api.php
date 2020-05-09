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
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function callApi($method,$param){
        $url = "https://chatapi.viber.com/pa/{$method}";
        $param['auth_token'] = $this->token;
        $param = json_encode($param);
        $return = $this->curl($url, $param);
    }
}
?>
