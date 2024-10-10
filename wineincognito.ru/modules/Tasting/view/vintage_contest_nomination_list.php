<?php 
    langSetDefault('tasting', 'contest');
    if(!isset($compact)){
        $compact = false;
    }
    $can_view_certificates = true;
    $rowcount = 4+($can_view_certificates?1:0);
?>
<table class="subcontent vintage-contest-nomination-list compactable <?=$compact?'compact':''?>">
<thead>
    <tr class="header"><th colspan="<?=$rowcount?>"><?=langTranslate('tasting', 'contest', 'Contest: Nomination List',  'Nomination List')?></th></tr>
    <tr><th class="logo"></th><th class="place"></th><th class="name"><?=langTranslate('Nomination: Name','Name')?></th>
        <th class="score score3"><span class="header non-sticky-tooltip" data-tooltip="<?=$expert_level_list[3]?>"></span></th>
<?php if($can_view_certificates): ?><th class="action"></th><?php endif; ?>
    </tr>
</thead>
<tbody>
<?php foreach($contest_nomination_list as $contest): ?>
    <tr class="contest"><td class="logo" rowspan="<?=1+count($contest['nominations'])?>"><?=$contest['logourl']?'<a href="'.BASE_URL.'/contest/'.$contest['id'].'/stats"><img src="'.$contest['logourl'].'" /></a>':''?></td><td class="name" colspan="<?=$rowcount-1?>"><a href="<?=BASE_URL?>/contest/<?=$contest['id']?>/stats"><?=htmlentities($contest['name'])?></a></td></tr>
<?php   foreach($contest['nominations'] as $nomination): ?>
    <tr class="nomination-winner"><td class="place"><?=$nomination['place']?></td><td class="name"><?=htmlentities($nomination['name'])?></td>
    <td class="score"><?php if($nomination['score3']): ?><a href="<?=BASE_URL?>/contest/<?=$contest['id']?>/stats/product/<?=$vintage_id?>/reviewmerge/3"><?=$nomination['score3']?></a><?php endif; ?></td>
<?php if($can_view_certificates): ?><td class="certificate-view"><a href="<?=BASE_URL?>/contest/<?=$contest['id']?>/product/<?=$vintage_id?>/certificate" target="_blank"></a></td><?php endif; ?>
    </tr>
<?php   endforeach;
    endforeach; ?>
</tbody>
</table>
<?php langClean('tasting', 'contest')?>
