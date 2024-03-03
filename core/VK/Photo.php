<?php
namespace Lichi\VK;

trait Photo{

    public function upload_photo($file, $flag_user = true, $flag_person = true, $retry = 0, $errors = []){


        if ($retry > 3) {
            throw new \RuntimeException(implode("\n", $errors));
        }
        
        if($flag_user) $method = "CallHowGroup";
        else       $method = "CallHowUser";

        $redis = \Cache::store('redis');

//        $attach = $redis->get($file);
//        if($attach && $flag_person == false)
//          return $attach;

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


        $handle = $redis->get("handle");
        if (is_null($handle)) {
            $handle = [];
        }
        $handle[$this->user_id]['log'][] = "Получил от вк сервер для загрузки " . $file;
        $redis->set("handle", $handle);

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

        $handle = $redis->get("handle");
        if (is_null($handle)) {
            $handle = [];
        }
        $handle[$this->user_id]['log'][] = "Загрузил и проверяю";
        $redis->set("handle", $handle);

        if (empty($res) || empty($res['photo'])) {
            $handle = $redis->get("handle");
            if (is_null($handle)) {
                $handle = [];
            }
            $handle[$this->user_id]['log'][] = "Не удалось загрузить " . $result;
            $redis->set("handle", $handle);

                $serverIp = curl_getinfo($ch, CURLINFO_PRIMARY_IP);
                $errors = sprintf("params_for_getting_upload_server: %s\nupload_response: %s\nserver_ip: %s\nupload_url: %s\nresponse: %s", json_encode($params_photo, true), json_encode($uploadData, true), $serverIp, $upload_url, $result);
                //$this->upload_photo($file, $flag_user, $flag_person, ($retry+1), $errors);
                throw new \RuntimeException($errors);
        }
        curl_close($ch);


        $handle = $redis->get("handle");
        if (is_null($handle)) {
            $handle = [];
        }
        $handle[$this->user_id]['log'][] = "Все ок, сохраняю в базе вк аттач " . var_export($res, true);
        $redis->set("handle", $handle);

        $attachment = [];
        try {
            $attachment = $this->{$method}("photos.saveMessagesPhoto", array("photo" => $res['photo'], "server" => $res['server'], "hash" => $res['hash']));
        } catch (\Throwable $e) {
            $handle = $redis->get("handle");
            if (is_null($handle)) {
                $handle = [];
            }
            $handle[$this->user_id]['log'][] = "Не удалось загрузить " . $e->getMessage();
            $redis->set("handle", $handle);

            $errors = sprintf("params_for_getting_upload_server: %s\nupload_response: %s\nupload_url: %s\nresponse: %s", json_encode($params_photo, true), json_encode($uploadData, true), $upload_url, $result);
            throw new \RuntimeException($errors);
        }

        $handle = $redis->get("handle");
        if (is_null($handle)) {
            $handle = [];
        }
        $handle[$this->user_id]['log'][] = "Все ок, Аттачей " . count($attachment);
        $redis->set("handle", $handle);
        
        if(isset($attachment[0]))
        {
          $attachment = $attachment[0];
        }else{
            $handle = $redis->get("handle");
            if (is_null($handle)) {
                $handle = [];
            }
            $handle[$this->user_id]['log'][] = "Не удалось загрузить " . var_export($attachment, true);
            $redis->set("handle", $handle);
            
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


        $handle = $redis->get("handle");
        if (is_null($handle)) {
            $handle = [];
        }
        $handle[$this->user_id]['log'][] = "Загрузил " . $attachment_vk . " " . $retry;
        $redis->set("handle", $handle);

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
