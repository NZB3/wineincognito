<?php 
    langSetDefault('product', 'vintage');
?>
<table class="subcontent vintageAltList">
<thead>
    <tr class="head-buttons"><th>

        <?php if($can_add): ?>
        <a class="add" href="<?=BASE_URL?>/product/<?=$product_id?>/vintage/add"></a>
        <?php endif; ?>
    </th></tr>
    <tr class="header"><th><?=langTranslate('Vintage', 'Vintage');?></th></tr>
</thead>
<tbody>
<?php if(empty($vintageList)): ?>
    <tr class="noentries"><td><?=langTranslate('No alternative vintages found', 'No alternative vintages found');?></td></tr>
<?php else: 
        foreach($vintageList as $vintage): ?>
    <tr><td><a href="<?=BASE_URL?>/vintage/<?=$vintage['id']?>"><?=htmlentities($vintage['name'])?>, <?=$vintage['year']?></a></td></tr>
<?php   endforeach;
      endif; ?>
</tbody>
</table>
<?php langClean('product', 'vintage')?>
