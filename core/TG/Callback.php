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
        else{
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
        } else {
            // $this->type_event                   = "message_new";
            // $this->not_keyboard                 = true;
            // $this->chat_id                      = $this->message["chat"]["id"];
            // $this->user_id                      = $this->message["chat"]["id"];
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
            $videos['url'] = $this->getImage($videos['file_id']);
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
                $photo['url'] = $this->getImage($photo['file_id']);
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
}
