<?php 
    langSetDefault('User', 'changePasswordForm');
    if(!isset($change_login)){
        $change_login = false;
    }
    if(!isset($login)){
        $login = null;
    }
?>
<form method="POST" id="edit-user-change-password-form"><input type="hidden" name="action" value="change_password" />
<script type="string" id="error_strings_password_match">
<?=langTranslate('User', 'err', 'Passwords do not match!', 'Passwords do not match!');?>
</script>
<table class="subcontent fieldlist changePassword"><tbody>
<?php if($require_old_password): ?>
<tr><td class="label"><label for="edit-user-change-password-form-old-password"><?=langTranslate('Old password', 'Old password');?></label></td><td><input type="password" id="edit-user-change-password-form-old-password" name="oldpass" /></td></tr>
<?php endif; 
    if($change_login): ?>
<tr><td class="label"><label for="edit-user-change-password-form-login"><?=langTranslate('New username', 'New e-mail');?></label></td><td><input type="text" id="edit-user-change-password-form-login" name="newlogin" value="<?=getpostval('newlogin', $login)?>" /></td></tr>
<?php endif; ?>
<tr><td class="label"><label for="edit-user-change-password-form-password"><?=langTranslate('New password', 'New password');?></label></td><td><input type="password" id="edit-user-change-password-form-password" name="newpass" /></td></tr>
<tr><td class="label"><label for="edit-user-change-password-form-rpassword"><?=langTranslate('Repeat password', 'Repeat password');?></label></td><td><input type="password" id="edit-user-change-password-form-rpassword" /></td></tr>
<tr><td class="submit" colspan="2"><input type="submit" value="<?=langTranslate('Change Password', 'Change Password');?>" /></td></tr>
</tbody></table></form>
<?php langClean('User', 'changePasswordForm')?>
