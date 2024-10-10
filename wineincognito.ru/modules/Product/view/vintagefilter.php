<?php 
    langSetDefault('product', 'vintage');
    $vintageurl = BASE_URL.'/vintage/{{id}}';
    $reviewmergeurl = BASE_URL.'/vintage/{{id}}/reviewmerge/{{expert_level}}';
    $certificateurl = null;
    $vintageurl_target_blank = true;
    $autosearch = false;
    $showscore = true;
    $show_only_scored_filter_option = true;
    $show_only_awarded_filter_option = true;
    $showyear = true;
    if(!isset($can_add)){
        $can_add = false;
    }
    if(!isset($show_only_personally_scored_filter_option)){
        $show_only_personally_scored_filter_option = false;    
    }
    if(!isset($only_scored)){
        $only_scored = false;
    }
    if(!isset($contest_id)){
        $contest_id = false;
    }
    if(!isset($contest_user_id)){
        $contest_user_id = false;
    }
    if(!isset($can_view_certificates)){
        $can_view_certificates = false;
    }
    if(!isset($tasting_list)){
        $tasting_list = array();
    }
    $can_view_review_merge = true;
    if(!isset($showfavourite)){
        $showfavourite = false;
    }
    if(!isset($showcompanyfavourite)){
        $showcompanyfavourite = false;
    }
    if(!isset($onlyblank)){
        $onlyblank = false;
    }
    if(!isset($only_waiting_for_approval)){
        $only_waiting_for_approval = false;
    }
    if(!isset($show_all_scores)){
        $show_all_scores = false;
    }
    if(!isset($showpersonalscore)){
        $showpersonalscore = false;
    }
    if(!isset($can_view_score_details)){
        $can_view_score_details = false;
    }
    if(!isset($customnameaction)){
        $customnameaction = false;
    }
    $customnameurl = false;
    if($onlyblank){
        $showscore = false;
        $show_only_scored_filter_option = false;
        $show_only_awarded_filter_option = false;
        $show_only_personally_scored_filter_option = false;
        $showyear = false;
    }
    if($only_waiting_for_approval){
        $showscore = false;
        $autosearch = true;
        $can_add = false;
        $vintageurl_target_blank = false;
    }
    if(isset($translations_only) && $translations_only){
        $customnameurl = true;
        $vintageurl = BASE_URL.'/translation/vintage/{{id}}';
        $can_add = false;
        $autosearch = true;
    }
    if(isset($myreviews_only) && $myreviews_only){
        $customnameurl = true;
        $vintageurl = BASE_URL.'/myreview/product/{{id}}/tasting/filter';
        $vintageurl_target_blank = false;
        $show_only_scored_filter_option = false;
        $show_only_personally_scored_filter_option = false;
        $can_add = false;
        $autosearch = true;
        $showscore = true;
        $show_all_scores = false;
        $can_view_score_details = false;
        $can_view_review_merge = false;
    }
    $show_evaluation_scores = false;
    if($contest_id){
        $customnameurl = true;
        $vintageurl = BASE_URL.'/contest/'.$contest_id.'/stats/product/{{id}}';
        if($contest_user_id){
            $show_evaluation_scores = true;
            $reviewmergeurl = BASE_URL.'/contest/'.$contest_id.'/stats/product/{{id}}/user/'.$contest_user_id.'/reviewmerge/{{expert_level}}';
        } else {
            $reviewmergeurl = BASE_URL.'/contest/'.$contest_id.'/stats/product/{{id}}/reviewmerge/{{expert_level}}';
        }
        
        $certificateurl = BASE_URL.'/contest/'.$contest_id.'/product/{{id}}/certificate';
        $vintageurl_target_blank = false;
        $show_only_scored_filter_option = true;
        $show_only_personally_scored_filter_option = true;
        $can_add = false;
        $autosearch = true;
        $showscore = true;
        $show_all_scores = true;
        $can_view_score_details = false;
        $can_view_review_merge = true;
    }
    if(!$certificateurl){
        $can_view_certificates = false;
    }
    if(strlen(getPostVal('vintage_search_text',''))){
        $autosearch = true;
    }
    $colcount = 5+($showyear?1:0)+($show_evaluation_scores?2:0)+($showscore?($show_all_scores?3:1)+($showpersonalscore?1:0)+($can_view_score_details?1:0):0)+($showfavourite?1:0)+($showcompanyfavourite?1:0)+($customnameaction?1:0)+(($customnameaction||$customnameurl)?1:0)+($can_view_certificates?1:0);
