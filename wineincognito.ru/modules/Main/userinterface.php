<?php
namespace Main;
if(!defined('IS_XMODULE')){
    exit();
}
require_once ABS_PATH.'/interface/userinterface.php';

class UserInterface extends \AbstractNS\UserInterface{

    public function index($relative_path = array()){
        if($this->XM->user->isLoggedIn()){
            $count = $err = null;
            $pagelimit = 10;
            $page = 1;
            $currently_participating_tastings = $this->XM->tasting->filter_tasting(null, null, null, false, false, false, true, false, false, null, false, false, null, true, true, null, false, $page, $pagelimit, $count, $err);
            if(is_array($currently_participating_tastings) && !empty($currently_participating_tastings)){
                $this->XM->__wrapview($this->XM->view->load('tasting/tastingfilter_manual',array('tastinglist'=>$currently_participating_tastings,'can_add'=>false,'pendingreview'=>true),true), 
                    null, array('css'=>array('/modules/Tasting/css/tastingfilter.css')));
                return true;
            }
        }
        return $this->XM->__UI->product->vintagefilter(array('onlyscored'),true);
    }
    
    public function agerestrict(){
        $this->XM->__wrapview($this->XM->view->load('main/agerestrict',null,true), 
            null, array('css'=>array('/modules/Main/css/agerestrict.css'),'js'=>array('/modules/Main/js/agerestrict.js')),false,false);
        return true;
    }

    public function e404(){
        $this->XM->__wrapview($this->XM->view->load('main/servererror',array('err_code'=>404,'err_text'=>langTranslate('main','servererror','404: Page not found','Page not found')),true), 
            null, array('css'=>array('/modules/Main/css/servererror.css')));
        return true;
    }
    
    public function php_info(){
        phpinfo();
        return true;
    }

    public function ajax_keepalive($relative_path){
        echo '{"success":1}';
        return true;
    }

}
