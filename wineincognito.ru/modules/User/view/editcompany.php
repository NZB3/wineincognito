<?php langSetDefault('User', 'editCompanyForm'); ?>
<form method="POST" id="edit-company-form"><input type="hidden" name="action" value="edit_company" />
<script type="string" id="error_strings_invalid_itn">
<?=langTranslate('User', 'err', 'Invalid ITN!', 'Invalid ITN!');?>
</script>
<table class="subcontent fieldlist editCompany"><tbody>
<tr><td class="label"><label for="edit-company-form-itn"><?=langTranslate('ITN', 'ITN');?></label></td><td><input type="text" id="edit-company-form-itn" name="itn" value="<?=getPostVal('itn',isset($companyinfo['itn'])?$companyinfo['itn']:'')?>" /></td></tr>
<tr><td class="tabblock" colspan="2">
    <?php 
if(count($languageList)>0):
    $labelWidth = floor(100/count($languageList));
    foreach($languageList as $language):
        $language_id = $language['id'];
        $language_name = $language['name']; 
    ?><input type="radio" id="language_tab_<?=$language_id?>" name="language_tab" class="ml_tab" value="<?=$language_id?>" /><label for="language_tab_<?=$language_id?>" style="width:<?=$labelWidth?>%"><?=htmlentities($language_name)?></label><?php 
    endforeach; ?>
</td></tr>
<?php foreach($languageList as $language):
        $language_id = $language['id']; ?>
<tr class="multilang lang_<?=$language_id?>"><td class="label"><label for="edit-company-form-name-<?=$language_id?>"><?=langTranslate('Name', 'Name');?></label></td><td><input type="text" id="edit-company-form-name-<?=$language_id?>" name="name[<?=$language_id?>]" value="<?=getPostVal('name['.$language_id.']',isset($companyinfo['name'][$language_id])?$companyinfo['name'][$language_id]:'')?>" /></td></tr>
<?php endforeach; 
endif; ?>
<tr><td class="submit" colspan="2"><input type="submit" value="<?=langTranslate('Save', 'Save');?>" /></td></tr>
</tbody></table>
</form>
<?php langClean('User', 'editCompanyForm')?>