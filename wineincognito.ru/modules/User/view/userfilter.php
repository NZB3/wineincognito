<?php langSetDefault('user', 'user'); 
$autosearch = false;
$hide_company_name = false;
$default_selectallexpertlevels = false;
$show_expert_level = false;
$default_url = BASE_URL.'/user/{{id}}';
$customnameurl = false;
$hide_role = false;
if(!isset($actions)){
    $actions = false;
}
if(!isset($showfavourite)){
    $showfavourite = false;
}
if(!isset($showmycompany)){
    $showmycompany = false;
}
if(!isset($showglobalexpertscores)){
    $showglobalexpertscores = false;
}
if(!isset($global_expert_scores)){
    $global_expert_scores = false;
}
if(!isset($approve_experts)){
    $approve_experts = false;
}
if(!isset($customnameaction)){
    $customnameaction = false;
}
if(!isset($contest_id)){
    $contest_id = null;
}
if(!isset($contest_product_id)){
    $contest_product_id = null;
}
if(isset($company_id)&&$company_id){//user list for company
    $autosearch = true;
    $hide_company_name = true;
}
if(isset($joinrequests_company_id)&&$joinrequests_company_id){//user join requests list for company
    $autosearch = true;
    $hide_company_name = true;
}
if($approve_experts){
    $autosearch = true;
    $show_expert_level = true;
    $default_url = BASE_URL.'/moderate/user/{{id}}/expert/approve';
    $customnameurl = true;
    $hide_role = true;
}
if($global_expert_scores){
    $autosearch = true;
    $show_expert_level = true;
    $hide_role = true;
    $default_selectallexpertlevels = true;
}
if(isset($tastingmodal)&&$tastingmodal){
    $customnameaction = true;
    $show_expert_level = true;
    $default_selectallexpertlevels = true;
    $hide_role = true;
}
$showscores = false;
$show_evaluation_scores = false;
$reviewmergeurl = '';
if($contest_id){
    $default_url = BASE_URL.'/contest/'.$contest_id.'/stats/user/{{id}}';
    $customnameurl = true;
    $hide_role = true;
    $hide_company_name = true;
    $show_expert_level = true;
    $show_evaluation_scores = true;
    $autosearch = true;
    if($contest_product_id){
        $reviewmergeurl = BASE_URL.'/contest/'.$contest_id.'/stats/product/'.$contest_product_id.'/user/{{id}}/reviewmerge/{{expert_level}}';
        $showscores = true;
    }
}
$colcount = 4 + (!$hide_role?1:0) + (!$hide_company_name?1:0) + ($show_evaluation_scores?2:0) + ($showscores?3:0) + ($customnameaction?1:0) + (($customnameaction||$customnameurl)?1:0) + ($show_expert_level?1:0) + ($actions?4+(isset($company_id)&&$company_id?1:0)+(isset($joinrequests_company_id)&&$joinrequests_company_id?2:0):0);
?>
<div class="filter-block user-filter <?=isset($company_id)&&$company_id?'single-company-user-list':''?> <?=$autosearch?'auto-search':''?>">
<form class="filter-form"><input type="hidden" name="action" value="user_filter" /><input type="hidden" class="filter-url" value="<?=BASE_URL?>/ajax/user/search" />
<?php if(isset($company_id)&&$company_id): ?>
<input type="hidden" name="company_id" value="<?=(int)$company_id?>" />
<?php endif; ?>
<?php if($hide_company_name): ?>
<input type="hidden" name="hide_company_name" value="1" />
<?php endif; ?>
<?php if($approve_experts): ?>
<input type="hidden" name="approve_expert_list" value="1" />
<?php endif; ?>
<?php if($global_expert_scores): ?>
<input type="hidden" name="only_global_expert_scores" value="1" />
<?php endif; ?>
<?php if($show_expert_level): ?>
<input type="hidden" name="show_expert_level" value="1" />
<?php endif; ?>
<?php if($contest_id): ?>
<input type="hidden" name="only_participants_of_contest_id" value="<?=$contest_id?>" />
<?php if($contest_product_id): ?>
<input type="hidden" name="only_participants_of_contest_product_id" value="<?=$contest_product_id?>" />
<?php   endif;
    endif; ?>
<?php if(isset($joinrequests_company_id)&&$joinrequests_company_id): ?>
<input type="hidden" name="joinrequests_company_id" value="<?=(int)$joinrequests_company_id?>" />
<?php endif; ?>

