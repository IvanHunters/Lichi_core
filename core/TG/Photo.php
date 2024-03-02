<?php
namespace Lichi\TG;

trait Photo{

    public function upload_photo($file, $a = true, $b = false)
    {
      $this->type_upload = "photo";

      return $file;
    }
    public function upload_video($file, $a = true, $b = false)
    {
      $this->type_upload = "video";

      return $file;
    }

    public function photo_send($post_fields)
    {
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

        $this->files_upload = false;
        unset($this->type_upload);

        return $output;
    }

    public function video_send($post_fields)
    {
        $post_fields['caption'] = $post_fields['text'];
        unset($post_fields['text']);

        $bot_url    = "https://api.telegram.org/bot{$this->token}/";
        $url        = $bot_url . "sendVideo?chat_id=" . $this->user_id ;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type:multipart/form-data"
        ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_PROXY, $this->proxy);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        $output = curl_exec($ch);

        $this->files_upload = false;
        unset($this->type_upload);

        return $output;
    }
}
