<?php langSetDefault('product', 'review'); 

    if(!isset($personal_review)){
        $personal_review = false;
    }
    if(!isset($tasting_assessment)){
        $tasting_assessment = true;
    }

    $skip_detailed_aroma_elements = in_array('detailed_aroma_elements', $tasting_review_particularity_data);
    function breakline($line,&$maxlinecount,$minlinelength=4){
        $line = preg_replace('#\s{2,}#', ' ', trim($line));
        $maxlinecount = (int)$maxlinecount;
        if($maxlinecount<=1){
            return $line;
        }
        $strlen = mb_strlen($line,'UTF-8');
        $spaces = array();
        $pos = -1;
        while($pos=mb_strpos($line, ' ',$pos+1,'UTF-8')){
            if($pos < $minlinelength || $strlen - $pos - 1 < $minlinelength){
                continue;
            }
            $spaces[] = $pos;
        }
        $maxlinecount = min($maxlinecount,count($spaces)+1);
        $chosenspaces = array();
        for(;$maxlinecount>1;$maxlinecount--){
            $breaklength = $strlen/$maxlinecount;
            $chosenspaces = array();
            for($i=1;$i<$maxlinecount;$i++){
                $lastspace = $lastdelta = null;
                $found = false;
                $currentbreak = $i*$breaklength;
                foreach($spaces as $space){
                    $delta = abs($space - $currentbreak);
                    if($lastdelta!==null && $delta > $lastdelta){
                        $chosenspaces[] = $lastspace;
                        $found = true;
                        break;
                    }
                    $lastspace = $space;
                    $lastdelta = $delta;
                }
                if(!$found && $lastspace!==null){
                    $chosenspaces[] = $lastspace;
                }
            }
            for($i=0;$i<count($chosenspaces)-1;$i++){
                if($chosenspaces[$i+1]-$chosenspaces[$i]-1<$minlinelength){
                    continue 2;//decrement maxlinecount
                }
            }
            break;
        }
        if($maxlinecount<=1){
            return $line;
        }
        $start = 0;
        $resultline = '';
        foreach($chosenspaces as $space){
            $resultline .= mb_substr($line, $start, $space-$start,'UTF-8').'<br />';
            $start = $space+1;
        }
        $resultline .= mb_substr($line, $start, $strlen,'UTF-8');
        return $resultline;
    }
    $pasteChoiceElements = function($group, $masterclass,$header=null) use ($review_elements, $review, $tasting_review_particularity_data, $skip_detailed_aroma_elements, $vintage_review_filter){
        if(in_array($group, $tasting_review_particularity_data)){
            return;
        }
        if(!isset($review_elements[$group])){
            return;
        }
        $elements = $review_elements[$group];
        $processed_parents = array();
        foreach($elements as $element_key=>$element){
            if(in_array($element['name'], $tasting_review_particularity_data) && $group!=='base_elements'){
                unset($elements[$element_key]);
                continue;
            }
            if($skip_detailed_aroma_elements&&isset($element['aroma'])&&$element['aroma']){
                unset($elements[$element_key]);
                continue;
            }
            $forced_values = null;
            if($group==='base_elements' && in_array($element['name'], $tasting_review_particularity_data) && isset($vintage_review_filter[$element['name']]) && is_array($vintage_review_filter[$element['name']]) && !empty($vintage_review_filter[$element['name']])){
                $elements[$element_key]['forced-value'] = true;
                $forced_values = $vintage_review_filter[$element['name']];
            }
            $found_checked = false;
            $default_key = null;
            foreach($element['values'] as $value_key=>$value){
                $checked = false;
                if($forced_values){
                    if(in_array($value['value'], $forced_values)){
                        $checked = true;
                    }
                } else {
                    if(isset($review[$element['name']])){
                        if(is_array($review[$element['name']])){
                            $checked = in_array($value['value'], $review[$element['name']]);
                        } else {
                            $checked = $review[$element['name']]==$value['value'];
                        }
                    }
                }
                if($checked){
                    $elements[$element_key]['values'][$value_key]['selected'] = true;
                    $found_checked = true;
                }    
                if(isset($value['default'])&&$value['default']){
                    $default_key = $value_key;
                }
            }
            if(!$found_checked && $default_key!==null){
                $elements[$element_key]['values'][$default_key]['selected'] = true;
            }
            //process subelements
            if(!isset($element['subelement-of'])||!$element['subelement-of']){
                continue;
            }
            $parent_name = $element['subelement-of'];
            if(in_array($parent_name, $processed_parents)){
                continue;
            }
            $processed_parents[] = $parent_name;
            foreach($elements as $key=>$parent_element){
                if($parent_element['name']==$parent_name){
                    $elements[$key]['has-subelements'] = true;
                    break;
                }
            }
        }
        unset($processed_parents);
        $result = '';
        foreach($elements as $element){
            $ulclass = '';
            switch($element['list']){
                case 'cols':
                    $count = count($element['values']);
                    if($count<=6){
                        $ulclass = 'count-'.count($element['values']);
                    } else {
                        $ulclass = 'count-7';
                    }
                    break;
                case 'rows':
                    $ulclass = 'rows';
                    break;
                default:
                    $ulclass = 'count-7';
            }
            $forced_values = isset($element['forced-value'])&&$element['forced-value'];
            if(isset($element['caption']) && strlen($element['caption'])){
                $result .= '<tr class="'.$masterclass.' '.(isset($element['subelement-of'])?$element['subelement-of'].'-subelement subelement':'').' '.(isset($element['class'])?$element['class']:'').' '.($forced_values?'forced-values':'').'"><td class="label"><label>'.htmlentities($element['caption']).'</label>';
                if(isset($element['hint']) && strlen($element['hint'])){
                    $result .= '<span class="tip">'.htmlentities($element['hint']).'</span>';
                }
                $result .= '</td></tr>';
            }
            $result .= '<tr class="'.$masterclass.' element-'.$element['name'].' '.(isset($element['subelement-of'])?$element['subelement-of'].'-subelement subelement':'').' '.((isset($element['optional'])&&$element['optional'])?'optional':'').' '.(isset($element['has-subelements'])?'has-subelements':'').' '.((isset($element['autofill_from'])&&$element['autofill_from'])?'autofill':'').' '.(isset($element['class'])?$element['class']:'').' '.((isset($element['aroma'])&&$element['aroma'])?'aroma':'').' '.((isset($element['segment-class'])&&$element['segment-class'])?$element['segment-class'].' segment':'').' '.($forced_values?'forced-values':'').'" '.((isset($element['autofill_from'])&&$element['autofill_from'])?'data-autofill-from="'.$element['autofill_from'].'"':'').'><td><ul class="'.$ulclass.'">';
            foreach($element['values'] as $num=>$value){
                $linecount = 2;
                $label = breakline($value['label'],$linecount);
                $result .= '<li class="line-'.$linecount.'">';
                if(!$forced_values || (isset($value['selected'])&&$value['selected']) ){
                    $result .= '<input type="'.((isset($element['multichoice'])&&$element['multichoice'])?'checkbox':'radio').'" name="'.$element['name'].''.((isset($element['multichoice'])&&$element['multichoice'])?'[]':'').'" value="'.$value['value'].'" id="edit-review-form-'.$element['name'].'-'.$num.'" class="'.((isset($value['segment-class'])&&$value['segment-class'])?$value['segment-class'].' segment':'').' '.((isset($value['default'])&&$value['default'])?'default':'').' '.((isset($value['segment-base-class'])&&$value['segment-base-class'])?'segment-base':'').'" '.((isset($value['segment-base-class'])&&$value['segment-base-class'])?'data-segment-base="'.$value['segment-base-class'].'"':'').' '.((isset($value['selected'])&&$value['selected'])?'checked="checked"':'').' '.($forced_values?'disabled="disabled"':'').' />';
                } else {
                    $result .= '<input type="hidden" class="'.((isset($value['segment-class'])&&$value['segment-class'])?$value['segment-class'].' segment':'').' '.((isset($value['segment-base-class'])&&$value['segment-base-class'])?'segment-base':'').'" '.((isset($value['segment-base-class'])&&$value['segment-base-class'])?'data-segment-base="'.$value['segment-base-class'].'"':'').' />';
                }
                
                $result .= '<label '.(!isset($element['forced-value'])||!$element['forced-value']?'for="edit-review-form-'.$element['name'].'-'.$num.'"':'class="forced-value"').'>'.(isset($value['image'])?'<span class="icon-container"><img src="'.$value['image'].'" /></span>':'').''.$label.'</label></li>';
            }
            $result .= '</ul></td></tr>';
        }
        if(strlen($result)&&strlen($header)){
            $result = '<tr class="'.$masterclass.'"><td class="header">'.$header.'</td></tr>'.$result;
        }
        return $result;
    }
