<?php 
    langSetDefault('product', 'vintage');
    $type_products = ($type=='products');
    $attribute_order = array();
    $attribute_labels = array();

    if($firstvintage){
        if($firstvintage['alcohol_content']!==null){
            array_unshift($firstvintage['attributes'], 
                array('id'=>'alcocont','label'=>langTranslate('Alcohol Content', 'Alcohol Content'),'values'=>array(array('value'=>$firstvintage['alcohol_content'].'%','part'=>null))));
        }
        foreach($firstvintage['attributes'] as $attribute){
            $attribute_order[] = $attribute['id'];
            $attribute_labels[$attribute['id']] = $attribute['label'];
        }
    }
    if($secondvintage){
        if($secondvintage['alcohol_content']!==null){
            array_unshift($secondvintage['attributes'], 
                array('id'=>'alcocont','label'=>langTranslate('Alcohol Content', 'Alcohol Content'),'values'=>array(array('value'=>$secondvintage['alcohol_content'].'%','part'=>null))));
        }
        $last_attribute_id = null;
        foreach($secondvintage['attributes'] as $attribute){
            $attribute_id = $attribute['id'];
            if(!in_array($attribute_id, $attribute_order)){
                $attribute_labels[$attribute_id] = $attribute['label'];

                if($last_attribute_id){
                    $splice_key = 0;
                    foreach($attribute_order as $attribute_order_id){
                        $splice_key++;
                        if($attribute_order_id == $last_attribute_id){
                            break;
                        }
                    }
                    array_splice($attribute_order, $splice_key, 0, $attribute_id);
                } else {
                    array_unshift($attribute_order, $attribute_id);
                }
            }
            $last_attribute_id = $attribute_id;
        }    
    }
    $firstvintage_full_name = $secondvintage_full_name = null;
    if($type_products){
        $firstvintage_full_name = htmlentities($firstvintage['name']);
        $secondvintage_full_name = htmlentities($secondvintage['name']);
    } else {
        $firstvintage_full_name = htmlentities($firstvintage['name']).($firstvintage['year']?', '.$firstvintage['year']:'').(!$firstvintage['isvintage']?', '.langTranslate('NV','NV'):'');
        $secondvintage_full_name = htmlentities($secondvintage['name']).($secondvintage['year']?', '.$secondvintage['year']:'').(!$secondvintage['isvintage']?', '.langTranslate('NV','NV'):'');
    }
    
?>
<table class="subcontent viewVintage compare" data-compare-url="<?=BASE_URL?>/products/compare">
<thead>
    <tr class="head-buttons"><th></th><th>
<?php if($type_products): ?>
        <span data-current="<?=$firstvintage?$firstvintage['product_id']:null?>" class="mainbtn select"><?=langTranslate('product','compare','Select','Select')?></span>
<?php endif; ?>
        <?php if($firstvintage): ?>
        <?php if($firstvintage['can_edit']): ?>
        <a class="edit" href="<?=BASE_URL?>/vintage/<?=$firstvintage['id']?>/edit"></a>
        <?php endif; ?>
        <?php if($firstvintage['can_favourite']||$firstvintage['favourite']): ?>
        <span class="favourite_btn <?=$firstvintage['favourite']?'favourite':''?> <?=$firstvintage['can_favourite']?'can-favourite':''?>" data-id="<?=$firstvintage['id']?>"></span>
        <?php endif; ?>
        <?php if($firstvintage['can_company_favourite']||$firstvintage['company_favourite']): ?>
        <span class="company_favourite_btn <?=$firstvintage['company_favourite']?'company-favourite':''?> <?=$firstvintage['can_company_favourite']?'can-favourite':''?>" data-id="<?=$firstvintage['product_id']?>"></span>
        <?php endif; ?>
        <?php if(isset($firstvintage['can_delete'])&&$firstvintage['can_delete']): ?>
        <script type="string" class="confirm_string_delete_product"><?=formatReplace(langTranslate('product','approve','Are you sure you want to delete @1?','Are you sure you want to delete @1?'),$firstvintage_full_name)?></script>
        <span data-slot="1" class="delete" data-id="<?=$firstvintage['id']?>"></span>
        <?php endif; ?>
        <?php if(isset($firstvintage['can_approve'])&&$firstvintage['can_approve']): ?>
        <span class="approve" data-id="<?=$firstvintage['product_id']?>"></span>
        <?php endif; ?>
        <?php endif; ?>
    </th><th>
<?php if($type_products): ?>
        <span data-current="<?=$secondvintage?$secondvintage['product_id']:null?>" class="mainbtn select"><?=langTranslate('product','compare','Select','Select')?></span>
