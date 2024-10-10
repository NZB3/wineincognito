<?php 
langSetDefault('product', 'review');
if(!isset($reviewmergeinfo['count'])){
    $reviewmergeinfo['count'] = array();
}
$count_experts = array();
$max_expert_level = 0;
foreach($expert_levels as $key=>$expert_level){
    if(!isset($reviewmergeinfo['count'][$expert_level]) || $reviewmergeinfo['count'][$expert_level]<=0){
        continue;
    }
    $count_experts[$expert_level] = $reviewmergeinfo['count'][$expert_level];
    $max_expert_level = max($max_expert_level,$expert_level);
}
$expert_levels = array_keys($count_experts);
$count_expert_levels = count($expert_levels);
if(!isset($evaluationform)){
    $evaluationform = false;
}
if(!isset($load_template_expert_list)){
    $load_template_expert_list = array();
}
if(!isset($evaluationview)){
    $evaluationview = false;
}
if(!isset($scores)||!is_array($scores)){
    $scores = array();
}
if(!isset($blindness)||$blindness!==true){
    $blindness = false;
}
$hide_zero_values = true;
if($evaluationform){
    $hide_zero_values = false;
}
?>
<?php if($evaluationform): 
        if(!empty($load_template_expert_list)):?>
<form method="POST"><input type="hidden" name="action" value="load_expert_template" /><table class="subcontent merge-review-template-expert-list"><thead><tr><th><?=langTranslate('tasting','tasting','Template expert list: Load scores from submitted review', 'Load scores from submitted review');?></th></tr></thead><tbody><tr><td><select name="expert"><?php 
            foreach($load_template_expert_list as $template_expert):
?><option value="<?=$template_expert['id']?>"><?=htmlentities($template_expert['name'])?></option><?php 
            endforeach; 
?></select></td></tr><tr><td class="submit"><input type="submit" value="<?=langTranslate('tasting','tasting','Template expert list: Load', 'Load');?>" /></td></tr></table></form>
<?php   endif; ?>
<form method="POST"><input type="hidden" name="action" value="set_manual_evaluation" />
<?php endif; ?>
<table class="subcontent merge-review">
<thead>
    <tr><th></th><th></th><th></th>
<?php foreach($expert_levels as $expert_level): ?>
<th><?=isset($expert_level_list[$expert_level])?htmlentities($expert_level_list[$expert_level]):''?></th>
<?php endforeach; ?>
<?php if($evaluationform): ?><th></th><?php endif; ?>
<?php if($evaluationview): ?><th></th><?php endif; ?>
    </tr>
</thead>
<tbody>

<tr><td><?=langTranslate('product','review merge','Count', 'Count');?></td><td colspan="2"></td>
<?php foreach($expert_levels as $expert_level): ?>
<td class="val"><?=isset($reviewmergeinfo['count'][$expert_level])?htmlentities($reviewmergeinfo['count'][$expert_level]):''?></td>
<?php endforeach; ?>
<?php if($evaluationform): ?><td></td><?php endif; ?>
<?php if($evaluationview): ?><td></td><?php endif; ?>

<?php if(isset($reviewmergeinfo['score'])): ?>
<tr><td><?=langTranslate('Score', 'Score');?></td><td colspan="2"></td>
<?php   foreach($expert_levels as $expert_level): ?>
<td class="val"><?=isset($reviewmergeinfo['score'][$expert_level])?htmlentities($reviewmergeinfo['score'][$expert_level]):''?></td>
<?php   endforeach; ?>
<?php   if($evaluationform): ?><td class="score-evaluation-settings">
<table><tbody>
    <tr><td colspan="2" class="score"><ul>