?>
<form method="POST" class="edit-review-form subcontent" data-tpv-id="<?=$tpv_id?>" data-personal-review="<?=$personal_review?1:0?>" data-tasting-assessment="<?=$tasting_assessment?1:0?>"><input type="hidden" name="action" value="<?=$adding?'add_review':'edit_review'?>" />
<input type="hidden" class="comlementary-submit-type didnottry" name="didnottry" value="0" />
<input type="hidden" class="comlementary-submit-type wineisfaulty" name="wineisfaulty" value="0" />
<script type="template" class="dropbox-template-select">
<tr><td class="label">{ifdef{name}}<label>{{name}}</label>{endifdef{name}}</td></tr><tr><td><div class="dropbox multiple" data-fieldname="{{fieldname}}" data-group="{{group}}" data-has-children="{{haschildren}}" data-depth="{{depth}}" data-system="{{system}}"><input type="checkbox" id="dropbox-review-form-{{fieldname}}-{{group}}-{{depth}}" /><label for="dropbox-review-form-{{fieldname}}-{{group}}-{{depth}}"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul><li class="search"><input type="text" /></li>{{options}}</ul></div></td></tr>
</script>
<script type="template" class="dropbox-template-option">
<li class="item {if{selected}}selected{endif{selected}} {!if{important}}not-important{end!if{important}}"><label><input type="checkbox" data-attr-id="{{attrId}}" name="{{fieldname}}[]" value="{{id}}" {if{selected}}checked{endif{selected}} data-se-text=" {{setext}}" /><span></span>{{name}}</label></li>
</script>
<script type="template" class="dropbox-template-option-header">
<li class="header">{{name}}</li>
</script>
<script type="template" class="tpv-draft-template-item">
<input type="hidden" name="{{name}}" value="{{value}}" />
</script>
<script type="template" class="tpv-draft-template-form">
<form method="POST" class="edit-review-load-draft-form subcontent">{{items}}<input type="submit" class="mainbtn" value="<?=langTranslate('Load draft', 'Load draft');?>" /></form>
</script>
<script type="string" class="dropbox-template-empty-string"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></script>
<script type="template" class="dropbox-template-option-important-toggle">
<li class="dropbox-item-list-toggle show-not-important"><?=langTranslate('main','dropbox','Show not important', 'Show not important');?></li><li class="dropbox-item-list-toggle hide-not-important"><?=langTranslate('main','dropbox','Hide not important', 'Hide not important');?></li>
</script>
<script type="string" class="confirm_string_stop_review"><?=langTranslate('Are you sure you want to finish reviewing this product?','Are you sure you want to finish reviewing this product?')?></script>
<div><div class="scroll-header"></div></div>
<table class="subcontent fieldlist edit-review">
    <thead>
        <tr class="header"><th><?=langTranslate('Review','Review')?></th></tr>
        <tr><th></th></tr>
    </thead>
