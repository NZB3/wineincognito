<?php langSetDefault('User', 'companyApiAccessSettingsForm'); 
if(!isset($compact)){
    $compact = true;
}
?>
<form method="POST"><input type="hidden" name="action" value="api_access" /><input type="hidden" name="active" value="<?=$autoinviteactive?0:1?>" />
<table class="subcontent fieldlist company-settings-api-access compactable <?=$compact?'compact':''?>">
<thead><tr class="header"><th colspan="2"><?=langTranslate('API access', 'API access');?></th></tr></thead>
<tbody>
<tr><td class="label"><label><?=langTranslate('Login', 'Login');?></label></td><td><?=htmlentities($api_login)?></td></tr>
<tr><td class="label"><label for="company-settings-api-access-form-password"><?=langTranslate('Password', 'Password');?></label></td><td><input type="password" name="password" id="company-settings-api-access-form-password" name="pass" /></td></tr>
<tr><td class="submit" colspan="2"><input type="submit" value="<?=langTranslate('Save', 'Save');?>" /></td></tr>
</tbody></table>
</form>
<?php langClean('User', 'companyApiAccessSettingsForm')?>