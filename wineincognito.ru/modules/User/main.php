<?php
namespace User;
if(!defined('IS_XMODULE')){
    exit();
}

DEFINE('USER\ACTIVITY_TIME_REFRESH_FREQ',600);//10min
DEFINE('USER\ACTIVITY_TIME_STILL_ONLINE',1800);//30min

DEFINE('USER\STATE_LOGGED_IN',1);
DEFINE('USER\STATE_IN_COMPANY',2);
DEFINE('USER\STATE_IN_APPROVED_COMPANY',5);
DEFINE('USER\STATE_IS_COMPANY_OWNER',3);
DEFINE('USER\STATE_IS_APPROVED_EXPERT',4);

// DEFINE('USER\DEFAULT_PRIVILEGIES',array());

DEFINE('USER\PRIVILEGE_APPROVE_TRANSLATION',0);// \USER\PRIVILEGE_APPROVE_TRANSLATION
//user
DEFINE('USER\PRIVILEGE_EDIT_COMPANIES',1);
DEFINE('USER\PRIVILEGE_EDIT_USER',2);
DEFINE('USER\PRIVILEGE_CHANGE_PASSWORD',3);
DEFINE('USER\PRIVILEGE_CHANGE_USER_SETTINGS',37);
DEFINE('USER\PRIVILEGE_INVITE_TO_COMPANIES',4);
DEFINE('USER\PRIVILEGE_DISMISS_FROM_COMPANIES',5);
DEFINE('USER\PRIVILEGE_USER_EDIT_ADD_RIGHTS',20);
DEFINE('USER\PRIVILEGE_VIEW_COMPANIES',25);
DEFINE('USER\PRIVILEGE_REGISTER_COMPANY',26);
DEFINE('USER\PRIVILEGE_APPROVE_COMPANY',6);
DEFINE('USER\PRIVILEGE_DELETE_COMPANY',7);
DEFINE('USER\PRIVILEGE_SHOW_USER_LIST',27);
DEFINE('USER\PRIVILEGE_SHOW_COMPANIES_USER_LISTS',8);
DEFINE('USER\PRIVILEGE_USER_APPROVE_EXPERT',16);
//product
DEFINE('USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES',9);
DEFINE('USER\PRIVILEGE_PRODUCT_USERFILL_ATTRIBUTES',12);
DEFINE('USER\PRIVILEGE_PRODUCT_ADD_PRODUCT',10);
DEFINE('USER\PRIVILEGE_PRODUCT_EDIT_PRODUCT',11);
DEFINE('USER\PRIVILEGE_PRODUCT_APPROVE_PRODUCT',21);
DEFINE('USER\PRIVILEGE_PRODUCT_EDIT_REVIEW',18);
DEFINE('USER\PRIVILEGE_PRODUCT_BLOCK_REVIEW',15);
DEFINE('USER\PRIVILEGE_PRODUCT_CHANGE_COMPANY_FAVOURITES',19);
DEFINE('USER\PRIVILEGE_PRODUCT_VIEW_ALL_SCORES',22);
DEFINE('USER\PRIVILEGE_PRODUCT_VIEW_SCORE_DETAILS',23);
DEFINE('USER\PRIVILEGE_PRODUCT_VIEW_ALL_REVIEWS',28);
//tasting
DEFINE('USER\PRIVILEGE_TASTING_ADD_TASTING',13);
DEFINE('USER\PRIVILEGE_TASTING_VIEW_ALL_TASTINGS',14);
DEFINE('USER\PRIVILEGE_TASTING_EDIT_ALL_TASTINGS',15);
DEFINE('USER\PRIVILEGE_TASTING_VIEW_DELETED_TASTINGS',17);
DEFINE('USER\PRIVILEGE_TASTING_VIEW_FULL_INFO_FOR_BLIND',24);
DEFINE('USER\PRIVILEGE_TASTING_APPROVE_TASTING',33);
DEFINE('USER\PRIVILEGE_TASTING_SWAP_REVIEWS',38);

DEFINE('USER\PRIVILEGE_TASTING_ADD_CONTEST',29);
DEFINE('USER\PRIVILEGE_TASTING_VIEW_ALL_CONTESTS',30);
DEFINE('USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS',31);
DEFINE('USER\PRIVILEGE_TASTING_APPROVE_CONTEST',32);

DEFINE('USER\PRIVILEGE_TASTING_EDIT_EXPERT_EVALUATION_TEMPLATE',35);
DEFINE('USER\PRIVILEGE_TASTING_VIEW_EXPERT_EVALUATION_SCORE',36);

DEFINE('USER\PRIVILEGE_BETA_TEST',34);

//39

require_once ABS_PATH.'/interface/main.php';

class Main extends \AbstractNS\Main{
    private $__userId;
    private $__companyId;
    private $__expertLevel;
    private $__privilegies;
    private $__isInReadOnlyMode;
    private $__refreshed_this_session;
    function __construct(){
        parent::__construct();
        $this->__userId = $this->XM->getSessionVar('user.userId',null);
        $this->__companyId = $this->XM->getSessionVar('user.companyId',null);
        $this->__expertLevel = $this->XM->getSessionVar('user.expertLevel',null);
        $this->__privilegies = null;
        $this->__isInReadOnlyMode = $this->XM->getSessionVar('user.isInReadOnlyMode',false);
        $this->__refreshed_this_session = false;
    }
    public function __init(){
        $this->__refresh_activity_time();
        $this->__check_refresh_time();
    }

    public function isAdmin(){
        return $this->__userId==4;
    }
    public function isLoggedIn(){
        return $this->__userId!==null;
    }
    public function getUserId(){
        return (int)$this->__userId;
    }
    public function isInCompany(){
        return $this->__companyId!==null;
    }
    public function getCompanyId(){
        return (int)$this->__companyId;
    }
    public function isCompanyOwner(){
        return $this->XM->getSessionVar('user.isCompanyOwner',false);
    }
    public function isCompanyApproved(){
        return $this->XM->getSessionVar('user.isCompanyApproved',false);
    }
    public function isApprovedExpert(){
        return (bool)$this->__expertLevel;
    }
    public function getExpertLevel(){
        return (int)$this->__expertLevel;
    }
    
    public function isInReadOnlyMode(){
        return $this->__isInReadOnlyMode;
    }
    public function getExpertRating(){
        return 500;//placeholder
    }
    public function getFullName(){
        $result = $this->XM->getSessionVar('user.fullName',false);
        if($result===false){
            $result = $this->__get_current_user_fullname();
            if(!$result){
                $this->XM->setSessionVar('user.fullName','');
            }
        }
        if(!$result){
            return false;
        }
        return $result;
    }
    private function __force_logout(){
        $this->__userId = $this->__companyId = $this->__expertLevel = null;
        $this->__privilegies = null;
        $this->XM->setSessionVar('user.userId',null);
        $this->XM->setSessionVar('user.companyId',null);
        $this->XM->setSessionVar('user.isCompanyOwner',null);
        $this->XM->setSessionVar('user.isCompanyApproved',null);
        $this->XM->setSessionVar('user.expertLevel',null);
        $this->XM->setSessionVar('user.privilegies',null);
        $this->XM->setSessionVar('user.userActivityTime',null);
        $this->XM->setSessionVar('user.fullName',null);
        
        $this->XM->setSessionVar('user.isInReadOnlyMode',null);
    }
    public function logout(){
        $this->__force_logout();
        redirect('/login');
        return true;
    }
    private function __refresh_activity_time(){
        if(!$this->isLoggedIn()){
            return false;
        }
        $time = time();
        if($this->XM->getSessionVar('user.userRefreshTime',0)>=$time-\USER\ACTIVITY_TIME_REFRESH_FREQ){
            return false;
        }
        $this->XM->sqlcore->query('UPDATE user set user_activity_time = '.$time.' where user_id = '.$this->getUserId());
        $this->XM->sqlcore->commit();
        return true;
    }
    private function __check_refresh_time(){
        if(!$this->isLoggedIn()){
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT user.user_refresh_time
            FROM user 
            WHERE user.user_id = '.$this->getUserId().' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $this->__force_logout();
            return false;
        }
        if($this->XM->getSessionVar('user.userRefreshTime',0)!==(int)$row['user_refresh_time']){
            $this->__refresh_states();
        }
        return true;
    }
    private function __refresh_states(){
        if(!$this->isLoggedIn()){
            return false;
        }
        if($this->__refreshed_this_session){
            return true;
        }
        $this->__refreshed_this_session = true;
        $res = $this->XM->sqlcore->query('SELECT company.company_id,user.user_iscompanyowner,company.company_is_approved,user.user_expert_level,user.user_can_add_product,user.user_can_add_tasting,user.user_can_register_company,user.user_refresh_time 
            FROM user 
            left join company on company.company_id = user.company_id
            WHERE user.user_id = '.$this->getUserId().' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        $this->__expertLevel = (int)$row['user_expert_level'];
        $this->XM->setSessionVar('user.expertLevel',$this->__expertLevel);
        if($row['company_id']!==null){
            $this->__companyId = (int)$row['company_id'];
            $this->XM->setSessionVar('user.companyId',$this->__companyId);
            if($row['user_iscompanyowner']){
                $this->XM->setSessionVar('user.isCompanyOwner',true);
            }
            if($row['company_is_approved']){
                $this->XM->setSessionVar('user.isCompanyApproved',true);
            }
        }
        $this->__privilegies = array();//\USER\DEFAULT_PRIVILEGIES;

        // special rights
        if($this->__userId==25){//e.bulgakova@auvix.ru
            $this->__add_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES,\USER\PRIVILEGE_USER_APPROVE_EXPERT,\USER\PRIVILEGE_SHOW_COMPANIES_USER_LISTS,\USER\PRIVILEGE_PRODUCT_APPROVE_PRODUCT,\USER\PRIVILEGE_TASTING_APPROVE_CONTEST);
        }
        if($this->__userId==95){//ivan.svinarev87@gmail.com - Ассоциация кавистов
            $this->__add_privilege(\USER\PRIVILEGE_PRODUCT_APPROVE_PRODUCT);
        }
        if($this->__userId==550){//designer access
            $this->__privilegies = array((1<<32)-1,(1<<32)-1);//2^64-1
            $this->__remove_privilege(\USER\PRIVILEGE_BETA_TEST);
            $this->XM->setSessionVar('user.isInReadOnlyMode',true);
        }
        if($this->__userId==4 || $this->__userId==24){//admin & v.antonov
            $this->__privilegies = array((1<<32)-1,(1<<32)-1);//2^64-1
        }
        if($this->isCompanyApproved() && $row['user_can_add_product']){
            $this->__add_privilege(\USER\PRIVILEGE_PRODUCT_USERFILL_ATTRIBUTES,\USER\PRIVILEGE_PRODUCT_ADD_PRODUCT);
        } else {
            $this->__remove_privilege(\USER\PRIVILEGE_PRODUCT_USERFILL_ATTRIBUTES,\USER\PRIVILEGE_PRODUCT_ADD_PRODUCT);
        }
        if($this->isCompanyApproved() && $row['user_can_add_tasting']){
            $this->__add_privilege(\USER\PRIVILEGE_TASTING_ADD_TASTING);
        } else {
            $this->__remove_privilege(\USER\PRIVILEGE_TASTING_ADD_TASTING);
        }
        if(!$this->isInCompany() && $row['user_can_register_company']){
            $this->__add_privilege(\USER\PRIVILEGE_REGISTER_COMPANY);
        } else {
            $this->__remove_privilege(\USER\PRIVILEGE_REGISTER_COMPANY);
        }
        if($this->isCompanyOwner()){
            $this->__add_privilege(\USER\PRIVILEGE_PRODUCT_CHANGE_COMPANY_FAVOURITES);
        } else {
            $this->__remove_privilege(\USER\PRIVILEGE_PRODUCT_CHANGE_COMPANY_FAVOURITES);
        }
        $this->XM->setSessionVar('user.privilegies',$this->__privilegies);

        $this->XM->setSessionVar('user.userRefreshTime',(int)$row['user_refresh_time']);        
        return true;
    }
    public function check_state($state,$refresh=false){
        if($state==\USER\STATE_LOGGED_IN){
            return $this->isLoggedIn();
        }
        // if($refresh){
        //     $result = $this->check_state($state);
        //     if($result){
        //         return $result;
        //     }
        //     $this->__refresh_states();
        //     return $this->check_state($state);
        // }
        switch($state){
            case \USER\STATE_IN_COMPANY:
                return $this->isInCompany();
                break;
            case \USER\STATE_IN_APPROVED_COMPANY:
                return $this->isCompanyApproved();
                break;
            case \USER\STATE_IS_COMPANY_OWNER:
                return $this->isCompanyOwner();
                break;
            case \USER\STATE_IS_APPROVED_EXPERT:
                return $this->isApprovedExpert();
                break;
        }
        return null;
    }
    
