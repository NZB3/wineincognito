<?php 
    langSetDefault('tasting', 'tasting');
?>
<table class="subcontent viewTasting <?=(isset($compact)&&$compact)?'compactable compact':''?>" data-t-id="<?=$tastinginfo['id']?>">
<thead>
<?php if(!isset($shortform) || !$shortform):?>
    <tr class="head-buttons"><th colspan="2">
        <a class="mainbtn back" href="<?=BASE_URL?>/tastings"><?=langTranslate('Tastings','Tastings')?></a>
        <span class="rightbar">
<?php if(isset($tastinginfo['can_view_statistics'])&&$tastinginfo['can_view_statistics']): ?>
            <a class="view-statistics non-sticky-tooltip" data-tooltip="<?=langTranslate('Tooltip: Statistics','Statistics')?>" href="<?=BASE_URL?>/tasting/<?=$tastinginfo['id']?>/stats"></a>
<?php endif; ?>
<?php if(isset($tastinginfo['can_assess'])&&$tastinginfo['can_assess']): ?>
            <span class="assess approve non-sticky-tooltip" data-tooltip="<?=langTranslate('Tooltip: Approve','Approve')?>"></span><span class="assess deny non-sticky-tooltip" data-tooltip="<?=langTranslate('Tooltip: Deny','Deny')?>"></span>
<?php endif; ?>
<?php if(isset($tastinginfo['can_swap_reviews'])&&$tastinginfo['can_swap_reviews']): ?>
            <a class="swap-reviews non-sticky-tooltip" data-tooltip="<?=langTranslate('Tooltip: Swap reviews','Swap reviews')?>" href="<?=BASE_URL?>/tasting/<?=$tastinginfo['id']?>/swapreviews"></a>
<?php endif; ?>
<?php if(isset($tastinginfo['can_change_to_deleted'])&&$tastinginfo['can_change_to_deleted']): ?>
            <script type="string" class="confirm_string_change_status delete"><?=langTranslate('Are you sure you want to delete this tasting?','Are you sure you want to delete this tasting?')?></script>
            <span class="change-status delete non-sticky-tooltip" data-tooltip="<?=langTranslate('Tooltip: Delete tasting','Delete tasting')?> "data-status="<?=\TASTING\TASTING_STATUS_DELETED?>"></span>
<?php endif; ?>
<?php if(isset($tastinginfo['can_change_to_draft'])&&$tastinginfo['can_change_to_draft']): ?>
            <span class="change-status draft non-sticky-tooltip" data-tooltip="<?=langTranslate('Tooltip: Draft tasting','Draft tasting')?>"data-status="<?=\TASTING\TASTING_STATUS_DRAFT?>"></span>
<?php endif; ?>
<?php if(isset($tastinginfo['can_change_to_preparation'])&&$tastinginfo['can_change_to_preparation']): ?>
            <span class="change-status preparation non-sticky-tooltip" data-tooltip="<?=langTranslate('Tooltip: Change status to preparation','Change status to preparation')?>" data-status="<?=\TASTING\TASTING_STATUS_PREPARATION?>"></span>
<?php endif; ?>
<?php if(isset($tastinginfo['can_change_to_started'])&&$tastinginfo['can_change_to_started']): ?>
            <span class="change-status start non-sticky-tooltip" data-tooltip="<?=langTranslate('Tooltip: Start tasting','Start tasting')?>" data-status="<?=\TASTING\TASTING_STATUS_STARTED?>"></span>
<?php endif; ?>
<?php if(isset($tastinginfo['can_change_to_finished'])&&$tastinginfo['can_change_to_finished']): ?>
            <script type="string" class="confirm_string_change_status finish"><?=langTranslate('Are you sure you want to finish this tasting?','Are you sure you want to finish this tasting?')?></script>
            <span class="change-status finish non-sticky-tooltip" data-tooltip="<?=langTranslate('Tooltip: Finish tasting','Finish tasting')?>" data-status="<?=\TASTING\TASTING_STATUS_FINISHED?>"></span>
<?php endif; ?>
<?php if(isset($tastinginfo['can_edit'])&&$tastinginfo['can_edit']): ?>
            <a class="edit non-sticky-tooltip" data-tooltip="<?=langTranslate('Tooltip: Edit','Edit')?>" href="<?=BASE_URL?>/tasting/<?=$tastinginfo['id']?>/edit"></a>
<?php endif; ?>
        </span>
    </th></tr>
<?php endif; ?>
    <tr class="header"><th colspan="2"><?=$tastinginfo['name']?htmlentities($tastinginfo['name']):formatReplace(langTranslate('Tasting №@1 from @2',  'Tasting №@1 from @2'), $tastinginfo['id'], date('d.m.Y', $tastinginfo['startts']))?></th></tr>
