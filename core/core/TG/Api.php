<?php
namespace Lichi\TG;

class Api{
    use Message, Keyboard, Photo, Document;

    protected $token, $proxy;

    public function __construct($config) {
        $this->conf = $config;
        $this->token      = $config["TG_TOKEN"];
        $this->proxy       = $config["TG_PROXY"];
    }

    public function curl($url,$param=[]){
        usleep(334000);
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_HEADER, false);
      if(!empty($this->proxy))
  	   curl_setopt ($ch, CURLOPT_PROXY, $this->proxy);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      if(count($param) > 0)
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	$data = curl_exec($ch);
      if($data)
        $data = json_decode($data, true);
      else $data = false;
    	curl_close($ch);
    	return $data;
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
        $url = "https://api.telegram.org/bot{$this->token}/{$method}";
        $return = $this->curl($url,$param);
        return $return;
    }

    public function getImage($image) {
        return  "https://api.telegram.org/file/bot{$this->token}/".$this->callApi("getFile", ["file_id" => $image])['result']['file_path'];
    }

    public function CallHowGroup($method,$param){
        $url = "https://api.telegram.org/bot{$this->token}/{$method}";
        $return = $this->curl($url,$param);
        return $return;
    }

    public function getUrlFile($file_id){
      $file = $this->callApi('getFile', ['file_id' => $file_id]);
      return "https://api.telegram.org/file/bot{$this->token}/{$file['result']['file_path']}";
    }
}
?>