<script type="template" class="dropbox-template-select">
<tr><td class="label">{ifdef{name}}<label>{{name}}</label>{endifdef{name}}</td><td><div class="dropbox multiple" data-fieldname="{{fieldname}}" data-group="{{group}}" data-has-children="{{haschildren}}" data-depth="{{depth}}" data-system="{{system}}"><input type="checkbox" id="dropbox-user-filter-{{fieldname}}-{{group}}-{{depth}}" /><label for="dropbox-user-filter-{{fieldname}}-{{group}}-{{depth}}"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul><li class="search"><input type="text" /></li>{{options}}</ul></div></td></tr>
</script>
<script type="template" class="dropbox-template-option">
<li class="item {if{selected}}selected{endif{selected}} {!if{important}}not-important{end!if{important}}"><label><input type="checkbox" data-attr-id="{{attrId}}" name="{{fieldname}}[]" value="{{id}}" {if{selected}}checked{endif{selected}} data-se-text=" {{setext}}" /><span></span>{{name}}</label></li>
</script>
<script type="template" class="dropbox-template-option-header">
<li class="header">{{name}}</li>
</script>
<script type="string" class="dropbox-template-empty-string"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></script>
<script type="template" class="dropbox-template-option-important-toggle">
<li class="dropbox-item-list-toggle show-not-important"><?=langTranslate('main','dropbox','Show not important', 'Show not important');?></li><li class="dropbox-item-list-toggle hide-not-important"><?=langTranslate('main','dropbox','Hide not important', 'Hide not important');?></li>
</script>
<table class="subcontent fieldlist filter">
<thead>
    <tr class="header dropdown"><th colspan="2"><label><input type="checkbox" checked><?=langTranslate('Main','Filter','Filter', 'Filter');?></label></th></tr>
</thead>
<tbody>
<tr><td class="label"><label for="user-filter-form-search-text"><?=langTranslate('Name', 'Name');?></label></td><td><input type="text" name="search_text" id="user-filter-form-search-text" value="" /></td></tr>
<?php 
foreach($attrvaltree as $group=>$groupvaltree):
    foreach($groupvaltree as $depth=>$depthvaltree):
        $keys = array_keys($depthvaltree);
        $first_key = $keys[0];
        $haschildren = 0;
        foreach($depthvaltree as $attr){
            if($attr['haschildren']){
                $haschildren = 1;
                break;
            }
        }
?>
<tr><td class="label"><?=(count($depthvaltree)==1)?'<label>'.htmlentities($depthvaltree[$first_key]['name']).'</label>':'';?></td><td><div class="dropbox fresh <?=count($depthvaltree)==1&&count($depthvaltree[$first_key]['vals'])==1?'disabled':''?> multiple" data-fieldname="attr" data-group="<?=$group?>" data-has-children="<?=$haschildren?>" data-depth="<?=$depth?>" data-system="<?=$depthvaltree[$first_key]['system']?>"><input type="checkbox" id="dropbox-user-filter-attr-<?=$group?>-<?=$depth?>" /><label for="dropbox-user-filter-attr-<?=$group?>-<?=$depth?>"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul><li class="search"><input type="text" /></li>
<?php   foreach($depthvaltree as $attr):?>
<?php       if(count($depthvaltree)>1): ?>
<li class="header"><?=htmlentities($attr['name'])?></li>
<?php       endif; ?>
<?php       foreach($attr['vals'] as $val): ?>
    <li class="item <?=$val['selected']?'selected':''?> <?=(!$val['important'])?'not-important':''?>"><label><input type="checkbox" data-attr-id="<?=$attr['id']?>" name="attr[]" value="<?=$val['id']?>" <?=$val['selected']?'checked':''?> <?=$val['setext']?'data-se-text=" '.$val['setext'].'"':''?> /><span></span><?=htmlentities($val['name'])?></label></li>
