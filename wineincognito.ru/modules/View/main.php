<?php
namespace View;
if(!defined('IS_XMODULE')){
    exit();
}
require_once ABS_PATH.'/interface/main.php';
class Main extends \AbstractNS\Main{
    protected $closureView;

    function __construct(){
        parent::__construct();
        $this->closureView = function($__path__, $__vars__){
            if(is_array($__vars__) && count($__vars__)){
                extract($__vars__);    
            }
            include $__path__;
            return true;
        };
        $this->closureView = $this->closureView->bindTo(null);
    }
    public function load($name, $vars = array(), $return = false){
        $slashpos = strpos($name, '/');
        if($slashpos===FALSE){
            // throw new \Exception('Invalid view name: '.$name);
            return false;
        }
        $modulename = $this->XM->getModuleCaseSensitiveName(substr($name, 0, $slashpos));
        if(!$modulename){
            return false;
        }
        $this->XM->lang->setDefault($modulename);
        $viewname = strtolower(substr($name, $slashpos+1, strlen($name)));
        $path = ABS_PATH.'/modules/'.$modulename.'/view/'.$viewname.'.php';
        if(!file_exists($path)){
            // throw new \Exception('View file not found: '.$name);
            return false;
        }

        return $return?$this->loadReturn($path, $vars):$this->loadPaste($path, $vars);
    }
    private function loadReturn($path, $vars){
        ob_start();
        $this->loadPaste($path, $vars);
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }
    private function loadPaste($path, $vars){
        return call_user_func($this->closureView, $path, $vars);
    }
}
