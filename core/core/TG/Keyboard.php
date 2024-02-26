<?php
namespace Lichi\TG;

trait Keyboard
{

    public function keyboard_construct($arrKeyboard = Array(), $typeKeyboard = "inline"){
        
        if(in_array("location", $arrKeyboard)) $typeKeyboard = "normal";
        
        if($typeKeyboard == "inline")  $name_keyboard = "inline_keyboard";
        
        elseif($typeKeyboard == "normal"){
            $name_keyboard = "keyboard";
            $keyboard["resize_keyboard"] = true;
            $keyboard["one_time_keyboard"] = true;
        }
        
        foreach($arrKeyboard as $i => $buttons){
            
            if(!is_array($buttons)){
                $text = $data = $buttons;
                if($text == "location"){
                    $keyboard[$name_keyboard][$i][0] = ["text"=>"Отправить местоположение","request_location"=>true];
                }else{
                    $keyboard[$name_keyboard][$i][0] = ["text"=>$text, "callback_data"=>$data];
                }
            }else{
                if(isset($buttons['color'])){
                    $text = isset($buttons['text'])? $buttons['text']: $buttons[0];
                    $keyboard[$name_keyboard][$i][0] = ["text"=>$text, "callback_data"=>$text];
                }else{
                    if(isset($buttons['type'])){
                        if($buttons['type'] == 'link')  
                            $keyboard[$name_keyboard][$i][0] = ["text"=>$buttons['text'], "url"=>$buttons['link']];
                    }else{
                        foreach($buttons as $i2 => $button){
                            
                            if(is_array($button)){
                                $text = isset($button['text'])? $button['text']: $button[0];
                                $data = $text;
                            }else{
                                $text = $data = $button;
                            }
                            if($text == "location"){
                               $keyboard[$name_keyboard][$i][$i2] = ["text"=>"Отправить местоположение","request_location"=>true];
                            }else{
                               $keyboard[$name_keyboard][$i][$i2] = ["text"=>$text, "callback_data"=>$data];
                            }
                        }
                    }
                }
            }
        }
        return json_encode($keyboard);
    }
}
