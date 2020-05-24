<?php
namespace Lichi\FB;

trait Photo{
    public function photo_send($post_fields){
        $post_fields['caption'] = $post_fields['text'];
        unset($post_fields['text']);

        $bot_url    = "https://api.telegram.org/bot{$this->token}/";
        $url        = $bot_url . "sendPhoto?chat_id=" . $this->user_id ;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type:multipart/form-data"
        ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_PROXY, $this->proxy);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        $output = curl_exec($ch);

        if(file_exists($this->files_upload))  unlink($this->files_upload);

        $this->files_upload = false;
        unset($this->type_upload);

        return $output;
    }
}
