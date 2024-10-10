<table class="subcontent viewTasting-vintage-list duorows">
<thead>
    <tr><th></th><th></th><th class="name"><?=langTranslate('product','vintage','Name','Name')?></th><th><?=langTranslate('product','vintage','Volume','Volume')?></th><th class="preparation"><?=langTranslate('tasting','preparation','Preparation','Preparation')?></th></tr>
</thead>
<tbody>
    <tr data-tpv-id="<?=$vintage['tpv_id']?>"><td class="index"><?=$vintage['index']?></td><td class="image wi-gallery"><?=$vintage['img']?'<img src="'.$vintage['img'].'" />':''?></td><td class="name"><?=$vintage['id']?'<a href="'.BASE_URL.'/vintage/'.$vintage['id'].'" target="_blank">':''?><?=htmlentities($vintage['fullname'])?><?=$vintage['id']?'</a>':''?></td><td class="volume"><?=htmlentities($vintage['volume'])?></td><td class="preparation"><span class="preparation-text"><?=htmlentities($vintage['preparation_type_text'])?></span><?=$vintage['preparation_minutes_elapsed_pretty']?'<span class="preparation-time">'.$vintage['preparation_minutes_elapsed_pretty'].'</span>':''?></td></tr>
<?php if(strlen($vintage['desc'])): ?>  
    <tr class="desc"><td colspan="5"><?=prepareMultilineValue($vintage['desc'])?></td></tr>
<?php endif; ?>
</tbody>
</table>