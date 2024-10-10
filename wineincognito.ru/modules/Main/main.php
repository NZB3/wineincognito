<?php
namespace Main;
if(!defined('IS_XMODULE')){
    exit();
}

require_once ABS_PATH.'/interface/main.php';
require_once ABS_PATH.'/interface/interfaces.php';
require_once ABS_PATH.'/interface/blankinterface.php';

class Main extends \AbstractNS\Main{
    private $__moduleNames;
    private $__blankInterface;
    public  $__UI;
    private $__models;
    private $__messagelog = array();
    private $__uri;
    private $__routes;
    private $__pushStateUrl = null;
    private $log_process_id;
    function __construct(){
        $this->XM = &$this;
        $this->__UI = new \AbstractNS\Interfaces($this, 'UserInterface', 'userinterface', true);
        $this->__models = new \AbstractNS\Interfaces($this, 'Main', 'main', true);
        $this->__blankInterface = new \AbstractNS\BlankInterface();
        $this->__loadModuleCache();
    }
    public function getLogProcessId(){
        if(!$this->log_process_id){
            $this->log_process_id = mt_rand(10000,99999);
        }
        return $this->log_process_id;
    }

    private function flushDirectory($dirPath, $erase = false, $check = true) {
        if($check){
            if(strpos($dirPath, ABS_PATH)!==0){
                return false;
            }
            $relDirPath = mb_substr($dirPath, mb_strlen(ABS_PATH, 'UTF-8'), mb_strlen($dirPath, 'UTF-8'), 'UTF-8');
            if(preg_match('#[\/]\.{1,2}[\/]#', $relDirPath)){
                return false;
            }
            if(preg_match('#[\/][\/]#', $relDirPath)){
                return false;
            }
        }
        if (is_dir($dirPath)){
            $objects = scandir($dirPath);
            foreach ($objects as $object){
                if(in_array($object, array('.','..'))){
                    continue;
                }
                if (is_dir($dirPath.$object)){
                    $this->flushDirectory($dirPath.$object.'/', true, false);
                } else {
                    @unlink($dirPath.$object);
                }
            }
            reset($objects);
            if($erase){
                @rmdir($dirPath);
            }
        }
        return true;
    }
    private function __loadModuleNames(){
        if(!is_dir(ABS_PATH.'/modules/')){
            exit('Internal structure error, modules not found');
        }
        $this->__moduleNames = array();
        $dir = dir(ABS_PATH.'/modules/');
        while(false !== ($module = $dir->read())){
            if(!is_dir(ABS_PATH.'/modules/'.$module) || !$this->__validateModuleName($module) || !file_exists(ABS_PATH.'/modules/'.$module.'/main.php')){
                continue;
            }
            $this->__moduleNames[strtolower($module)] = $module;
        }
        $dir->close();
        @file_put_contents(ABS_PATH.'/modules/Main/cache/moduleCache.ag.php', $this->generateArrayInclude(array('mn'=>$this->__moduleNames)));
    }
    private function __loadModuleCache(){
        $mn = array();
        if(file_exists(ABS_PATH.'/modules/Main/cache/moduleCache.ag.php')){
            include ABS_PATH.'/modules/Main/cache/moduleCache.ag.php';
        }
        if(empty($mn)){
            $this->__loadModuleNames();
            return;
        }
        $this->__moduleNames = $mn;
    }

    private function __generateVarCode($var){
        if(is_array($var)){
            $iter = 0;
            $numeric_arr = true;
            $array_vars = array();
            foreach($var as $key=>$val){
                if($numeric_arr && $key===$iter++){
                    $array_vars[]=$this->__generateVarCode($val);
                } else {
                    $numeric_arr = false;
                    $array_vars[]=$this->__generateVarCode($key).'=>'.$this->__generateVarCode($val);
                }
            }
            return 'array('.implode(',',$array_vars).')';
        } else {
            if(is_numeric($var)){
                return $var;
            } else if($var===null) {
                return 'null';
            } else {
                return '\''.str_replace(array('\\','\''), array('\\\\','\\\''), (string)$var).'\'';
            }
        }
    }
    public function generateArrayInclude($vars){
        $code = '<?php ';
        foreach($vars as $key=>$val){
            $code .= '$'.(string)$key.'='.$this->__generateVarCode($val).';';
        }
        return $code;
    }
    public function getModuleCaseSensitiveName($moduleName){
        $moduleName = strtolower($moduleName);
        if(!isset($this->__moduleNames[$moduleName])){
            return null;
        }
        return $this->__moduleNames[$moduleName];
    }
    public function moduleExists($moduleName){
        $moduleName = strtolower($moduleName);
        if(!isset($this->__moduleNames[$moduleName])){
            return false;
        }
        return true;
    }
    public function __validateModuleName($moduleName){
        if(strlen($moduleName)>0 && preg_match('#^[A-Za-z]+$#', $moduleName)){
            return true;
        }
        return false;
    }
    public function __get($name){
        $this->{$name} = $this->__models->{$name};
        $this->{$name}->__init();
        return $this->{$name};
    }

