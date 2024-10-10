<?php 
    langSetDefault('tasting', 'tasting'); 

    $showinfo = false;
    $showstatus = true;
    $showpendingreviewcount = false;
    $showprice = true;
    $tastingurl = BASE_URL.'/tasting/{{id}}';
    $tastingstatsurl = BASE_URL.'/tasting/{{id}}/stats';
    $lead_to_stats = true;
    $showattendanceresponse = true;
    $customnameaction = false;

    $autosearch = true;
    $default_status = array(0,1,2);
    $only_owned_default = false;
    $show_currently_participating = true;
    $show_only_approved = true;
    if(!isset($currently_participating_default)){
        $currently_participating_default = false;
    }
    if(!isset($only_approved_default)){
        $only_approved_default = false;
    }
    if($only_approved_default){
        if(!in_array(4, $default_status)){
            $default_status[] = 4;    
        }
    }
    if(!isset($pendingreview)){
        $pendingreview = false;
    }
    if(!isset($took_part)){
        $took_part = false;
    }
    if(!isset($contest)){
        $contest = false;
    }
    if(!isset($can_add)){
        $can_add = false;
    }
    if(!isset($only_for_assessment)){
        $only_for_assessment = false;
    }
    if(!isset($global_expert_ratings_for_user)){
        $global_expert_ratings_for_user = null;
    }
    if($pendingreview){
        $autosearch = true;
        $statuslist = array();
        $can_add = false;
        $only_owned_default = false;
        $show_currently_participating = false;

        $showattendanceresponse = false;
        $showstatus = false;
        $showpendingreviewcount = true;
        $showprice = false;
        $tastingurl = '{if{ranking_scoring}}'.BASE_URL.'/myreview/pending/tasting/{{id}}/ranking{endif{ranking_scoring}}{!if{ranking_scoring}}'.BASE_URL.'/myreview/pending/tasting/{{id}}/products{end!if{ranking_scoring}}';

        $lead_to_stats = false;
    }
    if($took_part){
        $autosearch = true;
        $statuslist = array();
        $can_add = false;
        $only_owned_default = false;
        $show_currently_participating = false;

        $showattendanceresponse = false;
        $showstatus = false;
        $showpendingreviewcount = false;
        $showprice = false;
        $tastingurl = BASE_URL.'/myreview/tasting/{{id}}/stats';
        $lead_to_stats = false;
        if(!isset($took_part_vintage_id)){
            $took_part_vintage_id = 0;
        }
        $took_part_vintage_id = (int)$took_part_vintage_id;
        if($took_part_vintage_id){
            $tastingurl = BASE_URL.'/myreview/product/'.$took_part_vintage_id.'/tasting/{{id}}/stats';
        }
    }
    if($contest){
        $autosearch = true;
        $can_add = false;
        $only_owned_default = false;
        $show_currently_participating = false;
        $customnameaction = true;
        $showattendanceresponse = false;
        $showstatus = true;
        $showpendingreviewcount = false;
        $showprice = false;
        $lead_to_stats = false;
    }
    if($only_for_assessment){
        $show_only_approved = false;
        $statuslist = array();
        $autosearch = true;
        $can_add = false;
        $only_owned_default = false;
        $show_currently_participating = false;
        $customnameaction = false;
        $showattendanceresponse = false;
        $showstatus = false;
        $showpendingreviewcount = false;
        $showprice = false;
        $lead_to_stats = true;
    }
    if($global_expert_ratings_for_user){
        $show_only_approved = true;
        $statuslist = array();
        $autosearch = true;
        $can_add = false;
        $only_owned_default = false;
        $show_currently_participating = false;
        $customnameaction = false;
        $showattendanceresponse = false;
        $showstatus = false;
        $showpendingreviewcount = false;
        $showprice = false;
        $lead_to_stats = true;
        $showinfo = true;
        $global_expert_stat_page_url = BASE_URL.'/tasting/{{id}}/stats/user/'.$global_expert_ratings_for_user;
    }
    $colcount = 2 + ($customnameaction?1:0) + ($showstatus?1:0) + ($showattendanceresponse?1:0) + ($showpendingreviewcount?1:0) + ($showprice?1:0) + ($showinfo?1:0);
