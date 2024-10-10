<?php
namespace Api;

ini_set("soap.wsdl_cache_enabled", 1);
if(!defined('IS_XMODULE')){
    exit();
}
require_once ABS_PATH.'/interface/userinterface.php';

class UserInterface extends \AbstractNS\UserInterface{

    public function index($relative_path){
        return false;
    }
    public function api(){
        if(!$this->XM->Api->check_api_auth()){
            return $this->XM->__401();
        }
        $action = null;
        $payload = null;
        if(!$this->XM->Api->get_api_request_params($action, $payload)){
            return false;
        }
        $this->XM->lang->setTempLang(\API\DEFAULT_LANG);
        switch($action){
            case 'getvolumelist':
                return $this->__getvolumelist($payload);
            case 'getwiscores':
                return $this->__getwiscores($payload);
            case 'getpersonalwiscores':
                return $this->__getpersonalwiscores($payload);
                
            case 'setproductprice':
                return $this->__setproductprice($payload);
            default:
                return $this->XM->api->generate_error('Invalid action');
        }
        return true;
    }
    private function __getvolumelist(){
        $this->XM->product->preload();
        $volumelist = $this->XM->product->get_attrval_list(\PRODUCT\VOLUME_ATTRIBUTE_ID);

        $result = array();
        foreach($volumelist as $volume){
            if(!in_array($volume['id'], array(1913,1914,1915,1916,1917,1918))){
                continue;
            }
            $result[] = array('id'=>$volume['id'],'value'=>$volume['name']);
        }
        return $this->XM->api->generate_return($result);
    }
    private function __getwiscores($payload){
        return $this->XM->api->generate_return($this->XM->product->api_get_wiscores(null,$payload));
    }
    private function __getpersonalwiscores($payload){
        return $this->XM->api->generate_return($this->XM->product->api_get_wiscores($this->XM->api->get_company_id(),$payload));
    }
    
    private function __setproductprice($payload){
        if(!$payload || empty($payload)){
            return $this->XM->api->generate_error('Empty payload');
        }

        $volume_white_list = array();
        $volumelist = $this->XM->product->get_attrval_list(\PRODUCT\VOLUME_ATTRIBUTE_ID);
        foreach($volumelist as $volume){
            if(!in_array($volume['id'], array(1913,1914,1915,1916,1917,1918))){
                continue;
            }
            $volume_white_list[] = $volume['id'];
        }
        unset($volumelist);

        $timestamp_limiter = $this->XM->product->get_product_company_price_timestamp_limiter($this->XM->api->get_company_id());

        $errlist = array();
        foreach($payload as $item){
            if(!is_array($item) || !isset($item['id']) || !isset($item['price']) || !isset($item['volume'])){
                $errlist[] = array('errmsg'=>'Invalid payload','item'=>$item);
                continue;
            }
            $err = null;
            if(!$this->XM->product->set_product_company_price($this->XM->api->get_company_id(),$item['id'],isset($item['year'])?$item['year']:null,$item['volume'],isset($item['gift'])?(bool)$item['gift']:false,$item['price'],isset($item['url'])?$item['url']:null,$volume_white_list,$timestamp_limiter,$err)){
                $errlist[] = array('errmsg'=>$err,'item'=>$item);
            }
        }
        $this->XM->product->clear_product_company_price($this->XM->api->get_company_id(),$timestamp_limiter);
        return $this->XM->api->generate_return(array('errors'=>$errlist));
    }
    
}
