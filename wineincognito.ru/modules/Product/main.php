<?php
namespace Product;
if(!defined('IS_XMODULE')){
    exit();
}

DEFINE('PRODUCT\PRODUCT_FILTER_ONLY_USED',1);
DEFINE('PRODUCT\PRODUCT_FILTER_ONLY_BLANK',2);
DEFINE('PRODUCT\PRODUCT_FILTER_ONLY_WAITING_FOR_APPROVAL',64);
DEFINE('PRODUCT\PRODUCT_FILTER_ONLY_SCORED',4);
DEFINE('PRODUCT\PRODUCT_FILTER_ONLY_AWARDED',256);
DEFINE('PRODUCT\PRODUCT_FILTER_ONLY_MY_FAVOURITES',8);
DEFINE('PRODUCT\PRODUCT_FILTER_ONLY_COMPANY_FAVOURITES',16);
DEFINE('PRODUCT\PRODUCT_FILTER_ONLY_USED_SHOW_PROXIMITY',32);
DEFINE('PRODUCT\PRODUCT_FILTER_ONLY_PERSONALLY_SCORED',128);


DEFINE('PRODUCT\PRODUCT_ATTRVAL_TREE_FOR_VINTAGE',1);
DEFINE('PRODUCT\PRODUCT_ATTRVAL_TREE_IS_NOT_BLEND',2);

DEFINE('PRODUCT\PVR_BLOCK_ONGOING_TASTING',1);
DEFINE('PRODUCT\PVR_BLOCK_BY_MODERATOR',2);
DEFINE('PRODUCT\PVR_BLOCK_FAULTY_OR_MISSED',4);
DEFINE('PRODUCT\PVR_BLOCK_PERSONAL',8);
DEFINE('PRODUCT\PVR_BLOCK_SOLITARY_REVIEW',16);
DEFINE('PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE',32);
DEFINE('PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE',64);

DEFINE('PRODUCT\SCORE_NOT_ACCURATE_DELTA',5);

DEFINE('PRODUCT\COLOR_ATTRIBUTE_GROUP_ID',4);//used in vintage lists
DEFINE('PRODUCT\COLOR_ATTRIBUTE_GROUP_IS_OVERLOAD',false);//used in vintage lists
DEFINE('PRODUCT\VOLUME_ATTRIBUTE_ID',48);//used in api

require_once ABS_PATH.'/interface/main.php';

class Main extends \AbstractNS\Main{

    public function get_attr_group_list(){
        $attrgrouplist = array();
        $res = $this->XM->sqlcore->query('SELECT product_attribute_group.pag_id, if(pagd.pag_id,1,0) as required, coalesce(product_attribute_group_ml.pag_ml_name,\'-\') as pag_ml_name,product_attribute_group.pag_ishidden,product_attribute_group.pag_zindex
            from product_attribute_group
            left join (select pag_id,substring_index(group_concat(pag_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pag_ml_id from product_attribute_group_ml where pag_ml_name is not null group by pag_id) as ln_glue on ln_glue.pag_id = product_attribute_group.pag_id
            left join product_attribute_group_ml on product_attribute_group_ml.pag_ml_id = ln_glue.pag_ml_id
            left join (
                SELECT distinct pag_id
                from product_attribute_group_dependency
                where pagd_required = 1
                ) pagd on pagd.pag_id = product_attribute_group.pag_id
            order by product_attribute_group.pag_zindex asc');
        $can_edit = $this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES);
        while($row = $this->XM->sqlcore->getRow($res)){
            $id = (int)$row['pag_id'];
            $attrgrouplist[] = array(
                    'id'=>$id,
                    'name'=>(string)$row['pag_ml_name'],
                    'zindex'=>(int)$row['pag_zindex'],
                    'is_hidden'=>(bool)$row['pag_ishidden'],

                    'can_hide'=>(bool)$can_edit&&!$row['required'],
                    'can_edit'=>(bool)$can_edit,
                );
        }
        $this->XM->sqlcore->freeResult($res);
        return $attrgrouplist;
    }

    public function add_attrgroup($name, $visible, $required, $doublecheck, $used_in_filter, $overload, $multiple, $analog, $zindex, &$err){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $used_in_filter = $used_in_filter?1:0;
        $overload = $overload?1:0;
        $multiple = $multiple?1:0;
        $analog = $analog?1:0;
        $zindex = (int)$zindex;
        if($zindex<0){
            $zindex = 0;
        }
        if($zindex>999999){
            $zindex = 999999;
        }
        $this->XM->sqlcore->query('INSERT INTO product_attribute_group (pag_used_in_filter, pag_overload, pag_multiple, pag_analog, pag_zindex) VALUES ('.$used_in_filter.','.$overload.','.$multiple.','.$analog.','.$zindex.')');
        $attrgroup_id = $this->XM->sqlcore->lastInsertId();
        $this->XM->sqlcore->commit();
        $this->edit_attrgroup($attrgroup_id, $name, $visible, $required, $doublecheck, $used_in_filter, $overload, $multiple, $analog, $zindex, $err);
        return $attrgroup_id;
    }

    public function edit_attrgroup($attrgroup_id, $name, $visible, $required, $doublecheck, $used_in_filter, $overload, $multiple, $analog, $zindex, &$err){
        $attrgroup_id = (int)$attrgroup_id;
        if($attrgroup_id<=0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $used_in_filter = $used_in_filter?1:0;
        $overload = $overload?1:0;
        $multiple = $multiple?1:0;
        $analog = $analog?1:0;
        if($attrgroup_id == \PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID){
            $used_in_filter = 1;
            $multiple = 0;
            $overload = 0;
            $visible = array();
            $required = array();
            $doublecheck = array();
        }
        $zindex = (int)$zindex;
        if($zindex<0){
            $zindex = 0;
        }
        if($zindex>999999){
            $zindex = 999999;
        }
        $languageIdList = $this->XM->lang->getLanguageIdList();
        $res = $this->XM->sqlcore->query('SELECT pag_used_in_filter,pag_overload,pag_multiple,pag_analog,pag_zindex,pag_system from product_attribute_group where pag_id = '.$attrgroup_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Attr Group doesn\'t exist',  'Attr Group doesn\'t exist');
            return false;
        }
        $pag_system = (bool)$row['pag_system'];
        $update_arr = array();
        if($used_in_filter!=$row['pag_used_in_filter']){
            $update_arr[] = 'pag_used_in_filter = '.$used_in_filter;
        }
        if($overload!=$row['pag_overload']){
            $update_arr[] = 'pag_overload = '.$overload;
        }
        if($multiple!=$row['pag_multiple']){
            $update_arr[] = 'pag_multiple = '.$multiple;
        }
        if($analog!=$row['pag_analog']){
            $update_arr[] = 'pag_analog = '.$analog;
        }
        if($zindex!=$row['pag_zindex']){
            $update_arr[] = 'pag_zindex = '.$zindex;
        }
        if(!empty($update_arr)){
            $this->XM->sqlcore->query('UPDATE product_attribute_group set '.implode(',', $update_arr).' where pag_id = '.$attrgroup_id);
            $this->XM->sqlcore->commit();
        }
        $ml_variants = array();
        $res = $this->XM->sqlcore->query('SELECT pag_ml_name, lang_id, pag_ml_id from product_attribute_group_ml where pag_id = '.$attrgroup_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $lang_id = (int)$row['lang_id'];
            if(!isset($ml_variants[$lang_id])){
                $ml_variants[$lang_id] = array();
            }
            $ml_variants[$lang_id][] = array('name'=>$row['pag_ml_name'],'id'=>$row['pag_ml_id']);
        }
        $this->XM->sqlcore->freeResult($res);

        foreach($languageIdList as $lang_id){
            $lang_name = getLangArrayVal($name,$lang_id);
            if(isset($ml_variants[$lang_id])){
                foreach($ml_variants[$lang_id] as $ml_variant){
                    if($lang_name==$ml_variant['name']){
                        continue 2;//same values, no need to insert/update
                    }
                }
            }
            if(mb_strlen($lang_name, 'UTF-8')>128){
                $err = formatReplace(langTranslate('product', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('product', 'attrgroup', 'Name', 'Name'),
                    128);
                return false;
            }
            $insertkeys = array();
            $insertvals = array();
            if(strlen($lang_name)){
                $insertkeys[] = 'pag_ml_name';
                $insertvals[] = '\''.$this->XM->sqlcore->prepString($lang_name,128).'\'';
            }
            if(empty($insertkeys)){
                continue;
            }
            $insertkeys[] = 'pag_id';
            $insertvals[] = $attrgroup_id;
            $insertkeys[] = 'lang_id';
            $insertvals[] = $lang_id;
            $this->XM->sqlcore->query('DELETE FROM product_attribute_group_ml where pag_id = '.$attrgroup_id.' and lang_id = '.$lang_id);
            $this->XM->sqlcore->query('INSERT INTO product_attribute_group_ml ('.implode(',', $insertkeys).') VALUES ('.implode(',', $insertvals).')');
            $this->XM->sqlcore->commit();
        }
        //dependencies
        if(!$pag_system){
            if(!is_array($doublecheck)){
                $doublecheck = array();
            }
            if(!is_array($required)){
                $required = array();
            }
            if(!is_array($visible)){
                $visible = array();
            }
            if($attrgroup_id==\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID){
                $res = $this->XM->sqlcore->query('SELECT distinct product_attribute_value.pav_id
                    from product_attribute_value
                    inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pag_id = '.\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID);
                while($row = $this->XM->sqlcore->getRow($res)){
                    $pav_id = (int)$row['pav_id'];
                    $doublecheck[] = $pav_id;
                    $required[] = $pav_id;
                }
                $this->XM->sqlcore->freeResult($res);
                $visible = array();
            }
            $foundation_attribute_ids = $this->__get_foundation_attributes(array_unique(array_merge($doublecheck,$required,$visible)));//array() as default

            $has_doublecheck = false;
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE doublecheck_temp_tbl (
                    pav_id BIGINT UNSIGNED NOT NULL
                )');
            $distinct_ids = array();
            foreach($doublecheck as $doublecheck_id){
                $doublecheck_id = (int)$doublecheck_id;
                if(!in_array($doublecheck_id, $distinct_ids) && in_array($doublecheck_id, $foundation_attribute_ids)){
                    $distinct_ids[] = $doublecheck_id;
                    $this->XM->sqlcore->query('INSERT INTO doublecheck_temp_tbl (pav_id) VALUES ('.$doublecheck_id.')');
                    $has_doublecheck = true;
                }
            }
            unset($doublecheck);


            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE required_temp_tbl (
                    pav_id BIGINT UNSIGNED NOT NULL
                )');
            $distinct_ids = array();
            foreach($required as $required_id){
                $required_id = (int)$required_id;
                if(!in_array($required_id, $distinct_ids) && in_array($required_id, $foundation_attribute_ids)){
                    $distinct_ids[] = $required_id;
                    $this->XM->sqlcore->query('INSERT INTO required_temp_tbl (pav_id) VALUES ('.$required_id.')');
                }
            }
            unset($required);


            $always_visible = true;
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE visible_temp_tbl (
                    pav_id BIGINT UNSIGNED NOT NULL
                )');
            $distinct_ids = array();
            foreach($visible as $visible_id){
                $visible_id = (int)$visible_id;
                if(!in_array($visible_id, $distinct_ids) && in_array($visible_id, $foundation_attribute_ids)){
                    $distinct_ids[] = $visible_id;
                    $this->XM->sqlcore->query('INSERT INTO visible_temp_tbl (pav_id) VALUES ('.$visible_id.')');
                    $always_visible = false;
                }
            }
            unset($visible);
            unset($foundation_attribute_ids);
            unset($distinct_ids);

            if($always_visible){
                $this->XM->sqlcore->query('UPDATE product_attribute_group set pag_always_visible = 1 where pag_id = '.$attrgroup_id.' and pag_always_visible = 0');
            } else {
                $this->XM->sqlcore->query('UPDATE product_attribute_group set pag_always_visible = 0 where pag_id = '.$attrgroup_id.' and pag_always_visible = 1');
            }
            if($has_doublecheck){
                $this->XM->sqlcore->query('UPDATE product_attribute_group set pag_ishidden = 0 where pag_id = '.$attrgroup_id.' and pag_ishidden = 1');
            }

            $this->XM->sqlcore->query('DELETE FROM product_attribute_group_dependency where pag_id = '.$attrgroup_id);
            $this->XM->sqlcore->query('INSERT INTO product_attribute_group_dependency (pag_id,pav_id,pagd_visible,pagd_required,pagd_doublecheck)
                SELECT '.$attrgroup_id.' as pag_id, product_attribute_value.pav_id, if(doublecheck_temp_tbl.pav_id is null && required_temp_tbl.pav_id is null && visible_temp_tbl.pav_id is null,product_attribute_group.pag_always_visible,1) as pagd_visible, if(doublecheck_temp_tbl.pav_id is null && required_temp_tbl.pav_id is null,0,1) as pagd_required, if(doublecheck_temp_tbl.pav_id is null,0,1) as pagd_doublecheck
                    from product_attribute_value
                    inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pag_id = '.\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID.'
                    inner join product_attribute_group on product_attribute_group.pag_id = '.$attrgroup_id.'
                    left join visible_temp_tbl on visible_temp_tbl.pav_id = product_attribute_value.pav_id
                    left join required_temp_tbl on required_temp_tbl.pav_id = product_attribute_value.pav_id
                    left join doublecheck_temp_tbl on doublecheck_temp_tbl.pav_id = product_attribute_value.pav_id');
            $this->XM->sqlcore->commit();
        }
        return true;
    }
    public function hide_attrgroup($attrgroup_id, $changeTo, &$err){
        $attrgroup_id = (int)$attrgroup_id;
        $changeTo = $changeTo?1:0;
        if($attrgroup_id<=0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        // if($attrgroup_id==\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID){
        //     $err = langTranslate('product', 'err', 'You can\'t hide foundation attribute group',  'You can\'t hide foundation attribute group');
        //     return false;
        // }

        $res = $this->XM->sqlcore->query('SELECT product_attribute_group.pag_ishidden,coalesce(product_attribute_group_dependency.pagd_required,0) as required
            from product_attribute_group
            left join product_attribute_group_dependency on product_attribute_group_dependency.pag_id = product_attribute_group.pag_id and product_attribute_group_dependency.pagd_required = 1
            where product_attribute_group.pag_id = '.$attrgroup_id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Attr Group doesn\'t exist',  'Attr Group doesn\'t exist');
            return false;
        }
        if($row['pag_ishidden']==$changeTo){
            return true;
        }
        if($row['required'] && $changeTo){
            $err = langTranslate('product', 'err', 'You can\'t hide required groups',  'You can\'t hide required groups');
            return false;
        }
        $this->XM->sqlcore->query('UPDATE product_attribute_group set pag_ishidden = '.$changeTo.' where pag_id = '.$attrgroup_id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function get_attrgroup_info_for_all_languages($attrgroup_id){
        $attrgroup_id = (int)$attrgroup_id;
        if($attrgroup_id<=0){//invalid attrgroup_id
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT product_attribute_group.pag_used_in_filter, product_attribute_group.pag_overload, product_attribute_group.pag_multiple, product_attribute_group.pag_analog, product_attribute_group.pag_system, product_attribute_group.pag_always_visible, product_attribute_group.pag_zindex, product_attribute_group_ml.pag_ml_name, product_attribute_group_ml.lang_id
            from product_attribute_group
            left join product_attribute_group_ml on product_attribute_group_ml.pag_id = product_attribute_group.pag_id
            where product_attribute_group.pag_id = '.$attrgroup_id);
        $result = array(
                'only_visible'=>array(),
                'required'=>array(),
                'doublecheck'=>array(),
                'used_in_filter'=>0,
                'overload'=>0,
                'multiple'=>0,
                'analog'=>0,
                'system'=>0,
                'zindex'=>10000,
                'name'=>array(),
            );
        $always_visible = false;
        $system = false;
        $first_iteration_flag = false;
        while($row = $this->XM->sqlcore->getRow($res)){
            if(!$first_iteration_flag){
                $always_visible = (bool)$row['pag_always_visible'];
                $result['used_in_filter'] = $row['pag_used_in_filter']?1:0;
                $result['overload'] = $row['pag_overload']?1:0;
                $result['multiple'] = $row['pag_multiple']?1:0;
                $result['analog'] = $row['pag_analog']?1:0;
                $result['system'] = $system = $row['pag_system']?1:0;
                $result['zindex'] = (int)$row['pag_zindex'];
                $first_iteration_flag = true;
            }
            $lang_id = (int)$row['lang_id'];
            $result['name'][$lang_id] = (string)$row['pag_ml_name'];
        }
        $this->XM->sqlcore->freeResult($res);
        if(!$system && $attrgroup_id!=\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID){
            $res = $this->XM->sqlcore->query('SELECT pav_id,pagd_visible,pagd_required,pagd_doublecheck from product_attribute_group_dependency where pag_id = '.$attrgroup_id);
            while($row = $this->XM->sqlcore->getRow($res)){
                $pav_id = (int)$row['pav_id'];
                if(!$always_visible && $row['pagd_visible']){
                    $result['only_visible'][] = $pav_id;
                }
                if($row['pagd_required']){
                    $result['required'][] = $pav_id;
                }
                if($row['pagd_doublecheck']){
                    $result['doublecheck'][] = $pav_id;
                }
            }
            $this->XM->sqlcore->freeResult($res);
        }

        return $result;
    }
    public function get_attrgroup_info($attrgroup_id){
        $attrgroup_id = (int)$attrgroup_id;
        if($attrgroup_id<=0){//invalid attrgroup_id
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT pag.pag_id, coalesce(pag_ml.pag_ml_name,\'-\') as pag_ml_name,pag.pag_ishidden
            from product_attribute_group pag
            left join (select product_attribute_group.pag_id,substring_index(group_concat(pag_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pag_ml_id from product_attribute_group_ml inner join product_attribute_group on product_attribute_group.pag_id = product_attribute_group_ml.pag_id and product_attribute_group.pag_id = '.$attrgroup_id.' where pag_ml_name is not null group by product_attribute_group.pag_id) as ln_glue on ln_glue.pag_id = pag.pag_id
            left join product_attribute_group_ml pag_ml on pag_ml.pag_ml_id = ln_glue.pag_ml_id
            where pag.pag_id = '.$attrgroup_id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        $can_edit = $this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES);
        $result = array(
                'id'=>(int)$row['pag_id'],
                'name'=>(string)$row['pag_ml_name'],
                'is_hidden'=>(bool)$row['pag_ishidden'],

                'can_hide'=>(bool)$can_edit,
                'can_edit'=>(bool)$can_edit,
            );
        return $result;
    }
    public function get_attr_list($attrgroup_id, $show_hidden = true, $show_hierarchy = true){
        $attrgroup_id = (int)$attrgroup_id;
        if($attrgroup_id<=0){//invalid attrgroup_id
            return false;
        }
        $hierarchy_select = 'pa_ml.pa_ml_name as pa_ml_name';
        $hierarchy_join = 'left join (
                select product_attribute.pa_id,substring_index(group_concat(pa_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pa_ml_id
                    from product_attribute_ml
                    inner join product_attribute on product_attribute.pa_id = product_attribute_ml.pa_id and product_attribute.pag_id = '.$attrgroup_id.'
                    where pa_ml_name is not null
                    group by product_attribute.pa_id
                ) as ln_glue on ln_glue.pa_id = pa.pa_id
            left join product_attribute_ml pa_ml on pa_ml.pa_ml_id = ln_glue.pa_ml_id';
        $hierarchy_group = '';
        if($show_hierarchy){
            $hierarchy_select = 'group_concat(coalesce(papart_ml.pa_ml_name,\'-\') order by papart.pa_depth asc separator \' / \') as pa_ml_name';
            $hierarchy_join = 'inner join product_attribute_tree pat on pat.pa_id = pa.pa_id
            inner join product_attribute papart on papart.pa_id = pat.pa_anc_id
            left join (
                select product_attribute.pa_id,substring_index(group_concat(pa_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pa_ml_id
                    from product_attribute_ml
                    inner join product_attribute on product_attribute.pa_id = product_attribute_ml.pa_id and product_attribute.pag_id = '.$attrgroup_id.'
                    where pa_ml_name is not null
                    group by product_attribute.pa_id
                ) as ln_glue on ln_glue.pa_id = papart.pa_id
            left join product_attribute_ml papart_ml on papart_ml.pa_ml_id = ln_glue.pa_ml_id';
            $hierarchy_group = 'group by pa.pa_id, pa.pa_ishidden';
        }
        $attrlist = array();

        $res = $this->XM->sqlcore->query('SELECT pa.pa_id, pa.pa_ishidden, '.$hierarchy_select.'
            from product_attribute pa
            '.$hierarchy_join.'
            where pa.pag_id = '.$attrgroup_id.' '.(!$show_hidden?' and pa.pa_ishidden = 0':'').'
            '.$hierarchy_group.'
            order by pa.pa_depth asc, 3 asc');
        $can_edit = $this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES);
        while($row = $this->XM->sqlcore->getRow($res)){
            $attrlist[] = array(
                    'id'=>(int)$row['pa_id'],
                    'name'=>(string)$row['pa_ml_name'],
                    'is_hidden'=>(bool)$row['pa_ishidden'],

                    'can_hide'=>(bool)$can_edit,
                    'can_edit'=>(bool)$can_edit,
                );
        }
        $this->XM->sqlcore->freeResult($res);
        return $attrlist;
    }
    public function get_possible_attr_parent_list($attrgroup_id,$attr_id = 0){
        $attrgroup_id = (int)$attrgroup_id;
        if($attrgroup_id<=0){//invalid attrgroup_id
            return false;
        }
        $attr_id = (int)$attr_id;
        if($attr_id<0){//invalid attr_id
            return false;
        }
        $attrgroup = $this->get_attrgroup_info($attrgroup_id);
        if(!$attrgroup){
            return false;
        }
        $attrparentlist = array(array('id'=>0,'name'=>$attrgroup['name']));
        $res = $this->XM->sqlcore->query('SELECT pa.pa_id, group_concat(coalesce(papart_ml.pa_ml_name,\'-\') order by papart.pa_depth asc separator \' / \') as pa_ml_name
            from product_attribute pa
            inner join product_attribute_tree pat on pat.pa_id = pa.pa_id
            inner join product_attribute papart on papart.pa_id = pat.pa_anc_id
            left join (select product_attribute.pa_id,substring_index(group_concat(pa_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pa_ml_id from product_attribute_ml inner join product_attribute on product_attribute.pa_id = product_attribute_ml.pa_id and product_attribute.pag_id = '.$attrgroup_id.' where pa_ml_name is not null group by product_attribute.pa_id) as ln_glue on ln_glue.pa_id = papart.pa_id
            left join product_attribute_ml papart_ml on papart_ml.pa_ml_id = ln_glue.pa_ml_id
            left join product_attribute_tree childcheck on childcheck.pa_id = pa.pa_id and childcheck.pa_anc_id = '.$attr_id.'
            where pa.pag_id = '.$attrgroup_id.' and childcheck.pa_id is null
            group by pa.pa_id
            order by 2 asc');
        while($row = $this->XM->sqlcore->getRow($res)){
            $attrparentlist[] = array(
                    'id'=>(int)$row['pa_id'],
                    'name'=>(string)($attrgroup['name'].' / '.$row['pa_ml_name']),
                );
        }
        $this->XM->sqlcore->freeResult($res);
        return $attrparentlist;
    }
    public function add_attr($attrgroup_id, $parent_id, $name, $show_only_origin, $has_important, &$err){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $attrgroup_id = (int)$attrgroup_id;
        if($attrgroup_id<=0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        $parent_id = (int)$parent_id;
        if($parent_id<0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        $depth = 0;
        if($parent_id>0){
            $res = $this->XM->sqlcore->query('SELECT pa_depth FROM product_attribute WHERE pa_id = '.$parent_id.' and pag_id = '.$attrgroup_id.' LIMIT 1');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if(!$row){
                $err = langTranslate('product', 'err', 'Parent Attr doesn\'t exist',  'Parent Attr doesn\'t exist');
                return false;
            }
            $depth = (int)$row['pa_depth']+1;
        }
        $this->XM->sqlcore->query('INSERT INTO product_attribute (pag_id,pa_parent_id,pa_depth) VALUES ('.$attrgroup_id.','.$parent_id.','.$depth.')');
        $attr_id = $this->XM->sqlcore->lastInsertId();
        $this->XM->sqlcore->commit();
        $this->edit_attr($attr_id, $parent_id, $name, $show_only_origin, $has_important, $err);
        return $attr_id;
    }

    public function edit_attr($attr_id, $parent_id, $name, $show_only_origin, $has_important, &$err){
        $attr_id = (int)$attr_id;
        if($attr_id<=0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        $parent_id = (int)$parent_id;
        if($parent_id<0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        $show_only_origin = $show_only_origin?1:0;
        $has_important = $has_important?1:0;
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT pa_depth,pa_parent_id,pag_id,pa_show_only_origin,pa_has_important_values from product_attribute where pa_id = '.$attr_id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Attr doesn\'t exist',  'Attr doesn\'t exist');
            return false;
        }
        $updatearr = array();
        if($row['pa_show_only_origin']!=$show_only_origin){
            $updatearr[] = 'pa_show_only_origin = '.$show_only_origin;
        }
        if($row['pa_has_important_values']!=$has_important){
            $updatearr[] = 'pa_has_important_values = '.$has_important;
        }
        if(!empty($updatearr)){
            $this->XM->sqlcore->query('UPDATE product_attribute SET '.implode(', ', $updatearr).' where pa_id = '.$attr_id);
            $this->XM->sqlcore->commit();
        }
        $attrgroup = (int)$row['pag_id'];
        if($parent_id!=$row['pa_parent_id']){
            if($parent_id>0){
                $res = $this->XM->sqlcore->query('SELECT product_attribute.pa_depth,product_attribute_tree.pa_id as pat_id from product_attribute left join product_attribute_tree on product_attribute_tree.pa_id = product_attribute.pa_id and product_attribute_tree.pa_anc_id = '.$attr_id.' where product_attribute.pa_id = '.$parent_id.' and product_attribute.pag_id = '.$attrgroup_id.' LIMIT 1');
                $parentRow = $this->XM->sqlcore->getRow($res);
                $this->XM->sqlcore->freeResult($res);
                if(!$parentRow){
                    $err = langTranslate('product', 'err', 'Parent Attr doesn\'t exist',  'Parent Attr doesn\'t exist');
                    return false;
                }
                if($parentRow['pat_id']){
                    $err = langTranslate('product', 'err', 'Attr can\'t be it\'s own parent',  'Attr can\'t be it\'s own parent');
                    return false;
                }
                $newDepth = $parentRow['pa_depth']+1;
            } else {
                $newDepth = 0;
            }
            $deltaDepth = $newDepth - $row['pa_depth'];
            $this->XM->sqlcore->query('UPDATE product_attribute SET pa_parent_id = '.$parent_id.' where pa_id = '.$attr_id);
            if($deltaDepth!=0){
                $this->XM->sqlcore->query('UPDATE product_attribute SET pa_depth = pa_depth '.($deltaDepth>0?'+':'-').' '.abs($deltaDepth).' where pa_id in (SELECT distinct pa_id FROM product_attribute_tree WHERE pa_anc_id = '.$attr_id.')');
            }
            $this->XM->sqlcore->commit();
        }

        $languageIdList = $this->XM->lang->getLanguageIdList();
        $ml_variants = array();
        $res = $this->XM->sqlcore->query('SELECT pa_ml_name, lang_id, pa_ml_id from product_attribute_ml where pa_id = '.$attr_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $lang_id = (int)$row['lang_id'];
            if(!isset($ml_variants[$lang_id])){
                $ml_variants[$lang_id] = array();
            }
            $ml_variants[$lang_id][] = array('name'=>$row['pa_ml_name'],'id'=>$row['pa_ml_id']);
        }
        $this->XM->sqlcore->freeResult($res);

        foreach($languageIdList as $lang_id){
            $lang_name = getLangArrayVal($name,$lang_id);
            if(isset($ml_variants[$lang_id])){
                foreach($ml_variants[$lang_id] as $ml_variant){
                    if($lang_name==$ml_variant['name']){
                        continue 2;//same values, no need to insert/update
                    }
                }
            }
            if(mb_strlen($lang_name, 'UTF-8')>128){
                $err = formatReplace(langTranslate('product', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('product', 'attr', 'Name', 'Name'),
                    128);
                return false;
            }
            $this->XM->sqlcore->query('DELETE FROM product_attribute_ml where pa_id = '.$attr_id.' and lang_id = '.$lang_id);
            $this->XM->sqlcore->commit();
            $insertkeys = array();
            $insertvals = array();
            if(strlen($lang_name)){
                $insertkeys[] = 'pa_ml_name';
                $insertvals[] = '\''.$this->XM->sqlcore->prepString($lang_name,128).'\'';
            }
            if(empty($insertkeys)){
                continue;
            }
            $insertkeys[] = 'pa_id';
            $insertvals[] = $attr_id;
            $insertkeys[] = 'lang_id';
            $insertvals[] = $lang_id;
            $this->XM->sqlcore->query('INSERT INTO product_attribute_ml ('.implode(',', $insertkeys).') VALUES ('.implode(',', $insertvals).')');
            $this->XM->sqlcore->commit();
        }
        return true;
    }
    public function get_attr_info_for_all_languages($attr_id,$attrgroup_id){
        $attr_id = (int)$attr_id;
        if($attr_id<=0){//invalid attr_id
            return false;
        }
        $attrgroup_id = (int)$attrgroup_id;
        if($attrgroup_id<=0){//invalid attrgroup_id
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT product_attribute.pa_parent_id, product_attribute.pa_show_only_origin, product_attribute.pa_has_important_values, product_attribute_ml.pa_ml_name, product_attribute_ml.lang_id
            from product_attribute
            left join product_attribute_ml on product_attribute_ml.pa_id = product_attribute.pa_id
            where product_attribute.pa_id = '.$attr_id.' and product_attribute.pag_id = '.$attrgroup_id);
        $result = array(
                'parent'=>0,
                'name'=>array(),
            );
        $first_iteration_flag = false;
        while($row = $this->XM->sqlcore->getRow($res)){
            if(!$first_iteration_flag){
                $result['parent'] = (int)$row['pa_parent_id'];
                $result['show_only_origin'] = (int)$row['pa_show_only_origin'];
                $result['has_important'] = (int)$row['pa_has_important_values'];
                $first_iteration_flag = true;
            }
            $lang_id = (int)$row['lang_id'];
            $result['name'][$lang_id] = (string)$row['pa_ml_name'];
        }
        $this->XM->sqlcore->freeResult($res);
        if(!$first_iteration_flag){//doesn't exist
            return false;
        }
        return $result;
    }
    public function get_attr_info($attr_id){
        $attr_id = (int)$attr_id;
        if($attr_id<=0){//invalid attr_id
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT pa.pa_id,pa.pag_id, coalesce(pa_ml.pa_ml_name,\'-\') as pa_ml_name,pa.pa_ishidden,pa.pa_parent_id,pag.pag_userfill,pag.pag_system
            from product_attribute pa
            inner join product_attribute_group pag on pag.pag_id = pa.pag_id
            left join (select product_attribute.pa_id,substring_index(group_concat(pa_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pa_ml_id from product_attribute_ml inner join product_attribute on product_attribute.pa_id = product_attribute_ml.pa_id and product_attribute.pa_id = '.$attr_id.' where pa_ml_name is not null group by product_attribute.pa_id) as ln_glue on ln_glue.pa_id = pa.pa_id
            left join product_attribute_ml pa_ml on pa_ml.pa_ml_id = ln_glue.pa_ml_id
            where pa.pa_id = '.$attr_id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        $can_edit = $this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES);
        $result = array(
                'id'=>(int)$row['pa_id'],
                'attrgroup_id'=>(int)$row['pag_id'],
                'name'=>(string)$row['pa_ml_name'],
                'parent_id'=>(int)$row['pa_parent_id'],
                'is_hidden'=>(bool)$row['pa_ishidden'],
                'system'=>(bool)$row['pag_system'],

                'can_hide'=>(bool)$can_edit,
                'can_edit'=>(bool)$can_edit,
                'can_add'=>(int)$row['pag_userfill'],
            );
        return $result;
    }
    public function attr_can_have_analog($attr_id){
        if(($attr_id = (int)$attr_id)<=0){
            return null;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            return null;
        }
        $res = $this->XM->sqlcore->query('SELECT product_attribute_group.pag_analog from product_attribute_group inner join product_attribute on product_attribute.pag_id = product_attribute_group.pag_id where product_attribute.pa_id = '.$attr_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return null;
        }
        return (1==(int)$row['pag_analog']);
    }
    public function hide_attr($attr_id, $changeTo, &$err){
        $attr_id = (int)$attr_id;
        $changeTo = $changeTo?1:0;
        if($attr_id<=0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }

        $res = $this->XM->sqlcore->query('SELECT pa_ishidden from product_attribute where pa_id = '.$attr_id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Attr doesn\'t exist',  'Attr doesn\'t exist');
            return false;
        }
        if($row['pa_ishidden']==$changeTo){
            return true;
        }
        $this->XM->sqlcore->query('UPDATE product_attribute set pa_ishidden = '.$changeTo.' where pa_id = '.$attr_id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function get_attrval_list($attr_id){
        $attr_id = (int)$attr_id;
        if($attr_id<=0){//invalid attr_id
            return false;
        }
        $attrvallist = array();
        $res = $this->XM->sqlcore->query('SELECT pav.pav_id, group_concat(coalesce(pavpart_ml.pav_ml_name,pavpart.pav_origin_name,\'-\') order by papart.pa_depth asc separator \' / \') as pav_ml_name, if(pa.pa_has_important_values=1,pav.pav_important,1) as pav_important
            from product_attribute_value pav
            inner join product_attribute pa on pa.pa_id = pav.pa_id
            inner join product_attribute_value_tree pavt on pavt.pav_id = pav.pav_id
            inner join product_attribute_value pavpart on pavpart.pav_id = pavt.pav_anc_id
            inner join product_attribute papart on papart.pa_id = pavpart.pa_id
            left join (
                select product_attribute_value_ml.pav_id,substring_index(group_concat(pav_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pav_ml_id
                    from product_attribute_value_ml
                    inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_ml.pav_id
                    inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pa_show_only_origin = 0
                    inner join product_attribute pa2 on pa2.pag_id = product_attribute.pag_id and pa2.pa_id = '.$attr_id.'
                    where pav_ml_name is not null and not (pav_origin_name is not null and lang_id <> '.$this->XM->lang->getCurrLangId().')
                    group by product_attribute_value_ml.pav_id) as ln_glue on ln_glue.pav_id = pavpart.pav_id
            left join product_attribute_value_ml pavpart_ml on pavpart_ml.pav_ml_id = ln_glue.pav_ml_id
            where pa.pa_id = '.$attr_id.'
            group by pav.pav_id
            order by 2 asc');
        $can_edit = $this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES);
        while($row = $this->XM->sqlcore->getRow($res)){
            $attrvallist[] = array(
                    'id'=>(int)$row['pav_id'],
                    'name'=>(string)$row['pav_ml_name'],
                    'important'=>$row['pav_important']?1:0,

                    'can_edit'=>(bool)$can_edit,
                );
        }
        $this->XM->sqlcore->freeResult($res);
        return $attrvallist;
    }
    public function get_analog_list($attrval_id){
        if(($attrval_id = (int)$attrval_id)<=0){//invalid attr_id
            return false;
        }
        $attrvallist = array();
        $res = $this->XM->sqlcore->query('SELECT product_attribute_value.pav_id, coalesce(product_attribute_value_ml.pav_ml_name,product_attribute_value.pav_origin_name,\'-\') as pav_ml_name, if(product_attribute.pa_has_important_values=1,product_attribute_value.pav_important,1) as pav_important
            from product_attribute_value_analog pava_main
            inner join product_attribute_value_analog on product_attribute_value_analog.pava_group_id = pava_main.pava_group_id
            inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_analog.pav_id
            inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
            left join (
                select product_attribute_value_ml.pav_id,substring_index(group_concat(pav_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pav_ml_id
                    from product_attribute_value_ml
                    inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_ml.pav_id
                    inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pa_show_only_origin = 0
                    inner join product_attribute_value_analog on product_attribute_value_analog.pav_id = product_attribute_value.pav_id
                    inner join product_attribute_value_analog pava_main on pava_main.pava_group_id = product_attribute_value_analog.pava_group_id and pava_main.pav_id = '.$attrval_id.'
                    where product_attribute_value_ml.pav_ml_name is not null and not (product_attribute_value.pav_origin_name is not null and product_attribute_value_ml.lang_id <> '.$this->XM->lang->getCurrLangId().')
                    group by product_attribute_value_ml.pav_id) as ln_glue on ln_glue.pav_id = product_attribute_value.pav_id
            left join product_attribute_value_ml on product_attribute_value_ml.pav_ml_id = ln_glue.pav_ml_id
            where pava_main.pav_id = '.$attrval_id.'
            order by 2 asc');
        $can_edit = $this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES);
        while($row = $this->XM->sqlcore->getRow($res)){
            $attrvallist[] = array(
                    'id'=>(int)$row['pav_id'],
                    'name'=>(string)$row['pav_ml_name'],
                    'important'=>$row['pav_important']?1:0,
                );
        }
        $this->XM->sqlcore->freeResult($res);
        return $attrvallist;
    }
    public function get_possible_analog_list($attrval_id){
        if(($attrval_id = (int)$attrval_id)<=0){//invalid attr_id
            return false;
        }
        $attrvallist = array();
        $res = $this->XM->sqlcore->query('SELECT product_attribute_value.pav_id, coalesce(product_attribute_value_ml.pav_ml_name,product_attribute_value.pav_origin_name,\'-\') as pav_ml_name, if(product_attribute.pa_has_important_values=1,product_attribute_value.pav_important,1) as pav_important, product_attribute_value_se.pav_se_text
            from product_attribute_value pav_main
            inner join product_attribute_value on product_attribute_value.pa_id = pav_main.pa_id and product_attribute_value.pav_parent_id = pav_main.pav_parent_id
            inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
            left join (
                select product_attribute_value_ml.pav_id,substring_index(group_concat(pav_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pav_ml_id
                    from product_attribute_value_ml
                    inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_ml.pav_id
                    inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pa_show_only_origin = 0
                    inner join product_attribute_value pav_main on pav_main.pa_id = product_attribute_value.pa_id and pav_main.pav_parent_id = product_attribute_value.pav_parent_id and pav_main.pav_id = '.$attrval_id.'
                    where product_attribute_value_ml.pav_ml_name is not null and not (product_attribute_value.pav_origin_name is not null and product_attribute_value_ml.lang_id <> '.$this->XM->lang->getCurrLangId().')
                    group by product_attribute_value_ml.pav_id) as ln_glue on ln_glue.pav_id = product_attribute_value.pav_id
            left join product_attribute_value_ml on product_attribute_value_ml.pav_ml_id = ln_glue.pav_ml_id
            left join (
                select product_attribute_value_se.pav_id, group_concat(distinct product_attribute_value_se.pav_se_text separator \' \') as pav_se_text
                    from product_attribute_value_se
                    inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_se.pav_id
                    inner join product_attribute_value pav_main on pav_main.pa_id = product_attribute_value.pa_id and pav_main.pav_parent_id = product_attribute_value.pav_parent_id and pav_main.pav_id = '.$attrval_id.'
                    group by product_attribute_value_se.pav_id
            ) as product_attribute_value_se on product_attribute_value_se.pav_id = product_attribute_value.pav_id
            where pav_main.pav_id = '.$attrval_id.' and product_attribute_value.pav_id <> '.$attrval_id.'
            order by 2 asc');
        $can_edit = $this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES);
        while($row = $this->XM->sqlcore->getRow($res)){
            $exploded_se_texts = explode(' ',$row['pav_se_text']);
            $cleared_se_texts = array();
            foreach($exploded_se_texts as $se_text){
                if(strlen($se_text)<3){
                    continue;
                }
                if(in_array($se_text, $cleared_se_texts)){
                    continue;
                }
                $cleared_se_texts[] = $se_text;
            }
            $attrvallist[] = array(
                    'id'=>(int)$row['pav_id'],
                    'name'=>(string)$row['pav_ml_name'],
                    'important'=>$row['pav_important']?1:0,
                    'setext'=>implode(' ',$cleared_se_texts),
                );
        }
        $this->XM->sqlcore->freeResult($res);
        return $attrvallist;
    }
    private function __get_attrval_fullname($attrval_id){
        $attrval_id = (int)$attrval_id;
        if($attrval_id<=0){//invalid attr_id
            return false;
        }
        $attrvallist = array();
        $res = $this->XM->sqlcore->query('SELECT group_concat(coalesce(pavpart_ml.pav_ml_name,pavpart.pav_origin_name,\'-\') order by papart.pa_depth asc separator \' / \') as pav_ml_name
            from product_attribute_value pav
            inner join product_attribute_value_tree pavt on pavt.pav_id = pav.pav_id
            inner join product_attribute_value pavpart on pavpart.pav_id = pavt.pav_anc_id
            inner join product_attribute papart on papart.pa_id = pavpart.pa_id
            left join (
                select product_attribute_value_ml.pav_id,substring_index(group_concat(pav_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pav_ml_id
                    from product_attribute_value_ml
                    inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_ml.pav_id
                    inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pa_show_only_origin = 0
                    inner join product_attribute_value_tree on product_attribute_value_tree.pav_anc_id = product_attribute_value.pav_id and product_attribute_value_tree.pav_id = '.$attrval_id.'
                    where pav_ml_name is not null and not (pav_origin_name is not null and lang_id <> '.$this->XM->lang->getCurrLangId().')
                    group by product_attribute_value_ml.pav_id
                ) as ln_glue on ln_glue.pav_id = pavpart.pav_id
            left join product_attribute_value_ml pavpart_ml on pavpart_ml.pav_ml_id = ln_glue.pav_ml_id
            where pav.pav_id = '.$attrval_id.'
            group by pav.pav_id
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        return $row['pav_ml_name'];
    }
    public function get_attrval_info($attrval_id){
        $attrval_id = (int)$attrval_id;
        if($attrval_id<=0){//invalid attr_id
            return false;
        }
        $attrval_info = array();
        $res = $this->XM->sqlcore->query('SELECT distinct pav.pav_id,pa.pa_id,pa.pag_id,coalesce(pav_ml.pav_ml_name,pav.pav_origin_name,\'-\') as pav_ml_name, if(pa.pa_has_important_values=1,pav.pav_important,null) as pav_important, pa.pa_depth
            FROM product_attribute_value pav
            inner join product_attribute pa on pa.pa_id = pav.pa_id
            left join (select product_attribute_value.pav_id,substring_index(group_concat(pav_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pav_ml_id from product_attribute_value_ml inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_ml.pav_id inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pa_show_only_origin = 0 where pav_ml_name is not null and product_attribute_value.pav_id = '.$attrval_id.' and not (pav_origin_name is not null and lang_id <> '.$this->XM->lang->getCurrLangId().') group by product_attribute_value.pav_id) as ln_glue on ln_glue.pav_id = pav.pav_id
            left join product_attribute_value_ml pav_ml on pav_ml.pav_ml_id = ln_glue.pav_ml_id
            where pav.pav_id = '.$attrval_id.'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        return array(
                'id'=>(int)$row['pav_id'],
                'attr_id'=>(int)$row['pa_id'],
                'group_id'=>(int)$row['pag_id'],
                'depth'=>(int)$row['pa_depth'],
                'name'=>(string)$row['pav_ml_name'],
                'important'=>$row['pav_important']?1:0,
            );
    }
    public function find_attrval($attr_id, $parent_id, $originname, &$err){
        $attr_id = (int)$attr_id;
        if($attr_id<=0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        $parent_id = (int)$parent_id;
        if($parent_id<0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        $originname = mb_strtolower(trim($originname),'UTF-8');
        if(!strlen($originname)){
            $err = langTranslate('product', 'err', 'Empty origin name',  'Empty origin name');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT product_attribute_value.pav_id
            from product_attribute_value
            left join product_attribute_value_ml on product_attribute_value_ml.pav_id = product_attribute_value.pav_id
            where product_attribute_value.pa_id = '.$attr_id.' and product_attribute_value.pav_parent_id = '.$parent_id.' and ( LOWER(product_attribute_value.pav_origin_name) = \''.$this->XM->sqlcore->prepString($originname,128).'\' or LOWER(product_attribute_value_ml.pav_ml_name) = \''.$this->XM->sqlcore->prepString($originname,128).'\' ) limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        return (int)$row['pav_id'];

    }
    public function add_attrval($attr_id, $attributes, $originname, $name, $important, &$err){
        $attr_id = (int)$attr_id;
        if($attr_id<=0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        $originname = trim($originname);
        if(empty($originname)){
            $err = formatReplace(langTranslate('product', 'err', 'Field @1 is empty',  'Field @1 is empty'),
                langTranslate('product', 'attrval', 'Origin Name', 'Origin Name'));
            return false;
        }
        if(mb_strlen($originname, 'UTF-8')>128){
            $err = formatReplace(langTranslate('product', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                langTranslate('product', 'attrval', 'Origin Name', 'Origin Name'),
                128);
            return false;
        }

        $res = $this->XM->sqlcore->query('SELECT product_attribute.pa_parent_id,product_attribute_group.pag_userfill,product_attribute_group.pag_id FROM product_attribute inner join product_attribute_group on product_attribute_group.pag_id = product_attribute.pag_id where product_attribute.pa_id = '.$attr_id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Attribute doesn\'t exist',  'Attribute doesn\'t exist');
            return false;
        }
        $pag_id = (int)$row['pag_id'];
        if(!($row['pag_userfill']&&$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_USERFILL_ATTRIBUTES)) && !$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $attr_parent_id = (int)$row['pa_parent_id'];
        $parent_id = 0;
        if($attr_parent_id > 0){
            if(!is_array($attributes)||empty($attributes)){
                $err = langTranslate('product', 'err', 'Invalid parent',  'Invalid parent');
                return false;
            };
            $clean_attributes = array();
            foreach($attributes as $attribute){
                $attribute = (int)$attribute;
                if(!in_array($attribute, $clean_attributes)){
                    $clean_attributes[] = $attribute;
                }
            }
            unset($attributes);
            $attribute_chunks = array_chunk($clean_attributes, 100);
            unset($clean_attributes);
            foreach($attribute_chunks as $attribute_chunk){
                $res = $this->XM->sqlcore->query('SELECT pav_id FROM product_attribute_value WHERE pa_id = '.$attr_parent_id.' and pav_id IN ('.implode(',', $attribute_chunk).') LIMIT 1');
                $row = $this->XM->sqlcore->getRow($res);
                $this->XM->sqlcore->freeResult($res);
                if($row){
                    $parent_id = (int)$row['pav_id'];
                    break;
                }
            }
            if(!$parent_id){
                $err = langTranslate('product', 'err', 'Invalid parent',  'Invalid parent');
                return false;
            }
        }
        $res = $this->XM->sqlcore->query('SELECT pav_id FROM product_attribute_value where pa_id = '.$attr_id.' and pav_parent_id = '.$parent_id.' and LOWER(pav_origin_name) = \''.$this->XM->sqlcore->prepString(mb_strtolower($originname,'UTF-8'),128).'\' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if($row){
            return (int)$row['pav_id'];
        }
        $this->XM->sqlcore->query('INSERT INTO product_attribute_value (pa_id,pav_parent_id,pav_origin_name) VALUES ('.$attr_id.','.$parent_id.',\''.$this->XM->sqlcore->prepString($originname,128).'\')');
        $attrval_id = $this->XM->sqlcore->lastInsertId();
        if(strlen($originname)){//excess
            $this->XM->sqlcore->query('INSERT INTO product_attribute_value_se (pav_id,pav_se_type,lang_id,pav_se_text) VALUES ('.$attrval_id.',0,null,\''.$this->XM->sqlcore->prepString($this->XM->sqlcore->search_engine_alias($originname),128).'\')');
        }


        $languageIdList = $this->XM->lang->getLanguageIdList();
        foreach($languageIdList as $lang_id){
            $lang_name = getLangArrayVal($name,$lang_id);
            if(mb_strlen($lang_name, 'UTF-8')>128){
                $err = formatReplace(langTranslate('product', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('product', 'attrval', 'Name', 'Name'),
                    128);
                $this->XM->sqlcore->rollback();
                return false;
            }
            $insertkeys = array();
            $insertvals = array();
            if(strlen($lang_name)){
                $insertkeys[] = 'pav_ml_name';
                $insertvals[] = '\''.$this->XM->sqlcore->prepString($lang_name,128).'\'';
            }
            if(empty($insertkeys)){
                continue;
            }
            $insertkeys[] = 'pav_id';
            $insertvals[] = $attrval_id;
            $insertkeys[] = 'lang_id';
            $insertvals[] = $lang_id;

            $this->XM->sqlcore->query('INSERT INTO product_attribute_value_ml ('.implode(',', $insertkeys).') VALUES ('.implode(',', $insertvals).')');
            if(strlen($lang_name)){//excess
                $this->XM->sqlcore->query('INSERT INTO product_attribute_value_se (pav_id,pav_se_type,lang_id,pav_se_text) VALUES ('.$attrval_id.',1,'.$lang_id.',\''.$this->XM->sqlcore->prepString($this->XM->sqlcore->search_engine_alias($lang_name),128).'\')');
            }
        }
        $this->XM->sqlcore->commit();

        if($pag_id==\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID){
            //fill dependencies
            $this->XM->sqlcore->query('INSERT INTO product_attribute_group_dependency (pag_id,pav_id,pagd_visible,pagd_required,pagd_doublecheck)
                SELECT product_attribute_group.pag_id, '.$attrval_id.' as pav_id, if(product_attribute_group.pag_id = '.\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID.',1,product_attribute_group.pag_always_visible) as pagd_visible, if(product_attribute_group.pag_id = '.\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID.',1,0) as pagd_required, if(product_attribute_group.pag_id = '.\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID.',1,0) as pagd_doublecheck
                    FROM product_attribute_group
                    where product_attribute_group.pag_system = 0');
            $this->XM->sqlcore->commit();
        }

        return $attrval_id;
    }
    public function edit_attrval($attrval_id, $attributes, $originname, $name, $important, &$err){
        $attrval_id = (int)$attrval_id;
        if($attrval_id<=0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        $originname = trim($originname);
        if(empty($originname)){
            $err = formatReplace(langTranslate('product', 'err', 'Field @1 is empty',  'Field @1 is empty'),
                langTranslate('product', 'attrval', 'Origin Name', 'Origin Name'));
            return false;
        }
        $important = $important?1:0;

        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT product_attribute_value.pav_origin_name, product_attribute_value.pav_parent_id,product_attribute_value.pa_id,product_attribute.pa_parent_id,if(product_attribute.pa_has_important_values=1,product_attribute_value.pav_important,null) as pav_important from product_attribute_value inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id where pav_id = '.$attrval_id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Attr doesn\'t exist',  'Attr doesn\'t exist');
            return false;
        }
        $attr_id = (int)$row['pa_id'];
        $attr_parent_id = (int)$row['pa_parent_id'];
        $updatearr = array();
        if($row['pav_important']!==null and $row['pav_important']!=$important){
            $updatearr[] = 'pav_important = '.$important;
        }
        $pav_origin_name = $row['pav_origin_name'];
        if($pav_origin_name!==$originname){
            if(mb_strlen($originname, 'UTF-8')>128){
                $err = formatReplace(langTranslate('product', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('product', 'attrval', 'Origin Name', 'Origin Name'),
                    128);
                return false;
            }
            $updatearr[] = 'pav_origin_name = \''.$this->XM->sqlcore->prepString($originname,128).'\'';

        }

        $pav_parent_id = (int)$row['pav_parent_id'];
        $parent_id = 0;
        if($attr_parent_id > 0){
            if(!is_array($attributes)||empty($attributes)){
                $err = langTranslate('product', 'err', 'Invalid parent',  'Invalid parent');
                return false;
            };
            $clean_attributes = array();
            foreach($attributes as $attribute){
                $attribute = (int)$attribute;
                if(!in_array($attribute, $clean_attributes)){
                    $clean_attributes[] = $attribute;
                }
            }
            unset($attributes);
            $attribute_chunks = array_chunk($clean_attributes, 100);
            unset($clean_attributes);
            foreach($attribute_chunks as $attribute_chunk){
                $res = $this->XM->sqlcore->query('SELECT pav_id FROM product_attribute_value WHERE pa_id = '.$attr_parent_id.' and pav_id IN ('.implode(',', $attribute_chunk).') LIMIT 1');
                $row = $this->XM->sqlcore->getRow($res);
                $this->XM->sqlcore->freeResult($res);
                if($row){
                    $parent_id = (int)$row['pav_id'];
                    break;
                }
            }
            if(!$parent_id){
                $err = langTranslate('product', 'err', 'Invalid parent',  'Invalid parent');
                return false;
            }
        }
        if($parent_id!=$pav_parent_id){
            $updatearr[] = 'pav_parent_id = '.$parent_id;
        }
        if(!empty($updatearr)){
            $this->XM->sqlcore->query('UPDATE product_attribute_value SET '.implode(', ', $updatearr).' where pav_id = '.$attrval_id);
            if($pav_origin_name!==$originname){
                $res = $this->XM->sqlcore->query('SELECT pav_se_text from product_attribute_value_se where pav_id = '.$attrval_id.' and pav_se_type = 0 limit 1');
                $se_row = $this->XM->sqlcore->getRow($res);
                $this->XM->sqlcore->freeResult($res);
                $se_origin_name = '';
                if($se_row){
                    $se_origin_name = $se_row['pav_se_text'];
                }
                $asciialias = $this->XM->sqlcore->search_engine_alias($originname);
                if($se_origin_name != $asciialias){
                    if(empty($asciialias)){
                        $this->XM->sqlcore->query('DELETE FROM product_attribute_value_se where pav_id = '.$attrval_id.' and pav_se_type = 0');
                    } elseif(!$se_row){
                        $this->XM->sqlcore->query('INSERT INTO product_attribute_value_se (pav_id,pav_se_type,lang_id,pav_se_text) VALUES ('.$attrval_id.',0,null,\''.$this->XM->sqlcore->prepString($asciialias,128).'\')');
                    } else {
                        $this->XM->sqlcore->query('UPDATE product_attribute_value_se SET pav_se_text = \''.$this->XM->sqlcore->prepString($asciialias,128).'\' where pav_id = '.$attrval_id.' and pav_se_type = 0');
                    }

                }
            }
            $this->XM->sqlcore->commit();
        }
        $languageIdList = $this->XM->lang->getLanguageIdList();
        $ml_variants = array();
        $res = $this->XM->sqlcore->query('SELECT pav_ml_name, lang_id, pav_ml_id from product_attribute_value_ml where pav_id = '.$attrval_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $lang_id = (int)$row['lang_id'];
            if(!isset($ml_variants[$lang_id])){
                $ml_variants[$lang_id] = array();
            }
            $ml_variants[$lang_id][] = array('name'=>$row['pav_ml_name'],'id'=>$row['pav_ml_id']);
        }
        $this->XM->sqlcore->freeResult($res);

        foreach($languageIdList as $lang_id){
            $lang_name = getLangArrayVal($name,$lang_id);
            if(isset($ml_variants[$lang_id])){
                foreach($ml_variants[$lang_id] as $ml_variant){
                    if($lang_name==$ml_variant['name']){
                        continue 2;//same values, no need to insert/update
                    }
                }
            }
            if(mb_strlen($lang_name, 'UTF-8')>128){
                $err = formatReplace(langTranslate('product', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('product', 'attrval', 'Name', 'Name'),
                    128);
                return false;
            }
            $this->XM->sqlcore->query('DELETE FROM product_attribute_value_ml where pav_id = '.$attrval_id.' and lang_id = '.$lang_id);
            $this->XM->sqlcore->commit();
            $insertkeys = array();
            $insertvals = array();
            if(strlen($lang_name)){
                $insertkeys[] = 'pav_ml_name';
                $insertvals[] = '\''.$this->XM->sqlcore->prepString($lang_name,128).'\'';
            }
            if(empty($insertkeys)){
                $this->XM->sqlcore->query('DELETE FROM product_attribute_value_se where pav_id = '.$attrval_id.' and pav_se_type = 1 and lang_id = '.$lang_id);
                $this->XM->sqlcore->commit();
                continue;
            }
            $insertkeys[] = 'pav_id';
            $insertvals[] = $attrval_id;
            $insertkeys[] = 'lang_id';
            $insertvals[] = $lang_id;

            $this->XM->sqlcore->query('INSERT INTO product_attribute_value_ml ('.implode(',', $insertkeys).') VALUES ('.implode(',', $insertvals).')');

            $res = $this->XM->sqlcore->query('SELECT pav_se_text from product_attribute_value_se where pav_id = '.$attrval_id.' and pav_se_type = 1 and lang_id = '.$lang_id.' limit 1');
            $se_row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            $se_lang_name = '';
            if($se_row){
                $se_lang_name = $se_row['pav_se_text'];
            }
            $asciialias = $this->XM->sqlcore->search_engine_alias($lang_name);
            if($se_lang_name != $asciialias){
                if(empty($asciialias)){
                    $this->XM->sqlcore->query('DELETE FROM product_attribute_value_se where pav_id = '.$attrval_id.' and pav_se_type = 1 and lang_id = '.$lang_id);
                } elseif(!$se_row){
                    $this->XM->sqlcore->query('INSERT INTO product_attribute_value_se (pav_id,pav_se_type,lang_id,pav_se_text) VALUES ('.$attrval_id.',1,'.$lang_id.',\''.$this->XM->sqlcore->prepString($asciialias,128).'\')');
                } else {
                    $this->XM->sqlcore->query('UPDATE product_attribute_value_se SET pav_se_text = \''.$this->XM->sqlcore->prepString($asciialias,128).'\' where pav_id = '.$attrval_id.' and pav_se_type = 1 and lang_id = '.$lang_id);
                }
            }
            $this->XM->sqlcore->commit();
        }
        return true;
    }
    public function get_attrval_info_for_all_languages($attrval_id){
        $attrval_id = (int)$attrval_id;
        if($attrval_id<=0){//invalid attrval_id
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT product_attribute_value.pav_parent_id, product_attribute_value.pav_origin_name, product_attribute_value_ml.pav_ml_name, if(product_attribute.pa_has_important_values=1,product_attribute_value.pav_important,null) as pav_important,product_attribute_value_ml.lang_id
            from product_attribute_value
            inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
            left join product_attribute_value_ml on product_attribute_value_ml.pav_id = product_attribute_value.pav_id
            where product_attribute_value.pav_id = '.$attrval_id);
        $result = array(
                'parent'=>0,
                'originname'=>'',
                'name'=>array(),
            );
        $first_iteration_flag = false;
        while($row = $this->XM->sqlcore->getRow($res)){
            if(!$first_iteration_flag){
                $result['parent'] = (int)$row['pav_parent_id'];
                $result['originname'] = (string)$row['pav_origin_name'];
                if($row['pav_important']!==null){
                    $result['important'] = $row['pav_important']?1:0;
                }
                $first_iteration_flag = true;
            }
            $lang_id = (int)$row['lang_id'];
            $result['name'][$lang_id] = (string)$row['pav_ml_name'];
        }
        $this->XM->sqlcore->freeResult($res);
        if(!$first_iteration_flag){//doesn't exist
            return false;
        }
        return $result;
    }
    public function get_alternate_spelling_list($attrval_id){
        if(($attrval_id = (int)$attrval_id)<=0){
            return array();
        }

        $res = $this->XM->sqlcore->query('SELECT pav_as_id, pav_as_spelling from product_attribute_value_alternate_spelling where pav_id = '.$attrval_id);
        $result = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $result[(int)$row['pav_as_id']] = $row['pav_as_spelling'];
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }
    public function add_attribute_alternate_spelling($attrval_id, $spelling, &$err){
        if(($attrval_id = (int)$attrval_id)<=0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $spelling = trim($spelling);
        if(empty($spelling)){
            $err = formatReplace(langTranslate('product', 'err', 'Field @1 is empty',  'Field @1 is empty'),
                langTranslate('product', 'attrval', 'Alternate Spellings: Spelling', 'Spelling'));
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT 1 from product_attribute_value_alternate_spelling where pav_as_spelling = \''.$this->XM->sqlcore->prepString($spelling,128).'\' and pav_id = '.$attrval_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if($row){
            $err = langTranslate('product', 'err', 'Provided alternate spelling already registered', 'Provided alternate spelling already registered');
            return false;
        }
        $this->XM->sqlcore->query('INSERT INTO product_attribute_value_alternate_spelling (pav_id,pav_as_spelling) VALUES ('.$attrval_id.',\''.$this->XM->sqlcore->prepString($spelling,128).'\')');
        $pav_as_id = $this->XM->sqlcore->lastInsertId();
        $this->XM->sqlcore->commit();
        $this->__update_product_attribute_value_se_alternate_spellings($attrval_id);
        return $pav_as_id;
    }
    public function edit_attribute_alternate_spelling($spelling_id, $spelling, &$err){
        if(($spelling_id = (int)$spelling_id)<=0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $spelling = trim($spelling);
        if(empty($spelling)){
            $err = formatReplace(langTranslate('product', 'err', 'Field @1 is empty',  'Field @1 is empty'),
                langTranslate('product', 'attrval', 'Alternate Spellings: Spelling', 'Spelling'));
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT pav_id, pav_as_spelling from product_attribute_value_alternate_spelling where pav_as_id = '.$spelling_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Alternate spelling doesn\'t exist', 'Alternate spelling doesn\'t exist');
            return false;
        }
        if($row['pav_as_spelling']==$spelling){
            //nothing changed
            return true;
        }
        $attrval_id = (int)$row['pav_id'];

        $res = $this->XM->sqlcore->query('SELECT 1 from product_attribute_value_alternate_spelling where pav_as_spelling = \''.$this->XM->sqlcore->prepString($spelling,128).'\' and pav_id = '.$attrval_id.' and pav_as_id <> '.$spelling_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if($row){
            $err = langTranslate('product', 'err', 'Provided alternate spelling already registered', 'Provided alternate spelling already registered');
            return false;
        }
        $this->XM->sqlcore->query('UPDATE product_attribute_value_alternate_spelling set pav_as_spelling = \''.$this->XM->sqlcore->prepString($spelling,128).'\' where pav_as_id = '.$spelling_id);
        $this->XM->sqlcore->commit();
        $this->__update_product_attribute_value_se_alternate_spellings($attrval_id);
        return true;
    }

    public function remove_attribute_alternate_spelling($spelling_id, &$err){
        if(($spelling_id = (int)$spelling_id)<=0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT pav_id from product_attribute_value_alternate_spelling where pav_as_id = '.$spelling_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            //already deleted
            return true;
        }
        $attrval_id = (int)$row['pav_id'];
        $this->XM->sqlcore->query('DELETE FROM product_attribute_value_alternate_spelling WHERE  pav_as_id = '.$spelling_id);
        $this->XM->sqlcore->commit();
        $this->__update_product_attribute_value_se_alternate_spellings($attrval_id);
        return true;
    }

    public function add_attribute_analog($attrval_id, $analog_id, &$err){
        if(($attrval_id = (int)$attrval_id)<=0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        if(($analog_id = (int)$analog_id)<=0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        if($attrval_id==$analog_id){
            return true;
        }
        $res = $this->XM->sqlcore->query('SELECT product_attribute_value.pa_id, product_attribute_value.pav_parent_id, product_attribute_value_analog.pava_group_id
            from product_attribute_value
            left join product_attribute_value_analog on product_attribute_value_analog.pav_id = product_attribute_value.pav_id
            where product_attribute_value.pav_id = '.$attrval_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Attr doesn\'t exist',  'Attr doesn\'t exist');
            return false;
        }
        $main_pa_id = (int)$row['pa_id'];
        $main_pav_parent_id = (int)$row['pav_parent_id'];
        $main_pava_group_id = (int)$row['pava_group_id'];

        $res = $this->XM->sqlcore->query('SELECT product_attribute_value.pa_id, product_attribute_value.pav_parent_id, product_attribute_value_analog.pava_group_id
            from product_attribute_value
            left join product_attribute_value_analog on product_attribute_value_analog.pav_id = product_attribute_value.pav_id
            where product_attribute_value.pav_id = '.$analog_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Attr doesn\'t exist',  'Attr doesn\'t exist');
            return false;
        }
        if($main_pa_id != (int)$row['pa_id'] || $main_pav_parent_id != (int)$row['pav_parent_id']){
            $err = langTranslate('product', 'err', 'You can only choose analog from the same attribute and parent', 'You can only choose analog from the same attribute and parent');
            return false;
        }
        $analog_pava_group_id = (int)$row['pava_group_id'];
        if($main_pava_group_id){
            if($analog_pava_group_id){
                if($main_pava_group_id==$analog_pava_group_id){
                    return true;
                }
                //confirmation
                $this->XM->sqlcore->query('UPDATE product_attribute_value_analog set pava_group_id = '.$main_pava_group_id.' where pava_group_id = '.$analog_pava_group_id);
            } else {
                $this->XM->sqlcore->query('INSERT INTO product_attribute_value_analog (pav_id, pava_group_id) VALUES ('.$analog_id.','.$main_pava_group_id.')');
            }
        } else {
            if($analog_pava_group_id){
                $this->XM->sqlcore->query('INSERT INTO product_attribute_value_analog (pav_id, pava_group_id) VALUES ('.$attrval_id.','.$analog_pava_group_id.')');
            } else {
                $res = $this->XM->sqlcore->query('SELECT coalesce(max(pava_group_id),0)+1 as pava_group_id FROM product_attribute_value_analog');
                $row = $this->XM->sqlcore->getRow($res);
                $this->XM->sqlcore->freeResult($res);
                $new_pava_group_id = (int)$row['pava_group_id'];
                $this->XM->sqlcore->query('INSERT INTO product_attribute_value_analog (pav_id, pava_group_id) VALUES ('.$attrval_id.','.$new_pava_group_id.')');
                $this->XM->sqlcore->query('INSERT INTO product_attribute_value_analog (pav_id, pava_group_id) VALUES ('.$analog_id.','.$new_pava_group_id.')');

            }
        }
        $this->XM->sqlcore->commit();
        return true;
    }

    public function remove_attribute_analog($attrval_id, $analog_id, &$err){
        if(($attrval_id = (int)$attrval_id)<=0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        if(($analog_id = (int)$analog_id)<=0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_MANAGE_ATTRIBUTES)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT pava_attrval.pava_group_id
            from product_attribute_value_analog pava_attrval
            inner join product_attribute_value_analog pava_analog on pava_analog.pava_group_id = pava_attrval.pava_group_id and pava_analog.pav_id = '.$analog_id.'
            where pava_attrval.pav_id = '.$attrval_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return true;
        }
        $pava_group_id = (int)$row['pava_group_id'];
        $this->XM->sqlcore->query('DELETE FROM product_attribute_value_analog where pav_id = '.$analog_id.' and pava_group_id = '.$pava_group_id);
        $this->XM->sqlcore->commit();
        return true;
    }



    private function __update_product_attribute_value_se_alternate_spellings($attrval_id){
        $attrval_id = (int)$attrval_id;
        $this->XM->sqlcore->query('DELETE FROM product_attribute_value_se where pav_id = '.$attrval_id.' and pav_se_type = 2');

        $se_aliases = array();
        $res = $this->XM->sqlcore->query('SELECT pav_as_spelling from product_attribute_value_alternate_spelling where pav_id = '.$attrval_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $se_alias = $this->XM->sqlcore->search_engine_alias($row['pav_as_spelling']);
            if(strlen($se_alias) && !in_array($se_alias, $se_aliases)){
                $se_aliases[] = $se_alias;
            }
        }
        $this->XM->sqlcore->freeResult($res);

        foreach($se_aliases as $se_alias){
            $this->XM->sqlcore->query('INSERT INTO product_attribute_value_se (pav_id,pav_se_type,pav_se_text) VALUES ('.$attrval_id.',2,\''.$this->XM->sqlcore->prepString($se_alias,128).'\')');
        }
        $this->XM->sqlcore->commit();
    }
    public function get_system_attrval_tree($pag_id,$values,&$err){
        return $this->get_attrval_tree(array($pag_id),null,$values,false,false,false,false,true,false,$err);
    }
    public function get_edit_product_attrval_tree($values,$for_vintage,$for_blend,&$err){
        $vintage_flags = 0;
        if($for_vintage){
            $vintage_flags = \PRODUCT\PRODUCT_ATTRVAL_TREE_FOR_VINTAGE;
            if(!$for_blend){
                $vintage_flags |= \PRODUCT\PRODUCT_ATTRVAL_TREE_IS_NOT_BLEND;
            }
        }
        return $this->get_attrval_tree(null,null,$values,false,true,true,$vintage_flags,false,!$vintage_flags,$err);
    }
    public function get_product_filter_attrval_tree($only_used,$onlyblank,$onlyscored,$only_waiting_for_approval,$onlymyfavourites,$onlycompanyfavourites,$showproximity,&$err){
        if($only_used){
            $only_used = \PRODUCT\PRODUCT_FILTER_ONLY_USED;
            if($onlyblank){
                $only_used |= \PRODUCT\PRODUCT_FILTER_ONLY_BLANK;
            }
            if($only_waiting_for_approval){
                $only_used |= \PRODUCT\PRODUCT_FILTER_ONLY_WAITING_FOR_APPROVAL;
            }
            if($onlyscored){
                $only_used |= \PRODUCT\PRODUCT_FILTER_ONLY_SCORED;
            }
            if($onlymyfavourites){
                $only_used |= \PRODUCT\PRODUCT_FILTER_ONLY_MY_FAVOURITES;
            }
            if($onlycompanyfavourites){
                $only_used |= \PRODUCT\PRODUCT_FILTER_ONLY_COMPANY_FAVOURITES;
            }
            if($showproximity){
                $only_used |= \PRODUCT\PRODUCT_FILTER_ONLY_USED_SHOW_PROXIMITY;
            }
        }
        return $this->get_attrval_tree(null,null,null,$only_used,false,true,false,false,false,$err);
    }
    public function get_attrval_edit_attrval_tree($pag_id,$last_attr_id,$values,$system,&$err){
        return $this->get_attrval_tree(array($pag_id),$last_attr_id,$values,false,false,false,false,$system,false,$err);
    }
    public function get_attrval_tree($pag_id_arr, $last_attr_id, $attrval_ids, $only_used, $only_available_from_foundation, $onlyvisible, $for_vintage, $system, $get_doublecheck, &$err){
        if(!is_array($attrval_ids)){
            $attrval_ids = array();
        }
        if(!is_array($pag_id_arr)){
            $pag_id_arr = array();
        }
        $show_all_scores = $this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_VIEW_ALL_SCORES);
        if($only_used & \PRODUCT\PRODUCT_FILTER_ONLY_WAITING_FOR_APPROVAL){
            if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_APPROVE_PRODUCT)){
                $only_used &= ~ \PRODUCT\PRODUCT_FILTER_ONLY_WAITING_FOR_APPROVAL;
            } else {
                $only_used |= \PRODUCT\PRODUCT_FILTER_ONLY_BLANK;
            }
        }
        $last_attr_id = (int)$last_attr_id;
        if($last_attr_id<0){
            $err = langTranslate('product', 'err', 'Invalid parent',  'Invalid parent');
            return false;
        }
        $sqlpagslice = 'inner join product_attribute_group pag on pag.pag_id = pa.pag_id'
            .($onlyvisible?' and pag.pag_ishidden = 0':'')
            .($for_vintage?' and pag.pag_overload = 1':'').($for_vintage&\PRODUCT\PRODUCT_ATTRVAL_TREE_IS_NOT_BLEND?' and pag.pag_id <> 7':'')
            .($system?' and pag.pag_system = 1':' and pag.pag_system = 0')
            .($only_used?' and pag.pag_used_in_filter = 1':'');
        $sqlpagslice2 = 'inner join product_attribute_group on product_attribute_group.pag_id = product_attribute.pag_id'
            .($onlyvisible?' and product_attribute_group.pag_ishidden = 0':'')
            .($for_vintage?' and product_attribute_group.pag_overload = 1':'').($for_vintage&\PRODUCT\PRODUCT_ATTRVAL_TREE_IS_NOT_BLEND?' and product_attribute_group.pag_id <> 7':'')
            .($system?' and product_attribute_group.pag_system = 1':' and product_attribute_group.pag_system = 0')
            .($only_used?' and product_attribute_group.pag_used_in_filter = 1':'');
        if(!empty($pag_id_arr)){
            $pag_ids = array();
            foreach($pag_id_arr as $pag_id){
                $pag_id = (int)$pag_id;
                if($pag_id<=0){
                    continue;
                }
                if(!in_array($pag_id, $pag_ids)){
                    $pag_ids[] = $pag_id;
                }
            }
            if(!empty($pag_ids)){
                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE pag_ids (
                            id BIGINT UNSIGNED NOT NULL
                        )');
                foreach($pag_ids as $pag_id){
                    $this->XM->sqlcore->query('INSERT INTO pag_ids (id) VALUES ('.$pag_id.')');
                }
                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE pag_ids2 SELECT * FROM pag_ids');
                $sqlpagslice = 'inner join product_attribute_group pag on pag.pag_id = pa.pag_id'
                                    .($onlyvisible?' and pag.pag_ishidden = 0':'')
                                    .($for_vintage?' and pag.pag_overload = 1':'').($for_vintage&\PRODUCT\PRODUCT_ATTRVAL_TREE_IS_NOT_BLEND?' and pag.pag_id <> 7':'')
                                    .($system?' and pag.pag_system = 1':' and pag.pag_system = 0').'
                                inner join pag_ids on pag_ids.id = pag.pag_id';
                $sqlpagslice2 = 'inner join product_attribute_group on product_attribute_group.pag_id = product_attribute.pag_id'
                                    .($onlyvisible?' and product_attribute_group.pag_ishidden = 0':'')
                                    .($for_vintage?' and product_attribute_group.pag_overload = 1':'').($for_vintage&\PRODUCT\PRODUCT_ATTRVAL_TREE_IS_NOT_BLEND?' and product_attribute_group.pag_id <> 7':'')
                                    .($system?' and product_attribute_group.pag_system = 1':' and product_attribute_group.pag_system = 0').'
                                 inner join pag_ids2 on pag_ids2.id = product_attribute_group.pag_id';
            }
        }




        $last_attr_sql = '';
        if($last_attr_id>0){
            $last_attr_sql = 'inner join product_attribute_tree pat on pat.pa_anc_id = pa.pa_id and pat.pa_id = '.$last_attr_id;
        }
        $doublecheck_select = '0 as doublecheck';
        $doublecheck_left_join = '';
        $only_available_from_foundation_inner_join = '';
        if($get_doublecheck || $only_available_from_foundation){
            $foundation_attributes = $this->__get_foundation_attributes($attrval_ids);
            if(!empty($foundation_attributes)){
                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE foundationvals (
                    id BIGINT UNSIGNED NOT NULL
                )');
                foreach($foundation_attributes as $attrval_id){
                    $this->XM->sqlcore->query('INSERT INTO foundationvals (id) VALUES ('.((int)$attrval_id).')');
                }
                if($get_doublecheck){
                    $this->XM->sqlcore->query('CREATE TEMPORARY TABLE doublecheckpag_ids
                            select distinct pag_id
                                from product_attribute_group_dependency
                                inner join foundationvals on foundationvals.id = product_attribute_group_dependency.pav_id
                                where product_attribute_group_dependency.pagd_doublecheck = 1');
                    $doublecheck_select = 'if(doublecheckpag_ids.pag_id is not null,1,0) as doublecheck';
                    $doublecheck_left_join = 'left join doublecheckpag_ids on doublecheckpag_ids.pag_id = pa.pag_id';
                }
                if($only_available_from_foundation){
                    $this->XM->sqlcore->query('CREATE TEMPORARY TABLE onlyavailablepag_ids
                            select distinct product_attribute_group_dependency.pag_id
                                from product_attribute_group_dependency
                                inner join foundationvals on foundationvals.id = product_attribute_group_dependency.pav_id
                                where product_attribute_group_dependency.pagd_visible = 1');
                    $only_available_from_foundation_inner_join = 'inner join onlyavailablepag_ids on onlyavailablepag_ids.pag_id = pa.pag_id';
                }
                $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS foundationvals");
            } else {
                if($get_doublecheck){
                    $doublecheck_select = 'if(pa.pag_id = '.\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID.',1,0) as doublecheck';
                    $doublecheck_left_join = '';
                }
                if($only_available_from_foundation){
                    $this->XM->sqlcore->query('CREATE TEMPORARY TABLE onlyavailablepag_ids
                            select distinct product_attribute_group.pag_id
                                from product_attribute_group
                                where product_attribute_group.pag_always_visible = 1');
                    $only_available_from_foundation_inner_join = 'inner join onlyavailablepag_ids on onlyavailablepag_ids.pag_id = pa.pag_id';
                }
            }



        }

        $has_region_lock = false;

        $res = $this->XM->sqlcore->query('SELECT pa.pa_id,'.$doublecheck_select.',pag.pag_multiple,pag.pag_system,pag.pag_userfill,coalesce(pa_ml.pa_ml_name,\'-\') as pa_ml_name,pa.pag_id,pa.pa_depth,if(isnull(isparent.pa_id),0,1) as haschildren,pag.pag_zindex,pag.pag_regionlock
            FROM product_attribute pa
            '.$sqlpagslice.'
            '.$last_attr_sql.'
            '.$only_available_from_foundation_inner_join.'
            '.$doublecheck_left_join.'
            left join (select product_attribute.pa_id,substring_index(group_concat(pa_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pa_ml_id from product_attribute_ml inner join product_attribute on product_attribute.pa_id = product_attribute_ml.pa_id '.$sqlpagslice2.' where pa_ml_name is not null group by product_attribute.pa_id) as ln_glue on ln_glue.pa_id = pa.pa_id
            left join product_attribute_ml pa_ml on pa_ml.pa_ml_id = ln_glue.pa_ml_id
            left join (
                    SELECT distinct pa_parent_id as pa_id from product_attribute where product_attribute.pa_ishidden = 0
                ) isparent on isparent.pa_id = pa.pa_id');
        $attrs = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $pa_id = (int)$row['pa_id'];
            $pag_id = (int)$row['pag_id'];
            if(!$has_region_lock && $row['pag_regionlock']){
                $has_region_lock = true;
            }
            $attrs[$pa_id] = array(
                    'id'=>$pa_id,
                    'name'=>(string)$row['pa_ml_name'],
                    'group'=>$pag_id,
                    'haschildren'=>$row['haschildren']?1:0,
                    'index'=>(int)$row['pag_zindex'],
                    'depth'=>(int)$row['pa_depth'],
                    'doublecheck'=>$row['doublecheck']?1:0,
                    'regionlock'=>$row['pag_regionlock']?1:0,
                    'is_foundation'=>($pag_id==\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID)?1:0,
                    'multiple'=>$row['pag_multiple']?1:0,
                    'system'=>$row['pag_system']?1:0,
                    'can_add'=>$row['pag_userfill']?1:0,
                );
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS doublecheckpag_ids");
        $clean_attrval_ids = $this->clean_attributes($attrval_ids,false);


        $last_attr_sql = '';
        if($last_attr_id>0){
            $last_attr_sql = 'inner join product_attribute_value_tree pavt on pavt.pav_anc_id = pav.pav_id
            inner join product_attribute_value lastpav on pavt.pav_id = lastpav.pav_id and lastpav.pa_id = '.$last_attr_id.'
            inner join product_attribute_tree pat on pat.pa_anc_id = pa.pa_id and pat.pa_id = '.$last_attr_id;
        }

        $null_pag_depths = array();
        $only_sql_inner_join = '';
        if($only_used && !$system){
            $clean_onlyusedcleanfiltervalspag_ids = array();
            $checked_filtervals_union_sql =
                $overload_added_sql =
                $non_overload_added_sql = '';

            if(!empty($clean_attrval_ids)){
                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE onlyusedcleanfiltervals (
                        id BIGINT UNSIGNED NOT NULL
                    )');
                foreach($clean_attrval_ids as $attrval_id){
                    $this->XM->sqlcore->query('INSERT INTO onlyusedcleanfiltervals (id) VALUES ('.$attrval_id.')');
                }
                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE onlyusedancfiltervalsext
                    SELECT distinct product_attribute_value_tree.pav_id, product_attribute_group.pag_id, product_attribute_group.pag_overload
                        from onlyusedcleanfiltervals
                        inner join product_attribute_value on product_attribute_value.pav_id = onlyusedcleanfiltervals.id
                        inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
                        inner join product_attribute_group on product_attribute_group.pag_id = product_attribute.pag_id
                        inner join product_attribute_value_tree on product_attribute_value_tree.pav_anc_id = product_attribute_value.pav_id');
                $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS onlyusedcleanfiltervals');
                $res = $this->XM->sqlcore->query('SELECT distinct pag_id, pag_overload from onlyusedancfiltervalsext');
                while($row = $this->XM->sqlcore->getRow($res)){
                    $pag_id = (int)$row['pag_id'];
                    $clean_onlyusedcleanfiltervalspag_ids[] = $pag_id;
                    if(!$row['pag_overload']){
                        $non_overload_added_sql .= ' inner join product_value pv'.$pag_id.' on pv'.$pag_id.'.p_id = product_vintage.p_id and pv'.$pag_id.'.pag_id = '.$pag_id.'
                        inner join onlyusedancfiltervalspagno'.$pag_id.' on onlyusedancfiltervalspagno'.$pag_id.'.pav_id = pv'.$pag_id.'.pav_id or product_attribute_group.pag_id = '.$pag_id;
                        $overload_added_sql .= ' inner join product_value pv'.$pag_id.' on pv'.$pag_id.'.p_id = product_vintage.p_id and pv'.$pag_id.'.pag_id = '.$pag_id.'
                        inner join onlyusedancfiltervalspago'.$pag_id.' on onlyusedancfiltervalspago'.$pag_id.'.pav_id = pv'.$pag_id.'.pav_id';
                    } else {
                        $non_overload_added_sql .= ' inner join product_vintage_value pvv'.$pag_id.' on pvv'.$pag_id.'.pv_id = product_vintage.pv_id and pvv'.$pag_id.'.pag_id = '.$pag_id.'
                        inner join onlyusedancfiltervalspagno'.$pag_id.' on onlyusedancfiltervalspagno'.$pag_id.'.pav_id = pvv'.$pag_id.'.pav_id';
                        $overload_added_sql .= ' inner join product_vintage_value pvv'.$pag_id.' on pvv'.$pag_id.'.pv_id = product_vintage.pv_id and pvv'.$pag_id.'.pag_id = '.$pag_id.'
                        inner join onlyusedancfiltervalspago'.$pag_id.' on onlyusedancfiltervalspago'.$pag_id.'.pav_id = pvv'.$pag_id.'.pav_id or product_attribute_group.pag_id = '.$pag_id;
                    }
                }
                $this->XM->sqlcore->freeResult($res);
                foreach($clean_onlyusedcleanfiltervalspag_ids as $pag_id){
                    $this->XM->sqlcore->query('CREATE TEMPORARY TABLE onlyusedancfiltervalspago'.$pag_id.'
                        SELECT distinct pav_id
                            from onlyusedancfiltervalsext
                            where pag_id = '.$pag_id);
                    $this->XM->sqlcore->query('CREATE TEMPORARY TABLE onlyusedancfiltervalspagno'.$pag_id.'
                        SELECT pav_id
                            from onlyusedancfiltervalspago'.$pag_id);
                }
                $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS onlyusedancfiltervalsext');

                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE onlyusedcheckedfiltervals (
                        id BIGINT UNSIGNED NOT NULL
                    )');
                $distinct_ids = array();
                foreach($attrval_ids as $attrval_id){
                    $attrval_id = (int)$attrval_id;
                    if(!in_array($attrval_id, $distinct_ids)){
                        $distinct_ids[] = $attrval_id;
                        $this->XM->sqlcore->query('INSERT INTO onlyusedcheckedfiltervals (id) VALUES ('.$attrval_id.')');
                    }
                }
                $checked_filtervals_union_sql = 'UNION DISTINCT
                            select id as pav_id
                                from onlyusedcheckedfiltervals';
            }



            $vintage_only_blank_sql = $vintage_only_blank_inner_join_sql = $vintage_only_waiting_for_approval_inner_join_sql = $vintage_scores_left_join = '';
            if($only_used & \PRODUCT\PRODUCT_FILTER_ONLY_BLANK){
                $vintage_only_blank_sql = ' and product_vintage.pv_blank = 1';//only blanks
            } else {
                $vintage_only_blank_inner_join_sql = 'inner join product on product.p_id = product_vintage.p_id and ( product.p_isvintage xor product_vintage.pv_blank )';//blanks only if no vintage exists
            }
            if($only_used & \PRODUCT\PRODUCT_FILTER_ONLY_WAITING_FOR_APPROVAL){
                $vintage_only_waiting_for_approval_inner_join_sql = 'inner join product product_approval_filter on product_approval_filter.p_id = product_vintage.p_id and product_approval_filter.p_is_approved = 0';
            } else {
                $vintage_only_waiting_for_approval_inner_join_sql = 'inner join product product_approval_filter on product_approval_filter.p_id = product_vintage.p_id and ( product_approval_filter.p_is_approved = 1 or product_approval_filter.company_id = '.$this->XM->user->getCompanyId().' )';
            }
            $personal_score_left_join = $personal_score_inner_join = '';
            $onlyscored_sql = '';
            if($only_used & \PRODUCT\PRODUCT_FILTER_ONLY_PERSONALLY_SCORED){
                $personal_score_inner_join = 'inner join product_vintage_personal_score on product_vintage_personal_score.pv_id = product_vintage.pv_id and product_vintage_personal_score.user_id = '.$this->XM->user->getUserId();
            } else {
                if($only_used & \PRODUCT\PRODUCT_FILTER_ONLY_SCORED){
                    $personal_score_left_join = 'left join product_vintage_personal_score on product_vintage_personal_score.pv_id = product_vintage.pv_id and product_vintage_personal_score.user_id = '.$this->XM->user->getUserId();
                    if($show_all_scores){
                        $vintage_scores_left_join = 'left join product_vintage_score product_vintage_score1 on product_vintage_score1.pv_id = product_vintage.pv_id and product_vintage_score1.user_expert_level = 1
                        left join product_vintage_score product_vintage_score2 on product_vintage_score2.pv_id = product_vintage.pv_id and product_vintage_score2.user_expert_level = 2
                        left join product_vintage_score product_vintage_score3 on product_vintage_score3.pv_id = product_vintage.pv_id and product_vintage_score3.user_expert_level = 3';
                        $onlyscored_sql = 'and ( product_vintage_score1.pvs_score is not null or product_vintage_score2.pvs_score is not null or product_vintage_score3.pvs_score is not null or product_vintage_personal_score.pvps_score is not null )';
                    } else {
                        $vintage_scores_left_join = 'left join product_vintage_score product_vintage_score3 on product_vintage_score3.pv_id = product_vintage.pv_id and product_vintage_score3.user_expert_level = 3';
                        $onlyscored_sql = 'and ( product_vintage_score3.pvs_score is not null or product_vintage_personal_score.pvps_score is not null )';
                    }
                }
            }
            $only_awarded_where_sql = '';
            if($only_used & \PRODUCT\PRODUCT_FILTER_ONLY_AWARDED){
                $only_awarded_where_sql = 'and product_vintage.pv_won_contest_nominations = 1';
            }
            $only_my_favourites_inner_join_sql = '';
            if($only_used & \PRODUCT\PRODUCT_FILTER_ONLY_MY_FAVOURITES){
                $only_my_favourites_inner_join_sql = 'inner join product_vintage_favourite on product_vintage_favourite.pv_id = product_vintage.pv_id and product_vintage_favourite.user_id = '.$this->XM->user->getUserId();
            }
            $only_company_favourites_inner_join_sql = '';
            if($only_used & \PRODUCT\PRODUCT_FILTER_ONLY_COMPANY_FAVOURITES){
                $only_company_favourites_inner_join_sql = 'inner join product_company_favourite on product_company_favourite.company_id = '.$this->XM->user->getCompanyId().' and product_company_favourite.p_id = product_vintage.p_id';
            }


            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE onlyusedfiltervals
                SELECT distinct product_attribute_value_tree.pav_anc_id as pav_id
                    from (
                        SELECT distinct product_value.pav_id
                            from product_value
                            inner join product_attribute_group on product_attribute_group.pag_id = product_value.pag_id and product_attribute_group.pag_overload = 0
                            inner join product_vintage on product_vintage.p_id = product_value.p_id '.$vintage_only_blank_sql.'
                            '.$vintage_only_blank_inner_join_sql.'
                            '.$vintage_only_waiting_for_approval_inner_join_sql.'
                            '.$only_my_favourites_inner_join_sql.'
                            '.$only_company_favourites_inner_join_sql.'
                            '.$personal_score_inner_join.'
                            '.$non_overload_added_sql.'
                            '.$personal_score_left_join.'
                            '.$vintage_scores_left_join.'
                            where 1=1 '.$onlyscored_sql.' '.$only_awarded_where_sql.'
                        UNION DISTINCT
                        SELECT distinct product_vintage_value.pav_id
                            from product_vintage_value
                            inner join product_attribute_group on product_attribute_group.pag_id = product_vintage_value.pag_id and product_attribute_group.pag_overload = 1
                            inner join product_vintage on product_vintage.pv_id = product_vintage_value.pv_id '.$vintage_only_blank_sql.'
                            '.$vintage_only_blank_inner_join_sql.'
                            '.$vintage_only_waiting_for_approval_inner_join_sql.'
                            '.$only_my_favourites_inner_join_sql.'
                            '.$only_company_favourites_inner_join_sql.'
                            '.$personal_score_inner_join.'
                            '.$overload_added_sql.'
                            '.$personal_score_left_join.'
                            '.$vintage_scores_left_join.'
                            where 1=1 '.$onlyscored_sql.' '.$only_awarded_where_sql.'
                        '.$checked_filtervals_union_sql.'
                    ) as product_vintage_value
                    inner join product_attribute_value_tree on product_attribute_value_tree.pav_id = product_vintage_value.pav_id
                    group by product_attribute_value_tree.pav_anc_id');

            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE lonelypadepths
                SELECT distinct product_attribute.pag_id, product_attribute.pa_depth
                from onlyusedfiltervals
                inner join product_attribute_value on product_attribute_value.pav_id = onlyusedfiltervals.pav_id
                inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
                group by product_attribute.pag_id, product_attribute.pa_depth
                having count(distinct product_attribute_value.pav_id)=1');
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE lonelypadepths2
                select * from lonelypadepths');
            $res = $this->XM->sqlcore->query('SELECT distinct lonelypadepths.pag_id, lonelypadepths.pa_depth
                    from product_vintage
                    inner join lonelypadepths on 1=1
                    inner join product_attribute_group on product_attribute_group.pag_id = lonelypadepths.pag_id and product_attribute_group.pag_overload = 0
                    '.$vintage_only_blank_inner_join_sql.'
                    '.$vintage_only_waiting_for_approval_inner_join_sql.'
                    '.$only_my_favourites_inner_join_sql.'
                    '.$only_company_favourites_inner_join_sql.'
                    '.$personal_score_inner_join.'
                    '.$non_overload_added_sql.'
                    '.$personal_score_left_join.'
                    '.$vintage_scores_left_join.'
                    left join product_value product_value_cur_pag on product_value_cur_pag.p_id = product_vintage.p_id and product_value_cur_pag.pag_id = lonelypadepths.pag_id
                    left join product_attribute_value_tree on product_attribute_value_tree.pav_id = product_value_cur_pag.pav_id
                    left join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_tree.pav_anc_id
                    left join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pag_id = lonelypadepths.pag_id and product_attribute.pa_depth = lonelypadepths.pa_depth
                    where 1=1 '.$onlyscored_sql.' '.$vintage_only_blank_sql.' '.$only_awarded_where_sql.'
                    group by lonelypadepths.pag_id, lonelypadepths.pa_depth
                    having sum(if(product_value_cur_pag.p_id is null,1,0)) > 0 or count(distinct product_vintage.pv_id)-count(distinct(if(product_attribute.pa_id is not null,product_vintage.pv_id,null))) > 0
                UNION DISTINCT
                SELECT distinct lonelypadepths2.pag_id, lonelypadepths2.pa_depth
                    from product_vintage
                    inner join lonelypadepths2 on 1=1
                    inner join product_attribute_group on product_attribute_group.pag_id = lonelypadepths2.pag_id and product_attribute_group.pag_overload = 1
                    '.$vintage_only_blank_inner_join_sql.'
                    '.$vintage_only_waiting_for_approval_inner_join_sql.'
                    '.$only_my_favourites_inner_join_sql.'
                    '.$only_company_favourites_inner_join_sql.'
                    '.$personal_score_inner_join.'
                    '.$overload_added_sql.'
                    '.$personal_score_left_join.'
                    '.$vintage_scores_left_join.'
                    left join product_vintage_value product_vintage_value_cur_pag on product_vintage_value_cur_pag.pv_id = product_vintage.pv_id and product_vintage_value_cur_pag.pag_id = lonelypadepths2.pag_id
                    left join product_attribute_value_tree on product_attribute_value_tree.pav_id = product_vintage_value_cur_pag.pav_id
                    left join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_tree.pav_anc_id
                    left join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pag_id = lonelypadepths2.pag_id and product_attribute.pa_depth = lonelypadepths2.pa_depth
                    where 1=1 '.$onlyscored_sql.' '.$vintage_only_blank_sql.' '.$only_awarded_where_sql.'
                    group by lonelypadepths2.pag_id, lonelypadepths2.pa_depth
                    having sum(if(product_vintage_value_cur_pag.pv_id is null,1,0)) > 0 or count(distinct product_vintage.pv_id)-count(distinct(if(product_attribute.pa_id is not null,product_vintage.pv_id,null))) > 0');
            while($row = $this->XM->sqlcore->getRow($res)){
                $null_pag_depths[] = array((int)$row['pag_id'],(int)$row['pa_depth']);
            }
            $this->XM->sqlcore->freeResult($res);
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS lonelypadepths');
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS lonelypadepths2');
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS onlyusedcheckedfiltervals');
            foreach($clean_onlyusedcleanfiltervalspag_ids as $pag_id){
                $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS onlyusedancfiltervalspago'.$pag_id);
                $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS onlyusedancfiltervalspagno'.$pag_id);
            }
            $only_sql_inner_join = 'inner join onlyusedfiltervals on onlyusedfiltervals.pav_id = pav.pav_id';
        }

        //region lock
        $region_lock_select_sql = '0 as regionlock_in_region';
        $region_lock_left_join = '';
        if(!$only_used && $has_region_lock && !empty($clean_attrval_ids)){
            $clean_onlyusedcleanfiltervalspag_ids = array();
            $checked_filtervals_union_sql =
                $overload_added_sql =
                $non_overload_added_sql = '';

            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE regionlockcleanfiltervals (
                    id BIGINT UNSIGNED NOT NULL
                )');
            foreach($clean_attrval_ids as $attrval_id){
                $this->XM->sqlcore->query('INSERT INTO regionlockcleanfiltervals (id) VALUES ('.$attrval_id.')');
            }
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE regionlockregionpavids
                SELECT distinct product_attribute_value_tree.pav_id, product_attribute.pa_depth
                    from regionlockcleanfiltervals
                    inner join product_attribute_value_tree on product_attribute_value_tree.pav_id = regionlockcleanfiltervals.id
                    inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_tree.pav_anc_id
                    inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pa_depth <= 1
                    inner join product_attribute_group on product_attribute_group.pag_id = product_attribute.pag_id and product_attribute_group.pag_id = 8');
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS regionlockcleanfiltervals');

            $res = $this->XM->sqlcore->query('SELECT max(pa_depth) as max_pa_depth from regionlockregionpavids');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if($row){
                $this->XM->sqlcore->query('DELETE from regionlockregionpavids where pa_depth < '.(int)$row['max_pa_depth']);
            }

            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE regionlockpavids
                (PRIMARY KEY regionlockpavids_pk (pav_id))
                SELECT distinct product_attribute_value_tree.pav_anc_id as pav_id
                    from product_attribute_value
                    inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
                    inner join product_attribute_group on product_attribute_group.pag_id = product_attribute.pag_id and product_attribute_group.pag_regionlock = 1
                    '.(!empty($pag_ids)?'inner join pag_ids on pag_ids.id = product_attribute_group.pag_id':'').'
                    inner join product_value pvrl on pvrl.pav_id = product_attribute_value.pav_id
                    inner join product_value pvr on pvr.p_id = pvrl.p_id and pvr.pag_id = 8
                    inner join product_attribute_value_tree pavtr on pavtr.pav_id = pvr.pav_id
                    inner join regionlockregionpavids on regionlockregionpavids.pav_id = pavtr.pav_anc_id
                    inner join product_attribute_value_tree on product_attribute_value_tree.pav_id = product_attribute_value.pav_id');
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS regionlockregionpavids');

            $region_lock_select_sql = 'if(isnull(regionlockpavids.pav_id),0,1) as regionlock_in_region';
            $region_lock_left_join = 'left join regionlockpavids on regionlockpavids.pav_id = product_attribute_value.pav_id';
        }







        $attrval_slice_sql_inner_join = '';
        $attrval_sel_sql_left_join = '';
        $attrval_sel_sql_select = '0 as selected';
        if(!empty($attrval_ids)){
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE cleanfiltervals (
                    id BIGINT UNSIGNED NOT NULL
                )');
            foreach($clean_attrval_ids as $attrval_id){
                $this->XM->sqlcore->query('INSERT INTO cleanfiltervals (id) VALUES ('.$attrval_id.')');
            }
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtervals (
                    id BIGINT UNSIGNED NOT NULL
                )');
            $distinct_ids = array();
            foreach($attrval_ids as $attrval_id){
                $attrval_id = (int)$attrval_id;
                if(!in_array($attrval_id, $distinct_ids)){
                    $distinct_ids[] = $attrval_id;
                    $this->XM->sqlcore->query('INSERT INTO filtervals (id) VALUES ('.$attrval_id.')');
                }
            }
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE cleanfiltervals2 SELECT * FROM cleanfiltervals');
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtervalsforsel SELECT * FROM filtervals');
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtervalpagsforunchecked
                (UNIQUE pag_id_index (pag_id))
                SELECT distinct product_attribute.pag_id FROM filtervals
                    inner join product_attribute_value on product_attribute_value.pav_id = filtervals.id
                    inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id');
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtervalsslice
                select distinct product_attribute_value_tree.pav_anc_id as pav_id
                    from (
                        select distinct product_attribute_value_tree.pav_id
                            from product_attribute_value_tree
                            inner join cleanfiltervals on cleanfiltervals.id = product_attribute_value_tree.pav_anc_id
                        union distinct
                        select distinct product_attribute_value.pav_id
                            from product_attribute_value
                            inner join product_attribute_value_tree on product_attribute_value_tree.pav_anc_id = product_attribute_value.pav_parent_id
                            inner join cleanfiltervals2 on cleanfiltervals2.id = product_attribute_value_tree.pav_id
                        union distinct
                        select distinct product_attribute_value.pav_id
                            from product_attribute_value
                            inner join filtervals on product_attribute_value.pav_parent_id = filtervals.id
                        union distinct
                        select product_attribute_value.pav_id
                            from product_attribute_value
                            where product_attribute_value.pav_parent_id = 0
                        union distinct
                        select product_attribute_value.pav_id
                            from product_attribute_value
                            inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
                            left join filtervalpagsforunchecked on filtervalpagsforunchecked.pag_id = product_attribute.pag_id
                            where filtervalpagsforunchecked.pag_id is null
                        ) children
                    inner join product_attribute_value_tree on product_attribute_value_tree.pav_id = children.pav_id');
            $attrval_slice_sql_inner_join = 'inner join filtervalsslice on filtervalsslice.pav_id = pav.pav_id';
            $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filtervalpagsforunchecked");
            $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filtervals");
            $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filtervalsforunchecked");
            $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS cleanfiltervals");
            $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS cleanfiltervals2");
            $attrval_sel_sql_left_join = 'left join (select distinct pavt.pav_anc_id from product_attribute_value_tree pavt inner join filtervalsforsel fv on pavt.pav_id = fv.id) pavtsel on pavtsel.pav_anc_id = pav.pav_id';
            $attrval_sel_sql_select = 'if(pavtsel.pav_anc_id is null,0,1) as selected';
        }
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filterpavids
            (UNIQUE pav_id_index (pav_id))
            select distinct pav.pav_id, pa.pag_id, pa.pa_depth, '.$attrval_sel_sql_select.'
                from product_attribute_value pav
            inner join product_attribute pa on pa.pa_id = pav.pa_id'.($onlyvisible?' and pa.pa_ishidden = 0':'').'
            '.$sqlpagslice.'
            '.$only_available_from_foundation_inner_join.'
            '.$only_sql_inner_join.'
            '.$last_attr_sql.'
            '.$attrval_slice_sql_inner_join.'
            '.$attrval_sel_sql_left_join);
        $res = $this->XM->sqlcore->query('SELECT count(pav_id) as cnt,sum(selected) as cnt_sel, pag_id, pa_depth
            from filterpavids
            group by pag_id, pa_depth
            order by pag_id asc, pa_depth asc');
        $break_group = null;
        $break_groups = array();
        $select_group_depths = array();
        $last_group = null;
        while($row = $this->XM->sqlcore->getRow($res)){
            $depth = (int)$row['pa_depth'];
            $group = (int)$row['pag_id'];
            if($break_group === $group){
                continue;
            }
            if($row['cnt']==1){
                if($last_group!=$group){
                    $null_pag_depths[] = array($group,$depth);
                } else {
                    if($row['cnt_sel']==0){
                        $is_null_pag_depth = false;
                        foreach($null_pag_depths as list($null_pag_id,$null_depth)){
                            if($group==$null_pag_id && $depth==$null_depth){
                                $is_null_pag_depth = true;
                                break;
                            }
                        }
                        if(!$is_null_pag_depth){
                            $row['cnt_sel'] = 1;
                            $select_group_depths[] = array($group,$depth);
                        }
                    }
                }
            }

            if($row['cnt_sel']==0){
                $break_groups[$group] = $depth;
                $break_group = $group;
            }
            $last_group = $group;
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filtervalsslice");
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filtervalsforsel");
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS pag_ids");
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS pag_ids2");
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS onlyavailablepag_ids");
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS onlyusedfiltervals");
        foreach($break_groups as $group=>$depth){
            $this->XM->sqlcore->query('DELETE FROM filterpavids where pag_id = '.$group.' and pa_depth > '.$depth);
        }
        foreach($select_group_depths as list($group,$depth)){
            $this->XM->sqlcore->query('UPDATE filterpavids set selected = 1 where pag_id = '.$group.' and pa_depth = '.$depth);
        }
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filterpavids2
            (UNIQUE pav_id_index (pav_id))
            SELECT pav_id from filterpavids');
        $this->XM->sqlcore->query('INSERT INTO filterpavids (pav_id,pag_id,pa_depth,selected)
            SELECT distinct product_attribute_value.pav_id,product_attribute.pag_id,product_attribute.pa_depth,0 as selected
                from product_value
                inner join product_attribute_value on product_attribute_value.pav_id = product_value.pav_id
                inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
                left join filterpavids2 on filterpavids2.pav_id = product_attribute_value.pav_id
                where filterpavids2.pav_id is null and product_attribute.pag_id = '.\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID);
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filterpavids2");

        $only_sql_score_select = 'null as proximityscore';
        $only_sql_score_inner_join = '';
        if($only_used && !$system && ($only_used&\PRODUCT\PRODUCT_FILTER_ONLY_USED_SHOW_PROXIMITY)){
            $new_attrval_ids = array();
            $res = $this->XM->sqlcore->query('SELECT pav_id from filterpavids where selected = 1');
            while($row = $this->XM->sqlcore->getRow($res)){
                $new_attrval_ids[] = (int)$row['pav_id'];
            }
            $this->XM->sqlcore->freeResult($res);
            $clean_attrval_ids = $this->clean_attributes($new_attrval_ids,false);

            $clean_onlyusedcleanfiltervalspag_ids = array();
            $checked_filtervals_union_sql =
                $overload_added_sql =
                $non_overload_added_sql = '';

            if(!empty($clean_attrval_ids)){
                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE onlyusedcleanfiltervals (
                        id BIGINT UNSIGNED NOT NULL
                    )');
                foreach($clean_attrval_ids as $attrval_id){
                    $this->XM->sqlcore->query('INSERT INTO onlyusedcleanfiltervals (id) VALUES ('.$attrval_id.')');
                }
                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE onlyusedancfiltervalsext
                    SELECT distinct product_attribute_value_tree.pav_id, product_attribute_group.pag_id, product_attribute_group.pag_overload
                        from onlyusedcleanfiltervals
                        inner join product_attribute_value on product_attribute_value.pav_id = onlyusedcleanfiltervals.id
                        inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
                        inner join product_attribute_group on product_attribute_group.pag_id = product_attribute.pag_id
                        inner join product_attribute_value_tree on product_attribute_value_tree.pav_anc_id = product_attribute_value.pav_id');
                $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS onlyusedcleanfiltervals');
                $res = $this->XM->sqlcore->query('SELECT distinct pag_id, pag_overload from onlyusedancfiltervalsext');
                while($row = $this->XM->sqlcore->getRow($res)){
                    $pag_id = (int)$row['pag_id'];
                    $clean_onlyusedcleanfiltervalspag_ids[] = $pag_id;
                    if(!$row['pag_overload']){
                        $non_overload_added_sql .= ' inner join product_value pv'.$pag_id.' on pv'.$pag_id.'.p_id = product_vintage.p_id and pv'.$pag_id.'.pag_id = '.$pag_id.'
                        inner join onlyusedancfiltervalspagno'.$pag_id.' on onlyusedancfiltervalspagno'.$pag_id.'.pav_id = pv'.$pag_id.'.pav_id or product_attribute_group.pag_id = '.$pag_id;
                        $overload_added_sql .= ' inner join product_value pv'.$pag_id.' on pv'.$pag_id.'.p_id = product_vintage.p_id and pv'.$pag_id.'.pag_id = '.$pag_id.'
                        inner join onlyusedancfiltervalspago'.$pag_id.' on onlyusedancfiltervalspago'.$pag_id.'.pav_id = pv'.$pag_id.'.pav_id';
                    } else {
                        $non_overload_added_sql .= ' inner join product_vintage_value pvv'.$pag_id.' on pvv'.$pag_id.'.pv_id = product_vintage.pv_id and pvv'.$pag_id.'.pag_id = '.$pag_id.'
                        inner join onlyusedancfiltervalspagno'.$pag_id.' on onlyusedancfiltervalspagno'.$pag_id.'.pav_id = pvv'.$pag_id.'.pav_id';
                        $overload_added_sql .= ' inner join product_vintage_value pvv'.$pag_id.' on pvv'.$pag_id.'.pv_id = product_vintage.pv_id and pvv'.$pag_id.'.pag_id = '.$pag_id.'
                        inner join onlyusedancfiltervalspago'.$pag_id.' on onlyusedancfiltervalspago'.$pag_id.'.pav_id = pvv'.$pag_id.'.pav_id or product_attribute_group.pag_id = '.$pag_id;
                    }
                }
                $this->XM->sqlcore->freeResult($res);
                foreach($clean_onlyusedcleanfiltervalspag_ids as $pag_id){
                    $this->XM->sqlcore->query('CREATE TEMPORARY TABLE onlyusedancfiltervalspago'.$pag_id.'
                        SELECT distinct pav_id
                            from onlyusedancfiltervalsext
                            where pag_id = '.$pag_id);
                    $this->XM->sqlcore->query('CREATE TEMPORARY TABLE onlyusedancfiltervalspagno'.$pag_id.'
                        SELECT pav_id
                            from onlyusedancfiltervalspago'.$pag_id);
                }
                $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS onlyusedancfiltervalsext');

                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE onlyusedcheckedfiltervals (
                        id BIGINT UNSIGNED NOT NULL
                    )');
                $distinct_ids = array();
                foreach($attrval_ids as $attrval_id){
                    $attrval_id = (int)$attrval_id;
                    if(!in_array($attrval_id, $distinct_ids)){
                        $distinct_ids[] = $attrval_id;
                        $this->XM->sqlcore->query('INSERT INTO onlyusedcheckedfiltervals (id) VALUES ('.$attrval_id.')');
                    }
                }
                $checked_filtervals_union_sql = 'UNION DISTINCT
                            select id as pav_id
                                from onlyusedcheckedfiltervals';
            }


            $vintage_only_blank_sql = $vintage_only_blank_inner_join_sql = $vintage_only_waiting_for_approval_inner_join_sql = $vintage_scores_left_join = '';
            if($only_used & \PRODUCT\PRODUCT_FILTER_ONLY_BLANK){
                $vintage_only_blank_sql = ' and product_vintage.pv_blank = 1';//only blanks
            } else {
                $vintage_only_blank_inner_join_sql = 'inner join product on product.p_id = product_vintage.p_id and ( product.p_isvintage xor product_vintage.pv_blank )';//blanks only if no vintage exists
            }
            if($only_used & \PRODUCT\PRODUCT_FILTER_ONLY_WAITING_FOR_APPROVAL){
                $vintage_only_waiting_for_approval_inner_join_sql = 'inner join product product_approval_filter on product_approval_filter.p_id = product_vintage.p_id and product_approval_filter.p_is_approved = 0';
            } else {
                $vintage_only_waiting_for_approval_inner_join_sql = 'inner join product product_approval_filter on product_approval_filter.p_id = product_vintage.p_id and ( product_approval_filter.p_is_approved = 1 or product_approval_filter.company_id = '.$this->XM->user->getCompanyId().' )';
            }
            $personal_score_left_join = $personal_score_inner_join = '';
            $onlyscored_sql = '';
            if($only_used & \PRODUCT\PRODUCT_FILTER_ONLY_PERSONALLY_SCORED){
                $personal_score_inner_join = 'inner join product_vintage_personal_score on product_vintage_personal_score.pv_id = product_vintage.pv_id and product_vintage_personal_score.user_id = '.$this->XM->user->getUserId();
            } else {
                if($only_used & \PRODUCT\PRODUCT_FILTER_ONLY_SCORED){
                    $personal_score_left_join = 'left join product_vintage_personal_score on product_vintage_personal_score.pv_id = product_vintage.pv_id and product_vintage_personal_score.user_id = '.$this->XM->user->getUserId();
                    if($show_all_scores){
                        $vintage_scores_left_join = 'left join product_vintage_score product_vintage_score1 on product_vintage_score1.pv_id = product_vintage.pv_id and product_vintage_score1.user_expert_level = 1
                        left join product_vintage_score product_vintage_score2 on product_vintage_score2.pv_id = product_vintage.pv_id and product_vintage_score2.user_expert_level = 2
                        left join product_vintage_score product_vintage_score3 on product_vintage_score3.pv_id = product_vintage.pv_id and product_vintage_score3.user_expert_level = 3';
                        $onlyscored_sql = 'and ( product_vintage_score1.pvs_score is not null or product_vintage_score2.pvs_score is not null or product_vintage_score3.pvs_score is not null or product_vintage_personal_score.pvps_score is not null )';
                    } else {
                        $vintage_scores_left_join = 'left join product_vintage_score product_vintage_score3 on product_vintage_score3.pv_id = product_vintage.pv_id and product_vintage_score3.user_expert_level = 3';
                        $onlyscored_sql = 'and ( product_vintage_score3.pvs_score is not null or product_vintage_personal_score.pvps_score is not null )';
                    }
                }
            }
            $only_awarded_where_sql = '';
            if($only_used & \PRODUCT\PRODUCT_FILTER_ONLY_AWARDED){
                $only_awarded_where_sql = 'and product_vintage.pv_won_contest_nominations = 1';
            }
            $only_my_favourites_inner_join_sql = '';
            if($only_used & \PRODUCT\PRODUCT_FILTER_ONLY_MY_FAVOURITES){
                $only_my_favourites_inner_join_sql = 'inner join product_vintage_favourite on product_vintage_favourite.pv_id = product_vintage.pv_id and product_vintage_favourite.user_id = '.$this->XM->user->getUserId();
            }
            $only_company_favourites_inner_join_sql = '';
            if($only_used & \PRODUCT\PRODUCT_FILTER_ONLY_COMPANY_FAVOURITES){
                $only_company_favourites_inner_join_sql = 'inner join product_company_favourite on product_company_favourite.company_id = '.$this->XM->user->getCompanyId().' and product_company_favourite.p_id = product_vintage.p_id';
            }

            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE onlyusedfiltervalscores
                (UNIQUE pav_id_index (pav_id))
                SELECT coalesce(product_attribute_value_analog.pav_id, filtervalscores.pav_id) as pav_id, sum(filtervalscores.score) as score
                    from (SELECT product_attribute_value_tree.pav_anc_id as pav_id, sum(product_vintage_value.cnt) as score
                        from (
                            SELECT product_value.pav_id, count(distinct product_vintage.pv_id) as cnt
                                from product_value
                                inner join product_attribute_group on product_attribute_group.pag_id = product_value.pag_id and product_attribute_group.pag_overload = 0
                                inner join product_vintage on product_vintage.p_id = product_value.p_id '.$vintage_only_blank_sql.'
                                '.$vintage_only_blank_inner_join_sql.'
                                '.$vintage_only_waiting_for_approval_inner_join_sql.'
                                '.$only_my_favourites_inner_join_sql.'
                                '.$only_company_favourites_inner_join_sql.'
                                '.$personal_score_inner_join.'
                                '.$non_overload_added_sql.'
                                '.$personal_score_left_join.'
                                '.$vintage_scores_left_join.'
                                where 1=1 '.$onlyscored_sql.' '.$only_awarded_where_sql.'
                                group by product_value.pav_id
                            UNION DISTINCT
                            SELECT product_vintage_value.pav_id, count(distinct product_vintage.pv_id) as cnt
                                from product_vintage_value
                                inner join product_attribute_group on product_attribute_group.pag_id = product_vintage_value.pag_id and product_attribute_group.pag_overload = 1
                                inner join product_vintage on product_vintage.pv_id = product_vintage_value.pv_id '.$vintage_only_blank_sql.'
                                '.$vintage_only_blank_inner_join_sql.'
                                '.$vintage_only_waiting_for_approval_inner_join_sql.'
                                '.$only_my_favourites_inner_join_sql.'
                                '.$only_company_favourites_inner_join_sql.'
                                '.$personal_score_inner_join.'
                                '.$overload_added_sql.'
                                '.$personal_score_left_join.'
                                '.$vintage_scores_left_join.'
                                where 1=1 '.$onlyscored_sql.' '.$only_awarded_where_sql.'
                                group by product_vintage_value.pav_id
                            UNION DISTINCT
                            SELECT filterpavids.pav_id, 0 as cnt
                                from filterpavids
                        ) as product_vintage_value
                        inner join product_attribute_value_tree on product_attribute_value_tree.pav_id = product_vintage_value.pav_id
                        group by product_attribute_value_tree.pav_anc_id
                    ) as filtervalscores
                    left join product_attribute_value_analog pava on pava.pav_id = filtervalscores.pav_id
                    left join product_attribute_value_analog on product_attribute_value_analog.pava_group_id = pava.pava_group_id
                    group by coalesce(product_attribute_value_analog.pav_id, filtervalscores.pav_id)');

            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS onlyusedcheckedfiltervals');
            foreach($clean_onlyusedcleanfiltervalspag_ids as $pag_id){
                $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS onlyusedancfiltervalspago'.$pag_id);
                $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS onlyusedancfiltervalspagno'.$pag_id);
            }

            $only_sql_score_select = 'onlyusedfiltervalscores.score as proximityscore';
            $only_sql_score_inner_join = 'inner join onlyusedfiltervalscores on onlyusedfiltervalscores.pav_id = product_attribute_value.pav_id';

        }

        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filterpavids2
            (UNIQUE pav_id_index (pav_id))
            SELECT pav_id from filterpavids');
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filterpavids3
            (UNIQUE pav_id_index (pav_id))
            SELECT pav_id from filterpavids');


        $res = $this->XM->sqlcore->query('SELECT distinct product_attribute_value.pav_id,product_attribute.pa_id,product_attribute.pag_id,product_attribute_group.pag_zindex,coalesce(pav_ml.pav_ml_name,product_attribute_value.pav_origin_name,\'-\') as pav_ml_name, if(product_attribute.pa_has_important_values=1,product_attribute_value.pav_important,1) as pav_important, filterpavids.selected, product_attribute.pa_depth, '.$only_sql_score_select.', '.$region_lock_select_sql.', coalesce(product_attribute_value_se.pav_se_text,\'\') as pav_se_text
            FROM product_attribute_value
            inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
            inner join product_attribute_group on product_attribute_group.pag_id = product_attribute.pag_id
            inner join filterpavids on filterpavids.pav_id = product_attribute_value.pav_id
            '.$only_sql_score_inner_join.'
            left join (
                select product_attribute_value.pav_id,substring_index(group_concat(pav_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pav_ml_id
                    from product_attribute_value_ml
                    inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_ml.pav_id
                    inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pa_show_only_origin = 0
                    inner join filterpavids2 on filterpavids2.pav_id = product_attribute_value.pav_id
                    where pav_ml_name is not null and not (pav_origin_name is not null and lang_id <> '.$this->XM->lang->getCurrLangId().')
                    group by product_attribute_value.pav_id
                ) as ln_glue on ln_glue.pav_id = product_attribute_value.pav_id
            left join product_attribute_value_ml pav_ml on pav_ml.pav_ml_id = ln_glue.pav_ml_id
            left join (
                select product_attribute_value_se.pav_id, group_concat(distinct product_attribute_value_se.pav_se_text separator \' \') as pav_se_text
                    from product_attribute_value_se
                    inner join filterpavids3 on filterpavids3.pav_id = product_attribute_value_se.pav_id
                    group by product_attribute_value_se.pav_id
            ) as product_attribute_value_se on product_attribute_value_se.pav_id = product_attribute_value.pav_id
            '.$region_lock_left_join.'
            order by product_attribute_group.pag_zindex asc,product_attribute.pa_depth asc, product_attribute.pa_id, 5 asc');
        $last_pa_id = $last_depth = $last_group = null;
        $counter = null;
        $selected = true;
        $result = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $pa_id = (int)$row['pa_id'];
            if(!isset($attrs[$pa_id])){
                continue;
            }
            $depth = (int)$row['pa_depth'];//$attrs[$pa_id]['depth'];
            $group = (int)$row['pag_id'];//$attrs[$pa_id]['depth'];
            if(!isset($result[$group][$depth][$pa_id])){
                $result[$group][$depth][$pa_id] = $attrs[$pa_id];
                $result[$group][$depth][$pa_id]['vals'] = array();
                $is_null_pag_depth = false;
                foreach($null_pag_depths as list($null_pag_id,$null_depth)){
                    if($group==$null_pag_id && $depth==$null_depth){
                        $is_null_pag_depth = true;
                        break;
                    }
                }
                if($is_null_pag_depth){
                    $result[$group][$depth][$pa_id]['can_null'] = 1;
                }
            }
            $exploded_se_texts = explode(' ',$row['pav_se_text']);
            $cleared_se_texts = array();
            foreach($exploded_se_texts as $se_text){
                if(strlen($se_text)<3){
                    continue;
                }
                if(in_array($se_text, $cleared_se_texts)){
                    continue;
                }
                $cleared_se_texts[] = $se_text;
            }
            $val = array(
                    'id'=>(int)$row['pav_id'],
                    'name'=>(string)$row['pav_ml_name'],
                    'selected'=>$row['selected']?1:0,
                    'important'=>$row['pav_important']?1:0,
                    'score'=>(int)$row['proximityscore'],
                    'setext'=>implode(' ',$cleared_se_texts),
                );
            if($row['regionlock_in_region']){
                $val['regionlock_in_region'] = 1;
            }
            $result[$group][$depth][$pa_id]['vals'][] = $val;
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filterpavids");
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filterpavids2");
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filterpavids3");
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS onlyusedfiltervalscores");
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS regionlockpavids");
        return $result;
    }
    private function __get_fullname_template_schemes(){
        return array(1=>array(11,'name',8,10), 2=>array(9,11,'name',8,10));
    }
    private function __get_fullname_template_ignore_pav_list(){
        return array(1804/*wine*/,1808/*dry*/);
    }
    private function __generate_full_name_templates($partvalues){
        $result = array();
        $templates = $this->__get_fullname_template_schemes();
        foreach($templates as $lang_id=>$template){
            $template_arr = array();
            foreach($template as $part){
                if($part==='name'){
                    $template_arr[] = '{{name}}';
                } elseif(isset($partvalues[$part])&&isset($partvalues[$part][$lang_id])){
                    $template_arr[] = $partvalues[$part][$lang_id];
                }
            }
            $result[$lang_id] = implode(' ', $template_arr);
        }
        return $result;
    }
    public function get_full_name_templates($values){
        if(!is_array($values)){
            return array();
        }
        $templates = $this->__get_fullname_template_schemes();
        $pag_ids = array();
        foreach($templates as $template){
            foreach($template as $part){
                if($part!=='name'){
                    if(!in_array($part, $pag_ids)){
                        $pag_ids[] = $part;
                    }
                }
            }
        }
        $partvalues = array();
        $template_ignore_pav_list = $this->__get_fullname_template_ignore_pav_list();
        if(!empty($values)){
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtervals (
                    id BIGINT UNSIGNED NOT NULL
                )');
            $distinct_ids = array();
            foreach($values as $attrval_id){
                $attrval_id = (int)$attrval_id;
                if(!in_array($attrval_id, $distinct_ids) && !in_array($attrval_id, $template_ignore_pav_list)){
                    $distinct_ids[] = $attrval_id;
                    $this->XM->sqlcore->query('INSERT INTO filtervals (id) VALUES ('.$attrval_id.')');
                }
            }
            unset($distinct_ids);
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtervals2 SELECT * FROM filtervals');

            $res = $this->XM->sqlcore->query('SELECT distinct pa.pag_id,coalesce(pav_ml.pav_ml_name,pav.pav_origin_name,\'\') as pav_ml_name,language.lang_id
                FROM product_attribute_value pav
                inner join filtervals on pav.pav_id = filtervals.id
                inner join product_attribute pa on pa.pa_id = pav.pa_id
                inner join (
                    select max(pa.pa_depth) as pa_depth,pa.pag_id
                        from filtervals2
                        inner join product_attribute_value pav on pav.pav_id = filtervals2.id
                        inner join product_attribute pa on pa.pa_id = pav.pa_id
                        where pa.pag_id in ('.implode(',', $pag_ids).')
                        group by pa.pag_id
                    ) depthfilter on depthfilter.pa_depth = pa.pa_depth and depthfilter.pag_id = pa.pag_id
                inner join language on 1=1
                left join product_attribute_value_ml pav_ml on pav_ml.pav_id = pav.pav_id and pav_ml.lang_id = language.lang_id');
            while($row = $this->XM->sqlcore->getRow($res)){
                $pag_id = (int)$row['pag_id'];
                if(!isset($partvalues[$pag_id])){
                    $partvalues[$pag_id] = array();
                }
                $partvalues[$pag_id][(int)$row['lang_id']] = $row['pav_ml_name'];
            }
            $this->XM->sqlcore->freeResult($res);
            $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filtervals");
            $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filtervals2");
        }
        return $this->__generate_full_name_templates($partvalues);
    }
    public function get_full_name_templates_for_product($product_id){
        $product_id = (int)$product_id;
        if($product_id<=0){
            return array();
        }
        $pag_ids = array();
        $templates = $this->__get_fullname_template_schemes();
        foreach($templates as $template){
            foreach($template as $part){
                if($part!=='name'){
                    if(!in_array($part, $pag_ids)){
                        $pag_ids[] = $part;
                    }
                }
            }
        }
        $template_ignore_pav_list = $this->__get_fullname_template_ignore_pav_list();
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE fullname_template_ignore_pav_list (
                id BIGINT UNSIGNED NOT NULL
            )');
        $distinct_ids = array();
        foreach($template_ignore_pav_list as $attrval_id){
            $attrval_id = (int)$attrval_id;
            if(!in_array($attrval_id, $distinct_ids)){
                $distinct_ids[] = $attrval_id;
                $this->XM->sqlcore->query('INSERT INTO fullname_template_ignore_pav_list (id) VALUES ('.$attrval_id.')');
            }
        }
        unset($distinct_ids);

        $partvalues = array();
        $res = $this->XM->sqlcore->query('SELECT distinct product_attribute.pag_id,coalesce(product_attribute_value_ml.pav_ml_name,product_attribute_value.pav_origin_name,\'\') as pav_ml_name,language.lang_id
            FROM product
            inner join product_value on product_value.p_id = product.p_id
            inner join product_attribute_value on product_attribute_value.pav_id = product_value.pav_id
            inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
            inner join language on 1=1
            left join fullname_template_ignore_pav_list on fullname_template_ignore_pav_list.id = product_attribute_value.pav_id
            left join product_attribute_value_ml on product_attribute_value_ml.pav_id = product_attribute_value.pav_id and product_attribute_value_ml.lang_id = language.lang_id
            where product.p_id = '.$product_id.' and fullname_template_ignore_pav_list.id is null');
        while($row = $this->XM->sqlcore->getRow($res)){
            $pag_id = (int)$row['pag_id'];
            if(!isset($partvalues[$pag_id])){
                $partvalues[$pag_id] = array();
            }
            $partvalues[$pag_id][(int)$row['lang_id']] = $row['pav_ml_name'];
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS fullname_template_ignore_pav_list");
        return $this->__generate_full_name_templates($partvalues);
    }

    public function upload_image($tmp_name, $size, $name, &$err){
        if($size>1.5*1048576){
            $err = formatReplace(langTranslate('main', 'err', 'Size of @1 exceeds limit of @2 megabytes',  'Size of @1 exceeds limit of @2 megabytes'),
                    $name,
                    1.5);
            return false;
        }
        $ext = strtolower(substr($name, strrpos($name,'.')+1,strlen($name)));
        $valid_exts = array('png','gif','jpg','jpeg');
        if(!in_array($ext, $valid_exts)){
            $err = formatReplace(langTranslate('main', 'err', 'Invalid image type for file @1. Supported types: @2',  'Invalid image type for file @1. Supported types: @2'),
                    $name,
                    implode(', ', $valid_exts));
            return false;
        }
        $this->XM->sqlcore->query('INSERT INTO product_image (pi_ext) VALUES (\''.$this->XM->sqlcore->prepString($ext,5).'\')');
        $pi_id = $this->XM->sqlcore->lastInsertId();
        $path = ABS_PATH.'/modules/Product/productimg/'.$pi_id.'.'.$ext;
        if (!move_uploaded_file($tmp_name, ABS_PATH.'/modules/Product/productimg/'.$pi_id.'.'.$ext)){
            $err = formatReplace(langTranslate('main', 'err', 'Upload error (@2) for file @1',  'Upload error (@2) for file @1'),
                    $name,
                    '-89');
            $this->XM->sqlcore->rollback();
            return false;
        }
        $this->XM->sqlcore->commit();
        return array('id'=>$pi_id,'url'=>'/modules/Product/productimg/'.$pi_id.'.'.$ext);
    }

    public function delete_image($pi_id, &$err){
        $pi_id = (int)$pi_id;
        if($pi_id<=0){
            return true;
        }
        $res = $this->XM->sqlcore->query('SELECT pi_id, pi_ext, pi_isprimary, p_id FROM product_image WHERE pi_id = '.$pi_id);
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return true;
        }
        if(@unlink(ABS_PATH.'/modules/Product/productimg/'.$row['pi_id'].'.'.$row['pi_ext'])===FALSE){
            $err = langTranslate('product', 'err', 'File deletion failed',  'File deletion failed');
            return false;
        }
        $this->XM->sqlcore->query('DELETE FROM product_image WHERE pi_id = '.$pi_id);
        $this->XM->sqlcore->commit();
        if($row['pi_isprimary']&&$row['p_id']){
            $res = $this->XM->sqlcore->query('SELECT pi_id FROM product_image WHERE p_id = '.$row['p_id'].' LIMIT 1');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if($row){
                $this->XM->sqlcore->query('UPDATE product_image SET pi_isprimary = 1 where pi_id = '.$row['pi_id']);
                $this->XM->sqlcore->commit();
            }
        }
        return true;
    }
    public function make_image_primary($pi_id, &$err){
        $pi_id = (int)$pi_id;
        if($pi_id<=0){
            return true;
        }
        $res = $this->XM->sqlcore->query('SELECT pi_isprimary,p_id FROM product_image WHERE pi_id = '.$pi_id);
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Image doesn\'t exist',  'Image doesn\'t exist');
            return false;
        }
        if($row['pi_isprimary']){
            return true;
        }
        if($row['p_id']){
            $this->XM->sqlcore->query('UPDATE product_image SET pi_isprimary = 0 WHERE p_id = '.$row['p_id'].' and pi_id <> '.$pi_id.' and pi_isprimary = 1');
        }
        $this->XM->sqlcore->query('UPDATE product_image SET pi_isprimary = 1 WHERE pi_id = '.$pi_id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function get_image_data($image_ids){
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE imageids (
                id BIGINT UNSIGNED NOT NULL
            )');
        $distinct_ids = array();
        foreach($image_ids as $image_id){
            $image_id = (int)$image_id;
            if(!in_array($image_id, $distinct_ids)){
                $distinct_ids[] = $image_id;
                $this->XM->sqlcore->query('INSERT INTO imageids (id) VALUES ('.$image_id.')');
            }
        }
        $result = array();
        $res = $this->XM->sqlcore->query('SELECT distinct pi_id,pi_ext,pi_isprimary
            FROM product_image
            inner join imageids on product_image.pi_id = imageids.id
            order by pi_isprimary desc');
        while($row = $this->XM->sqlcore->getRow($res)){
            $result[] = array('id'=>(int)$row['pi_id'],'url'=>BASE_URL.'/modules/Product/productimg/'.$row['pi_id'].'.'.$row['pi_ext'],'primary'=>(int)$row['pi_isprimary'],'can_delete'=>true);
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->commit();
        return $result;
    }

    private function __get_foundation_attributes($attributes){
        if(!is_array($attributes)||empty($attributes)){
            return array();
        }
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE foundationvals (
                id BIGINT UNSIGNED NOT NULL
            )');
        $distinct_ids = array();
        foreach($attributes as $attrval_id){
            $attrval_id = (int)$attrval_id;
            if(!in_array($attrval_id, $distinct_ids)){
                $distinct_ids[] = $attrval_id;
                $this->XM->sqlcore->query('INSERT INTO foundationvals (id) VALUES ('.$attrval_id.')');
            }
        }
        unset($distinct_ids);
        $res = $this->XM->sqlcore->query('SELECT distinct pav.pav_id
            from foundationvals
            inner join product_attribute_value pav on pav.pav_id = foundationvals.id
            inner join product_attribute pa on pa.pa_id = pav.pa_id and pa.pag_id = '.\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID.'
            ');
        $attributes = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $attributes[] = (int)$row['pav_id'];
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS foundationvals');
        return $attributes;
    }

    public function clean_attributes($attributes, $clean_for_product = false, $clean_for_doublecheck = false){
        if(!is_array($attributes)||empty($attributes)){
            return array();
        }
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtervals (
                id BIGINT UNSIGNED NOT NULL
            )');
        $distinct_ids = array();
        foreach($attributes as $attrval_id){
            $attrval_id = (int)$attrval_id;
            if(!in_array($attrval_id, $distinct_ids)){
                $distinct_ids[] = $attrval_id;
                $this->XM->sqlcore->query('INSERT INTO filtervals (id) VALUES ('.$attrval_id.')');
            }
        }
        unset($distinct_ids);
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtervals2 SELECT * FROM filtervals');

        $clean_for_product_inner_join = '';
        if($clean_for_product){
            $foundation_attributes = $this->__get_foundation_attributes($attributes);
            if(!empty($foundation_attributes)){
                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE foundationvals (
                    id BIGINT UNSIGNED NOT NULL
                )');
                foreach($foundation_attributes as $attrval_id){
                    $this->XM->sqlcore->query('INSERT INTO foundationvals (id) VALUES ('.((int)$attrval_id).')');
                }
                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE clean_for_product_pag_ids
                        select distinct product_attribute_group_dependency.pag_id
                            from product_attribute_group_dependency
                            inner join foundationvals on foundationvals.id = product_attribute_group_dependency.pav_id
                            where product_attribute_group_dependency.pagd_visible = 1');
                $clean_for_product_inner_join = 'inner join clean_for_product_pag_ids on clean_for_product_pag_ids.pag_id = product_attribute.pag_id';
                $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS foundationvals");
            } else {
                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE clean_for_product_pag_ids
                        select distinct product_attribute_group.pag_id
                            from product_attribute_group
                            where product_attribute_group.pag_always_visible = 1');
                $clean_for_product_inner_join = 'inner join clean_for_product_pag_ids on clean_for_product_pag_ids.pag_id = product_attribute.pag_id';
            }
        }
        $res = $this->XM->sqlcore->query('SELECT distinct product_attribute_value.pav_id
            from filtervals
            inner join product_attribute_value on product_attribute_value.pav_id = filtervals.id
            inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
            '.$clean_for_product_inner_join.'
            inner join (
                    select max(product_attribute.pa_depth) as pa_depth,product_attribute.pag_id
                        from filtervals2
                        inner join product_attribute_value on product_attribute_value.pav_id = filtervals2.id
                        inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
                        group by product_attribute.pag_id
                ) depthfilter on depthfilter.pag_id = product_attribute.pag_id and depthfilter.pa_depth = product_attribute.pa_depth');
        $attributes = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $attributes[] = (int)$row['pav_id'];
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filtervals2');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filtervals');
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS clean_for_product_pag_ids");
        return $attributes;
    }
    public function clean_attribute_children($attributes){//deletes children with unchecked parent
        if(!is_array($attributes)||empty($attributes)){
            return array();
        }
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtervals (
                id BIGINT UNSIGNED NOT NULL
            )');
        $distinct_ids = array();
        foreach($attributes as $attrval_id){
            $attrval_id = (int)$attrval_id;
            if(!in_array($attrval_id, $distinct_ids)){
                $distinct_ids[] = $attrval_id;
                $this->XM->sqlcore->query('INSERT INTO filtervals (id) VALUES ('.$attrval_id.')');
            }
        }
        unset($distinct_ids);
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtervals2 SELECT * FROM filtervals');
        $res = $this->XM->sqlcore->query('SELECT product_attribute_value_tree.pav_id,count(distinct product_attribute_value_tree.pav_anc_id) as total,count(distinct filtervals2.id) as cnt
            from filtervals
            inner join product_attribute_value_tree on product_attribute_value_tree.pav_id = filtervals.id
            left join filtervals2 on filtervals2.id = product_attribute_value_tree.pav_anc_id
            group by product_attribute_value_tree.pav_id
            having count(distinct product_attribute_value_tree.pav_anc_id) = count(distinct filtervals2.id)');
        $attributes = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $attributes[] = (int)$row['pav_id'];
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filtervals2');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filtervals');
        return $attributes;
    }
    public function check_double_product($product_id, $attributes, $originname, &$double_product_id, &$err){
        $product_id = (int)$product_id;
        if(!is_array($attributes)){
            $attributes = array();
        }

        $attributes = $this->clean_attributes($attributes, false);

        $foundation_attributes = $this->__get_foundation_attributes($attributes);
        if(empty($foundation_attributes) || !isset($foundation_attributes[0]) || $foundation_attributes[0]<=0){
            $res = $this->XM->sqlcore->query('SELECT distinct coalesce(pag_ml.pag_ml_name,\'-\') as pag_ml_name
                from product_attribute_group
                left join (select pag_id,substring_index(group_concat(pag_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pag_ml_id from product_attribute_group_ml where pag_ml_name is not null group by pag_id) as ln_glue on ln_glue.pag_id = product_attribute_group.pag_id
                left join product_attribute_group_ml pag_ml on pag_ml.pag_ml_id = ln_glue.pag_ml_id
                where product_attribute_group.pag_id = '.\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID.' LIMIT 1' );
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if(!$row){//never
                $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
                return false;
            }
            $err = formatReplace(langTranslate('product', 'err', 'Fill out required fields: @1',  'Fill out required fields: @1'),
                    $row['pag_ml_name']);
            return false;
        }
        if(count($foundation_attributes)>1){//never
            $res = $this->XM->sqlcore->query('SELECT distinct coalesce(pag_ml.pag_ml_name,\'-\') as pag_ml_name
                from product_attribute_group
                left join (select pag_id,substring_index(group_concat(pag_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pag_ml_id from product_attribute_group_ml where pag_ml_name is not null group by pag_id) as ln_glue on ln_glue.pag_id = product_attribute_group.pag_id
                left join product_attribute_group_ml pag_ml on pag_ml.pag_ml_id = ln_glue.pag_ml_id
                where product_attribute_group.pag_id = '.\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID.' LIMIT 1' );
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if(!$row){//never
                $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
                return false;
            }
            $err = formatReplace(langTranslate('product', 'err', 'You can\'t select multiple values in fields: @1',  'You can\'t select multiple values in fields: @1'),
                    $row['pag_ml_name']);
            return false;
        }
        $foundation_pav_id = (int)$foundation_attributes[0];

        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtervals (
                id BIGINT UNSIGNED NOT NULL
            )');
        foreach($attributes as $attrval_id){
            $this->XM->sqlcore->query('INSERT INTO filtervals (id) VALUES ('.(int)$attrval_id.')');
        }
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE doublecheckfiltervals
            select distinct product_attribute_value.pav_id,product_attribute.pag_id
                from filtervals
                inner join product_attribute_value on product_attribute_value.pav_id = filtervals.id
                inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
                inner join product_attribute_group_dependency on product_attribute_group_dependency.pag_id = product_attribute.pag_id
                where product_attribute_group_dependency.pav_id = '.$foundation_pav_id.' and product_attribute_group_dependency.pagd_doublecheck = 1');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filtervals');

        $res = $this->XM->sqlcore->query('SELECT distinct product_attribute_group.pag_id, coalesce(pag_ml.pag_ml_name,\'-\') as pag_ml_name
            from product_attribute_group
            inner join product_attribute_group_dependency on product_attribute_group_dependency.pag_id = product_attribute_group.pag_id and product_attribute_group_dependency.pav_id = '.$foundation_pav_id.' and product_attribute_group_dependency.pagd_doublecheck = 1
            left join doublecheckfiltervals on doublecheckfiltervals.pag_id = product_attribute_group.pag_id
            left join (select pag_id,substring_index(group_concat(pag_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pag_ml_id from product_attribute_group_ml where pag_ml_name is not null group by pag_id) as ln_glue on ln_glue.pag_id = product_attribute_group.pag_id
            left join product_attribute_group_ml pag_ml on pag_ml.pag_ml_id = ln_glue.pag_ml_id
            where doublecheckfiltervals.pag_id is null');
        $pag_names = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $pag_names[] = $row['pag_ml_name'];
        }
        $this->XM->sqlcore->freeResult($res);
        if(!empty($pag_names)){
            $err = formatReplace(langTranslate('product', 'err', 'Fill out required fields: @1',  'Fill out required fields: @1'),
                    implode(', ', $pag_names));
            $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS doublecheckfiltervals");
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT count(1) as cnt from doublecheckfiltervals');
        $row = $this->XM->sqlcore->getRow($res);
        $doublecheckfiltervals_count = (int)$row['cnt'];

        $res = $this->XM->sqlcore->query('SELECT product_vintage.p_id, product_vintage.pv_id, coalesce(p_ml.p_ml_fullname,product_value.p_id) as p_ml_fullname
            from product_value
            inner join doublecheckfiltervals on doublecheckfiltervals.pav_id = product_value.pav_id
            inner join product on product.p_id = product_value.p_id
            inner join product_vintage on product_vintage.p_id = product.p_id and product_vintage.pv_blank = 1
            left join (select p_id,substring_index(group_concat(p_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as p_ml_id from product_ml group by p_id) as ln_glue on ln_glue.p_id = product.p_id
            left join product_ml p_ml on p_ml.p_ml_id = ln_glue.p_ml_id
            where product.p_is_approved = 1 and product.p_origin_name = \''.$this->XM->sqlcore->prepString($originname,128).'\''.($product_id?' and product_value.p_id <> '.$product_id:'').'
            group by product_value.p_id,product_vintage.pv_id
            having count(distinct doublecheckfiltervals.pav_id)='.$doublecheckfiltervals_count.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS doublecheckfiltervals');
		if($row){
			$double_product_id = (int)$row['p_id'];
			$err = formatReplace(langTranslate('product', 'err', 'Product already exists: @1',  'Product already exists: @1'),
                    '<a href="'.BASE_URL.'/vintage/'.$row['pv_id'].'">'.htmlentities($row['p_ml_fullname']).'</a>');
			return false;
		}
        return true;
    }

    public function add_product($attributes, $isvintage, $vineyard_name, $alcohol_content, $originname, $name, $image_ids, $blend, $grape_variety_concentration, &$err){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_ADD_PRODUCT)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $blend = $blend?1:0;
        if($blend){
            foreach($grape_variety_concentration as $pav_id=>$concentration){
                if($concentration!=='' and $concentration<=0){
                    unset($grape_variety_concentration[$pav_id]);
                    if(($key = array_search($pav_id,$attributes))!==false){
                        unset($attributes[$key]);
                    }
                }
            }
        }
        $attributes = $this->clean_attributes($attributes,true);
		$double_product_id = null;
        if(!$this->check_double_product(null,$attributes,$originname,$double_product_id,$err)){
			if($double_product_id){
				return $double_product_id;
			}
            return false;
        }
        $isvintage = $isvintage?1:0;
        $vineyard_id = null;
        // $vineyard_id = $this->__get_vineyard_id($vineyard_name);
        if(strlen($alcohol_content)){
            $alcohol_content = (int)((float)str_replace(',', '.', $alcohol_content)*100);
        } else {
            $alcohol_content = null;
        }
        if(mb_strlen($originname, 'UTF-8')>128){
            $err = formatReplace(langTranslate('product', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                langTranslate('product', 'product', 'Origin Name', 'Origin Name'),
                128);
            return false;
        }
        $this->XM->sqlcore->query('INSERT INTO product (p_origin_name,p_isvintage,p_isblend,pvy_id,p_alcohol_content,company_id) VALUES (\''.$this->XM->sqlcore->prepString($originname,128).'\','.$isvintage.','.$blend.','.(($vineyard_id!==null)?$vineyard_id:'null').','.(($alcohol_content!==null)?$alcohol_content:'null').','.$this->XM->user->getCompanyId().')');
        $product_id = $this->XM->sqlcore->lastInsertId();
        if(strlen($originname)){
            $this->XM->sqlcore->query('INSERT INTO product_se (p_id,p_se_type,lang_id,p_se_text) VALUES ('.$product_id.',0,null,\''.$this->XM->sqlcore->prepString($this->XM->sqlcore->search_engine_alias($originname),128).'\')');
        }

        $templates = $this->get_full_name_templates($attributes);
        $languageIdList = $this->XM->lang->getLanguageIdList();
        foreach($languageIdList as $lang_id){
            $lang_name = getLangArrayVal($name,$lang_id);
            if(mb_strlen($lang_name, 'UTF-8')>128){
                $err = formatReplace(langTranslate('product', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('product', 'product', 'Name', 'Name'),
                    128);
                $this->XM->sqlcore->rollback();
                return false;
            }
            $insertkeys = array();
            $insertvals = array();
            if(strlen($lang_name)){
                $insertkeys[] = 'p_ml_name';
                $insertvals[] = '\''.$this->XM->sqlcore->prepString($lang_name,128).'\'';
            }
            if(isset($templates[$lang_id])){//always
                $fullname = $this->XM->sqlcore->prepString(str_replace('{{name}}', strlen($lang_name)?$lang_name:$originname, $templates[$lang_id]),4096);
                if(strlen($fullname)){//always
                    $insertkeys[] = 'p_ml_fullname';
                    $insertvals[] = '\''.$fullname.'\'';
                }

            }
            if(empty($insertkeys)){
                continue;
            }
            $insertkeys[] = 'p_id';
            $insertvals[] = $product_id;
            $insertkeys[] = 'lang_id';
            $insertvals[] = $lang_id;

            $this->XM->sqlcore->query('INSERT INTO product_ml ('.implode(',', $insertkeys).') VALUES ('.implode(',', $insertvals).')');
            if(strlen($lang_name)){
                $this->XM->sqlcore->query('INSERT INTO product_se (p_id,p_se_type,lang_id,p_se_text) VALUES ('.$product_id.',1,'.$lang_id.',\''.$this->XM->sqlcore->prepString($this->XM->sqlcore->search_engine_alias($lang_name),128).'\')');
            }
        }

        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE basefiltervals (
                id BIGINT UNSIGNED NOT NULL
            )');
        foreach($attributes as $attrval_id){
            $this->XM->sqlcore->query('INSERT INTO basefiltervals (id) VALUES ('.(int)$attrval_id.')');
        }
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtervals (
                id BIGINT UNSIGNED NOT NULL,
                pag_id BIGINT UNSIGNED NOT NULL,
                pv_part TINYINT UNSIGNED NULL
            )');
        $this->XM->sqlcore->query('INSERT INTO filtervals (id, pag_id)
            SELECT product_attribute_value.pav_id as id, product_attribute.pag_id
                FROM basefiltervals
                inner join product_attribute_value on product_attribute_value.pav_id = basefiltervals.id
                INNER JOIN product_attribute on product_attribute.pa_id = product_attribute_value.pa_id');
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS basefiltervals");

        if($blend){
            $pv_part_filled = false;
            foreach($grape_variety_concentration as $pav_id=>$pv_part){
                $pav_id = (int)$pav_id;
                $pv_part = (int)$pv_part;
                if($pv_part<=0){
                    continue;
                }
                if($pv_part>100){
                    $pv_part = 100;
                }
                $this->XM->sqlcore->query('UPDATE filtervals SET pv_part = '.$pv_part.' WHERE id = '.$pav_id.' and pag_id = 7');
                $pv_part_filled = true;
            }
            if($pv_part_filled){
                $this->XM->sqlcore->query('DELETE FROM filtervals WHERE pv_part is null and pag_id = 7');
            }
        } else {
            $this->XM->sqlcore->query('UPDATE filtervals SET pv_part = 100 WHERE pag_id = 7');
        }
        $res = $this->XM->sqlcore->query('SELECT distinct coalesce(pag_ml.pag_ml_name,\'-\') as pag_ml_name
            from product_attribute_group pag
            inner join (
                select distinct product_attribute_group.pag_id
                from filtervals
                inner join product_attribute_group on product_attribute_group.pag_id = filtervals.pag_id and ( product_attribute_group.pag_multiple = 0  '.($blend?'and product_attribute_group.pag_id <> 7':'').' )
                group by product_attribute_group.pag_id
                having count(1)>1
            ) pag2 on pag2.pag_id = pag.pag_id
            left join (select pag_id,substring_index(group_concat(pag_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pag_ml_id from product_attribute_group_ml where pag_ml_name is not null group by pag_id) as ln_glue on ln_glue.pag_id = pag.pag_id
            left join product_attribute_group_ml pag_ml on pag_ml.pag_ml_id = ln_glue.pag_ml_id');
        $pag_names = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $pag_names[] = $row['pag_ml_name'];
        }
        $this->XM->sqlcore->freeResult($res);
        if(!empty($pag_names)){
            $err = formatReplace(langTranslate('product', 'err', 'You can\'t select multiple values in fields: @1',  'You can\'t select multiple values in fields: @1'),
                    implode(', ', $pag_names));
            $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filtervals");
            $this->XM->sqlcore->rollback();
            return false;
        }
        //parts
        $res = $this->XM->sqlcore->query('SELECT distinct coalesce(pag_ml.pag_ml_name,\'-\') as pag_ml_name
            from product_attribute_group pag
            inner join (
                select distinct filtervals.pag_id
                from filtervals
                group by filtervals.pag_id
                having sum(filtervals.pv_part) <> 100
            ) pag2 on pag2.pag_id = pag.pag_id
            left join (select pag_id,substring_index(group_concat(pag_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pag_ml_id from product_attribute_group_ml where pag_ml_name is not null group by pag_id) as ln_glue on ln_glue.pag_id = pag.pag_id
            left join product_attribute_group_ml pag_ml on pag_ml.pag_ml_id = ln_glue.pag_ml_id');
        $pag_names = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $pag_names[] = $row['pag_ml_name'];
        }
        $this->XM->sqlcore->freeResult($res);
        if(!empty($pag_names)){
            $err = formatReplace(langTranslate('product', 'err', 'Sum of parts not equal to 100 for fields: @1',  'Sum of parts not equal to 100 for fields: @1'),
                    implode(', ', $pag_names));
            $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filtervals");
            $this->XM->sqlcore->rollback();
            return false;
        }

        $this->XM->sqlcore->query('INSERT INTO product_value (p_id,pav_id,pag_id,pv_part)
            SELECT '.$product_id.' as p_id, filtervals.id as pav_id, filtervals.pag_id,filtervals.pv_part
                FROM filtervals');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filtervals');
        $this->XM->sqlcore->commit();
        $image_id_chunks = array_chunk($image_ids, 100);
        foreach($image_id_chunks as $image_id_chunk){
            $this->XM->sqlcore->query('UPDATE product_image SET p_id = '.$product_id.' where pi_id in ('.implode(',', $image_id_chunk).') and p_id is null');
        }
        $res = $this->XM->sqlcore->query('SELECT pi_id,pi_isprimary FROM product_image WHERE p_id = '.$product_id.' order by pi_isprimary desc');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if($row){//there are images
            if(!$row['pi_isprimary']){//none of them is primary
                $this->XM->sqlcore->query('UPDATE product_image SET pi_isprimary = 1 where pi_id = '.$row['pi_id']);
            } else {
                $this->XM->sqlcore->query('UPDATE product_image SET pi_isprimary = 0 where p_id = '.$product_id.' and pi_id <> '.$row['pi_id'].' and pi_isprimary = 1');
            }
        }
        $this->XM->sqlcore->commit();
        //company favourite
        $dummy = null;
        $this->company_favourite_product($product_id,true,true,$dummy);
        if($this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_APPROVE_PRODUCT)){
            $dummy = null;
            $this->approve_product($product_id, $dummy);
        }
        return $product_id;
    }
    private function __get_vineyard_id($vineyard_name){
        return null;
        // $vineyard_name = trim($vineyard_name);
        // if(!$vineyard_name){
        //     return null;
        // }
        // $res = $this->XM->sqlcore->query('SELECT pvy_id FROM product_vineyard WHERE pvy_name = \''.$this->XM->sqlcore->prepString($vineyard_name,64).'\' limit 1');
        // $row = $this->XM->sqlcore->getRow($res);
        // $this->XM->sqlcore->freeResult($res);
        // if($row){
        //     return (int)$row['pvy_id'];
        // }
        // $this->XM->sqlcore->query('INSERT INTO product_vineyard (pvy_name) VALUES(\''.$this->XM->sqlcore->prepString($vineyard_name,64).'\')');
        // $vineyard_id = $this->XM->sqlcore->lastInsertId();
        // $this->XM->sqlcore->commit();
        // return $vineyard_id;
    }
    public function edit_product($product_id, $attributes, $isvintage, $vineyard_name, $alcohol_content, $originname, $name, $image_ids, $blend, $grape_variety_concentration, &$err){
        $product_id = (int)$product_id;
        if($product_id<=0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        if(!$this->can_edit_product($product_id)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $blend = $blend?1:0;
        if($blend){
            foreach($grape_variety_concentration as $pav_id=>$concentration){
                if($concentration!=='' and $concentration<=0){
                    unset($grape_variety_concentration[$pav_id]);
                    if(($key = array_search($pav_id,$attributes))!==false){
                        unset($attributes[$key]);
                    }
                }
            }
        }
        $attributes = $this->clean_attributes($attributes,true);
		$double_product_id = null;
        if(!$this->check_double_product($product_id,$attributes,$originname,$double_product_id,$err)){
            return false;
        }
        $isvintage = $isvintage?1:0;
        if(strlen($alcohol_content)){
            $alcohol_content = (int)((float)str_replace(',', '.', $alcohol_content)*100);
        } else {
            $alcohol_content = null;
        }

        // $vineyard_id = $this->__get_vineyard_id($vineyard_name);
        $res = $this->XM->sqlcore->query('SELECT p_origin_name, p_alcohol_content, p_isvintage, pvy_id, p_isblend FROM product WHERE p_id = '.$product_id);
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Product doesn\'t exist',  'Product doesn\'t exist');
            return false;
        }
        $updatearr = array();
        if($row['p_origin_name']!=$originname){
            if(mb_strlen($originname, 'UTF-8')>128){
                $err = formatReplace(langTranslate('product', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('product', 'product', 'Origin Name', 'Origin Name'),
                    128);
                return false;
            }
            $updatearr[] = 'p_origin_name = \''.$this->XM->sqlcore->prepString($originname,128).'\'';
        }
        // if($row['pvy_id']!=$vineyard_id){
        //     if($vineyard_id===null){
        //         if($row['pvy_id']!==null){
        //             $updatearr[] = 'pvy_id = null';
        //         }
        //     } else {
        //         $updatearr[] = 'pvy_id = '.$vineyard_id;
        //     }
        // }
        if($row['p_alcohol_content']!=$alcohol_content){
            if($alcohol_content===null){
                if($row['p_alcohol_content']!==null){
                    $updatearr[] = 'p_alcohol_content = null';
                }
            } else {
                $updatearr[] = 'p_alcohol_content = '.$alcohol_content;
            }
        }
        if($row['p_isvintage']!=$isvintage){
            $updatearr[] = 'p_isvintage = '.$isvintage;
        }
        if($row['p_isblend']!=$blend){
            $updatearr[] = 'p_isblend = '.$blend;
        }
        if(!empty($updatearr)){
            $this->XM->sqlcore->query('UPDATE product SET '.implode(',', $updatearr).' WHERE p_id = '.$product_id);

            if($row['p_origin_name']!==$originname){
                $res = $this->XM->sqlcore->query('SELECT p_se_text from product_se where p_id = '.$product_id.' and p_se_type = 0 limit 1');
                $se_row = $this->XM->sqlcore->getRow($res);
                $this->XM->sqlcore->freeResult($res);
                $se_origin_name = '';
                if($se_row){
                    $se_origin_name = $se_row['p_se_text'];
                }
                $asciialias = $this->XM->sqlcore->search_engine_alias($originname);
                if($se_origin_name != $asciialias){
                    if(empty($asciialias)){
                        $this->XM->sqlcore->query('DELETE FROM product_se where p_id = '.$product_id.' and p_se_type = 0');
                    } elseif(!$se_row){
                        $this->XM->sqlcore->query('INSERT INTO product_se (p_id,p_se_type,lang_id,p_se_text) VALUES ('.$product_id.',0,null,\''.$this->XM->sqlcore->prepString($asciialias,128).'\')');
                    } else {
                        $this->XM->sqlcore->query('UPDATE product_se SET p_se_text = \''.$this->XM->sqlcore->prepString($asciialias,128).'\' where p_id = '.$product_id.' and p_se_type = 0');
                    }

                }
            }
        }

        $ml_variants = array();
        $res = $this->XM->sqlcore->query('SELECT p_ml_name, p_ml_fullname, lang_id, p_ml_id from product_ml where p_id = '.$product_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $ml_variants[(int)$row['lang_id']] = array('name'=>$row['p_ml_name'],'fullname'=>$row['p_ml_fullname'],'id'=>$row['p_ml_id']);
        }
        $this->XM->sqlcore->freeResult($res);

        $templates = $this->get_full_name_templates($attributes);
        $languageIdList = $this->XM->lang->getLanguageIdList();
        foreach($languageIdList as $lang_id){
            $lang_name = getLangArrayVal($name,$lang_id);
            if(mb_strlen($lang_name, 'UTF-8')>128){
                $err = formatReplace(langTranslate('product', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('product', 'product', 'Name', 'Name'),
                    128);
                $this->XM->sqlcore->rollback();
                return false;
            }
            $fullname = '';
            if(isset($templates[$lang_id])){//always
                $fullname = str_replace('{{name}}', strlen($lang_name)?$lang_name:$originname, $templates[$lang_id]);
            }
            if(isset($ml_variants[$lang_id])){
                $updatearr = array();
                if($lang_name!=$ml_variants[$lang_id]['name']){
                    $updatearr[] = 'p_ml_name = \''.$this->XM->sqlcore->prepString($lang_name,128).'\'';
                }
                if($fullname!=$ml_variants[$lang_id]['fullname']){
                    $updatearr[] = 'p_ml_fullname = \''.$this->XM->sqlcore->prepString($fullname,4096).'\'';
                }
                if(!empty($updatearr)){
                    $this->XM->sqlcore->query('UPDATE product_ml SET '.implode(',',$updatearr).' WHERE p_ml_id = '.$ml_variants[$lang_id]['id']);

                    if($lang_name!=$ml_variants[$lang_id]['name']){
                        $res = $this->XM->sqlcore->query('SELECT p_se_text from product_se where p_id = '.$product_id.' and p_se_type = 1 and lang_id = '.$lang_id.' limit 1');
                        $se_row = $this->XM->sqlcore->getRow($res);
                        $this->XM->sqlcore->freeResult($res);
                        $se_origin_name = '';
                        if($se_row){
                            $se_origin_name = $se_row['p_se_text'];
                        }
                        $asciialias = $this->XM->sqlcore->search_engine_alias($lang_name);
                        if($se_origin_name != $asciialias){
                            if(empty($asciialias)){
                                $this->XM->sqlcore->query('DELETE FROM product_se where p_id = '.$product_id.' and p_se_type = 1 and lang_id = '.$lang_id);
                            } elseif(!$se_row){
                                $this->XM->sqlcore->query('INSERT INTO product_se (p_id,p_se_type,lang_id,p_se_text) VALUES ('.$product_id.',1,'.$lang_id.',\''.$this->XM->sqlcore->prepString($asciialias,128).'\')');
                            } else {
                                $this->XM->sqlcore->query('UPDATE product_se SET p_se_text = \''.$this->XM->sqlcore->prepString($asciialias,128).'\' where p_id = '.$product_id.' and p_se_type = 1 and lang_id = '.$lang_id);
                            }

                        }
                    }
                }
                continue;
            }
            $insertkeys = array();
            $insertvals = array();
            if(strlen($lang_name)){
                $insertkeys[] = 'p_ml_name';
                $insertvals[] = '\''.$this->XM->sqlcore->prepString($lang_name,128).'\'';
            }
            if(isset($templates[$lang_id])){//always
                $insertkeys[] = 'p_ml_fullname';
                $insertvals[] = '\''.$this->XM->sqlcore->prepString($fullname,4096).'\'';
            }
            if(empty($insertkeys)){
                continue;
            }
            $insertkeys[] = 'p_id';
            $insertvals[] = $product_id;
            $insertkeys[] = 'lang_id';
            $insertvals[] = $lang_id;

            $this->XM->sqlcore->query('INSERT INTO product_ml ('.implode(',', $insertkeys).') VALUES ('.implode(',', $insertvals).')');
            $this->XM->sqlcore->commit();
        }
        //attributes
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE basefiltervals (
                id BIGINT UNSIGNED NOT NULL
            )');
        foreach($attributes as $attrval_id){
            $this->XM->sqlcore->query('INSERT INTO basefiltervals (id) VALUES ('.(int)$attrval_id.')');
        }
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtervals (
                id BIGINT UNSIGNED NOT NULL,
                pag_id BIGINT UNSIGNED NOT NULL,
                pv_part TINYINT UNSIGNED NULL
            )');
        $this->XM->sqlcore->query('INSERT INTO filtervals (id, pag_id)
            SELECT product_attribute_value.pav_id as id, product_attribute.pag_id
                FROM basefiltervals
                inner join product_attribute_value on product_attribute_value.pav_id = basefiltervals.id
                INNER JOIN product_attribute on product_attribute.pa_id = product_attribute_value.pa_id');
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS basefiltervals");

        if($blend){
            $pv_part_filled = false;
            foreach($grape_variety_concentration as $pav_id=>$pv_part){
                $pav_id = (int)$pav_id;
                $pv_part = (int)$pv_part;
                if($pv_part<=0){
                    continue;
                }
                if($pv_part>100){
                    $pv_part = 100;
                }
                $this->XM->sqlcore->query('UPDATE filtervals SET pv_part = '.$pv_part.' WHERE id = '.$pav_id.' and pag_id = 7');
                $pv_part_filled = true;
            }
            if($pv_part_filled){
                $this->XM->sqlcore->query('DELETE FROM filtervals WHERE pv_part is null and pag_id = 7');
            }
        } else {
            $this->XM->sqlcore->query('UPDATE filtervals SET pv_part = 100 WHERE pag_id = 7');
        }
        $res = $this->XM->sqlcore->query('SELECT distinct coalesce(pag_ml.pag_ml_name,\'-\') as pag_ml_name
            from product_attribute_group pag
            inner join (
                select distinct product_attribute_group.pag_id
                from filtervals
                inner join product_attribute_group on product_attribute_group.pag_id = filtervals.pag_id and ( product_attribute_group.pag_multiple = 0  '.($blend?'and product_attribute_group.pag_id <> 7':'').' )
                group by product_attribute_group.pag_id
                having count(1)>1
            ) pag2 on pag2.pag_id = pag.pag_id
            left join (select pag_id,substring_index(group_concat(pag_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pag_ml_id from product_attribute_group_ml where pag_ml_name is not null group by pag_id) as ln_glue on ln_glue.pag_id = pag.pag_id
            left join product_attribute_group_ml pag_ml on pag_ml.pag_ml_id = ln_glue.pag_ml_id');
        $pag_names = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $pag_names[] = $row['pag_ml_name'];
        }
        $this->XM->sqlcore->freeResult($res);
        if(!empty($pag_names)){
            $err = formatReplace(langTranslate('product', 'err', 'You can\'t select multiple values in fields: @1',  'You can\'t select multiple values in fields: @1'),
                    implode(', ', $pag_names));
            $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filtervals");
            $this->XM->sqlcore->rollback();
            return false;
        }
        //parts
        $res = $this->XM->sqlcore->query('SELECT distinct coalesce(pag_ml.pag_ml_name,\'-\') as pag_ml_name
            from product_attribute_group pag
            inner join (
                select distinct filtervals.pag_id
                from filtervals
                group by filtervals.pag_id
                having sum(filtervals.pv_part) <> 100
            ) pag2 on pag2.pag_id = pag.pag_id
            left join (select pag_id,substring_index(group_concat(pag_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pag_ml_id from product_attribute_group_ml where pag_ml_name is not null group by pag_id) as ln_glue on ln_glue.pag_id = pag.pag_id
            left join product_attribute_group_ml pag_ml on pag_ml.pag_ml_id = ln_glue.pag_ml_id');
        $pag_names = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $pag_names[] = $row['pag_ml_name'];
        }
        $this->XM->sqlcore->freeResult($res);
        if(!empty($pag_names)){
            $err = formatReplace(langTranslate('product', 'err', 'Sum of parts not equal to 100 for fields: @1',  'Sum of parts not equal to 100 for fields: @1'),
                    implode(', ', $pag_names));
            $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filtervals");
            $this->XM->sqlcore->rollback();
            return false;
        }

        //delete old values
        $this->XM->sqlcore->query('DELETE FROM product_value WHERE p_id = '.$product_id.' and NOT EXISTS (SELECT 1 FROM filtervals WHERE id = product_value.pav_id and filtervals.pv_part <=> product_value.pv_part LIMIT 1)');
        //insert new values
        $this->XM->sqlcore->query('INSERT INTO product_value (p_id,pav_id,pag_id,pv_part)
            SELECT '.$product_id.' as p_id, filtervals.id as pav_id, filtervals.pag_id, filtervals.pv_part
                FROM filtervals
                left join product_value on product_value.p_id = '.$product_id.' and product_value.pav_id = filtervals.id
                WHERE product_value.pav_id is null');
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filtervals");
        $this->XM->sqlcore->commit();

        $image_id_chunks = array_chunk($image_ids, 100);
        foreach($image_id_chunks as $image_id_chunk){
            $this->XM->sqlcore->query('UPDATE product_image SET p_id = '.$product_id.' where pi_id in ('.implode(',', $image_id_chunk).') and p_id is null');
        }
        $res = $this->XM->sqlcore->query('SELECT pi_id,pi_isprimary FROM product_image WHERE p_id = '.$product_id.' order by pi_isprimary desc');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if($row){//there are images
            if(!$row['pi_isprimary']){//none of them is primary
                $this->XM->sqlcore->query('UPDATE product_image SET pi_isprimary = 1 where pi_id = '.$row['pi_id']);
            } else {
                $this->XM->sqlcore->query('UPDATE product_image SET pi_isprimary = 0 where p_id = '.$product_id.' and pi_id <> '.$row['pi_id'].' and pi_isprimary = 1');
            }
        }
        $this->XM->sqlcore->commit();
        if($this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_APPROVE_PRODUCT)){
            $dummy = null;
            $this->approve_product($product_id, $dummy);
        }
        return true;
    }
    private function __delete_product($id, &$err){
        $id = (int)$id;
        if($id <= 0){
            $err = langTranslate('product', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }

        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_APPROVE_PRODUCT)){
            $res = $this->XM->sqlcore->query('SELECT p_is_approved, company_id from product where p_id = '.$id.' LIMIT 1');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if(!$row){
                $err = langTranslate('product', 'err', 'Product doesn\'t exist',  'Product doesn\'t exist');
                return false;
            }
            if($row['p_is_approved']==1 || $row['company_id']!=$this->XM->user->getCompanyId()){
                $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
                return false;
            }
        }
        $res = $this->XM->sqlcore->query('SELECT 1
            from product_vintage
            inner join tasting_product_vintage on tasting_product_vintage.pv_id = product_vintage.pv_id
            where product_vintage.p_id = '.$id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if($row){
            $err = langTranslate('product', 'err', 'Can\'t delete product. Product already added to a tasting',  'Can\'t delete product. Product already added to a tasting');
            return false;
        }
        //лишнее, пока не удаляются старые дегустации
        $res = $this->XM->sqlcore->query('SELECT 1
            from product_vintage
            inner join product_vintage_review on product_vintage_review.pv_id = product_vintage.pv_id
            where product_vintage.p_id = '.$id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if($row){
            $err = langTranslate('product', 'err', 'Can\'t delete product. Product already has a review',  'Can\'t delete product. Product already has a review');
            return false;
        }
        $this->XM->sqlcore->query('DELETE from product_vintage where p_id = '.$id);
        $this->XM->sqlcore->query('DELETE from product where p_id = '.$id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function delete_vintage($id, &$err){
        $id = (int)$id;
        if($id <= 0){
            $err = langTranslate('product', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_APPROVE_PRODUCT)){
            $res = $this->XM->sqlcore->query('SELECT product.p_is_approved, product.company_id from product inner join product_vintage on product_vintage.p_id = product.p_id where product_vintage.pv_id = '.$id.' LIMIT 1');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if(!$row){
                $err = langTranslate('product', 'err', 'Product doesn\'t exist',  'Product doesn\'t exist');
                return false;
            }
            if($row['p_is_approved']==1 || $row['company_id']!=$this->XM->user->getCompanyId()){
                $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
                return false;
            }
        }
        $res = $this->XM->sqlcore->query('SELECT p_id,pv_blank from product_vintage where pv_id = '.$id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Product doesn\'t exist',  'Product doesn\'t exist');
            return false;
        }
        if($row['pv_blank']){
            return $this->__delete_product($row['p_id'],$err);
        }
        $res = $this->XM->sqlcore->query('SELECT 1
            from product_vintage
            inner join tasting_product_vintage on tasting_product_vintage.pv_id = product_vintage.pv_id
            where product_vintage.pv_id = '.$id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if($row){
            $err = langTranslate('product', 'err', 'Can\'t delete product. Product already added to a tasting',  'Can\'t delete product. Product already added to a tasting');
            return false;
        }
        //лишнее, пока не удаляются старые дегустации
        $res = $this->XM->sqlcore->query('SELECT 1
            from product_vintage
            inner join product_vintage_review on product_vintage_review.pv_id = product_vintage.pv_id
            where product_vintage.pv_id = '.$id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if($row){
            $err = langTranslate('product', 'err', 'Can\'t delete product. Product already has a review',  'Can\'t delete product. Product already has a review');
            return false;
        }
        $this->XM->sqlcore->query('DELETE from product_vintage where pv_id = '.$id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function approve_product($product_id, &$err){
        $product_id = (int)$product_id;
        if($product_id <= 0){
            $err = langTranslate('product', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }

        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_APPROVE_PRODUCT)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT p_origin_name from product where p_id = '.$product_id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Product doesn\'t exist',  'Product doesn\'t exist');
            return false;
        }
        $originname = (string)$row['p_origin_name'];
        $attributes = array();
        $res = $this->XM->sqlcore->query('SELECT pav_id from product_value where p_id = '.$product_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $attributes[] = (int)$row['pav_id'];
        }
        $this->XM->sqlcore->freeResult($res);
		$double_product_id = null;
        if(!$this->check_double_product($product_id,$attributes,$originname,$double_product_id,$err)){
            return false;
        }
        $this->XM->sqlcore->query('UPDATE product set p_is_approved = 1 where p_id = '.$product_id);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function merge_product($merge_into_product_id, $merge_from_product_id, &$has_conflicts, &$err){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_APPROVE_PRODUCT)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $merge_into_product_id = (int)$merge_into_product_id;
        $merge_from_product_id = (int)$merge_from_product_id;
        $has_conflicts = false;

        $has_approved = false;
        $into_approved = false;
        $count = 0;
        $res = $this->XM->sqlcore->query('SELECT p_id,p_is_approved from product where p_id IN ('.$merge_into_product_id.','.$merge_from_product_id.')');
        while($row = $this->XM->sqlcore->getRow($res)){
            $count++;
            if($row['p_is_approved']){
                $has_approved = true;
                if($merge_into_product_id==(int)$row['p_id']){
                    $into_approved = true;
                }
            }
        }
        $this->XM->sqlcore->freeResult($res);
        if($count<2){
            $err = langTranslate('product', 'err', 'Product doesn\'t exist',  'Product doesn\'t exist');
            return false;
        }
        if($has_approved && !$into_approved){
            $err = langTranslate('product', 'err', 'You can\'t merge approved products into not yet approved',  'You can\'t merge approved products into not yet approved');
            return false;
        }

        $this->XM->sqlcore->query('UPDATE product_vintage SET p_id = '.$merge_into_product_id.' where pv_blank = 0 and p_id = '.$merge_from_product_id);
        $this->XM->sqlcore->query('UPDATE product_image SET p_id = '.$merge_into_product_id.' where p_id = '.$merge_from_product_id);
        $this->XM->sqlcore->query('INSERT INTO product_company_favourite
            SELECT '.$merge_into_product_id.' as p_id, pcf_from.company_id
                from product_company_favourite pcf_from
                left join product_company_favourite pcf_into on pcf_into.p_id = '.$merge_into_product_id.' and pcf_into.company_id = pcf_from.company_id
                where pcf_from.p_id = '.$merge_from_product_id.' and pcf_into.p_id is null');
        $res = $this->XM->sqlcore->query('SELECT pv_id,p_id
            from product_vintage
            where p_id IN ('.$merge_into_product_id.','.$merge_from_product_id.') and pv_blank = 1');
        $merge_into_vintage_id = null;
        $merge_from_vintage_id = null;
        while($row = $this->XM->sqlcore->getRow($res)){
            if((int)$row['p_id']==$merge_into_product_id){
                $merge_into_vintage_id = (int)$row['pv_id'];
                continue;
            }
            if((int)$row['p_id']==$merge_from_product_id){
                $merge_from_vintage_id = (int)$row['pv_id'];
            }
        }
        $this->XM->sqlcore->freeResult($res);
        if($merge_into_vintage_id && $merge_from_vintage_id){//always
            $this->__merge_vintage_links($merge_into_vintage_id, $merge_from_vintage_id);
        }

        $this->XM->sqlcore->query('DELETE from product_vintage where p_id = '.$merge_from_product_id);
        $this->XM->sqlcore->query('DELETE from product where p_id = '.$merge_from_product_id);
        $this->XM->sqlcore->commit();
        $res = $this->XM->sqlcore->query('SELECT 1
            from product_vintage
            where p_id = '.$merge_into_product_id.'
            group by pv_year
            having count(1)>1
            limit 1');
        if($this->XM->sqlcore->getRow($res)){
            $has_conflicts = true;
        }
        $this->XM->sqlcore->freeResult($res);
        return true;
    }
    public function merge_vintage($merge_into_vintage_id, $merge_from_vintage_id, &$err){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_APPROVE_PRODUCT)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $merge_into_vintage_id = (int)$merge_into_vintage_id;
        $merge_from_vintage_id = (int)$merge_from_vintage_id;

        $count = 0;
        $res = $this->XM->sqlcore->query('SELECT count(distinct p_id) as p_id_cnt,count(distinct pv_id) as pv_id_cnt from product_vintage where pv_blank = 0 and pv_id IN ('.$merge_into_vintage_id.','.$merge_from_vintage_id.')');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if($row['pv_id_cnt']<2){//never
            $err = langTranslate('product', 'err', 'Vintage doesn\'t exist',  'Vintage doesn\'t exist');
            return false;
        }
        if($row['p_id_cnt']>1){//never
            $err = langTranslate('product', 'err', 'You can\'t merge vintages of different products',  'You can\'t merge vintages of different products');
            return false;
        }
        $this->__merge_vintage_links($merge_into_vintage_id, $merge_from_vintage_id);
        $this->XM->sqlcore->query('DELETE from product_vintage where pv_id = '.$merge_from_vintage_id);
        $this->XM->sqlcore->commit();
        return true;
    }
    private function __merge_vintage_links($merge_into_vintage_id, $merge_from_vintage_id){
        $merge_into_vintage_id = (int)$merge_into_vintage_id;
        $merge_from_vintage_id = (int)$merge_from_vintage_id;
        $this->XM->sqlcore->query('UPDATE tasting_product_vintage SET pv_id = '.$merge_into_vintage_id.' where pv_id = '.$merge_from_vintage_id);

        $this->XM->sqlcore->query('UPDATE product_vintage_review SET pv_id = '.$merge_into_vintage_id.' where pv_id = '.$merge_from_vintage_id);
        $this->__refresh_all_personal_vintage_scores($merge_into_vintage_id);
        $this->__refresh_vintage_score($merge_into_vintage_id);

        $this->XM->sqlcore->query('INSERT INTO product_vintage_favourite
            SELECT '.$merge_into_vintage_id.' as pv_id, pvf_from.user_id
                from product_vintage_favourite pvf_from
                left join product_vintage_favourite pvf_into on pvf_into.pv_id = '.$merge_into_vintage_id.' and pvf_into.user_id = pvf_from.user_id
                where pvf_from.pv_id = '.$merge_from_vintage_id.' and pvf_into.pv_id is null');
        //company prices
        $non_existing_company_prices = array();
        $res = $this->XM->sqlcore->query('SELECT pvcp_from.company_id
            from product_vintage_company_price pvcp_from
            inner join product_vintage_company_price pvcp_to on pvcp_to.pv_id = '.$merge_into_vintage_id.' and pvcp_from.company_id = pvcp_to.company_id
            where pvcp_from.pv_id = '.$merge_into_vintage_id.' and pvcp_to.pv_id is null');
        while($row = $this->XM->sqlcore->getRow($res)){
            $non_existing_company_prices[] = (int)$row['company_id'];
        }
        $this->XM->sqlcore->freeResult($res);
        $non_existing_company_price_chunks = array_chunk($non_existing_company_prices, 50);
        unset($non_existing_company_prices);
        foreach($non_existing_company_price_chunks as $non_existing_company_price_chunk){
            $this->XM->sqlcore->query('UPDATE product_vintage_company_price set pv_id = '.$merge_into_vintage_id.' where pv_id = '.$merge_from_vintage_id.' and company_id in ('.implode(',', $non_existing_company_price_chunk).')');
        }
    }

    public function get_double_vintage_ids($product_id, &$first_vintage_id, &$second_vintage_id){
        $first_vintage_id = $second_vintage_id = null;
        $res = $this->XM->sqlcore->query('SELECT distinct pv_id from product_vintage where p_id = '.$product_id.' and pv_blank = 0 and pv_year = (SELECT pv_year from product_vintage where p_id = '.$product_id.' and pv_blank = 0 group by pv_year having count(1)>1 limit 1) limit 2');
        $pv_ids = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $pv_ids[] = (int)$row['pv_id'];
        }
        $this->XM->sqlcore->freeResult($res);
        if(count($pv_ids)<2){
            return false;
        }
        $first_vintage_id = $pv_ids[0];
        $second_vintage_id = $pv_ids[1];
        return true;
    }
    private function __get_product_images($product_id){
        $result = array();
        $can_delete_images = $this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_EDIT_PRODUCT);
        $res = $this->XM->sqlcore->query('SELECT pi_id,pi_ext from product_image where p_id = '.$product_id.' order by pi_isprimary desc');
        while($row = $this->XM->sqlcore->getRow($res)){
            $result[] = array('id'=>(int)$row['pi_id'],'url'=>BASE_URL.'/modules/Product/productimg/'.$row['pi_id'].'.'.$row['pi_ext'],'can_delete'=>$can_delete_images);
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }
    public function get_blank_vintage_id($product_id){
        if(($product_id = (int)$product_id)<=0){
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT pv_id FROM product_vintage WHERE p_id = '.$product_id.' and pv_blank = 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        return (int)$row['pv_id'];
    }
    public function get_product_attributes($product_id){
        $result = array();
        $res = $this->XM->sqlcore->query('SELECT pav_id
            FROM product_value
            where p_id = '.$product_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $result[] = (int)$row['pav_id'];
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }
    public function can_edit_product($product_id){
        if($this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_EDIT_PRODUCT)){
            return true;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_ADD_PRODUCT)){
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT p_is_approved,company_id FROM product where p_id = '.$product_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        return $row['p_is_approved']!=1 && $row['company_id']==$this->XM->user->getCompanyId();
    }
    public function get_product_info_for_all_languages($product_id){
        if(($product_id = (int)$product_id)<=0){
            return false;
        }
        $result = array();
        $res = $this->XM->sqlcore->query('SELECT product.p_id, product.p_origin_name, product.p_isvintage,product.p_isblend,product.p_alcohol_content
            from product

            where product.p_id = '.$product_id.'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        $result = array(
                'id'=>(int)$row['p_id'],
                'originname'=>$row['p_origin_name'],
                'isvintage'=>$row['p_isvintage']?1:0,
                'isblend'=>$row['p_isblend']?1:0,
                // 'vineyard'=>$row['pvy_name'],
                'alcohol_content'=>($row['p_alcohol_content']!==null)?str_replace('.', ',', ((float)$row['p_alcohol_content'])/100):null,
                'images'=>array(),
                'full_name_template'=>array(),
                'name'=>array(),
                'attr'=>array(),

                'can_edit'=>$this->can_edit_product($product_id),
            );

        $res = $this->XM->sqlcore->query('SELECT lang_id, p_ml_name
            from product_ml
            where p_id = '.$product_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $lang_id = (int)$row['lang_id'];
            $result['name'][$lang_id] = (string)$row['p_ml_name'];
        }
        $this->XM->sqlcore->freeResult($res);

        $result['full_name_template'] = $this->XM->product->get_full_name_templates_for_product($product_id);

        $can_delete_images = $this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_EDIT_PRODUCT);
        $res = $this->XM->sqlcore->query('SELECT pi_id,pi_ext,pi_isprimary from product_image where p_id = '.$product_id.' order by pi_isprimary desc');
        while($row = $this->XM->sqlcore->getRow($res)){
            $result['images'][] = array('id'=>(int)$row['pi_id'],'url'=>BASE_URL.'/modules/Product/productimg/'.$row['pi_id'].'.'.$row['pi_ext'],'primary'=>(int)$row['pi_isprimary'],'can_delete'=>$can_delete_images);
        }
        $this->XM->sqlcore->freeResult($res);
        $result['attr'] = $this->get_product_attributes($product_id);

        $result['grape_variety_concentration'] = $this->get_product_grape_variety_concentration($product_id);
        return $result;
    }
    public function get_product_grape_variety_concentration($product_id){
        $product_id = (int)$product_id;
        $grape_variety_concentration = array();
        $res = $this->XM->sqlcore->query('SELECT pav_id,pv_part from product_value where p_id = '.$product_id.' and pag_id = 7');//grape variery
        while($row = $this->XM->sqlcore->getRow($res)){
            $grape_variety_concentration[(int)$row['pav_id']] = ($row['pv_part']>0)?(int)$row['pv_part']:null;
        }
        $this->XM->sqlcore->freeResult($res);
        return $grape_variety_concentration;
    }
    public function filter_vintage($search_string, $is_strict, $attributes, $year_from, $year_to, $score_from, $score_to, $alcohol_content_from, $alcohol_content_to, $only_favourites, $only_company_favourites, $only_blank, $only_waiting_for_approval, $only_translations, $only_myreviews, $only_scored, $only_awarded, $tasting_list, $only_personally_scored, $only_pending_reviews_for_tasting, $only_from_contest, $only_from_contest_participant, $only_having_offers, $only_having_vintages, $return_vintages, &$vintages, $return_full_product_id_list, $order_by_field, $order_by_direction_asc, &$page, &$pagelimit, &$count, &$err){
        if(($page = (int)$page)<=0){
            $page = 1;
        }
        $pagelimit = (int)$pagelimit;
        if($pagelimit<=0 || $pagelimit>100){
            $pagelimit = 50;
        }
        if($only_translations && !$this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
            $count = 0;
            return array();
        }
        if($only_waiting_for_approval){
            if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_APPROVE_PRODUCT)){
                $only_waiting_for_approval = false;
            } else {
                $only_blank = true;
            }
        }
        $show_all_scores = $this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_VIEW_ALL_SCORES);
        $year_from = (int)$year_from;
        $year_to = (int)$year_to;
        $score_from = (int)((float)str_replace(',', '.', $score_from)*100);
        $score_to = (int)((float)str_replace(',', '.', $score_to)*100);
        $alcohol_content_from = (int)((float)str_replace(',', '.', $alcohol_content_from)*100);
        $alcohol_content_to = (int)((float)str_replace(',', '.', $alcohol_content_to)*100);
        $only_favourites = (bool)$only_favourites;

        $only_from_contest = (int)$only_from_contest;
        $$only_from_contest_participant = (int)$only_from_contest_participant;
        if(!is_array($tasting_list)){
            $tasting_list = array();
        }
        if(!empty($tasting_list)){
            $clear_tasting_list = array();
            foreach($tasting_list as $tasting_id){
                $tasting_id = (int)$tasting_id;
                if(!in_array($tasting_id, $clear_tasting_list)){
                    $clear_tasting_list[] = $tasting_id;
                }
            }
            $tasting_list = $clear_tasting_list;
            unset($clear_tasting_list);
        }
        $attributes = $this->clean_attributes($attributes,false);
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtervals (
                id BIGINT UNSIGNED NOT NULL
            )');
        foreach($attributes as $attrval_id){
            $this->XM->sqlcore->query('INSERT INTO filtervals (id) VALUES ('.$attrval_id.')');
        }
        //analogs
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE analog_filtervals_copy SELECT id FROM filtervals');
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE analog_filtervals_copy2 SELECT id FROM filtervals');
        $this->XM->sqlcore->query('INSERT INTO filtervals
            SELECT distinct product_attribute_value_analog.pav_id
                from product_attribute_value_analog
                inner join product_attribute_value_analog pava_group on pava_group.pava_group_id = product_attribute_value_analog.pava_group_id
                inner join analog_filtervals_copy on analog_filtervals_copy.id = pava_group.pav_id
                where not exists (select 1 from analog_filtervals_copy2 where analog_filtervals_copy2.id = product_attribute_value_analog.pav_id limit 1)');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS analog_filtervals_copy');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS analog_filtervals_copy2');

        $res = $this->XM->sqlcore->query('SELECT distinct product_attribute_group.pag_id,product_attribute_group.pag_overload
                from product_attribute_value
                inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
                inner join product_attribute_group on product_attribute_group.pag_id = product_attribute.pag_id
                inner join filtervals on filtervals.id = product_attribute_value.pav_id');
        $pag_ids = array();
        $joinstack = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $pag_id = (int)$row['pag_id'];
            $pag_ids[] = $pag_id;
            if($row['pag_overload']==1){
                $joinstack[] = 'inner join product_vintage_value pvv'.$pag_id.' on pvv'.$pag_id.'.pv_id = product_vintage.pv_id and pvv'.$pag_id.'.pag_id = '.$pag_id.'
                                inner join filtergv'.$pag_id.' on filtergv'.$pag_id.'.pav_id = pvv'.$pag_id.'.pav_id';
            } else {
                $joinstack[] = 'inner join product_value pv'.$pag_id.' on pv'.$pag_id.'.p_id = product.p_id and pv'.$pag_id.'.pag_id = '.$pag_id.'
                                inner join filtergv'.$pag_id.' on filtergv'.$pag_id.'.pav_id = pv'.$pag_id.'.pav_id';
            }

        }
        $this->XM->sqlcore->freeResult($res);

        foreach($pag_ids as $pag_id){
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtergv'.$pag_id.'
            SELECT distinct product_attribute_value_tree.pav_id
                from product_attribute_value
                inner join filtervals on filtervals.id = product_attribute_value.pav_id
                inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
                inner join product_attribute_value_tree on product_attribute_value_tree.pav_anc_id = product_attribute_value.pav_id
                where product_attribute.pag_id = '.$pag_id);

        }
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filtervals');
        //prepare params
        $where_arr = array();
        if($only_waiting_for_approval){
            $where_arr[] = 'product.p_is_approved = 0';
        } else {
            $where_arr[] = '( product.p_is_approved = 1 or product.company_id = '.$this->XM->user->getCompanyId().')';
        }
        $personal_score_left_join = $personal_score_inner_join = $vintage_scores_left_join = '';
        if(!$only_blank){//year and score is vintage param
            if($year_from>0){
                if($year_to>0){
                    $where_arr[] = 'product_vintage.pv_year between '.$year_from.' and '.$year_to;
                } else {
                    $where_arr[] = 'product_vintage.pv_year >= '.$year_from;
                }
            } elseif($year_to>0){
                $where_arr[] = 'product_vintage.pv_year <= '.$year_to;
            }

            if($only_personally_scored){
                $personal_score_inner_join = 'inner join product_vintage_personal_score on product_vintage_personal_score.pv_id = product_vintage.pv_id and product_vintage_personal_score.user_id = '.$this->XM->user->getUserId();
            } else {
                if($only_scored){
                    $personal_score_left_join = 'left join product_vintage_personal_score on product_vintage_personal_score.pv_id = product_vintage.pv_id and product_vintage_personal_score.user_id = '.$this->XM->user->getUserId();
                    if($show_all_scores){
                        $vintage_scores_left_join = 'left join product_vintage_score product_vintage_score1 on product_vintage_score1.pv_id = product_vintage.pv_id and product_vintage_score1.user_expert_level = 1
                        left join product_vintage_score product_vintage_score2 on product_vintage_score2.pv_id = product_vintage.pv_id and product_vintage_score2.user_expert_level = 2
                        left join product_vintage_score product_vintage_score3 on product_vintage_score3.pv_id = product_vintage.pv_id and product_vintage_score3.user_expert_level = 3';
                        $where_arr[] = '( product_vintage_score1.pvs_score is not null or product_vintage_score2.pvs_score is not null or product_vintage_score3.pvs_score is not null or product_vintage_personal_score.pv_id is not null )';
                    } else {
                        $vintage_scores_left_join = 'left join product_vintage_score product_vintage_score3 on product_vintage_score3.pv_id = product_vintage.pv_id and product_vintage_score3.user_expert_level = 3';
                        $where_arr[] = '( product_vintage_score3.pvs_score is not null or product_vintage_personal_score.pv_id is not null )';
                    }
                }
            }
            if($score_from>0){
                if(!$vintage_scores_left_join){
                    $vintage_scores_left_join = 'left join product_vintage_score product_vintage_score3 on product_vintage_score3.pv_id = product_vintage.pv_id and product_vintage_score3.user_expert_level = 3';
                }
                if($score_to>0){
                    $where_arr[] = 'product_vintage_score3.pvs_score between '.$score_from.' and '.$score_to;
                } else {
                    $where_arr[] = 'product_vintage_score3.pvs_score >= '.$score_from;
                }
            } elseif($score_to>0){
                if(!$vintage_scores_left_join){
                    $vintage_scores_left_join = 'left join product_vintage_score product_vintage_score3 on product_vintage_score3.pv_id = product_vintage.pv_id and product_vintage_score3.user_expert_level = 3';
                }
                $where_arr[] = 'product_vintage_score3.pvs_score <= '.$score_to;
            }
        }
        if($alcohol_content_from>0){
            if($alcohol_content_to>0){
                $where_arr[] = 'product_vintage.pv_alcohol_content between '.$alcohol_content_from.' and '.$alcohol_content_to;
            } else {
                $where_arr[] = 'product_vintage.pv_alcohol_content >= '.$alcohol_content_from;
            }
        } elseif($alcohol_content_to>0){
            $where_arr[] = 'product_vintage.pv_alcohol_content <= '.$alcohol_content_to;
        }
        if($only_awarded){
            $where_arr[] = 'product_vintage.pv_won_contest_nominations = 1';
        }
        $where_sql = '';
        if(!empty($where_arr)){
            $where_sql = 'where '.implode(' and ', $where_arr);
        }
        $vintage_on_sql = '';
        if($only_blank){
            $vintage_on_sql = 'product_vintage.pv_blank = 1';//only blanks
        } else {
            $vintage_on_sql = '( product.p_isvintage xor product_vintage.pv_blank )';//blanks only if non-vintage products
        }
        $favjoin = '';
        if($this->XM->user->isLoggedIn()&&$only_favourites){
            $favjoin = 'inner join product_vintage_favourite on product_vintage_favourite.user_id = '.$this->XM->user->getUserId().' and product_vintage_favourite.pv_id = product_vintage.pv_id';
        }
        $favcompanyjoin = '';
        if($this->XM->user->isInCompany()&&$only_company_favourites){
            $favcompanyjoin = 'inner join product_company_favourite on product_company_favourite.company_id = '.$this->XM->user->getCompanyId().' and product_company_favourite.p_id = product_vintage.p_id';
        }
        $translationjoin = '';
        if($only_translations){
            $translationjoin = 'inner join product_vintage_ml on product_vintage_ml.pv_id = product_vintage.pv_id and product_vintage_ml.pv_ml_is_approved = 0 and product_vintage_ml.lang_id = '.$this->XM->lang->getCurrLangId();
        }
        $myreviews_innerjoin = '';
        if($only_myreviews){
            $myreviews_innerjoin = 'inner join (
                select distinct pv_id from product_vintage_review where user_id = '.$this->XM->user->getUserId().' and pvr_block&~'.(\PRODUCT\PVR_BLOCK_PERSONAL|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE).' = 0
            ) as only_myreviews on only_myreviews.pv_id = product_vintage.pv_id';
        }
        $tasting_list_inner_join = '';
        if(!empty($tasting_list)){
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE tasting_list_tasting_ids (
                `t_id` bigint(20) UNSIGNED NOT NULL
            )');
            foreach($tasting_list as $tasting_id){
                $this->XM->sqlcore->query('INSERT INTO tasting_list_tasting_ids (t_id) VALUES ('.$tasting_id.')');
            }
            $tasting_list_inner_join = 'inner join (select distinct pv_id
                    from tasting_product_vintage
                    inner join tasting_list_tasting_ids on tasting_list_tasting_ids.t_id = tasting_product_vintage.t_id
                ) as tasting_list_inner_join on tasting_list_inner_join.pv_id = product_vintage.pv_id';
        }
        $pendingreviewsfortastingjoin = '';
        // if($only_pending_reviews_for_tasting){
        //     $pendingreviewsfortastingjoin = 'inner join tasting_user_review on tasting_user_review.pv_id = product_vintage.pv_id and tasting_user_review.user_id = '.$this->XM->user->getUserId().' and tasting_user_review.t_id = '.$only_pending_reviews_for_tasting.' and tasting_user_review.pvr_id is null and tasting_user_review.tur_didnt_taste = 0';
        // }
        $only_from_contest_inner_join = '';
        if($only_from_contest){
            $only_from_contest_participant_inner_join = '';
            if($only_from_contest_participant){
                $only_from_contest_participant_inner_join = 'inner join tasting_user on tasting_user.t_id = tasting_product_vintage.t_id and tasting_user.user_id = '.$only_from_contest_participant;
            }
            $only_from_contest_inner_join = 'inner join (
                    select distinct tasting_product_vintage.pv_id
                        from tasting_contest_tasting
                        inner join tasting_product_vintage on tasting_product_vintage.t_id = tasting_contest_tasting.t_id
                        '.$only_from_contest_participant_inner_join.'
                        where tasting_contest_tasting.tc_id = '.$only_from_contest.'
                ) only_from_contest_inner_join on only_from_contest_inner_join.pv_id = product_vintage.pv_id';
        }
        $only_having_offers_inner_join = '';
        if($only_having_offers){
            if($only_blank){
                $only_having_offers_inner_join = 'inner join (
                        select distinct product.p_id
                            from product
                            inner join product_vintage on product_vintage.p_id = product.p_id
                            inner join product_vintage_company_price on product_vintage_company_price.pv_id = product_vintage.pv_id
                            inner join company on company.company_id = product_vintage_company_price.company_id and company.company_is_approved = 1
                    ) as only_having_offers_p_ids on only_having_offers_p_ids.p_id = product.p_id';
            } else {
                $only_having_offers_inner_join = 'inner join (
                        select distinct pv_id
                            from product_vintage_company_price
                            inner join company on company.company_id = product_vintage_company_price.company_id and company.company_is_approved = 1
                    ) as only_having_offers_pv_ids on only_having_offers_pv_ids.pv_id = product_vintage.pv_id';
            }

        }

        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filterpvids (
                `pv_id` bigint(20) UNSIGNED NOT NULL,
                `rel` bigint(20) UNSIGNED NOT NULL,
                PRIMARY KEY filterpvids_pkey (pv_id)
            )');
        $this->XM->sqlcore->query('INSERT INTO filterpvids
            SELECT distinct product_vintage.pv_id, 0 as rel
            from product
            inner join product_vintage on product.p_id = product_vintage.p_id and '.$vintage_on_sql.'
            '.$favjoin.'
            '.$favcompanyjoin.'
            '.$translationjoin.'
            '.$personal_score_inner_join.'
            '.$myreviews_innerjoin.'
            '.$tasting_list_inner_join.'
            '.$only_from_contest_inner_join.'
            '.$only_having_offers_inner_join.'
            '.$pendingreviewsfortastingjoin.'
            '.implode(' ', $joinstack).'
            '.$personal_score_left_join.'
            '.$vintage_scores_left_join.'
            '.$where_sql);
        foreach($pag_ids as $pag_id){
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filtergv'.$pag_id);
        }
        if(strlen($search_string)){
            $search_string_words = explode(' ', $this->XM->sqlcore->search_engine_alias($search_string));
            unset($search_string);
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filterpvids2 SELECT pv_id FROM filterpvids');
            $digits_search_strings = array();
            foreach($search_string_words as $key=>$search_string_word){
                $mb_strlen = mb_strlen($search_string_word,'UTF-8');
                if($mb_strlen<=2){//excessive
                    continue;
                }
                //attribute
                $this->XM->sqlcore->query('UPDATE filterpvids SET rel = rel + 2 WHERE pv_id IN (
                        select distinct filterpvids2.pv_id
                            from filterpvids2
                            inner join product_vintage on product_vintage.pv_id = filterpvids2.pv_id
                            inner join product_value on product_value.p_id = product_vintage.p_id
                            inner join product_attribute_group on product_attribute_group.pag_id = product_value.pag_id and product_attribute_group.pag_overload = 0 and product_attribute_group.pag_used_in_filter = 1
                            inner join product_attribute_value_se on product_attribute_value_se.pav_id = product_value.pav_id and match(product_attribute_value_se.pav_se_text) against(\''.$this->XM->sqlcore->prepString($search_string_word,128).'*\' in boolean mode)
                    )');
                $this->XM->sqlcore->query('UPDATE filterpvids SET rel = rel + 2 WHERE pv_id IN (
                        select distinct filterpvids2.pv_id
                            from filterpvids2
                            inner join product_vintage_value on product_vintage_value.pv_id = filterpvids2.pv_id
                            inner join product_attribute_group on product_attribute_group.pag_id = product_vintage_value.pag_id and product_attribute_group.pag_overload = 1 and product_attribute_group.pag_used_in_filter = 1
                            inner join product_attribute_value_se on product_attribute_value_se.pav_id = product_vintage_value.pav_id and match(product_attribute_value_se.pav_se_text) against(\''.$this->XM->sqlcore->prepString($search_string_word,128).'*\' in boolean mode)
                    )');
                //year
                if(preg_match('#^\d+$#', $search_string_word)){
                    $digits_search_strings[] = (int)$search_string_word;
                }
                //name
                $this->XM->sqlcore->query('UPDATE filterpvids SET rel = rel + 20 WHERE pv_id IN (
                        select distinct filterpvids2.pv_id
                            from filterpvids2
                            inner join product_vintage on product_vintage.pv_id = filterpvids2.pv_id
                            inner join product_se on product_se.p_id = product_vintage.p_id and match(product_se.p_se_text) against(\''.$this->XM->sqlcore->prepString($search_string_word,128).'*\' in boolean mode)
                    )');
            }
            if(!empty($digits_search_strings)){
                $digits_search_strings = array_unique($digits_search_strings);
                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE digitssearchstrings (
                    `val` bigint(20) UNSIGNED NOT NULL
                )');
                foreach($digits_search_strings as $digits_search_string){
                    $this->XM->sqlcore->query('INSERT INTO digitssearchstrings (val) VALUES ('.$digits_search_string.')');
                }
                $this->XM->sqlcore->query('UPDATE filterpvids SET rel = rel + 1 WHERE pv_id IN (
                    select distinct filterpvids2.pv_id
                        from filterpvids2
                        inner join product_vintage on product_vintage.pv_id = filterpvids2.pv_id
                        inner join digitssearchstrings on digitssearchstrings.val = product_vintage.pv_year
                )');
                $this->XM->sqlcore->query('UPDATE filterpvids SET rel = rel + 100 WHERE pv_id IN (
                        select distinct filterpvids2.pv_id
                            from filterpvids2
                            inner join product_vintage on product_vintage.pv_id = filterpvids2.pv_id
                            inner join digitssearchstrings on digitssearchstrings.val = product_vintage.p_id
                    )');
                $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS digitssearchstrings');

            }
            if($is_strict){
                $res = $this->XM->sqlcore->query('SELECT max(rel) as maxrel from filterpvids where rel > 0');
                $row = $this->XM->sqlcore->getRow($res);
                $this->XM->sqlcore->freeResult($res);
                if(!$row || !$row['maxrel']){
                    $this->XM->sqlcore->query('DELETE FROM filterpvids');
                } else {
                    $this->XM->sqlcore->query('DELETE FROM filterpvids where rel < '.(int)$row['maxrel']);
                }
            } else {
                $this->XM->sqlcore->query('DELETE FROM filterpvids WHERE rel = 0');
            }
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filterpvids2');
        }
        if($return_vintages){
            $vintages = array();
            $res = $this->XM->sqlcore->query('SELECT distinct coalesce(product_vintage.pv_year,\'NV\') as pv_year
                from filterpvids
                inner join product_vintage pv_filter on pv_filter.pv_id = filterpvids.pv_id
                inner join product on product.p_id = pv_filter.p_id
                inner join product_vintage on product_vintage.p_id = product.p_id and (product_vintage.pv_blank xor product.p_isvintage)
                '.($only_having_offers?'inner join product_vintage_company_price on product_vintage_company_price.pv_id = product_vintage.pv_id
                inner join company as company_offers_check on company_offers_check.company_id = product_vintage_company_price.company_id and company_offers_check.company_is_approved = 1':'').'
                order by coalesce(product_vintage.pv_year,\'NV\') asc');
            while($row = $this->XM->sqlcore->getRow($res)){
                $vintages[] = $row['pv_year'];
            }
            $this->XM->sqlcore->freeResult($res);
        }
        if(is_array($only_having_vintages) && !empty($only_having_vintages)){
            $only_having_vintages_inner_join = '';
            $only_having_vintages_where_sql = '';
            $include_nv = in_array('NV', $only_having_vintages);
            if(count($only_having_vintages)>($include_nv?1:0)){
                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE only_having_vintages_vintages (
                    `pv_year` smallint(5) UNSIGNED NULL
                )');
                foreach($only_having_vintages as $vintage){
                    if($include_nv && $vintage=='NV'){
                        continue;
                    }
                    $this->XM->sqlcore->query('INSERT INTO only_having_vintages_vintages (pv_year) VALUES ('.((int)$vintage).')');
                }
                $only_having_vintages_inner_join = 'inner join only_having_vintages_vintages on only_having_vintages_vintages.pv_year = product_vintage.pv_year'.($include_nv?' or product.p_isvintage = 0':'');
            } elseif($include_nv){
                $only_having_vintages_where_sql = 'where product.p_isvintage = 0';
            }
            if($only_having_vintages_inner_join || $only_having_vintages_where_sql){
                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE only_having_vintages_pv_ids (
                    `pv_id` bigint(20) UNSIGNED NOT NULL,
                    PRIMARY KEY only_having_vintages_pv_ids_pk (pv_id)
                )');
                if($only_blank){
                    $this->XM->sqlcore->query('INSERT INTO only_having_vintages_pv_ids (pv_id)
                        select distinct filterpvids.pv_id
                            from filterpvids
                            inner join product_vintage pv_blank on pv_blank.pv_id = filterpvids.pv_id
                            inner join product on product.p_id = pv_blank.p_id
                            inner join product_vintage on product_vintage.p_id = product.p_id
                            '.($only_having_offers?'inner join product_vintage_company_price on product_vintage_company_price.pv_id = product_vintage.pv_id
                                inner join company as company_offers_check on company_offers_check.company_id = product_vintage_company_price.company_id and company_offers_check.company_is_approved = 1':'').'
                            '.$only_having_vintages_inner_join.'
                            '.$only_having_vintages_where_sql);
                } else {
                    $this->XM->sqlcore->query('INSERT INTO only_having_vintages_pv_ids (pv_id)
                    select distinct filterpvids.pv_id
                        from filterpvids
                        inner join product_vintage on product_vintage.pv_id = filterpvids.pv_id
                        inner join product on product.p_id = product_vintage.p_id
                        '.$only_having_vintages_inner_join.'
                        '.$only_having_vintages_where_sql);
                }

                $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS only_having_vintages_vintages');
                $this->XM->sqlcore->query('DELETE FROM filterpvids where not exists (select 1 from only_having_vintages_pv_ids where pv_id = filterpvids.pv_id limit 1)');
                $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS only_having_vintages_pv_ids');
            }
        }
        if($return_full_product_id_list){//alter return, used in searcher
            $result = array();
            $res = $this->XM->sqlcore->query('SELECT distinct p_id from filterpvids inner join product_vintage on product_vintage.pv_id = filterpvids.pv_id');
            while($row = $this->XM->sqlcore->getRow($res)){
                $result[] = (int)$row['p_id'];
            }
            $this->XM->sqlcore->freeResult($res);
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filterpvids');
            return $result;
        }
        $res = $this->XM->sqlcore->query('SELECT count(1) as cnt from filterpvids');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        $count = (int)$row['cnt'];
        if($count==0){
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filterpvids');
            return array();
        }
        if(($page-1)*$pagelimit>=$count){
            $page = ceil($count/$pagelimit);
        }
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filterpvids2 SELECT pv_id FROM filterpvids');

        $favselect = '0 as favourite';
        $favjoin = '';
        if($this->XM->user->isLoggedIn()){
            if($only_favourites){
                $favselect = '1 as favourite';
            } else {
                $favselect = 'IF(product_vintage_favourite.pv_id is null, 0, 1) as favourite';
                $favjoin = 'left join product_vintage_favourite on product_vintage_favourite.user_id = '.$this->XM->user->getUserId().' and product_vintage_favourite.pv_id = product_vintage.pv_id';
            }
        }
        $favcompanyselect = '0 as company_favourite';
        $favcompanyjoin = '';
        if($this->XM->user->isInCompany()){
            if($only_company_favourites){
                $favcompanyselect = '1 as company_favourite';
            } else {
                $favcompanyselect = 'IF(product_company_favourite.p_id is null, 0, 1) as company_favourite';
                $favcompanyjoin = 'left join product_company_favourite on product_company_favourite.company_id = '.$this->XM->user->getCompanyId().' and product_company_favourite.p_id = product_vintage.p_id';
            }
        }

        if(!$personal_score_inner_join){
            $personal_score_left_join = 'left join product_vintage_personal_score on product_vintage_personal_score.pv_id = product_vintage.pv_id and product_vintage_personal_score.user_id = '.$this->XM->user->getUserId();
        }
        $personal_score_select = 'product_vintage_personal_score.pvps_score as personal_score';

        $select_score_sql = $vintage_scores_left_join = '';
        if($show_all_scores){
            $vintage_scores_left_join = 'left join product_vintage_score product_vintage_score1 on product_vintage_score1.pv_id = product_vintage.pv_id and product_vintage_score1.user_expert_level = 1
                left join product_vintage_score product_vintage_score2 on product_vintage_score2.pv_id = product_vintage.pv_id and product_vintage_score2.user_expert_level = 2
                left join product_vintage_score product_vintage_score3 on product_vintage_score3.pv_id = product_vintage.pv_id and product_vintage_score3.user_expert_level = 3';
            $select_score_sql = 'product_vintage_score1.pvs_score as score1,product_vintage_score2.pvs_score as score2,product_vintage_score3.pvs_score as score3';
        } else {
            $vintage_scores_left_join = 'left join product_vintage_score product_vintage_score3 on product_vintage_score3.pv_id = product_vintage.pv_id and product_vintage_score3.user_expert_level = 3';
            $select_score_sql = 'null as score1,null as score2,product_vintage_score3.pvs_score as score3';
        }
        $select_score_left_join = '';
        if(!empty($tasting_list) || $only_from_contest){
            $select_score_sql = 'select_score.score1 as score1,select_score.score2 as score2,select_score.score3 as score3';
            $personal_score_select = 'personal_select_score.personal_score as personal_score';
            $personal_score_inner_join = '';
            $select_score_tasting_list_inner_join = '';
            if(!empty($tasting_list)){
                $select_score_tasting_list_inner_join = 'inner join tasting_list_tasting_ids on tasting_list_tasting_ids.t_id = product_vintage_review.t_id';
            }
            $select_score_only_from_contest_inner_join = '';
            $select_score_only_from_contest_where_sql = '';
            if($only_from_contest){
                $select_score_only_from_contest_inner_join = 'inner join tasting_contest_tasting on tasting_contest_tasting.t_id = product_vintage_review.t_id and tasting_contest_tasting.tc_id = '.$only_from_contest;
                if($only_from_contest_participant){
                    $select_score_only_from_contest_where_sql = ' and product_vintage_review.user_id = '.$only_from_contest_participant;
                }
            }
            $select_score_left_join = 'left join (
                select product_vintage_review.pv_id,round(avg(if(product_vintage_review.user_expert_level = 1,product_vintage_review.pvr_score,null))) as score1,
                        round(avg(if(product_vintage_review.user_expert_level = 2,product_vintage_review.pvr_score,null))) as score2,
                        round(avg(if(product_vintage_review.user_expert_level = 3,product_vintage_review.pvr_score,null))) as score3
                    from product_vintage_review
                    '.$select_score_tasting_list_inner_join.'
                    '.$select_score_only_from_contest_inner_join.'
                    where product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_PERSONAL|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE).' = 0 '.$select_score_only_from_contest_where_sql.'
                    group by product_vintage_review.pv_id
            ) as select_score on select_score.pv_id = product_vintage.pv_id';
            $personal_score_left_join = 'left join (
                select product_vintage_review.pv_id,round(avg(product_vintage_review.pvr_score)) as personal_score
                    from product_vintage_review
                    '.$select_score_tasting_list_inner_join.'
                    '.$select_score_only_from_contest_inner_join.'
                    where product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_PERSONAL|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE).' = 0 and product_vintage_review.user_id = '.$this->XM->user->getUserId().'
                    group by product_vintage_review.pv_id
            ) as personal_select_score on personal_select_score.pv_id = product_vintage.pv_id';
        }

        $evaluation_scores_select_sql = '';
        $evaluation_scores_left_join = '';
        if($only_from_contest && $only_from_contest_participant){
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE only_participants_of_contest_evaluation_scores (
                `opoces_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `tpv_id` bigint(20) UNSIGNED NOT NULL,
                `tue_type` tinyint(1) UNSIGNED NOT NULL,
                `tueus_score` int(5) UNSIGNED NOT NULL,
                PRIMARY KEY only_participants_of_contest_evaluation_scores_pk (opoces_id),
                INDEX only_participants_of_contest_evaluation_scores_tpv_id_index (tpv_id)
            )');
            $this->XM->sqlcore->query('INSERT INTO only_participants_of_contest_evaluation_scores
                SELECT null,tasting_user_evaluation.tpv_id,tasting_user_evaluation.tue_type,floor(coalesce(tasting_user_evaluation_user_score.tueus_score,0)*10000/only_participants_of_contest_evaluation_scores_max_scores.tueus_score) as tueus_score
                from tasting_user_evaluation
                inner join tasting_contest_tasting on tasting_contest_tasting.t_id = tasting_user_evaluation.t_id and tasting_contest_tasting.tc_id = '.$only_from_contest.'
                inner join tasting_user on tasting_user.t_id = tasting_user_evaluation.t_id and tasting_user.user_id = '.$only_from_contest_participant.'
                inner join (
                        select tasting_user_evaluation.tpv_id,tasting_user_evaluation.tue_type,max(tasting_user_evaluation_user_score.tueus_score) as tueus_score
                            from tasting_user_evaluation
                            inner join tasting_contest_tasting on tasting_contest_tasting.t_id = tasting_user_evaluation.t_id and tasting_contest_tasting.tc_id = '.$only_from_contest.'
                            inner join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id
                            group by tasting_user_evaluation.tpv_id,tasting_user_evaluation.tue_type
                    ) as only_participants_of_contest_evaluation_scores_max_scores on only_participants_of_contest_evaluation_scores_max_scores.tpv_id = tasting_user_evaluation.tpv_id and only_participants_of_contest_evaluation_scores_max_scores.tue_type = tasting_user_evaluation.tue_type
                left join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id and tasting_user_evaluation_user_score.user_id = tasting_user.user_id
                where tasting_user_evaluation.tue_type in (1,2)');
            //leniency disabled
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE only_participants_of_contest_evaluation_scores_manual
                (PRIMARY KEY only_participants_of_contest_evaluation_scores_manual_pkey (pv_id))
                select tasting_product_vintage.pv_id, avg(tueus_score) as score
                    from only_participants_of_contest_evaluation_scores
                    inner join tasting_product_vintage on tasting_product_vintage.tpv_id = only_participants_of_contest_evaluation_scores.tpv_id
                    where only_participants_of_contest_evaluation_scores.tue_type = 1
                    group by tasting_product_vintage.pv_id');
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE only_participants_of_contest_evaluation_scores_automatic
                (PRIMARY KEY only_participants_of_contest_evaluation_scores_automatic_pkey (pv_id))
                select tasting_product_vintage.pv_id, avg(tueus_score) as score
                    from only_participants_of_contest_evaluation_scores
                    inner join tasting_product_vintage on tasting_product_vintage.tpv_id = only_participants_of_contest_evaluation_scores.tpv_id
                    where only_participants_of_contest_evaluation_scores.tue_type = 2
                    group by tasting_product_vintage.pv_id');
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS only_participants_of_contest_evaluation_scores');
            $evaluation_scores_left_join = 'left join only_participants_of_contest_evaluation_scores_manual as manual_evaluation_score on manual_evaluation_score.pv_id = product_vintage.pv_id
                left join only_participants_of_contest_evaluation_scores_automatic as automatic_evaluation_score on automatic_evaluation_score.pv_id = product_vintage.pv_id';
            $evaluation_scores_select_sql = 'manual_evaluation_score.score as manual_evaluation_score,automatic_evaluation_score.score as automatic_evaluation_score';
        }

        $only_from_contest_select_sql = '';
        $only_from_contest_inner_join = '';
        if($only_from_contest){
            $only_from_contest_select_sql = ',only_from_contest_inner_join.can_view_certificate';
            $only_from_contest_inner_join = 'inner join (
                    select tasting_product_vintage.pv_id,min(if(tasting_product_vintage.tpv_review_request_status=2,1,0)) as can_view_certificate
                        from tasting_contest
                        inner join tasting_contest_tasting on tasting_contest_tasting.tc_id = tasting_contest.tc_id
                        inner join tasting_product_vintage on tasting_product_vintage.t_id = tasting_contest_tasting.t_id
                        where tasting_contest.tc_id = '.$only_from_contest.'
                        group by tasting_product_vintage.pv_id
                ) only_from_contest_inner_join on only_from_contest_inner_join.pv_id = product_vintage.pv_id';
        }
        $only_myreviews_score_inner_join = '';
        $order_by_sql = null;
        switch($order_by_field){
            case 'name':
                $order_by_sql = 'p_ml_fullname '.($order_by_direction_asc?'asc':'desc');
                break;
            case 'year':
                $order_by_sql = '(p_isvintage = 0 || pv_year is not null) desc, pv_year '.($order_by_direction_asc?'asc':'desc').', p_isvintage '.($order_by_direction_asc?'asc':'desc').'';
                break;
            case 'score1':
                $order_by_sql = 'score1 is not null desc, score1 '.($order_by_direction_asc?'asc':'desc').', pv_year desc, p_ml_fullname asc';
                break;
            case 'score2':
                $order_by_sql = 'score2 is not null desc, score2 '.($order_by_direction_asc?'asc':'desc').', pv_year desc, p_ml_fullname asc';
                break;
            case 'score3':
                $order_by_sql = 'score3 is not null desc, score3 '.($order_by_direction_asc?'asc':'desc').', pv_year desc, p_ml_fullname asc';
                break;
            case 'score-personal':
                $order_by_sql = 'personal_score is not null desc, personal_score '.($order_by_direction_asc?'asc':'desc');
                break;
            case 'automatic-evaluation':
                if($evaluation_scores_left_join){
                    $order_by_sql = 'coalesce(automatic_evaluation_score.score,0) '.($order_by_direction_asc?'asc':'desc').', pv_year desc, p_ml_fullname asc';
                } else {
                    $order_by_sql = 'pv_year desc, p_ml_fullname asc';
                }
                break;
            case 'manual-evaluation':
                if($evaluation_scores_left_join){
                    $order_by_sql = 'coalesce(manual_evaluation_score.score,0) '.($order_by_direction_asc?'asc':'desc').', pv_year desc, p_ml_fullname asc';
                } else {
                    $order_by_sql = 'pv_year desc, p_ml_fullname asc';
                }
                break;
            default:

                if($only_scored || $only_from_contest){
                    $order_by_sql = 'score3 desc, score2 desc, score1 desc';
                } else {
                    $order_by_sql = 'pv_year desc, p_ml_fullname asc';
                }
        }

        $color_captions = array();
        $res = $this->XM->sqlcore->query('SELECT distinct product_attribute_value.pav_id,coalesce(product_attribute_value_ml.pav_ml_name,product_attribute_value.pav_origin_name,\'-\') as pav_ml_name
            FROM product_attribute_value
            inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
            left join (select product_attribute_value.pav_id,substring_index(group_concat(pav_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pav_ml_id
                from product_attribute_value_ml
                inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_ml.pav_id
                inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pa_show_only_origin = 0
                where pav_ml_name is not null and product_attribute.pag_id = '.\PRODUCT\COLOR_ATTRIBUTE_GROUP_ID.' and not (pav_origin_name is not null and lang_id <> '.$this->XM->lang->getCurrLangId().')
                group by product_attribute_value.pav_id
            ) as ln_glue on ln_glue.pav_id = product_attribute_value.pav_id
            left join product_attribute_value_ml on product_attribute_value_ml.pav_ml_id = ln_glue.pav_ml_id
            where product_attribute.pag_id = '.\PRODUCT\COLOR_ATTRIBUTE_GROUP_ID);
        while($row = $this->XM->sqlcore->getRow($res)){
            $color_captions[(int)$row['pav_id']] = trim($row['pav_ml_name']);
        }
        $this->XM->sqlcore->freeResult($res);

        $result = array();
        $res = $this->XM->sqlcore->query('SELECT product_vintage.pv_id, coalesce(p_ml.p_ml_fullname,product_vintage.p_id) as p_ml_fullname, product_vintage.pv_year, '.$select_score_sql.', '.$personal_score_select.', product.p_isvintage, product_vintage.pv_blank, product_image.pi_id, product_image.pi_ext, color_pav.pav_id as color_pav_id, product_vintage.p_id, product.p_is_approved, product_vintage.pv_won_contest_nominations, '.$favselect.','.$favcompanyselect.' '.$only_from_contest_select_sql.($evaluation_scores_select_sql?','.$evaluation_scores_select_sql:'').'
            from filterpvids
            inner join product_vintage on product_vintage.pv_id = filterpvids.pv_id
            inner join product on product.p_id = product_vintage.p_id
            '.$only_myreviews_score_inner_join.'
            '.$personal_score_inner_join.'
            '.$only_from_contest_inner_join.'
            '.$favjoin.'
            '.$favcompanyjoin.'
            '.$select_score_left_join.'
            '.$personal_score_left_join.'
            '.$vintage_scores_left_join.'
            '.$evaluation_scores_left_join.'
            '.(\PRODUCT\COLOR_ATTRIBUTE_GROUP_IS_OVERLOAD
            ?'left join product_vintage_value as color_pav on color_pav.pv_id = product_vintage.pv_id and color_pav.pag_id = '.\PRODUCT\COLOR_ATTRIBUTE_GROUP_ID
            :'left join product_value as color_pav on color_pav.p_id = product_vintage.p_id and color_pav.pag_id = '.\PRODUCT\COLOR_ATTRIBUTE_GROUP_ID).'
            left join product_image on product_image.p_id = product_vintage.p_id and product_image.pi_isprimary = 1
            left join (select product_ml.p_id,substring_index(group_concat(distinct product_ml.p_ml_id order by product_ml.lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as p_ml_id
                from filterpvids2
                inner join product_vintage on product_vintage.pv_id = filterpvids2.pv_id
                inner join product_ml on product_ml.p_id = product_vintage.p_id
                group by product_ml.p_id
            ) as ln_glue on ln_glue.p_id = product_vintage.p_id
            left join product_ml p_ml on p_ml.p_ml_id = ln_glue.p_ml_id
            order by filterpvids.rel desc'.($order_by_sql?','.$order_by_sql:'').'
            limit '.$pagelimit.' offset '.(($page-1)*$pagelimit));
        $can_favourite = $this->XM->user->isLoggedIn();
        $can_company_favourite = $this->XM->user->isInCompany()&&$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_CHANGE_COMPANY_FAVOURITES);
        $color_conversion_array = array(
                13=>'white',//white
                14=>'pink',//pink
                12=>'red',//red
            );
        while($row = $this->XM->sqlcore->getRow($res)){
            $img = null;
            if($row['pi_id']){
                $img = BASE_URL.'/modules/Product/productimg/'.$row['pi_id'].'.'.$row['pi_ext'];
            }
            $row['color_pav_id'] = (int)$row['color_pav_id'];
            $item = array(
                    'id'=>(int)$row['pv_id'],
                    'pid'=>(int)$row['p_id'],
                    'name'=>$row['p_ml_fullname'],
                    'color'=>isset($color_conversion_array[$row['color_pav_id']])?$color_conversion_array[$row['color_pav_id']]:null,
                    'score1'=>($row['score1']!==null)?str_replace('.', ',', ((float)$row['score1'])/100):null,
                    'score2'=>($row['score2']!==null)?str_replace('.', ',', ((float)$row['score2'])/100):null,
                    'score3'=>($row['score3']!==null)?str_replace('.', ',', ((float)$row['score3'])/100):null,
                    'personal_score'=>($row['personal_score']!==null)?str_replace('.', ',', ((float)$row['personal_score'])/100):null,
                    'isnonvintage'=>$row['p_isvintage']?0:1,
                    'img'=>$img,
                    'favourite'=>$row['favourite']?1:0,
                    'nomination_winner'=>$row['pv_won_contest_nominations']?1:0,
                    'can_favourite'=>$can_favourite,
                    'company_favourite'=>$row['company_favourite']?1:0,
                    'can_company_favourite'=>$can_company_favourite,
                    'can_add_review'=>$this->XM->user->isLoggedIn()&&(!$row['pv_blank']||!$row['p_isvintage']),
                    'can_view_review_merge'=>(empty($tasting_list))?true:false,
                );
            if($row['p_isvintage']){
                if($row['pv_year']){
                    $item['year'] = (int)$row['pv_year'];
                }
            } else {
                $item['year'] = langTranslate('product', 'vintage', 'NV','NV');
            }
            if(isset($color_captions[$row['color_pav_id']])){
                $item['color_caption'] = $color_captions[$row['color_pav_id']];
            }

            if($row['p_is_approved']!=1){
                $item['awaiting_approval'] = 1;
            }
            if(isset($row['can_view_certificate'])&&$row['can_view_certificate']){
                $item['can_view_certificate'] = 1;
            }
            $item['has_score'] = ($item['score1'] || $item['score2'] || $item['score3']);
            if($only_from_contest && $only_from_contest_participant){
                $item['manual_evaluation_score'] = isset($row['manual_evaluation_score'])&&$row['manual_evaluation_score']>0?str_replace('.', ',', round($row['manual_evaluation_score'])/100):null;
                $item['automatic_evaluation_score'] = isset($row['automatic_evaluation_score'])&&$row['automatic_evaluation_score']>0?str_replace('.', ',', round($row['automatic_evaluation_score'])/100):null;
            }
            $result[] = $item;
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filterpvids');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS filterpvid2');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS only_scored_tasting_ids');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS only_participants_of_contest_evaluation_scores_manual');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS only_participants_of_contest_evaluation_scores_automatic');
        return $result;
    }

    public function favourite_vintage($id,$to_state,&$err){
        if(!$this->XM->user->isLoggedIn()){
            $err = langTranslate('user', 'err', 'You\'re not logged in',  'You\'re not logged in');
            return false;
        }
        $id = (int)$id;
        if(!$to_state){
            $this->XM->sqlcore->query('DELETE FROM product_vintage_favourite where pv_id = '.$id.' and user_id = '.$this->XM->user->getUserId());
            $this->XM->sqlcore->commit();
        } else {
            $res = $this->XM->sqlcore->query('SELECT 1 FROM product_vintage_favourite where pv_id = '.$id.' and user_id = '.$this->XM->user->getUserId().' limit 1');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if($row){//already favourited
                return true;
            }
            $this->XM->sqlcore->query('INSERT INTO product_vintage_favourite (pv_id,user_id) VALUES ('.$id.','.$this->XM->user->getUserId().')');
            $this->XM->sqlcore->commit();
        }
        return true;
    }
    public function company_favourite_product($id,$to_state,$forced,&$err){
        if(!$this->XM->user->isLoggedIn()){
            $err = langTranslate('user', 'err', 'You\'re not logged in',  'You\'re not logged in');
            return false;
        }
        if(!$this->XM->user->isInCompany()){
            $err = langTranslate('user', 'err', 'You\'re not a member of a company',  'You\'re not a member of a company');
            return false;
        }
        if(!$forced&&!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_CHANGE_COMPANY_FAVOURITES)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $id = (int)$id;
        if(!$this->check_product_exists($id)){
            $err = langTranslate('product', 'err', 'Product doesn\'t exist',  'Product doesn\'t exist');
            return false;
        }
        if(!$to_state){
            $this->XM->sqlcore->query('DELETE FROM product_company_favourite where p_id = '.$id.' and company_id = '.$this->XM->user->getCompanyId());
            $this->XM->sqlcore->commit();
        } else {
            $res = $this->XM->sqlcore->query('SELECT 1 FROM product_company_favourite where p_id = '.$id.' and company_id = '.$this->XM->user->getCompanyId().' limit 1');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if($row){//already favourited
                return true;
            }
            $this->XM->sqlcore->query('INSERT INTO product_company_favourite (p_id,company_id) VALUES ('.$id.','.$this->XM->user->getCompanyId().')');
            $this->XM->sqlcore->commit();
        }
        return true;
    }
    public function company_favourite_product_by_vintage($id,$to_state,$forced,&$err){
        $res = $this->XM->sqlcore->query('SELECT p_id from product_vintage where pv_id = '.$id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Vintage doesn\'t exist',  'Vintage doesn\'t exist');
            return false;
        }
        return $this->company_favourite_product((int)$row['p_id'],$to_state,$forced,$err);
    }




    private function __has_edit_rights_for_tasting($tasting_id){
        $tasting_id = (int)$tasting_id;
        $has_edit_rights = $this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_TASTINGS);
        if($has_edit_rights){
            return true;
        }
        $res = $this->XM->sqlcore->query('SELECT 1
            from tasting
            where t_id = '.$tasting_id.' and user_id = '.$this->XM->user->getUserId().'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        return true;
    }
    public function get_tasting_vintage_list($tasting_id, $only_can_review = false, $actions = false, $request_review = false,$evaluations = false,$show_global_expert_automatic_evaluation = false,$scores = false,$awaiting_review_count=false, $show_desc=true, $user_id=null, $tpv_id=null, $vintage_id=null, $personal_reviews=false, $order_by_index=false){
        $tasting_id = (int)$tasting_id;
        $user_id = (int)$user_id;
        if($personal_reviews){
            $user_id = $this->XM->user->getuserId();
            $tasting_id = 0;
        }
        if($user_id){
            $awaiting_review_count = false;
        }
        $tpv_id = (int)$tpv_id;

        $has_edit_rights = null;
        $tasting_status = null;
        $is_tasting_owner = null;
        $tasting_evaluation_manual = 0;
        $tasting_score_method = 0;
        if(!$personal_reviews){
            $res = $this->XM->sqlcore->query('SELECT user_id, t_status,t_evaluation_manual,t_score_method from tasting where t_id = '.$tasting_id.' limit 1');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if(!$row){
                return array();
            }
            $tasting_evaluation_manual = (int)$row['t_evaluation_manual'];
            $tasting_score_method = (int)$row['t_score_method'];

            $is_tasting_owner = $this->XM->user->isLoggedIn()&&($this->XM->user->getUserId()==(int)$row['user_id']);
            $has_edit_rights = ($is_tasting_owner||$this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_EDIT_ALL_TASTINGS));
            $tasting_status = (int)$row['t_status'];
        } else {
            $this->XM->tasting->preload();
            $tasting_status = \TASTING\TASTING_STATUS_FINISHED;
            $tasting_evaluation_manual = 0;
            $has_edit_rights = false;
            $is_tasting_owner = false;
        }



        $result = array();
        $only_can_review_inner_join = '';
        $only_can_review_select_sql = '';
        if($only_can_review){
            $only_can_review_select_sql = ',only_can_review.review_exists';
            $only_can_review_inner_join = 'inner join ( SELECT distinct tasting_product_vintage.tpv_id, if(product_vintage_review.tpv_id is not null,1,0) as review_exists
                from tasting_product_vintage
                inner join tasting_user on tasting_user.t_id = tasting_product_vintage.t_id and tasting_user.user_id = '.$this->XM->user->getUserId().' and tasting_user.tu_presence = 1
                left join product_vintage_review on product_vintage_review.tpv_id = tasting_product_vintage.tpv_id and product_vintage_review.user_id = '.$this->XM->user->getUserId().'
                where tasting_product_vintage.t_id = '.$tasting_id.' and tasting_product_vintage.tpv_review_request_status = 1 '.($tpv_id?'and tasting_product_vintage.tpv_id = '.$tpv_id:'').' '.($vintage_id?'and tasting_product_vintage.pv_id = '.$vintage_id:'').') as only_can_review on only_can_review.tpv_id = tasting_product_vintage.tpv_id';
        }
        $evaluation_results_left_join = '';
        $evaluation_select_sql = '0 as can_set_manual_evaluation,0 as can_view_manual_evaluation';
        if($tasting_evaluation_manual && $evaluations){
            $evaluation_select_sql = 'if(tasting_product_vintage.tpv_review_request_status=2 and tasting_user_evaluation_manual.tue_id is null,1,0) as can_set_manual_evaluation,if(tasting_product_vintage.tpv_review_request_status=2 and tasting_user_evaluation_manual.tue_id is not null,1,0) as can_view_manual_evaluation';
            $evaluation_results_left_join = 'left join tasting_user_evaluation as tasting_user_evaluation_manual on tasting_user_evaluation_manual.tpv_id = tasting_product_vintage.tpv_id and tasting_user_evaluation_manual.tue_type=1';
        }

        $awaiting_review_count_results_left_join = '';
        $awaiting_review_count_select_sql = 'null as awaiting_review_count';
        if($awaiting_review_count){
            $awaiting_review_count_select_sql = 'awaiting_review_count.cnt as awaiting_review_count';
            $awaiting_review_count_results_left_join = 'left join (
                    SELECT count(1) as cnt,tasting_product_vintage.tpv_id
                        from tasting_product_vintage
                        inner join tasting_user on tasting_user.t_id = tasting_product_vintage.t_id and tasting_user.tu_presence = 1
                        left join product_vintage_review on product_vintage_review.tpv_id = tasting_product_vintage.tpv_id and  product_vintage_review.user_id = tasting_user.user_id
                        where tasting_product_vintage.t_id = '.$tasting_id.' and tasting_product_vintage.tpv_review_request_status = 1 and product_vintage_review.pvr_id is null '.($tpv_id?'and tasting_product_vintage.tpv_id = '.$tpv_id:'').' '.($vintage_id?'and tasting_product_vintage.pv_id = '.$vintage_id:'').'
                        group by tasting_product_vintage.tpv_id
                ) as awaiting_review_count on awaiting_review_count.tpv_id = tasting_product_vintage.tpv_id';
        }
        $evaluation_scores_select_sql = 'null as manual_evaluation_score, null as automatic_evaluation_score';
        $evaluation_scores_left_join = '';
        $global_expert_automatic_evaluation_scores_select_sql = 'null as global_expert_automatic_evaluation_score';
        $global_expert_automatic_evaluation_scores_left_join = '';
        if($user_id){
            $evaluation_scores_select_sql = 'manual_evaluation_score.score as manual_evaluation_score,automatic_evaluation_score.score as automatic_evaluation_score';
            $evaluation_scores_left_join = '
                left join (
                        SELECT tasting_user_evaluation.tpv_id, floor(tasting_user_evaluation_user_score.tueus_score*10000/maxscores.tueus_score) as score
                            from tasting_user_evaluation
                            inner join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id
                            inner join tasting_product_vintage on tasting_product_vintage.t_id = tasting_user_evaluation.t_id and tasting_product_vintage.tpv_review_request_status = 2 '.($tpv_id?'and tasting_product_vintage.tpv_id = '.$tpv_id:'').' '.($vintage_id?'and tasting_product_vintage.pv_id = '.$vintage_id:'').'
                            inner join (
                                select max(tasting_user_evaluation_user_score.tueus_score) as tueus_score, tasting_user_evaluation.tpv_id
                                    from tasting_user_evaluation
                                    inner join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id
                                    where tasting_user_evaluation.t_id = '.$tasting_id.' and tasting_user_evaluation.tue_type = 1
                                    group by tasting_user_evaluation.tpv_id
                            ) as maxscores on maxscores.tpv_id = tasting_user_evaluation.tpv_id
                            where tasting_user_evaluation.t_id = '.$tasting_id.' and tasting_user_evaluation.tue_type = 1 and tasting_user_evaluation_user_score.user_id = '.$user_id.'
                    ) as manual_evaluation_score on manual_evaluation_score.tpv_id = tasting_product_vintage.tpv_id
                left join (
                        SELECT tasting_user_evaluation.tpv_id, floor(tasting_user_evaluation_user_score.tueus_score*10000/maxscores.tueus_score) as score
                            from tasting_user_evaluation
                            inner join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id
                            inner join tasting_product_vintage on tasting_product_vintage.t_id = tasting_user_evaluation.t_id and tasting_product_vintage.tpv_review_request_status = 2 '.($tpv_id?'and tasting_product_vintage.tpv_id = '.$tpv_id:'').' '.($vintage_id?'and tasting_product_vintage.pv_id = '.$vintage_id:'').'
                            inner join (
                                select max(tasting_user_evaluation_user_score.tueus_score) as tueus_score, tasting_user_evaluation.tpv_id
                                    from tasting_user_evaluation
                                    inner join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id
                                    where tasting_user_evaluation.t_id = '.$tasting_id.' and tasting_user_evaluation.tue_type = 2
                                    group by tasting_user_evaluation.tpv_id
                            ) as maxscores on maxscores.tpv_id = tasting_user_evaluation.tpv_id
                            where tasting_user_evaluation.t_id = '.$tasting_id.' and tasting_user_evaluation.tue_type = 2 and tasting_user_evaluation_user_score.user_id = '.$user_id.'
                    ) as automatic_evaluation_score on automatic_evaluation_score.tpv_id = tasting_product_vintage.tpv_id';
            $this->XM->tasting->preload();
            if($show_global_expert_automatic_evaluation && $tasting_status==\TASTING\TASTING_STATUS_FINISHED && $this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_EXPERT_EVALUATION_SCORE)){
                $global_expert_automatic_evaluation_scores_select_sql = 'global_expert_automatic_evaluation_score.score as global_expert_automatic_evaluation_score';
                $global_expert_automatic_evaluation_scores_left_join = 'left join (
                        SELECT tasting_user_evaluation.tpv_id, floor(tasting_user_evaluation_user_score.tueus_score*10000/maxscores.tueus_score) as score
                            from tasting_user_evaluation
                            inner join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id
                            inner join tasting_product_vintage on tasting_product_vintage.t_id = tasting_user_evaluation.t_id and tasting_product_vintage.tpv_review_request_status = 2 '.($tpv_id?'and tasting_product_vintage.tpv_id = '.$tpv_id:'').' '.($vintage_id?'and tasting_product_vintage.pv_id = '.$vintage_id:'').'
                            inner join product_vintage_review on product_vintage_review.pvr_id = tasting_user_evaluation_user_score.pvr_id
                            inner join (
                                select max(tasting_user_evaluation_user_score.tueus_score) as tueus_score, tasting_user_evaluation.tpv_id, product_vintage_review.user_expert_level
                                    from tasting_user_evaluation
                                    inner join tasting_user_evaluation_user_score on tasting_user_evaluation_user_score.tue_id = tasting_user_evaluation.tue_id
                                    inner join product_vintage_review on product_vintage_review.pvr_id = tasting_user_evaluation_user_score.pvr_id
                                    where tasting_user_evaluation.t_id = '.$tasting_id.' and tasting_user_evaluation.tue_type = 4
                                    group by tasting_user_evaluation.tpv_id, product_vintage_review.user_expert_level
                            ) as maxscores on maxscores.tpv_id = tasting_user_evaluation.tpv_id and maxscores.user_expert_level = product_vintage_review.user_expert_level
                            where tasting_user_evaluation.t_id = '.$tasting_id.' and tasting_user_evaluation.tue_type = 4 and tasting_user_evaluation_user_score.user_id = '.$user_id.'
                    ) as global_expert_automatic_evaluation_score on global_expert_automatic_evaluation_score.tpv_id = tasting_product_vintage.tpv_id';
            }
        }

        $personal_reviews_inner_join = '';
        if($personal_reviews && !$tpv_id){
            $personal_reviews_inner_join = 'inner join (
                    select distinct tasting_product_vintage.tpv_id from tasting_product_vintage inner join product_vintage_review on product_vintage_review.tpv_id = tasting_product_vintage.tpv_id and tasting_product_vintage.tpv_personal = 1
                ) as personal_reviews_inner_join on personal_reviews_inner_join.tpv_id = tasting_product_vintage.tpv_id';
        }
        $show_desc_select_sql = '\'\' as description';
        if($show_desc){
            $show_desc_select_sql = 'coalesce(tasting_product_vintage.tpv_desc,\'\') as description';
        }
        $order_by = array();
        if($only_can_review){
            $order_by[] = 'only_can_review.review_exists asc';
        }
        $order_by[] = 'tasting_product_vintage.tpv_index asc';
        $order_by_sql = 'order by '.implode(',', $order_by);
        $res = $this->XM->sqlcore->query('SELECT distinct tasting_product_vintage.tpv_id,product_vintage.pv_id, tasting_product_vintage.tpv_index, tasting_product_vintage.tpv_review_request_status, coalesce(p_ml.p_ml_fullname,product_vintage.p_id) as p_ml_fullname, product_vintage.pv_year, product.p_isvintage, product.p_is_approved, product_image.pi_id, product_image.pi_ext, coalesce(product_attribute_value_ml.pav_ml_name,product_attribute_value.pav_origin_name,\'-\') as volume, tasting_product_vintage.tpv_preparation_type,if(tasting_product_vintage.tpv_preparation_type>0,coalesce(tasting_product_vintage.tpv_tasting_mts,FLOOR(UNIX_TIMESTAMP(CURRENT_TIMESTAMP)/60))-tasting_product_vintage.tpv_preparation_mts,null) as tpv_preparation_minutes_elapsed, tasting_product_vintage.tpv_blind, tasting_product_vintage.tpv_blindname, tasting_product_vintage.tpv_primeur, tasting_product_vintage.tpv_lot, '.$show_desc_select_sql.','.$evaluation_select_sql.','.$awaiting_review_count_select_sql.','.$evaluation_scores_select_sql.','.$global_expert_automatic_evaluation_scores_select_sql.$only_can_review_select_sql.'
            from tasting_product_vintage
            '.$only_can_review_inner_join.'
            '.$personal_reviews_inner_join.'
            inner join product_vintage on product_vintage.pv_id = tasting_product_vintage.pv_id
            inner join product on product.p_id = product_vintage.p_id

            inner join product_attribute_value on product_attribute_value.pav_id = tasting_product_vintage.tpv_volume
            inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pag_id = 16

            '.$evaluation_results_left_join.'
            '.$awaiting_review_count_results_left_join.'
            '.$evaluation_scores_left_join.'
            '.$global_expert_automatic_evaluation_scores_left_join.'

            left join (
                select product_attribute_value.pav_id,substring_index(group_concat(pav_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pav_ml_id
                    from product_attribute_value_ml
                    inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_ml.pav_id
                    inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pa_show_only_origin = 0 and product_attribute.pag_id = 16
                    where pav_ml_name is not null
                    group by product_attribute_value.pav_id
            ) as pav_ln_glue on pav_ln_glue.pav_id = product_attribute_value.pav_id
            left join product_attribute_value_ml on product_attribute_value_ml.pav_ml_id = pav_ln_glue.pav_ml_id

            left join product_image on product_image.p_id = product_vintage.p_id and product_image.pi_isprimary = 1
            left join (select product_ml.p_id,substring_index(group_concat(distinct product_ml.p_ml_id order by product_ml.lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as p_ml_id
                from tasting_product_vintage
                inner join product_vintage on product_vintage.pv_id = tasting_product_vintage.pv_id
                inner join product_ml on product_ml.p_id = product_vintage.p_id
                where tasting_product_vintage.t_id = '.$tasting_id.' '.($tpv_id?'and tasting_product_vintage.tpv_id = '.$tpv_id:'').' '.($vintage_id?'and tasting_product_vintage.pv_id = '.$vintage_id:'').'
                group by product_ml.p_id
            ) as ln_glue on ln_glue.p_id = product_vintage.p_id
            left join product_ml p_ml on p_ml.p_ml_id = ln_glue.p_ml_id
            where tasting_product_vintage.t_id = '.$tasting_id.' '.($tpv_id?'and tasting_product_vintage.tpv_id = '.$tpv_id:'').' '.($vintage_id?'and tasting_product_vintage.pv_id = '.$vintage_id:'').'
            '.$order_by_sql);
        $can_view_full_info = $this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_FULL_INFO_FOR_BLIND);
        $tasting_vintage_preparation_list = $this->XM->tasting->get_tasting_vintage_preparation_list();
        $index = 1;
        while($row = $this->XM->sqlcore->getRow($res)){
            $blind = ($row['tpv_blind'] && !$can_view_full_info && $tasting_status!=\TASTING\TASTING_STATUS_FINISHED && !$is_tasting_owner);
            $item = array(
                    'tpv_id'=>(int)$row['tpv_id'],
                    'index'=>$index++,
                    'id'=>(!$blind)?(int)$row['pv_id']:null,
                    'fullname'=>$row['tpv_blind']?$row['tpv_blindname']:$row['p_ml_fullname'].(($row['pv_year']>0)?', '.(int)$row['pv_year']:(!$row['p_isvintage']?', '.langTranslate('product', 'vintage', 'NV','NV'):'')),
                    'img'=>(!$blind&&$row['pi_id'])?BASE_URL.'/modules/Product/productimg/'.$row['pi_id'].'.'.$row['pi_ext']:null,
                    'isprimeur'=>(!$blind)?(bool)$row['tpv_primeur']:null,
                    'lot'=>(!$blind)?$row['tpv_lot']:null,
                    'volume'=>$row['volume'],
                    'blind'=>(bool)$row['tpv_blind'],
                    'desc'=>$row['description'],
                    'manual_evaluation_score'=>$row['manual_evaluation_score']?str_replace('.', ',', round($row['manual_evaluation_score'])/100):null,
                    'automatic_evaluation_score'=>$row['automatic_evaluation_score']?str_replace('.', ',', round($row['automatic_evaluation_score'])/100):null,
                    'global_expert_automatic_evaluation_score'=>$row['global_expert_automatic_evaluation_score']?str_replace('.', ',', round($row['global_expert_automatic_evaluation_score'])/100):null,
                );

            if($row['p_is_approved']!=1){
                $item['awaiting_approval'] = true;
            }
            $preparation_type = (int)$row['tpv_preparation_type'];
            if($preparation_type!=0 && array_key_exists($preparation_type, $tasting_vintage_preparation_list)){
                $item['preparation_type_text'] = $tasting_vintage_preparation_list[$preparation_type];
                $item['preparation_minutes_elapsed_pretty'] = prettifyMinutes((int)$row['tpv_preparation_minutes_elapsed']);
            } else {
                $item['preparation_type_text'] = $tasting_vintage_preparation_list[0];
                $item['preparation_minutes_elapsed_pretty'] = null;
            }

            if($actions){
                $item['can_change_preparation'] = ($has_edit_rights && $row['tpv_review_request_status'] == 0 && in_array($tasting_status, array(\TASTING\TASTING_STATUS_DRAFT,\TASTING\TASTING_STATUS_PREPARATION,\TASTING\TASTING_STATUS_STARTED)));
                $item['can_edit'] =
                    $item['can_delete'] = $has_edit_rights && in_array($tasting_status, array(\TASTING\TASTING_STATUS_DRAFT,\TASTING\TASTING_STATUS_PREPARATION,\TASTING\TASTING_STATUS_STARTED)) && $row['tpv_review_request_status']==0;
            }
            if($request_review && $tasting_score_method==0){
                $item['can_request_reviews'] = ($has_edit_rights && $row['tpv_review_request_status'] == 0 && $tasting_status == \TASTING\TASTING_STATUS_STARTED);
                $item['can_stop_reviews'] = ($has_edit_rights && $row['tpv_review_request_status'] == 1 && $tasting_status == \TASTING\TASTING_STATUS_STARTED);
            }
            if($evaluations){
                $item['can_set_manual_evaluation'] = ($has_edit_rights && $row['can_set_manual_evaluation']);
                $item['can_view_manual_evaluation'] = ($has_edit_rights && $row['can_view_manual_evaluation']);
            }
            if($awaiting_review_count){
                $item['awaiting_review_count'] = (int)$row['awaiting_review_count'];
            }
            if($only_can_review && isset($row['review_exists'])){
                $item['review_exists'] = (bool)$row['review_exists'];
            }
            $result[] = $item;
        }
        $this->XM->sqlcore->freeResult($res);
        if($scores && ($has_edit_rights || $tasting_status==\TASTING\TASTING_STATUS_FINISHED)){
            if(!$personal_reviews){
                $expert_level_list = array_keys($this->XM->user->get_expert_level_list());
                $existing_expert_level_list = array();
                switch($tasting_score_method){
                    case 1:
                        $sql = 'SELECT ceil(avg(tasting_product_vintage_ranking.tpvr_rank*100)) as pvr_score,count(tasting_product_vintage_ranking.tpvr_rank) as pvr_count,tasting_product_vintage_ranking.user_expert_level,tasting_product_vintage_ranking.tpv_id
                            FROM tasting_product_vintage_ranking
                            WHERE tasting_product_vintage_ranking.t_id = '.$tasting_id.' '.($tpv_id?'and tasting_product_vintage_ranking.tpv_id = '.$tpv_id:'').'
                            GROUP BY tasting_product_vintage_ranking.tpv_id,tasting_product_vintage_ranking.user_expert_level
                            order by tasting_product_vintage_ranking.tpv_id asc, tasting_product_vintage_ranking.user_expert_level asc';
                        break;
                    case 0:
                    default:
                        $this->XM->tasting->preload();
                        $sql = 'SELECT ceil(avg(product_vintage_review.pvr_score)) as pvr_score,count(product_vintage_review.pvr_score) as pvr_count,product_vintage_review.user_expert_level,product_vintage_review.tpv_id,if(count(product_vintage_review.pvr_score)=1,min(pvr_id),null) as pvr_id,
                        if(tpv_review_request_status = 1 and max(product_vintage_review.pvr_score) - min(product_vintage_review.pvr_score) > '.(\TASTING\TASTING_SCORE_DRAW_ATTENTION_DELTA*100).',1,0) as pvr_draw_attention
                            FROM product_vintage_review
                            inner join tasting_product_vintage on tasting_product_vintage.tpv_id = product_vintage_review.tpv_id
                            WHERE product_vintage_review.t_id = '.$tasting_id.' '.($tpv_id?'and product_vintage_review.tpv_id = '.$tpv_id:'').' and product_vintage_review.pvr_block'.($has_edit_rights?'&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW):'').' = 0
                            GROUP BY product_vintage_review.tpv_id,product_vintage_review.user_expert_level
                            order by product_vintage_review.tpv_id asc, product_vintage_review.user_expert_level asc';
                }
                $res = $this->XM->sqlcore->query($sql);
                $result_key = $last_tpv_id = null;
                while($row = $this->XM->sqlcore->getRow($res)){
                    $expert_level = (int)$row['user_expert_level'];
                    if(!in_array($expert_level, $expert_level_list)){
                        continue;
                    }
                    $tpv_id = (int)$row['tpv_id'];
                    if($tpv_id !== $last_tpv_id){
                        $found = false;//excessive
                        foreach($result as $key=>$result_row){
                            if($result_row['tpv_id']==$tpv_id){
                                $result_key = $key;
                                $last_tpv_id = $tpv_id;
                                $found = true;
                                break;
                            }
                        }
                        if(!$found){//excessive
                            continue;
                        }
                    }
                    if(!isset($result[$result_key]['scores'])){
                        $result[$result_key]['scores'] = array();
                    }
                    $result[$result_key]['scores'][$expert_level] = array('score'=>str_replace('.', ',', round($row['pvr_score'])/100),'count'=>(int)$row['pvr_count']);
                    if(isset($row['pvr_id'])&&$row['pvr_id']){
                        $result[$result_key]['scores'][$expert_level]['review_id'] = (int)$row['pvr_id'];
                    }
                    if(isset($row['pvr_draw_attention'])&&$row['pvr_draw_attention']){
                        $result[$result_key]['scores'][$expert_level]['draw_attention'] = 1;
                    }
                    if(!in_array($expert_level, $existing_expert_level_list)){
                        $existing_expert_level_list[] = $expert_level;
                    }
                }
                $this->XM->sqlcore->freeResult($res);
            }


            if($user_id){
                switch($tasting_score_method){
                    case 1:
                        $sql = 'SELECT tasting_product_vintage_ranking.tpvr_rank*100 as pvr_score,tasting_product_vintage_ranking.tpv_id,null as pvr_id
                            FROM tasting_product_vintage_ranking
                            WHERE tasting_product_vintage_ranking.t_id = '.$tasting_id.' and tasting_product_vintage_ranking.user_id = '.$user_id;
                        break;
                    case 0:
                    default:
                        $sql = 'SELECT product_vintage_review.pvr_score,product_vintage_review.tpv_id,product_vintage_review.pvr_id
                            FROM product_vintage_review
                            WHERE product_vintage_review.t_id = '.$tasting_id.' and product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|($tasting_id==0?\PRODUCT\PVR_BLOCK_PERSONAL:0)|($has_edit_rights?\PRODUCT\PVR_BLOCK_ONGOING_TASTING:0)).' = 0 and product_vintage_review.user_id = '.$user_id;
                }
                $res = $this->XM->sqlcore->query($sql);
                while($row = $this->XM->sqlcore->getRow($res)){
                    $tpv_id = (int)$row['tpv_id'];
                    $found = false;//excessive
                    foreach($result as $key=>$result_row){
                        if($result_row['tpv_id']==$tpv_id){
                            $result_key = $key;
                            $found = true;
                            break;
                        }
                    }
                    if(!$found){//excessive
                        continue;
                    }
                    if(!isset($result[$result_key]['userscore'])){
                        $result[$result_key]['userscore'] = array();
                    }
                    $result[$result_key]['userscore'][] = array('score'=>str_replace('.', ',', round($row['pvr_score'])/100),'review_id'=>(int)$row['pvr_id']);
                }
                $this->XM->sqlcore->freeResult($res);
            }
            if(!$order_by_index && !empty($existing_expert_level_list)){
                rsort($existing_expert_level_list);
                if($tasting_score_method==1){
                    usort($result, function($a, $b) use ($existing_expert_level_list){
                        foreach($existing_expert_level_list as $expert_level){
                            if(isset($a['scores'][$expert_level])){
                                if(isset($b['scores'][$expert_level])){
                                    if($a['scores'][$expert_level]['score']==$b['scores'][$expert_level]['score']){
                                        continue;
                                    }
                                    if($a['scores'][$expert_level]['score']>$b['scores'][$expert_level]['score']){
                                        return 1;
                                        break;
                                    } else {
                                        return -1;
                                        break;
                                    }
                                } else {
                                    return 1;
                                    break;
                                }
                            } else {
                                if(isset($b['scores'][$expert_level])){
                                    return -1;
                                    break;
                                } else {
                                    continue;
                                }
                            }
                        }
                        return 0;
                    });
                } else {
                    usort($result, function($a, $b) use ($existing_expert_level_list){
                        foreach($existing_expert_level_list as $expert_level){
                            if(isset($a['scores'][$expert_level])){
                                if(isset($b['scores'][$expert_level])){
                                    if($a['scores'][$expert_level]['score']==$b['scores'][$expert_level]['score']){
                                        continue;
                                    }
                                    if($a['scores'][$expert_level]['score']>$b['scores'][$expert_level]['score']){
                                        return -1;
                                        break;
                                    } else {
                                        return 1;
                                        break;
                                    }
                                } else {
                                    return -1;
                                    break;
                                }
                            } else {
                                if(isset($b['scores'][$expert_level])){
                                    return 1;
                                    break;
                                } else {
                                    continue;
                                }
                            }
                        }
                        return 0;
                    });
                }
            }
            $index = 1;
            foreach($result as &$row){
                $row['index'] = $index++;
            }

        }

        return $result;
    }
    public function get_tasting_vintage_ranking_for_user($tasting_id, $user_id){
        $tasting_id = (int)$tasting_id;
        $user_id = (int)$user_id;

        $res = $this->XM->sqlcore->query('SELECT 1
            from tasting
            inner join tasting_user on tasting_user.t_id = tasting.t_id and tasting_user.user_id = '.$user_id.' and tasting_user.tu_presence = 1
            inner join tasting_product_vintage on tasting_product_vintage.t_id = tasting.t_id
            where tasting_product_vintage.t_id = '.$tasting_id.' and tasting.t_score_method = 1
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }

        $res = $this->XM->sqlcore->query('SELECT tasting_product_vintage.tpv_id,coalesce(tasting_product_vintage_ranking.tpvr_rank,10000) as tpvr_rank,tasting_product_vintage.tpv_index,tasting_product_vintage.tpv_blind,coalesce(tasting_product_vintage.tpv_blindname,tasting_product_vintage.tpv_id) as blindname,coalesce(p_ml.p_ml_fullname,product_vintage.p_id) as fullname,product_vintage.pv_year, product.p_isvintage, product_image.pi_id, product_image.pi_ext,coalesce(product_attribute_value_ml.pav_ml_name,product_attribute_value.pav_origin_name,\'-\') as volume,tasting_product_vintage.tpv_primeur,tasting_product_vintage.tpv_lot
            from tasting_product_vintage
            inner join product_vintage on product_vintage.pv_id = tasting_product_vintage.pv_id
            inner join product on product.p_id = product_vintage.p_id

            inner join product_attribute_value on product_attribute_value.pav_id = tasting_product_vintage.tpv_volume
            inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pag_id = 16

            left join (
                select product_attribute_value.pav_id,substring_index(group_concat(pav_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pav_ml_id
                    from product_attribute_value_ml
                    inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_ml.pav_id
                    inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pa_show_only_origin = 0 and product_attribute.pag_id = 16
                    where pav_ml_name is not null
                    group by product_attribute_value.pav_id
            ) as pav_ln_glue on pav_ln_glue.pav_id = product_attribute_value.pav_id
            left join product_attribute_value_ml on product_attribute_value_ml.pav_ml_id = pav_ln_glue.pav_ml_id

            left join product_image on product_image.p_id = product_vintage.p_id and product_image.pi_isprimary = 1
            left join (select product_ml.p_id,substring_index(group_concat(distinct product_ml.p_ml_id order by product_ml.lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as p_ml_id
                from tasting_product_vintage
                inner join product_vintage on product_vintage.pv_id = tasting_product_vintage.pv_id
                inner join product_ml on product_ml.p_id = product_vintage.p_id
                where tasting_product_vintage.t_id = '.$tasting_id.'
                group by product_ml.p_id
            ) as ln_glue on ln_glue.p_id = product_vintage.p_id
            left join product_ml p_ml on p_ml.p_ml_id = ln_glue.p_ml_id

            left join tasting_product_vintage_ranking on tasting_product_vintage_ranking.tpv_id = tasting_product_vintage.tpv_id and tasting_product_vintage_ranking.user_id = '.$user_id.'

            where tasting_product_vintage.t_id = '.$tasting_id.'
            order by coalesce(tasting_product_vintage_ranking.tpvr_rank,10000+tasting_product_vintage.tpv_index,100000) asc');
        $can_view_full_info = $this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_FULL_INFO_FOR_BLIND);
        $tasting_vintage_preparation_list = $this->XM->tasting->get_tasting_vintage_preparation_list();
        $index = 1;
        while($row = $this->XM->sqlcore->getRow($res)){
            $result[] = array(
                    'id'=>(int)$row['tpv_id'],
                    'index'=>$index++,
                    'fullname'=>$row['tpv_blind']?$row['blindname']:$row['fullname'].(($row['pv_year']>0)?', '.(int)$row['pv_year']:(!$row['p_isvintage']?', '.langTranslate('product', 'vintage', 'NV','NV'):'')),
                    'img'=>(!$row['tpv_blind']&&$row['pi_id'])?BASE_URL.'/modules/Product/productimg/'.$row['pi_id'].'.'.$row['pi_ext']:null,
                    'isprimeur'=>(!$row['tpv_blind'])?(bool)$row['tpv_primeur']:null,
                    'lot'=>(!$row['tpv_blind'])?$row['tpv_lot']:null,
                    'volume'=>$row['volume'],
                );
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }
    public function set_tasting_vintage_ranking_for_current_user($tasting_id, $index, &$err){
        $tasting_id = (int)$tasting_id;
        if(!is_array($index)){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }

        $res = $this->XM->sqlcore->query('SELECT 1
            from tasting
            inner join tasting_user on tasting_user.t_id = tasting.t_id and tasting_user.user_id = '.$this->XM->user->getUserId().' and tasting_user.tu_presence = 1
            where tasting.t_id = '.$tasting_id.' and tasting.t_score_method = 1
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $clearedIndex = array();
        $minvalue = 2;
        $maxvalue = 0;
        $res = $this->XM->sqlcore->query('SELECT tpv_id from tasting_product_vintage where tasting_product_vintage.t_id = '.$tasting_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $tpv_id = (int)$row['tpv_id'];
            if(!isset($index[$tpv_id])){
                $this->XM->sqlcore->freeResult($res);
                $err = langTranslate('tasting', 'err', 'Can\'t save ranking: missing vintage index',  'Can\'t save ranking: missing vintage index');
                return false;
            }
            $value = (int)$index[$tpv_id];
            if(in_array($value, $clearedIndex)){
                $this->XM->sqlcore->freeResult($res);
                $err = langTranslate('tasting', 'err', 'Can\'t save ranking: duplicate vintage index values',  'Can\'t save ranking: duplicate vintage index values');
                return false;
            }
            $minvalue = min($minvalue,$value);
            $maxvalue = max($maxvalue,$value);
            $clearedIndex[$tpv_id] = $value;
        }
        $this->XM->sqlcore->freeResult($res);
        if($minvalue!=1 || $maxvalue!=count($clearedIndex)){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        $this->XM->sqlcore->query('DELETE FROM tasting_product_vintage_ranking WHERE t_id = '.$tasting_id.' and user_id = '.$this->XM->user->getUserId());
        foreach($clearedIndex as $tpv_id=>$rank){
            $this->XM->sqlcore->query('INSERT INTO tasting_product_vintage_ranking (t_id,tpv_id,user_id,user_expert_level,tpvr_rank) values ('.$tasting_id.','.$tpv_id.','.$this->XM->user->getUserId().','.$this->XM->user->getExpertLevel().','.$rank.')');
        }
        $this->XM->sqlcore->commit();
        return true;
    }
    public function get_vintage_id_for_tasting_product_vintage($tpv_id,$ignore_blindness = false){
        if($this->XM->user->check_privilege(\USER\PRIVILEGE_TASTING_VIEW_FULL_INFO_FOR_BLIND) || $ignore_blindness){
            $sql = 'SELECT pv_id from tasting_product_vintage where tpv_id = '.$tpv_id.' limit 1';
        } else {
            $this->XM->tasting->load();
            $sql = 'SELECT tasting_product_vintage.pv_id
                from tasting_product_vintage
                inner join tasting on tasting.t_id = tasting_product_vintage.t_id
                where tasting_product_vintage.tpv_id = '.$tpv_id.' and ( tasting_product_vintage.tpv_blind = 0 or tasting.user_id = '.$this->XM->user->getUserId().' or tasting.t_status = '.\TASTING\TASTING_STATUS_FINISHED.' )
                limit 1';
        }
        $res = $this->XM->sqlcore->query($sql);
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        return (int)$row['pv_id'];
    }
    public function get_blindness_for_tasting_product_vintage($tpv_id){
        $res = $this->XM->sqlcore->query('SELECT tpv_blind from tasting_product_vintage where tpv_id = '.$tpv_id.' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return null;
        }
        return (bool)$row['tpv_blind'];
    }
    public function check_double_vintage($vintage_id, $product_id, $year, &$double_id, &$err){
        $vintage_id = (int)$vintage_id;
        $product_id = (int)$product_id;
        $year = (int)$year;
        $res = $this->XM->sqlcore->query('SELECT product_vintage.pv_id, concat(coalesce(p_ml.p_ml_fullname,product.p_id),\', \',product_vintage.pv_year) as p_ml_fullname
            from product_vintage
            inner join product on product.p_id = product_vintage.p_id
            left join (select p_id,substring_index(group_concat(p_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as p_ml_id from product_ml group by p_id) as ln_glue on ln_glue.p_id = product.p_id
            left join product_ml p_ml on p_ml.p_ml_id = ln_glue.p_ml_id
            where product.p_id = '.$product_id.' and product_vintage.pv_year = '.$year.' '.($vintage_id?'and product_vintage.pv_id <> '.$vintage_id:'').'
            limit 1');
        while($row = $this->XM->sqlcore->getRow($res)){
            $double_id = (int)$row['pv_id'];
            $double_anchors[] = '<a href="'.BASE_URL.'/vintage/'.$row['pv_id'].'">'.htmlentities($row['p_ml_fullname']).'</a>';
        }
        $this->XM->sqlcore->freeResult($res);
        if(!empty($double_anchors)){
            $err = formatReplace(langTranslate('product', 'err', 'Vintage already exists: @1',  'Vintage already exists: @1'),
                    implode(', ', $double_anchors));
            return false;
        }
        return true;
    }
    public function check_vintage_exists($vintage_id){
        $res = $this->XM->sqlcore->query('SELECT 1 from product_vintage where pv_id = '.((int)$vintage_id).' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        return true;
    }
    public function check_product_exists($product_id){
        $res = $this->XM->sqlcore->query('SELECT 1 from product where p_id = '.((int)$product_id).' limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        return true;
    }
    private function __get_product_foundation($product_id){
        $product_id = (int)$product_id;
        $res = $this->XM->sqlcore->query('SELECT distinct product_value.pav_id
            from product_value
            where product_value.p_id = '.$product_id.' and product_value.pag_id = '.\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID);
        $result = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $result[] = (int)$row['pav_id'];
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }
    public function get_vintage_review_filter($vintage_id){
        $filter_link = array(
                4=>array(//color
                        'name'=>'color-spectrum',
                        'values'=>array(
                                13=>1,//white
                                14=>2,//pink
                                12=>3,//red
                            )
                    ),
                9=>array(//type by category
                        'name'=>'wine-type',
                        'values'=>array(
                                1805=>2,//sparkling - sparkling
                                1806=>2,//shampagne - sparkling
                                1807=>3,//sherry - fortified
                                2241=>3,//portwine - fortified
                            )
                    ),
                19=>array(//type
                        'name'=>'wine-type',
                        'values'=>array(
                                2225=>1,//still
                                2226=>2,//sparkling
                                2227=>3,//fortified
                            )
                    )
            );
        $vintage_id = (int)$vintage_id;
        $res = $this->XM->sqlcore->query('SELECT distinct product_value.pav_id,product_value.pag_id
            from product_value
            inner join product_vintage on product_vintage.p_id = product_value.p_id
            where product_vintage.pv_id = '.$vintage_id.' and product_value.pag_id IN ('.implode(',', array_keys($filter_link)).')');
        $result = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $pag_id = (int)$row['pag_id'];
            $pav_id = (int)$row['pav_id'];
            if(!isset($filter_link[$pag_id]['values'][$pav_id])){
                continue;
            }
            if(!isset($result[$filter_link[$pag_id]['name']])){
                $result[$filter_link[$pag_id]['name']] = array();
            } elseif(in_array($filter_link[$pag_id]['values'][$pav_id], $result[$filter_link[$pag_id]['name']])){
                continue;
            }
            $result[$filter_link[$pag_id]['name']][] = $filter_link[$pag_id]['values'][$pav_id];
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }
    public function get_vintage_info($vintage_id){
        if(($vintage_id = (int)$vintage_id)<=0){
            return false;
        }
        $favjoin = '';
        $favselect = '0 as favourite';
        if($this->XM->user->isLoggedIn()){
            $favjoin = 'left join product_vintage_favourite on product_vintage_favourite.user_id = '.$this->XM->user->getUserId().' and product_vintage_favourite.pv_id = product_vintage.pv_id';
            $favselect = 'IF(product_vintage_favourite.pv_id is null, 0, 1) as favourite';
        }
        $favcompanyjoin = '';
        $favcompanyselect = '0 as company_favourite';
        if($this->XM->user->isInCompany()){
            $favcompanyjoin = 'left join product_company_favourite on product_company_favourite.company_id = '.$this->XM->user->getCompanyId().' and product_company_favourite.p_id = product_vintage.p_id';
            $favcompanyselect = 'IF(product_company_favourite.p_id is null, 0, 1) as company_favourite';
        }
        $res = $this->XM->sqlcore->query('SELECT product_vintage.pv_id,product.p_id, coalesce(p_ml.p_ml_fullname,product.p_id) as p_ml_fullname, product_vintage.pv_alcohol_content, product_vintage.pv_year, product_vintage_score3.pvs_score as score3, product_vintage.pv_blank, product.p_isvintage, product.p_isblend, product.p_is_approved, product.company_id, '.$favselect.', '.$favcompanyselect.', coalesce(product_vintage_ml.pv_ml_desc,\'\') as pv_ml_desc, product_vintage.pv_won_contest_nominations
            from product_vintage
            inner join product on product.p_id = product_vintage.p_id
            '.$favjoin.'
            '.$favcompanyjoin.'
            left join product_vintage_score product_vintage_score3 on product_vintage_score3.pv_id = product_vintage.pv_id and product_vintage_score3.user_expert_level = 3
            left join (select p_id,substring_index(group_concat(p_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as p_ml_id from product_ml group by p_id) as ln_glue on ln_glue.p_id = product.p_id
            left join product_ml p_ml on p_ml.p_ml_id = ln_glue.p_ml_id
            left join product_vintage_ml on product_vintage_ml.pv_id = product_vintage.pv_id and product_vintage_ml.pv_ml_is_approved = 1 and product_vintage_ml.lang_id = '.$this->XM->lang->getCurrLangId().'
            where product_vintage.pv_id = '.$vintage_id.' '.(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_APPROVE_PRODUCT)?'and ( product.p_is_approved = 1 or product.company_id = '.$this->XM->user->getCompanyId().' )':'').'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        $product_id = (int)$row['p_id'];
        $privilege_product_approve_product = $this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_APPROVE_PRODUCT);
        $result = array(
                'id'=>(int)$row['pv_id'],
                'product_id'=>$product_id,
                'name'=>$row['p_ml_fullname'],
                'year'=>($row['pv_year']!==null)?(int)$row['pv_year']:null,
                'score'=>($row['score3']!==null)?str_replace('.', ',', ((float)$row['score3'])/100):null,
                'desc'=>$row['pv_ml_desc'],
                //'vineyard_name'=>$row['pvy_name'],
                'alcohol_content'=>($row['pv_alcohol_content']!==null)?str_replace('.', ',', ((float)$row['pv_alcohol_content'])/100):null,
                'favourite'=>$row['favourite']?1:0,
                'company_favourite'=>$row['company_favourite']?1:0,
                'isblank'=>(bool)$row['pv_blank'],
                'isvintage'=>(bool)$row['p_isvintage'],
                'isblend'=>(bool)$row['p_isblend'],
                'won_contest_nominations'=>(bool)$row['pv_won_contest_nominations'],
                'images'=>$this->__get_product_images($product_id),
                'attributes'=>array(),

                'can_edit'=>$this->can_edit_product($product_id),
                'can_favourite'=>$this->XM->user->isLoggedIn(),
                'can_company_favourite'=>$this->XM->user->isInCompany()&&$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_CHANGE_COMPANY_FAVOURITES),
                'can_add_review'=>$this->XM->user->isLoggedIn()&&(!$row['pv_blank']||!$row['p_isvintage']),

                'can_delete'=>$privilege_product_approve_product || ( $row['p_is_approved']!=1 && $row['company_id']==$this->XM->user->getCompanyId() ),
                'can_compare'=>$privilege_product_approve_product,
            );
        if($row['p_is_approved']!=1){
            $result['awaiting_approval'] = true;
            if($privilege_product_approve_product){
                $result['can_approve'] = true;
            }
        }
        $result['fullname'] = $result['name'].($result['year']?', '.$result['year']:(!$result['isvintage']?', '.langTranslate('product', 'vintage', 'NV','NV'):''));

        $res = $this->XM->sqlcore->query('SELECT pav_value.pag_id, coalesce(pag_ml.pag_ml_name,\'-\') as pag_ml_name, pav_value.pav_ml_name, pav_value.part
            from (
                    select product_attribute.pag_id,group_concat(coalesce(product_attribute_value_ml.pav_ml_name,product_attribute_value.pav_origin_name,\'-\') order by product_attribute.pa_depth asc separator \', \') as pav_ml_name,product_vintage_value.part
                        from (
                            SELECT product_vintage_value.pv_id,product_vintage_value.pav_id,product_vintage_value.pag_id,product_vintage_value.pvv_part as part
                                from product_vintage_value
                                inner join product_attribute_group on product_attribute_group.pag_id = product_vintage_value.pag_id
                                where product_vintage_value.pv_id = '.$vintage_id.' and product_attribute_group.pag_overload = 1
                            union distinct
                            SELECT product_vintage.pv_id,product_value.pav_id,product_value.pag_id,product_value.pv_part as part
                                from product_vintage
                                inner join product_value on product_value.p_id = product_vintage.p_id
                                inner join product_attribute_group on product_attribute_group.pag_id = product_value.pag_id
                                where product_vintage.pv_id = '.$vintage_id.' and product_attribute_group.pag_overload = 0
                            ) product_vintage_value
                        inner join (
                            SELECT distinct product_attribute_group_dependency.pag_id
                                from product_vintage
                                inner join product_value on product_value.p_id = product_vintage.p_id and product_value.pag_id = '.\PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID.'
                                inner join product_attribute_group_dependency on product_attribute_group_dependency.pav_id = product_value.pav_id and product_attribute_group_dependency.pagd_visible = 1
                                where product_vintage.pv_id = '.$vintage_id.'
                            ) as foundationpag_ids on foundationpag_ids.pag_id = product_vintage_value.pag_id
                        inner join product_attribute_value_tree on product_attribute_value_tree.pav_id = product_vintage_value.pav_id
                        inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_tree.pav_anc_id
                        inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
                        left join (
                            select product_attribute_value.pav_id,substring_index(group_concat(pav_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pav_ml_id
                                from product_attribute_value_ml
                                inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_ml.pav_id
                                inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pa_show_only_origin = 0
                                where pav_ml_name is not null
                                group by product_attribute_value.pav_id
                        ) as ln_glue on ln_glue.pav_id = product_attribute_value.pav_id
                        left join product_attribute_value_ml on product_attribute_value_ml.pav_ml_id = ln_glue.pav_ml_id
                        group by product_vintage_value.pav_id,product_attribute.pag_id,product_vintage_value.part
                        order by 2
                ) pav_value
            inner join product_attribute_group on pav_value.pag_id = product_attribute_group.pag_id
            left join (select pag_id,substring_index(group_concat(pag_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pag_ml_id from product_attribute_group_ml where pag_ml_name is not null group by pag_id) as ln_glue on ln_glue.pag_id = pav_value.pag_id
            left join product_attribute_group_ml pag_ml on pag_ml.pag_ml_id = ln_glue.pag_ml_id
            order by product_attribute_group.pag_zindex asc,2 asc,pav_value.part desc,pav_value.pav_ml_name asc');
        $attributes = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $pag_id = (int)$row['pag_id'];
            if(!isset($attributes[$pag_id])){
                $attributes[$pag_id] = array('id'=>$pag_id,'label'=>$row['pag_ml_name'],'values'=>array());
            }
            $attributes[$pag_id]['values'][] = array('value'=>$row['pav_ml_name'],'part'=>$row['part']!==null?(int)$row['part']:null);
        }
        $this->XM->sqlcore->freeResult($res);
        foreach($attributes as $value){
            $result['attributes'][] = $value;
        }
        return $result;
    }
    public function get_vintage_info_for_all_languages($vintage_id){
        $vintage_id = (int)$vintage_id;
        if($vintage_id<=0){
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT product_vintage.pv_id, product_vintage.p_id, product_vintage.pv_year, product_vintage.pv_alcohol_content, product_vintage.pv_blank, product_vintage_ml.lang_id, product_vintage_ml.pv_ml_desc
            from product_vintage
            left join product_vintage_ml on product_vintage_ml.pv_id = product_vintage.pv_id and product_vintage_ml.pv_ml_is_approved = 1
            where product_vintage.pv_id = '.$vintage_id);
        $result = array();
        $first_iteration_flag = false;
        $product_id;
        while($row = $this->XM->sqlcore->getRow($res)){
            if(!$first_iteration_flag){
                $product_id = (int)$row['p_id'];
                $result = array(
                        'id'=>(int)$row['pv_id'],
                        'product_id'=>$product_id,
                        'year'=>(int)$row['pv_year'],
                        'alcohol_content'=>str_replace('.', ',', ((float)$row['pv_alcohol_content'])/100),
                        'is_blank'=>(bool)$row['pv_blank'],
                        'desc'=>array(),
                        'attr'=>array(),
                        'can_edit'=>$this->can_edit_product($product_id),
                    );
                $first_iteration_flag = true;
            }
            $lang_id = (int)$row['lang_id'];
            $result['desc'][$lang_id] = (string)$row['pv_ml_desc'];
        }
        $this->XM->sqlcore->freeResult($res);
        if(empty($result)){
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT pav_id
            FROM product_vintage_value
            where pv_id = '.$vintage_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $result['attr'][] = (int)$row['pav_id'];
        }
        $result['attr'] = array_unique(array_merge($result['attr'],$this->__get_product_foundation($product_id)));
        $this->XM->sqlcore->freeResult($res);

        $result['grape_variety_concentration'] = array();
        $res = $this->XM->sqlcore->query('SELECT pav_id,pvv_part from product_vintage_value where pv_id = '.$vintage_id.' and pag_id = 7');//grape variery
        while($row = $this->XM->sqlcore->getRow($res)){
            $result['grape_variety_concentration'][(int)$row['pav_id']] = ($row['pvv_part']>0)?(int)$row['pvv_part']:null;
        }
        return $result;
    }
    public function get_alt_vintage_list($product_id, $vintage_id){
        $product_id = (int)$product_id;
        if($product_id<=0){
            return false;
        }
        $vintage_id = (int)$vintage_id;
        $res = $this->XM->sqlcore->query('SELECT product_vintage.pv_id,product.p_id, coalesce(p_ml.p_ml_fullname,product.p_id) as p_ml_fullname, product_vintage.pv_year
            from product_vintage
            inner join product on product.p_id = product_vintage.p_id
            left join (select p_id,substring_index(group_concat(p_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as p_ml_id from product_ml group by p_id) as ln_glue on ln_glue.p_id = product.p_id
            left join product_ml p_ml on p_ml.p_ml_id = ln_glue.p_ml_id
            where product.p_id = '.$product_id.' and product_vintage.pv_blank = 0 '.($vintage_id?'and product_vintage.pv_id <> '.$vintage_id:'').'
            order by product_vintage.pv_year asc');
        $result = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $result[] = array(
                    'id'=>(int)$row['pv_id'],
                    'product_id'=>(int)$row['p_id'],
                    'name'=>$row['p_ml_fullname'],
                    'year'=>(int)$row['pv_year'],
                );
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }
    public function get_vintage_translations($id, &$err){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
            $err = langTranslate('user', 'err', 'You don\'t have a privilege to approve translations',  'You don\'t have a privilege to approve translations');
            return false;
        }
        $id = (int)$id;
        $result = array();
        $res = $this->XM->sqlcore->query('SELECT pv_ml_id, pv_ml_desc from product_vintage_ml where pv_id = '.$id.' and lang_id = '.$this->XM->lang->getCurrLangId().' and pv_ml_is_approved = 0');
        while($row = $this->XM->sqlcore->getRow($res)){
            $result[] = array('id'=>(int)$row['pv_ml_id'],'desc'=>$row['pv_ml_desc']);
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }

    public function approve_vintage_translation($id, $approve, &$err){
        $id = (int)$id;
        $approve = (bool)$approve;
        if($id <= 0){
            $err = langTranslate('product', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
            $err = langTranslate('user', 'err', 'You don\'t have a privilege to approve translations',  'You don\'t have a privilege to approve translations');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT pv_id, lang_id from product_vintage_ml where pv_ml_id = '.$id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('user', 'err', 'Translation doesn\'t exist',  'Translation doesn\'t exist');
            return false;
        }
        if($approve){
            $this->XM->sqlcore->query('UPDATE product_vintage_ml set pv_ml_is_approved=1 where pv_ml_id = '.$id);
            $this->XM->sqlcore->query('DELETE FROM product_vintage_ml where pv_id = '.$row['pv_id'].' and lang_id = '.$row['lang_id'].' and pv_ml_is_approved=1 and pv_ml_id <> '.$id);
        } else {
            $this->XM->sqlcore->query('DELETE FROM product_vintage_ml where pv_ml_id = '.$id);
        }
        $this->XM->sqlcore->commit();
        return true;
    }
    public function add_vintage($product_id, $year, $alcohol_content, $attributes, $grape_variety_concentration, $description, &$err){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_ADD_PRODUCT)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $product_id = (int)$product_id;
        if($product_id<=0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        if(strlen($alcohol_content)){
            $alcohol_content = (int)((float)str_replace(',', '.', $alcohol_content)*100);
        } else {
            $alcohol_content = null;
        }
        if($alcohol_content!==null && ($alcohol_content>10000 || $alcohol_content<0)){
            $err = formatReplace(langTranslate('product', 'err', 'Invalid value of @1',  'Invalid value of @1'),
                    langTranslate('product', 'vintage', 'Alcohol Content', 'Alcohol Content'));
            return false;
        }
        $year = (int)$year;
        if($year<0 || $year>date('Y')){
            $err = formatReplace(langTranslate('product', 'err', 'Invalid value of @1',  'Invalid value of @1'),
                    langTranslate('product', 'vintage', 'Year', 'Year'));
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT p_isblend FROM product WHERE p_id = '.$product_id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Product doesn\'t exist',  'Product doesn\'t exist');
            return false;
        }
        $blend = (bool)$row['p_isblend'];
        $double_id = null;
        if(!$this->check_double_vintage(null,$product_id,$year,$double_id,$err)){
            return false;
        }
        $this->XM->sqlcore->query('INSERT INTO product_vintage (p_id,pv_year,pv_alcohol_content) VALUES ('.$product_id.','.$year.','.(($alcohol_content!==null)?$alcohol_content:'null').')');
        $vintage_id = $this->XM->sqlcore->lastInsertId();

        $languageIdList = $this->XM->lang->getLanguageIdList();
        foreach($languageIdList as $lang_id){
            $lang_desc = getLangArrayVal($description,$lang_id);
            if(mb_strlen($lang_desc,'UTF-8')>60000){
                $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                        langTranslate('product', 'vintage', 'Description', 'Description'),
                        60000);
                return false;
            }
        }
        $this->XM->sqlcore->commit();

        foreach($languageIdList as $lang_id){
            $lang_desc = getLangArrayVal($description,$lang_id);
            if(!strlen($lang_desc)){
                continue;
            }
            $insertkeys = array();
            $insertvals = array();
            $insertkeys[] = 'pv_ml_desc';
            $insertvals[] = '\''.$this->XM->sqlcore->prepString($lang_desc,60000).'\'';
            $insertkeys[] = 'pv_id';
            $insertvals[] = $vintage_id;
            $insertkeys[] = 'lang_id';
            $insertvals[] = $lang_id;
            $this->XM->sqlcore->query('INSERT INTO product_vintage_ml ('.implode(',', $insertkeys).') VALUES ('.implode(',', $insertvals).')');
            if($this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
                $dummy = null;
                $pv_ml_id = $this->XM->sqlcore->lastInsertId();
                $this->approve_vintage_translation($pv_ml_id,true,$dummy);
            }
            $this->XM->sqlcore->commit();
        }

        $attributes = array_merge($attributes,$this->__get_product_foundation($product_id));
        $attributes = $this->clean_attributes($attributes,true);
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE basefiltervals (
                id BIGINT UNSIGNED NOT NULL
            )');
        foreach($attributes as $attrval_id){
            $this->XM->sqlcore->query('INSERT INTO basefiltervals (id) VALUES ('.(int)$attrval_id.')');
        }
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtervals (
                id BIGINT UNSIGNED NOT NULL,
                pag_id BIGINT UNSIGNED NOT NULL,
                pvv_part TINYINT UNSIGNED NULL
            )');
        $this->XM->sqlcore->query('INSERT INTO filtervals (id, pag_id)
            SELECT product_attribute_value.pav_id as id, product_attribute.pag_id
                FROM basefiltervals
                inner join product_attribute_value on product_attribute_value.pav_id = basefiltervals.id
                INNER JOIN product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
                inner join product_attribute_group on product_attribute_group.pag_id = product_attribute.pag_id and product_attribute_group.pag_overload = 1 '.(!$blend?'and product_attribute_group.pag_id <> 7':''));
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS basefiltervals");

        if($blend){
            $pvv_part_filled = false;
            foreach($grape_variety_concentration as $pav_id=>$pv_part){
                $pav_id = (int)$pav_id;
                $pv_part = (int)$pv_part;
                if($pv_part<=0){
                    continue;
                }
                if($pv_part>100){
                    $pv_part = 100;
                }
                $this->XM->sqlcore->query('UPDATE filtervals SET pvv_part = '.$pv_part.' WHERE id = '.$pav_id.' and pag_id = 7');
                $pvv_part_filled = true;
            }
            if($pvv_part_filled){
                $this->XM->sqlcore->query('DELETE FROM filtervals WHERE pvv_part is null and pag_id = 7');
            }
        } else {
            $this->XM->sqlcore->query('INSERT INTO filtervals (id,pag_id,pvv_part)
                SELECT pav_id,pag_id,pv_part as pvv_part from product_value where p_id = '.$product_id.' and pag_id = 7');
        }
        $res = $this->XM->sqlcore->query('SELECT distinct coalesce(pag_ml.pag_ml_name,\'-\') as pag_ml_name
            from product_attribute_group pag
            inner join (
                select distinct product_attribute_group.pag_id
                from filtervals
                inner join product_attribute_group on product_attribute_group.pag_id = filtervals.pag_id and ( product_attribute_group.pag_multiple = 0  '.($blend?'and product_attribute_group.pag_id <> 7':'').' )
                group by product_attribute_group.pag_id
                having count(1)>1
            ) pag2 on pag2.pag_id = pag.pag_id
            left join (select pag_id,substring_index(group_concat(pag_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pag_ml_id from product_attribute_group_ml where pag_ml_name is not null group by pag_id) as ln_glue on ln_glue.pag_id = pag.pag_id
            left join product_attribute_group_ml pag_ml on pag_ml.pag_ml_id = ln_glue.pag_ml_id');
        $pag_names = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $pag_names[] = $row['pag_ml_name'];
        }
        $this->XM->sqlcore->freeResult($res);
        if(!empty($pag_names)){
            $err = formatReplace(langTranslate('product', 'err', 'You can\'t select multiple values in fields: @1',  'You can\'t select multiple values in fields: @1'),
                    implode(', ', $pag_names));
            $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filtervals");
            $this->XM->sqlcore->rollback();
            return false;
        }
        //parts
        $res = $this->XM->sqlcore->query('SELECT distinct coalesce(pag_ml.pag_ml_name,\'-\') as pag_ml_name
            from product_attribute_group pag
            inner join (
                select distinct filtervals.pag_id
                from filtervals
                group by filtervals.pag_id
                having sum(filtervals.pvv_part)<>100
            ) pag2 on pag2.pag_id = pag.pag_id
            left join (select pag_id,substring_index(group_concat(pag_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pag_ml_id from product_attribute_group_ml where pag_ml_name is not null group by pag_id) as ln_glue on ln_glue.pag_id = pag.pag_id
            left join product_attribute_group_ml pag_ml on pag_ml.pag_ml_id = ln_glue.pag_ml_id');
        $pag_names = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $pag_names[] = $row['pag_ml_name'];
        }
        $this->XM->sqlcore->freeResult($res);
        if(!empty($pag_names)){
            $err = formatReplace(langTranslate('product', 'err', 'Sum of parts not equal to 100 for fields: @1',  'Sum of parts not equal to 100 for fields: @1'),
                    implode(', ', $pag_names));
            $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filtervals");
            $this->XM->sqlcore->rollback();
            return false;
        }
        //insert new values
        $this->XM->sqlcore->query('INSERT INTO product_vintage_value (pv_id,pav_id,pag_id,pvv_part)
            SELECT '.$vintage_id.' as pv_id, filtervals.id as pav_id, filtervals.pag_id, filtervals.pvv_part
                FROM filtervals');
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filtervals");
        $this->XM->sqlcore->commit();

        //company favourite
        $dummy = null;
        $this->company_favourite_product($product_id,true,true,$dummy);
        return $vintage_id;
    }
    public function edit_vintage($vintage_id, $year, $alcohol_content, $attributes, $grape_variety_concentration, $description, &$err){
        $vintage_id = (int)$vintage_id;
        if($vintage_id<=0){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }

        // if(!$this->can_edit_product($product_id)){
        //     $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
        //     return false;
        // }
        if(strlen($alcohol_content)){
            $alcohol_content = (int)((float)str_replace(',', '.', $alcohol_content)*100);
        } else {
            $alcohol_content = null;
        }
        if($alcohol_content>10000 || $alcohol_content<0){
            $err = formatReplace(langTranslate('product', 'err', 'Invalid value of @1',  'Invalid value of @1'),
                    langTranslate('product', 'vintage', 'Alcohol Content', 'Alcohol Content'));
            return false;
        }
        $year = (int)$year;
        if($year<0 || $year>date('Y')){
            $err = formatReplace(langTranslate('product', 'err', 'Invalid value of @1',  'Invalid value of @1'),
                    langTranslate('product', 'vintage', 'Year', 'Year'));
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT product_vintage.p_id,product_vintage.pv_alcohol_content,product_vintage.pv_year,product.p_isblend
            FROM product_vintage
            inner join product on product.p_id = product_vintage.p_id
            WHERE product_vintage.pv_id = '.$vintage_id.' and product_vintage.pv_blank = 0');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Vintage doesn\'t exist',  'Vintage doesn\'t exist');
            return false;
        }
        $blend = $row['p_isblend']?true:false;
        $product_id = (int)$row['p_id'];
        $update_arr = array();
        if($row['pv_alcohol_content']!=$alcohol_content){
            if($alcohol_content===null){
                if($row['pv_alcohol_content']!==null){
                    $updatearr[] = 'pv_alcohol_content = null';
                }
            } else {
                $update_arr[] = 'pv_alcohol_content = '.$alcohol_content;
            }
        }
        if($row['pv_year']!=$year){
            $update_arr[] = 'pv_year = '.$year;
        }
        if(!empty($update_arr)){
            $double_id = null;
            if(!$this->check_double_vintage($vintage_id,$product_id,$year,$double_id,$err)){
                return false;
            }
            $this->XM->sqlcore->query('UPDATE product_vintage SET '.implode(',', $update_arr).' WHERE pv_id = '.$vintage_id);
            $this->XM->sqlcore->commit();
        }

        $ml_variants = array();
        $res = $this->XM->sqlcore->query('SELECT pv_ml_desc, lang_id, pv_ml_id, pv_ml_is_approved from product_vintage_ml where pv_id = '.$vintage_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $lang_id = (int)$row['lang_id'];
            if(!isset($ml_variants[$lang_id])){
                $ml_variants[$lang_id] = array();
            }
            $ml_variants[$lang_id][] = array('desc'=>$row['pv_ml_desc'],'id'=>$row['pv_ml_id'],'approved'=>(bool)$row['pv_ml_is_approved']);
        }
        $this->XM->sqlcore->freeResult($res);

        $languageIdList = $this->XM->lang->getLanguageIdList();
        foreach($languageIdList as $lang_id){
            $lang_desc = getLangArrayVal($description,$lang_id);
            if(isset($ml_variants[$lang_id])){
                foreach($ml_variants[$lang_id] as $ml_variant){
                    if($lang_desc==$ml_variant['desc']){
                        if(!$ml_variant['approved']&&$this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){//approve if can and have rights to do so
                            $dummy = null;
                            $this->approve_vintage_translation($ml_variant['id'],true,$dummy);
                        }
                        continue 2;//same values, no need to insert/update
                    }
                }
            }
            if(mb_strlen($lang_desc,'UTF-8')>60000){
                $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                        langTranslate('product', 'vintage', 'Description', 'Description'),
                        60000);
                return false;
            }
            $insertkeys = array();
            $insertvals = array();
            if(strlen($lang_desc)){
                $insertkeys[] = 'pv_ml_desc';
                $insertvals[] = '\''.$this->XM->sqlcore->prepString($lang_desc,60000).'\'';
            }
            $insertkeys[] = 'pv_id';
            $insertvals[] = $vintage_id;
            $insertkeys[] = 'lang_id';
            $insertvals[] = $lang_id;
            $this->XM->sqlcore->query('INSERT INTO product_vintage_ml ('.implode(',', $insertkeys).') VALUES ('.implode(',', $insertvals).')');
            if($this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
                $dummy = null;
                $pv_ml_id = $this->XM->sqlcore->lastInsertId();
                $this->approve_vintage_translation($pv_ml_id,true,$dummy);
            }
            $this->XM->sqlcore->commit();
        }
        //attributes
        $attributes = array_merge($attributes,$this->__get_product_foundation($product_id));
        $attributes = $this->clean_attributes($attributes,true);
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE basefiltervals (
                id BIGINT UNSIGNED NOT NULL
            )');
        foreach($attributes as $attrval_id){
            $this->XM->sqlcore->query('INSERT INTO basefiltervals (id) VALUES ('.(int)$attrval_id.')');
        }
        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE filtervals (
                id BIGINT UNSIGNED NOT NULL,
                pag_id BIGINT UNSIGNED NOT NULL,
                pvv_part TINYINT UNSIGNED NULL
            )');
        $this->XM->sqlcore->query('INSERT INTO filtervals (id, pag_id)
            SELECT product_attribute_value.pav_id as id, product_attribute.pag_id
                FROM basefiltervals
                inner join product_attribute_value on product_attribute_value.pav_id = basefiltervals.id
                INNER JOIN product_attribute on product_attribute.pa_id = product_attribute_value.pa_id
                inner join product_attribute_group on product_attribute_group.pag_id = product_attribute.pag_id and product_attribute_group.pag_overload = 1 '.(!$blend?'and product_attribute_group.pag_id <> 7':''));
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS basefiltervals");

        if($blend){
            $pvv_part_filled = false;
            foreach($grape_variety_concentration as $pav_id=>$pv_part){
                $pav_id = (int)$pav_id;
                $pv_part = (int)$pv_part;
                if($pv_part<=0){
                    continue;
                }
                if($pv_part>100){
                    $pv_part = 100;
                }
                $this->XM->sqlcore->query('UPDATE filtervals SET pvv_part = '.$pv_part.' WHERE id = '.$pav_id.' and pag_id = 7');
                $pvv_part_filled = true;
            }
            if($pvv_part_filled){
                $this->XM->sqlcore->query('DELETE FROM filtervals WHERE pvv_part is null and pag_id = 7');
            }
        } else {
            $this->XM->sqlcore->query('INSERT INTO filtervals (id,pag_id,pvv_part)
                SELECT pav_id,pag_id,pv_part as pvv_part from product_value where p_id = '.$product_id.' and pag_id = 7');
        }
        $res = $this->XM->sqlcore->query('SELECT distinct coalesce(pag_ml.pag_ml_name,\'-\') as pag_ml_name
            from product_attribute_group pag
            inner join (
                select distinct product_attribute_group.pag_id
                from filtervals
                inner join product_attribute_group on product_attribute_group.pag_id = filtervals.pag_id and ( product_attribute_group.pag_multiple = 0  '.($blend?'and product_attribute_group.pag_id <> 7':'').' )
                group by product_attribute_group.pag_id
                having count(1)>1
            ) pag2 on pag2.pag_id = pag.pag_id
            left join (select pag_id,substring_index(group_concat(pag_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pag_ml_id from product_attribute_group_ml where pag_ml_name is not null group by pag_id) as ln_glue on ln_glue.pag_id = pag.pag_id
            left join product_attribute_group_ml pag_ml on pag_ml.pag_ml_id = ln_glue.pag_ml_id');
        $pag_names = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $pag_names[] = $row['pag_ml_name'];
        }
        $this->XM->sqlcore->freeResult($res);
        if(!empty($pag_names)){
            $err = formatReplace(langTranslate('product', 'err', 'You can\'t select multiple values in fields: @1',  'You can\'t select multiple values in fields: @1'),
                    implode(', ', $pag_names));
            $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filtervals");
            $this->XM->sqlcore->rollback();
            return false;
        }
        //parts
        $res = $this->XM->sqlcore->query('SELECT distinct coalesce(pag_ml.pag_ml_name,\'-\') as pag_ml_name
            from product_attribute_group pag
            inner join (
                select distinct filtervals.pag_id
                from filtervals
                group by filtervals.pag_id
                having sum(filtervals.pvv_part)<>100
            ) pag2 on pag2.pag_id = pag.pag_id
            left join (select pag_id,substring_index(group_concat(pag_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pag_ml_id from product_attribute_group_ml where pag_ml_name is not null group by pag_id) as ln_glue on ln_glue.pag_id = pag.pag_id
            left join product_attribute_group_ml pag_ml on pag_ml.pag_ml_id = ln_glue.pag_ml_id');
        $pag_names = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $pag_names[] = $row['pag_ml_name'];
        }
        $this->XM->sqlcore->freeResult($res);
        if(!empty($pag_names)){
            $err = formatReplace(langTranslate('product', 'err', 'Sum of parts not equal to 100 for fields: @1',  'Sum of parts not equal to 100 for fields: @1'),
                    implode(', ', $pag_names));
            $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filtervals");
            $this->XM->sqlcore->rollback();
            return false;
        }
        //delete old values
        $this->XM->sqlcore->query('DELETE FROM product_vintage_value WHERE pv_id = '.$vintage_id.' and NOT EXISTS (SELECT 1 FROM filtervals WHERE id = product_vintage_value.pav_id and filtervals.pvv_part <=> product_vintage_value.pvv_part LIMIT 1)');
        //insert new values
        $this->XM->sqlcore->query('INSERT INTO product_vintage_value (pv_id,pav_id,pag_id,pvv_part)
            SELECT '.$vintage_id.' as pv_id, filtervals.id as pav_id, filtervals.pag_id, filtervals.pvv_part
                FROM filtervals
                left join product_vintage_value on product_vintage_value.pv_id = '.$vintage_id.' and product_vintage_value.pav_id = filtervals.id
                WHERE product_vintage_value.pav_id is null');
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS filtervals");
        $this->XM->sqlcore->commit();

        return true;
    }


    public function get_review_elements(){
        $list = array();
        include 'review_elements_list.php';
        if(!is_array($list)){
            return array();
        }
        return $list;
    }
    public function get_filtered_review_elements($filter_by_review_info){
        $list = $this->get_review_elements();
        if(!is_array($filter_by_review_info)){
            return $list;
        }
        $segment_white_list = array();
        foreach($list as $elements){
            foreach($elements as $element){
                if(!isset($element['values'])){
                    continue;
                }
                foreach($element['values'] as $value){
                    if(!isset($value['segment-base-class'])||!$value['segment-base-class']){
                        continue;//not segment-base
                    }
                    if(isset($filter_by_review_info[$element['name']])&&((is_array($filter_by_review_info[$element['name']])&&in_array($value['value'], $filter_by_review_info[$element['name']]))||$filter_by_review_info[$element['name']]==$value['value'])){
                        $segment_white_list[] = $value['segment-base-class'];
                        // continue;//this value is chosen in filter
                    }
                }
            }
        }
        foreach($list as $elements_key=>$elements){
            foreach($elements as $element_key=>$element){
                if(isset($element['segment-class'])){
                    $is_deleting = true;
                    foreach($segment_white_list as $allowed_segment){
                        if(strpos($element['segment-class'], $allowed_segment)!==false){
                            $is_deleting = false;
                            break;
                        }
                    }
                    if($is_deleting){
                        unset($list[$elements_key][$element_key]);
                        continue;
                    }
                }
                if(!isset($element['values'])){
                    continue;
                }
                foreach($element['values'] as $value_key=>$value){
                    if(isset($value['segment-class'])){
                        $is_deleting = true;
                        foreach($segment_white_list as $allowed_segment){
                            if(strpos($value['segment-class'], $allowed_segment)!==false){
                                $is_deleting = false;
                                break;
                            }
                        }
                        if($is_deleting){
                            unset($list[$elements_key][$element_key]['values'][$value_key]);
                            continue;
                        }
                    }
                }
                if(empty($element['values'])){
                    unset($list[$elements_key][$element_key]);
                }
            }
        }
        return $list;
    }
    public function add_review($tpv_id, $score, $personal_comment, $review, $subdata, $faulty, $didnottaste, &$err){
        $tpv_id = (int)$tpv_id;
        $res = $this->XM->sqlcore->query('SELECT pv_id, t_id, tpv_personal from tasting_product_vintage where tpv_id = '.$tpv_id);
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Review request not found',  'Review request not found');
            return false;
        }
        $tasting_id = (int)$row['t_id'];
        $vintage_id = (int)$row['pv_id'];
        $personal = (bool)$row['tpv_personal'];
        $old_review_id = null;
        $assessment = false;
        $assessment_approved = false;

        if(!$personal){
            $res = $this->XM->sqlcore->query('SELECT tasting_product_vintage.pv_id, tasting_product_vintage.t_id, product_vintage_review.pvr_id, tasting.t_assessment, coalesce(tasting.t_is_approved,0) as t_is_approved
                from tasting_product_vintage
                inner join product_vintage on product_vintage.pv_id = tasting_product_vintage.pv_id
                inner join tasting_user on tasting_user.t_id = tasting_product_vintage.t_id and tasting_user.user_id = '.$this->XM->user->getUserId().' and tasting_user.tu_presence = 1
                inner join tasting on tasting.t_id = tasting_product_vintage.t_id
                left join product_vintage_review on product_vintage_review.tpv_id = tasting_product_vintage.tpv_id and product_vintage_review.user_id = '.$this->XM->user->getUserId().'
                where tasting_product_vintage.tpv_id = '.$tpv_id.' and tasting_product_vintage.tpv_review_request_status = 1
                limit 1');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            if(!$row){
                $err = langTranslate('product', 'err', 'Review request not found',  'Review request not found');
                return false;
            }
            if($row['pvr_id']){
                $old_review_id = (int)$row['pvr_id'];
            }
            $assessment = (bool)$row['t_assessment'];
            $assessment_approved = $assessment && (bool)$row['t_is_approved'];
        }
        if($personal && ( $faulty || $didnottaste )){
            return true;
        }
        if($faulty || $didnottaste){
            $score = null;
        } else {
            if(!strlen($score)){
                $err = formatReplace(langTranslate('product', 'err', 'Invalid value of @1',  'Invalid value of @1'),
                        langTranslate('product', 'review', 'Score', 'Score'));
                return false;
            }
            $score = ((int)$score)*100;
            if($assessment){
                if($score>10000 || $score<7500){
                    $err = formatReplace(langTranslate('product', 'err', 'Invalid value of @1',  'Invalid value of @1'),
                            langTranslate('product', 'review', 'Score', 'Score'));
                    return false;
                }
            } else {
                if($score>10000 || $score<0){
                    $err = formatReplace(langTranslate('product', 'err', 'Invalid value of @1',  'Invalid value of @1'),
                            langTranslate('product', 'review', 'Score', 'Score'));
                    return false;
                }
            }

        }

        if(mb_strlen($personal_comment,'UTF-8')>60000){
            $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                    langTranslate('product', 'review', 'Personal Comment', 'Personal Comment'),
                    60000);
            return false;
        }
        $languageIdList = $this->XM->lang->getLanguageIdList();
        foreach($languageIdList as $lang_id){
            $lang_review = getLangArrayVal($review,$lang_id);
            if(mb_strlen($lang_review,'UTF-8')>60000){
                $err = formatReplace(langTranslate('tasting', 'err', 'Value of @1 exceeds limit of @2 characters',  'Value of @1 exceeds limit of @2 characters'),
                        langTranslate('product', 'review', 'Review', 'Review'),
                        60000);
                return false;
            }
        }

        //subdata

        $faultcheck_custom = isset($subdata['faultcheck-custom'])?(string)$subdata['faultcheck-custom']:null;

        // custom subdata
        if(isset($subdata['similarity_location'])){
            $subdata['similarity_location'] = $this->clean_attributes($subdata['similarity_location']);
        }
        if(isset($subdata['similarity-year-nv'])){
            if($subdata['similarity-year-nv']){
                $subdata['similarity-year-nv'] = 1;
                if(isset($subdata['similarity-year'])){
                    unset($subdata['similarity-year']);
                }
            } else {
                unset($subdata['similarity-year-nv']);
            }
        }
        if(isset($subdata['similarity-year'])){
            if(strlen($subdata['similarity-year'])){
                $subdata['similarity-year'] = (int)$subdata['similarity-year'];
            } else {
                unset($subdata['similarity-year']);
            }
        }
        if(isset($subdata['similarity-alcohol-content'])){
            if(strlen($subdata['similarity-alcohol-content'])){
                $subdata['similarity-alcohol-content'] = (int)((float)str_replace(',', '.', $subdata['similarity-alcohol-content'])*100);
            } else {
                unset($subdata['similarity-alcohol-content']);
            }
        }
        $similarity_alcohol_content = null;
        if(isset($subdata['similarity_grape'])){
            $subdata['similarity_grape'] = $this->clean_attributes($subdata['similarity_grape']);
        }
        if(isset($subdata['recommendation-temperature_from'])){
            if(strlen($subdata['recommendation-temperature_from'])){
                $subdata['recommendation-temperature_from'] = (int)$subdata['recommendation-temperature_from'];
                if($subdata['recommendation-temperature_from']>25 || $subdata['recommendation-temperature_from']<5){
                    $err = formatReplace(langTranslate('product', 'err', 'Invalid value of @1',  'Invalid value of @1'),
                            langTranslate('product', 'review elements', 'Flow temperature (°C)','Flow temperature (°C)'));
                    return false;
                }
            } else {
                unset($subdata['recommendation-temperature_from']);
            }
        }
        if(isset($subdata['recommendation-temperature_to'])){
            if(strlen($subdata['recommendation-temperature_to'])){
                $subdata['recommendation-temperature_to'] = (int)$subdata['recommendation-temperature_to'];
                if($subdata['recommendation-temperature_to']>25 || $subdata['recommendation-temperature_to']<5){
                    $err = formatReplace(langTranslate('product', 'err', 'Invalid value of @1',  'Invalid value of @1'),
                            langTranslate('product', 'review elements', 'Flow temperature (°C)','Flow temperature (°C)'));
                    return false;
                }
            } else {
                unset($subdata['recommendation-temperature_to']);
            }
        }
        if(isset($subdata['recommendation-decantation'])){
            if(strlen($subdata['recommendation-decantation'])){
                $subdata['recommendation-decantation'] = (int)$subdata['recommendation-decantation'];
            } else {
                unset($subdata['recommendation-decantation']);
            }
        }
        if(isset($subdata['recommendation-open-time'])){
            if(strlen($subdata['recommendation-open-time'])){
                $subdata['recommendation-open-time'] = (int)$subdata['recommendation-open-time'];
            } else {
                unset($subdata['recommendation-open-time']);
            }
        }
        if(isset($subdata['recommendation-year_from'])){
            if(strlen($subdata['recommendation-year_from'])){
                $subdata['recommendation-year_from'] = (int)$subdata['recommendation-year_from'];
            } else {
                unset($subdata['recommendation-year_from']);
            }
        }
        if(isset($subdata['recommendation-year_to'])){
            if(strlen($subdata['recommendation-year_to'])){
                $subdata['recommendation-year_to'] = (int)$subdata['recommendation-year_to'];
            } else {
                unset($subdata['recommendation-year_to']);
            }
        }
        $review_elements = $this->get_review_elements();
        foreach($review_elements as $elements){
            foreach($elements as $element){
                if(!isset($element['name'])||!isset($element['values'])){
                    continue;
                }
                if(!isset($subdata[$element['name']])){
                    continue;
                }
                if(is_array($subdata[$element['name']])){
                    $new_subdata_values = array();
                    foreach($subdata[$element['name']] as $subdata_key=>$subdata_value){
                        foreach($element['values'] as $elem_value){
                            if($elem_value['value']==$subdata_value){
                                $new_subdata_values[] = $subdata_value;
                                break;
                            }
                        }
                    }
                    if(!empty($new_subdata_values)){
                        $subdata[$element['name']] = $new_subdata_values;
                    } else {
                        unset($subdata[$element['name']]);
                    }
                } else {
                    $found = false;
                    foreach($element['values'] as $elem_value){
                        if($elem_value['value']==$subdata[$element['name']]){
                            $found = true;
                            break;
                        }
                    }
                    if(!$found){
                        unset($subdata[$element['name']]);
                    }
                }
            }
        }
        unset($review_elements);

        $review_param_list = array();
        $res = $this->XM->sqlcore->query('SELECT pvrpl_id,pvrpl_name,pvrpl_multichoice FROM product_vintage_review_param_list');
        while($row = $this->XM->sqlcore->getRow($res)){
            if(!isset($subdata[(string)$row['pvrpl_name']])){
                continue;
            }
            $id = (int)$row['pvrpl_id'];
            $subdata_element = $subdata[(string)$row['pvrpl_name']];
            if($row['pvrpl_multichoice'] && is_array($subdata_element)){
                foreach($subdata_element as $value){
                    $review_param_list[] = array(
                            'id'=>$id,
                            'value'=>(int)$value,
                        );
                }
            } else {
                $review_param_list[] = array(
                        'id'=>$id,
                        'value'=>(int)$subdata_element,
                    );
            }
        }
        $this->XM->sqlcore->freeResult($res);
        $pvr_block = \PRODUCT\PVR_BLOCK_ONGOING_TASTING;
        if($personal){
            $pvr_block = \PRODUCT\PVR_BLOCK_PERSONAL;
        } else {
            if(!$assessment_approved){
                $pvr_block |= \PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE;
            }
        }
        if($faulty||$didnottaste){
            $pvr_block |= \PRODUCT\PVR_BLOCK_FAULTY_OR_MISSED;
        }
        if(!$personal && $old_review_id){
            $this->XM->sqlcore->query('DELETE FROM product_vintage_review where pvr_id = '.$old_review_id);
        }
        $this->XM->sqlcore->query('INSERT INTO product_vintage_review (pv_id,t_id,tpv_id,user_id,user_expert_level,pvr_score,pvr_personal_comment,pvr_faulty,pvr_didnottaste,pvr_block) VALUES ('.$vintage_id.','.$tasting_id.','.$tpv_id.','.$this->XM->user->getUserId().','.$this->XM->user->getExpertLevel().','.(($score!==null)?$score:'null').',\''.$this->XM->sqlcore->prepString($personal_comment,60000).'\','.($faulty?1:0).','.($didnottaste?1:0).','.$pvr_block.')');
        $review_id = (int)$this->XM->sqlcore->lastInsertId();
        $this->XM->sqlcore->commit();


        foreach($languageIdList as $lang_id){
            $lang_review = getLangArrayVal($review,$lang_id);
            if(!strlen($lang_review)){
                continue;
            }
            $insertkeys = array();
            $insertvals = array();
            $insertkeys[] = 'pvr_ml_review';
            $insertvals[] = '\''.$this->XM->sqlcore->prepString($lang_review,60000).'\'';
            $insertkeys[] = 'pvr_id';
            $insertvals[] = $review_id;
            $insertkeys[] = 'lang_id';
            $insertvals[] = $lang_id;
            $this->XM->sqlcore->query('INSERT INTO product_vintage_review_ml ('.implode(',', $insertkeys).') VALUES ('.implode(',', $insertvals).')');
            if($this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
                $dummy = null;
                $pvr_ml_id = $this->XM->sqlcore->lastInsertId();
                $this->approve_vintage_review_translation($pvr_ml_id,true,$dummy);
            }
            $this->XM->sqlcore->commit();
        }

        //save subdata
        foreach($review_param_list as $review_param){
            $this->XM->sqlcore->query('INSERT INTO product_vintage_review_param (pvr_id,pvrpl_id,pvrp_value) VALUES ('.$review_id.','.$review_param['id'].','.$review_param['value'].')');
        }
        $this->XM->sqlcore->commit();
        if(strlen($faultcheck_custom)){
            $this->XM->sqlcore->query('INSERT INTO product_vintage_review_custom_param (pvr_id,pvrcp_custom_faultcheck) VALUES ('.$review_id.',\''.$this->XM->sqlcore->prepString($faultcheck_custom,60000).'\')');
            $this->XM->sqlcore->commit();
        }
        if($pvr_block&\PRODUCT\PVR_BLOCK_PERSONAL && ($pvr_block&~(\PRODUCT\PVR_BLOCK_PERSONAL|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE))==0){
            $this->__refresh_personal_vintage_score($vintage_id, $this->XM->user->getUserId());
        }
        return $review_id;
    }
    public function approve_vintage_review_translation($id, $approve, &$err){
        $id = (int)$id;
        $approve = (bool)$approve;
        if($id <= 0){
            $err = langTranslate('product', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_APPROVE_TRANSLATION)){
            $err = langTranslate('user', 'err', 'You don\'t have a privilege to approve translations',  'You don\'t have a privilege to approve translations');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT pvr_id, lang_id from product_vintage_review_ml where pvr_ml_id = '.$id.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('user', 'err', 'Translation doesn\'t exist',  'Translation doesn\'t exist');
            return false;
        }
        if($approve){
            $this->XM->sqlcore->query('UPDATE product_vintage_review_ml set pvr_ml_is_approved=1 where pvr_ml_id = '.$id);
        } else {
            $this->XM->sqlcore->query('DELETE FROM product_vintage_review_ml where pvr_ml_id = '.$id);
        }
        $this->XM->sqlcore->commit();
        return true;
    }
    public function get_review_info_for_edit($review_id){
        $result = array('score'=>'');
        $review_id = (int)$review_id;
        $res = $this->XM->sqlcore->query('SELECT product_vintage_review.pvr_score, if(product_vintage_review.user_id='.$this->XM->user->getUserId().',product_vintage_review.pvr_personal_comment,null) as pvr_personal_comment
            from product_vintage_review
            inner join tasting_product_vintage on tasting_product_vintage.tpv_id = product_vintage_review.tpv_id and tasting_product_vintage.tpv_review_request_status = 1
            where product_vintage_review.pvr_id = '.$review_id.' and product_vintage_review.user_id='.$this->XM->user->getUserId().' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return $result;
        }
        $result = array(
                'score'=>($row['pvr_score']!==null)?str_replace('.', ',', ((float)$row['pvr_score'])/100):null,
                'personal_comment'=>$row['pvr_personal_comment'],
                'review'=>array(),
                'params'=>array(),
            );
        $res = $this->XM->sqlcore->query('SELECT product_vintage_review_ml.lang_id, product_vintage_review_ml.pvr_ml_review
            from product_vintage_review_ml
            where product_vintage_review_ml.pvr_id = '.$review_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $result['review'][(int)$row['lang_id']] = $row['pvr_ml_review'];
        }
        $this->XM->sqlcore->freeResult($res);

        $res = $this->XM->sqlcore->query('SELECT product_vintage_review_param_list.pvrpl_name, product_vintage_review_param.pvrp_value, product_vintage_review_param_list.pvrpl_multichoice
            from product_vintage_review_param
            inner join product_vintage_review_param_list on product_vintage_review_param_list.pvrpl_id = product_vintage_review_param.pvrpl_id
            where product_vintage_review_param.pvr_id = '.$review_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            if(!$row['pvrpl_multichoice']){
                $result[$row['pvrpl_name']] = (int)$row['pvrp_value'];
                continue;
            }
            if(!isset($result[$row['pvrpl_name']])){
                $result[$row['pvrpl_name']] = array();
            }
            $result[$row['pvrpl_name']][] = (int)$row['pvrp_value'];
        }
        $this->XM->sqlcore->freeResult($res);
        //custom data
        //similarity-alcohol-content
        if(isset($result['similarity-alcohol-content'])){
            $result['similarity-alcohol-content'] = str_replace('.', ',', ((float)$result['similarity-alcohol-content'])/100);
        }
        return $result;
    }





    public function get_review_info($review_id){
        $review_id = (int)$review_id;
        $review_access_left_join = '';
        $review_access_where_sql = '';
        $review_access_on_sql = '';
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_VIEW_ALL_REVIEWS)){
            $review_access_left_join = 'left join tasting on tasting.t_id = product_vintage_review.t_id';
            $review_access_where_sql = 'and ( (product_vintage_review.user_id='.$this->XM->user->getUserId().' and product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE).' = 0) or (tasting.user_id = '.$this->XM->user->getUserId().' and product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE).' = 0) )';
            $review_access_on_sql = 'and ( product_vintage_review_ml.pvr_ml_is_approved = 1 or product_vintage_review.user_id='.$this->XM->user->getUserId().' or tasting.user_id = '.$this->XM->user->getUserId().' )';
        }

        $res = $this->XM->sqlcore->query('SELECT product_vintage_review.pvr_id, product_vintage_review.pv_id, product_vintage_review.pvr_score, if(product_vintage_review.user_id='.$this->XM->user->getUserId().',product_vintage_review.pvr_personal_comment,null) as pvr_personal_comment, product_vintage_review_ml.pvr_ml_review, product_vintage_review.user_id, user_ml.user_ml_fullname
            from product_vintage_review
            '.$review_access_left_join.'
            left join product_vintage_review_ml on product_vintage_review_ml.pvr_id = product_vintage_review.pvr_id and product_vintage_review_ml.lang_id = '.$this->XM->lang->getCurrLangId().' '.$review_access_on_sql.'

            left join (
                    select user_ml.user_id,substring_index(group_concat(user_ml.user_ml_id order by user_ml.lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as user_ml_id
                    from user_ml
                    inner join product_vintage_review on product_vintage_review.user_id = user_ml.user_id and product_vintage_review.pvr_id = '.$review_id.'
                    where user_ml.user_ml_is_approved = 1 and user_ml.user_ml_fullname is not null group by user_ml.user_id
                ) as user_ln_glue on user_ln_glue.user_id = product_vintage_review.user_id
            left join user_ml on user_ml.user_ml_id = user_ln_glue.user_ml_id

            where product_vintage_review.pvr_id = '.$review_id.' '.$review_access_where_sql.' LIMIT 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            return false;
        }
        $can_edit = false;//$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_EDIT_REVIEW);
        $result = array(
                'id'=>(int)$row['pvr_id'],
                'vintage_id'=>(int)$row['pv_id'],
                'score'=>($row['pvr_score']!==null)?str_replace('.', ',', ((float)$row['pvr_score'])/100):null,
                'personal_comment'=>$row['pvr_personal_comment'],
                'review'=>$row['pvr_ml_review'],
                'author_id'=>(int)$row['user_id'],
                'author_name'=>(string)$row['user_ml_fullname'],
                'can_edit'=>$can_edit,
            );
        $params = array();
        $res = $this->XM->sqlcore->query('SELECT product_vintage_review_param_list.pvrpl_name, product_vintage_review_param.pvrp_value, product_vintage_review_param_list.pvrpl_multichoice
            from product_vintage_review_param
            inner join product_vintage_review_param_list on product_vintage_review_param_list.pvrpl_id = product_vintage_review_param.pvrpl_id
            where product_vintage_review_param.pvr_id = '.$review_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            if(!$row['pvrpl_multichoice']){
                $params[$row['pvrpl_name']] = (int)$row['pvrp_value'];
                continue;
            }
            if(!isset($params[$row['pvrpl_name']])){
                $params[$row['pvrpl_name']] = array();
            }
            $params[$row['pvrpl_name']][] = (int)$row['pvrp_value'];
        }
        $this->XM->sqlcore->freeResult($res);
        //custom data
        //attrvals
        foreach(array('similarity_location','similarity_grape') as $attrval_param){
            if(!isset($params[$attrval_param])){
                continue;
            }
            if(!is_array($params[$attrval_param])){
                $params[$attrval_param] = array($params[$attrval_param]);
            }
            array_unique($params[$attrval_param]);
            $attrval_fullnames = array();
            foreach($params[$attrval_param] as $attrval){
                $fullname = $this->__get_attrval_fullname($attrval);
                if(!in_array($fullname, $attrval_fullnames)){
                    $attrval_fullnames[] = $fullname;
                }
            }
            $params[$attrval_param] = $attrval_fullnames;
        }
        //similarity-alcohol-content
        if(isset($params['similarity-alcohol-content'])){
            $params['similarity-alcohol-content'] = str_replace('.', ',', ((float)$params['similarity-alcohol-content'])/100);
        }
        $result['params'] = $params;
        return $result;
    }
    public function get_review_merge_info($vintage_id,$expert_levels,$tasting_id=null,$user_id=null,$tasting_product_vintage_id=null,$contest_id=null,$hide_faults=true,$include_ongoing=false,$personal=false){
        $vintage_id = (int)$vintage_id;
        $clean_expert_levels = array();
        $expert_level_list = array_keys($this->XM->user->get_expert_level_list());
        $show_all_scores = $include_ongoing || $this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_VIEW_ALL_SCORES);
        foreach($expert_levels as $expert_level){
            $expert_level = (int)$expert_level;
            if(!in_array($expert_level, $expert_level_list)){
                continue;
            }
            if(!$show_all_scores && $expert_level!=3){
                continue;
            }
            if(in_array($expert_level, $clean_expert_levels)){
                continue;
            }
            $clean_expert_levels[] = $expert_level;
        }
        unset($expert_level_list);
        if(empty($clean_expert_levels)){
            return false;
        }
        $tasting_id = (int)$tasting_id;
        $user_id = (int)$user_id;
        $tasting_product_vintage_id = (int)$tasting_product_vintage_id;
        $where_sql_arr = array();
        if($tasting_id){
            $where_sql_arr[] = 'product_vintage_review.t_id = '.$tasting_id;
        }
        if($user_id){
            $where_sql_arr[] = 'product_vintage_review.user_id = '.$user_id;
        }
        if($tasting_product_vintage_id){
            $where_sql_arr[] = 'product_vintage_review.tpv_id = '.$tasting_product_vintage_id;
        }
        $contest_merge_inner_join = '';
        if($contest_id){
            $contest_merge_inner_join = 'inner join tasting_contest_tasting on tasting_contest_tasting.t_id = product_vintage_review.t_id and tasting_contest_tasting.tc_id = '.$contest_id;
        }

        $result = array('count'=>array(),'score'=>array(),'params'=>array());
        $allowed_blocks = 0;
        if($include_ongoing){
            $allowed_blocks |= \PRODUCT\PVR_BLOCK_ONGOING_TASTING|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE;
        }
        if($personal){
            $allowed_blocks |= \PRODUCT\PVR_BLOCK_PERSONAL|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW;
        }
        if($tasting_product_vintage_id || $contest_id){
            $allowed_blocks |= \PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE;
        }

        $res = $this->XM->sqlcore->query('SELECT avg(product_vintage_review.pvr_score) as pvr_score,count(1) as pvr_count, product_vintage_review.user_expert_level
            from product_vintage_review
            '.$contest_merge_inner_join.'
            where product_vintage_review.pv_id = '.$vintage_id.' and
                product_vintage_review.user_expert_level in ('.implode(',', $clean_expert_levels).') and
                '.($allowed_blocks?'product_vintage_review.pvr_block&~'.$allowed_blocks.' = 0':'product_vintage_review.pvr_block = 0').'
                '.($personal?'and product_vintage_review.user_id = '.$this->XM->user->getUserId():'').'
                '.((!empty($where_sql_arr))?' and '.implode(' and ', $where_sql_arr):'').'
            group by product_vintage_review.user_expert_level');
        while($row = $this->XM->sqlcore->getRow($res)){
            $expert_level = (int)$row['user_expert_level'];
            $result['count'][$expert_level] = (int)$row['pvr_count'];
            $result['score'][$expert_level] = ($row['pvr_score']!==null)?str_replace('.', ',', round($row['pvr_score'])/100):null;
        }
        $this->XM->sqlcore->freeResult($res);

        $params = array();
        $res = $this->XM->sqlcore->query('SELECT product_vintage_review.user_expert_level, product_vintage_review_param_list.pvrpl_name, product_vintage_review_param.pvrp_value, count(1) as cnt
            from product_vintage_review_param
            inner join product_vintage_review_param_list on product_vintage_review_param_list.pvrpl_id = product_vintage_review_param.pvrpl_id
            inner join product_vintage_review on product_vintage_review.pvr_id = product_vintage_review_param.pvr_id
            '.$contest_merge_inner_join.'
            where product_vintage_review.pv_id = '.$vintage_id.' and
                ( product_vintage_review_param_list.pvrpl_merge_include_zeros = 1 or product_vintage_review_param.pvrp_value <> 0 ) and
                product_vintage_review.user_expert_level in ('.implode(',', $clean_expert_levels).') and
                '.($allowed_blocks?'product_vintage_review.pvr_block&~'.$allowed_blocks.' = 0':'product_vintage_review.pvr_block = 0').' and
                product_vintage_review_param_list.pvrpl_merge_type = 1 '.($hide_faults?' and product_vintage_review_param_list.pvrpl_fault = 0':'').'
                '.($personal?'and product_vintage_review.user_id = '.$this->XM->user->getUserId():'').'
                '.((!empty($where_sql_arr))?' and '.implode(' and ', $where_sql_arr):'').'
            group by product_vintage_review.user_expert_level, product_vintage_review_param_list.pvrpl_name, product_vintage_review_param.pvrp_value');
        while($row = $this->XM->sqlcore->getRow($res)){
            $name = $row['pvrpl_name'];
            if(!isset($result['params'][$name])){
                $result['params'][$name] = array();
            }
            $value = (int)$row['pvrp_value'];
            if(!isset($result['params'][$name][$value])){
                $result['params'][$name][$value] = array();
            }
            $expert_level = (int)$row['user_expert_level'];
            $result['params'][$name][$value][$expert_level] = (int)$row['cnt'];
        }
        $this->XM->sqlcore->freeResult($res);
        //custom data
        //color
        $colorcodes = array();
        $res = $this->XM->sqlcore->query('SELECT colorcodeconcat.colorcode,colorcodeconcat.user_expert_level,count(1) as cnt from (
            SELECT group_concat(product_vintage_review_param.pvrp_value order by product_vintage_review_param_list.pvrpl_color asc) as colorcode,product_vintage_review.user_expert_level
                from product_vintage_review_param
                inner join product_vintage_review_param_list on product_vintage_review_param_list.pvrpl_id = product_vintage_review_param.pvrpl_id
                inner join product_vintage_review on product_vintage_review.pvr_id = product_vintage_review_param.pvr_id
                '.$contest_merge_inner_join.'
                where product_vintage_review.pv_id = '.$vintage_id.' and
                    product_vintage_review.user_expert_level in ('.implode(',', $clean_expert_levels).') and
                    '.($allowed_blocks?'product_vintage_review.pvr_block&~'.$allowed_blocks.' = 0':'product_vintage_review.pvr_block = 0').' and
                    product_vintage_review_param_list.pvrpl_color > 0
                    '.($personal?'and product_vintage_review.user_id = '.$this->XM->user->getUserId():'').'
                    '.((!empty($where_sql_arr))?' and '.implode(' and ', $where_sql_arr):'').'
                group by product_vintage_review.pvr_id
            ) as colorcodeconcat
            group by colorcodeconcat.colorcode,colorcodeconcat.user_expert_level');

        while($row = $this->XM->sqlcore->getRow($res)){
            $code = $row['colorcode'];
            if(!isset($colorcodes[$code])){
                $colorcodes[$code] = array();
            }
            $colorcodes[$code][(int)$row['user_expert_level']] = (int)$row['cnt'];
        }
        $this->XM->sqlcore->freeResult($res);
        $result['subcolor'] = array();
        foreach($colorcodes as $code=>$expertsegment){
            $colorparts = explode(',', $code);
            if(count($colorparts)!=3){
                continue;
            }
            $result['subcolor'][] = array('color'=>(int)$colorparts[0],'subcolor'=>(int)$colorparts[1],'depth'=>(int)$colorparts[2],'counts'=>$expertsegment);
        }
        //grape variety and location
        $res = $this->XM->sqlcore->query('SELECT product_vintage_review.user_expert_level, product_vintage_review_param_list.pvrpl_name, product_attribute_value.pa_id, product_attribute_value.pav_id as pvrp_value, if(correct_pav_ids.pav_id is null,0,1) as correct_value, count(1) as cnt
            from product_vintage_review_param
            inner join product_vintage_review_param_list on product_vintage_review_param_list.pvrpl_id = product_vintage_review_param.pvrpl_id and product_vintage_review_param_list.pvrpl_exact_blind = 1
            inner join product_vintage_review on product_vintage_review.pvr_id = product_vintage_review_param.pvr_id
            '.$contest_merge_inner_join.'
            inner join product_attribute_value_tree on product_attribute_value_tree.pav_id = product_vintage_review_param.pvrp_value
            inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_tree.pav_anc_id

            inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id

            left join (
                SELECT distinct coalesce(product_attribute_value_analog.pav_id, product_attribute_value_tree.pav_anc_id) as pav_id
                    from product_vintage
                    inner join product_attribute_group on product_attribute_group.pag_id in (7,8)
                    left join product_vintage_value on product_vintage_value.pv_id = product_vintage.pv_id and product_vintage_value.pag_id = product_attribute_group.pag_id and product_attribute_group.pag_overload = 1
                    left join product_value on product_value.p_id = product_vintage.p_id and product_value.pag_id = product_attribute_group.pag_id
                    inner join product_attribute_value_tree on product_attribute_value_tree.pav_id = coalesce(product_vintage_value.pav_id,product_value.pav_id)
                    left join product_attribute_value_analog pava on pava.pav_id = product_attribute_value_tree.pav_anc_id
                    left join product_attribute_value_analog on product_attribute_value_analog.pava_group_id = pava.pava_group_id
                    where product_vintage.pv_id = '.$vintage_id.'
                ) as correct_pav_ids on correct_pav_ids.pav_id = product_attribute_value.pav_id

            where product_vintage_review.pv_id = '.$vintage_id.' and
                product_vintage_review.user_expert_level in ('.implode(',', $clean_expert_levels).') and
                '.($allowed_blocks?'product_vintage_review.pvr_block&~'.$allowed_blocks.' = 0':'product_vintage_review.pvr_block = 0').'
                '.($personal?'and product_vintage_review.user_id = '.$this->XM->user->getUserId():'').'
                '.($hide_faults?' and product_vintage_review_param_list.pvrpl_fault = 0':'').'
                '.((!empty($where_sql_arr))?' and '.implode(' and ', $where_sql_arr):'').'
            group by product_vintage_review.user_expert_level, product_vintage_review_param_list.pvrpl_name, product_attribute_value.pa_id, product_attribute_value.pav_id
            order by product_attribute.pa_depth asc');

        while($row = $this->XM->sqlcore->getRow($res)){
            if(!isset($result['hierarchy_params'])){
                $result['hierarchy_params'] = array();
            }
            $name = $row['pvrpl_name'];
            if(!isset($result['hierarchy_params'][$name])){
                $result['hierarchy_params'][$name] = array();
            }
            $pa_id = (int)$row['pa_id'];
            if(!isset($result['hierarchy_params'][$name][$pa_id])){
                $result['hierarchy_params'][$name][$pa_id] = array('label'=>'-','values'=>array());
            }
            $value = (int)$row['pvrp_value'];
            if(!isset($result['params'][$name][$pa_id][$value])){
                $result['hierarchy_params'][$name][$pa_id]['values'][$value] = array('label'=>'-','values'=>array());
                if($row['correct_value']){
                    $result['hierarchy_params'][$name][$pa_id]['values'][$value]['correct_value'] = true;
                }
            }
            $expert_level = (int)$row['user_expert_level'];
            $result['hierarchy_params'][$name][$pa_id]['values'][$value]['values'][$expert_level]  = (int)$row['cnt'];
        }
        $this->XM->sqlcore->freeResult($res);
        if(isset($result['hierarchy_params'])){
            $res = $this->XM->sqlcore->query('SELECT ln_glue.pa_id, product_attribute_ml.pa_ml_name as pa_ml_name
                from (
                    select product_attribute_ml.pa_id,substring_index(group_concat(pa_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pa_ml_id
                        from product_vintage_review_param
                            inner join product_vintage_review_param_list on product_vintage_review_param_list.pvrpl_id = product_vintage_review_param.pvrpl_id and product_vintage_review_param_list.pvrpl_exact_blind = 1
                            inner join product_vintage_review on product_vintage_review.pvr_id = product_vintage_review_param.pvr_id
                            '.$contest_merge_inner_join.'
                            inner join product_attribute_value_tree on product_attribute_value_tree.pav_id = product_vintage_review_param.pvrp_value
                            inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_tree.pav_anc_id
                            inner join product_attribute_ml on product_attribute_ml.pa_id = product_attribute_value.pa_id
                            where product_vintage_review.pv_id = '.$vintage_id.' and
                            product_vintage_review.user_expert_level in ('.implode(',', $clean_expert_levels).') and
                            '.($allowed_blocks?'product_vintage_review.pvr_block&~'.$allowed_blocks.' = 0':'product_vintage_review.pvr_block = 0').'
                            '.($personal?'and product_vintage_review.user_id = '.$this->XM->user->getUserId():'').'
                            '.($hide_faults?' and product_vintage_review_param_list.pvrpl_fault = 0':'').'
                            '.((!empty($where_sql_arr))?' and '.implode(' and ', $where_sql_arr):'').' and
                            pa_ml_name is not null
                            group by product_attribute_ml.pa_id
                    ) as ln_glue
                inner join product_attribute_ml on product_attribute_ml.pa_ml_id = ln_glue.pa_ml_id');
            while($row = $this->XM->sqlcore->getRow($res)){
                $pa_id = (int)$row['pa_id'];
                foreach($result['hierarchy_params'] as $key=>$pa_blocks){
                    $pa_ids = array_keys($pa_blocks);
                    if(in_array($pa_id, $pa_ids)){
                        $result['hierarchy_params'][$key][$pa_id]['label'] = $row['pa_ml_name'];
                        continue 2;
                    }
                }
            }
            $res = $this->XM->sqlcore->query('SELECT product_attribute_value.pa_id, product_attribute_value.pav_id, group_concat(coalesce(pavpart_ml.pav_ml_name,pavpart.pav_origin_name,\'-\') order by papart.pa_depth asc separator \' / \') as pav_ml_name
            from (
                select distinct product_attribute_value.pa_id, product_attribute_value.pav_id
                    from product_vintage_review_param
                    inner join product_vintage_review_param_list on product_vintage_review_param_list.pvrpl_id = product_vintage_review_param.pvrpl_id and product_vintage_review_param_list.pvrpl_exact_blind = 1
                    inner join product_vintage_review on product_vintage_review.pvr_id = product_vintage_review_param.pvr_id
                    '.$contest_merge_inner_join.'
                    inner join product_attribute_value_tree on product_attribute_value_tree.pav_id = product_vintage_review_param.pvrp_value
                    inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_tree.pav_anc_id
                    where product_vintage_review.pv_id = '.$vintage_id.' and
                        product_vintage_review.user_expert_level in ('.implode(',', $clean_expert_levels).') and
                        '.($allowed_blocks?'product_vintage_review.pvr_block&~'.$allowed_blocks.' = 0':'product_vintage_review.pvr_block = 0').'
                        '.($personal?'and product_vintage_review.user_id = '.$this->XM->user->getUserId():'').'
                        '.($hide_faults?' and product_vintage_review_param_list.pvrpl_fault = 0':'').'
                        '.((!empty($where_sql_arr))?' and '.implode(' and ', $where_sql_arr):'').'
            ) as product_attribute_value

            inner join product_attribute_value_tree pavt on pavt.pav_id = product_attribute_value.pav_id
            inner join product_attribute_value pavpart on pavpart.pav_id = pavt.pav_anc_id
            inner join product_attribute papart on papart.pa_id = pavpart.pa_id
            left join (
                select product_attribute_value_ml.pav_id,substring_index(group_concat(pav_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pav_ml_id
                    from product_attribute_value_ml
                    inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_ml.pav_id
                    inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pa_show_only_origin = 0
                    inner join product_attribute_value_tree on product_attribute_value_tree.pav_anc_id = product_attribute_value.pav_id
                    inner join product_vintage_review_param on product_vintage_review_param.pvrp_value = product_attribute_value_tree.pav_id
                    inner join product_vintage_review_param_list on product_vintage_review_param_list.pvrpl_id = product_vintage_review_param.pvrpl_id and product_vintage_review_param_list.pvrpl_exact_blind = 1
                    inner join product_vintage_review on product_vintage_review.pvr_id = product_vintage_review_param.pvr_id
                    '.$contest_merge_inner_join.'
                    where product_vintage_review.pv_id = '.$vintage_id.' and
                        product_vintage_review.user_expert_level in ('.implode(',', $clean_expert_levels).') and
                        '.($allowed_blocks?'product_vintage_review.pvr_block&~'.$allowed_blocks.' = 0':'product_vintage_review.pvr_block = 0').'
                        '.($personal?'and product_vintage_review.user_id = '.$this->XM->user->getUserId():'').'
                        '.($hide_faults?' and product_vintage_review_param_list.pvrpl_fault = 0':'').'
                        '.((!empty($where_sql_arr))?' and '.implode(' and ', $where_sql_arr):'').' and
                    pav_ml_name is not null and not (pav_origin_name is not null and lang_id <> '.$this->XM->lang->getCurrLangId().')
                    group by product_attribute_value_ml.pav_id) as pav_ln_glue on pav_ln_glue.pav_id = pavpart.pav_id
            left join product_attribute_value_ml pavpart_ml on pavpart_ml.pav_ml_id = pav_ln_glue.pav_ml_id
            group by product_attribute_value.pa_id, product_attribute_value.pav_id');

            while($row = $this->XM->sqlcore->getRow($res)){
                $pa_id = (int)$row['pa_id'];
                $pav_id = (int)$row['pav_id'];
                foreach($result['hierarchy_params'] as $key=>$pa_blocks){
                    $pa_ids = array_keys($pa_blocks);
                    if(!in_array($pa_id, $pa_ids)){
                        continue;
                    }
                    if(isset($result['hierarchy_params'][$key][$pa_id]['values'][$pav_id])){
                        $result['hierarchy_params'][$key][$pa_id]['values'][$pav_id]['label'] = $row['pav_ml_name'];
                    }
                    continue 2;
                }
            }
            $this->XM->sqlcore->freeResult($res);
        }

        return $result;
    }

    public function get_vintage_reviews($vintage_id, $limit = null, $onlyowned = false){
        $vintage_id = (int)$vintage_id;
        $limit = (int)$limit;
        $res = $this->XM->sqlcore->query('SELECT product_vintage_review.pvr_id, product_vintage_review.pv_id, product_vintage_review.pvr_score, if(product_vintage_review.user_id='.$this->XM->user->getUserId().',product_vintage_review.pvr_personal_comment,null) as pvr_personal_comment, product_vintage_review_ml.pvr_ml_review, product_vintage_review.user_id, user_ml.user_ml_fullname
            from product_vintage_review
            inner join product_vintage on product_vintage.pv_id = product_vintage_review.pv_id
            left join product_vintage_score product_vintage_score3 on product_vintage_score3.pv_id = product_vintage.pv_id and product_vintage_score3.user_expert_level = 3
            left join product_vintage_review_ml on product_vintage_review_ml.pvr_id = product_vintage_review.pvr_id and product_vintage_review_ml.lang_id = '.$this->XM->lang->getCurrLangId().(!$onlyowned?' and product_vintage_review_ml.pvr_ml_is_approved = 1':'').'
            left join (
                    select user_ml.user_id,substring_index(group_concat(user_ml.user_ml_id order by user_ml.lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as user_ml_id
                    from user_ml
                    inner join product_vintage_review on product_vintage_review.user_id = user_ml.user_id and product_vintage_review.pv_id = '.$vintage_id.'
                    where user_ml.user_ml_is_approved = 1 and user_ml.user_ml_fullname is not null '.($onlyowned?'and user_ml.user_id = '.$this->XM->user->getUserId():'').'
                    group by user_ml.user_id
                ) as user_ln_glue on user_ln_glue.user_id = product_vintage_review.user_id
            left join user_ml on user_ml.user_ml_id = user_ln_glue.user_ml_id
            where product_vintage_review.pv_id = '.$vintage_id.' and char_length(product_vintage_review_ml.pvr_ml_review)>30 '.($onlyowned?'and product_vintage_review.user_id = '.$this->XM->user->getUserId().' and product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_PERSONAL|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE).' = 0':'and product_vintage_review.user_expert_level = 3 and product_vintage_review.pvr_block = 0').'
            order by abs(cast(product_vintage_review.pvr_score as signed)-cast(product_vintage_score3.pvs_score as signed)) asc
            '.($limit?' LIMIT '.$limit:''));
        $can_edit = false;//$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_EDIT_REVIEW);
        $result = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $result[] = array(
                    'id'=>(int)$row['pvr_id'],
                    'vintage_id'=>(int)$row['pv_id'],
                    'score'=>($row['pvr_score']!==null)?str_replace('.', ',', ((float)$row['pvr_score'])/100):null,
                    'personal_comment'=>$row['pvr_personal_comment'],
                    'review'=>$row['pvr_ml_review'],
                    'author_id'=>(int)$row['user_id'],
                    'author_name'=>(string)$row['user_ml_fullname'],
                    'can_edit'=>$can_edit,
                );
        }
        $this->XM->sqlcore->freeResult($res);
        return $result;
    }
    public function __refresh_personal_vintage_score($vintage_id, $user_id){
        $vintage_id = (int)$vintage_id;
        $user_id = (int)$user_id;
        //personal score
        $res = $this->XM->sqlcore->query('SELECT new_scores.pvr_score, product_vintage_personal_score.pvps_score from (
                SELECT
                    ceil(coalesce(avg(if(tasting_product_vintage.tpv_primeur = 0,product_vintage_review.pvr_score,null)),avg(product_vintage_review.pvr_score))) as pvr_score
                FROM product_vintage_review
                inner join tasting_product_vintage on tasting_product_vintage.tpv_id = product_vintage_review.tpv_id
                WHERE product_vintage_review.pv_id = '.$vintage_id.' and product_vintage_review.user_id = '.$user_id.' and product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_PERSONAL|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE).' = 0
            ) as new_scores
            left join product_vintage_personal_score on product_vintage_personal_score.pv_id = '.$vintage_id.' and product_vintage_personal_score.user_id = '.$user_id.'
            where not new_scores.pvr_score <=> product_vintage_personal_score.pvps_score');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if($row){
            if($row['pvr_score']===null){
                $this->XM->sqlcore->query('DELETE FROM product_vintage_personal_score where pv_id = '.$vintage_id.' and user_id = '.$user_id);
            } else {
                if($row['pvps_score']===null){
                    $this->XM->sqlcore->query('INSERT INTO product_vintage_personal_score (user_id,pv_id,pvps_score) VALUES ('.$user_id.','.$vintage_id.','.((int)$row['pvr_score']).')');
                } else {
                    $this->XM->sqlcore->query('UPDATE product_vintage_personal_score set pvps_score = '.((int)$row['pvr_score']).' where pv_id = '.$vintage_id.' and user_id = '.$user_id);
                }
            }
            $this->XM->sqlcore->commit();
        }
        return true;
    }
    private function __refresh_all_personal_vintage_scores($vintage_id){
        $vintage_id = (int)$vintage_id;
        $user_ids = array();
        $res = $this->XM->sqlcore->query('SELECT distinct user_id from product_vintage_review WHERE product_vintage_review.pv_id = '.$vintage_id.' and product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_PERSONAL|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE).' = 0');
        while($row = $this->XM->sqlcore->getRow($res)){
            $user_ids[] = (int)$row['user_id'];
        }
        $this->XM->sqlcore->freeResult($res);
        foreach($user_ids as $user_id){
            $this->__refresh_personal_vintage_score($vintage_id, $user_id);
        }
        return true;
    }
    private function __refresh_vintage_score($vintage_id){
        $vintage_id = (int)$vintage_id;
        $res = $this->XM->sqlcore->query('SELECT new_scores.pvr_score, new_scores.expert_count, new_scores.tasting_count, new_scores.user_expert_level,if(new_scores.pvr_score <=> product_vintage_score.pvs_score and new_scores.expert_count <=> product_vintage_score.pvs_expert_count and new_scores.tasting_count <=> product_vintage_score.pvs_tasting_count,1,0) as unchanged from (
                SELECT
                    product_vintage_review.pv_id,
                    ceil(coalesce(avg(if(tasting_product_vintage.tpv_primeur = 0,product_vintage_review.pvr_score,null)),avg(product_vintage_review.pvr_score))) as pvr_score,
                    count(distinct product_vintage_review.user_id) as expert_count,
                    count(distinct product_vintage_review.t_id) as tasting_count,
                    product_vintage_review.user_expert_level
                FROM product_vintage_review
                inner join tasting_product_vintage on tasting_product_vintage.tpv_id = product_vintage_review.tpv_id
                WHERE product_vintage_review.pv_id = '.$vintage_id.' and product_vintage_review.pvr_block = 0
                GROUP BY product_vintage_review.pv_id,product_vintage_review.user_expert_level
            ) as new_scores
            inner join product_vintage on product_vintage.pv_id = new_scores.pv_id
            left join product_vintage_score on product_vintage_score.pv_id = product_vintage.pv_id and product_vintage_score.user_expert_level = new_scores.user_expert_level');
        $scores = array();
        $expert_levels = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $user_expert_level = (int)$row['user_expert_level'];
            if(!in_array($user_expert_level, array(1,2,3))){
                continue;
            }
            if($row['pvr_score']===null){
                continue;
            }
            $expert_levels[] = $user_expert_level;
            if($row['unchanged']){
                continue;
            }
            $scores[$user_expert_level] = array((int)$row['pvr_score'],(int)$row['expert_count'],(int)$row['tasting_count']);
        }
        $this->XM->sqlcore->freeResult($res);

        $expert_levels_for_deletion = array();
        $expert_levels_existing = array();
        $res = $this->XM->sqlcore->query('SELECT user_expert_level from product_vintage_score where pv_id = '.$vintage_id);
        while($row = $this->XM->sqlcore->getRow($res)){
            $user_expert_level = (int)$row['user_expert_level'];
            if(!in_array($user_expert_level, $expert_levels)){
                $expert_levels_for_deletion[] = $user_expert_level;
            } else {
                $expert_levels_existing[] = $user_expert_level;
            }
        }
        $this->XM->sqlcore->freeResult($res);
        $expert_levels_for_deletion_chunks = array_chunk($expert_levels_for_deletion, 50);
        foreach($expert_levels_for_deletion_chunks as $expert_levels_for_deletion_chunk){
            $this->XM->sqlcore->query('DELETE from product_vintage_score where pv_id = '.$vintage_id.' and user_expert_level in ('.implode(',', $expert_levels_for_deletion_chunk).')');
        }
        $this->XM->sqlcore->commit();

        if(!empty($scores)){
            foreach($scores as $user_expert_level=>$score){
                if(in_array($user_expert_level, $expert_levels_existing)){
                    $this->XM->sqlcore->query('UPDATE product_vintage_score set pvs_score = '.$score[0].',pvs_expert_count = '.$score[1].',pvs_tasting_count = '.$score[2].' where pv_id = '.$vintage_id.' and user_expert_level = '.$user_expert_level);
                } else {
                    $this->XM->sqlcore->query('INSERT INTO product_vintage_score (pv_id,user_expert_level,pvs_score,pvs_expert_count,pvs_tasting_count) values ('.$vintage_id.','.$user_expert_level.','.$score[0].','.$score[1].','.$score[2].')');
                }
            }
            $this->XM->sqlcore->commit();
        }
        return true;
    }
    public function __refresh_personal_vintage_scores_for_tasting($tasting_id){
        $tasting_id = (int)$tasting_id;
        $res = $this->XM->sqlcore->query('SELECT new_scores.pvr_score, new_scores.pv_id, new_scores.user_id, product_vintage_personal_score.pvps_score from (
                SELECT
                    ceil(coalesce(avg(if(tasting_product_vintage.tpv_primeur = 0,product_vintage_review.pvr_score,null)),avg(product_vintage_review.pvr_score))) as pvr_score,
                    product_vintage_review.pv_id,
                    product_vintage_review.user_id
                FROM product_vintage_review
                inner join tasting_product_vintage on tasting_product_vintage.tpv_id = product_vintage_review.tpv_id
                inner join (
                    select distinct product_vintage_review.pv_id
                        from product_vintage_review
                        where product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE).' = 0 and product_vintage_review.t_id = '.$tasting_id.'
                    ) as pv_ids on pv_ids.pv_id = product_vintage_review.pv_id
                WHERE product_vintage_review.pvr_block&~'.(\PRODUCT\PVR_BLOCK_PERSONAL|\PRODUCT\PVR_BLOCK_SOLITARY_REVIEW|\PRODUCT\PVR_BLOCK_SCORE_NOT_ACCURATE|\PRODUCT\PVR_BLOCK_ASSESSMENT_PRIVATE).' = 0
                GROUP BY product_vintage_review.pv_id, product_vintage_review.user_id
            ) as new_scores
            left join product_vintage_personal_score on product_vintage_personal_score.pv_id = new_scores.pv_id and product_vintage_personal_score.user_id = new_scores.user_id
            where not new_scores.pvr_score <=> product_vintage_personal_score.pvps_score');
        while($row = $this->XM->sqlcore->getRow($res)){
            $user_id = (int)$row['user_id'];
            $pv_id = (int)$row['pv_id'];
            $score = (int)$row['pvr_score'];
            if($row['pvps_score']===null){
                //insert
                $this->XM->sqlcore->query('INSERT INTO product_vintage_personal_score (user_id,pv_id,pvps_score) VALUES ('.$user_id.','.$pv_id.','.$score.')');
            } else {
                //update
                $this->XM->sqlcore->query('UPDATE product_vintage_personal_score set pvps_score = '.$score.' where pv_id = '.$pv_id.' and user_id = '.$user_id);
            }
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function __refresh_vintage_scores_for_tasting($tasting_id){
        $tasting_id = (int)$tasting_id;
        //global scores
        $res = $this->XM->sqlcore->query('SELECT new_scores.pvr_score, new_scores.expert_count, new_scores.tasting_count, new_scores.pv_id, new_scores.user_expert_level,if(new_scores.pvr_score <=> product_vintage_score.pvs_score and new_scores.expert_count <=> product_vintage_score.pvs_expert_count and new_scores.tasting_count <=> product_vintage_score.pvs_tasting_count,1,0) as unchanged, product_vintage_score.pv_id as product_vintage_score_exists from (
                SELECT
                    ceil(coalesce(avg(if(tasting_product_vintage.tpv_primeur = 0,product_vintage_review.pvr_score,null)),avg(product_vintage_review.pvr_score))) as pvr_score,
                    count(distinct product_vintage_review.user_id) as expert_count,
                    count(distinct product_vintage_review.t_id) as tasting_count,
                    product_vintage_review.pv_id,
                    product_vintage_review.user_expert_level
                FROM product_vintage_review
                inner join tasting_product_vintage on tasting_product_vintage.tpv_id = product_vintage_review.tpv_id
                inner join (
                    select distinct product_vintage_review.pv_id
                        from product_vintage_review
                        where product_vintage_review.pvr_block = 0 and product_vintage_review.t_id = '.$tasting_id.'
                    ) as pv_ids on pv_ids.pv_id = product_vintage_review.pv_id
                WHERE product_vintage_review.pvr_block = 0
                GROUP BY product_vintage_review.pv_id, product_vintage_review.user_expert_level
            ) as new_scores
            inner join product_vintage on product_vintage.pv_id = new_scores.pv_id
            left join product_vintage_score on product_vintage_score.pv_id = product_vintage.pv_id and product_vintage_score.user_expert_level = new_scores.user_expert_level
            order by new_scores.pv_id asc');
        $sql_arr = array();
        $expert_levels = array();
        $old_pv_id = null;
        while($row = $this->XM->sqlcore->getRow($res)){
            $pv_id = (int)$row['pv_id'];
            if(!isset($expert_levels[$pv_id])){
                $expert_levels[$pv_id] = array();
            }
            $user_expert_level = (int)$row['user_expert_level'];
            if(!in_array($user_expert_level, array(1,2,3))){
                continue;
            }
            if($row['pvr_score']===null){
                continue;
            }
            $expert_levels[$pv_id][] = $user_expert_level;
            if($row['unchanged']){
                continue;
            }
            if($row['product_vintage_score_exists']){
                $sql_arr[] = 'UPDATE product_vintage_score set pvs_score = '.(int)$row['pvr_score'].',pvs_expert_count = '.(int)$row['expert_count'].',pvs_tasting_count = '.(int)$row['tasting_count'].' where pv_id = '.$pv_id.' and user_expert_level = '.$user_expert_level;
            } else {
                $sql_arr[] = 'INSERT INTO product_vintage_score (pv_id,user_expert_level,pvs_score,pvs_expert_count,pvs_tasting_count) values ('.$pv_id.','.$user_expert_level.','.(int)$row['pvr_score'].','.(int)$row['expert_count'].','.(int)$row['tasting_count'].')';
            }

        }
        $this->XM->sqlcore->freeResult($res);

        $res = $this->XM->sqlcore->query('SELECT product_vintage_score.pv_id,product_vintage_score.user_expert_level
            from product_vintage_score
            inner join (
                select distinct product_vintage_review.pv_id
                    from product_vintage_review
                    where product_vintage_review.pvr_block = 0 and product_vintage_review.t_id = '.$tasting_id.'
                ) as pv_ids on pv_ids.pv_id = product_vintage_score.pv_id');
        while($row = $this->XM->sqlcore->getRow($res)){
            $pv_id = (int)$row['pv_id'];
            $user_expert_level = (int)$row['user_expert_level'];
            if(!isset($expert_levels[$pv_id]) || !in_array($user_expert_level, $expert_levels[$pv_id])){
                $sql_arr[] = 'DELETE from product_vintage_score where pv_id = '.$pv_id.' and user_expert_level = '.$user_expert_level;
            }
        }
        $this->XM->sqlcore->freeResult($res);
        foreach($sql_arr as $sql_query){
            $this->XM->sqlcore->query($sql_query);
        }
        $this->XM->sqlcore->commit();
        return true;
    }
    public function refresh_all_vintage_scores(){
        $pv_ids = array();
        $res = $this->XM->sqlcore->query('SELECT distinct pv_id from product_vintage_review where product_vintage_review.pvr_block = 0');
        while($row = $this->XM->sqlcore->getRow($res)){
            $pv_ids[] = (int)$row['pv_id'];
        }
        $this->XM->sqlcore->freeResult($res);
        foreach($pv_ids as $pv_id){
            $this->__refresh_vintage_score($pv_id);
            $this->__refresh_all_personal_vintage_scores($pv_id);
        }
        return true;
    }

    public function get_vintage_review_details_list($vintage_id, $user_id, $tasting_id, &$err){
        $vintage_id = (int)$vintage_id;
        if($vintage_id <= 0){
            $err = langTranslate('product', 'err', 'Invalid ID',  'Invalid ID');
            return false;
        }
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_VIEW_SCORE_DETAILS)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $user_id = (int)$user_id;
        $tasting_id = (int)$tasting_id;

        $where_sql_arr = array();
        $where_sql_arr[] = 'product_vintage_review.pv_id = '.$vintage_id;
        $where_sql_arr[] = 'product_vintage_review.pvr_block = 0';

        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_VIEW_ALL_SCORES)){
            $where_sql_arr[] = 'product_vintage_review.user_expert_level = 3';
        }
        if($user_id){
            $where_sql_arr[] = 'product_vintage_review.user_id = '.$user_id;
        }
        if($tasting_id){
            $where_sql_arr[] = 'product_vintage_review.t_id = '.$tasting_id;
        }

        $result = array();
        //tastings
        if($tasting_id<=0){
            $tastings = array();
            $res = $this->XM->sqlcore->query('SELECT avg(product_vintage_review.pvr_score) as pvr_score,count(product_vintage_review.pvr_score) as pvr_count, if(count(product_vintage_review.pvr_score)=1,min(pvr_id),null) as pvr_id, product_vintage_review.user_expert_level, tasting.t_id, tasting.t_start_ts
                from product_vintage_review
                inner join tasting on tasting.t_id = product_vintage_review.t_id
                where '.implode(' and ', $where_sql_arr).'
                group by product_vintage_review.user_expert_level, tasting.t_id, tasting.t_start_ts
                order by tasting.t_start_ts desc, tasting.t_id desc');
            while($row = $this->XM->sqlcore->getRow($res)){
                $t_id = (int)$row['t_id'];
                if(!isset($tastings[$t_id])){
                    $tastings[$t_id] = array(
                            'id'=>$t_id,
                            'startts'=>(int)$row['t_start_ts'],
                            'scores'=>array()
                        );
                }
                $tastings[$t_id]['scores'][(int)$row['user_expert_level']] = array('score'=>str_replace('.', ',', round($row['pvr_score'])/100),'count'=>(int)$row['pvr_count'],'review_id'=>(int)$row['pvr_id']);
            }
            $this->XM->sqlcore->freeResult($res);
            if(!empty($tastings)){
                $result['tastings'] = $tastings;
            }
        }

        //users
        if($user_id<=0){
            $users = array();
            $expert_level_list = $this->XM->user->get_expert_level_list();
            $res = $this->XM->sqlcore->query('SELECT avgscores.pvr_score,avgscores.pvr_count,avgscores.pvr_id,avgscores.user_expert_level as pvr_expert_level, user.user_id, coalesce(user_ml.user_ml_fullname,\'-\') as user_ml_fullname, user.user_expert_level
                from (
                    SELECT avg(product_vintage_review.pvr_score) as pvr_score,count(product_vintage_review.pvr_score) as pvr_count, if(count(product_vintage_review.pvr_score)=1,min(pvr_id),null) as pvr_id, product_vintage_review.user_expert_level, product_vintage_review.user_id
                    from product_vintage_review
                    where '.implode(' and ', $where_sql_arr).'
                    group by product_vintage_review.user_expert_level, product_vintage_review.user_id
                ) as avgscores
                inner join user on user.user_id = avgscores.user_id

                left join (select user_id,substring_index(group_concat(user_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as user_ml_id from user_ml where user_ml_is_approved = 1 group by user_id) as ln_glue on ln_glue.user_id = user.user_id
                left join user_ml on user_ml.user_ml_id = ln_glue.user_ml_id

                order by user_ml.user_ml_fullname asc');
            while($row = $this->XM->sqlcore->getRow($res)){
                $user_id = (int)$row['user_id'];
                if(!isset($users[$user_id])){
                    $users[$user_id] = array(
                            'id'=>$user_id,
                            'name'=>$row['user_ml_fullname'],
                            'expert_level'=>isset($expert_level_list[$row['user_expert_level']])?$expert_level_list[$row['user_expert_level']]:null,
                            'scores'=>array()
                        );
                }
                $users[$user_id]['scores'][(int)$row['pvr_expert_level']] = array('score'=>str_replace('.', ',', round($row['pvr_score'])/100),'count'=>(int)$row['pvr_count'],'review_id'=>(int)$row['pvr_id']);
            }
            $this->XM->sqlcore->freeResult($res);
            if(!empty($users)){
                $result['users'] = $users;
            }
        }
        if(empty($result)){
            $err = langTranslate('product', 'err', 'Internal Error',  'Internal Error');
            return false;
        }
        return $result;
    }
    public function block_review_for_tasting_product_user($tpv_id,$user_id,$block,&$err){
        if(!$this->XM->user->check_privilege(\USER\PRIVILEGE_PRODUCT_BLOCK_REVIEW)){
            $err = langTranslate('product', 'err', 'Access Denied',  'Access Denied');
            return false;
        }
        $tpv_id = (int)$tpv_id;
        $user_id = (int)$user_id;
        $block = (bool)$block;
        $res = $this->XM->sqlcore->query('SELECT product_vintage_review.pvr_id,if(product_vintage_review.pvr_block&'.\PRODUCT\PVR_BLOCK_BY_MODERATOR.',1,0) as pvr_blocked_by_moderator,if(product_vintage_review.pvr_block&~'.\PRODUCT\PVR_BLOCK_BY_MODERATOR.'=0,1,0) as pvr_need_score_refresh
            from product_vintage_review

            where product_vintage_review.tpv_id = '.$tpv_id.' and product_vintage_review.user_id = '.$user_id);
        $review_exists = false;
        $pvr_ids = array();
        $need_score_refresh = false;
        while($row = $this->XM->sqlcore->getRow($res)){
            $review_exists = true;
            if((bool)$row['pvr_blocked_by_moderator']==$block){
                continue;
            }
            $pvr_ids[] = (int)$row['pvr_id'];
            if($row['pvr_need_score_refresh']){
                $need_score_refresh = true;
            }
        }
        $this->XM->sqlcore->freeResult($res);
        if(!$review_exists){
            $err = langTranslate('product', 'err', 'Review doesn\'t exist',  'Review doesn\'t exist');
            return false;
        }
        if(empty($pvr_ids)){
            return true;
        }
        $pvr_id_chunks = array_chunk($pvr_ids, 50);
        foreach($pvr_id_chunks as $pvr_id_chunk){
            if($block){
                $this->XM->sqlcore->query('UPDATE product_vintage_review set pvr_block = pvr_block|'.\PRODUCT\PVR_BLOCK_BY_MODERATOR.'  where pvr_id in ('.implode(',', $pvr_id_chunk).')');
                //delete expert rating
            } else {
                $this->XM->sqlcore->query('UPDATE product_vintage_review set pvr_block = pvr_block&~'.\PRODUCT\PVR_BLOCK_BY_MODERATOR.'  where pvr_id in ('.implode(',', $pvr_id_chunk).')');
            }
        }
        $this->XM->sqlcore->commit();
        if($need_score_refresh){
            $this->__refresh_vintage_score($this->get_vintage_id_for_tasting_product_vintage($tpv_id,true));
        }
        if($block){
            $this->XM->sqlcore->query('DELETE from tasting_user_evaluation_user_score where tasting_user_evaluation_user_score.user_id = '.$user_id.' and tasting_user_evaluation_user_score.tue_id in (
                    select tue_id from tasting_user_evaluation where tasting_user_evaluation.tpv_id = '.$tpv_id.' and tue_type in (1,2)
                )');
            $this->XM->sqlcore->commit();
        }
        return true;
    }

    public function __refresh_product_fullnames(){
        $res = $this->XM->sqlcore->query('SELECT distinct p_id from product');
        $product_ids = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $product_ids[] = (int)$row['p_id'];
        }
        $this->XM->sqlcore->freeResult($res);
        foreach($product_ids as $product_id){
            $updatelist = array();
            $templates = $this->get_full_name_templates_for_product($product_id);
            $res = $this->XM->sqlcore->query('SELECT product_ml.p_ml_id, product_ml.p_ml_name, product_ml.p_ml_fullname, product_ml.lang_id, product.p_origin_name
                from product
                inner join product_ml on product_ml.p_id = product.p_id
                where product.p_id = '.$product_id);
            while($row = $this->XM->sqlcore->getRow($res)){
                $lang_id = (int)$row['lang_id'];
                $originname = (string)$row['p_origin_name'];
                $lang_name = (string)$row['p_ml_name'];
                $fullname = '';
                if(isset($templates[$lang_id])){//always
                    $fullname = str_replace('{{name}}', strlen($lang_name)?$lang_name:$originname, $templates[$lang_id]);
                }
                if($fullname!=$row['p_ml_fullname']){
                    $updatelist[] = array((int)$row['p_ml_id'],$fullname);
                }
            }
            $this->XM->sqlcore->freeResult($res);
            foreach($updatelist as $update){
                $this->XM->sqlcore->query('UPDATE product_ml SET p_ml_fullname = \''.$this->XM->sqlcore->prepString($update[1],4096).'\' where p_ml_id = '.$update[0]);
                $this->XM->sqlcore->commit();
            }
        }
        return true;
    }

    private function __search_engine_alias($text){
        return preg_replace('#(?:^|\s)[^\s]{1,2}(?:\s|$)#', ' ', $this->XM->sqlcore->asciialias($text));
    }

    public function __refresh_search_engine(){
        //product_attribute_value
        $res = $this->XM->sqlcore->query('SELECT product_attribute_value.pav_id, product_attribute_value.pav_origin_name, coalesce(product_attribute_value_se.pav_se_text,\'\') as pav_se_text, product_attribute_value_se.pav_se_type
            from product_attribute_value
            left join product_attribute_value_se on product_attribute_value_se.pav_id = product_attribute_value.pav_id and product_attribute_value_se.pav_se_type = 0');
        while($row = $this->XM->sqlcore->getRow($res)){
            $attrval_id = (int)$row['pav_id'];
            $asciialias = $this->XM->sqlcore->search_engine_alias($row['pav_origin_name']);
            if($asciialias==$row['pav_se_text']){
                continue;
            }
            if(empty($asciialias)){
                $this->XM->sqlcore->query('DELETE FROM product_attribute_value_se where pav_id = '.$attrval_id.' and pav_se_type = 0');
            } elseif($row['pav_se_type']===null){
                $this->XM->sqlcore->query('INSERT INTO product_attribute_value_se (pav_id,pav_se_type,lang_id,pav_se_text) VALUES ('.$attrval_id.',0,null,\''.$this->XM->sqlcore->prepString($asciialias,128).'\')');
            } else {
                $this->XM->sqlcore->query('UPDATE product_attribute_value_se SET pav_se_text = \''.$this->XM->sqlcore->prepString($asciialias,128).'\' where pav_id = '.$attrval_id.' and pav_se_type = 0');
            }
            $this->XM->sqlcore->commit();
        }
        $this->XM->sqlcore->freeResult($res);

        //product_attribute_value_ml
        $res = $this->XM->sqlcore->query('SELECT product_attribute_value.pav_id, language.lang_id, coalesce(product_attribute_value_ml.pav_ml_name,\'\') as pav_ml_name, coalesce(product_attribute_value_se.pav_se_text,\'\') as pav_se_text, product_attribute_value_se.pav_se_type
            from product_attribute_value
            inner join language on 1=1
            left join product_attribute_value_ml on product_attribute_value_ml.pav_id = product_attribute_value.pav_id and product_attribute_value_ml.lang_id = language.lang_id
            left join product_attribute_value_se on product_attribute_value_se.pav_id = product_attribute_value.pav_id and product_attribute_value_se.lang_id = language.lang_id and product_attribute_value_se.pav_se_type = 1');
        while($row = $this->XM->sqlcore->getRow($res)){
            $attrval_id = (int)$row['pav_id'];
            $lang_id = (int)$row['lang_id'];
            $asciialias = $this->XM->sqlcore->search_engine_alias($row['pav_ml_name']);
            if($asciialias==$row['pav_se_text']){
                continue;
            }
            if(empty($asciialias)){
                $this->XM->sqlcore->query('DELETE FROM product_attribute_value_se where pav_id = '.$attrval_id.' and pav_se_type = 1 and lang_id = '.$lang_id);
            } elseif($row['pav_se_type']===null){
                $this->XM->sqlcore->query('INSERT INTO product_attribute_value_se (pav_id,pav_se_type,lang_id,pav_se_text) VALUES ('.$attrval_id.',1,'.$lang_id.',\''.$this->XM->sqlcore->prepString($asciialias,128).'\')');
            } else {
                $this->XM->sqlcore->query('UPDATE product_attribute_value_se SET pav_se_text = \''.$this->XM->sqlcore->prepString($asciialias,128).'\' where pav_id = '.$attrval_id.' and pav_se_type = 1 and lang_id = '.$lang_id);
            }
            $this->XM->sqlcore->commit();
        }
        $this->XM->sqlcore->freeResult($res);

        //product
        $res = $this->XM->sqlcore->query('SELECT product.p_id, product.p_origin_name, coalesce(product_se.p_se_text,\'\') as p_se_text, product_se.p_se_type
            from product
            left join product_se on product_se.p_id = product.p_id and product_se.p_se_type = 0');
        while($row = $this->XM->sqlcore->getRow($res)){
            $product_id = (int)$row['p_id'];
            $asciialias = $this->XM->sqlcore->search_engine_alias($row['p_origin_name']);
            if($asciialias==$row['p_se_text']){
                continue;
            }
            if(empty($asciialias)){
                $this->XM->sqlcore->query('DELETE FROM product_se where p_id = '.$product_id.' and p_se_type = 0');
            } elseif($row['p_se_type']===null){
                $this->XM->sqlcore->query('INSERT INTO product_se (p_id,p_se_type,lang_id,p_se_text) VALUES ('.$product_id.',0,null,\''.$this->XM->sqlcore->prepString($asciialias,128).'\')');
            } else {
                $this->XM->sqlcore->query('UPDATE product_se SET p_se_text = \''.$this->XM->sqlcore->prepString($asciialias,128).'\' where p_id = '.$product_id.' and p_se_type = 0');
            }
            $this->XM->sqlcore->commit();
        }
        $this->XM->sqlcore->freeResult($res);

        //product_ml
        $res = $this->XM->sqlcore->query('SELECT product.p_id, language.lang_id, coalesce(product_ml.p_ml_name,\'\') as p_ml_name, coalesce(product_se.p_se_text,\'\') as p_se_text, product_se.p_se_type
            from product
            inner join language on 1=1
            left join product_ml on product_ml.p_id = product.p_id and product_ml.lang_id = language.lang_id
            left join product_se on product_se.p_id = product.p_id and product_se.lang_id = language.lang_id and product_se.p_se_type = 1');
        while($row = $this->XM->sqlcore->getRow($res)){
            $product_id = (int)$row['p_id'];
            $lang_id = (int)$row['lang_id'];
            $asciialias = $this->XM->sqlcore->search_engine_alias($row['p_ml_name']);
            if($asciialias==$row['p_se_text']){
                continue;
            }
            if(empty($asciialias)){
                $this->XM->sqlcore->query('DELETE FROM product_se where p_id = '.$product_id.' and p_se_type = 1 and lang_id = '.$lang_id);
            } elseif($row['p_se_type']===null){
                $this->XM->sqlcore->query('INSERT INTO product_se (p_id,p_se_type,lang_id,p_se_text) VALUES ('.$product_id.',1,'.$lang_id.',\''.$this->XM->sqlcore->prepString($asciialias,128).'\')');
            } else {
                $this->XM->sqlcore->query('UPDATE product_se SET p_se_text = \''.$this->XM->sqlcore->prepString($asciialias,128).'\' where p_id = '.$product_id.' and p_se_type = 1 and lang_id = '.$lang_id);
            }
            $this->XM->sqlcore->commit();
        }
        $this->XM->sqlcore->freeResult($res);
        return true;
    }

    public function api_get_wiscores($company_id,$pv_list){
        $api_get_wiscores_pv_ids_inner_join = '';
        if(is_array($pv_list) && !empty($pv_list)){
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE api_get_wiscores_pv_data (
                    id BIGINT UNSIGNED NOT NULL,
                    year smallint UNSIGNED NULL
                )');
            foreach($pv_list as $pv_data){
                if(!is_array($pv_data)||!isset($pv_data['id'])){
                    continue;
                }
                if(isset($pv_data['year']) && $pv_data['year'] > 0){

                } else {

                }
                $this->XM->sqlcore->query('INSERT INTO api_get_wiscores_pv_data (id,year) values ('.(int)$pv_data['id'].','.(isset($pv_data['year'])&&$pv_data['year']>0?(int)$pv_data['year']:'null').')');
            }
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE api_get_wiscores_pv_ids
                SELECT distinct product_vintage.pv_id
                    from product_vintage
                    inner join product on product.p_id = product_vintage.p_id
                    inner join api_get_wiscores_pv_data on api_get_wiscores_pv_data.id = product.p_id
                    where product.p_isvintage = 0 and product_vintage.pv_blank = 1 and api_get_wiscores_pv_data.year is null or product.p_isvintage = 1 and product_vintage.pv_year = api_get_wiscores_pv_data.year');
            $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS api_get_wiscores_pv_data");
            $api_get_wiscores_pv_ids_inner_join = 'inner join api_get_wiscores_pv_ids on api_get_wiscores_pv_ids.pv_id = product_vintage.pv_id';

        }
        $company_id = (int)$company_id;
        $res = $this->XM->sqlcore->query('SELECT product.p_id,product_vintage.pv_year,product.p_isvintage,product_vintage_score.pvs_score,product_vintage_score.pvs_expert_count,product_vintage_score.pvs_tasting_count
            from product_vintage
            '.($company_id?'inner join (
                    select distinct pv_id from product_vintage_company_price where company_id = '.$company_id.'
                ) pv_ids on pv_ids.pv_id = product_vintage.pv_id':'').'
            '.$api_get_wiscores_pv_ids_inner_join.'
            inner join product on product.p_id = product_vintage.p_id
            inner join product_vintage_score on product_vintage_score.pv_id = product_vintage.pv_id and product_vintage_score.user_expert_level = 3');
        $result = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $result[] = array(
                    'id'=>(int)$row['p_id'],
                    'year'=>($row['pv_year']>0)?(int)$row['pv_year']:(!$row['p_isvintage']?langTranslate('product', 'vintage', 'NV','NV'):''),
                    'score'=>str_replace('.', ',', ((float)$row['pvs_score'])/100),
                    'expert_count'=>(int)$row['pvs_expert_count'],
                    'tasting_count'=>(int)$row['pvs_tasting_count'],
                );
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->query("DROP TEMPORARY TABLE IF EXISTS api_get_wiscores_pv_ids");
        return $result;
    }
    public function clear_product_company_price($company_id,$timestamp_limiter){
        $company_id = (int)$company_id;
        $timestamp_limiter = (int)$timestamp_limiter;
        if(!$timestamp_limiter){
            return true;
        }
        $this->XM->sqlcore->query('DELETE from product_vintage_company_price where company_id = '.$company_id.' and pvcp_timestamp < '.$timestamp_limiter);
        $this->XM->sqlcore->commit();
        return true;
    }
    public function get_product_company_price_timestamp_limiter($company_id){
        $company_id = (int)$company_id;
        $res = $this->XM->sqlcore->query('SELECT least(max(pvcp_timestamp),UNIX_TIMESTAMP(now())-60) as timestamp_limiter from product_vintage_company_price where company_id = '.$company_id);
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        return (int)$row['timestamp_limiter'];
    }
    public function set_product_company_price($company_id,$p_id,$year,$volume,$gift_wrap,$price,$url,&$volume_white_list,&$timestamp_limiter,&$err){
        $company_id = (int)$company_id;
        $p_id = (int)$p_id;
        $year = (int)$year;
        $volume = (int)$volume;
        if(!in_array($volume, $volume_white_list)){
            $err = formatReplace(langTranslate('product', 'err', 'Invalid value of @1',  'Invalid value of @1'),
                    'volume');
            return false;
        }
        $gift_wrap = (bool)$gift_wrap;
        $url = trim($url);

        $price = (int)($price*100);
        if(!$price){
            $err = formatReplace(langTranslate('product', 'err', 'Invalid value of @1',  'Invalid value of @1'),
                    'price');
            return false;
        }
        switch($volume){
            case 1913:
                $normalize_koef = 0.5;
                break;
            case 1914:
                $normalize_koef = 0.7;
                break;
            case 1916:
                $normalize_koef = 0.75;
                break;
            case 1917:
                $normalize_koef = 1.5;
                break;
            case 1918:
                $normalize_koef = 0.375;
                break;
            case 1915:
            default:
                $normalize_koef = 1;
        }
        $normalized_price = (int)($price/$normalize_koef);

        $res = $this->XM->sqlcore->query('SELECT product.p_isvintage
            from product
            where product.p_id = '.$p_id.'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Product doesn\'t exist',  'Product doesn\'t exist');
            return false;
        }
        if($year && !$row['p_isvintage']){
            $err = langTranslate('product', 'err', 'Product is not a vintage product',  'Product is not a vintage product');
            return false;
        }
        if(!$year && $row['p_isvintage']){
            $err = langTranslate('product', 'err', 'Product is a vintage product',  'Product is a vintage product');
            return false;
        }
        $res = $this->XM->sqlcore->query('SELECT product_vintage.pv_id,product_vintage_company_price.pvcp_id,product_vintage_company_price.pvcp_normalized_price,product_vintage_company_price.pvcp_direct_url,product_vintage_company_price.pvcp_timestamp
            from product_vintage
            left join product_vintage_company_price on product_vintage_company_price.pv_id = product_vintage.pv_id and product_vintage_company_price.company_id = '.$company_id.' and product_vintage_company_price.pvcp_volume_pav_id = '.$volume.' and product_vintage_company_price.pvcp_gift_wrap = '.($gift_wrap?1:0).'
            where product_vintage.p_id = '.$p_id.' and '.($year?'product_vintage.pv_year = '.$year:'product_vintage.pv_blank = 1').'
            limit 1');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        if(!$row){
            $err = langTranslate('product', 'err', 'Vintage doesn\'t exist',  'Vintage doesn\'t exist');
            return false;
        }
        $pv_id = (int)$row['pv_id'];
        if($row['pvcp_id']){
            if($timestamp_limiter && $timestamp_limiter < (int)$row['pvcp_timestamp']){
                $err = langTranslate('product', 'err', 'Company price has already been updated',  'Company price has already been updated');
                return false;
            }
            $update_arr = array();
            $update_arr[] = 'pvcp_price = '.$price;
            if($row['pvcp_normalized_price'] != $normalized_price){
                $update_arr[] = 'pvcp_normalized_price = '.$normalized_price;
            }
            if(!strlen($url)){
                $update_arr[] = 'pvcp_direct_url = null';
            } elseif($row['pvcp_direct_url'] != $url){
                $update_arr[] = 'pvcp_direct_url = \''.$this->XM->sqlcore->prepString($url,256).'\'';
            }
            if(!empty($update_arr)){
                $this->XM->sqlcore->query('UPDATE product_vintage_company_price set '.implode(',', $update_arr).' where pvcp_id = '.(int)$row['pvcp_id']);
                $this->XM->sqlcore->commit();
            }
        } else {
            $this->XM->sqlcore->query('INSERT INTO product_vintage_company_price (pv_id,company_id,pvcp_volume_pav_id,pvcp_gift_wrap,pvcp_price,pvcp_normalized_price,pvcp_direct_url)
                VALUES ('.$pv_id.','.$company_id.','.$volume.','.($gift_wrap?1:0).','.$price.','.$normalized_price.','.(strlen($url)?'\''.$this->XM->sqlcore->prepString($url,256).'\'':'null').')');
            $this->XM->sqlcore->commit();
        }
        return true;
    }
    public function pricelist_filter($p_ids, $only_having_vintages, $omit_fullname, $omit_score, $return_vintages, &$vintages, $check_singles, &$is_single_vintage, &$is_single_volume, $order_by_field, $order_by_direction_asc, &$page, &$pagelimit, &$count, $err){
        if(!is_array($p_ids)){
            $p_ids = array();
        }
        if(($page = (int)$page)<=0){
            $page = 1;
        }
        $pagelimit = (int)$pagelimit;
        if($pagelimit<=0 || $pagelimit>100){
            $pagelimit = 50;
        }

        $pricelist_filter_p_ids_inner_join = '';
        if(!empty($p_ids)){
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE pricelist_filter_p_ids (
                `p_id` bigint(20) UNSIGNED NOT NULL,
                PRIMARY KEY pricelist_filter_p_ids_pk (p_id)
            )');
            foreach($p_ids as $p_id){
                $this->XM->sqlcore->query('INSERT INTO pricelist_filter_p_ids (p_id) VALUES ('.((int)$p_id).')');
            }
            $pricelist_filter_p_ids_inner_join = 'inner join pricelist_filter_p_ids on pricelist_filter_p_ids.p_id = product.p_id';
        }

        if($return_vintages){
            $res = $this->XM->sqlcore->query('SELECT distinct coalesce(product_vintage.pv_year,\'NV\') as pv_year
                from product
                '.$pricelist_filter_p_ids_inner_join.'
                inner join product_vintage on product_vintage.p_id = product.p_id and (product_vintage.pv_blank xor product.p_isvintage)
                inner join product_vintage_company_price on product_vintage_company_price.pv_id = product_vintage.pv_id
                inner join company on company.company_id = product_vintage_company_price.company_id and company.company_is_approved = 1
                order by coalesce(product_vintage.pv_year,\'NV\') asc');
            while($row = $this->XM->sqlcore->getRow($res)){
                $vintages[] = $row['pv_year'];
            }
            $this->XM->sqlcore->freeResult($res);
        }


        $vintages_inner_join = '';
        $product_join_condition = '';
        $product_vintage_join_condition = 'and ( product.p_isvintage xor product_vintage.pv_blank )';
        if(is_array($only_having_vintages) && !empty($only_having_vintages)){
            $include_nv = in_array('NV', $only_having_vintages);
            if(count($only_having_vintages)>($include_nv?1:0)){
                $this->XM->sqlcore->query('CREATE TEMPORARY TABLE pricelist_filter_vintages (
                    `pv_year` smallint(5) UNSIGNED NULL
                )');
                foreach($only_having_vintages as $vintage){
                    if($include_nv && $vintage=='NV'){
                        continue;
                    }
                    $this->XM->sqlcore->query('INSERT INTO pricelist_filter_vintages (pv_year) VALUES ('.((int)$vintage).')');
                }
                $vintages_inner_join = 'inner join pricelist_filter_vintages on pricelist_filter_vintages.pv_year = product_vintage.pv_year'.($include_nv?' or product.p_isvintage = 0':'');
                if(!$include_nv){
                    $product_vintage_join_condition = 'and product_vintage.pv_blank = 0';
                }
            } elseif($include_nv){
                $vintages_where_sql = 'where product.p_isvintage = 0';
                $product_vintage_join_condition = 'and product_vintage.pv_blank = 1';
            }
        }

        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE pricelist_filter_pvcp_ids (
            `pvcp_id` bigint(20) UNSIGNED NOT NULL,
            PRIMARY KEY pricelist_filter_pvcp_ids_pk (pvcp_id)
        )');
        $this->XM->sqlcore->query('INSERT INTO pricelist_filter_pvcp_ids (pvcp_id)
            SELECT distinct product_vintage_company_price.pvcp_id
            from product
            '.$pricelist_filter_p_ids_inner_join.'
            inner join product_vintage on product_vintage.p_id = product.p_id '.$product_vintage_join_condition.'
            '.$vintages_inner_join.'
            inner join product_vintage_company_price on product_vintage_company_price.pv_id = product_vintage.pv_id
            inner join company on company.company_id = product_vintage_company_price.company_id and company.company_is_approved = 1');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS pricelist_filter_vintages');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS pricelist_filter_p_ids');

        //count
        $res = $this->XM->sqlcore->query('SELECT count(1) as cnt from pricelist_filter_pvcp_ids');
        $row = $this->XM->sqlcore->getRow($res);
        $this->XM->sqlcore->freeResult($res);
        $count = (int)$row['cnt'];
        if($count==0){
            $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS pricelist_filter_pvcp_ids');
            return array();
        }
        if(($page-1)*$pagelimit>=$count){
            $page = ceil($count/$pagelimit);
        }
        if($check_singles){
            $res = $this->XM->sqlcore->query('SELECT count(distinct product_vintage_company_price.pv_id) as cnt_pv_id, count(distinct product_vintage_company_price.pvcp_volume_pav_id) as cnt_pav_id
                from pricelist_filter_pvcp_ids
                inner join product_vintage_company_price on product_vintage_company_price.pvcp_id = pricelist_filter_pvcp_ids.pvcp_id');
            $row = $this->XM->sqlcore->getRow($res);
            $this->XM->sqlcore->freeResult($res);
            $is_single_vintage = (int)$row['cnt_pv_id']<=1;
            $is_single_volume = (int)$row['cnt_pav_id']<=1;
        }

        $fullname_left_join = '';
        $name_select_sql = '';
        if($omit_fullname){
            $name_select_sql = 'if(product.p_isvintage=0,\'NV\',product_vintage.pv_year)';
        } else {
            $this->XM->sqlcore->query('CREATE TEMPORARY TABLE pricelist_filter_pvcp_ids_product
                select pvcp_id from pricelist_filter_pvcp_ids');
            $name_select_sql = 'concat(coalesce(product_ml.p_ml_fullname,\'-\'), \', \', if(product.p_isvintage=0,\'NV\',product_vintage.pv_year))';
            $fullname_left_join = 'left join (select product_ml.p_id,substring_index(group_concat(distinct product_ml.p_ml_id order by product_ml.lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as p_ml_id
                    from pricelist_filter_pvcp_ids_product
                    inner join product_vintage_company_price on product_vintage_company_price.pvcp_id = pricelist_filter_pvcp_ids_product.pvcp_id
                    inner join product_vintage on product_vintage.pv_id = product_vintage_company_price.pv_id
                    inner join product_ml on product_ml.p_id = product_vintage.p_id
                    group by product_ml.p_id
                ) as product_ln_glue on product_ln_glue.p_id = product.p_id
                left join product_ml on product_ml.p_ml_id = product_ln_glue.p_ml_id';
        }

        $score_left_join = '';
        $score_select_sql = '';
        if($omit_score){
            if($order_by_field=='score'){
                $order_by_field = null;
            }
            $score_select_sql = 'null';
            $score_left_join = '';
        } else {
            $score_left_join = 'left join product_vintage_score product_vintage_score3 on product_vintage_score3.pv_id = product_vintage.pv_id and product_vintage_score3.user_expert_level = 3';
            $score_select_sql = 'product_vintage_score3.pvs_score';
        }


        $order_by_sql = null;
        $order_by_required_join = '';
        switch($order_by_field){
            case 'name':
                $order_by_sql = $name_select_sql.' '.($order_by_direction_asc?'asc':'desc').', product_vintage_company_price.pvcp_normalized_price asc';
                break;
            case 'company':
                $order_by_sql = 'coalesce(company_ml.company_ml_name,\'-\') '.($order_by_direction_asc?'asc':'desc').', product_vintage_company_price.pvcp_normalized_price asc';
                break;
            case 'score':
                $order_by_sql = 'product_vintage_score3.pvs_score is not null desc, product_vintage_score3.pvs_score '.($order_by_direction_asc?'asc':'desc').', product_vintage_company_price.pvcp_normalized_price asc';
                break;
            case 'volume':
                $order_by_sql = 'coalesce(product_attribute_value_ml.pav_ml_name,product_attribute_value.pav_origin_name,\'-\') '.($order_by_direction_asc?'asc':'desc').', product_vintage_company_price.pvcp_normalized_price asc';
                break;
            case 'price':
                $order_by_sql = 'product_vintage_company_price.pvcp_price '.($order_by_direction_asc?'asc':'desc').', product_vintage_company_price.pvcp_normalized_price asc';
                break;
            case 'normalized-price':
                $order_by_sql = 'product_vintage_company_price.pvcp_normalized_price '.($order_by_direction_asc?'asc':'desc');
                break;
            default:
                $order_by_sql = 'product_vintage_company_price.pvcp_normalized_price desc';
        }

        $this->XM->sqlcore->query('CREATE TEMPORARY TABLE pricelist_filter_pvcp_ids_company
            select pvcp_id from pricelist_filter_pvcp_ids');

        $res = $this->XM->sqlcore->query('SELECT
                product_vintage_company_price.company_id, coalesce(company_ml.company_ml_name,\'-\') as company_name,
                product.p_id, '.$name_select_sql.'  as product_name, '.$score_select_sql.' as score,
                coalesce(product_attribute_value_ml.pav_ml_name,product_attribute_value.pav_origin_name,\'-\') as volume,
                product_vintage_company_price.pvcp_normalized_price, product_vintage_company_price.pvcp_price,
                product_vintage_company_price.pvcp_direct_url
            from pricelist_filter_pvcp_ids
            inner join product_vintage_company_price on product_vintage_company_price.pvcp_id = pricelist_filter_pvcp_ids.pvcp_id
            inner join product_vintage on product_vintage.pv_id = product_vintage_company_price.pv_id
            inner join product on product.p_id = product_vintage.p_id
            inner join product_attribute_value on product_attribute_value.pav_id = product_vintage_company_price.pvcp_volume_pav_id and product_attribute_value.pa_id = '.\PRODUCT\VOLUME_ATTRIBUTE_ID.'

            left join (
                select product_attribute_value_ml.pav_id,substring_index(group_concat(pav_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as pav_ml_id
                    from product_attribute_value_ml
                    inner join product_attribute_value on product_attribute_value.pav_id = product_attribute_value_ml.pav_id
                    inner join product_attribute on product_attribute.pa_id = product_attribute_value.pa_id and product_attribute.pa_show_only_origin = 0 and product_attribute.pa_id = '.\PRODUCT\VOLUME_ATTRIBUTE_ID.'
                    where product_attribute_value_ml.pav_ml_name is not null and not (product_attribute_value.pav_origin_name is not null and product_attribute_value_ml.lang_id <> '.$this->XM->lang->getCurrLangId().')
                    group by product_attribute_value_ml.pav_id) as product_attribute_value_ln_glue on product_attribute_value_ln_glue.pav_id = product_attribute_value.pav_id
            left join product_attribute_value_ml on product_attribute_value_ml.pav_ml_id = product_attribute_value_ln_glue.pav_ml_id

            left join (
                select company_ml.company_id,substring_index(group_concat(company_ml_id order by lang_id = '.$this->XM->lang->getCurrLangId().' desc),\',\',1) as company_ml_id
                    from pricelist_filter_pvcp_ids_company
                    inner join product_vintage_company_price on product_vintage_company_price.pvcp_id = pricelist_filter_pvcp_ids_company.pvcp_id
                    inner join company on company.company_id = product_vintage_company_price.company_id and company.company_is_approved = 1
                    inner join company_ml on company_ml.company_id = company.company_id
                    where company_ml.company_ml_is_approved = 1 and company_ml.company_ml_name is not null
                    group by company_ml.company_id
                ) as company_ln_glue on company_ln_glue.company_id = product_vintage_company_price.company_id
            left join company_ml on company_ml.company_ml_id = company_ln_glue.company_ml_id

            '.$fullname_left_join.'

            '.$score_left_join.'

            '.($order_by_sql?'order by '.$order_by_sql:'').'
            limit '.$pagelimit.' offset '.(($page-1)*$pagelimit));
        $result = array();
        while($row = $this->XM->sqlcore->getRow($res)){
            $result[] = array(
                    'id'=>(int)$row['p_id'],
                    'name'=>(string)$row['product_name'],
                    'score'=>($row['score']!==null)?str_replace('.', ',', ((float)$row['score'])/100):null,
                    'volume'=>(string)$row['volume'],

                    'price'=>number_format($row['pvcp_price']/100,2,',',' '),
                    'normalized_price'=>number_format($row['pvcp_normalized_price']/100,2,',',' '),
                    'url'=>(string)$row['pvcp_direct_url'],

                    'company_id'=>(int)$row['company_id'],
                    'company_name'=>(string)$row['company_name'],
                );
        }
        $this->XM->sqlcore->freeResult($res);
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS pricelist_filter_pvcp_ids');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS pricelist_filter_pvcp_ids_company');
        $this->XM->sqlcore->query('DROP TEMPORARY TABLE IF EXISTS pricelist_filter_pvcp_ids_product');
        return $result;
    }

}