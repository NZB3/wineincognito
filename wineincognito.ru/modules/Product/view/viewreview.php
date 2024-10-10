<table class="subcontent view-review">
<thead>
<?php if($reviewinfo['can_edit']): ?>    
    <tr class="head-buttons"><th>
        <a class="edit" href="<?=BASE_URL?>/vintage/<?=$reviewinfo['vintage_id']?>/review/<?=$reviewinfo['id']?>/edit"></a>
    </th></tr>
<?php endif; ?>    
</thead>
<tbody>
<tr><td class="score" colspan="2"><?=$reviewinfo['score']?></td></tr>
<?php if(strlen($reviewinfo['review'])): ?>
<tr><td class="review" colspan="2"><span class="quote-open"></span><?=prepareMultilineValue($reviewinfo['review'])?><span class="quote-close"></span></td></tr>
<tr><td class="author" colspan="2"><a href="<?=BASE_URL?>/user/<?=$reviewinfo['author_id']?>"><?=$reviewinfo['author_name']?></a></td></tr>
<?php endif; ?>
<?php if(strlen($reviewinfo['personal_comment'])): ?>
<tr><td class="personal-comment" colspan="2"><?=prepareMultilineValue($reviewinfo['personal_comment'])?></td></tr>
<?php endif; ?>
<?php if(!isset($short_review) || !$short_review):?>
<?php 
    $pasteparam_userfunc = function($group, $header = null,$return=false) use ($reviewinfo, $review_elements){
        if(!isset($review_elements[$group])){
            return;
        }
        $result = '';
        foreach($review_elements[$group] as $element){
            if(!isset($reviewinfo['params'][$element['name']])){
                continue;
            }
            $int_values = $reviewinfo['params'][$element['name']];
            if(!is_array($int_values)){
                $int_values = array($int_values);
            }
            foreach($int_values as $key=>$int_value){
                if($int_value==0){
                    unset($int_values[$key]);
                }
            }
            if(empty($int_values)){
                continue;
            }
            $list_values = array();
            foreach($element['values'] as $elem_val){
                if(in_array($elem_val['value'], $int_values)){
                    $list_values[] = array($elem_val['label'],isset($elem_val['image'])?$elem_val['image']:null);
                }
            }
            if(!empty($list_values)){
                $result .= '<tr><td class="label">'.(isset($element['caption'])?htmlentities($element['caption']):'').'</td><td class="val">';
                if(count($list_values)>1){
                    $result .= '<ul>';
                    foreach($list_values as $list_value){
                        $result .= '<li>';
                        if(strlen($list_value[1])){
                            $result .= '<img src="'.$list_value[1].'" />';
                        }
                        $result .= htmlentities($list_value[0]).'</li>';
                    }
                    $result .= '</ul>';
                } else {
                    $result .= '<span>';
                    if(strlen($list_values[0][1])){
                        $result .= '<img src="'.$list_values[0][1].'" />';
                    }
                    $result .= htmlentities($list_values[0][0]).'</span>';
                }
                
                $result .= '</td></tr>';
            }
        }
        if(strlen($result)){
            if($header){
                $result = '<tr><td colspan="2" class="header">'.htmlentities($header).'</td></tr>'.$result;
            }
            if($return){
                return $result;
            } else {
                echo $result;    
            }
        }
        if($return){
            return '';
        }
    };
    $pasteparam_userfunc('base_elements');
?>
<?php 
    if(isset($reviewinfo['params']['color-spectrum']) &&
        isset($reviewinfo['params']['color-spectrum-subcolor']) &&
        isset($reviewinfo['params']['color-spectrum-depth'])):
        $colorname = null;
        foreach($review_elements['color_spectrum_subcolor_data'] as $subcolor_data){
            if($subcolor_data['color']==$reviewinfo['params']['color-spectrum'] && 
                    $subcolor_data['subcolor']==$reviewinfo['params']['color-spectrum-subcolor'] && 
                    $subcolor_data['depth']==$reviewinfo['params']['color-spectrum-depth']){
                $colorname = $subcolor_data['title'];
                break;
            }
        }
        if($colorname):
