<?php langSetDefault('product', 'vintage');
$step = $vintageinfo['id']||(isset($_POST['action'])&&$_POST['action']=='edit_vintage')?'step2':'step1';
$backurl = '';
if($vintageinfo['id']){
    $backurl = BASE_URL.'/vintage/'.$vintageinfo['id'];
} elseif(isset($blankvintageid)&&$blankvintageid){
    $backurl = BASE_URL.'/vintage/'.$blankvintageid;
} else {
    $backurl = BASE_URL.'/products';
}
$isblend = (bool)$blankvintageinfo['isblend'];
$grape_variety_concentration = array();
if(isset($_POST['grape_variety_concentration'])){
    $grape_variety_concentration = $_POST['grape_variety_concentration'];    
} else {
    $grape_variety_concentration = $vintageinfo['grape_variety_concentration'];
}
?>
<form method="POST" class="edit-vintage-form <?=$step?>" data-pid="<?=$blankvintageinfo['product_id']?>"><input type="hidden" name="action" value="edit_vintage" />
<script type="template" class="dropbox-template-select">
<tr><td class="label">{ifdef{name}}<label>{{name}}</label>{endifdef{name}}</td><td><div class="dropbox {if{multiple}}multiple{endif{multiple}}" data-fieldname="{{fieldname}}" data-group="{{group}}" data-has-children="{{haschildren}}" data-depth="{{depth}}" data-system="{{system}}" data-foundation_exclusive="{{foundation_exclusive}}" data-index="{{index}}"><input type="checkbox" id="dropbox-vintage-{{fieldname}}-{{group}}-{{depth}}" /><label for="dropbox-vintage-{{fieldname}}-{{group}}-{{depth}}"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul><li class="search"><input type="text" /></li>{{options}}</ul></div></td></tr>
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
<script type="template" class="grape-variety-concentration-row-template">
<tr data-id="{{id}}"><td><label for="grape-variety-concentration-{{id}}">{{name}}</label></td><td><input type="text" id="grape-variety-concentration-{{id}}" name="grape_variety_concentration[{{id}}]" {if{autogenerate}}class="autogenerate"{endif{autogenerate}} value="{{val}}" /></td></tr>
</script>
<script type="string" class="dropbox-template-empty-string"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></script>
<script type="template" class="dropbox-template-option-important-toggle">
<li class="dropbox-item-list-toggle show-not-important"><?=langTranslate('main','dropbox','Show not important', 'Show not important');?></li><li class="dropbox-item-list-toggle hide-not-important"><?=langTranslate('main','dropbox','Hide not important', 'Hide not important');?></li>
</script>
<table class="subcontent fieldlist editVintage">
<thead>
    <tr class="head-buttons"><th colspan="2"><a class="mainbtn back" href="<?=$backurl?>"><?=langTranslate('menu','navigation','Back','Back')?></a></th></tr>
    <tr class="header"><th colspan="2"><?=htmlentities($blankvintageinfo['name'])?></th></tr>
</thead>
<tbody>
<tr><td class="label"><label for="edit-vintage-form-year"><?=langTranslate('Year', 'Year');?></label></td><td><input type="text" name="year" class="edit-vintage-form-year" id="edit-vintage-form-year" value="<?=getPostVal('year',isset($vintageinfo['year'])?$vintageinfo['year']:'')?>" /></td></tr>
<tr class="step2"><td class="label"><label for="edit-vintage-form-alcohol-content"><?=langTranslate('Alcohol Content', 'Alcohol Content');?></label></td><td><input type="text" name="alcohol_content" id="edit-vintage-form-alcohol-content" value="<?=getPostVal('alcohol_content',isset($vintageinfo['alcohol_content'])?$vintageinfo['alcohol_content']:'')?>" /></td></tr>
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
        if($isblend&&$group==7){
            $grape_variety_concentration_html = '';
        }