    public function __404(){
        if(!headers_sent()){
            header("HTTP/1.0 404 Not Found");
            return $this->__UI->main->e404();
        }
        return true;
    }
    public function __401(){
        if(!headers_sent()){
            header("HTTP/1.1 401 Unauthorized");
        }
        return true;
    }
    private function __checkagerestrict($uri){
        if($uri=='/main/ajax_keepalive'){//ignore keep alive
            return true;
        }
        if($uri=='/api/api'){//ignore api
            return true;
        }
        if(!isset($_COOKIE)||!isset($_COOKIE['majority'])||$_COOKIE['majority']!=1){
            return false;
        }
        return true;
    }
    public function __start(){
        $className;
        $pathComponents = array();
        $this->uri = $_SERVER['REQUEST_URI'];
        if(preg_match('#^[\/]([^\/]+)([\/].+)?#',$this->uri,$match)){
            if($this->XM->lang->setCurrLangByCode($match[1])){
                $this->uri = isset($match[2])&&strlen($match[2])?$match[2]:'/';
            }
        }
        $uri = $this->uri;
        if(preg_match('#^[^?\#]+#',$uri,$match)){
            $uri = $match[0];
        }
        
        if(file_exists(ABS_PATH.'/modules/Main/routes.php')){
            require ABS_PATH.'/modules/Main/routes.php';
            $count = 0;
            foreach($routes as $regexr=>$replace){
                $uri = preg_replace('#^'.$regexr.'[\/]?$#',$replace,$uri,1,$count);
                if($count){
                    break;
                }
            }
        }
        if(!preg_match_all('#[\/]?([^\/]+)[\/]?#', $uri, $matches)){
            $className = 'main';
        } else {
            $firstMatch = $matches[1][0];
            if($this->XM->lang->setCurrLangByCode($firstMatch)){
                $i = 2;
                $className = $matches[1][1];
            } else {
                $i = 1;
                $className = $matches[1][0];    
            }
            if(count($matches[1])>$i){
                for(;$i<count($matches[1]);$i++){
                    $pathComponents[] = $matches[1][$i];
                }
            }
        }
        if(!$this->__validateModuleName($className)){
            return $this->__404();
        }
        if(!$this->__checkagerestrict($uri)){
            return $this->__UI->main->agerestrict();
        }
        $this->XM->user->process_login();
        $res = $this->__UI->{$className}->__act($pathComponents);
        if(!$res){
            if($res===false && !$this->XM->user->isLoggedIn()){
                return $this->__UI->user->login();
            }
            return $this->__404();
        }
        return $res;
    }
    private $session_status;
    public function session_start(){
        if(session_status() != PHP_SESSION_NONE){
            return true;
        }
        if(!headers_sent()){
            session_start();
            return true;
        }
        return false;
    }
    public function getSessionVar($key, $default = null){
        $this->session_start();
        if(isset($_SESSION) && isset($_SESSION[$key])){
            return $_SESSION[$key];
        }
        return $default;
    }
    public function setSessionVar($key, $val){
        $this->session_start();
        if(isset($_SESSION)){
            if($val!==null){
                $_SESSION[$key] = $val;
            } else {
                if(isset($_SESSION[$key])){
                    unset($_SESSION[$key]);    
                }
            }
        }
    }
    
