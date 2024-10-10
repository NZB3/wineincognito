<?php 
    langSetDefault('tasting', 'contest');
    if(!isset($can_refresh)){
        $can_refresh = false;
    }
    if(!isset($can_edit)){
        $can_edit = false;
    }
    if(!$can_edit){
        $actions = false;
    }
    if(!isset($actions)){
        $actions = false;
    }
    if(!isset($showempty)){
        $showempty = false;
    }
    if(!isset($compact)){
        $compact = false;
    }
    if(!isset($can_view_certificates)){
        $can_view_certificates = false;
    }
    if(!isset($vintage_id)){
        $vintage_id = null;
    }
    $can_view_certificates = true;
    $rowcount = 5+($actions?5:0)+($can_view_certificates?1:0);
    $vintageurl = BASE_URL.'/contest/'.$contest_id.'/stats/product/{{id}}';
    $reviewmergeurl = BASE_URL.'/contest/'.$contest_id.'/stats/product/{{id}}/reviewmerge/{{expert_level}}';
    $certificateurl = BASE_URL.'/contest/'.$contest_id.'/product/{{id}}/certificate';
?>
<div class="group-block">
<script type="template" class="nominationlist-item-template">
<tr class="nomination" data-id="{{id}}"><td class="name" colspan="<?=$can_view_certificates?6:5?>">{{name}}</td>
<?php if($actions): ?><td class="raise-index">{if{can_edit}}<span></span>{endif{can_edit}}</td><td class="lower-index">{if{can_edit}}<span></span>{endif{can_edit}}</td><td class="add">{if{can_edit}}<span></span>{endif{can_edit}}</td><td class="edit">{if{can_edit}}<span></span>{endif{can_edit}}</td><td class="remove">{if{can_remove}}<span></span>{endif{can_remove}}</td><?php endif; ?>
</tr>
</script>
<script type="template" class="nominationwinnerlist-item-template">
<tr class="nomination-winner" data-id="{{id}}" data-nid="{{nid}}"><td class="place">{{place}}</td><td class="name"><a href="<?=$vintageurl?>">{{fullname}}</a></td>
<td class="score">{if{score1}}<a href="<?=str_replace('{{expert_level}}','1',$reviewmergeurl)?>">{{score1}}</a>{endif{score1}}</td><td class="score">{if{score2}}<a href="<?=str_replace('{{expert_level}}','2',$reviewmergeurl)?>">{{score2}}</a>{endif{score2}}</td><td class="score">{if{score3}}<a href="<?=str_replace('{{expert_level}}','3',$reviewmergeurl)?>">{{score3}}</a>{endif{score3}}</td>
<?php if($can_view_certificates): ?><td class="certificate-view"><a href="<?=$certificateurl?>" target="_blank"></a></td><?php endif; ?>
<?php if($actions): ?><td colspan="4"></td><td class="remove">{if{can_remove}}<span></span>{endif{can_remove}}</td><?php endif; ?>
</tr>
</script>
<table class="subcontent view-contest-nomination-list compactable <?=$compact?'compact':''?>" data-tc-id="<?=$contest_id?>" data-showempty="<?=$showempty?1:0?>" <?=$vintage_id?'data-product="'.$vintage_id.'"':''?>>
<thead>
    <tr class="head-buttons"><th colspan="<?=$rowcount?>">
<?php if($can_refresh): ?>
        <span class="refresh"></span>
<?php endif;
      if($actions && $can_edit): ?>
        <span class="add"></span>
<?php endif; ?>
    </th></tr>
    <tr class="header"><th colspan="<?=$rowcount?>"><?=langTranslate('Contest: Nomination List',  'Nomination List')?></th></tr>
    <tr><th class="place"></th><th class="name"><?=langTranslate('product', 'vintage','Name','Name')?></th>
            <th class="score score1"><span class="header non-sticky-tooltip" data-tooltip="<?=$expert_level_list[1]?>"></span></th>
            <th class="score score2"><span class="header non-sticky-tooltip" data-tooltip="<?=$expert_level_list[2]?>"></span></th>
            <th class="score score3"><span class="header non-sticky-tooltip" data-tooltip="<?=$expert_level_list[3]?>"></span></th>
<?php if($can_view_certificates): ?><th class="action"></th><?php endif; ?>
<?php if($actions): ?><th class="action"></th><th class="action"></th><th class="action"></th><th class="action"></th><th class="action"></th><?php endif; ?>
    </tr>
</thead>
<tbody>
<?php foreach($contest_nomination_list as $nomination): ?>
    <tr class="nomination" data-id="<?=$nomination['id']?>"><td class="name" colspan="<?=$can_view_certificates?6:5?>"><?=htmlentities($nomination['name'])?></td>
<?php   if($actions): ?><td class="raise-index"><?=$nomination['can_edit']?'<span></span>':''?></td><td class="lower-index"><?=$nomination['can_edit']?'<span></span>':''?></td><td class="add"><?=$nomination['can_edit']?'<span></span>':''?></td><td class="edit"><?=$nomination['can_edit']?'<span></span>':''?></td><td class="remove"><?=$nomination['can_remove']?'<span></span>':''?></td><?php endif; ?>
    </tr>
<?php   if(!empty($nomination['products'])): 
            foreach($nomination['products'] as $nomination_winner): ?>
    <tr class="nomination-winner" data-id="<?=$nomination_winner['id']?>" data-nid="<?=$nomination['id']?>"><td class="place"><?=$nomination_winner['place']?></td><td class="name"><a href="<?=str_replace('{{id}}', $nomination_winner['id'], $vintageurl)?>"><?=htmlentities($nomination_winner['fullname'])?></a></td>
    <td class="score"><?php if($nomination_winner['score1']): ?><a href="<?=str_replace('{{id}}',$nomination_winner['id'],str_replace('{{expert_level}}','1',$reviewmergeurl))?>"><?=$nomination_winner['score1']?></a><?php endif; ?></td><td class="score"><?php if($nomination_winner['score2']): ?><a href="<?=str_replace('{{id}}',$nomination_winner['id'],str_replace('{{expert_level}}','2',$reviewmergeurl))?>"><?=$nomination_winner['score2']?></a><?php endif; ?></td><td class="score"><?php if($nomination_winner['score3']): ?><a href="<?=str_replace('{{id}}',$nomination_winner['id'],str_replace('{{expert_level}}','3',$reviewmergeurl))?>"><?=$nomination_winner['score3']?></a><?php endif; ?></td>
<?php if($can_view_certificates): ?><td class="certificate-view"><a href="<?=str_replace('{{id}}',$nomination_winner['id'],$certificateurl)?>" class="non-sticky-tooltip" data-tooltip="<?=langTranslate('Tooltip: Certificate','Certificate')?>" target="_blank"></a></td><?php endif; ?>
<?php           if($actions): ?><td colspan="4"></td><td class="remove"><?=$nomination['can_remove']?'<span></span>':''?></td><?php endif; ?>
    </tr>
<?php       endforeach;
        endif; 
    endforeach; ?>
</tbody>
</table>
</div>
<?php langClean('tasting', 'contest')?>