<tbody>
<?php if($adding): ?>
<tr class="step step0 didnottry"><td><input type="button" class="comlementary-submit didnottry" value="<?=langTranslate('Did not try', 'Did not try');?>" /></td></tr>
<?php endif; ?>
<?php
    echo $pasteChoiceElements('base_elements', 'step step0');
?>

<?php 
    if(!in_array('color_spectrum_subcolor', $tasting_review_particularity_data)):
        $color_spectrum_subcolors = array();
        $color_spectrum_depths = array();
        foreach($review_elements['color_spectrum_subcolor_data'] as $color_spectrum_subcolor){
            if($color_spectrum_subcolor['subcolor'] && !in_array($color_spectrum_subcolor['subcolor'], $color_spectrum_subcolors)){
                $color_spectrum_subcolors[] = $color_spectrum_subcolor['subcolor'];
            }
            if($color_spectrum_subcolor['depth'] && !in_array($color_spectrum_subcolor['depth'], $color_spectrum_depths)){
                $color_spectrum_depths[] = $color_spectrum_subcolor['depth'];
            }
        }
        echo '<script class="color-spectrum-subcolor-data">'.json_encode($review_elements['color_spectrum_subcolor_data']).'</script>';
?>
<tr class="step step0 subelement color-spectrum-subdata"><td class="label"><label><?=langTranslate('product','review elements','Color spectrum - Tint','Tint')?></label></td></tr>
<tr class="step step0 subelement color-spectrum-subdata"><td><ul class="rows"><?php 
    foreach($color_spectrum_subcolors as $color_spectrum_subcolor):