?>
<div class="filter-block vintage-filter <?=$onlyblank?'only-blank':''?> <?=isset($tastingmodal)&&$tastingmodal?'tasting-modal':''?> <?=isset($translations_only)&&$translations_only?'translations-only':''?> <?=$autosearch?'auto-search':''?>"><form class="filter-form" <?=$onlyblank?'data-dropbox-filter-only-blank="1"':''?> <?=$only_waiting_for_approval?'data-dropbox-filter-only-waiting-for-approval="1"':''?> data-dropbox-filter-only-used="1" data-dropbox-filter-show-proximity="1" <?=$only_scored?'data-dropbox-filter-only-scored="1"':''?>><input type="hidden" name="action" value="vintage_filter" /><input type="hidden" class="filter-url" value="<?=BASE_URL?>/ajax/vintage/search" />
<?php if(isset($translations_only) && $translations_only): ?>
<input type="hidden" name="onlytranslations" value="1" />
<?php endif; ?>
<?php if($onlyblank): ?>
<input type="hidden" name="onlyblank" value="1" />
<?php endif; ?>
<?php if($only_waiting_for_approval): ?>
<input type="hidden" name="only_waiting_for_approval" value="1" />
<?php endif; ?>
<?php if(isset($myreviews_only) && $myreviews_only): ?>
<input type="hidden" name="onlymyreviews" value="1" />
<?php endif; ?>
<?php if($contest_id): ?>
<input type="hidden" name="only_from_contest" value="<?=(int)$contest_id?>" />
<?php   if($contest_user_id): ?>
<input type="hidden" name="only_from_contest_participant" value="<?=(int)$contest_user_id?>" />
<?php   endif;
    endif; ?>
<script type="template" class="dropbox-template-select">
<tr><td class="label">{ifdef{name}}<label>{{name}}</label>{endifdef{name}}</td><td><div class="dropbox multiple" data-fieldname="{{fieldname}}" data-group="{{group}}" data-has-children="{{haschildren}}" data-depth="{{depth}}" data-index="{{index}}" data-system="{{system}}" data-loadsiblings="1"><input type="checkbox" id="dropbox-filter-{{fieldname}}-{{group}}-{{depth}}" /><label for="dropbox-filter-{{fieldname}}-{{group}}-{{depth}}"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul><li class="search"><input type="text" /></li>{{options}}</ul></div></td></tr>
</script>
<script type="template" class="dropbox-template-option">
<li class="item {if{selected}}selected{endif{selected}} {!if{important}}not-important{end!if{important}}"><label><input type="checkbox" data-attr-id="{{attrId}}" name="{{fieldname}}[]" value="{{id}}" {if{selected}}checked{endif{selected}} data-se-text=" {{setext}}" /><span></span>{{name}}{if{score}} ({{score}}){endif{score}}</label></li>
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
<tr class=""><td class="label"><label for="vintage-filter-form-search_text"><?=langTranslate('Search', 'Search');?></label></td><td><input type="text" name="search_text" id="vintage-filter-form-search_text" value="<?=getPostVal('vintage_search_text','')?>" /></td></tr>
<tr class="non-blank"><td class="label"><label for="vintage-filter-form-year_from"><?=langTranslate('Year', 'Year');?></label></td><td><span class="form-from-to"><label for="vintage-filter-form-year_from"><?=langTranslate('From', 'From');?></label><input type="text" name="year_from" class="vintage-filter-form-year" id="vintage-filter-form-year_from" value="" /><label for="vintage-filter-form-year_to"><?=langTranslate('To', 'To');?></label><input type="text" name="year_to" class="vintage-filter-form-year" id="vintage-filter-form-year_to" value="" /></span></td></tr>
<tr class="non-blank"><td class="label"><label for="vintage-filter-form-score_from"><?=langTranslate('Score', 'Score');?></label></td><td><span class="form-from-to"><label for="vintage-filter-form-score_from"><?=langTranslate('From', 'From');?></label><input type="text" name="score_from" class="vintage-filter-form-score" id="vintage-filter-form-score_from" value="" /><label for="vintage-filter-form-score_to"><?=langTranslate('To', 'To');?></label><input type="text" name="score_to" class="vintage-filter-form-score" id="vintage-filter-form-score_to" value="" /></span></td></tr>
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
<tr><td class="label"><?=(count($depthvaltree)==1)?'<label>'.htmlentities($depthvaltree[$first_key]['name']).'</label>':'';?></td><td><div class="dropbox fresh <?=count($depthvaltree)==1&&!$depthvaltree[$first_key]['can_null']&&count($depthvaltree[$first_key]['vals'])==1?'disabled':''?> multiple" data-fieldname="attr" data-group="<?=$group?>" data-has-children="<?=$haschildren?>" data-depth="<?=$depth?>" data-index="<?=$depthvaltree[$first_key]['index']?>" data-system="<?=$depthvaltree[$first_key]['system']?>" data-loadsiblings="1"><input type="checkbox" id="dropbox-filter-attr-<?=$group?>-<?=$depth?>" /><label for="dropbox-filter-attr-<?=$group?>-<?=$depth?>"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul><li class="search"><input type="text" /></li>
<?php   foreach($depthvaltree as $attr):?>
<?php       if(count($depthvaltree)>1): ?>
<li class="header"><?=htmlentities($attr['name'])?></li>
<?php       endif; ?>
<?php       foreach($attr['vals'] as $val): ?>
    <li class="item <?=$val['selected']?'selected':''?> <?=(!$val['important'])?'not-important':''?>"><label><input type="checkbox" data-attr-id="<?=$attr['id']?>" name="attr[]" value="<?=$val['id']?>" <?=$val['selected']?'checked':''?> <?=$val['setext']?'data-se-text=" '.$val['setext'].'"':''?> /><span></span><?=htmlentities($val['name']).($val['score']?' ('.$val['score'].')':'')?></label></li>