    public function addMessage($msgtext, $msgtype=0, $admin = false){
        if(!strlen($msgtext)){
            return false;
        }
        //log
        $class = '';
        switch($msgtype){
            case 0:
                $class = 'ERROR';
                break;
            case 1:
                $class = 'WARNING';
                break;
            case 2:
                $class = 'SUCCESS';
                break;
            default:
                $class = '';
        }
        if($admin){
            @file_put_contents(LOG_DIRECTORY.'/messagelog_'.date('d-m-Y').'.log', '['.date('H:i:s').'] '.$this->getLogProcessId().' '.$class.': '.$msgtext."\n", FILE_APPEND);
        }
        if($admin && !$this->user->isAdmin()){
            return false;
        }
        $this->__messagelog[] = array($msgtext, $msgtype);
    }
    public function getMessageLog(){
        return $this->__messagelog;
    }
    public function setPushStateUrl($url){
        $this->__pushStateUrl = $url;
        $_SERVER['REQUEST_URI'] = preg_replace('#^https?://[^/]+#', '', $url);
    }
    public function __getCurrUrlForLangCode($langCode){
        $langCodes = &$this->XM->lang->getLangCodes();
        return preg_replace('#^(?:/(?:'.implode('|',$langCodes).'))?(.+)$#','/'.$langCode.'$1',$_SERVER['REQUEST_URI']);
    }
    public function __wrapview($content, $title = null, $includes = array(), $showmenu = true, $showsearchbar = true){
        $includejs = array('/modules/Main/js/jquery.min.js','/modules/Main/js/script.js');
        $includecss = array('/modules/Main/css/style.css');
        if(is_array($includes)){
            if(isset($includes['pack']) && is_array($includes['pack'])){
                if(in_array('datepicker', $includes['pack'])){
                    $includecss[] = '/modules/Main/css/redmond/jquery-ui-1.10.4.min.css';
                    $includejs[] = '/modules/Main/js/jquery-ui-1.10.4.min.js';
                    switch($this->XM->lang->getCurrLangId()){
                        case 2:
                            $includejs[] = '/modules/Main/js/jquery.ui.datepicker.ml.ru.js';
                            break;
                        case 1:
                        default:
                            $includejs[] = '/modules/Main/js/jquery.ui.datepicker.ml.en.js';
                    }
                }
                if(in_array('mask', $includes['pack'])){
                    $includejs[] = '/modules/Main/js/jquery.mask.min.js';
                }
                if(in_array('gallery', $includes['pack'])){
                    $includejs[] = '/modules/Main/js/gallery.js';
                    $includecss[] = '/modules/Main/css/gallery.css';
                }
                if(in_array('dropbox', $includes['pack'])){
                    $includejs[] = '/modules/Main/js/dropbox.js';
                    $includecss[] = '/modules/Main/css/dropbox.css';
                }
                if(in_array('filterform', $includes['pack'])){
                    $includejs[] = '/modules/Main/js/filterform.js';
                    $includecss[] = '/modules/Main/css/filterform.css';
                }
                if(in_array('tabcontent', $includes['pack'])){
                    $includejs[] = '/modules/Main/js/tabcontent.js';
                    $includecss[] = '/modules/Main/css/tabcontent.css';
                }
                
                $includecss = array_unique($includecss);
                $includejs = array_unique($includejs);
            }
            if(isset($includes['css']) && is_array($includes['css'])){
                foreach($includes['css'] as $include){
                    $includecss[] = (string)$include;
                }
                $includecss = array_unique($includecss);
            }
            if(isset($includes['js']) && is_array($includes['js'])){
                foreach($includes['js'] as $include){
                    $includejs[] = (string)$include;
                }
                $includejs = array_unique($includejs);
            }
        }
        
        if(!strlen($title)){
            $title = 'Wine Incognito';
        }
        $language_selector = array();
        $languageList = $this->XM->lang->getLanguageListForWrap();
        foreach($languageList as $language){
            $language_selector[] = array('url'=>$this->__getCurrUrlForLangCode($language['code']),'name'=>$language['name'],'current'=>$language['current']);
        }
        $menu = null;
        $submenu = null;
        if($showmenu){
            $menu = $this->XM->menu->getMenuHtml();
            $submenu = $this->XM->menu->getSubMenuHtml();
        }
        $this->XM->view->load('Main/wrap', array('content'=>$content, 'messagelog'=>$this->getMessageLog(),'menu'=>$menu, 'submenu'=>$submenu, 'showsearchbar'=>$showsearchbar, 'language_selector'=>$language_selector, 'title'=> $title, 'includejs'=>$includejs, 'includecss'=>$includecss, 'pushStateUrl'=>$this->__pushStateUrl, 'curLangId'=>$this->XM->lang->getCurrLangId()), false);
        return true;
    }
}