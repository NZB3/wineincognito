<?php
namespace Sendmail;
if(!defined('IS_XMODULE')){
    exit();
}
require_once ABS_PATH.'/interface/userinterface.php';

class UserInterface extends \AbstractNS\UserInterface{

    public function index($relative_path){
        return false;
    }

    public function preview($relative_path){
        $content = 'test';
        echo $this->XM->sendmail->get_wrapped_letter($content,true,null);
        return true;
    }
    
}