<?php       endforeach; ?>
</ul></div></td></tr>
<?php   endforeach; ?>
<?php endforeach; ?>
<?php endforeach; ?>
<tr><td class="label"><label for="vintage-filter-form-alcohol-content_from"><?=langTranslate('Alcohol Content', 'Alcohol Content');?></label></td><td><span class="form-from-to"><label for="vintage-filter-form-alcohol-content_from"><?=langTranslate('From', 'From');?></label><input type="text" name="alcohol_content_from" class="vintage-filter-form-alcohol-content" id="vintage-filter-form-alcohol-content_from" value="" /><label for="vintage-filter-form-alcohol-content_to"><?=langTranslate('To', 'To');?></label><input type="text" name="alcohol_content_to" class="vintage-filter-form-alcohol-content" id="vintage-filter-form-alcohol-content_to" value="" /></span></td></tr>
<?php if(!empty($tasting_list)): ?>
<tr><td class="label"><label><?=langTranslate('Tastings', 'Tastings');?></label></td><td><div class="dropbox multiple" data-fieldname="tasting" data-custom="1"><input type="checkbox" id="dropbox-filter-tasting" /><label for="dropbox-filter-tasting"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul><li class="search"><input type="text" /></li>
<?php   foreach($tasting_list as $tasting):?>
    <li class="item"><label><input type="checkbox" name="tasting[]" value="<?=$tasting['id']?>" /><span></span><?=htmlentities($tasting['name'])?></label></li>
<?php   endforeach; ?>
</ul></div></td></tr>
<?php endif; ?>
<?php if($only_scored): ?>
<input type="hidden" name="onlyscored" value="1" />
<?php else: ?>
<?php   if($show_only_scored_filter_option): ?>
<tr><td></td><td><label class="radio"><input type="checkbox" name="onlyscored" value="1"><span></span><?=langTranslate('Only scored', 'Only scored');?></label></td></tr>
<?php   endif;
    endif; 
    if($show_only_awarded_filter_option): ?>
<tr><td></td><td><label class="radio"><input type="checkbox" name="onlyawarded" value="1"><span></span><?=langTranslate('Only awarded', 'Only awarded');?></label></td></tr>
<?php endif; 
    if($show_only_personally_scored_filter_option): ?>
