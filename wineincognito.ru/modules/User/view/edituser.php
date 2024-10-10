<?php 
    langSetDefault('User', 'editUserForm');
?>
<form method="POST" id="edit-user-edit-profile-form"><input type="hidden" name="action" value="edit_user" />
<script type="template" class="dropbox-template-select">
<tr><td class="label">{ifdef{name}}<label>{{name}}</label>{endifdef{name}}</td><td><div class="dropbox" data-fieldname="{{fieldname}}" data-group="{{group}}" data-has-children="{{haschildren}}" data-depth="{{depth}}" data-system="{{system}}"><input type="checkbox" id="dropbox-edit-user-{{fieldname}}-{{group}}-{{depth}}" /><label for="dropbox-edit-user-{{fieldname}}-{{group}}-{{depth}}"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul><li class="search"><input type="text" /></li><li class="cancel"><label><?=langTranslate('main','dropbox','Clear', 'Clear');?></label></li>{{options}}</ul></div></td></tr>
</script>
<script type="template" class="dropbox-template-option">
<li class="item {if{selected}}selected{endif{selected}} {!if{important}}not-important{end!if{important}}"><label><input type="checkbox" data-attr-id="{{attrId}}" name="{{fieldname}}[]" value="{{id}}" {if{selected}}checked{endif{selected}} data-se-text=" {{setext}}" /><span></span>{{name}}</label></li>
</script>
<script type="template" class="dropbox-template-option-header">
<li class="header">{{name}}</li>
</script>
<script type="template" class="dropbox-template-option-add">
<li class="add" data-id="{{id}}"><?=langTranslate('product','attrval','Add','Add')?></li>
</script>
<script type="string" class="dropbox-template-empty-string"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></script>
<script type="template" class="dropbox-template-option-important-toggle">
<li class="dropbox-item-list-toggle show-not-important"><?=langTranslate('main','dropbox','Show not important', 'Show not important');?></li><li class="dropbox-item-list-toggle hide-not-important"><?=langTranslate('main','dropbox','Hide not important', 'Hide not important');?></li>
</script>
<table class="subcontent fieldlist edit-user"><tbody>
<tr><td class="label"><label><?=langTranslate('E-mail', 'E-mail');?></label></td><td class="value"><?=htmlentities($userinfo['email'])?></td></tr>
<?php 
foreach($attrvaltree as $group=>$groupvaltree):
    foreach($groupvaltree as $depth=>$depthvaltree):
        $keys = array_keys($depthvaltree);
        $first_key = $keys[0];
        $haschildren = 0;
        foreach($depthvaltree as $attr){
            if($attr['haschildren']){
                $haschildren = 1;
                break;
            }
        }
?>
<tr><td class="label"><?=(count($depthvaltree)==1)?'<label>'.htmlentities($depthvaltree[$first_key]['name']).'</label>':'';?></td><td><div class="dropbox fresh <?=count($depthvaltree)==1&&count($depthvaltree[$first_key]['vals'])==1?'disabled':''?> <?=$depthvaltree[$first_key]['multiple']?'multiple':''?>" data-fieldname="attr" data-group="<?=$group?>" data-has-children="<?=$haschildren?>" data-depth="<?=$depth?>" data-system="<?=$depthvaltree[$first_key]['system']?>"><input type="checkbox" id="dropbox-edit-user-attr-<?=$group?>-<?=$depth?>" /><label for="dropbox-edit-user-attr-<?=$group?>-<?=$depth?>"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul><li class="search"><input type="text" /></li><li class="cancel"><label><?=langTranslate('main','dropbox','Clear', 'Clear');?></label></li>
<?php   foreach($depthvaltree as $attr):?>
<?php       if(count($depthvaltree)>1): ?>
<li class="header"><?=htmlentities($attr['name'])?></li>
<?php       endif; ?>
<?php       if($attr['can_add']==1): ?>
<li class="add" data-id="<?=$attr['id']?>"><?=langTranslate('product','attrval','Add','Add')?></li>
<?php       endif; ?>
<?php       foreach($attr['vals'] as $val): ?>
    <li class="item <?=$val['selected']?'selected':''?> <?=(!$val['important'])?'not-important':''?>"><label><input type="checkbox" data-attr-id="<?=$attr['id']?>" name="attr[]" value="<?=$val['id']?>" <?=$val['selected']?'checked':''?> <?=$val['setext']?'data-se-text=" '.$val['setext'].'"':''?> /><span></span><?=htmlentities($val['name'])?></label></li>
<?php       endforeach; ?>
</ul></div></td></tr>
<?php   endforeach; ?>
<?php endforeach; ?>
<?php endforeach; ?>
<?php if(!empty($languageList)): ?>
<tr><td class="label"><label><?=langTranslate('Default interface language','Interface')?></label></td><td>
<?php   
        $default_interface_lang = getPostVal('default_interface_lang',$userinfo['default_interface_lang']);
        foreach($languageList as $language): ?>
    <label class="radio"><input type="radio" name="default_interface_lang" value="<?=$language['id']?>" <?=($default_interface_lang==$language['id'])?'checked':''?> /><span></span><?=htmlentities($language['name'])?></label>
<?php   endforeach; ?>
</td></tr>
<?php endif; ?>
<?php 
if(!empty($languageList)):
    foreach($languageList as $language):
        $language_id = $language['id'];
    ?><tr><td class="header" colspan="2"><?=htmlentities($language['name'])?></td></tr>
<tr class="required"><td class="label"><label for="edit-user-edit-profile-form-lastname-<?=$language_id?>"><?=langTranslate('Last name', 'Last name');?></label></td><td><input type="text" id="edit-user-edit-profile-form-lastname-<?=$language_id?>" name="lastname[<?=$language_id?>]" value="<?=getPostVal('lastname['.$language_id.']',isset($userinfo['lastname'][$language_id])?$userinfo['lastname'][$language_id]:'')?>" /></td></tr>
<tr class="required"><td class="label"><label for="edit-user-edit-profile-form-firstname-<?=$language_id?>"><?=langTranslate('First name', 'First name');?></label></td><td><input type="text" id="edit-user-edit-profile-form-firstname-<?=$language_id?>" name="firstname[<?=$language_id?>]" value="<?=getPostVal('firstname['.$language_id.']',isset($userinfo['firstname'][$language_id])?$userinfo['firstname'][$language_id]:'')?>" /></td></tr>
<tr><td class="label"><label for="edit-user-edit-profile-form-patronymic-<?=$language_id?>"><?=langTranslate('Patronymic', 'Patronymic');?></label></td><td><input type="text" id="edit-user-edit-profile-form-patronymic-<?=$language_id?>" name="patronymic[<?=$language_id?>]" value="<?=getPostVal('patronymic['.$language_id.']',isset($userinfo['patronymic'][$language_id])?$userinfo['patronymic'][$language_id]:'')?>" /></td></tr>
<?php 
    endforeach; 
endif; ?>
<tr><td colspan="2" class="guide"><?=langTranslate('* - required for expert level request', '* - required for expert level request')?></td></tr>
<tr><td class="submit" colspan="2"><input type="submit" value="<?=langTranslate('Save', 'Save');?>" /></td></tr>
</tbody></table>
</form>
<?php langClean('User', 'editUserForm')?>