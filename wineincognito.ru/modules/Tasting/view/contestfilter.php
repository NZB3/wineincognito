<?php 
    langSetDefault('tasting', 'contest'); 

    $showinfo = false;
    $showstatus = true;
    $contesturl = BASE_URL.'/contest/{{id}}';
    $conteststaturl = BASE_URL.'/contest/{{id}}/stats';

    $autosearch = true;
    $default_status = array();
    $only_owned_default = false;
    $only_approved_default = true;
    if(!isset($can_add)){
        $can_add = false;
    }
    if(!isset($only_for_assessment)){
        $only_for_assessment = false;
    }
    if($only_for_assessment){
        $can_add = false;
        $autosearch = true;
        $statuslist = array();
    }
    $colcount = 2 + ($showstatus?1:0) + ($showinfo?1:0);
?>
<div class="subcontent filter-block contest-filter <?=$autosearch?'auto-search':''?>">
<form class="filter-form"><input type="hidden" name="action" value="contest_filter" /><input type="hidden" class="filter-url" value="<?=BASE_URL?>/ajax/contest/search" />
<?php if($only_for_assessment): ?>
<input type="hidden" name="only_for_assessment" value="1" />
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
<tr><td class="label"><label for="contest-filter-form-start-date-from"><?=langTranslate('Start', 'Start');?></label></td><td>
    <span class="form-date-from-to">
        <label for="contest-filter-form-start-date-from"><?=langTranslate('From', 'From');?></label>
        <input type="text" name="start_date_from" id="contest-filter-form-start-date-from" value="" maxlength="10" />
        <label for="contest-filter-form-start-date-to"><?=langTranslate('To', 'To');?></label>
        <input type="text" name="start_date_to" id="contest-filter-form-start-date-to" value="" maxlength="10" />
        <span></span>
    </span>
</td></tr>
<?php if(!empty($statuslist)): ?>
<tr><td class="label"><label for="dropbox-custom-contest-filter-status"><?=langTranslate('Status', 'Status');?></label></td><td><div class="dropbox fresh multiple" data-custom="1"><input type="checkbox" id="dropbox-custom-contest-filter-status" /><label for="dropbox-custom-contest-filter-status"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul>
<?php   foreach($statuslist as $status=>$caption): ?>
    <li class="item <?=in_array($status, $default_status)?'selected':''?>"><label><input type="checkbox" name="status[]" value="<?=$status?>" <?=in_array($status, $default_status)?'checked="checked"':''?> /><span></span><?=htmlentities($caption)?></label></li>
<?php   endforeach; ?>
</ul></div></td></tr>
<?php endif; ?>
<tr><td></td><td><label class="radio"><input type="checkbox" name="only_owned" value="1" <?=$only_owned_default?'checked="checked"':''?>><span></span><?=langTranslate('Only owned', 'Only owned');?></label></td></tr>
<tr><td></td><td><label class="radio"><input type="checkbox" name="only_organized" value="1"><span></span><?=langTranslate('Only organized by me', 'Only organized by me');?></label></td></tr>
<?php if(!$only_for_assessment): ?>
<tr><td></td><td><label class="radio"><input type="checkbox" name="only_approved" value="1" <?=$only_approved_default?'checked="checked"':''?>><span></span><?=langTranslate('Only approved', 'Only approved');?></label></td></tr>
<?php endif; ?>
<tr><td></td><td><label class="radio"><input type="checkbox" name="only_participated" value="1"><span></span><?=langTranslate('Participated as expert', 'Participated as expert');?></label></td></tr>
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
<tr class="item"><td class="date">{{date}}</td><td class="name"><a href="{if{lead_to_stats}}<?=$conteststaturl?>{endif{lead_to_stats}}{!if{lead_to_stats}}<?=$contesturl?>{end!if{lead_to_stats}}">{{name}}</a></td>
<?php if($showstatus): ?>
<td class="status">{{status}}</td>
<?php endif;
    if($showinfo): ?>
<td class="info"><a href="<?=BASE_URL?>/contest/{{id}}"></td>
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
            <a class="add" href="<?=BASE_URL?>/contest/add"></a>
<?php endif; ?>
        </th></tr>
        <tr class="pagination"><th colspan="<?=$colcount?>"><ul class="pagination"></ul></th></tr>
        <tr>
            <th class="date filter-block-can-order-by" data-filter-block-order-field="date"><?=langTranslate('Start','Start')?><span class="filter-block-order-direction"></span></th>
            <th class="name filter-block-can-order-by" data-filter-block-order-field="name"><?=langTranslate('Name','Name')?><span class="filter-block-order-direction"></span></th>
<?php if($showstatus): ?>
            <th class="status filter-block-can-order-by" data-filter-block-order-field="status"><?=langTranslate('Status','Status')?><span class="filter-block-order-direction"></span></th>
<?php endif;
    if($showinfo): ?>
            <th class="info"></th>
<?php endif; ?>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th class="date filter-block-can-order-by" data-filter-block-order-field="date"><?=langTranslate('Start','Start')?><span class="filter-block-order-direction"></span></th>
            <th class="name filter-block-can-order-by" data-filter-block-order-field="name"><?=langTranslate('Name','Name')?><span class="filter-block-order-direction"></span></th>
<?php if($showstatus): ?>
            <th class="status filter-block-can-order-by" data-filter-block-order-field="status"><?=langTranslate('Status','Status')?><span class="filter-block-order-direction"></span></th>
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
<?php langClean('tasting', 'contest')?>