?><li><input type="radio" name="color-spectrum-subcolor" value="<?=$color_spectrum_subcolor?>" id="edit-review-form-color-spectrum-subcolor-<?=$color_spectrum_subcolor?>" <?=(isset($review['color-spectrum-subcolor'])&&$review['color-spectrum-subcolor']==$color_spectrum_subcolor)?'checked="checked"':''?> /><label for="edit-review-form-color-spectrum-subcolor-<?=$color_spectrum_subcolor?>"><span class="example"></span><span class="title"></span></label></li><?php 
    endforeach; 
    unset($color_spectrum_subcolors);
?></ul></td></tr>
<tr class="step step0 subelement color-spectrum-subdata"><td class="label"><label><?=langTranslate('product','review elements','Color spectrum - Intensity','Intensity')?></label></td></tr>
<tr class="step step0 subelement color-spectrum-subdata"><td><ul class="rows"><?php 
    foreach($color_spectrum_depths as $color_spectrum_depth):
?><li><input type="radio" name="color-spectrum-depth" value="<?=$color_spectrum_depth?>" id="edit-review-form-color-spectrum-depth-<?=$color_spectrum_depth?>" <?=(isset($review['color-spectrum-depth'])&&$review['color-spectrum-depth']==$color_spectrum_depth)?'checked="checked"':''?> /><label for="edit-review-form-color-spectrum-depth-<?=$color_spectrum_depth?>"><span class="example"></span><span class="title"></span></label></li><?php 
    endforeach;
    unset($color_spectrum_depths);
?></ul></td></tr>
<tr class="step step0 subelement color-spectrum-subdata-example"><td><span class="example"></span></td></tr>
<?php 
    endif;
    echo $pasteChoiceElements('external_observation_elements', 'step step1');
    echo $pasteChoiceElements('sparkling_rating_elements', 'step step1', langTranslate('product','review elements','Perlage','Perlage')/*Оценка перляжа*/);
    $faultcheck_elements = $pasteChoiceElements('faultcheck_elements', 'step step1 faultcheck', langTranslate('product','review elements','Faultcheck - Faulty','Faulty')/*Проверка на дефекты*/);
    if($faultcheck_elements):
        echo $faultcheck_elements;
?>
<tr class="step step1 faultcheck-subelement subelement"><td class="label"><label for="edit-review-form-faultcheck-custom"><?=langTranslate('product','review elements','Faultcheck - Other','Other')/*Другие дефекты*/?></label></td></tr>
<tr class="step step1 faultcheck-subelement subelement"><td><textarea name="faultcheck-custom" id="edit-review-form-faultcheck-custom" /><?=getPostVal('faultcheck-custom','')?></textarea></td></tr>
<tr class="step step1 faultcheck-subelement subelement"><td class="submit"><input type="button" class="comlementary-submit wineisfaulty" value="<?=langTranslate('product','review elements','Faulty, can’t be evaluated','Faulty, can’t be evaluated')/*Вино испорчено, дегустацию продолжать нельзя*/?>" /></td></tr>
<?php 
    endif;
    echo $pasteChoiceElements('overall_aroma_elements', 'step step2', langTranslate('product','review elements','Nose. General characteristics','Nose. General characteristics')/*Общая оценка аромата*/);
    echo $pasteChoiceElements('primary_aroma_elements', 'step step3', langTranslate('product','review elements','Aromas. Primary aromas','Aromas. Primary aromas')/*Первичные ароматы*/);
    echo $pasteChoiceElements('secondary_aroma_elements', 'step step4', langTranslate('product','review elements','Secondary aromas','Secondary aromas')/*Вторичные ароматы*/);
    echo $pasteChoiceElements('tertiary_aroma_elements', 'step step5', langTranslate('product','review elements','Tretiary Aromas','Tretiary Aromas')/*Третичные ароматы*/);
    echo $pasteChoiceElements('taste_elements', 'step step6', langTranslate('product','review elements','Palate. General characteristics','Palate. General characteristics')/*Общая оценка вкуса*/);
    $taste_structure_elements = $pasteChoiceElements('taste_structure_elements', 'step step7', langTranslate('product','review elements','Structural characteristics','Structural characteristics')/*Оценка выраженности структурных параметров вкуса*/);
    if($taste_structure_elements):
        echo $taste_structure_elements;
        if(!in_array('average-balance-score', $tasting_review_particularity_data)):
