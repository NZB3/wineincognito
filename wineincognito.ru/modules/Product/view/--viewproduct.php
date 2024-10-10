<?php 
    langSetDefault('product', 'product');
    $has_images = !empty($productinfo['images']);
    $attribute_count = count($productinfo['attributes']);
?>
<table class="subcontent viewProduct">
<thead>
    <tr class="head-buttons"><th colspan="<?=$has_images?3:2?>">
        <a class="mainbtn back" href="<?=BASE_URL?>/products"><?=langTranslate('menu','navigation','Back','Back')?></a>
        <?php if($productinfo['can_edit']): ?>
        <a class="edit" href="<?=BASE_URL?>/product/<?=$productinfo['id']?>/edit"></a>
        <?php endif; ?>
    </th></tr>
    <tr class="header"><th colspan="<?=$has_images?3:2?>"><?=htmlentities($productinfo['name'])?></th></tr>
</thead>
<tbody>
<tr>
<?php   if($has_images):?>
    <td rowspan="<?=max(1,$attribute_count)?>"  colspan="<?=$attribute_count?1:3?>" class="wi-gallery gallery">
<?php foreach($productinfo['images'] as $image):?>
<img src="<?=$image['url']?>" />
<?php endforeach; ?>
    </td>
<?php 
    endif; 
    if($attribute_count):
        $first_iter = true;
        foreach($productinfo['attributes'] as $attribute): 
            if(!$first_iter){
                echo '<tr>';
            } else {
                $first_iter = false;
            }?>
<td class="label"><label><?=htmlentities($attribute['label']);?></label></td><td class="value">
<?php if(count($attribute['values'])==1): ?>
<?=htmlentities($attribute['values'][0])?>
<?php else: ?>
    <ul>
<?php   foreach($attribute['values'] as $value): ?>
        <li><?=htmlentities($value)?></li>
<?php   endforeach; ?>
    </ul>
<?php endif; ?>
</td></tr>
<?php   
        endforeach; 
    else:
?>
</tr>
<?php endif; ?>
</tbody></table>
<?php langClean('product', 'product')?>
