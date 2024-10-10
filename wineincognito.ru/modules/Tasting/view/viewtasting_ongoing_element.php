<?php 
    langSetDefault('tasting', 'tasting');
?>
<div class="subcontent">
<?php if($ongoing_tasting_user_status==2): 
        if($ranking_scoring): ?>
<a class="submitbtn" href="<?=BASE_URL?>/myreview/pending/tasting/<?=$tasting_id?>/ranking"><?=langTranslate('Navigate to pending ranking','Navigate to pending ranking')?></a>
<?php   else: ?>
<a class="submitbtn" href="<?=BASE_URL?>/myreview/pending/tasting/<?=$tasting_id?>/products"><?=langTranslate('Navigate to pending reviews','Navigate to pending reviews')?></a>
<?php   endif;
    endif; ?>
<?php if($ongoing_tasting_user_status==1): ?>
<div class="ext-infoBlock info"><?=langTranslate('Currently there are no pending reviews','Currently there are no pending reviews')?></div>
<?php endif; ?>
<?php if($ongoing_tasting_user_status==0): ?>
<div class="ext-infoBlock warning"><?=langTranslate('Your participation in this tasting hasn\'t been confirmed','Your participation in this tasting hasn\'t been confirmed')?></div>
<?php endif; ?>
</div>
<?php langClean('tasting', 'tasting')?>
