<?php
namespace Lichi\TG;

class Api{
    use Message, Keyboard, Photo, Document;
    
    protected $token, $proxy;

    public function __construct($config) {
        $this->token      = $config["TG_TOKEN"];
        $this->proxy       = $config["TG_PROXY"];
    }

    public function curl($url,$param){
        usleep(334000);
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_HEADER, false);
    	curl_setopt ($ch, CURLOPT_PROXY, $this->proxy); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	$data = curl_exec($ch);
    	die($data);
    	curl_close($ch);
    	return $data;
    }

    public function callApi($method,$param){
        $url = "https://api.telegram.org/bot{$this->token}/{$method}";
        $return = $this->curl($url,$param);
    }
}
?>