?><tr><td class="label"><?=langTranslate('product','review elements','Color spectrum - Tint','Tint')?></td><td><?=htmlentities($colorname)?></td></tr><?php
        endif;
    endif;
?>
<?php
    $pasteparam_userfunc('external_observation_elements',langTranslate('product','review elements','Observation','Observation'));
    $pasteparam_userfunc('sparkling_rating_elements',langTranslate('product','review elements','Perlage','Perlage'));
    // $pasteparam_userfunc('consistency_analysis_elements','Оценка консистенции');
    $pasteparam_userfunc('faultcheck_elements',langTranslate('product','review elements','Faultcheck - Faulty','Faulty'));
    $pasteparam_userfunc('overall_aroma_elements',langTranslate('product','review elements','Nose. General characteristics','Nose. General characteristics'));
    $pasteparam_userfunc('primary_aroma_elements',langTranslate('product','review elements','Aromas. Primary aromas','Aromas. Primary aromas'));
    $pasteparam_userfunc('secondary_aroma_elements',langTranslate('product','review elements','Secondary aromas','Secondary aromas'));
    $pasteparam_userfunc('tertiary_aroma_elements',langTranslate('product','review elements','Tretiary Aromas','Tretiary Aromas'));
    $pasteparam_userfunc('taste_elements',langTranslate('product','review elements','Palate. General characteristics','Palate. General characteristics'));
    $pasteparam_userfunc('taste_structure_elements',langTranslate('product','review elements','Structural characteristics','Structural characteristics'));
    $pasteparam_userfunc('primary_flavor_elements',langTranslate('product','review elements','Primary flavours','Primary flavours'));
    $pasteparam_userfunc('secondary_flavor_elements',langTranslate('product','review elements','Secondary flavours','Secondary flavours'));
    $pasteparam_userfunc('tertiary_flavor_elements',langTranslate('product','review elements','Tretiary flavours','Tretiary flavours'));
?>

<?php 
    $similarity = '';
    if(isset($reviewinfo['params']['similarity_location'])){
        $similarity .= '<tr><td class="label">'.langTranslate('product','review elements','Similarities - location','Similarities - location').'</td><td><ul>';
        foreach($reviewinfo['params']['similarity_location'] as $similarity_location){
            $similarity .= '<li>'.htmlentities($similarity_location).'</li>';
        }
        $similarity .= '</ul></td></tr>';
    }
    if(isset($reviewinfo['params']['similarity-year-nv'])&&$reviewinfo['params']['similarity-year-nv']){
        $similarity .= '<tr><td class="label">'.langTranslate('product','vintage','Year', 'Year').'</td><td>'.langTranslate('product','vintage','NV','NV').'</td></tr>';
    } elseif(isset($reviewinfo['params']['similarity-year'])){
        $similarity .= '<tr><td class="label">'.langTranslate('product','vintage','Year', 'Year').'</td><td>'.$reviewinfo['params']['similarity-year'].'</td></tr>';
    }
    if(isset($reviewinfo['params']['similarity-alcohol-content'])){
        $similarity .= '<tr><td class="label">'.langTranslate('Product','vintage','Alcohol Content', 'Alcohol Content').'</td><td>'.$reviewinfo['params']['similarity-alcohol-content'].'%</td></tr>';
    }
    $similarity .= $pasteparam_userfunc('similarity_age_elements',null,true);
    if(isset($reviewinfo['params']['similarity_grape'])){
        $similarity .= '<tr><td class="label">'.langTranslate('product','review elements','Grape variety','Grape variety').'</td><td><ul>';
        foreach($reviewinfo['params']['similarity_grape'] as $similarity_grape){
            $similarity .= '<li>'.htmlentities($similarity_grape).'</li>';
        }
        $similarity .= '</ul></td></tr>';
    }
    if(strlen($similarity)){
        echo '<tr><td colspan="2" class="header">'.langTranslate('product','review elements','Similarities','Similarities').'</td></tr>'.$similarity;
    }
    unset($similarity);
