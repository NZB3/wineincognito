<?php
namespace Tasting;
if(!defined('IS_XMODULE')){
    exit();
}
require_once ABS_PATH.'/interface/userinterface.php';

class UserInterface extends \AbstractNS\UserInterface{
    public function index($relative_path = array()){
        return false;
    }
    public function testmail_php_interface(){
        include "/home/http-content/include/php_interface/custom_mail.generic.php";
        custom_mail('s.krasnikov@auvix.ru','subject','message');
        return true;
    }
    public function tastingfilter_myreviews($relative_path){
        if(!$this->XM->user->isLoggedIn()){
            return false;
        }
        $took_part_vintage_id = null;
        $subcontent = '';
        if(isset($relative_path[0])){
            $took_part_vintage_id = (int)$relative_path[0];
            if($this->XM->user->isLoggedIn()){
                $tasting_vintage_list = $this->XM->product->get_tasting_vintage_list(null, false, false, false, false, false, true,false,false,$this->XM->user->getUserId(),null,$took_part_vintage_id,true, true);
                if(!empty($tasting_vintage_list)){
                    $subcontent = $this->XM->view->load('tasting/viewtasting_products',array('tasting_id'=>0,'tasting_vintage_list'=>$tasting_vintage_list,'scores'=>true,'personal_reviews'=>true,'order_by_index'=>true),true);
                }    
            }
        }
        $this->XM->__wrapview($this->XM->view->load('tasting/tastingfilter',array('statuslist'=>array(),'took_part'=>true,'took_part_vintage_id'=>$took_part_vintage_id),true).
            $subcontent, 
            null, array('css'=>array('/modules/Tasting/css/tastingfilter.css','/modules/Tasting/css/viewtasting_products.css'),'js'=>array('/modules/Tasting/js/tastingfilter.js','/modules/Tasting/js/viewtasting_products.js'),'pack'=>array('dropbox','datepicker','filterform')));
        return true;
    }
    public function tastingfilter($relative_path,$pending_review = false){
        $can_add = false;
        $status_list = array();
        $subcontent = '';
        $only_for_assessment = false;
        $global_expert_ratings_for_user = null;
        if(!empty($relative_path)){
            if($relative_path[0]=='approve'){
                if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_APPROVE_TASTING)){
                    return false;
                }
                $only_for_assessment = true;
            }
            if(count($relative_path)==2 && $relative_path[0]=='global_evaluation_for_user'){
                if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_EXPERT_EVALUATION_SCORE)){
                    return false;
                }
                $global_expert_ratings_for_user = (int)$relative_path[1];
            }
        }
        if(!$pending_review && !$only_for_assessment){
            $can_add = $this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_ADD_TASTING);
            $status_list = $this->XM->tasting->get_status_list();
            if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_DELETED_TASTINGS) && isset($status_list[\TASTING\TASTING_STATUS_DELETED])){
                unset($status_list[\TASTING\TASTING_STATUS_DELETED]);
            }    
        }
        
        $this->XM->__wrapview($this->XM->view->load('tasting/tastingfilter',array('statuslist'=>$status_list,'can_add'=>$can_add,'pendingreview'=>$pending_review,'only_for_assessment'=>$only_for_assessment,'global_expert_ratings_for_user'=>$global_expert_ratings_for_user),true), 
            null, array('css'=>array('/modules/Tasting/css/tastingfilter.css'),'js'=>array('/modules/Tasting/js/tastingfilter.js'),'pack'=>array('dropbox','datepicker','filterform')));
        return true;
    }

    public function pendingreviewtastingfilter(){
        return $this->tastingfilter(null,true);
    }
    public function pendingtastingproducts($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $tasting_id = (int)$relative_path[0];
        $tastinginfo = $this->XM->tasting->get_tasting($tasting_id);
        if(!$tastinginfo){
            return false;
        }
        $tasting_vintage_list = $this->XM->product->get_tasting_vintage_list($tasting_id, true, false, false, false, false, false, false, true, null, null, null, false, true);
        $this->XM->__wrapview(
            $this->XM->view->load('tasting/viewtasting',array('tastinginfo'=>$tastinginfo,'status_list'=>$this->XM->tasting->get_status_list(),'compact'=>true),true).
            $this->XM->view->load('tasting/tastingpendingreviews',array('tasting_vintage_list'=>$tasting_vintage_list,'tasting_id'=>$tasting_id),true), 
            null, array('css'=>array('/modules/Tasting/css/viewtasting.css','/modules/Tasting/css/viewtasting_products.css'),'js'=>array('/modules/Tasting/js/viewtasting.js')));
        return true;
    }
    public function myreview_tastingstatisticsview($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $tasting_id = (int)$relative_path[0];
        $vintage_id = (isset($relative_path[1])&&$relative_path[1])?(int)$relative_path[1]:null;
        $tastinginfo = $this->XM->tasting->get_tasting($tasting_id);
        if(!$tastinginfo){
            return false;
        }
        $css = array();
        $js = array();
        $pack = array();
        $content = $this->XM->view->load('tasting/viewtasting',array('tastinginfo'=>$tastinginfo,'status_list'=>$this->XM->tasting->get_status_list(),'compact'=>true),true);
        $css[] = '/modules/Tasting/css/viewtasting.css';
        $js[] = '/modules/Tasting/js/viewtasting.js';
        $expert_level_list = $this->XM->user->get_expert_level_list();

        if($vintage_id){
            if(($vintageinfo = $this->XM->product->get_vintage_info($vintage_id))===FALSE){
                return false;
            }
            $content .= $this->XM->view->load('product/viewvintage',array('vintageinfo'=>$vintageinfo,'compact'=>true),true);
            $css[] = '/modules/Product/css/viewvintage.css';
            $js[] = '/modules/Product/js/viewvintage.js';
            $pack[] = 'gallery';
        }
        $awaiting_review_count = false;
        $content .= $this->XM->view->load('tasting/viewtasting_products',array('tasting_id'=>$tasting_id,'can_add'=>false,'can_edit_vintage_list'=>false,'tasting_vintage_list'=>$this->XM->product->get_tasting_vintage_list($tasting_id, false, false, false, true, false, true,true,false,$this->XM->user->getUserId(),null,$vintage_id,false,false),'show_desc'=>false,'scores'=>true,'awaiting_review_count'=>false,'expert_level_list'=>$expert_level_list,'can_refresh'=>false,'stat_url'=>false,'myreview_urls'=>true,'order_by_index'=>false),true);
        $css[] = '/modules/Tasting/css/viewtasting_products.css';
        $js[] = '/modules/Tasting/js/viewtasting_products.js';
        

        $this->XM->__wrapview(
            $content, 
            null, array('css'=>$css,'js'=>$js,'pack'=>$pack));
    }


    public function tastingstatisticsview($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $tasting_id = (int)$relative_path[0];
        $tpv_id = (isset($relative_path[1])&&$relative_path[1])?(int)$relative_path[1]:null;
        $user_id = (isset($relative_path[2])&&$relative_path[2])?(int)$relative_path[2]:null;
        $tastinginfo = $this->XM->tasting->get_tasting($tasting_id);
        if(!$tastinginfo || !$tastinginfo['can_view_statistics']){
            return false;
        }
		$tastinginfo['can_view_statistics'] = false;
        $css = array();
        $js = array();
        $pack = array();
        $content = $this->XM->view->load('tasting/viewtasting',array('tastinginfo'=>$tastinginfo,'status_list'=>$this->XM->tasting->get_status_list(),'compact'=>true),true);
        $css[] = '/modules/Tasting/css/viewtasting.css';
        $js[] = '/modules/Tasting/js/viewtasting.js';
        $expert_level_list = $this->XM->user->get_expert_level_list();

        $show_global_expert_automatic_evaluation = $this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_EXPERT_EVALUATION_SCORE);
        
        if($tpv_id){
            $tasting_vintage_list = $this->XM->product->get_tasting_vintage_list($tasting_id, false, false, (!$user_id)?true:false, true, $user_id&&$show_global_expert_automatic_evaluation, true,true,true,$user_id,$tpv_id,null,false,false);
            if(empty($tasting_vintage_list)){
                return false;
            }
            $content .= $this->XM->view->load('tasting/viewtasting_products',array('tasting_id'=>$tasting_id,'can_add'=>false,'can_refresh'=>($tastinginfo['status']!=\TASTING\TASTING_STATUS_FINISHED),'can_edit_vintage_list'=>false,'tasting_vintage_list'=>$tasting_vintage_list,'show_desc'=>true,'request_review'=>(!$user_id)?true:false,'evaluations'=>true,'show_global_expert_automatic_evaluation'=>$user_id&&$show_global_expert_automatic_evaluation,'scores'=>true,'awaiting_review_count'=>true,'expert_level_list'=>$expert_level_list,'stat_url'=>true,'user_id'=>$user_id,'tpv_id'=>$tpv_id,'can_refresh'=>false,'can_merge_reviews'=>($tastinginfo['score_method']==0),'order_by_index'=>false),true);
            $css[] = '/modules/Tasting/css/viewtasting_products.css';
            $js[] = '/modules/Tasting/js/viewtasting_products.js';
            if($tasting_vintage_list[0]['id']){
                if(($vintageinfo = $this->XM->product->get_vintage_info($tasting_vintage_list[0]['id']))===FALSE){
                    return false;
                }
                $content .= $this->XM->view->load('product/viewvintage',array('vintageinfo'=>$vintageinfo,'compact'=>true),true);
                $css[] = '/modules/Product/css/viewvintage.css';
                $js[] = '/modules/Product/js/viewvintage.js';
                $pack[] = 'gallery';
            }
        }
        if($user_id&&isset($tastinginfo['can_view_users'])&&$tastinginfo['can_view_users']){
            $content .= $this->XM->view->load('tasting/viewtasting_users',array('tasting_id'=>$tasting_id,'can_add'=>false,'can_refresh'=>($tastinginfo['status']!=\TASTING\TASTING_STATUS_FINISHED),'can_edit_users'=>(isset($tastinginfo['can_edit_users'])&&$tastinginfo['can_edit_users']),'can_mark_user_presence'=>(isset($tastinginfo['can_mark_user_presence'])&&$tastinginfo['can_mark_user_presence']),'tasting_user_list'=>$this->XM->tasting->get_tasting_user_list($tasting_id,true,false,false,true,$show_global_expert_automatic_evaluation,$tpv_id,false,$user_id),'only_present'=>true,'show_response'=>false,'show_background'=>false,'evaluation_scores'=>true,'show_global_expert_automatic_evaluation'=>$show_global_expert_automatic_evaluation,'product_id'=>$tpv_id,'user_id'=>$user_id,'expert_level_list'=>$expert_level_list,'stat_url'=>true,'actions'=>false,'can_block_reviews'=>$tpv_id&&$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_BLOCK_REVIEW)),true);
            $css[] = '/modules/Tasting/css/viewtasting_users.css';
            $js[] = '/modules/Tasting/js/viewtasting_users.js';
        }
        if(!$tpv_id){
            $awaiting_review_count = true;
            $content .= $this->XM->view->load('tasting/viewtasting_products',array('tasting_id'=>$tasting_id,'can_add'=>false,'can_refresh'=>($tastinginfo['status']!=\TASTING\TASTING_STATUS_FINISHED),'can_edit_vintage_list'=>(isset($tastinginfo['can_edit_vintage_list'])&&$tastinginfo['can_edit_vintage_list']),'tasting_vintage_list'=>$this->XM->product->get_tasting_vintage_list($tasting_id, false, false, (!$user_id)?true:false, true, $user_id&&$show_global_expert_automatic_evaluation, true,true,false,$user_id,null,null,false,false),'show_desc'=>false,'request_review'=>!$user_id,'evaluations'=>true,'show_global_expert_automatic_evaluation'=>$user_id&&$show_global_expert_automatic_evaluation,'scores'=>true,'awaiting_review_count'=>true,'expert_level_list'=>$expert_level_list,'stat_url'=>true,'auto_refresh_timer'=>(!$user_id && $tastinginfo['status']!=\TASTING\TASTING_STATUS_FINISHED)?30:0,'user_id'=>$user_id,'can_merge_reviews'=>($tastinginfo['score_method']==0),'order_by_index'=>false),true);
            $css[] = '/modules/Tasting/css/viewtasting_products.css';
            $js[] = '/modules/Tasting/js/viewtasting_products.js';
        }
        
        if(!$user_id&&isset($tastinginfo['can_view_users'])&&$tastinginfo['can_view_users']){
            $content .= $this->XM->view->load('tasting/viewtasting_users',array('tasting_id'=>$tasting_id,'can_add'=>false,'can_refresh'=>($tastinginfo['status']!=\TASTING\TASTING_STATUS_FINISHED),'can_edit_users'=>(isset($tastinginfo['can_edit_users'])&&$tastinginfo['can_edit_users']),'can_mark_user_presence'=>(isset($tastinginfo['can_mark_user_presence'])&&$tastinginfo['can_mark_user_presence']),'tasting_user_list'=>$this->XM->tasting->get_tasting_user_list($tasting_id,true,false,false,true,$show_global_expert_automatic_evaluation,$tpv_id,false,null),'only_present'=>true,'show_response'=>false,'show_background'=>false,'evaluation_scores'=>true,'show_global_expert_automatic_evaluation'=>$show_global_expert_automatic_evaluation,'product_id'=>$tpv_id,'expert_level_list'=>$expert_level_list,'stat_url'=>true,'actions'=>false,'can_block_reviews'=>$tpv_id&&$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_BLOCK_REVIEW)),true);
            $css[] = '/modules/Tasting/css/viewtasting_users.css';
            $js[] = '/modules/Tasting/js/viewtasting_users.js';
        }
        

        $this->XM->__wrapview(
            $content, 
            null, array('css'=>$css,'js'=>$js,'pack'=>$pack));
    }
    public function tastingview($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $tasting_id = (int)$relative_path[0];
        $tastinginfo = $this->XM->tasting->get_tasting($tasting_id);
        if(!$tastinginfo){
            return false;
        }
        
        $css = array();
        $js = array();
        $content = $this->XM->view->load('tasting/viewtasting',array('tastinginfo'=>$tastinginfo,'status_list'=>$this->XM->tasting->get_status_list()),true);
        $css[] = '/modules/Tasting/css/viewtasting.css';
        $js[] = '/modules/Tasting/js/viewtasting.js';
        $current_user_attendance_response = $this->XM->tasting->get_current_user_attendance_response($tasting_id);
        if($current_user_attendance_response!==false){
            $content .= $this->XM->view->load('tasting/viewtasting_user_attendance',array('tasting_id'=>$tasting_id,'current_user_attendance_response'=>$current_user_attendance_response,'user_response_list'=>$this->XM->tasting->get_user_response_list(false)),true);
            $css[] = '/modules/Tasting/css/viewtasting_user_attendance.css';
            $js[] = '/modules/Tasting/js/viewtasting_user_attendance.js';
        }
        if(($ongoing_tasting_user_status = $this->XM->tasting->get_current_user_ongoing_status($tasting_id))!==false){
            $content .= $this->XM->view->load('tasting/viewtasting_ongoing_element',array('tasting_id'=>$tasting_id,'ongoing_tasting_user_status'=>$ongoing_tasting_user_status,'ranking_scoring'=>($tastinginfo['score_method']==1)?1:0),true);
        }
        if($tastinginfo['can_change_expert_evaluation_options']){
            if(($tasting_evaluation_data = $this->XM->tasting->get_tasting_evaluation_data($tasting_id))!==false){
                $content .= $this->XM->view->load('tasting/viewtasting_expert_evaluation_options',array('tasting_id'=>$tasting_id,'tasting_evaluation_data'=>$tasting_evaluation_data,'review_elements'=>$this->XM->product->get_review_elements(),'grape_variety_list'=>$this->XM->product->get_attr_list(\PRODUCT\GRAPE_ATTRIBUTE_GROUP_ID,false,false),'location_list'=>$this->XM->product->get_attr_list(\PRODUCT\LOCATION_ATTRIBUTE_GROUP_ID,false,false)),true);
                $css[] = '/modules/Tasting/css/viewtasting_expert_evaluation_options.css';
                $js[] = '/modules/Tasting/js/viewtasting_expert_evaluation_options.js';
            }
        }
        if($tastinginfo['can_change_review_particularity_options']){
            $content .= $this->XM->view->load('tasting/viewtasting_review_particularity_options',array('tasting_id'=>$tasting_id,'review_particularity_data'=>$this->XM->tasting->get_tasting_review_particularity_data($tasting_id),'review_particularity_option_list'=>$this->XM->tasting->get_tasting_review_particularity_option_list($tasting_id),'review_elements'=>$this->XM->product->get_review_elements()),true);
            $css[] = '/modules/Tasting/css/viewtasting_review_particularity_options.css';
            $js[] = '/modules/Tasting/js/viewtasting_review_particularity_options.js';
        }

        $content .= $this->XM->view->load('tasting/viewtasting_products',array('tasting_id'=>$tasting_id,'can_add'=>(isset($tastinginfo['can_edit_vintage_list'])&&$tastinginfo['can_edit_vintage_list']),'can_edit_vintage_list'=>(isset($tastinginfo['can_edit_vintage_list'])&&$tastinginfo['can_edit_vintage_list']),'tasting_vintage_list'=>$this->XM->product->get_tasting_vintage_list($tasting_id, false, true, true, false, false, false, false, true, null, null, null, false, true),'show_desc'=>true,'actions'=>true,'request_review'=>true,'order_by_index'=>true),true);
        $css[] = '/modules/Tasting/css/viewtasting_products.css';
        $css[] = '/modules/Tasting/css/vintagepreparationform.css';
        $css[] = '/modules/Product/css/vintagefilter.css';
        $css[] = '/modules/Product/css/editattrval.css';
        $css[] = '/modules/Product/css/editvintage.css';
        $css[] = '/modules/Product/css/viewvintage.css';
        $css[] = '/modules/Product/css/tastingform.css';
        $js[] = '/modules/Tasting/js/viewtasting_products.js';
        $js[] = '/modules/Tasting/js/vintagepreparationform.js';
        $js[] = '/modules/Product/js/vintagefilter.js';
        $js[] = '/modules/Product/js/editvintage.modal.js';
        $js[] = '/modules/Product/js/viewvintage.js';
        $js[] = '/modules/Product/js/tastingform.js';

        if(isset($tastinginfo['can_view_users'])&&$tastinginfo['can_view_users']){
            $content .= $this->XM->view->load('tasting/viewtasting_users',array('tasting_id'=>$tasting_id,'can_add'=>(isset($tastinginfo['can_edit_users'])&&$tastinginfo['can_edit_users']),'can_edit_users'=>(isset($tastinginfo['can_edit_users'])&&$tastinginfo['can_edit_users']),'can_mark_user_presence'=>(isset($tastinginfo['can_mark_user_presence'])&&$tastinginfo['can_mark_user_presence']),'tasting_user_list'=>$this->XM->tasting->get_tasting_user_list($tasting_id,false,true,true,false,false,null,false,null),'actions'=>true,'show_response'=>true,'show_background'=>true),true);
            $css[] = '/modules/Tasting/css/viewtasting_users.css';
            $css[] = '/modules/User/css/userfilter.css';
            $js[] = '/modules/Tasting/js/viewtasting_users.js';
            $js[] = '/modules/User/js/userfilter.js';
        }
        $this->XM->__wrapview($content, 
            null, array(
                'css'=>$css,
                'js'=>$js,
                'pack'=>array('gallery','dropbox','mask','filterform')));
        return true;
    }
    public function tastingadd(){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_ADD_TASTING)){
            return false;
        }
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_tasting')){
            $name = isset($_POST['name'])?$_POST['name']:'';
            $location = isset($_POST['location'])?$_POST['location']:'';
            $start_date = isset($_POST['start_date'])?$_POST['start_date']:'';
            $start_time = isset($_POST['start_time'])?$_POST['start_time']:'';
            $end_date = isset($_POST['end_date'])?$_POST['end_date']:'';
            $end_time = isset($_POST['end_time'])?$_POST['end_time']:'';
            $desc = isset($_POST['desc'])?$_POST['desc']:'';
            $participation = (isset($_POST['participation'])&&$_POST['participation'])?1:0;
            $participation_rating = isset($_POST['participation_rating'])?$_POST['participation_rating']:0;
            $chargeability = isset($_POST['chargeability'])?$_POST['chargeability']:0;
            $price_grid = array(
                'guest_price'           =>isset($_POST['price_grid_guest'])?$_POST['price_grid_guest']:0,
                'expert_price'          =>isset($_POST['price_grid_expert'])?$_POST['price_grid_expert']:0,
                'rated_expert_rating'   =>isset($_POST['price_grid_rated_expert_rating'])?$_POST['price_grid_rated_expert_rating']:0,
                'rated_expert_price'    =>isset($_POST['price_grid_rated_expert'])?$_POST['price_grid_rated_expert']:0,
            );
            $assessment = isset($_POST['assessment'])?($_POST['assessment']?1:0):1;
            $score_method = isset($_POST['scoremethod'])?(int)$_POST['scoremethod']:0;
            $err = null;
            if(!$tasting_id = $this->XM->tasting->add_tasting($name, $location, $start_date, $start_time, $end_date, $end_time, $desc, $participation, $participation_rating, $chargeability, $price_grid, $assessment, $score_method, $err)){
                $this->XM->addMessage($err, 0);
            } else {
                $this->XM->setPushStateUrl(BASE_URL.'/tasting/'.$tasting_id);
                return $this->tastingview(array($tasting_id));
                // redirect('/tasting/'.$tasting_id);
            }
        }
        $this->XM->__wrapview($this->XM->view->load('tasting/edittasting',array('tastinginfo'=>array()),true), 
            null, array('css'=>array('/modules/Tasting/css/edittasting.css'),'js'=>array('/modules/Tasting/js/edittasting.js'),'pack'=>array('datepicker','mask')));
        return true;
    }
    public function tastingedit($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $tasting_id = (int)$relative_path[0];
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_ADD_TASTING)){
            return false;
        }
        $tastinginfo = $this->XM->tasting->get_tasting($tasting_id);
        if(!$tastinginfo){
            return false;
        }
        if($tastinginfo['status']!==\TASTING\TASTING_STATUS_DRAFT){
            $this->XM->addMessage(langTranslate('tasting','err','You can only edit tastings in draft stage','You can only edit tastings in draft stage'), 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_tasting')){
            $name = isset($_POST['name'])?$_POST['name']:'';
            $location = isset($_POST['location'])?$_POST['location']:'';
            $start_date = isset($_POST['start_date'])?$_POST['start_date']:'';
            $start_time = isset($_POST['start_time'])?$_POST['start_time']:'';
            $end_date = isset($_POST['end_date'])?$_POST['end_date']:'';
            $end_time = isset($_POST['end_time'])?$_POST['end_time']:'';
            $desc = isset($_POST['desc'])?$_POST['desc']:'';
            $participation = isset($_POST['participation'])?$_POST['participation']:0;
            $participation_rating = isset($_POST['participation_rating'])?$_POST['participation_rating']:0;
            $chargeability = isset($_POST['chargeability'])?$_POST['chargeability']:0;
            $price_grid = array(
                'guest_price'           =>isset($_POST['price_grid_guest'])?$_POST['price_grid_guest']:0,
                'expert_price'          =>isset($_POST['price_grid_expert'])?$_POST['price_grid_expert']:0,
                'rated_expert_rating'   =>isset($_POST['price_grid_rated_expert_rating'])?$_POST['price_grid_rated_expert_rating']:0,
                'rated_expert_price'    =>isset($_POST['price_grid_rated_expert'])?$_POST['price_grid_rated_expert']:0,
            );
            $assessment = isset($_POST['assessment'])?($_POST['assessment']?1:0):1;
            $score_method = isset($_POST['scoremethod'])?(int)$_POST['scoremethod']:0;
            $err = null;
            if(!$this->XM->tasting->edit_tasting($tasting_id, $name, $location, $start_date, $start_time, $end_date, $end_time, $desc, $participation, $participation_rating, $chargeability, $price_grid, $assessment, $score_method, $err)){
                $this->XM->addMessage($err, 0);
            } else {
                $this->XM->setPushStateUrl(BASE_URL.'/tasting/'.$tasting_id);
                return $this->tastingview(array($tasting_id));
                // redirect('/tasting/'.$tasting_id);
            }
        }

        $this->XM->__wrapview($this->XM->view->load('tasting/edittasting',array('tastinginfo'=>$tastinginfo),true), 
            null, array('css'=>array('/modules/Tasting/css/edittasting.css'),'js'=>array('/modules/Tasting/js/edittasting.js'),'pack'=>array('datepicker','mask')));
        return true;
    }
    public function external_tasting_user_respond($relative_path = array()){
        if(count($relative_path)!=4){
            return false;
        }
        $tasting_id = (int)$relative_path[0];
        $tu_id = (int)$relative_path[1];
        $usercode = (int)$relative_path[2];
        $response = (int)$relative_path[3];
        $err = null;
        if($this->XM->tasting->external_tasting_user_respond($tasting_id,$tu_id,$usercode,$response,$err) === false){
            $this->XM->addMessage($err, 0);
        } else {
            $this->XM->addMessage(langTranslate('tasting','tasting','Thank you for your response. It has been taken into account', 'Thank you for your response. It has been taken into account'), 2);
        }
        if($this->XM->user->isLoggedIn()){
            $this->XM->setPushStateUrl(BASE_URL.'/tasting/'.$tasting_id);
            return $this->XM->__UI->tasting->tastingview(array($tasting_id));
        } else {
            $this->XM->setPushStateUrl(BASE_URL);
            return $this->XM->__UI->main->index();
        }
        
    }
    public function tastingrankingedit($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        $tasting_id = (int)$relative_path[0];
        if(!$this->XM->user->isLoggedIn()){
            return false;
        }
        if(isset($_POST)&&isset($_POST['action'])&&$_POST['action']=='edit_tasting_vintage_ranking'&&isset($_POST['index'])&&is_array($_POST['index'])){
            $index = $_POST['index'];
            $err = null;
            if(!$this->XM->product->set_tasting_vintage_ranking_for_current_user($tasting_id, $index, $err)){
                $this->XM->addMessage($err, 0);
            } else {
                $this->XM->addMessage(langTranslate('tasting','ranking','Provided product ranking has been successfully saved', 'Provided product ranking has been successfully saved'), 2);
            }
        }
        if(!($tastinginfo = $this->XM->tasting->get_tasting($tasting_id))){
            return false;
        }
        if($tastinginfo['score_method']!=1){
            return false;
        }
        if(($vintage_ranking_list = $this->XM->product->get_tasting_vintage_ranking_for_user($tasting_id, $this->XM->user->getUserId()))===FALSE){
            return false;
        }
        $css = array();
        $js = array();
        $content = $this->XM->view->load('tasting/viewtasting',array('tastinginfo'=>$tastinginfo,'shortform'=>true,'compact'=>true),true);
        $css[] = '/modules/Tasting/css/viewtasting.css';
        $js[] = '/modules/Tasting/js/viewtasting.js';

        $content .= $this->XM->view->load('tasting/editranking',array('vintage_ranking_list'=>$vintage_ranking_list),true);
        $css[] = '/modules/Tasting/css/editranking.css';
        $js[] = '/modules/Tasting/js/editranking.js';
        $this->XM->__wrapview($content, null, array('css'=>$css,'js'=>$js,'pack'=>array('gallery','dropbox')));
        return true;
    }
    public function tastingsetmanualevaluation_for_tasting_and_tastingproduct($relative_path = array()){
        if(count($relative_path)!=2){
            return false;
        }
        $tasting_id = (int)$relative_path[0];
        $tpv_id = (int)$relative_path[1];
        if(isset($_POST) && isset($_POST['action']) && $_POST['action']=='set_manual_evaluation'){
            $manual_scores = $_POST;
            unset($manual_scores['action']);
            $err = null;
            if($this->XM->tasting->set_tasting_product_vintage_manual_evaluation($tpv_id, $manual_scores, $err)===false){
                $this->XM->addMessage($err, 0);
            } else {
                $this->XM->addMessage(langTranslate('tasting','tasting','Manual evaluation for this tasting product has been configured', 'Manual evaluation for this tasting product has been configured'), 2);    
                $this->XM->setPushStateUrl(BASE_URL.'/tasting/'.$tasting_id.'/stats/product/'.$tpv_id);
                return $this->tastingstatisticsview(array($tasting_id,$tpv_id));
            }
        }
        if(($vintage_id = $this->XM->product->get_vintage_id_for_tasting_product_vintage($tpv_id))===false){
            return false;
        }
        $expert_level_list = $this->XM->user->get_expert_level_list();
        $expert_levels = array_keys($expert_level_list);
        if(($reviewmergeinfo = $this->XM->product->get_review_merge_info($vintage_id,$expert_levels,$tasting_id,null,$tpv_id,null,true,true))===FALSE){
            return false;
        }
        $review_elements = $this->XM->product->get_filtered_review_elements($this->XM->product->get_vintage_review_filter($vintage_id));
        $load_template_expert_list = $this->XM->tasting->get_tasting_user_list($tasting_id,false,false,false,false,false,$tpv_id,true,null);
        if(isset($_POST) && isset($_POST['action']) && $_POST['action']=='load_expert_template'){
            $template_reviewmergeinfo = $this->XM->product->get_review_merge_info($vintage_id,$expert_levels,$tasting_id,(int)$_POST['expert'],$tpv_id,null,true,true);
            $_POST = array();
            if(isset($template_reviewmergeinfo['score'])&&!empty($template_reviewmergeinfo['score'])){
                $_POST['score_score'] = 3;
                $_POST['score_value'] = array_pop($template_reviewmergeinfo['score']);
            }
            if(isset($template_reviewmergeinfo['subcolor'])&&!empty($template_reviewmergeinfo['subcolor'])){
                $_POST['subcolorcode'] = array();
                foreach($template_reviewmergeinfo['subcolor'] as $subcolor_data){
                    $_POST['subcolorcode'][$subcolor_data['color'].','.$subcolor_data['subcolor'].','.$subcolor_data['depth']] = 3;
                }
            }
            foreach($template_reviewmergeinfo['params'] as $param=>$values){
                $_POST[$param] = array();
                foreach($values as $value=>$dummy){
                    $_POST[$param][$value] = 3;
                }
            }
            unset($template_reviewmergeinfo);
        }
        $this->XM->__wrapview($this->XM->view->load('product/mergereview',array('reviewmergeinfo'=>$reviewmergeinfo,'expert_levels'=>$expert_levels,'expert_level_list'=>$expert_level_list,'review_elements'=>$review_elements,'evaluationform'=>true,'load_template_expert_list'=>$load_template_expert_list,'blindness'=>$this->XM->product->get_blindness_for_tasting_product_vintage($tpv_id)),true),
            null, array('css'=>array('/modules/Product/css/mergereview.css'),'js'=>array('/modules/Product/js/mergereview_evaluationform.js'),'pack'=>array('mask')));
        return true;
    }
    public function tastingviewmanualevaluation_for_tasting_and_tastingproduct($relative_path = array()){
        if(count($relative_path)!=2){
            return false;
        }
        $tasting_id = (int)$relative_path[0];
        $tpv_id = (int)$relative_path[1];
        if(($vintage_id = $this->XM->product->get_vintage_id_for_tasting_product_vintage($tpv_id))===false){
            return false;
        }
        $expert_level_list = $this->XM->user->get_expert_level_list();
        $expert_levels = array_keys($expert_level_list);
        if(($reviewmergeinfo = $this->XM->product->get_review_merge_info($vintage_id,$expert_levels,$tasting_id,null,$tpv_id,null,true,true))===FALSE){
            return false;
        }
        $review_elements = $this->XM->product->get_filtered_review_elements($this->XM->product->get_vintage_review_filter($vintage_id));
        $err = null;
        if(($scores = $this->XM->tasting->get_tasting_product_vintage_manual_evaluation($tpv_id, $err))===false){
            $this->XM->addMessage($err, 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        $this->XM->__wrapview($this->XM->view->load('product/mergereview',array('reviewmergeinfo'=>$reviewmergeinfo,'expert_levels'=>$expert_levels,'expert_level_list'=>$expert_level_list,'review_elements'=>$review_elements,'evaluationview'=>true,'scores'=>$scores,'blindness'=>$this->XM->product->get_blindness_for_tasting_product_vintage($tpv_id)),true),
            null, array('css'=>array('/modules/Product/css/mergereview.css')));
        return true;
    }
	public function tastingswapreviews_for_tasting($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        $tasting_id = (int)$relative_path[0];
        $tastinginfo = $this->XM->tasting->get_tasting($tasting_id);
        if(!$tastinginfo || !isset($tastinginfo['can_swap_reviews']) || !$tastinginfo['can_swap_reviews']){
            return false;
        }
		$tastinginfo['can_swap_reviews'] = false;
        $css = array();
        $js = array();
        $pack = array();
        $content = $this->XM->view->load('tasting/viewtasting',array('tastinginfo'=>$tastinginfo,'status_list'=>$this->XM->tasting->get_status_list(),'compact'=>true),true);
        $css[] = '/modules/Tasting/css/viewtasting.css';
        $js[] = '/modules/Tasting/js/viewtasting.js';
		
		$content .= $this->XM->view->load('tasting/viewtasting_users',array('tasting_id'=>$tasting_id,'can_add'=>false,'can_refresh'=>($tastinginfo['status']!=\TASTING\TASTING_STATUS_FINISHED),'can_edit_users'=>false,'can_mark_user_presence'=>false,'tasting_user_list'=>$this->XM->tasting->get_tasting_user_list($tasting_id,true,false,false,true,false,false,false,null),'only_present'=>true,'show_response'=>false,'show_background'=>false,'evaluation_scores'=>true,'show_global_expert_automatic_evaluation'=>false,'product_id'=>false,'expert_level_list'=>$this->XM->user->get_expert_level_list(),'swap_url'=>true,'actions'=>false),true);
            $css[] = '/modules/Tasting/css/viewtasting_users.css';
            $js[] = '/modules/Tasting/js/viewtasting_users.js';
		
		$this->XM->__wrapview(
            $content, 
            null, array('css'=>$css,'js'=>$js,'pack'=>$pack));
        return true;
    }
	public function tastingswapreviews_for_tasting_and_user($relative_path = array()){
        if(count($relative_path)!=2){
            return false;
        }
        $tasting_id = (int)$relative_path[0];
		$user_id = (int)$relative_path[1];
        $tastinginfo = $this->XM->tasting->get_tasting($tasting_id);
        if(!$tastinginfo || !isset($tastinginfo['can_swap_reviews']) || !$tastinginfo['can_swap_reviews']){
            return false;
        }
        $css = array();
        $js = array();
        $pack = array();
        $content = $this->XM->view->load('tasting/viewtasting',array('tastinginfo'=>$tastinginfo,'status_list'=>$this->XM->tasting->get_status_list(),'compact'=>true),true);
        $css[] = '/modules/Tasting/css/viewtasting.css';
        $js[] = '/modules/Tasting/js/viewtasting.js';
		
		$expert_level_list = $this->XM->user->get_expert_level_list();
		
		$content .= $this->XM->view->load('tasting/viewtasting_users',array('tasting_id'=>$tasting_id,'can_add'=>false,'can_refresh'=>($tastinginfo['status']!=\TASTING\TASTING_STATUS_FINISHED),'can_edit_users'=>false,'can_mark_user_presence'=>false,'tasting_user_list'=>$this->XM->tasting->get_tasting_user_list($tasting_id,true,false,false,true,false,false,false,$user_id),'only_present'=>true,'show_response'=>false,'show_background'=>false,'evaluation_scores'=>true,'show_global_expert_automatic_evaluation'=>false,'user_id'=>$user_id,'expert_level_list'=>$expert_level_list,'actions'=>false),true);
            $css[] = '/modules/Tasting/css/viewtasting_users.css';
            $js[] = '/modules/Tasting/js/viewtasting_users.js';
		
		$content .= $this->XM->view->load('tasting/viewtasting_products',array('tasting_id'=>$tasting_id,'can_add'=>false,'can_refresh'=>($tastinginfo['status']!=\TASTING\TASTING_STATUS_FINISHED),'can_edit_vintage_list'=>false,'tasting_vintage_list'=>$this->XM->product->get_tasting_vintage_list($tasting_id, false, false, false, true, false, true,false,false,$user_id,null,null,false,true),'show_desc'=>false,'request_review'=>false,'evaluations'=>true,'show_global_expert_automatic_evaluation'=>false,'scores'=>true,'awaiting_review_count'=>false,'expert_level_list'=>$expert_level_list,'auto_refresh_timer'=>0,'user_id'=>$user_id,'swap_reviews'=>true,'can_merge_reviews'=>($tastinginfo['score_method']==0),'order_by_index'=>true),true);
		$css[] = '/modules/Tasting/css/viewtasting_products.css';
		$js[] = '/modules/Tasting/js/viewtasting_products.js';
		$pack[] = 'gallery';
		
		$content .= $this->XM->view->load('tasting/tastingswapreviews_button',array('tasting_id'=>$tasting_id,'user_id'=>$user_id),true);
		$css[] = '/modules/Tasting/css/tastingswapreviews_button.css';
		$js[] = '/modules/Tasting/js/tastingswapreviews_button.js';
		
		$this->XM->__wrapview(
            $content, 
            null, array('css'=>$css,'js'=>$js,'pack'=>$pack));
        return true;
    }
    public function product_certificate($relative_path = array()){
        if(count($relative_path)!=2){
            return false;
        }
        $vintage_id = (int)$relative_path[0];
        $contest_id = (int)$relative_path[1];
        $this->XM->lang->setTempLang(2);
        if(($vintageinfo = $this->XM->product->get_vintage_info($vintage_id))===FALSE){
            return false;
        }
        if(($reviewmergeinfo = $this->XM->product->get_review_merge_info($vintage_id,array(3),null,null,null,$contest_id,true,true))===FALSE){
            return false;
        }
        if(($contestinfo = $this->XM->tasting->get_contest($contest_id))===FALSE){
            return false;
        }
        $review_elements = $this->XM->product->get_filtered_review_elements($this->XM->product->get_vintage_review_filter($vintage_id));
        //exception
        $reviewer_rating = null;
        if($contest_id==0){
            if(($reviewer_reviewmergeinfo = $this->XM->product->get_review_merge_info($vintage_id,array(1,2),null,null,null,$contest_id,true,true))!==FALSE){
                $total_count = 0;
                $total_score = 0;
                foreach($reviewer_reviewmergeinfo['count'] as $expert_level=>$count){
                    if($count){
                        $total_count+=$count;
                        $total_score+=$count*str_replace(',', '.', $reviewer_reviewmergeinfo['score'][$expert_level]);
                    }
                }
                if($total_count){
                    $score = round($total_score/$total_count);
                    if($score<=79){
                        $reviewer_rating = 1;
                    } elseif($score<=84){
                        $reviewer_rating = 2;
                    } elseif($score<=89){
                        $reviewer_rating = 3;
                    } elseif($score<=94){
                        $reviewer_rating = 4;
                    } else {
                        $reviewer_rating = 5;
                    }
                }
            }
        }
        $highlight_expert_list = null;
        if($contest_id==31){
            $page = 1;
            $pagelimit = 11;
            $count = null;
            $err = null;
            $highlight_expert_list = $this->XM->user->filter_user('', array(), array(), false, null, false, false, true, false, $contest_id, $vintage_id, null, false, true, 'name', true, $page, $pagelimit, $count, $err);
        }
        $this->XM->view->load('tasting/product_certificate',array('vintageinfo'=>$vintageinfo,'contestinfo'=>$contestinfo,'reviewmergeinfo'=>$reviewmergeinfo,'review_elements'=>$review_elements,'expert_level'=>3,'reviewer_rating'=>$reviewer_rating,'highlight_expert_list'=>$highlight_expert_list));
        $this->XM->lang->revertTempLang();
        return true;
    }
    //contest
    public function contestfilter($relative_path = array()){
        $only_for_assessment = false;
        if(!empty($relative_path)){
            if($relative_path[0]=='approve'){
                if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_APPROVE_CONTEST)){
                    return false;
                }
                $only_for_assessment = true;
            }
        }
        
        $status_list = array();
        $can_add = false;
        if(!$only_for_assessment){
            $can_add = $this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_ADD_CONTEST);
            $status_list = $this->XM->tasting->get_contest_status_list();    
        }
        
        $this->XM->__wrapview($this->XM->view->load('tasting/contestfilter',array('statuslist'=>$status_list,'can_add'=>$can_add,'only_for_assessment'=>$only_for_assessment),true), 
            null, array('css'=>array('/modules/Tasting/css/contestfilter.css'),'js'=>array('/modules/Tasting/js/contestfilter.js'),'pack'=>array('dropbox','datepicker','filterform')));
        return true;
    }
    public function contestadd(){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_ADD_CONTEST)){
            return false;
        }
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_tasting_contest')){
            $name = isset($_POST['name'])?$_POST['name']:'';
            $location = isset($_POST['location'])?$_POST['location']:'';
            $desc = isset($_POST['desc'])?$_POST['desc']:'';
            $assessment = isset($_POST['assessment'])?($_POST['assessment']?1:0):1;
            $logoinfo = (isset($_FILES)&&isset($_FILES['tasting_contest_logo_file']))?$_FILES['tasting_contest_logo_file']:null;
            $err = null;
            if(!$contest_id = $this->XM->tasting->add_contest($name, $logoinfo, $location, $desc, $assessment, $err)){
                $this->XM->addMessage($err, 0);
            } else {
                $this->XM->setPushStateUrl(BASE_URL.'/contest/'.$contest_id);
                return $this->contestview(array($contest_id));
                // redirect('/contest/'.$tasting_id);
            }
        }
        $this->XM->__wrapview($this->XM->view->load('tasting/editcontest',array('contestinfo'=>array()),true), 
            null, array('css'=>array('/modules/Tasting/css/editcontest.css'),'js'=>array('/modules/Tasting/js/editcontest.js')));
        return true;
    }
    public function contestedit($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $contest_id = (int)$relative_path[0];
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_ADD_TASTING)){
            return false;
        }
        $contestinfo = $this->XM->tasting->get_contest($contest_id);
        if(!$contestinfo){
            return false;
        }
        if($contestinfo['status']!==\TASTING\CONTEST_STATUS_DRAFT){
            $this->XM->addMessage(langTranslate('tasting','err','You can only edit contests in draft stage','You can only edit contests in draft stage'), 0);
            $this->XM->__wrapview(null, null, null);
            return true;
        }
        if(isset($_POST)&&isset($_POST['action'])&&($_POST['action']=='edit_tasting_contest')){
            $name = isset($_POST['name'])?$_POST['name']:'';
            $location = isset($_POST['location'])?$_POST['location']:'';
            $desc = isset($_POST['desc'])?$_POST['desc']:'';
            $assessment = isset($_POST['assessment'])?($_POST['assessment']?1:0):1;
            $logoinfo = (isset($_FILES)&&isset($_FILES['tasting_contest_logo_file']))?$_FILES['tasting_contest_logo_file']:null;
            $err = null;
            if(!$this->XM->tasting->edit_contest($contest_id, $name, $logoinfo, $location, $desc, $assessment, $err)){
                $this->XM->addMessage($err, 0);
            } else {
                $this->XM->setPushStateUrl(BASE_URL.'/contest/'.$contest_id);
                return $this->contestview(array($contest_id));
                // redirect('/contest/'.$tasting_id);
            }
        }
        $this->XM->__wrapview($this->XM->view->load('tasting/editcontest',array('contestinfo'=>$contestinfo),true), 
            null, array('css'=>array('/modules/Tasting/css/editcontest.css'),'js'=>array('/modules/Tasting/js/editcontest.js')));
        return true;
    }
    public function contestview($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $contest_id = (int)$relative_path[0];
        $contestinfo = $this->XM->tasting->get_contest($contest_id);
        if(!$contestinfo){
            return false;
        }
        $can_refresh = ($contestinfo['status']!=\TASTING\CONTEST_STATUS_FINISHED);
        
        $css = array();
        $js = array();
        $pack = array();
        $content = $this->XM->view->load('tasting/viewcontest',array('contestinfo'=>$contestinfo,'status_list'=>$this->XM->tasting->get_contest_status_list()),true);
        $css[] = '/modules/Tasting/css/viewcontest.css';
        $js[] = '/modules/Tasting/js/viewcontest.js';

        if($contestinfo['can_view_nominations']){
            $showempty = true;
            $content .= $this->XM->view->load('tasting/viewcontest_nominations',array('contest_id'=>$contest_id,'can_edit'=>(isset($contestinfo['can_edit_nominations'])&&$contestinfo['can_edit_nominations']),'contest_nomination_list'=>$this->XM->tasting->get_contest_nomination_list($contest_id,null,$showempty),'expert_level_list'=>$this->XM->user->get_expert_level_list(),'showempty'=>$showempty,'actions'=>true,'can_refresh'=>$can_refresh,'compact'=>true),true);
            $css[] = '/modules/Tasting/css/viewcontest_nominations.css';
            $js[] = '/modules/Tasting/js/viewcontest_nominations.js';
            $css[] = '/modules/Product/css/vintagefilter.css';
            $js[] = '/modules/Product/js/vintagefilter.js';
            $css[] = '/modules/Product/css/viewvintage.css';
            $js[] = '/modules/Product/js/viewvintage.js';
            $pack[] = 'dropbox';
            $pack[] = 'filterform';
            $pack[] = 'gallery';
            $pack[] = 'mask';
        }

        if($contestinfo['can_view_tasting_list']){
            $showstatus = true;
            $showowner = true;
            $showassessment = $contestinfo['can_assess'];
            $content .= $this->XM->view->load('tasting/viewcontest_tastings',array('contest_id'=>$contest_id,'can_add'=>(isset($contestinfo['can_add_tasting'])&&$contestinfo['can_add_tasting']),'contest_tasting_list'=>$this->XM->tasting->get_contest_tasting_list($contest_id,null,null,$showstatus,$showowner,$showassessment),'showstatus'=>$showstatus,'showowner'=>$showowner,'showassessment'=>$showassessment,'actions'=>true,'can_refresh'=>$can_refresh,'compact'=>true),true);
            $css[] = '/modules/Tasting/css/viewcontest_tastings.css';
            $js[] = '/modules/Tasting/js/viewcontest_tastings.js';
            $css[] = '/modules/Tasting/css/tastingfilter.css';
            $js[] = '/modules/Tasting/js/tastingfilter.js';
            $pack[] = 'datepicker';
            $pack[] = 'dropbox';
            $pack[] = 'filterform';
        }
        if($contestinfo['can_view_user_access_list']){
            $content .= $this->XM->view->load('tasting/viewcontest_user_access',array('contest_id'=>$contest_id,'can_add'=>(isset($contestinfo['can_edit_user_access_list'])&&$contestinfo['can_edit_user_access_list']),'can_edit_users'=>(isset($contestinfo['can_edit_user_access_list'])&&$contestinfo['can_edit_user_access_list']),'contest_user_list'=>$this->XM->tasting->get_contest_user_access_list($contest_id),'can_refresh'=>$can_refresh,'actions'=>true),true);
            $css[] = '/modules/Tasting/css/viewcontest_user_access.css';
            $css[] = '/modules/User/css/userfilter.css';
            $js[] = '/modules/Tasting/js/viewcontest_user_access.js';
            $js[] = '/modules/User/js/userfilter.js';
            $pack[] = 'dropbox';
            $pack[] = 'filterform';
        }
        

        $this->XM->__wrapview($content, 
            null, array(
                'css'=>$css,
                'js'=>$js,
                'pack'=>$pack));
        return true;
    }
    public function conteststatisticsview($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $contest_id = (int)$relative_path[0];
        $vintage_id = (isset($relative_path[1])&&$relative_path[1])?(int)$relative_path[1]:null;
        $user_id = (isset($relative_path[2])&&$relative_path[2])?(int)$relative_path[2]:null;
        $contestinfo = $this->XM->tasting->get_contest($contest_id);
        if(!$contestinfo || !$contestinfo['can_view_statistics']){
            return false;
        }
        $css = array();
        $js = array();
        $pack = array();
        $content = $this->XM->view->load('tasting/viewcontest',array('contestinfo'=>$contestinfo,'status_list'=>$this->XM->tasting->get_contest_status_list(),'compact'=>true),true);
        $css[] = '/modules/Tasting/css/viewcontest.css';
        $js[] = '/modules/Tasting/js/viewcontest.js';


        if($vintage_id){
            if(($vintageinfo = $this->XM->product->get_vintage_info($vintage_id))===FALSE){
                return false;
            }
            $content .= $this->XM->view->load('product/viewvintage',array('vintageinfo'=>$vintageinfo,'compact'=>true),true);
            $css[] = '/modules/Product/css/viewvintage.css';
            $js[] = '/modules/Product/js/viewvintage.js';
            $pack[] = 'gallery';
        }
        if($user_id){
            if(($userinfo = $this->XM->user->get_user_info($user_id))===false){
                return false;
            }
            $content .= $this->XM->view->load('user/viewUser',array('userinfo'=>$userinfo,'expert_level_list'=>$this->XM->user->get_expert_level_list(),'can_approve_expert'=>$this->XM->user->check_privilege(\USER\PRIVILEGE_USER_APPROVE_EXPERT),'compact'=>true),true);
            $css[] = '/modules/User/css/viewuser.css';
            $js[] = '/modules/User/js/viewuser.js';
            $pack[] = 'dropbox';
        }

        $tabcontents = array();

        //nominations
        if(!$user_id){
            $showempty = false;
            $contest_nomination_list = $this->XM->tasting->get_contest_nomination_list($contest_id,$vintage_id,$showempty);
            if(!empty($contest_nomination_list)){
                $tabcontents[] = array(
                        'header'=>langTranslate('tasting', 'contest', 'Contest: Nomination List',  'Nomination List'),
                        'content'=>$this->XM->view->load('tasting/viewcontest_nominations',array('contest_id'=>$contest_id,'vintage_id'=>$vintage_id,'can_edit'=>(isset($contestinfo['can_edit_nominations'])&&$contestinfo['can_edit_nominations']),'contest_nomination_list'=>$contest_nomination_list,'expert_level_list'=>$this->XM->user->get_expert_level_list(),'showempty'=>$showempty,'actions'=>false,'can_refresh'=>true,'compact'=>false),true)
                    );
                $css[] = '/modules/Tasting/css/viewcontest_nominations.css';
                $js[] = '/modules/Tasting/js/viewcontest_nominations.js';
            }
        }

        if(!$vintage_id){
            //vintage filter
            $showfavourite = false;
            $show_only_personally_scored_filter_option = false;
            $showpersonalscore = false;
            if($this->XM->user->isLoggedIn()){
                $showfavourite = true;
                $show_only_personally_scored_filter_option = true;
                $showpersonalscore = true;
            }
            $showcompanyfavourite = false;
            if($this->XM->user->isInCompany()){
                $showcompanyfavourite = true;
            }
            
            $err = null;
            if(($attrvaltree = $this->XM->product->get_product_filter_attrval_tree(true,false,false,false,false,false,true,$err))===FALSE){
                $this->XM->addMessage($err, 0);
                $this->XM->__wrapview(null, null, null);
                return true;
            }
            $page = 1;
            $pagelimit = null;
            $count = null;
            $err = null;
            if(($tasting_list = $this->XM->tasting->filter_tasting(null, null, array(\TASTING\TASTING_STATUS_FINISHED), false, false, false, false, false, false, null, false, $contest_id, null, false, false, null, false, $page, $pagelimit, $count, $err)) === false){
                $this->XM->addMessage($err, 0);
                $this->XM->__wrapview(null, null, null);
                return true;
            }
            $tabcontents[] = array(
                    'header'=>langTranslate('tasting', 'tasting', 'Product List', 'Product List'),
                    'content'=>$this->XM->view->load('product/vintagefilter',array('attrvaltree'=>$attrvaltree,'show_all_scores'=>true,'tasting_list'=>$tasting_list,'contest_id'=>$contest_id,'contest_user_id'=>$user_id,'showcompanyfavourite'=>$showcompanyfavourite,'showfavourite'=>$showfavourite,'show_only_personally_scored_filter_option'=>$show_only_personally_scored_filter_option,'showpersonalscore'=>$showpersonalscore,'expert_level_list'=>$this->XM->user->get_expert_level_list(),'can_view_certificates'=>$contestinfo['can_view_certificates']),true)
                );
            $css[] = '/modules/Product/css/vintagefilter.css';
            $js[] = '/modules/Product/js/vintagefilter.js';
            $pack[] = 'dropbox';
            $pack[] = 'gallery';
            $pack[] = 'mask';
            $pack[] = 'filterform';
        }
        if(!$user_id){
            if(($attrvaltree = $this->XM->product->get_system_attrval_tree(17,null,$err))===FALSE){
                $this->XM->addMessage($err, 0);
                $this->XM->__wrapview(null, null, null);
                return true;
            }
            $tabcontents[] = array(
                    'header'=>langTranslate('tasting','tasting','Invite List', 'Invite List'),
                    'content'=>$this->XM->view->load('user/userfilter',array('attrvaltree'=>$attrvaltree,'expert_level_list'=>$this->XM->user->get_expert_level_list(),'showfavourite'=>$this->XM->user->isLoggedIn(),'showmycompany'=>$this->XM->user->isInCompany(),'showglobalexpertscores'=>$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_EXPERT_EVALUATION_SCORE),'contest_id'=>$contest_id,'contest_product_id'=>$vintage_id,'actions'=>false),true)
                );
            $css[] = '/modules/User/css/userfilter.css';
            $js[] = '/modules/User/js/userfilter.js';
            $pack[] = 'dropbox';
            $pack[] = 'filterform';
        }
        if($contestinfo['can_view_tasting_list']){
            $showstatus = false;
            $showowner = false;
            $showassessment = $contestinfo['can_assess'];
            $tabcontents[] = array(
                    'header'=>langTranslate('tasting', 'contest', 'Contest: Tasting List', 'Tasting List'),
                    'content'=>$this->XM->view->load('tasting/viewcontest_tastings',array('contest_id'=>$contest_id,'vintage_id'=>$vintage_id,'user_id'=>$user_id,'can_add'=>false,'contest_tasting_list'=>$this->XM->tasting->get_contest_tasting_list($contest_id,$vintage_id,$user_id,$showstatus,$showowner,$showassessment),'showstatus'=>$showstatus,'showowner'=>$showowner,'showassessment'=>$showassessment,'actions'=>false,'can_refresh'=>false),true)
                );
            $css[] = '/modules/Tasting/css/viewcontest_tastings.css';
            $js[] = '/modules/Tasting/js/viewcontest_tastings.js';
            $css[] = '/modules/Tasting/css/tastingfilter.css';
            $js[] = '/modules/Tasting/js/tastingfilter.js';
            
            $pack[] = 'datepicker';
            $pack[] = 'dropbox';
            $pack[] = 'filterform';
        }
        if(!empty($tabcontents)){
            $content .= $this->XM->view->load('main/tabcontent',array('contents'=>$tabcontents),true);
            $pack[] = 'tabcontent';
        }

        $this->XM->__wrapview(
            $content, 
            null, array('css'=>$css,'js'=>$js,'pack'=>$pack));
    }

    public function global_expert_evaluation_template(){
        if(($tasting_evaluation_data = $this->XM->tasting->get_expert_evaluation_data())===false){
            return false;
        }
        $this->XM->__wrapview(
            $this->XM->view->load('tasting/refresh_global_evaluation_form',null,true).
            $this->XM->view->load('tasting/viewtasting_expert_evaluation_options',array('expert_evaluation_template'=>true,'tasting_evaluation_data'=>$tasting_evaluation_data,'review_elements'=>$this->XM->product->get_review_elements(),'grape_variety_list'=>$this->XM->product->get_attr_list(\PRODUCT\GRAPE_ATTRIBUTE_GROUP_ID,false,false),'location_list'=>$this->XM->product->get_attr_list(\PRODUCT\LOCATION_ATTRIBUTE_GROUP_ID,false,false)),true),
            null, array('css'=>array('/modules/Tasting/css/refresh_global_evaluation_form.css','/modules/Tasting/css/viewtasting_expert_evaluation_options.css'),'js'=>array('/modules/Tasting/js/refresh_global_evaluation_form.js','/modules/Tasting/js/viewtasting_expert_evaluation_options.js')));
    }
    public function global_evaluation_tasting_list_for_user($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $user_id = (int)$relative_path[0];

    }


    //ajax
    public function ajax_modifyindex_tasting_product_vintage($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        $tpv_id = (int)$relative_path[0];
        $direction = (isset($_POST['direction'])&&$_POST['direction']==1)?1:-1;
        $err = null;
        if($this->XM->tasting->modifyindex_tasting_product_vintage($tpv_id,$direction,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    
    public function ajax_change_review_status_tasting_product_vintage($relative_path = array()){
        if(count($relative_path)!=2){
            return false;
        }
        $tpv_id = (int)$relative_path[0];
        $status = (int)$relative_path[1];
        $err = null;
        if($this->XM->tasting->change_review_status_tasting_product_vintage($tpv_id,$status,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_get_vintage_preparation_form($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        $tpv_id = (int)$relative_path[0];
        $err = null;
        if(($tasting_vintage_preparation_data = $this->XM->tasting->get_tasting_vintage_preparation_data($tpv_id, $err))===FALSE){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $tasting_vintage_preparation_list = $this->XM->tasting->get_tasting_vintage_preparation_list();
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>$this->XM->view->load('tasting/vintagepreparationform',array('tasting_vintage_preparation_data'=>$tasting_vintage_preparation_data,'tasting_vintage_preparation_list'=>$tasting_vintage_preparation_list),true))));
        return true;
    }
    public function ajax_vintage_preparation_change($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        if(!isset($_POST)){
            return false;
        }
        $tpv_id = (int)$relative_path[0];
        $preparation_type = isset($_POST['preparation-type'])?(int)$_POST['preparation-type']:0;
        $preparation_time = isset($_POST['preparation-time'])?(int)$_POST['preparation-time']:0;
        $err = null;
        if($this->XM->tasting->change_tasting_vintage_preparation($tpv_id,$preparation_type,$preparation_time,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_remove_tasting_product_vintage($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        $tpv_id = (int)$relative_path[0];
        $err = null;
        if($this->XM->tasting->remove_tasting_product_vintage($tpv_id,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_edit_tasting_product_vintage_form($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        $tpv_id = (int)$relative_path[0];
        
        $err = null;
        if(($tpv_info = $this->XM->tasting->get_tasting_product_vintage_edit_info($tpv_id,$err))===FALSE){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $vintage_id = $tpv_info['pv_id'];
        if(($vintageinfo = $this->XM->product->get_vintage_info($vintage_id))===FALSE){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1)));
            return true;
        }
        if(($attrvaltree = $this->XM->product->get_system_attrval_tree(16,array($tpv_info['volume']),$err))===FALSE){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>array('formtype'=>'vintageview','form'=>$this->XM->view->load('product/tastingform',array('vintage_id'=>$vintage_id,'attrvaltree'=>$attrvaltree,'tasting_product_vintage_info'=>$tpv_info,'vintageinfo'=>$vintageinfo),true).$this->XM->view->load('product/viewvintage',array('vintageinfo'=>$vintageinfo),true)))));
        return true;
    }
    public function ajax_add_tasting_product_vintage($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        if(!isset($_POST)||!isset($_POST['action'])||$_POST['action']!='edit_tasting_vintage'){
            return false;
        }
        $t_id = (int)$relative_path[0];
        $pv_id = isset($_POST['id'])?(int)$_POST['id']:0;
        $isprimeur = isset($_POST['primeur'])?(bool)$_POST['primeur']:false;
        $lot = isset($_POST['lot'])?$_POST['lot']:'';
        $attr = isset($_POST['attr'])?$_POST['attr']:array();
        $desc = isset($_POST['desc'])?$_POST['desc']:'';
        $nominate = isset($_POST['nominate'])?(bool)$_POST['nominate']:false;
        $blindname = (isset($_POST['blind'])&&$_POST['blind']&&isset($_POST['blindname']))?$_POST['blindname']:null;
        $err = null;
        if($this->XM->tasting->add_tasting_product_vintage($t_id,$pv_id,$isprimeur,$lot,$attr,$blindname,$desc,$nominate,false,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>langTranslate('tasting','tasting','Product has been successfully added to the tasting', 'Product has been successfully added to the tasting'))));
        return true;
    }
    public function ajax_edit_tasting_product_vintage($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        if(!isset($_POST)||!isset($_POST['action'])||$_POST['action']!='edit_tasting_vintage'){
            return false;
        }
        $tpv_id = (int)$relative_path[0];
        $isprimeur = isset($_POST['primeur'])?(bool)$_POST['primeur']:false;
        $lot = isset($_POST['lot'])?$_POST['lot']:'';
        $attr = isset($_POST['attr'])?$_POST['attr']:array();
        $desc = isset($_POST['desc'])?$_POST['desc']:'';
        $nominate = isset($_POST['nominate'])?(bool)$_POST['nominate']:false;
        $blindname = (isset($_POST['blind'])&&$_POST['blind']&&isset($_POST['blindname']))?$_POST['blindname']:null;
        $err = null;
        if($this->XM->tasting->edit_tasting_product_vintage($tpv_id,$isprimeur,$lot,$attr,$blindname,$desc,$nominate,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>langTranslate('tasting','tasting','Product has been successfully edited', 'Product has been successfully edited'))));
        return true;
    }
    
    public function ajax_remove_tasting_user($relative_path = array()){
        if(count($relative_path)!=2){
            return false;
        }
        $user_id = (int)$relative_path[0];
        $t_id = (int)$relative_path[1];
        $err = null;
        if($this->XM->tasting->remove_tasting_user($t_id,$user_id,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_invite_tasting_user($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        if(!isset($_POST)){
            return false;
        }
        $t_id = (int)$relative_path[0];
        $user_id = isset($_POST['id'])?(int)$_POST['id']:0;
        $err = null;
        if($this->XM->tasting->invite_tasting_user($t_id,$user_id,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>langTranslate('tasting','tasting','User has been successfully invited', 'User has been successfully invited'))));
        return true;
    }
    public function ajax_tasting_user_respond($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        if(!isset($_POST)){
            return false;
        }
        $t_id = (int)$relative_path[0];
        $response = isset($_POST['response'])?(int)$_POST['response']:null;
        $err = null;
        if($this->XM->tasting->tasting_user_respond($t_id,$response,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>langTranslate('tasting','tasting','Thank you for your response. It has been taken into account', 'Thank you for your response. It has been taken into account'))));
        return true;
    }
    public function ajax_tasting_user_mark_presence($relative_path = array()){
        if(count($relative_path)!=2){
            return false;
        }
        if(!isset($_POST)||!isset($_POST['present'])){
            return false;
        }
        $user_id = (int)$relative_path[0];
        $t_id = (int)$relative_path[1];
        $present = (bool)$_POST['present'];
        $err = null;
        if($this->XM->tasting->mark_presence_tasting_user($t_id,$user_id,$present,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    
    public function ajax_get_tasting_user_list($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $tasting_id = (int)$relative_path[0];
        $only_present = (isset($_POST['only_present'])&&$_POST['only_present']);
        $show_response = (isset($_POST['show_response'])&&$_POST['show_response']);
        $show_background = (isset($_POST['show_background'])&&$_POST['show_background']);
        $evaluation_scores = (isset($_POST['evaluation_scores'])&&$_POST['evaluation_scores']);
        $show_global_expert_automatic_evaluation = (isset($_POST['show_global_expert_automatic_evaluation'])&&$_POST['show_global_expert_automatic_evaluation']);
        $product_id = (isset($_POST['product_id'])&&$_POST['product_id'])?$_POST['product_id']:null;
        $user_id = (isset($_POST['user_id'])&&$_POST['user_id'])?$_POST['user_id']:null;
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>$this->XM->tasting->get_tasting_user_list($tasting_id,$only_present,$show_response,$show_background,$evaluation_scores,$show_global_expert_automatic_evaluation,$product_id,false,$user_id))));
        return true;
    }
    public function ajax_search_tasting(){
        if(!isset($_POST) || !isset($_POST['action']) || $_POST['action']!='tasting_filter'){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>langTranslate('product', 'err', 'Internal Error',  'Internal Error'))));
            return true;
        }
        $start_date_from = isset($_POST['start_date_from'])?$_POST['start_date_from']:'';
        $start_date_to = isset($_POST['start_date_to'])?$_POST['start_date_to']:'';
        $status = isset($_POST['status'])?$_POST['status']:array();
        $only_owned = (isset($_POST['only_owned'])&&$_POST['only_owned']);
        $currently_participating = (isset($_POST['currently_participating'])&&$_POST['currently_participating']);
        $only_pending_reviews = (isset($_POST['only_pending_reviews'])&&$_POST['only_pending_reviews']);
        $only_took_part = (isset($_POST['only_took_part'])&&$_POST['only_took_part']);
        $took_part_vintage_id = isset($_POST['took_part_vintage_id'])?(int)$_POST['took_part_vintage_id']:null;
        $show_attendance_response = (isset($_POST['show_attendance_response'])&&$_POST['show_attendance_response']);
        $can_add_to_contest = isset($_POST['can_add_to_contest'])?$_POST['can_add_to_contest']:false;
        $used_in_contest = isset($_POST['used_in_contest'])?$_POST['used_in_contest']:false;
        $global_expert_ratings_for_user = isset($_POST['global_expert_ratings_for_user'])?$_POST['global_expert_ratings_for_user']:false;
        $only_approved = (isset($_POST['only_approved'])&&$_POST['only_approved']);
        $only_for_assessment = (isset($_POST['only_for_assessment'])&&$_POST['only_for_assessment']);
        $show_location = false;
        $page = isset($_POST['page'])?(int)$_POST['page']:1;
        $pagelimit = isset($_POST['pagelimit'])?(int)$_POST['pagelimit']:50;
        $order_by_field = isset($_POST['orderbyfield'])?$_POST['orderbyfield']:null;
        $order_by_direction_asc = isset($_POST['orderbydirection'])&&$_POST['orderbydirection']?true:false;
        $err = null;
        $count = 0;
        if(($list = $this->XM->tasting->filter_tasting($start_date_from, $start_date_to, $status, $only_owned, $only_approved, $only_for_assessment, $currently_participating, $only_pending_reviews, $only_took_part, $took_part_vintage_id, $can_add_to_contest, $used_in_contest, $global_expert_ratings_for_user, $show_attendance_response, $show_location, $order_by_field, $order_by_direction_asc, $page, $pagelimit, $count, $err)) === false){
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
    public function ajax_tasting_status_change($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        if(!isset($_POST)){
            return false;
        }
        $t_id = (int)$relative_path[0];
        $status = isset($_POST['status'])?(int)$_POST['status']:null;
        $err = null;
        if($this->XM->tasting->change_tasting_status($t_id,$status,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_tasting_assess($relative_path = array()){
        if(count($relative_path)!=2){
            return false;
        }
        $tasting_id = (int)$relative_path[0];
        $assessment = (bool)$relative_path[1];
        $err = null;
        if($this->XM->tasting->assess_tasting($tasting_id,$assessment,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_tasting_evaluation_change($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        if(!isset($_POST)){
            return false;
        }
        $t_id = (int)$relative_path[0];
        $evaluation_automatic = isset($_POST['evaluation_automatic'])?(int)$_POST['evaluation_automatic']:0;
        $evaluation_manual = isset($_POST['evaluation_manual'])?(int)$_POST['evaluation_manual']:0;
        $automatic_scores = $_POST;
        unset($automatic_scores['evaluation_automatic']);
        unset($automatic_scores['evaluation_manual']);
        $err = null;
        if($this->XM->tasting->edit_tasting_evaluation_data($t_id,$evaluation_manual,$evaluation_automatic,$automatic_scores,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>langTranslate('tasting','expert evaluation','Evaluation points have been successfully modified', 'Evaluation points have been successfully modified'))));
        return true;
    }
    public function ajax_global_expert_evaluation_template_set($relative_path = array()){
        if(!isset($_POST)){
            return false;
        }
        $automatic_scores = $_POST;
        unset($automatic_scores['evaluation_automatic']);
        unset($automatic_scores['evaluation_manual']);
        $err = null;
        if($this->XM->tasting->edit_global_expert_evaluation_data($automatic_scores,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>langTranslate('tasting','expert evaluation','Evaluation points have been successfully modified', 'Evaluation points have been successfully modified'))));
        return true;
    }
    public function ajax_global_expert_evaluation_refresh($relative_path = array()){
        if(!$this->XM->tasting->__refresh_global_expert_evaluations()){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>langTranslate('tasting', 'err', 'Access Denied',  'Access Denied'))));
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>langTranslate('tasting','expert evaluation','Global expert evaluation has been refreshed', 'Global expert evaluation has been refreshed'))));
        return true;
    }
    
    public function ajax_tasting_particularity_change($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        if(!isset($_POST)){
            return false;
        }
        $t_id = (int)$relative_path[0];
        $skip_options = array();
        foreach($_POST as $key=>$value){
            if((int)$value==0){
                $skip_options[] = trim($key);
            }
        }
        $err = null;
        if($this->XM->tasting->edit_tasting_review_particularity_data($t_id,$skip_options,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>langTranslate('product','review particularity','Review particularity have been successfully modified', 'Review particularity have been successfully modified'))));
        return true;
    }
    public function ajax_tastingswapreviews_swap($relative_path = array()){
        if(count($relative_path)!=2){
            return false;
        }
        if(!isset($_POST)){
            return false;
        }
        $tasting_id = (int)$relative_path[0];
        $user_id = (int)$relative_path[1];
        $ids = isset($_POST['ids'])&&is_array($_POST['ids'])?$_POST['ids']:array();
        $err = null;
        if($this->XM->tasting->tasting_reviews_swap($tasting_id,$user_id,$ids,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>langTranslate('tasting','swap reviews','User reviews were successfully swapped', 'User reviews were successfully swapped'))));
        return true;
    }
    public function ajax_block_review_tasting_product_user($relative_path = array()){
        if(count($relative_path)!=2){
            return false;
        }
        if(!isset($_POST)){
            return false;
        }
        $tpv_id = (int)$relative_path[0];
        $user_id = (int)$relative_path[1];
        $block = isset($_POST['block'])&&$_POST['block'];
        if($this->XM->product->block_review_for_tasting_product_user($tpv_id,$user_id,$block,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_search_contest(){
        if(!isset($_POST) || !isset($_POST['action']) || $_POST['action']!='contest_filter'){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>langTranslate('product', 'err', 'Internal Error',  'Internal Error'))));
            return true;
        }
        $start_date_from = isset($_POST['start_date_from'])?$_POST['start_date_from']:'';
        $start_date_to = isset($_POST['start_date_to'])?$_POST['start_date_to']:'';
        $status = isset($_POST['status'])?$_POST['status']:array();
        $only_owned = (isset($_POST['only_owned'])&&$_POST['only_owned']);
        $only_organized = (isset($_POST['only_organized'])&&$_POST['only_organized']);
        $only_approved = (isset($_POST['only_approved'])&&$_POST['only_approved']);
        $only_for_assessment = (isset($_POST['only_for_assessment'])&&$_POST['only_for_assessment']);
        $only_participated = (isset($_POST['only_participated'])&&$_POST['only_participated']);
        
        $page = isset($_POST['page'])?(int)$_POST['page']:1;
        $pagelimit = isset($_POST['pagelimit'])?(int)$_POST['pagelimit']:50;
        $order_by_field = isset($_POST['orderbyfield'])?$_POST['orderbyfield']:null;
        $order_by_direction_asc = isset($_POST['orderbydirection'])&&$_POST['orderbydirection']?true:false;
        $err = null;
        $count = 0;
        if(($list = $this->XM->tasting->filter_contest($start_date_from, $start_date_to, $status, $only_owned, $only_organized, $only_approved, $only_for_assessment, $only_participated, $order_by_field, $order_by_direction_asc, $page, $pagelimit, $count, $err)) === false){
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
    public function ajax_get_contest_user_access_list($relative_path = array()){
        if(count($relative_path)<1){
            return false;
        }
        $contest_id = (int)$relative_path[0];
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>$this->XM->tasting->get_contest_user_access_list($contest_id))));
        return true;
    }
    public function ajax_grant_contest_user_access($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        if(!isset($_POST)){
            return false;
        }
        $contest_id = (int)$relative_path[0];
        $user_id = isset($_POST['id'])?(int)$_POST['id']:0;
        $err = null;
        if($this->XM->tasting->grant_contest_user_access($contest_id,$user_id,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>langTranslate('tasting','contest','Access has been successfully granted', 'Access has been successfully granted'))));
        return true;
    }




    public function ajax_get_contest_tasting_list($relative_path = array()){
        if(count($relative_path)<1 || !isset($_POST)){
            return false;
        }
        $contest_id = (int)$relative_path[0];
        $vintage_id = isset($relative_path[1])?(int)$relative_path[1]:null;
        $user_id = isset($relative_path[2])?(int)$relative_path[2]:null;
        $showstatus = isset($_POST['showstatus'])&&$_POST['showstatus'];
        $showowner = isset($_POST['showowner'])&&$_POST['showowner'];
        $showassessment = isset($_POST['showassessment'])&&$_POST['showassessment'];
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>$this->XM->tasting->get_contest_tasting_list($contest_id,$vintage_id,$user_id,$showstatus,$showowner,$showassessment))));
        return true;
    }
    public function ajax_get_tasting_filter_form(){
        $can_add = false;
        $status_list = $this->XM->tasting->get_status_list();
        unset($status_list[\TASTING\TASTING_STATUS_DELETED]);
        $contest = (isset($_POST)&&isset($_POST['contest'])&&$_POST['contest'])?(int)$_POST['contest']:false;
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>$this->XM->view->load('tasting/tastingfilter',array('statuslist'=>$status_list,'can_add'=>$can_add,'contest'=>$contest),true))));
        return true;
    }
    public function ajax_add_contest_tasting($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        if(!isset($_POST)){
            return false;
        }
        $contest_id = (int)$relative_path[0];
        $tasting_id = isset($_POST['id'])?(int)$_POST['id']:0;
        $err = null;
        if($this->XM->tasting->add_contest_tasting($contest_id,$tasting_id,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'successmsg'=>langTranslate('tasting','contest','Tasting has been successfully added', 'Tasting has been successfully added'))));
        return true;
    }
    public function ajax_revoke_contest_user_access($relative_path = array()){
        if(count($relative_path)!=2){
            return false;
        }
        $contest_id = (int)$relative_path[0];
        $user_id = (int)$relative_path[1];
        $err = null;
        if($this->XM->tasting->revoke_contest_user_access($contest_id,$user_id,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_remove_contest_tasting($relative_path = array()){
        if(count($relative_path)!=2){
            return false;
        }
        $contest_id = (int)$relative_path[0];
        $tasting_id = (int)$relative_path[1];
        $err = null;
        if($this->XM->tasting->remove_contest_tasting($contest_id,$tasting_id,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_contest_delete($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        $contest_id = (int)$relative_path[0];
        $err = null;
        if($this->XM->tasting->delete_contest($contest_id,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_contest_status_change($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        if(!isset($_POST)){
            return false;
        }
        $contest_id = (int)$relative_path[0];
        $status = isset($_POST['status'])?(int)$_POST['status']:null;
        $err = null;
        if($this->XM->tasting->change_contest_status($contest_id,$status,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_contest_assess($relative_path = array()){
        if(count($relative_path)!=2){
            return false;
        }
        $contest_id = (int)$relative_path[0];
        $assessment = (bool)$relative_path[1];
        $err = null;
        if($this->XM->tasting->assess_contest($contest_id,$assessment,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_contest_nomination_form($relative_path = array()){
        $tcn_id = (!empty($relative_path)&&isset($relative_path[0]))?(int)$relative_path[0]:null;
        $nomination_info = $tcn_id?$this->XM->tasting->get_contest_nomination_info($tcn_id):array('name'=>null);
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>$this->XM->view->load('tasting/contestnominationform',array('nomination_info'=>$nomination_info),true))));
        return true;
    }
    public function ajax_add_contest_nomination($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        if(!isset($_POST)){
            return false;
        }
        $contest_id = (int)$relative_path[0];
        $name = isset($_POST['name'])?$_POST['name']:'';
        $err = null;
        if($this->XM->tasting->add_contest_nomination($contest_id,$name,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
    }
    public function ajax_edit_contest_nomination($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        if(!isset($_POST)){
            return false;
        }
        $tcn_id = (int)$relative_path[0];
        $name = isset($_POST['name'])?$_POST['name']:'';
        $err = null;
        if($this->XM->tasting->edit_contest_nomination($tcn_id,$name,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
    }
    public function ajax_remove_contest_nomination($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        $tcn_id = (int)$relative_path[0];
        $err = null;
        if($this->XM->tasting->remove_contest_nomination($tcn_id,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
    }
    public function ajax_get_contest_nomination_list($relative_path = array()){
        if(count($relative_path)<1 || !isset($_POST)){
            return false;
        }
        $contest_id = (int)$relative_path[0];
        $vintage_id = isset($relative_path[1])?(int)$relative_path[1]:null;
        $showempty = isset($_POST['showempty'])&&$_POST['showempty'];
        $product = isset($_POST['product'])?(int)$_POST['product']:null;
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>$this->XM->tasting->get_contest_nomination_list($contest_id,$product,$showempty))));
        return true;
    }
    
    public function ajax_modifyindex_contest_vintage($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        $tcn_id = (int)$relative_path[0];
        $direction = (isset($_POST['direction'])&&$_POST['direction']==1)?1:-1;
        $err = null;
        if($this->XM->tasting->modifyindex_contest_nomination($tcn_id,$direction,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
        return true;
    }
    public function ajax_contest_nomination_winner_form($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        $tcn_id = (int)$relative_path[0];
        $vintage_id = isset($_POST)&&isset($_POST['vid'])?(int)$_POST['vid']:null;
        if(($vintageinfo = $this->XM->product->get_vintage_info($vintage_id))===FALSE){
            return false;
        }
        
        $this->XM->view->load('view/json',array('data'=>array('success'=>1,'data'=>
            $this->XM->view->load('product/viewvintage',array('vintageinfo'=>$vintageinfo,'compact'=>true),true).
            $this->XM->view->load('tasting/contestnominationwinnerform',array('vintage_id'=>$vintage_id,'place'=>$this->XM->tasting->get_nomination_next_place($tcn_id)),true))));
        return true;
    }
    public function ajax_add_contest_nomination_winner($relative_path = array()){
        if(count($relative_path)!=1){
            return false;
        }
        if(!isset($_POST)){
            return false;
        }
        $nomination_id = (int)$relative_path[0];
        $vintage_id = isset($_POST['vid'])?(int)$_POST['vid']:null;
        $place = isset($_POST['place'])?(int)$_POST['place']:null;
        $err = null;
        if($this->XM->tasting->add_contest_nomination_winner($nomination_id,$vintage_id,$place,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
    }
    public function ajax_remove_contest_nomination_winner($relative_path = array()){
        if(count($relative_path)!=2){
            return false;
        }
        $nomination_id = (int)$relative_path[0];
        $vintage_id = (int)$relative_path[1];
        $err = null;
        if($this->XM->tasting->remove_contest_nomination_winner($nomination_id,$vintage_id,$err) === false){
            $this->XM->view->load('view/json',array('data'=>array('err'=>1,'errmsg'=>$err)));
            return true;
        }
        $this->XM->view->load('view/json',array('data'=>array('success'=>1)));
    }
}
