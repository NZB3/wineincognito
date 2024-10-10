<?php langSetDefault('User', 'user'); ?>
<table class="companyuserlist">
    <thead>
        <tr><th></th><th><?=langTranslate('Name','Name')?></th><th></th><th></th></tr>
    </thead>
    <tfoot>
        <tr><th></th><th><?=langTranslate('Name','Name')?></th><th></th><th></th></tr>
    </tfoot>
    <tbody>
<?php foreach($userlist as $user): ?>
        <tr><td class="role <?=($user['is_owner'])?'supervisor':''?>"><span></span></td><td><a href="<?=BASE_URL?>/user/<?=$user['id']?>"><?=htmlentities($user['name'])?></a></td><td class="change_password">
<?php if($user['can_change_password']): ?>
            <a href="<?=BASE_URL?>/user/<?=$user['id']?>/change_password"></a>
<?php endif; ?>
            
        </td><td class="edit">
<?php if($user['can_edit']): ?>
            <a href="<?=BASE_URL?>/user/<?=$user['id']?>/edit"></a>
<?php endif; ?>
        </td></tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php langClean('User', 'user')?>