?>
<tr class="step step7 average-balance-score"><td class="label"><label><?=langTranslate('product','review elements','General balance','General balance')/*Общая оценка баланса*/?></label></td></tr>
<tr class="step step7 average-balance-score"><td class="average-balance-score"></td></tr>
<?php 
        endif;
    endif;
    echo $pasteChoiceElements('primary_flavor_elements', 'step step8', langTranslate('product','review elements','Primary flavours','Primary flavours')/*Первичные вкусовые ароматы (Flavours)*/);
    echo $pasteChoiceElements('secondary_flavor_elements', 'step step9', langTranslate('product','review elements','Secondary flavours','Secondary flavours')/*Вторичные вкусовые ароматы (Flavours)*/);
    echo $pasteChoiceElements('tertiary_flavor_elements', 'step step10', langTranslate('product','review elements','Tretiary flavours','Tretiary flavours')/*Третичные вкусовые ароматы (Flavours)*/);

$rows = '';
if(!in_array('similarity_location', $tasting_review_particularity_data)){
    foreach($similarity_location as $group=>$groupvaltree){
        foreach($groupvaltree as $depth=>$depthvaltree){
            $keys = array_keys($depthvaltree);
            $first_key = $keys[0];
            $haschildren = 0;
            foreach($depthvaltree as $attr){
                if($attr['haschildren']){
                    $haschildren = 1;
                    break;
                }
            }
            $rows .= '<tr class="step step11"><td class="label">'.((count($depthvaltree)==1)?'<label>'.htmlentities($depthvaltree[$first_key]['name']).'</label>':'').'</td></tr><tr class="step step11"><td><div class="dropbox fresh '.(count($depthvaltree)==1&&count($depthvaltree[$first_key]['vals'])==1?'disabled':'').' multiple" data-fieldname="similarity_location" data-group="'.$group.'" data-has-children="'.$haschildren.'" data-depth="'.$depth.'" data-system="'.$depthvaltree[$first_key]['system'].'"><input type="checkbox" id="dropbox-review-form-similarity_location-'.$group.'-'.$depth.'" /><label for="dropbox-review-form-similarity_location-'.$group.'-'.$depth.'">'.langTranslate('main','dropbox','Click to select', 'Click to select').'</label><ul><li class="search"><input type="text" /></li>';
            foreach($depthvaltree as $attr){
                if(count($depthvaltree)>1){
                    $rows .= '<li class="header">'.htmlentities($attr['name']).'</li>';
                }
                foreach($attr['vals'] as $val){
                    $rows .= '<li class="item '.($val['selected']?'selected':'').' '.((!$val['important'])?'not-important':'').'"><label><input type="checkbox" data-attr-id="'.$attr['id'].'" name="similarity_location[]" value="'.$val['id'].'" '.($val['selected']?'checked':'').' /><span></span>'.htmlentities($val['name']).'</label></li>';
                }
                $rows .= '</ul></div></td></tr>';
            }
        }
    }
}
if(!in_array('similarity-year', $tasting_review_particularity_data)){
    $rows .= '<tr class="step step11"><td class="label"><label for="edit-review-form-similarity-year">'.langTranslate('product','vintage','Year', 'Year').'</label></td></tr>'.
             '<tr class="step step11"><td><span class="form-nv-year"><label class="checklabel"><input type="checkbox" name="similarity-year-nv" value="1" '.(isset($review['similarity-year-nv'])&&$review['similarity-year-nv']?'checked="checked"':'').' /><span></span>'.langTranslate('product','vintage','NV','NV').'</label><input type="text" name="similarity-year" class="edit-review-form-year" id="edit-review-form-similarity-year" value="'.(isset($review['similarity-year'])?htmlentities($review['similarity-year']):'').'" /></span></td></tr>';
}
if(!in_array('similarity-alcohol-content', $tasting_review_particularity_data)){
    $rows .= '<tr class="step step11"><td class="label"><label for="edit-review-form-similarity-alcohol-content">'.langTranslate('Product','vintage','Alcohol Content', 'Alcohol Content').'</label></td></tr>'.
             '<tr class="step step11"><td><input type="text" name="similarity-alcohol-content" class="edit-review-form-alcohol-content" id="edit-review-form-similarity-alcohol-content" value="'.(isset($review['similarity-alcohol-content'])?htmlentities($review['similarity-alcohol-content']):'').'" /></td></tr>';
}
$rows .= $pasteChoiceElements('similarity_age_elements', 'step step11');
if(!in_array('similarity_grape', $tasting_review_particularity_data)){
    foreach($similarity_grape as $group=>$groupvaltree){
        foreach($groupvaltree as $depth=>$depthvaltree){
            $keys = array_keys($depthvaltree);
            $first_key = $keys[0];
            $haschildren = 0;
            foreach($depthvaltree as $attr){
                if($attr['haschildren']){
                    $haschildren = 1;
                    break;
                }
            }
            $rows .= '<tr class="step step11"><td class="label">'.((count($depthvaltree)==1)?'<label>'.htmlentities($depthvaltree[$first_key]['name']).'</label>':'').'</td></tr><tr class="step step11"><td><div class="dropbox fresh '.(count($depthvaltree)==1&&count($depthvaltree[$first_key]['vals'])==1?'disabled':'').' multiple" data-fieldname="similarity_grape" data-group="'.$group.'" data-has-children="'.$haschildren.'" data-depth="'.$depth.'" data-system="'.$depthvaltree[$first_key]['system'].'"><input type="checkbox" id="dropbox-review-form-similarity_grape-'.$group.'-'.$depth.'" /><label for="dropbox-review-form-similarity_grape-'.$group.'-'.$depth.'">'.langTranslate('main','dropbox','Click to select', 'Click to select').'</label><ul><li class="search"><input type="text" /></li>';
            foreach($depthvaltree as $attr){
                if(count($depthvaltree)>1){
                    $rows .= '<li class="header">'.htmlentities($attr['name']).'</li>';
                }
                foreach($attr['vals'] as $val){
                    $rows .= '<li class="item '.($val['selected']?'selected':'').' '.((!$val['important'])?'not-important':'').'"><label><input type="checkbox" data-attr-id="'.$attr['id'].'" name="similarity_grape[]" value="'.$val['id'].'" '.($val['selected']?'checked':'').' /><span></span>'.htmlentities($val['name']).'</label></li>';
                }
                $rows .= '</ul></div></td></tr>';
            }
        }
    }
}
if(strlen($rows)){
    echo '<tr class="step step11"><td class="header">'.langTranslate('product','review elements','Similarities','Similarities')/*На что похоже*/.'</td></tr>'.$rows;
}

