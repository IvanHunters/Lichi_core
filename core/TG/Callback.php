<?php
namespace Lichi\TG;

class Callback extends Api
{
    public $not_keyboard = false;
    /**
     * @var mixed
     */
    public $realData;

    function response($return = true){
//
        header("Content-Encoding: none");
        header("Connection: close");
        if($return) echo ('ok');
        else exit($this->confirm_token);
        fastcgi_finish_request();
    }

    public function resend($req = false)
    {
            $this->response();
    }

    public function handler($callback, $req = false){

        if (!$req) {
            $req = file_get_contents('php://input');
        }
        $this->data = $data = json_decode($req, true);

        if(isset($data["callback_query"])) $this->method_require = "keyboard_message";
        elseif(isset($data['message'])) $this->method_require = "message";
        elseif (isset($data['pre_checkout_query'])){
            $paymentData = $data['pre_checkout_query'];
            $this->CallHowGroup("answerPreCheckoutQuery", [
                'ok' => true,
                'pre_checkout_query_id' => $paymentData['id']
            ]);
            die("ok");
        }else{
            print_r($this);
            die("\nFor Debug");
        }

        if($this->method_require == "message"){
            $this->message                  = @$this->data["message"];
            $this->message_id               = @$this->message['message']['message_id'];
            $this->text                     = @$this->message["text"];
            $this->text_lower               = preg_replace("/\//","",mb_strtolower($this->text));

        }elseif ($this->method_require == "keyboard_message") {
            $this->message                  = @$this->data["callback_query"];
            $this->message_id               = @$this->message['message']['message_id'];
            $this->message["chat"]["id"]    = @$this->message["from"]["id"];
            $this->text                     = @$this->message["data"];
            $this->text_lower               = preg_replace("/\//","",mb_strtolower($this->text));
        }

        $this->is_ref                       = false;
        $check_ref = explode(" ", $this->text_lower);
        if($check_ref[0] == '/start'){
          if(isset($check_ref[1])){
            $this->is_ref = true;
            $this->ref = $check_ref[1] != ''? $check_ref[1] : 'none';
          }
        }
        $this->username                     = $this->message["from"]["first_name"];
        $this->user_id                      = @$this->message["from"]["id"];
        $this->username_short               = $this->message["from"]["username"] ?? 'Не удалось узнать';
        $this->chat_id                      = $this->message["chat"]["id"];
        if ($this->user_id == $this->chat_id) {
            $this->type_event                   = "message_new";
        }
        $this->attachments_data_last = false;
        $this->attachments_data = false;
        $this->have_attachments = false;
        if (isset($this->data["message"]['video']) || isset($this->data["message"]["reply_to_message"]['video'])) {
            $this->have_attachments = true;
            if (isset($this->data["message"]['video'])) {
                $videos = $this->data["message"]['video'];
            } else {
                $videos = $this->data["message"]["reply_to_message"]['video'];
            }
            $this->attachments_data['video'][] = $this->attachments_data_last['video'][0] = $videos;
        }
        if (isset($this->data["message"]['photo']) || isset($this->data["message"]["reply_to_message"]['photo'])) {
            $this->have_attachments = true;
            if (isset($this->data["message"]['photo'])) {
                $photos = $this->data["message"]['photo'];
            } else {
                $photos = $this->data["message"]["reply_to_message"]['photo'];
            }
            foreach ($photos as $photo) {
                $this->attachments_data['photo'][] = $this->attachments_data_last['photo'][0] = $photo;
            }
        }

        $this->realData       = json_decode($req, true);

        $callback($this);
    }

    public function set_webhook($url){
        $data = $this->curl("https://api.telegram.org/bot{$this->token}/setWebhook", array("url"=>$url));

        return $data;
    }

    public function getBetterImageUrl(int $index = 0, $maxWidth = null, $maxHeight = null): string
    {
        $attachments = $this->attachments_data ?? [];
        $url_image = '';
        if (count($attachments) === 0) {
            return '';
        }

        if (!isset($attachments['photo'])) {
            throw new \RuntimeException("attach must have a photo type!");
        }

        $attachment = $attachments['photo'];

        usort($attachment, function ($item1, $item2) {
            return $item2['height'] <=> $item1['height'];
        });
        foreach($attachment as $photo) {
            if (!is_null($maxWidth) && $photo['width'] <= $maxWidth) {
                $url_image = $this->getImage($photo['file_id']);
                break;
            } elseif (!is_null($maxHeight) && $photo['height'] <= $maxHeight) {
                $url_image = $this->getImage($photo['file_id']);
                break;
            } elseif(!is_null($maxWidth) || !is_null($maxHeight)) {
                continue;
            } else {
                $url_image = $this->getImage($photo['file_id']);
                break;
            }
        }

        return $url_image;
    }
}
