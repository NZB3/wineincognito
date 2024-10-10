<?php
    $can_be_primeur = ($vintageinfo['year']>=((int)date('Y'))-4);
    $personal = isset($personal)&&$personal;
?>
<form method="POST" class="edit-tasting-vintage-form" data-pid="<?=$vintage_id?>">
<input type="hidden" name="action" value="edit_tasting_vintage" />
<input type="hidden" name="id" value="<?=$vintage_id?>" />
<?php if(!$can_be_primeur): ?>
<input type="hidden" name="primeur" value="0" />
<?php endif; ?>
<script type="template" class="dropbox-template-select">
<tr><td class="label">{ifdef{name}}<label>{{name}}</label>{endifdef{name}}</td><td><div class="dropbox" data-fieldname="{{fieldname}}" data-group="{{group}}" data-has-children="{{haschildren}}" data-depth="{{depth}}" data-system="{{system}}"><input type="checkbox" id="dropbox-tasting-{{fieldname}}-{{group}}-{{depth}}" /><label for="dropbox-tasting-{{fieldname}}-{{group}}-{{depth}}"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul><li class="search"><input type="text" /></li>{{options}}</ul></div></td></tr>
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
<table class="subcontent fieldlist addTastingVintage">
<thead>
    <tr class="header"><th colspan="2"><?=htmlentities($vintageinfo['fullname'])?></th></tr>
</thead>
<tbody>
<?php if($can_be_primeur): ?>
    <tr class="edit-tasting-vintage-form-primeur"><td></td><td><ul><li><input type="radio" id="edit-tasting-vintage-form-primeur-0" name="primeur" value="0" <?=(getPostVal('primeur',$tasting_product_vintage_info['isprimeur'])==0)?'checked':''?> /><label for="edit-tasting-vintage-form-primeur-0">Бутилировано</label></li><li><input type="radio" id="edit-tasting-vintage-form-primeur-1" name="primeur" value="1" <?=(getPostVal('primeur',$tasting_product_vintage_info['isprimeur'])==1)?'checked':''?> /><label for="edit-tasting-vintage-form-primeur-1"><?=langTranslate('tasting','vintage','En primeur', 'En primeur');?></label></li></ul></td></tr>
<?php endif; ?>
    <tr class="edit-tasting-vintage-form-lot"><td class="label"><label for="edit-tasting-vintage-form-lot"><?=langTranslate('tasting','vintage','Lot','Lot')?></label></td><td><input type="text" id="edit-tasting-vintage-form-lot" maxlength="6" name="lot" value="<?=getPostVal('lot',$tasting_product_vintage_info['lot'])?>" /></td></tr>
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
<tr><td class="label"><?=(count($depthvaltree)==1)?'<label>'.htmlentities($depthvaltree[$first_key]['name']).'</label>':'';?></td><td><div class="dropbox fresh <?=count($depthvaltree)==1&&count($depthvaltree[$first_key]['vals'])==1?'disabled':''?> <?=$depthvaltree[$first_key]['multiple']?'multiple':''?>" data-fieldname="attr" data-group="<?=$group?>" data-has-children="<?=$haschildren?>" data-depth="<?=$depth?>" data-system="<?=$depthvaltree[$first_key]['system']?>"><input type="checkbox" id="dropbox-tasting-attr-<?=$group?>-<?=$depth?>" /><label for="dropbox-tasting-attr-<?=$group?>-<?=$depth?>"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul><li class="search"><input type="text" /></li>
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
<?php if(!$personal): ?>
<tr><td></td><td><label class="radio"><input type="checkbox" name="blind" class="edit-tasting-vintage-form-blind" value="1" <?=getPostVal('blind',$tasting_product_vintage_info['isblind'])?'checked="checked"':''?> /><span></span><?=langTranslate('tasting','tasting','Blind tasting', 'Blind tasting');?></label></td></tr>
<tr class="edit-tasting-vintage-form-blindname"><td class="label"><label for="edit-tasting-vintage-form-blindname"><?=langTranslate('tasting','tasting','Blind name','Blind name')?></label></td><td><input type="text" id="edit-tasting-vintage-form-blindname" name="blindname" value="<?=getPostVal('desc',strlen($tasting_product_vintage_info['blindname'])?$tasting_product_vintage_info['blindname']:$vintageinfo['fullname'])?>" /></td></tr>
<?php endif; ?>
<tr><td class="label"><label for="edit-tasting-vintage-form-desc"><?=langTranslate('product','vintage','Description','Description')?></label></td><td><textarea name="desc" id="edit-tasting-vintage-form-desc"><?=getPostVal('desc',$tasting_product_vintage_info['desc'])?></textarea></td></tr>
<?php if(!$personal): ?>
<tr><td></td><td><label class="radio"><input type="checkbox" name="nominate" value="1"><span></span><?=langTranslate('product','vintage','Nominate to replace public description', 'Nominate to replace public description');?></label></td></tr>
<?php endif; ?>
<tr><td class="submit" colspan="2"><input type="submit" value="<?=langTranslate('product','vintage','Save', 'Save');?>" /></td></tr>
</tbody></table>
</form>