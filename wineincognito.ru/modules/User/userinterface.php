<?php
namespace User;
if(!defined('IS_XMODULE')){
    exit();
}
require_once ABS_PATH.'/interface/userinterface.php';

class UserInterface extends \AbstractNS\UserInterface{

    public function index($relative_path){
        return false;
    }
    public function login(){
        if($this->XM->user->isLoggedIn() && isset($_POST) && isset($_POST['action']) && $_POST['action']=='user_login'){
            if(!$this->XM->user->getFullName()){//redirect to profile edit every user with blank credentials
                $this->XM->setPushStateUrl(BASE_URL.'/profile/edit');
                return $this->edit_self();
            }
            $this->XM->setPushStateUrl(BASE_URL);
            return $this->XM->__UI->main->index();
        }
        $this->XM->__wrapview($this->XM->view->load('user/login',null,true), 
            null, array('css'=>array('/modules/User/css/login.css')));
        return true;
    }
    public function directauth($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        if(!preg_match('#^(\d+)([a-f0-9]{32})$#', $relative_path[0], $match)){
            $this->XM->addMessage(langTranslate('User', 'err', 'Invalid direct auth code', 'Invalid direct auth code'), 0);
            $this->XM->__wrapview(null, null, null);
        }
        $user_id = $match[1];
        $code = $match[2];
        $err = null;
        if(!$this->XM->user->direct_auth($user_id,$code,$err)){
            $this->XM->addMessage($err, 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        if(!$this->XM->user->getFullName()){//redirect to profile edit every user with blank credentials
            $this->XM->setPushStateUrl(BASE_URL.'/profile/edit');
            return $this->edit_self();
        }
        $this->XM->setPushStateUrl(BASE_URL);
        return $this->XM->__UI->main->index();


        return true;
    }
    public function passwordrecovery(){
        if(isset($_POST) && isset($_POST['action']) && $_POST['action']=='user_password_recover'){
            $err = null;
            if($this->XM->user->password_reset_request($_POST['login'], $err)){
                $this->XM->addMessage(langTranslate('User', 'passwordRecoveryForm', 'Please check your inbox for further instructions', 'Please check your inbox for further instructions'), 2);
                $this->XM->__wrapview(null, null, null);
                return true;
            } else {
                $this->XM->addMessage($err, 0);
            }
        }
        $this->XM->__wrapview($this->XM->view->load('user/passwordrecovery',null,true), 
            null, array('css'=>array('/modules/User/css/passwordrecovery.css')));
        return true;
    }
    public function password_reset($relative_path){
        if(count($relative_path)<1){
            return false;
        }
        $code = (string)$relative_path[0];
        $err = null;
        if(($user_id = $this->XM->user->password_reset_get_user_id($code,$err))===FALSE){
            $this->XM->addMessage($err, 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='change_password')&&isset($_POST['newpass'])){
            $err = null;
            if(!$this->XM->user->change_password($user_id, null, $_POST['newpass'], true, $err)){
                $this->XM->addMessage($err, 0);
            } else {
                $this->XM->user->password_reset_delete_code($code);
                $this->XM->addMessage(langTranslate('User', 'editUserForm', 'User password have been changed', 'User password have been changed'), 2);
                $this->XM->setPushStateUrl(BASE_URL.'/login');
                return $this->login();
            }
        }
        $this->XM->__wrapview($this->XM->view->load('user/changepassword',array('require_old_password'=>false,'change_login'=>false),true), 
            null, array('css'=>array('/modules/User/css/changepassword.css'),'js'=>array('/modules/User/js/changepassword.js')));
        return true;
    }

    public function register_user(){
        if($this->XM->user->isLoggedIn()){
            $this->XM->addMessage(langTranslate('User', 'err', 'You\'re already registered', 'You\'re already registered'), 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        if(isset($_POST) && isset($_POST['action']) && $_POST['action']=='user_register'){
			if(isset($_POST['consent'])&&$_POST['consent']==1){
				$err = null;
				$login = isset($_POST['login'])?$_POST['login']:null;
				$pass = isset($_POST['pass'])?$_POST['pass']:null;
				if($this->XM->user->register_user($login, $pass, $err)){
					$this->XM->addMessage(langTranslate('User', 'registerUserForm', 'User have been registered', 'User have been registered'), 2);
					if($this->XM->user->login($_POST['login'], $_POST['pass'], $err)){
						$this->XM->setPushStateUrl(BASE_URL.'/profile/edit');
						return $this->edit_self();
					}
				} else {
					$this->XM->addMessage($err, 0);
				}
			} else {
				$this->XM->addMessage(langTranslate('User', 'err', 'To register in the system, you must confirm your consent to the processing of personal data', 'To register in the system, you must confirm your consent to the processing of personal data'), 0);
			}
			
        }
        $this->XM->__wrapview($this->XM->view->load('user/registerUser',null,true), 
            null, array('css'=>array('/modules/User/css/registeruser.css'),'js'=>array('/modules/User/js/registeruser.js')));
        return true;
    }
    public function view_self(){
        if(!$this->XM->user->isLoggedIn()){
            return false;
        }
        return $this->view(array($this->XM->user->getUserId()));
    }
    public function view($relative_path){
        if(count($relative_path)<1){
            return false;
        }
        $user_id = (int)$relative_path[0];
        if(($userinfo = $this->XM->user->get_user_info($user_id))===false){
            return false;
        }
        $this->XM->__wrapview($this->XM->view->load('user/viewUser',array('userinfo'=>$userinfo,'expert_level_list'=>$this->XM->user->get_expert_level_list(),'can_approve_expert'=>$this->XM->user->check_privilege(\USER\PRIVILEGE_USER_APPROVE_EXPERT)),true), 
            null, array('css'=>array('/modules/User/css/viewuser.css'),'js'=>array('/modules/User/js/viewuser.js'),'pack'=>array('dropbox','datepicker')));
        return true;
    }
    public function expertrequests_user($relative_path){
        if(count($relative_path)<1){
            return false;
        }
        $user_id = (int)$relative_path[0];
        $can_approve_expert = $this->XM->user->check_privilege(\USER\PRIVILEGE_USER_APPROVE_EXPERT);
        if(!$can_approve_expert){
            $this->XM->addMessage(langTranslate('User', 'err', 'Access Denied', 'Access Denied'), 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        if(($userinfo = $this->XM->user->get_user_info($user_id))===false){
            $this->XM->addMessage(langTranslate('User', 'err', 'Access Denied', 'Access Denied'), 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $err = null;
        if(($expert_requests = $this->XM->user->get_expert_requests($user_id,$err))===false){
            $this->XM->addMessage($err, 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $expert_level_list = $this->XM->user->get_expert_level_list();
        $expert_requests = $this->XM->user->get_expert_requests($user_id,$err);
        $this->XM->__wrapview($this->XM->view->load('user/viewUser',array('userinfo'=>$userinfo,'expert_level_list'=>$expert_level_list,'can_approve_expert'=>$can_approve_expert,'compact'=>true),true).
            $this->XM->view->load('user/expertrequests',array('requests'=>$expert_requests,'user_id'=>$user_id,'current_expert_level'=>$userinfo['expert_level'],'expert_level_list'=>$expert_level_list),true), 
            null, array('css'=>array('/modules/User/css/viewuser.css','/modules/User/css/expertrequests.css'),'js'=>array('/modules/User/js/viewuser.js','/modules/User/js/expertrequests.js'),'pack'=>array('dropbox','datepicker')));
        return true;
    }
    

    public function edit_self(){
        if(!$this->XM->user->isLoggedIn()){
            return false;
        }
        return $this->edit(array($this->XM->user->getUserId()));
    }
    public function edit($relative_path){
        if(count($relative_path)<1){
            return false;
        }
        $user_id = (int)$relative_path[0];
        if(!$this->XM->user->can_edit_user($user_id)){
            $this->XM->addMessage(langTranslate('user', 'err', 'Access Denied',  'Access Denied'), 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $languageList = $this->XM->lang->getLanguageList();
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_user')){
            $phone = isset($_POST['phone'])?$_POST['phone']:'';
            $lastname = $firstname = $patronymic = array();
            $default_interface_lang = isset($_POST['default_interface_lang'])?$_POST['default_interface_lang']:null;
            $attributes = isset($_POST['attr'])?$_POST['attr']:array();
            $employment = isset($_POST['employment'])?$_POST['employment']:0;
            foreach($languageList as $language){
                $language_id = $language['id'];
                $lastname[$language_id] = (isset($_POST['lastname'])&&is_array($_POST['lastname'])&&isset($_POST['lastname'][$language_id]))?$_POST['lastname'][$language_id]:'';
                $firstname[$language_id] = (isset($_POST['firstname'])&&is_array($_POST['firstname'])&&isset($_POST['firstname'][$language_id]))?$_POST['firstname'][$language_id]:'';
                $patronymic[$language_id] = (isset($_POST['patronymic'])&&is_array($_POST['patronymic'])&&isset($_POST['patronymic'][$language_id]))?$_POST['patronymic'][$language_id]:'';
                $placeofwork[$language_id] = (isset($_POST['placeofwork'])&&is_array($_POST['placeofwork'])&&isset($_POST['placeofwork'][$language_id]))?$_POST['placeofwork'][$language_id]:'';
            }
            $err = null;
            if(!$this->XM->user->edit_user($user_id, $phone, $default_interface_lang, $attributes, $employment, $lastname, $firstname, $patronymic, $placeofwork, $err)){
                $this->XM->addMessage($err, 0);
            } else {
                $this->XM->addMessage(langTranslate('User', 'editUserForm', 'User profile have been edited', 'User profile have been edited'), 2);
                $this->XM->setPushStateUrl(BASE_URL.'/user/'.$user_id);
                return $this->view(array($user_id));
                // redirect('/user/'.$user_id);
            }
        }
        $userinfo = $this->XM->user->get_user_info_for_all_languages($user_id);
        if(!$userinfo){
            return false;
        }
        if(($attrvaltree = $this->XM->product->get_system_attrval_tree(17,array($userinfo['background']),$err))===FALSE){
            $this->XM->addMessage($err, 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $this->XM->__wrapview($this->XM->view->load('user/editUser',array('languageList'=>$languageList,'userinfo'=>$userinfo,'attrvaltree'=>$attrvaltree),true), 
            null, array('css'=>array('/modules/User/css/edituser.css'),'js'=>array('/modules/User/js/edituser.js'),'pack'=>array('dropbox')));
        return true;
    }
    public function change_password_self(){
        if(!$this->XM->user->isLoggedIn()){
            return false;
        }
        return $this->change_password(array($this->XM->user->getUserId()));
    }
    public function change_password($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $user_id = (int)$relative_path[0];
        if(!$this->XM->user->can_change_password($user_id)){
            $this->XM->addMessage(langTranslate('user', 'err', 'Access Denied',  'Access Denied'), 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $change_password_privilege = $this->XM->user->check_privilege(\USER\PRIVILEGE_CHANGE_PASSWORD);
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='change_password')&&isset($_POST['newpass'])){//isset($_POST['oldpass'])
            $err = null;
            if(!$change_password_privilege&&!$this->XM->user->check_password_for_user_id($user_id, isset($_POST['oldpass'])?$_POST['oldpass']:'', $err)){
                $this->XM->addMessage($err, 0);
            } else {
                $err = null;
                if(!$this->XM->user->change_password($user_id, isset($_POST['newlogin'])?$_POST['newlogin']:null, $_POST['newpass'], false, $err)){
                    $this->XM->addMessage($err, 0);
                } else {
                    $this->XM->addMessage(langTranslate('User', 'editUserForm', 'User password have been changed', 'User password have been changed'), 2);
                    $this->XM->setPushStateUrl(BASE_URL.'/user/'.$user_id);
                    return $this->view(array($user_id));
                }
            }
            
        }
        $change_login = true;
        $old_login = null;
        if($change_login){
            $old_login = $this->XM->user->get_user_login($user_id);
        }
        $this->XM->__wrapview($this->XM->view->load('user/changepassword',array('require_old_password'=>!$change_password_privilege,'change_login'=>$change_login,'login'=>$old_login),true), 
            null, array('css'=>array('/modules/User/css/changepassword.css'),'js'=>array('/modules/User/js/changepassword.js')));
        return true;
    }
    public function user_settings_self(){
        if(!$this->XM->user->isLoggedIn()){
            return false;
        }
        return $this->user_settings(array($this->XM->user->getUserId()));
    }
    public function user_settings($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $user_id = (int)$relative_path[0];
        if(!$this->XM->user->can_access_user_settings($user_id)){
            $this->XM->addMessage(langTranslate('user', 'err', 'Access Denied',  'Access Denied'), 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }

        $this->XM->__wrapview($this->XM->view->load('user/usersettings',array('user_id'=>$user_id,'direct_auth_enabled'=>$this->XM->user->is_direct_auth_enabled($user_id)),true), 
            null, array('css'=>array('/modules/User/css/usersettings.css'),'js'=>array('/modules/User/js/usersettings.js')));
        return true;
    }
    
    public function userfilter($relative_path = array()){
        if(($attrvaltree = $this->XM->product->get_system_attrval_tree(17,null,$err))===FALSE){
            $this->XM->addMessage($err, 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $showfavourite = false;
        if($this->XM->user->isLoggedIn()){
            $showfavourite = true;
        }
        $showmycompany = false;
        if($this->XM->user->isInCompany()){
            $showmycompany = true;
        }
        $approve_experts = false;
        $global_expert_scores = false;
        if(isset($relative_path[0])){
            if($relative_path[0]=='approve_experts'){
                if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_USER_APPROVE_EXPERT)){
                    $this->XM->addMessage(langTranslate('User', 'err', 'Access Denied', 'Access Denied'), 0);
                    $this->XM->__wrapview(null, null, null);
                    return true;
                }
                $approve_experts = true;
            }
            if($relative_path[0]=='global_expert_scores'){
                if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_EXPERT_EVALUATION_SCORE)){
                    $this->XM->addMessage(langTranslate('User', 'err', 'Access Denied', 'Access Denied'), 0);
                    $this->XM->__wrapview(null, null, null);
                    return true;
                }
                $global_expert_scores = true;
            }
        }
        $expert_level_list = $this->XM->user->get_expert_level_list();
        $this->XM->__wrapview($this->XM->view->load('user/userfilter',array('attrvaltree'=>$attrvaltree,'expert_level_list'=>$expert_level_list,'showfavourite'=>$showfavourite,'showmycompany'=>$showmycompany,'showglobalexpertscores'=>$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_EXPERT_EVALUATION_SCORE),'approve_experts'=>$approve_experts,'global_expert_scores'=>$global_expert_scores,'actions'=>true),true), 
            null, array('css'=>array('/modules/User/css/userfilter.css'),'js'=>array('/modules/User/js/userfilter.js'),'pack'=>array('dropbox','filterform')));
        return true;
    }
    public function logout(){
        $this->XM->user->logout();
        return true;
    }

    //companies
    public function register_company(){
        if($this->XM->user->isInCompany()){
            $this->XM->addMessage(langTranslate('user', 'err', 'You\'re already in a company',  'You\'re already in a company'), 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $languageList = $this->XM->lang->getLanguageList();
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_company')){
            $itn = isset($_POST['itn'])?$_POST['itn']:'';
            $name = array();
            foreach($languageList as $language){
                $language_id = $language['id'];
                $name[$language_id] = (isset($_POST['name'])&&is_array($_POST['name'])&&isset($_POST['name'][$language_id]))?$_POST['name'][$language_id]:'';
            }
            $err = null;
            if(!$this->XM->user->register_company($itn, $err)){
                $this->XM->addMessage($err, 0);
            } else {
                $this->XM->user->edit_company($this->XM->user->getCompanyId(), $itn, $name, $err);
                $this->XM->setPushStateUrl(BASE_URL.'/mycompany');
                return $this->view_company_self();
                // redirect('/mycompany');
            }
        }
        $this->XM->__wrapview($this->XM->view->load('user/editCompany',array('languageList'=>$languageList,'companyinfo'=>array()),true), 
            null, array('css'=>array('/modules/User/css/editcompany.css'),'js'=>array('/modules/User/js/editcompany.js')));
        return true;
    }
    public function view_company_self($relative_path = array()){
        if(!$this->XM->user->isInCompany()){
            return false;
        }
        return $this->view_company(array($this->XM->user->getCompanyId()));
    }
    public function view_company($relative_path){
        if(count($relative_path)<1){
            return false;
        }
        $company_id = (int)$relative_path[0];
        $companyinfo = $this->XM->user->get_company_info($company_id);
        if(!$companyinfo){
            return false;
        }
        $this->XM->__wrapview($this->XM->view->load('user/viewCompany',array('companyinfo'=>$companyinfo),true), 
            null, array('css'=>array('/modules/User/css/viewcompany.css'),'js'=>array('/modules/User/js/viewcompany.js')));
        return true;
    }
    public function edit_company_self($relative_path){
        if(!$this->XM->user->isInCompany()){
            return false;
        }
        return $this->edit_company(array($this->XM->user->getCompanyId()));
    }
    public function edit_company($relative_path){
        if(count($relative_path)<1){
            return false;
        }
        $company_id = (int)$relative_path[0];
        if(!$this->XM->user->can_edit_company($company_id)){
            $this->XM->addMessage(langTranslate('user', 'err', 'Access Denied',  'Access Denied'), 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $languageList = $this->XM->lang->getLanguageList();
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_company')){
            $itn = isset($_POST['itn'])?$_POST['itn']:'';
            $name = array();
            foreach($languageList as $language){
                $language_id = $language['id'];
                $name[$language_id] = (isset($_POST['name'])&&is_array($_POST['name'])&&isset($_POST['name'][$language_id]))?$_POST['name'][$language_id]:'';
            }
            $err = null;
            if(!$this->XM->user->edit_company($company_id, $itn, $name, $err)){
                $this->XM->addMessage($err, 0);
            } else {
                $this->XM->addMessage(langTranslate('User', 'editCompanyForm', 'Company info have been edited', 'Company info have been edited'), 2);
                $this->XM->setPushStateUrl(BASE_URL.'/company/'.$company_id);
                return $this->view_company(array($company_id));
                // redirect('/company/'.$company_id);
            }
        }
        $companyinfo = $this->XM->user->get_company_info_for_all_languages($company_id);
        if(!$companyinfo){
            return false;
        }
        $this->XM->__wrapview($this->XM->view->load('user/editCompany',array('languageList'=>$languageList,'companyinfo'=>$companyinfo),true), 
            null, array('css'=>array('/modules/User/css/editcompany.css'),'js'=>array('/modules/User/js/editcompany.js')));
        return true;
    }
    public function companylist($relative_path = array()){
        $companylist = $this->XM->user->get_company_list(false);
        if($companylist===false){
            return false;
        }
        $this->XM->__wrapview($this->XM->view->load('user/companylist',array('companylist'=>$companylist,'can_set_api_access'=>$this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_COMPANY)),true), 
            null, array('css'=>array('/modules/User/css/companylist.css'),'js'=>array('/modules/User/js/companylist.js')));
        return true;
    }
    public function company_user_list_self($relative_path = array()){
        if(!$this->XM->user->isInCompany()){
            return false;
        }
        return $this->company_user_list(array($this->XM->user->getCompanyId()));
    }
    public function company_user_list($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $company_id = (int)$relative_path[0];

        if(($attrvaltree = $this->XM->product->get_system_attrval_tree(17,null,$err))===FALSE){
            $this->XM->addMessage($err, 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $showfavourite = false;
        if($this->XM->user->isLoggedIn()){
            $showfavourite = true;
        }
        $expert_level_list = $this->XM->user->get_expert_level_list();
        $this->XM->__wrapview($this->XM->view->load('user/userfilter',array('attrvaltree'=>$attrvaltree,'expert_level_list'=>$expert_level_list,'company_id'=>$company_id,'showfavourite'=>$showfavourite,'showglobalexpertscores'=>$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_EXPERT_EVALUATION_SCORE),'actions'=>true),true), 
            null, array('css'=>array('/modules/User/css/userfilter.css'),'js'=>array('/modules/User/js/userfilter.js'),'pack'=>array('dropbox','filterform')));
        return true;
    }
    public function company_settings_self($relative_path = array()){
        if(!$this->XM->user->isInCompany()){
            return false;
        }
        return $this->company_settings(array($this->XM->user->getCompanyId()));
    }
    public function company_settings($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $company_id = (int)$relative_path[0];
        if(!$this->XM->user->can_edit_company($company_id)){
            $this->XM->addMessage(langTranslate('user', 'err', 'Access Denied',  'Access Denied'), 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $company_has_api_access = $this->XM->user->company_has_api_access($company_id);
        if(isset($_POST)&&isset($_POST['action'])){
            if($_POST['action']=='auto_invite' && isset($_POST['active'])){
                $err = null;
                if(!$this->XM->user->company_enable_autoinvite((bool)$_POST['active'], $company_id, $err)){
                    $this->XM->addMessage($err, 0);
                }
            }
            
            if($_POST['action']=='mail_settings'){
                $header_logo_url = isset($_POST['header_logo_url'])?$_POST['header_logo_url']:null;
                $footer_logo_url = isset($_POST['footer_logo_url'])?$_POST['footer_logo_url']:null;
                $text_color = isset($_POST['text_color'])?$_POST['text_color']:null;
                $anchor_color = isset($_POST['anchor_color'])?$_POST['anchor_color']:null;
                $header_background_color = isset($_POST['header_background_color'])?$_POST['header_background_color']:null;
                $footer_background_color = isset($_POST['footer_background_color'])?$_POST['footer_background_color']:null;
                $err = null;
                if(!$this->XM->user->set_mail_settings($company_id, $header_logo_url, $footer_logo_url, $text_color, $anchor_color, $header_background_color, $footer_background_color, $err)){
                    $this->XM->addMessage($err, 0);
                } else {
                    unset($_POST);
                }
            }
            if($company_has_api_access && $_POST['action']=='api_access' && isset($_POST['password'])){
                $password = $_POST['password'];
                $err = null;
                if(!$this->XM->user->set_api_access_password($company_id, $password, $err)){
                    $this->XM->addMessage($err, 0);
                } else {
                    $this->XM->addMessage(langTranslate('user', 'companyApiAccessSettingsForm', 'Password set',  'Password set'), 2);
                    unset($_POST);
                }
            }
        }
        $autoinvitecode = $this->XM->user->get_company_autoinvite_code($company_id);
        $this->XM->__wrapview($this->XM->view->load('user/companysettings',array('autoinviteactive'=>(bool)$autoinvitecode,'autoinviteurl'=>$autoinvitecode?BASE_URL.'/company/ai'.urlencode($autoinvitecode).'/join':'','compact'=>(isset($_POST)&&isset($_POST['action'])&&$_POST['action']=='auto_invite')?false:true),true).
            $this->XM->view->load('user/companymailsettings',array('mailsettings'=>$this->XM->user->get_mail_settings($company_id),'defaultmailsettings'=>$this->XM->user->get_default_mail_settings(),'company_id'=>$company_id,'compact'=>(isset($_POST)&&isset($_POST['action'])&&$_POST['action']=='mail_settings')?false:true),true).
            ($company_has_api_access?$this->XM->view->load('user/companyapiaccesssettings',array('api_login'=>$this->XM->user->get_api_access_login($company_id),'compact'=>(isset($_POST)&&isset($_POST['action'])&&$_POST['action']=='api_access')?false:true),true):''),
            null, array('css'=>array('/modules/User/css/companysettings.css','/modules/User/css/companymailsettings.css'),'js'=>array('/modules/User/js/companymailsettings.js'),'pack'=>array('mask')));
        return true;
    }
    public function join_company($relative_path = array()){
        if(count($relative_path)!=2){
            return false;
        }
        if(!$this->XM->user->isLoggedIn()){
            return false;
            // redirect('/login?redir='.urlencode($_SERVER['REQUEST_URI']));
        }
        $relative_path[1] = @urldecode($relative_path[1]);

        if($this->XM->user->isInCompany()){
            $this->XM->addMessage(langTranslate('user', 'err', 'You\'re already in a company',  'You\'re already in a company'), 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $company_id = null;
        switch($relative_path[0]){
            case 'autojoin':
                $company_id = $this->XM->user->get_company_id_from_autoinvite_code($relative_path[1]);
                if(!$company_id){
                    $this->XM->addMessage(langTranslate('user', 'err', 'Invite code has expired',  'Invite code has expired'), 0);
                    $this->XM->__wrapview(null, null, null);
                    return true;
                }
                break;
            default:
                return false;
        }

        if(isset($_POST)&&isset($_POST['action'])&&$_POST['action']=='joincompany'&&isset($_POST['join'])&&$_POST['join']=='1'){
            if($this->XM->user->join_company($company_id)){
                redirect('/mycompany');
                return true;
            }
        }
        $companyinfo = $this->XM->user->get_company_info($company_id);
        if(!$companyinfo){
            $this->XM->addMessage(langTranslate('user', 'err', 'Invite code has expired',  'Invite code has expired'), 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $this->XM->__wrapview($this->XM->view->load('user/joincompany',array('companyinfo'=>$companyinfo),true), 
            null, array('css'=>array('/modules/User/css/joincompany.css')));
        return true;
    }
    // public function requestjoin_company($relative_path = array()){
    //     if(count($relative_path)<1){
    //         return false;
    //     }
    //     $company_id = (int)$relative_path[0];
        
    //     if(!$this->XM->user->isLoggedIn()){
    //         redirect('/login?redir='.urlencode($_SERVER['REQUEST_URI']));
    //     }
    //     if($this->XM->user->isInCompany()){
    //         $this->XM->addMessage(langTranslate('user', 'err', 'You\'re already in a company',  'You\'re already in a company'), 0);
    //         $this->XM->__wrapview(null, null, null);
    //         return true;
    //     }
    //     if(isset($_POST)&&isset($_POST['action'])&&$_POST['action']=='requestjoincompany'&&isset($_POST['join'])&&$_POST['join']=='1'){
    //         $err = null;
    //         if($this->XM->user->request_join_company($company_id,$err)){
    //             $this->XM->addMessage(langTranslate('user', 'joinCompany', 'Request sent',  'Request sent'), 2);
    //             $this->XM->__wrapview(null, null, null);
    //             return true;
    //         } else {
    //             $this->XM->addMessage($err, 0);
    //         }
    //     }
    //     $companyinfo = $this->XM->user->get_company_info($company_id);
    //     if(!$companyinfo){
    //         return false;
    //     }
    //     $this->XM->__wrapview($this->XM->view->load('user/requestjoincompany',array('companyinfo'=>$companyinfo),true), 
    //         null, array('css'=>array('/modules/User/css/joincompany.css')));
    //     return true;
    // }
    public function company_joinrequests_self(){
        if(!$this->XM->user->isInCompany()){
            return false;
        }
        return $this->company_joinrequests(array($this->XM->user->getCompanyId()));
    }
    public function company_joinrequests($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $company_id = (int)$relative_path[0];
        if(!$this->XM->user->can_invite_users_to_company($company_id)){
            $this->XM->addMessage(langTranslate('user', 'err', 'Access Denied',  'Access Denied'), 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        if(($attrvaltree = $this->XM->product->get_system_attrval_tree(17,null,$err))===FALSE){
            $this->XM->addMessage($err, 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $showfavourite = false;
        if($this->XM->user->isLoggedIn()){
            $showfavourite = true;
        }
        $showmycompany = false;
        if($this->XM->user->isInCompany()){
            $showmycompany = true;
        }
        $expert_level_list = $this->XM->user->get_expert_level_list();
        $this->XM->__wrapview($this->XM->view->load('user/userfilter',array('attrvaltree'=>$attrvaltree,'expert_level_list'=>$expert_level_list,'joinrequests_company_id'=>$company_id,'showfavourite'=>$showfavourite,'showmycompany'=>$showmycompany,'showglobalexpertscores'=>$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_EXPERT_EVALUATION_SCORE),'actions'=>true),true), 
            null, array('css'=>array('/modules/User/css/userfilter.css'),'js'=>array('/modules/User/js/userfilter.js'),'pack'=>array('dropbox','filterform')));
        return true;
    }
    // ajax
    public function ajax_resolvejoinrequest($relative_path = array()){
        if(count($relative_path)!=2 || !isset($_POST) || !isset($_POST['approve'])){
            return false;
        }
        $company_id = (int)$relative_path[0];
        $user_id = (int)$relative_path[1];
        $approve = (bool)$_POST['approve'];
        $err = null;
        if(!$this->XM->user->resolve_join_request($company_id, $user_id, $approve, $err)){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $successmsg = '';
        if($approve){
            $successmsg = langTranslate('user', 'resolvejoinrequest', 'User joining has been successfully approved',  'User joining has been successfully approved');
        } else {
            $successmsg = langTranslate('user', 'resolvejoinrequest', 'User joining has been successfully denied',  'User joining has been successfully denied');
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>$successmsg)));
        return true;
    }
    public function ajax_dismissuser($relative_path = array()){
        if(count($relative_path)!=2){
            return false;
        }
        $company_id = (int)$relative_path[0];
        $user_id = (int)$relative_path[1];
        $err = null;
        if(!$this->XM->user->dismiss_user($company_id, $user_id, $err)){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>langTranslate('user', 'user', 'User has been successfully dismissed',  'User has been successfully dismissed'))));
        return true;
    }
    public function ajax_requestjoin_company($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $company_id = (int)$relative_path[0];
        $err = null;
        if(!$this->XM->user->request_join_company($company_id,$err)){
           $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>langTranslate('user', 'requestjoin', 'Request sent',  'Request sent'))));
        return true;
    }
    public function ajax_delete_company($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $company_id = (int)$relative_path[0];
        $err = null;
        if(!$this->XM->user->delete_company($company_id,$err)){
           $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>langTranslate('user', 'deletecompany', 'Company has been successfully deleted',  'Company has been successfully deleted'))));
        return true;
    }
    public function ajax_approve_company($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $company_id = (int)$relative_path[0];
        $err = null;
        if(!$this->XM->user->approve_company($company_id,$err)){
           $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>langTranslate('user', 'approvecompany', 'Company has been successfully approved',  'Company has been successfully approved'))));
        return true;
    }
    public function ajax_requestexpert(){
        $request_comment = (string)$_POST['comment'];
        $err = null;
        if(!$this->XM->user->user_expert_request($request_comment,$err)){
           $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>langTranslate('user', 'request expert change', 'Expert change has been successfully requested', 'Expert change has been successfully requested'))));
        return true;
    }
    public function ajax_resolve_expertrequest($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $uer_id = (int)$relative_path[0];
        $approve = (isset($_POST['approve'])&&$_POST['approve'])?true:false;
        $expert_level = (isset($_POST['expert_level'])&&$_POST['expert_level'])?$_POST['expert_level']:null;
        $err = null;
        if(!$this->XM->user->resolve_user_expert_request($uer_id,$approve,$expert_level,$err)){
           $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_setexpertlevel_user($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $user_id = (int)$relative_path[0];
        $expert_level = (int)$_POST['expertLevel'];
        $convert_from_date = isset($_POST['date'])?$_POST['date']:date('d.m.Y',time()+86400);
        $err = null;
        if(!$this->XM->user->user_set_expert_level($user_id,$expert_level,$convert_from_date,$err)){
           $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    
    public function ajax_changeaddright_user($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $user_id = (int)$relative_path[0];
        $enable = $_POST['enable']?true:false;
        $right = (string)$_POST['right'];
        $err = null;
        if(!$this->XM->user->user_change_add_right($user_id,$right,$enable,$err)){
           $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_setdirectauth_user($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $user_id = (int)$relative_path[0];
        $enable = $_POST['enable']?true:false;
        $err = null;
        $code = null;
        if(!$this->XM->user->user_set_direct_auth($user_id,$enable,$code,$err)){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $result = array('success'=>1);
        if($code){
            $result['data'] = array('url'=>BASE_URL.'/login/'.$user_id.$code);
        }
        $this->XM->view->load('view/json',array('data'=>$result));
        return true;
    }
    
    public function ajax_search_user(){
        if(!isset($_POST) || !isset($_POST['action']) || $_POST['action']!='user_filter'){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>langTranslate('product', 'err', 'Internal Error',  'Internal Error'))));
            return true;
        }
        $attr = isset($_POST['attr'])?$_POST['attr']:array();
        $expert_level = (isset($_POST['expert_level'])&&is_array($_POST['expert_level']))?$_POST['expert_level']:array();
        $show_expert_level = isset($_POST['show_expert_level'])&&$_POST['show_expert_level']?true:false;
        $only_my_company = isset($_POST['only_my_company'])&&$_POST['only_my_company']?true:false;
        $only_favourite = isset($_POST['only_favourite'])&&$_POST['only_favourite']?true:false;
        $only_online = isset($_POST['only_online'])&&$_POST['only_online']?true:false;
        $only_expert = isset($_POST['only_expert'])&&$_POST['only_expert']?true:false;
        $only_global_expert_scores = isset($_POST['only_global_expert_scores'])&&$_POST['only_global_expert_scores']?true:false;
        $company_id = isset($_POST['company_id'])?(int)$_POST['company_id']:null;
        $only_participants_of_contest_id = isset($_POST['only_participants_of_contest_id'])?(int)$_POST['only_participants_of_contest_id']:null;
        $only_participants_of_contest_product_id = isset($_POST['only_participants_of_contest_product_id'])?(int)$_POST['only_participants_of_contest_product_id']:null;
        $hide_company_name = isset($_POST['hide_company_name'])&&$_POST['hide_company_name']?true:false;
        $joinrequests_company_id = isset($_POST['joinrequests_company_id'])?(int)$_POST['joinrequests_company_id']:null;
        $approve_expert_list = isset($_POST['approve_expert_list'])&&$_POST['approve_expert_list']?true:false;
        $search_string = isset($_POST['search_text'])?(string)$_POST['search_text']:'';
        $page = isset($_POST['page'])?(int)$_POST['page']:1;
        $pagelimit = isset($_POST['pagelimit'])?(int)$_POST['pagelimit']:50;

        $order_by_field = isset($_POST['orderbyfield'])?$_POST['orderbyfield']:null;
        $order_by_direction_asc = isset($_POST['orderbydirection'])&&$_POST['orderbydirection']?true:false;
        
        if($only_my_company){
            $company_id = $this->XM->user->getCompanyId();
        }
        $err = null;
        $count = 0;
        if(($list = $this->XM->user->filter_user($search_string, $attr, $expert_level, $show_expert_level, $company_id, $only_favourite, $only_online, $only_expert, $only_global_expert_scores, $only_participants_of_contest_id, $only_participants_of_contest_product_id, $joinrequests_company_id, $approve_expert_list, $hide_company_name, $order_by_field, $order_by_direction_asc, $page, $pagelimit, $count, $err)) === false){
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
    public function ajax_favourite_user(){
        if(!isset($_POST) || !isset($_POST['id']) || !isset($_POST['favourite_to'])){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>langTranslate('product', 'err', 'Internal Error',  'Internal Error'))));
            return true;
        }
        $id = (int)$_POST['id'];
        $favourite_to = (bool)$_POST['favourite_to'];
        $err = null;
        if($this->XM->user->favourite_user($id,$favourite_to,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_get_user_filter_form($relative_path = array()){
        if(($attrvaltree = $this->XM->product->get_system_attrval_tree(17,null,$err))===FALSE){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $showfavourite = false;
        if($this->XM->user->isLoggedIn()){
            $showfavourite = true;
        }
        $showmycompany = false;
        if($this->XM->user->isInCompany()){
            $showmycompany = true;
        }
        $expert_level_list = $this->XM->user->get_expert_level_list();
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>$this->XM->view->load('user/userfilter',array('attrvaltree'=>$attrvaltree,'expert_level_list'=>$expert_level_list,'tastingmodal'=>true,'showfavourite'=>$showfavourite,'showmycompany'=>$showmycompany,'showglobalexpertscores'=>$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_EXPERT_EVALUATION_SCORE),'actions'=>false),true))));
        return true;
    }
    public function ajax_company_mail_settings_upload_image($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $company_id = (int)$relative_path[0];
        if(!isset($_FILES)||!isset($_FILES['image'])||!isset($_POST['type'])){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1)));
            return true;
        }
        $type = $_POST['type'];
        $err = null;
        if(($fileurl = $this->XM->user->upload_company_mail_settings_image($company_id,$type,$_FILES['image']['tmp_name'],$_FILES['image']['size'],$_FILES['image']['name'],$err)) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>array('file_url'=>$fileurl))));
        return true;
    }
    public function ajax_company_change_api_access($relative_path = array()){
        if(count($relative_path)<2){
            return false;
        }
        $company_id = (int)$relative_path[0];
        $grant = (bool)$relative_path[1];
        $err = null;
        if(!$this->XM->user->company_change_api_access($company_id,$grant,$err)){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    //moderate
    public function approve_company_list($relative_path = array()){
        $companylist = $this->XM->user->get_company_list(true);
        if($companylist===false){
            return false;
        }
        $this->XM->__wrapview($this->XM->view->load('user/companylist',array('companylist'=>$companylist),true), 
            null, array('css'=>array('/modules/User/css/companylist.css'),'js'=>array('/modules/User/js/companylist.js')));
        return true;
    }
	//private
	public function refresh_user_search_engine_entries(){
		$this->XM->user->__refresh_user_search_engine_entries();
		return true;
	}
}
