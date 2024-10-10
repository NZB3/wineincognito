<?php 
    langSetDefault('tasting', 'contest');
    if(!isset($can_refresh)){
        $can_refresh = false;
    }
    if(!isset($can_add)){
        $can_add = false;
    }
    if(!isset($actions)){
        $actions = false;
    }
    if(!isset($showowner)){
        $showowner = false;
    }
    if(!isset($showstatus)){
        $showstatus = false;
    }
    if(!isset($showassessment)){
        $showassessment = false;
    }
    if(!isset($compact)){
        $compact = false;
    }
    $rowcount = 2+($showstatus?1:0)+($showowner?1:0)+($actions?1:0);
    $tastingurl = BASE_URL.'/tasting/{{id}}';
    $tastingstatsurl = BASE_URL.'/tasting/{{id}}/stats';
?>
<div class="group-block">
<script type="template" class="tastinglist-item-template">
<tr data-id="{{id}}"><td class="date">{{date}}</td><td class="name"><a href="{if{lead_to_stats}}<?=$tastingstatsurl?>{endif{lead_to_stats}}{!if{lead_to_stats}}<?=$tastingurl?>{end!if{lead_to_stats}}">{{name}}</a>{if{assessment_private}}<span class="assessment private"><?=langTranslate('tasting', 'tasting', 'Assessment - Private', 'Private')?></span>{endif{assessment_private}}{if{assessment_denied}}<span class="assessment denied"><?=langTranslate('tasting', 'tasting', 'Assessment - Denied', 'Assessment denied')?></span>{endif{assessment_denied}}{if{assessment_awaiting}}<span class="assessment awaiting"><?=langTranslate('tasting', 'tasting', 'Assessment - Awaiting', 'Awaiting assessment')?></span>{endif{assessment_awaiting}}{if{location}}<span class="location">{{location}}</span>{endif{location}}</td>
<?php if($showstatus): ?><td class="status">{{status}}</td><?php endif; ?>
<?php if($showowner): ?><td class="owner">{if{owner_id}}<a href="<?=BASE_URL?>/user/{{owner_id}}">{{owner_name}}</a>{endif{owner_id}}</td><?php endif; ?>
<?php if($actions): ?><td class="remove">{if{can_remove}}<span></span>{endif{can_remove}}</td><?php endif; ?>
</tr>
</script>
<table class="subcontent view-contest-tasting-list compactable <?=$compact?'compact':''?>" data-tc-id="<?=$contest_id?>" data-showstatus="<?=$showstatus?1:0?>" data-showowner="<?=$showowner?1:0?>" data-showassessment="<?=$showassessment?1:0?>">
<thead>
    <tr class="head-buttons"><th colspan="<?=$rowcount?>">
<?php if($can_refresh): ?>
        <span class="refresh"></span>
<?php endif;
      if($can_add): ?>
        <span class="add"></span>
<?php endif; ?>
    </th></tr>
    <tr class="header"><th colspan="<?=$rowcount?>"><?=langTranslate('Contest: Tasting List',  'Tasting List')?></th></tr>
    <tr><th class="date"><?=langTranslate('tasting','tasting','Start','Start')?></th><th class="name"><?=langTranslate('tasting','tasting','Name','Name')?></th>
<?php if($showstatus): ?><th class="status"><?=langTranslate('tasting','tasting','Status','Status')?></span></th><?php endif; ?>
<?php if($showowner): ?><th class="owner"><?=langTranslate('tasting','contest','Tasting owner','Owner')?></span></th><?php endif; ?>
<?php if($actions): ?><th class="remove"></th><?php endif; ?>
    </tr>
</thead>
<tbody>
<?php foreach($contest_tasting_list as $tasting): ?>
    <tr data-id="<?=$tasting['id']?>"><td class="date"><?=htmlentities($tasting['date'])?></td><td class="name"><a href="<?=str_replace('{{id}}', $tasting['id'], $tasting['lead_to_stats']?$tastingstatsurl:$tastingurl)?>"><?=htmlentities($tasting['name'])?></a><?=isset($tasting['assessment_private'])&&$tasting['assessment_private']?'<span class="assessment private">'.langTranslate('tasting', 'tasting', 'Assessment - Private', 'Private').'</span>':''?><?=isset($tasting['assessment_denied'])&&$tasting['assessment_denied']?'<span class="assessment denied">'.langTranslate('tasting', 'tasting', 'Assessment - Denied', 'Assessment denied').'</span>':''?><?=isset($tasting['assessment_awaiting'])&&$tasting['assessment_awaiting']?'<span class="assessment awaiting">'.langTranslate('tasting', 'tasting', 'Assessment - Awaiting', 'Awaiting assessment').'</span>':''?><?=$tasting['location']?'<span class="location">'.htmlentities($tasting['location']).'</span>':''?></td>
<?php if($showstatus): ?><td class="status"><?=htmlentities($tasting['status'])?></td><?php endif; ?>
<?php if($showowner): ?><td class="owner"><?php if(isset($tasting['owner_id'])&&$tasting['owner_id']): ?><a href="<?=BASE_URL?>/user/<?=$tasting['owner_id']?>"><?=htmlentities($tasting['owner_name'])?></a><?php endif; ?></td><?php endif; ?>
<?php if($actions): ?><td class="remove"><?=$tasting['can_remove']?'<span></span>':''?></td><?php endif; ?>
    </tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php langClean('tasting', 'contest')?>
