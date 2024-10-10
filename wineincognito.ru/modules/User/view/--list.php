<?php langSetDefault('User', 'list'); ?>
<script type="string" id="confirm_string_dismiss_user"><?=langTranslate('Are you sure you want to dismiss {{fullname}}?','Are you sure you want to dismiss {{fullname}}?')?></script>
<table class="userlist <?=$tableclass?>" <?=isset($company_id)?'data-company-id="'.$company_id.'"':''?>>
    <thead>
        <tr><th class="role"></th><th><?=langTranslate('Name','Name')?></th><th class="company"><?=langTranslate('Company','Company')?></th><th class="separator"></th><th class="approve"></th><th class="deny"></th><th class="dismiss"></th><th class="change_password"></th><th class="edit"></th></tr>
    </thead>
    <tfoot>
        <tr><th class="role"></th><th><?=langTranslate('Name','Name')?></th><th class="company"><?=langTranslate('Company','Company')?></th><th class="separator"></th><th class="approve"></th><th class="deny"></th><th class="dismiss"></th><th class="change_password"></th><th class="edit"></th></tr>
    </tfoot>
    <tbody>
<?php foreach($userlist as $user): ?>
        <tr data-id="<?=$user['id']?>" <?=(isset($user['company_id'])&&$user['company_id']>0)?'data-company-id="'.$user['company_id'].'"':''?>><td class="role <?=(isset($user['is_owner'])&&$user['is_owner'])?'supervisor':''?>"><span></span></td><td class="name"><a href="<?=BASE_URL?>/user/<?=$user['id']?>"><?=htmlentities($user['name'])?></a></td><td class="company">
    <?php if(isset($user['company_id'])&&$user['company_id']>0): ?>
            <a href="<?=BASE_URL?>/company/<?=$user['company_id']?>"><?=htmlentities($user['company_name'])?></a>
    <?php endif; ?>
        </td><td></td><td class="approve">
<?php if(isset($user['can_approve'])&&$user['can_approve']): ?>
            <span></span>
<?php endif; ?>
        </td><td class="deny">
<?php if(isset($user['can_deny'])&&$user['can_deny']): ?>
            <span></span>
<?php endif; ?>
        </td><td class="dismiss">
<?php if(isset($user['can_dismiss'])&&$user['can_dismiss']&&isset($user['company_id'])&&$user['company_id']>0): ?>
            <span></span>
<?php endif; ?>
        </td><td class="change_password">
<?php if(isset($user['can_change_password'])&&$user['can_change_password']): ?>
            <a href="<?=BASE_URL?>/user/<?=$user['id']?>/change_password"></a>
<?php endif; ?>
        </td><td class="edit">
<?php if(isset($user['can_edit'])&&$user['can_edit']): ?>
            <a href="<?=BASE_URL?>/user/<?=$user['id']?>/edit"></a>
<?php endif; ?>
        </td></tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php langClean('User', 'list')?>
