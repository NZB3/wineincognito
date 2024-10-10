<?php 
    langSetDefault('tasting', 'expert evaluation');
    if(!isset($expert_evaluation_template)){
        $expert_evaluation_template = false;
    }
    if(!isset($tasting_id)){
        $tasting_id = null;
    }
?>
<form class="viewTasting-expert-evaluation-options <?=$expert_evaluation_template?'expert-evaluation-template':''?> subcontent" <?=$expert_evaluation_template?'':'data-t-id="'.$tasting_id.'"'?>>
<table class="subcontent <?=$expert_evaluation_template?'show-automatic-evaluation-options':'compactable compact'?>">
<thead>
    <tr class="header"><th colspan="3"><?=langTranslate('Expert evaluation','Expert evaluation')?></th></tr>
</thead>
<tbody>
<?php if(!$expert_evaluation_template): ?>
    <tr class="evaluation-mode"><td class="label"><label for="viewTasting-expert-evaluation-options-manual-0"><?=langTranslate('Manual evaluation','Manual evaluation')?></label></td><td colspan="2"><ul class="count-2">
        <li><input type="radio" name="evaluation_manual" id="viewTasting-expert-evaluation-options-manual-0" value="0" <?=$tasting_evaluation_data['evaluation_manual']==0?'checked="checked"':''?>><label for="viewTasting-expert-evaluation-options-manual-0"><?=langTranslate('Disabled','Disabled')?></label></li><li><input type="radio" name="evaluation_manual" id="viewTasting-expert-evaluation-options-manual-1" value="1" <?=$tasting_evaluation_data['evaluation_manual']==1?'checked="checked"':''?>><label for="viewTasting-expert-evaluation-options-manual-1"><?=langTranslate('Enabled','Enabled')?></label></li>
    </ul></td></tr>
    <tr class="evaluation-mode"><td class="label"><label for="viewTasting-expert-evaluation-options-automatic-0"><?=langTranslate('Automatic evaluation','Automatic evaluation')?></label></td><td colspan="2"><ul class="count-3">
        <li><input type="radio" name="evaluation_automatic" class="viewTasting-expert-evaluation-options-automatic" id="viewTasting-expert-evaluation-options-automatic-0" value="0" <?=$tasting_evaluation_data['evaluation_automatic']==0?'checked="checked"':''?>><label for="viewTasting-expert-evaluation-options-automatic-0"><?=langTranslate('Disabled','Disabled')?></label></li><li><input type="radio" name="evaluation_automatic" class="viewTasting-expert-evaluation-options-automatic" id="viewTasting-expert-evaluation-options-automatic-1" value="1" <?=$tasting_evaluation_data['evaluation_automatic']==1?'checked="checked"':''?>><label for="viewTasting-expert-evaluation-options-automatic-1"><?=langTranslate('Hidden','Hidden')?></label></li><li><input type="radio" name="evaluation_automatic" class="viewTasting-expert-evaluation-options-automatic" id="viewTasting-expert-evaluation-options-automatic-2" value="2" <?=$tasting_evaluation_data['evaluation_automatic']==2?'checked="checked"':''?>><label for="viewTasting-expert-evaluation-options-automatic-2"><?=langTranslate('Public','Public')?></label></li>
    </ul></td></tr>
<?php endif; ?>
    <tr class="automatic-evaluation-options"><td colspan="3" class="header"><?=langTranslate('product','review','Score', 'Score');?></td></tr>
    <tr class="automatic-evaluation-options score-permissible-variation"><td class="label"><label><?=langTranslate('Permissible variation','Permissible variation')?></label></td><td colspan="2">
        <ul><li><input type="radio" name="score_permissible_variation" id="viewTasting-expert-evaluation-options-score-permissible-variation-0" value="0" <?=isset($tasting_evaluation_data['score_permissible_variation'])&&$tasting_evaluation_data['score_permissible_variation']==0?'checked="checked"':''?>><label for="viewTasting-expert-evaluation-options-score-permissible-variation-0"><?=langTranslate('Automatic','Automatic')?></label></li><li><input type="radio" name="score_permissible_variation" id="viewTasting-expert-evaluation-options-score-permissible-variation-1" value="1" <?=isset($tasting_evaluation_data['score_permissible_variation'])&&$tasting_evaluation_data['score_permissible_variation']==1?'checked="checked"':''?>><label for="viewTasting-expert-evaluation-options-score-permissible-variation-1"><?=langTranslate('Manual','Manual')?></label></li></ul>
        <input type="text" name="score_permissible_variation_value" class="viewTasting-expert-evaluation-options-score-permissible-variation-manual-value" value="<?=isset($tasting_evaluation_data['score_permissible_variation_value'])?htmlentities($tasting_evaluation_data['score_permissible_variation_value']):'0'?>" /></td></tr>
    <tr class="automatic-evaluation-options"><td colspan="2"><?=langTranslate('product','review','Score', 'Score');?></td><td class="score"><ul>
<?php   for($i=0;$i<=5;$i++): 
?><li><label><input type="radio" name="score" value="<?=$i?>" <?=isset($tasting_evaluation_data['score'])&&$tasting_evaluation_data['score']==$i?'checked="checked"':''?></label></li><?php 
        endfor; ?>
    </ul></td></tr>
