<?php 
    langSetDefault('tasting', 'contest');
    if(!isset($can_edit_users)){
        $can_edit_users = false;
    }
    if(!isset($can_add)){
        $can_add = false;
    }
    if(!isset($can_refresh)){
        $can_refresh = false;
    }
    if(!isset($actions)){
        $actions = false;
    }
    $rowcount = 4+($actions?1:0);
?>
<div class="group-block">
<script type="template" class="userlist-item-template">
<tr data-id="{{id}}"><td class="owner">{if{is_owner}}<span></span>{endif{is_owner}}</td><td class="name"><a href="<?=BASE_URL?>/user/{{id}}">{{name}}</a></td><td class="company">{if{company_id}}<a href="<?=BASE_URL?>/company/{{company_id}}" target="_blank">{{company_name}}</a>{endif{company_id}}</td>
<td class="tasting-count">{{tasting_count}}</td>
<?php if($actions): ?><td class="remove"><?=$can_edit_users?'{if{can_remove}}<span></span>{endif{can_remove}}':''?></td><?php endif; ?>
</tr>
</script>
<table class="subcontent view-contest-user-list compactable compact" data-tc-id="<?=$contest_id?>">
<thead>
    <tr class="head-buttons"><th colspan="<?=$rowcount?>">
<?php if($can_add): ?>
        <span class="refresh"></span>
<?php endif;
    if($can_add): ?>
        <span class="add"></span>
<?php endif; ?>
    </th></tr>
    <tr class="header"><th colspan="<?=$rowcount?>"><?=langTranslate('Organizer List',  'Organizer List')?></th></tr>
    <tr><th class="owner"></th><th class="name"><?=langTranslate('user','user','Name','Name')?></th><th class="company"><?=langTranslate('user','user','Company','Company')?></th><th class="tasting-count" data-tooltip="<?=langTranslate('Contest user list: Tastings added','Tastings added')?>"></th>
<?php if($actions): ?><th class="remove"></th><?php endif; ?>
    </tr>
</thead>
<tbody>
<?php foreach($contest_user_list as $user): ?>
    <tr data-id="<?=$user['id']?>"><td class="owner"><?=$user['is_owner']?'<span></span>':''?></td><td class="name"><a href="<?=BASE_URL?>/user/<?=$user['id']?>"><?=htmlentities($user['name'])?></a></td><td class="company"><?php if($user['company_id']): ?><a href="<?=BASE_URL?>/company/<?=$user['company_id']?>" target="_blank"><?=htmlentities($user['company_name'])?></a><?php endif; ?></td>
<td class="tasting-count"><?=(int)$user['tasting_count']?></td>
<?php if($actions): ?><td class="remove"><?=$can_edit_users&&$user['can_remove']?'<span></span>':''?></td><?php endif; ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php langClean('tasting', 'contest')?>