?>
<div class="subcontent filter-block tasting-filter <?=$autosearch?'auto-search':''?>">
<?php if(!$pendingreview&&!$took_part&&!$contest&&!$only_for_assessment): ?>
<div class="tutorial-block"><?=langTranslate('Tastings that you have created or in which you can participate','Tastings that you have created or in which you can participate')?></div>
<?php endif; ?>
<?php if($took_part): ?>
<div class="tutorial-block"><?=langTranslate('Tastings you\'ve took part in','Tastings you\'ve took part in')?></div>
<?php endif; ?>
<form class="filter-form"><input type="hidden" name="action" value="tasting_filter" /><input type="hidden" class="filter-url" value="<?=BASE_URL?>/ajax/tasting/search" />
<?php if($pendingreview): ?>
<input type="hidden" name="only_pending_reviews" value="1" />
<?php endif; ?>
<?php if($showattendanceresponse): ?>
<input type="hidden" name="show_attendance_response" value="1" />
<?php endif; ?>
<?php if($contest): ?>
<input type="hidden" name="can_add_to_contest" value="<?=(int)$contest?>" />
<?php endif; ?>
<?php if($took_part): ?>
<input type="hidden" name="only_took_part" value="1" />
<?php   if($took_part_vintage_id): ?>
<input type="hidden" name="took_part_vintage_id" value="<?=$took_part_vintage_id?>" />
<?php   endif;
    endif;
    if($only_for_assessment): ?>
<input type="hidden" name="only_for_assessment" value="1" />
<?php endif;
    if($global_expert_ratings_for_user): ?>
<input type="hidden" name="global_expert_ratings_for_user" value="<?=$global_expert_ratings_for_user?>" />
<?php endif; ?>
<script type="string" class="dropbox-template-empty-string"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></script>
<script type="template" class="dropbox-template-option-important-toggle">
<li class="dropbox-item-list-toggle show-not-important"><?=langTranslate('main','dropbox','Show not important', 'Show not important');?></li><li class="dropbox-item-list-toggle hide-not-important"><?=langTranslate('main','dropbox','Hide not important', 'Hide not important');?></li>
</script>
<table class="subcontent fieldlist filter">
<thead>
    <tr class="header dropdown"><th colspan="2"><label><input type="checkbox" checked><?=langTranslate('Main','Filter','Filter', 'Filter');?></label></th></tr>
</thead>
<tbody>
<tr><td class="label"><label for="tasting-filter-form-start-date-from"><?=langTranslate('Start', 'Start');?></label></td><td>
    <span class="form-date-from-to">
        <label for="tasting-filter-form-start-date-from"><?=langTranslate('From', 'From');?></label>
        <input type="text" name="start_date_from" id="tasting-filter-form-start-date-from" value="" maxlength="10" />
        <label for="tasting-filter-form-start-date-to"><?=langTranslate('To', 'To');?></label>
        <input type="text" name="start_date_to" id="tasting-filter-form-start-date-to" value="" maxlength="10" />
        <span></span>
    </span>
</td></tr>
<?php if(!empty($statuslist)): ?>
<tr><td class="label"><label for="dropbox-custom-tasting-filter-status"><?=langTranslate('Status', 'Status');?></label></td><td><div class="dropbox fresh multiple" data-custom="1"><input type="checkbox" id="dropbox-custom-tasting-filter-status" /><label for="dropbox-custom-tasting-filter-status"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul>
<?php   foreach($statuslist as $status=>$caption): ?>
    <li class="item <?=in_array($status, $default_status)?'selected':''?>"><label><input type="checkbox" name="status[]" value="<?=$status?>" <?=in_array($status, $default_status)?'checked="checked"':''?> /><span></span><?=htmlentities($caption)?></label></li>
<?php   endforeach; ?>
</ul></div></td></tr>
<?php endif; ?>
<tr><td></td><td><label class="radio"><input type="checkbox" name="only_owned" value="1" <?=$only_owned_default?'checked="checked"':''?>><span></span><?=langTranslate('Only owned', 'Only owned');?></label></td></tr>
<?php if($show_only_approved): ?>
<tr><td></td><td><label class="radio"><input type="checkbox" name="only_approved" value="1" <?=$only_approved_default?'checked="checked"':''?>><span></span><?=langTranslate('Only approved', 'Only approved');?></label></td></tr>
<?php endif;
    if($show_currently_participating): ?>
<tr><td></td><td><label class="radio"><input type="checkbox" name="currently_participating" value="1" <?=$currently_participating_default?'checked="checked"':''?>><span></span><?=langTranslate('Currently participating', 'Currently participating');?></label></td></tr>
<?php endif; ?>
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
<tr class="item" data-id="{{id}}">
<?php if($customnameaction): ?>
<td class="custom-add"><span></span></td>
<?php endif; ?>
<td class="date">{{date}}</td><td class="name"><a href="<?=$lead_to_stats?'{!if{lead_to_stats}}'.$tastingurl.'{end!if{lead_to_stats}}{if{lead_to_stats}}'.$tastingstatsurl.'{endif{lead_to_stats}}':$tastingurl?>">{{name}}</a></td>
<?php if($showstatus): ?>
<td class="status">{{status}}</td>
<?php endif;
    if($showattendanceresponse): ?>
