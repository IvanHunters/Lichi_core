<?php
namespace Lichi\VK;

trait Photo{

    public function upload_photo($file, $flag_user = true, $flag_person = true, $retry = 0, $errors = []){

        if($flag_user) $method = "CallHowGroup";
        else       $method = "CallHowUser";

        if($flag_person)
        {
          $params_photo = ['peer_id'=>$this->user_id];
        }
        else
        {
          $params_photo = [];
        }

        $upload_url  = $uploadData = $this->{$method}("photos.getMessagesUploadServer", $params_photo);
        
        if(isset($upload_url['upload_url']))
        {
          $upload_url = $upload_url['upload_url'];
        }else{
            throw new \RuntimeException("Не смог получить ссылку на загрузку");
        }

        $aPost = array(
            'photo' => new \CURLFile($file)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $upload_url);
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $aPost);
        curl_setopt($ch,CURLOPT_TIMEOUT,40);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $res = json_decode($result, true);

        if (empty($res) || empty($res['photo'])) {
            throw new \RuntimeException("Не смог загрузить изображение");
        }
        curl_close($ch);

        $attachment = [];
        try {
            $attachment = $this->{$method}("photos.saveMessagesPhoto", array("photo" => $res['photo'], "server" => $res['server'], "hash" => $res['hash']));
        } catch (\Throwable $e) {
            throw new \RuntimeException("Не смог получить аттач изображения");
        }
        
        if(isset($attachment[0]))
        {
          $attachment = $attachment[0];
        }else{
            throw new \RuntimeException("Не смог получить аттач изображения по индексу");
        }

        $attachment_vk = "photo".$attachment['owner_id']."_".$attachment['id'];

        return $attachment_vk;
    }

    public function upload_on_wall($file, $params){

        $upload_url = $this->{$method}("photos.getWallUploadServer", array("group_id"=>$params['group_id']))['response']['upload_url'];
        $aPost = array(
            'file' => new \CURLFile($file)
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $upload_url);
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $aPost);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = json_decode(curl_exec($ch), true);
        curl_close($ch);
        $attachment = $this->{$method}("photos.saveWallPhoto", array("group_id"=>$params['group_id'], "photo"=>$res['photo'], "server"=>$res['server'], "hash"=>$res['hash']))['response'][0];
        return "photo".$attachment['owner_id']."_".$attachment['id'];
    }
}