<?php       endforeach; ?>
</ul></div></td></tr>
<?php   endforeach; ?>
<?php endforeach; ?>
<?php endforeach; ?>
<tr><td class="label"><label for="dropbox-custom-user-expert-level"><?=langTranslate('Expert', 'Expert');?></label></td><td><div class="dropbox fresh multiple" data-custom="1"><input type="checkbox" id="dropbox-custom-user-expert-level" /><label for="dropbox-custom-user-expert-level"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul>
<?php   foreach($expert_level_list as $expert_level=>$caption): ?>
<li class="item <?=($default_selectallexpertlevels&&$expert_level!=0)?'selected':''?>"><label><input type="checkbox" name="expert_level[]" value="<?=$expert_level?>" <?=($default_selectallexpertlevels&&$expert_level!=0)?'checked="checked"':''?> /><span></span><?=htmlentities($caption)?></label></li>
<?php   endforeach; ?>
</ul></div></td></tr>
<?php if($showmycompany): ?>
<tr><td></td><td><label class="radio"><input type="checkbox" name="only_my_company" value="1"><span></span><?=langTranslate('Only users from my company', 'Only users from my company');?></label></td></tr>
<?php endif; ?>
<?php if($showfavourite): ?>
<tr><td></td><td><label class="radio"><input type="checkbox" name="only_favourite" value="1"><span></span><?=langTranslate('Only favourites', 'Only favourites');?></label></td></tr>
<?php endif; ?>
<tr><td></td><td><label class="radio"><input type="checkbox" name="only_online" value="1"><span></span><?=langTranslate('Only online', 'Only online');?></label></td></tr>
<tr><td class="submit" colspan="2">
    <label><?=langTranslate('Main','Filter','Per Page', 'Per Page');?></label>
    <select class="page-limit">
        <option value="10">10</option>
        <option value="20">20</option>
        <option value="30">30</option>
        <option value="50" selected>50</option>
        <option value="100">100</option>
    </select>
    <input type="submit" value="<?=langTranslate('Main','Filter','Search', 'Search');?>" />
    <input type="reset" value="<?=langTranslate('Main','Filter','Reset', 'Reset');?>" />
</td></tr>
</tbody></table>
</form>

<script type="template" class="datalist-item-template">
<tr class="item {if{favourite}}favourite{endif{favourite}} {if{can_add_product}}can-add-product{endif{can_add_product}} {if{can_add_tasting}}can-add-tasting{endif{can_add_tasting}} {!if{user_offline_time}}user-online{end!if{user_offline_time}}" data-id="{{id}}" data-company-id="{{company_id}}">
<?php if($customnameaction): ?>
<td class="custom-add"><span></span></td>
<?php endif; ?>
<td class="activity"><span class="tooltip-right" data-tooltip="{if{user_offline_time}}<?=formatReplace(langTranslate('Last seen @1 ago','Last seen @1 ago'),'{{user_offline_time}}')?>{endif{user_offline_time}}{!if{user_offline_time}}<?=langTranslate('Online', 'Online');?>{end!if{user_offline_time}}"></span></td>
<?php if($showfavourite): ?>
<td class="favourite {if{can_favourite}}can-favourite{endif{can_favourite}}"><span></span></td>
<?php endif; 
    if(!$hide_role): ?>
<td class="role">{if{is_owner}}<span class="supervisor"></span>{endif{is_owner}}</td>
<?php endif; ?>
<td class="name"><a <?=!$customnameaction?'href="'.$default_url.'"':''?>>{{name}}</a></td>
<?php if(!$hide_company_name): ?>
<td class="company">{if{company_id}}<a href="<?=BASE_URL?>/company/{{company_id}}" target="_blank">{{company_name}}</a>{endif{company_id}}</td>
<?php endif; ?>
<?php if($show_expert_level): ?>
<td class="expert">{{expert_level}}<?php
        if($showglobalexpertscores):
?>{if{global_expert_score}} - <a href="<?=BASE_URL?>/moderate/user/experts/rating/{{id}}">{{global_expert_score}} ({{global_expert_count}})</a>{endif{global_expert_score}}<?php
        endif;
?></td>
<?php endif; ?>
<td class="separator"></td>
<?php if($show_evaluation_scores): ?>
<td class="score">{{automatic_evaluation_score}}</td><td class="score">{{manual_evaluation_score}}</td>
<?php endif;
    if($showscores): ?>
<td class="score">{if{score1}}<?php if($reviewmergeurl):?><a href="<?=str_replace('{{expert_level}}','1',$reviewmergeurl)?>"><?php endif; ?>{{score1}}<?php if($reviewmergeurl):?></a><?php endif; ?>{endif{score1}}</td><td class="score">{if{score2}}<?php if($reviewmergeurl):?><a href="<?=str_replace('{{expert_level}}','2',$reviewmergeurl)?>"><?php endif; ?>{{score2}}<?php if($reviewmergeurl):?></a><?php endif; ?>{endif{score2}}</td><td class="score">{if{score3}}<?php if($reviewmergeurl):?><a href="<?=str_replace('{{expert_level}}','3',$reviewmergeurl)?>"><?php endif; ?>{{score3}}<?php if($reviewmergeurl):?></a><?php endif; ?>{endif{score3}}</td>
<?php endif;
    if($customnameaction||$customnameurl): ?>
