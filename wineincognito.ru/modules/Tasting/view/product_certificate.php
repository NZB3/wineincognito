<?php
    if(!isset($expert_level)){
        $expert_level = 3;
    }
    if(!isset($reviewer_rating)){
        $reviewer_rating = null;
    }
    if(!isset($highlight_expert_list)||!is_array($highlight_expert_list)){
        $highlight_expert_list = array();
    }
    $count_experts = isset($reviewmergeinfo['count'][$expert_level])?$reviewmergeinfo['count'][$expert_level]:1;
    $producer = '-';
    $location = '-';
    $winetype = null;//19
    $winecolor = null;//4
    $sweetness = null;//10
    foreach($vintageinfo['attributes'] as $attribute){
        if(!isset($attribute['id'])||!in_array($attribute['id'], array(11,8,4,19,10))||!isset($attribute['values'])||!isset($attribute['values'][0])||!isset($attribute['values'][0]['value'])){
            continue;
        }
        if($attribute['id']==11){//producer
            $producer = $attribute['values'][0]['value'];
        } elseif($attribute['id']==8){//location
            $location = $attribute['values'][0]['value'];
        } elseif($attribute['id']==4){//winecolor
            $winecolor = $attribute['values'][0]['value'];
        } elseif($attribute['id']==19){//winetype
            $winetype = $attribute['values'][0]['value'];
        } elseif($attribute['id']==10){//sweetness
            $sweetness = $attribute['values'][0]['value'];
        }
    }
    $winetype_code = null;
    switch(mb_strtolower($winetype,'UTF-8')){
        case 'тихое':
            $winetype_code = 'still';
            break;
        case 'игристое':
            $winetype_code = 'sparkling';
            break;
        case 'специальное':
            $winetype_code = 'fortified';
            break;
    }
    $winecolor_code = null;
    switch(mb_strtolower($winecolor,'UTF-8')){
        case 'красное':
            $winecolor_code = 'red';
            break;
        case 'белое':
            $winecolor_code = 'white';
            break;
        case 'розовое':
            $winecolor_code = 'pink';
            break;
    }
    $location_parts = explode(',', $location);
    $last_location_part = null;
    foreach($location_parts as $key=>$location_part){
        $location_parts[$key] = trim($location_part);
        if($last_location_part===$location_parts[$key]){
            unset($location_parts[$key]);
            continue;
        }
        $last_location_part = $location_parts[$key];
    }
    $location = implode(', ', $location_parts);
    //image
    $image_url = null;
    if(isset($vintageinfo['images'])&&!empty($vintageinfo['images'])){
        $image_url = $vintageinfo['images'][0]['url'];
    }
    if($image_url===null){
        switch($winecolor_code){
            case 'red':
                $image_url = BASE_URL.'/modules/Product/img/noimage-wine-red.png';
                break;
            case 'white':
                $image_url = BASE_URL.'/modules/Product/img/noimage-wine-white.png';
                break;
            case 'pink':
                $image_url = BASE_URL.'/modules/Product/img/noimage-wine-rose.png';
                break;
        }
        
    }
    //score
    $score = isset($reviewmergeinfo['score'][$expert_level])?floor(str_replace(',', '.', $reviewmergeinfo['score'][$expert_level])):null;
    $score_label = '';
    $score_single_digit = $score%10;
    if(($score%100>=11&&$score%100<=14)||$score_single_digit==0||$score_single_digit>4){
        $score_label = 'баллов';
    } elseif($score_single_digit==1){
        $score_label = 'балл';
    } else {
        $score_label = 'балла';
    }
    //popular params
    $popular_review_params = array();
    foreach($review_elements as $review_elements_group){
        foreach($review_elements_group as $element){
            if(!isset($element['name'])){
                continue;
            }
            $element_name = $element['name'];
            if(!isset($reviewmergeinfo['params'][$element_name])){
                continue;
            }
            foreach($element['values'] as $elem_val){
                $value = $elem_val['value'];
                if(!isset($reviewmergeinfo['params'][$element_name][$value])){
                    continue;
                }
                if(!isset($reviewmergeinfo['params'][$element_name][$value][$expert_level])){
                    continue;
                }
                if(!isset($popular_review_params[$element_name])){
                    $popular_review_params[$element_name] = array();
                }
                $popular_review_params[$element_name][$value] = array('label'=>$elem_val['label'],'votes'=>$reviewmergeinfo['params'][$element_name][$value][$expert_level]);
            }
        }
    }
    $get_popular_value = function($element_name,$return_label=true,$include_zero=true) use ($popular_review_params,$count_experts){
        if(!isset($popular_review_params[$element_name])){
            return null;
        }
        $max_vote = null;
        $chosen_label = null;
        $chosen_value = null;
        $sumvotes = 0;
        foreach($popular_review_params[$element_name] as $value=>$value_data){
            if(!$include_zero && $value==0){
                continue;
            }
            if($max_vote===null || $max_vote<=$value_data['votes']){
                $max_vote = $value_data['votes'];
                $chosen_label = $value_data['label'];
                $chosen_value = $value;
            }
            $sumvotes+=$value_data['votes'];
        }
        if($sumvotes<2 || floor($sumvotes*10/$count_experts)<3){
            return null;
        }
        if($return_label){
            return $chosen_label;
        }
        return $chosen_value;
    };
    $filter_aroma_values = function($element_name) use ($popular_review_params,$count_experts){
        if(!isset($popular_review_params[$element_name])){
            return null;
        }
        $result = array();
        $element_values = $popular_review_params[$element_name];
        usort($element_values,function($a,$b){
            if($a['votes'] > $b['votes']){
                return -1;
            }
            if($a['votes'] < $b['votes']){
                return 1;
            }
            return 0;
        });
        foreach($element_values as $value_data){
            if(count($result)>=3){
                break;
            }
            if($value_data['votes']<2 || floor($value_data['votes']*10/$count_experts)<3){
                break;
            }
            $result[] = $value_data['label'];
        }
        return $result;
    };



    //wine
    $data = array();
    if($winetype){
        $data[] = $winetype;
    }
    if($winecolor){
        $data[] = $winecolor;
    }
    if(isset($reviewmergeinfo['subcolor'])){
        $popular_count = null;
        $popular_subcolor_info = null;
        foreach($reviewmergeinfo['subcolor'] as $subcolor_info){
            if(!isset($subcolor_info['counts'][$expert_level])){
                continue;
            }
            if($popular_count===null||$popular_count<$subcolor_info['counts'][$expert_level]){
                $popular_count = $subcolor_info['counts'][$expert_level];
                $popular_subcolor_info = $subcolor_info;
            }
        }
        if($popular_subcolor_info!==null){
            foreach($review_elements['color_spectrum_subcolor_data'] as $subcolor_data){
                if($subcolor_data['color']==$popular_subcolor_info['color'] && 
                        $subcolor_data['subcolor']==$popular_subcolor_info['subcolor'] && 
                        $subcolor_data['depth']==$popular_subcolor_info['depth']){
                    $subcolor_row = '';
                    $data[] = formatReplace('имеет @1 цвет',$subcolor_data['title']);
                    break;
                }
            }
        }
    }
    if($winetype_code=='sparkling'){
        $val = $get_popular_value('sparkling-rating-bubblesize');
        if($val){
            $data[] = formatReplace('пузырьки @1',$val);
            $val = $get_popular_value('sparkling-rating-quantity');
            if($val){
                $data[] = formatReplace('их @1 количество',$val);
            }
            $val = $get_popular_value('sparkling-rating-continuance');
            if($val){
                $data[] = formatReplace('длительность перляжа @1',$val);
            }
        }
        
    }
    if(!empty($data)){
        $descrows[] = '<tr><td class="term">Вино</td><td class="definition">'.implode(', ', $data).'</td></tr>';
    }
    //aroma
    $data = array();
    $val = $get_popular_value('overall-aroma-complexity');
    if($val){
        $data[] = $val;
    }
    $val = $get_popular_value('overall-aroma-intensity');
    if($val){
        switch($val){
            case 'Ниже среднего':
                $val = 'с интенсивностью ниже среднего';
                break;
            case 'Средний':
                $val = 'со средней интенсивностью';
                break;
            case 'Выше среднего':
                $val = 'с интенсивностью выше среднего';
                break;
        }
        $data[] = $val;
    }
    $val = $get_popular_value('overall-aroma-development');
    if($val){
        $data[] = $val;
    }
    $aroma_list = array(
            array(
                    'primary-aroma-flowery'=>'primary-aroma-flowery-type',
                    'primary-aroma-greenery'=>'primary-aroma-greenery-type',
                    'primary-aroma-herbs'=>'primary-aroma-herbs-type',
                    'primary-aroma-citrus'=>'primary-aroma-citrus-type',
                    'primary-aroma-white-fruit'=>'primary-aroma-white-fruit-type',
                    'primary-aroma-red-fruit'=>'primary-aroma-red-fruit-type',
                    'primary-aroma-black-fruit'=>'primary-aroma-black-fruit-type',
                    'primary-aroma-ossicle-fruit'=>'primary-aroma-ossicle-fruit-type',
                    'primary-aroma-tropical-fruit'=>'primary-aroma-tropical-fruit-type',
                    'primary-aroma-mineral'=>'primary-aroma-mineral-type',
                ),
            array(
                    'secondary-aroma-yeast'=>'secondary-aroma-yeast-type',
                    'secondary-aroma-milk'=>'secondary-aroma-milk-type',
                    'secondary-aroma-spice'=>'secondary-aroma-spice-type',
                    'secondary-aroma-confectionery'=>'secondary-aroma-confectionery-type',
                    'secondary-aroma-empirical'=>'secondary-aroma-empirical-type',
                ),
            array(
                    'tertiary-aroma-dried-fruit'=>'tertiary-aroma-dried-fruit-type',
                    'tertiary-aroma-nuts'=>'tertiary-aroma-nuts-type',
                    'tertiary-aroma-confection'=>'tertiary-aroma-confection-type',
                    'tertiary-aroma-aging'=>'tertiary-aroma-aging-type',
                ),
        );
    
    $first_aroma = true;
    foreach($aroma_list as $aroma_tier_list){
        $aroma_intensities = array();
        foreach($aroma_tier_list as $aroma_element_name=>$aroma_element_type_name){
            $value = $get_popular_value($aroma_element_name,false,false);
            if($value<=0){
                continue;
            }
            if(!isset($aroma_intensities[$value])){
                $aroma_intensities[$value] = array();
            }
            $aroma_intensities[$value][] = $aroma_element_type_name;
        }
        if(!empty($aroma_intensities)){
            $aroma_tier_flip_list = array_flip($aroma_tier_list);
            $aroma_intensity_keys = array_keys($aroma_intensities);
            rsort($aroma_intensity_keys);
            foreach($aroma_intensity_keys as $key){
                foreach($aroma_intensities[$key] as $aroma_element_type_name){
                    $caption = null;
                    foreach($review_elements as $review_elements_group){
                        foreach($review_elements_group as $element){
                            if(isset($element['name'])&&$element['name']==$aroma_tier_flip_list[$aroma_element_type_name]){
                                $caption = $element['caption'];
                                break 2;
                            }
                        }
                    }
                    if(!$caption){
                        continue;
                    }
                    $aromas = $filter_aroma_values($aroma_element_type_name);
                    $data[] = ($first_aroma?'тона: ':'').$caption.(!empty($aromas)?' ('.implode(', ', $aromas).')':'');
                    $first_aroma = false;
                }
            }
        }
    }
    
    
    

    if(!empty($data)){
        $descrows[] = '<tr><td class="term">Аромат</td><td class="definition">'.implode(', ', $data).'</td></tr>';
    }
    //taste
    $val = $get_popular_value('taste-complexity');
    if($val){
        $data[] = $val;
    }
    $val = $get_popular_value('taste-intensity');
    if($val){
        switch($val){
            case 'Ниже среднего':
                $val = 'с интенсивностью ниже среднего';
                break;
            case 'Средний':
                $val = 'со средней интенсивностью';
                break;
            case 'Выше среднего':
                $val = 'с интенсивностью выше среднего';
                break;
        }
        $data[] = $val;
    }

    $val = $get_popular_value('taste-development');
    if($val){
        switch($val){
            case 'Молодой':
                $val = 'Неразвитый';
                break;
        }
        $data[] = $val;
    }
    $data = array();
    if(!empty($data)){
        $descrows[] = '<tr><td class="term">Вкус</td><td class="definition">'.implode(', ', $data).'</td></tr>';
    }
    //sweetness
    $data = array();
    if($sweetness){
        $data[] = $sweetness;
    }
    if(!empty($data)){
        $descrows[] = '<tr><td class="term">Сахар</td><td class="definition">'.implode(', ', $data).'</td></tr>';
    }
    //acidity
    $data = array();
    $val = $get_popular_value('taste-structure-acidity');
    if($val){
        $data[] = $val;
    }
    if(!empty($data)){
        $descrows[] = '<tr><td class="term">Кислотность</td><td class="definition">'.implode(', ', $data).'</td></tr>';
    }
    //alcohol
    $data = array();
    $val = $get_popular_value('taste-structure-alcohol');
    if($val){
        $data[] = $val;
    }
    
    if(!empty($data)){
        $descrows[] = '<tr><td class="term">Алкоголь</td><td class="definition">'.implode(', ', $data).'</td></tr>';
    }
    //balance
    $data = array();
    $total_score = 1;
    $val = $get_popular_value('balance-fruit',false);
    if(!$val){
        $val = 1;
    }
    $total_score *= $val*100;
    $val = $get_popular_value('balance-alcohol',false);
    if(!$val){
        $val = 1;
    }
    $total_score *= $val*100;
    $val = $get_popular_value('balance-acid',false);
    if(!$val){
        $val = 1;
    }
    $total_score *= $val*100;
    $total_score = floor(round(pow($total_score,1/3))/100);
    if(isset($review_elements['taste_structure_elements'])){
        foreach($review_elements['taste_structure_elements'] as $element){
            if($element['name']=='balance-acid'){
                foreach($element['values'] as $elem_value){
                    if($elem_value['value']==$total_score){
                        $val = $elem_value['label'];
                        switch($val){
                            case 'Легкий дисбаланс':
                                $val = 'Имеет легкий дисбаланс';
                                break;
                            case 'Заметный дисбаланс':
                                $val = 'Имеет заметный дисбаланс';
                                break;
                        }

                        $data[] = $val;
                        break;
                    }
                }
                break;
            }
        }
    }
    if(!empty($data)){
        $descrows[] = '<tr><td class="term">Баланс</td><td class="definition">'.implode(', ', $data).'</td></tr>';
    }
    //taste-continuance
    $data = array();
    $val = $get_popular_value('taste-continuance');
    if($val){
        switch($val){
            case 'Короткий':
                $val = 'Короткое';
                break;
            case 'Средний':
                $val = 'Среднее';
                break;
            case 'Долгий':
                $val = 'Длительное';
                break;
        }
        $data[] = $val;
    }
    if(!empty($data)){
        $descrows[] = '<tr><td class="term">Послевкусие</td><td class="definition">'.implode(', ', $data).'</td></tr>';
    }
    //aroma
    $data = array();
    $val = $get_popular_value('similarity-age');
    if($val){
        $data[] = $val;
    }
    if(!empty($data)){
        $descrows[] = '<tr><td class="term">Степень зрелости</td><td class="definition">'.implode(', ', $data).'</td></tr>';
    }
    $descrows[count($descrows)-1] = preg_replace('#^<tr>#', '<tr class="stretch">', $descrows[count($descrows)-1]);

    $contest_footer_name = preg_replace('#(?:^,|,$)#', '', preg_replace('#(?:\s+,|,(?:\s*,)+)#', ',', preg_replace('#\s{2,}#', ' ', trim($contestinfo['name'].', '.$contestinfo['location'].', '.date('d.m.Y',$contestinfo['startts'])))));
