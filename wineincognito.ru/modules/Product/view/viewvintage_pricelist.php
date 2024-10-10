<?php 
    $volume_id_list = array();
?>
<table class="subcontent view-vintage-pricelist compactable <?=(isset($compact)&&$compact)?'compact':''?>">
<thead>
    <tr class="header"><th colspan="<?=1+count($volumelist)?>"><?=langTranslate('product','vintagepricelist','Price List','Price List')?></th></tr>
    <tr><th><?=langTranslate('User','companylist','Name','Name')?></th><?php
        foreach($volumelist as $volume):
            $volume_id_list[] = $volume['id'];
            ?><th><?=htmlentities($volume['name'])?></th><?php
        endforeach;
    ?></tr>
</thead>
<tbody><?php
    foreach($pricelist as $pricerow):
?><tr><td class="name"><?=htmlentities($pricerow['name'])?></td><?php
        foreach($volume_id_list as $volume_id):
            ?><td><?php
            if(isset($pricerow['prices'][$volume_id])):
                if($pricerow['prices'][$volume_id]['url']):
                    ?><a href="<?=$pricerow['prices'][$volume_id]['url']?>"><?=$pricerow['prices'][$volume_id]['price']?></a><?php
                else:
                    echo $pricerow['prices'][$volume_id]['price'];
                endif;
            endif;
            ?></td><?php
        endforeach;
    endforeach;
?></tbody></table>