?>

<?php
    $recommendation = '';
    if(isset($reviewinfo['params']['recommendation-temperature_from'])||isset($reviewinfo['params']['recommendation-temperature_to'])){
        $recommendation .= '<tr><td class="label">'.langTranslate('product','review elements','Flow temperature (°C)','Flow temperature (°C)').'</td><td>'.(isset($reviewinfo['params']['recommendation-temperature_from'])?'от '.$reviewinfo['params']['recommendation-temperature_from']:'').' '.(isset($reviewinfo['params']['recommendation-temperature_to'])?'до '.$reviewinfo['params']['recommendation-temperature_to']:'').'</td></tr>';
    }
    if(isset($reviewinfo['params']['recommendation-decantation'])){
        $recommendation .= '<tr><td class="label">'.langTranslate('product','review elements','Decantation in a decanter (min.)','Decantation in a decanter (min.)').'</td><td>'.$reviewinfo['params']['recommendation-decantation'].'</td></tr>';
    }
    if(isset($reviewinfo['params']['recommendation-open-time'])){
        $recommendation .= '<tr><td class="label">'.langTranslate('product','review elements','Bottle opening time (min.)','Bottle opening time (min.)').'</td><td>'.$reviewinfo['params']['recommendation-open-time'].'</td></tr>';
    }
    if(isset($reviewinfo['params']['recommendation-year_from'])||isset($reviewinfo['params']['recommendation-year_to'])){
        $recommendation .= '<tr><td class="label">'.langTranslate('product','review elements','Drink (year)','Drink (year)').'</td><td>'.(isset($reviewinfo['params']['recommendation-year_from'])?'c '.$reviewinfo['params']['recommendation-year_from']:'').' '.(isset($reviewinfo['params']['recommendation-year_to'])?'по '.$reviewinfo['params']['recommendation-year_to']:'').'</td></tr>';
    }
    $recommendation .= $pasteparam_userfunc('recommendation_elements',null,true);
    if(strlen($recommendation)){
        echo '<tr><td colspan="2" class="header">'.langTranslate('product','review elements','Recommendations','Recommendations').'</td></tr>'.$recommendation;
    }
    unset($recommendation);
?>

