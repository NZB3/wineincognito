<?php 
    langSetDefault('tasting', 'tasting');
?>
<table class="subcontent viewTasting-vintage-list">
<thead>
    <tr class="header"><th colspan="6"><?=langTranslate('Product List',  'Product List')?></th></tr>
    <tr><th></th><th></th><th></th><th class="name"><?=langTranslate('product','vintage','Name','Name')?></th><th><?=langTranslate('product','vintage','Volume','Volume')?></th><th class="preparation"><?=langTranslate('tasting','preparation','Preparation','Preparation')?></th></tr>
</thead>
<tbody>
<?php foreach($tasting_vintage_list as $vintage): ?>
    <tr><td class="review-existance <?=isset($vintage['review_exists'])&&$vintage['review_exists']?'review-exists':''?>"><span></span></td><td class="index"><?=$vintage['index']?></td><td class="image wi-gallery"><?=$vintage['img']?'<img src="'.$vintage['img'].'" />':''?></td><td class="name"><a href="<?=BASE_URL?>/myreview/pending/tasting/<?=$tasting_id?>/product/<?=$vintage['tpv_id']?>" /><?=htmlentities($vintage['fullname'])?></a></td><td class="volume"><?=htmlentities($vintage['volume'])?></td><td class="preparation"><span class="preparation-text"><?=htmlentities($vintage['preparation_type_text'])?></span><?=$vintage['preparation_minutes_elapsed_pretty']?'<span class="preparation-time">'.$vintage['preparation_minutes_elapsed_pretty'].'</span>':''?></td></tr>
<?php endforeach; ?>
</tbody>
</table>
<?php langClean('tasting', 'tasting')?>
