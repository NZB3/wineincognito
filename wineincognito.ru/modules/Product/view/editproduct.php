<?php langSetDefault('product', 'product'); 
$isvintage = getPostVal('isvintage',isset($productinfo['isvintage'])?$productinfo['isvintage']:1)?1:0;
$isblend = getPostVal('blend',isset($productinfo['isblend'])&&$productinfo['isblend']?1:0)?1:0;
$grape_variety_concentration = array();
if(isset($_POST['grape_variety_concentration'])){
    $grape_variety_concentration = $_POST['grape_variety_concentration'];    
} else {
    $grape_variety_concentration = $productinfo['grape_variety_concentration'];
}
$step = $productinfo['id']||isset($_POST['action'])&&$_POST['action']=='edit_product'?'step2':'step1';
$backurl = (isset($blankvintageid)&&$blankvintageid)?BASE_URL.'/vintage/'.$blankvintageid:BASE_URL.'/products';
?>
<script type="string" id="confirm_string_delete_image"><?=langTranslate('Are you sure you want to delete this image?','Are you sure you want to delete this image?')?></script>
<form method="POST" id="edit-product-form" class="<?=$step?>"><input type="hidden" name="action" value="edit_product" />
<script type="template" class="dropbox-template-select">
<tr {!if{doublecheck}}class="step2"{end!if{doublecheck}}><td class="label">{ifdef{name}}<label>{{name}}</label>{endifdef{name}}</td><td><div class="dropbox {if{multiple}}multiple{endif{multiple}} {if{regionlock}}dropbox-regionlock{endif{regionlock}}" data-fieldname="{{fieldname}}" data-group="{{group}}" data-has-children="{{haschildren}}" data-depth="{{depth}}" data-system="{{system}}" data-doublecheck="{{doublecheck}}" data-foundation_exclusive="{{foundation_exclusive}}" data-index="{{index}}" {if{is_foundation}}data-loadsiblings="1"{endif{is_foundation}}><input type="checkbox" id="dropbox-{{fieldname}}-{{group}}-{{depth}}" /><label for="dropbox-{{fieldname}}-{{group}}-{{depth}}"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul><li class="search"><input type="text" /></li>{{options}}</ul></div></td></tr>
</script>
<script type="template" class="dropbox-template-option">
<li class="item {if{selected}}selected{endif{selected}} {!if{important}}not-important{end!if{important}} {if{regionlock_in_region}}regionlock-in-region{endif{regionlock_in_region}}"><label><input type="checkbox" data-attr-id="{{attrId}}" name="{{fieldname}}[]" value="{{id}}" {if{selected}}checked{endif{selected}} data-se-text=" {{setext}}"/><span></span>{{name}}</label></li>
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
<script type="template" class="dropbox-template-option-regionlock-toggle">
<li class="dropbox-item-list-toggle regionlock-show-out-of-region"><?=langTranslate('main','dropbox','Show non-regional', 'Show non-regional');?></li><li class="dropbox-item-list-toggle regionlock-hide-out-of-region"><?=langTranslate('main','dropbox','Hide non-regional', 'Hide non-regional');?></li>
</script>
<script type="template" id="edit-product-form-template-image-list">
<li {ifdef{primary}}class="primary"{endifdef{primary}}><input type="hidden" name="image_id[]" value="{{id}}" /><span class="helper"></span><img src="{{url}}" /><span class="delete"></span><span class="make-primary"></span></li>
</script>
<table class="subcontent fieldlist editProduct">
<thead>
    <tr class="head-buttons"><th colspan="2"><a class="mainbtn back" href="<?=$backurl?>"><?=langTranslate('menu','navigation','Back','Back')?></a></th></tr>
</thead>
<tbody>
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
<tr class="<?=!$depthvaltree[$first_key]['doublecheck']?'step2':''?> <?=$group==7?'grape-variety':''?>"><td class="label"><?=(count($depthvaltree)==1)?'<label>'.htmlentities($depthvaltree[$first_key]['name']).'</label>':'';?></td><td><?php 
        if($group==7):
            $grape_variety_concentration_html = '';
            ?><ul class="blend"><li><input type="radio" id="edit-product-form-blend-0" name="blend" value="0" <?=($isblend==0)?'checked':''?> /><label for="edit-product-form-blend-0"><?=langTranslate('Blend Sort', 'Sort');?></label></li><li><input type="radio" id="edit-product-form-blend-1" name="blend" value="1" <?=($isblend==1)?'checked':''?> /><label for="edit-product-form-blend-1"><?=langTranslate('Blend', 'Blend');?></label></li></ul><?php
        endif; 
?><div class="dropbox fresh <?=count($depthvaltree)==1&&count($depthvaltree[$first_key]['vals'])==1?'disabled':''?> <?=$depthvaltree[$first_key]['multiple']?'multiple':''?> <?=$depthvaltree[$first_key]['regionlock']?'dropbox-region-lock':''?>" data-fieldname="attr" <?=$group==7?'data-custom="1"':''?> data-group="<?=$group?>" data-has-children="<?=$haschildren?>" data-depth="<?=$depth?>" data-system="<?=$depthvaltree[$first_key]['system']?>" data-index="<?=$depthvaltree[$first_key]['index']?>" data-doublecheck="<?=$depthvaltree[$first_key]['doublecheck']?>"  data-foundation_exclusive="1" <?=$depthvaltree[$first_key]['is_foundation']?'data-loadsiblings="1"':''?>><input type="checkbox" id="dropbox-attr-<?=$group?>-<?=$depth?>" /><label for="dropbox-attr-<?=$group?>-<?=$depth?>"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul><li class="search"><input type="text" /></li>
<?php   foreach($depthvaltree as $attr):?>
<?php       if(count($depthvaltree)>1): ?>
<li class="header"><?=htmlentities($attr['name'])?></li>
<?php       endif; ?>
<?php       if($attr['can_add']==1): ?>
<li class="add" data-id="<?=$attr['id']?>"><?=langTranslate('product','attrval','Add','Add')?></li>
<?php       endif; ?>
<?php       foreach($attr['vals'] as $val): 
                if($group==7){
                    if(array_key_exists($val['id'], $grape_variety_concentration)){
                        $grape_variety_concentration_html .= '<tr data-id="'.$val['id'].'"><td><label for="grape-variety-concentration-'.$val['id'].'">'.htmlentities($val['name']).'</label></td><td><input type="text" id="grape-variety-concentration-'.$val['id'].'" name="grape_variety_concentration['.$val['id'].']" value="'.htmlentities($grape_variety_concentration[$val['id']]).'" /></td></tr>';
                    }
                }