    private function __add_privilege(){
        if(!$this->isLoggedIn()){
            return false;
        }
        if($this->__privilegies===null){
            $this->__privilegies = $this->XM->getSessionVar('user.privilegies',array());//\USER\DEFAULT_PRIVILEGIES
        }
        foreach (func_get_args() as $privilege){
            $group = floor($privilege/32);
            $privilege = $privilege%32;
            if(!isset($this->__privilegies[$group])){
                $this->__privilegies[$group] = 0;
            }
            $this->__privilegies[$group] |= 1 << $privilege;
        }
        return true;
    }
    private function __remove_privilege(){
        if(!$this->isLoggedIn()){
            return false;
        }
        if($this->__privilegies===null){
            $this->__privilegies = $this->XM->getSessionVar('user.privilegies',array());//\USER\DEFAULT_PRIVILEGIES
        }
        foreach (func_get_args() as $privilege){
            $group = floor($privilege/32);
            $privilege = $privilege%32;
            if(!isset($this->__privilegies[$group])){
                continue;
            }
            if(!(($this->__privilegies[$group] >> $privilege) & 1)){
                continue;
            }
            $this->__privilegies[$group] &= ~(1 << $privilege);
        }
        return true;
    }
    public function check_privilege($privilege){
        if(!$this->isLoggedIn()){
            return null;
        }
        if($this->__privilegies===null){
            $this->__privilegies = $this->XM->getSessionVar('user.privilegies',array());//\USER\DEFAULT_PRIVILEGIES
        }
        $group = floor($privilege/32);
        $privilege = $privilege%32;
        if(!isset($this->__privilegies[$group])){
            return false;
        }
        if(($this->__privilegies[$group] >> $privilege) & 1){
            return true;
        }
        return false;
    }
    public function join_company($company_id){
        if(!$this->isLoggedIn()){
            return false;
        }
        if($this->isInCompany()){
            return false;
        }
        $company_id = (int)$company_id;
        $this->XM->sqlcore->query('UPDATE user set company_id = '.$company_id.' where user_id = '.$this->XM->user->getUserId().' and not company_id <=>' .$company_id);
        $this->XM->sqlcore->commit();
        $this->__companyId = $company_id;
        $this->XM->setSessionVar('user.companyId',$this->__companyId);
        return true;
    }
    public function process_login(){
        if(isset($_POST) && isset($_POST['action']) && $_POST['action']=='user_login' && isset($_POST['login']) && isset($_POST['pass'])){
            $err = null;
            if($this->XM->user->login($_POST['login'], $_POST['pass'], $err)){
                $this->XM->addMessage(langTranslate('User', 'login', 'You\'ve been logged in', 'You\'ve been logged in'), 2);
            } else {
                $this->XM->addMessage($err, 0);
            }
        }
    }
    public function login($login, $pass, &$err){
        $login = mb_strtolower(trim($login, " \t\n\r\0\x0B\xC2\xA0"),'UTF-8');
        if(!$this->__validate_login($login, $err)){
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT user_id FROM user WHERE user_login_checksum = '.$this->XM->sqlcore->checksum($login).' and user_login = \''.$this->XM->sqlcore->prepString($login,64).'\' and user_password = \''.$this->XM->sqlcore->prepString($this->__encodeLI($login,$pass),32).'\' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('User', 'err', 'Auth Error. Wrong username/password combination', 'Auth Error. Wrong username/password combination');
            return false;
        }
        $this->__auth((int)$row['user_id']);
        setcookie('user_lastLogin', $login, time()+31536000, '/');
        return true;
    }
    public function direct_auth($user_id, $code, &$err){
        $user_id = (int)$user_id;
        if(!preg_match('#^[a-f0-9]{32}$#', $code)){
            $err = langTranslate('User', 'err', 'Invalid direct auth code', 'Invalid direct auth code');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT 1 FROM user_direct_auth WHERE user_id = '.$user_id.' and uda_code = \''.$this->XM->sqlcore->prepString($this->__encodeLI($user_id,$code),32).'\' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('User', 'err', 'Invalid direct auth code', 'Invalid direct auth code');
            return false;
        }
        $this->__auth($user_id);
        return true;
    }
    private function __auth($user_id){
        $user_id = (int)$user_id;
        $res = $this->XM->sqlcore->query('SELECT lang_id FROM user WHERE user_id = '.$user_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//never
            return false;
        }
        $this->__userId = $user_id;
        $this->XM->setSessionVar('user.userId',$this->__userId);
        if($row['lang_id']!==null){
            $this->XM->lang->setLang((int)$row['lang_id']);    
        }
        $this->__refresh_states();
        return true;
    }
    private function __encodeLI($login, $pass){
        $login = (string)$login;
        $pass = (string)trim($pass);
        $salt = '#%!^%@';
        $md5 = md5($login.$salt.$pass);
        $md5rev = md5($pass.$salt.$login);
        $encoded = '';
        $change = array('0'=>')','1'=>'!','2'=>'@','3'=>'#','4'=>'$','5'=>'%','6'=>'^','7'=>'&','8'=>'*','9'=>'(','a'=>'A','b'=>'B','c'=>'C','d'=>'D','e'=>'E','f'=>'F');
        $willChange = array('0','2','4','6','8','a','c','e');
        for($i=0;$i<32;$i++){
            $revchar = substr($md5rev, $i, 1);
            $char = substr($md5, $i, 1);
            if(in_array(substr($md5rev, $i, 1),$willChange) && array_key_exists($char, $change)){
                $encoded .= $change[$char];
            } else {
                $encoded .= $char;
            }
        }
        return $encoded;
    }
    private function __validate_login($login,&$err){
        if(!strlen($login)){
            $err = langTranslate('User', 'err', 'Login field is empty', 'Login field is empty');
            return false;
        }
        if(mb_strlen($login, 'UTF-8')>64){
            $err = formatReplace(langTranslate('user', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                langTranslate('User', 'loginForm', 'Username', 'E-mail'),
                64);
            return false;
        }
        return true;
    }
    public function register_user($login,$pass,&$err){
        //$err = langTranslate('User', 'err', 'User registration is temporarily closed', 'User registration is temporarily closed');
        //return false;
        if($this->isLoggedIn()){
            $err = langTranslate('User', 'err', 'You\'re already registered', 'You\'re already registered');
            return false;
        }
        $login = mb_strtolower(trim($login, " \t\n\r\0\x0B\xC2\xA0"),'UTF-8');
        if(!$this->__validate_login($login, $err)){
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT 1 FROM user WHERE user_login_checksum = '.$this->XM->sqlcore->checksum($login).' and user_login = \''.$this->XM->sqlcore->prepString($login,64).'\' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if($row){
            $err = langTranslate('User', 'err', 'Username isn\'t available', 'Username isn\'t available');
            return false;
        }
        $this->XM->sqlcore->query('INSERT INTO user (user_login_checksum,user_login,user_password,lang_id) VALUES ('.$this->XM->sqlcore->checksum($login).',\''.$this->XM->sqlcore->prepString($login,64).'\',\''.$this->__encodeLI($login,$pass).'\','.\LANG\DEFAULT_LANG_ID.')');
        $user_id = $this->XM->sqlcore->lastInsertId();
        if(!$this->isInReadOnlyMode()){
            $this->XM->sqlcore->query('INSERT INTO user_se (user_id,u_se_type,lang_id,u_se_text) VALUES ('.$user_id.',0,null,\''.$this->XM->sqlcore->prepString($this->XM->sqlcore->search_engine_alias($login),128).'\')');
        }
        
        $this->XM->sqlcore->commit();
        return true;
    }
    public function get_user_info_for_all_languages($user_id){
        $user_id = (int)$user_id;
        if($user_id<=0){//invalid user_id
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT user.user_login, user.user_phone, user.user_background, user.user_employment, user_ml.user_ml_last_name, user_ml.user_ml_first_name, user_ml.user_ml_patronymic, if(user.user_employment=2,user_ml.user_ml_placeofwork,null) as user_ml_placeofwork, user_ml.lang_id, user.lang_id as default_interface_lang
            from user
            left join user_ml on user_ml.user_id = user.user_id and user_ml.user_ml_is_approved = 1
            where user.user_id = '.$user_id);
        $result = array(
                'email'=>'',
                'background'=>null,
                'default_interface_lang'=>null,
                'lastname'=>array(),
                'firstname'=>array(),
                'patronymic'=>array(),
            );
        $flag_first_iter = true;
        while($row = $this->XM->sqlcore->getRow($res)){
            if($flag_first_iter){
                $result['email'] = (string)$row['user_login'];
                $result['background'] = (int)$row['user_background'];
                if($row['default_interface_lang']!==null){
                    $result['default_interface_lang'] = (int)$row['default_interface_lang'];
                }
                $flag_first_iter = false;
            }
            $lang_id = (int)$row['lang_id'];
            $result['lastname'][$lang_id] = (string)$row['user_ml_last_name'];
            $result['firstname'][$lang_id] = (string)$row['user_ml_first_name'];
            $result['patronymic'][$lang_id] = (string)$row['user_ml_patronymic'];
        }
        $this->XM->sqlcore->freeResult($res);
        if($flag_first_iter){//user doesn't exist
            return false;
        }
        return $result;
    }
    public function get_expert_level_list(){
        return array(
                0=>langTranslate('user','Expert Level','Amateur','Amateur'),
                1=>langTranslate('user','Expert Level','Beginner Expert','Beginner Expert'),
                2=>langTranslate('user','Expert Level','Expert','Expert'),
                3=>langTranslate('user','Expert Level','Professional Expert','Professional Expert'),
            );
    }
    private function __get_current_user_fullname(){
        if(!$this->isLoggedIn()){
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT user_ml.user_ml_fullname
            from user
            inner join user_ml on user_ml.user_id = user.user_id
            where user.user_id = '.$this->XM->user->getUserId().'
            order by user_ml.lang_id = user.lang_id desc
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//never
            return false;
        }
        return (string)$row['user_ml_fullname'];
    }
    public function get_user_info($user_id){
        $user_id = (int)$user_id;
        if($user_id<=0){//invalid user_id
            return false;
        }
        $useraccesslimit_left_join_sql = '';
        $useraccesslimit_where_sql = '';
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_SHOW_COMPANIES_USER_LISTS)){
            $useraccesslimit_left_join_sql = 'left join company_join_requests on company_join_requests.user_id = user.user_id and company_join_requests.company_id = '.$this->XM->user->getCompanyId().' and user.company_id is null';
            $useraccesslimit_where_sql = ' and (user.user_id = '.$this->XM->user->getUserId().' or user.company_id = '.$this->XM->user->getCompanyId().' or user.user_expert_level > 0 or company_join_requests.user_id is not null)';
        }
        $res = $this->XM->sqlcore->query('SELECT distinct user.user_id, user.user_login, user.user_phone, user.user_employment, user_ml.user_ml_last_name, user_ml.user_ml_first_name, user_ml.user_ml_patronymic, user_ml.user_ml_fullname, if(user.user_employment=2,user_ml.user_ml_placeofwork,null) as user_ml_placeofwork, company_ml.company_id, company_ml.company_ml_name, product_attribute_value_ml.background, user.user_expert_level, user.user_requested_expert_change
            from user
            left join (select user_id,substring_index(group_concat(user_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as user_ml_id from user_ml where user_ml_is_approved = 1 group by user_id) as ln_glue on ln_glue.user_id = user.user_id
            left join user_ml on user_ml.user_ml_id = ln_glue.user_ml_id
            left join (select company_ml.company_id,substring_index(group_concat(company_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as company_ml_id from company_ml inner join company on company.company_id = company_ml.company_id and company.company_is_approved = 1 where company_ml_is_approved = 1 and company_ml_name is not null group by company_id) as company_ln_glue on company_ln_glue.company_id = user.company_id
            left join company_ml on company_ml.company_ml_id = company_ln_glue.company_ml_id

            left join (
                select product_attribute_value_ml.pav_id, coalesce(product_attribute_value_ml.pav_ml_name,product_attribute_value.pav_origin_name,null) as background 
                from product_attribute_value
                inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pag_id = 17
                left join (
                    select product_attribute_value.pav_id,substring_index(group_concat(pav_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pav_ml_id 
                        from product_attribute_value_ml 
                        inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_ml.pav_id 
                        inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pa_show_only_origin = 0 and product_attribute.pag_id = 17
                        where pav_ml_name is not null
                        group by product_attribute_value.pav_id
                ) as pav_ln_glue on pav_ln_glue.pav_id = product_attribute_value.pav_id
                left join product_attribute_value_ml on product_attribute_value_ml.pav_ml_id = pav_ln_glue.pav_ml_id
            ) product_attribute_value_ml on product_attribute_value_ml.pav_id = user.user_background

            '.$useraccesslimit_left_join_sql.'
            where user.user_id = '.$user_id.' '.$useraccesslimit_where_sql.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//user doesn't exist
            return false;
        }
        $user_id = (int)$row['user_id'];
        $user_expert_level = (int)$row['user_expert_level'];
        $can_request_expert_change = false;
        $can_request_expert_change_reason = null;
        if($user_id==$this->getUserId()){
            $required_fields = $this->__request_expert_change_required_fields();
            if(!empty($required_fields)){
                $can_request_expert_change_reason = formatReplace(langTranslate('user', 'err', 'To request expert level change fill out fields: @1',  'To request expert level change fill out fields: @1'),
                    implode(', ', $required_fields));
            } else {
                $can_request_expert_change = true;
            }
        }
        
        return array(
                'id'=>$user_id,
                'email'=>(string)$row['user_login'],
                'lastname'=>(string)$row['user_ml_last_name'],
                'firstname'=>(string)$row['user_ml_first_name'],
                'patronymic'=>(string)$row['user_ml_patronymic'],
                'fullname'=>(string)$row['user_ml_fullname'],
                'background'=>$row['background'],
                'expert_level'=>$user_expert_level,
                'requested_expert_change'=>($user_id==$this->getUserId()||$this->XM->user->check_privilege(\USER\PRIVILEGE_USER_APPROVE_EXPERT))&&$row['user_requested_expert_change'],

                'company_id'=>(int)$row['company_id'],
                'company_name'=>(string)$row['company_ml_name'],

                'can_edit'=>(bool)$this->can_edit_user($user_id),
                'can_change_password'=>(bool)$this->can_change_password($user_id),
                'can_access_user_settings'=>(bool)$this->can_access_user_settings($user_id),
                
                'can_request_expert_change'=>$can_request_expert_change,
                'can_request_expert_change_reason'=>$can_request_expert_change_reason,

            );
    }
    public function is_direct_auth_enabled($user_id){
        if(!$this->can_access_user_settings($user_id)){
            return null;
        }
        $res = $this->XM->sqlcore->query('SELECT 1 from user_direct_auth where user_id = '.(int)$user_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        return true;
    }
    public function get_user_login($user_id){
        $user_id = (int)$user_id;
        $res = $this->XM->sqlcore->query('SELECT user_login
            from user
            where user_id = '.$user_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//user doesn't exist
            return null;
        }
        return $row['user_login'];
    }
    public function can_edit_user($user_id){
        return $user_id==$this->getUserId()||$this->check_privilege(\USER\PRIVILEGE_EDIT_USER);
    }
    public function edit_user($user_id, $phone, $default_interface_lang, $attributes, $employment, $lastname, $firstname, $patronymic, $placeofwork, &$err){
        $user_id = (int)$user_id;
        if($user_id<=0){
            $err = langTranslate('user', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        if(!$this->can_edit_user($user_id)){
            $err = langTranslate('user', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT user_phone, user_background, user_employment, lang_id from user where user_id = '.$user_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('user', 'err', 'User doesn\'t exist',  'User doesn\'t exist');
            return false;
        }
        $old_user_background = $row['user_background'];
        $old_employment = (int)$row['user_employment'];
        $update_arr = array();
        if($row['lang_id']!=$default_interface_lang && $default_interface_lang!==null){
            $default_interface_lang = (int)$default_interface_lang;
            if(!in_array($default_interface_lang, $this->XM->lang->getLanguageIdList())){
                $err = formatReplace(langTranslate('user', 'err', 'Invalid value of @1',  'Invalid value of @1'),
                       langTranslate('user', 'editUserForm', 'Default interface language','Interface'));
                return false;
            }
            $update_arr[] = 'lang_id = '.$default_interface_lang;
        }

        $languageIdList = $this->XM->lang->getLanguageIdList();
        $found_lastname = false;
        foreach($languageIdList as $lang_id){
            if(strlen(getLangArrayVal($lastname,$lang_id))){
                $found_lastname = true;
                break;
            }
        }
        if(!$found_lastname){
            $err = formatReplace(langTranslate('user', 'err', 'Fill in @1 in at least one language',  'Fill in @1 in at least one language'),
                       langTranslate('User', 'editUserForm', 'Last name', 'Last name'));
                return false;
        }

        $background_pav_id = null;
        $attributes = $this->XM->product->clean_attributes($attributes,false);
        if(!empty($attributes)){
            $attribute_chunks = array_chunk($attributes, 50);
            foreach($attribute_chunks as $attribute_chunk){
                $res = $this->XM->sqlcore->query('SELECT product_attribute_value.pav_id from product_attribute_value inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pag_id = 17 where product_attribute_value.pav_id in ('.implode(',', $attribute_chunk).') limit 1');//17 = expert
                $row = $this->XM->sqlcore->getRow($res);
                $this->XM->sqlcore->freeResult($res);
                if($row){
                    $background_pav_id = (int)$row['pav_id'];
                    break;
                }
            }
        }
        if($background_pav_id !== (int)$old_user_background){
            if($background_pav_id){
                $update_arr[] = 'user_background = '.$background_pav_id;    
            } else {
                $update_arr[] = 'user_background = null';
            }
            
        }
        if(!empty($update_arr)){
            $this->XM->sqlcore->query('UPDATE user set '.implode(',', $update_arr).' where user_id = '.$user_id);
            $this->XM->sqlcore->commit();
        }
       
        $ml_variants = array();
        $res = $this->XM->sqlcore->query('SELECT user_ml_last_name, user_ml_first_name, user_ml_patronymic, user_ml_placeofwork, lang_id, user_ml_id, user_ml_is_approved from user_ml where user_id = '.$user_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $lang_id = (int)$row['lang_id'];
            if(!isset($ml_variants[$lang_id])){
                $ml_variants[$lang_id] = array();
            }
            $ml_variants[$lang_id][] = array('last_name'=>$row['user_ml_last_name'],'first_name'=>$row['user_ml_first_name'],'patronymic'=>$row['user_ml_patronymic'],'id'=>$row['user_ml_id'],'is_approved'=>(bool)$row['user_ml_is_approved']);
        }
        $this->XM->sqlcore->freeResult($res);

        foreach($languageIdList as $lang_id){
            $lang_lastname = getLangArrayVal($lastname,$lang_id);
            $lang_firstname = getLangArrayVal($firstname,$lang_id);
            $lang_patronymic = getLangArrayVal($patronymic,$lang_id);
            if(isset($ml_variants[$lang_id])){
                foreach($ml_variants[$lang_id] as $ml_variant){
                    if($lang_lastname==$ml_variant['last_name'] && $lang_firstname==$ml_variant['first_name'] && $lang_patronymic==$ml_variant['patronymic']){
                        if($ml_variant['is_approved']){//delete all edits
                            $this->XM->sqlcore->query('DELETE FROM user_ml where user_id = '.$user_id.' and lang_id = '.$lang_id.' and user_ml_is_approved <> 1');
                            $this->XM->sqlcore->commit();
                        } else {
                            //if($this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
                            $dummy = null;
                            $this->approve_user_translation($ml_variant['id'],$dummy);
                        }
                        continue 2;//same values, no need to insert/update
                    }
                }
            }
            $insertkeys = array();
            $insertvals = array();
            if(strlen($lang_lastname)){
                if(mb_strlen($lang_lastname, 'UTF-8')>64){
                    $err = formatReplace(langTranslate('user', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                        langTranslate('User', 'editUserForm', 'Last name', 'Last name'),
                        64);
                    return false;
                }
                $insertkeys[] = 'user_ml_last_name';
                $insertvals[] = '\''.$this->XM->sqlcore->prepString($lang_lastname,64).'\'';
            }
            if(strlen($lang_firstname)){
                if(mb_strlen($lang_firstname, 'UTF-8')>64){
                    $err = formatReplace(langTranslate('user', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                        langTranslate('User', 'editUserForm', 'First name', 'First name'),
                        64);
                    return false;
                }
                $insertkeys[] = 'user_ml_first_name';
                $insertvals[] = '\''.$this->XM->sqlcore->prepString($lang_firstname,64).'\'';
            }
            if(strlen($lang_patronymic)){
                if(mb_strlen($lang_patronymic, 'UTF-8')>64){
                    $err = formatReplace(langTranslate('user', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                        langTranslate('User', 'editUserForm', 'Patronymic', 'Patronymic'),
                        64);
                    return false;
                }
                $insertkeys[] = 'user_ml_patronymic';
                $insertvals[] = '\''.$this->XM->sqlcore->prepString($lang_patronymic,64).'\'';
            }
            if(empty($insertkeys)){
                continue;
            }
            if(strlen($lang_lastname)){
                $lang_fullname = $lang_lastname;
                if(strlen($lang_firstname)){
                    $lang_fullname .= ' '.mb_substr($lang_firstname,0,1,'UTF-8').'.';
                    if(strlen($lang_patronymic)){
                        $lang_fullname .= ' '.mb_substr($lang_patronymic,0,1,'UTF-8').'.';
                    }
                }
                $insertkeys[] = 'user_ml_fullname';
                $insertvals[] = '\''.$this->XM->sqlcore->prepString($lang_fullname,70).'\'';
            }
            $insertkeys[] = 'user_id';
            $insertvals[] = $user_id;
            $insertkeys[] = 'lang_id';
            $insertvals[] = $lang_id;
            $this->XM->sqlcore->query('DELETE FROM user_ml where user_id = '.$user_id.' and lang_id = '.$lang_id.' and user_ml_is_approved <> 1');
            $res = $this->XM->sqlcore->query('INSERT INTO user_ml ('.implode(',', $insertkeys).') VALUES ('.implode(',', $insertvals).')');
            $last_id = $this->XM->sqlcore->lastInsertId();
            //temp

            $this->XM->sqlcore->commit();
            //if($this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
                $dummy = null;
                $this->approve_user_translation($last_id,$dummy);
            //}
            
        }
        return true;
    }
    private function __request_expert_change_required_fields(){
        $required_fields = array();
        //processing all user_mls, even not approved ones
        $res = $this->XM->sqlcore->query('SELECT user_ml.lang_id, if(length(user_ml.user_ml_last_name)>0,1,0) as user_ml_last_name, if(length(user_ml.user_ml_first_name)>0,1,0) as user_ml_first_name, if(user.user_employment=2,if(length(user_ml.user_ml_placeofwork)>0,1,0),1) as user_ml_placeofwork
            from user
            left join user_ml on user_ml.user_id = user.user_id
            where user.user_id = '.$this->getUserId());
        $fields = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $lang_id = (int)$row['lang_id'];
            if(!isset($fields[$lang_id])){
                $fields[$lang_id] = array(false,false,false);
            }
            if(!$fields[$lang_id][0] && $row['user_ml_last_name']){
                $fields[$lang_id][0] = true;
            }
            if(!$fields[$lang_id][1] && $row['user_ml_first_name']){
                $fields[$lang_id][1] = true;
            }
        }
        $languageList = $this->XM->lang->getLanguageList();
        foreach($languageList as $language){
            $lang_id = $language['id'];
            $lang_name = $language['name'];
            if(!isset($fields[$lang_id])){
                $fields[$lang_id] = array(false,false,false);
            }
            if(!$fields[$lang_id][0]){
                $required_fields[] = formatReplace('@1 (@2)',
                    langTranslate('User', 'editUserForm', 'Last name', 'Last name'),
                    $lang_name);
            }
            if(!$fields[$lang_id][1]){
                $required_fields[] = formatReplace('@1 (@2)',
                    langTranslate('User', 'editUserForm', 'First name', 'First name'),
                    $lang_name);
            }
        }
        return $required_fields;
    }
    public function user_expert_request($request_comment,&$err){
        if(!$this->isLoggedIn()){
            $err = langTranslate('user', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $required_fields = $this->__request_expert_change_required_fields();
        if(!empty($required_fields)){
            $err = formatReplace(langTranslate('user', 'err', 'To request expert level change fill out fields: @1',  'To request expert level change fill out fields: @1'),
                '<br />'.implode(',<br />', $required_fields));
            return false;
        }
        $request_comment = trim($request_comment);
        $comment_sql = strlen($request_comment)?'\''.$this->XM->sqlcore->prepString($request_comment,512).'\'':'null';

        $res = $this->XM->sqlcore->query('SELECT uer_id from user_expert_request where user_id = '.$this->getUserId().' and uer_active = 1 limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        
        if($row){//update
            $this->XM->sqlcore->query('UPDATE user_expert_request SET uer_comment = '.$comment_sql.',uer_request_timestamp = '.time().' where uer_id = '.(int)$row['uer_id']);
            $this->XM->sqlcore->commit();
        } else {//insert
            $this->XM->sqlcore->query('INSERT INTO user_expert_request (user_id,uer_comment,uer_request_timestamp) VALUES('.$this->getUserId().','.$comment_sql.','.time().')');
            $this->XM->sqlcore->commit();
        }
        return true;
    }
    public function get_expert_requests($user_id,&$err){
        $user_id = (int)$user_id;
        if($user_id <= 0){
            $err = langTranslate('user', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_USER_APPROVE_EXPERT)){
            $err = langTranslate('user', 'err', 'You don\'t have a privilege to approve experts',  'You don\'t have a privilege to approve experts');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT uer_id,uer_active,uer_comment,uer_accepted,uer_request_timestamp
            from user_expert_request 
            where user_id = '.$user_id.'
            order by uer_active desc, uer_request_timestamp desc');
        $result = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $result[] = array(
                    'id'=>(int)$row['uer_id'],
                    'active'=>(bool)$row['uer_active'],
                    'comment'=>(string)$row['uer_comment'],
                    'is_accepted'=>(bool)$row['uer_accepted'],
                    'request_timestamp'=>(int)$row['uer_request_timestamp']
                );
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }
    public function resolve_user_expert_request($id, $approve, $expert_level, &$err){
        $id = (int)$id;
        if($id <= 0){
            $err = langTranslate('user', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_USER_APPROVE_EXPERT)){
            $err = langTranslate('user', 'err', 'You don\'t have a privilege to approve experts',  'You don\'t have a privilege to approve experts');
            return false;
        }
        if($approve){
            $res = $this->XM->sqlcore->query('SELECT user.user_id,user.user_expert_level,user_expert_request.uer_active
                from user
                inner join user_expert_request on user_expert_request.user_id = user.user_id
                where user_expert_request.uer_id = '.$id.'
                limit 1');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if(!$row){//something doesn't exist
                $err = langTranslate('user', 'err', 'Invalid ID',  'Invalid ID');
                return false;
            }
            if(!$row['uer_active']){
                return true;//request already resolved
            }
            if((int)$row['user_expert_level']==$expert_level){
                $err = langTranslate('user', 'err', 'You have to choose option different from current user expert level',  'You have to choose option different from current user expert level');
                return false;
            }
            if($this->user_set_expert_level((int)$row['user_id'],$expert_level, null, $err)===false){
                return false;
            }
            $this->XM->sqlcore->query('UPDATE user_expert_request SET uer_accepted = 1 where uer_id = '.$id.' and uer_active = 1');
            $this->XM->sqlcore->commit();
        } else {
            $this->XM->sqlcore->query('UPDATE user_expert_request SET uer_accepted = 0 where uer_id = '.$id.' and uer_active = 1');
            $this->XM->sqlcore->commit();
        }
        return true;
    }
    public function user_set_expert_level($id, $expert_level, $convert_from_date, &$err){
        $id = (int)$id;
        $expert_level = (int)$expert_level;
        if($id <= 0){
            $err = langTranslate('user', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_USER_APPROVE_EXPERT)){
            $err = langTranslate('user', 'err', 'You don\'t have a privilege to approve experts',  'You don\'t have a privilege to approve experts');
            return false;
        }
        if(!array_key_exists($expert_level, $this->get_expert_level_list())){
            $err = langTranslate('user', 'err', 'Invalid expert level',  'Invalid expert level');
            return false;
        }
        $this->XM->sqlcore->query('UPDATE user SET user_expert_level = '.$expert_level.' where user_id = '.$id);
        if($convert_from_date){
            $convert_from_date_timestamp = strtotime($convert_from_date);
            if($convert_from_date_timestamp){
                $t_ids = array();
                $res = $this->XM->sqlcore->query('SELECT distinct tasting.t_id, if(product_vintage_review.user_expert_level '.($expert_level==3?' <> 3':' = 3').',1,0) as refresh_eval
                    from product_vintage_review
                    inner join tasting on tasting.t_id = product_vintage_review.t_id
                    where product_vintage_review.user_expert_level <> '.$expert_level.' and tasting.t_status = 3 and tasting.t_assessment = 1 and product_vintage_review.user_id = '.$id.' and product_vintage_review.pvr_timestamp > '.$convert_from_date_timestamp);
                while($row = $this->XM->sqlcore->getRow($res)){
                    $t_ids[] = array((int)$row['t_id'],(bool)$row['refresh_eval']);
                }
                $this->XM->sqlcore->freeResult($res);

                $this->XM->sqlcore->query('UPDATE product_vintage_review SET user_expert_level = '.$expert_level.' where user_id = '.$id.' and pvr_timestamp > '.$convert_from_date_timestamp);
                foreach($t_ids as list($t_id,$need_eval)){
                    if($need_eval){
                        $this->XM->tasting->__refresh_global_expert_evaluation_for_tasting($t_id);
                    } else {
                        $this->XM->tasting->__process_global_evaluation_for_tasting($t_id);
                    }
                }
            }
        }
        $this->XM->sqlcore->commit();
        return true;
    }
    public function user_change_add_right($id, $right, $enable, &$err){
        $id = (int)$id;
        if($id <= 0){
            $err = langTranslate('user', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT company_id from user where user_id = '.$id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('user', 'err', 'User doesn\'t exist',  'User doesn\'t exist');
            return false;
        }
        if(!$this->__can_edit_add_rights($row['company_id'])){
            $err = langTranslate('user', 'err', 'You don\'t have a privilege to edit access rights of this user',  'You don\'t have a privilege to edit access rights of this user');
            return false;
        }
        $tablecol = null;
        switch($right){
            case 'add-product':
                $tablecol = 'user_can_add_product';
                break;
            case 'add-tasting':
                $tablecol = 'user_can_add_tasting';
                break;
            default:
                $err = langTranslate('user', 'err', 'Internal Error',  'Internal Error');
                return false;
        }
        if($enable){
            $this->XM->sqlcore->query('UPDATE user SET '.$tablecol.' = 1 where user_id = '.$id);
        } else {
            $this->XM->sqlcore->query('UPDATE user SET '.$tablecol.' = 0 where user_id = '.$id);
        }
        $this->XM->sqlcore->commit();
        return true;
    }

    public function user_set_direct_auth($user_id,$enable,&$code,&$err){
        $user_id = (int)$user_id;
        if($user_id <= 0){
            $err = langTranslate('user', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        if(!$this->can_access_user_settings($user_id)){
            $err = langTranslate('user', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $this->XM->sqlcore->query('DELETE FROM user_direct_auth WHERE user_id = '.$user_id);//clear PK anyway
        if($enable){
            $code = md5($user_id.'^%((%'.time());
            $this->XM->sqlcore->query('INSERT INTO user_direct_auth (user_id,uda_code) VALUES ('.$user_id.',\''.$this->XM->sqlcore->prepString($this->__encodeLI($user_id,$code),32).'\')');
        }
        $this->XM->sqlcore->commit();
        return true;
    }
    
    public function approve_user_translation($id, &$err){
        $id = (int)$id;
        if($id <= 0){
            $err = langTranslate('user', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        //if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
        //    $err = langTranslate('user', 'err', 'You don\'t have a privilege to approve translations',  'You don\'t have a privilege to approve translations');
        //    return false;
        //}
        $res = $this->XM->sqlcore->query('SELECT user_id, lang_id, user_ml_is_approved from user_ml where user_ml_id = '.$id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('user', 'err', 'Translation doesn\'t exist',  'Translation doesn\'t exist');
            return false;
        }
        if($row['user_ml_is_approved']){
            return true;
        }
        $this->XM->sqlcore->query('UPDATE user_ml set user_ml_approved_user_id = '.$this->XM->user->getUserId().', user_ml_approved_timestamp = '.time().', user_ml_is_approved=1 where user_ml_id = '.$id);
        $this->XM->sqlcore->query('DELETE FROM user_ml where user_id = '.$row['user_id'].' and lang_id = '.$row['lang_id'].' and user_ml_id <> '.$id);
        $this->XM->sqlcore->commit();
        //se
        $user_id = (int)$row['user_id'];
        $lang_id = (int)$row['lang_id'];
        $this->XM->sqlcore->query('DELETE FROM user_se where user_id = '.$user_id.' and lang_id = '.$lang_id.' and u_se_type = 2');
        $res = $this->XM->sqlcore->query('SELECT user_ml_first_name,user_ml_last_name,user_ml_patronymic from user_ml where user_id = '.$user_id.' and lang_id = '.$lang_id.' and user_ml_is_approved = 1 LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        $se_aliases = array();
        foreach(array('user_ml_first_name','user_ml_last_name','user_ml_patronymic') as $key){
            $se_alias = $this->XM->sqlcore->search_engine_alias($row[$key]);
            if(strlen($se_alias) && !in_array($se_alias, $se_aliases)){
                $se_aliases[] = $se_alias;
            }
        }
        foreach($se_aliases as $se_alias){
            $this->XM->sqlcore->query('INSERT INTO user_se (user_id,u_se_type,lang_id,u_se_text) VALUES ('.$user_id.',2,'.$lang_id.',\''.$this->XM->sqlcore->prepString($se_alias,64).'\')');
        }
        return true;
    }
    public function __refresh_user_search_engine_entries(){
        $res = $this->XM->sqlcore->query('DELETE from user_se');
        $res = $this->XM->sqlcore->query('SELECT user_id, user_login, null as user_phone from user');
        while($row = $this->XM->sqlcore->getRow($res)){
            $user_id = (int)$row['user_id'];
            foreach(array(0=>'user_login',1=>'user_phone') as $se_type=>$key){
                $se_alias = $this->XM->sqlcore->search_engine_alias($row[$key]);
                if(strlen($se_alias)){
                    $this->XM->sqlcore->query('INSERT INTO user_se (user_id,u_se_type,lang_id,u_se_text) VALUES ('.$user_id.','.$se_type.',null,\''.$this->XM->sqlcore->prepString($se_alias,64).'\')');
                }
            }
        }
        $res = $this->XM->sqlcore->query('SELECT user_id, user_ml_first_name, user_ml_last_name, user_ml_patronymic, lang_id from user_ml where user_ml_is_approved = 1');
        while($row = $this->XM->sqlcore->getRow($res)){
            $user_id = (int)$row['user_id'];
            $lang_id = (int)$row['lang_id'];
            
            $se_aliases = array();
            foreach(array('user_ml_first_name','user_ml_last_name','user_ml_patronymic') as $key){
                $se_alias = $this->XM->sqlcore->search_engine_alias($row[$key]);
                if(strlen($se_alias) && !in_array($se_alias, $se_aliases)){
                    $se_aliases[] = $se_alias;
                }
            }
            foreach($se_aliases as $se_alias){
                $this->XM->sqlcore->query('INSERT INTO user_se (user_id,u_se_type,lang_id,u_se_text) VALUES ('.$user_id.',2,'.$lang_id.',\''.$this->XM->sqlcore->prepString($se_alias,64).'\')');
            }
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->commit();
    }
    public function check_password_for_user_id($user_id, $password, &$err){
        $user_id = (int)$user_id;
        if($user_id<=0){//invalid user id
            $err = langTranslate('user', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT user_login, user_password from user where user_id = '.$user_id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//no such user
            $err = langTranslate('user', 'err', 'User doesn\'t exist',  'User doesn\'t exist');
            return false;
        }
        if($this->__encodeLI($row['user_login'],$password)!==$row['user_password']){
            $err = langTranslate('user', 'err', 'Wrong password',  'Wrong password');
            return false;
        }
        return true;
    }
    public function can_change_password($user_id){
        return $user_id==$this->getUserId()||$this->check_privilege(\USER\PRIVILEGE_CHANGE_PASSWORD);
    }
    public function can_access_user_settings($user_id){
        return $user_id==$this->getUserId()||$this->check_privilege(\USER\PRIVILEGE_CHANGE_USER_SETTINGS);
    }
    public function change_password($user_id, $login, $password, $force_change_by_code, &$err){
        $user_id = (int)$user_id;
        if($user_id<=0){//invalid user id
            $err = langTranslate('user', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        if(!$force_change_by_code && !$this->can_change_password($user_id)){
            $err = langTranslate('user', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT user_login from user where user_id = '.$user_id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//no such user
            $err = langTranslate('user', 'err', 'User doesn\'t exist',  'User doesn\'t exist');
            return false;
        }
        $login_updated = false;
        $update_arr = array();
        $login = mb_strtolower(trim($login, " \t\n\r\0\x0B\xC2\xA0"),'UTF-8');
        if(strlen($login) && $login!=$row['user_login']){
            if(!$this->__validate_login($login, $err)){
                return false;
            }
            $res = $this->XM->sqlcore->query('SELECT 1 FROM user WHERE user_login_checksum = '.$this->XM->sqlcore->checksum($login).' and user_login = \''.$this->XM->sqlcore->prepString($login,64).'\' LIMIT 1');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if($row){
                $err = langTranslate('User', 'err', 'Username isn\'t available', 'Username isn\'t available');
                return false;
            }
            $update_arr[] = 'user_login_checksum = '.$this->XM->sqlcore->checksum($login);
            $update_arr[] = 'user_login = \''.$this->XM->sqlcore->prepString($login,64).'\'';
            $login_updated = true;
        } else {
            $login = $row['user_login'];
        }
        $update_arr[] = 'user_password = \''.$this->__encodeLI($login,$password).'\'';
        $this->XM->sqlcore->query('UPDATE user SET '.implode(',', $update_arr).' where user_id = '.$user_id);
        $this->XM->sqlcore->commit();
        if($login_updated){
            $res = $this->XM->sqlcore->query('SELECT u_se_text from user_se where user_id = '.$user_id.' and u_se_type = 0 limit 1');
            $se_row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            $se_login = '';
            if($se_row){
                $se_login = $se_row['u_se_text'];
            }
            $asciialias = $this->XM->sqlcore->search_engine_alias($login);
            if($se_login != $asciialias){
                if(empty($asciialias)){
                    $this->XM->sqlcore->query('DELETE FROM user_se where user_id = '.$user_id.' and u_se_type = 0');
                } elseif(!$se_row){
                    $this->XM->sqlcore->query('INSERT INTO user_se (user_id,u_se_type,lang_id,u_se_text) VALUES ('.$user_id.',0,null,\''.$this->XM->sqlcore->prepString($asciialias,64).'\')');    
                } else {
                    $this->XM->sqlcore->query('UPDATE user_se SET u_se_text = \''.$this->XM->sqlcore->prepString($asciialias,64).'\' where user_id = '.$user_id.' and u_se_type = 0');
                }
                $this->XM->sqlcore->commit();
            }
        }
        return true;
    }
    public function password_reset_request($login, &$err){
        $login = mb_strtolower(trim($login, " \t\n\r\0\x0B\xC2\xA0"),'UTF-8');
        if(!$this->__validate_login($login, $err)){
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT user.user_id, user_ml.user_ml_fullname 
            FROM user 
            left join (select user_ml.user_id,substring_index(group_concat(user_ml.user_ml_id order by user_ml.lang_id = user.lang_id desc),\',\',1) as user_ml_id 
                from user_ml 
                inner join user on user.user_id = user_ml.user_id
                where user_ml.user_ml_is_approved = 1 and user.user_login_checksum = '.$this->XM->sqlcore->checksum($login).' 
                group by user_ml.user_id
            ) as ln_glue on ln_glue.user_id = user.user_id
            left join user_ml on user_ml.user_ml_id = ln_glue.user_ml_id
            WHERE user.user_login_checksum = '.$this->XM->sqlcore->checksum($login).' and user.user_login = \''.$this->XM->sqlcore->prepString($login,64).'\' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('User', 'err', 'The specified user is not yet registered', 'The specified user is not yet registered');
            return false;
        }
        $user_id = (int)$row['user_id'];
        $user_fullname = (string)$row['user_ml_fullname'];
        $code = md5('!^'.$user_id.'%@'.time().'#%');
        $this->XM->sqlcore->query('INSERT INTO user_password_recovery_code (user_id,uprc_code) VALUES ('.$user_id.',\''.$this->XM->sqlcore->prepString($code,32).'\')');
        $this->XM->sqlcore->commit();
        $this->XM->sendmail->reset();
        $this->XM->sendmail->addAddress($login,$user_fullname);
        $this->XM->sendmail->setSubject(langTranslate('User','passwordRecoveryForm','Password recovery','Password recovery'));
        $this->XM->sendmail->setBody($this->XM->view->load('user/mailpasswordrecovery',array('code'=>$code),true),true,'',true);
        $this->XM->sendmail->send();
        return true;
    }
    public function password_reset_get_user_id($code, &$err){
        if(!preg_match('#^[a-f0-9]{32}$#', $code)){
            $err = langTranslate('User', 'err', 'Invalid password reset code', 'Invalid password reset code');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT user_id from user_password_recovery_code where uprc_code = \''.$this->XM->sqlcore->prepString($code,32).'\' and uprc_timestamp >= '.(time()-3600));
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('User', 'err', 'Specified password reset code doesn\'t exist', 'Specified password reset code doesn\'t exist');
            return false;
        }
        return (int)$row['user_id'];
    }
    public function password_reset_delete_code($code){
        $this->XM->sqlcore->query('DELETE from user_password_recovery_code where uprc_code = \''.$this->XM->sqlcore->prepString($code,32).'\'');
        $this->XM->sqlcore->query('DELETE from user_password_recovery_code where uprc_timestamp < '.(time()-3600));
        $this->XM->sqlcore->commit();
    }
    public function filter_user($search_string, $attributes, $expert_level, $show_expert_level, $company_id, $only_favourites, $only_online, $only_experts, $only_global_expert_scores, $only_participants_of_contest_id, $only_participants_of_contest_product_id, $joinrequests_company_id, $approve_expert_list, $hide_company_name, $order_by_field, $order_by_direction_asc, &$page, &$pagelimit, &$count, &$err){
        if(($page = (int)$page)<=0){
            $page = 1;
        }
        $pagelimit = (int)$pagelimit;
        if($pagelimit<=0 || $pagelimit>100){
            $pagelimit = 50;
        }
        $company_id = (int)$company_id;
        $joinrequests_company_id = (int)$joinrequests_company_id;
        if($joinrequests_company_id&&!$this->XM->user->can_invite_users_to_company($joinrequests_company_id)){
            $err = langTranslate('user', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $current_timestamp = time();

        $attributes = $this->XM->product->clean_attributes($attributes,false);
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtervals (
                id BIGINT UNSIGNED NOT NULL
            )');
        foreach($attributes as $attrval_id){
            $this->XM->sqlcore->query('INSERT INTO filtervals (id) VALUES ('.$attrval_id.')');
        }
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filterbackground 
            SELECT distinct product_attribute_value_tree.pav_id 
                from product_attribute_value
                inner join filtervals on filtervals.id = product_attribute_value.pav_id
                inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
                inner join product_attribute_value_tree on product_attribute_value_tree.pav_anc_id = product_attribute_value.pav_id
                where product_attribute.pag_id = 17');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filtervals');
        $res = $this->XM->sqlcore->query('SELECT 1 from filterbackground limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        $backgroundsqljoin = '';
        if($row){
            $backgroundsqljoin = 'inner join filterbackground on filterbackground.pav_id = user.user_background';
        }
        $expert_level_list = $this->get_expert_level_list();
        $expertlevelsqljoin = '';
        if(is_array($expert_level)&&!empty($expert_level)){

            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filterexpertlevel (
                id BIGINT UNSIGNED NOT NULL
            )');
            $unique_expert_levels = array();
            foreach($expert_level as $expert_level_id){
                $expert_level_id = (int)$expert_level_id;
                if(in_array($expert_level_id, $unique_expert_levels) || !array_key_exists($expert_level_id, $expert_level_list)){
                    continue;
                }
                $unique_expert_levels[] = $expert_level_id;
                $this->XM->sqlcore->query('INSERT INTO filterexpertlevel (id) VALUES ('.$expert_level_id.')');
            }
            $expertlevelsqljoin = 'inner join filterexpertlevel on filterexpertlevel.id = user.user_expert_level';
        }
        // $expert_level
        $favjoin = '';
        if($this->XM->user->isLoggedIn()&&$only_favourites){
            $favjoin = 'inner join user_favourite on user_favourite.user_id = '.$this->XM->user->getUserId().' and user_favourite.uf_user_id = user.user_id';
        }

        //prepare params
        $company_join_requests_sqljoin = '';
        $where_arr = array();
        if($joinrequests_company_id){
            $company_join_requests_sqljoin = 'inner join company_join_requests on company_join_requests.user_id = user.user_id and company_join_requests.company_id = '.$joinrequests_company_id.' and user.company_id is null';
            //skip default limit by company_id
        } elseif(!$this->XM->user->check_privilege(\USER\PRIVILEGE_SHOW_COMPANIES_USER_LISTS)){
            $where_arr[] = '( user.company_id = '.$this->XM->user->getCompanyId().' or user.user_expert_level > 0 )';
        }
        if($company_id){
            $where_arr[] = 'user.company_id = '.$company_id;
        }
        if($approve_expert_list){
            if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_USER_APPROVE_EXPERT)){
                $err = langTranslate('User', 'err', 'Access Denied', 'Access Denied');
                return false;
            }
            $where_arr[] = 'user.user_requested_expert_change = 1';
        }
        if($only_experts){
            $where_arr[] = 'user.user_expert_level > 0';
        }
        if($only_online){
            $where_arr[] = 'user.user_activity_time > '.($current_timestamp-\USER\ACTIVITY_TIME_STILL_ONLINE);
        }
        $only_participants_of_contest_id = (int)$only_participants_of_contest_id;
        $only_participants_of_contest_product_id = (int)$only_participants_of_contest_product_id;
        $only_participants_of_contest_id_inner_join = '';
        if($only_participants_of_contest_id){
            $only_participants_of_contest_id_inner_join = 'inner join (
                    select distinct product_vintage_review.user_id
                        from product_vintage_review
                        inner join tasting_contest_tasting on tasting_contest_tasting.t_id = product_vintage_review.t_id
                        where tasting_contest_tasting.tc_id = '.$only_participants_of_contest_id.' '.($only_participants_of_contest_product_id?' and product_vintage_review.pv_id = '.$only_participants_of_contest_product_id:'').' and product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_PERSONAL|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE).' = 0
                    union distinct
                    select distinct tasting_product_vintage_ranking.user_id
                        from tasting_product_vintage_ranking
                        '.($only_participants_of_contest_product_id?'inner join tasting_product_vintage on tasting_product_vintage.tpv_id = tasting_product_vintage_ranking.tpv_id':'').'
                        inner join tasting_contest_tasting on tasting_contest_tasting.t_id = tasting_product_vintage_ranking.t_id
                        where tasting_contest_tasting.tc_id = '.$only_participants_of_contest_id.' '.($only_participants_of_contest_product_id?' and tasting_product_vintage.pv_id = '.$only_participants_of_contest_product_id:'').'
                ) as only_participants_of_contest_id on only_participants_of_contest_id.user_id = user.user_id';
        }
        $global_expert_score_inner_join = '';
        if($show_expert_level && $only_global_expert_scores && $this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_EXPERT_EVALUATION_SCORE)){
            $global_expert_score_inner_join = 'inner join (
                select distinct user.user_id 
                    from user 
                    inner join tasting_user_global_evaluation_score on tasting_user_global_evaluation_score.user_id = user.user_id and tasting_user_global_evaluation_score.user_expert_level = user.user_expert_level
                    where tasting_user_global_evaluation_score.tuges_leniency = 0 and tasting_user_global_evaluation_score.tuges_zero=0 and tasting_user_global_evaluation_score.tuges_score > 0
            ) as global_expert_scores on global_expert_scores.user_id = user.user_id';
        }
        
        $where_sql = '';
        if(!empty($where_arr)){
            $where_sql = 'where '.implode(' and ', $where_arr);
        }
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filteruserids
            SELECT distinct user.user_id
            from user 
            '.$company_join_requests_sqljoin.'
            '.$backgroundsqljoin.'
            '.$expertlevelsqljoin.'
            '.$favjoin.'
            '.$only_participants_of_contest_id_inner_join.'
            '.$global_expert_score_inner_join.'
            '.$where_sql);

        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filterbackground');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filterexpertlevel');

        if(strlen($search_string)){
            $search_string_arr = array_unique(preg_split('#[\s,]+#', mb_strtolower(mb_substr(trim((string)$search_string),0,256,'UTF-8'),'UTF-8')));
            unset($search_string);
            foreach($search_string_arr as $key=>$search_string){
                if(mb_strlen($search_string,'UTF-8')<=2){
                    unset($search_string_arr[$key]);
                }
            }
            if(!empty($search_string_arr)){
                $user_se_where_arr = array();
                foreach($search_string_arr as $search_string){
                    $user_se_where_arr[] = 'locate(\''.$this->XM->sqlcore->prepString($this->XM->sqlcore->search_engine_alias($search_string),64).'\',user_se.u_se_text)>0';
                }
                unset($search_string_arr);
                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filteruseridsbysearchstring (
                    user_id INT UNSIGNED NOT NULL
                )');
                if(!empty($user_se_where_arr)){
                    $user_se_where_arr_chunks = array_chunk($user_se_where_arr, 100);
                    unset($user_se_where_arr);
                    foreach($user_se_where_arr_chunks as $user_se_where_arr_chunk){
                        $this->XM->sqlcore->query('INSERT INTO filteruseridsbysearchstring
                            SELECT distinct user_se.user_id
                                from filteruserids
                                inner join user_se on user_se.user_id = filteruserids.user_id
                                where '.implode(' or ', $user_se_where_arr_chunk));
                    }
                    unset($user_se_where_arr_chunks);
                }
                $this->XM->sqlcore->query('TRUNCATE TABLE filteruserids');
                $this->XM->sqlcore->query('INSERT INTO filteruserids SELECT distinct user_id from filteruseridsbysearchstring');
                $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filteruseridsbysearchstring');
            }
        }
        

        $res = $this->XM->sqlcore->query('SELECT count(1) as cnt from filteruserids');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        $count = (int)$row['cnt'];
        if($count==0){
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filteruserids');
            return array();
        }
        if(($page-1)*$pagelimit>=$count){
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filteruserids');
            return array();
        }
        $favselect = '0 as favourite';
        $favjoin = '';
        if($this->XM->user->isLoggedIn()){
            if($only_favourites){
                $favselect = '1 as favourite';
            } else {
                $favselect = 'IF(user_favourite.uf_user_id is null, 0, 1) as favourite';
                $favjoin = 'left join user_favourite on user_favourite.user_id = '.$this->XM->user->getUserId().' and user_favourite.uf_user_id = user.user_id';
            }
        }
        $company_ml_select = 'null as company_ml_name';
        $company_ml_join = '';
        if(!$hide_company_name){
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filteruserids3 SELECT * FROM filteruserids');
            $company_ml_select = 'company_ml.company_ml_name';
            $company_ml_join = 'left join (
                select company_ml.company_id,substring_index(group_concat(company_ml.company_ml_id order by company_ml.lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as company_ml_id 
                    from filteruserids3 
                    inner join user on user.user_id = filteruserids3.user_id
                    inner join company on company.company_id = user.company_id and company.company_is_approved = 1 
                    inner join company_ml on company_ml.company_id = company.company_id and company_ml.company_ml_is_approved = 1
                    where company_ml.company_ml_name is not null 
                    group by company_ml.company_id
                ) as company_ln_glue on company_ln_glue.company_id = user.company_id
            left join company_ml on company_ml.company_ml_id = company_ln_glue.company_ml_id';
        }

        $only_participants_of_contest_id = (int)$only_participants_of_contest_id;
        $only_participants_of_contest_product_id = (int)$only_participants_of_contest_product_id;
        $select_score_sql = '';
        $select_score_left_join = '';
        if($only_participants_of_contest_id && $only_participants_of_contest_product_id){
            $select_score_sql = 'select_score.score1 as score1,select_score.score2 as score2,select_score.score3 as score3';
            $select_score_left_join = 'left join (
                select product_vintage_review.user_id,round(avg(if(product_vintage_review.user_expert_level = 1,product_vintage_review.pvr_score,null))) as score1,
                        round(avg(if(product_vintage_review.user_expert_level = 2,product_vintage_review.pvr_score,null))) as score2,
                        round(avg(if(product_vintage_review.user_expert_level = 3,product_vintage_review.pvr_score,null))) as score3
                    from product_vintage_review 
                    inner join tasting_contest_tasting on tasting_contest_tasting.t_id = product_vintage_review.t_id and tasting_contest_tasting.tc_id = '.$only_participants_of_contest_id.'
                    where product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_PERSONAL|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE).' = 0 and product_vintage_review.pv_id = '.$only_participants_of_contest_product_id.'
                    group by product_vintage_review.user_id
            ) as select_score on select_score.user_id = user.user_id';
        }
        //evaluations
        $evaluation_scores_select_sql = '';
        $evaluation_scores_left_join = '';
        if($only_participants_of_contest_id){
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE only_participants_of_contest_evaluation_scores (
                `opoces_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `tpv_id` bigint(20) UNSIGNED NOT NULL,
                `tue_type` tinyint(1) UNSIGNED NOT NULL,
                `user_id` int(10) UNSIGNED NOT NULL,
                `tueus_score` int(5) UNSIGNED NOT NULL,
                PRIMARY KEY only_participants_of_contest_evaluation_scores_pk (opoces_id),
                INDEX only_participants_of_contest_evaluation_scores_tpv_id_index (tpv_id)
            )');
            $this->XM->sqlcore->query('INSERT INTO only_participants_of_contest_evaluation_scores
                SELECT null,tasting_user_evaluation.tpv_id,tasting_user_evaluation.tue_type,tasting_user.user_id,floor(coalesce(tasting_user_evaluation_user_score.tueus_score,0)*10000/only_participants_of_contest_evaluation_scores_max_scores.tueus_score) as tueus_score
                from tasting_user_evaluation
                inner join tasting_contest_tasting on tasting_contest_tasting.t_id = tasting_user_evaluation.t_id and tasting_contest_tasting.tc_id = '.$only_participants_of_contest_id.'
                '.($only_participants_of_contest_product_id?'inner join tasting_product_vintage on tasting_product_vintage.t_id = tasting_user_evaluation.t_id and tasting_product_vintage.pv_id = '.$only_participants_of_contest_product_id:'').'
                inner join tasting_user on tasting_user.t_id = tasting_user_evaluation.t_id
                inner join (
                        select tasting_user_evaluation.tpv_id,tasting_user_evaluation.tue_type,max(tasting_user_evaluation_user_score.tueus_score) as tueus_score
                            from tasting_user_evaluation
                            inner join tasting_contest_tasting on tasting_contest_tasting.t_id = tasting_user_evaluation.t_id and tasting_contest_tasting.tc_id = '.$only_participants_of_contest_id.'
                            '.($only_participants_of_contest_product_id?'inner join tasting_product_vintage on tasting_product_vintage.t_id = tasting_user_evaluation.t_id and tasting_product_vintage.pv_id = '.$only_participants_of_contest_product_id:'').'
                            inner join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id
                            group by tasting_user_evaluation.tpv_id,tasting_user_evaluation.tue_type
                    ) as only_participants_of_contest_evaluation_scores_max_scores on only_participants_of_contest_evaluation_scores_max_scores.tpv_id = tasting_user_evaluation.tpv_id and only_participants_of_contest_evaluation_scores_max_scores.tue_type = tasting_user_evaluation.tue_type
                left join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id and tasting_user_evaluation_user_score.user_id = tasting_user.user_id

                where tasting_user_evaluation.tue_type in (1,2)');
            //leniency
            $res = $this->XM->sqlcore->query('SELECT tasting_user_evaluation.t_id,tasting_user_evaluation.tue_type,count(1) as cnt
                from tasting_user_evaluation
                inner join tasting_contest_tasting on tasting_contest_tasting.t_id = tasting_user_evaluation.t_id and tasting_contest_tasting.tc_id = '.$only_participants_of_contest_id.'
                '.($only_participants_of_contest_product_id?'inner join tasting_product_vintage on tasting_product_vintage.t_id = tasting_user_evaluation.t_id and tasting_product_vintage.pv_id = '.$only_participants_of_contest_product_id:'').'
                where tasting_user_evaluation.tue_type in (1,2)
                group by tasting_user_evaluation.t_id,tasting_user_evaluation.tue_type');
            $this->XM->tasting->preload();
            $tasting_user_evaluation_leniency_arr = array();
            while($row = $this->XM->sqlcore->getRow($res)){
                $leniency = floor((int)$row['cnt']*\TASTING\EVALUATION_LENIENCY_PERCENT/100);
                if(!$leniency){
                    continue;
                }
                $t_id = (int)$row['t_id'];
                if(!isset($tasting_user_evaluation_leniency_arr[$t_id])){
                    $tasting_user_evaluation_leniency_arr[$t_id] = array();
                }
                $tasting_user_evaluation_leniency_arr[$t_id][(int)$row['tue_type']] = $leniency;
            }
            $this->XM->sqlcore->freeResult($res);
            $delete_opoces_ids = array();
            if(!empty($tasting_user_evaluation_leniency_arr)){
                foreach($tasting_user_evaluation_leniency_arr as $t_id=>$tasting_user_evaluation_leniency_sub_arr){
                    foreach($tasting_user_evaluation_leniency_sub_arr as $tue_type=>$leniency){
                        $res = $this->XM->sqlcore->query('SELECT substring_index(group_concat(only_participants_of_contest_evaluation_scores.opoces_id order by only_participants_of_contest_evaluation_scores.tueus_score asc),\',\','.$leniency.') as opoces_ids
                        from only_participants_of_contest_evaluation_scores
                        inner join tasting_product_vintage on tasting_product_vintage.tpv_id = only_participants_of_contest_evaluation_scores.tpv_id
                        where tasting_product_vintage.t_id = '.$t_id.' and only_participants_of_contest_evaluation_scores.tue_type = '.$tue_type.'
                        group by only_participants_of_contest_evaluation_scores.user_id');
                        while($row = $this->XM->sqlcore->getRow($res)){
                            $delete_opoces_ids = array_merge($delete_opoces_ids,explode(',', $row['opoces_ids']));
                        }
                    }
                }
                if(!empty($delete_opoces_ids)){
                    $delete_opoces_ids_chunks = array_chunk($delete_opoces_ids, 100);
                    foreach($delete_opoces_ids_chunks as $delete_opoces_ids_chunk){
                        $this->XM->sqlcore->query('DELETE FROM only_participants_of_contest_evaluation_scores where opoces_id in ('.implode(',', $delete_opoces_ids).')');
                    }
                }
            }
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE only_participants_of_contest_evaluation_scores_manual
                (PRIMARY KEY only_participants_of_contest_evaluation_scores_manual_pkey (user_id))
                select only_participants_of_contest_evaluation_scores.user_id, avg(tueus_score) as score
                    from only_participants_of_contest_evaluation_scores
                    where only_participants_of_contest_evaluation_scores.tue_type = 1
                    group by only_participants_of_contest_evaluation_scores.user_id');
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE only_participants_of_contest_evaluation_scores_automatic
                (PRIMARY KEY only_participants_of_contest_evaluation_scores_automatic_pkey (user_id))
                select only_participants_of_contest_evaluation_scores.user_id, avg(tueus_score) as score
                    from only_participants_of_contest_evaluation_scores
                    where only_participants_of_contest_evaluation_scores.tue_type = 2
                    group by only_participants_of_contest_evaluation_scores.user_id');
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS only_participants_of_contest_evaluation_scores');

            $evaluation_scores_left_join = 'left join only_participants_of_contest_evaluation_scores_manual as manual_evaluation_score on manual_evaluation_score.user_id = user.user_id
                left join only_participants_of_contest_evaluation_scores_automatic as automatic_evaluation_score on automatic_evaluation_score.user_id = user.user_id';
            $evaluation_scores_select_sql = 'manual_evaluation_score.score as manual_evaluation_score,automatic_evaluation_score.score as automatic_evaluation_score';
        }
        $global_expert_score_select_sql = '';
        $global_expert_score_join = '';
        if($show_expert_level && $this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_EXPERT_EVALUATION_SCORE)){
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filteruserids_global_expert_score SELECT * FROM filteruserids');
            $global_expert_score_select_sql = 'global_expert_scores.score as global_expert_score, global_expert_scores.cnt as global_expert_count';
            $global_expert_score_join = ($only_global_expert_scores?'inner join':'left join').' (
                    select user.user_id, avg(if(tasting_user_global_evaluation_score.tuges_zero=0,tasting_user_global_evaluation_score.tuges_score,0)) as score, count(distinct tasting_user_global_evaluation_score.t_id) as cnt
                        from filteruserids_global_expert_score
                        inner join user on user.user_id = filteruserids_global_expert_score.user_id
                        inner join tasting_user_global_evaluation_score on tasting_user_global_evaluation_score.user_id = user.user_id and tasting_user_global_evaluation_score.user_expert_level = user.user_expert_level and tasting_user_global_evaluation_score.tuges_leniency = 0
                        group by user.user_id
                        having avg(if(tasting_user_global_evaluation_score.tuges_zero=0,tasting_user_global_evaluation_score.tuges_score,0)) > 0
                ) as global_expert_scores on global_expert_scores.user_id = user.user_id';
        }
        
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filteruserids2 SELECT * FROM filteruserids');

        switch($order_by_field){
            case 'activity':
                $order_by_sql = '( '.$current_timestamp. ' - user.user_activity_time ) < '.\USER\ACTIVITY_TIME_STILL_ONLINE.' '.($order_by_direction_asc?'asc':'desc').', 2 asc';
                break;
            case 'name':
                $order_by_sql = '2 '.($order_by_direction_asc?'asc':'desc');
                break;
            case 'company':
                $order_by_sql = '5 '.($order_by_direction_asc?'asc':'desc').', 2 asc';
                break;
            case 'expert':
                if($global_expert_score_join){
                    $order_by_sql = 'user.user_expert_level '.($order_by_direction_asc?'asc':'desc').', coalesce(global_expert_scores.score,0) '.($order_by_direction_asc?'asc':'desc').', 2 asc';
                } else {
                    $order_by_sql = 'user.user_expert_level '.($order_by_direction_asc?'asc':'desc').', 2 asc';
                }
                break;
            case 'score1':
                if($select_score_left_join){
                    $order_by_sql = 'select_score.score1 '.($order_by_direction_asc?'asc':'desc').', 2 asc';
                } else {
                    $order_by_sql = '2 asc';
                }
                break;
            case 'score2':
                if($select_score_left_join){
                    $order_by_sql = 'select_score.score2 '.($order_by_direction_asc?'asc':'desc').', 2 asc';
                } else {
                    $order_by_sql = '2 asc';
                }
                break;
            case 'score3':
                if($select_score_left_join){
                    $order_by_sql = 'select_score.score3 '.($order_by_direction_asc?'asc':'desc').', 2 asc';
                } else {
                    $order_by_sql = '2 asc';
                }
                break;
            case 'automatic-evaluation':
                if($evaluation_scores_left_join){
                    $order_by_sql = 'coalesce(automatic_evaluation_score.score,0) '.($order_by_direction_asc?'asc':'desc').', 2 asc';
                } else {
                    $order_by_sql = '2 asc';
                }
                break;
            case 'manual-evaluation':
                if($evaluation_scores_left_join){
                    $order_by_sql = 'coalesce(manual_evaluation_score.score,0) '.($order_by_direction_asc?'asc':'desc').', 2 asc';
                } else {
                    $order_by_sql = '2 asc';
                }
                break;
            default:
                if($only_global_expert_scores){
                    $order_by_sql = 'user.user_expert_level '.($order_by_direction_asc?'asc':'desc').', coalesce(global_expert_scores.score,0) '.($order_by_direction_asc?'asc':'desc').', 2 asc';
                } elseif($evaluation_scores_left_join){
                    $order_by_sql = 'coalesce(manual_evaluation_score.score,0)+coalesce(automatic_evaluation_score.score,0) desc, 2 asc';
                } elseif($select_score_left_join){
                    $order_by_sql = 'select_score.score3 desc, select_score.score2 desc, select_score.score1 desc, 2 asc';
                } else {
                    $order_by_sql = '2 asc';    
                }
                
        }

        $result = array();
        $res = $this->XM->sqlcore->query('SELECT user.user_id, coalesce(user_ml.user_ml_fullname,\'-\') as user_ml_fullname, user.user_iscompanyowner, user.company_id, '.$company_ml_select.', '.$favselect.', user.user_can_add_tasting, user.user_can_add_product,'.($show_expert_level?'user.user_expert_level,':'').'user.user_activity_time'.($select_score_sql?','.$select_score_sql:'').($evaluation_scores_select_sql?','.$evaluation_scores_select_sql:'').($global_expert_score_select_sql?','.$global_expert_score_select_sql:'').'
            from filteruserids
            inner join user on user.user_id = filteruserids.user_id
            left join (
                select user_ml.user_id,substring_index(group_concat(user_ml.user_ml_id order by user_ml.lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as user_ml_id 
                    from filteruserids2 
                    inner join user_ml on user_ml.user_id = filteruserids2.user_id and user_ml.user_ml_is_approved = 1
                    where user_ml.user_ml_fullname is not null 
                    group by user_ml.user_id
                ) as ln_glue on ln_glue.user_id = user.user_id
            left join user_ml on user_ml.user_ml_id = ln_glue.user_ml_id
            '.$company_ml_join.'
            '.$favjoin.'
            '.$global_expert_score_join.'
            '.$select_score_left_join.'
            '.$evaluation_scores_left_join.'
            '.($order_by_sql?'order by '.$order_by_sql:'').'
            limit '.$pagelimit.' offset '.(($page-1)*$pagelimit));
        $can_favourite = $this->XM->user->isLoggedIn();
        while($row = $this->XM->sqlcore->getRow($res)){
            $user_id = (int)$row['user_id'];
            $can_edit_add_rights = (bool)$this->__can_edit_add_rights($row['company_id']);
            $user_last_activity = $current_timestamp-(int)$row['user_activity_time'];
            $item = array(
                    'id'=>$user_id,
                    'name'=>(string)$row['user_ml_fullname'],
                    'is_owner'=>(bool)$row['user_iscompanyowner'],
                    'user_offline_time'=>($user_last_activity<\USER\ACTIVITY_TIME_STILL_ONLINE)?null:prettifyMinutes(floor($user_last_activity/60)),

                    'company_id'=>(int)$row['company_id'],
                    'company_name'=>(string)$row['company_ml_name'],

                    

                    'favourite'=>$row['favourite']?1:0,

                    'can_favourite'=>$can_favourite,
                    'can_dismiss'=>(bool)(!$row['user_iscompanyowner']&&$this->can_dismiss_from_company($row['company_id'])),
                    'can_edit'=>(bool)$this->can_edit_user($user_id),
                    'can_change_password'=>(bool)$this->can_change_password($user_id),

                    
                );
            if($show_expert_level){
                $item['expert_level'] = isset($expert_level_list[$row['user_expert_level']])?$expert_level_list[$row['user_expert_level']]:null;
                if(isset($row['global_expert_score']) && $row['global_expert_score'] > 0){
                    $item['global_expert_score'] = str_replace('.', ',', ((float)$row['global_expert_score'])/100);
                    $item['global_expert_count'] = (int)$row['global_expert_count'];
                }
            }
            if($can_edit_add_rights){
                $item['can_edit_add_rights']=1;
                $item['can_add_tasting']=$row['user_can_add_tasting']?1:0;
                $item['can_add_product']=$row['user_can_add_product']?1:0;
            }
            if($joinrequests_company_id){//the check is performed earlier in the code
                $item['can_approve_join_request'] = $item['can_reject_join_request'] = true;
            }
            if($only_participants_of_contest_id){
                $item['manual_evaluation_score'] = isset($row['manual_evaluation_score'])&&$row['manual_evaluation_score']>0?str_replace('.', ',', round($row['manual_evaluation_score'])/100):null;
                $item['automatic_evaluation_score'] = isset($row['automatic_evaluation_score'])&&$row['automatic_evaluation_score']>0?str_replace('.', ',', round($row['automatic_evaluation_score'])/100):null;
                if($only_participants_of_contest_product_id){
                    $item['score1'] = (isset($row['score1'])&&$row['score1']!==null)?str_replace('.', ',', ((float)$row['score1'])/100):null;
                    $item['score2'] = (isset($row['score2'])&&$row['score2']!==null)?str_replace('.', ',', ((float)$row['score2'])/100):null;
                    $item['score3'] = (isset($row['score3'])&&$row['score3']!==null)?str_replace('.', ',', ((float)$row['score3'])/100):null;
                }
            }
            
            $result[] = $item;
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filteruserids');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filteruserids2');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filteruserids3');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filteruserids_global_expert_score');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS only_participants_of_contest_evaluation_scores_manual');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS only_participants_of_contest_evaluation_scores_automatic');
        
        return $result;
    }

    // company
    private function validateITN($itn, $company_id, &$err){
        if(!preg_match('#^\d+$#',$itn)){
            $err = langTranslate('User', 'err', 'Invalid ITN!', 'Invalid ITN!');
            return false;  
        }
        $sitn = str_split($itn);
        if(count($sitn) == 10){
            if((int)$sitn[9] != ((2*$sitn[0] + 4*$sitn[1] + 10*$sitn[2] + 3*$sitn[3] + 5*$sitn[4] + 9*$sitn[5] + 4*$sitn[6] + 6*$sitn[7] + 8*$sitn[8]) % 11) % 10){
                $err = langTranslate('User', 'err', 'Invalid ITN!', 'Invalid ITN!');
                return false;
            }
        } else if(count($sitn) == 12){
            if( (int)$sitn[10] != ((7*$sitn[0] + 2*$sitn[1] + 4*$sitn[2] + 10*$sitn[3] + 3*$sitn[4] + 5*$sitn[5] + 9*$sitn[6] + 4*$sitn[7] + 6*$sitn[8] + 8*$sitn[9]) % 11) % 10 
            || (int)$sitn[11] != ((3*$sitn[0] +  7*$sitn[1] + 2*$sitn[2] + 4*$sitn[3] + 10*$sitn[4] + 3*$sitn[5] + 5*$sitn[6] +  9*$sitn[7] + 4*$sitn[8] + 6*$sitn[9] + 8*$sitn[10]) % 11) % 10 ){
                $err = langTranslate('User', 'err', 'Invalid ITN!', 'Invalid ITN!');
                return false;
            }
        }
        $res = $this->XM->sqlcore->query('SELECT 1 from company where company_itn = \''.$this->XM->sqlcore->prepString($itn,12).'\' and company_id != '.(int)$company_id);
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if($row){
            $err = langTranslate('User', 'err', 'Company with that ITN already exists', 'Company with that ITN already exists');
            return false;
        }
        return true;
    }
    public function register_company($itn, &$err){
        if($this->XM->user->isInCompany()){
            $err = langTranslate('user', 'err', 'You\'re already in a company',  'You\'re already in a company');
            return false;
        }
        if(strlen($itn)&&!$this->validateITN($itn,null,$err)){
            return false;
        }
        $this->XM->sqlcore->query('INSERT INTO company (company_itn, company_owner_user_id) VALUES (\''.$this->XM->sqlcore->prepString($itn,12).'\','.$this->XM->user->getUserId().')');
        $company_id = (int)$this->XM->sqlcore->lastInsertId();
        $this->XM->sqlcore->query('UPDATE user SET company_id = '.$company_id.',user_iscompanyowner = 1 where user_id = '.$this->XM->user->getUserId());
        $this->XM->sqlcore->commit();
        if($this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_COMPANY)){
            $dummy = null;
            $this->approve_company($company_id,$dummy);
        }
        $this->__refresh_states();
        return true;
    }
    public function can_edit_company($company_id){
        return $company_id==$this->getCompanyId()&&$this->isCompanyOwner()||$this->check_privilege(\USER\PRIVILEGE_EDIT_COMPANIES);
    }
    public function can_invite_users_to_company($company_id){
        return $company_id>0&&($company_id==$this->getCompanyId()&&$this->isCompanyOwner()||$this->check_privilege(\USER\PRIVILEGE_INVITE_TO_COMPANIES));
    }
    public function can_dismiss_from_company($company_id){
        return $company_id>0&&($company_id==$this->getCompanyId()&&$this->isCompanyOwner()||$this->check_privilege(\USER\PRIVILEGE_DISMISS_FROM_COMPANIES));
    }
    private function __can_edit_add_rights($company_id){
        return $company_id>0&&($company_id==$this->getCompanyId()&&$this->isCompanyOwner()&&$this->isCompanyApproved()||$this->check_privilege(\USER\PRIVILEGE_USER_EDIT_ADD_RIGHTS));
    }
    public function can_view_company_user_list($company_id){
        return $company_id==$this->getCompanyId()||$this->check_privilege(\USER\PRIVILEGE_SHOW_COMPANIES_USER_LISTS);
    }
    
    public function edit_company($company_id, $itn, $name, &$err){
        $company_id = (int)$company_id;
        if($company_id<=0){
            $err = langTranslate('user', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        if(!$this->can_edit_company($company_id)){
            $err = langTranslate('user', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        if(strlen($itn)&&!$this->validateITN($itn,$company_id,$err)){
            return false;
        }
        $languageIdList = $this->XM->lang->getLanguageIdList();
        $this->XM->sqlcore->query('UPDATE company set company_itn = \''.$this->XM->sqlcore->prepString($itn,12).'\' where company_id = '.$company_id.' and not company_itn <=> \''.$this->XM->sqlcore->prepString($itn,12).'\'');
        $this->XM->sqlcore->commit();
        $ml_variants = array();
        $res = $this->XM->sqlcore->query('SELECT company_ml_name, lang_id, company_ml_id, company_ml_is_approved from company_ml where company_id = '.$company_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $lang_id = (int)$row['lang_id'];
            if(!isset($ml_variants[$lang_id])){
                $ml_variants[$lang_id] = array();
            }
            $ml_variants[$lang_id][] = array('name'=>$row['company_ml_name'],'id'=>$row['company_ml_id'],'is_approved'=>(bool)$row['company_ml_is_approved']);
        }
        $this->XM->sqlcore->freeResult($res);
        foreach($languageIdList as $lang_id){
            $lang_name = getLangArrayVal($name,$lang_id);
            if(isset($ml_variants[$lang_id])){
                foreach($ml_variants[$lang_id] as $ml_variant){
                    if($lang_name==$ml_variant['name']){
                        if($ml_variant['is_approved']){//delete all edits
                            $this->XM->sqlcore->query('DELETE FROM company_ml where company_id = '.$company_id.' and lang_id = '.$lang_id.' and company_ml_is_approved <> 1');
                            $this->XM->sqlcore->commit();
                        } elseif($this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
                            $dummy = null;
                            $this->approve_company_translation($ml_variant['id'],$dummy);
                        }
                        continue 2;//same values, no need to insert/update
                    }
                }
            }
            $insertkeys = array();
            $insertvals = array();
            if(strlen($lang_name)){
                if(mb_strlen($lang_name, 'UTF-8')>128){
                    $err = formatReplace(langTranslate('user', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                        langTranslate('User', 'editCompanyForm', 'Name', 'Name'),
                        128);
                    return false;
                }
                $insertkeys[] = 'company_ml_name';
                $insertvals[] = '\''.$this->XM->sqlcore->prepString($lang_name,128).'\'';
            }
            if(empty($insertkeys)){
                continue;
            }
            $insertkeys[] = 'company_id';
            $insertvals[] = $company_id;
            $insertkeys[] = 'lang_id';
            $insertvals[] = $lang_id;
            $this->XM->sqlcore->query('DELETE FROM company_ml where company_id = '.$company_id.' and lang_id = '.$lang_id.' and company_ml_is_approved <> 1');
            $res = $this->XM->sqlcore->query('INSERT INTO company_ml ('.implode(',', $insertkeys).') VALUES ('.implode(',', $insertvals).')');
            $last_id = $this->XM->sqlcore->lastInsertId();
            $this->XM->sqlcore->commit();
            if($this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
                $dummy = null;
                $this->approve_company_translation($last_id,$dummy);
            }
        }
        return true;
    }
    public function approve_company_translation($id, &$err){
        $id = (int)$id;
        if($id <= 0){
            $err = langTranslate('user', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
            $err = langTranslate('user', 'err', 'You don\'t have a privilege to approve translations',  'You don\'t have a privilege to approve translations');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT company_id, lang_id from company_ml where company_ml_id = '.$id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('user', 'err', 'Translation doesn\'t exist',  'Translation doesn\'t exist');
            return false;
        }
        $this->XM->sqlcore->query('UPDATE company_ml set company_ml_approved_user_id = '.$this->XM->user->getUserId().', company_ml_approved_timestamp = '.time().', company_ml_is_approved=1 where company_ml_id = '.$id);
        $this->XM->sqlcore->query('DELETE FROM company_ml where company_id = '.$row['company_id'].' and lang_id = '.$row['lang_id'].' and company_ml_id <> '.$id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function approve_company($id, &$err){
        $id = (int)$id;
        if($id <= 0){
            $err = langTranslate('user', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_COMPANY)){
            $err = langTranslate('user', 'err', 'You don\'t have a privilege to approve companies',  'You don\'t have a privilege to approve companies');
            return false;
        }
        $this->XM->sqlcore->query('UPDATE company SET company_approved_user_id = '.$this->XM->user->getUserId().', company_approved_timestamp = '.time().', company_is_approved=1 where company_id = '.$id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function delete_company($id, &$err){
        $id = (int)$id;
        if($id <= 0){
            $err = langTranslate('user', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_DELETE_COMPANY)){
            $err = langTranslate('user', 'err', 'You don\'t have a privilege to delete companies',  'You don\'t have a privilege to delete companies');
            return false;
        }
        $this->XM->sqlcore->query('DELETE FROM company WHERE company_id = '.$id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function get_company_info_for_all_languages($company_id){
        $company_id = (int)$company_id;
        if($company_id<=0){//invalid company_id
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT company_itn, company_ml_name, company_ml.lang_id
            from company
            left join company_ml on company_ml.company_id = company.company_id and company_ml.company_ml_is_approved = 1
            where company.company_id = '.$company_id);
        $result = array(
                'itn'=>'',
                'name'=>array(),
            );
        $flag_first_iter = true;
        while($row = $this->XM->sqlcore->getRow($res)){
            if($flag_first_iter){
                $result['itn'] = (string)$row['company_itn'];
                $flag_first_iter = false;
            }
            $lang_id = (int)$row['lang_id'];
            $result['name'][$lang_id] = (string)$row['company_ml_name'];
        }
        $this->XM->sqlcore->freeResult($res);
        if($flag_first_iter){//company doesn't exist
            return false;
        }
        return $result;
    }
    public function get_company_info($company_id){
        $company_id = (int)$company_id;
        if($company_id<=0){//invalid user_id
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT company.company_id, company.company_is_approved, company_itn, coalesce(company_ml_name,\'-\') as company_ml_name, company_owner_user_id, user.user_id as owner_id, coalesce(user_ml.user_ml_fullname,\'-\') as owner_fullname, user.user_phone as owner_phone, user.user_login as owner_email
            from company
            inner join user on company.company_owner_user_id = user.user_id
            left join (select company_id,substring_index(group_concat(company_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as company_ml_id from company_ml where company_ml_is_approved = 1 group by company_id) as ln_glue on ln_glue.company_id = company.company_id
            left join company_ml on company_ml.company_ml_id = ln_glue.company_ml_id

            left join (select user_id,substring_index(group_concat(user_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as user_ml_id from user_ml where user_ml_is_approved = 1 and user_ml_fullname is not null group by user_id) as user_ln_glue on user_ln_glue.user_id = user.user_id
            left join user_ml on user_ml.user_ml_id = user_ln_glue.user_ml_id

            where company.company_id = '.$company_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//user doesn't exist
            return false;
        }
        $company_id = (int)$row['company_id'];
        return array(
                'id'=>$company_id,
                'itn'=>(string)$row['company_itn'],
                'name'=>(string)$row['company_ml_name'],

                'owner_id'=>(int)$row['owner_id'],
                'owner_name'=>(string)$row['owner_fullname'],
                'owner_email'=>(string)$row['owner_email'],

                'can_edit'=>(bool)$this->can_edit_company($company_id),
                'can_join'=>$this->isLoggedIn()&&!$this->isInCompany(),
                'can_delete'=>$this->check_privilege(\USER\PRIVILEGE_DELETE_COMPANY),
                'can_approve'=>!$row['company_is_approved']&&$this->check_privilege(\USER\PRIVILEGE_APPROVE_COMPANY),
            );
    }
    public function get_company_list($for_approvement = false){
        if($for_approvement&&!$this->check_privilege(\USER\PRIVILEGE_APPROVE_COMPANY)){
            return false;
        }
        if(!$for_approvement&&!$this->check_privilege(\USER\PRIVILEGE_VIEW_COMPANIES)){
            return false;
        }
        $companylist = array();
        $res = $this->XM->sqlcore->query('SELECT company.company_id, company.company_itn, coalesce(company_ml.company_ml_name,\'-\') as company_ml_name,company.company_is_approved,company.company_can_use_api
            from company
            left join (select company_id,substring_index(group_concat(company_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as company_ml_id from company_ml where company_ml_is_approved = 1 and company_ml_name is not null group by company_id) as ln_glue on ln_glue.company_id = company.company_id
            left join company_ml on company_ml.company_ml_id = ln_glue.company_ml_id
            where company_is_approved = '.($for_approvement?0:1));
        $canjoin = $this->isLoggedIn()&&!$this->isInCompany();
        $canapprove = $for_approvement&&$this->check_privilege(\USER\PRIVILEGE_APPROVE_COMPANY);
        $candelete = $this->check_privilege(\USER\PRIVILEGE_DELETE_COMPANY);
        while($row = $this->XM->sqlcore->getRow($res)){
            $company_id = (int)$row['company_id'];
            $companylist[] = array(
                    'id'=>$company_id,
                    'name'=>$row['company_ml_name'],
                    'itn'=>$row['company_itn'],

                    'can_use_api'=>(bool)$row['company_can_use_api'],
                    'can_edit'=>(bool)$this->can_edit_company($company_id),
                    'can_join'=>$canjoin,
                    'can_delete'=>$candelete,
                    'can_approve'=>$canapprove&&!$row['company_is_approved'],
                );
        }
        $this->XM->sqlcore->freeResult($res);
        return $companylist;
    }
    public function resolve_join_request($company_id, $user_id, $approve, &$err){
        $company_id = (int)$company_id;
        $user_id = (int)$user_id;
        if($company_id<=0){
            $err = langTranslate('user', 'err', 'Invalid ID',  'Invalid ID');
            return true;
        }
        if(!$this->XM->user->can_invite_users_to_company($company_id)){
            $err = langTranslate('user', 'err', 'Access Denied',  'Access Denied');
            return true;
        }
        if($user_id<=0){
            $err = langTranslate('user', 'err', 'Invalid ID',  'Invalid ID');
            return true;
        }
        if(!$approve){
            $this->XM->sqlcore->query('DELETE FROM company_join_requests WHERE company_id = '.$company_id.' and user_id = '.$user_id);
            $this->XM->sqlcore->commit();
            return true;
        }
        $res = $this->XM->sqlcore->query('SELECT user.user_id,user.company_id 
            from user 
            inner join company_join_requests on company_join_requests.user_id = user.user_id
            where company_join_requests.company_id = '.$company_id.' and user.user_id = '.$user_id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->query('DELETE FROM company_join_requests WHERE user_id = '.$user_id);
        $this->XM->sqlcore->commit();
        if(!$row){
            $err = langTranslate('user', 'err', 'Join request is outdated',  'Join request is outdated');
            return false;//invalid user/already deleted request
        }
        if($row['company_id']>0){
            if($row['company_id']==$company_id){
                return true;//user has already joined that company
            }
            $err = langTranslate('user', 'err', 'Join request is outdated',  'Join request is outdated');
            return false;//user has already joined another company
        }
        $this->XM->sqlcore->query('UPDATE user SET company_id = '.$company_id.' where user_id = '.$user_id.' and company_id is null');
        $this->XM->sqlcore->commit();
        return true;
    }
    public function dismiss_user($company_id, $user_id, &$err){
        $company_id = (int)$company_id;
        if($company_id<=0){
            $err = langTranslate('user', 'err', 'Invalid company ID',  'Invalid company ID');
            return true;
        }
        if(!$this->can_dismiss_from_company($company_id)){
            $err = langTranslate('user', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $user_id = (int)$user_id;
        if($user_id<=0){
            $err = langTranslate('user', 'err', 'Invalid user ID',  'Invalid user ID');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT user.user_id,user.company_id,user.user_iscompanyowner 
            from user 
            where user.user_id = '.$user_id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('user', 'err', 'User doesn\'t exist',  'User doesn\'t exist');
            return false;
        }
        if($row['company_id']!=$company_id){
            return true;//user is already dismissed from this company
        }
        if($row['user_iscompanyowner']){
            $err = langTranslate('user', 'err', 'Can\'t dismiss company owner',  'Can\'t dismiss company owner');
            return false;
        }
        $this->XM->sqlcore->query('UPDATE user SET company_id = null where user_id = '.$user_id.' and company_id = '.$company_id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function favourite_user($id,$to,&$err){
        if(!$this->XM->user->isLoggedIn()){
            $err = langTranslate('user', 'err', 'You\'re not logged in',  'You\'re not logged in');
            return false;
        }
        $id = (int)$id;
        if(!$to){
            $this->XM->sqlcore->query('DELETE FROM user_favourite where uf_user_id = '.$id.' and user_id = '.$this->XM->user->getUserId());
            $this->XM->sqlcore->commit();
        } else {
            $res = $this->XM->sqlcore->query('SELECT 1 FROM user_favourite where uf_user_id = '.$id.' and user_id = '.$this->XM->user->getUserId().' limit 1');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if($row){//already favourited
                return true;
            }
            $this->XM->sqlcore->query('INSERT INTO user_favourite (uf_user_id,user_id) VALUES ('.$id.','.$this->XM->user->getUserId().')');
            $this->XM->sqlcore->commit();
        }
        return true;
    }
    public function check_user_exists($user_id){
        $res = $this->XM->sqlcore->query('SELECT 1 from user where user_id = '.((int)$user_id).' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        return true;
    }
    public function get_company_autoinvite_code($company_id){
        $company_id = (int)$company_id;
        if($company_id<=0){//invalid user_id
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT cai_code from company_autoinvite where company_id = '.$company_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        return (string)$row['cai_code'];
    }
    public function get_company_id_from_autoinvite_code($code){
        $res = $this->XM->sqlcore->query('SELECT company_id from company_autoinvite where cai_checksum = '.$this->XM->sqlcore->checksum($code).' and cai_code = \''.$this->XM->sqlcore->prepString($code,32).'\' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        return (int)$row['company_id'];
    }
    public function company_enable_autoinvite($tostate, $company_id, &$err){
        if(!$this->can_invite_users_to_company($company_id)){
            $err = langTranslate('user', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $tostate = (bool)$tostate;
        $company_id = (int)$company_id;
        if($company_id<=0){//invalid user_id
            $err = langTranslate('user', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        if($tostate==(bool)$this->XM->user->get_company_autoinvite_code($company_id)){
            return true;
        }
        if($tostate){
            $code = $this->__encodeLI($company_id,time());
            $this->XM->sqlcore->query('INSERT INTO company_autoinvite (company_id,cai_code,cai_checksum) VALUES ('.$company_id.',\''.$this->XM->sqlcore->prepString($code,32).'\','.$this->XM->sqlcore->checksum($code).')');
            $this->XM->sqlcore->commit();
        } else {
            $this->XM->sqlcore->query('DELETE FROM company_autoinvite WHERE company_id = '.$company_id);
            $this->XM->sqlcore->commit();
        }
        return true;
    }
    public function request_join_company($company_id, &$err){
        $company_id = (int)$company_id;
        if($company_id<=0){//invalid user_id
            $err = langTranslate('user', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        if(!$this->isLoggedIn()){
            $err = langTranslate('user', 'err', 'You\'re not logged in',  'You\'re not logged in');
            return false;
        }
        if($this->isInCompany()){
            $err = langTranslate('user', 'err', 'You\'re already in a company',  'You\'re already in a company');
            return false;
        }
        $this->XM->sqlcore->query('INSERT INTO company_join_requests (company_id, user_id)
            SELECT '.$company_id.' as company_id, '.$this->XM->user->getUserId().' as user_id FROM company_join_requests where company_id = '.$company_id.' and user_id = '.$this->XM->user->getUserId().' having count(1) = 0');
        $this->XM->sqlcore->commit();
        return true;
    }
    public function upload_company_mail_settings_image($company_id, $type, $tmp_name, $size, $name, &$err){
        $company_id = (int)$company_id;
        if(!($company_id==$this->getCompanyId()&&$this->isCompanyOwner()||$this->check_privilege(\USER\PRIVILEGE_EDIT_COMPANIES))){
            $err = langTranslate('user', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $type = ($type=='logo')?1:0;
        if($size>50*1024){
            $err = formatReplace(langTranslate('main', 'err', 'Size of @1 exceeds limit of @2 kilobytes',  'Size of @1 exceeds limit of @2 kilobytes'),
                    $name,
                    50);
            return false;
        }
        $ext = strtolower(substr($name, strrpos($name,'.')+1,strlen($name)));
        $valid_exts = array('jpg','jpeg');
        if(!in_array($ext, $valid_exts)){
            $err = formatReplace(langTranslate('main', 'err', 'Invalid image type for file @1. Supported types: @2',  'Invalid image type for file @1. Supported types: @2'),
                    $name,
                    implode(', ', $valid_exts));
            return false;
        }
        list($width, $height) = getimagesize($tmp_name);
        if($type){
            if($width>700||$height>43){
                $err = formatReplace(langTranslate('main', 'err', 'Invalid image dimensions. Required dimensions for @1 are @2',  'Invalid image dimensions. Required dimensions for @1 are @2'),
                    langTranslate('User', 'companyMailSettingsForm', 'Logo', 'Logo'),
                    '700x43');
                return false;
            }
        } else {
            if($width>700&&$height>26){
                $err = formatReplace(langTranslate('main', 'err', 'Invalid image dimensions. Required dimensions for @1 are @2',  'Invalid image dimensions. Required dimensions for @1 are @2'),
                    langTranslate('User', 'companyMailSettingsForm', 'Small logo', 'Small logo'),
                    '700x26');
                return false;
            }
        }
        $path = '/modules/Sendmail/cimg/'.($type?'logo':'logo-small').'-'.$company_id.'-temp.jpg';
        if (!move_uploaded_file($tmp_name, ABS_PATH.$path)){
            $err = formatReplace(langTranslate('main', 'err', 'Upload error (@2) for file @1',  'Upload error (@2) for file @1'),
                    $name,
                    '-89');
            $this->XM->sqlcore->rollback();
            return false;
        }
        return $path;
    }
    public function set_mail_settings($company_id, $header_logo_url, $footer_logo_url, $text_color, $anchor_color, $header_background_color, $footer_background_color, &$err){
        $company_id = (int)$company_id;
        if(!($company_id==$this->getCompanyId()&&$this->isCompanyOwner()||$this->check_privilege(\USER\PRIVILEGE_EDIT_COMPANIES))){
            $err = langTranslate('user', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $all_default = true;
        $res = $this->XM->sqlcore->query('SELECT company.company_id,company_mail_settings.cms_header_logo_image_type,company_mail_settings.cms_footer_logo_image_type,company_mail_settings.cms_text_color,company_mail_settings.cms_anchor_color,company_mail_settings.cms_header_background_color,company_mail_settings.cms_footer_background_color from company left join company_mail_settings on company_mail_settings.company_id = company.company_id where company.company_id = '.$company_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//company doesn't exist
            $err = langTranslate('user', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        $need_insert = false;
        if($row['cms_header_logo_image_type']===null){
            $row = array('company_id'=>$company_id,'cms_header_logo_image_type'=>2,'cms_footer_logo_image_type'=>2,'cms_text_color'=>null,'cms_anchor_color'=>null);
            $need_insert = true;
        }
        
        if($need_insert){
            $insert_arr = array();    
        } else {
            $update_arr = array();    
        }
        $default_mail_settings = $this->get_default_mail_settings();
        
        $image_type = 2;
        if($header_logo_url==$default_mail_settings['header_logo_url']){
            $image_type = 2;
        } elseif($header_logo_url=='/modules/Sendmail/cimg/header-logo-'.$company_id.'-temp.jpg'){
            $image_type = 1;
            if(!file_exists(ABS_PATH.'/modules/Sendmail/cimg/header-logo-'.$company_id.'-temp.jpg')){
                $err = langTranslate('user', 'err', 'Internal Error',  'Internal Error');
                return false;
            }
            if(!@rename(ABS_PATH.'/modules/Sendmail/cimg/header-logo-'.$company_id.'-temp.jpg', ABS_PATH.'/modules/Sendmail/cimg/header-logo-'.$company_id.'.jpg')){
                $err = langTranslate('user', 'err', 'Internal Error',  'Internal Error');
                return false;
            }
        } elseif($header_logo_url=='/modules/Sendmail/cimg/header-logo-'.$company_id.'.jpg'){
            $image_type = 1;
        } else {
            $image_type = 0;
        }
        if($all_default && $image_type!=2){
            $all_default = false;
        }
        if($row['cms_header_logo_image_type']!=$image_type){
            if($need_insert){
                $insert_arr['cms_header_logo_image_type'] = $image_type;
            } else {
                $update_arr[] = 'cms_header_logo_image_type = '.$image_type;
            }
        }

        if($footer_logo_url==$default_mail_settings['footer_logo_url']){
            $image_type = 2;
        } elseif($footer_logo_url=='/modules/Sendmail/cimg/footer-logo-'.$company_id.'-temp.jpg'){
            $image_type = 1;
            if(!file_exists(ABS_PATH.'/modules/Sendmail/cimg/footer-logo-'.$company_id.'-temp.jpg')){
                $err = langTranslate('user', 'err', 'Internal Error',  'Internal Error');
                return false;
            }
            if(!@rename(ABS_PATH.'/modules/Sendmail/cimg/footer-logo-'.$company_id.'-temp.jpg', ABS_PATH.'/modules/Sendmail/cimg/footer-logo-'.$company_id.'.jpg')){
                $err = langTranslate('user', 'err', 'Internal Error',  'Internal Error');
                return false;
            }
        } elseif($footer_logo_url=='/modules/Sendmail/cimg/footer-logo-'.$company_id.'.jpg'){
            $image_type = 1;
        } else {
            $image_type = 0;
        }
        if($all_default && $image_type!=2){
            $all_default = false;
        }
        if($row['cms_footer_logo_image_type']!=$image_type){
            if($need_insert){
                $insert_arr['cms_footer_logo_image_type'] = $image_type;
            } else {
                $update_arr[] = 'cms_footer_logo_image_type = '.$image_type;
            }
        }
        $text_color = strtolower($text_color);
        if($text_color==$default_mail_settings['text_color']){
            $text_color = null;
        } else {
            $all_default = false;
        }
        if($text_color!==$row['cms_text_color']){
            if($text_color!==null && !preg_match('#^[0-9a-f]{6}$#',$text_color)){
                $err = formatReplace(langTranslate('user', 'err', 'Invalid value of @1',  'Invalid value of @1'),
                       langTranslate('User', 'companyMailSettingsForm', 'Text color', 'Text color'));
                return false;
            }
            if($need_insert){
                if($text_color!==null){
                    $insert_arr['cms_text_color'] = '\''.$this->XM->sqlcore->prepString($text_color,6).'\'';
                } else {
                    $insert_arr['cms_text_color'] = 'null';
                }
            } else {
                if($text_color!==null){
                    $update_arr[] = 'cms_text_color = \''.$this->XM->sqlcore->prepString($text_color,6).'\'';
                } else {
                    $update_arr[] = 'cms_text_color = null';
                }
            }
        }
        
        $anchor_color = strtolower($anchor_color);
        if($anchor_color==$default_mail_settings['anchor_color']){
            $anchor_color = null;
        } else {
            $all_default = false;
        }
        if($anchor_color!==$row['cms_anchor_color']){
            if($anchor_color!==null && !preg_match('#^[0-9a-f]{6}$#',$anchor_color)){
                $err = formatReplace(langTranslate('user', 'err', 'Invalid value of @1',  'Invalid value of @1'),
                       langTranslate('User', 'companyMailSettingsForm', 'Anchor color', 'URL color'));
                return false;
            }
            if($need_insert){
                if($anchor_color!==null){
                    $insert_arr['cms_anchor_color'] = '\''.$this->XM->sqlcore->prepString($anchor_color,6).'\'';
                } else {
                    $insert_arr['cms_anchor_color'] = 'null';
                }
            } else {
                if($anchor_color!==null){
                    $update_arr[] = 'cms_anchor_color = \''.$this->XM->sqlcore->prepString($anchor_color,6).'\'';
                } else {
                    $update_arr[] = 'cms_anchor_color = null';
                }
            }
        }

        $header_background_color = strtolower($header_background_color);
        if($header_background_color==$default_mail_settings['header_background_color']){
            $header_background_color = null;
        } else {
            $all_default = false;
        }
        if($header_background_color!==$row['cms_header_background_color']){
            if($header_background_color!==null && !preg_match('#^[0-9a-f]{6}$#',$header_background_color)){
                $err = formatReplace(langTranslate('user', 'err', 'Invalid value of @1',  'Invalid value of @1'),
                       langTranslate('User', 'companyMailSettingsForm', 'Header background color', 'Header background color'));
                return false;
            }
            if($need_insert){
                if($header_background_color!==null){
                    $insert_arr['cms_header_background_color'] = '\''.$this->XM->sqlcore->prepString($header_background_color,6).'\'';
                } else {
                    $insert_arr['cms_header_background_color'] = 'null';
                }
            } else {
                if($header_background_color!==null){
                    $update_arr[] = 'cms_header_background_color = \''.$this->XM->sqlcore->prepString($header_background_color,6).'\'';
                } else {
                    $update_arr[] = 'cms_header_background_color = null';
                }
            }
        }

        $footer_background_color = strtolower($footer_background_color);
        if($footer_background_color==$default_mail_settings['footer_background_color']){
            $footer_background_color = null;
        } else {
            $all_default = false;
        }
        if($footer_background_color!==$row['cms_footer_background_color']){
            if($footer_background_color!==null && !preg_match('#^[0-9a-f]{6}$#',$footer_background_color)){
                $err = formatReplace(langTranslate('user', 'err', 'Invalid value of @1',  'Invalid value of @1'),
                       langTranslate('User', 'companyMailSettingsForm', 'Header background color', 'Footer background color'));
                return false;
            }
            if($need_insert){
                if($footer_background_color!==null){
                    $insert_arr['cms_footer_background_color'] = '\''.$this->XM->sqlcore->prepString($footer_background_color,6).'\'';
                } else {
                    $insert_arr['cms_footer_background_color'] = 'null';
                }
            } else {
                if($footer_background_color!==null){
                    $update_arr[] = 'cms_footer_background_color = \''.$this->XM->sqlcore->prepString($footer_background_color,6).'\'';
                } else {
                    $update_arr[] = 'cms_footer_background_color = null';
                }
            }
        }


        if($need_insert){
            if($all_default){
                return true;
            } else {
                $this->XM->sqlcore->query('INSERT INTO company_mail_settings (company_id,'.implode(',',array_keys($insert_arr)).') VALUES ('.$company_id.','.implode(',', $insert_arr).')');
            }
        } else {
            if($all_default){
                $this->XM->sqlcore->query('DELETE FROM company_mail_settings WHERE company_id = '.$company_id);
            } else {
                if(empty($update_arr)){
                    return true;
                }
                $this->XM->sqlcore->query('UPDATE company_mail_settings SET '.implode(',', $update_arr).' WHERE company_id = '.$company_id);
            }
        }
        $this->XM->sqlcore->commit();
        return true;
    }
    public function get_mail_settings($company_id){
        $company_id = (int)$company_id;
        $default_mail_settings = $this->get_default_mail_settings();
        $res = $this->XM->sqlcore->query('SELECT cms_header_logo_image_type,cms_footer_logo_image_type,cms_text_color,cms_anchor_color,cms_header_background_color,cms_footer_background_color from company_mail_settings where company_id = '.$company_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return $default_mail_settings;
        }
        $header_logo_url = '';
        switch($row['cms_header_logo_image_type']){
            case 0:
                $header_logo_url = '';
                break;
            case 1:
                $header_logo_url = '/modules/Sendmail/cimg/header-logo-'.$company_id.'.jpg';
                break;
            case 2:
                $header_logo_url = $default_mail_settings['header_logo_url'];
                break;
            default:
                return $default_mail_settings;
        }
        $footer_logo_url = '';
        switch($row['cms_footer_logo_image_type']){
            case 0:
                $footer_logo_url = '';
                break;
            case 1:
                $footer_logo_url = '/modules/Sendmail/cimg/footer-logo-'.$company_id.'.jpg';
                break;
            case 2:
                $footer_logo_url = $default_mail_settings['footer_logo_url'];
                break;
            default:
                return $default_mail_settings;
        }
        return array(
                'header_logo_url'=>$header_logo_url,
                'footer_logo_url'=>$footer_logo_url,
                'text_color'=>$row['cms_text_color']!==null?$row['cms_text_color']:$default_mail_settings['text_color'],
                'anchor_color'=>$row['cms_anchor_color']!==null?$row['cms_anchor_color']:$default_mail_settings['anchor_color'],
                'header_background_color'=>$row['cms_header_background_color']!==null?$row['cms_header_background_color']:$default_mail_settings['header_background_color'],
                'footer_background_color'=>$row['cms_footer_background_color']!==null?$row['cms_footer_background_color']:$default_mail_settings['footer_background_color'],
            );
    }
    public function get_default_mail_settings(){
        return array(
                'header_logo_url'=>'/modules/Sendmail/img/header-logo.jpg',
                'footer_logo_url'=>'/modules/Sendmail/img/footer-logo.jpg',
                'text_color'=>'4d4d4d',
                'anchor_color'=>'0074a2',
                'header_background_color'=>'ffffff',
                'footer_background_color'=>'000000',
            );
    }
    public function get_api_access_login($company_id){
        return 'ca'.(int)$company_id;
    }
    public function get_company_id_from_api_access_login($api_access_login){
        if(preg_match('#^ca(\d+)$#', $api_access_login, $match)){
            return (int)$match[1];
        }
        return false;
    }
    public function company_has_api_access($company_id){
        $res = $this->XM->sqlcore->query('SELECT company_can_use_api from company where company_id = '.$company_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            // $err = langTranslate('user', 'err', 'Company doesn\'t exist',  'Company doesn\'t exist');
            return false;
        }
        if(!$row['company_can_use_api']){
            // $err = langTranslate('user', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        return true;
    }
    public function company_change_api_access($company_id,$grant,&$err){
        $company_id = (int)$company_id;
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_COMPANY)){
            $err = langTranslate('user', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT company_can_use_api from company where company_id = '.$company_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('user', 'err', 'Company doesn\'t exist',  'Company doesn\'t exist');
            return false;
        }
        if((bool)$grant==(bool)$row['company_can_use_api']){
            return true;
        }
        $this->XM->sqlcore->query('UPDATE company set company_can_use_api = '.($grant?1:0).' where company_id = '.$company_id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function set_api_access_password($company_id, $password, &$err){
        $company_id = (int)$company_id;
        $password = trim($password);
        if(!($company_id==$this->getCompanyId()&&$this->isCompanyOwner()||$this->check_privilege(\USER\PRIVILEGE_EDIT_COMPANIES))){
            $err = langTranslate('user', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        if(!$this->company_has_api_access($company_id)){
            $err = langTranslate('user', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $this->XM->sqlcore->query('UPDATE company set company_api_password = \''.$this->XM->sqlcore->prepString($this->__encodeLI($this->get_api_access_login($company_id),$password),32).'\' where company_id = '.$company_id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function check_api_access($company_id, $password){
        $company_id = (int)$company_id;
        $password = trim($password);
        $res = $this->XM->sqlcore->query('SELECT 1 from company where company_id = '.$company_id.' and company_can_use_api = 1 and company_api_password = \''.$this->XM->sqlcore->prepString($this->__encodeLI($this->get_api_access_login($company_id),$password),32).'\' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        return true;
    }

    

}