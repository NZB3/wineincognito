<?php 
    langSetDefault('product', 'review particularity');
?>
<form class="viewTasting-review-particularity-options subcontent" data-t-id="<?=$tasting_id?>">
<table class="subcontent compactable compact">
<thead>
    <tr class="header"><th colspan="3"><?=langTranslate('Review particularity options','Review particularity options')?></th></tr>
</thead>
<tbody>

<?php 
    $particularity_pasteparam_userfunc = function($group, $header) use ($review_particularity_data, $review_particularity_option_list, $review_elements){
        $result = '<tr class="header '.(!in_array($group, $review_particularity_option_list)?'support':'').'"><td>'.$header.'</td><td><input type="radio" name="'.$group.'" id="'.$group.'-0" value="0" '.(in_array($group, $review_particularity_data)?'checked="checked"':'').' /><label for="'.$group.'-0">'.langTranslate('Skip','Skip').'</label></td><td><input type="radio" name="'.$group.'" id="'.$group.'-1" value="1" '.(!in_array($group, $review_particularity_data)?'checked="checked"':'').' /><label for="'.$group.'-1">'.langTranslate('Request','Request').'</label></td></tr>';
        if(in_array($group, $review_particularity_option_list)){
            return $result;
        }
        if(isset($review_elements[$group])){
            $found_something = false;
            $add_balance_score = false;
            foreach($review_elements[$group] as $element){
                $name = $element['name'];
                if(in_array($name, $review_particularity_option_list)){
                    if(isset($element['class']) && strpos($element['class'],'balance-score')!==false){
                        $add_balance_score = true;
                        continue;
                    }
                    $result .= '<tr class="element element-'.$group.'" data-header-group="'.$group.'"><td>'.$element['caption'].'</td><td><input type="radio" name="'.$name.'" id="'.$name.'-0" value="0" '.(in_array($name, $review_particularity_data)?'checked="checked"':'').' /><label for="'.$name.'-0">'.langTranslate('Skip','Skip').'</label></td><td><input type="radio" name="'.$name.'" id="'.$name.'-1" value="1" '.(!in_array($name, $review_particularity_data)?'checked="checked"':'').' /><label for="'.$name.'-1">'.langTranslate('Request','Request').'</label></td></tr>';
                    $found_something = true;
                }
            }
            if($add_balance_score && in_array('balance-scores', $review_particularity_option_list)){
                $result .= '<tr class="element element-'.$group.'" data-header-group="'.$group.'"><td>'.langTranslate('product','review elements','Particularity - Balance scores','Balance scores').'</td><td><input type="radio" name="balance-scores" id="balance-scores-0" value="0" '.(in_array('balance-scores', $review_particularity_data)?'checked="checked"':'').' /><label for="balance-scores-0">'.langTranslate('Skip','Skip').'</label></td><td><input type="radio" name="balance-scores" id="balance-scores-1" value="1" '.(!in_array('balance-scores', $review_particularity_data)?'checked="checked"':'').' /><label for="balance-scores-1">'.langTranslate('Request','Request').'</label></td></tr>';
                $found_something = true;
            }
            if($found_something){
                return $result;
            }
        }
        return '';
    };
    $particularity_group_pasteparam_userfunc = function($elements, $group = null, $header = null) use ($review_particularity_data, $review_particularity_option_list){
        $result = '';
        foreach($elements as $key=>$label){
            if(!in_array($key, $review_particularity_option_list)){
                continue;
            }
            $result .= '<tr '.($group?' class="element element-'.$group.'" data-header-group="'.$group.'"':'').'><td>'.$label.'</td><td><input type="radio" name="'.$key.'" id="'.$key.'-0" value="0" '.(in_array($key, $review_particularity_data)?'checked="checked"':'').' /><label for="'.$key.'-0">'.langTranslate('Skip','Skip').'</label></td><td><input type="radio" name="'.$key.'" id="'.$key.'-1" value="1" '.(!in_array($key, $review_particularity_data)?'checked="checked"':'').' /><label for="'.$key.'-1">'.langTranslate('Request','Request').'</label></td></tr>';
        }
        if(strlen($result)){
            if($group && strlen($header)){
                $result = '<tr class="header support"><td>'.$header.'</td><td><input type="radio" name="'.$group.'" id="'.$group.'-0" value="0" /><label for="'.$group.'-0">'.langTranslate('Skip','Skip').'</label></td><td><input type="radio" name="'.$group.'" id="'.$group.'-1" value="1" checked="checked" /><label for="'.$group.'-1">'.langTranslate('Request','Request').'</label></td></tr>'.$result;
            }
            echo $result;
        }
    };
    $group = array();
    if(isset($review_elements['base_elements'])){
        foreach($review_elements['base_elements'] as $element){
            if(!isset($element['name']) || !isset($element['caption'])){
                continue;
            }
            $group[$element['name']] = $element['caption'];
        }
    }
    $group['color_spectrum_subcolor'] = langTranslate('product','review elements','Color spectrum - Tint','Tint');
    echo $particularity_group_pasteparam_userfunc($group,null,null);

    echo $particularity_pasteparam_userfunc('external_observation_elements',langTranslate('product','review elements','Observation','Observation'));
    echo $particularity_pasteparam_userfunc('sparkling_rating_elements',langTranslate('product','review elements','Perlage','Perlage'));
    echo $particularity_pasteparam_userfunc('faultcheck_elements',langTranslate('product','review elements','Faultcheck - Faulty','Faulty'));

    echo $particularity_pasteparam_userfunc('overall_aroma_elements',langTranslate('product','review elements','Nose. General characteristics','Nose. General characteristics'));
    echo $particularity_pasteparam_userfunc('primary_aroma_elements',langTranslate('product','review elements','Aromas. Primary aromas','Aromas. Primary aromas'));
    echo $particularity_pasteparam_userfunc('secondary_aroma_elements',langTranslate('product','review elements','Secondary aromas','Secondary aromas'));
    echo $particularity_pasteparam_userfunc('tertiary_aroma_elements',langTranslate('product','review elements','Tretiary Aromas','Tretiary Aromas'));

    $group = array(
            'detailed_aroma_elements'=>langTranslate('product','review elements','Particularity - Detailed aroma elements','Detailed aroma elements')
            );
    echo $particularity_group_pasteparam_userfunc($group,null,null);

    echo $particularity_pasteparam_userfunc('taste_elements',langTranslate('product','review elements','Palate. General characteristics','Palate. General characteristics'));
    echo $particularity_pasteparam_userfunc('taste_structure_elements',langTranslate('product','review elements','Structural characteristics','Structural characteristics'));
    echo $particularity_pasteparam_userfunc('primary_flavor_elements',langTranslate('product','review elements','Primary flavours','Primary flavours'));
    echo $particularity_pasteparam_userfunc('secondary_flavor_elements',langTranslate('product','review elements','Secondary flavours','Secondary flavours'));
    echo $particularity_pasteparam_userfunc('tertiary_flavor_elements',langTranslate('product','review elements','Tretiary flavours','Tretiary flavours'));

    $group = array(
            'similarity_location'=>langTranslate('product','review elements','Similarities - location','Location'),
            'similarity-year'=>langTranslate('product','vintage','Year', 'Year'),
            'similarity-alcohol-content'=>langTranslate('Product','vintage','Alcohol Content', 'Alcohol Content')
        );
    if(isset($review_elements['similarity_age_elements'])){
        foreach($review_elements['similarity_age_elements'] as $element){
            if(!isset($element['name']) || !isset($element['caption'])){
                continue;
            }
            $group[$element['name']] = $element['caption'];
        }
    }
    $group['similarity_grape'] = langTranslate('product','review elements','Grape variety','Grape variety');
    echo $particularity_group_pasteparam_userfunc($group,'similarity',langTranslate('product','review elements','Similarities','Similarities'));

    $group = array(
            'recommendation-temperature'=>langTranslate('product','review elements','Flow temperature (°C)','Flow temperature (°C)'),
            'recommendation-decantation'=>langTranslate('product','review elements','Decantation in a decanter (min.)','Decantation in a decanter (min.)'),
            'recommendation-open-time'=>langTranslate('product','review elements','Bottle opening time (min.)','Bottle opening time (min.)'),
            'recommendation-year'=>langTranslate('product','review elements','Drink (year)','Drink (year)'),
        );
    if(isset($review_elements['recommendation_elements'])){
        foreach($review_elements['recommendation_elements'] as $element){
            if(!isset($element['name']) || !isset($element['caption'])){
                continue;
            }
            $group[$element['name']] = $element['caption'];
        }
    }
    echo $particularity_group_pasteparam_userfunc($group,'recommendations',langTranslate('product','review elements','Recommendations','Recommendations'));
    echo '<tr class="header"><td>'.langTranslate('product','review','Score', 'Score').'</td><td><input type="radio" name="require_score_calc" id="require_score_calc-0" value="0" '.(in_array('require_score_calc', $review_particularity_data)?'checked="checked"':'').' /><label for="require_score_calc-0">'.langTranslate('Score submission - Score calc','Score calc').'</label></td><td><input type="radio" name="require_score_calc" id="require_score_calc-1" value="1" '.(!in_array('require_score_calc', $review_particularity_data)?'checked="checked"':'').' /><label for="require_score_calc-1">'.langTranslate('Score submission - Manual','Manual').'</label></td></tr>';
    $group = array(
            'personal_comment'=>langTranslate('product','review','Personal Comment', 'Personal Comment'),
            'review_text'=>langTranslate('product','review','Review','Review')
        );
    echo $particularity_group_pasteparam_userfunc($group,'review',langTranslate('product','review','Review','Review'));
?>
    <tr><td class="submit" colspan="3"><input type="submit" value="<?=langTranslate('tasting','tasting','Save', 'Save');?>" /></td></tr>
</tbody>
</table>
</form>
<?php langClean('tasting', 'review particularity')?>
