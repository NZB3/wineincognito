<?php langSetDefault('product', 'attrgroup'); ?>
<table class="subcontent attrgrouplist">
    <thead>
        <tr class="head-buttons"><th colspan="5"><a class="add" href="<?=BASE_URL?>/moderate/product/attributes/add"></a></th></tr>
        <tr class="header"><th colspan="5"><?=langTranslate('Attribute Groups','Attribute Groups')?></tr>
        <tr><th><?=langTranslate('Name','Name')?></th><th class="separator"></th><th class="index"><?=langTranslate('Index','Index')?></th><th class="hide"></th><th class="edit"></th></tr>
    </thead>
    <tfoot>
        <tr><th><?=langTranslate('Name','Name')?></th><th class="separator"></th><th class="index"><?=langTranslate('Index','Index')?></th><th class="hide"></th><th class="edit"></th></tr>
    </tfoot>
    <tbody>
<?php foreach($attrgrouplist as $attrgroup): ?>
        <tr data-id="<?=$attrgroup['id']?>" class="<?=$attrgroup['is_hidden']?'hidden':'visible'?>"><td class="name"><a href="<?=BASE_URL?>/moderate/product/attributes/<?=$attrgroup['id']?>"><?=htmlentities($attrgroup['name'])?></a></td><td></td><td class="index"><?=$attrgroup['zindex']?></td><td class="hide">
<?php if(isset($attrgroup['can_hide'])&&$attrgroup['can_hide']): ?>
            <span></span>
<?php endif; ?>
        </td><td class="edit">
<?php if(isset($attrgroup['can_edit'])&&$attrgroup['can_edit']): ?>
            <a href="<?=BASE_URL?>/moderate/product/attributes/<?=$attrgroup['id']?>/edit"></a>
<?php endif; ?>
        </td></tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php langClean('product', 'attrgroup')?>
