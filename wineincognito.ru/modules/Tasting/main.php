<?php
namespace Tasting;
if(!defined('IS_XMODULE')){
    exit();
}

DEFINE('TASTING\TASTING_STATUS_DELETED',8);
DEFINE('TASTING\TASTING_STATUS_DRAFT',0);
DEFINE('TASTING\TASTING_STATUS_PREPARATION',1);
DEFINE('TASTING\TASTING_STATUS_STARTED',2);
DEFINE('TASTING\TASTING_STATUS_FINISHED',3);

DEFINE('TASTING\CONTEST_STATUS_DRAFT',0);
DEFINE('TASTING\CONTEST_STATUS_PREPARATION',1);
DEFINE('TASTING\CONTEST_STATUS_SUMMING_UP',2);
DEFINE('TASTING\CONTEST_STATUS_FINISHED',3);

DEFINE('TASTING\TASTING_USER_RESPONSE_PENDING',0);
DEFINE('TASTING\TASTING_USER_RESPONSE_ACCEPT',1);
DEFINE('TASTING\TASTING_USER_RESPONSE_DECLINE',2);
DEFINE('TASTING\TASTING_USER_RESPONSE_UNCERTAIN',3);
DEFINE('TASTING\TASTING_USER_RESPONSE_INTERESTED_BUT_CANT',4);

DEFINE('TASTING\TASTING_SCORE_DRAW_ATTENTION_DELTA',5);

DEFINE('TASTING\EVALUATION_CUSTOM_PARAM_SUBCOLOR_DEPTH_PVRPL_ID',139);

DEFINE('TASTING\EVALUATION_LENIENCY_PERCENT',15);

DEFINE('TASTING\GLOBAL_EVALUATION_MIN_RANK_3_EXPERT_COUNT',2);
DEFINE('TASTING\GLOBAL_EVALUATION_MIN_EXPERT_COUNT',2);
DEFINE('TASTING\GLOBAL_EVALUATION_STORAGE_TIME',31536000);//365 days
DEFINE('TASTING\GLOBAL_EVALUATION_TASTING_LENIENCY_PERCENT',15);
DEFINE('TASTING\GLOBAL_EVALUATION_NON_ZERO_EVALUATION_PERCENT',30);
DEFINE('TASTING\GLOBAL_EVALUATION_LENIENCY_PERCENT',15);

require_once ABS_PATH.'/interface/main.php';

class Main extends \AbstractNS\Main{
    public function get_tasting_vintage_preparation_list(){
        return array(
                0=>langTranslate('tasting','preparation','None','None'),
                1=>langTranslate('tasting','preparation','Bottle opening','Bottle opening'),
                2=>langTranslate('tasting','preparation','Decantation','Decantation'),
            );
    }
    public function get_status_list(){
        return array(
                \TASTING\TASTING_STATUS_DELETED=>langTranslate('tasting','status','Deleted','Deleted'),
                \TASTING\TASTING_STATUS_DRAFT=>langTranslate('tasting','status','Draft','Draft'),
                \TASTING\TASTING_STATUS_PREPARATION=>langTranslate('tasting','status','Preparation','Preparation'),
                \TASTING\TASTING_STATUS_STARTED=>langTranslate('tasting','status','Started','Started'),
                \TASTING\TASTING_STATUS_FINISHED=>langTranslate('tasting','status','Finished','Finished'),
            );
    }
    public function get_user_response_list($with_pending = false){
        if($with_pending){
            return array(
                    \TASTING\TASTING_USER_RESPONSE_PENDING=>langTranslate('tasting','userresponse','Pending','Pending'),
                    \TASTING\TASTING_USER_RESPONSE_ACCEPT=>langTranslate('tasting','userresponse','Accept','Accept'),
                    \TASTING\TASTING_USER_RESPONSE_DECLINE=>langTranslate('tasting','userresponse','Decline','Decline'),
                    \TASTING\TASTING_USER_RESPONSE_UNCERTAIN=>langTranslate('tasting','userresponse','Uncertain','Uncertain'),
                    \TASTING\TASTING_USER_RESPONSE_INTERESTED_BUT_CANT=>langTranslate('tasting','userresponse','Interested, but can\'t attend','Interested, but can\'t attend'),
                );
        }
        return array(
                \TASTING\TASTING_USER_RESPONSE_ACCEPT=>langTranslate('tasting','userresponse','Accept','Accept'),
                \TASTING\TASTING_USER_RESPONSE_DECLINE=>langTranslate('tasting','userresponse','Decline','Decline'),
                \TASTING\TASTING_USER_RESPONSE_UNCERTAIN=>langTranslate('tasting','userresponse','Uncertain','Uncertain'),
                \TASTING\TASTING_USER_RESPONSE_INTERESTED_BUT_CANT=>langTranslate('tasting','userresponse','Interested, but can\'t attend','Interested, but can\'t attend'),
            );
        
    }
    public function get_tasting_user_list($tasting_id,$only_present,$show_response,$show_background,$evaluation_scores,$show_global_expert_automatic_evaluation,$tpv_id,$reviewed_only,$single_user_id){
        $tasting_id = (int)$tasting_id;
        $tpv_id = (int)$tpv_id;
        $single_user_id = (int)$single_user_id;

        $res = $this->XM->sqlcore->query('SELECT user_id,t_score_method from tasting where t_id = '.$tasting_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return array();
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_TASTINGS) && $this->XM->user->getUserId()!=(int)$row['user_id']){
            return array();
        }
        $tasting_score_method = (int)$row['t_score_method'];

