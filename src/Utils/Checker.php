<?php
namespace Spark\Project\Utils;

use Spark\Adr\PayloadInterface;

class Checker
{

    public function hasAuthorization($serverParams, $payload, $checkManager=false)
    {
        if (! isset($serverParams[Constants::HTTP_AUTHORIZATION])) {
            
            return false;
        }
        
        if($checkManager)
        {      
            return $this->isManager($serverParams);
        }

        return true;
    }

    public function getErrorStatus($payload)
    {
        return $payload->withStatus(PayloadInterface::INVALID)->withOutput(self::setDefaultMessage(Constants::AUTHORIZATION_REQUIRED));
    }
    
    public function setDefaultMessage($message)
    {
        
        $message = array(Constants::ERROR=>$message);
        
        return array(json_encode($message));
    }
    
    private function isManager($serverParams)
    {
                
        $data = Utilities::getInstance()->extractData($serverParams);
        
        if(!isset($data->role) ||
             $data->role!=Constants::MANAGER
        ){
           
            return false;
        }

        return true;
    }
}
