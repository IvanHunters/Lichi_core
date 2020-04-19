<?php
namespace Lichi\VK;

trait Photo{

    public function upload_in_message($file, $flag_user = true){

        if($flag_user) $method = "CallHowGroup";
        else       $method = "CallHowUser";

        $upload_url = $this->{$method}("photos.getMessagesUploadServer", [])['response']['upload_url'];

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
        $attachment = $this->{$method}("photos.saveMessagesPhoto", array("photo"=>$res['photo'], "server"=>$res['server'], "hash"=>$res['hash']))['response'][0];
        return "photo".$attachment['owner_id']."_".$attachment['id'];
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
