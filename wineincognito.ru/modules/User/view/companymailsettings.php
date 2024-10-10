<?php langSetDefault('User', 'companyMailSettingsForm'); 
$header_logo_url = getPostVal('header_logo_url',$mailsettings['header_logo_url']);
$footer_logo_url = getPostVal('footer_logo_url',$mailsettings['footer_logo_url']);
$text_color = getPostVal('text_color',$mailsettings['text_color']);
$anchor_color = getPostVal('anchor_color',$mailsettings['anchor_color']);
$header_background_color = getPostVal('header_background_color',$mailsettings['header_background_color']);
$footer_background_color = getPostVal('footer_background_color',$mailsettings['footer_background_color']);
if(!isset($compact)){
    $compact = true;
}
?>
<form method="POST" class="company-mail-settings-form"><input type="hidden" name="action" value="mail_settings" />
<table class="subcontent fieldlist company-mail-settings compactable <?=$compact?'compact':''?>" data-company-id="<?=$company_id?>">
<thead><tr class="header"><th colspan="2"><?=langTranslate('Mail Settings', 'Mail Settings');?></td></th></thead>
<tbody>
<tr><td class="label"><label><?=langTranslate('Header logo', 'Header logo');?></label></td><td><?php
    if($header_logo_url):
?>
    <img src="<?=BASE_URL.$header_logo_url?>" />
<?php else: ?>
    <img class="no-image" />
<?php endif; ?>
    <input type="hidden" name="header_logo_url" value="<?=$header_logo_url?>" />
    <div><input type="file" class="company-mail-settings-form-change-image-file" data-type="logo" /><input type="button" class="company-mail-settings-form-change-image" value="<?=langTranslate('Change', 'Change');?>" /><input type="button" class="company-mail-settings-form-change-image-remove" value="<?=langTranslate('Remove', 'Remove');?>" /></div>
</td></tr>
<tr><td class="label"><label><?=langTranslate('Footer logo', 'Footer logo');?></label></td><td><?php
    if($footer_logo_url):
?>
    <img src="<?=BASE_URL.$footer_logo_url?>" />
<?php else: ?>
    <img class="no-image" />
<?php endif; ?>
    <input type="hidden" name="footer_logo_url" value="<?=$footer_logo_url?>" />
    <div><input type="file" class="company-mail-settings-form-change-image-file" data-type="small" /><input type="button" class="company-mail-settings-form-change-image" value="<?=langTranslate('Change', 'Change');?>" /><input type="button" class="company-mail-settings-form-change-image-remove" value="<?=langTranslate('Remove', 'Remove');?>" /></div>
</td></tr>
<tr><td class="label"><label><?=langTranslate('Text color', 'Text color');?></label></td><td><input type="text" name="text_color" class="company-mail-settings-form-color" value="<?=$text_color?>" /></td></tr>
<tr><td class="label"><label><?=langTranslate('Anchor color', 'URL color');?></label></td><td><input type="text" name="anchor_color" class="company-mail-settings-form-color" value="<?=$anchor_color?>" /></td></tr>
<tr><td class="label"><label><?=langTranslate('Header background color', 'Header background color');?></label></td><td><input type="text" name="header_background_color" class="company-mail-settings-form-color" value="<?=$header_background_color?>" /></td></tr>
<tr><td class="label"><label><?=langTranslate('Footer background color', 'Footer background color');?></label></td><td><input type="text" name="footer_background_color" class="company-mail-settings-form-color" value="<?=$footer_background_color?>" /></td></tr>
<tr><td class="submit" colspan="2"><input type="button" class="company-mail-settings-form-reset-default" value="<?=langTranslate('Default', 'Default');?>" data-header-logo-url="<?=$defaultmailsettings["header_logo_url"]?>" data-footer-logo-url="<?=$defaultmailsettings["footer_logo_url"]?>" data-text-color="<?=$defaultmailsettings["text_color"]?>" data-anchor-color="<?=$defaultmailsettings["anchor_color"]?>" data-header-background-color="<?=$defaultmailsettings["header_background_color"]?>" data-footer-background-color="<?=$defaultmailsettings["footer_background_color"]?>" /><input type="submit" value="<?=langTranslate('Save', 'Save');?>" /></td></tr>
</tbody></table>
</form>
<?php langClean('User', 'companyMailSettingsForm')?>