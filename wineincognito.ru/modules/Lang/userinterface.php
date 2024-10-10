<?php
namespace Lang;
if(!defined('IS_XMODULE')){
    exit();
}
require_once ABS_PATH.'/interface/userinterface.php';

class UserInterface extends \AbstractNS\UserInterface{

    public function index($relative_path = array()){
        return false;
    }

    public function modules($relative_path = array()){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
            return false;
        }
        $modulelist = $this->XM->lang->getInterfaceTranslationModuleList();
        $this->XM->__wrapview($this->XM->view->load('lang/modulelist',array('modulelist'=>$modulelist),true), 
            null, array('css'=>array('/modules/Lang/css/modulelist.css')));
        return true;
    }

    public function groups($relative_path = array()){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
            return false;
        }
        if(count($relative_path)<1){
            return false;
        }
        $module_id = (int)$relative_path[0];
        $grouplist = $this->XM->lang->getInterfaceTranslationGroupList($module_id);
        $this->XM->__wrapview($this->XM->view->load('lang/grouplist',array('grouplist'=>$grouplist,'module_id'=>$module_id),true), 
            null, array('css'=>array('/modules/Lang/css/grouplist.css')));
        return true;
    }
    public function editinterfacetranslations($relative_path = array()){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
            return false;
        }
        if(count($relative_path)<2){
            return false;
        }
        $group_id = (int)$relative_path[1];
        $module_id = (int)$relative_path[0];
        $stringlist = $this->XM->lang->getInterfaceTranslationStringsForAllLanguages($group_id);
        $languageList = $this->XM->lang->getLanguageList();
        $this->XM->__wrapview($this->XM->view->load('lang/editinterfacetranslations',array('stringlist'=>$stringlist,'languageList'=>$languageList,'module_id'=>$module_id),true), 
            null, array('css'=>array('/modules/Lang/css/editinterfacetranslations.css'),'js'=>array('/modules/Lang/js/editinterfacetranslations.js')));
        return true;
    }

    public function testTranslations(){
        $this->XM->__wrapview($this->XM->view->load('lang/testTranslations',null,true), null, null);
        return true;
    }

    //ajax
    public function ajax_interfacetranslation_edit($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $id = (int)$relative_path[0];
        $lang = (int)$_POST['lang'];
        $translation = (string)$_POST['translation'];
        $err = null;
        if($this->XM->lang->edit_interface_translation($id, $lang, $translation, $err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    
}