<?php 
    $score_calc_items = array(
            array(
                    'caption'=>langTranslate('product','review score calc','Visual','Visual'),
                    'items'=>array(
                            array(
                                    'caption'=>langTranslate('product','review score calc','Visual - Limpidity','Limpidity'),
                                    'name'=>'review_score_calc-visual-limpidity',
                                    'values_ns'=>array(5,4,3,2,1),
                                    'values_s'=>array(5,4,3,2,1)
                                ),
                            array(
                                    'caption'=>langTranslate('product','review score calc','Visual - Color','Color'),
                                    'name'=>'review_score_calc-visual-color',
                                    'values_ns'=>array(10,8,6,4,2),
                                    'values_s'=>array(10,8,6,4,2)
                                ),
                            array(
                                    'caption'=>langTranslate('product','review score calc','Visual - Effervescence','Effervescence'),
                                    'name'=>'review_score_calc-visual-effervescence',
                                    'values_s'=>array(10,8,6,4,2)
                                ),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review score calc','Nose','Nose'),
                    'items'=>array(
                            array(
                                    'caption'=>langTranslate('product','review score calc','Nose - Positive Intensity','Positive Intensity'),
                                    'name'=>'review_score_calc-nose-positive_intensity',
                                    'values_ns'=>array(6,5,4,3,2),
                                    'values_s'=>array(7,6,5,4,3)
                                ),
                            array(
                                    'caption'=>langTranslate('product','review score calc','Nose - Genuineness','Genuineness'),
                                    'name'=>'review_score_calc-nose-genuineness',
                                    'values_ns'=>array(8,7,6,4,2),
                                    'values_s'=>array(7,6,5,4,3)
                                ),
                            array(
                                    'caption'=>langTranslate('product','review score calc','Nose - Quality','Quality'),
                                    'name'=>'review_score_calc-nose-quality',
                                    'values_ns'=>array(16,14,12,10,8),
                                    'values_s'=>array(14,12,10,8,6)
                                ),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review score calc','Taste','Taste'),
                    'items'=>array(
                            array(
                                    'caption'=>langTranslate('product','review score calc','Taste - Positive Intensity','Positive Intensity'),
                                    'name'=>'review_score_calc-taste-positive_intensity',
                                    'values_ns'=>array(6,5,4,3,2),
                                    'values_s'=>array(7,6,5,4,3)
                                ),
                            array(
                                    'caption'=>langTranslate('product','review score calc','Taste - Genuineness','Genuineness'),
                                    'name'=>'review_score_calc-taste-genuineness',
                                    'values_ns'=>array(8,7,6,4,2),
                                    'values_s'=>array(7,6,5,4,3)
                                ),
                            array(
                                    'caption'=>langTranslate('product','review score calc','Taste - Harmonious Persistence','Harmonious Persistence'),
                                    'name'=>'review_score_calc-taste-harmonious_persistence',
                                    'values_ns'=>array(8,7,6,5,4),
                                    'values_s'=>array(7,6,5,4,3)
                                ),
                            array(
                                    'caption'=>langTranslate('product','review score calc','Taste - Quality','Quality'),
                                    'name'=>'review_score_calc-taste-quality',
                                    'values_ns'=>array(22,19,16,13,10),
                                    'values_s'=>array(14,12,10,8,6)
                                ),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review score calc','Harmony – Overall Judgement','Harmony – Overall Judgement'),
                    'name'=>'review_score_calc-harmony-overall_judgement',
                    'values_ns'=>array(11,10,9,8,7),
                    'values_s'=>array(12,11,10,9,8)
                ),
        );
    $score_calc_table = '';
    $values_key = isset($reviewinfo['params'])&&$reviewinfo['params']==2?'values_s':'values_ns';
    $found_selected = false;
    $echo_score_cols = function($values, $name, &$score_calc_table, &$found_selected) use ($reviewinfo){
        foreach($values as $value){
            if(isset($reviewinfo['params'][$name])&&$reviewinfo['params'][$name]==$value){
                $score_calc_table .= '<td class="selected">'.$value.'</td>';
                $found_selected = true;
            } else {
                $score_calc_table .= '<td>'.$value.'</td>';
            }
        }
    };
    foreach($score_calc_items as $section){
        $single_row = false;
        $itemcount = 0;
        if(!isset($section['items'])){
            $single_row = true;
            if(!isset($section[$values_key])){
                continue;
            }
        } else {
            foreach($section['items'] as $item){
                if(isset($item[$values_key])){
                    $itemcount++;
                }
            }
            if($itemcount==0){
                continue;
            }
        }
        $score_calc_table .= '<tr><td class="caption" '.($single_row?'colspan="2"':'rowspan="'.$itemcount.'"').'>'.$section['caption'].'</td>';
        if($single_row){
            $echo_score_cols($section[$values_key], $section['name'], $score_calc_table, $found_selected);
            continue;
        }
        $first_row = true;
        foreach($section['items'] as $item){
            if(!isset($item[$values_key])){
                continue;
            }
            if(!$first_row){
                $score_calc_table .= '<tr>';
            } else {
                $first_row = false;
            }
            $score_calc_table .= '<td class="caption">'.$item['caption'].'</td>';
            $echo_score_cols($item[$values_key], $item['name'], $score_calc_table, $found_selected);
            $score_calc_table .= '</tr>';
        }
    }
    
    if($found_selected){
        echo '<tr><td colspan="2"><table class="score-calc">'.$score_calc_table.'<table></td></tr>';
    }
    unset($score_calc_table);
?>
<?php endif; //end of if !short review
?>
</tbody></table>