<td class="attendance-response {{attendance_response_status}}"><span {if{attendance_response_status_text}}data-tooltip="{{attendance_response_status_text}}"{endif{attendance_response_status_text}}></span></td>
<?php endif;
    if($showprice): ?>
<td class="price">{{personal_price}}</th>
<?php endif;
    if($showpendingreviewcount): ?>
<td class="pending-review-count">{{pending_review_count}}</td>
<?php endif;
    if($global_expert_ratings_for_user): ?>
<td class="global-expert-rating {if{global_expert_rating_leniency}}global-expert-rating-lenient{endif{global_expert_rating_leniency}} {if{global_expert_rating_zero}}global-expert-rating-zero{endif{global_expert_rating_zero}}"><a href="<?=$global_expert_stat_page_url?>">{{global_expert_rating_score}} ({{global_expert_rating_place}})</a></td>
<?php endif;
    if($showinfo): ?>
<td class="info"><a href="<?=BASE_URL?>/tasting/{{id}}"></td>
<?php endif; ?>
</tr>
</script>
<script type="template" class="datalist-pagination-item-template">
<li class="page {if{current}}current{endif{current}}" data-page="{{page}}">{{caption}}</li>
</script>
<script type="template" class="datalist-pagination-separator-template">
<li class="separator">&hellip;</li>
</script>
<table class="subcontent datalist">
    <thead>
        <tr class="head-buttons"><th colspan="<?=$colcount?>">
<?php if($can_add): ?>
            <a class="add" href="<?=BASE_URL?>/tasting/add"></a>
<?php endif; ?>
        </th></tr>
        <tr class="pagination"><th colspan="<?=$colcount?>"><ul class="pagination"></ul></th></tr>
        <tr>
<?php if($customnameaction): ?>
            <th class="custom-add"></th>
<?php endif; ?>
            <th class="date filter-block-can-order-by" data-filter-block-order-field="date"><?=langTranslate('Start','Start')?><span class="filter-block-order-direction"></span></th>
            <th class="name filter-block-can-order-by" data-filter-block-order-field="name"><?=langTranslate('Name','Name')?><span class="filter-block-order-direction"></span></th>
<?php if($showstatus): ?>
            <th class="status filter-block-can-order-by" data-filter-block-order-field="status"><?=langTranslate('Status','Status')?><span class="filter-block-order-direction"></span></th>
<?php endif;
    if($showattendanceresponse): ?>
            <th class="attendance-response"></th>
<?php endif;
    if($showprice): ?>
            <th class="price"><?=langTranslate('Price','Price')?></th>
<?php endif;
    if($showpendingreviewcount): ?>
            <th class="pending-review-count"></th>
<?php endif;
    if($global_expert_ratings_for_user): ?>
            <th class="global-expert-rating  filter-block-can-order-by" data-filter-block-order-field="global-expert-rating"><span class="header"></span><span class="filter-block-order-direction"></span></th>
<?php endif;
    if($showinfo): ?>
            <th class="info"></th>
<?php endif; ?>
        </tr>
    </thead>
    <tfoot>
        <tr>
<?php if($customnameaction): ?>
            <th class="custom-add"></th>
<?php endif; ?>
            <th class="date filter-block-can-order-by" data-filter-block-order-field="date"><?=langTranslate('Start','Start')?><span class="filter-block-order-direction"></span></th>
            <th class="name filter-block-can-order-by" data-filter-block-order-field="name"><?=langTranslate('Name','Name')?><span class="filter-block-order-direction"></span></th>
<?php if($showstatus): ?>
            <th class="status filter-block-can-order-by" data-filter-block-order-field="status"><?=langTranslate('Status','Status')?><span class="filter-block-order-direction"></span></th>
<?php endif;
    if($showattendanceresponse): ?>
            <th class="attendance-response"></th>
<?php endif;
    if($showprice): ?>
            <th class="price"><?=langTranslate('Price','Price')?></th>
<?php endif;
    if($showpendingreviewcount): ?>
            <th class="pending-review-count"></th>
<?php endif;
    if($global_expert_ratings_for_user): ?>
            <th class="global-expert-rating  filter-block-can-order-by" data-filter-block-order-field="global-expert-rating"><span class="header"></span><span class="filter-block-order-direction"></span></th>
<?php endif;
    if($showinfo): ?>
            <th class="info"></th>
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
<?php langClean('tasting', 'tasting')?>