<?php endif; ?>
        <?php if($secondvintage): ?>
        <?php if($secondvintage['can_edit']): ?>
        <a class="edit" href="<?=BASE_URL?>/vintage/<?=$secondvintage['id']?>/edit"></a>
        <?php endif; ?>
        <?php if($secondvintage['can_favourite']||$secondvintage['favourite']): ?>
        <span class="favourite_btn <?=$secondvintage['favourite']?'favourite':''?> <?=$secondvintage['can_favourite']?'can-favourite':''?>" data-id="<?=$secondvintage['id']?>"></span>
        <?php endif; ?>
        <?php if($secondvintage['can_company_favourite']||$secondvintage['company_favourite']): ?>
        <span class="company_favourite_btn <?=$secondvintage['company_favourite']?'company-favourite':''?> <?=$secondvintage['can_company_favourite']?'can-favourite':''?>" data-id="<?=$secondvintage['product_id']?>"></span>
        <?php endif; ?>
        <?php if(isset($secondvintage['can_delete'])&&$secondvintage['can_delete']): ?>
        <script type="string" class="confirm_string_delete_product"><?=formatReplace(langTranslate('product','approve','Are you sure you want to delete @1?','Are you sure you want to delete @1?'),$secondvintage_full_name)?></script>
        <span data-slot="2" class="delete" data-id="<?=$secondvintage['id']?>"></span>
        <?php endif; ?>
        <?php if(isset($secondvintage['can_approve'])&&$secondvintage['can_approve']): ?>
        <span class="approve" data-id="<?=$secondvintage['product_id']?>"></span>
        <?php endif; ?>
        <?php endif; ?>
    </th></tr>
    <tr class="header"><th></th>
        <th><?=$firstvintage_full_name?><?=isset($firstvintage['awaiting_approval'])&&$firstvintage['awaiting_approval']?'<span class="awaiting-approval">'.langTranslate('Awaiting approval','Awaiting approval').'</span>':''?></th>
        <th><?=$secondvintage_full_name?><?=isset($secondvintage['awaiting_approval'])&&$secondvintage['awaiting_approval']?'<span class="awaiting-approval">'.langTranslate('Awaiting approval','Awaiting approval').'</span>':''?></th>
    </tr>
</thead>
<tbody>
<tr>
<?php foreach($attribute_order as $attribute_order_id): ?>
    <tr><td class="label"><label><?=htmlentities($attribute_labels[$attribute_order_id]);?></label></td><td>
<?php   $found_attribute = null;
        if($firstvintage){
            foreach($firstvintage['attributes'] as $attribute){
                if($attribute['id']==$attribute_order_id){
                    $found_attribute = $attribute;
                    break;
                }
            }
        }
        if($found_attribute):
            if(count($attribute['values'])==1): 
                echo htmlentities($attribute['values'][0]['value']).($attribute['values'][0]['part']?' ('.$attribute['values'][0]['part'].'%)':'');
            else: ?>
    <ul>
<?php           foreach($attribute['values'] as $value): ?>
        <li><?=htmlentities($value['value'])?> <?=$value['part']?'('.$value['part'].'%)':''?></li>
<?php           endforeach; ?>
    </ul>
<?php       endif;
        endif; ?>
    </td><td>
<?php   $found_attribute = null;
        if($secondvintage){
            foreach($secondvintage['attributes'] as $attribute){
                if($attribute['id']==$attribute_order_id){
                    $found_attribute = $attribute;
                    break;
                }
            }
        }
        if($found_attribute):
            if(count($attribute['values'])==1): 
                echo htmlentities($attribute['values'][0]['value']).($attribute['values'][0]['part']?' ('.$attribute['values'][0]['part'].'%)':'');
            else: ?>
    <ul>
<?php           foreach($attribute['values'] as $value): ?>
        <li><?=htmlentities($value['value'])?> <?=$value['part']?'('.$value['part'].'%)':''?></li>
<?php           endforeach; ?>
    </ul>
<?php       endif;
        endif; ?>
    </td></tr>
<?php endforeach; ?>
<?php if($firstvintage&&strlen($firstvintage['desc']) || $secondvintage&&strlen($secondvintage['desc'])): ?>
<tr><td class="label"><label><?=langTranslate('Description', 'Description');?></label></td><td class="value description">
    <?=($firstvintage&&strlen($firstvintage['desc']))?prepareMultilineValue($firstvintage['desc']):''?>
</td><td class="value description">
    <?=($secondvintage&&strlen($secondvintage['desc']))?prepareMultilineValue($secondvintage['desc']):''?>
</td></tr>
<?php endif; ?>
<?php if($firstvintage&&$secondvintage):
    $has_approved = !isset($firstvintage['awaiting_approval'])||!$firstvintage['awaiting_approval']||!isset($firstvintage['secondvintage'])||!$firstvintage['secondvintage'];
?>
    <tr class="merge-into"><td></td><td>
<?php   if($type_products):?>
<?php       if(!$has_approved || !isset($firstvintage['awaiting_approval']) || !$firstvintage['awaiting_approval']): ?>
        <span class="mainbtn merge-into merge-products" data-id="<?=$firstvintage['product_id']?>" data-from-id="<?=$secondvintage['product_id']?>"><?=langTranslate('product','compare','Merge into','Merge into')?></span>
<?php       endif; ?>
    </td><td>
<?php       if(!$has_approved || !isset($secondvintage['awaiting_approval']) || !$secondvintage['awaiting_approval']): ?>
        <span class="mainbtn merge-into merge-products" data-id="<?=$secondvintage['product_id']?>" data-from-id="<?=$firstvintage['product_id']?>"><?=langTranslate('product','compare','Merge into','Merge into')?></span>
<?php       endif; ?>
<?php   else: ?>
<?php       if(!$has_approved || !isset($firstvintage['awaiting_approval']) || !$firstvintage['awaiting_approval']): ?>
        <span class="mainbtn merge-into merge-vintages" data-id="<?=$firstvintage['id']?>" data-from-id="<?=$secondvintage['id']?>"><?=langTranslate('product','compare','Merge into','Merge into')?></span>
<?php       endif; ?>
    </td><td>
<?php       if(!$has_approved || !isset($secondvintage['awaiting_approval']) || !$secondvintage['awaiting_approval']): ?>
        <span class="mainbtn merge-into merge-vintages" data-id="<?=$secondvintage['id']?>" data-from-id="<?=$firstvintage['id']?>"><?=langTranslate('product','compare','Merge into','Merge into')?></span>
<?php       endif; ?>
<?php   endif; ?>
    </td></tr>
<?php endif; ?>
</tbody></table>
<?php langClean('product', 'vintage')?>