?>
<tr class="step2 <?=$isblend&&$group==7?'grape-variety':''?>"><td class="label"><?=(count($depthvaltree)==1)?'<label>'.htmlentities($depthvaltree[$first_key]['name']).'</label>':'';?></td><td><div class="dropbox fresh <?=count($depthvaltree)==1&&count($depthvaltree[$first_key]['vals'])==1?'disabled':''?> <?=$depthvaltree[$first_key]['multiple']||($isblend&&$group==7)?'multiple':''?>" data-fieldname="attr" <?=$isblend&&$group==7?'data-custom="1"':''?> data-group="<?=$group?>" data-has-children="<?=$haschildren?>" data-depth="<?=$depth?>" data-index="<?=$depthvaltree[$first_key]['index']?>" data-foundation_exclusive="1"><input type="checkbox" id="dropbox-vintage-attr-<?=$group?>-<?=$depth?>" /><label for="dropbox-vintage-attr-<?=$group?>-<?=$depth?>"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul><li class="search"><input type="text" /></li>
<?php   foreach($depthvaltree as $attr):?>
<?php       if(count($depthvaltree)>1): ?>
<li class="header"><?=htmlentities($attr['name'])?></li>
<?php       endif; ?>
<?php       if($attr['can_add']==1): ?>
<li class="add" data-id="<?=$attr['id']?>"><?=langTranslate('product','attrval','Add','Add')?></li>
<?php       endif; ?>
<?php       foreach($attr['vals'] as $val): 
                if($isblend&&$group==7){
                    if(array_key_exists($val['id'], $grape_variety_concentration)){
                        $grape_variety_concentration_html .= '<tr data-id="'.$val['id'].'"><td><label for="grape-variety-concentration-'.$val['id'].'">'.htmlentities($val['name']).'</label></td><td><input type="text" id="grape-variety-concentration-'.$val['id'].'" name="grape_variety_concentration['.$val['id'].']" value="'.htmlentities($grape_variety_concentration[$val['id']]).'" /></td></tr>';
                    }
                }?>
    <li class="item <?=$val['selected']?'selected':''?> <?=(!$val['important'])?'not-important':''?>"><label><input type="checkbox" data-attr-id="<?=$attr['id']?>" name="attr[]" value="<?=$val['id']?>" <?=$val['selected']?'checked':''?> <?=$val['setext']?'data-se-text=" '.$val['setext'].'"':''?> /><span></span><?=htmlentities($val['name'])?></label></li>
<?php       endforeach; ?>
<?php   endforeach; ?>
</ul></div><?php 
        if($isblend&&$group==7):
            ?><table class="blend"><tbody><?=$grape_variety_concentration_html?></tbody></table><?php
        endif; 
?></td></tr>
<?php endforeach; ?>
<?php endforeach; ?>
<?php if(!isset($hide_description) || !$hide_description): ?>
<tr class="step2"><td class="tabblock" colspan="2">
<?php 
        if(count($languageList)>0):
            $labelWidth = floor(100/count($languageList));
            foreach($languageList as $language):
                $language_id = $language['id'];
                $language_name = $language['name']; 
                ?><input type="radio" id="edit-editattrval-form-language_tab_<?=$language_id?>" name="language_tab" class="ml_tab" value="<?=$language_id?>" /><label for="edit-editattrval-form-language_tab_<?=$language_id?>" style="width:<?=$labelWidth?>%"><?=htmlentities($language_name)?></label><?php 
            endforeach; ?>
</td></tr>
<?php       foreach($languageList as $language):
        $language_id = $language['id']; ?>
<tr class="step2 multilang lang_<?=$language_id?>"><td class="label"><label for="edit-vintage-form-desc-<?=$language_id?>"><?=langTranslate('Description', 'Description');?></label></td><td><textarea name="desc[<?=$language_id?>]" id="add-tasting-vintage-form-desc" /><?=getPostVal('desc['.$language_id.']',isset($vintageinfo['desc'][$language_id])?$vintageinfo['desc'][$language_id]:'')?></textarea></td></tr>
<?php       endforeach; 
        endif;
    endif;
?>
<tr class="step1"><td class="submit" colspan="2"><input type="button" class="edit-vintage-form-check-doubles" value="<?=langTranslate('Check', 'Check');?>" data-id="<?=$vintageinfo['id']?>" data-pid="<?=$vintageinfo['product_id']?>" /></td></tr>
<tr class="step2"><td class="submit" colspan="2"><input type="submit" value="<?=langTranslate('Save', 'Save');?>" /></td></tr>
</tbody></table>
</form>
<script language="JavaScript">
$(".edit-vintage-form #edit-vintage-form-year").mask("#999");
$(".edit-vintage-form #edit-vintage-form-alcohol-content").mask('99,99');
</script>
<?php langClean('product', 'vintage')?>