<tr><td></td><td><label class="radio"><input type="checkbox" name="only_personally_scored" value="1"><span></span><?=langTranslate('Only personally scored', 'Only personally scored');?></label></td></tr>
<?php endif; ?>
<?php if($showcompanyfavourite): ?>
<tr><td></td><td><label class="radio"><input type="checkbox" name="only_company_favourite" value="1"><span></span><?=langTranslate('Only company favourites', 'Only company favourites');?></label></td></tr>
<?php endif; ?>
<?php if($showfavourite): ?>
<tr><td></td><td><label class="radio"><input type="checkbox" name="only_favourite" value="1"><span></span><?=langTranslate('Only my favourites', 'Only my favourites');?></label></td></tr>
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
<script language="JavaScript">
$(".vintage-filter .filter-form .vintage-filter-form-year").mask("#999");
$(".vintage-filter .filter-form .vintage-filter-form-score").mask('99,99');
$(".vintage-filter .filter-form .vintage-filter-form-alcohol-content").mask('99,99');
</script>

<script type="template" class="datalist-item-template">
<tr class="item {if{favourite}}favourite{endif{favourite}} {if{company_favourite}}company-favourite{endif{company_favourite}}" data-id="{{id}}" data-pid="{{pid}}">
<?php if($customnameaction): ?>
<td class="custom-add"><span></span></td>
<?php endif;
    if($showcompanyfavourite): ?>
<td class="company-favourite {if{can_company_favourite}}can-favourite{endif{can_company_favourite}}"><span></span></td>
<?php endif; ?>
<?php if($showfavourite): ?>
<td class="favourite {if{can_favourite}}can-favourite{endif{can_favourite}}"><span></span></td>
<?php endif; ?>
<td class="nomination-winner">{if{nomination_winner}}<span class="tooltip-right" data-tooltip="<?=langTranslate('Has contest awards','Has contest awards')?>"></span>{endif{nomination_winner}}</td><td class="image wi-gallery">{if{img}}<img src="{{img}}" />{endif{img}}</td><td class="color {if{color}}color-{{color}}{endif{color}} {if{color_caption}}tooltip-right{endif{color_caption}}" {if{color_caption}}data-tooltip="{{color_caption}}"{endif{color_caption}}><span></span></td>
<?php if($showyear): ?>
<td class="year">{{year}}</td>
<?php endif; ?>
<td class="name"><a <?=!$customnameaction?'href="'.$vintageurl.'"'.($vintageurl_target_blank?' target="_blank"':''):''?>>{{name}}{if{awaiting_approval}}<span class="awaiting-approval"><?=langTranslate('Awaiting approval','Awaiting approval')?></span>{endif{awaiting_approval}}</a></td><td></td>
<?php if($show_evaluation_scores): ?>
<td class="score">{{automatic_evaluation_score}}</td><td class="score">{{manual_evaluation_score}}</td>
<?php endif;
    if($showscore): 
        if($show_all_scores):
?>
<td class="score">{if{score1}}<?php if($can_view_review_merge):?>{if{can_view_review_merge}}<a href="<?=str_replace('{{expert_level}}','1',$reviewmergeurl)?>">{endif{can_view_review_merge}}<?php endif; ?>{{score1}}<?php if($can_view_review_merge):?>{if{can_view_review_merge}}</a>{endif{can_view_review_merge}}<?php endif; ?>{endif{score1}}</td><td class="score">{if{score2}}<?php if($can_view_review_merge):?>{if{can_view_review_merge}}<a href="<?=str_replace('{{expert_level}}','2',$reviewmergeurl)?>">{endif{can_view_review_merge}}<?php endif; ?>{{score2}}<?php if($can_view_review_merge):?>{if{can_view_review_merge}}</a>{endif{can_view_review_merge}}<?php endif; ?>{endif{score2}}</td><td class="score">{if{score3}}<?php if($can_view_review_merge):?>{if{can_view_review_merge}}<a href="<?=str_replace('{{expert_level}}','3',$reviewmergeurl)?>">{endif{can_view_review_merge}}<?php endif; ?>{{score3}}<?php if($can_view_review_merge):?>{if{can_view_review_merge}}</a>{endif{can_view_review_merge}}<?php endif; ?>{endif{score3}}</td>
<?php   else: ?>
<td class="score">{if{score3}}<?php if($can_view_review_merge):?>{if{can_view_review_merge}}<a href="<?=str_replace('{{expert_level}}','3',$reviewmergeurl)?>">{endif{can_view_review_merge}}<?php endif; ?>{{score3}}<?php if($can_view_review_merge):?>{if{can_view_review_merge}}</a>{endif{can_view_review_merge}}<?php endif; ?>{endif{score3}}</td>
<?php   endif;  
        if($showpersonalscore): ?>
