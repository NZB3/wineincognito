<?php langSetDefault('User', 'companySettingsForm'); 
if(!isset($compact)){
    $compact = true;
}
?>
<form method="POST" id="company-settings-auto-invite-form"><input type="hidden" name="action" value="auto_invite" /><input type="hidden" name="active" value="<?=$autoinviteactive?0:1?>" />
<table class="subcontent fieldlist CompanySettings compactable <?=$compact?'compact':''?>">
<thead><tr class="header"><th colspan="2"><?=langTranslate('Auto Invite', 'Auto Invite');?></th></tr></thead>
<tbody>
<tr><td class="label"><label><?=langTranslate('URL', 'URL');?></label></td><td><?php
if($autoinviteurl):
?><a href="<?=$autoinviteurl?>"><?=htmlentities($autoinviteurl)?></a><?php
endif;
?></td></tr>
<tr><td class="submit" colspan="2"><input type="submit" value="<?=$autoinviteactive?langTranslate('Deactivate', 'Deactivate'):langTranslate('Activate', 'Activate');?>" /></td></tr>
</tbody></table>
</form>
<?php langClean('User', 'companySettingsForm')?>