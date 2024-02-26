<?php
namespace Lichi\VK;

trait Document
{

  public function upload_document($file, $flag_user = true){

    if($flag_user) $method = "CallHowGroup";
    else       $method = "CallHowUser";

    $redis = \Cache::store('redis');

    $attach = $redis->get($file);
    if($attach && $flag_person == false)
      return $attach;

    $upload_url = $this->{$method}("docs.getMessagesUploadServer", array('type'=>'doc', 'peer_id'=>$this->user_id));

    $upload_url = $upload_url['response']['upload_url'];
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

    $attachment = $this->{$method}("docs.save",array('file'=>$res['file']));
    $attachment = $attachment['response'];

    $attachment_vk = "doc".$attachment[$attachment['type']]['owner_id']."_".$attachment[$attachment['type']]['id'];

    if($flag_person == false)
      $redis->set($file, $attachment_vk);

    return $attachment_vk;
  }
}