<td class="score">{if{personal_score}}<?php if($can_view_review_merge):?>{if{can_view_review_merge}}<a href="<?=str_replace('{{expert_level}}','personal',$reviewmergeurl)?>">{endif{can_view_review_merge}}<?php endif; ?>{{personal_score}}<?php if($can_view_review_merge):?>{if{can_view_review_merge}}</a>{endif{can_view_review_merge}}<?php endif; ?>{endif{personal_score}}</td>
<?php   endif;
        if($can_view_certificates): ?>
<td class="certificate-view">{if{can_view_certificate}}<a href="<?=$certificateurl?>" class="non-sticky-tooltip" data-tooltip="<?=langTranslate('tasting', 'contest', 'Tooltip: Certificate','Certificate')?>" target="_blank"></a>{endif{can_view_certificate}}</td>
<?php   endif; 
        if($can_view_score_details): ?>
<td class="score-details">{if{has_score}}<a href="<?=BASE_URL?>/vintage/{{id}}/scoredetails" target="_blank"></a>{endif{has_score}}</td>
<?php   endif; 
    endif; ?>
<?php if($customnameaction||$customnameurl): ?>
<td class="info"><a href="<?=BASE_URL?>/vintage/{{id}}" target="_blank"></a></td>
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
        <tr class="pagination"><th colspan="<?=$colcount?>"><ul class="pagination"></ul></th></tr>
        <tr>
<?php if($customnameaction): ?>
            <th class="custom-add"></th>
<?php endif;
    if($showcompanyfavourite): ?>
            <th class="company-favourite"></th>
<?php endif; ?>
<?php if($showfavourite): ?>
            <th class="favourite"></th>
<?php endif; ?>
            <th class="nomination-winner"></th><th class="image"></th><th class="color"></th>
<?php if($showyear): ?>
            <th class="filter-block-can-order-by" data-filter-block-order-field="year"><?=langTranslate('Year','Year')?><span class="filter-block-order-direction"></span></th>
<?php endif; ?>
            <th class="filter-block-can-order-by" data-filter-block-order-field="name"><?=langTranslate('Name','Name')?><span class="filter-block-order-direction"></span></th><th class="separator"></th>
<?php if($show_evaluation_scores): ?>
            <th class="score automatic-evaluation filter-block-can-order-by" data-filter-block-order-field="automatic-evaluation"><span class="header non-sticky-tooltip" data-tooltip="<?=langTranslate('tasting', 'expert evaluation', 'Automatic evaluation','Automatic evaluation')?>"></span><span class="filter-block-order-direction"></span></th><th class="score manual-evaluation filter-block-can-order-by" data-filter-block-order-field="manual-evaluation"><span class="header non-sticky-tooltip" data-tooltip="<?=langTranslate('tasting', 'expert evaluation', 'Manual evaluation','Manual evaluation')?>"></span><span class="filter-block-order-direction"></span></th>
<?php endif;
    if($showscore): 
        if($show_all_scores): ?>
            <th class="score score1 filter-block-can-order-by" data-filter-block-order-field="score1"><span class="header non-sticky-tooltip" data-tooltip="<?=$expert_level_list[1]?>"></span><span class="filter-block-order-direction"></span></th><th class="score score2 filter-block-can-order-by" data-filter-block-order-field="score2"><span class="header non-sticky-tooltip" data-tooltip="<?=$expert_level_list[2]?>"></span><span class="filter-block-order-direction"></span></th><th class="score score3 filter-block-can-order-by" data-filter-block-order-field="score3"><span class="header non-sticky-tooltip" data-tooltip="<?=$expert_level_list[3]?>"></span><span class="filter-block-order-direction"></span></th>