<?php   for($i=0;$i<=5;$i++): 
?><li><label><input type="radio" name="score_score" value="<?=$i?>" <?=getPostVal('score_score',null)==$i?'checked="checked"':''?></label></li><?php 
        endfor; ?>
    </ul></td></tr>
    <tr><td class="label"><label for="merge-review-expert-evaluation-options-score-manual-value"><?=langTranslate('product','review','Score', 'Score');?></label></td><td class="score"><input type="text" name="score_value" id="merge-review-expert-evaluation-options-score-manual-value" value="<?=getPostVal('score_value',isset($reviewmergeinfo['score'][$max_expert_level])?htmlentities($reviewmergeinfo['score'][$max_expert_level]):100)?>" /></td></tr>
    <tr class="score-permissible-variation"><td class="label"><label><?=langTranslate('tasting', 'expert evaluation','Permissible variation','Permissible variation')?></label></td><td>
        <ul><li><input type="radio" name="score_permissible_variation" id="merge-review-expert-evaluation-options-score-permissible-variation-0" value="0" <?=getPostVal('score_permissible_variation',0)==0?'checked="checked"':''?>><label for="merge-review-expert-evaluation-options-score-permissible-variation-0"><?=langTranslate('tasting','expert evaluation','Automatic','Automatic')?></label></li><li><input type="radio" name="score_permissible_variation" id="merge-review-expert-evaluation-options-score-permissible-variation-1" value="1" <?=getPostVal('score_permissible_variation',0)==1?'checked="checked"':''?>><label for="merge-review-expert-evaluation-options-score-permissible-variation-1"><?=langTranslate('tasting','expert evaluation','Manual','Manual')?></label></li></ul>
        <input type="text" name="score_permissible_variation_value" class="merge-review-expert-evaluation-options-score-permissible-variation-manual-value" value="<?=getPostVal('score_permissible_variation_value',0)?>" /></td></tr>
    
</tbody></table>
</td><?php endif; ?>
<?php   if($evaluationview): ?><td class="scoreview">
<?php       if(isset($scores['score_score'])&&$scores['score_score']): ?>
    <span><?=$scores['score_value']?></span><ul>
<?php           for($i=0;$i<=$scores['score_score'];$i++): 
?><li><label></label></li><?php 
                endfor; ?></ul>
<?php       endif; ?>
</td>
<?php   endif; ?>
</tr>
<?php endif; ?>

<?php 
    $headercolspan = 3+$count_expert_levels+($evaluationform?1:0)+($evaluationview?1:0);
    $pasteparam_userfunc = function($group, $header = null) use ($reviewmergeinfo, $review_elements, $expert_levels, $count_expert_levels, $count_experts, $evaluationform, $evaluationview, $scores, $headercolspan, $hide_zero_values){
        if(!isset($review_elements[$group])){
            return;
        }
        if($count_expert_levels<=0){
            return;
        }
        $result = '';
        foreach($review_elements[$group] as $element){
            $element_name = $element['name'];
            if(!isset($reviewmergeinfo['params'][$element_name])){
                continue;
            }

            $rows = array();
            foreach($element['values'] as $elem_val){
                $value = $elem_val['value'];
                if($hide_zero_values && $value===0){
                    continue;
                }
                $no_values = false;
                if(!isset($reviewmergeinfo['params'][$element_name][$value])){
                    $no_values = true;
                }
                if($no_values&&isset($element['hide_empty_from_merge'])&&$element['hide_empty_from_merge']){
                    continue;
                }
                $row = '<td class="img">';
                if(isset($elem_val['image'])&&strlen($elem_val['image'])){
                    $row .= '<img src="'.$elem_val['image'].'" />';
                }
                $row .= '</td><td>'.$elem_val['label'].'</td>';
                $total_count = 0;
                if($no_values){
                    $row .= '<td colspan="'.$count_expert_levels.'"></td>';
                } else {
                    foreach($expert_levels as $expert_level){
                        if(!isset($reviewmergeinfo['params'][$element_name][$value][$expert_level])){
                            $row .= '<td></td>';
                            continue;
                        }
                        $total_count += $reviewmergeinfo['params'][$element_name][$value][$expert_level];
                        $row .= '<td class="val">'.round($reviewmergeinfo['params'][$element_name][$value][$expert_level]*100/$count_experts[$expert_level]).'%</td>';
                    }    
                }
                if($evaluationform){
                    if(!$no_values){
                        $row .= '<td class="score"><ul>';
                        for($i=0;$i<=5;$i++){
                            $row .= '<li><label><input type="radio" name="'.$element_name.'['.$value.']" value="'.$i.'" '.(getPostVal($element_name.'['.$value.']',null)==$i?'checked="checked"':'').' /></label></li>';
                        }
                        $row .= '</ul></td>';
                    } else {
                        $row .= '<td></td>';
                    }
                    
                }
                if($evaluationview){
                    if(isset($scores[$element_name])&&is_array($scores[$element_name])&&isset($scores[$element_name][$value])&&$scores[$element_name][$value]>0){
                        $row .= '<td class="scoreview"><ul>';
                        for($i=0;$i<(int)$scores[$element_name][$value];$i++){
                            $row .= '<li><label></label></li>';
                        }
                        $row .= '</ul></td>';
                    } else {
                        $row .= '<td></td>';
                    }
                }
                $row .= '</tr>';
                $rows[] = array($row,$total_count);
            }
            if(!empty($rows)){
                if(isset($element['order_by_total_count_in_merge'])&&$element['order_by_total_count_in_merge']){
                    usort($rows, function ($a, $b) { if($a[1]==$b[1]){return 0;}return ($a[1]<$b[1])?1:-1; });
                }
                $result .= '<tr><td rowspan="'.count($rows).'">'.(isset($element['caption'])?htmlentities($element['caption']):'').'</td>';
                $first_row = true;
                foreach($rows as $row){
                    if(!$first_row){
                        $result .= '<tr>';
                    } else {
                        $first_row = false;
                    }
                    $result .= $row[0];
                }
            }
        }
        if(strlen($result)){
            if($header){
                $result = '<tr><td colspan="'.$headercolspan.'" class="header">'.htmlentities($header).'</td></tr>'.$result;
            }
            echo $result;
        }
    };
    $pasteparam_userfunc('base_elements');
