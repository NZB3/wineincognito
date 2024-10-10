<?php langSetDefault('product', 'product'); ?>
<table class="subcontent productlist">
    <thead>
        <tr class="head-buttons"><th colspan="4">
<?php if($can_add): ?>
            <a class="add" href="<?=BASE_URL?>/product/add"></a>
<?php endif; ?>
        </th></tr>
        <tr><th class="image"></th><th><?=langTranslate('Name','Name')?></th><th class="separator"></th><th class="edit"></th></tr>
    </thead>
    <tfoot>
        <tr><th class="image"></th><th><?=langTranslate('Name','Name')?></th><th class="separator"></th><th class="edit"></th></tr>
    </tfoot>
    <tbody>
<?php foreach($productlist as $product): ?>
        <tr data-id="<?=$product['id']?>"><td class="image wi-gallery">
<?php   if($product['img']): ?>
            <img src="<?=$product['img']?>" />
<?php   endif; ?>
        </td><td class="name"><a href="<?=BASE_URL?>/product/<?=$product['id']?>"><?=htmlentities($product['name'])?>
<?php   if(isset($product['year'])&&$product['year']): ?>
            , <?=$product['year']?>
<?php endif; ?>
        </a></td><td></td><td class="edit">
<?php if(isset($product['can_edit'])&&$product['can_edit']): ?>
            <a href="<?=BASE_URL?>/product/<?=$product['id']?>/edit"></a>
<?php endif; ?>
        </td></tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php langClean('product', 'product')?>
