<?php
namespace AbstractNS;
if(!defined('IS_XMODULE')){
    exit();
}
require_once ABS_PATH.'/interface/blankinterface.php';
class Interfaces{
    private $__XM;
    private $__blankInterface;
    private $__className;
    private $__classFileName;
    private $__onlyActive;

    function __construct($__XM, $className, $classFileName, $onlyActive){
        $this->__XM = $__XM;
        $this->__blankInterface = new \AbstractNS\BlankInterface();
        $this->__className = $className;
        $this->__classFileName = $classFileName;
        $this->__onlyActive = $onlyActive;
        if($this->__className=='Main'){
            $this->main = $__XM;
        }
    }
    protected function __clone(){}//blocking object cloning

    public function __get($name){
        if(!$name){
            return $this->__blankInterface;
        }
        $name = strtolower($name);
        if(property_exists($this, $name)){
            return $this->{$name};
        }
        $moduleName = $this->__XM->getModuleCaseSensitiveName($name, $this->__onlyActive);
        if($moduleName && file_exists(ABS_PATH.'/modules/'.$moduleName.'/'.$this->__classFileName.'.php')){
            require_once ABS_PATH.'/modules/'.$moduleName.'//'.$this->__classFileName.'.php';
            $classname = '\\'.$moduleName.'\\'.$this->__className;
            if(class_exists($classname)){
                $this->{$name} = new $classname;
            } else {
                $this->{$name} = $this->__blankInterface;
            }
        } else {
            $this->{$name} = $this->__blankInterface;
        }
        return $this->{$name};
    }
    public function forceLoad($moduleName){
        $name = strtolower($moduleName);
        if($moduleName && $this->__XM->__validateModuleName($moduleName) && file_exists(ABS_PATH.'/modules/'.$moduleName.'/'.$this->__classFileName.'.php')){
            require_once ABS_PATH.'/modules/'.$moduleName.'//'.$this->__classFileName.'.php';
            $classname = '\\'.$moduleName.'\\'.$this->__className;
            if(class_exists($classname)){
                $this->{$name} = new $classname;  
                if($this->classFileName=='main'){
                    $this->{$name}->__init();
                }
            }
        }
    }
}