?>
<?php 
    $subcolor_rows = array();
    if(isset($reviewmergeinfo['subcolor'])){
        foreach($reviewmergeinfo['subcolor'] as $subcolor_info){
            foreach($review_elements['color_spectrum_subcolor_data'] as $subcolor_data){
                if($subcolor_data['color']==$subcolor_info['color'] && 
                        $subcolor_data['subcolor']==$subcolor_info['subcolor'] && 
                        $subcolor_data['depth']==$subcolor_info['depth']){
                    $subcolor_row = '';
                    if(isset($subcolor_data['example']) && strlen($subcolor_data['example'])){
                        $subcolor_row .= '<td class="color" style="background-color: '.$subcolor_data['example'].'"></td>';    
                    } else {
                        $subcolor_row .= '<td></td>';
                    }
                    $subcolor_row .= '<td>'.htmlentities($subcolor_data['title']).'</td>';
                    $total_count = 0;
                    foreach($expert_levels as $expert_level){
                        if(!isset($subcolor_info['counts'][$expert_level])){
                            $subcolor_row .= '<td></td>';
                            continue;
                        }
                        $total_count += $subcolor_info['counts'][$expert_level];
                        $subcolor_row .= '<td class="val">'.round(((int)$subcolor_info['counts'][$expert_level])*100/$count_experts[$expert_level]).'%</td>';
                    }
                    if($evaluationform){
                        $subcolor_row .= '<td class="score"><ul>';
                        $colorcode = $subcolor_data['color'].','.$subcolor_data['subcolor'].','.$subcolor_data['depth'];
                        for($i=0;$i<=5;$i++){
                            $subcolor_row .= '<li><label><input type="radio" name="subcolorcode['.$colorcode.']" value="'.$i.'" '.(getPostVal('subcolorcode['.$colorcode.']',null)==$i?'checked="checked"':'').'</label></li>';
                        }
                        $subcolor_row .= '</td>';
                    }
                    if($evaluationview){
                        $colorcode = $subcolor_data['color'].','.$subcolor_data['subcolor'].','.$subcolor_data['depth'];
                        if(isset($scores['subcolorcode'])&&is_array($scores['subcolorcode'])&&isset($scores['subcolorcode'][$colorcode])&&$scores['subcolorcode'][$colorcode]>0){
                            $subcolor_row .= '<td class="scoreview"><ul>';
                            for($i=0;$i<=(int)$scores['subcolorcode'][$colorcode];$i++){
                                $subcolor_row .= '<li><label></label></li>';
                            }
                            $subcolor_row .= '</ul></td>';
                        } else {
                            $subcolor_row .= '<td></td>';
                        }
                    }
                    $subcolor_row .= '</tr>';
                    $subcolor_rows[] = array($subcolor_row,$total_count);
                    break;
                }
            }
        }
    }
    if(!empty($subcolor_rows)){
        usort($subcolor_rows, function ($a, $b) { if($a[1]==$b[1]){return 0;}return ($a[1]<$b[1])?1:-1; });
        echo '<tr><td rowspan="'.count($subcolor_rows).'">'.langTranslate('product','review elements','Color spectrum - Tint','Tint').'</td>';
        $first_row = true;
        foreach($subcolor_rows as $subcolor_row){
            if(!$first_row){
                echo '<tr>';
            } else {
                $first_row = false;
            }
            echo $subcolor_row[0];
        }
    }
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
    $pasteparam_userfunc('similarity_age_elements',langTranslate('product','review elements','Similarities','Similarities'));