$rows = '';
if(!in_array('recommendation-temperature', $tasting_review_particularity_data)){
    $rows .= '<tr class="step step11 flow-temperature"><td class="label"><label for="edit-review-form-recommendation-temperature">'.langTranslate('product','review elements','Flow temperature (°C)','Flow temperature (°C)').'</label><span class="tip folded">'.langTranslate('5 to 25 degrees.','5 to 25 degrees.').'</span></td></tr>'.
             '<tr class="step step11 flow-temperature"><td><span class="form-from-to"><label for="edit-review-form-recommendation-temperature_from">'.langTranslate('Temperature From', 'From').'</label><input type="text" name="recommendation-temperature_from" class="edit-review-form-flow-temperature" id="edit-review-form-recommendation-temperature_from" value="'.(isset($review['recommendation-temperature_from'])?htmlentities($review['recommendation-temperature_from']):'').'" /><label for="edit-review-form-recommendation-temperature_to">'.langTranslate('Temperature To', 'To').'</label><input type="text" name="recommendation-temperature_to" class="edit-review-form-flow-temperature" id="edit-review-form-recommendation-temperature_to" value="'.(isset($review['recommendation-temperature_to'])?htmlentities($review['recommendation-temperature_to']):'').'" /></span></td></tr>';
}
if(!in_array('recommendation-decantation', $tasting_review_particularity_data)){
    $rows .= '<tr class="step step11"><td class="label"><label for="edit-review-form-recommendation-decantation">'.langTranslate('product','review elements','Decantation in a decanter (min.)','Decantation in a decanter (min.)')/*Декантация в графине (мин.)*/.'</label></td></tr>'.
             '<tr class="step step11"><td><input type="text" name="recommendation-decantation" class="edit-review-form-minutes" id="edit-review-form-recommendation-decantation" value="'.(isset($review['recommendation-decantation'])?htmlentities($review['recommendation-decantation']):'').'" /></td></tr>';
}
if(!in_array('recommendation-open-time', $tasting_review_particularity_data)){
    $rows .= '<tr class="step step11"><td class="label"><label for="edit-review-form-recommendation-open-time">'.langTranslate('product','review elements','Bottle opening time (min.)','Bottle opening time (min.)')/*За какое время открыть бутылку (мин.)*/.'</label></td></tr>'.
             '<tr class="step step11"><td><input type="text" name="recommendation-open-time" class="edit-review-form-minutes" id="edit-review-form-recommendation-open-time" value="'.(isset($review['recommendation-open-time'])?htmlentities($review['recommendation-open-time']):'').'" /></td></tr>';
}
if(!in_array('recommendation-year', $tasting_review_particularity_data)){
    $rows .= '<tr class="step step11"><td class="label"><label for="edit-review-form-recommendation-year_from">'.langTranslate('product','review elements','Drink (year)','Drink (year)')/*Пить (год)*/.'</label></td></tr>'.
             '<tr class="step step11"><td><span class="form-from-to"><label for="edit-review-form-recommendation-year_from">'.langTranslate('Year From', 'From').'</label><input type="text" name="recommendation-year_from" class="edit-review-form-year" id="edit-review-form-recommendation-year_from" value="'.(isset($review['recommendation-year_from'])?htmlentities($review['recommendation-year_from']):'').'" /><label for="edit-review-form-recommendation-year_to">'.langTranslate('Year To', 'To').'</label><input type="text" name="recommendation-year_to" class="edit-review-form-year" id="edit-review-form-recommendation-year_to" value="'.(isset($review['recommendation-year_to'])?htmlentities($review['recommendation-year_to']):'').'" /></span></td></tr>';
}
$rows .= $pasteChoiceElements('recommendation_elements', 'step step11');
if(strlen($rows)){
    echo '<tr class="step step11"><td class="header">'.langTranslate('product','review elements','Recommendations','Recommendations')/*Рекомендации*/.'</td></tr>'.$rows;
}
?><tr class="step step11"><td class="header"><?=langTranslate('Review','Review')?></td></tr>
<?php if($adding): ?>
<tr class="step step11 score"><td class="label"><label for="edit-review-form-score"><?=langTranslate('Score', 'Score');?></label><?php 
        $require_score_calc = in_array('require_score_calc', $tasting_review_particularity_data);
        if($require_score_calc):
        //<span class="show-score-calc"><?=langTranslate('Show score calc','Show score calc')?></span>
