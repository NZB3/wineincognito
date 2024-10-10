<?php langSetDefault('tasting', 'tasting');
$backurl = '';
if(isset($tastinginfo['id'])&&$tastinginfo['id']){
    $backurl = BASE_URL.'/tasting/'.(int)$tastinginfo['id'];
} else {
    $backurl = BASE_URL.'/tastings';
}
?>
<form method="POST" id="edit-tasting-form"><input type="hidden" name="action" value="edit_tasting" />
<table class="subcontent fieldlist editTasting">
<thead>
    <tr class="head-buttons"><th colspan="3"><a class="mainbtn back" href="<?=$backurl?>"><?=langTranslate('menu','navigation','Back','Back')?></a></th></tr>
</thead>
<tbody>
<tr><td class="label"><label for="edit-tasting-form-name"><?=langTranslate('Name', 'Name');?></label></td><td colspan="2"><input type="text" name="name" id="edit-tasting-form-name" value="<?=getPostVal('name',isset($tastinginfo['name'])?$tastinginfo['name']:'')?>" maxlength="128" /></td></tr>
<tr><td class="label"><label for="edit-tasting-form-location"><?=langTranslate('Location', 'Location');?></label></td><td colspan="2"><input type="text" name="location" id="edit-tasting-form-location" value="<?=getPostVal('location',isset($tastinginfo['location'])?$tastinginfo['location']:'')?>" maxlength="512" /></td></tr>
<tr><td class="label"><label for="edit-tasting-form-start-date"><?=langTranslate('Start', 'Start');?></label></td><td colspan="2">
    <span class="form-date-time">
        <label for="edit-tasting-form-start-date"><?=langTranslate('Date', 'Date');?></label>
        <input type="text" name="start_date" id="edit-tasting-form-start-date" value="<?=getPostVal('start_date',isset($tastinginfo['startts'])?date('d.m.Y',$tastinginfo['startts']):'')?>" maxlength="10" />
        <label for="edit-tasting-form-start-time"><?=langTranslate('Time', 'Time');?></label>
        <input type="text" name="start_time" id="edit-tasting-form-start-time" value="<?=getPostVal('start_time',isset($tastinginfo['startts'])?date('H:i',$tastinginfo['startts']):'')?>" maxlength="5" />
        <span></span>
    </span>
</td></tr>
<tr><td class="label"><label for="edit-tasting-form-end-date"><?=langTranslate('End', 'End');?></label></td><td colspan="2">
    <span class="form-date-time">
        <label for="edit-tasting-form-end-date"><?=langTranslate('Date', 'Date');?></label>
        <input type="text" name="end_date" id="edit-tasting-form-end-date" value="<?=getPostVal('end_date',isset($tastinginfo['endts'])?date('d.m.Y',$tastinginfo['endts']):'')?>" maxlength="10" />
        <label for="edit-tasting-form-end-time"><?=langTranslate('Time', 'Time');?></label>
        <input type="text" name="end_time" id="edit-tasting-form-end-time" value="<?=getPostVal('end_time',isset($tastinginfo['endts'])?date('H:i',$tastinginfo['endts']):'')?>" maxlength="5" />
        <span></span>
    </span>
</td></tr>
<tr><td class="label"><label><?=langTranslate('Participation', 'Participation');?></label></td><td colspan="2">
    <label class="radio"><input type="radio" name="participation" value="0" <?=(getPostVal('participation',isset($tastinginfo['participation'])?$tastinginfo['participation']:0)==0)?'checked="checked"':''?>><span></span><?=langTranslate('Invite only', 'Invite only');?></label>
    <label class="radio"><input type="radio" name="participation" value="1" <?=(getPostVal('participation',isset($tastinginfo['participation'])?$tastinginfo['participation']:0)==1)?'checked="checked"':''?>><span></span><?=langTranslate('Limited by rating', 'Limited by rating');?></label>
    <label class="radio"><input type="radio" name="participation" value="2" <?=(getPostVal('participation',isset($tastinginfo['participation'])?$tastinginfo['participation']:0)==2)?'checked="checked"':''?>><span></span><?=langTranslate('Public Tasting', 'Public Tasting');?></label>
