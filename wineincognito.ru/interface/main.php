<?php
namespace AbstractNS;
if(!defined('IS_XMODULE')){
    exit();
}
abstract class Main{
    protected $XM;
    protected function __clone(){}//blocking object cloning
    public function __construct(){
        $this->XM = &get_instance();
    }
    public function __init(){}
    public function __call($method_name, $args){return;}
}
