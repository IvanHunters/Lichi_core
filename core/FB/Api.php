<?php
namespace Lichi\FB;

class Api{
    use Message, Keyboard, Photo, Document;

    protected $token;

    public function __construct($config) {
        $this->token      = $config["FB_TOKEN"];
        $this->confirm_token      = $config["FB_CONFIRM_TOKEN"];
    }

    public function curl($url,$param){
        usleep(334000);
        $headers = ['Content-Type: application/json',];
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $param); // Данные для отправки
    	$data = curl_exec($ch); // Выполняем запрос
    	curl_close($ch); // Закрываем соединение
    	return $data; // Парсим JSON и отдаем
    }

    public function callApi($method,$param){
        $url = "https://graph.facebook.com/v2.6/me/{$method}?access_token={$this->token}";
        return $this->curl($url,$param);
    }
}
?>
