<?php
namespace AbstractNS;
if(!defined('IS_XMODULE')){
    exit();
}

class BlankInterface{
    protected function __clone(){}//blocking object cloning
    public function __get($name){
        return $this;
    }
    public function __call($method_name, $args){
       return;
    }
}