?><!doctype html>
<html>
<head>
    <title>Wine Incognito - certificate</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="<?=BASE_URL?>/modules/Tasting/css/product_certificate.css">
    <script src="<?=BASE_URL?>/modules/Main/js/jquery.min.js"></script>
</head>
<body>
<div id="page"><div id="certificate-body">

<table class="body"><tbody>
<tr><td colspan="3"><table class="header"><tbody>
<?php if(in_array($contestinfo['id'], array(21,23))): ?>
<tr id="header-name"><td colspan="3"><?=htmlentities($contestinfo['name'])?></td></tr>
<?php else: ?>
<tr id="header-logo"><td colspan="3"><img src="<?=BASE_URL?>/modules/Main/img/logo.png" /></td></tr>
<?php endif; ?>
<tr class="sub-header"><td><span class="label">Название вина</span><span class="data"><?=htmlentities($vintageinfo['name'])?></span></td><td></td><td><span class="label">Страна</span><span class="data"><?=htmlentities($location)?></span></td></tr>
<tr class="sub-header"><td><span class="label">Год урожая</span><span class="data"><?=$vintageinfo['isvintage']?htmlentities($vintageinfo['year']):'NV'?></span></td><td></td><td><span class="label">Производитель</span><span class="data"><?=htmlentities($producer)?></span></td></tr>
</tbody></table></td></tr>
<tr><td rowspan="<?=count($descrows)+1?>" class="body-left <?=!$score?'no-score':''?>"><div class="score"><?php 
    if($score): 
