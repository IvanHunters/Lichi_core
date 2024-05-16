<?php
namespace Lichi\VK;

trait Keyboard
{

  protected $active_color = ["positive", "negative", "default", "primary"];

  public function keyboard_hide(){

    return '{"buttons":[],"one_time":true}';
  }

  public function keyboard_construct($arrKeyboard = Array(), $typeKeyboard = "normal"){

      if($typeKeyboard == "inline"){
          $keyboard['inline'] = true;
          if(count($arrKeyboard) > 5){
            $arrKeyboard = array_slice($arrKeyboard, 0, 5);
          }
      }

      $keyboard['buttons']=array();

      foreach ($arrKeyboard as $i=>$line) {
        if(is_array($line)){
            if(isset($line['color'])){
                $keyboard = $keyboard['buttons'][$i][0] = $this->keyboard_assoc($line['text'],$line['color']);
            }elseif(isset($line['type'])){
                $keyboard['buttons'][$i][0] = $this->keyboard_assoc($line);
            }else{
                foreach($line as $buttons){

                    if(is_array($buttons)){
                        if(isset($buttons['color'])){
                            $keyboard['buttons'][$i][] = $this->keyboard_assoc($buttons['text'],$buttons['color']);
                        }elseif(!isset($buttons['color'])){
                            $keyboard['buttons'][$i][] = $this->keyboard_assoc(@$buttons[0],@$buttons[1]);
                        }
                    }
                    else $keyboard['buttons'][$i][] = $this->keyboard_assoc($buttons);
                }
            }
        }else{
            $keyboard['buttons'][$i][0] = $this->keyboard_assoc($line);
        }
      }
      return json_encode($keyboard, JSON_UNESCAPED_UNICODE);
  }




  private function keyboard_assoc($value, $color = "primary", $label = false){
      if(!$label){
          if($value == "location") return ['action'=>['type'=>'location']];
          elseif(isset($value['type']) && $value['type'] == "callback")	return  ['action'=>['type'=>'callback',"label"=>$value['text']]];
          elseif($value == "vk_pay")	return  ['action'=>['type'=>'vkpay','hash'=>"action=transfer-to-group&group_id={$this->group_id}&aid=10"]];
          elseif(isset($value['type']) && $value['type'] == "link")	return  ['action'=>['type'=>'open_link','link'=>$value['link'],"label"=>$value['text']]];
          else return ['action'=>['type'=>'text','label'=>$value],'color'=>$color];
      }
  }

}
