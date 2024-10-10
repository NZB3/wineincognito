<?php
namespace AbstractNS;
if(!defined('IS_XMODULE')){
    exit();
}
abstract class UserInterface{
    protected $XM;
    protected function __clone(){}//blocking object cloning

    public function __construct(){
        $this->XM = &get_instance();
    }
    public function __call($method_name, $args){
       return;// call_user_func_array(array($this, $method_name), $args);
    }

    protected function __404(){
        return $this->XM->__404();
    }

    public function __act($relative_path){//gets called from request headers
        $method_name = 'index';
        if(count($relative_path)){
            $method_name = (string)$relative_path[0];
        }
        $new_relative_path = array();
        if(count($relative_path)>1){
            for($i=1;$i<count($relative_path);$i++){
                $new_relative_path[] = $relative_path[$i];
            }
        }
        if(!method_exists($this, $method_name) || preg_match('#^__#', $method_name)){
            $method_name = '__404';
        }
        if(method_exists($this, $method_name)){
            return call_user_func_array(array($this, $method_name), array($new_relative_path));
        }
        return;
    }
}
