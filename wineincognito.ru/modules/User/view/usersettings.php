<?php langSetDefault('user', 'usersettings'); 
if(!isset($compact)){
    $compact = true;
}
if(!isset($direct_auth_enabled)){
    $direct_auth_enabled = null;
}
if(!isset($user_id)){
    $user_id = 0;
}
?>
<?php if($direct_auth_enabled!==null): ?>
<form class="user-settings-direct-auth" data-user-id="<?=$user_id?>"><table class="subcontent fieldlist user-settings-direct-auth compactable <?=$compact?'compact':''?> <?=$direct_auth_enabled?'status-enabled':'status-disabled'?>">
<thead><tr class="header"><th colspan="2"><?=langTranslate('Direct Auth', 'Direct Auth');?></th></tr></thead>
<tbody>
<tr class="direct-auth-status"><td class="label"><label><?=langTranslate('Direct Auth: Status', 'Status');?></label></td><td><span class="status-enabled"><?=langTranslate('Direct Auth: Status: Enabled', 'Enabled')?></span><span class="status-disabled"><?=langTranslate('Direct Auth: Status: Disabled', 'Disabled')?></span></td></tr>
<tr class="direct-auth-url"><td class="label"><label><?=langTranslate('Direct Auth: URL', 'URL');?></label></td><td class="value"></td></tr>
<tr><td class="submit" colspan="2"><input type="submit" class="action-disable" value="<?=langTranslate('Direct Auth: Status: Disable', 'Disable')?>" /><input type="submit" class="action-enable" value="<?=langTranslate('Direct Auth: Status: Enable', 'Enable');?>" /></td></tr>
</tbody></table></form>
<?php endif; ?>
<?php langClean('User', 'usersettings')?>