?><table class="score-calc <?=!$require_score_calc?'optional':''?>"><tbody><?php 
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
            $echo_score_cols = function($values, $name) use ($review){
                foreach($values as $value){
                    echo '<td><label><input type="radio" name="'.$name.'" value="'.$value.'" '.(isset($review[$name])&&$review[$name]==$value?'checked="checked"':'').' /><span>'.$value.'</span></label></td>';
                }
            };
            for($sparkling=0;$sparkling<=1;$sparkling++){
                $row_class = $sparkling?'wine-type-sparkling':'wine-type-still wine-type-fortified';
                $values_key = $sparkling?'values_s':'values_ns';
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
                    $tr_definition = '<tr class="segment '.$row_class.' '.(!$require_score_calc?'optional':'').'">';
                    echo $tr_definition.'<td class="caption" '.($single_row?'colspan="2"':'rowspan="'.$itemcount.'"').'>'.$section['caption'].'</td>';
                    if($single_row){
                        $echo_score_cols($section[$values_key], $section['name']);
                        continue;
                    }
                    $first_row = true;
                    foreach($section['items'] as $item){
                        if(!isset($item[$values_key])){
                            continue;
                        }
                        if(!$first_row){
                            echo $tr_definition;
                        } else {
                            $first_row = false;
                        }
                        echo '<td class="caption">'.$item['caption'].'</td>';
                        $echo_score_cols($item[$values_key], $item['name']);
                        echo '</tr>';
                    }
                }
            }
?></tbody></table><?php
        endif;
?><?php
if(in_array($tasting_id, array(61,93,145,146,147,148))):
?><span class="tip"><?php
        $currLangId = langCurrId();
        if($currLangId==2)://russian
?>80-83 – Бронза.<br />
84-86 – Серебро.<br />
87-89 – Золото.<br />
90-100 – Гран при.<?php
        else:
?>80-83 – Bronze.<br />
84-86 – Silver.<br />
87-89 – Gold.<br />
90-100 – The Grand Prix.<?php
        endif;
?></span><?php
else:
    if(!$personal_review && $tasting_assessment):
?><span class="tip"><?php
        $currLangId = langCurrId();
        if($currLangId==2)://russian
