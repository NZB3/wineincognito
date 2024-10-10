<?php langSetDefault('tasting', 'contest');
$backurl = '';
if(isset($contestinfo['id'])&&$contestinfo['id']){
    $backurl = BASE_URL.'/contest/'.(int)$contestinfo['id'];
} else {
    $backurl = BASE_URL.'/contests';
}
?>
<form enctype="multipart/form-data" method="POST" id="edit-tasting-contest-form"><input type="hidden" name="action" value="edit_tasting_contest" />
<table class="subcontent fieldlist edit-tasting-contest">
<thead>
    <tr class="head-buttons"><th colspan="2"><a class="mainbtn back" href="<?=$backurl?>"><?=langTranslate('menu','navigation','Back','Back')?></a></th></tr>
</thead>
<tbody>
<tr><td class="label"><label for="edit-tasting-contest-form-name"><?=langTranslate('Name', 'Name');?></label></td><td><input type="text" name="name" id="edit-tasting-contest-form-name" value="<?=getPostVal('name',isset($contestinfo['name'])?$contestinfo['name']:'')?>" maxlength="128" /></td></tr>
<tr><td class="label"><label><?=langTranslate('Logo', 'Logo');?></label></td><td class="edit-tasting-contest-form-image-block"><img <?=isset($contestinfo['logourl'])&&$contestinfo['logourl']?'src="'.$contestinfo['logourl'].'"':'class="empty"'?> /><input type="file" name="tasting_contest_logo_file" /><input type="button" value="<?=langTranslate('Logo - Set', 'Set');?>" /></td></tr>
<tr><td class="label"><label for="edit-tasting-contest-form-location"><?=langTranslate('Location', 'Location');?></label></td><td><input type="text" name="location" id="edit-tasting-contest-form-location" value="<?=getPostVal('location',isset($contestinfo['location'])?$contestinfo['location']:'')?>" maxlength="512" /></td></tr>
<tr><td class="label"><label><?=langTranslate('Assessment', 'Assessment');?></label></td><td>
    <label class="radio"><input type="radio" name="assessment" value="0" <?=(getPostVal('assessment',isset($contestinfo['assessment'])?$contestinfo['assessment']:0)==0)?'checked="checked"':''?>><span></span><?=langTranslate('Assessment - Private', 'Private');?></label>
    <label class="radio"><input type="radio" name="assessment" value="1" <?=(getPostVal('assessment',isset($contestinfo['assessment'])?$contestinfo['assessment']:0)==1)?'checked="checked"':''?>><span></span><?=langTranslate('Assessment - Public', 'Public');?></label>
</td></tr>
<tr><td class="label"><label for="edit-tasting-contest-form-desc"><?=langTranslate('Description', 'Description');?></label></td><td><textarea name="desc" id="edit-tasting-contest-form-desc" /><?=getPostVal('desc',isset($contestinfo['desc'])?$contestinfo['desc']:'')?></textarea></td></tr>
<tr><td class="submit" colspan="2"><input type="submit" value="<?=langTranslate('Save', 'Save');?>" /></td></tr>
</tbody></table>
</form>
<?php langClean('tasting', 'contest')?>