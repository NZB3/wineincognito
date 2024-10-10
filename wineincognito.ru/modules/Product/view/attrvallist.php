<?php langSetDefault('product', 'attrval'); ?>
<table class="subcontent attrvallist">
    <thead>
        <tr class="head-buttons"><th colspan="3"><a class="mainbtn back" href="<?=BASE_URL?>/moderate/product/attributes/<?=$attrinfo['attrgroup_id']?>"><?=langTranslate('menu','navigation','Back','Back')?></a><a class="add" href="<?=BASE_URL?>/moderate/product/attributes/<?=$attrinfo['attrgroup_id']?>/<?=$attrinfo['id']?>/add"></a></th></tr>
        <tr class="header"><th colspan="3"><?=htmlentities($attrinfo['name'])?></th></tr>
        <tr class="subheader"><th colspan="3"><?=langTranslate('Attribute Values','Attribute Values')?></tr>
        <tr><th><?=langTranslate('Name','Name')?></th><th class="separator"></th><th class="edit"></th></tr>
    </thead>
    <tfoot>
        <tr><th><?=langTranslate('Name','Name')?></th><th class="separator"></th><th class="edit"></th></tr>
    </tfoot>
    <tbody>
<?php foreach($attrvallist as $attrval): ?>
        <tr data-id="<?=$attrval['id']?>" <?=!$attrval['important']?'class="not-important"':''?> ><td class="name"><?=htmlentities($attrval['name'])?></td><td></td><td class="edit">
<?php if(isset($attrval['can_edit'])&&$attrval['can_edit']): ?>
            <a href="<?=BASE_URL?>/moderate/product/attributes/<?=$attrinfo['attrgroup_id']?>/<?=$attrinfo['id']?>/<?=$attrval['id']?>/edit"></a>
<?php endif; ?>
        </td></tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php langClean('product', 'attrval')?>
