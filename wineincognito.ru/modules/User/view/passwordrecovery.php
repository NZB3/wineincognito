<?php 
    langSetDefault('User', 'passwordRecoveryForm');
?><form method="POST" class="password-recovery-form"><input type="hidden" name="action" value="user_password_recover" /><table class="subcontent fieldlist"><thead><tr><th colspan="2"><?=langTranslate('Reset password','Reset password')?></th></tr></thead><tbody>
<tr><td class="label"><label for="password-recovery-form-login"><?=langTranslate('user','loginForm','Username', 'Username');?></label></td><td><input type="text" id="password-recovery-form-login" name="login" value="<?=getpostval('login', (isset($_COOKIE)&&isset($_COOKIE['user_lastLogin']))?$_COOKIE['user_lastLogin']:'')?>" /></td></tr>
<tr class="submit"><td colspan="2"><input type="submit" value="<?=langTranslate('Reset', 'Reset');?>" /></td></tr>
</tbody></table></form>
<?php langClean('User', 'passwordRecoveryForm')?>
