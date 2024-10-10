<?php langSetDefault('product', 'attrval-analog'); ?>
<table class="subcontent attrval-analog compactable compact" data-id="<?=$attrval_id?>">
    <thead>
        <tr class="head-buttons"><th colspan="2">
            <script type="template" class="analog-add-form-template"><form class="attrval-analog-add-form"><div class="dropbox fresh" data-custom="1"><input type="checkbox" id="dropbox-custom-analog-candidate-list" /><label for="dropbox-custom-analog-candidate-list"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul><li class="search"><input type="text" /></li><li class="dropbox-item-list-toggle show-not-important"><?=langTranslate('main','dropbox','Show not important', 'Show not important');?></li><li class="dropbox-item-list-toggle hide-not-important"><?=langTranslate('main','dropbox','Hide not important', 'Hide not important');?></li><?php foreach($possible_analog_list as $attrval): ?><li class="item <?=(!$attrval['important'])?'not-important':''?>"><label><input type="checkbox" value="<?=$attrval['id']?>" <?=$attrval['setext']?'data-se-text=" '.$attrval['setext'].'"':''?> /><span></span><?=htmlentities($attrval['name'])?></label></li><?php endforeach;?></ul></div><div class="submit-line"><input type="submit" value="<?=langTranslate('Add','Add')?>" /></div></form></script>
            <script type="template" class="analog-item-template"><tr data-id="{{id}}" {!if{important}}class="not-important"{end!if{important}} ><td class="name">{{name}}</td><td class="remove"><span></span></td></tr></script>
            <span class="add"></span>
        </th></tr>
        <tr class="header"><th colspan="2"><?=langTranslate('Analogs','Analogs')?></th></tr>
    </thead>
    <tbody>
<?php foreach($list as $attrval): ?>
        <tr data-id="<?=$attrval['id']?>" <?=!$attrval['important']?'class="not-important"':''?> ><td class="name"><?=htmlentities($attrval['name'])?></td><td class="remove"><span></span></td></tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php langClean('product', 'attrval')?>
