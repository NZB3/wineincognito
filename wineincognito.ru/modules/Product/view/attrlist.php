<?php langSetDefault('product', 'attr'); ?>
<table class="subcontent attrlist">
    <thead>
        <tr class="head-buttons"><th colspan="4"><a class="mainbtn back" href="<?=BASE_URL?>/moderate/product/attributes/"><?=langTranslate('menu','navigation','Back','Back')?></a><a class="add" href="<?=BASE_URL?>/moderate/product/attributes/<?=$attrgroupinfo['id']?>/add"></a></th></tr>
        <tr class="header"><th colspan="4"><?=htmlentities($attrgroupinfo['name'])?></th></tr>
        <tr class="subheader"><th colspan="4"><?=langTranslate('Attributes','Attributes')?></tr>
        <tr><th><?=langTranslate('Name','Name')?></th><th class="separator"></th><th class="hide"></th><th class="edit"></th></tr>
    </thead>
    <tfoot>
        <tr><th><?=langTranslate('Name','Name')?></th><th class="separator"></th><th class="hide"></th><th class="edit"></th></tr>
    </tfoot>
    <tbody>
<?php foreach($attrlist as $attr): ?>
        <tr data-id="<?=$attr['id']?>" class="<?=$attr['is_hidden']?'hidden':'visible'?>"><td class="name"><a href="<?=BASE_URL?>/moderate/product/attributes/<?=$attrgroupinfo['id']?>/<?=$attr['id']?>/"><?=htmlentities($attr['name'])?></a></td><td></td><td class="hide">
<?php if(isset($attr['can_hide'])&&$attr['can_hide']): ?>
            <span></span>
<?php endif; ?>
        </td><td class="edit">
<?php if(isset($attr['can_edit'])&&$attr['can_edit']): ?>
            <a href="<?=BASE_URL?>/moderate/product/attributes/<?=$attrgroupinfo['id']?>/<?=$attr['id']?>/edit"></a>
<?php endif; ?>
        </td></tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php langClean('product', 'attr')?>