?><span class="score"><?=$score?></span><span class="label"><?=$score_label?></span><?php 
    endif;
?><?php 
    if($reviewer_rating):
?><div class="header">Экспертный рейтинг</div><div class="header">Рейтинг обозревателей</div><div class="reviewer-rating reviewer-rating-<?=$reviewer_rating?>"><?php 
        for($i=1;$i<=5;$i++):
?><img src="<?=BASE_URL?>/modules/Tasting/img/product-certificate-rating<?=($i<=$reviewer_rating)?'-fill':''?>.png" /><?php
        endfor;
?></div><?php 
    endif;
?></div><?php 
    if($image_url): 
?><img src="<?=$image_url?>" /><?php 
    endif; 
?></td><td colspan="2" class="header">Экспертная оценка</td></tr>
<?=implode('', $descrows)?>
<tr><td colspan="3" class="footer">WINEINCOGNITO - профессиональный инструмент для оценки потребительских свойств вина.<?=strlen($contest_footer_name)?'<br />'.htmlentities($contest_footer_name):''?><?php
if(!empty($highlight_expert_list)){
    $highlight_expert_names = array();
    $highlight_expert_list_count = count($highlight_expert_list);
    for($i=0;$i<min(10,$highlight_expert_list_count);$i++){
        $highlight_expert_names[] = $highlight_expert_list[$i]['name'];
    }
    echo '<br />Эксперты: '.implode(', ', $highlight_expert_names);
    if($highlight_expert_list_count>10){
        echo ' и др.';
    }
}
?></td></tr>
<?php if(in_array($contestinfo['id'], array(21,23))): ?>
<tr><td colspan="3" class="footer-logo"><ul><li><img src="<?=BASE_URL?>/modules/Tasting/img/certmp/WoG.png" /></li><li><img src="<?=BASE_URL?>/modules/Main/img/logo.png" /></li><li><img src="<?=BASE_URL?>/modules/Tasting/img/certmp/WineState.png" /></li></ul></td></tr>
<?php endif; ?>
</tbody></table>
</div></div>
<script language="JavaScript">
window.onload = function(){
    $(function(){
<?php if($image_url): ?>
        var $bodyLeft = $("table.body td.body-left");
        var remainingHeight = $bodyLeft.innerHeight()-$bodyLeft.find("div.score").outerHeight()-10;
        $bodyLeft.children("img").css({maxHeight:remainingHeight+"px",display:"block"});
<?php endif; ?>
        window.print();
    });
}
</script>
</body></html>
<?php 