<?php
namespace Lichi\VK;

trait Document
{

  public function document_upload($file, $params, $flag_user = true){

    if($flag_user) $method = "CallHowGroup";
    else       $method = "CallHowUser";

    $upload_url = $this->{$method}("docs.getMessagesUploadServer", $params)['response']['upload_url'];
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
    $attachment = $this->{$method}("docs.save",array('file'=>$res['file']))['response'];
    $attach = "doc".$attachment[$attachment['type']]['owner_id']."_".$attachment[$attachment['type']]['id'];

    return $attach;
  }
}
