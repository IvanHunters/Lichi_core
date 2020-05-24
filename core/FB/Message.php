<?php
namespace Lichi\FB;

trait Message
{

    public function message_send($message = "", $other_param = array()){

        if(is_array($message) || is_object($message)){
          $message = var_export($message, true);
        }

        if(!isset($other_param['recipient'])){
            $other_param['recipient'] = ["id"=>$this->user_id];
        }
        
        if(isset($other_param['keyboard'])){
            $other_param['message'] =   ["attachment"=>
                                            ["type"=>"template"
                                                ,"payload"=>
                                                    ["template_type"=>"button",
                                                    "text"=>$message,
                                                    "buttons"=>$other_param['keyboard']
                                                    ]
                                            ]
                                        ];
            unset($other_param['keyboard']);
        }else 
            $other_param['message'] = $message;
        
        $body_request = json_encode($other_param);
                                     
        $response = $this->callApi("messages",$body_request);
        
        return $response;
        
        
    }

}


 ?>