if(isset($reviewmergeinfo['hierarchy_params'])){
    foreach(array('similarity_grape','similarity_location') as $element_name){
        if(!isset($reviewmergeinfo['hierarchy_params'][$element_name])){
            continue;
        }
        foreach($reviewmergeinfo['hierarchy_params'][$element_name] as $pa_id=>$pa_block){
            $rows = array();
            foreach($pa_block['values'] as $value=>$elem_val){
                $total_count = 0;
                $correct_value_class = '';
                if(isset($elem_val['correct_value'])&&$elem_val['correct_value']){
                    if(!$blindness){
                        continue;
                    }
                    $correct_value_class = 'correct-value';
                    $total_count += 10000;
                }
                $row = '<td class="img"></td><td class="'.$correct_value_class.'">'.$elem_val['label'].'</td>';
                
                
                foreach($expert_levels as $expert_level){
                    if(!isset($elem_val['values'][$expert_level])){
                        $row .= '<td></td>';
                        continue;
                    }
                    $total_count += $elem_val['values'][$expert_level];
                    $row .= '<td class="val '.$correct_value_class.'">'.round($elem_val['values'][$expert_level]*100/$count_experts[$expert_level]).'%</td>';
                }    
                if($evaluationform){
                    if($blindness){
                        $row .= '<td class="score"><ul>';
                        for($i=0;$i<=5;$i++){
                            $row .= '<li><label><input type="radio" name="'.$element_name.'['.$pa_id.','.$value.']" value="'.$i.'" '.(getPostVal($element_name.'['.$pa_id.','.$value.']',null)==$i?'checked="checked"':'').'</label></li>';
                        }
                        $row .= '</td>';
                    } else {
                        $row .= '<td></td>';
                    }
                }
                if($evaluationview){
                    $pacode = $pa_id.','.$value;
                    if(isset($scores[$element_name])&&is_array($scores[$element_name])&&isset($scores[$element_name][$pacode])&&$scores[$element_name][$pacode]>0){
                        $row .= '<td class="scoreview"><ul>';
                        for($i=0;$i<=$scores[$element_name][$pacode];$i++){
                            $row .= '<li><label></label></li>';
                        }
                        $row .= '</ul></td>';
                    } else {
                        $row .= '<td></td>';
                    }
                }
                $row .= '</tr>';
                $rows[] = array($row,$total_count);
            }

            if(!empty($rows)){
                usort($rows, function ($a, $b) { if($a[1]==$b[1]){return 0;}return ($a[1]<$b[1])?1:-1; });
                echo '<tr><td rowspan="'.count($rows).'">'.$pa_block['label'].'</td>';
                $first_row = true;
                foreach($rows as $row){
                    if(!$first_row){
                        echo '<tr>';
                    } else {
                        $first_row = false;
                    }
                    echo $row[0];
                }
            }
        }
        
    }
}

    $pasteparam_userfunc('recommendation_elements',langTranslate('product','review elements','Recommendations','Recommendations')); 
?>
<?php if($evaluationform): ?><td class="submit" colspan="<?=$headercolspan?>"><input type="submit" value="<?=langTranslate('tasting','tasting','Save', 'Save');?>" /></td><?php endif; ?>
</tbody></table>
<?php if($evaluationform): ?></form><?php endif; ?>
