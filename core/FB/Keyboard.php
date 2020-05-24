<?php
namespace Lichi\FB;

trait Keyboard
{

    public function keyboard_construct($array_keyboard = Array()){
        
        foreach($array_keyboard as $but){
            if(!is_array($but)) $button['title']            = $but;
            if(!isset($but['type'])) $button['type']        = "postback";
            if(!isset($but['payload'])) $button['payload']  = $button['title'];
            $buttons[]                                      = $button;
        }
        
        return  $buttons;
    }
}
