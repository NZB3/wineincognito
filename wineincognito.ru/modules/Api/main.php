<?php
namespace Api;
if(!defined('IS_XMODULE')){
    exit();
}

DEFINE('API\DEFAULT_LANG',1);

require_once ABS_PATH.'/interface/main.php';

class Main extends \AbstractNS\Main{
    private $company_id;
    public function get_company_id(){
        return $this->company_id;
    }
    public function check_api_auth(){
        $api_access_login = isset($_SERVER['PHP_AUTH_USER'])?$_SERVER['PHP_AUTH_USER']:'';
        $api_access_pass = isset($_SERVER['PHP_AUTH_PW'])?$_SERVER['PHP_AUTH_PW']:'';
        if(($company_id = $this->XM->user->get_company_id_from_api_access_login($api_access_login))===false){
            return false;
        }
        if(!$this->XM->user->check_api_access($company_id, $api_access_pass)){
            return false;
        }
        $this->company_id = $company_id;
        return true;
    }

    public function get_api_request_params(&$action,&$payload){
        $requestBody = @json_decode(file_get_contents('php://input'),true);
        if(empty($requestBody) || !isset($requestBody['action'])){
            return false;
        }
        $action = strtolower($requestBody['action']);
        if(isset($requestBody['payload'])&&is_array($requestBody['payload'])){
            $payload = $requestBody['payload'];
        }
        return true;
    }
    public function generate_error($error_msg){
        echo json_encode(array('error'=>1,'errmsg'=>$error_msg));
        return true;
    }
    public function generate_return($payload){
        if(empty($payload)){
            echo json_encode(array('success'=>1));
        }
        echo json_encode(array('success'=>1,'payload'=>$payload));
        return true;
    }
}