<td class="info"><a href="<?=BASE_URL?>/user/{{id}}"></a></td>
<?php endif; 
    if($actions): ?>
<td class="add-product-right">{if{can_edit_add_rights}}<span></span>{endif{can_edit_add_rights}}</td><td class="add-tasting-right">{if{can_edit_add_rights}}<span></span>{endif{can_edit_add_rights}}</td>
<?php   if(isset($joinrequests_company_id)&&$joinrequests_company_id): ?>
<td class="approve-join-request">{if{can_approve_join_request}}<span data-company-id="<?=$joinrequests_company_id?>"></span>{endif{can_approve_join_request}}</td><td class="reject-join-request">{if{can_reject_join_request}}<span data-company-id="<?=$joinrequests_company_id?>"></span>{endif{can_reject_join_request}}</td>
<?php   endif; 
        if(isset($company_id)&&$company_id): ?>
<td class="dismiss">{if{can_dismiss}}<span></span>{endif{can_dismiss}}</td>
<?php   endif; ?>
<td class="change_password">{if{can_change_password}}<a href="<?=BASE_URL?>/user/{{id}}/change_password"></a>{endif{can_change_password}}</td><td class="edit">{if{can_edit}}<a href="<?=BASE_URL?>/user/{{id}}/edit"></a>{endif{can_edit}}</td></tr>
<?php endif; ?>
</script>
<script type="template" class="datalist-pagination-item-template">
<li class="page {if{current}}current{endif{current}}" data-page="{{page}}">{{caption}}</li>
</script>
<script type="template" class="datalist-pagination-separator-template">
<li class="separator">&hellip;</li>
</script>
<script type="string" class="confirm_string_dismiss_user"><?=formatReplace(langTranslate('Are you sure you want to dismiss @1?','Are you sure you want to dismiss @1?'),
                '{{fullname}}')?></script>
<table class="subcontent datalist">
    <thead>
        <tr class="pagination"><th colspan="<?=$colcount?>"><ul class="pagination"></ul></th></tr>
        <tr>
<?php if($customnameaction): ?>
            <th class="custom-add"></th>
<?php endif; ?>
            <th class="activity filter-block-can-order-by" data-filter-block-order-field="activity"><span class="filter-block-order-direction"></span></th>
<?php if($showfavourite): ?>
            <th class="favourite"></th>
<?php endif; 
    if(!$hide_role): ?>
            <th class="role"></th>
<?php endif; ?>
            <th class="filter-block-can-order-by" data-filter-block-order-field="name"><?=langTranslate('Name','Name')?><span class="filter-block-order-direction"></span></th>
<?php if(!$hide_company_name): ?>
            <th class="company filter-block-can-order-by" data-filter-block-order-field="company"><?=langTranslate('Company','Company')?><span class="filter-block-order-direction"></span></th>
<?php endif;
    if($show_expert_level): ?>
            <th class="expert filter-block-can-order-by" data-filter-block-order-field="expert"><?=langTranslate('Expert','Expert')?><span class="filter-block-order-direction"></span></th>
<?php endif; ?>
            <th class="separator"></th>
<?php if($show_evaluation_scores): ?>
            <th class="score automatic-evaluation filter-block-can-order-by" data-filter-block-order-field="automatic-evaluation"><span class="header non-sticky-tooltip" data-tooltip="<?=langTranslate('tasting', 'expert evaluation', 'Automatic evaluation','Automatic evaluation')?>"></span><span class="filter-block-order-direction"></span></th><th class="score manual-evaluation filter-block-can-order-by" data-filter-block-order-field="manual-evaluation"><span class="header non-sticky-tooltip" data-tooltip="<?=langTranslate('tasting', 'expert evaluation', 'Manual evaluation','Manual evaluation')?>"></span><span class="filter-block-order-direction"></span></th>
