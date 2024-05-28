<?php
namespace Lichi\VK;

trait Photo{

    public function upload_photo($file, $flag_user = true, $flag_person = true, $retry = 0, $errors = []){


        if ($retry > 3) {
            throw new \RuntimeException(implode("\n", $errors));
        }
        
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
            $errors[] = sprintf("params_for_getting_upload_server: %s\nupload_response: %s\nupload_url: %s\nresponse: %s", json_encode($params_photo, true), json_encode($uploadData, true), $upload_url, $upload_url);
            $this->upload_photo($file, $flag_user, $flag_person, ($retry+1), $errors);
//          while(!isset($upload_url)){
//            $upload_url = $this->{$method}("photos.getMessagesUploadServer", $params_photo);
//          }
//          $upload_url = $upload_url['upload_url'];
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
            $errors[] = sprintf("params_for_getting_upload_server: %s\nupload_response: %s\nupload_url: %s\nresponse: %s", json_encode($params_photo, true), json_encode($uploadData, true), $upload_url, $result);
            $this->upload_photo($file, $flag_user, $flag_person, ($retry+1), $errors);
        }
        curl_close($ch);

        $attachment = [];
        try {
            $attachment = $this->{$method}("photos.saveMessagesPhoto", array("photo" => $res['photo'], "server" => $res['server'], "hash" => $res['hash']));
        } catch (\Throwable $e) {
            $errors = sprintf("params_for_getting_upload_server: %s\nupload_response: %s\nupload_url: %s\nresponse: %s", json_encode($params_photo, true), json_encode($uploadData, true), $upload_url, $result);
            throw new \RuntimeException($errors);
        }
        
        if(isset($attachment[0]))
        {
          $attachment = $attachment[0];
        }else{
            $errors = sprintf("params_for_getting_upload_server: %s\nupload_response: %s\nupload_url: %s\nresponse: %s", json_encode($params_photo, true), json_encode($uploadData, true), $upload_url, $result);
            throw new \RuntimeException($errors);
            //$this->upload_photo($file, $flag_user, $flag_person, ($retry+1), $errors);
            //throw new \RuntimeException($errors);
//            while(!isset($attachment)){
//            $attachment = $this->{$method}("photos.saveMessagesPhoto", array("photo"=>$res['photo'], "server"=>$res['server'], "hash"=>$res['hash']));
//            }
//            $attachment = $attachment[0];
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