<?php 
    $evaluation_pasteparam_userfunc = function($group, $header = null) use ($review_elements, $tasting_evaluation_data){
        if(!isset($review_elements[$group])){
            return;
        }
        $result = '';
        foreach($review_elements[$group] as $element){
            if(!isset($element['automatic-evaluation'])||!$element['automatic-evaluation']){
                continue;
            }
            $element_name = $element['name'];
            $result .= '<tr class="automatic-evaluation-options"><td colspan="2">'.(isset($element['caption'])?htmlentities($element['caption']):$element_name).'</td><td class="score"><ul>';
            for($i=0;$i<=5;$i++){
                $result .= '<li><label><input type="radio" name="'.$element_name.'" value="'.$i.'" '.(isset($tasting_evaluation_data[$element_name])&&$tasting_evaluation_data[$element_name]==$i?'checked="checked"':'').' /></label></li>';
            }
            $result .= '</ul></td></tr>';
        }
        if(strlen($result)){
            if($header){
                $result = '<tr class="automatic-evaluation-options"><td colspan="3" class="header">'.htmlentities($header).'</td></tr>'.$result;
            }
            echo $result;
        }
    };
    $evaluation_pasteparam_userfunc('external_observation_elements',langTranslate('product','review elements','Observation','Observation'));
    $evaluation_pasteparam_userfunc('sparkling_rating_elements',langTranslate('product','review elements','Perlage','Perlage'));
    $evaluation_pasteparam_userfunc('faultcheck_elements',langTranslate('product','review elements','Faultcheck - Faulty','Faulty'));
    $evaluation_pasteparam_userfunc('overall_aroma_elements',langTranslate('product','review elements','Nose. General characteristics','Nose. General characteristics'));
    $evaluation_pasteparam_userfunc('primary_aroma_elements',langTranslate('product','review elements','Aromas. Primary aromas','Aromas. Primary aromas'));
    $evaluation_pasteparam_userfunc('secondary_aroma_elements',langTranslate('product','review elements','Secondary aromas','Secondary aromas'));
    $evaluation_pasteparam_userfunc('tertiary_aroma_elements',langTranslate('product','review elements','Tretiary Aromas','Tretiary Aromas'));
    $evaluation_pasteparam_userfunc('taste_elements',langTranslate('product','review elements','Palate. General characteristics','Palate. General characteristics'));
    $evaluation_pasteparam_userfunc('taste_structure_elements',langTranslate('product','review elements','Structural characteristics','Structural characteristics'));
    $evaluation_pasteparam_userfunc('primary_flavor_elements',langTranslate('product','review elements','Primary flavours','Primary flavours'));
    $evaluation_pasteparam_userfunc('secondary_flavor_elements',langTranslate('product','review elements','Secondary flavours','Secondary flavours'));
    $evaluation_pasteparam_userfunc('tertiary_flavor_elements',langTranslate('product','review elements','Tretiary flavours','Tretiary flavours'));
    $evaluation_pasteparam_userfunc('similarity_age_elements',langTranslate('product','review elements','Similarities','Similarities'));
    // similarity_location
    echo '<tr class="automatic-evaluation-options"><td colspan="3" class="header">'.langTranslate('product','review elements','Similarities - location','Similarities - location').'</td></tr>';
    foreach($location_list as $location_row){
        echo '<tr class="automatic-evaluation-options"><td colspan="2">'.htmlentities($location_row['name']).'</td><td class="score"><ul>';
        for($i=0;$i<=5;$i++){
            echo '<li><label><input type="radio" name="similarity_location['.$location_row['id'].']" value="'.$i.'" '.(isset($tasting_evaluation_data['similarity_location'])&&isset($tasting_evaluation_data['similarity_location'][$location_row['id']])&&$tasting_evaluation_data['similarity_location'][$location_row['id']]==$i?'checked="checked"':'').'</label></li>';
        }
    }
    // similarity_grape
    echo '<tr class="automatic-evaluation-options"><td colspan="3" class="header">'.langTranslate('product','review elements','Grape variety','Grape variety').'</td></tr>';
    foreach($grape_variety_list as $grape_variety_row){
        echo '<tr class="automatic-evaluation-options"><td colspan="2">'.htmlentities($grape_variety_row['name']).'</td><td class="score"><ul>';
        for($i=0;$i<=5;$i++){
            echo '<li><label><input type="radio" name="similarity_grape['.$grape_variety_row['id'].']" value="'.$i.'" '.(isset($tasting_evaluation_data['similarity_grape'])&&isset($tasting_evaluation_data['similarity_grape'][$grape_variety_row['id']])&&$tasting_evaluation_data['similarity_grape'][$grape_variety_row['id']]==$i?'checked="checked"':'').'</label></li>';
        }
    }
    $evaluation_pasteparam_userfunc('recommendation_elements',langTranslate('product','review elements','Recommendations','Recommendations')); 
?>
    <tr><td class="submit" colspan="3"><input type="submit" value="<?=langTranslate('tasting','tasting','Save', 'Save');?>" /></td></tr>
</tbody>
</table>
</form>
<?php langClean('tasting', 'expert evaluation')?>
