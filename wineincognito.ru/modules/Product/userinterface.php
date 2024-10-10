<?php
namespace Product;
if(!defined('IS_XMODULE')){
    exit();
}
require_once ABS_PATH.'/interface/userinterface.php';

class UserInterface extends \AbstractNS\UserInterface{

    public function index($relative_path){
        return false;
    }

    public function attrgrouplist(){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            return false;
        }
        $attrgrouplist = $this->XM->product->get_attr_group_list();
        if($attrgrouplist===false){
            return false;
        }
        $this->XM->__wrapview($this->XM->view->load('product/attrgrouplist',array('attrgrouplist'=>$attrgrouplist),true), 
            null, array('css'=>array('/modules/Product/css/attrgrouplist.css'),'js'=>array('/modules/Product/js/attrgrouplist.js')));
        return true;
    }

    public function attrgroupadd(){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            return false;
        }
        $languageList = $this->XM->lang->getLanguageList();
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_attrgroup')){
            $visible = (isset($_POST['visible'])&&is_array($_POST['visible']))?$_POST['visible']:array();
            $required = (isset($_POST['required'])&&is_array($_POST['required']))?$_POST['required']:array();
            $doublecheck = (isset($_POST['doublecheck'])&&is_array($_POST['doublecheck']))?$_POST['doublecheck']:array();

            $used_in_filter = $overload = isset($_POST['used_in_filter'])&&$_POST['used_in_filter']?1:0;
            $overload = isset($_POST['overload'])&&$_POST['overload']?1:0;
            $multiple = isset($_POST['multiple'])&&$_POST['multiple']?1:0;
            $analog = isset($_POST['analog'])&&$_POST['analog']?1:0;
            $zindex = isset($_POST['zindex'])?(int)$_POST['zindex']:10000;
            $name = array();
            foreach($languageList as $language){
                $language_id = $language['id'];
                $name[$language_id] = (isset($_POST['name'])&&is_array($_POST['name'])&&isset($_POST['name'][$language_id]))?$_POST['name'][$language_id]:'';
            }
            $err = null;
            if(!$attrgroupid = $this->XM->product->add_attrgroup($name, $visible, $required, $doublecheck, $used_in_filter, $overload, $multiple, $analog, $zindex, $err)){
                $this->XM->addMessage($err, 0);
            } else {
                $this->XM->setPushStateUrl(BASE_URL.'/moderate/product/attributes/'.$attrgroupid);
                return $this->attrlist(array($attrgroupid));
                // redirect('/moderate/product/attributes/'.$attrgroupid);
            }
        }
        $this->XM->__wrapview($this->XM->view->load('product/editattrgroup',array('languageList'=>$languageList,'attrgroup'=>array(),'foundation_id'=>\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID),true), 
            null, array('css'=>array('/modules/Product/css/editattrgroup.css'),'js'=>array('/modules/Product/js/editattrgroup.js'),'pack'=>array('dropbox')));
        return true;
    }
    public function attrgroupedit($relative_path = array()){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            return false;
        }
        if(count($relative_path)<1){
            return false;
        }
        $attrgroup_id = (int)$relative_path[0];
        $languageList = $this->XM->lang->getLanguageList();
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_attrgroup')){
            $visible = (isset($_POST['visible'])&&is_array($_POST['visible']))?$_POST['visible']:array();
            $required = (isset($_POST['required'])&&is_array($_POST['required']))?$_POST['required']:array();
            $doublecheck = (isset($_POST['doublecheck'])&&is_array($_POST['doublecheck']))?$_POST['doublecheck']:array();

            $used_in_filter = $overload = isset($_POST['used_in_filter'])&&$_POST['used_in_filter']?1:0;
            $overload = isset($_POST['overload'])&&$_POST['overload']?1:0;
            $multiple = isset($_POST['multiple'])&&$_POST['multiple']?1:0;
            $analog = isset($_POST['analog'])&&$_POST['analog']?1:0;
            $zindex = isset($_POST['zindex'])?(int)$_POST['zindex']:10000;
            $name = array();
            foreach($languageList as $language){
                $language_id = $language['id'];
                $name[$language_id] = (isset($_POST['name'])&&is_array($_POST['name'])&&isset($_POST['name'][$language_id]))?$_POST['name'][$language_id]:'';
            }
            $err = null;
            if(!$attrgroupid = $this->XM->product->edit_attrgroup($attrgroup_id, $name, $visible, $required, $doublecheck, $used_in_filter, $overload, $multiple, $analog, $zindex, $err)){
                $this->XM->addMessage($err, 0);
            } else {
                unset($_POST);
                $this->XM->addMessage(langTranslate('product', 'attrgroup', 'Attribute group have been edited', 'Attribute group have been edited'), 2);
                $this->XM->setPushStateUrl(BASE_URL.'/moderate/product/attributes');
                return $this->attrgrouplist();
            }
        }
        $attrgroup = $this->XM->product->get_attrgroup_info_for_all_languages($attrgroup_id);
        if(!$attrgroup){
            return false;
        }
        $this->XM->__wrapview($this->XM->view->load('product/editattrgroup',array('languageList'=>$languageList,'attrgroup'=>$attrgroup,'is_foundation'=>($attrgroup_id==\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID),'foundation_id'=>\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID),true), 
            null, array('css'=>array('/modules/Product/css/editattrgroup.css'),'js'=>array('/modules/Product/js/editattrgroup.js'),'pack'=>array('dropbox')));
        return true;
    }
    //attr
    public function attrlist($relative_path = array()){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            return false;
        }
        if(count($relative_path)<1){
            return false;
        }
        $attrgroup_id = (int)$relative_path[0];

        $attrgroupinfo = $this->XM->product->get_attrgroup_info($attrgroup_id);
        if(!$attrgroupinfo){
            return false;
        }
        $attrlist = $this->XM->product->get_attr_list($attrgroup_id);
        if($attrlist===false){
            return false;
        }
        $this->XM->__wrapview($this->XM->view->load('product/attrlist',array('attrgroupinfo'=>$attrgroupinfo,'attrlist'=>$attrlist),true), 
            null, array('css'=>array('/modules/Product/css/attrlist.css'),'js'=>array('/modules/Product/js/attrlist.js')));
        return true;
    }
    public function attradd($relative_path = array()){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            return false;
        }
        if(count($relative_path)<1){
            return false;
        }
        $attrgroup_id = (int)$relative_path[0];

        $languageList = $this->XM->lang->getLanguageList();
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_attr')){
            $name = array();
            $parent = isset($_POST['parent'])?(int)$_POST['parent']:0;
            $show_only_origin = isset($_POST['show_only_origin'])?(int)$_POST['show_only_origin']:0;
            $has_important = isset($_POST['has_important'])?(int)$_POST['has_important']:0;
            foreach($languageList as $language){
                $language_id = $language['id'];
                $name[$language_id] = (isset($_POST['name'])&&is_array($_POST['name'])&&isset($_POST['name'][$language_id]))?$_POST['name'][$language_id]:'';
            }
            $err = null;
            if(!$attr_id = $this->XM->product->add_attr($attrgroup_id, $parent, $name, $show_only_origin, $has_important, $err)){
                $this->XM->addMessage($err, 0);
            } else {
                $this->XM->setPushStateUrl(BASE_URL.'/moderate/product/attributes/'.$attrgroup_id.'/'.$attr_id);
                return $this->attrvallist(array($attr_id));
                // redirect('/moderate/product/attributes/'.$attrgroup_id.'/'.$attr_id);
            }
        }
        $possibleParentList = $this->XM->product->get_possible_attr_parent_list($attrgroup_id);
        if(!$possibleParentList){
            return false;
        }
        $this->XM->__wrapview($this->XM->view->load('product/editattr',array('attrgroup_id'=>$attrgroup_id,'languageList'=>$languageList,'attr'=>array(),'possibleParentList'=>$possibleParentList),true), 
            null, array('css'=>array('/modules/Product/css/editattr.css')));
        return true;
    }
    public function attredit($relative_path = array()){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            return false;
        }
        if(count($relative_path)<2){
            return false;
        }
        $attrgroup_id = (int)$relative_path[0];
        $attr_id = (int)$relative_path[1];

        $languageList = $this->XM->lang->getLanguageList();
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_attr')){
            $name = array();
            $parent = isset($_POST['parent'])?(int)$_POST['parent']:0;
            $show_only_origin = isset($_POST['show_only_origin'])?(int)$_POST['show_only_origin']:0;
            $has_important = isset($_POST['has_important'])?(int)$_POST['has_important']:0;
            foreach($languageList as $language){
                $language_id = $language['id'];
                $name[$language_id] = (isset($_POST['name'])&&is_array($_POST['name'])&&isset($_POST['name'][$language_id]))?$_POST['name'][$language_id]:'';
            }
            $err = null;
            if(!$this->XM->product->edit_attr($attr_id, $parent, $name, $show_only_origin, $has_important, $err)){
                $this->XM->addMessage($err, 0);
            } else {
                unset($_POST);
                $this->XM->addMessage(langTranslate('product', 'attr', 'Attribute have been edited', 'Attribute have been edited'), 2);
                $this->XM->setPushStateUrl(BASE_URL.'/moderate/product/attributes/'.$attrgroup_id);
                return $this->attrlist(array($attrgroup_id));
            }
        }
        $possibleParentList = $this->XM->product->get_possible_attr_parent_list($attrgroup_id,$attr_id);
        if(!$possibleParentList){
            return false;
        }
        $attr = $this->XM->product->get_attr_info_for_all_languages($attr_id,$attrgroup_id);
        if(!$attr){
            return false;
        }
        $this->XM->__wrapview($this->XM->view->load('product/editattr',array('attrgroup_id'=>$attrgroup_id,'languageList'=>$languageList,'attr'=>$attr,'possibleParentList'=>$possibleParentList),true), 
            null, array('css'=>array('/modules/Product/css/editattr.css')));
        return true;
    }
    //attrval
    public function attrvallist($relative_path = array()){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            return false;
        }
        if(count($relative_path)<1){
            return false;
        }
        $attr_id = (int)$relative_path[0];

        $attrinfo = $this->XM->product->get_attr_info($attr_id);
        if(!$attrinfo){
            return false;
        }
        $attrvallist = $this->XM->product->get_attrval_list($attr_id);
        if($attrvallist===false){
            return false;
        }

        $this->XM->__wrapview($this->XM->view->load('product/attrvallist',array('attrinfo'=>$attrinfo,'attrvallist'=>$attrvallist),true), 
            null, array('css'=>array('/modules/Product/css/attrvallist.css')));
        return true;
    }
    public function attrvaladd($relative_path = array()){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            return false;
        }
        if(count($relative_path)<1){
            return false;
        }
        $attr_id = (int)$relative_path[0];
        if(($attrinfo = $this->XM->product->get_attr_info($attr_id))===false){
            return false;
        }
        $languageList = $this->XM->lang->getLanguageList();
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_attrval')){
            $name = array();
            $attr = isset($_POST['attr'])?$_POST['attr']:array();
            $important = isset($_POST['important'])?(int)$_POST['important']:0;
            $originname = isset($_POST['originname'])?(string)$_POST['originname']:'';
            foreach($languageList as $language){
                $language_id = $language['id'];
                $name[$language_id] = (isset($_POST['name'])&&is_array($_POST['name'])&&isset($_POST['name'][$language_id]))?$_POST['name'][$language_id]:'';
            }
            $err = null;
            if(!$this->XM->product->add_attrval($attr_id, $attr, $originname, $name, $important, $err)){
                $this->XM->addMessage($err, 0);
            } else {
                unset($_POST);
                $this->XM->addMessage(langTranslate('product', 'attrval', 'Attribute value have been added', 'Attribute value have been added'), 2);
                $this->XM->setPushStateUrl(BASE_URL.'/moderate/product/attributes/'.$attrinfo['attrgroup_id'].'/'.$attr_id);
                return $this->attrvallist(array($attr_id));
            }
        }
        
        $attrvalinfo = array('parent'=>0);
        $needparent = $attrinfo['parent_id']!=0;
        $attrvaltree = array();
        if($needparent){
            $err = null;
            if(($attrvaltree = $this->XM->product->get_attrval_edit_attrval_tree($attrinfo['attrgroup_id'],$attrinfo['parent_id'],isset($_POST['attr'])?$_POST['attr']:array(),$attrinfo['system'],$err))===FALSE){
                $this->XM->addMessage($err, 0);
                $this->XM->__wrapview(null, null, null);
                return true;
            }
        }
        $this->XM->__wrapview($this->XM->view->load('product/editattrval',array('languageList'=>$languageList,'attrinfo'=>$attrinfo,'attrvalinfo'=>$attrvalinfo,'needparent'=>$needparent,'attrvaltree'=>$attrvaltree,'is_modal'=>false),true), 
            null, array('css'=>array('/modules/Product/css/editattrval.css'),'pack'=>array('dropbox')));
        return true;
    }
    public function attrvaledit($relative_path = array()){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            return false;
        }
        if(count($relative_path)<2){
            return false;
        }
        $attr_id = (int)$relative_path[0];
        $attrval_id = (int)$relative_path[1];
        if(($attrinfo = $this->XM->product->get_attr_info($attr_id))===false){
            return false;
        }
        $languageList = $this->XM->lang->getLanguageList();
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_attrval')){
            $name = array();
            $attr = isset($_POST['attr'])?$_POST['attr']:array();
            $important = isset($_POST['important'])?(int)$_POST['important']:0;
            $originname = isset($_POST['originname'])?(string)$_POST['originname']:'';
            foreach($languageList as $language){
                $language_id = $language['id'];
                $name[$language_id] = (isset($_POST['name'])&&is_array($_POST['name'])&&isset($_POST['name'][$language_id]))?$_POST['name'][$language_id]:'';
            }
            $err = null;
            if(!$this->XM->product->edit_attrval($attrval_id, $attr, $originname, $name, $important, $err)){
                $this->XM->addMessage($err, 0);
            } else {
                unset($_POST);
                $this->XM->addMessage(langTranslate('product', 'attrval', 'Attribute value have been edited', 'Attribute value have been edited'), 2);
                $this->XM->setPushStateUrl(BASE_URL.'/moderate/product/attributes/'.$attrinfo['attrgroup_id'].'/'.$attr_id);
                return $this->attrvallist(array($attr_id));
            }
        }
        
        $attrvalinfo = $this->XM->product->get_attrval_info_for_all_languages($attrval_id);
        $needparent = $attrinfo['parent_id']!=0;
        $attrvaltree = array();
        if($needparent){
            $err = null;
            if(($attrvaltree = $this->XM->product->get_attrval_edit_attrval_tree($attrinfo['attrgroup_id'],$attrinfo['parent_id'],isset($_POST['attr'])?$_POST['attr']:array($attrvalinfo['parent']),$attrinfo['system'],$err))===FALSE){
                $this->XM->addMessage($err, 0);
                $this->XM->__wrapview(null, null, null);
                return true;
            }
        }
        $css = array();
        $js = array();
        $content = $this->XM->view->load('product/editattrval',array('languageList'=>$languageList,'attrinfo'=>$attrinfo,'attrvalinfo'=>$attrvalinfo,'needparent'=>$needparent,'attrvaltree'=>$attrvaltree,'is_modal'=>false), true);
        $css[] = '/modules/Product/css/editattrval.css';
        //alternate spellings
        $content .= $this->XM->view->load('product/attrvalalternatespelling',array('attrval_id'=>$attrval_id,'list'=>$this->XM->product->get_alternate_spelling_list($attrval_id)), true);
        $css[] = '/modules/Product/css/attrvalalternatespelling.css';
        $js[] = '/modules/Product/js/attrvalalternatespelling.js';
        //analogs
        if($this->XM->product->attr_can_have_analog($attr_id)){
            $content .= $this->XM->view->load('product/attrvalanalog',array('attrval_id'=>$attrval_id,'list'=>$this->XM->product->get_analog_list($attrval_id),'possible_analog_list'=>$this->XM->product->get_possible_analog_list($attrval_id)), true);
            $css[] = '/modules/Product/css/attrvalanalog.css';
            $js[] = '/modules/Product/js/attrvalanalog.js';
        }
        $this->XM->__wrapview($content, null, array('css'=>$css,'js'=>$js,'pack'=>array('dropbox')));
        return true;
    }
    //product
    public function productadd(){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_ADD_PRODUCT)){
            return false;
        }
        $languageList = $this->XM->lang->getLanguageList();
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_product')){
            $name = array();
            $isvintage = isset($_POST['isvintage'])&&$_POST['isvintage']?1:0;
            $vineyard = isset($_POST['vineyard'])?(string)$_POST['vineyard']:'';
            $alcohol_content = isset($_POST['alcohol_content'])?(string)$_POST['alcohol_content']:'';
            $originname = isset($_POST['originname'])?(string)$_POST['originname']:'';
            $image_ids = isset($_POST['image_id'])?$_POST['image_id']:array();
            $attributes = isset($_POST['attr'])?$_POST['attr']:array();
            $blend = isset($_POST['blend'])&&$_POST['blend']?true:false;
            $grape_variety_concentration = array();
            if($blend){
                $grape_variety_concentration = isset($_POST['grape_variety_concentration'])&&is_array($_POST['grape_variety_concentration'])?$_POST['grape_variety_concentration']:array();
            }
            foreach($languageList as $language){
                $language_id = $language['id'];
                $name[$language_id] = (isset($_POST['name'])&&is_array($_POST['name'])&&isset($_POST['name'][$language_id]))?$_POST['name'][$language_id]:'';
            }
            $err = null;
            if(!($product_id = $this->XM->product->add_product($attributes, $isvintage, $vineyard, $alcohol_content, $originname, $name, $image_ids, $blend, $grape_variety_concentration, $err))){
                $this->XM->addMessage($err, 0);
            } else {
                unset($_POST);
                $this->XM->addMessage(langTranslate('product', 'product', 'Product have been added', 'Product have been added'), 2);
                $this->XM->setPushStateUrl(BASE_URL.'/product/'.$product_id.'/vintage/add');
                return $this->vintageadd(array($product_id));
                // redirect(BASE_URL.'/product/'.$product_id.'/vintage/add');
            }
        }
        $err = null;
        if(($attrvaltree = $this->XM->product->get_edit_product_attrval_tree(isset($_POST['attr'])?$_POST['attr']:null,false,false,$err))===FALSE){
            $this->XM->addMessage($err, 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $productinfo = array('id'=>null,'images'=>array(),'full_name_template'=>array(),'grape_variety_concentration'=>array());
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_product')){
            $imagedata = array();
            if(isset($_POST['image_id'])&&is_array($_POST['image_id'])&&!empty($_POST['image_id'])){
                $imagedata = $this->XM->product->get_image_data($_POST['image_id']);
            }
            $productinfo['images'] = $imagedata;
            $full_name_templates = array();
            if(isset($_POST['attr'])){
                $full_name_templates = $this->XM->product->get_full_name_templates($_POST['attr']);
            }
            $productinfo['full_name_template'] = $full_name_templates;
        }
        
        $this->XM->__wrapview($this->XM->view->load('product/editproduct',array('languageList'=>$languageList,'attrvaltree'=>$attrvaltree,'productinfo'=>$productinfo),true), 
            null, array('css'=>array('/modules/Product/css/editproduct.css','/modules/Product/css/editattrval.css'),'js'=>array('/modules/Product/js/editproduct.js'),'pack'=>array('dropbox','gallery','mask')));
        return true;
    }
    private function __productedit($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $product_id = (int)$relative_path[0];

        $productinfo = $this->XM->product->get_product_info_for_all_languages($product_id);
        if(!$productinfo){
            return false;
        }
        if(!$productinfo['can_edit']){
            return false;
        }

        $languageList = $this->XM->lang->getLanguageList();
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_product')){
            $name = array();
            $isvintage = isset($_POST['isvintage'])&&$_POST['isvintage']?1:0;
            $vineyard = isset($_POST['vineyard'])?(string)$_POST['vineyard']:'';
            $alcohol_content = isset($_POST['alcohol_content'])?(string)$_POST['alcohol_content']:'';
            $originname = isset($_POST['originname'])?(string)$_POST['originname']:'';
            $image_ids = isset($_POST['image_id'])?$_POST['image_id']:array();
            $attributes = isset($_POST['attr'])?$_POST['attr']:array();
            $blend = isset($_POST['blend'])&&$_POST['blend']?true:false;
            $grape_variety_concentration = array();
            if($blend){
                $grape_variety_concentration = isset($_POST['grape_variety_concentration'])&&is_array($_POST['grape_variety_concentration'])?$_POST['grape_variety_concentration']:array();
            }
            foreach($languageList as $language){
                $language_id = $language['id'];
                $name[$language_id] = (isset($_POST['name'])&&is_array($_POST['name'])&&isset($_POST['name'][$language_id]))?$_POST['name'][$language_id]:'';
            }
            $err = null;
            if(!($this->XM->product->edit_product($product_id, $attributes, $isvintage, $vineyard, $alcohol_content, $originname, $name, $image_ids, $blend, $grape_variety_concentration, $err))){
                $this->XM->addMessage($err, 0);
            } else {
                unset($_POST);
                $this->XM->addMessage(langTranslate('product', 'product', 'Product have been edited', 'Product have been edited'), 2);
                if(($vintage_id = $this->XM->product->get_blank_vintage_id($product_id))!==false){
                    $this->XM->setPushStateUrl(BASE_URL.'/vintage/'.$vintage_id);
                    return $this->vintageview(array($vintage_id));
                    // redirect(BASE_URL.'/vintage/'.$this->XM->product->get_blank_vintage_id($product_id));
                }
            }
        }


        $err = null;
        if(($attrvaltree = $this->XM->product->get_edit_product_attrval_tree(isset($_POST['attr'])?$_POST['attr']:$productinfo['attr'],false,false,$err))===FALSE){
            $this->XM->addMessage($err, 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_product')){
            $imagedata = array();
            if(isset($_POST['image_id'])&&is_array($_POST['image_id'])&&!empty($_POST['image_id'])){
                $imagedata = $this->XM->product->get_image_data($_POST['image_id']);
            }
            $productinfo['images'] = $imagedata;
            $full_name_templates = array();
            if(isset($_POST['attr'])){
                $full_name_templates = $this->XM->product->get_full_name_templates($_POST['attr']);
            }
            $productinfo['full_name_template'] = $full_name_templates;
        }
        
        $this->XM->__wrapview($this->XM->view->load('product/editproduct',array('languageList'=>$languageList,'attrvaltree'=>$attrvaltree,'productinfo'=>$productinfo,'blankvintageid'=>$this->XM->product->get_blank_vintage_id($productinfo['id'])),true), 
            null, array('css'=>array('/modules/Product/css/editproduct.css','/modules/Product/css/editattrval.css'),'js'=>array('/modules/Product/js/editproduct.js'),'pack'=>array('dropbox','gallery','mask')));
        return true;
    }
    //vintage
    public function vintagefilter($relative_path = array(),$showlogoheader = false){
        $translations_only = false;
        $myreviews_only = false;
        $pending_reviews_for_tasting = null;
        $only_scored = false;
        $onlyblank = false;
        $only_waiting_for_approval = false;
        $showfavourite = false;
        $show_only_personally_scored_filter_option = false;
        $showpersonalscore = false;
        if($this->XM->user->isLoggedIn()){
            $showfavourite = true;
            $show_only_personally_scored_filter_option = true;
            $showpersonalscore = true;
        }
        $showcompanyfavourite = false;
        if($this->XM->user->isInCompany()){
            $showcompanyfavourite = true;
        }
        if(!empty($relative_path)){
            switch($relative_path[0]){
                case 'onlyscored':
                    $only_scored = true;
                    break;
                case 'blanks':
                    if(!$this->XM->user->isLoggedIn() || !$this->XM->user->isInCompany()){
                        return false;
                    }
                    $onlyblank = true;
                    break;
                case 'translations':
                    if(!$this->XM->user->isLoggedIn()){
                        return false;
                    }
                    $translations_only = true;
                    break;
                case 'myreviews':
                    if(!$this->XM->user->isLoggedIn()){
                        return false;
                    }
                    $myreviews_only = true;
                    break;
                case 'pending_reviews_for_tasting':
                    if(!$this->XM->user->isLoggedIn()){
                        return false;
                    }
                    if(count($relative_path)<2){
                        return false;
                    }
                    $pending_reviews_for_tasting = (int)$relative_path[1];
                    $only_scored = false;
                    break;
                case 'approve':
                    if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_APPROVE_PRODUCT)){
                        return false;
                    }
                    if($this->XM->user->getUserId()==95){
                        return false;
                    }
                    $onlyblank = true;
                    $only_waiting_for_approval = true;
                    break;
            }
        }
        $err = null;
        if(($attrvaltree = $this->XM->product->get_product_filter_attrval_tree(true,$onlyblank,$only_scored,$only_waiting_for_approval,false,false,true,$err))===FALSE){
            $this->XM->addMessage($err, 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $tasting_list = array();
        if($only_scored){
            $this->XM->tasting->preload();
            $page = 1;
            $pagelimit = null;
            $count = null;
            $err = null;
            if(($tasting_list = $this->XM->tasting->filter_tasting(null, null, array(\TASTING\TASTING_STATUS_FINISHED), false, true, false, false, false, false, null, false, false, null, false, false, null, false, $page, $pagelimit, $count, $err)) === false){
                $this->XM->addMessage($err, 0);
                $this->XM->__wrapview(null, null, null);
                return true;
            }
        }
        $this->XM->__wrapview(
            ($showlogoheader?$this->XM->view->load('main/logoheader',null,true):'').
            $this->XM->view->load('product/vintagefilter',array('attrvaltree'=>$attrvaltree,'show_all_scores'=>$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_VIEW_ALL_SCORES),'can_view_score_details'=>$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_VIEW_SCORE_DETAILS),'can_add'=>$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_ADD_PRODUCT),'translations_only'=>$translations_only,'pending_reviews_for_tasting'=>$pending_reviews_for_tasting,'myreviews_only'=>$myreviews_only,'only_scored'=>$only_scored,'tasting_list'=>$tasting_list,'only_waiting_for_approval'=>$only_waiting_for_approval,'showcompanyfavourite'=>$showcompanyfavourite,'showfavourite'=>$showfavourite,'show_only_personally_scored_filter_option'=>$show_only_personally_scored_filter_option,'showpersonalscore'=>$showpersonalscore,'onlyblank'=>$onlyblank,'expert_level_list'=>$this->XM->user->get_expert_level_list()),true), 
            null, array('css'=>array('/modules/Product/css/vintagefilter.css'),'js'=>array('/modules/Product/js/vintagefilter.js'),'pack'=>array('dropbox','gallery','mask','filterform')));
        return true;
    }
    public function compareproduct($relative_path = array()){
        $first_vintage_id = null;
        if(count($relative_path)>=1){
            $first_vintage_id = $this->XM->product->get_blank_vintage_id((int)$relative_path[0]);
        }
        $second_vintage_id = null;
        if(count($relative_path)>=2){
            $second_vintage_id = $this->XM->product->get_blank_vintage_id((int)$relative_path[1]);
        }
        if($second_vintage_id==$first_vintage_id){
            $second_vintage_id = null;
        }
        return $this->__comparevintage($first_vintage_id, $second_vintage_id,'products');
    }
    private function __comparevintage($first_vintage_id, $second_vintage_id,$type='products'){
        if(($firstvintageinfo = $this->XM->product->get_vintage_info($first_vintage_id))===FALSE){
            $firstvintageinfo = null;
        }
        if(($secondvintageinfo = $this->XM->product->get_vintage_info($second_vintage_id))===FALSE){
            $secondvintageinfo = null;
        }
        if(!$firstvintageinfo && $secondvintageinfo){
            $firstvintageinfo = $secondvintageinfo;
            $secondvintageinfo = null;
        }
        $this->XM->__wrapview($this->XM->view->load('product/comparevintage',array('firstvintage'=>$firstvintageinfo,'secondvintage'=>$secondvintageinfo,'type'=>$type),true), 
            null, array('css'=>array('/modules/Product/css/comparevintage.css','/modules/Product/css/viewvintage.css','/modules/Product/css/vintagefilter.css'),
                'js'=>array('/modules/Product/js/comparevintage.js','/modules/Product/js/viewvintage.js','/modules/Product/js/vintagefilter.js'),
                'pack'=>array('dropbox','mask','filterform')));
        return true;
    }
    public function resolve_vintage_doubles_for_product($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $product_id = (int)$relative_path[0];
        $first_vintage_id = $second_vintage_id = null;
        if(!$this->XM->product->get_double_vintage_ids($product_id, $first_vintage_id, $second_vintage_id)){
            $this->XM->addMessage(langTranslate('product','compare','All problems were resolved','All problems were resolved'), 2);
            return $this->vintageview(array($this->XM->product->get_blank_vintage_id($product_id)));
        }
        return $this->__comparevintage($first_vintage_id, $second_vintage_id,'vintages');
    }
    
    public function vintageview($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $vintage_id = (int)$relative_path[0];
        if(($vintageinfo = $this->XM->product->get_vintage_info($vintage_id))===FALSE){
            return false;
        }
        $css = array();
        $js = array();
        $content = $this->XM->view->load('product/viewvintage',array('vintageinfo'=>$vintageinfo),true);
        $css[] = '/modules/Product/css/viewvintage.css';
        $js[] = '/modules/Product/js/viewvintage.js';
        //rewards
        if($vintageinfo['won_contest_nominations']){
            $contest_nomination_list = $this->XM->tasting->get_vintage_contest_nomination_list($vintage_id);
            if(!empty($contest_nomination_list)){
                $content .= $this->XM->view->load('tasting/vintage_contest_nomination_list',array('contest_nomination_list'=>$contest_nomination_list,'expert_level_list'=>$this->XM->user->get_expert_level_list(),'vintage_id'=>$vintage_id),true);
                $css[] = '/modules/Tasting/css/vintage_contest_nomination_list.css';    
            }
        }

        $reviews = array();
        if(count($relative_path)>=2 && $relative_path[1]=='myreviews'){
            $reviews = $this->XM->product->get_vintage_reviews($vintage_id,null,true);
        }
        if(!empty($reviews)){
            foreach($reviews as $reviewinfo){
                $content .= $this->XM->view->load('product/viewreview',array('reviewinfo'=>$reviewinfo,'review_elements'=>array(),'short_review'=>true),true);
            }
            $css[] = '/modules/Product/css/viewreview.css';
        }
        
        if($vintageinfo['isvintage']){
            $vintageList = $this->XM->product->get_alt_vintage_list($vintageinfo['product_id'], $vintage_id);
            $content.= $this->XM->view->load('product/vintagealtlist',array('product_id'=>$vintageinfo['product_id'],'vintageList'=>$vintageList,'can_add'=>$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_ADD_PRODUCT)),true);
            $css[] = '/modules/Product/css/vintagealtlist.css';
        }
        $this->XM->__wrapview($content, null, array('css'=>$css,'js'=>$js,'pack'=>array('gallery')));
        return true;
    }

    public function vintageadd($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $product_id = (int)$relative_path[0];
        if(($blankvintageinfo = $this->XM->product->get_vintage_info($this->XM->product->get_blank_vintage_id($product_id)))===FALSE){
            return false;
        }
        if(!$blankvintageinfo['isvintage']){//can't add vintage no non-vintage product
            redirect('/vintage/'.$blankvintageinfo['id']);
            return false;//never
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_ADD_PRODUCT)){
            return false;
        }
        $languageList = $this->XM->lang->getLanguageList();
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_vintage')){
            $year = (int)$_POST['year'];
            $alcohol_content = (float)str_replace(',', '.', $_POST['alcohol_content']);
            $attributes = isset($_POST['attr'])?$_POST['attr']:array();
            $grape_variety_concentration = isset($_POST['grape_variety_concentration'])&&is_array($_POST['grape_variety_concentration'])?$_POST['grape_variety_concentration']:array();
            $description = array();
            foreach($languageList as $language){
                $language_id = $language['id'];
                $description[$language_id] = (isset($_POST['desc'])&&is_array($_POST['desc'])&&isset($_POST['desc'][$language_id]))?$_POST['desc'][$language_id]:'';
            }
            $err = null;
            if(!($vintage_id = $this->XM->product->add_vintage($product_id, $year, $alcohol_content, $attributes, $grape_variety_concentration, $description, $err))){
                $this->XM->addMessage($err, 0);
            } else {
                unset($_POST);
                $this->XM->addMessage(langTranslate('product', 'vintage', 'Vintage have been added', 'Vintage have been added'), 2);
                $this->XM->setPushStateUrl(BASE_URL.'/vintage/'.$vintage_id);
                return $this->vintageview(array($vintage_id));
                // redirect(BASE_URL.'/vintage/'.$vintage_id);
            }
        }
        $err = null;
        if(($attrvaltree = $this->XM->product->get_edit_product_attrval_tree(isset($_POST['attr'])?$_POST['attr']:$this->XM->product->get_product_attributes($product_id),true,$blankvintageinfo['isblend'],$err))===FALSE){
            $this->XM->addMessage($err, 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $vintageinfo = array('id'=>null,'product_id'=>$product_id,'year'=>date('Y'),'alcohol_content'=>$blankvintageinfo['alcohol_content'],'grape_variety_concentration'=>$this->XM->product->get_product_grape_variety_concentration($product_id));
        $this->XM->__wrapview($this->XM->view->load('product/editvintage',array('languageList'=>$languageList,'attrvaltree'=>$attrvaltree,'vintageinfo'=>$vintageinfo,'blankvintageinfo'=>$blankvintageinfo,'blankvintageid'=>$this->XM->product->get_blank_vintage_id($product_id)),true).
            $this->XM->view->load('product/viewvintage',array('vintageinfo'=>$blankvintageinfo),true), 
            null, array('css'=>array('/modules/Product/css/editvintage.css','/modules/Product/css/viewvintage.css','/modules/Product/css/editattrval.css'),'js'=>array('/modules/Product/js/editvintage.js','/modules/Product/js/viewvintage.js'),'pack'=>array('dropbox','gallery','mask')));
        return true;
    }
    public function vintageedit($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $vintage_id = (int)$relative_path[0];
        if(($vintageinfo = $this->XM->product->get_vintage_info_for_all_languages($vintage_id))===FALSE){
            return false;
        }
        if(!$vintageinfo['can_edit']){
            return false;
        }
        if($vintageinfo['is_blank']){
            return $this->__productedit(array($vintageinfo['product_id']));
        }
        $languageList = $this->XM->lang->getLanguageList();
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_vintage')){
            $year = (int)$_POST['year'];
            $alcohol_content = (float)str_replace(',', '.', $_POST['alcohol_content']);
            $attributes = isset($_POST['attr'])?$_POST['attr']:array();
            $grape_variety_concentration = isset($_POST['grape_variety_concentration'])&&is_array($_POST['grape_variety_concentration'])?$_POST['grape_variety_concentration']:array();
            $description = array();
            foreach($languageList as $language){
                $language_id = $language['id'];
                $description[$language_id] = (isset($_POST['desc'])&&is_array($_POST['desc'])&&isset($_POST['desc'][$language_id]))?$_POST['desc'][$language_id]:'';
            }
            $err = null;
            if(!$this->XM->product->edit_vintage($vintage_id, $year, $alcohol_content, $attributes, $grape_variety_concentration, $description, $err)){
                $this->XM->addMessage($err, 0);
            } else {
                unset($_POST);
                // redirect(BASE_URL.'/vintage/'.$vintage_id);
                $this->XM->addMessage(langTranslate('product', 'vintage', 'Vintage have been edited', 'Vintage have been edited'), 2);
                $this->XM->setPushStateUrl(BASE_URL.'/vintage/'.$vintage_id);
                return $this->vintageview(array($vintage_id));
            }
        }
        if(($blankvintageinfo = $this->XM->product->get_vintage_info($this->XM->product->get_blank_vintage_id($vintageinfo['product_id'])))===FALSE){
            return false;
        }
        $err = null;
        if(($attrvaltree = $this->XM->product->get_edit_product_attrval_tree(isset($_POST['attr'])?$_POST['attr']:$vintageinfo['attr'],true,$blankvintageinfo['isblend'],$err))===FALSE){
            $this->XM->addMessage($err, 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        

        $this->XM->__wrapview($this->XM->view->load('product/editvintage',array('languageList'=>$languageList,'attrvaltree'=>$attrvaltree,'vintageinfo'=>$vintageinfo,'blankvintageinfo'=>$blankvintageinfo),true).
            $this->XM->view->load('product/viewvintage',array('vintageinfo'=>$blankvintageinfo),true), 
            null, array('css'=>array('/modules/Product/css/editvintage.css','/modules/Product/css/viewvintage.css','/modules/Product/css/editattrval.css'),'js'=>array('/modules/Product/js/editvintage.js','/modules/Product/js/viewvintage.js'),'pack'=>array('dropbox','gallery','mask')));
        return true;
    }
    public function vintagetranslations($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $vintage_id = (int)$relative_path[0];
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
            return false;
        }
        if(($vintageinfo = $this->XM->product->get_vintage_info($vintage_id))===FALSE){
            return false;
        }
        $err = null;
        if(($translations = $this->XM->product->get_vintage_translations($vintage_id, $err))===false){
            $this->XM->addMessage($err, 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }

        $this->XM->__wrapview($this->XM->view->load('product/viewvintage',array('vintageinfo'=>$vintageinfo),true).
            $this->XM->view->load('product/viewvintagetranslations',array('vintageinfo'=>$vintageinfo,'translations'=>$translations),true), 
            null, array('css'=>array('/modules/Product/css/viewvintage.css','/modules/Product/css/viewvintagetranslations.css'),'js'=>array('/modules/Product/js/viewvintage.js','/modules/Product/js/viewvintagetranslations.js'),'pack'=>array('gallery')));
        return true;
    }
    public function vintagepersonalreviewadd($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $tpv_id = (int)$relative_path[0];
        return $this->vintagereviewadd(array($tpv_id,null),true);
    }
    
    public function vintagereviewadd($relative_path = array(),$personal_review = false){
        if(count($relative_path)<2){
            return false;
        }
        $tpv_id = (int)$relative_path[0];
        $tasting_id = null;
        $review_id = null;
        if(!$personal_review){
            $tasting_id = (int)$relative_path[1];
            if(!$this->XM->tasting->check_review_request($tpv_id, $review_id)){
                return false;
            }
        } else {
            $tasting_id = 0;
        }

        $tasting_vintage_list = $this->XM->product->get_tasting_vintage_list($tasting_id, false, false, false, false, false, false,false,true,null,$tpv_id,null,$personal_review,true);
        if(empty($tasting_vintage_list)){
            return false;
        }
        $vintage_id = $tasting_vintage_list[0]['id']?$tasting_vintage_list[0]['id']:null;
        
        $languageList = $this->XM->lang->getLanguageList();
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='add_review')){
            $score = (float)str_replace(',', '.', isset($_POST['score'])?$_POST['score']:0);
            $personal_comment = isset($_POST['personal_comment'])?(string)$_POST['personal_comment']:null;
            $review = array();
            foreach($languageList as $language){
                $language_id = $language['id'];
                $review[$language_id] = (isset($_POST['review'])&&is_array($_POST['review'])&&isset($_POST['review'][$language_id]))?$_POST['review'][$language_id]:'';
            }
            $subdata = $_POST;
            $err = null;
            if(!($review_id = $this->XM->product->add_review($tpv_id, $score, $personal_comment, $review, $subdata, (isset($_POST['wineisfaulty'])&&$_POST['wineisfaulty']), (isset($_POST['didnottry'])&&$_POST['didnottry']), $err))){
                $this->XM->addMessage($err, 0);
            } else {
                if((isset($_POST['wineisfaulty'])&&$_POST['wineisfaulty']) || (isset($_POST['didnottry'])&&$_POST['didnottry'])){
                    $this->XM->addMessage(langTranslate('product', 'review', 'Review request has been cancelled', 'Review request has been cancelled'), 2);
                } else {
                    $this->XM->addMessage(langTranslate('product', 'review', 'Review has been saved', 'Review has been saved'), 2);    
                }
                unset($_POST);
                if($personal_review){
                    $this->XM->setPushStateUrl(BASE_URL.'/vintage/'.$vintage_id);
                    return $this->vintageview(array($vintage_id));    
                } else {
                    $this->XM->setPushStateUrl(BASE_URL.'/myreview/pending/tasting/'.$tasting_id.'/products');
                    return $this->XM->__UI->tasting->pendingtastingproducts(array($tasting_id));    
                }
            }
        }
        
        $tasting_content = '';
        $tasting_assessment = true;
        if(!$personal_review){
            if(!($tastinginfo = $this->XM->tasting->get_tasting($tasting_id))){
                return false;
            }
            $tasting_assessment = (bool)$tastinginfo['assessment'];
            $tasting_content = $this->XM->view->load('tasting/viewtasting',array('tastinginfo'=>$tastinginfo,'shortform'=>true,'compact'=>true),true);
            $css[] = '/modules/Tasting/css/viewtasting.css';
            $js[] = '/modules/Tasting/js/viewtasting.js';
        }
        $review = array();
        if(isset($_POST)&&isset($_POST['action'])&&in_array($_POST['action'],array('add_review','load-review-draft'))){
            $review = $_POST;
        } elseif($review_id){
            $review = $this->XM->product->get_review_info_for_edit($review_id);
        } else {
            $review = array('score'=>'');
        }
        $tasting_review_particularity_data = $this->XM->tasting->get_tasting_review_particularity_data($tasting_id);
        $vintage_review_filter = array();
        if(in_array('wine-type', $tasting_review_particularity_data) || in_array('color-spectrum', $tasting_review_particularity_data)){
            if($vintage_id){
                $vintage_review_filter = $this->XM->product->get_vintage_review_filter($vintage_id);
            } else {
                $vintage_review_filter = $this->XM->product->get_vintage_review_filter($this->XM->product->get_vintage_id_for_tasting_product_vintage($tpv_id,true));
            }
        }

        $similarity_location = array();
        if(!in_array('similarity_location', $tasting_review_particularity_data)){
            $values = array();
            if(isset($review['similarity_location']) && is_array($review['similarity_location'])){
                $values = $review['similarity_location'];
            }
            $err = null;
            $similarity_location = $this->XM->product->get_attrval_edit_attrval_tree(\PRODUCT\LOCATION_ATTRIBUTE_GROUP_ID,null,$values,FALSE,$err);    
        }
        $similarity_grape = array();
        if(!in_array('similarity_grape', $tasting_review_particularity_data)){
            $values = array();
            if(isset($review['similarity_grape']) && is_array($review['similarity_grape'])){
                $values = $review['similarity_grape'];
            }
            $err = null;
            $similarity_grape = $this->XM->product->get_attrval_edit_attrval_tree(\PRODUCT\GRAPE_ATTRIBUTE_GROUP_ID,null,$values,FALSE,$err);
        }
        

        $review_elements = $this->XM->product->get_review_elements();



        $vintage_content = '';
        
        $css = array();
        $js = array();

        $vintage_content = $this->XM->view->load('tasting/viewtasting_products',array('tasting_id'=>$tasting_id,'can_add'=>false,'can_edit_vintage_list'=>false,'tasting_vintage_list'=>$tasting_vintage_list,'show_desc'=>true,'stat_url'=>false,'tpv_id'=>$tpv_id,'can_refresh'=>false,'order_by_index'=>true),true);
        $css[] = '/modules/Tasting/css/viewtasting_products.css';
        $js[] = '/modules/Tasting/js/viewtasting_products.js';
        if($vintage_id){
            if(($vintageinfo = $this->XM->product->get_vintage_info($vintage_id))!==FALSE){
                $vintage_content .= $this->XM->view->load('product/viewvintage',array('vintageinfo'=>$vintageinfo,'compact'=>true),true);
                $css[] = '/modules/Product/css/viewvintage.css';
                $js[] = '/modules/Product/js/viewvintage.js';
            }
        }
        
        $css[] = '/modules/Product/css/editreview.css';
        $js[] = '/modules/Product/js/editreview.js';
        $this->XM->__wrapview(
            $tasting_content.
            $vintage_content.
            $this->XM->view->load('product/editreview',array('review'=>$review,'review_elements'=>$review_elements,'languageList'=>$languageList,'similarity_location'=>$similarity_location,'similarity_grape'=>$similarity_grape,'tasting_review_particularity_data'=>$tasting_review_particularity_data,'vintage_review_filter'=>$vintage_review_filter,'adding'=>true,'tpv_id'=>$tpv_id,'tasting_id'=>$tasting_id,'personal_review'=>$personal_review,'tasting_assessment'=>$tasting_assessment),true), 
            null, array('css'=>$css,'js'=>$js,'pack'=>array('gallery','mask','dropbox')));
        return true;
    }
    public function vintagereviewview($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $review_id = (int)$relative_path[0];
        if(($reviewinfo = $this->XM->product->get_review_info($review_id))===FALSE){
            return false;
        }
        if(($vintageinfo = $this->XM->product->get_vintage_info($reviewinfo['vintage_id']))===FALSE){
            return false;
        }
        $review_elements = $this->XM->product->get_filtered_review_elements($reviewinfo['params']);
        $this->XM->__wrapview($this->XM->view->load('product/viewvintage',array('vintageinfo'=>$vintageinfo,'compact'=>true),true).
            $this->XM->view->load('product/viewreview',array('reviewinfo'=>$reviewinfo,'review_elements'=>$review_elements,'short_review'=>false),true),
            null, array('css'=>array('/modules/Product/css/viewvintage.css','/modules/Product/css/viewreview.css'),'js'=>array('/modules/Product/js/viewvintage.js'),'pack'=>array('gallery')));
        return true;
    }
    public function vintagereviewmerge_personal($relative_path = array()){
        if(!$this->XM->user->isLoggedIn()){
            return false;
        }
        if(count($relative_path)<1){
            return false;
        }
        $vintage_id = (int)$relative_path[0];
        $contest_id = isset($relative_path[1])?(int)$relative_path[1]:0;
        $expert_level_list = $this->XM->user->get_expert_level_list();
        $expert_levels = array_keys($expert_level_list);
        if(($reviewmergeinfo = $this->XM->product->get_review_merge_info($vintage_id,$expert_levels,null,null,null,$contest_id,true,false,true))===FALSE){
            return false;
        }
        $review_elements = $this->XM->product->get_filtered_review_elements($this->XM->product->get_vintage_review_filter($vintage_id));
        
        $this->XM->__wrapview($this->XM->view->load('product/mergereview',array('reviewmergeinfo'=>$reviewmergeinfo,'expert_levels'=>$expert_levels,'expert_level_list'=>$expert_level_list,'review_elements'=>$review_elements),true),
            null, array('css'=>array('/modules/Product/css/mergereview.css')));
        return true;
    }
    public function vintagereviewmerge_ongoing_from_tpv_id($relative_path = array()){
        if(count($relative_path)<2){
            return false;
        }
        $tpv_id = (int)$relative_path[0];
        $expert_level = (int)$relative_path[1];
        if(($vintage_id = $this->XM->product->get_vintage_id_for_tasting_product_vintage($tpv_id))===false){
            return false;
        }
        if(($reviewmergeinfo = $this->XM->product->get_review_merge_info($vintage_id,array($expert_level),null,null,$tpv_id,null,true,true))===FALSE){
            return false;
        }
        $review_elements = $this->XM->product->get_filtered_review_elements($this->XM->product->get_vintage_review_filter($vintage_id));
        $expert_level_list = $this->XM->user->get_expert_level_list();
        $this->XM->__wrapview($this->XM->view->load('product/mergereview',array('reviewmergeinfo'=>$reviewmergeinfo,'expert_levels'=>array($expert_level),'expert_level_list'=>$expert_level_list,'review_elements'=>$review_elements,'blindness'=>$this->XM->product->get_blindness_for_tasting_product_vintage($tpv_id)),true),
            null, array('css'=>array('/modules/Product/css/mergereview.css')));
        return true;
    }
    public function vintagereviewmerge($relative_path = array()){
        if(count($relative_path)<2){
            return false;
        }
        $vintage_id = (int)$relative_path[0];
        $expert_level = (int)$relative_path[1];
        $tasting_id = isset($relative_path[2])?(int)$relative_path[2]:0;
        $user_id = isset($relative_path[3])?(int)$relative_path[3]:0;
        $contest_id = isset($relative_path[4])?(int)$relative_path[4]:0;

        $include_ongoing = false;
        if($contest_id){
            $include_ongoing = true;
        }
        if(($reviewmergeinfo = $this->XM->product->get_review_merge_info($vintage_id,array($expert_level),$tasting_id,$user_id,null,$contest_id,true,$include_ongoing))===FALSE){
            return false;
        }
        $review_elements = $this->XM->product->get_filtered_review_elements($this->XM->product->get_vintage_review_filter($vintage_id));
        $expert_level_list = $this->XM->user->get_expert_level_list();
        $this->XM->__wrapview($this->XM->view->load('product/mergereview',array('reviewmergeinfo'=>$reviewmergeinfo,'expert_levels'=>array($expert_level),'expert_level_list'=>$expert_level_list,'review_elements'=>$review_elements),true),
            null, array('css'=>array('/modules/Product/css/mergereview.css')));
        return true;
    }
    public function vintagescoredetails($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $vintage_id = (int)$relative_path[0];
        if(($vintageinfo = $this->XM->product->get_vintage_info($vintage_id))===FALSE){
            return false;
        }
        $content = $this->XM->view->load('product/viewvintage',array('vintageinfo'=>$vintageinfo,'compact'=>true),true);

        $expert_level_list = $this->XM->user->get_expert_level_list();

        $user_id = isset($relative_path[1])?$relative_path[1]:0;
        if($user_id){
            $userinfo = $this->XM->user->get_user_info($user_id);
            if(!$userinfo){
                return false;
            }
            $content .= $this->XM->view->load('user/viewUser',array('userinfo'=>$userinfo,'expert_level_list'=>$expert_level_list,'can_approve_expert'=>$this->XM->user->check_privilege(\USER\PRIVILEGE_USER_APPROVE_EXPERT), 'compact'=>true),true);
        }


        $tasting_id = isset($relative_path[2])?$relative_path[2]:0;
        if($tasting_id){
            $tastinginfo = $this->XM->tasting->get_tasting($tasting_id);
            if(!$tastinginfo){
                return false;
            }
            $content .= $this->XM->view->load('tasting/viewtasting',array('tastinginfo'=>$tastinginfo,'shortform'=>true, 'compact'=>true),true);
        }
        
        $err = null;
        if(!($vintage_review_details_list = $this->XM->product->get_vintage_review_details_list($vintage_id, $user_id, $tasting_id, $err))){
            $this->XM->addMessage($err, 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $show_depth_urls = (!$user_id && !$tasting_id);
        $this->XM->__wrapview(
            $content.
            $this->XM->view->load('product/scoredetails',array('expert_level_list'=>$expert_level_list,'vintage_review_details_list'=>$vintage_review_details_list,'show_depth_urls'=>$show_depth_urls,'vintage_id'=>$vintage_id,'user_id'=>$user_id,'tasting_id'=>$tasting_id),true),
            null, array('css'=>array('/modules/Product/css/viewvintage.css','/modules/User/css/viewuser.css','/modules/Tasting/css/viewtasting.css','/modules/Product/css/scoredetails.css'),'js'=>array('/modules/Product/js/viewvintage.js','/modules/User/js/viewuser.js','/modules/Tasting/js/viewtasting.js'),'pack'=>array('gallery','dropbox')));
        return true;
    }
    public function vintage_setpersonaltastingproduct($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $vintage_id = (int)$relative_path[0];
        if(($vintageinfo = $this->XM->product->get_vintage_info($vintage_id))===FALSE){
            return false;
        }

        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_tasting_vintage')){
            $isprimeur = isset($_POST['primeur'])?(bool)$_POST['primeur']:false;
            $lot = isset($_POST['lot'])?$_POST['lot']:'';
            $attr = isset($_POST['attr'])?$_POST['attr']:array();
            $desc = isset($_POST['desc'])?$_POST['desc']:'';
            $err = null;
            if(($tpv_id = $this->XM->tasting->add_tasting_product_vintage(null,$vintage_id,$isprimeur,$lot,$attr,null,$desc,false,true,$err)) === false){
                $this->XM->addMessage($err, 0);
            } else {
                $this->XM->setPushStateUrl(BASE_URL.'/myreview/pending/product/'.$tpv_id);
                return $this->vintagepersonalreviewadd(array($tpv_id));
            }
        }

        $tpv_info = array(
                't_id'=>null,
                'pv_id'=>$vintage_id,
                'isprimeur'=>0,
                'lot'=>null,
                'volume'=>null,
                'desc'=>$vintageinfo['desc'],
            ); 
        if(($attrvaltree = $this->XM->product->get_system_attrval_tree(16,isset($_POST['attr'])?$_POST['attr']:null,$err))===FALSE){
            $this->XM->addMessage($err, 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $this->XM->__wrapview($this->XM->view->load('product/tastingform',array('vintage_id'=>$vintage_id,'attrvaltree'=>$attrvaltree,'tasting_product_vintage_info'=>$tpv_info,'vintageinfo'=>$vintageinfo,'personal'=>true),true).$this->XM->view->load('product/viewvintage',array('vintageinfo'=>$vintageinfo),true),
            null, array('css'=>array('/modules/Product/css/viewvintage.css','/modules/Product/css/tastingform.css'),'js'=>array('/modules/Product/js/viewvintage.js','/modules/Product/js/tastingform.js'),'pack'=>array('dropbox')));
        return true;
    }

    public function vintage_offer_searcher($relative_path = array()){
        $search_text = null;
        if(count($relative_path)>=1 && $relative_path[0]!='0'){
            $search_text = urldecode((string)$relative_path[0]);
        }
        $vintagelist = array();
        if(count($relative_path)>=2 && $relative_path[1]!='0'){
            $vintagelist = explode(',', urldecode($relative_path[1]));
        }
        $product_id = null;
        if(count($relative_path)>=3){
            $product_id = (int)$relative_path[2];
        }
        $language_selector = array();
        $languageList = $this->XM->lang->getLanguageListForWrap();
        foreach($languageList as $language){
            $language_selector[] = array('url'=>$this->XM->main->__getCurrUrlForLangCode($language['code']),'code'=>$language['code'],'name'=>$language['name'],'current'=>$language['current']);
        }
        $this->XM->view->load('product/vintageoffersearcher',array('search_text'=>$search_text,'vintagelist'=>$vintagelist,'product_id'=>$product_id,'language_selector'=>$language_selector));
        return true;
    }
   
    
    //ajax
    public function ajax_hide_attrgroup($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        if(!isset($_POST['changeTo'])){
            return false;
        }
        $attrgroup_id = (int)$relative_path[0];
        $changeTo = (bool)$_POST['changeTo'];

        $err = null;
        if(!$this->XM->product->hide_attrgroup($attrgroup_id,$changeTo,$err)){
           $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>array('hide_status'=>$changeTo?1:0))));
        return true;
    }
    public function ajax_hide_attr($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        if(!isset($_POST['changeTo'])){
            return false;
        }
        $attr_id = (int)$relative_path[0];
        $changeTo = (bool)$_POST['changeTo'];

        $err = null;
        if(!$this->XM->product->hide_attr($attr_id,$changeTo,$err)){
           $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>array('hide_status'=>$changeTo?1:0))));
        return true;
    }
    public function ajax_get_attr_val_tree(){
        $pag_ids = isset($_POST['group'])?$_POST['group']:array();
        $max_parent_attr_id = (int)$_POST['maxAttrId'];
        if(!isset($_POST['values'])||!is_array($_POST['values'])){
            $_POST['values'] = array();
        }
        $attr_val_ids = $this->XM->product->clean_attribute_children($_POST['values']);
        $onlyvisible = isset($_POST['onlyvisible'])?(bool)$_POST['onlyvisible']:true;
        $system = isset($_POST['system'])?(bool)$_POST['system']:false;
        $foundation_exclusive = isset($_POST['foundation_exclusive'])?(bool)$_POST['foundation_exclusive']:false;
        $get_doublecheck = isset($_POST['get_doublecheck'])?(bool)$_POST['get_doublecheck']:false;

        $only_used = isset($_POST['only_used'])?(bool)$_POST['only_used']:false;
        if($only_used){
            $only_used = \PRODUCT\PRODUCT_FILTER_ONLY_USED;
            if(isset($_POST['onlyblank'])&&$_POST['onlyblank']){
                $only_used |= \PRODUCT\PRODUCT_FILTER_ONLY_BLANK;
            }
            if(isset($_POST['only_waiting_for_approval'])&&$_POST['only_waiting_for_approval']){
                $only_used |= \PRODUCT\PRODUCT_FILTER_ONLY_WAITING_FOR_APPROVAL;
            }
            if(isset($_POST['onlyscored'])&&$_POST['onlyscored']){
                $only_used |= \PRODUCT\PRODUCT_FILTER_ONLY_SCORED;
            }
            if(isset($_POST['onlyawarded'])&&$_POST['onlyawarded']){
                $only_used |= \PRODUCT\PRODUCT_FILTER_ONLY_AWARDED;
            }
            if(isset($_POST['only_personally_scored'])&&$_POST['only_personally_scored']){
                $only_used |= \PRODUCT\PRODUCT_FILTER_ONLY_PERSONALLY_SCORED;
            }
            if(isset($_POST['onlymyfavourites'])&&$_POST['onlymyfavourites']){
                $only_used |= \PRODUCT\PRODUCT_FILTER_ONLY_MY_FAVOURITES;
            }
            if(isset($_POST['onlycompanyfavourites'])&&$_POST['onlycompanyfavourites']){
                $only_used |= \PRODUCT\PRODUCT_FILTER_ONLY_COMPANY_FAVOURITES;
            }
            if(isset($_POST['showproximity'])&&$_POST['showproximity']){
                $only_used |= \PRODUCT\PRODUCT_FILTER_ONLY_USED_SHOW_PROXIMITY;
            }
        }
        if(($attrvaltree = $this->XM->product->get_attrval_tree($pag_ids,$max_parent_attr_id,$attr_val_ids,$only_used,$foundation_exclusive,$onlyvisible,false,$system,$get_doublecheck,$err)) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>$attrvaltree)));
        return true;
    }
    public function ajax_get_attr_val_form($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $attr_id = (int)$relative_path[0];

        if(($attrinfo = $this->XM->product->get_attr_info($attr_id))===false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>langTranslate('product', 'err', 'Attr doesn\'t exist',  'Attr doesn\'t exist'))));
            return true;
        }
        if(!($attrinfo['can_add']&&$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_USERFILL_ATTRIBUTES))&&!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>langTranslate('product', 'err', 'Access Denied',  'Access Denied'))));
            return true;
        }
        $attrvalinfo = array('parent'=>0);
        $needparent = $attrinfo['parent_id']!=0;
        $attrvaltree = array();
        if($needparent){
            $err = null;
            if(($attrvaltree = $this->XM->product->get_attrval_edit_attrval_tree($attrinfo['attrgroup_id'],$attrinfo['parent_id'],isset($_POST['attr'])?$_POST['attr']:array($attrvalinfo['parent']),$attrinfo['system'],$err))===FALSE){
                $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
                return true;
            }
        }
        $languageList = $this->XM->lang->getLanguageList();
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>$this->XM->view->load('product/editattrval',array('languageList'=>$languageList,'attrinfo'=>$attrinfo,'attrvalinfo'=>$attrvalinfo,'needparent'=>$needparent,'attrvaltree'=>$attrvaltree,'is_modal'=>true),true))));
        return true;
    }
    public function ajax_get_product_filter_form($relative_path = array()){
        $only_blank = true;
        $contest_id = isset($_POST)&&isset($_POST['contest'])?(int)$_POST['contest']:null;
        if($contest_id){
            $only_blank = false;
        }
        if(($attrvaltree = $this->XM->product->get_product_filter_attrval_tree(true,$only_blank,false,false,false,false,true,$err))===FALSE){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $customnameaction = isset($_POST)&&isset($_POST['customnameaction'])&&$_POST['customnameaction'];
        $showfavourite = false;
        $show_only_personally_scored_filter_option = false;
        $showpersonalscore = false;
        if($this->XM->user->isLoggedIn()){
            $showfavourite = true;
            $show_only_personally_scored_filter_option = true;
            $showpersonalscore = true;
        }
        $showcompanyfavourite = false;
        if($this->XM->user->isInCompany()){
            $showcompanyfavourite = true;
        }
        $tasting_list = array();
        if($contest_id){
            $page = 1;
            $pagelimit = null;
            $count = null;
            $err = null;
            if(($tasting_list = $this->XM->tasting->filter_tasting(null, null, array(\TASTING\TASTING_STATUS_FINISHED), false, $contest_id?false:true, false, false, false, false, null, false, $contest_id, null, false, false, null, false, $page, $pagelimit, $count, $err)) === false){
                $this->XM->addMessage($err, 0);
                $this->XM->__wrapview(null, null, null);
                return true;
            }
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>$this->XM->view->load('product/vintagefilter',array('attrvaltree'=>$attrvaltree,'show_all_scores'=>true,'tasting_list'=>$tasting_list,'contest_id'=>$contest_id,'can_view_score_details'=>$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_VIEW_SCORE_DETAILS),'can_add'=>$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_ADD_PRODUCT),'onlyblank'=>$only_blank,'tastingmodal'=>true,'showcompanyfavourite'=>$showcompanyfavourite,'showfavourite'=>$showfavourite,'show_only_personally_scored_filter_option'=>$show_only_personally_scored_filter_option,'showpersonalscore'=>$showpersonalscore,'customnameaction'=>$customnameaction,'expert_level_list'=>$this->XM->user->get_expert_level_list()),true))));
        return true;
    }
    public function ajax_vintageadd_form($relative_path = array()){
        if(count($relative_path)<1){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>langTranslate('product', 'err', 'Internal Error',  'Internal Error'))));
            return true;
        }
        $product_id = (int)$relative_path[0];
        if(($blankvintageinfo = $this->XM->product->get_vintage_info($this->XM->product->get_blank_vintage_id($product_id)))===FALSE){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>langTranslate('product', 'err', 'Internal Error',  'Internal Error'))));
            return true;
        }
        if(!$blankvintageinfo['isvintage']){//can't add vintage no non-vintage product
            redirect('/ajax/vintage/'.$blankvintageinfo['id'].'/view/form');
            return false;//never
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_ADD_PRODUCT)){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>langTranslate('product', 'err', 'Access Denied',  'Access Denied'))));
            return true;
        }
        $err = null;
        if(($attrvaltree = $this->XM->product->get_edit_product_attrval_tree(isset($_POST['attr'])?$_POST['attr']:$this->XM->product->get_product_attributes($product_id),true,$blankvintageinfo['isblend'],$err))===FALSE){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $vintageinfo = array('id'=>null,'product_id'=>$product_id,'year'=>date('Y'),'alcohol_content'=>$blankvintageinfo['alcohol_content'],'grape_variety_concentration'=>$this->XM->product->get_product_grape_variety_concentration($product_id));
        $languageList = $this->XM->lang->getLanguageList();
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>array('formtype'=>'vintageadd','form'=>$this->XM->view->load('product/editvintage',array('languageList'=>$languageList,'attrvaltree'=>$attrvaltree,'vintageinfo'=>$vintageinfo,'blankvintageinfo'=>$blankvintageinfo,'blankvintageid'=>$this->XM->product->get_blank_vintage_id($product_id),'hide_description'=>true),true)))));
        return true;
    }
    
    public function ajax_add_attr_val($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $attr_id = (int)$relative_path[0];

        $languageList = $this->XM->lang->getLanguageList();
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_attrval')){
            $name = array();
            $attr = isset($_POST['attr'])?$_POST['attr']:array();
            $important = isset($_POST['important'])?(int)$_POST['important']:0;
            $originname = isset($_POST['originname'])?(string)$_POST['originname']:'';
            foreach($languageList as $language){
                $language_id = $language['id'];
                $name[$language_id] = (isset($_POST['name'])&&is_array($_POST['name'])&&isset($_POST['name'][$language_id]))?$_POST['name'][$language_id]:'';
            }
            $err = null;
            if(!$attrval_id = $this->XM->product->add_attrval($attr_id, $attr, $originname, $name, $important, $err)){
                $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
                return true;
            } else {
                $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>$this->XM->product->get_attrval_info($attrval_id))));
                return true;
            }
        }
    }
    public function ajax_add_attribute_alternate_spelling($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $attrval_id = (int)$relative_path[0];
        $spelling = isset($_POST['spelling'])?$_POST['spelling']:null;
        $err = null;
        if(!$spelling_id = $this->XM->product->add_attribute_alternate_spelling($attrval_id, $spelling, $err)){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        } else {
            $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>array('id'=>$spelling_id))));
            return true;
        }
    }
    public function ajax_edit_attribute_alternate_spelling($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $spelling_id = (int)$relative_path[0];
        $spelling = isset($_POST['spelling'])?$_POST['spelling']:null;
        $err = null;
        if(!$this->XM->product->edit_attribute_alternate_spelling($spelling_id, $spelling, $err)){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        } else {
            $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
            return true;
        }
    }
    public function ajax_remove_attribute_alternate_spelling($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $spelling_id = (int)$relative_path[0];
        $err = null;
        if(!$this->XM->product->remove_attribute_alternate_spelling($spelling_id, $err)){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        } else {
            $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
            return true;
        }
    }
    public function ajax_attribute_analog_list($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $attrval_id = (int)$relative_path[0];
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>$this->XM->product->get_analog_list($attrval_id))));
        return true;
    }
    public function ajax_add_attribute_analog($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $attrval_id = (int)$relative_path[0];
        $analog_id = isset($_POST['id'])?(int)$_POST['id']:null;
        $err = null;
        if(!$this->XM->product->add_attribute_analog($attrval_id, $analog_id, $err)){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        } else {
            $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
            return true;
        }
    }
    public function ajax_remove_attribute_analog($relative_path = array()){
        if(count($relative_path)<2){
            return false;
        }
        $attrval_id = (int)$relative_path[0];
        $analog_id = (int)$relative_path[1];
        $err = null;
        if(!$this->XM->product->remove_attribute_analog($attrval_id, $analog_id, $err)){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        } else {
            $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
            return true;
        }
    }
    
    public function ajax_get_full_name_templates(){
        $values = (isset($_POST['values'])&&is_array($_POST['values']))?$_POST['values']:array();
        if(($full_name_templates = $this->XM->product->get_full_name_templates($values)) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>$full_name_templates)));
        return true;
    }
    public function ajax_upload_images(){
        if(!isset($_FILES)||!isset($_FILES['image'])){
            $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>array())));
            return true;
        }
        $errList = array();
        $result = array();
        foreach($_FILES['image']['tmp_name'] as $key=>$tmp_name){
            $err = null;
            if(($fileInfo = $this->XM->product->upload_image($tmp_name,$_FILES['image']['size'][$key],$_FILES['image']['name'][$key],$err)) === false){
                $result[] = array('err'=>1,'errmsg'=>$err);
                continue;
            }
            $fileInfo['success'] = 1;
            $result[] = $fileInfo;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>$result)));
        return true;
    }
    public function ajax_delete_image(){
        $id = (int)$_POST['id'];
        $err = null;
        if($this->XM->product->delete_image($id,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_make_image_primary(){
        $id = (int)$_POST['id'];
        $err = null;
        if($this->XM->product->make_image_primary($id,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_check_for_doubles(){
        $product_id = (int)$_POST['id'];
        $attributes = isset($_POST['values'])?$_POST['values']:array();
        $originname = (string)$_POST['originname'];
        $err = null;
        $double_product_id = null;
        if($this->XM->product->check_double_product($product_id, $attributes, $originname, $double_product_id, $err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_check_vintage_for_doubles($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $product_id = (int)$relative_path[0];
        $id = isset($_POST['id'])?(int)$_POST['id']:0;
        $year = isset($_POST['year'])?(int)$_POST['year']:0;
        $double_id = null;
        $err = null;
        if($this->XM->product->check_double_vintage($id, $product_id, $year, $double_id, $err) === false){
            $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>array('double_id'=>$double_id,'double_url'=>BASE_URL.'/vintage/'.$double_id))));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_search_vintage(){
        if(!isset($_POST) || !isset($_POST['action']) || $_POST['action']!='vintage_filter'){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>langTranslate('product', 'err', 'Internal Error',  'Internal Error'))));
            return true;
        }
        $year_from = isset($_POST['year_from'])?(int)$_POST['year_from']:0;
        $year_to = isset($_POST['year_to'])?(int)$_POST['year_to']:0;
        $score_from = isset($_POST['score_from'])?(int)$_POST['score_from']:0;
        $score_to = isset($_POST['score_to'])?(int)$_POST['score_to']:0;
        $alcohol_content_from = isset($_POST['alcohol_content_from'])?$_POST['alcohol_content_from']:0;
        $alcohol_content_to = isset($_POST['alcohol_content_to'])?$_POST['alcohol_content_to']:0;
        $attr = isset($_POST['attr'])?$_POST['attr']:array();
        $search_string = isset($_POST['search_text'])?(string)$_POST['search_text']:'';
        $only_favourite = isset($_POST['only_favourite'])?(bool)$_POST['only_favourite']:false;
        $only_company_favourite = isset($_POST['only_company_favourite'])?(bool)$_POST['only_company_favourite']:false;
        $page = isset($_POST['page'])?(int)$_POST['page']:1;
        $pagelimit = isset($_POST['pagelimit'])?(int)$_POST['pagelimit']:50;
        $only_blank = isset($_POST['onlyblank'])&&$_POST['onlyblank']?true:false;
        $only_waiting_for_approval = isset($_POST['only_waiting_for_approval'])&&$_POST['only_waiting_for_approval']?true:false;
        $only_translations = isset($_POST['onlytranslations'])&&$_POST['onlytranslations']?true:false;
        $only_myreviews = isset($_POST['onlymyreviews'])&&$_POST['onlymyreviews']?true:false;
        $only_scored = isset($_POST['onlyscored'])&&$_POST['onlyscored']?true:false;
        $only_awarded = isset($_POST['onlyawarded'])&&$_POST['onlyawarded']?true:false;
        $tasting_list = (isset($_POST['tasting'])&&is_array($_POST['tasting']))?$_POST['tasting']:array();
        $only_personally_scored = isset($_POST['only_personally_scored'])&&$_POST['only_personally_scored']?true:false;
        $only_from_contest = isset($_POST['only_from_contest'])?(int)$_POST['only_from_contest']:null;
        $only_from_contest_participant = $only_from_contest&&isset($_POST['only_from_contest_participant'])?(int)$_POST['only_from_contest_participant']:null;

        $order_by_field = isset($_POST['orderbyfield'])?$_POST['orderbyfield']:null;
        $order_by_direction_asc = isset($_POST['orderbydirection'])&&$_POST['orderbydirection'];
        
        $only_pending_reviews_for_tasting = isset($_POST['only_pending_reviews_for_tasting'])?$_POST['only_pending_reviews_for_tasting']:null;
        $err = null;
        $count = 0;
        $vintages = null;
        if(($list = $this->XM->product->filter_vintage($search_string, false, $attr, $year_from, $year_to, $score_from, $score_to, $alcohol_content_from, $alcohol_content_to, $only_favourite, $only_company_favourite, $only_blank, $only_waiting_for_approval, $only_translations, $only_myreviews, $only_scored, $only_awarded, $tasting_list, $only_personally_scored, $only_pending_reviews_for_tasting, $only_from_contest, $only_from_contest_participant, false, null, false, $vintages, false, $order_by_field, $order_by_direction_asc, $page, $pagelimit, $count, $err)) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>array(
            'count'=>$count,
            'page'=>$page,
            'pagelimit'=>$pagelimit,
            'list'=>$list))));
        return true;
    }
    public function ajax_vintage_offer_search(){
        if(!isset($_POST) || !isset($_POST['action']) || $_POST['action']!='vintage_filter'){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>langTranslate('product', 'err', 'Internal Error',  'Internal Error'))));
            return true;
        }
        $search_string = isset($_POST['search_text'])?(string)$_POST['search_text']:'';
        $only_having_vintages = isset($_POST['only_having_vintages'])?$_POST['only_having_vintages']:null;
        $return_vintages = isset($_POST['return_vintages'])&&$_POST['return_vintages'];
        $page = isset($_POST['page'])?(int)$_POST['page']:1;
        $err = null;
        $count = 0;
        $pagelimit = 50;
        $vintages = null;
        if(($list = $this->XM->product->filter_vintage($search_string, true, array(), null, null, null, null, null, null, false, false, true, false, false, false, false, false, null, false, false, false, false, true, $only_having_vintages, $return_vintages, $vintages, false, null, true, $page, $pagelimit, $count, $err)) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $key_whitelist = array('pid','name');
        foreach($list as $key=>$value){
            foreach($value as $check_key=>$dummy){
                if(!in_array($check_key, $key_whitelist)){
                    unset($list[$key][$check_key]);
                }
            }
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>array(
            'count'=>$count,
            'page'=>$page,
            'pagelimit'=>$pagelimit,
            'vintages'=>$vintages,
            'list'=>$list))));
        return true;
    }
    public function ajax_vintage_offer_pricelist_get(){
        if(!isset($_POST) || !isset($_POST['action']) || $_POST['action']!='pricelist_get'){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>langTranslate('product', 'err', 'Internal Error',  'Internal Error'))));
            return true;
        }
        $p_id = isset($_POST['id'])?(int)$_POST['id']:null;
        $search_string = isset($_POST['search_text'])?(string)$_POST['search_text']:null;
        $only_having_vintages = isset($_POST['only_having_vintages'])?$_POST['only_having_vintages']:null;
        $omit_fullname = isset($_POST['omit_fullname'])&&$_POST['omit_fullname'];
        $omit_score = isset($_POST['omit_score'])&&$_POST['omit_score'];
        $return_vintages = isset($_POST['return_vintages'])&&$_POST['return_vintages'];
        $check_singles = isset($_POST['check_singles'])&&$_POST['check_singles'];
        

        $order_by_field = isset($_POST['orderbyfield'])?$_POST['orderbyfield']:null;
        $order_by_direction_asc = isset($_POST['orderbydirection'])&&$_POST['orderbydirection'];
        $page = isset($_POST['page'])?(int)$_POST['page']:1;

        $p_ids = array();
        if($p_id){
            $p_ids = array($p_id);
        } elseif($search_string){
            $err = null;
            $dummycount = 0;
            $dummypagelimit = 50;
            $dummyvintages = null;
            $dummypage = 1;
            if(($p_ids = $this->XM->product->filter_vintage($search_string, true, array(), null, null, null, null, null, null, false, false, true, false, false, false, false, false, null, false, false, false, false, true, $only_having_vintages, false, $dummyvintages, true, null, true, $dummypage, $dummypagelimit, $dummycount, $err)) === false){
                $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
                return true;
            }
        }
        // $pagelimit = 1;
        $count = 0;
        $list = array();
        $vintages = array();
        $is_single_vintage = false;
        $is_single_volume = false;
        if(!empty($p_ids)||!$search_string){
            $err = null;
            if(($list = $this->XM->product->pricelist_filter($p_ids, $only_having_vintages, $omit_fullname, $omit_score, $return_vintages, $vintages, $check_singles, $is_single_vintage, $is_single_volume, $order_by_field, $order_by_direction_asc, $page, $pagelimit, $count, $err)) === false){
                $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
                return true;
            }
        }
        
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>array(
            'count'=>$count,
            'page'=>$page,
            'pagelimit'=>$pagelimit,
            'vintages'=>$return_vintages?$vintages:null,
            'singlevintage'=>$check_singles?$is_single_vintage:null,
            'singlevolume'=>$check_singles?$is_single_volume:null,
            'list'=>$list))));
        return true;
    }
    public function ajax_favourite_vintage(){
        if(!isset($_POST) || !isset($_POST['id']) || !isset($_POST['favourite_to'])){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>langTranslate('product', 'err', 'Internal Error',  'Internal Error'))));
            return true;
        }
        $id = (int)$_POST['id'];
        $favourite_to = (bool)$_POST['favourite_to'];
        $err = null;
        if($this->XM->product->favourite_vintage($id,$favourite_to,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_company_favourite_product(){
        if(!isset($_POST) || !isset($_POST['id']) || !isset($_POST['favourite_to'])){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>langTranslate('product', 'err', 'Internal Error',  'Internal Error'))));
            return true;
        }
        $id = (int)$_POST['id'];
        $favourite_to = (bool)$_POST['favourite_to'];
        $err = null;
        if($this->XM->product->company_favourite_product($id,$favourite_to,false,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_vintageadd($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $product_id = (int)$relative_path[0];
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_ADD_PRODUCT)){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>langTranslate('product', 'err', 'Access Denied',  'Access Denied'))));
            return true;
        }
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_vintage')){
            $year = (int)$_POST['year'];
            $double_id = null;
            $err = null;
            if($this->XM->product->check_double_vintage(0, $product_id, $year, $double_id, $err) === false){
                $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>array('id'=>$double_id))));
                return true;
            }
            $alcohol_content = (float)str_replace(',', '.', $_POST['alcohol_content']);
            $attributes = isset($_POST['attr'])?$_POST['attr']:array();
            $description = array();
            $grape_variety_concentration = isset($_POST['grape_variety_concentration'])&&is_array($_POST['grape_variety_concentration'])?$_POST['grape_variety_concentration']:array();
            $languageList = $this->XM->lang->getLanguageList();
            foreach($languageList as $language){
                $language_id = $language['id'];
                $description[$language_id] = (isset($_POST['desc'])&&is_array($_POST['desc'])&&isset($_POST['desc'][$language_id]))?$_POST['desc'][$language_id]:'';
            }
            $err = null;
            if(!($vintage_id = $this->XM->product->add_vintage($product_id, $year, $alcohol_content, $attributes, $grape_variety_concentration, $description, $err))){
                $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
                return true;
            } else {
                unset($_POST);
                $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>array('id'=>$vintage_id))));
                return true;
            }
        }
        $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>langTranslate('product', 'err', 'Internal Error',  'Internal Error'))));
        return true;
    }
    public function ajax_vintageview_form($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $vintage_id = (int)$relative_path[0];
        if(($vintageinfo = $this->XM->product->get_vintage_info($vintage_id))===FALSE){
            return false;
        }
        if(($attrvaltree = $this->XM->product->get_system_attrval_tree(16,null,$err))===FALSE){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $tpv_info = array(
                't_id'=>null,
                'pv_id'=>$vintage_id,
                'isprimeur'=>0,
                'lot'=>null,
                'volume'=>null,
                'isblind'=>0,
                'blindname'=>$vintageinfo['fullname'],
                'desc'=>$vintageinfo['desc'],
            );        
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>array('formtype'=>'vintageview','form'=>$this->XM->view->load('product/tastingform',array('vintage_id'=>$vintage_id,'attrvaltree'=>$attrvaltree,'tasting_product_vintage_info'=>$tpv_info,'vintageinfo'=>$vintageinfo),true).$this->XM->view->load('product/viewvintage',array('vintageinfo'=>$vintageinfo),true)))));
        return true;
    }
    public function ajax_get_tasting_vintage_list($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $tasting_id = (int)$relative_path[0];
        $actions = (isset($_POST['actions'])&&$_POST['actions']);
        $request_review = (isset($_POST['request_review'])&&$_POST['request_review']);
        $evaluations = (isset($_POST['evaluations'])&&$_POST['evaluations']);
        $show_global_expert_automatic_evaluation = (isset($_POST['show_global_expert_automatic_evaluation'])&&$_POST['show_global_expert_automatic_evaluation']);
        $scores = (isset($_POST['scores'])&&$_POST['scores']);
        $awaiting_review_count = (isset($_POST['awaiting_review_count'])&&$_POST['awaiting_review_count']);
        $show_desc = (isset($_POST['show_desc'])&&$_POST['show_desc']);
        $user_id = (isset($_POST['user_id'])&&$_POST['user_id'])?$_POST['user_id']:null;
        $tpv_id = (isset($_POST['tpv_id'])&&$_POST['tpv_id'])?$_POST['tpv_id']:null;
        $order_by_index = isset($_POST['order_by_index'])?(bool)$_POST['order_by_index']:false;

        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>$this->XM->product->get_tasting_vintage_list($tasting_id,false,$actions,$request_review,$evaluations,$show_global_expert_automatic_evaluation,$scores,$awaiting_review_count,$show_desc,$user_id,$tpv_id,null,false,$order_by_index))));
        return true;
    }
    public function ajax_vintage_info($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $product_id = (int)$relative_path[0];
        $vintageinfo = $this->XM->product->get_vintage_info($this->XM->product->get_blank_vintage_id($product_id));

        if($vintageinfo['score']){
            array_unshift($vintageinfo['attributes'], 
                array('label'=>langTranslate('product', 'vintage', 'Score', 'Score'),'values'=>array(array('value'=>$vintageinfo['score']))));
        }
        if($vintageinfo['year']){
            array_unshift($vintageinfo['attributes'], 
                array('label'=>langTranslate('product', 'vintage', 'Year', 'Year'),'values'=>array(array('value'=>$vintageinfo['year']))));
        }
        // if($vintageinfo['vineyard_name']!==null){
        //     array_push($vintageinfo['attributes'], 
        //         array('label'=>langTranslate('product','product','Vineyard', 'Vineyard'),'values'=>array(array('value'=>$vintageinfo['vineyard_name'],'part'=>null))));
        // }
        if($vintageinfo['alcohol_content']!==null){
            array_unshift($vintageinfo['attributes'], 
                array('label'=>langTranslate('product', 'vintage', 'Alcohol Content', 'Alcohol Content'),'values'=>array(array('value'=>$vintageinfo['alcohol_content'].'%'))));
        }
        $key_whitelist = array('fullname','attributes','images','product_id');
        foreach($vintageinfo as $key=>$dummy){
            if(!in_array($key, $key_whitelist)){
                unset($vintageinfo[$key]);
            }
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>$vintageinfo)));
        return true;
    }
    public function ajax_vintage_translation_approve($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        if(!isset($_POST)||!isset($_POST['approve'])){
            return false;
        }
        $translation_id = (int)$relative_path[0];
        $approve = $_POST['approve']?true:false;
        $err = null;
        if($this->XM->product->approve_vintage_translation($translation_id, $approve, $err)===false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_vintagedelete($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $vintage_id = (int)$relative_path[0];
        $err = null;
        if($this->XM->product->delete_vintage($vintage_id, $err)===false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>langTranslate('product','approve','Product has been successfully deleted','Product has been successfully deleted'))));
        return true;
    }
    public function ajax_productapprove($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        if($this->XM->user->getUserId()==95){
            return false;
        }
        $product_id = (int)$relative_path[0];
        $err = null;
        if($this->XM->product->approve_product($product_id, $err)===false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>langTranslate('product','approve','Product has been successfully approved','Product has been successfully approved'))));
        return true;
    }
    public function ajax_productmerge($relative_path = array()){
        if(count($relative_path)<2){
            return false;
        }
        $merge_into_product_id = (int)$relative_path[0];
        $merge_from_product_id = (int)$relative_path[1];
        $has_conflicts = false;
        $err = null;
        if($this->XM->product->merge_product($merge_into_product_id, $merge_from_product_id, $has_conflicts, $err)===false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        if($has_conflicts){
            $redirect_url = BASE_URL.'/product/'.$merge_into_product_id.'/resolve';
        } else {
            $redirect_url = BASE_URL.'/vintage/'.$this->XM->product->get_blank_vintage_id($merge_into_product_id);
        }
        $this->XM->view->load('view/json',array('data'=>array(
            'success'=>1,
            'successmsg'=>langTranslate('product','approve','Products have been successfully merged','Products have been successfully merged'),
            'data'=>array('redirect_url'=>$redirect_url),
            )));
        return true;
    }
    public function ajax_vintagemerge($relative_path = array()){
        if(count($relative_path)<2){
            return false;
        }
        $merge_into_vintage_id = (int)$relative_path[0];
        $merge_from_vintage_id = (int)$relative_path[1];
        $has_conflicts = false;
        $err = null;
        if($this->XM->product->merge_vintage($merge_into_vintage_id, $merge_from_vintage_id, $err)===false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array(
            'success'=>1,
            'successmsg'=>langTranslate('product','approve','Products have been successfully merged','Products have been successfully merged'),
            )));
        return true;
    }
    
    //temp

    private function __get_attr_val_id($attr_id, $attr_text){
        $attr_text = mb_strtolower(trim($attr_text),'UTF-8');
        if(!strlen($attr_text)){
            return false;
        }
        $res = $this->XM->sqlcore->query('select product_attribute_value.pav_id 
            from product_attribute_value
            left join product_attribute_value_ml on product_attribute_value_ml.pav_id = product_attribute_value.pav_id
            where product_attribute_value.pa_id = '.((int)$attr_id).' and ( LOWER(product_attribute_value.pav_origin_name) = \''.$this->XM->sqlcore->prepString($attr_text,128).'\' or LOWER(product_attribute_value_ml.pav_ml_name) = \''.$this->XM->sqlcore->prepString($attr_text,128).'\' )
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        return (int)$row['pav_id'];
    }

    public function import_products(){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_ADD_PRODUCT)){
            return false;
        }
        set_time_limit(0);
        require_once 'csviter.class.php';
        $csv_iter = new \CSVIter();
        if(!$csv_iter->open(ABS_PATH.'/modules/Product/luding.csv')){
            echo 'Couldn\'t open the file';
            return false;
        }
        $article_id = null;
        $category_ids = array();
        $wine_type_ids = array();
        $appellation_ids = array(39=>array(),40=>array(),41=>array(),42=>array());
        $producer_origin_id = null;
        $producer_rus_id = null;
        $color_ids = array();
        $sweetness_ids = array();
        $grape_ids = array();
        $cert_ids = array();
        $name_origin_id = null;
        $name_rus_id = null;
        $oak_matured_ids = array();
        $coupage_id = null;
        $is_vintage_id = null;
        $alcohol_content_id = null;
        $table_of_contents = $csv_iter->get_row();
        foreach($table_of_contents as $key=>$caption){
            $caption = mb_strtolower($caption,'UTF-8');
            if(strpos($caption,'артикул')!==false){
                $article_id = $key;
                continue;
            }
            if(strpos($caption,'категория')!==false){
                $category_ids[] = $key;
                continue;
            }
            if(strpos($caption,'тип вина')!==false){
                $wine_type_ids[] = $key;
                continue;
            }
            if(strpos($caption,'производитель')!==false){
                if(strpos($caption,'рус')){
                    $producer_rus_id = $key;
                } else {
                    $producer_origin_id = $key;
                }
                continue;
            }
            if(strpos($caption,'страна')!==false){
                $appellation_ids[39][] = $key;
                continue;
            }
            if(strpos($caption,'субрегион')!==false){
                $appellation_ids[41][] = $key;
                continue;
            }
            if(strpos($caption,'регион')!==false){
                $appellation_ids[40][] = $key;
                continue;
            }
            
            if(strpos($caption,'аппелласьон')!==false){
                $appellation_ids[42][] = $key;
                continue;
            }
            if(strpos($caption,'цвет')!==false){
                $color_ids[] = $key;
                continue;
            }
            if(strpos($caption,'сахар')!==false){
                $sweetness_ids[] = $key;
                continue;
            }
            if(strpos($caption,'виноград')!==false){
                $grape_ids[] = $key;
                continue;
            }
            if(strpos($caption,'сертификат')!==false){
                $cert_ids[] = $key;
                continue;
            }
            if(strpos($caption,'название')!==false){
                if(strpos($caption,'рус')){
                    $name_rus_id = $key;
                } else {
                    $name_origin_id = $key;
                }
                continue;
            }
            if(strpos($caption,'дуб')!==false){
                $oak_matured_ids[] = $key;
                continue;
            }
            if(strpos($caption,'сепаж')!==false || strpos($caption,'купаж')!==false){
                $coupage_id = $key;
                continue;
            }
            if(strpos($caption,'винтаж')!==false){
                $is_vintage_id = $key;
                continue;
            }
            if(strpos($caption,'алкогол')!==false){
                $alcohol_content_id = $key;
                continue;
            }
        }

        if(empty($category_ids)){
            echo 'Столбец категорий не найден'.PHP_EOL;
            return false;
        }
        if(empty($color_ids)){
            echo 'Столбец цвета не найден'.PHP_EOL;
            return false;
        }
        if(empty($sweetness_ids)){
            echo 'Столбец содержания сахара не найден'.PHP_EOL;
            return false;
        }
        if(!$name_origin_id){
            echo 'Столбец наименования не найден'.PHP_EOL;
            return false;
        }
        if(!$name_rus_id){
            echo 'Столбец наименования на русском не найден'.PHP_EOL;
            return false;
        }
        if(!$is_vintage_id){
            echo 'Столбец признака винтажной продукции не найден'.PHP_EOL;
            return false;
        }
        $producer_ids = array();
        if($producer_origin_id){
            $producer_ids[] = $producer_origin_id;
        }
        if($producer_rus_id){
            $producer_ids[] = $producer_rus_id;
        }
        $num = 0;
        echo '<style>body,html{padding:0}table{border-collapse:collapse;margin:0 auto}th,td{padding:5px}tbody tr{border-bottom:1px solid #000}tr.success{background-color:#ddf}tr.failure{background-color:#fdd}</style><table><thead><tr><th>Артикул</th><th>WID</th><th>Ошибки</th></tr></thead><tbody>';
        while($product = $csv_iter->get_row()){
            $critical_error = false;
            $num++;
            $errorlist = array();
            $attr = array();
            //article
            if($article_id!==null){
                $article = $product[$article_id];
            } else {
                $article = 'Строка '.$num;
            }
            //category
            $error_values = array();
            $newattr = false;
            foreach($category_ids as $id){
                $newattr = $this->__get_attr_val_id(43,$product[$id]);
                if($newattr!==false){
                    break;
                }
                $error_values[] = $product[$id];
            }
            if($newattr===false){
                $critical_error = true;
                if(!empty($error_values)){
                    $errorlist[] = 'Неизвестная категория: '.implode(' | ', array_unique($error_values));
                }
            }
            $is_vintage = (mb_strtolower($product[$is_vintage_id],'UTF-8')=='винтажная');
            if(in_array($newattr, array(1805,1806))){
                $is_vintage = false;
            }
            $attr[] = $newattr;
            //wine type
            $error_values = array();
            $newattr = false;
            foreach($wine_type_ids as $id){
                $newattr = $this->__get_attr_val_id(51,$product[$id]);
                if($newattr!==false){
                    break;
                }
                $error_values[] = $product[$id];
            }
            if($newattr!==false){
                $attr[] = $newattr;
            } else {
                if(!empty($error_values)){
                    $errorlist[] = 'Неизвестный тип вина: '.implode(' | ', array_unique($error_values));
                }
            }
            //appellation
            $error_values = array();
            $newattr = false;
            $parent_id = false;
            foreach($appellation_ids as $pa_id=>$ids){
                $err = null;
                $parent = false;
                $all_empty = true;
                foreach($ids as $id){
                    if(strlen($product[$id])<2){
                        continue;
                    }
                    $product[$id] = trim(preg_replace('#\s+(?:'.implode('|', array('DOCG','DOP','АОС','IGT','IGP','DOC','DO','AOP','WO','AOC','АОР','АОP','ДОК','ИГП','VR')).')$#iu', '', trim(str_ireplace(array(' DOC ',' DOCG '), ' ', $product[$id]))));
                    $all_empty = false;
                    $parent = $this->XM->product->find_attrval($pa_id, $parent_id, $product[$id], $err);
                    if($parent){
                        break;
                    }
                }
                if($all_empty){
                    break;
                }
                if($parent===FALSE){
                    $parent_id = false;
                    break;
                }
                $parent_id = $parent;
            }
            $newattr = $parent_id;

            if($newattr===false){
                $location_strings = array();
                foreach($appellation_ids as $ids){
                    foreach($ids as $id){
                        $value = trim($product[$id]);
                        if(empty($value)){
                            continue;
                        }
                        $location_strings[] = $value;
                    }
                    
                }
                $critical_error = true;
                $errorlist[] = 'Неизвестный location: '.implode(' | ', array_unique($location_strings));
                $unknown_appelation = array();
                foreach(array(3,2,5,4,7,6) as $id){
                    $unknown_appelation[] = trim(str_ireplace(array('Premier Cru','Премьер Крю','1-er Cru','1-ер Крю'),array('1er Cru','1ер Крю','1er Cru','1ер Крю'),preg_replace('#(?:'.implode('|', array('DOCG','DOP','АОС','IGT','IGP','DOC','DO','AOP','WO','AOC','АОР','АОP','ДОК','ИГП','VR')).')$#iu', '', trim(str_ireplace(array(' DOC ',' DOCG '), ' ', $product[$id])))));
                }
            }
            $attr[] = $newattr;
            //producer
            $newattr = false;
            if(!empty($producer_ids)){
                foreach($producer_ids as $id){
                    $newattr = $this->__get_attr_val_id(46,$product[$id]);
                    if($newattr!==false){
                        break;
                    }
                    $error_values[] = $product[$id];
                }
                if($newattr===false){
                    if($producer_origin_id!==null && $producer_rus_id!==null && $product[$producer_origin_id] && $product[$producer_rus_id]){
                        if(!$critical_error){
                            if(!$newattr = $this->XM->product->add_attrval(46, null, $product[$producer_origin_id], array('1'=>$product[$producer_origin_id],'2'=>$product[$producer_rus_id]), 0, $err)){
                                $errorlist[] = 'Ошибка добавления производителя: '.$err;
                                $critical_error = true;
                            }
                        }
                        
                    } else {
                        $errorlist[] = 'Неизвестный производитель: '.implode(' | ', array_unique($error_values));
                        $critical_error = true;
                    }
                }
                $attr[] = $newattr;
            }
            //color
            foreach($color_ids as $id){
                if(!strlen(trim($product[$id]))){
                    continue;
                }
                $newattr = $this->__get_attr_val_id(23,$product[$id]);
                if($newattr===false){
                    $errorlist[] = 'Неизвестный цвет: '.trim($product[$id]);
                    continue;
                }
                break;
            }
            if(!$newattr){
                $critical_error = true;
            }
            $attr[] = $newattr;

            //sweetness
            foreach($sweetness_ids as $id){
                if(!strlen(trim($product[$id]))){
                    continue;
                }
                $newattr = $this->__get_attr_val_id(44,$product[$id]);
                if($newattr===false){
                    $errorlist[] = 'Неизвестное содержание сахара: '.trim($product[$id]);
                    continue;
                }
                $attr[] = $newattr;
                break;
            }

            
            //grape
            $is_coupage = in_array(mb_strtolower($product[$coupage_id],'UTF-8'),array('купаж','ассамбляж'));
            $grape_concentrations = array();
            foreach($grape_ids as $id){
                if(!strlen(trim($product[$id]))){
                    continue;
                }
                if(preg_match_all('#[^\r\n;,]+#iu',$product[$id],$grapes)){
                    foreach($grapes[0] as $grape){
                        $concentration = '';
                        if(preg_match('#^(.+?)\s*-?\s*(\d+)%#', $grape,$match)){
                            $grape = $match[1];
                            $concentration = (int)$match[2];
                        }
                        $newattr = $this->__get_attr_val_id(38,$grape);
                        if($newattr===false){
                            $errorlist[] = 'Неизвестный сорт винограда: '.trim($grape);
                            $critical_error = true;
                            continue;
                        }
                        $attr[] = $newattr;
                        $grape_concentrations[$newattr] = $concentration;
                    }
                }
                break;
            }

            //certificate
            foreach($cert_ids as $id){
                if(!strlen(trim($product[$id])) || mb_strtolower($product[$id],'UTF-8')=='нет' || mb_strtolower($product[$id],'UTF-8')=='есть'){
                    continue;
                }
                $newattr = $this->__get_attr_val_id(47,$product[$id]);
                if($newattr===false){
                    $errorlist[] = 'Неизвестный сертификат: '.trim($product[$id]);
                    continue;
                }
                $attr[] = $newattr;
                break;
            }
            
            //oak
            foreach($oak_matured_ids as $id){
                if(!strlen(trim($product[$id]))){
                    continue;
                }
                $newattr = $this->__get_attr_val_id(52,$product[$id]);
                if($newattr===false){
                    $errorlist[] = 'Неизвестная выдержка в дубе: '.trim($product[$id]);
                    continue;
                }
                $attr[] = $newattr;
                break;
            }
            
            //alcohol content
            $alcohol_content = null;
            if($alcohol_content_id!==null){
                $alcohol_content_string = str_replace(',', '.', $product[$alcohol_content_id]);
                $alcohol_content = '';
                if(preg_match('#(\d{1,2}(?:\.\d{1,2})?)%?\s*-\s*(\d{1,2}(?:\.\d{1,2})?)#', $alcohol_content_string, $match)){
                    $alcohol_content = (((float)$match[1])+((float)$match[2]))/2.0;
                    
                } elseif(((float)$alcohol_content_string)<1) {
                    $alcohol_content = ((float)$alcohol_content_string)*100;
                } else {
                    $alcohol_content = (float)$alcohol_content_string;
                }
            }
            
            //name
            $name_origin = trim($product[$name_origin_id]);
            $name_rus = trim($product[$name_rus_id]);
            $name_eng = $name_origin;
            $product_id = null;
            if(!$critical_error){
                $err = null;
                /* */
                if(!($product_id = $this->XM->product->add_product($attr, $is_vintage, null, $alcohol_content, $name_origin, array(1=>$name_eng,2=>$name_rus), array(), $is_coupage, $grape_concentrations, $err))){
                    $errorlist[] = 'Ошибка добавления товара: '.$err;
                    
                    $critical_error = true;
                }
            }
            echo '<tr class="'.($product_id?'success':'failure').'"><td>'.htmlentities($article).'</td><td>'.($product_id?'<a href="'.BASE_URL.'/vintage/'.$this->XM->product->get_blank_vintage_id($product_id).'" target="__blank">'.$product_id.'</a>':'').'</td><td>'.implode('<br />',$errorlist).'</td></tr>';
        }
        echo '</tbody></table>';
        $csv_iter->close();
        return true;
    }
    public function refresh_product_fullnames(){
        // $this->XM->product->__refresh_product_fullnames();
        return true;
    }
    public function refresh_search_engine(){
        // $this->XM->product->__refresh_search_engine();
        return true;
    }
    public function vacate_pvrs(){
        $vacate_pvr_ids = array(981,1515,1489);
        // vacates pvr_ids in close proximity
        // $min_vacate_pvr_id = null;
        // foreach($vacate_pvr_ids as $vacate_pvr_id){
        //     if($min_vacate_pvr_id===null || $vacate_pvr_id < $min_vacate_pvr_id){
        //         $min_vacate_pvr_id = $vacate_pvr_id;
        //     }
        // }
        // $res = $this->XM->sqlcore->query('SELECT pvr_id from product_vintage_review where pvr_id >= '.$min_vacate_pvr_id.' order by pvr_id desc');
        // $pvr_ids = array();
        // while($row = $this->XM->sqlcore->getRow($res)){
        //     $pvr_ids[] = (int)$row['pvr_id'];
        // }
        // $this->XM->sqlcore->freeResult($res);
        // foreach($pvr_ids as $pvr_id){
        //     $increment = 0;
        //     foreach($vacate_pvr_ids as $vacate_pvr_id){
        //         if($vacate_pvr_id <= $pvr_id){
        //             $increment++;
        //         }
        //     }
        //     if(!$increment){
        //         continue;
        //     }
        //     $this->XM->sqlcore->query('update product_vintage_review set pvr_id = '.($pvr_id+$increment).' where pvr_id = '.$pvr_id);
        //     $this->XM->sqlcore->query('update product_vintage_review_custom_param set pvr_id = '.($pvr_id+$increment).' where pvr_id = '.$pvr_id);
        //     $this->XM->sqlcore->query('update product_vintage_review_ml set pvr_id = '.($pvr_id+$increment).' where pvr_id = '.$pvr_id);
        //     $this->XM->sqlcore->query('update product_vintage_review_param set pvr_id = '.($pvr_id+$increment).' where pvr_id = '.$pvr_id);
        // }
        // $res = $this->XM->sqlcore->commit();
    }
    public function refresh_scores(){
        // $this->XM->product->__refresh_vintage_scores_for_tasting(65);
        // $this->XM->product->__refresh_vintage_scores_for_tasting(66);
        // $this->XM->product->__refresh_vintage_scores_for_tasting(67);
        // $this->XM->product->__refresh_vintage_scores_for_tasting(68);
        // $this->XM->product->__refresh_vintage_scores_for_tasting(69);
        // $this->XM->product->__refresh_vintage_scores_for_tasting(70);
        // $this->XM->product->__refresh_vintage_scores_for_tasting(71);
        // $this->XM->product->__refresh_vintage_scores_for_tasting(72);
        // $this->XM->product->__refresh_vintage_score(7911);
        // $this->XM->product->__refresh_vintage_scores_for_tasting(116);
        $this->XM->product->refresh_all_vintage_scores();
        return true;
        
    }

    
}
