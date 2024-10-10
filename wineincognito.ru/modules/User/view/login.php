<?php 
    langSetDefault('User', 'loginForm');
?><form method="POST" class="login-form"><input type="hidden" name="action" value="user_login" /><table class="subcontent fieldlist"><tbody>
<tr><td class="label"><label for="login-form-login"><?=langTranslate('Username', 'E-mail');?></label></td><td><input type="text" id="login-form-login" name="login" value="<?=getpostval('login', (isset($_COOKIE)&&isset($_COOKIE['user_lastLogin']))?$_COOKIE['user_lastLogin']:'')?>" /></td></tr>
<tr><td class="label"><label for="login-form-password"><?=langTranslate('Password', 'Password');?></label></td><td><input type="password" id="login-form-password" name="pass" /></td></tr>
<tr><td colspan="2" class="password-recovery"><a href="<?=BASE_URL?>/passwordrecovery"><?=langTranslate('Forgotten password?','Forgotten password?')?></a></td></tr>
<tr class="buttons"><td colspan="2"><div class="login"><input type="submit" value="<?=langTranslate('Log in', 'Log in');?>" /></div><?php
/*
<div class="register"><a class="mainbtn" href="<?=BASE_URL?>/register"><?=langTranslate('Register', 'Register');?></a></div>
*/
?></td></tr>
</tbody></table></form>
<?php langClean('User', 'loginForm')?>
