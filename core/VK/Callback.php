<?php
namespace Lichi\VK;

class Callback extends Api
{
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
        if (!$req) {
            $req = file_get_contents('php://input');
        }
        $this->data = $data = json_decode($req);
        if($data->type == 'confirmation')
            $this->response(false);
        else
            $this->response();
    }

    public function handler($callback, $req = false){
        if (!$req) {
            $req = file_get_contents('php://input');
        }
        $this->data = $data = json_decode($req);

        $this->secret_key_request  = @$this->data->secret;
        if($this->secret_key && $this->secret_key_request != $this->secret_key) return false;
        $this->type_event       = $this->data->type;
        $this->client_info      = @$this->data->object->client_info;
        $this->chat_id          = is_null(@$data->object->message->from_id) ? @$data->object->user_id : @$data->object->message->from_id;
        $this->user_id          = is_null(@$data->object->message->peer_id) ? @$data->object->user_id : @$data->object->message->peer_id;
        $this->username         = @$this->CallHowGroup("users.get", array("user_ids"=>$this->user_id))[0]['first_name'];
        $this->group_id         = @$data->group_id;
        $this->message_id       = @$data->object->message->id;
        $this->realData       = json_decode($req, true);
        $this->object       = @$data->object;

        if (isset($this->realData['object']['message']['is_cropped']) && $this->realData['object']['message']['is_cropped']) {
            $this->realData['object']['message'] = $this->CallHowGroup("messages.getById", array('message_ids' => $this->realData['object']['message']['id']))['items'][0];
        }
        
        if (isset($this->realData['object']['message']['reply_message']['attachments'])) {
            $this->realData['object']['message']['attachments'] = $this->realData['object']['message']['reply_message']['attachments'] ?? [];
        }elseif (isset($this->realData['object']['message']['fwd_messages'][0]['attachments'])) {
            $this->realData['object']['message']['attachments'] = $this->realData['object']['message']['fwd_messages'][0]['attachments'] ?? [];
        }
        $this->text             = @$data->object->message->text;
        $this->is_ref           = false;
        $this->have_attachments = false;
        $this->attachments_data_last = false;
        $this->attachments_data = false;

        if(isset($this->realData['object']['message']['attachments']))
        {
          //$this->message_send($this->text."dsdsf");
          $attach = $this->realData['object']['message']['attachments'];
          $this->have_attachments = true;
          if(count($attach) > 0){
            $this->have_attachments = true;
            $this->attachments = $attach;

            $maxHeight = 0;
            foreach($this->attachments as $index => $attachment)
            {
              $attachment_type = $attachment['type'];
              if ($attachment_type === "photo") {
                  foreach ($attachment[$attachment_type]['sizes'] ?? [] as $size) {
                      $height = $size['height'];
                      if ($maxHeight <= $height) {
                          $maxHeight = $height;
                      }
                      $this->attachments_data_last[$attachment_type] = [$size['url']];
                      $this->attachments_data[$index][$attachment_type][] = $size;
                  }
              } elseif ($attachment_type === "video") {
                  $this->attachments_data_last[$attachment_type] = $attachment[$attachment_type];
                  $this->attachments_data[$index][$attachment_type][] = $attachment[$attachment_type];
              }
            }
          }
        }
        if(isset($this->object->message->ref))
        {
          $this->is_ref = true;
          $this->ref = $this->object->message->ref != ''? $this->object->message->ref : 'none';
        }
        $this->text_lower   = @mb_strtolower($this->text);
        $this->publish_date = @$data->object->message->date;

        $callback($this);
    }

    public function get_connect_data(){
        $group_id = $this->CallHowGroup('groups.getById', ['']);
        if(isset($group_id)){
            $group_id = $group_id['groups'][0]['id'];
            $code_connect = $this->CallHowGroup('groups.getCallbackConfirmationCode', ["group_id"=>$group_id])['code'];
            return ['group_id'=>$group_id, 'code_connect'=>$code_connect];
        }
        else
        {
            return false;
        }
    }

    public function set_webhook($url, $secret_key, $group_id){
        $server_id = $this->CallHowGroup('groups.addCallbackServer', ["url"=>$url, "title"=>"Lichi", 'group_id'=>$group_id, 'secret_key'=>$secret_key]);
        if(!isset($server_id))
          return false;
        $server_id = $server_id['server_id'];
        $this->CallHowGroup('groups.setCallbackSettings',
                                        [ "group_id"=>$group_id, "server_id"=>$server_id, "api_version"=>"5.103",
                                          "message_new"=>1, "message_reply"=>1, "message_allow"=>1,
                                          "message_typing_state"=>1]);

        return $server_id;
    }

    public function delete_webhook($server_id){
      $group_id = $this->CallHowGroup('groups.getById', ['']);
      if(isset($group_id)){
          $group_id = $group_id['groups'][0]['id'];
      }
      return $this->CallHowGroup('groups.deleteCallbackServer',
                                      [ "group_id"=>$group_id,
                                        "server_id"=>$server_id ]);
    }
}