</thead>
<tbody>
<tr><td class="label"><label><?=langTranslate('ID','ID')?></label></td><td class="value"><?=$tastinginfo['id']?></td></tr>
<tr><td class="label"><label><?=langTranslate('Status','Status')?></label></td><td class="value"><?=htmlentities($tastinginfo['status_text'])?></td></tr>
<?php
    $assessment_text = '';
    switch($tastinginfo['assessment']){
        case 0:
            $assessment_text = langTranslate('Assessment - Private', 'Private');
            break;
        case 1:
            $assessment_text = langTranslate('Assessment - Public', 'Public');
            break;
    }
?>
<tr><td class="label"><label><?=langTranslate('Assessment', 'Assessment');?></label></td><td class="value"><?=$assessment_text?></td></tr>
<?php
    $assessment_text = '';
    switch($tastinginfo['score_method']){
        case 0:
            $assessment_text = langTranslate('Score method - Review collection', 'Review collection');
            break;
        case 1:
            $assessment_text = langTranslate('Score method - Ranking collection', 'Ranking collection');
            break;
    }
?>
<tr><td class="label"><label><?=langTranslate('Score method', 'Score method');?></label></td><td class="value"><?=$assessment_text?></td></tr>
<?php
    $participation_text = '';
    switch($tastinginfo['participation']){
        case 0:
            $participation_text = langTranslate('Invite only', 'Invite only');
            break;
        case 1:
            if($tastinginfo['participation_rating']==0){
                $participation_text = langTranslate('Experts only', 'Experts only');
            } else {
                $participation_text = formatReplace(langTranslate('Experts with rating higher than @1', 'Experts with rating higher than @1'),
                    $tastinginfo['participation_rating']);
            }
            break;
        case 2:
            $participation_text = langTranslate('Public Tasting', 'Public Tasting');
            break;
    }
?>
<tr><td class="label"><label><?=langTranslate('Participation','Participation')?></label></td><td class="value"><?=$participation_text?></td></tr>
<tr><td class="label"><label><?=langTranslate('Location', 'Location');?></label></td><td class="value"><?=htmlentities($tastinginfo['location'])?></td></tr>
<tr><td class="label"><label><?=langTranslate('Start','Start')?></label></td><td class="value"><?=date('d.m.Y H:i',$tastinginfo['startts'])?></td></tr>
<tr><td class="label"><label><?=langTranslate('End','End')?></label></td><td class="value"><?=date('d.m.Y H:i',$tastinginfo['endts'])?></td></tr>
<tr><td class="label"><label><?=langTranslate('Duration','Duration')?></label></td><td class="value"><?php
    $durationseconds = $tastinginfo['endts'] - $tastinginfo['startts'];
    $hours = floor($durationseconds/3600);
    if($hours>0){
        echo $hours.' '.langTranslate('h.','h.');
    }
    $minutes = ceil(($durationseconds%3600)/60);
    if($minutes>0){
        echo ' '.$minutes.' '.langTranslate('m.','m.');
    }

?></td></tr>
<?php if($tastinginfo['chargeability']&&!empty($tastinginfo['pricegrid'])): ?>
<tr><td class="label"><label><?=langTranslate('Price grid', 'Price grid');?></label></td><td class="pricegrid">
<table><tbody>
<tr><td class="label"><label><?=langTranslate('Guest', 'Guest');?></label></td><td><?=htmlentities(formatPrice($tastinginfo['pricegrid']['guest_price']))?></td></tr>
<tr><td class="label"><label><?=langTranslate('Expert', 'Expert');?></label></td><td><?=htmlentities(formatPrice($tastinginfo['pricegrid']['expert_price']))?></td></tr>
<tr><td class="label"><label><?=formatReplace(langTranslate('Experts with rating higher than @1', 'Experts with rating higher than @1'),$tastinginfo['pricegrid']['rated_expert_rating']);?></td><td><?=htmlentities(formatPrice($tastinginfo['pricegrid']['rated_expert_price']))?></td></tr>
</tbody></table></td></tr>
<?php endif; ?>
<tr><td class="label"><label><?=langTranslate('Description','Description')?></label></td><td class="value description"><?=prepareMultilineValue($tastinginfo['desc'])?></td></tr>
<?php if(isset($tastinginfo['personal_price'])): ?>
<tr><td class="label"><label><?=langTranslate('Price', 'Price');?></label></td><td class="value"><?=htmlentities(formatPrice($tastinginfo['personal_price']))?></td></tr>
<?php endif; ?>
</tbody></table>
<?php langClean('tasting', 'tasting')?>