</td></tr>
<tr class="participation-rating"><td class="label"><label for="edit-tasting-participation-rating"><?=langTranslate('Rating Limitation', 'Rating Limitation');?></label></td><td colspan="2"><input type="text" name="participation_rating" class="rating-limitation" id="edit-tasting-participation-rating" value="<?=getPostVal('participation_rating',isset($tastinginfo['participation_rating'])?$tastinginfo['participation_rating']:'0')?>" /></td></tr>
<tr><td class="label"><label><?=langTranslate('Chargeability', 'Chargeability');?></label></td><td colspan="2">
    <label class="radio"><input type="radio" name="chargeability" value="0" <?=(getPostVal('chargeability',isset($tastinginfo['chargeability'])?$tastinginfo['chargeability']:0)==0)?'checked="checked"':''?>><span></span><?=langTranslate('Free', 'Free');?></label>
    <label class="radio"><input type="radio" name="chargeability" value="1" <?=(getPostVal('chargeability',isset($tastinginfo['chargeability'])?$tastinginfo['chargeability']:0)==1)?'checked="checked"':''?>><span></span><?=langTranslate('Chargeable', 'Chargeable');?></label>
</td></tr>
<tr class="price-grid"><td class="label"><label for="edit-tasting-price_grid_guest"><?=langTranslate('Price grid', 'Price grid');?></label></td><td class="label"><label for="edit-tasting-price_grid_guest"><?=langTranslate('Guest', 'Guest');?></label></td><td><input type="text" name="price_grid_guest" class="price" id="edit-tasting-price_grid_guest" value="<?=getPostVal('price_grid_guest',isset($tastinginfo['pricegrid']['guest_price'])?$tastinginfo['pricegrid']['guest_price']:'0')?>" /></td></tr>
<tr class="price-grid"><td></td><td class="label"><label for="edit-tasting-price_grid_expert"><?=langTranslate('Expert', 'Expert');?></label></td><td><input type="text" name="price_grid_expert" class="price" id="edit-tasting-price_grid_expert" value="<?=getPostVal('price_grid_expert',isset($tastinginfo['pricegrid']['expert_price'])?$tastinginfo['pricegrid']['expert_price']:'0')?>" /></td></tr>
<tr class="price-grid"><td></td><td class="label"><label><?=langTranslate('Experts with rating higher than @1', 'Experts with rating higher than @1');?><input type="text" name="price_grid_rated_expert_rating" class="rating-limitation edit-tasting-price_grid_rated_expert_rating" value="<?=getPostVal('price_grid_rated_expert_rating',isset($tastinginfo['pricegrid']['rated_expert_rating'])?$tastinginfo['pricegrid']['rated_expert_rating']:'0')?>" /></label></td><td><input type="text" name="price_grid_rated_expert" class="price" id="edit-tasting-price_grid_rated_expert" value="<?=getPostVal('price_grid_rated_expert',isset($tastinginfo['pricegrid']['rated_expert_price'])?$tastinginfo['pricegrid']['rated_expert_price']:'0')?>" /></td></tr>
<tr><td class="label"><label><?=langTranslate('Assessment', 'Assessment');?></label></td><td colspan="2">
    <label class="radio"><input type="radio" name="assessment" value="0" <?=(getPostVal('assessment',isset($tastinginfo['assessment'])?$tastinginfo['assessment']:1)==0)?'checked="checked"':''?>><span></span><?=langTranslate('Assessment - Private', 'Private');?></label>
    <label class="radio"><input type="radio" name="assessment" value="1" <?=(getPostVal('assessment',isset($tastinginfo['assessment'])?$tastinginfo['assessment']:1)==1)?'checked="checked"':''?>><span></span><?=langTranslate('Assessment - Public', 'Public');?></label>
</td></tr>
<tr><td class="label"><label><?=langTranslate('Score method', 'Score method');?></label></td><td colspan="2">
    <label class="radio"><input type="radio" name="scoremethod" value="0" <?=(getPostVal('scoremethod',isset($tastinginfo['score_method'])?$tastinginfo['score_method']:0)==0)?'checked="checked"':''?>><span></span><?=langTranslate('Score method - Review collection', 'Review collection');?></label>
    <label class="radio"><input type="radio" name="scoremethod" value="1" <?=(getPostVal('scoremethod',isset($tastinginfo['score_method'])?$tastinginfo['score_method']:0)==1)?'checked="checked"':''?>><span></span><?=langTranslate('Score method - Ranking collection', 'Ranking collection');?></label>
</td></tr>
<tr><td class="label"><label for="edit-tasting-form-desc"><?=langTranslate('Description', 'Description');?></label></td><td colspan="2"><textarea name="desc" id="edit-tasting-form-desc" /><?=getPostVal('desc',isset($tastinginfo['desc'])?$tastinginfo['desc']:'')?></textarea></td></tr>
<tr><td class="submit" colspan="3"><input type="submit" value="<?=langTranslate('Save', 'Save');?>" /></td></tr>
</tbody></table>
</form>
<?php langClean('tasting', 'tasting')?>