        $user_response_list = null;
        if($show_response){
            $user_response_list = $this->get_user_response_list(true);
        }
        $show_background_select_sql = 'null as background';
        $show_background_left_join = '';
        if($show_background){
            $show_background_select_sql = 'user_background.background';
            $show_background_left_join = 'left join (
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
            ) user_background on user_background.pav_id = user.user_background';
        }
        $evaluation_scores_select_sql = 'null as manual_evaluation_score, null as automatic_evaluation_score';
        $evaluation_scores_left_join = '';
        $evaluation_scores_left_join_order_by_sql = '';
        if($evaluation_scores){
            $evaluation_scores_select_sql = 'manual_evaluation_score.score as manual_evaluation_score,automatic_evaluation_score.score as automatic_evaluation_score';
            $evaluation_scores_left_join_order_by_sql = 'coalesce(manual_evaluation_score.score,0)+coalesce(automatic_evaluation_score.score,0) desc,';
            if($tpv_id){
                $evaluation_scores_left_join = '
                    left join (
                            SELECT tasting_user_evaluation_user_score.user_id, avg(floor(tasting_user_evaluation_user_score.tueus_score*10000/maxscores.tueus_score)) as score
                                from tasting_user_evaluation
                                inner join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id
                                inner join (
                                    select max(tasting_user_evaluation_user_score.tueus_score) as tueus_score, tasting_user_evaluation.tpv_id
                                        from tasting_user_evaluation 
                                        inner join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id
                                        where tasting_user_evaluation.t_id = '.$tasting_id.' and tasting_user_evaluation.tue_type = 1 and tasting_user_evaluation.tpv_id = '.$tpv_id.'
                                        group by tasting_user_evaluation.tpv_id
                                ) as maxscores on maxscores.tpv_id = tasting_user_evaluation.tpv_id
                                where tasting_user_evaluation.t_id = '.$tasting_id.' and tasting_user_evaluation.tue_type = 1 and tasting_user_evaluation.tpv_id = '.$tpv_id.' '.($single_user_id?'and tasting_user_evaluation_user_score.user_id = '.$single_user_id:'').'
                                group by tasting_user_evaluation_user_score.user_id
                        ) as manual_evaluation_score on manual_evaluation_score.user_id = tasting_user.user_id
                    left join (
                            SELECT tasting_user_evaluation_user_score.user_id, avg(floor(tasting_user_evaluation_user_score.tueus_score*10000/maxscores.tueus_score)) as score
                                from tasting_user_evaluation
                                inner join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id
                                inner join (
                                    select max(tasting_user_evaluation_user_score.tueus_score) as tueus_score, tasting_user_evaluation.tpv_id
                                        from tasting_user_evaluation 
                                        inner join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id
                                        where tasting_user_evaluation.t_id = '.$tasting_id.' and tasting_user_evaluation.tue_type = 2 and tasting_user_evaluation.tpv_id = '.$tpv_id.'
                                        group by tasting_user_evaluation.tpv_id
                                ) as maxscores on maxscores.tpv_id = tasting_user_evaluation.tpv_id
                                where tasting_user_evaluation.t_id = '.$tasting_id.' and tasting_user_evaluation.tue_type = 2 and tasting_user_evaluation.tpv_id = '.$tpv_id.' '.($single_user_id?'and tasting_user_evaluation_user_score.user_id = '.$single_user_id:'').'
                                group by tasting_user_evaluation_user_score.user_id
                        ) as automatic_evaluation_score on automatic_evaluation_score.user_id = tasting_user.user_id';
            } else {




                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE tasting_user_list_evaluation_scores (
                    `tules_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `tpv_id` bigint(20) UNSIGNED NOT NULL,
                    `tue_type` tinyint(1) UNSIGNED NOT NULL,
                    `user_id` int(10) UNSIGNED NOT NULL,
                    `tueus_score` int(5) UNSIGNED NOT NULL,
                    PRIMARY KEY tasting_user_list_evaluation_scores_pk (tules_id),
                    INDEX tasting_user_list_evaluation_scores_tpv_id_index (tpv_id)
                )');
                $this->XM->sqlcore->query('INSERT INTO tasting_user_list_evaluation_scores
                    SELECT null,tasting_user_evaluation.tpv_id,tasting_user_evaluation.tue_type,tasting_user.user_id,floor(coalesce(tasting_user_evaluation_user_score.tueus_score,0)*10000/tasting_user_list_evaluation_scores_max_scores.tueus_score) as tueus_score
                    from tasting_user_evaluation
                    inner join tasting_user on tasting_user.t_id = tasting_user_evaluation.t_id
                    inner join (
                            select tasting_user_evaluation.tue_id,max(tasting_user_evaluation_user_score.tueus_score) as tueus_score
                                from tasting_user_evaluation
                                inner join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id
                                where tasting_user_evaluation.t_id = '.$tasting_id.'
                                group by tasting_user_evaluation.tue_id
                        ) as tasting_user_list_evaluation_scores_max_scores on tasting_user_list_evaluation_scores_max_scores.tue_id = tasting_user_evaluation.tue_id
                    left join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id and tasting_user_evaluation_user_score.user_id = tasting_user.user_id
                    where tasting_user_evaluation.t_id = '.$tasting_id.' '.($single_user_id?'and tasting_user.user_id = '.$single_user_id:'').' and tasting_user_evaluation.tue_type in (1,2)');
                
                //leniency
                $this->XM->tasting->preload();
                $res = $this->XM->sqlcore->query('CREATE TEMPORARY TABLE tasting_user_list_evaluation_score_leniency
                    SELECT tasting_user_list_evaluation_scores.tue_type,tasting_user_list_evaluation_scores.user_id,floor(count(1)*'.\TASTING\EVALUATION_LENIENCY_PERCENT.'/100) as leniency
                    from tasting_user_list_evaluation_scores
                    group by tasting_user_list_evaluation_scores.tue_type,tasting_user_list_evaluation_scores.user_id
                    having floor(count(1)*'.\TASTING\EVALUATION_LENIENCY_PERCENT.'/100) > 0');

                $delete_tules_ids = array();

                $res = $this->XM->sqlcore->query('SELECT substring_index(group_concat(tasting_user_list_evaluation_scores.tules_id order by tasting_user_list_evaluation_scores.tueus_score asc),\',\',tasting_user_list_evaluation_score_leniency.leniency) as tules_ids
                from tasting_user_list_evaluation_scores
                inner join tasting_user_list_evaluation_score_leniency on tasting_user_list_evaluation_score_leniency.user_id = tasting_user_list_evaluation_scores.user_id and tasting_user_list_evaluation_score_leniency.tue_type = tasting_user_list_evaluation_scores.tue_type
                group by tasting_user_list_evaluation_scores.user_id,tasting_user_list_evaluation_scores.tue_type,tasting_user_list_evaluation_score_leniency.leniency');
                while($row = $this->XM->sqlcore->getRow($res)){
                    $tules_ids = explode(',', $row['tules_ids']);
                    foreach($tules_ids as $tules_id){
                        $delete_tules_ids[] = (int)$tules_id;
                    }
                }
                if(!empty($delete_tules_ids)){
                    $delete_tules_ids_chunks = array_chunk($delete_tules_ids, 100);
                    foreach($delete_tules_ids_chunks as $delete_tules_ids_chunk){
                        $this->XM->sqlcore->query('DELETE FROM tasting_user_list_evaluation_scores where tules_id in ('.implode(',', $delete_tules_ids).')');
                    }
                }

                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE tasting_user_list_evaluation_scores_manual
                    (PRIMARY KEY tasting_user_list_evaluation_scores_manual_pkey (user_id))
                    select tasting_user_list_evaluation_scores.user_id, avg(tueus_score) as score
                        from tasting_user_list_evaluation_scores
                        where tasting_user_list_evaluation_scores.tue_type = 1
                        group by tasting_user_list_evaluation_scores.user_id');
                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE tasting_user_list_evaluation_scores_automatic
                    (PRIMARY KEY tasting_user_list_evaluation_scores_automatic_pkey (user_id))
                    select tasting_user_list_evaluation_scores.user_id, avg(tueus_score) as score
                        from tasting_user_list_evaluation_scores
                        where tasting_user_list_evaluation_scores.tue_type = 2
                        group by tasting_user_list_evaluation_scores.user_id');
                $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS tasting_user_list_evaluation_scores');

                $evaluation_scores_left_join = 'left join tasting_user_list_evaluation_scores_manual as manual_evaluation_score on manual_evaluation_score.user_id = tasting_user.user_id
                    left join tasting_user_list_evaluation_scores_automatic as automatic_evaluation_score on automatic_evaluation_score.user_id = tasting_user.user_id';








            }
        }
        $global_expert_automatic_evaluation_scores_select_sql = 'null as global_expert_automatic_evaluation_score';
        $global_expert_automatic_evaluation_scores_left_join = '';
        if($show_global_expert_automatic_evaluation && $this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_EXPERT_EVALUATION_SCORE)){
            $global_expert_automatic_evaluation_scores_select_sql = 'global_expert_automatic_evaluation_score.score as global_expert_automatic_evaluation_score';
            if($tpv_id){
                $global_expert_automatic_evaluation_scores_left_join = 'left join (
                        SELECT tasting_user_evaluation_user_score.user_id, floor(tasting_user_evaluation_user_score.tueus_score*10000/maxscores.tueus_score) as score
                            from tasting_user_evaluation
                            inner join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id
                            inner join product_vintage_review on product_vintage_review.pvr_id = tasting_user_evaluation_user_score.pvr_id
                            inner join (
                                select max(tasting_user_evaluation_user_score.tueus_score) as tueus_score, tasting_user_evaluation.tpv_id, product_vintage_review.user_expert_level
                                    from tasting_user_evaluation 
                                    inner join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id
                                    inner join product_vintage_review on product_vintage_review.pvr_id = tasting_user_evaluation_user_score.pvr_id
                                    where tasting_user_evaluation.tpv_id = '.$tpv_id.' and tasting_user_evaluation.tue_type = 4
                                    group by tasting_user_evaluation.tpv_id, product_vintage_review.user_expert_level
                            ) as maxscores on maxscores.tpv_id = tasting_user_evaluation.tpv_id and maxscores.user_expert_level = product_vintage_review.user_expert_level
                            where tasting_user_evaluation.tpv_id = '.$tpv_id.' and tasting_user_evaluation.tue_type = 4 '.($single_user_id?'and tasting_user_evaluation_user_score.user_id = '.$single_user_id:'').'
                    ) as global_expert_automatic_evaluation_score on global_expert_automatic_evaluation_score.user_id = tasting_user.user_id';
            } else {
                $global_expert_automatic_evaluation_scores_left_join = 'left join (
                        SELECT tasting_user_global_evaluation_score.user_id, tasting_user_global_evaluation_score.tuges_score as score
                            from tasting_user_global_evaluation_score
                            inner join user on user.user_id = tasting_user_global_evaluation_score.user_id and user.user_expert_level = tasting_user_global_evaluation_score.user_expert_level
                            where tasting_user_global_evaluation_score.t_id = '.$tasting_id.' '.($single_user_id?'and tasting_user_global_evaluation_score.user_id = '.$single_user_id:'').'
                    ) as global_expert_automatic_evaluation_score on global_expert_automatic_evaluation_score.user_id = tasting_user.user_id';
            }
            
        }
        $review_info_select_sql = 'null as pvr_score, null as pvr_id, null as pvr_expert';
        $review_info_skip_sql = 'null as pvr_faulty, null as pvr_didnottaste';
        $review_info_moderator_block_sql = 'null as pvr_blocked_by_moderator';
        $review_info_join = '';
        $can_block_reviews = ($tasting_score_method==0 && $this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_BLOCK_REVIEW));
        if($tpv_id){
            switch($tasting_score_method){
                case 1:
                    $review_info_select_sql = 'tasting_product_vintage_ranking.tpvr_rank*100 as pvr_score, null as pvr_id, tasting_product_vintage_ranking.user_expert_level as pvr_expert';
                    $review_info_join = 'left join tasting_product_vintage_ranking on tasting_product_vintage_ranking.tpv_id = '.$tpv_id.' and tasting_product_vintage_ranking.t_id = tasting_user.t_id and tasting_product_vintage_ranking.user_id = tasting_user.user_id';
                    if($reviewed_only){
                        $review_info_select_sql = 'tasting_product_vintage_ranking.tpvr_rank*100 as pvr_score, null as pvr_id, tasting_product_vintage_ranking.user_expert_level as pvr_expert';
                        $review_info_join = 'inner join tasting_product_vintage_ranking on tasting_product_vintage_ranking.tpv_id = '.$tpv_id.' and tasting_product_vintage_ranking.t_id = tasting_user.t_id and tasting_product_vintage_ranking.user_id = tasting_user.user_id';
                    }
                    break;
                case 0:
                default:
                    $this->XM->product->load();
                    $review_info_skip_sql = 'product_vintage_review.pvr_faulty, product_vintage_review.pvr_didnottaste';
                    if($can_block_reviews){
                        $review_info_moderator_block_sql = 'if(product_vintage_review.pvr_block&'.\PRODUCT\PVR_BLOCK_BY_MODERATOR.',1,0) as pvr_blocked_by_moderator';
                    }
                    $review_info_select_sql = 'if(product_vintage_review.pvr_block&'.\PRODUCT\PVR_BLOCK_FAULTY_OR_MISSED.',null,product_vintage_review.pvr_score) as pvr_score, product_vintage_review.pvr_id, product_vintage_review.user_expert_level as pvr_expert';
                    $review_info_join = 'left join product_vintage_review on product_vintage_review.tpv_id = '.$tpv_id.' and product_vintage_review.t_id = tasting_user.t_id and product_vintage_review.user_id = tasting_user.user_id and product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE|\PRODUCT\PVR_BLOCK_FAULTY_OR_MISSED|($can_block_reviews?\PRODUCT\PVR_BLOCK_BY_MODERATOR:0)).' = 0';
                    if($reviewed_only){
                        $review_info_skip_sql = '0 as pvr_faulty, 0 as pvr_didnottaste';
                        $review_info_select_sql = 'product_vintage_review.pvr_score, product_vintage_review.pvr_id, product_vintage_review.user_expert_level as pvr_expert';
                        $review_info_join = 'inner join product_vintage_review on product_vintage_review.tpv_id = '.$tpv_id.' and product_vintage_review.t_id = tasting_user.t_id and product_vintage_review.user_id = tasting_user.user_id and product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE|($can_block_reviews?\PRODUCT\PVR_BLOCK_BY_MODERATOR:0)).' = 0';
                    }
            }

            
            
        }


        
        $res = $this->XM->sqlcore->query('SELECT user.user_id, user.user_expert_level, coalesce(user_ml.user_ml_fullname,\'-\') as user_ml_fullname, company_ml.company_id, coalesce(company_ml.company_ml_name,\'-\') as company_ml_name, '.$show_background_select_sql.', tasting_user.tu_response, tasting_user.tu_presence, '.$evaluation_scores_select_sql.', '.$review_info_select_sql.', '.$review_info_skip_sql.', '.$review_info_moderator_block_sql.', '.$global_expert_automatic_evaluation_scores_select_sql.'
            from tasting_user 
            inner join user on user.user_id = tasting_user.user_id
            '.$review_info_join.'
            left join (select user_ml.user_id,substring_index(group_concat(user_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as user_ml_id 
                from user_ml 
                inner join tasting_user on tasting_user.user_id = user_ml.user_id and tasting_user.t_id = '.$tasting_id.' '.($only_present?'and tasting_user.tu_presence = 1':'').' '.($single_user_id?'and tasting_user.user_id = '.$single_user_id:'').'
                where user_ml_is_approved = 1 group by user_id
            ) as ln_glue on ln_glue.user_id = user.user_id
            left join user_ml on user_ml.user_ml_id = ln_glue.user_ml_id
            left join (select company_ml.company_id,substring_index(group_concat(company_ml.company_ml_id order by company_ml.lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as company_ml_id 
                from company_ml 
                inner join company on company.company_id = company_ml.company_id and company.company_is_approved = 1 
                inner join user on user.company_id = company.company_id
                inner join tasting_user on tasting_user.user_id = user.user_id and tasting_user.t_id = '.$tasting_id.' '.($only_present?'and tasting_user.tu_presence = 1':'').' '.($single_user_id?'and tasting_user.user_id = '.$single_user_id:'').'
                where company_ml_is_approved = 1 and company_ml_name is not null group by company_ml.company_id
            ) as company_ln_glue on company_ln_glue.company_id = user.company_id
            left join company_ml on company_ml.company_ml_id = company_ln_glue.company_ml_id

            '.$show_background_left_join.'
            '.$evaluation_scores_left_join.'
            '.$global_expert_automatic_evaluation_scores_left_join.'

            where tasting_user.t_id = '.$tasting_id.' '.($only_present?'and tasting_user.tu_presence = 1':'').' '.($single_user_id?'and tasting_user.user_id = '.$single_user_id:'').'
            order by '.$evaluation_scores_left_join_order_by_sql.' 2 desc, 3 asc');
        $result = array();
        $expert_level_list = $this->XM->user->get_expert_level_list();
        while($row = $this->XM->sqlcore->getRow($res)){
            $responce_str = '';
            if($show_response){
                $response = (int)$row['tu_response'];
                $responce_str = isset($user_response_list[$response])?$user_response_list[$response]:'';
            }
            $result_row = array(
                    'id'=>(int)$row['user_id'],
                    'name'=>(string)$row['user_ml_fullname'],
                    'company_id'=>(int)$row['company_id'],
                    'company_name'=>(string)$row['company_ml_name'],
                    'background'=>(string)$row['background'],
                    'expert_level'=>isset($expert_level_list[$row['user_expert_level']])?$expert_level_list[$row['user_expert_level']]:null,
                    'isguest'=>($row['user_expert_level']>0)?0:1,
                    'response'=>$responce_str,
                    'presence'=>(bool)$row['tu_presence'],
                    'manual_evaluation_score'=>$row['manual_evaluation_score']?str_replace('.', ',', round($row['manual_evaluation_score'])/100):null,
                    'automatic_evaluation_score'=>$row['automatic_evaluation_score']?str_replace('.', ',', round($row['automatic_evaluation_score'])/100):null,
                    'global_expert_automatic_evaluation_score'=>$row['global_expert_automatic_evaluation_score']?str_replace('.', ',', round($row['global_expert_automatic_evaluation_score'])/100):null,
                );
            if($row['pvr_score']){
                $result_row['score'] = array('score'=>$row['pvr_score']?str_replace('.', ',', round($row['pvr_score'])/100):null,'expert_level'=>(int)$row['pvr_expert']);
                if($row['pvr_id']){
                    $result_row['score']['review_id']=(int)$row['pvr_id'];
                    if($row['pvr_faulty']){
                        $result_row['score']['faulty'] = 1;
                    }
                    if($row['pvr_didnottaste']){
                        $result_row['score']['didnottaste'] = 1;
                    }
                }
                
                if($can_block_reviews){
                    $result_row['can_block_review'] = 1;
                    if($row['pvr_blocked_by_moderator']){
                        $result_row['review_blocked_by_moderator'] = 1;
                    }
                }
                

            }
            $result[] = $result_row;
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }
    public function get_current_user_attendance_response($tasting_id){
        if(!$this->XM->user->isLoggedIn()){
            return false;
        }
        $tasting_id = (int)$tasting_id;
        $where_sql = '';
        if($this->XM->user->check_state(\USER\STATE_IS_APPROVED_EXPERT)){
            $where_sql = '( tasting.user_id = '.$this->XM->user->getUserId().' or ( tasting.t_status = '.\TASTING\TASTING_STATUS_PREPARATION.' and ( ( tasting.t_participation_type = 2 or tasting.t_participation_type = 1 and tasting.t_participation_rating_limit <= '.$this->XM->user->getExpertRating().' ) and tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.' or tasting_user.user_id is not null and ( tasting_user.tu_presence = 1 or tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.' ) ) ) )';
        } else {
            $where_sql = '( tasting.user_id = '.$this->XM->user->getUserId().' or ( tasting.t_status = '.\TASTING\TASTING_STATUS_PREPARATION.' and ( tasting.t_participation_type = 2 and tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.' or tasting_user.user_id is not null and ( tasting_user.tu_presence = 1 or tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.' ) ) ) )';
        }
        $res = $this->XM->sqlcore->query('SELECT coalesce(tasting_user.tu_response,'.\TASTING\TASTING_USER_RESPONSE_PENDING.') as tu_response
            from tasting
            left join tasting_user on tasting_user.t_id = tasting.t_id and tasting_user.user_id = '.$this->XM->user->getUserId().'
            where tasting.t_id = '.$tasting_id.' and tasting.t_status in ('.\TASTING\TASTING_STATUS_DRAFT.','.\TASTING\TASTING_STATUS_PREPARATION.') and '.$where_sql.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        return (int)$row['tu_response'];
    }
    public function get_current_user_ongoing_status($tasting_id){//0-not present,1-idle,2-awaiting reviews
        if(!$this->XM->user->isLoggedIn()){
            return false;
        }
        $tasting_id = (int)$tasting_id;
        $where_sql = '';
        if($this->XM->user->check_state(\USER\STATE_IS_APPROVED_EXPERT)){
            $where_sql = '( tasting.user_id = '.$this->XM->user->getUserId().' or ( tasting.t_participation_type = 2 or tasting.t_participation_type = 1 and tasting.t_participation_rating_limit <= '.$this->XM->user->getExpertRating().' ) and tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.' or tasting_user.user_id is not null and ( tasting_user.tu_presence = 1 or tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.' ) )';
        } else {
            $where_sql = '( tasting.user_id = '.$this->XM->user->getUserId().' or tasting.t_participation_type = 2 and tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.' or tasting_user.user_id is not null and ( tasting_user.tu_presence = 1 or tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.' ) )';
        }
        $res = $this->XM->sqlcore->query('SELECT coalesce(tasting_user.tu_presence,0) as tu_presence
            from tasting
            left join tasting_user on tasting_user.t_id = tasting.t_id and tasting_user.user_id = '.$this->XM->user->getUserId().'
            where tasting.t_id = '.$tasting_id.' and tasting.t_status = '.\TASTING\TASTING_STATUS_STARTED.' and '.$where_sql.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        $result = $row['tu_presence']?1:0;
        if($result){
            $res = $this->XM->sqlcore->query('SELECT 1
                from tasting
                inner join tasting_product_vintage on tasting_product_vintage.t_id = tasting.t_id
                inner join tasting_user on tasting_user.t_id = tasting_product_vintage.t_id and tasting_user.user_id = '.$this->XM->user->getUserId().' and tasting_user.tu_presence = 1
                left join product_vintage_review on product_vintage_review.tpv_id = tasting_product_vintage.tpv_id and product_vintage_review.user_id = '.$this->XM->user->getUserId().'
                where tasting_product_vintage.t_id = '.$tasting_id.' and ( tasting_product_vintage.tpv_review_request_status = 1 or tasting.t_score_method = 1 ) and product_vintage_review.tpv_id is null
                limit 1');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if($row){
                $result = 2;
            }
        }
        return $result;
    }
    public function get_tasting($tasting_id){
        $tasting_id = (int)$tasting_id;
        $tasting_user_join = '';
        $where_arr = array();
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_ALL_TASTINGS)){
            if($this->XM->user->check_state(\USER\STATE_IS_APPROVED_EXPERT)){
                $where_arr[] = '( tasting.user_id = '.$this->XM->user->getUserId().' or tasting.t_is_approved = 1 or ( tasting.t_status <>'.\TASTING\TASTING_STATUS_DRAFT.' and ( ( ( tasting.t_participation_type = 2 or tasting.t_participation_type = 1 and tasting.t_participation_rating_limit <= '.$this->XM->user->getExpertRating().' ) and tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.' ) or tasting_user.user_id is not null and ( tasting_user.tu_presence = 1 or tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.' ) ) ) )';
            } else {
                $where_arr[] = '( tasting.user_id = '.$this->XM->user->getUserId().' or tasting.t_is_approved = 1 or ( tasting.t_status <> '.\TASTING\TASTING_STATUS_DRAFT.' and ( ( tasting.t_participation_type = 2 and tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.' ) or tasting_user.user_id is not null and ( tasting_user.tu_presence = 1 or tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.' ) ) ) )';
            }
            $tasting_user_join = 'left join tasting_user on tasting_user.t_id = tasting.t_id and tasting_user.user_id = '.$this->XM->user->getUserId();
        }
        $personal_price = '0 as personal_price';
        if($this->XM->user->check_state(\USER\STATE_IS_APPROVED_EXPERT)){
            $personal_price = 'if(tasting.t_chargeability=1,if(tasting.t_pricegrid_rated_expert_rating<='.$this->XM->user->getExpertRating().',tasting.t_pricegrid_rated_expert,tasting.t_pricegrid_expert),0) as personal_price';
        } else {
            $personal_price = 'if(tasting.t_chargeability=1,tasting.t_pricegrid_guest,0) as personal_price';
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_DELETED_TASTINGS)){
            $where_arr[] = 'tasting.t_status <> '.\TASTING\TASTING_STATUS_DELETED;
        }
        $res = $this->XM->sqlcore->query('SELECT tasting.t_id,tasting.user_id,tasting.t_start_ts,tasting.t_end_ts,tasting.t_name,tasting.t_location,tasting.t_desc,tasting.t_participation_type,if(tasting.t_participation_type=1,tasting.t_participation_rating_limit,0) as t_participation_rating_limit,tasting.t_chargeability,if(tasting.t_chargeability=1,tasting.t_pricegrid_guest,0) as t_pricegrid_guest,if(tasting.t_chargeability=1,tasting.t_pricegrid_expert,0) as t_pricegrid_expert,if(tasting.t_chargeability=1,tasting.t_pricegrid_rated_expert,0) as t_pricegrid_rated_expert,if(tasting.t_chargeability=1,tasting.t_pricegrid_rated_expert_rating,0) as t_pricegrid_rated_expert_rating,tasting.t_assessment,tasting.t_score_method,tasting.t_is_approved,tasting.t_status,'.$personal_price.'
            from tasting 
            '.$tasting_user_join.'
            where tasting.t_id = '.$tasting_id.' '.(!empty($where_arr)?'and '.implode(' and ', $where_arr):'').' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        $statuslist = $this->get_status_list();
        $status = (int)$row['t_status'];
        $id = (int)$row['t_id'];
        $has_edit_rights = $this->XM->user->getUserId()==(int)$row['user_id']||$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_TASTINGS);
        $can_view_users = $has_edit_rights;
        $can_edit_users = $has_edit_rights;
        $pricegrid = array();
        if($has_edit_rights){
            $pricegrid = array(
                    'guest_price'=>(int)$row['t_pricegrid_guest'],
                    'expert_price'=>(int)$row['t_pricegrid_expert'],
                    'rated_expert_rating'=>(int)$row['t_pricegrid_rated_expert_rating'],
                    'rated_expert_price'=>(int)$row['t_pricegrid_rated_expert'],
                );
        }
        $score_method = (int)$row['t_score_method'];
        return array(
                'id'=>$id,
                'startts'=>(int)$row['t_start_ts'],
                'endts'=>(int)$row['t_end_ts'],
                'name'=>$row['t_name'],
                'location'=>(string)$row['t_location'],
                'desc'=>(string)$row['t_desc'],
                'participation'=>(int)$row['t_participation_type'],
                'participation_rating'=>(int)$row['t_participation_rating_limit'],
                'chargeability'=>(int)$row['t_chargeability'],
                'pricegrid'=>$pricegrid,
                'personal_price'=>(int)$row['personal_price'],
                'assessment'=>(int)$row['t_assessment'],
                'score_method'=>$score_method,
                'status'=>$status,
                'status_text'=>isset($statuslist[$status])?$statuslist[$status]:'',
                
                'can_edit'=>$has_edit_rights&&\TASTING\TASTING_STATUS_DRAFT==$status,
                'can_edit_vintage_list'=>$has_edit_rights&&($status==\TASTING\TASTING_STATUS_DRAFT||$status==\TASTING\TASTING_STATUS_PREPARATION||$status==\TASTING\TASTING_STATUS_STARTED),
                'can_view_users'=>$can_view_users,
                'can_edit_users'=>$can_edit_users&&($status==\TASTING\TASTING_STATUS_DRAFT||$status==\TASTING\TASTING_STATUS_PREPARATION||$status==\TASTING\TASTING_STATUS_STARTED),
                'can_mark_user_presence'=>$can_edit_users&&$status==\TASTING\TASTING_STATUS_STARTED,
                'can_change_to_deleted'=>$has_edit_rights&&\TASTING\TASTING_STATUS_DRAFT==$status,
                'can_change_to_draft'=>$has_edit_rights&&(\TASTING\TASTING_STATUS_PREPARATION==$status||\TASTING\TASTING_STATUS_DELETED==$status),
                'can_change_to_preparation'=>$has_edit_rights&&(\TASTING\TASTING_STATUS_DRAFT==$status||\TASTING\TASTING_STATUS_STARTED==$status),
                'can_change_to_started'=>$has_edit_rights&&\TASTING\TASTING_STATUS_PREPARATION==$status,
                'can_change_to_finished'=>$has_edit_rights&&\TASTING\TASTING_STATUS_STARTED==$status,
                'can_change_expert_evaluation_options'=>$score_method==0&&$has_edit_rights&&in_array($status, array(\TASTING\TASTING_STATUS_DRAFT,\TASTING\TASTING_STATUS_PREPARATION,\TASTING\TASTING_STATUS_STARTED)),
                'can_change_review_particularity_options'=>$score_method==0&&$has_edit_rights&&in_array($status, array(\TASTING\TASTING_STATUS_DRAFT,\TASTING\TASTING_STATUS_PREPARATION,\TASTING\TASTING_STATUS_STARTED)),
                'can_view_statistics'=>$has_edit_rights&&\TASTING\TASTING_STATUS_STARTED==$status||\TASTING\TASTING_STATUS_FINISHED==$status,
				'can_swap_reviews'=>$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_SWAP_REVIEWS)&&(\TASTING\TASTING_STATUS_STARTED==$status||\TASTING\TASTING_STATUS_FINISHED==$status),
                'can_assess'=>$row['t_assessment']==1&&$row['t_is_approved']===null&&\TASTING\TASTING_STATUS_FINISHED==$status&&$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_APPROVE_TASTING),
            );
    }
    public function get_tasting_evaluation_data($tasting_id){
        $tasting_id = (int)$tasting_id;
        if($tasting_id<=0){
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT tasting.user_id,tasting.t_evaluation_manual,tasting.t_evaluation_automatic 
            from tasting
            where tasting.t_id = '.$tasting_id.'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        $result = array(
                'evaluation_automatic'=>(int)$row['t_evaluation_automatic'],
                'evaluation_manual'=>(int)$row['t_evaluation_manual'],
                'score_permissible_variation'=>0
            );
        $has_edit_rights = $this->XM->user->getUserId()==(int)$row['user_id']||$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_TASTINGS);
        if(!$has_edit_rights&&$result['evaluation_automatic']==1){
            $result['evaluation_automatic'] = 0;
        }
        if($result['evaluation_automatic'] == 0){
            return $result;
        }
        $res = $this->XM->sqlcore->query('SELECT tue_id,tue_score_permissible_variation_type,tue_score_permissible_variation_value,tue_score_score,tue_total_score from tasting_user_evaluation where t_id = '.$tasting_id.' and tue_type = 3 limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return $result;
        }
        $tue_id = (int)$row['tue_id'];
        $result['score_permissible_variation'] = $row['tue_score_permissible_variation_type']?1:0;
        $result['score_permissible_variation_value'] = $row['tue_score_permissible_variation_value']?str_replace('.', ',', round($row['tue_score_permissible_variation_value'])/100):null;
        $result['score']=(int)$row['tue_score_score'];
        if($result['score']==(int)$row['tue_total_score']){//evaluating only by score
            return $result;
        }
        $res = $this->XM->sqlcore->query('SELECT distinct product_vintage_review_param_list.pvrpl_name, tasting_user_evaluation_score.pa_id, tasting_user_evaluation_score.tues_score 
            from tasting_user_evaluation_score
            inner join product_vintage_review_param_list on product_vintage_review_param_list.pvrpl_id = tasting_user_evaluation_score.pvrpl_id
            where tue_id = '.$tue_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $pa_id = (int)$row['pa_id'];
            if($pa_id){
                if(!isset($result[$row['pvrpl_name']])||!is_array($result[$row['pvrpl_name']])){
                    $result[$row['pvrpl_name']] = array();
                }
                $result[$row['pvrpl_name']][$pa_id] = (int)$row['tues_score'];
            } else {
                $result[$row['pvrpl_name']] = (int)$row['tues_score'];    
            }
            
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }
    public function get_expert_evaluation_data(){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_EXPERT_EVALUATION_TEMPLATE)){
            return false;
        }
        $result = array(
                'score_permissible_variation'=>0
            );
        $res = $this->XM->sqlcore->query('SELECT tue_id,tue_score_permissible_variation_type,tue_score_permissible_variation_value,tue_score_score,tue_total_score from tasting_user_evaluation where tue_type = 5 limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return $result;
        }
        $tue_id = (int)$row['tue_id'];
        $result['score_permissible_variation'] = $row['tue_score_permissible_variation_type']?1:0;
        $result['score_permissible_variation_value'] = $row['tue_score_permissible_variation_value']?str_replace('.', ',', round($row['tue_score_permissible_variation_value'])/100):null;
        $result['score']=(int)$row['tue_score_score'];
        if($result['score']==(int)$row['tue_total_score']){//evaluating only by score
            return $result;
        }
        $res = $this->XM->sqlcore->query('SELECT distinct product_vintage_review_param_list.pvrpl_name, tasting_user_evaluation_score.pa_id, tasting_user_evaluation_score.tues_score 
            from tasting_user_evaluation_score
            inner join product_vintage_review_param_list on product_vintage_review_param_list.pvrpl_id = tasting_user_evaluation_score.pvrpl_id
            where tue_id = '.$tue_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $pa_id = (int)$row['pa_id'];
            if($pa_id){
                if(!isset($result[$row['pvrpl_name']])||!is_array($result[$row['pvrpl_name']])){
                    $result[$row['pvrpl_name']] = array();
                }
                $result[$row['pvrpl_name']][$pa_id] = (int)$row['tues_score'];
            } else {
                $result[$row['pvrpl_name']] = (int)$row['tues_score'];    
            }
            
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }
    public function edit_tasting_evaluation_data($tasting_id,$evaluation_manual,$evaluation_automatic,$scores,&$err){
        $tasting_id = (int)$tasting_id;
        if($tasting_id<=0){
            $err = langTranslate('tasting', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        $evaluation_manual = (int)$evaluation_manual;
        if(!in_array($evaluation_manual, array(0,1))){
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting','expert evaluation','Manual evaluation','Manual evaluation'));
            return false;
        }
        $evaluation_automatic = (int)$evaluation_automatic;
        if(!in_array($evaluation_automatic, array(0,1,2))){
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting','expert evaluation','Automatic evaluation','Automatic evaluation'));
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT tasting.user_id,tasting.t_status,tasting.t_score_method,tasting.t_evaluation_manual,tasting.t_evaluation_automatic 
            from tasting
            where tasting.t_id = '.$tasting_id.'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Tasting doesn\'t exist', 'Tasting doesn\'t exist');
            return false;
        }
        if((int)$row['t_score_method']!=0){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        if(!(($this->XM->user->getUserId()==(int)$row['user_id']||$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_TASTINGS))&&in_array((int)$row['t_status'],array(\TASTING\TASTING_STATUS_DRAFT,\TASTING\TASTING_STATUS_PREPARATION,\TASTING\TASTING_STATUS_STARTED)))){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $update_arr = array();
        if($evaluation_manual!=(int)$row['t_evaluation_manual']){
            $update_arr[] = 't_evaluation_manual = '.$evaluation_manual;
        }
        if($evaluation_automatic!=(int)$row['t_evaluation_automatic']){
            $update_arr[] = 't_evaluation_automatic = '.$evaluation_automatic;
        }
        if(!empty($update_arr)){
            $this->XM->sqlcore->query('UPDATE tasting set '.implode(',', $update_arr).' where t_id = '.$tasting_id);
        }
        if(!$evaluation_automatic){//garbage deleted by mysql trigger
            $this->XM->sqlcore->commit();
            return true;
        }
        $this->XM->sqlcore->query('DELETE FROM tasting_user_evaluation where t_id = '.$tasting_id.' and tue_type = 3');

        $review_param_id_list = array();
        $res = $this->XM->sqlcore->query('SELECT pvrpl_id,pvrpl_name from product_vintage_review_param_list where pvrpl_exact_blind = 0');
        while($row = $this->XM->sqlcore->getRow($res)){
            if(!isset($scores[$row['pvrpl_name']]) || !$scores[$row['pvrpl_name']]){
                continue;
            }
            $review_param_id_list[$row['pvrpl_name']] = (int)$row['pvrpl_id'];
        }
        $this->XM->sqlcore->freeResult($res);

        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE scoreids (
                pvrpl_id BIGINT UNSIGNED NOT NULL,
                pa_id INT UNSIGNED NULL,
                score tinyint(1) UNSIGNED NOT NULL
            )');
        $review_elements = $this->XM->product->get_review_elements();
        $sumscore = 0;
        foreach($review_elements as $review_element_group){
            foreach($review_element_group as $review_element){
                if(!isset($review_element['automatic-evaluation'])||!$review_element['automatic-evaluation']){
                    continue;
                }
                $review_element_name = $review_element['name'];
                if(!isset($review_param_id_list[$review_element_name])){
                    continue;
                }
                $pvrpl_id = $review_param_id_list[$review_element_name];
                if(!isset($scores[$review_element_name])){//excessive
                    continue;
                }
                $review_element_score = (int)$scores[$review_element_name];
                if($review_element_score==0){
                    continue;
                }
                if($review_element_score<0||$review_element_score>5){
                    $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                        isset($review_element['caption'])?htmlentities($review_element['caption']):$review_element_name);
                    $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
                    $this->XM->sqlcore->rollback();
                    return false;
                }
                $this->XM->sqlcore->query('INSERT INTO scoreids (pvrpl_id,score) VALUES ('.$pvrpl_id.','.$review_element_score.')');
                $sumscore += $review_element_score;
            }
        }
        //pa_id section
        $exact_blind_list = array();
        $res = $this->XM->sqlcore->query('SELECT pvrpl_id,pvrpl_name from product_vintage_review_param_list where pvrpl_exact_blind = 1');
        while($row = $this->XM->sqlcore->getRow($res)){
            if(!isset($scores[$row['pvrpl_name']]) || !$scores[$row['pvrpl_name']]){
                continue;
            }
            $exact_blind_list[(int)$row['pvrpl_id']] = $row['pvrpl_name'];
        }
        $this->XM->sqlcore->freeResult($res);
        foreach($exact_blind_list as $pvrpl_id=>$review_element_name){
            if(!isset($scores[$review_element_name])){//excessive
                continue;
            }
            foreach($scores[$review_element_name] as $pa_id=>$review_element_score){
                $pa_id = (int)$pa_id;
                $review_element_score = (int)$review_element_score;
                if($review_element_score<0||$review_element_score>5){
                    $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                        $review_element_name);
                    $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
                    $this->XM->sqlcore->rollback();
                    return false;
                }
                $this->XM->sqlcore->query('INSERT INTO scoreids (pvrpl_id,pa_id,score) VALUES ('.$pvrpl_id.','.$pa_id.','.$review_element_score.')');
                $sumscore += $review_element_score;
            }
        }
        


        $score = isset($scores['score'])?(int)$scores['score']:0;
        if($score<0||$score>5){
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                langTranslate('product','review','Score', 'Score'));
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
            $this->XM->sqlcore->rollback();
            return false;
        }
        $sumscore+=$score;
        if($sumscore==0){
            $err = langTranslate('tasting', 'err', 'You have to select a score for at least one evaluation point', 'You have to select a score for at least one evaluation point');
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
            $this->XM->sqlcore->rollback();
            return false;
        }
        $tasting_user_evaluation_id = null;
        $score_permissible_variation_type = isset($scores['score_permissible_variation'])&&$scores['score_permissible_variation']?1:0;
        if($score_permissible_variation_type){
            $score_permissible_variation_value = isset($scores['score_permissible_variation_value'])?(int)((float)str_replace(',', '.', $scores['score_permissible_variation_value'])*100):0;
            if($score_permissible_variation_value<0||$score_permissible_variation_value>10000){
                $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting','expert evaluation','Permissible variation','Permissible variation'));
                $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
                $this->XM->sqlcore->rollback();
                return false;
            }
            $this->XM->sqlcore->query('INSERT INTO tasting_user_evaluation (t_id,tue_type,tue_score_permissible_variation_type,tue_score_permissible_variation_value,tue_score_score,tue_total_score) 
                values ('.$tasting_id.',3,'.$score_permissible_variation_type.','.$score_permissible_variation_value.','.$score.','.$sumscore.')');
            $tasting_user_evaluation_id = $this->XM->sqlcore->lastInsertId();
        } else {
            $this->XM->sqlcore->query('INSERT INTO tasting_user_evaluation (t_id,tue_type,tue_score_permissible_variation_type,tue_score_score,tue_total_score) 
                values ('.$tasting_id.',3,'.$score_permissible_variation_type.','.$score.','.$sumscore.')');
            $tasting_user_evaluation_id = $this->XM->sqlcore->lastInsertId();
        }

        $this->XM->sqlcore->query('INSERT INTO tasting_user_evaluation_score (tue_id,pvrpl_id,pa_id,tues_score)
            SELECT distinct '.$tasting_user_evaluation_id.' as tue_id, pvrpl_id, pa_id, score as tues_score
                from scoreids');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
        $this->XM->sqlcore->commit();
        return true;
    }
    public function edit_global_expert_evaluation_data($scores,&$err){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_EXPERT_EVALUATION_TEMPLATE)){
            return false;
        }
        $this->XM->sqlcore->query('DELETE FROM tasting_user_evaluation where tue_type = 5');
        $review_param_id_list = array();
        $res = $this->XM->sqlcore->query('SELECT pvrpl_id,pvrpl_name from product_vintage_review_param_list where pvrpl_exact_blind = 0');
        while($row = $this->XM->sqlcore->getRow($res)){
            if(!isset($scores[$row['pvrpl_name']]) || !$scores[$row['pvrpl_name']]){
                continue;
            }
            $review_param_id_list[$row['pvrpl_name']] = (int)$row['pvrpl_id'];
        }
        $this->XM->sqlcore->freeResult($res);

        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE scoreids (
                pvrpl_id BIGINT UNSIGNED NOT NULL,
                pa_id INT UNSIGNED NULL,
                score tinyint(1) UNSIGNED NOT NULL
            )');
        $review_elements = $this->XM->product->get_review_elements();
        $sumscore = 0;
        foreach($review_elements as $review_element_group){
            foreach($review_element_group as $review_element){
                if(!isset($review_element['automatic-evaluation'])||!$review_element['automatic-evaluation']){
                    continue;
                }
                $review_element_name = $review_element['name'];
                if(!isset($review_param_id_list[$review_element_name])){
                    continue;
                }
                $pvrpl_id = $review_param_id_list[$review_element_name];
                if(!isset($scores[$review_element_name])){//excessive
                    continue;
                }
                $review_element_score = (int)$scores[$review_element_name];

                if($review_element_score==0){
                    continue;
                }
                if($review_element_score<0||$review_element_score>5){
                    $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                        isset($review_element['caption'])?htmlentities($review_element['caption']):$review_element_name);
                    $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
                    $this->XM->sqlcore->rollback();
                    return false;
                }
                $this->XM->sqlcore->query('INSERT INTO scoreids (pvrpl_id,score) VALUES ('.$pvrpl_id.','.$review_element_score.')');
                $sumscore += $review_element_score;
            }
        }
        //pa_id section
        $exact_blind_list = array();
        $res = $this->XM->sqlcore->query('SELECT pvrpl_id,pvrpl_name from product_vintage_review_param_list where pvrpl_exact_blind = 1');
        while($row = $this->XM->sqlcore->getRow($res)){
            if(!isset($scores[$row['pvrpl_name']]) || !$scores[$row['pvrpl_name']]){
                continue;
            }
            $exact_blind_list[(int)$row['pvrpl_id']] = $row['pvrpl_name'];
        }
        $this->XM->sqlcore->freeResult($res);
        foreach($exact_blind_list as $pvrpl_id=>$review_element_name){
            if(!isset($scores[$review_element_name])){//excessive
                continue;
            }
            foreach($scores[$review_element_name] as $pa_id=>$review_element_score){
                $pa_id = (int)$pa_id;
                $review_element_score = (int)$review_element_score;
                if($review_element_score<0||$review_element_score>5){
                    $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                        $review_element_name);
                    $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
                    $this->XM->sqlcore->rollback();
                    return false;
                }
                $this->XM->sqlcore->query('INSERT INTO scoreids (pvrpl_id,pa_id,score) VALUES ('.$pvrpl_id.','.$pa_id.','.$review_element_score.')');
                $sumscore += $review_element_score;
            }
        }

        $score = isset($scores['score'])?(int)$scores['score']:0;
        if($score<0||$score>5){
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                langTranslate('product','review','Score', 'Score'));
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
            $this->XM->sqlcore->rollback();
            return false;
        }
        $sumscore+=$score;
        if($sumscore==0){
            $err = langTranslate('tasting', 'err', 'You have to select a score for at least one evaluation point', 'You have to select a score for at least one evaluation point');
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
            $this->XM->sqlcore->rollback();
            return false;
        }
        $tasting_user_evaluation_id = null;
        $score_permissible_variation_type = isset($scores['score_permissible_variation'])&&$scores['score_permissible_variation']?1:0;
        if($score_permissible_variation_type){
            $score_permissible_variation_value = isset($scores['score_permissible_variation_value'])?(int)((float)str_replace(',', '.', $scores['score_permissible_variation_value'])*100):0;
            if($score_permissible_variation_value<0||$score_permissible_variation_value>10000){
                $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting','expert evaluation','Permissible variation','Permissible variation'));
                $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
                $this->XM->sqlcore->rollback();
                return false;
            }
            $this->XM->sqlcore->query('INSERT INTO tasting_user_evaluation (tue_type,tue_score_permissible_variation_type,tue_score_permissible_variation_value,tue_score_score,tue_total_score) 
                values (5,'.$score_permissible_variation_type.','.$score_permissible_variation_value.','.$score.','.$sumscore.')');
            $tasting_user_evaluation_id = $this->XM->sqlcore->lastInsertId();
        } else {
            $this->XM->sqlcore->query('INSERT INTO tasting_user_evaluation (tue_type,tue_score_permissible_variation_type,tue_score_score,tue_total_score) 
                values (5,'.$score_permissible_variation_type.','.$score.','.$sumscore.')');
            $tasting_user_evaluation_id = $this->XM->sqlcore->lastInsertId();
        }

        $this->XM->sqlcore->query('INSERT INTO tasting_user_evaluation_score (tue_id,pvrpl_id,pa_id,tues_score)
            SELECT distinct '.$tasting_user_evaluation_id.' as tue_id, pvrpl_id, pa_id, score as tues_score
                from scoreids');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
        $this->XM->sqlcore->commit();
        return true;
    }
    public function get_tasting_product_vintage_manual_evaluation($tpv_id, &$err){
        $tpv_id = (int)$tpv_id;
        $res = $this->XM->sqlcore->query('SELECT tasting_user_evaluation.tue_id, if(tasting_user_evaluation.tpv_id is null,0,1) as tue_exists,tasting_user_evaluation.tue_score_avg,tasting_user_evaluation.tue_score_score
            from tasting_product_vintage 
            left join tasting_user_evaluation on tasting_user_evaluation.tpv_id = tasting_product_vintage.tpv_id and tasting_user_evaluation.tue_type = 1
            where tasting_product_vintage.tpv_id = '.$tpv_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Vintage doesn\'t exist',  'Vintage doesn\'t exist');
            return false;
        }
        if(!$row['tue_exists']){
            $err = langTranslate('tasting', 'err', 'Manual evaluation for this product hasn\'t been configured',  'Manual evaluation for this product hasn\'t been configured');
            return false;
        }
        $tue_id = (int)$row['tue_id'];
        $scores = array();
        if($row['tue_score_score']){
            $scores['score_value'] = round(((int)$row['tue_score_avg'])/100,2);
            $scores['score_score'] = (int)$row['tue_score_score'];    
        }
        $res = $this->XM->sqlcore->query('SELECT tasting_user_evaluation_score.pvrpl_id,product_vintage_review_param_list.pvrpl_name,tasting_user_evaluation_score.pa_id,tasting_user_evaluation_score.pvrp_value,tasting_user_evaluation_score.tues_score
            from tasting_user_evaluation_score
            inner join product_vintage_review_param_list on product_vintage_review_param_list.pvrpl_id = tasting_user_evaluation_score.pvrpl_id
            where tasting_user_evaluation_score.tue_id = '.$tue_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $tues_score = (int)$row['tues_score'];
            if($tues_score<=0){
                continue;
            }
            
            if($row['pa_id']){
                $value_code = ((int)$row['pa_id']).','.((int)$row['pvrp_value']);
            } elseif($row['pvrpl_id']==\TASTING\EVALUATION_CUSTOM_PARAM_SUBCOLOR_DEPTH_PVRPL_ID) {
                $pvrp_value = (int)$row['pvrp_value'];
                if($pvrp_value<10000){
                    continue;
                }
                $value_code = floor($pvrp_value/10000).','.floor(($pvrp_value%10000)/100).','.($pvrp_value%100);
                $row['pvrpl_name'] = 'subcolorcode';
            } else {
                $value_code = (int)$row['pvrp_value'];
            }
            $scores[$row['pvrpl_name']][$value_code] = $tues_score;
        }
        $this->XM->sqlcore->freeResult($res);
        return $scores;
    }
    public function set_tasting_product_vintage_manual_evaluation($tpv_id, $scores, &$err){
        $tpv_id = (int)$tpv_id;
        $res = $this->XM->sqlcore->query('SELECT tasting.t_score_method, tasting_product_vintage.t_id, tasting_product_vintage.tpv_blind, tasting_product_vintage.tpv_review_request_status, if(tasting_user_evaluation.tpv_id is null,0,1) as tue_exists
            from tasting_product_vintage 
            inner join tasting on tasting.t_id = tasting_product_vintage.t_id
            left join tasting_user_evaluation on tasting_user_evaluation.tpv_id = tasting_product_vintage.tpv_id and tasting_user_evaluation.tue_type = 1
            where tasting_product_vintage.tpv_id = '.$tpv_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Vintage doesn\'t exist',  'Vintage doesn\'t exist');
            return false;
        }
        if((int)$row['t_score_method']!=0){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        if($row['tpv_review_request_status']!=2){
            $err = langTranslate('tasting', 'err', 'You can configure this manual evaluation only after finishing the tasting of the product',  'You can configure this manual evaluation only after finishing the tasting of the product');
            return false;
        }
        if($row['tue_exists']){
            $err = langTranslate('tasting', 'err', 'Manual evaluation for this product has already been configured',  'Manual evaluation for this product has already been configured');
            return false;
        }
        $tasting_id = (int)$row['t_id'];
        $tpv_blind = (bool)$row['tpv_blind'];

        $review_param_id_list = array();
        $res = $this->XM->sqlcore->query('SELECT pvrpl_id,pvrpl_name from product_vintage_review_param_list where pvrpl_exact_blind = 0');
        while($row = $this->XM->sqlcore->getRow($res)){
            if(!isset($scores[$row['pvrpl_name']]) || !$scores[$row['pvrpl_name']]){
                continue;
            }
            $review_param_id_list[$row['pvrpl_name']] = (int)$row['pvrpl_id'];
        }
        
        $this->XM->sqlcore->freeResult($res);


        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE scoreids (
                pvrpl_id BIGINT UNSIGNED NOT NULL,
                pa_id INT UNSIGNED NULL,
                pvrp_value BIGINT UNSIGNED NOT NULL,
                score tinyint(1) UNSIGNED NOT NULL
            )');
        $review_elements = $this->XM->product->get_review_elements();
        $sumscore = 0;
        foreach($review_elements as $review_element_group){
            foreach($review_element_group as $review_element){
                if(!isset($review_element['manual-evaluation'])||!$review_element['manual-evaluation']){
                    continue;
                }
                $review_element_name = $review_element['name'];
                if(!isset($review_param_id_list[$review_element_name])){
                    continue;
                }
                $pvrpl_id = $review_param_id_list[$review_element_name];
                if(!isset($scores[$review_element_name])){//excessive
                    continue;
                }
                foreach($scores[$review_element_name] as $pvrp_value=>$review_element_score){
                    $pvrp_value = (int)$pvrp_value;
                    $review_element_score = (int)$review_element_score;
                    if($review_element_score==0){
                        continue;
                    }
                    if($review_element_score<0||$review_element_score>5){
                        $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                            isset($review_element['caption'])?htmlentities($review_element['caption']):$review_element_name);
                        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
                        $this->XM->sqlcore->rollback();
                        return false;
                    }
                    $this->XM->sqlcore->query('INSERT INTO scoreids (pvrpl_id,pvrp_value,score) VALUES ('.$pvrpl_id.','.$pvrp_value.','.$review_element_score.')');
                }
                
            }
        }
        //pa_id section
        if($tpv_blind){
            $exact_blind_list = array();
            $res = $this->XM->sqlcore->query('SELECT pvrpl_id,pvrpl_name from product_vintage_review_param_list where pvrpl_exact_blind = 1');
            while($row = $this->XM->sqlcore->getRow($res)){
                if(!isset($scores[$row['pvrpl_name']]) || !$scores[$row['pvrpl_name']]){
                    continue;
                }
                $exact_blind_list[(int)$row['pvrpl_id']] = $row['pvrpl_name'];
            }
            $this->XM->sqlcore->freeResult($res);
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE blindscoreids (
                    pvrpl_id BIGINT UNSIGNED NOT NULL,
                    pa_id INT UNSIGNED NULL,
                    pvrp_value BIGINT UNSIGNED NULL,
                    score tinyint(1) UNSIGNED NOT NULL
                )');
            foreach($exact_blind_list as $pvrpl_id=>$review_element_name){
                if(!isset($scores[$review_element_name])){//excessive
                    continue;
                }
                foreach($scores[$review_element_name] as $data_code=>$review_element_score){
                    if(!preg_match('#^(\d+),(\d+)$#', $data_code,$match)){
                        continue;
                    }
                    $pa_id = (int)$match[1];
                    $pvrp_value = (int)$match[2];
                    $review_element_score = (int)$review_element_score;
                    if($review_element_score==0){
                        continue;
                    }
                    if($review_element_score<0||$review_element_score>5){
                        $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                            $review_element_name);
                        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
                        $this->XM->sqlcore->rollback();
                        return false;
                    }
                    $this->XM->sqlcore->query('INSERT INTO blindscoreids (pvrpl_id,pa_id,pvrp_value,score) VALUES ('.$pvrpl_id.','.$pa_id.','.$pvrp_value.','.$review_element_score.')');
                }
            }
            $this->XM->sqlcore->query('INSERT INTO scoreids (pvrpl_id,pa_id,pvrp_value,score)
                SELECT distinct blindscoreids.pvrpl_id,blindscoreids.pa_id,blindscoreids.pvrp_value,blindscoreids.score
                    from blindscoreids
                    inner join product_vintage_review_param_list on product_vintage_review_param_list.pvrpl_id = blindscoreids.pvrpl_id and product_vintage_review_param_list.pvrpl_exact_blind = 1
                    inner join product_attribute on product_attribute.pa_id = blindscoreids.pa_id
                    inner join product_attribute_value on product_attribute_value.pa_id = product_attribute.pa_id and product_attribute_value.pav_id = blindscoreids.pvrp_value
                    inner join tasting_product_vintage on tasting_product_vintage.tpv_id = '.$tpv_id.' and tasting_product_vintage.tpv_blind = 1');
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS blindscoreids');
        }
        //subcolor section
        if(isset($scores['subcolorcode'])&&is_array($scores['subcolorcode'])&&!empty($scores['subcolorcode'])){
            foreach($scores['subcolorcode'] as $subcolorcode=>$review_element_score){
                if(!preg_match('#^(\d+),(\d+),(\d+)$#', $subcolorcode, $match)){
                    continue;
                }
                $color = (int)$match[1];
                $subcolor = (int)$match[2];
                $depth = (int)$match[3];
                $element_title = null;
                foreach($review_elements['color_spectrum_subcolor_data'] as $color_spectrum_element){
                    if( $color_spectrum_element['color']==$color &&
                        $color_spectrum_element['subcolor']==$subcolor &&
                        $color_spectrum_element['depth']==$depth ){
                        $element_title = $color_spectrum_element['title'];
                        break;
                    }
                }
                if($element_title===null){
                    continue;//invalid subcolor
                }
                $review_element_score = (int)$review_element_score;
                if($review_element_score==0){
                    continue;
                }
                if($review_element_score<0||$review_element_score>5){
                    $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                        $element_title);
                    $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
                    $this->XM->sqlcore->rollback();
                    return false;
                }
                $pvrp_value = $color*10000+$subcolor*100+$depth;
                $this->XM->sqlcore->query('INSERT INTO scoreids (pvrpl_id,pvrp_value,score) VALUES ('.\TASTING\EVALUATION_CUSTOM_PARAM_SUBCOLOR_DEPTH_PVRPL_ID.','.$pvrp_value.','.$review_element_score.')');
            }
        }

        $res = $this->XM->sqlcore->query('SELECT sum(score) as sumscore from scoreids');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        $sumscore += (int)$row['sumscore'];

        $score_score = isset($scores['score_score'])?(int)$scores['score_score']:0;
        if($score_score<0||$score_score>5){
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                langTranslate('product','review','Score', 'Score'));
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
            $this->XM->sqlcore->rollback();
            return false;
        }
        $sumscore+=$score_score;
        if($sumscore==0){
            $err = langTranslate('tasting', 'err', 'You have to select a score for at least one evaluation point', 'You have to select a score for at least one evaluation point');
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
            $this->XM->sqlcore->rollback();
            return false;
        }
        $score_value = isset($scores['score_value'])?(int)((float)str_replace(',', '.', $scores['score_value'])*100):0;
        $tasting_user_evaluation_id = null;
        $score_permissible_variation_type = isset($scores['score_permissible_variation'])&&$scores['score_permissible_variation']?1:0;


        if($score_permissible_variation_type==0){//auto
            $res = $this->XM->sqlcore->query('SELECT max(user_expert_level) as user_expert_level
                from product_vintage_review 
                where tpv_id = '.$tpv_id.' and pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE).' = 0');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if(!$row){//have no reviews
                return false;
            }
            $max_user_expert_level = (int)$row['user_expert_level'];
            $res = $this->XM->sqlcore->query('SELECT min(pvr_score) as minscore,max(pvr_score) as maxscore
                from product_vintage_review 
                where tpv_id = '.$tpv_id.' and pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE).' = 0 and user_expert_level = '.$max_user_expert_level);
            $deltascore_row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if(!$deltascore_row){//excessive
                return false;
            }
            $score_permissible_variation_value = max(abs((int)$deltascore_row['minscore']-$score_value),abs((int)$deltascore_row['maxscore']-$score_value));
        } else {
            $score_permissible_variation_value = isset($scores['score_permissible_variation_value'])?(int)((float)str_replace(',', '.', $scores['score_permissible_variation_value'])*100):0;
            if($score_permissible_variation_value<0||$score_permissible_variation_value>10000){
                $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting','expert evaluation','Permissible variation','Permissible variation'));
                $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
                $this->XM->sqlcore->rollback();
                return false;
            }
        }
        $this->XM->sqlcore->query('INSERT INTO tasting_user_evaluation (t_id,tpv_id,tue_type,tue_score_permissible_variation_type,tue_score_permissible_variation_value,tue_score_avg,tue_score_score,tue_total_score) 
                values ('.$tasting_id.','.$tpv_id.',1,'.$score_permissible_variation_type.','.$score_permissible_variation_value.','.$score_value.','.$score_score.','.$sumscore.')');
        $tasting_user_evaluation_id = $this->XM->sqlcore->lastInsertId();


        $this->XM->sqlcore->query('INSERT INTO tasting_user_evaluation_score (tue_id,pvrpl_id,pa_id,pvrp_value,tues_score)
            SELECT distinct '.$tasting_user_evaluation_id.' as tue_id, pvrpl_id, pa_id, pvrp_value, score as tues_score
                from scoreids');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
        $this->XM->sqlcore->commit();
        $this->__process_evaluation($tasting_user_evaluation_id);
        return true;
    }
    public function __refresh_global_expert_evaluations(){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_EXPERT_EVALUATION_TEMPLATE)){
            return false;
        }
        $this->XM->product->load();
        $res = $this->XM->sqlcore->query('SELECT distinct tasting.t_id 
            from tasting
            inner join product_vintage_review on product_vintage_review.t_id = tasting.t_id and product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW).' = 0 and product_vintage_review.pvr_timestamp > UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - '.\TASTING\GLOBAL_EVALUATION_STORAGE_TIME.'
            where tasting.t_status = 3 and tasting.t_assessment = 1');
        $tasting_ids = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $tasting_ids[] = (int)$row['t_id'];
        }
        $this->XM->sqlcore->freeResult($res);
        foreach($tasting_ids as $tasting_id){
            $this->__refresh_global_expert_evaluation_for_tasting($tasting_id);
        }
        return true;
    }
    public function __refresh_global_expert_evaluation_for_tasting($tasting_id){
        $tasting_id = (int)$tasting_id;
        $this->XM->sqlcore->query('DELETE from tasting_user_global_evaluation_score where t_id = '.$tasting_id);
        $this->XM->product->load();
        $res = $this->XM->sqlcore->query('SELECT 1 
            from tasting
            inner join product_vintage_review on product_vintage_review.t_id = tasting.t_id and product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW).' = 0 and product_vintage_review.pvr_timestamp > UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - '.\TASTING\GLOBAL_EVALUATION_STORAGE_TIME.'
            where tasting.t_id = '.$tasting_id.' and tasting.t_status = 3 and tasting.t_assessment = 1
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        $this->XM->sqlcore->query('DELETE from tasting_user_evaluation where t_id = '.$tasting_id.' and tue_type = 4');
        $this->XM->sqlcore->commit();
        $tpv_ids = array();
        $res = $this->XM->sqlcore->query('SELECT distinct tpv_id FROM tasting_product_vintage WHERE tasting_product_vintage.t_id = '.$tasting_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $tpv_ids[] = (int)$row['tpv_id'];
        }
        $this->XM->sqlcore->freeResult($res);
        foreach($tpv_ids as $tpv_id){
            $this->__generate_automatic_evaluation($tpv_id,true);
        }
        return $this->__process_global_evaluation_for_tasting($tasting_id);
    }
    public function __process_global_evaluation_for_tasting($tasting_id){
        $tasting_id = (int)$tasting_id;
        $this->XM->sqlcore->query('DELETE from tasting_user_global_evaluation_score where t_id = '.$tasting_id);
        $this->XM->product->load();
        $res = $this->XM->sqlcore->query('SELECT 1 
            from tasting
            inner join product_vintage_review on product_vintage_review.t_id = tasting.t_id and product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW).' = 0 and product_vintage_review.pvr_timestamp > UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - '.\TASTING\GLOBAL_EVALUATION_STORAGE_TIME.'
            where tasting.t_id = '.$tasting_id.' and tasting.t_status = 3 and tasting.t_assessment = 1
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE userscores
            SELECT tasting_user_evaluation_user_score.user_id, floor(tasting_user_evaluation_user_score.tueus_score*10000/maxscores.tueus_score) as score, product_vintage_review.pvr_id, product_vintage_review.user_expert_level
            from tasting_user_evaluation 
            inner join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id
            inner join product_vintage_review on product_vintage_review.pvr_id = tasting_user_evaluation_user_score.pvr_id
            inner join (
                select max(tasting_user_evaluation_user_score.tueus_score) as tueus_score, count(1) as tueus_count, tasting_user_evaluation.tpv_id, product_vintage_review.user_expert_level
                    from tasting_user_evaluation 
                    inner join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id
                    inner join product_vintage_review on product_vintage_review.pvr_id = tasting_user_evaluation_user_score.pvr_id
                    where tasting_user_evaluation.t_id = '.$tasting_id.' and tasting_user_evaluation.tue_type = 4
                    group by tasting_user_evaluation.tpv_id, product_vintage_review.user_expert_level
            ) as maxscores on maxscores.tpv_id = tasting_user_evaluation.tpv_id and maxscores.user_expert_level = product_vintage_review.user_expert_level
            where tasting_user_evaluation.t_id = '.$tasting_id.' and tasting_user_evaluation.tue_type = 4 and maxscores.tueus_count >= '.\TASTING\GLOBAL_EVALUATION_MIN_EXPERT_COUNT);
        // tasting leniency
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE userscore_leniency
            select userscores.user_id, userscores.user_expert_level, floor(count(1)*'.\TASTING\GLOBAL_EVALUATION_TASTING_LENIENCY_PERCENT.'/100) as leniency_count
                from userscores
                group by userscores.user_id, userscores.user_expert_level
                having floor(count(1)*'.\TASTING\GLOBAL_EVALUATION_TASTING_LENIENCY_PERCENT.'/100) > 0');
        $leniency_indexes = array();
        $res = $this->XM->sqlcore->query('SELECT substring_index(group_concat(userscores.pvr_id order by userscores.score asc),\',\',userscore_leniency.leniency_count) as leniency_index
            from userscores
            inner join userscore_leniency on userscore_leniency.user_id = userscores.user_id and userscore_leniency.user_expert_level = userscores.user_expert_level
            group by userscores.user_id, userscores.user_expert_level, userscore_leniency.leniency_count');
        while($row = $this->XM->sqlcore->getRow($res)){
            $user_leniency_indexes = explode(',', $row['leniency_index']);
            foreach($user_leniency_indexes as $user_leniency_index){
                $leniency_indexes[] = (int)$user_leniency_index;
            }
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS userscore_leniency');

        if(!empty($leniency_indexes)){
            $leniency_index_chunks = array_chunk($leniency_indexes, 50);
            foreach($leniency_index_chunks as $leniency_index_chunk){
                $this->XM->sqlcore->query('DELETE from userscores where pvr_id in ('.implode(',', $leniency_index_chunk).')');
            }
            unset($leniency_index_chunks);
        }
        unset($leniency_indexes);
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE userscore_summary
            select userscores.user_id, userscores.user_expert_level, floor(avg(userscores.score)) as score
                from userscores
                group by userscores.user_id, userscores.user_expert_level');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS userscores');
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE userscore_summary_copy
            SELECT * from userscore_summary');
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE userscore_non_zero_evaluation_requirement
            SELECT user_expert_level, ceil(count(1)*'.\TASTING\GLOBAL_EVALUATION_NON_ZERO_EVALUATION_PERCENT.'/100) as required_place
                from userscore_summary
                group by user_expert_level');
        $this->XM->sqlcore->query('DELETE from tasting_user_global_evaluation_score where t_id = '.$tasting_id);
        $this->XM->sqlcore->query('INSERT INTO tasting_user_global_evaluation_score (user_id,t_id,user_expert_level,tuges_score,tuges_zero,tuges_place)
            select userscore_summary.user_id, '.$tasting_id.' as t_id, userscore_summary.user_expert_level, userscore_summary.score as tuges_score, if(userscore_summary.place>userscore_non_zero_evaluation_requirement.required_place,1,0) as tuges_zero, userscore_summary.place as tuges_place
                from (
                    select userscore_summary.user_id, userscore_summary.user_expert_level, userscore_summary.score, coalesce(count(userscore_summary_copy.user_id),0)+1 as place
                    from userscore_summary
                    left join userscore_summary_copy on userscore_summary_copy.user_expert_level = userscore_summary.user_expert_level and userscore_summary.score < userscore_summary_copy.score
                    group by userscore_summary.user_id, userscore_summary.user_expert_level, userscore_summary.score
                ) as userscore_summary
                inner join userscore_non_zero_evaluation_requirement on userscore_non_zero_evaluation_requirement.user_expert_level = userscore_summary.user_expert_level');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS userscore_summary_copy');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS userscore_non_zero_evaluation_requirement');
        // global leniency
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE userscore_leniency
            select tasting_user_global_evaluation_score.user_id, tasting_user_global_evaluation_score.user_expert_level, floor(count(1)*'.\TASTING\GLOBAL_EVALUATION_LENIENCY_PERCENT.'/100) as leniency_count
                from tasting_user_global_evaluation_score
                inner join userscore_summary on userscore_summary.user_id = tasting_user_global_evaluation_score.user_id and userscore_summary.user_expert_level = tasting_user_global_evaluation_score.user_expert_level
                group by tasting_user_global_evaluation_score.user_id, tasting_user_global_evaluation_score.user_expert_level');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS userscore_summary');

        $leniency_indexes = array();
        $res = $this->XM->sqlcore->query('SELECT tasting_user_global_evaluation_score.user_id, tasting_user_global_evaluation_score.user_expert_level,substring_index(group_concat(tasting_user_global_evaluation_score.t_id order by tasting_user_global_evaluation_score.tuges_zero desc, tasting_user_global_evaluation_score.tuges_score asc),\',\',userscore_leniency.leniency_count) as leniency_index
            from tasting_user_global_evaluation_score
            inner join userscore_leniency on userscore_leniency.user_id = tasting_user_global_evaluation_score.user_id and userscore_leniency.user_expert_level = tasting_user_global_evaluation_score.user_expert_level
            group by tasting_user_global_evaluation_score.user_id, tasting_user_global_evaluation_score.user_expert_level, userscore_leniency.leniency_count');
        while($row = $this->XM->sqlcore->getRow($res)){
            $cleared_user_leniency_indexes = array();
            $user_leniency_indexes = explode(',', $row['leniency_index']);
            foreach($user_leniency_indexes as $user_leniency_index){
                $cleared_user_leniency_indexes[] = (int)$user_leniency_index;
            }
            $leniency_indexes[] = array('user_id'=>(int)$row['user_id'],'user_expert_level'=>(int)$row['user_expert_level'],'indexes'=>$cleared_user_leniency_indexes);
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS userscore_leniency');
        if(!empty($leniency_indexes)){
            foreach($leniency_indexes as $data){
                $this->XM->sqlcore->query('UPDATE tasting_user_global_evaluation_score SET tuges_leniency = 0 where user_id = '.$data['user_id'].' and user_expert_level = '.$data['user_expert_level']);
                if(!empty($data['indexes'])){
                    $leniency_index_chunks = array_chunk($data['indexes'], 50);
                    foreach($leniency_index_chunks as $leniency_index_chunk){
                        $this->XM->sqlcore->query('UPDATE tasting_user_global_evaluation_score SET tuges_leniency = 1 where user_id = '.$data['user_id'].' and user_expert_level = '.$data['user_expert_level'].' and t_id in ('.implode(',', $leniency_index_chunk).')');
                    }
                    unset($leniency_index_chunks);
                }
            }
        }
        unset($leniency_indexes);
        

        $this->XM->sqlcore->commit();
    }
    private function __generate_automatic_evaluation($tpv_id,$global_expert_evaluation){
        $tpv_id = (int)$tpv_id;
        $this->XM->product->load();
        $max_user_expert_level = 3;
        if($global_expert_evaluation){
            $res = $this->XM->sqlcore->query('SELECT count(1) as expert_count
                from product_vintage_review 
                where tpv_id = '.$tpv_id.' and user_expert_level = 3 and pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW).' = 0');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if(!$row || (int)$row['expert_count']<\TASTING\GLOBAL_EVALUATION_MIN_RANK_3_EXPERT_COUNT){
                return false;
            }
            $max_user_expert_level = 3;
        } else {
            $res = $this->XM->sqlcore->query('SELECT max(user_expert_level) as user_expert_level
                from product_vintage_review 
                where tpv_id = '.$tpv_id.' and pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW).' = 0');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if(!$row){//have no reviews
                return false;
            }
            $max_user_expert_level = (int)$row['user_expert_level'];
        }
        $res = $this->XM->sqlcore->query('SELECT tasting.t_id,tasting.t_score_method,tasting_product_vintage.tpv_blind,tasting_user_evaluation.tue_id,tasting.t_evaluation_automatic, tasting_user_evaluation.tue_score_permissible_variation_type, tasting_user_evaluation.tue_score_permissible_variation_value, tasting_user_evaluation.tue_score_score, tasting_user_evaluation.tue_total_score, tasting_user_evaluation_left.tue_id as tue_exists
            from tasting
            inner join tasting_product_vintage on tasting_product_vintage.t_id = tasting.t_id and tasting_product_vintage.tpv_review_request_status = 2
            '.($global_expert_evaluation?
                'inner join tasting_user_evaluation on tasting_user_evaluation.tue_type = 5':
                'inner join tasting_user_evaluation on tasting_user_evaluation.t_id = tasting.t_id and tasting_user_evaluation.tue_type = 3').'
            left join tasting_user_evaluation as tasting_user_evaluation_left on tasting_user_evaluation_left.tpv_id = tasting_product_vintage.tpv_id and tasting_user_evaluation_left.tue_type = '.($global_expert_evaluation?4:2).'
            where tasting_product_vintage.tpv_id = '.$tpv_id.'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row || $row['tue_exists'] || (int)$row['t_score_method']!=0){
            return false;
        }
        if(!$global_expert_evaluation && !$row['t_evaluation_automatic']){
            return false;
        }
        $tasting_id = (int)$row['t_id'];
        $tpv_blind = (bool)$row['tpv_blind'];
        $template_tue_id = (int)$row['tue_id'];
        $score_permissible_variation_type = $row['tue_score_permissible_variation_type']?1:0;
        $score_permissible_variation_value = null;
        if($score_permissible_variation_type==0){//auto
            $res = $this->XM->sqlcore->query('SELECT greatest(avg(pvr_score)-min(pvr_score),max(pvr_score)-avg(pvr_score)) as deltascore,avg(pvr_score) as avgscore
                from product_vintage_review 
                where tpv_id = '.$tpv_id.' and pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW).' = 0 and user_expert_level = '.$max_user_expert_level);
            $deltascore_row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if(!$deltascore_row){//excessive
                return false;
            }
            $score_permissible_variation_value = (int)$deltascore_row['deltascore'];
            $score_avg = (int)$deltascore_row['avgscore'];
        } else {
            $res = $this->XM->sqlcore->query('SELECT avg(pvr_score) as avgscore
                from product_vintage_review 
                where tpv_id = '.$tpv_id.' and pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW).' = 0 and user_expert_level = '.$max_user_expert_level);
            $avgscore_row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if(!$avgscore_row){//excessive
                return false;
            }
            $score_permissible_variation_value = (int)$row['tue_score_permissible_variation_value'];
            $score_avg = (int)$avgscore_row['avgscore'];
        }
        $score_score = (int)$row['tue_score_score'];
        $has_element_scores = ($score_score!=(int)$row['tue_total_score']);
        $sumscore = $score_score;
        if($has_element_scores){
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE scoreids(
                    pvrpl_id INT UNSIGNED NOT NULL,
                    pa_id INT UNSIGNED NULL,
                    pvrp_value BIGINT UNSIGNED NOT NULL,
                    tues_score int(5) UNSIGNED NOT NULL
                )');
            $this->XM->sqlcore->query('INSERT INTO scoreids (pvrpl_id,pvrp_value,tues_score)
                select distinct valuescnt.pvrpl_id,valuescnt.pvrp_value,tasting_user_evaluation_score.tues_score
                    from (
                        SELECT tasting_user_evaluation_score.pvrpl_id,product_vintage_review_param.pvrp_value,count(1) as cnt
                            from product_vintage_review 
                            inner join product_vintage_review_param on product_vintage_review_param.pvr_id = product_vintage_review.pvr_id 
                            inner join product_vintage_review_param_list on product_vintage_review_param_list.pvrpl_id = product_vintage_review_param.pvrpl_id and product_vintage_review_param_list.pvrpl_exact_blind = 0
                            inner join tasting_user_evaluation_score on tasting_user_evaluation_score.pvrpl_id = product_vintage_review_param.pvrpl_id and tasting_user_evaluation_score.tue_id = '.$template_tue_id.' 
                            where product_vintage_review.tpv_id = '.$tpv_id.' and product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW).' = 0 and product_vintage_review.user_expert_level = '.$max_user_expert_level.'
                            group by tasting_user_evaluation_score.pvrpl_id,product_vintage_review_param.pvrp_value
                    ) as valuescnt 
                    inner join (
                        select pvrpl_id,max(cnt) as maxcnt
                            from (
                                SELECT tasting_user_evaluation_score.pvrpl_id,count(1) as cnt
                                    from product_vintage_review 
                                    inner join product_vintage_review_param on product_vintage_review_param.pvr_id = product_vintage_review.pvr_id 
                                    inner join product_vintage_review_param_list on product_vintage_review_param_list.pvrpl_id = product_vintage_review_param.pvrpl_id and product_vintage_review_param_list.pvrpl_exact_blind = 0
                                    inner join tasting_user_evaluation_score on tasting_user_evaluation_score.pvrpl_id = product_vintage_review_param.pvrpl_id and tasting_user_evaluation_score.tue_id = '.$template_tue_id.' 
                                    where product_vintage_review.tpv_id = '.$tpv_id.' and product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW).' = 0 and product_vintage_review.user_expert_level = '.$max_user_expert_level.'
                                    group by tasting_user_evaluation_score.pvrpl_id,product_vintage_review_param.pvrp_value
                            ) as cnts
                            group by pvrpl_id
                    ) as maxcnts on maxcnts.pvrpl_id = valuescnt.pvrpl_id and maxcnts.maxcnt = valuescnt.cnt
                    inner join tasting_user_evaluation_score on tasting_user_evaluation_score.pvrpl_id = valuescnt.pvrpl_id and tasting_user_evaluation_score.tue_id = '.$template_tue_id);
            if($tpv_blind){
                //excessive blind check
                $this->XM->sqlcore->query('INSERT INTO scoreids (pvrpl_id,pa_id,pvrp_value,tues_score)
                    SELECT tasting_user_evaluation_score.pvrpl_id,tasting_user_evaluation_score.pa_id as pa_id,product_attribute_value.pav_id as pvrp_value,tasting_user_evaluation_score.tues_score
                        from tasting_user_evaluation_score
                        inner join product_vintage_review_param_list on product_vintage_review_param_list.pvrpl_id = tasting_user_evaluation_score.pvrpl_id and product_vintage_review_param_list.pvrpl_exact_blind = 1
                        inner join product_attribute on product_attribute.pa_id = tasting_user_evaluation_score.pa_id
                        inner join product_attribute_group on product_attribute_group.pag_id = product_attribute.pag_id
                        inner join tasting_product_vintage on tasting_product_vintage.tpv_id = '.$tpv_id.' and tasting_product_vintage.tpv_blind = 1
                        inner join product_vintage on product_vintage.pv_id = tasting_product_vintage.pv_id
                        left join product_vintage_value on product_vintage_value.pv_id = product_vintage.pv_id and product_vintage_value.pag_id = product_attribute_group.pag_id and product_attribute_group.pag_overload = 1
                        left join product_value on product_value.p_id = product_vintage.p_id and product_value.pag_id = product_attribute_group.pag_id
                        inner join product_attribute_value_tree on product_attribute_value_tree.pav_id = coalesce(product_vintage_value.pav_id,product_value.pav_id)
                        inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_tree.pav_anc_id and product_attribute_value.pa_id = tasting_user_evaluation_score.pa_id
                        where tasting_user_evaluation_score.tue_id = '.$template_tue_id);
            }
            $res = $this->XM->sqlcore->query('SELECT sum(tues_score) as sumscore from scoreids');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if(!$row){//excessive
                $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
                return false;
            }
            $sumscore = (int)$row['sumscore'] + $score_score;
        }
        $this->XM->sqlcore->query('INSERT INTO tasting_user_evaluation (t_id,tpv_id,tue_type,tue_score_permissible_variation_type,tue_score_permissible_variation_value,tue_score_avg,tue_score_score,tue_total_score) 
            values ('.$tasting_id.','.$tpv_id.','.($global_expert_evaluation?4:2).','.$score_permissible_variation_type.','.$score_permissible_variation_value.','.$score_avg.','.$score_score.','.$sumscore.')');
        $tasting_user_evaluation_id = $this->XM->sqlcore->lastInsertId();
        if($has_element_scores){
            $this->XM->sqlcore->query('INSERT INTO tasting_user_evaluation_score (tue_id,pvrpl_id,pa_id,pvrp_value,tues_score)
                SELECT distinct '.$tasting_user_evaluation_id.' as tue_id, pvrpl_id, pa_id, pvrp_value, tues_score
                    from scoreids');
        }
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS scoreids');
        $this->XM->sqlcore->commit();
        $this->__process_evaluation($tasting_user_evaluation_id);
        return true;
    }
    public function __process_evaluation($tasting_user_evaluation_id){
        $tasting_user_evaluation_id = (int)$tasting_user_evaluation_id;
        if($tasting_user_evaluation_id<=0){
            return false;
        }
        $this->XM->product->load();
        $res = $this->XM->sqlcore->query('SELECT tue_score_permissible_variation_value, tue_score_score, tue_score_avg, tue_total_score, tpv_id from tasting_user_evaluation where tue_id = '.$tasting_user_evaluation_id);
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//excessive
            return false;
        }
        $tpv_id = (int)$row['tpv_id'];
        $score_score = (int)$row['tue_score_score'];
        $total_score = (int)$row['tue_total_score'];
        
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE userscores (
            user_id BIGINT UNSIGNED NOT NULL,
            pvr_id BIGINT UNSIGNED NOT NULL,
            score int(5) UNSIGNED NOT NULL
            )');
        if($score_score){
            $score_avg = (int)$row['tue_score_avg'];
            $score_permissible_variation_value = (int)$row['tue_score_permissible_variation_value'];
            if($score_permissible_variation_value!=0){//div 0
                $this->XM->sqlcore->query('INSERT INTO userscores (user_id,pvr_id,score)
                    select user_id,pvr_id,(pow('.$score_permissible_variation_value.'-userdeltascores.deltascore,2)*10000*'.$score_score.'/'.pow($score_permissible_variation_value,2).') as score
                        from (
                            SELECT user_id,pvr_id,abs(cast(pvr_score as signed)-'.$score_avg.') as deltascore
                                from product_vintage_review
                                where tpv_id = '.$tpv_id.' and pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW).' = 0
                        ) as userdeltascores
                        where deltascore<'.$score_permissible_variation_value);
            } else {
                $this->XM->sqlcore->query('INSERT INTO userscores (user_id,pvr_id,score)
                    select user_id,pvr_id,'.($score_score*10000).' as score
                        from product_vintage_review
                        where pvr_score = '.$score_avg.' and tpv_id = '.$tpv_id.' and pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW).' = 0 ');
            }
        }
        // pvrpl_exact_blind = 0
        $this->XM->sqlcore->query('INSERT INTO userscores (user_id,pvr_id,score)
            select product_vintage_review.user_id,product_vintage_review.pvr_id,sum(tasting_user_evaluation_score.tues_score)*10000
                from product_vintage_review
                inner join product_vintage_review_param on product_vintage_review_param.pvr_id = product_vintage_review.pvr_id
                inner join product_vintage_review_param_list on product_vintage_review_param_list.pvrpl_id = product_vintage_review_param.pvrpl_id and product_vintage_review_param_list.pvrpl_exact_blind = 0
                inner join tasting_user_evaluation_score on tasting_user_evaluation_score.pvrpl_id = product_vintage_review_param.pvrpl_id and tasting_user_evaluation_score.pvrp_value = product_vintage_review_param.pvrp_value and tasting_user_evaluation_score.tue_id = '.$tasting_user_evaluation_id.'
                where product_vintage_review.tpv_id = '.$tpv_id.' and product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW).' = 0
                group by product_vintage_review.pvr_id,product_vintage_review.user_id');
        $this->XM->sqlcore->query('INSERT INTO userscores (user_id,pvr_id,score)
            select score.user_id,score.pvr_id,sum(score.tues_score)*10000 as score
                from (
                    select product_vintage_review.user_id, product_vintage_review.pvr_id, max(tasting_user_evaluation_score.tues_score) as tues_score
                        from product_vintage_review
                        inner join product_vintage_review_param on product_vintage_review_param.pvr_id = product_vintage_review.pvr_id
                        inner join product_vintage_review_param_list on product_vintage_review_param_list.pvrpl_id = product_vintage_review_param.pvrpl_id and product_vintage_review_param_list.pvrpl_exact_blind = 1
                        inner join product_attribute_value_tree on product_attribute_value_tree.pav_id = product_vintage_review_param.pvrp_value
                        left join product_attribute_value_analog pava_main on pava_main.pav_id = product_attribute_value_tree.pav_anc_id
                        left join product_attribute_value_analog on product_attribute_value_analog.pava_group_id = pava_main.pava_group_id
                        inner join tasting_user_evaluation_score on tasting_user_evaluation_score.pvrpl_id = product_vintage_review_param.pvrpl_id and tasting_user_evaluation_score.pvrp_value = coalesce(product_attribute_value_analog.pav_id,product_attribute_value_tree.pav_anc_id) and tasting_user_evaluation_score.tue_id = '.$tasting_user_evaluation_id.'
                        where product_vintage_review.tpv_id = '.$tpv_id.' and product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW).' = 0
                        group by coalesce(product_attribute_value_analog.pava_group_id,product_attribute_value_tree.pav_anc_id), product_vintage_review_param_list.pvrpl_id, product_vintage_review.user_id, product_vintage_review.pvr_id
                    ) as score
                group by score.pvr_id,score.user_id');
        //subcolor section
        $this->XM->sqlcore->query('INSERT INTO userscores (user_id,pvr_id,score)
            select product_vintage_review.user_id, product_vintage_review.pvr_id, sum(tasting_user_evaluation_score.tues_score)*10000 as score
                from product_vintage_review
                inner join tasting_user_evaluation_score on tasting_user_evaluation_score.pvrpl_id = '.\TASTING\EVALUATION_CUSTOM_PARAM_SUBCOLOR_DEPTH_PVRPL_ID.' and tasting_user_evaluation_score.tue_id = '.$tasting_user_evaluation_id.'
                inner join product_vintage_review_param_list product_vintage_review_param_list_color on product_vintage_review_param_list_color.pvrpl_color = 1
                inner join product_vintage_review_param_list product_vintage_review_param_list_subcolor on product_vintage_review_param_list_subcolor.pvrpl_color = 2
                inner join product_vintage_review_param_list product_vintage_review_param_list_depth on product_vintage_review_param_list_depth.pvrpl_color = 3
                inner join product_vintage_review_param product_vintage_review_param_color on product_vintage_review_param_color.pvr_id = product_vintage_review.pvr_id and product_vintage_review_param_color.pvrpl_id = product_vintage_review_param_list_color.pvrpl_id
                inner join product_vintage_review_param product_vintage_review_param_subcolor on product_vintage_review_param_subcolor.pvr_id = product_vintage_review.pvr_id and product_vintage_review_param_subcolor.pvrpl_id = product_vintage_review_param_list_subcolor.pvrpl_id
                inner join product_vintage_review_param product_vintage_review_param_depth on product_vintage_review_param_depth.pvr_id = product_vintage_review.pvr_id and product_vintage_review_param_depth.pvrpl_id = product_vintage_review_param_list_depth.pvrpl_id
                where product_vintage_review.tpv_id = '.$tpv_id.' and product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW).' = 0
                    and tasting_user_evaluation_score.pvrp_value = product_vintage_review_param_color.pvrp_value*10000+product_vintage_review_param_subcolor.pvrp_value*100+product_vintage_review_param_depth.pvrp_value
            group by product_vintage_review.pvr_id,product_vintage_review.user_id');

        $this->XM->sqlcore->query('INSERT INTO tasting_user_evaluation_user_score (tue_id,user_id,pvr_id,tueus_score)
            SELECT '.$tasting_user_evaluation_id.' as tue_id,userscores.user_id,userscores.pvr_id,cast(sum(userscores.score)/'.$total_score.' as unsigned) as tueus_score
                from userscores
                left join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = '.$tasting_user_evaluation_id.' and tasting_user_evaluation_user_score.user_id = userscores.user_id
                where tasting_user_evaluation_user_score.tue_id is null
                group by userscores.pvr_id,userscores.user_id');//left join is excessive
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS userscores');
        $this->XM->sqlcore->commit();

        return true;
    }
    public function change_tasting_status($tasting_id, $new_status, &$err){
        $status_list = $this->get_status_list();
        if(!array_key_exists($new_status, $status_list)){
            $err = langTranslate('tasting', 'err', 'Invalid status',  'Invalid status');
            return false;
        }
        $new_status = (int)$new_status;
        $tasting_id = (int)$tasting_id;
        $res = $this->XM->sqlcore->query('SELECT user_id,t_status,t_evaluation_automatic,t_score_method,t_assessment from tasting where t_id = '.$tasting_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Tasting doesn\'t exist', 'Tasting doesn\'t exist');
            return false;
        }
        if($this->XM->user->getUserId()!=(int)$row['user_id']&&!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_TASTINGS)){//has_edit_rights
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $old_status = (int)$row['t_status'];
        $evaluation_automatic = (bool)$row['t_evaluation_automatic'];
        $score_method = (int)$row['t_score_method'];
        $assessment = (bool)$row['t_assessment'];
        if($old_status == $new_status){
            return true;
        }
        switch($new_status){
            case \TASTING\TASTING_STATUS_DELETED:
                if($old_status!==\TASTING\TASTING_STATUS_DRAFT){
                    $err = langTranslate('tasting', 'err', 'You can delete only draft tastings', 'You can delete only draft tastings');
                    return false;
                }
                $res = $this->XM->sqlcore->query('SELECT 1 from tasting_contest_tasting where t_id = '.$tasting_id.' limit 1');
                $row = $this->XM->sqlcore->getRow($res);
                $this->XM->sqlcore->freeResult($res);
                if($row){
                    $err = langTranslate('tasting', 'err', 'You can\'t delete tastings that are being used in contests', 'You can\'t delete tastings that are being used in contests');
                    return false;
                }
                break;
            case \TASTING\TASTING_STATUS_DRAFT:
                if($old_status!==\TASTING\TASTING_STATUS_PREPARATION && $old_status!==\TASTING\TASTING_STATUS_DELETED){
                    $err = langTranslate('tasting', 'err', 'Only preparation and deleted tastings can convert to draft stage', 'Only preparation and deleted tastings can convert to draft stage');
                    return false;
                }
                break;
            case \TASTING\TASTING_STATUS_PREPARATION:
                if($old_status!==\TASTING\TASTING_STATUS_DRAFT && $old_status!==\TASTING\TASTING_STATUS_STARTED){
                    $err = langTranslate('tasting', 'err', 'Only draft and started tastings can convert to preparation stage', 'Only draft and started tastings can convert to preparation stage');
                    return false;
                }
                if($old_status===\TASTING\TASTING_STATUS_STARTED){
                    $res = $this->XM->sqlcore->query('SELECT 1 from product_vintage_review where t_id = '.$tasting_id.' limit 1');
                    $row = $this->XM->sqlcore->getRow($res);
                    $this->XM->sqlcore->freeResult($res);
                    if($row){
                        $err = langTranslate('tasting', 'err', 'You can\'t rollback tasting status with saved expert reviews', 'You can\'t rollback tasting status with saved expert reviews');
                        return false;
                    }    
                }
                
                break;
            case \TASTING\TASTING_STATUS_STARTED:
                if($old_status!==\TASTING\TASTING_STATUS_PREPARATION){
                    $err = langTranslate('tasting', 'err', 'You can start tastings only in preparation stage', 'You can start tastings only in preparation stage');
                    return false;
                }
                break;
            case \TASTING\TASTING_STATUS_FINISHED:
                if($old_status!==\TASTING\TASTING_STATUS_STARTED){
                    $err = langTranslate('tasting', 'err', 'You can finalize only started tastings', 'You can finalize only started tastings');
                    return false;
                }
                break;
            default: //never
                $err = langTranslate('tasting', 'err', 'Invalid status',  'Invalid status');
                return false;
        }
        //manipulating user responses
        if($new_status==\TASTING\TASTING_STATUS_DRAFT && $old_status==\TASTING\TASTING_STATUS_PREPARATION){//cancelled
            $this->__send_tasting_cancelled_mail($tasting_id);
            //refresh all invite responses
            $this->XM->sqlcore->query('UPDATE tasting_user set tu_response = '.\TASTING\TASTING_USER_RESPONSE_PENDING.' where t_id = '.$tasting_id);
            $this->XM->sqlcore->commit();
        }
        if($new_status==\TASTING\TASTING_STATUS_PREPARATION && $old_status==\TASTING\TASTING_STATUS_DRAFT){
            $res = $this->XM->sqlcore->query('SELECT 1 
                from product 
                inner join product_vintage on product_vintage.p_id = product.p_id
                inner join tasting_product_vintage on tasting_product_vintage.pv_id = product_vintage.pv_id
                where tasting_product_vintage.t_id = '.$tasting_id.' and product.p_is_approved = 0 
                limit 1');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if($row){//tasting has not approved products
                $err = langTranslate('tasting', 'err', 'You will be able to progress tasting after all declared products are approved', 'You will be able to progress tasting after all declared products are approved');
                return false;
            }
            $this->__send_tasting_invite_mails($tasting_id);
        }
        if($new_status==\TASTING\TASTING_STATUS_FINISHED){
            // stop review requests
            if($score_method==0){
                $tpv_ids = array();
                $res = $this->XM->sqlcore->query('SELECT distinct tpv_id FROM tasting_product_vintage WHERE tasting_product_vintage.t_id = '.$tasting_id.' and tasting_product_vintage.tpv_review_request_status <> 2');
                while($row = $this->XM->sqlcore->getRow($res)){
                    $tpv_ids[] = (int)$row['tpv_id'];
                }
                $this->XM->sqlcore->freeResult($res);
                $this->XM->sqlcore->query('UPDATE tasting_product_vintage set tpv_review_request_status = 2 where tasting_product_vintage.t_id = '.$tasting_id);
                foreach($tpv_ids as $tpv_id){
                    $this->__fill_pvr_block_for_tpv_id($tpv_id);    
                }
                if($evaluation_automatic){
                    foreach($tpv_ids as $tpv_id){
                        $this->__generate_automatic_evaluation($tpv_id,false);
                    }
                }
                $this->XM->product->load();
                $this->XM->sqlcore->query('UPDATE product_vintage_review set pvr_block = pvr_block&~'.\PRODUCT\PVR_BLOCK_ONGOING_TASTING.' where t_id = '.$tasting_id);
                $this->XM->product->__refresh_personal_vintage_scores_for_tasting($tasting_id);
            }
        }
        //end of manipulating user responses
        $this->XM->sqlcore->query('UPDATE tasting set t_status = '.$new_status.' where t_id = '.$tasting_id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function assess_tasting($tasting_id,$assessment,&$err){
        $tasting_id = (int)$tasting_id;
        $assessment = $assessment?1:0;
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_APPROVE_TASTING)){
            $err = langTranslate('tasting', 'err', 'Access Denied', 'Access Denied');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT t_status, t_assessment, t_is_approved, t_score_method
            from tasting
            where t_id = '.$tasting_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Tasting doesn\'t exist', 'Tasting doesn\'t exist');
            return false;
        }
        if((int)$row['t_status']!=\TASTING\TASTING_STATUS_FINISHED || !$row['t_assessment']){
            $err = langTranslate('tasting', 'err', 'You can only assess finalized public tastings', 'You can only assess finalized public tastings');
            return false;
        }
        if($row['t_is_approved']!==null){
            return true;
        }
        $this->XM->sqlcore->query('UPDATE tasting set t_is_approved = '.$assessment.' where t_id = '.$tasting_id.' and t_is_approved is null and t_assessment = 1');
        $this->XM->sqlcore->commit();
        if($assessment){
            $this->XM->product->load();
            $this->XM->sqlcore->query('UPDATE product_vintage_review set pvr_block = pvr_block&~'.\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE.' where t_id = '.$tasting_id);
            $this->XM->sqlcore->commit();
            if((int)$row['t_score_method']==0){
                $this->XM->product->__refresh_vintage_scores_for_tasting($tasting_id);
            }
            $this->__refresh_global_expert_evaluation_for_tasting($tasting_id);
        }
        return true;
    }
    private function __send_tasting_invite_mail_with_data($tasting_id,$tastinginfo,$tasting_vintage_list,$user_response_list,$tu_id,$user_id,$email,$fullname){
        $usercode = $this->XM->sqlcore->checksum(md5($tu_id.$tasting_id.$user_id));
        $body = $this->XM->view->load('tasting/mailtasting',array('tastinginfo'=>$tastinginfo, 'tasting_vintage_list'=>$tasting_vintage_list,'invited'=>true,'user_response_list'=>$this->XM->tasting->get_user_response_list(false),'tu_id'=>$tu_id,'usercode'=>$usercode),true);
        
        $this->XM->sendmail->reset();
        $this->XM->sendmail->addAddress($email,$fullname);
        $this->XM->sendmail->setSubject(langTranslate('tasting','tasting','You\'ve been invited to the tasting','You\'ve been invited to the tasting'));
        $this->XM->sendmail->setBody($body,true,'',true);
        $this->XM->sendmail->send();
        return  true;
    }
    private function __send_tasting_invite_mail($tasting_id, $user_id){
        $tasting_id = (int)$tasting_id;
        $user_id = (int)$user_id;
        
        $res = $this->XM->sqlcore->query('SELECT tu_id from tasting_user where t_id = '.$tasting_id.' and user_id = '.$user_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        $tu_id = (int)$row['tu_id'];
        $res = $this->XM->sqlcore->query('SELECT user_login as email,user.lang_id as letter_lang, user_ml_fullname
            from user
            left join (select user_ml.user_id,substring_index(group_concat(user_ml.user_ml_id order by user_ml.lang_id = user.lang_id desc),\',\',1) as user_ml_id 
                from user_ml 
                inner join user on user.user_id = user_ml.user_id
                where user_ml.user_ml_is_approved = 1 group by user_ml.user_id
            ) as ln_glue on ln_glue.user_id = user.user_id
            left join user_ml on user_ml.user_ml_id = ln_glue.user_ml_id
            where user.user_id = '.$user_id.' 
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        $this->XM->lang->setTempLang((int)$row['letter_lang']);
        $email = $row['email'];
        $fullname = $row['user_ml_fullname'];
        $tastinginfo = $this->XM->tasting->get_tasting($tasting_id);
        if(!$tastinginfo){
            return false;
        }
        $tasting_vintage_list = $this->XM->product->get_tasting_vintage_list($tasting_id, false, false, false, false, false, false, false, true, null, null, null, false, true);
        $user_response_list = $this->XM->tasting->get_user_response_list(false);
        $result = $this->__send_tasting_invite_mail_with_data($tasting_id,$tastinginfo,$tasting_vintage_list,$user_response_list,$tu_id,$user_id,$email,$fullname);
        $this->XM->lang->revertTempLang();
        return $result;
    }
    private function __send_tasting_invite_mails($tasting_id){
        $tasting_id = (int)$tasting_id;
        if(!$this->XM->tasting->get_tasting($tasting_id)){
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT distinct tasting_user.tu_id,user.user_id, user_login as email,user.lang_id as letter_lang, user_ml_fullname
            from tasting_user
            inner join user on user.user_id = tasting_user.user_id
            left join (select user_ml.user_id,substring_index(group_concat(user_ml.user_ml_id order by user_ml.lang_id = user.lang_id desc),\',\',1) as user_ml_id 
                from user_ml 
                inner join user on user.user_id = user_ml.user_id
                where user_ml.user_ml_is_approved = 1 group by user_ml.user_id) as ln_glue on ln_glue.user_id = user.user_id
            left join user_ml on user_ml.user_ml_id = ln_glue.user_ml_id
            where tasting_user.t_id = '.$tasting_id.' and tasting_user.tu_response = '.\TASTING\TASTING_USER_RESPONSE_PENDING);
        $langReceivers = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $letter_lang = (int)$row['letter_lang'];
            if(!isset($langReceivers[$letter_lang])){
                $langReceivers[$letter_lang] = array();
            }
            $langReceivers[$letter_lang][] = array('user_id'=>(int)$row['user_id'],'tu_id'=>(int)$row['tu_id'],'email'=>$row['email'],'user_ml_fullname'=>$row['user_ml_fullname']);
        }
        $this->XM->sqlcore->freeResult($res);

        foreach($langReceivers as $letter_lang=>$receivers){
            $this->XM->lang->setTempLang($letter_lang);
            $tastinginfo = $this->XM->tasting->get_tasting($tasting_id);
            $tasting_vintage_list = $this->XM->product->get_tasting_vintage_list($tasting_id, false, false, false, false, false, false, false, true, null, null, null, false, true);
            $user_response_list = $this->XM->tasting->get_user_response_list(false);
            foreach($receivers as $receiver){
                $user_id = (int)$receiver['user_id'];
                $tu_id = (int)$receiver['tu_id'];
                $email = $receiver['email'];
                $fullname = $receiver['user_ml_fullname'];
                $this->__send_tasting_invite_mail_with_data($tasting_id,$tastinginfo,$tasting_vintage_list,$user_response_list,$tu_id,$user_id,$email,$fullname);
            }
            $this->XM->lang->revertTempLang();
        }
        return true;
    }
    private function __send_tasting_cancelled_mail($tasting_id){
        $tasting_id = (int)$tasting_id;
        if(!$this->XM->tasting->get_tasting($tasting_id)){
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT user.user_login as email,user.lang_id as letter_lang, user_ml.user_ml_fullname
            from tasting_user
            inner join user on user.user_id = tasting_user.user_id
            left join (select user_ml.user_id,substring_index(group_concat(user_ml.user_ml_id order by user_ml.lang_id = user.lang_id desc),\',\',1) as user_ml_id 
                from user_ml 
                inner join user on user.user_id = user_ml.user_id
                where user_ml.user_ml_is_approved = 1 group by user_ml.user_id
            ) as ln_glue on ln_glue.user_id = user.user_id
            left join user_ml on user_ml.user_ml_id = ln_glue.user_ml_id
            where tasting_user.t_id = '.$tasting_id.' and tasting_user.tu_response IN ('.\TASTING\TASTING_USER_RESPONSE_ACCEPT.','.\TASTING\TASTING_USER_RESPONSE_UNCERTAIN.')
            order by 2');
        $langReceivers = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $letter_lang = (int)$row['letter_lang'];
            if(!isset($langReceivers[$letter_lang])){
                $langReceivers[$letter_lang] = array();
            }
            $langReceivers[$letter_lang][] = array('email'=>$row['email'],'user_ml_fullname'=>$row['user_ml_fullname']);
        }
        $this->XM->sqlcore->freeResult($res);

        foreach($langReceivers as $letter_lang=>$receivers){
            $this->XM->lang->setTempLang($letter_lang);
            $this->XM->sendmail->reset();
            $this->XM->sendmail->setBody($this->XM->view->load('tasting/mailtasting',array('tastinginfo'=>$this->XM->tasting->get_tasting($tasting_id),'tasting_vintage_list'=>$this->XM->product->get_tasting_vintage_list($tasting_id, false, false, false, false, false, false, false, true, null, null, null, false, true),'cancelled'=>true),true),true,'',true);
            $this->XM->sendmail->setSubject(langTranslate('tasting','mail','Tasting you\'ve been invited to has been cancelled','Tasting you\'ve been invited to has been cancelled'));
            foreach($receivers as $receiver){
                $this->XM->sendmail->addAddress($receiver['email'],$receiver['user_ml_fullname']);        
            }
            $this->XM->sendmail->send();
            $this->XM->lang->revertTempLang();
        }
        return true;
    }
    private function __send_tasting_invite_revoked_mail($tasting_id,$user_id){
        $tasting_id = (int)$tasting_id;
        $user_id = (int)$user_id;
        if(!$this->XM->tasting->get_tasting($tasting_id)){
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT user_login as email,user.lang_id as letter_lang, user_ml_fullname
            from user
            left join (select user_ml.user_id,substring_index(group_concat(user_ml.user_ml_id order by user_ml.lang_id = user.lang_id desc),\',\',1) as user_ml_id 
                from user_ml 
                inner join user on user.user_id = user_ml.user_id
                where user_ml.user_ml_is_approved = 1 group by user_ml.user_id
            ) as ln_glue on ln_glue.user_id = user.user_id
            left join user_ml on user_ml.user_ml_id = ln_glue.user_ml_id
            where user.user_id = '.$user_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        $this->XM->lang->setTempLang((int)$row['letter_lang']);
        $this->XM->sendmail->reset();
        $this->XM->sendmail->addAddress($row['email'],$row['user_ml_fullname']);
        $this->XM->sendmail->setSubject(langTranslate('tasting','mail','Your invite to the tasting has been revoked','Your invite to the tasting has been revoked'));
        $this->XM->sendmail->setBody($this->XM->view->load('tasting/mailtasting',array('tastinginfo'=>$this->XM->tasting->get_tasting($tasting_id),'tasting_vintage_list'=>$this->XM->product->get_tasting_vintage_list($tasting_id, false, false, false, false, false, false, false, true, null, null, null, false, true),'invite_revoked'=>true),true),true,'',true);
        $this->XM->sendmail->send();
        $this->XM->lang->revertTempLang();
    }
    private function __send_review_request_mails($tasting_id){
        $tasting_id = (int)$tasting_id;
        $tastinginfo = $this->XM->tasting->get_tasting($tasting_id);
        if(!$tastinginfo){
            return false;
        }
        $tasting_vintage_list = $this->XM->product->get_tasting_vintage_list($tasting_id, false, false, false, false, false, false, false, true, null, null, null, false, true);

        $res = $this->XM->sqlcore->query('SELECT distinct user.user_login as email,user.lang_id as letter_lang, user_ml.user_ml_fullname, tasting_user.tu_id
            from tasting_user
            inner join user on user.user_id = tasting_user.user_id and user.user_expert_level > 0
            left join (select user_ml.user_id,substring_index(group_concat(user_ml.user_ml_id order by user_ml.lang_id = user.lang_id desc),\',\',1) as user_ml_id 
                from user_ml 
                inner join user on user.user_id = user_ml.user_id
                where user_ml.user_ml_is_approved = 1 group by user_ml.user_id) as ln_glue on ln_glue.user_id = user.user_id
            left join user_ml on user_ml.user_ml_id = ln_glue.user_ml_id
            where tasting_user.t_id = '.$tasting_id.' and tasting_user.tu_presence = 1 and tasting_user.tu_review_request_sent = 0
            order by 2');
        $langReceivers = array();
        $sent_review_requests = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $letter_lang = (int)$row['letter_lang'];
            if(!isset($langReceivers[$letter_lang])){
                $langReceivers[$letter_lang] = array();
            }
            $langReceivers[$letter_lang][] = array('email'=>$row['email'],'user_ml_fullname'=>$row['user_ml_fullname']);
            $sent_review_requests[] = (int)$row['tu_id'];
        }
        $this->XM->sqlcore->freeResult($res);

        foreach($langReceivers as $letter_lang=>$receivers){
            $this->XM->lang->setTempLang($letter_lang);
            $this->XM->sendmail->reset();
            $this->XM->sendmail->setBody($this->XM->view->load('tasting/mailtasting',array('tastinginfo'=>$tastinginfo,'tasting_vintage_list'=>$tasting_vintage_list,'review_request'=>true),true),true,'',true);
            $this->XM->sendmail->setSubject(langTranslate('tasting','mail','Requesting reviews for tasting you\'ve participated in','Requesting reviews for tasting you\'ve participated in'));
            foreach($receivers as $receiver){
                $this->XM->sendmail->addAddress($receiver['email'],$receiver['user_ml_fullname']);        
            }
            $this->XM->sendmail->send();
            $this->XM->lang->revertTempLang();
        }
        $sent_review_requests_chunks = array_chunk($sent_review_requests, 50);
        foreach($sent_review_requests_chunks as $sent_review_requests_chunk){
            $this->XM->sqlcore->query('UPDATE tasting_user set tu_review_request_sent = 1 where tu_id in ('.implode(',', $sent_review_requests_chunk).')');
            $this->XM->sqlcore->commit();
        }
        return true;
    }
    public function add_tasting($name, $location, $start_date, $start_time, $end_date, $end_time, $desc, $participation, $participation_rating, $chargeability, $price_grid, $assessment, $score_method, &$err){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_ADD_TASTING)){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $insert_keys[] = 'user_id';
        $insert_vals[] = $this->XM->user->getUserId();
        $insert_keys[] = 't_status';
        $insert_vals[] = \TASTING\TASTING_STATUS_DRAFT;

        $name = trim($name);
        if(strlen($name)){
            if(mb_strlen($name,'UTF-8')>128){
                $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                        langTranslate('tasting', 'tasting', 'Name', 'Name'),
                        128);
                return false;
            }
            $insert_keys[] = 't_name';
            $insert_vals[] = '\''.$this->XM->sqlcore->prepString($name,128).'\'';
        }

        $location = trim($location);
        if(!strlen($location)){
            $err = formatReplace(langTranslate('tasting', 'err', 'Field @1 is empty',  'Field @1 is empty'),
                    langTranslate('tasting', 'tasting', 'Location', 'Location'));
            return false;
        }
        if(mb_strlen($location,'UTF-8')>512){
            $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('tasting', 'tasting', 'Location', 'Location'),
                    512);
            return false;
        }
        $insert_keys[] = 't_location';
        $insert_vals[] = '\''.$this->XM->sqlcore->prepString($location,512).'\'';

        if(!preg_match('#^[0-3]?[0-9]\.[0-1][0-9]\.\d{4}$#', $start_date)){
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'Start Date', 'Start Date'));
            return false;
        }
        $start_timestamp = strtotime($start_date);
        if(!preg_match('#^([0-2]?[0-9]):([0-5][0-9])$#', $start_time,$match)){
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'Start Time', 'Start Time'));
            return false;
        }
        $start_timestamp += $match[1]*3600+$match[2]*60;
        $insert_keys[] = 't_start_ts';
        $insert_vals[] = $start_timestamp;

        if(!preg_match('#^[0-3]?[0-9]\.[0-1][0-9]\.\d{4}$#', $end_date)){
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'End Date', 'End Date'));
            return false;
        }
        $end_timestamp = strtotime($end_date);
        if(!preg_match('#^([0-2]?[0-9]):([0-5][0-9])$#', $end_time,$match)){
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'End Time', 'End Time'));
            return false;
        }
        $end_timestamp += $match[1]*3600+$match[2]*60;
        if($end_timestamp <= $start_timestamp){
            $err = langTranslate('tasting', 'err', 'Starting time is greater than ending time',  'Starting time is greater than ending time');
            return false;
        }
        $insert_keys[] = 't_end_ts';
        $insert_vals[] = $end_timestamp;

        $desc = trim($desc);
        if(mb_strlen($desc,'UTF-8')>60000){
            $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('tasting', 'tasting', 'Description', 'Description'),
                    60000);
            return false;
        }
        $insert_keys[] = 't_desc';
        $insert_vals[] = '\''.$this->XM->sqlcore->prepString($desc,60000).'\'';

        $participation = (int)$participation;
        if(!in_array($participation, array(0,1,2))){
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'Participation', 'Participation'));
            return false;
        }
        $insert_keys[] = 't_participation_type';
        $insert_vals[] = $participation;

        if(!$participation){
            $participation_rating = 0;
        }
        $participation_rating = (int)$participation_rating;
        if($participation_rating<0 || $participation_rating>65000){
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'Rating Limitation', 'Rating Limitation'));
            return false;
        }
        $insert_keys[] = 't_participation_rating_limit';
        $insert_vals[] = $participation_rating;

        $chargeability = $chargeability?1:0;
        $insert_keys[] = 't_chargeability';
        $insert_vals[] = $chargeability;

        if($chargeability!=0){
            $guest_price = isset($price_grid['guest_price'])?(int)$price_grid['guest_price']:0;
            if($guest_price<0 || $guest_price>=1000000){
                $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'Price grid', 'Price grid').': '.langTranslate('tasting', 'tasting', 'Guest', 'Guest'));
                return false;
            }
            $insert_keys[] = 't_pricegrid_guest';
            $insert_vals[] = $guest_price;

            $expert_price = isset($price_grid['expert_price'])?(int)$price_grid['expert_price']:0;
            if($expert_price<0 || $expert_price>=1000000){
                $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'Price grid', 'Price grid').': '.langTranslate('tasting', 'tasting', 'Expert', 'Expert'));
                return false;
            }
            $insert_keys[] = 't_pricegrid_expert';
            $insert_vals[] = $expert_price;

            $rated_expert_rating = isset($price_grid['rated_expert_rating'])?(int)$price_grid['rated_expert_rating']:0;
            if($rated_expert_rating<0 || $rated_expert_rating>=65000){
                $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'Price grid', 'Price grid').': '.langTranslate('tasting', 'tasting', 'Rating Limitation', 'Rating Limitation'));
                return false;
            }
            $insert_keys[] = 't_pricegrid_rated_expert_rating';
            $insert_vals[] = $rated_expert_rating;

            $rated_expert_price = isset($price_grid['rated_expert_price'])?(int)$price_grid['rated_expert_price']:0;
            if($rated_expert_price<0 || $rated_expert_price>=1000000){
                $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'Price grid', 'Price grid').': '.langTranslate('tasting', 'tasting', 'Rating Limited Expert', 'Rating Limited Expert'));
                return false;
            }
            $insert_keys[] = 't_pricegrid_rated_expert';
            $insert_vals[] = $rated_expert_price;
        }
        $assessment = $assessment?1:0;
        $insert_keys[] = 't_assessment';
        $insert_vals[] = $assessment;

        $score_method = (int)$score_method;
        if(!in_array($score_method, array(0,1))){
             $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'Score method', 'Score method'));
             return false;
        }
        $insert_keys[] = 't_score_method';
        $insert_vals[] = $score_method;

        $this->XM->sqlcore->query('INSERT INTO tasting ('.implode(',', $insert_keys).') VALUES ('.implode(',', $insert_vals).')');
        $tasting_id = $this->XM->sqlcore->lastInsertId();
        $this->XM->sqlcore->commit();
        return $tasting_id;
    }
    public function edit_tasting($tasting_id, $name, $location, $start_date, $start_time, $end_date, $end_time, $desc, $participation, $participation_rating, $chargeability, $price_grid, $assessment, $score_method, &$err){
        if(($tasting_id = (int)$tasting_id)<=0){
            $err = langTranslate('tasting', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT t_start_ts,t_end_ts,t_name,t_location,t_desc,t_participation_type,t_participation_rating_limit,t_chargeability,t_pricegrid_guest,t_pricegrid_expert,t_pricegrid_rated_expert,t_pricegrid_rated_expert_rating,t_assessment,t_score_method,t_status,user_id from tasting where t_id = '.$tasting_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Tasting doesn\'t exist',  'Tasting doesn\'t exist');
            return false;
        }
        if($this->XM->user->getUserId()!=(int)$row['user_id']&&!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_TASTINGS)){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        if(\TASTING\TASTING_STATUS_DRAFT!=(int)$row['t_status']){
            $err = langTranslate('tasting','err','You can only edit tastings in draft stage','You can only edit tastings in draft stage');
            return false;
        }
        $update_arr = array();
        $name = trim($name);
        if(!strlen($name)){
            $name = null;
        }
        if($name!==$row['t_name']){
            if(mb_strlen($name,'UTF-8')>128){
                $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                        langTranslate('tasting', 'tasting', 'Name', 'Name'),
                        128);
                return false;
            }
            $update_arr[] = 't_name = '.($name!==null?'\''.$this->XM->sqlcore->prepString($name,128).'\'':'null');
        }
        $location = trim($location);
        if($location!=$row['t_location']){
            if(!strlen($location)){
                $err = formatReplace(langTranslate('tasting', 'err', 'Field @1 is empty',  'Field @1 is empty'),
                        langTranslate('tasting', 'tasting', 'Location', 'Location'));
                return false;
            }
            if(mb_strlen($location,'UTF-8')>512){
                $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                        langTranslate('tasting', 'tasting', 'Location', 'Location'),
                        512);
                return false;
            }
            $update_arr[] = 't_location = \''.$this->XM->sqlcore->prepString($location,512).'\'';
        }
        if(!preg_match('#^[0-3]?[0-9]\.[0-1][0-9]\.\d{4}$#', $start_date)){
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'Start Date', 'Start Date'));
            return false;
        }
        $start_timestamp = strtotime($start_date);
        if(!preg_match('#^([0-2]?[0-9]):([0-5][0-9])$#', $start_time,$match)){
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'Start Time', 'Start Time'));
            return false;
        }
        $start_timestamp += $match[1]*3600+$match[2]*60;
        if($start_timestamp!=$row['t_start_ts']){
            $update_arr[] = 't_start_ts = '.$start_timestamp;
        }
        if(!preg_match('#^[0-3]?[0-9]\.[0-1][0-9]\.\d{4}$#', $end_date)){
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'End Date', 'End Date'));
            return false;
        }
        $end_timestamp = strtotime($end_date);
        if(!preg_match('#^([0-2]?[0-9]):([0-5][0-9])$#', $end_time,$match)){
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'End Time', 'End Time'));
            return false;
        }
        $end_timestamp += $match[1]*3600+$match[2]*60;
        if($end_timestamp!=$row['t_end_ts']){
            $update_arr[] = 't_end_ts = '.$end_timestamp;
        }
        if($end_timestamp <= $start_timestamp){
            $err = langTranslate('tasting', 'err', 'Starting time is greater than ending time',  'Starting time is greater than ending time');
            return false;
        }
        $desc = trim($desc);
        if($desc!=$row['t_desc']){
            if(mb_strlen($desc,'UTF-8')>60000){
                $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                        langTranslate('tasting', 'tasting', 'Description', 'Description'),
                        60000);
                return false;
            }
            $update_arr[] = 't_desc = \''.$this->XM->sqlcore->prepString($desc,60000).'\'';
        }
        $participation = (int)$participation;
        if(!in_array($participation, array(0,1,2))){
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'Participation', 'Participation'));
            return false;
        }
        if($participation!=(int)$row['t_participation_type']){
            $update_arr[] = 't_participation_type = '.$participation;
        }
        if(!$participation){
            $participation_rating = 0;
        }
        $participation_rating = (int)$participation_rating;
        if($participation_rating<0 || $participation_rating>65000){
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'Rating Limitation', 'Rating Limitation'));
            return false;
        }
        if($participation_rating!=(int)$row['t_participation_rating_limit']){
            $update_arr[] = 't_participation_rating_limit = '.$participation_rating;
        }

        $chargeability = $chargeability?1:0;
        if($chargeability!=(int)$row['t_chargeability']){
            $update_arr[] = 't_chargeability = '.$chargeability;
        }
        if($chargeability==0){
            if($row['t_pricegrid_guest']!==null){
                $update_arr[] = 't_pricegrid_guest = null';
            }
            if($row['t_pricegrid_expert']!==null){
                $update_arr[] = 't_pricegrid_expert = null';
            }
            if($row['t_pricegrid_rated_expert']!==null){
                $update_arr[] = 't_pricegrid_rated_expert = null';
            }
            if($row['t_pricegrid_rated_expert_rating']!==null){
                $update_arr[] = 't_pricegrid_rated_expert_rating = null';
            }
        } else {
            $guest_price = isset($price_grid['guest_price'])?(int)$price_grid['guest_price']:0;
            if($guest_price<0 || $guest_price>=1000000){
                $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'Price grid', 'Price grid').': '.langTranslate('tasting', 'tasting', 'Guest', 'Guest'));
                return false;
            }
            if($guest_price!=(int)$row['t_pricegrid_guest']){
                $update_arr[] = 't_pricegrid_guest = '.$guest_price;
            }

            $expert_price = isset($price_grid['expert_price'])?(int)$price_grid['expert_price']:0;
            if($expert_price<0 || $expert_price>=1000000){
                $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'Price grid', 'Price grid').': '.langTranslate('tasting', 'tasting', 'Expert', 'Expert'));
                return false;
            }
            if($expert_price!=(int)$row['t_pricegrid_expert']){
                $update_arr[] = 't_pricegrid_expert = '.$expert_price;
            }

            $rated_expert_rating = isset($price_grid['rated_expert_rating'])?(int)$price_grid['rated_expert_rating']:0;
            if($rated_expert_rating<0 || $rated_expert_rating>=65000){
                $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'Price grid', 'Price grid').': '.langTranslate('tasting', 'tasting', 'Rating Limitation', 'Rating Limitation'));
                return false;
            }
            if($rated_expert_rating!=(int)$row['t_pricegrid_rated_expert_rating']){
                $update_arr[] = 't_pricegrid_rated_expert_rating = '.$rated_expert_rating;
            }

            $rated_expert_price = isset($price_grid['rated_expert_price'])?(int)$price_grid['rated_expert_price']:0;
            if($rated_expert_price<0 || $rated_expert_price>=1000000){
                $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting', 'tasting', 'Price grid', 'Price grid').': '.langTranslate('tasting', 'tasting', 'Rating Limited Expert', 'Rating Limited Expert'));
                return false;
            }
            if($rated_expert_price!=(int)$row['t_pricegrid_rated_expert']){
                $update_arr[] = 't_pricegrid_rated_expert = '.$rated_expert_price;
            }
        }
        $assessment = $assessment?1:0;
        if($assessment!=(int)$row['t_assessment']){
            $update_arr[] = 't_assessment = '.$assessment;
        }

        $score_method = (int)$score_method;
        if($score_method!=(int)$row['t_score_method']){
            if(!in_array($score_method, array(0,1))){
                $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                       langTranslate('tasting', 'tasting', 'Score method', 'Score method'));
                return false;
            }
            $res = $this->XM->sqlcore->query('SELECT 1 from product_vintage_review where t_id = '.$tasting_id.' limit 1');
            $row_check = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if($row_check){
                $err = formatReplace(langTranslate('tasting', 'err', 'You can\'t modify field @1 after a review has been submitted',  'You can\'t modify field @1 after a review has been submitted'),
                       langTranslate('tasting', 'tasting', 'Score method', 'Score method'));
                return false;
            }
        
            $update_arr[] = 't_score_method = '.$score_method;
        }
        

        if(!empty($update_arr)){
            $this->XM->sqlcore->query('UPDATE tasting SET '.implode(',',$update_arr).' where t_id = '.$tasting_id);
            $this->XM->sqlcore->commit();    
        }
        return true;
    }
    
    public function modifyindex_tasting_product_vintage($tpv_id,$direction,&$err){
        $tpv_id = (int)$tpv_id;
        $direction = ($direction==1)?1:-1;

        $res = $this->XM->sqlcore->query('SELECT tasting.t_status,tasting.user_id, tasting.t_id, tasting_product_vintage.tpv_index from tasting inner join tasting_product_vintage on tasting_product_vintage.t_id = tasting.t_id and tasting_product_vintage.tpv_id = '.$tpv_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//something doesn't exist
            return true;
        }
        $status = (int)$row['t_status'];

        $tasting_id = (int)$row['t_id'];
        $index = (int)$row['tpv_index'];

        $has_edit_rights = $this->XM->user->getUserId()==(int)$row['user_id']||$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_TASTINGS);
        if(!$has_edit_rights){
            $err = langTranslate('tasting','err','Access Denied','Access Denied');
            return false;
        }
        if($status!=\TASTING\TASTING_STATUS_DRAFT&&$status!=\TASTING\TASTING_STATUS_PREPARATION&&$status!=\TASTING\TASTING_STATUS_STARTED){
            $err = langTranslate('tasting','err','You can only edit product list in draft, preparation or started stages','You can only edit product list in draft, preparation or started stages');
            return false;
        }
        if($direction==1){
            $res = $this->XM->sqlcore->query('SELECT tasting_product_vintage.tpv_id,tasting_product_vintage.tpv_index 
                from tasting_product_vintage 
                where tasting_product_vintage.t_id = '.$tasting_id.' and tasting_product_vintage.tpv_index > '.$index.'
                order by tasting_product_vintage.tpv_index asc
                limit 1');
            
        } else {
            $res = $this->XM->sqlcore->query('SELECT tasting_product_vintage.tpv_id,tasting_product_vintage.tpv_index 
                from tasting_product_vintage 
                where tasting_product_vintage.t_id = '.$tasting_id.' and tasting_product_vintage.tpv_index < '.$index.'
                order by tasting_product_vintage.tpv_index desc
                limit 1');
        }
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//nothing to swap with
            return true;
        }
        $swap_tpv_id = (int)$row['tpv_id'];
        $swap_tpv_index = (int)$row['tpv_index'];

        $this->XM->sqlcore->query('UPDATE tasting_product_vintage set tpv_index = '.$swap_tpv_index.' where tpv_id = '.$tpv_id);
        $this->XM->sqlcore->query('UPDATE tasting_product_vintage set tpv_index = '.$index.' where tpv_id = '.$swap_tpv_id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function __fill_pvr_block_for_tpv_id($tpv_id){
        $tpv_id = (int)$tpv_id;
        $this->XM->product->load();
        $res = $this->XM->sqlcore->query('SELECT count(1) as cnt from product_vintage_review where pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE).' = 0 and tpv_id = '.$tpv_id);
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if($row['cnt']==1){
            $this->XM->sqlcore->query('UPDATE product_vintage_review set pvr_block = pvr_block|'.\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW.' where tpv_id  = '.$tpv_id.' and pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE).' = 0');
            $this->XM->sqlcore->commit();
        }


        // test it!!!
        // $res = $this->XM->sqlcore->query('SELECT pvr_id,pvr_score from product_vintage_review WHERE pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE).' = 0 and tpv_id = '.$tpv_id);
        // $reviews = array();
        // $sumscore = 0;
        // $count = 0;
        // $maxdelta = \PRODUCT\SCORE_NOT_ACCURATE_DELTA*100;
        // while($row = $this->XM->sqlcore->getRow($res)){
        //     $score = (int)$row['pvr_score'];
        //     $reviews[(int)$row['pvr_id']] = $score;
        //     $count++;
        //     $sumscore += $score;
        // }
        // $this->XM->sqlcore->freeResult($res);
        // $not_accurate_pvr_ids = array();
        // while(true){
        //     $deltascore = (float)$sumscore/$count;//test it
        //     $target_pvr_id = null;
        //     foreach($reviews as $pvr_id=>$score){
        //         if(abs($score-$deltascore)<=$maxdelta){
        //             continue;
        //         }
        //         if($target_pvr_id === null || abs($score-$deltascore) > abs($reviews[$target_pvr_id]-$deltascore)){
        //             $target_pvr_id = $pvr_id;
        //         }
        //     }
        //     if($target_pvr_id===null){
        //         break;
        //     }
        //     $not_accurate_pvr_ids[] = $target_pvr_id;
        //     $sumscore-=$reviews[$target_pvr_id];
        //     $count--;
        //     unset($reviews[$target_pvr_id]);
        // }
        // if(!empty($not_accurate_pvr_ids)){
        //     $not_accurate_pvr_id_chunks = array_chunk($not_accurate_pvr_ids, 50);
        //     foreach($not_accurate_pvr_id_chunks as $not_accurate_pvr_id_chunk){
        //         $this->XM->sqlcore->query('UPDATE product_vintage_review set pvr_block = pvr_block|'.\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE.' where pvr_id in ('.implode(',', $not_accurate_pvr_id_chunk).')');
        //         $this->XM->sqlcore->commit();
        //     }
        // }

        




    }
    public function change_review_status_tasting_product_vintage($tpv_id,$status,&$err){
        $tpv_id = (int)$tpv_id;
        $status = (int)$status;
        if($status!==1 && $status!==2){
            $err = langTranslate('tasting', 'err', 'Invalid status',  'Invalid status');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT tasting.t_id,tasting.t_status,tasting.t_score_method,tasting.user_id,tasting_product_vintage.tpv_review_request_status,tasting_product_vintage.pv_id,tasting.t_evaluation_automatic
            from tasting_product_vintage 
            inner join tasting on tasting.t_id = tasting_product_vintage.t_id 
            where tasting_product_vintage.tpv_id = '.$tpv_id.'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//something doesn't exist
            $err = langTranslate('tasting', 'err', 'Vintage doesn\'t exist',  'Vintage doesn\'t exist');
            return false;
        }
        $has_edit_rights = $this->XM->user->getUserId()==(int)$row['user_id']||$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_TASTINGS);
        if(!$has_edit_rights){
            $err = langTranslate('tasting','err','Access Denied','Access Denied');
            return false;
        }
        if((int)$row['t_status']!=\TASTING\TASTING_STATUS_STARTED){
            $err = langTranslate('tasting','err','You can only request reviews during tasting started stage','You can only request reviews during tasting started stage');
            return false;
        }
        if((int)$row['t_score_method']!=0){
            $err = langTranslate('tasting','err','You can only request reviews during tasting with review collection score method','You can only request reviews during tasting with review collection score method');
            return false;
        }

        $review_request_status = (int)$row['tpv_review_request_status'];
        $tasting_id = (int)$row['t_id'];
        $vintage_id = (int)$row['pv_id'];
        $evaluation_automatic = (bool)$row['t_evaluation_automatic'];

        if($status==1 && $review_request_status != 0){
            $err = langTranslate('tasting','err','Reviews already have been requested','Reviews already have been requested');
            return false;
        }
        if($status==2 && $review_request_status != 1){
            $err = langTranslate('tasting','err','You can only stop reviews after they have been requested','You can only stop reviews after they have been requested');
            return false;
        }
        $this->XM->sqlcore->query('UPDATE tasting_product_vintage set tpv_review_request_status = '.$status.' where tpv_id = '.$tpv_id);
        $this->XM->sqlcore->commit();
        if($status==1){
            $this->__send_review_request_mails($tasting_id);
        }
        if($status==2){
            if((int)$row['t_score_method']==0){//excessive
                $this->__fill_pvr_block_for_tpv_id($tpv_id);
                if($evaluation_automatic){
                    $this->__generate_automatic_evaluation($tpv_id,false);
                }
            }
            
        }
        return true;
    }
    public function remove_tasting_product_vintage($tpv_id,&$err){
        $tpv_id = (int)$tpv_id;

        $res = $this->XM->sqlcore->query('SELECT tasting.t_status,tasting.user_id, tasting.t_id, tasting_product_vintage.tpv_index,product_vintage_review.tpv_id 
            from tasting 
            inner join tasting_product_vintage on tasting_product_vintage.t_id = tasting.t_id and tasting_product_vintage.tpv_id = '.$tpv_id.'
            left join product_vintage_review on product_vintage_review.tpv_id = tasting_product_vintage.tpv_id
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//something doesn't exist
            return true;
        }
        $status = (int)$row['t_status'];

        $tasting_id = (int)$row['t_id'];
        $index = (int)$row['tpv_index'];
        if($row['tpv_id']){
            $err = langTranslate('tasting','err','You can\'t remove product that has been reviewed already','You can\'t remove product that has been reviewed already');
            return false;
        }
        $has_edit_rights = $this->XM->user->getUserId()==(int)$row['user_id']||$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_TASTINGS);
        if(!$has_edit_rights){
            $err = langTranslate('tasting','err','Access Denied','Access Denied');
            return false;
        }
        if($status!=\TASTING\TASTING_STATUS_DRAFT&&$status!=\TASTING\TASTING_STATUS_PREPARATION&&$status!=\TASTING\TASTING_STATUS_STARTED){
            $err = langTranslate('tasting','err','You can only edit product list in draft, preparation or started stages','You can only edit product list in draft, preparation or started stages');
            return false;
        }
        $this->XM->sqlcore->query('DELETE FROM tasting_product_vintage where tpv_id = '.$tpv_id);
        $this->XM->sqlcore->query('UPDATE tasting_product_vintage set tpv_index = tpv_index - 1 where t_id = '.$tasting_id.' and tpv_index > '.$index);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function get_tasting_status($tasting_id){
        $res = $this->XM->sqlcore->query('SELECT t_status from tasting where t_id = '.((int)$tasting_id).' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        return (int)$row['t_status'];
    }
    public function get_tasting_product_vintage_edit_info($tpv_id,&$err){
        $tpv_id = (int)$tpv_id;
        $res = $this->XM->sqlcore->query('SELECT tasting.user_id,tasting.t_id,tasting_product_vintage.pv_id,tasting_product_vintage.tpv_primeur,tasting_product_vintage.tpv_lot,tasting_product_vintage.tpv_volume,tasting_product_vintage.tpv_blind,tasting_product_vintage.tpv_blindname,tasting_product_vintage.tpv_desc from tasting_product_vintage inner join tasting on tasting.t_id = tasting_product_vintage.t_id where tpv_id = '.$tpv_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Vintage doesn\'t exist',  'Vintage doesn\'t exist');
            return false;
        }
        if($this->XM->user->getUserId()!=(int)$row['user_id']&&!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_TASTINGS)){
            $err = langTranslate('tasting','err','Access Denied','Access Denied');
            return false;
        }
        return array(
                't_id'=>(int)$row['t_id'],
                'pv_id'=>(int)$row['pv_id'],
                'isprimeur'=>$row['tpv_primeur']?1:0,
                'lot'=>$row['tpv_lot'],
                'volume'=>(int)$row['tpv_volume'],
                'isblind'=>$row['tpv_blind']?1:0,
                'blindname'=>$row['tpv_blindname'],
                'desc'=>$row['tpv_desc'],
            );
    }
    public function add_tasting_product_vintage($tasting_id,$vintage_id,$isprimeur,$lot,$attributes,$blindname,$description,$nominate,$personal,&$err){
        $tasting_id = (int)$tasting_id;
        $vintage_id = (int)$vintage_id;
        $description = trim((string)$description);
        if(mb_strlen($description,'UTF-8')>60000){
            $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('product', 'vintage', 'Description', 'Description'),
                    60000);
            return false;
        }
        $nominate = $nominate?1:0;

        $isprimeur = $isprimeur?1:0;
        if($isprimeur){
            $lot = null;
        } else {
            $lot = trim((string)$lot);
        }
        if(mb_strlen($lot,'UTF-8')>6){
            $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('tasting', 'vintage', 'Lot', 'Lot'),
                    6);
            return false;
        }

        if($blindname!==null){
            if(!strlen($blindname)){
                $err = formatReplace(langTranslate('tasting', 'err', 'Field @1 is empty',  'Field @1 is empty'),
                            langTranslate('tasting','tasting','Blind name','Blind name'));
                return false;
            }
            if(mb_strlen($blindname, 'UTF-8')>128){
                $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('tasting','tasting','Blind name','Blind name'),
                    128);
                return false;
            }
        }

        $personal = (bool)$personal;

        $attributes = $this->XM->product->clean_attributes($attributes,false);
        if(empty($attributes)){
            $err = langTranslate('tasting', 'err', 'Fill all fields',  'Fill all fields');
            return false;
        }
        $attribute_chunks = array_chunk($attributes, 50);
        $volume_pav_id = null;
        foreach($attribute_chunks as $attribute_chunk){
            $res = $this->XM->sqlcore->query('SELECT product_attribute_value.pav_id from product_attribute_value inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pag_id = 16 where product_attribute_value.pav_id in ('.implode(',', $attribute_chunk).') limit 1');//16 = volume
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if($row){
                $volume_pav_id = (int)$row['pav_id'];
                break;
            }
        }

        if(!$this->XM->product->check_vintage_exists($vintage_id)){
            $err = langTranslate('tasting', 'err', 'Vintage doesn\'t exist',  'Vintage doesn\'t exist');
            return false;
        }
        $index = null;
        if(!$personal){
            if(($status = $this->get_tasting_status($tasting_id))===FALSE){
                $err = langTranslate('tasting', 'err', 'Tasting doesn\'t exist',  'Tasting doesn\'t exist');
                return false;
            }
            if($status!==\TASTING\TASTING_STATUS_DRAFT && $status!==\TASTING\TASTING_STATUS_PREPARATION && $status!==\TASTING\TASTING_STATUS_STARTED){
                $err = langTranslate('tasting','err','You can only edit product list in draft, preparation or started stages','You can only edit product list in draft, preparation or started stages');
                return false;
            }
            if($status!==\TASTING\TASTING_STATUS_DRAFT){
                $res = $this->XM->sqlcore->query('SELECT 1 
                    from product 
                    inner join product_vintage on product_vintage.p_id = product.p_id
                    where product_vintage.pv_id = '.$vintage_id.' and product.p_is_approved = 0 
                    limit 1');
                $row = $this->XM->sqlcore->getRow($res);
                $this->XM->sqlcore->freeResult($res);
                if($row){//adding not yet approved product
                    $err = langTranslate('tasting', 'err', 'You can only add not yet approved products during draft stage', 'You can only add not yet approved products during draft stage');
                    return false;
                }
            }
            $res = $this->XM->sqlcore->query('select coalesce(max(tpv_index)+1,1) as tpv_index from tasting_product_vintage where t_id = '.$tasting_id);
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            $index = (int)$row['tpv_index'];
        }
        

        $sql_description = strlen($description)?'\''.$this->XM->sqlcore->prepString($description,60000).'\'':'null';
        $tpv_id = null;
        if(!$personal){
            $this->XM->sqlcore->query('INSERT INTO tasting_product_vintage (t_id,pv_id,tpv_index,tpv_primeur,tpv_lot,tpv_volume,tpv_desc,tpv_blind,tpv_blindname) values ('.$tasting_id.','.$vintage_id.','.$index.','.$isprimeur.','.(($lot!==null)?'\''.$this->XM->sqlcore->prepString($lot,6).'\'':'null').','.$volume_pav_id.','.$sql_description.','.(($blindname!==null)?1:0).','.(($blindname!==null)?'\''.$this->XM->sqlcore->prepString($blindname,128).'\'':'null').')');
            $tpv_id = $this->XM->sqlcore->lastInsertId();
        } else {
            $res = $this->XM->sqlcore->query('SELECT tpv_id from tasting_product_vintage where pv_id = '.$vintage_id.' and tpv_personal = 1 and tpv_primeur = '.$isprimeur.' and tpv_lot '.(($lot!==null)?'= \''.$this->XM->sqlcore->prepString($lot,6).'\'':'is null').' and tpv_volume = '.$volume_pav_id.' and tpv_desc '.($sql_description!='null'?'= '.$sql_description:'is null').' limit 1');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if($row){
                $tpv_id = (int)$row['tpv_id'];
            } else {
                $this->XM->sqlcore->query('INSERT INTO tasting_product_vintage (t_id,pv_id,tpv_primeur,tpv_lot,tpv_volume,tpv_desc,tpv_personal) values (0,'.$vintage_id.','.$isprimeur.','.(($lot!==null)?'\''.$this->XM->sqlcore->prepString($lot,6).'\'':'null').','.$volume_pav_id.','.$sql_description.',1)');
                $tpv_id = $this->XM->sqlcore->lastInsertId();
            }
        }
        $this->XM->sqlcore->commit();
        if(strlen($description)&&$nominate){
            $this->XM->sqlcore->query('INSERT INTO product_vintage_ml (pv_ml_desc,pv_id,lang_id) VALUES (\''.$this->XM->sqlcore->prepString($description,60000).'\','.$vintage_id.','.$this->XM->lang->getCurrLangId().')');
            if($this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
                $dummy = null;
                $pv_ml_id = $this->XM->sqlcore->lastInsertId();
                $this->XM->product->approve_vintage_translation($pv_ml_id,true,$dummy);
            }
            $this->XM->sqlcore->commit();
        }
        //company favourite
        if(!$personal){
            $dummy = null;
            $this->XM->product->company_favourite_product_by_vintage($vintage_id,true,true,$dummy);    
        }
        
        return $tpv_id;
    }

    public function edit_tasting_product_vintage($tpv_id,$isprimeur,$lot,$attributes,$blindname,$description,$nominate,&$err){
        $tpv_id = (int)$tpv_id;
        $description = trim((string)$description);
        if(mb_strlen($description,'UTF-8')>60000){
            $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('product', 'vintage', 'Description', 'Description'),
                    60000);
            return false;
        }
        $nominate = $nominate?1:0;

        $isprimeur = $isprimeur?1:0;
        if($isprimeur){
            $lot = null;
        } else {
            $lot = trim((string)$lot);
        }
        if(mb_strlen($lot,'UTF-8')>6){
            $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('tasting', 'vintage', 'Lot', 'Lot'),
                    6);
            return false;
        }

        if($blindname!==null){
            if(!strlen($blindname)){
                $err = formatReplace(langTranslate('tasting', 'err', 'Field @1 is empty',  'Field @1 is empty'),
                            langTranslate('tasting','tasting','Blind name','Blind name'));
                return false;
            }
            if(mb_strlen($blindname, 'UTF-8')>128){
                $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('tasting','tasting','Blind name','Blind name'),
                    128);
                return false;
            }
        }

        $attributes = $this->XM->product->clean_attributes($attributes,false);
        if(empty($attributes)){
            $err = langTranslate('tasting', 'err', 'Fill all fields',  'Fill all fields');
            return false;
        }
        $attribute_chunks = array_chunk($attributes, 50);
        $volume_pav_id = null;
        foreach($attribute_chunks as $attribute_chunk){
            $res = $this->XM->sqlcore->query('SELECT product_attribute_value.pav_id from product_attribute_value inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pag_id = 16 where product_attribute_value.pav_id in ('.implode(',', $attribute_chunk).') limit 1');//16 = volume
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if($row){
                $volume_pav_id = (int)$row['pav_id'];
                break;
            }
        }

        $res = $this->XM->sqlcore->query('SELECT tasting.t_status,tasting_product_vintage.tpv_review_request_status,tasting_product_vintage.tpv_primeur,tasting_product_vintage.tpv_lot,tasting_product_vintage.tpv_volume,tasting_product_vintage.tpv_blind,tasting_product_vintage.tpv_blindname,tasting_product_vintage.tpv_desc from tasting_product_vintage inner join tasting on tasting.t_id = tasting_product_vintage.t_id where tasting_product_vintage.tpv_id = '.$tpv_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Vintage doesn\'t exist',  'Vintage doesn\'t exist');
            return false;
        }
        $status = (int)$row['t_status'];

        if($status!==\TASTING\TASTING_STATUS_DRAFT && $status!==\TASTING\TASTING_STATUS_PREPARATION && $status!==\TASTING\TASTING_STATUS_STARTED){
            $err = langTranslate('tasting','err','You can only edit product list in draft, preparation or started stages','You can only edit product list in draft, preparation or started stages');
            return false;
        }
        if((int)$row['tpv_review_request_status']!=0){
            $err = langTranslate('tasting','err','You can\'t edit products that are being reviewed already','You can\'t edit products that are being reviewed already');
            return false;
        }
        $update_arr = array();
        $desc_changed = (string)$description!=(string)$row['tpv_desc'];
        if($desc_changed){
            $update_arr[] = 'tpv_desc = '.(strlen($description)?'\''.$this->XM->sqlcore->prepString($description,60000).'\'':'null');
        }
        $blind = ($blindname!==null)?1:0;
        if($blind!=(int)$row['tpv_blind']){
            $update_arr[] = 'tpv_blind = '.$blind;
        }
        if((string)$blindname!=(string)$row['tpv_blindname']){
            $update_arr[] = 'tpv_blindname = '.(($blindname!==null)?'\''.$this->XM->sqlcore->prepString($blindname,128).'\'':'null');
        }
        if($isprimeur!=(int)$row['tpv_primeur']){
            $update_arr[] = 'tpv_primeur = '.$isprimeur;
        }
        if((string)$lot!=(string)$row['tpv_lot']){
            $update_arr[] = 'tpv_lot = '.(($lot!==null)?'\''.$this->XM->sqlcore->prepString($lot,6).'\'':'null');
        }
        if($volume_pav_id != (int)$row['tpv_volume']){
            $update_arr[] = 'tpv_volume = '.$volume_pav_id;
        }

        if($update_arr){
            $this->XM->sqlcore->query('UPDATE tasting_product_vintage SET '.implode(',', $update_arr).' where tpv_id = '.$tpv_id);
            $this->XM->sqlcore->commit();
        }
        if($desc_changed&&strlen($description)&&$nominate){
            $this->XM->sqlcore->query('INSERT INTO product_vintage_ml (pv_ml_desc,pv_id,lang_id) VALUES (\''.$this->XM->sqlcore->prepString($description,60000).'\','.$vintage_id.','.$this->XM->lang->getCurrLangId().')');
            if($this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
                $dummy = null;
                $pv_ml_id = $this->XM->sqlcore->lastInsertId();
                $this->XM->product->approve_vintage_translation($pv_ml_id,true,$dummy);
            }
            $this->XM->sqlcore->commit();
        }
        return true;
    }

    public function invite_tasting_user($tasting_id,$user_id,&$err){
        $tasting_id = (int)$tasting_id;
        $user_id = (int)$user_id;
        if(($status = $this->get_tasting_status($tasting_id))===FALSE){
            $err = langTranslate('tasting', 'err', 'Tasting doesn\'t exist',  'Tasting doesn\'t exist');
            return false;
        }
        if($status!==\TASTING\TASTING_STATUS_DRAFT && $status!==\TASTING\TASTING_STATUS_PREPARATION && $status!==\TASTING\TASTING_STATUS_STARTED){
            $err = langTranslate('tasting','err','You can only edit user invite list in draft, preparation or started stages','You can only edit user invite list in draft, preparation or started stages');
            return false;
        }
        if(!$this->XM->user->check_user_exists($user_id)){
            $err = langTranslate('tasting', 'err', 'User doesn\'t exist',  'User doesn\'t exist');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT 1 from tasting_user where t_id = '.$tasting_id.' and user_id = '.$user_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if($row){
            return true;//already exists
        }
        $response = $user_id==$this->XM->user->getUserId()?\TASTING\TASTING_USER_RESPONSE_ACCEPT:\TASTING\TASTING_USER_RESPONSE_PENDING;
        $this->XM->sqlcore->query('INSERT INTO tasting_user (t_id,user_id,tu_response) values ('.$tasting_id.','.$user_id.','.$response.')');
        $this->XM->sqlcore->commit();
        if($status===\TASTING\TASTING_STATUS_PREPARATION && $user_id!=$this->XM->user->getUserId()){
            $this->__send_tasting_invite_mail($tasting_id,$user_id);    
        }
        return true;
    }
    public function external_tasting_user_respond($tasting_id,$tu_id,$usercode,$response,&$err){
        $tasting_id = (int)$tasting_id;
        $tu_id = (int)$tu_id;
        $usercode = (int)$usercode;
        $response = (int)$response;

        $user_response_list = $this->get_user_response_list(false);
        if(!array_key_exists($response, $user_response_list)){
            $err = langTranslate('tasting', 'err', 'Invalid response',  'Invalid response');
            return false;
        }
        unset($user_response_list);
        if(($status = $this->get_tasting_status($tasting_id))===FALSE){
            $err = langTranslate('tasting', 'err', 'Tasting doesn\'t exist',  'Tasting doesn\'t exist');
            return false;
        }
        if($status!==\TASTING\TASTING_STATUS_DRAFT && $status!==\TASTING\TASTING_STATUS_PREPARATION){
            $err = langTranslate('tasting','err','You can only respond to an invite in draft or preparation stages','You can only respond to an invite in draft or preparation stages');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT user_id
            from tasting_user
            where tu_id = '.$tu_id.' and t_id = '.$tasting_id.'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//user not invited
            $err = langTranslate('tasting', 'err', 'You weren\'t invited to this tasting',  'You weren\'t invited to this tasting');
            return false;
        }
        $user_id = (int)$row['user_id'];
        if($this->XM->sqlcore->checksum(md5($tu_id.$tasting_id.$user_id))==$usercode){
            $this->XM->sqlcore->query('UPDATE tasting_user set tu_response = '.$response.' where tu_id = '.$tu_id);
            $this->XM->sqlcore->commit();
            return true;
        }
        $err = langTranslate('tasting', 'err', 'Invalid usercode',  'Invalid usercode');
        return false;
    }
    public function tasting_user_respond($tasting_id,$response,&$err){
        $tasting_id = (int)$tasting_id;
        $response = (int)$response;
        $user_response_list = $this->get_user_response_list(false);
        if(!array_key_exists($response, $user_response_list)){
            $err = langTranslate('tasting', 'err', 'Invalid response',  'Invalid response');
            return false;
        }
        unset($user_response_list);
        if(($status = $this->get_tasting_status($tasting_id))===FALSE){
            $err = langTranslate('tasting', 'err', 'Tasting doesn\'t exist',  'Tasting doesn\'t exist');
            return false;
        }
        if($status!==\TASTING\TASTING_STATUS_DRAFT && $status!==\TASTING\TASTING_STATUS_PREPARATION){
            $err = langTranslate('tasting','err','You can only respond to an invite in draft or preparation stages','You can only respond to an invite in draft or preparation stages');
            return false;
        }
        if($this->XM->user->check_state(\USER\STATE_IS_APPROVED_EXPERT)){
            $where_sql = '( tasting.user_id = '.$this->XM->user->getUserId().' or ( tasting.t_status = '.\TASTING\TASTING_STATUS_PREPARATION.' and ( ( tasting.t_participation_type = 2 or tasting.t_participation_type = 1 and tasting.t_participation_rating_limit <= '.$this->XM->user->getExpertRating().' ) and tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.' or tasting_user.user_id is not null ) ) )';
        } else {
            $where_sql = '( tasting.user_id = '.$this->XM->user->getUserId().' or ( tasting.t_status = '.\TASTING\TASTING_STATUS_PREPARATION.' and ( tasting.t_participation_type = 2 and tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.' or tasting_user.user_id is not null ) ) )';
        }
        $res = $this->XM->sqlcore->query('SELECT tasting_user.tu_id
            from tasting
            left join tasting_user on tasting_user.t_id = tasting.t_id and tasting_user.user_id = '.$this->XM->user->getUserId().'
            where tasting.t_id = '.$tasting_id.' and '.$where_sql.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//user not invited and tasting is not public
            $err = langTranslate('tasting', 'err', 'You weren\'t invited to this tasting',  'You weren\'t invited to this tasting');
            return false;
        }
        if($row['tu_id']){
            $this->XM->sqlcore->query('UPDATE tasting_user set tu_response = '.$response.' where tu_id = '.(int)$row['tu_id']);
            $this->XM->sqlcore->commit();
            return true;
        } else {
            $this->XM->sqlcore->query('INSERT INTO tasting_user (t_id,user_id,tu_response) values ('.$tasting_id.','.$this->XM->user->getUserId().','.$response.')');
            $this->XM->sqlcore->commit();
            return true;
        }
        //never
        return false;
    }
    public function mark_presence_tasting_user($tasting_id,$user_id,$presence,&$err){
        $tasting_id = (int)$tasting_id;
        $user_id = (int)$user_id;
        $presence = $presence?1:0;
        $res = $this->XM->sqlcore->query('SELECT tasting.t_status,tasting_user.tu_presence
            from tasting
            inner join tasting_user on tasting_user.t_id = tasting.t_id
            where tasting.t_id = '.$tasting_id.' and tasting_user.user_id = '.$user_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//tasting doesn't exist or user wasn't invited
            $err = langTranslate('tasting','err','Internal Error','Internal Error');
            return false;
        }
        if((int)$row['t_status']!=\TASTING\TASTING_STATUS_STARTED){
            $err = langTranslate('tasting','err','You can only mark invitee presence in ongoing tastings','You can only mark invitee presence in ongoing tastings');
            return false;
        }
        if((int)$row['tu_presence']==$presence){
            return true;//nothing to update
        }
        $this->XM->sqlcore->query('UPDATE tasting_user set tu_presence = '.$presence.' where t_id = '.$tasting_id.' and user_id = '.$user_id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function remove_tasting_user($tasting_id,$user_id,&$err){
        $tasting_id = (int)$tasting_id;
        $user_id = (int)$user_id;
        $res = $this->XM->sqlcore->query('SELECT tasting.t_status,tasting.user_id as owner,tasting_user.tu_response,product_vintage_review.pvr_id 
            from tasting 
            inner join tasting_user on tasting_user.t_id = tasting.t_id and tasting_user.user_id = '.$user_id.'
            left join product_vintage_review on product_vintage_review.user_id = tasting_user.user_id and product_vintage_review.t_id = tasting.t_id
            where tasting.t_id = '.$tasting_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//something doesn't exist
            return true;
        }
        if($row['pvr_id']){//already filled at least one review
            $err = langTranslate('tasting','err','You can\'t remove a user that has filed a review already','You can\'t remove a user that has filed a review already');
            return false;
        }
        $has_edit_rights = $this->XM->user->getUserId()==(int)$row['owner']||$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_TASTINGS);
        if(!$has_edit_rights){
            $err = langTranslate('tasting','err','Access Denied','Access Denied');
            return false;
        }
        $status = (int)$row['t_status'];
        if($status!=\TASTING\TASTING_STATUS_DRAFT&&$status!=\TASTING\TASTING_STATUS_PREPARATION&&$status!=\TASTING\TASTING_STATUS_STARTED){
            $err = langTranslate('tasting','err','You can only edit user invite list in draft, preparation or started stages','You can only edit user invite list in draft, preparation or started stages');
            return false;
        }
        $this->XM->sqlcore->query('DELETE FROM tasting_user where t_id = '.$tasting_id.' and user_id = '.$user_id);
        $this->XM->sqlcore->commit();
        if(in_array((int)$row['tu_response'],array(\TASTING\TASTING_USER_RESPONSE_ACCEPT,\TASTING\TASTING_USER_RESPONSE_UNCERTAIN)) && in_array($status, array(\TASTING\TASTING_STATUS_DRAFT,\TASTING\TASTING_STATUS_PREPARATION))){//don't send out if tasting is already started
            $this->__send_tasting_invite_revoked_mail($tasting_id,$user_id);
        }
        return true;
    }
    public function check_review_request($tpv_id, &$review_id){
        $tpv_id = (int)$tpv_id;
        $res = $this->XM->sqlcore->query('SELECT tasting_product_vintage.tpv_id, product_vintage_review.pvr_id
                from tasting_product_vintage 
                inner join product_vintage on product_vintage.pv_id = tasting_product_vintage.pv_id
                inner join tasting_user on tasting_user.t_id = tasting_product_vintage.t_id and tasting_user.user_id = '.$this->XM->user->getUserId().' and tasting_user.tu_presence = 1
                left join product_vintage_review on product_vintage_review.tpv_id = tasting_product_vintage.tpv_id and product_vintage_review.user_id = '.$this->XM->user->getUserId().'
                where tasting_product_vintage.tpv_id = '.$tpv_id.' and tasting_product_vintage.tpv_review_request_status = 1
                limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        if($row['pvr_id']){
            $review_id = (int)$row['pvr_id'];
        }
        return true;
    }
    public function filter_tasting($start_date_from, $start_date_to, $status, $only_owned, $only_approved, $only_for_assessment, $currently_participating, $only_pending_reviews, $only_took_part, $took_part_vintage_id, $can_add_to_contest, $used_in_contest, $global_expert_ratings_for_user, $show_attendance_response, $show_location, $order_by_field, $order_by_direction_asc, &$page, &$pagelimit, &$count, &$err){
        if(($page = (int)$page)<=0){
            $page = 1;
        }
        $pagelimit = (int)$pagelimit;
        if($pagelimit<=0 || $pagelimit>100){
            $pagelimit = 50;
        }
        $start_from_timestamp = $start_to_timestamp = null;
        if(preg_match('#^[0-3]?[0-9]\.[0-1][0-9]\.\d{4}$#', $start_date_from)){
            $start_from_timestamp = strtotime($start_date_from);
        }
        if(preg_match('#^[0-3]?[0-9]\.[0-1][0-9]\.\d{4}$#', $start_date_to)){
            $start_to_timestamp = strtotime($start_date_to)+86400;
        }
        $statuslist = array();
        $allstatuslist = $this->get_status_list();
        if(is_array($status)&&!empty($status)){
            foreach($status as $key){
                $key = (int)$key;
                if(array_key_exists($key, $allstatuslist)){
                    $statuslist[] = $key;
                }
            }
            if(count($statuslist)==count($allstatuslist)){//find all anyway
                $statuslist = array();
            }
        }
        
        $only_owned = (bool)$only_owned;
        
        //prepare params
        $pending_reviews_inner_join = '';
        $pending_reviews_select = 'null as pending_review_count';
        if($only_pending_reviews){
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filterpendingreviews
                SELECT tasting.t_id, count(distinct tasting_product_vintage.tpv_id) as cnt
                    from tasting
                    inner join tasting_product_vintage on tasting_product_vintage.t_id = tasting.t_id
                    inner join tasting_user on tasting_user.t_id = tasting_product_vintage.t_id and tasting_user.user_id = '.$this->XM->user->getUserId().' and tasting_user.tu_presence = 1
                    left join product_vintage_review on product_vintage_review.tpv_id = tasting_product_vintage.tpv_id and product_vintage_review.user_id = '.$this->XM->user->getUserId().'
                    where product_vintage_review.tpv_id is null and tasting.t_status = '.\TASTING\TASTING_STATUS_STARTED.' and ( tasting_product_vintage.tpv_review_request_status = 1 or tasting.t_score_method = 1)
                    group by tasting_product_vintage.t_id');
            $pending_reviews_inner_join = 'inner join filterpendingreviews on filterpendingreviews.t_id = tasting.t_id';//can be used in both queries
            $pending_reviews_select = 'filterpendingreviews.cnt as pending_review_count';
        }
        $where_arr = array();
        $took_part_inner_join = '';
        if($only_took_part){
            $this->XM->product->load();
            $took_part_vintage_id = (int)$took_part_vintage_id;
            $took_part_inner_join = 'inner join (
                    select distinct t_id from product_vintage_review where user_id = '.$this->XM->user->getUserId().' and pvr_block&~'.(\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE).' = 0 '.($took_part_vintage_id?'and pv_id = '.$took_part_vintage_id:'').'
                    union distinct
                    select distinct tasting_product_vintage_ranking.t_id 
                        from tasting_product_vintage_ranking 
                        '.($took_part_vintage_id?'inner join tasting_product_vintage on tasting_product_vintage.tpv_id = tasting_product_vintage_ranking.tpv_id':'').'
                        where tasting_product_vintage_ranking.user_id = '.$this->XM->user->getUserId().' '.($took_part_vintage_id?'and tasting_product_vintage.pv_id = '.$took_part_vintage_id:'').'
                ) as took_part_inner_join on took_part_inner_join.t_id = tasting.t_id';
            $where_arr[] = 'tasting.t_status = '.\TASTING\TASTING_STATUS_FINISHED;
        }
        $attendance_response_select_sql = 'null as attendance_response_response';
        $attendance_response_left_join = '';
        if($show_attendance_response){
            $attendance_response_select_sql = 'attendance_response_tasting_user.tu_response as attendance_response_response';
            $attendance_response_left_join = 'left join tasting_user as attendance_response_tasting_user on attendance_response_tasting_user.user_id = '.$this->XM->user->getUserId().' and attendance_response_tasting_user.t_id = tasting.t_id';
        }
        $show_location_select = 'null as t_location';
        if($show_location){
            $show_location_select = 'tasting.t_location';
        }

        
        if($start_from_timestamp>0){
            if($start_to_timestamp>0){
                $where_arr[] = 'tasting.t_start_ts between '.$start_from_timestamp.' and '.$start_to_timestamp;
            } else {
                $where_arr[] = 'tasting.t_start_ts >= '.$start_from_timestamp;
            }
        } elseif($start_to_timestamp>0){
            $where_arr[] = 'tasting.t_start_ts <= '.$start_to_timestamp;
        }
        if(!empty($statuslist)){
            $where_arr[] = 'tasting.t_status in ('.implode(',', $statuslist).')';
        }
        $tasting_user_join = '';
        if($only_owned){
            $where_arr[] = 'tasting.user_id = '.$this->XM->user->getUserId();
        } else {
            if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_ALL_TASTINGS)){
                if($this->XM->user->check_state(\USER\STATE_IS_APPROVED_EXPERT)){
                    $where_arr[] = '( tasting.user_id = '.$this->XM->user->getUserId().' or tasting.t_is_approved = 1 or ( tasting.t_status <> '.\TASTING\TASTING_STATUS_DRAFT.' and ( ( tasting.t_participation_type = 2 or tasting.t_participation_type = 1 and tasting.t_participation_rating_limit <= '.$this->XM->user->getExpertRating().' ) and tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.' or tasting_user.user_id is not null and ( tasting_user.tu_presence = 1 or tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.' ) ) ) )';
                } else {
                    $where_arr[] = '( tasting.user_id = '.$this->XM->user->getUserId().' or tasting.t_is_approved = 1 or ( tasting.t_status <> '.\TASTING\TASTING_STATUS_DRAFT.' and ( tasting.t_participation_type = 2 and tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.' or tasting_user.user_id is not null and ( tasting_user.tu_presence = 1 or tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.' ) ) ) )';
                }
                $tasting_user_join = 'left join tasting_user on tasting_user.t_id = tasting.t_id and tasting_user.user_id = '.$this->XM->user->getUserId();
                //owned everything
                //public or invited to everything but drafts
            }
        }
        if($only_approved){
            $where_arr[] = 'tasting.t_is_approved = 1';
        }
        if($only_for_assessment){
            if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_APPROVE_TASTING)){
                return array();
            }
            $where_arr[] = 'tasting.t_is_approved is null and tasting.t_assessment = 1 and tasting.t_status = '.\TASTING\TASTING_STATUS_FINISHED;
        }
        if($currently_participating){
            $where_arr[] = 'tasting.t_status = '.\TASTING\TASTING_STATUS_STARTED.' and tasting_user.tu_presence = 1';
            $tasting_user_join = 'inner join tasting_user on tasting_user.t_id = tasting.t_id and tasting_user.user_id = '.$this->XM->user->getUserId();
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_DELETED_TASTINGS)){
            $where_arr[] = 'tasting.t_status <> '.\TASTING\TASTING_STATUS_DELETED;
        }
        $can_add_to_contest = (int)$can_add_to_contest;
        $can_add_to_contest_inner_join = '';
        if($can_add_to_contest){
            $can_add_to_contest_inner_join = 'inner join tasting_contest_user_access t_tcua on t_tcua.user_id = tasting.user_id and t_tcua.tc_id = '.$can_add_to_contest.'
                inner join tasting_contest on tasting_contest.tc_id = '.$can_add_to_contest.' and tasting_contest.tc_status in ('.\TASTING\CONTEST_STATUS_DRAFT.','.\TASTING\CONTEST_STATUS_PREPARATION.','.\TASTING\CONTEST_STATUS_SUMMING_UP.')';
            if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
                $where_arr[] = 'tasting.user_id = '.$this->XM->user->getUserId();
            }
            $where_arr[] = 'tasting.t_status <> '.\TASTING\TASTING_STATUS_DELETED;
            $where_arr[] = '( tasting_contest.tc_status <> '.CONTEST_STATUS_SUMMING_UP.' or tasting.t_status = '.\TASTING\TASTING_STATUS_FINISHED.' )';
        }
        $used_in_contest = (int)$used_in_contest;
        $used_in_contest_inner_join = '';
        if($used_in_contest){
            $used_in_contest_inner_join = 'inner join tasting_contest_tasting on tasting_contest_tasting.t_id = tasting.t_id and tasting_contest_tasting.tc_id = '.$used_in_contest;
        }
        $global_expert_ratings_for_user = (int)$global_expert_ratings_for_user;
        $global_expert_ratings_for_user_inner_join = '';
        $global_expert_ratings_for_user_select_sql = '';
        if($global_expert_ratings_for_user && $this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_EXPERT_EVALUATION_SCORE)){
            $global_expert_ratings_for_user_select_sql = 'tasting_user_global_evaluation_score.tuges_score,tasting_user_global_evaluation_score.tuges_place,tasting_user_global_evaluation_score.tuges_leniency,tasting_user_global_evaluation_score.tuges_zero';
            $global_expert_ratings_for_user_inner_join = 'inner join user as global_expert_ratings_for_user_user on global_expert_ratings_for_user_user.user_id = '.$global_expert_ratings_for_user.'
            inner join tasting_user_global_evaluation_score on tasting_user_global_evaluation_score.t_id = tasting.t_id and tasting_user_global_evaluation_score.user_id = global_expert_ratings_for_user_user.user_id and tasting_user_global_evaluation_score.user_expert_level = global_expert_ratings_for_user_user.user_expert_level';
        }
        $where_sql = '';
        if(!empty($where_arr)){
            $where_arr = array_unique($where_arr);
            $where_sql = 'where '.implode(' and ', $where_arr);
        }
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtertids
            SELECT distinct tasting.t_id
            from tasting 
            '.$took_part_inner_join.'
            '.$pending_reviews_inner_join.'
            '.$can_add_to_contest_inner_join.'
            '.$used_in_contest_inner_join.'
            '.$global_expert_ratings_for_user_inner_join.'
            '.$tasting_user_join.'
            '.$where_sql);
        $res = $this->XM->sqlcore->query('SELECT count(1) as cnt from filtertids');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        $count = (int)$row['cnt'];
        if($count==0){
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filtertids');
            return array();
        }
        if(($page-1)*$pagelimit>=$count){
            $page = ceil($count/$pagelimit);
        }
        $personal_price = '0 as personal_price';
        if($this->XM->user->check_state(\USER\STATE_IS_APPROVED_EXPERT)){
            $personal_price = 'if(tasting.t_chargeability=1,if(tasting.t_pricegrid_rated_expert_rating<='.$this->XM->user->getExpertRating().',tasting.t_pricegrid_rated_expert,tasting.t_pricegrid_expert),0) as personal_price';
        } else {
            $personal_price = 'if(tasting.t_chargeability=1,tasting.t_pricegrid_guest,0) as personal_price';
        }
        $order_by_sql = null;
        switch($order_by_field){
            case 'date':
                $order_by_sql = 'tasting.t_start_ts div 86400 '.($order_by_direction_asc?'asc':'desc').', tasting.t_name is null asc, tasting.t_name asc, tasting.t_id desc';
                break;
            case 'name':
                $order_by_sql = 'tasting.t_name is null '.($order_by_direction_asc?'asc':'desc').', tasting.t_name '.($order_by_direction_asc?'asc':'desc').', tasting.t_start_ts div 86400 desc, tasting.t_id desc';
                break;
            case 'status':
                $order_by_sql = 'tasting.t_status '.($order_by_direction_asc?'asc':'desc').', tasting.t_start_ts div 86400 desc, tasting.t_name is null asc, tasting.t_name asc, tasting.t_id desc';
                break;
            case 'global-expert-rating':
                if($global_expert_ratings_for_user_inner_join){
                    $order_by_sql = 'if(tasting_user_global_evaluation_score.tuges_zero=0,tasting_user_global_evaluation_score.tuges_score,0) '.($order_by_direction_asc?'asc':'desc').', tasting.t_start_ts div 86400 desc, tasting.t_name is null asc, tasting.t_name asc, tasting.t_id desc';
                } else {
                    $order_by_sql = 'tasting.t_start_ts div 86400 desc, tasting.t_name is null asc, tasting.t_name asc, tasting.t_id desc';    
                }
                break;
            default:
                if($global_expert_ratings_for_user_inner_join){
                    $order_by_sql = 'if(tasting_user_global_evaluation_score.tuges_zero=0,tasting_user_global_evaluation_score.tuges_score,0) desc, tasting.t_start_ts div 86400 desc, tasting.t_name is null asc, tasting.t_name asc, tasting.t_id desc';
                } else {
                    $order_by_sql = 'tasting.t_start_ts div 86400 desc, tasting.t_name is null asc, tasting.t_name asc, tasting.t_id desc';    
                }
                
        }

        $result = array();
        $res = $this->XM->sqlcore->query('SELECT tasting.t_id, tasting.t_start_ts, tasting.t_name, tasting.t_status, tasting.t_score_method, tasting.user_id, tasting.t_participation_type,'.$show_location_select.', '.$pending_reviews_select.', '.$personal_price.', '.$attendance_response_select_sql.($global_expert_ratings_for_user_select_sql?','.$global_expert_ratings_for_user_select_sql:'').'
            from filtertids
            inner join tasting on tasting.t_id = filtertids.t_id
            '.$pending_reviews_inner_join.'
            '.$global_expert_ratings_for_user_inner_join.'
            '.$attendance_response_left_join.'
            '.($order_by_sql?'order by '.$order_by_sql:'').'
            limit '.$pagelimit.' offset '.(($page-1)*$pagelimit));
        while($row = $this->XM->sqlcore->getRow($res)){
            $id = (int)$row['t_id'];
            $status = (int)$row['t_status'];
            $attendance_response_status = null;
            $attendance_response_status_text = null;
            if($this->XM->user->getUserId()==(int)$row['user_id']){
                $attendance_response_status = 'owned';
                $attendance_response_status_text = langTranslate('tasting','tasting','Owned tasting','Owned tasting');
            } else {
                if($row['attendance_response_response']!==null){
                    switch((int)$row['attendance_response_response']){
                        case \TASTING\TASTING_USER_RESPONSE_PENDING:
                            $attendance_response_status = 'pending';
                            $attendance_response_status_text = langTranslate('tasting','userresponse','Pending','Pending');
                            break;
                        case \TASTING\TASTING_USER_RESPONSE_ACCEPT:
                            $attendance_response_status = 'accepted';
                            $attendance_response_status_text = langTranslate('tasting','userresponse','Accept','Accept');
                            break;
                        case \TASTING\TASTING_USER_RESPONSE_DECLINE:
                            $attendance_response_status = 'declined';
                            $attendance_response_status_text = langTranslate('tasting','userresponse','Decline','Decline');
                            break;
                        case \TASTING\TASTING_USER_RESPONSE_INTERESTED_BUT_CANT:
                            $attendance_response_status = 'interested';
                            $attendance_response_status_text = langTranslate('tasting','userresponse','Interested, but can\'t attend','Interested, but can\'t attend');
                            break;
                        case \TASTING\TASTING_USER_RESPONSE_UNCERTAIN:
                            $attendance_response_status = 'uncertain';
                            $attendance_response_status_text = langTranslate('tasting','userresponse','Uncertain','Uncertain');
                            break;
                        default:
                            $attendance_response_status = null;//never
                            $attendance_response_status_text = null;
                    }
                } else {
                    if((int)$row['t_participation_type']==2){//public
                        $attendance_response_status = 'public';
                        $attendance_response_status_text = langTranslate('tasting','tasting','Public Tasting', 'Public Tasting');
                    }
                }
            }
            $date = date('d.m.Y', $row['t_start_ts']);
            $tasting = array(
                    'id'=>$id,
                    'date'=>$date,
                    'name'=>$row['t_name']?$row['t_name']:formatReplace(langTranslate('tasting','tasting','Tasting №@1 from @2',  'Tasting №@1 from @2'), $id, $date),
                    'location'=>$row['t_location'],
                    'status'=>isset($allstatuslist[$status])?$allstatuslist[$status]:'',
                    'attendance_response_status'=>$attendance_response_status,
                    'attendance_response_status_text'=>$attendance_response_status_text,
                    'pending_review_count'=>$row['pending_review_count'],
                    'personal_price'=>($status==\TASTING\TASTING_STATUS_PREPARATION && (int)$row['user_id']!=$this->XM->user->getUserId())?formatPrice($row['personal_price']):null,
                    'lead_to_stats'=>$status==\TASTING\TASTING_STATUS_FINISHED,
                    'ranking_scoring'=>((int)$row['t_score_method']==1)?1:0,
                );
            if(isset($row['tuges_score'])&&isset($row['tuges_place'])&&isset($row['tuges_leniency'])&&isset($row['tuges_zero'])){
                $tasting['global_expert_rating_score'] = ($row['tuges_score']!==null)?str_replace('.', ',', ((float)$row['tuges_score'])/100):null;
                $tasting['global_expert_rating_place'] = (int)$row['tuges_place'];
                $tasting['global_expert_rating_leniency'] = $row['tuges_leniency']?1:0;
                $tasting['global_expert_rating_zero'] = $row['tuges_zero']?1:0;
            }
            $result[] = $tasting;
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filtertids');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filterpendingreviews');
        return $result;
    }
    public function get_tasting_vintage_preparation_data($tpv_id, &$err){
        $tpv_id = (int)$tpv_id;
        if($tpv_id<=0){
            $err = langTranslate('tasting', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT tasting_product_vintage.tpv_preparation_type,if(tasting_product_vintage.tpv_preparation_type>0,coalesce(tasting_product_vintage.tpv_tasting_mts,FLOOR(UNIX_TIMESTAMP(CURRENT_TIMESTAMP)/60))-tasting_product_vintage.tpv_preparation_mts,null) as tpv_preparation_minutes_elapsed
            from tasting_product_vintage
            where tasting_product_vintage.tpv_id = '.$tpv_id.'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Vintage doesn\'t exist',  'Vintage doesn\'t exist');
            return false;
        }
        return array(
                'preparation_type'=>(int)$row['tpv_preparation_type'],
                'preparation_time'=>(int)$row['tpv_preparation_minutes_elapsed']
            );
    }
    public function change_tasting_vintage_preparation($tpv_id,$preparation_type,$preparation_time,&$err){
        $tpv_id = (int)$tpv_id;
        $preparation_type = (int)$preparation_type;
        $preparation_time = (int)$preparation_time;
        if($preparation_time<0 || $preparation_time>60*24*7){//week
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                    langTranslate('tasting','preparation','Started (minutes ago)','Started (minutes ago)'));
            return false;
        }

        $res = $this->XM->sqlcore->query('SELECT tasting.t_status,tasting.user_id, tasting_product_vintage.tpv_review_request_status
            from tasting 
            inner join tasting_product_vintage on tasting_product_vintage.t_id = tasting.t_id and tasting_product_vintage.tpv_id = '.$tpv_id.'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Vintage doesn\'t exist',  'Vintage doesn\'t exist');
            return false;
        }
        $status = (int)$row['t_status'];
        if($status!=\TASTING\TASTING_STATUS_DRAFT&&$status!=\TASTING\TASTING_STATUS_PREPARATION&&$status!=\TASTING\TASTING_STATUS_STARTED){
            $err = langTranslate('tasting','err','You can only edit vintage preparation in draft, preparation or started stages','You can only edit vintage preparation in draft, preparation or started stages');
            return false;
        }
        if($row['tpv_review_request_status']!=0){
            $err = langTranslate('tasting','err','You can\'t modify product preparation data for product that has it\'s tasting already started','You can\'t modify product preparation data for product that has it\'s tasting already started');
            return false;
        }
        $has_edit_rights = $this->XM->user->getUserId()==(int)$row['user_id']||$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_TASTINGS);
        if(!$has_edit_rights){
            $err = langTranslate('tasting','err','Access Denied','Access Denied');
            return false;
        }
        if(!in_array($preparation_type, array_keys($this->get_tasting_vintage_preparation_list()))){
            $preparation_type = 0;
        }
        $update_arr = array();
        $update_arr[] = 'tpv_preparation_type = '.$preparation_type;
        if($preparation_type==0){
            $update_arr[] = 'tpv_preparation_mts = null';
        } else {
            $update_arr[] = 'tpv_preparation_mts = floor(UNIX_TIMESTAMP(CURRENT_TIMESTAMP)/60)-'.$preparation_time;
        }
        $this->XM->sqlcore->query('UPDATE tasting_product_vintage set '.implode(',', $update_arr).' where tpv_id = '.$tpv_id);
        $this->XM->sqlcore->commit();
        return true;
    }

    public function get_tasting_review_particularity_option_list($tasting_id){
        $tasting_id = (int)$tasting_id;
        $result = array();
        $res = $this->XM->sqlcore->query('SELECT product_vintage_review_particularity_option_list.pvrpol_id,product_vintage_review_particularity_option_list.pvrpol_name 
            from product_vintage_review_particularity_option_list 
            inner join tasting on tasting.t_id = '.$tasting_id.'
            where product_vintage_review_particularity_option_list.pvrpol_active = 1 and ( tasting.t_assessment = 0 or product_vintage_review_particularity_option_list.pvrpol_only_private = 0 )');
        while($row = $this->XM->sqlcore->getRow($res)){
            $result[(int)$row['pvrpol_id']] = $row['pvrpol_name'];
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }
    public function get_tasting_review_particularity_data($tasting_id){
        $result = array();
        $res = $this->XM->sqlcore->query('SELECT distinct product_vintage_review_particularity_option_list.pvrpol_name
            from tasting_review_particularity_options 
            inner join product_vintage_review_particularity_option_list on product_vintage_review_particularity_option_list.pvrpol_id = tasting_review_particularity_options.pvrpol_id and product_vintage_review_particularity_option_list.pvrpol_active = 1
            inner join tasting on tasting.t_id = tasting_review_particularity_options.t_id
            where tasting_review_particularity_options.t_id = '.(int)$tasting_id.' and ( tasting.t_assessment = 0 or product_vintage_review_particularity_option_list.pvrpol_only_private = 0 )');
        while($row = $this->XM->sqlcore->getRow($res)){
            $result[] = $row['pvrpol_name'];
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }
    public function edit_tasting_review_particularity_data($tasting_id, $skip_options, &$err){
        $tasting_id = (int)$tasting_id;
        if(!is_array($skip_options)){
            $err = langTranslate('tasting', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        $tasting_info = $this->get_tasting($tasting_id);
        if(!$tasting_info || !$tasting_info['can_change_review_particularity_options']){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        if(in_array('balance-scores', $skip_options)){
            if(!in_array('balance-acid', $skip_options)){
                $skip_options[] = 'balance-acid';
            }
            if(!in_array('balance-fruit', $skip_options)){
                $skip_options[] = 'balance-fruit';
            }
            if(!in_array('balance-alcohol', $skip_options)){
                $skip_options[] = 'balance-alcohol';
            }
            if(!in_array('average-balance-score', $skip_options)){
                $skip_options[] = 'average-balance-score';
            }
        }
        $this->XM->sqlcore->query('DELETE FROM tasting_review_particularity_options where t_id = '.$tasting_id);
        $tasting_review_particularity_option_list = $this->get_tasting_review_particularity_option_list($tasting_id);
        foreach($tasting_review_particularity_option_list as $pvrpol_id=>$pvrpol_name){
            if(in_array($pvrpol_name, $skip_options)){
                $this->XM->sqlcore->query('INSERT INTO tasting_review_particularity_options (t_id,pvrpol_id) VALUES ('.$tasting_id.','.$pvrpol_id.')');
            }
        }
        $this->XM->sqlcore->commit();
        return true;
    }
    public function tasting_reviews_swap($tasting_id, $user_id, $tpv_ids, &$err){
        $tasting_id = (int)$tasting_id;
        $user_id = (int)$user_id;
        $res = $this->XM->sqlcore->query('SELECT tasting.t_status, if(tasting.t_assessment=1 and tasting.t_is_approved = 1,1,0) as assessed from tasting where tasting.t_id = '.$tasting_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Tasting doesn\'t exist', 'Tasting doesn\'t exist');
            return false;
        }
        $tasting_finished = false;
        if($row['t_status']==\TASTING\TASTING_STATUS_FINISHED){
            $tasting_finished = true;
        }
        $tasting_assessed = false;
        if($row['assessed']==1){
            $tasting_assessed = true;
        }
        
        $clean_tpv_ids = array();
        foreach($tpv_ids as $id){
            $id = (int)$id;
            if(!in_array($id,$clean_tpv_ids)){
                $clean_tpv_ids[] = $id;
            }
        }
        $tpv_ids = $clean_tpv_ids;
        unset($clean_tpv_ids);
        if(count($tpv_ids)!=2){
            $err = langTranslate('tasting','err','You have to choose two tasting products to swap reviews','You have to choose two tasting products to swap reviews');
            return false;
        }
        $reviews = array();
        $pv_ids = array();
        $need_global_refresh = false;
        $res = $this->XM->sqlcore->query('SELECT tasting_product_vintage.tpv_id, tasting_product_vintage.pv_id, product_vintage_review.pvr_id, product_vintage_review.user_expert_level
            from tasting_product_vintage
            left join product_vintage_review on product_vintage_review.tpv_id = tasting_product_vintage.tpv_id and product_vintage_review.user_id = '.$user_id.'
            where tasting_product_vintage.t_id = '.$tasting_id.' and tasting_product_vintage.tpv_id in ('.implode(',',$tpv_ids).')');
        while($row = $this->XM->sqlcore->getRow($res)){
            $pv_id = (int)$row['pv_id'];
            $reviews[] = array('tpv_id'=>(int)$row['tpv_id'],'pv_id'=>$pv_id,'pvr_id'=>(int)$row['pvr_id']);
            if(!in_array($pv_id,$pv_ids)){
                $pv_ids[] = $pv_id;
            }
            
            if((int)$row['user_expert_level']===3){
                $need_global_refresh = true;
            }
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->product->load();
        for($i=0;$i<2;$i++){
            $pvr_id = $reviews[$i]['pvr_id'];
            if(!$pvr_id){
                continue;
            }
            $this->XM->sqlcore->query('UPDATE product_vintage_review SET tpv_id = '.$reviews[1-$i]['tpv_id'].', pv_id = '.$reviews[1-$i]['pv_id'].',pvr_block = pvr_block&~'.(\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE).' where pvr_id = '.$pvr_id);
        }
        $this->XM->sqlcore->commit();
        foreach($tpv_ids as $tpv_id){
            $this->__fill_pvr_block_for_tpv_id($tpv_id);
        }
        $rows = array();
        $res = $this->XM->sqlcore->query('select 1 as auto, product_vintage_review.tpv_id, tasting_user_evaluation.tue_id
            from product_vintage_review 
            inner join tasting_user_evaluation on tasting_user_evaluation.tpv_id = product_vintage_review.tpv_id and tasting_user_evaluation.tue_type = 2
            where product_vintage_review.tpv_id in ('.implode(',',$tpv_ids).') 
            group by product_vintage_review.tpv_id, tasting_user_evaluation.tue_id
            having max(if(product_vintage_review.user_id='.$user_id.',product_vintage_review.user_expert_level,null))>=max(if(product_vintage_review.user_id<>'.$user_id.',product_vintage_review.user_expert_level,null))
            union all
            SELECT 0 as auto, tasting_user_evaluation.tpv_id, tasting_user_evaluation.tue_id
            from tasting_user_evaluation 
            where tasting_user_evaluation.tue_type <> 4 and tasting_user_evaluation.tpv_id in ('.implode(',',$tpv_ids).')');
        while($row = $this->XM->sqlcore->getRow($res)){
            $rows[] = $row;
        }
        $this->XM->sqlcore->freeResult($res);
        $tues = array();
        foreach($rows as $row){
            $tue_id = (int)$row['tue_id'];
            if(in_array($tue_id,$tues)){
                continue;
            }
            $auto = (bool)$row['auto'];
            if((bool)$row['auto']){
                $this->XM->sqlcore->query('DELETE FROM tasting_user_evaluation where tue_id = '.$tue_id);
                $this->__generate_automatic_evaluation((int)$row['tpv_id'],false);
            } else {
                $this->XM->sqlcore->query('DELETE FROM tasting_user_evaluation_user_score where tue_id = '.$tue_id);
                $this->__process_evaluation($tue_id);
            }
        }
        
        if($tasting_finished){//tasting finished
            foreach($pv_ids as $vintage_id){
                $this->XM->product->__refresh_personal_vintage_score($vintage_id, $this->XM->user->getUserId());
            }
            if($tasting_assessed){//tasting public and approved
                $this->XM->product->__refresh_vintage_scores_for_tasting($tasting_id);
                if($need_global_refresh){
                    $this->__refresh_global_expert_evaluation_for_tasting($tasting_id);
                } else {
                    $this->__process_global_evaluation_for_tasting($tasting_id);
                }
            }
        }
        return true;
    }
    public function get_contest_status_list(){
        return array(
                \TASTING\CONTEST_STATUS_DRAFT=>langTranslate('tasting','contest status','Draft','Draft'),
                \TASTING\CONTEST_STATUS_PREPARATION=>langTranslate('tasting','contest status','Preparation','Preparation'),
                \TASTING\CONTEST_STATUS_SUMMING_UP=>langTranslate('tasting','contest status','Summing up','Summing up'),
                \TASTING\CONTEST_STATUS_FINISHED=>langTranslate('tasting','contest status','Finished','Finished'),
            );
    }
    public function get_contest($contest_id){
        $contest_id = (int)$contest_id;
        $where_arr = array();
        $edit_access_level_select_sql = '';

        if($this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
            $edit_access_level_select_sql = '2 as edit_access_level';
        } else {
            $edit_access_level_select_sql = 'case tasting_contest_user_access.tcua_owner when 1 then 2 when 0 then 1 else 0 end as edit_access_level';
        }
        $tasting_contest_user_access_left_join = 'left join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId();
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_ALL_CONTESTS)){
            $where_arr[] = '(tasting_contest.tc_status = '.\TASTING\CONTEST_STATUS_FINISHED.' and tasting_contest.tc_is_approved = 1 or tasting_contest_user_access.tcua_owner = 1 or tasting_contest_user_access.user_id is not null and tasting_contest.tc_status <> '.\TASTING\CONTEST_STATUS_DRAFT.')';
        }
        if($this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_ALL_CONTESTS) && $this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
            $tasting_contest_user_access_left_join = '';
        }

        $res = $this->XM->sqlcore->query('SELECT tasting_contest.tc_id,tasting_contest.tc_name,tasting_contest.tc_logo_ext,tasting_contest.tc_location,tasting_contest.tc_desc,tasting_contest.tc_assessment,tasting_contest.tc_status,tasting_contest.tc_is_approved,tasting_contest_timeframe.tc_start_ts,tasting_contest_timeframe.tc_end_ts,coalesce(tasting_contest_timeframe.all_tastings_finished,0) as all_tastings_finished,'.$edit_access_level_select_sql.'
            from tasting_contest
            '.$tasting_contest_user_access_left_join.'
            left join (
                    select tasting_contest_tasting.tc_id,min(tasting.t_start_ts) as tc_start_ts,max(tasting.t_end_ts) as tc_end_ts,min(if(tasting.t_status='.\TASTING\TASTING_STATUS_FINISHED.',1,0)) as all_tastings_finished
                        from tasting
                        inner join tasting_contest_tasting on tasting_contest_tasting.t_id = tasting.t_id
                        where tasting_contest_tasting.tc_id = '.$contest_id.'
                        group by tasting_contest_tasting.tc_id
                ) as tasting_contest_timeframe on tasting_contest_timeframe.tc_id = tasting_contest.tc_id
            where tasting_contest.tc_id = '.$contest_id.' '.(!empty($where_arr)?'and '.implode(' and ', $where_arr):'').' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        $statuslist = $this->get_contest_status_list();
        $status = (int)$row['tc_status'];
        $id = (int)$row['tc_id'];
        $has_full_access = ($row['edit_access_level'] == 2);
        $has_limited_access = ($row['edit_access_level'] >= 1);
        $can_be_assessed = ($row['tc_assessment'] == 1) && ($row['tc_is_approved']===null) && (\TASTING\CONTEST_STATUS_FINISHED==$status);
        if($can_be_assessed){
            $can_be_assessed = $this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_APPROVE_CONTEST);
        }
        return array(
                'id'=>$id,
                'startts'=>(int)$row['tc_start_ts'],
                'endts'=>(int)$row['tc_end_ts'],
                'name'=>(string)$row['tc_name'],
                'logourl'=>$row['tc_logo_ext']?BASE_URL.'/modules/Tasting/contestimg/'.$id.'.'.$row['tc_logo_ext']:null,
                'location'=>(string)$row['tc_location'],
                'desc'=>(string)$row['tc_desc'],
                'assessment'=>(int)$row['tc_assessment'],
                'status'=>$status,
                'status_text'=>isset($statuslist[$status])?$statuslist[$status]:'',
                
                'can_edit'=>$has_full_access&&\TASTING\CONTEST_STATUS_DRAFT==$status,
                'can_edit_user_access_list'=>$has_full_access&&($status==\TASTING\CONTEST_STATUS_DRAFT||$status==\TASTING\CONTEST_STATUS_PREPARATION||$status==\TASTING\CONTEST_STATUS_SUMMING_UP),
                'can_view_user_access_list'=>$has_full_access,

                'can_edit_nominations'=>$has_full_access&&$status==\TASTING\CONTEST_STATUS_SUMMING_UP,
                'can_view_nominations'=>($has_limited_access&&($status==\TASTING\CONTEST_STATUS_SUMMING_UP||$status==\TASTING\CONTEST_STATUS_FINISHED))||(\TASTING\CONTEST_STATUS_FINISHED==$status&&$row['tc_is_approved']),

                'can_delete'=>$has_full_access&&\TASTING\CONTEST_STATUS_DRAFT==$status,
                'can_change_to_draft'=>$has_full_access&&\TASTING\CONTEST_STATUS_PREPARATION==$status,
                'can_change_to_preparation'=>$has_full_access&&\TASTING\CONTEST_STATUS_DRAFT==$status,
                'can_change_to_summing_up'=>$has_full_access&&\TASTING\CONTEST_STATUS_PREPARATION==$status&&$row['all_tastings_finished'],
                'can_change_to_finished'=>$has_full_access&&\TASTING\CONTEST_STATUS_SUMMING_UP==$status&&$row['all_tastings_finished'],

                'can_view_statistics'=>$has_limited_access&&(\TASTING\CONTEST_STATUS_SUMMING_UP==$status||\TASTING\CONTEST_STATUS_PREPARATION==$status||\TASTING\CONTEST_STATUS_FINISHED==$status)||(\TASTING\CONTEST_STATUS_FINISHED==$status&&$row['tc_is_approved']),
                'can_view_certificates'=>$row['tc_is_approved']&&($has_limited_access&&(\TASTING\CONTEST_STATUS_SUMMING_UP==$status||\TASTING\CONTEST_STATUS_PREPARATION==$status)||\TASTING\CONTEST_STATUS_FINISHED==$status),
                'can_view_tasting_list'=>$has_limited_access,
                'can_add_tasting'=>$has_limited_access&&($status==\TASTING\CONTEST_STATUS_DRAFT||$status==\TASTING\CONTEST_STATUS_PREPARATION||$status==\TASTING\CONTEST_STATUS_SUMMING_UP),
                'can_assess'=>$can_be_assessed,
            );
    }
    public function add_contest($name, $logoinfo, $location, $desc, $assessment, &$err){
        if(!$this->XM->user->isLoggedIn() || !$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_ADD_CONTEST)){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $insert_keys[] = 'tc_status';
        $insert_vals[] = \TASTING\CONTEST_STATUS_DRAFT;

        $name = trim($name);
        if(!strlen($name)){
            $err = formatReplace(langTranslate('tasting', 'err', 'Field @1 is empty',  'Field @1 is empty'),
                    langTranslate('tasting', 'contest', 'Name', 'Name'));
            return false;
        }
        if(mb_strlen($name,'UTF-8')>128){
            $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('tasting', 'contest', 'Name', 'Name'),
                    128);
            return false;
        }
        $insert_keys[] = 'tc_name';
        $insert_vals[] = '\''.$this->XM->sqlcore->prepString($name,128).'\'';

        $location = trim($location);
        if(!strlen($location)){
            $err = formatReplace(langTranslate('tasting', 'err', 'Field @1 is empty',  'Field @1 is empty'),
                    langTranslate('tasting', 'contest', 'Location', 'Location'));
            return false;
        }
        if(mb_strlen($location,'UTF-8')>512){
            $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('tasting', 'contest', 'Location', 'Location'),
                    512);
            return false;
        }
        $insert_keys[] = 'tc_location';
        $insert_vals[] = '\''.$this->XM->sqlcore->prepString($location,512).'\'';

        $desc = trim($desc);
        if(strlen($desc)){
            if(mb_strlen($desc,'UTF-8')>60000){
                $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                        langTranslate('tasting', 'contest', 'Description', 'Description'),
                        60000);
                return false;
            }
            $insert_keys[] = 'tc_desc';
            $insert_vals[] = '\''.$this->XM->sqlcore->prepString($desc,60000).'\'';    
        }
        $ext = null;
        if(is_array($logoinfo) && isset($logoinfo['error']) && $logoinfo['error']!=UPLOAD_ERR_NO_FILE){
            if($logoinfo['error'] != UPLOAD_ERR_OK){
                $err = formatReplace(langTranslate('main', 'err', 'Upload error (@2) for file @1',  'Upload error (@2) for file @1'),
                        $logoinfo['name'],
                        $logoinfo['error']);
                return false;
            }
            if($logoinfo['size']>100*1024){
                $err = formatReplace(langTranslate('main', 'err', 'Size of @1 exceeds limit of @2 kilobytes',  'Size of @1 exceeds limit of @2 kilobytes'),
                        $logoinfo['name'],
                        100);
                return false;
            }
            $ext = strtolower(substr($logoinfo['name'], strrpos($logoinfo['name'],'.')+1,strlen($logoinfo['name'])));
            $valid_exts = array('png','gif','jpg','jpeg');
            if(!in_array($ext, $valid_exts)){
                $err = formatReplace(langTranslate('main', 'err', 'Invalid image type for file @1. Supported types: @2',  'Invalid image type for file @1. Supported types: @2'),
                        $logoinfo['name'],
                        implode(', ', $valid_exts));
                return false;
            }
            $insert_keys[] = 'tc_logo_ext';
            $insert_vals[] = '\''.$this->XM->sqlcore->prepString($ext,5).'\'';
        }
        
        $assessment = $assessment?1:0;
        $insert_keys[] = 'tc_assessment';
        $insert_vals[] = $assessment;

        $this->XM->sqlcore->query('INSERT INTO tasting_contest ('.implode(',', $insert_keys).') VALUES ('.implode(',', $insert_vals).')');
        $contest_id = $this->XM->sqlcore->lastInsertId();
        if($ext && !move_uploaded_file($logoinfo['tmp_name'], ABS_PATH.'/modules/Tasting/contestimg/'.$contest_id.'.'.$ext)){
            $err = formatReplace(langTranslate('main', 'err', 'Upload error (@2) for file @1',  'Upload error (@2) for file @1'),
                    $name,
                    '-89');
            $this->XM->sqlcore->rollback();
            return false;
        }
        $this->XM->sqlcore->query('INSERT INTO tasting_contest_user_access (tc_id,user_id,tcua_owner) VALUES ('.$contest_id.','.$this->XM->user->getUserId().',1)');
        $this->XM->sqlcore->commit();
        return $contest_id;
    }
    public function edit_contest($contest_id, $name, $logoinfo, $location, $desc, $assessment, &$err){
        $contest_id = (int)$contest_id;

        $tasting_contest_user_access_left_join = '';
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
            $edit_access_level_select_sql = '2 as edit_access_level';
            $tasting_contest_user_access_left_join = '';
        } else {
            $edit_access_level_select_sql = 'case tasting_contest_user_access.tcua_owner when 1 then 2 when 0 then 1 else 0 end as edit_access_level';
            $tasting_contest_user_access_left_join = 'left join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId();
        }
        $res = $this->XM->sqlcore->query('SELECT tasting_contest.tc_name,tasting_contest.tc_logo_ext,tasting_contest.tc_location,tasting_contest.tc_desc,tasting_contest.tc_assessment,tasting_contest.tc_status,'.$edit_access_level_select_sql.'
            from tasting_contest
            '.$tasting_contest_user_access_left_join.'
            where tasting_contest.tc_id = '.$contest_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Contest doesn\'t exist',  'Contest doesn\'t exist');
            return false;
        }
        if($row['edit_access_level']!=2){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        if(\TASTING\CONTEST_STATUS_DRAFT!=(int)$row['tc_status']){
            $err = langTranslate('tasting','err','You can only edit contests in draft stage','You can only edit contests in draft stage');
            return false;
        }
        $update_arr = array();
        $name = trim($name);
        if($name!==$row['tc_name']){
            if(!strlen($name)){
                $err = formatReplace(langTranslate('tasting', 'err', 'Field @1 is empty',  'Field @1 is empty'),
                        langTranslate('tasting', 'contest', 'Name', 'Name'));
                return false;
            }
            if(mb_strlen($name,'UTF-8')>128){
                $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                        langTranslate('tasting', 'contest', 'Name', 'Name'),
                        128);
                return false;
            }
            $update_arr[] = 'tc_name = \''.$this->XM->sqlcore->prepString($name,128).'\'';
        }
        $location = trim($location);
        if($location!==$row['tc_location']){
            if(!strlen($location)){
                $err = formatReplace(langTranslate('tasting', 'err', 'Field @1 is empty',  'Field @1 is empty'),
                        langTranslate('tasting', 'contest', 'Location', 'Location'));
                return false;
            }
            if(mb_strlen($location,'UTF-8')>512){
                $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                        langTranslate('tasting', 'contest', 'Location', 'Location'),
                        512);
                return false;
            }
            $update_arr[] = 'tc_location = \''.$this->XM->sqlcore->prepString($location,512).'\'';
        }
        $desc = trim($desc);
        if($desc!==$row['tc_desc']){
            if(!strlen($desc)){
                $desc = null;
            }
            if(mb_strlen($desc,'UTF-8')>60000){
                $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                        langTranslate('tasting', 'contest', 'Description', 'Description'),
                        60000);
                return false;
            }
            $update_arr[] = 'tc_desc = '.($desc!==null?'\''.$this->XM->sqlcore->prepString($desc,512).'\'':'null');
        }

        $ext = null;
        $origin_fname = null;
        if(is_array($logoinfo) && isset($logoinfo['error']) && $logoinfo['error'] != UPLOAD_ERR_NO_FILE){
            if($logoinfo['error'] != UPLOAD_ERR_OK){
                $err = formatReplace(langTranslate('main', 'err', 'Upload error (@2) for file @1',  'Upload error (@2) for file @1'),
                        $logoinfo['name'],
                        $logoinfo['error']);
                return false;
            }
            if($logoinfo['size']>100*1024){
                $err = formatReplace(langTranslate('main', 'err', 'Size of @1 exceeds limit of @2 kilobytes',  'Size of @1 exceeds limit of @2 kilobytes'),
                        $logoinfo['name'],
                        100);
                return false;
            }
            $ext = strtolower(substr($logoinfo['name'], strrpos($logoinfo['name'],'.')+1,strlen($logoinfo['name'])));
            $valid_exts = array('png','gif','jpg','jpeg');
            if(!in_array($ext, $valid_exts)){
                $err = formatReplace(langTranslate('main', 'err', 'Invalid image type for file @1. Supported types: @2',  'Invalid image type for file @1. Supported types: @2'),
                        $logoinfo['name'],
                        implode(', ', $valid_exts));
                return false;
            }
            if(!move_uploaded_file($logoinfo['tmp_name'], ABS_PATH.'/modules/Tasting/contestimg/'.$contest_id.'.'.$ext)){
                $err = formatReplace(langTranslate('main', 'err', 'Upload error (@2) for file @1',  'Upload error (@2) for file @1'),
                        $name,
                        '-89');
                $this->XM->sqlcore->rollback();
                return false;
            }
            if($ext!==$row['tc_logo_ext']){
                if($row['tc_logo_ext'] && file_exists(ABS_PATH.'/modules/Tasting/contestimg/'.$contest_id.'.'.$row['tc_logo_ext'])){
                    @unlink(ABS_PATH.'/modules/Tasting/contestimg/'.$contest_id.'.'.$row['tc_logo_ext']);    
                }
                $update_arr[] = 'tc_logo_ext = \''.$this->XM->sqlcore->prepString($ext,5).'\'';
            }
        }
        
        $assessment = $assessment?1:0;
        if($assessment!=(int)$row['tc_assessment']){
            $update_arr[] = 'tc_assessment = '.$assessment;
        }

        $this->XM->sqlcore->query('UPDATE tasting_contest set '.implode(',', $update_arr).' where tc_id = '.$contest_id);
        $this->XM->sqlcore->commit();
        return true;
    }




    public function filter_contest($start_date_from, $start_date_to, $status, $only_owned, $only_organized, $only_approved, $only_for_assessment, $only_participated, $order_by_field, $order_by_direction_asc, &$page, &$pagelimit, &$count, &$err){
        if(($page = (int)$page)<=0){
            $page = 1;
        }
        $pagelimit = (int)$pagelimit;
        if($pagelimit<=0 || $pagelimit>100){
            $pagelimit = 50;
        }
        $start_from_timestamp = $start_to_timestamp = null;
        if(preg_match('#^[0-3]?[0-9]\.[0-1][0-9]\.\d{4}$#', $start_date_from)){
            $start_from_timestamp = strtotime($start_date_from);
        }
        if(preg_match('#^[0-3]?[0-9]\.[0-1][0-9]\.\d{4}$#', $start_date_to)){
            $start_to_timestamp = strtotime($start_date_to)+86400;
        }
        $statuslist = array();
        $allstatuslist = $this->get_contest_status_list();
        if(is_array($status)&&!empty($status)){
            foreach($status as $key){
                $key = (int)$key;
                if(array_key_exists($key, $allstatuslist)){
                    $statuslist[] = $key;
                }
            }
            if(count($statuslist)==count($allstatuslist)){//find all anyway
                $statuslist = array();
            }
        }
        //prepare params
        $where_arr = array();
        if(!empty($statuslist)){
            $where_arr[] = 'tasting_contest.tc_status in ('.implode(',', $statuslist).')';
        }
        if($only_approved){
            $where_arr[] = 'tasting_contest.tc_is_approved = 1';
        }
        if($only_for_assessment){
            if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_APPROVE_CONTEST)){
                return array();
            }
            $where_arr[] = 'tasting_contest.tc_is_approved is null and tasting_contest.tc_assessment = 1 and tasting_contest.tc_status = '.\TASTING\CONTEST_STATUS_FINISHED;
        }
        $tasting_contest_user_access_left_join = 'left join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId();
        $tasting_contest_user_access_inner_join = '';
        if($only_owned){
            $tasting_contest_user_access_inner_join = 'inner join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId().' and tasting_contest_user_access.tcua_owner = 1';
        } elseif($only_organized){
            $tasting_contest_user_access_inner_join = 'inner join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId();
        } elseif(!$only_approved){
            if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_ALL_CONTESTS)){
                $where_arr[] = '(tasting_contest_user_access.tcua_owner = 1 or tasting_contest_user_access.tc_id is not null and tasting_contest.tc_status <> '.\TASTING\CONTEST_STATUS_DRAFT.' or tasting_contest.tc_status = '.\TASTING\CONTEST_STATUS_FINISHED.' and tasting_contest.tc_is_approved = 1)';
            }
        }
        $tasting_contest_timeframe_inner_join = '';
        $tasting_contest_timeframe_left_join = 'left join (
                    select tasting_contest_tasting.tc_id,min(tasting.t_start_ts) as tc_start_ts
                        from tasting
                        inner join tasting_contest_tasting on tasting_contest_tasting.t_id = tasting.t_id
                        inner join tasting_contest on tasting_contest.tc_id = tasting_contest_tasting.tc_id
                        '.(strlen($tasting_contest_user_access_inner_join)?$tasting_contest_user_access_inner_join:$tasting_contest_user_access_left_join).'
                        '.(!empty($where_arr)?'where '.implode(' and ', $where_arr):'').'
                        group by tasting_contest_tasting.tc_id
                ) as tasting_contest_timeframe on tasting_contest_timeframe.tc_id = tasting_contest.tc_id';
        if($start_from_timestamp>0){
            $tasting_contest_timeframe_inner_join = 'inner join (
                    select tasting_contest_tasting.tc_id,min(tasting.t_start_ts) as tc_start_ts
                        from tasting
                        inner join tasting_contest_tasting on tasting_contest_tasting.t_id = tasting.t_id
                        inner join tasting_contest on tasting_contest.tc_id = tasting_contest_tasting.tc_id
                        '.(strlen($tasting_contest_user_access_inner_join)?$tasting_contest_user_access_inner_join:$tasting_contest_user_access_left_join).'
                        '.(!empty($where_arr)?'where '.implode(' and ', $where_arr):'').'
                        group by tasting_contest_tasting.tc_id
                ) as tasting_contest_timeframe on tasting_contest_timeframe.tc_id = tasting_contest.tc_id';
            if($start_to_timestamp>0){
                $where_arr[] = 'tasting_contest_timeframe.tc_start_ts between '.$start_from_timestamp.' and '.$start_to_timestamp;
            } else {
                $where_arr[] = 'tasting_contest_timeframe.tc_start_ts >= '.$start_from_timestamp;
            }
        } elseif($start_to_timestamp>0){
            $tasting_contest_timeframe_inner_join = 'inner join (
                    select tasting_contest_tasting.tc_id,min(tasting.t_start_ts) as tc_start_ts
                        from tasting
                        inner join tasting_contest_tasting on tasting_contest_tasting.t_id = tasting.t_id
                        inner join tasting_contest on tasting_contest.tc_id = tasting_contest_tasting.tc_id
                        '.(strlen($tasting_contest_user_access_inner_join)?$tasting_contest_user_access_inner_join:$tasting_contest_user_access_left_join).'
                        '.(!empty($where_arr)?'where '.implode(' and ', $where_arr):'').'
                        group by tasting_contest_tasting.tc_id
                ) as tasting_contest_timeframe on tasting_contest_timeframe.tc_id = tasting_contest.tc_id';
            $where_arr[] = 'tasting_contest_timeframe.tc_start_ts <= '.$start_to_timestamp;
        }
        $only_participated_inner_join = '';
        if($only_participated){
            $only_participated_inner_join = 'inner join (
                    select distinct tasting_contest_tasting.tc_id
                        from tasting_user
                        inner join tasting_contest_tasting on tasting_contest_tasting.t_id = tasting_user.t_id
                        where tasting_user.user_id = '.$this->XM->user->getUserId().'
                ) only_participated on only_participated.tc_id = tasting_contest.tc_id';
        }
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtertcids
            SELECT distinct tasting_contest.tc_id
            from tasting_contest 
            '.$only_participated_inner_join.'
            '.$tasting_contest_timeframe_inner_join.'
            '.(strlen($tasting_contest_user_access_inner_join)?$tasting_contest_user_access_inner_join:$tasting_contest_user_access_left_join).'
            '.(!empty($where_arr)?'where '.implode(' and ', $where_arr):''));
        $res = $this->XM->sqlcore->query('SELECT count(1) as cnt from filtertcids');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        $count = (int)$row['cnt'];
        if($count==0){
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filtertcids');
            return array();
        }
        if(($page-1)*$pagelimit>=$count){
            $page = ceil($count/$pagelimit);
        }
        $order_by_sql = null;
        switch($order_by_field){
            case 'date':
                $order_by_sql = 'tasting_contest_timeframe.tc_start_ts div 86400 '.($order_by_direction_asc?'asc':'desc').', tasting_contest.tc_name asc, tasting_contest.tc_id desc';
                break;
            case 'name':
                $order_by_sql = 'tasting_contest.tc_name '.($order_by_direction_asc?'asc':'desc').', tasting_contest_timeframe.tc_start_ts div 86400 desc, tasting_contest.tc_id desc';
                break;
            case 'status':
                $order_by_sql = 'tasting_contest.tc_status '.($order_by_direction_asc?'asc':'desc').', tasting_contest_timeframe.tc_start_ts div 86400 desc, tasting_contest.tc_name asc, tasting_contest.tc_id desc';
                break;
            default:
                $order_by_sql = 'tasting_contest_timeframe.tc_start_ts div 86400 desc, tasting_contest.tc_name asc, tasting_contest.tc_id desc';
        }

        $result = array();
        $res = $this->XM->sqlcore->query('SELECT distinct tasting_contest.tc_id, tasting_contest_timeframe.tc_start_ts, tasting_contest.tc_name, tasting_contest.tc_location, tasting_contest.tc_status, tasting_contest_user_access.tcua_owner, tasting_contest.tc_is_approved
            from filtertcids
            inner join tasting_contest on tasting_contest.tc_id = filtertcids.tc_id
            '.(strlen($tasting_contest_user_access_inner_join)?$tasting_contest_user_access_inner_join:$tasting_contest_user_access_left_join).'
            '.$tasting_contest_timeframe_left_join.'
            '.($order_by_sql?'order by '.$order_by_sql:'').'
            limit '.$pagelimit.' offset '.(($page-1)*$pagelimit));
        while($row = $this->XM->sqlcore->getRow($res)){
            $id = (int)$row['tc_id'];
            $status = (int)$row['tc_status'];
            $date = $row['tc_start_ts']?date('d.m.Y', $row['tc_start_ts']):null;
            $result[] = array(
                    'id'=>$id,
                    'date'=>$date,
                    'name'=>$row['tc_name'],
                    'location'=>$row['tc_location'],
                    'status'=>isset($allstatuslist[$status])?$allstatuslist[$status]:'',
                    'lead_to_stats'=>in_array($status, array(\TASTING\CONTEST_STATUS_SUMMING_UP,\TASTING\CONTEST_STATUS_FINISHED)),
                );
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filtertcids');
        return $result;
    }
    public function get_contest_tasting_list($contest_id,$vintage_id,$user_id,$showstatus,$showowner,$showassessment){
        $contest_id = (int)$contest_id;

        $tasting_contest_user_access_select_sql = 'tasting_contest_user_access.tcua_owner, tasting_contest_user_access.user_id';
        $tasting_contest_user_access_left_join = 'left join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId();
        if($this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_ALL_CONTESTS)){
            $tasting_contest_user_access_left_join = '';
            if($this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
                $tasting_contest_user_access_select_sql = '1 as tcua_owner, 1 as user_id';    
            } else {
                $tasting_contest_user_access_select_sql = '0 as tcua_owner, 1 as user_id';
            }
        }

        $res = $this->XM->sqlcore->query('SELECT tasting_contest.tc_status, tasting_contest.tc_is_approved, '.$tasting_contest_user_access_select_sql.'
            from tasting_contest
            '.$tasting_contest_user_access_left_join.'
            where tasting_contest.tc_id = '.$contest_id.'
            limit 1');//grant access to everyone on user access list
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row||(!$row['tc_is_approved']&&!$row['user_id'])){//grant access to everyone on user access list
            return array();
        }
        $tasting_status_list = array();
        if($showstatus){
            $tasting_status_list = $this->get_status_list();    
        }
        $showowner_select_sql = '';
        $showowner_left_join = '';
        if($showowner){
            $showowner_select_sql = ',coalesce(user_ml.user_ml_fullname,\'-\') as user_ml_fullname';
            $showowner_left_join = 'left join (select user_ml.user_id,substring_index(group_concat(user_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as user_ml_id 
                from user_ml 
                inner join tasting_contest_user_access on tasting_contest_user_access.user_id = user_ml.user_id and tasting_contest_user_access.tc_id = '.$contest_id.'
                where user_ml_is_approved = 1 group by user_id
            ) as ln_glue on ln_glue.user_id = tasting.user_id
            left join user_ml on user_ml.user_ml_id = ln_glue.user_ml_id';
        }
        $vintage_id_inner_join = '';
        if($vintage_id){
            $vintage_id_inner_join = 'inner join tasting_product_vintage on tasting_product_vintage.t_id = tasting.t_id and tasting_product_vintage.pv_id = '.(int)$vintage_id;
        }
        $user_id_inner_join = '';
        if($user_id){
            $user_id_inner_join = 'inner join tasting_user on tasting_user.t_id = tasting.t_id and tasting_user.user_id = '.(int)$user_id;
        }
        $showassessment_select_sql = '';
        if($showassessment){
            $showassessment_select_sql = ',tasting.t_assessment,tasting.t_is_approved';
        }
        
        $can_remove = in_array((int)$row['tc_status'], array(\TASTING\CONTEST_STATUS_DRAFT,\TASTING\CONTEST_STATUS_PREPARATION,\TASTING\CONTEST_STATUS_SUMMING_UP));
        $can_remove_all = $can_remove&&$row['tcua_owner'];
        $result = array();
        $res = $this->XM->sqlcore->query('SELECT distinct tasting.t_id, tasting.t_start_ts,tasting.t_name,tasting.t_location,tasting.t_status,tasting.user_id'.$showowner_select_sql.$showassessment_select_sql.'
            from tasting
            inner join tasting_contest_tasting on tasting_contest_tasting.t_id = tasting.t_id
            '.$vintage_id_inner_join.'
            '.$user_id_inner_join.'
            '.$showowner_left_join.'
            where tasting_contest_tasting.tc_id = '.$contest_id.'
            order by tasting.t_start_ts desc,tasting.t_name asc,tasting.t_id asc');
        while($row = $this->XM->sqlcore->getRow($res)){
            $id = (int)$row['t_id'];
            $status = (int)$row['t_status'];
            $date = date('d.m.Y', $row['t_start_ts']);
            $result_row = array(
                    'id'=>$id,
                    'date'=>$date,
                    'name'=>$row['t_name']?$row['t_name']:formatReplace(langTranslate('tasting','tasting','Tasting №@1 from @2',  'Tasting №@1 from @2'), $id, $date),
                    'location'=>$row['t_location'],

                    'lead_to_stats'=>$status==\TASTING\TASTING_STATUS_FINISHED,
                    'can_remove'=>$can_remove_all||$can_remove&&((int)$row['user_id']==$this->XM->user->getUserId()),
                );
            if($showstatus){
                $result_row['status'] = isset($tasting_status_list[$status])?$tasting_status_list[$status]:'';
            }
            if($showowner){
                $result_row['owner_id'] = (int)$row['user_id'];
                $result_row['owner_name'] = $row['user_ml_fullname'];
            }
            if($showassessment){
                if($row['t_assessment']==0){
                    $result_row['assessment_private'] = 1;
                } else {
                    if($row['t_is_approved']===null){
                        $result_row['assessment_awaiting'] = 1;
                    } elseif($row['t_is_approved']==0){
                        $result_row['assessment_denied'] = 1;
                    }
                }
            }
            $result[] = $result_row;
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }

    public function get_contest_nomination_info($tcn_id){
        $tcn_id = (int)$tcn_id;
        $tasting_contest_user_access_select_sql = 'tasting_contest_user_access.user_id';
        $tasting_contest_user_access_left_join = 'left join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId();
        if($this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_ALL_CONTESTS)){
            $tasting_contest_user_access_select_sql = '1 as user_id';
            $tasting_contest_user_access_left_join = '';
        }
        $res = $this->XM->sqlcore->query('SELECT tasting_contest.tc_status, tasting_contest.tc_is_approved, tasting_contest_nomination.tcn_name, '.$tasting_contest_user_access_select_sql.'
            from tasting_contest
            inner join tasting_contest_nomination on tasting_contest_nomination.tc_id = tasting_contest.tc_id
            '.$tasting_contest_user_access_left_join.'
            where tasting_contest_nomination.tcn_id = '.$tcn_id.'
            limit 1');//grant access to everyone on user access list
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row||(!$row['tc_is_approved']&&!$row['user_id'])){//grant access to everyone on user access list
            return array(
                    'name'=>null
                );
        }
        return array(
                'name'=>$row['tcn_name']
            );
    }
    public function add_contest_nomination($contest_id,$name,&$err){
        $contest_id = (int)$contest_id;
        $tasting_contest_user_access_inner_join = 'inner join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId().' and tasting_contest_user_access.tcua_owner = 1';
        if($this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
            $tasting_contest_user_access_inner_join = '';
        }
        $res = $this->XM->sqlcore->query('SELECT tasting_contest.tc_status
            from tasting_contest
            '.$tasting_contest_user_access_inner_join.'
            where tasting_contest.tc_id = '.$contest_id.'
            limit 1');//only contest owner can add nominations
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        if((int)$row['tc_status']!=\TASTING\CONTEST_STATUS_SUMMING_UP){
            $err = langTranslate('tasting', 'err', 'Contest: You can only edit nominations during the summing up stage',  'You can only edit nominations during the summing up stage');
            return false;
        }
        $insert_keys = array();
        $insert_vals = array();
        $insert_keys[] = 'tc_id';
        $insert_vals[] = $contest_id;
        $insert_keys[] = 'tcn_index';
        $insert_vals[] = 0;
        $name = trim($name);
        if(!strlen($name)){
            $err = formatReplace(langTranslate('tasting', 'err', 'Field @1 is empty',  'Field @1 is empty'),
                    langTranslate('tasting', 'contest', 'Nomination: Name','Name'));
            return false;
        }
        if(mb_strlen($name,'UTF-8')>256){
            $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('tasting', 'contest', 'Nomination: Name','Name'),
                    256);
            return false;
        }
        $insert_keys[] = 'tcn_name';
        $insert_vals[] = '\''.$this->XM->sqlcore->prepString($name,256).'\'';


        $this->XM->sqlcore->query('UPDATE tasting_contest_nomination SET tcn_index = tcn_index + 1 where tc_id = '.$contest_id);
        $this->XM->sqlcore->query('INSERT INTO tasting_contest_nomination ('.implode(',', $insert_keys).') values ('.implode(',', $insert_vals).')');
        $this->XM->sqlcore->commit();
        return true;
    }
    public function edit_contest_nomination($nomination_id,$name,&$err){
        $nomination_id = (int)$nomination_id;
        $tasting_contest_user_access_inner_join = 'inner join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId().' and tasting_contest_user_access.tcua_owner = 1';
        if($this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
            $tasting_contest_user_access_inner_join = '';
        }
        $res = $this->XM->sqlcore->query('SELECT tasting_contest.tc_status, tasting_contest_nomination.tcn_name
            from tasting_contest_nomination
            inner join tasting_contest on tasting_contest.tc_id = tasting_contest_nomination.tc_id
            '.$tasting_contest_user_access_inner_join.'
            where tasting_contest_nomination.tcn_id = '.$nomination_id.'
            limit 1');//only contest owner can edit nominations
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        if((int)$row['tc_status']!=\TASTING\CONTEST_STATUS_SUMMING_UP){
            $err = langTranslate('tasting', 'err', 'Contest: You can only edit nominations during the summing up stage',  'You can only edit nominations during the summing up stage');
            return false;
        }
        $update_arr = array();
        $name = trim($name);
        if($name != $row['tcn_name']){
            if(!strlen($name)){
                $err = formatReplace(langTranslate('tasting', 'err', 'Field @1 is empty',  'Field @1 is empty'),
                        langTranslate('tasting', 'contest', 'Nomination: Name','Name'));
                return false;
            }
            if(mb_strlen($name,'UTF-8')>256){
                $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                        langTranslate('tasting', 'contest', 'Nomination: Name','Name'),
                        256);
                return false;
            }
            $update_arr[] = 'tcn_name = \''.$this->XM->sqlcore->prepString($name,256).'\'';
        }
        if(!empty($update_arr)){
            $this->XM->sqlcore->query('UPDATE tasting_contest_nomination SET '.implode(',', $update_arr).' where tcn_id = '.$nomination_id);
            $this->XM->sqlcore->commit();
        }
        return true;
    }
    public function remove_contest_nomination($nomination_id,&$err){
        $nomination_id = (int)$nomination_id;

        $tasting_contest_user_access_inner_join = 'inner join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId().' and tasting_contest_user_access.tcua_owner = 1';
        if($this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
            $tasting_contest_user_access_inner_join = '';
        }
        $res = $this->XM->sqlcore->query('SELECT tasting_contest.tc_status, tasting_contest.tc_id, tasting_contest_nomination.tcn_index
            from tasting_contest_nomination
            inner join tasting_contest on tasting_contest.tc_id = tasting_contest_nomination.tc_id
            '.$tasting_contest_user_access_inner_join.'
            where tasting_contest_nomination.tcn_id = '.$nomination_id.'
            limit 1');//only contest owner can add nominations
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        if((int)$row['tc_status']!=\TASTING\CONTEST_STATUS_SUMMING_UP){
            $err = langTranslate('tasting', 'err', 'Contest: You can only edit nominations during the summing up stage',  'You can only edit nominations during the summing up stage');
            return false;
        }
        $contest_id = (int)$row['tc_id'];
        $index = (int)$row['tcn_index'];

        $this->XM->sqlcore->query('DELETE FROM tasting_contest_nomination where tcn_id = '.$nomination_id);
        $this->XM->sqlcore->query('UPDATE tasting_contest_nomination set tcn_index = tcn_index - 1 where tc_id = '.$contest_id.' and tcn_index > '.$index);
        $this->XM->sqlcore->commit();
        return true;
    }
    
    public function get_contest_nomination_list($contest_id,$vintage_id,$showempty = false){
        $contest_id = (int)$contest_id;

        $tasting_contest_user_access_select_sql = 'tasting_contest_user_access.tcua_owner, tasting_contest_user_access.user_id';
        $tasting_contest_user_access_left_join = 'left join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId();
        if($this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_ALL_CONTESTS)){
            $tasting_contest_user_access_left_join = '';
            if($this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
                $tasting_contest_user_access_select_sql = '1 as tcua_owner, 1 as user_id';    
            } else {
                $tasting_contest_user_access_select_sql = '0 as tcua_owner, 1 as user_id';
            }
        }
        
        $res = $this->XM->sqlcore->query('SELECT tasting_contest.tc_status, tasting_contest.tc_is_approved, '.$tasting_contest_user_access_select_sql.'
            from tasting_contest
            '.$tasting_contest_user_access_left_join.'
            where tasting_contest.tc_id = '.$contest_id.'
            limit 1');//grant access to everyone on user access list
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row||(!$row['tc_is_approved']&&!$row['user_id'])){//grant access to everyone on user access list
            return array();
        }
        $vintage_id_inner_join = '';
        if($vintage_id){
            $vintage_id_inner_join = 'inner join tasting_contest_nomination_winner on tasting_contest_nomination_winner.tcn_id = tasting_contest_nomination.tcn_id and tasting_contest_nomination_winner.pv_id = '.(int)$vintage_id;
        }
        $showempty_inner_join = '';
        if(!$showempty && !$vintage_id){
            $showempty_inner_join = 'inner join tasting_contest_nomination_winner as showempty_inner_join on showempty_inner_join.tcn_id = tasting_contest_nomination.tcn_id';
        }
        
        $can_edit = in_array((int)$row['tc_status'], array(\TASTING\CONTEST_STATUS_DRAFT,\TASTING\CONTEST_STATUS_PREPARATION,\TASTING\CONTEST_STATUS_SUMMING_UP))&&$row['tcua_owner'];
        $result = array();
        $pointers = array();
        $res = $this->XM->sqlcore->query('SELECT distinct tasting_contest_nomination.tcn_id,tasting_contest_nomination.tcn_name,tasting_contest_nomination.tcn_index
            from tasting_contest_nomination
            '.$vintage_id_inner_join.'
            '.$showempty_inner_join.'
            where tasting_contest_nomination.tc_id = '.$contest_id.'
            order by tasting_contest_nomination.tcn_index asc');
        while($row = $this->XM->sqlcore->getRow($res)){
            $id = (int)$row['tcn_id'];
            $result[] = array(
                    'id'=>$id,
                    'name'=>$row['tcn_name'],
                    'products'=>array(),
                    'can_remove'=>$can_edit,
                    'can_edit'=>$can_edit,
                    'can_move'=>$can_edit
                );
            $pointers[$id] = count($result)-1;
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->product->preload();
        $res = $this->XM->sqlcore->query('SELECT tasting_contest_nomination_winner.tcn_id,product_vintage.pv_id,tasting_contest_nomination_winner.tcnw_place,coalesce(product_ml.p_ml_fullname,product_vintage.p_id) as p_ml_fullname, product_vintage.pv_year, product.p_isvintage, select_score.score1 as score1,select_score.score2 as score2,select_score.score3 as score3
            from tasting_contest_nomination
            inner join tasting_contest_nomination_winner on tasting_contest_nomination_winner.tcn_id = tasting_contest_nomination.tcn_id '.($vintage_id?'and tasting_contest_nomination_winner.pv_id = '.(int)$vintage_id:'').'
            inner join product_vintage on product_vintage.pv_id = tasting_contest_nomination_winner.pv_id
            inner join product on product.p_id = product_vintage.p_id

            left join (
                select product_vintage_review.pv_id,round(avg(if(product_vintage_review.user_expert_level = 1,product_vintage_review.pvr_score,null))) as score1,
                        round(avg(if(product_vintage_review.user_expert_level = 2,product_vintage_review.pvr_score,null))) as score2,
                        round(avg(if(product_vintage_review.user_expert_level = 3,product_vintage_review.pvr_score,null))) as score3
                    from product_vintage_review 
                    inner join tasting_contest_tasting on tasting_contest_tasting.t_id = product_vintage_review.t_id and tasting_contest_tasting.tc_id = '.$contest_id.'
                    inner join tasting_contest_nomination on tasting_contest_nomination.tc_id = tasting_contest_tasting.tc_id
                    inner join tasting_contest_nomination_winner on tasting_contest_nomination_winner.tcn_id = tasting_contest_nomination.tcn_id and tasting_contest_nomination_winner.pv_id = product_vintage_review.pv_id
                    where product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_PERSONAL|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE).' = 0 '.($vintage_id?'and product_vintage_review.pv_id = '.(int)$vintage_id:'').'
                    group by product_vintage_review.pv_id
            ) as select_score on select_score.pv_id = product_vintage.pv_id

            left join (select product_ml.p_id,substring_index(group_concat(distinct product_ml.p_ml_id order by product_ml.lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as p_ml_id 
                from tasting_contest_nomination
                inner join tasting_contest_nomination_winner on tasting_contest_nomination_winner.tcn_id = tasting_contest_nomination.tcn_id '.($vintage_id?'and tasting_contest_nomination_winner.pv_id = '.(int)$vintage_id:'').'
                inner join product_vintage on product_vintage.pv_id = tasting_contest_nomination_winner.pv_id
                inner join product_ml on product_ml.p_id = product_vintage.p_id 
                where tasting_contest_nomination.tc_id = '.$contest_id.'
                group by product_ml.p_id
            ) as ln_glue on ln_glue.p_id = product.p_id
            left join product_ml on product_ml.p_ml_id = ln_glue.p_ml_id
            where tasting_contest_nomination.tc_id = '.$contest_id.'
            order by tasting_contest_nomination_winner.tcnw_place asc');
        while($row = $this->XM->sqlcore->getRow($res)){
            $tcn_id = (int)$row['tcn_id'];
            if(!isset($pointers[$tcn_id])){
                continue;
            }
            $result[$pointers[$tcn_id]]['products'][] = array(
                    'id'=>(int)$row['pv_id'],
                    'place'=>(int)$row['tcnw_place'],
                    'fullname'=>$row['p_ml_fullname'].(($row['pv_year']>0)?', '.(int)$row['pv_year']:($row['p_isvintage']?', '.langTranslate('product', 'vintage', 'NV','NV'):'')),
                    'score1'=>($row['score1']!==null)?str_replace('.', ',', ((float)$row['score1'])/100):null,
                    'score2'=>($row['score2']!==null)?str_replace('.', ',', ((float)$row['score2'])/100):null,
                    'score3'=>($row['score3']!==null)?str_replace('.', ',', ((float)$row['score3'])/100):null,
                    'can_remove'=>$can_edit,
                );
        }
        return $result;
    }
    
    public function get_contest_user_access_list($contest_id){
        $contest_id = (int)$contest_id;
        $tasting_contest_user_access_inner_join = '';
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
            $tasting_contest_user_access_inner_join = 'inner join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId().' and tasting_contest_user_access.tcua_owner = 1';
        }
        $res = $this->XM->sqlcore->query('SELECT tasting_contest.tc_status
            from tasting_contest
            '.$tasting_contest_user_access_inner_join.'
            where tasting_contest.tc_id = '.$contest_id.'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return array();
        }
        $can_edit = in_array((int)$row['tc_status'], array(\TASTING\CONTEST_STATUS_DRAFT,\TASTING\CONTEST_STATUS_PREPARATION,\TASTING\CONTEST_STATUS_SUMMING_UP));

        $res = $this->XM->sqlcore->query('SELECT user.user_id, coalesce(user_ml.user_ml_fullname,\'-\') as user_ml_fullname, company_ml.company_id, coalesce(company_ml.company_ml_name,\'-\') as company_ml_name, tasting_contest_user_access.tcua_owner,tasting_counts.cnt as tasting_count
            from tasting_contest_user_access 
            inner join user on user.user_id = tasting_contest_user_access.user_id
            left join (select user_ml.user_id,substring_index(group_concat(user_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as user_ml_id 
                from user_ml 
                inner join tasting_contest_user_access on tasting_contest_user_access.user_id = user_ml.user_id and tasting_contest_user_access.tc_id = '.$contest_id.'
                where user_ml_is_approved = 1 group by user_id
            ) as ln_glue on ln_glue.user_id = user.user_id
            left join user_ml on user_ml.user_ml_id = ln_glue.user_ml_id
            left join (select company_ml.company_id,substring_index(group_concat(company_ml.company_ml_id order by company_ml.lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as company_ml_id 
                from company_ml 
                inner join company on company.company_id = company_ml.company_id and company.company_is_approved = 1 
                inner join user on user.company_id = company.company_id
                inner join tasting_contest_user_access on tasting_contest_user_access.user_id = user.user_id and tasting_contest_user_access.tc_id = '.$contest_id.'
                where company_ml_is_approved = 1 and company_ml_name is not null group by company_ml.company_id
            ) as company_ln_glue on company_ln_glue.company_id = user.company_id
            left join company_ml on company_ml.company_ml_id = company_ln_glue.company_ml_id

            left join (
                    select tasting.user_id,count(distinct tasting_contest_tasting.t_id) as cnt
                        from tasting_contest_tasting
                        inner join tasting on tasting.t_id = tasting_contest_tasting.t_id
                        where tasting_contest_tasting.tc_id = '.$contest_id.'
                        group by tasting.user_id
                ) as tasting_counts on tasting_counts.user_id = tasting_contest_user_access.user_id

            where tasting_contest_user_access.tc_id = '.$contest_id.' 
            order by 2 desc, 3 asc');
        $result = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $is_owner = $row['tcua_owner']?1:0;
            $result[] = array(
                    'id'=>(int)$row['user_id'],
                    'name'=>(string)$row['user_ml_fullname'],
                    'company_id'=>(int)$row['company_id'],
                    'company_name'=>(string)$row['company_ml_name'],
                    'tasting_count'=>(int)$row['tasting_count'],
                    'is_owner'=>$is_owner,
                    'can_remove'=>$can_edit&&!$is_owner
                );
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }
    public function grant_contest_user_access($contest_id,$user_id,&$err){
        $contest_id = (int)$contest_id;
        $user_id = (int)$user_id;

        $tasting_contest_user_access_inner_join = '';
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
            $tasting_contest_user_access_inner_join = 'inner join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId().' and tasting_contest_user_access.tcua_owner = 1';
        }
        $res = $this->XM->sqlcore->query('SELECT tasting_contest.tc_status
            from tasting_contest
            '.$tasting_contest_user_access_inner_join.'
            where tasting_contest.tc_id = '.$contest_id.'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        if(!in_array((int)$row['tc_status'], array(\TASTING\CONTEST_STATUS_DRAFT,\TASTING\CONTEST_STATUS_PREPARATION,\TASTING\CONTEST_STATUS_SUMMING_UP))){
            $err = langTranslate('tasting','err','Contests: You can only edit user access list in draft, preparation or started stages','You can only edit user access list in draft, preparation or started stages');
            return false;
        }
        if(!$this->XM->user->check_user_exists($user_id)){
            $err = langTranslate('tasting', 'err', 'User doesn\'t exist',  'User doesn\'t exist');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT 1 from tasting_contest_user_access where tc_id = '.$contest_id.' and user_id = '.$user_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if($row){
            return true;//already exists
        }
        $this->XM->sqlcore->query('INSERT INTO tasting_contest_user_access (tc_id,user_id) values ('.$contest_id.','.$user_id.')');
        $this->XM->sqlcore->commit();
        return true;
    }
    public function add_contest_tasting($contest_id,$tasting_id,&$err){
        $contest_id = (int)$contest_id;
        $tasting_id = (int)$tasting_id;

        $where = array();
        $where_arr[] = 'tasting.t_id = '.$tasting_id;
        $can_add_to_contest_inner_join = '';
        if($this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
            $can_add_to_contest_inner_join = 'inner join tasting_contest_user_access t_tcua on t_tcua.user_id = tasting.user_id and t_tcua.tc_id = '.$contest_id.'
                inner join tasting_contest on tasting_contest.tc_id = '.$contest_id.' and tasting_contest.tc_status in ('.\TASTING\CONTEST_STATUS_DRAFT.','.\TASTING\CONTEST_STATUS_PREPARATION.','.\TASTING\CONTEST_STATUS_SUMMING_UP.')';
            $where_arr[] = 'tasting.t_status <> '.\TASTING\TASTING_STATUS_DELETED;
        } else {
            $can_add_to_contest_inner_join = 'inner join tasting_contest_user_access t_tcua on t_tcua.user_id = tasting.user_id and t_tcua.tc_id = '.$contest_id.'
                inner join tasting_contest on tasting_contest.tc_id = '.$contest_id.' and tasting_contest.tc_status in ('.\TASTING\CONTEST_STATUS_DRAFT.','.\TASTING\CONTEST_STATUS_PREPARATION.','.\TASTING\CONTEST_STATUS_SUMMING_UP.')
                inner join tasting_contest_user_access cu_tcua on cu_tcua.user_id = '.$this->XM->user->getUserId().' and cu_tcua.tc_id = '.$contest_id;
            $where_arr[] = '( t_tcua.user_id = '.$this->XM->user->getUserId().' or cu_tcua.tcua_owner = 1 )';
            $where_arr[] = 'tasting.t_status <> '.\TASTING\TASTING_STATUS_DELETED;
        }
        $res = $this->XM->sqlcore->query('SELECT tasting_contest.tc_status,tasting.t_status
            from tasting
            '.$can_add_to_contest_inner_join.'
            '.(!empty($where_arr)?'where '.implode(' and ', $where_arr):'').'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        if((int)$row['tc_status']==\TASTING\CONTEST_STATUS_SUMMING_UP && (int)$row['t_status']!=\TASTING\TASTING_STATUS_FINISHED){
            $err = langTranslate('tasting', 'err', 'You can only add finished tastings to contests currently on summing up stage', 'You can only add finished tastings to contests currently on summing up stage');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT 1 from tasting_contest_tasting where tc_id = '.$contest_id.' and t_id = '.$tasting_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if($row){
            return true;//already exists
        }
        $this->XM->sqlcore->query('INSERT INTO tasting_contest_tasting (tc_id,t_id) values ('.$contest_id.','.$tasting_id.')');
        $this->XM->sqlcore->commit();
        return true;
    }
    public function revoke_contest_user_access($contest_id,$user_id,&$err){
        $contest_id = (int)$contest_id;
        $user_id = (int)$user_id;

        $tasting_contest_user_access_inner_join = '';
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
            $tasting_contest_user_access_inner_join = 'inner join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId().' and tasting_contest_user_access.tcua_owner = 1';
        }
        $res = $this->XM->sqlcore->query('SELECT tasting_contest.tc_status
            from tasting_contest
            '.$tasting_contest_user_access_inner_join.'
            where tasting_contest.tc_id = '.$contest_id.'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        if(!in_array((int)$row['tc_status'], array(\TASTING\CONTEST_STATUS_DRAFT,\TASTING\CONTEST_STATUS_PREPARATION,\TASTING\CONTEST_STATUS_SUMMING_UP))){
            $err = langTranslate('tasting','err','Contests: You can only edit user access list in draft, preparation or started stages','You can only edit user access list in draft, preparation or started stages');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT tcua_owner 
            from tasting_contest_user_access
            where tc_id = '.$contest_id.' and user_id = '.$user_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return true;//already revoked
        }
        if($row['tcua_owner']){
            $err = langTranslate('tasting','err','Contests: You can\'t revoke access of contest owner','You can\'t revoke access of contest owner');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT 1 
            from tasting_contest_tasting
            inner join tasting on tasting.t_id = tasting_contest_tasting.t_id
            where tasting_contest_tasting.tc_id = '.$contest_id.' and tasting.user_id = '.$user_id.'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if($row){
            $err = langTranslate('tasting','err','Contests: You can\'t revoke access of the owner of used tastings','You can\'t revoke access of the owner of used tastings');
            return false;
        }
        
        $this->XM->sqlcore->query('DELETE from tasting_contest_user_access where tc_id = '.$contest_id.' and user_id = '.$user_id.' and tcua_owner = 0');
        $this->XM->sqlcore->commit();
        return true;
    }

    public function remove_contest_tasting($contest_id,$tasting_id,&$err){
        $contest_id = (int)$contest_id;
        $tasting_id = (int)$tasting_id;

        $tasting_contest_user_access_select_sql = '1 as tcua_owner';
        $tasting_contest_user_access_inner_join = '';
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
            $tasting_contest_user_access_select_sql = 'tasting_contest_user_access.tcua_owner';
            $tasting_contest_user_access_inner_join = 'inner join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId();
        }

        $res = $this->XM->sqlcore->query('SELECT tasting_contest.tc_status, tasting.user_id, '.$tasting_contest_user_access_select_sql.'
            from tasting_contest
            inner join tasting_contest_tasting on tasting_contest_tasting.tc_id = tasting_contest.tc_id
            inner join tasting on tasting.t_id = tasting_contest_tasting.t_id
            '.$tasting_contest_user_access_inner_join.'
            where tasting_contest.tc_id = '.$contest_id.' and tasting.t_id = '.$tasting_id.'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return true;//already removed
        }
        if(!in_array((int)$row['tc_status'], array(\TASTING\CONTEST_STATUS_DRAFT,\TASTING\CONTEST_STATUS_PREPARATION,\TASTING\CONTEST_STATUS_SUMMING_UP))){
            $err = langTranslate('tasting','err','Contests: You can only edit tasting list in draft, preparation or started stages','You can only edit tasting list in draft, preparation or started stages');
            return false;
        }
        if((int)$row['tcua_owner']!=1 and (int)$row['user_id']!=$this->XM->user->getUserId()){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $this->XM->sqlcore->query('DELETE FROM tasting_contest_tasting where tc_id = '.$contest_id.' and t_id = '.$tasting_id);
        $this->XM->sqlcore->commit();
        return true;
    }

    public function delete_contest($contest_id, &$err){
        $contest_id = (int)$contest_id;
        $tasting_contest_user_access_inner_join = '';
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
            $tasting_contest_user_access_inner_join = 'inner join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId().' and tasting_contest_user_access.tcua_owner = 1';
        }
        $res = $this->XM->sqlcore->query('SELECT tc_status, tc_logo_ext
            from tasting_contest
            '.$tasting_contest_user_access_inner_join.'
            where tc_id = '.$contest_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Access Denied', 'Access Denied');
            return false;
        }
        if(\TASTING\CONTEST_STATUS_DRAFT!=(int)$row['tc_status']){
            $err = langTranslate('tasting', 'err', 'You can only delete draft contests', 'You can only delete draft contests');
            return false;
        }
        if($row['tc_logo_ext'] && file_exists(ABS_PATH.'/modules/Tasting/contestimg/'.$contest_id.'.'.$row['tc_logo_ext'])){
            @unlink(ABS_PATH.'/modules/Tasting/contestimg/'.$contest_id.'.'.$row['tc_logo_ext']);
        }
        $this->XM->sqlcore->query('DELETE from tasting_contest where tc_id = '.$contest_id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function change_contest_status($contest_id, $new_status, &$err){
        $status_list = $this->get_contest_status_list();
        if(!array_key_exists($new_status, $status_list)){
            $err = langTranslate('tasting', 'err', 'Invalid status',  'Invalid status');
            return false;
        }
        $new_status = (int)$new_status;
        $contest_id = (int)$contest_id;
        $tasting_contest_user_access_inner_join = '';
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
            $tasting_contest_user_access_inner_join = 'inner join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId().' and tasting_contest_user_access.tcua_owner = 1';
        }
        $res = $this->XM->sqlcore->query('SELECT tc_status
            from tasting_contest
            '.$tasting_contest_user_access_inner_join.'
            where tc_id = '.$contest_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Access Denied', 'Access Denied');
            return false;
        }
        $old_status = (int)$row['tc_status'];
        if($old_status == $new_status){
            return true;
        }
        switch($new_status){
            case \TASTING\CONTEST_STATUS_DRAFT:
                if($old_status!==\TASTING\CONTEST_STATUS_PREPARATION){
                    $err = langTranslate('tasting', 'err', 'Only preparation stage contests can be converted to draft stage', 'Only preparation stage contests can be converted to draft stage');
                    return false;
                }
                break;
            case \TASTING\CONTEST_STATUS_PREPARATION:
                if($old_status!==\TASTING\CONTEST_STATUS_DRAFT){
                    $err = langTranslate('tasting', 'err', 'Only draft stage contests can be converted to preparation stage', 'Only draft stage contests can be converted to preparation stage');
                    return false;
                }
                break;
            case \TASTING\CONTEST_STATUS_SUMMING_UP:
                if($old_status!==\TASTING\CONTEST_STATUS_PREPARATION){
                    $err = langTranslate('tasting', 'err', 'Only preparation stage contests can be converted to summing up stage', 'Only preparation stage contests can be converted to summing up stage');
                    return false;
                }
                break;
            case \TASTING\CONTEST_STATUS_FINISHED:
                if($old_status!==\TASTING\CONTEST_STATUS_SUMMING_UP){
                    $err = langTranslate('tasting', 'err', 'You can finalize only summed up contests', 'You can finalize only summed up contests');
                    return false;
                }
                break;
            default: //never
                $err = langTranslate('tasting', 'err', 'Invalid status',  'Invalid status');
                return false;
        }
        if($new_status==\TASTING\CONTEST_STATUS_SUMMING_UP || $new_status==\TASTING\CONTEST_STATUS_FINISHED){
            $res = $this->XM->sqlcore->query('SELECT 1
                from tasting
                inner join tasting_contest_tasting on tasting_contest_tasting.t_id = tasting.t_id
                where tasting_contest_tasting.tc_id = '.$contest_id.' and tasting.t_status <> '.\TASTING\TASTING_STATUS_FINISHED.'
                limit 1');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if($row){//has unfinished tastings
                $err = langTranslate('tasting', 'err', 'You have to finish listed tastings before proceeding', 'You have to finish listed tastings before proceeding');
                return false;
            }
        }
        $this->XM->sqlcore->query('UPDATE tasting_contest set tc_status = '.$new_status.' where tc_id = '.$contest_id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function assess_contest($contest_id,$assessment,&$err){
        $contest_id = (int)$contest_id;
        $assessment = $assessment?1:0;
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_APPROVE_CONTEST)){
            $err = langTranslate('tasting', 'err', 'Access Denied', 'Access Denied');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT tc_status, tc_assessment, tc_is_approved
            from tasting_contest
            where tc_id = '.$contest_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Contest doesn\'t exist', 'Contest doesn\'t exist');
            return false;
        }
        if((int)$row['tc_status']!=\TASTING\CONTEST_STATUS_FINISHED || !$row['tc_assessment']){
            $err = langTranslate('tasting', 'err', 'You can only assess finalized public contests', 'You can only assess finalized public contests');
            return false;
        }
        if($row['tc_is_approved']!==null){
            return true;
        }
        if($assessment){
            $res = $this->XM->sqlcore->query('SELECT 1
                from tasting_contest_tasting
                inner join tasting on tasting.t_id = tasting_contest_tasting.t_id
                where tasting_contest_tasting.tc_id = '.$contest_id.' and not (tasting.t_is_approved <=> 1) limit 1');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if($row){
                $err = langTranslate('tasting', 'err', 'You can only approve contests comprising of already approved tastings', 'You can only approve contests comprising of already approved tastings');
                return false;
            }
        }
        $this->XM->sqlcore->query('UPDATE tasting_contest set tc_is_approved = '.$assessment.' where tc_id = '.$contest_id.' and tc_is_approved is null and tc_assessment = 1');
        if($assessment){
            //calculate best nomination positions
            $res = $this->XM->sqlcore->query('SELECT tasting_contest_nomination_winner.pv_id, substring_index(group_concat(tasting_contest_nomination_winner.tcn_id order by tasting_contest_nomination_winner.tcnw_place asc, tasting_contest_nomination.tcn_index asc),\',\',1) as tcn_id, min(tasting_contest_nomination_winner.tcnw_place) as tcnw_place
                from tasting_contest_nomination_winner
                inner join tasting_contest_nomination on tasting_contest_nomination.tcn_id = tasting_contest_nomination_winner.tcn_id
                where tasting_contest_nomination.tc_id = 5
                group by tasting_contest_nomination_winner.pv_id');
            $best_nominations = array();
            $pv_ids = array();
            while($row = $this->XM->sqlcore->getRow($res)){
                $best_nominations[] = array((int)$row['pv_id'],(int)$row['tcn_id'],(int)$row['tcnw_place']);
                $pv_ids[] = (int)$row['pv_id'];
            }
            $this->XM->sqlcore->freeResult($res);
            foreach($best_nominations as $best_nomination){
                $this->XM->sqlcore->query('UPDATE tasting_contest_nomination_winner set tcnw_show_in_product = 1 where pv_id = '.$best_nomination[0].' and tcn_id = '.$best_nomination[1].' and tcnw_place = '.$best_nomination[2]);
            }
            $pv_id_chunks = array_chunk($pv_ids, 50);
            foreach($pv_id_chunks as $pv_id_chunk){
                $this->XM->sqlcore->query('UPDATE product_vintage set pv_won_contest_nominations = 1 where pv_id in ('.implode(',', $pv_id_chunk).') and pv_won_contest_nominations = 0');
            }
        }
        $this->XM->sqlcore->commit();
        return true;
    }
    public function modifyindex_contest_nomination($nomination_id,$direction,&$err){
        $nomination_id = (int)$nomination_id;
        $direction = ($direction==1)?1:-1;

        $tasting_contest_user_access_inner_join = 'inner join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId().' and tasting_contest_user_access.tcua_owner = 1';
        if($this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
            $tasting_contest_user_access_inner_join = '';
        }
        $res = $this->XM->sqlcore->query('SELECT tasting_contest.tc_status, tasting_contest.tc_id, tasting_contest_nomination.tcn_index
            from tasting_contest_nomination
            inner join tasting_contest on tasting_contest.tc_id = tasting_contest_nomination.tc_id
            '.$tasting_contest_user_access_inner_join.'
            where tasting_contest_nomination.tcn_id = '.$nomination_id.'
            limit 1');//only contest owner can add nominations
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        if((int)$row['tc_status']!=\TASTING\CONTEST_STATUS_SUMMING_UP){
            $err = langTranslate('tasting', 'err', 'Contest: You can only edit nominations during the summing up stage',  'You can only edit nominations during the summing up stage');
            return false;
        }

        $contest_id = (int)$row['tc_id'];
        $index = (int)$row['tcn_index'];

        if($direction==1){
            $res = $this->XM->sqlcore->query('SELECT tasting_contest_nomination.tcn_id,tasting_contest_nomination.tcn_index
                from tasting_contest_nomination 
                where tasting_contest_nomination.tc_id = '.$contest_id.' and tasting_contest_nomination.tcn_index > '.$index.'
                order by tasting_contest_nomination.tcn_index asc
                limit 1');
        } else {
            $res = $this->XM->sqlcore->query('SELECT tasting_contest_nomination.tcn_id,tasting_contest_nomination.tcn_index
                from tasting_contest_nomination 
                where tasting_contest_nomination.tc_id = '.$contest_id.' and tasting_contest_nomination.tcn_index < '.$index.'
                order by tasting_contest_nomination.tcn_index desc
                limit 1');
        }
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){//nothing to swap with
            return true;
        }
        $swap_tcn_id = (int)$row['tcn_id'];
        $swap_tcn_index = (int)$row['tcn_index'];

        $this->XM->sqlcore->query('UPDATE tasting_contest_nomination set tcn_index = '.$swap_tcn_index.' where tcn_id = '.$nomination_id);
        $this->XM->sqlcore->query('UPDATE tasting_contest_nomination set tcn_index = '.$index.' where tcn_id = '.$swap_tcn_id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function get_nomination_next_place($nomination_id){
        $nomination_id = (int)$nomination_id;
        $suggested_place = 1;
        $res = $this->XM->sqlcore->query('SELECT distinct tcnw_place from tasting_contest_nomination_winner where tcn_id = '.$nomination_id.' and tcnw_place between 1 and 10 order by tcnw_place asc');
        while($row = $this->XM->sqlcore->getRow($res)){
            if((int)$row['tcnw_place']!=$suggested_place){
                break;
            }
            $suggested_place++;
        }
        $this->XM->sqlcore->freeResult($res);
        if($suggested_place>10){
            $suggested_place = null;
        }
        return $suggested_place;
    }
    public function add_contest_nomination_winner($nomination_id,$vintage_id,$place,&$err){
        $nomination_id = (int)$nomination_id;
        $vintage_id = (int)$vintage_id;
        $place = (int)$place;

        $tasting_contest_user_access_inner_join = 'inner join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId().' and tasting_contest_user_access.tcua_owner = 1';
        if($this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
            $tasting_contest_user_access_inner_join = '';
        }
        $res = $this->XM->sqlcore->query('SELECT tasting_contest.tc_status, tasting_product_vintage.tpv_id, tasting_contest_nomination_winner.tcnw_place
            from tasting_contest_nomination
            inner join tasting_contest on tasting_contest.tc_id = tasting_contest_nomination.tc_id
            '.$tasting_contest_user_access_inner_join.'
            inner join tasting_contest_tasting on tasting_contest_tasting.tc_id = tasting_contest.tc_id
            left join tasting_product_vintage on tasting_product_vintage.t_id = tasting_contest_tasting.t_id and tasting_product_vintage.pv_id = '.$vintage_id.'
            left join tasting_contest_nomination_winner on tasting_contest_nomination_winner.pv_id = tasting_product_vintage.pv_id and tasting_contest_nomination_winner.tcn_id = tasting_contest_nomination.tcn_id
            where tasting_contest_nomination.tcn_id = '.$nomination_id.'
            order by tasting_product_vintage.tpv_id desc
            limit 1');//only contest owner can add nominations
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        if((int)$row['tc_status']!=\TASTING\CONTEST_STATUS_SUMMING_UP){
            $err = langTranslate('tasting', 'err', 'Contest: You can only edit nominations during the summing up stage',  'You can only edit nominations during the summing up stage');
            return false;
        }
        if(!$row['tpv_id']){
            $err = langTranslate('tasting', 'err', 'Vintage doesn\'t exist',  'Vintage doesn\'t exist');
            return false;
        }
        if($place > 100 or $place <= 0){
            $err = formatReplace(langTranslate('tasting', 'err', '@1 field value is invalid',  '@1 field value is invalid'),
                langTranslate('tasting', 'contest', 'Nomination Winner: Place','Place'));
            return false;
        }
        if($row['tcnw_place']!==null){
            //update
            if((int)$row['tcnw_place']!=$place){
                $this->XM->sqlcore->query('UPDATE tasting_contest_nomination_winner SET tcnw_place = '.$place.' WHERE tcn_id = '.$nomination_id.' and pv_id = '.$vintage_id);
                $this->XM->sqlcore->commit();
            }
        } else {
            //insert
            $this->XM->sqlcore->query('INSERT INTO tasting_contest_nomination_winner (tcn_id,pv_id,tcnw_place) VALUES ('.$nomination_id.','.$vintage_id.','.$place.')');
            $this->XM->sqlcore->commit();
        }

        return true;
    }
    public function remove_contest_nomination_winner($nomination_id,$vintage_id,&$err){
        $nomination_id = (int)$nomination_id;
        $vintage_id = (int)$vintage_id;

        $tasting_contest_user_access_inner_join = 'inner join tasting_contest_user_access on tasting_contest_user_access.tc_id = tasting_contest.tc_id and tasting_contest_user_access.user_id = '.$this->XM->user->getUserId().' and tasting_contest_user_access.tcua_owner = 1';
        if($this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_CONTESTS)){
            $tasting_contest_user_access_inner_join = '';
        }
        $res = $this->XM->sqlcore->query('SELECT tasting_contest.tc_status
            from tasting_contest_nomination
            inner join tasting_contest on tasting_contest.tc_id = tasting_contest_nomination.tc_id
            '.$tasting_contest_user_access_inner_join.'
            where tasting_contest_nomination.tcn_id = '.$nomination_id.'
            limit 1');//only contest owner can add nominations
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('tasting', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        if((int)$row['tc_status']!=\TASTING\CONTEST_STATUS_SUMMING_UP){
            $err = langTranslate('tasting', 'err', 'Contest: You can only edit nominations during the summing up stage',  'You can only edit nominations during the summing up stage');
            return false;
        }
        $this->XM->sqlcore->query('DELETE FROM tasting_contest_nomination_winner where tcn_id = '.$nomination_id.' and pv_id = '.$vintage_id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function get_vintage_contest_nomination_list($vintage_id){
        $vintage_id = (int)$vintage_id;
        $result = array();
        $resultkeys = array();
        $res = $this->XM->sqlcore->query('SELECT tasting_contest.tc_id,tasting_contest.tc_name,tasting_contest.tc_location,tasting_contest.tc_logo_ext,tasting_contest_timeframe.tc_start_ts,tasting_contest_nomination.tcn_name,tasting_contest_nomination_winner.tcnw_place,select_score.score3
            from tasting_contest_nomination_winner
            inner join tasting_contest_nomination on tasting_contest_nomination.tcn_id = tasting_contest_nomination_winner.tcn_id
            inner join tasting_contest on tasting_contest.tc_id = tasting_contest_nomination.tc_id and tasting_contest.tc_is_approved = 1 and tasting_contest.tc_status = '.\TASTING\CONTEST_STATUS_FINISHED.'
            left join (
                    select tasting_contest_tasting.tc_id,min(tasting.t_start_ts) as tc_start_ts
                        from tasting_contest_nomination_winner
                        inner join tasting_contest_nomination on tasting_contest_nomination.tcn_id = tasting_contest_nomination_winner.tcn_id
                        inner join tasting_contest on tasting_contest.tc_id = tasting_contest_nomination.tc_id and tasting_contest.tc_is_approved = 1 and tasting_contest.tc_status = '.\TASTING\CONTEST_STATUS_FINISHED.'
                        inner join tasting_contest_tasting on tasting_contest_tasting.tc_id = tasting_contest.tc_id
                        inner join tasting on tasting.t_id = tasting_contest_tasting.t_id
                        where tasting_contest_nomination_winner.pv_id = '.$vintage_id.' and tasting_contest_nomination_winner.tcnw_show_in_product = 1
                        group by tasting_contest_tasting.tc_id
                ) as tasting_contest_timeframe on tasting_contest_timeframe.tc_id = tasting_contest.tc_id
            left join (
                select tasting_contest_nomination.tc_id,round(avg(if(product_vintage_review.user_expert_level = 3,product_vintage_review.pvr_score,null))) as score3
                    from product_vintage_review 
                    inner join tasting_contest_tasting on tasting_contest_tasting.t_id = product_vintage_review.t_id
                    inner join tasting_contest_nomination on tasting_contest_nomination.tc_id = tasting_contest_tasting.tc_id
                    inner join tasting_contest_nomination_winner on tasting_contest_nomination_winner.tcn_id = tasting_contest_nomination.tcn_id and tasting_contest_nomination_winner.pv_id = product_vintage_review.pv_id and tasting_contest_nomination_winner.tcnw_show_in_product = 1
                    where product_vintage_review.pv_id = '.$vintage_id.' and product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_PERSONAL|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE).' = 0
                    group by tasting_contest_nomination.tc_id
            ) as select_score on select_score.tc_id = tasting_contest.tc_id
            where tasting_contest_nomination_winner.pv_id = '.$vintage_id.' and tasting_contest_nomination_winner.tcnw_show_in_product = 1
            order by tasting_contest_timeframe.tc_start_ts desc, tasting_contest_nomination_winner.tcnw_place asc');
        while($row = $this->XM->sqlcore->getRow($res)){
            $contest_id = (int)$row['tc_id'];
            if(!isset($resultkeys[$contest_id])){
                $resultkeys[$contest_id] = count($result);
                $tc_start_ts = (int)$row['tc_start_ts'];
                $result[] = array(
                        'id'=>$contest_id,
                        'name'=>preg_replace('#(?:^,|,$)#', '', preg_replace('#(?:\s+,|,(?:\s*,)+)#', ',', preg_replace('#\s{2,}#', ' ', trim((string)$row['tc_name'].', '.(string)$row['tc_location'].($tc_start_ts?', '.date('d.m.Y',$tc_start_ts):''))))),
                        'logourl'=>$row['tc_logo_ext']?BASE_URL.'/modules/Tasting/contestimg/'.$contest_id.'.'.$row['tc_logo_ext']:null,
                        'nominations'=>array(),
                        
                    );
            }
            $result[$resultkeys[$contest_id]]['nominations'][] = array(
                    'name'=>trim($row['tcn_name']),
                    'place'=>(int)$row['tcnw_place'],
                    'score3'=>($row['score3']!==null)?str_replace('.', ',', ((float)$row['score3'])/100):null,
                );
            
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }










    
}