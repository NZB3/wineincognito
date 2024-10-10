<?php langSetDefault('product', 'attrval'); ?>
<form method="POST" <?=!$is_modal?'data-dropbox-show-hidden="0"':''?> class="edit-editattrval-form">
    <input type="hidden" class="dropbox-parent-limit-<?=$attrinfo['attrgroup_id']?>" data-parent-attr-id="<?=$attrinfo['parent_id']?>" />
    <input type="hidden" name="action" value="edit_attrval" />
<table class="subcontent fieldlist editAttrVal">
<thead>
<?php if(!$is_modal): ?>
    <tr class="head-buttons"><th colspan="2"><a class="mainbtn back" href="<?=BASE_URL?>/moderate/product/attributes/<?=$attrinfo['attrgroup_id']?>/<?=$attrinfo['id']?>"><?=langTranslate('menu','navigation','Back','Back')?></a></th></tr>
<?php endif; ?>    
    <tr class="header"><th colspan="2"><?=htmlentities($attrinfo['name'])?></th></tr>
</thead>
<tbody>
<?php if($needparent):?>
<tr><td class="label"><label><?=langTranslate('Parent', 'Parent');?></label></td><td>
<script type="template" class="dropbox-template-select">
<tr><td class="label">{ifdef{name}}<label>{{name}}</label>{endifdef{name}}</td><td><div class="dropbox fresh {ifdef{disabled}}disabled{endifdef{disabled}}" data-fieldname="{{fieldname}}" data-group="{{group}}" data-has-children="{{haschildren}}" data-depth="{{depth}}" data-system="{{system}}" ><input type="checkbox" id="dropbox-{{fieldname}}-{{group}}-{{depth}}" /><label for="dropbox-{{fieldname}}-{{group}}-{{depth}}"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul>{{options}}</ul></div></td></tr>
</script>
<script type="template" class="dropbox-template-option">
<li class="item {if{selected}}selected{endif{selected}} {!if{important}}not-important{end!if{important}}"><label><input type="checkbox" data-attr-id="{{attrId}}" name="{{fieldname}}[]" value="{{id}}" {if{selected}}checked{endif{selected}} data-se-text=" {{setext}}" /><span></span>{{name}}</label></li>
</script>
<script type="template" class="dropbox-template-option-header">
<li class="header">{{name}}</li>
</script>
<script type="string" class="dropbox-template-empty-string"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></script>
<script type="template" class="dropbox-template-option-important-toggle">
<li class="dropbox-item-list-toggle show-not-important"><?=langTranslate('main','dropbox','Show not important', 'Show not important');?></li><li class="dropbox-item-list-toggle hide-not-important"><?=langTranslate('main','dropbox','Hide not important', 'Hide not important');?></li>
</script>
</td></tr>
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
<tr><td class="label"><?=(count($depthvaltree)==1)?'<label>'.htmlentities($depthvaltree[$first_key]['name']).'</label>':'';?></td><td><div class="dropbox fresh <?=(count($depthvaltree)==1)&&count($depthvaltree[$first_key]['vals'])==1?'disabled':''?>" data-fieldname="attr" data-group="<?=$group?>" data-has-children="<?=$haschildren?>" data-depth="<?=$depth?>" data-system="<?=$depthvaltree[$first_key]['system']?>"><input type="checkbox" id="dropbox-attr-<?=$group?>-<?=$depth?>" /><label for="dropbox-attr-<?=$group?>-<?=$depth?>"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul>
<?php   foreach($depthvaltree as $attr):?>
<?php       if(count($depthvaltree)>1): ?>
<li class="header"><?=htmlentities($attr['name'])?></li>
<?php       endif; ?>
<?php       foreach($attr['vals'] as $val): ?>
    <li class="item <?=$val['selected']?'selected':''?> <?=(!$val['important'])?'not-important':''?>"><label><input type="checkbox" data-attr-id="<?=$attr['id']?>" name="attr[]" value="<?=$val['id']?>" <?=$val['selected']?'checked':''?> <?=$val['setext']?'data-se-text=" '.$val['setext'].'"':''?> /><span></span><?=htmlentities($val['name'])?></label></li>
<?php       endforeach; ?>
</ul></div></td></tr>
<?php   endforeach; ?>
<?php endforeach; ?>
<?php endforeach; ?>
<?php endif; ?>
<?php if(isset($attrvalinfo['important'])): ?>
<tr><td><label for="edit-editattrval-form-important"><?=langTranslate('Important', 'Important');?></label></td><td><input type="checkbox" name="important" id="edit-editattrval-form-important" value="1" <?=($attrvalinfo['important'])?'checked':''?> /></td></tr>
<?php endif; ?>
<tr><td class="label"><label for="edit-editattrval-form-originname"><?=langTranslate('Origin Name', 'Origin Name');?></label></td><td><input type="text" id="edit-editattrval-form-originname" name="originname" value="<?=getPostVal('originname',isset($attrvalinfo['originname'])?$attrvalinfo['originname']:'')?>" /></td></tr>
<tr><td class="tabblock" colspan="2">
    <?php 
if(count($languageList)>0):
    $labelWidth = floor(100/count($languageList));
    foreach($languageList as $language):
        $language_id = $language['id'];
        $language_name = $language['name']; 
    ?><input type="radio" id="edit-editattrval-form-language_tab_<?=$language_id?>" name="language_tab" class="ml_tab" value="<?=$language_id?>" /><label for="edit-editattrval-form-language_tab_<?=$language_id?>" style="width:<?=$labelWidth?>%"><?=htmlentities($language_name)?></label><?php 
    endforeach; ?>
</td></tr>
<?php foreach($languageList as $language):
        $language_id = $language['id']; ?>
<tr class="multilang lang_<?=$language_id?>"><td class="label"><label for="edit-editattrval-form-name-<?=$language_id?>"><?=langTranslate('Name', 'Name');?></label></td><td><input type="text" id="edit-editattrval-form-name-<?=$language_id?>" name="name[<?=$language_id?>]" value="<?=getPostVal('name['.$language_id.']',isset($attrvalinfo['name'][$language_id])?$attrvalinfo['name'][$language_id]:'')?>" /></td></tr>
<?php endforeach; 
endif; ?>
<tr><td class="submit" colspan="2">
    <input type="submit" value="<?=langTranslate('Save', 'Save');?>" />
<?php if($is_modal): ?>
    <input type="button" class="close" value="<?=langTranslate('Cancel', 'Cancel');?>" />
<?php endif; ?>
</td></tr>
</tbody></table>
</form>
<?php langClean('product', 'attrval')?>