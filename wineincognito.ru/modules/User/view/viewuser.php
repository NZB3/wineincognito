<?php 
    langSetDefault('User', 'viewUser');
?>
<div class="group-block">
<table class="subcontent viewUser <?=(isset($compact)&&$compact)?'compactable compact':''?>" data-id="<?=$userinfo['id']?>">
<thead>
    <tr class="head-buttons"><th colspan="2">
        <?php if($userinfo['can_access_user_settings']): ?>
        <a class="user-settings" href="<?=BASE_URL?>/user/<?=$userinfo['id']?>/settings"></a>
        <?php endif; ?>
        <?php if($userinfo['can_change_password']): ?>
        <a class="change_password" href="<?=BASE_URL?>/user/<?=$userinfo['id']?>/change_password"></a>
        <?php endif; ?>
        <?php if($userinfo['can_edit']): ?>
        <a class="edit" href="<?=BASE_URL?>/user/<?=$userinfo['id']?>/edit"></a>
        <?php endif; ?>
    </th></tr>
    <tr class="header"><th colspan="2"><?=htmlentities($userinfo['fullname'])?></th></tr>
</thead>
<tbody>
<tr><td class="label"><label><?=langTranslate('E-mail', 'E-mail');?></label></td><td class="value"><?=htmlentities($userinfo['email'])?></td></tr>
<tr><td class="label"><label><?=langTranslate('Last name', 'Last name');?></label></td><td class="value"><?=htmlentities($userinfo['lastname'])?></td></tr>
<tr><td class="label"><label><?=langTranslate('First name', 'First name');?></label></td><td class="value"><?=htmlentities($userinfo['firstname'])?></td></tr>
<tr><td class="label"><label><?=langTranslate('Patronymic', 'Patronymic');?></label></td><td class="value"><?=htmlentities($userinfo['patronymic'])?></td></tr>
<?php if(strlen($userinfo['background'])): ?>
<tr><td class="label"><label><?=langTranslate('Background', 'Background');?></label></td><td class="value"><?=htmlentities($userinfo['background'])?></td></tr>
<?php endif; ?>
<?php if($can_approve_expert):?>
<tr><td class="label"><label for="dropbox-custom-user-expert-level"><?=langTranslate('Expert', 'Expert');?></label></td><td class="user-expert-level"><form><script type="string" class="dropbox-template-empty-string"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></script><script type="template" class="dropbox-template-option-important-toggle"><li class="dropbox-item-list-toggle show-not-important"><?=langTranslate('main','dropbox','Show not important', 'Show not important');?></li><li class="dropbox-item-list-toggle hide-not-important"><?=langTranslate('main','dropbox','Hide not important', 'Hide not important');?></li></script><div class="dropbox fresh" data-custom="1"><input type="checkbox" id="dropbox-custom-user-expert-level" /><label for="dropbox-custom-user-expert-level"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul>
<?php   foreach($expert_level_list as $expert_level=>$caption): ?>
<li class="item <?=$userinfo['expert_level']===$expert_level?'selected':''?>"><label><input type="checkbox" value="<?=$expert_level?>" <?=$userinfo['expert_level']===$expert_level?'checked="checked"':''?> /><span></span><?=htmlentities($caption)?></label></li>
<?php   endforeach; ?>
</ul></div></form></td></tr>
<?php else: 
        if($userinfo['expert_level']!==null && isset($expert_level_list[$userinfo['expert_level']])): ?>
<tr><td class="label expert"><label><?=langTranslate('Expert', 'Expert');?></label></td><td class="value expert"><?=htmlentities($expert_level_list[$userinfo['expert_level']])?>
<?php       if($userinfo['requested_expert_change']): ?>
<span class="waiting-for-approval"><?=langTranslate('Waiting for approval', 'Waiting for approval');?></span>
<?php       endif;
            if($userinfo['can_request_expert_change']): ?>
<span class="request-expert-change mainbtn"><?=langTranslate('user','request expert change','Request expert level change','Request expert level change')?></span>
<?php       else: 
                if(strlen($userinfo['can_request_expert_change_reason'])): ?>
<span class="request-expert-change-reason" data-tooltip="<?=$userinfo['can_request_expert_change_reason']?>"></span>
<?php           endif;
            endif; ?>
</td></tr>
<?php   endif;
    endif; ?>

<?php if($userinfo['company_id']): ?>
<tr><td class="label"><label><?=langTranslate('Company', 'Company');?></label></td><td class="value"><a href="<?=BASE_URL?>/company/<?=$userinfo['company_id']?>"><?=htmlentities($userinfo['company_name'])?></a></td></tr>
<?php endif; ?>
</tbody></table>

<?php if($can_approve_expert): ?>
<script type="template" class="user-expert-change-date-form-template">
<form class="expert-change-date-form"><table class="subcontent fieldlist expert-change-date-form">
<thead>
    <tr class="header"><th colspan="2"><?=langTranslate('user','expert date change','Upgrade expert reviews retroactively','Upgrade expert reviews retroactively')?></th></tr>
</thead>
<tr><td class="label"><label for="expert-change-date-form-date"><?=langTranslate('user','expert date change','Date','Date')?></label></td><td><input type="text" id="expert-change-date-form-date" value="<?=date('d.m.Y')?>" /></td></tr>
<tr><td class="submit" colspan="2"><input type="submit" value="<?=langTranslate('user','expert date change','Change','Change');?>" /></td></tr>
</tbody></table></form>
</script>
<?php endif; ?>


<?php if(!$can_approve_expert&&$userinfo['can_request_expert_change']): ?>
<script type="template" class="user-request-expert-change-form-template">
<form><table class="subcontent fieldlist request-expert-change-form">
<thead>
    <tr class="header"><th colspan="2"><?=langTranslate('user','request expert change','Request expert level change','Request expert level change')?></th></tr>
</thead>
<tr><td class="label"><label for="request-expert-change-form-comment"><?=langTranslate('user','request expert change','Comment','Comment')?></label></td><td><textarea name="comment" id="request-expert-change-form-comment"></textarea></td></tr>
<tr><td class="submit" colspan="2"><input type="submit" value="<?=langTranslate('user','request expert change','Request','Request');?>" /></td></tr>
</tbody></table></form>
</script>
<?php endif; ?>
</div>
<?php langClean('User', 'viewUser')?>