<?php endif;
    if($showscores): ?>
            <th class="score score1 filter-block-can-order-by" data-filter-block-order-field="score1"><span class="header non-sticky-tooltip" data-tooltip="<?=$expert_level_list[1]?>"></span><span class="filter-block-order-direction"></span></th><th class="score score2 filter-block-can-order-by" data-filter-block-order-field="score2"><span class="header non-sticky-tooltip" data-tooltip="<?=$expert_level_list[2]?>"></span><span class="filter-block-order-direction"></span></th><th class="score score3 filter-block-can-order-by" data-filter-block-order-field="score3"><span class="header non-sticky-tooltip" data-tooltip="<?=$expert_level_list[3]?>"></span><span class="filter-block-order-direction"></span></th>
<?php endif;
    if($customnameaction||$customnameurl): ?>
            <th class="info"></th>
<?php endif; 
    if($actions):?>
            <th class="add-product-right"></th><th class="add-tasting-right"></th>
<?php   if(isset($joinrequests_company_id)&&$joinrequests_company_id): ?>
            <th class="approve-join-request"></th><th class="reject-join-request"></th>
<?php   endif; 
        if(isset($company_id)&&$company_id): ?>
            <th class="dismiss"></th>
<?php   endif; ?>            
            <th class="change_password"></th><th class="edit"></th>
<?php endif; ?>
        </tr>
    </thead>
    <tfoot>
        <tr>
<?php if($customnameaction): ?>
            <th class="custom-add"></th>
<?php endif; ?>
            <th class="activity filter-block-can-order-by" data-filter-block-order-field="activity"><span class="filter-block-order-direction"></span></th>
<?php if($showfavourite): ?>
            <th class="favourite"></th>
<?php endif; 
    if(!$hide_role): ?>
            <th class="role"></th>
<?php endif; ?>
            <th class="filter-block-can-order-by" data-filter-block-order-field="name"><?=langTranslate('Name','Name')?><span class="filter-block-order-direction"></span></th>
<?php if(!$hide_company_name): ?>
            <th class="company filter-block-can-order-by" data-filter-block-order-field="company"><?=langTranslate('Company','Company')?><span class="filter-block-order-direction"></span></th>
<?php endif;
    if($show_expert_level): ?>
            <th class="expert filter-block-can-order-by" data-filter-block-order-field="expert"><?=langTranslate('Expert','Expert')?><span class="filter-block-order-direction"></span></th>
<?php endif; ?>
            <th class="separator"></th>
<?php if($show_evaluation_scores): ?>
            <th class="score automatic-evaluation"></th><th class="score manual-evaluation"></th>
<?php endif;
    if($showscores): ?>
            <th class="score score1 filter-block-can-order-by" data-filter-block-order-field="score1"><span class="header non-sticky-tooltip" data-tooltip="<?=$expert_level_list[1]?>"></span><span class="filter-block-order-direction"></span></th><th class="score score2 filter-block-can-order-by" data-filter-block-order-field="score2"><span class="header non-sticky-tooltip" data-tooltip="<?=$expert_level_list[2]?>"></span><span class="filter-block-order-direction"></span></th><th class="score score3 filter-block-can-order-by" data-filter-block-order-field="score3"><span class="header non-sticky-tooltip" data-tooltip="<?=$expert_level_list[3]?>"></span><span class="filter-block-order-direction"></span></th>
<?php endif;
    if($customnameaction||$customnameurl): ?>
            <th class="info"></th>
<?php endif; 
    if($actions): ?>
            <th class="add-product-right"></th><th class="add-tasting-right"></th>
<?php   if(isset($joinrequests_company_id)&&$joinrequests_company_id): ?>
            <th class="approve-join-request"></th><th class="reject-join-request"></th>
<?php   endif; 
        if(isset($company_id)&&$company_id): ?>
            <th class="dismiss"></th>
<?php   endif; ?> 
            <th class="change_password"></th><th class="edit"></th>
<?php endif; ?>
        </tr>
        <tr class="pagination"><th colspan="<?=$colcount?>"><ul class="pagination"></ul></th></tr>
    </tfoot>
    <tbody>
        <tr class="noentries"><td colspan="<?=$colcount?>"><?=langTranslate('Sorry, no matches found', 'Sorry, no matches found');?></td></tr>
        <tr class="loading"><td colspan="<?=$colcount?>"></td></tr>
        <tr class="errmsg"><td colspan="<?=$colcount?>"></td></tr>
    </tbody>
</table>
</div>
<?php langClean('user', 'user')?>