<?php   else: ?>
            <th class="score score3 filter-block-can-order-by" data-filter-block-order-field="score3"><span class="header non-sticky-tooltip" data-tooltip="<?=$expert_level_list[3]?>"></span><span class="filter-block-order-direction"></span></th>
<?php   endif;
        if($showpersonalscore): ?>
            <th class="score score-personal filter-block-can-order-by" data-filter-block-order-field="score-personal"><span class="header non-sticky-tooltip" data-tooltip="<?=langTranslate('user','Expert Level','Personal','Personal')?>"></span><span class="filter-block-order-direction"></span></th>
<?php   endif;
        if($can_view_certificates): ?>
            <th class="certificate-view"></th>
<?php   endif; 
        if($can_view_score_details): ?>
            <th class="score-details"></th>
<?php   endif; 
    endif; 
    if($customnameaction||$customnameurl): ?>
            <th class="info"></th>
<?php endif; ?>
        </tr>
    </thead>
    <tfoot>
        <tr>
<?php if($customnameaction): ?>
            <th class="custom-add"></th>
<?php endif;
    if($showcompanyfavourite): ?>
            <th class="company-favourite"></th>
<?php endif; ?>
<?php if($showfavourite): ?>
            <th class="favourite"></th>
<?php endif; ?>
            <th class="nomination-winner"></th><th class="image"></th><th class="color"></th>
<?php if($showyear): ?>
            <th class="filter-block-can-order-by" data-filter-block-order-field="year"><?=langTranslate('Year','Year')?><span class="filter-block-order-direction"></span></th>
<?php endif; ?>
            <th class="filter-block-can-order-by" data-filter-block-order-field="name"><?=langTranslate('Name','Name')?><span class="filter-block-order-direction"></span></th><th class="separator"></th>
<?php if($show_evaluation_scores): ?>
            <th class="score automatic-evaluation filter-block-can-order-by" data-filter-block-order-field="automatic-evaluation"><span class="header non-sticky-tooltip" data-tooltip="<?=langTranslate('tasting', 'expert evaluation', 'Automatic evaluation','Automatic evaluation')?>"></span><span class="filter-block-order-direction"></span></th><th class="score manual-evaluation filter-block-can-order-by" data-filter-block-order-field="manual-evaluation"><span class="header non-sticky-tooltip" data-tooltip="<?=langTranslate('tasting', 'expert evaluation', 'Manual evaluation','Manual evaluation')?>"></span><span class="filter-block-order-direction"></span></th>
<?php endif;
    if($showscore): 
        if($show_all_scores): ?>
            <th class="score score1 filter-block-can-order-by" data-filter-block-order-field="score1"><span class="header non-sticky-tooltip" data-tooltip="<?=$expert_level_list[1]?>"></span><span class="filter-block-order-direction"></span></th><th class="score score2 filter-block-can-order-by" data-filter-block-order-field="score2"><span class="header non-sticky-tooltip" data-tooltip="<?=$expert_level_list[2]?>"></span><span class="filter-block-order-direction"></span></th><th class="score score3 filter-block-can-order-by" data-filter-block-order-field="score3"><span class="header non-sticky-tooltip" data-tooltip="<?=$expert_level_list[3]?>"></span><span class="filter-block-order-direction"></span></th>
<?php   else: ?>
            <th class="score score3 filter-block-can-order-by" data-filter-block-order-field="score3"><span class="header non-sticky-tooltip" data-tooltip="<?=$expert_level_list[3]?>"></span><span class="filter-block-order-direction"></span></th>
<?php   endif;
        if($showpersonalscore): ?>
            <th class="score score-personal filter-block-can-order-by" data-filter-block-order-field="score-personal"><span class="header non-sticky-tooltip" data-tooltip="<?=langTranslate('user','Expert Level','Personal','Personal')?>"></span><span class="filter-block-order-direction"></span></th>
<?php   endif;
        if($can_view_certificates): ?>
            <th class="certificate-view"></th>
<?php   endif; 
        if($can_view_score_details): ?>
            <th class="score-details"></th>
<?php   endif; 
    endif; 
    if($customnameaction||$customnameurl): ?>
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
<?php langClean('product', 'vintage')?>