?>
    <li class="item <?=$val['selected']?'selected':''?> <?=(!$val['important'])?'not-important':''?>"><label><input type="checkbox" data-attr-id="<?=$attr['id']?>" name="attr[]" value="<?=$val['id']?>" <?=$val['selected']?'checked':''?> <?=$val['setext']?'data-se-text=" '.$val['setext'].'"':''?> /><span></span><?=htmlentities($val['name'])?></label></li>
<?php       endforeach; ?>
<?php   endforeach; ?>
</ul></div><?php 
        if($group==7):
            ?><table class="blend"><tbody><?=$grape_variety_concentration_html?></tbody></table><?php
        endif; 
?></td></tr>
<?php endforeach; ?>
<?php endforeach; ?>
<tr class="step2"><td></td><td>
    <label class="radio"><input type="radio" name="isvintage" value="0" <?=($isvintage==0)?'checked':''?> /><span></span><?=langTranslate('Non-Vintage', 'Non-Vintage');?></label>
    <label class="radio"><input type="radio" name="isvintage" value="1" <?=($isvintage==1)?'checked':''?> /><span></span><?=langTranslate('Vintage', 'Vintage');?></label>
</td></tr>
<?php /* <tr class="step2"><td class="label"><label for="edit-product-form-vineyard"><?=langTranslate('Vineyard', 'Vineyard');?></label></td><td><input type="text" name="vineyard" id="edit-product-form-vineyard" value="<?=getPostVal('vineyard',isset($productinfo['vineyard'])?$productinfo['vineyard']:'')?>" /></td></tr> */ ?>
<tr class="step2"><td class="label"><label for="edit-product-form-alcohol-content"><?=langTranslate('Alcohol Content', 'Alcohol Content');?></label></td><td><input type="text" name="alcohol_content" id="edit-product-form-alcohol-content" value="<?=getPostVal('alcohol_content',isset($productinfo['alcohol_content'])?$productinfo['alcohol_content']:'')?>" /></td></tr>
<tr><td class="label"><label for="edit-product-form-origin-name"><?=langTranslate('Origin Name', 'Origin Name');?></label></td><td><input type="text" name="originname" id="edit-product-form-origin-name" value="<?=getPostVal('originname',isset($productinfo['originname'])?$productinfo['originname']:'')?>" /></td></tr>
<tr class="step2"><td class="tabblock" colspan="2">
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
<tr class="multilang lang_<?=$language_id?> step2"><td class="label"><label for="edit-product-form-name-<?=$language_id?>"><?=langTranslate('Name', 'Name');?></label></td><td><input type="text" class="edit-product-form-name" id="edit-product-form-name-<?=$language_id?>" data-lang="<?=$language_id?>" name="name[<?=$language_id?>]" value="<?=getPostVal('name['.$language_id.']',isset($productinfo['name'][$language_id])?$productinfo['name'][$language_id]:'')?>" /></td></tr>
<tr class="multilang lang_<?=$language_id?> step2"><td class="label"><label><?=langTranslate('Full name', 'Full name');?></label></td><td class="edit-product-form-full_name" data-template="<?=isset($productinfo['full_name_template'][$language_id])?htmlentities($productinfo['full_name_template'][$language_id]):'{{name}}'?>"></td></tr>
<?php endforeach; 
endif; ?>

<tr class="step1"><td class="submit" colspan="2"><input type="button" id="edit-product-form-check-doubles" value="<?=langTranslate('Check', 'Check');?>" data-id="<?=$productinfo['id']?>" /></td></tr>
<tr class="step2"><td class="header" colspan="2"><?=langTranslate('Images', 'Images');?></td></tr>
<tr class="step2"><td colspan="2"><ul id="edit-product-form-image-list" class="wi-gallery">
<?php foreach($productinfo['images'] as $image): ?>
<li <?=$image['primary']?'class="primary"':''?>><input type="hidden" name="image_id[]" value="<?=$image['id']?>" /><span class="helper"></span><img src="<?=$image['url']?>" /><?=$image['can_delete']?'<span class="delete"></span>':''?><span class="make-primary"></span></li>
<?php endforeach; ?>
</ul></td></tr>
<tr class="step2"><td class="submit" colspan="2"><input type="file" id="edit-product-form-add-image-file" multiple /><input type="button" id="edit-product-form-add-image" value="<?=langTranslate('Add Image', 'Add Image');?>" /></td></tr>
<tr class="step2"><td class="submit" colspan="2"><input type="submit" value="<?=langTranslate('Save', 'Save');?>" /></td></tr>
</tbody></table>
</form>
<script language="JavaScript">
$("#edit-product-form #edit-product-form-alcohol-content").mask('99,99');
</script>
<?php langClean('product', 'product')?>