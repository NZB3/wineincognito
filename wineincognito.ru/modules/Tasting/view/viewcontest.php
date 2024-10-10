<?php 
    langSetDefault('tasting', 'contest');
    $hasimage = (strlen($contestinfo['logourl'])>0);
    $showdaterows = $contestinfo['startts']?(date('d.m.Y',$contestinfo['startts'])!=date('d.m.Y',$contestinfo['endts'])?2:1):0;
    $cols = 5 + $showdaterows;
?>
<table class="subcontent view-tasting-contest <?=(isset($compact)&&$compact)?'compactable compact':''?>" data-tc-id="<?=$contestinfo['id']?>">
<thead>
<?php if(!isset($shortform) || !$shortform):?>
    <tr class="head-buttons"><th colspan="<?=$hasimage?3:2?>">
        <a class="mainbtn back" href="<?=BASE_URL?>/contests"><?=langTranslate('Contests','Contests')?></a>
        <span class="rightbar">
<?php if(isset($contestinfo['can_view_statistics'])&&$contestinfo['can_view_statistics']): ?>
            <a class="view-statistics" href="<?=BASE_URL?>/contest/<?=$contestinfo['id']?>/stats"></a>
<?php endif; ?>
<?php if(isset($contestinfo['can_assess'])&&$contestinfo['can_assess']): ?>
            <span class="assess approve"></span><span class="assess deny"></span>
<?php endif; ?>
            <script type="string" class="confirm_string_change_status"><?=formatReplace(langTranslate('Are you sure you want to change this contest\'s status to @1?','Are you sure you want to change this contest\'s status to @1?'),'{{name}}')?></script>
<?php if(isset($contestinfo['can_delete'])&&$contestinfo['can_delete']): ?>
            <script type="string" class="confirm_string_delete"><?=langTranslate('Are you sure you want to delete this contest?','Are you sure you want to delete this contest?')?></script>
            <span class="change-status delete"></span>
<?php endif; ?>
<?php if(isset($contestinfo['can_change_to_draft'])&&$contestinfo['can_change_to_draft']): ?>
            <span class="change-status draft" data-status="<?=\TASTING\CONTEST_STATUS_DRAFT?>" data-status-text="<?=$status_list[\TASTING\CONTEST_STATUS_DRAFT]?>"></span>
<?php endif; ?>
<?php if(isset($contestinfo['can_change_to_preparation'])&&$contestinfo['can_change_to_preparation']): ?>
            <span class="change-status preparation" data-status="<?=\TASTING\CONTEST_STATUS_PREPARATION?>" data-status-text="<?=$status_list[\TASTING\CONTEST_STATUS_PREPARATION]?>"></span>
<?php endif; ?>
<?php if(isset($contestinfo['can_change_to_summing_up'])&&$contestinfo['can_change_to_summing_up']): ?>
            <span class="change-status summing-up" data-status="<?=\TASTING\CONTEST_STATUS_SUMMING_UP?>" data-status-text="<?=$status_list[\TASTING\CONTEST_STATUS_SUMMING_UP]?>"></span>
<?php endif; ?>
<?php if(isset($contestinfo['can_change_to_finished'])&&$contestinfo['can_change_to_finished']): ?>
            <span class="change-status finish" data-status="<?=\TASTING\CONTEST_STATUS_FINISHED?>" data-status-text="<?=$status_list[\TASTING\CONTEST_STATUS_FINISHED]?>"></span>
<?php endif; ?>
<?php if(isset($contestinfo['can_edit'])&&$contestinfo['can_edit']): ?>
            <a class="edit" href="<?=BASE_URL?>/contest/<?=$contestinfo['id']?>/edit"></a>
<?php endif; ?>
        </span>
    </th></tr>
<?php endif; ?>
    <tr class="header"><th colspan="<?=$hasimage?3:2?>"><?=htmlentities($contestinfo['name'])?></th></tr>
</thead>
<tbody>
<tr>
<?php if($hasimage):?>
    <td class="image" rowspan="<?=$cols?>"><img src="<?=$contestinfo['logourl']?>" alt="Contest logo" /></td>
<?php endif; ?>
    <td class="label"><label><?=langTranslate('ID','ID')?></label></td><td class="value"><?=$contestinfo['id']?></td></tr>
<tr><td class="label"><label><?=langTranslate('Status','Status')?></label></td><td class="value"><?=htmlentities($contestinfo['status_text'])?></td></tr>
<?php
    $assessment_text = '';
    switch($contestinfo['assessment']){
        case 0:
            $assessment_text = langTranslate('Assessment - Private', 'Private');
            break;
        case 1:
            $assessment_text = langTranslate('Assessment - Public', 'Public');
            break;
    }
?>
<tr><td class="label"><label><?=langTranslate('Assessment', 'Assessment');?></label></td><td class="value"><?=$assessment_text?></td></tr>
<tr><td class="label"><label><?=langTranslate('Location', 'Location');?></label></td><td class="value"><?=htmlentities($contestinfo['location'])?></td></tr>
<?php if($showdaterows): 
        if($showdaterows==2): ?>
<tr><td class="label"><label><?=langTranslate('Start','Start')?></label></td><td class="value"><?=date('d.m.Y',$contestinfo['startts'])?></td></tr>
<tr><td class="label"><label><?=langTranslate('End','End')?></label></td><td class="value"><?=date('d.m.Y',$contestinfo['endts'])?></td></tr>
<?php   else: ?>
<tr><td class="label"><label><?=langTranslate('Date','Date')?></label></td><td class="value"><?=date('d.m.Y',$contestinfo['startts'])?></td></tr>
<?php   endif; 
    endif;
?>
<tr><td class="label"><label><?=langTranslate('Description','Description')?></label></td><td class="value description"><?=prepareMultilineValue($contestinfo['desc'])?></td></tr>
<?php if($hasimage):?>
    <tr class="lastrow"><td colspan="3"></td></tr>
<?php endif; ?>
</tbody></table>
<?php langClean('tasting', 'contest')?>
