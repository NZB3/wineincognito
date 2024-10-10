<?php langSetDefault('product', 'attr'); 
$parent = getPostVal('parent',isset($attr['parent'])?$attr['parent']:0);
$show_only_origin = getPostVal('show_only_origin',isset($attr['show_only_origin'])?$attr['show_only_origin']:0);
$has_important = getPostVal('has_important',isset($attr['has_important'])?$attr['has_important']:0);
?>
<form method="POST"><input type="hidden" name="action" value="edit_attr" />
<table class="subcontent fieldlist editAttr">
<thead>
    <tr class="head-buttons"><th colspan="2"><a class="mainbtn back" href="<?=BASE_URL?>/moderate/product/attributes/<?=$attrgroup_id?>"><?=langTranslate('menu','navigation','Back','Back')?></a></th></tr>
</thead>
<tbody>
<tr><td class="label"><label for="edit-editattr-form-parent"><?=langTranslate('Parent', 'Parent');?></label></td><td><select name="parent">
<?php foreach($possibleParentList as $possibleParent): ?>
    <option value="<?=$possibleParent['id']?>" <?=$parent==$possibleParent['id']?'selected':''?>><?=htmlentities($possibleParent['name'])?></option>
<?php endforeach; ?>
</select></td></tr>
<tr><td class="label top"><label><?=langTranslate('Display', 'Display');?></label></td><td>
    <label class="radio"><input type="radio" name="show_only_origin" value="0" <?=($show_only_origin==0)?'checked':''?> /><span></span><?=langTranslate('Show translation', 'Show translation');?></label>
    <label class="radio"><input type="radio" name="show_only_origin" value="1" <?=($show_only_origin==1)?'checked':''?> /><span></span><?=langTranslate('Show only origin name', 'Show only origin name');?></label>
</td></tr>
<tr><td class="label top"><label><?=langTranslate('Values', 'Values');?></label></td><td>
    <label class="radio"><input type="radio" name="has_important" value="0" <?=($has_important==0)?'checked':''?> /><span></span><?=langTranslate('Equivalent', 'Equivalent');?></label>
    <label class="radio"><input type="radio" name="has_important" value="1" <?=($has_important==1)?'checked':''?> /><span></span><?=langTranslate('Divided by importance', 'Divided by importance');?></label>
</td></tr>
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
<tr class="multilang lang_<?=$language_id?>"><td class="label"><label for="edit-editattr-form-name-<?=$language_id?>"><?=langTranslate('Name', 'Name');?></label></td><td><input type="text" id="edit-editattr-form-name-<?=$language_id?>" name="name[<?=$language_id?>]" value="<?=getPostVal('name['.$language_id.']',isset($attr['name'][$language_id])?$attr['name'][$language_id]:'')?>" /></td></tr>
<?php endforeach; 
endif; ?>
<tr><td class="submit" colspan="2"><input type="submit" value="<?=langTranslate('Save', 'Save');?>" /></td></tr>
</tbody></table>
</form>
<?php langClean('product', 'attr')?>