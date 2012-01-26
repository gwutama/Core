<?php

class Flash
{
    private $error = array();
    private $warning = array();
    private $success = array();
    private $info = array();
    
    
    public function __construct()
    {
        if(!isset($_SESSION)) {
            session_start();
        }
        
        $this->error = $_SESSION["flash"][FlashType::ERROR];
        $this->warning = $_SESSION["flash"][FlashType::WARNING];
        $this->success = $_SESSION["flash"][FlashType::SUCCESS];
        $this->info = $_SESSION["flash"][FlashType::INFO];
    }

    
    private function clearFromSession($type)
    {
        unset($_SESSION["flash"][$type]);
    }    
    
    
    private function copyToSession()
    {
        $_SESSION["flash"][FlashType::ERROR] = $this->error;
        $_SESSION["flash"][FlashType::WARNING] = $this->warning;
        $_SESSION["flash"][FlashType::SUCCESS] = $this->success;
        $_SESSION["flash"][FlashType::INFO] = $this->info;        
    }
    
    
    public function append($msg, $type)
    {
        switch($type) {
            case FlashType::ERROR :
                $this->error[] = $msg;
                break;
            case FlashType::WARNING :
                $this->warning[] = $msg;
                break;
            case FlashType::SUCCESS :
                $this->success[] = $msg;
                break;
            case FlashType::INFO :
                $this->info[] = $msg;
                break;
        }
        $this->copyToSession();
    }
    
    
    public function get($type)
    {
        $flash = $_SESSION["flash"][$type];
        $this->clearFromSession($type);
        
        if(!is_array($flash)) {
            return (array) $flash;
        }
        else {        
            return $flash;
        }
    }
}


/*
Struct for flash object
*/
class FlashType
{
    const ERROR = 4;
    const WARNING = 3;
    const SUCCESS = 2;
    const INFO = 1;
}

?>
