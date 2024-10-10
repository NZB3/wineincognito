<?php 
    langSetDefault('product', 'vintage');
    $has_images = !empty($vintageinfo['images']);
    if($vintageinfo['score']){
        array_unshift($vintageinfo['attributes'], 
            array('label'=>langTranslate('Score', 'Score'),'values'=>array(array('value'=>$vintageinfo['score'],'part'=>null))));
    }
    if($vintageinfo['year']){
        array_unshift($vintageinfo['attributes'], 
            array('label'=>langTranslate('Year', 'Year'),'values'=>array(array('value'=>$vintageinfo['year'],'part'=>null))));
    }
    // if($vintageinfo['vineyard_name']!==null){
    //     array_push($vintageinfo['attributes'], 
    //         array('label'=>langTranslate('product','product','Vineyard', 'Vineyard'),'values'=>array(array('value'=>$vintageinfo['vineyard_name'],'part'=>null))));
    // }
    if($vintageinfo['alcohol_content']!==null){
        array_unshift($vintageinfo['attributes'], 
            array('label'=>langTranslate('Alcohol Content', 'Alcohol Content'),'values'=>array(array('value'=>$vintageinfo['alcohol_content'].'%','part'=>null))));
    }
    $attribute_count = count($vintageinfo['attributes'])+(strlen($vintageinfo['desc'])?1:0);
    $product_full_name = htmlentities($vintageinfo['name']).($vintageinfo['year']?', '.$vintageinfo['year']:'').(!$vintageinfo['isvintage']?', '.langTranslate('NV','NV'):'');
?>
<table class="subcontent viewVintage <?=(isset($compact)&&$compact)?'compactable compact':''?>">
<thead>
    <tr class="head-buttons"><th colspan="<?=$has_images?3:2?>">
        <a class="mainbtn back" href="<?=BASE_URL?>/products"><?=langTranslate('menu','navigation','Back','Back')?></a>
        <?php if($vintageinfo['can_edit']): ?>
        <a class="edit non-sticky-tooltip" data-tooltip="<?=langTranslate('Tooltip: Edit','Edit')?>" href="<?=BASE_URL?>/vintage/<?=$vintageinfo['id']?>/edit"></a>
        <?php endif; ?>
        <?php if($vintageinfo['can_favourite']||$vintageinfo['favourite']): ?>
        <span class="favourite_btn non-sticky-tooltip <?=$vintageinfo['favourite']?'favourite':''?> <?=$vintageinfo['can_favourite']?'can-favourite':''?>" data-tooltip="<?=langTranslate('Tooltip: Add to personal favourites','Add to personal favourites')?>" data-id="<?=$vintageinfo['id']?>"></span>
        <?php endif; ?>
        <?php if($vintageinfo['can_company_favourite']||$vintageinfo['company_favourite']): ?>
        <span class="company_favourite_btn non-sticky-tooltip <?=$vintageinfo['company_favourite']?'company-favourite':''?> <?=$vintageinfo['can_company_favourite']?'can-favourite':''?>" data-tooltip="<?=langTranslate('Tooltip: Add to company favourites','Add to company favourites')?>" data-id="<?=$vintageinfo['product_id']?>"></span>
        <?php endif; ?>
        <?php if(isset($vintageinfo['can_delete'])&&$vintageinfo['can_delete']): ?>
        <script type="string" class="confirm_string_delete_product"><?=formatReplace(langTranslate('product','approve','Are you sure you want to delete @1?','Are you sure you want to delete @1?'),$product_full_name)?></script>
        <span class="delete non-sticky-tooltip" data-tooltip="<?=langTranslate('Tooltip: Delete','Delete')?>" data-id="<?=$vintageinfo['id']?>"></span>
        <?php endif; ?>
        <?php if(isset($vintageinfo['can_compare'])&&$vintageinfo['can_compare']): ?>
        <a class="compare non-sticky-tooltip" data-tooltip="<?=langTranslate('Tooltip: Compare','Compare')?>" href="<?=BASE_URL?>/products/compare/<?=$vintageinfo['product_id']?>"></a>
        <?php endif; ?>
        <?php if(isset($vintageinfo['can_approve'])&&$vintageinfo['can_approve']): ?>
        <span class="approve non-sticky-tooltip" data-tooltip="<?=langTranslate('Tooltip: Approve','Approve')?>" data-id="<?=$vintageinfo['product_id']?>"></span>
        <?php endif; ?>
        <?php if(isset($vintageinfo['can_add_review'])&&$vintageinfo['can_add_review']): ?>
        <a class="add-review non-sticky-tooltip" data-tooltip="<?=langTranslate('Tooltip: Add personal review','Add personal review')?>" href="<?=BASE_URL?>/vintage/<?=$vintageinfo['id']?>/review/set"></a>
        <?php endif; ?>
    </th></tr>
    <tr class="header"><th colspan="<?=$has_images?3:2?>"><?=$product_full_name?><?=isset($vintageinfo['awaiting_approval'])&&$vintageinfo['awaiting_approval']?'<span class="awaiting-approval">'.langTranslate('Awaiting approval','Awaiting approval').'</span>':''?></th></tr>
</thead>
<tbody>
<tr>
<?php   if($has_images):?>
    <td rowspan="<?=max(1,$attribute_count+1)?>"  colspan="<?=$attribute_count?1:3?>" class="wi-gallery gallery">
<?php foreach($vintageinfo['images'] as $image):?>
<img src="<?=$image['url']?>" />
<?php endforeach; ?>
    </td>
<?php endif; ?>
<td class="label"><label><?=langTranslate('product','product','ID', 'ID')?></label></td><td class="value"><?=$vintageinfo['product_id']?></td></tr>
<?php
    if($attribute_count):
        foreach($vintageinfo['attributes'] as $attribute): 
?>
<tr><td class="label"><label><?=htmlentities($attribute['label']);?></label></td><td class="value">
<?php if(count($attribute['values'])==1): ?>
<?=htmlentities($attribute['values'][0]['value'])?> <?=$attribute['values'][0]['part']?'('.$attribute['values'][0]['part'].'%)':''?>
<?php else: ?>
    <ul>
<?php   foreach($attribute['values'] as $value): ?>
        <li><?=htmlentities($value['value'])?> <?=$value['part']?'('.$value['part'].'%)':''?></li>
<?php   endforeach; ?>
    </ul>
<?php endif; ?>
</td></tr>
<?php   
        endforeach; 
        if(strlen($vintageinfo['desc'])): ?>
<tr><td class="label"><label><?=langTranslate('Description', 'Description');?></label></td><td class="value description"><?=prepareMultilineValue($vintageinfo['desc'])?></td></tr>
<?php   endif;
    else:
?>
</tr>
<?php endif; ?>
</tbody></table>
<?php langClean('product', 'vintage')?>
