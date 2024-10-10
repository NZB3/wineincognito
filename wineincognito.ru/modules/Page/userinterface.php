<?php
namespace Page;
if(!defined('IS_XMODULE')){
    exit();
}
require_once ABS_PATH.'/interface/userinterface.php';

class UserInterface extends \AbstractNS\UserInterface{
    public function view($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        switch($relative_path[0]){
            case 'privacy-policy':
                return $this->__privacy_policy();
        }
        return false;//default
    }
    private function __privacy_policy(){
        
        $viewname = '';
        switch($this->XM->lang->getCurrLangId()){
            case 2:
                $viewname = 'privacypolicy.ru';
                break;
            case 1:
                $viewname = 'privacypolicy.en';
                break;
            default:
                return false;
        }
        $this->XM->__wrapview($this->XM->view->load('page/'.$viewname,null,true), 
            null, null);
        return true;
    }
}