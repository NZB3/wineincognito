<?php 
    langSetDefault('User', 'registerUserForm');
?>
<form method="POST" class="register-user-form"><input type="hidden" name="action" value="user_register" />
<script type="string" class="error_strings_password_match"><?=langTranslate('User', 'err', 'Passwords do not match!', 'Passwords do not match!');?></script>
<script type="string" class="error_strings_consent_required"><?=langTranslate('User', 'err', 'To register in the system, you must confirm your consent to the processing of personal data', 'To register in the system, you must confirm your consent to the processing of personal data');?></script>

<table class="subcontent fieldlist"><tbody>
<tr><td class="label"><label for="register-user-form-login"><?=langTranslate('Username', 'Username');?></label></td><td><input type="text" id="register-user-form-login" name="login" value="<?=getpostval('login', '')?>" /></td></tr>
<tr><td class="label"><label for="register-user-form-password"><?=langTranslate('Password', 'Password');?></label></td><td><input type="password" id="register-user-form-password" name="pass" /></td></tr>
<tr><td class="label"><label for="register-user-form-rpassword"><?=langTranslate('Repeat password', 'Repeat password');?></label></td><td><input type="password" id="register-user-form-rpassword" /></td></tr>
<tr class="consent"><td></td><td><label><input type="checkbox" name="consent" value="1" /><span></span><?=formatReplace(langTranslate('Processing consent', 'I agree to the @1processing of my personal data@2'),'<a href="'.BASE_URL.'/about/privacy-policy" target="_blank">','</a>');?></label></td></tr>
<tr class="buttons"><td colspan="2"><input type="submit" value="<?=langTranslate('Register', 'Register');?>" /></td></tr>
</tbody></table></form>
<?php langClean('User', 'registerUserForm')?>
