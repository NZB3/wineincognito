<?php langSetDefault('product', 'vintage'); ?>
<table class="subcontent vintagelist">
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
<?php foreach($vintagelist as $vintage): ?>
        <tr data-id="<?=$vintage['id']?>"><td class="image wi-gallery">
<?php   if($vintage['img']): ?>
            <img src="<?=$vintage['img']?>" />
<?php   endif; ?>
        </td><td class="name"><a href="<?=BASE_URL?>/vintage/<?=$vintage['id']?>"><?=htmlentities($vintage['name'])?>
<?php   if(isset($vintage['year'])&&$vintage['year']): ?>
            , <?=$vintage['year']?>
<?php endif; ?>
        </a></td><td></td><td class="edit">
<?php if(isset($vintage['can_edit'])&&$vintage['can_edit']): ?>
            <a href="<?=BASE_URL?>/vintage/<?=$vintage['id']?>/edit"></a>
<?php endif; ?>
        </td></tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php langClean('product', 'vintage')?>
