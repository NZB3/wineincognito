<?php langSetDefault('product', 'attrgroup'); 
$used_in_filter = getPostVal('used_in_filter',isset($attrgroup['used_in_filter'])?$attrgroup['used_in_filter']:0)?1:0;
$overload = getPostVal('overload',isset($attrgroup['overload'])?$attrgroup['overload']:0)?1:0;
$multiple = getPostVal('multiple',isset($attrgroup['multiple'])?$attrgroup['multiple']:0)?1:0;
$analog   = getPostVal('analog',  isset($attrgroup['analog'])?  $attrgroup['analog']  :0)?1:0;
$is_foundation = (isset($is_foundation)&&$is_foundation)?true:false;
if(isset($_POST)&&isset($_POST['action'])&&$_POST['action']=='edit_attrgroup'){
    $only_visible = (isset($_POST['visible'])&&is_array($_POST['visible']))?$_POST['visible']:array();
    $required = (isset($_POST['required'])&&is_array($_POST['required']))?$_POST['required']:array();
    $doublecheck = (isset($_POST['doublecheck'])&&is_array($_POST['doublecheck']))?$_POST['doublecheck']:array();
} else {
    $only_visible = (isset($attrgroup['only_visible'])&&is_array($attrgroup['only_visible']))?$attrgroup['only_visible']:array();
    $required = (isset($attrgroup['required'])&&is_array($attrgroup['required']))?$attrgroup['required']:array();
    $doublecheck = (isset($attrgroup['doublecheck'])&&is_array($attrgroup['doublecheck']))?$attrgroup['doublecheck']:array();
}
?>
<form method="POST"><input type="hidden" name="action" value="edit_attrgroup" />
<script type="template" class="dropbox-template-select">
<tr><td class="label">{ifdef{name}}<label>{{name}}</label>{endifdef{name}}</td><td><div class="dropbox multiple" data-fieldname="{{fieldname}}" data-group="{{group}}" data-has-children="{{haschildren}}" data-depth="{{depth}}" data-system="{{system}}"><input type="checkbox" id="dropbox-{{fieldname}}-{{group}}-{{depth}}" /><label for="dropbox-{{fieldname}}-{{group}}-{{depth}}"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul><li class="search"><input type="text" /></li>{{options}}</ul></div></td></tr>
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
<table class="subcontent fieldlist edit-attr-group">
<thead>
    <tr class="head-buttons"><th colspan="2"><a class="mainbtn back" href="<?=BASE_URL?>/moderate/product/attributes"><?=langTranslate('menu','navigation','Back','Back')?></a></th></tr>
</thead>
<tbody>
<tr><td class="label"><label for="edit-editattrgroup-form-zindex"><?=langTranslate('Index', 'Index');?></label></td><td><input type="text" id="edit-editattrgroup-form-zindex" name="zindex" value="<?=getPostVal('zindex',isset($attrgroup['zindex'])?$attrgroup['zindex']:10000)?>" /></td></tr>
<?php if((!isset($attrgroup['system'])||!$attrgroup['system'])&&!$is_foundation): ?>
    <tr><td colspan="2" class="header"><?=langTranslate('Visible only for', 'Visible only for');?></td></tr>
    <tr class="loading visible-for" data-group="<?=$foundation_id?>" data-values="<?=implode(',',$only_visible)?>"><td colspan="2"></td></tr>
    <tr><td colspan="2" class="header"><?=langTranslate('Required for', 'Required for');?></td></tr>
    <tr class="loading required-for" data-group="<?=$foundation_id?>" data-values="<?=implode(',',$required)?>"><td colspan="2"></td></tr>
    <tr><td colspan="2" class="header"><?=langTranslate('Has part in double check for', 'Has part in double check for');?></td></tr>
    <tr class="loading doublecheck-for" data-group="<?=$foundation_id?>" data-values="<?=implode(',',$doublecheck)?>"><td colspan="2"></td></tr>
<tr><td></td><td>
    <label class="radio"><input type="checkbox" name="used_in_filter" value="1" <?=($used_in_filter)?'checked':''?> /><span></span><?=langTranslate('Used in filter', 'Used in filter');?></label>
</td></tr>
<tr><td></td><td>
    <label class="radio"><input type="radio" name="overload" value="0" <?=($overload==0)?'checked':''?> /><span></span><?=langTranslate('Set in product', 'Set in product');?></label>
    <label class="radio"><input type="radio" name="overload" value="1" <?=($overload==1)?'checked':''?> /><span></span><?=langTranslate('Overload in vintage', 'Overload in vintage');?></label>
</td></tr>
<tr><td></td><td>
    <label class="radio"><input type="radio" name="multiple" value="0" <?=($multiple==0)?'checked':''?> /><span></span><?=langTranslate('Single select', 'Single select');?></label>
    <label class="radio"><input type="radio" name="multiple" value="1" <?=($multiple==1)?'checked':''?> /><span></span><?=langTranslate('Multiselect', 'Multiselect');?></label>
</td></tr>
<tr><td></td><td>
    <label class="radio"><input type="checkbox" name="analog" value="1" <?=($analog==1)?'checked':''?> /><span></span><?=langTranslate('Can have analogs', 'Can have analogs');?></label>
</td></tr>
<?php endif; ?>
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
<tr class="multilang lang_<?=$language_id?>"><td class="label"><label for="edit-editattrgroup-form-name-<?=$language_id?>"><?=langTranslate('Name', 'Name');?></label></td><td><input type="text" id="edit-editattrgroup-form-name-<?=$language_id?>" name="name[<?=$language_id?>]" value="<?=getPostVal('name['.$language_id.']',isset($attrgroup['name'][$language_id])?$attrgroup['name'][$language_id]:'')?>" /></td></tr>
<?php endforeach; 
endif; ?>
<tr><td class="submit" colspan="2"><input type="submit" value="<?=langTranslate('Save', 'Save');?>" /></td></tr>
</tbody></table>
</form>
<?php langClean('product', 'attrgroup')?>