?> 
65-75 – Вино с дефектом, к употреблению не рекомендуется.<br />
75-79 – Посредственное, очень простое вино, граничащее с дефектным.<br />
80-82 – Приемлемое по качеству вино, можно пить в обычных, ни к чему не обязывающих обстоятельствах.<br />
83-86 – Хорошее, качественное вино, подходит на каждый день. Обладает типичными ароматом и вкусом.<br />
87-89 – Очень хорошее вино с особенными потребительскими характеристиками.<br />
90-93 – Отличное, сложное вино с ярко выраженной индивидуальностью.<br />
94-97 – Выдающееся вино исключительной сложности и характера, с длительным послевкусием. Вино для особого случая.<br />
98-100 - Необыкновенное вино. Это великолепное классическое вино. Вершина качества.<?php
        else:
?>65-75 – A wine deemed to be unacceptable. Not recommended.<br />
75-79 – A below average wine. It has little deficiencies, very simple, innocuous wine.<br />
80-82 – Acceptable wine. Can be employed in casual, less-critical circumstances.<br />
83-86 – Good-quality wine, suitable for everyday. Wine displaying various degrees of flavor.<br />
87-89 – Very good wine with special consumer characteristic. More complex, almost always it has pronounced aftertaste.<br />
90-93 – Excellent wine. Complex wine displaying own character. Highly recommended.<br />
94-97 – Outstanding wine of exceptional complexity and character with a long aftertaste. Wine for a special occasion.<br />
98-100 – Extraordinary wine. It’s great classic wine of its variety. The pinnacle of quality.<?php
        endif;
?></span><?php
    endif;
endif;
?></td></tr>
<tr class="step step11 score"><td><input type="text" name="score" id="edit-review-form-score" value="<?=getPostVal('score',isset($review['score'])?$review['score']:'')?>" <?=$require_score_calc?'readonly="readonly"':''?> /></td></tr>
<?php else: ?>
<tr class="step step11 score"><td class="label"><label><?=langTranslate('Score', 'Score');?></label></td></tr>
<tr class="step step11 score"><td><?=isset($review['score'])?$review['score']:''?></td></tr>
<?php endif;
    if($adding && !in_array('personal_comment', $tasting_review_particularity_data)): ?>
<tr class="step step11"><td class="label"><label for="edit-review-form-personal_comment"><?=langTranslate('Personal Comment', 'Personal Comment');?></label></td></tr>
<tr class="step step11"><td><textarea name="personal_comment" id="edit-review-form-personal_comment" /><?=getPostVal('personal_comment',isset($review['personal_comment'])?$review['personal_comment']:'')?></textarea></td></tr>
<?php endif;
    if(!in_array('review_text', $tasting_review_particularity_data)):
        if(count($languageList)>0): ?>
<tr class="step step11"><td class="tabblock">
<?php       $labelWidth = floor(100/count($languageList));
            foreach($languageList as $language):
                $language_id = $language['id'];
                $language_name = $language['name']; 
                ?><input type="radio" id="edit-review-form-language_tab_<?=$language_id?>" name="language_tab" class="ml_tab" value="<?=$language_id?>" /><label for="edit-review-form-language_tab_<?=$language_id?>" style="width:<?=$labelWidth?>%"><?=htmlentities($language_name)?></label><?php 
            endforeach; ?>
</td></tr>
<?php       foreach($languageList as $language):
                $language_id = $language['id']; ?>
<tr class="step step11 multilang lang_<?=$language_id?>"><td class="label"><label for="edit-review-form-review-<?=$language_id?>"><?=langTranslate('Review', 'Review');?></label></td></tr>
<tr class="step step11 multilang lang_<?=$language_id?>"><td><textarea name="review[<?=$language_id?>]" id="edit-review-form-review-<?=$language_id?>" /><?=getPostVal('review['.$language_id.']',isset($review['review'][$language_id])?$review['review'][$language_id]:'')?></textarea></td></tr>
<?php       endforeach; 
        endif;
    endif;
?>
<tr class="step step11"><td class="submit"><input type="submit" value="<?=langTranslate('Save', 'Save');?>" /></td></tr>
<tr class="next-btn"><td><input type="button" class="comlementary-submit didnottry" value="<?=langTranslate('Did not try', 'Did not try');?>" /><input type="button" class="main-btn next-btn" value="<?=langTranslate('Next', 'Next');?>" /></td></tr>
</tbody></table>
</form>
<?php langClean('product', 'review')?>