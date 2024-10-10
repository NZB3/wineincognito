<?php 
    if(!isset($personal_reviews)){
        $personal_reviews = false;
    }
    if(!isset($can_refresh)){
        $can_refresh = false;
    }
    if(!isset($can_add)){
        $can_add = false;
    }
    if(!isset($can_edit_vintage_list)){
        $can_edit_vintage_list = false;
    }
    if(!isset($can_merge_reviews)){
        $can_merge_reviews = false;
    }
    if(!isset($actions)){
        $actions = false;
    }
    if(!isset($request_review)){
        $request_review = false;
    }
    if(!isset($evaluations)){
        $evaluations = false;
    }
    if(!isset($evaluation_scores)){
        $evaluation_scores = false;
    }
    if(!isset($show_global_expert_automatic_evaluation)){
        $show_global_expert_automatic_evaluation = false;
    }
    if(!isset($scores)){
        $scores = false;
    }
    if(!isset($awaiting_review_count)){
        $awaiting_review_count = false;
    }
    
    if(!isset($show_desc)){
        $show_desc = false;
    }
    if(!isset($user_id)){
        $user_id = null;
    }
    if(!isset($tpv_id)){
        $tpv_id = null;
    }
    if($user_id){
        $awaiting_review_count = false;
        $evaluation_scores = true;
    }
    if(!isset($order_by_index)){
        $order_by_index = false;
    }
    if(!isset($only_can_review)){
        $only_can_review = false;
    }
    if(!isset($swap_reviews)){
        $swap_reviews = false;
    }
    if($swap_reviews){
        $multiple_select = true;
    }
    if(!isset($multiple_select)){
        $multiple_select = false;
    }
    if(!isset($stat_url)){
        $stat_url = false;
    }
    if(!isset($expert_level_list)||!is_array($expert_level_list)){
        $expert_level_list = array();
    }
    $expert_level_list_keys = array_keys($expert_level_list);
    $rowcount = 6+($actions?4:0)+($request_review?2:0)+($evaluations?1:0)+($evaluation_scores?2:0)+($show_global_expert_automatic_evaluation?1:0)+($scores?count($expert_level_list)+1:0)+($awaiting_review_count?1:0)+($only_can_review?1:0)+($multiple_select?1:0);
    $can_refresh = true;
?>
<div class="group-block subcontent">
<?php if($personal_reviews):?>
<div class="tutorial-block"><?=langTranslate('tasting','vintage','Your personal reviews','Your personal reviews')?></div>
<?php endif; ?>
<?php if($can_refresh || $can_add): ?>
<script type="template" class="vintagelist-item-template">
<tr data-tpv-id="{{tpv_id}}">
<?php if($multiple_select): ?><td class="multiple-select"><span></span></td><?php endif; ?><?php if($only_can_review): ?><td class="review-existance {if{review_exists}}review-exists{endif{review_exists}}"><span></span></td><?php endif; ?>
<td class="index">{{index}}</td><td class="image wi-gallery">{if{img}}<img src="{{img}}" />{endif{img}}</td><td class="name"><?php if(!$stat_url):?>{if{id}}<a href="<?=BASE_URL?>/vintage/{{id}}">{endif{id}}{{fullname}}{if{id}}</a>{endif{id}}<?php else: ?><a href="<?=BASE_URL?>/tasting/<?=$tasting_id?>/stats/product/{{tpv_id}}">{{fullname}}</a><?php endif;?>{if{awaiting_approval}}<span class="awaiting-approval"><?=langTranslate('product','vintage','Awaiting approval','Awaiting approval')?></span>{endif{awaiting_approval}}{if{isprimeur}}<span class="primeur"><?=langTranslate('tasting','vintage','En primeur', 'En primeur');?></span>{endif{isprimeur}}{if{lot}}<span class="lot"><label><?=langTranslate('tasting', 'vintage', 'Lot', 'Lot')?></label>{{lot}}</span>{endif{lot}}</td><td class="volume">{{volume}}</td><td class="preparation <?=$can_edit_vintage_list?'{if{can_change_preparation}}can-change{endif{can_change_preparation}}':''?>"><span class="preparation-text">{{preparation_type_text}}</span>{if{preparation_minutes_elapsed_pretty}}<span class="preparation-time">{{preparation_minutes_elapsed_pretty}}</span>{endif{preparation_minutes_elapsed_pretty}}</td><td class="blindness {if{blind}}blind{endif{blind}}"><span></span></td>
<?php if($actions): ?><td class="raise-index"><?=$can_edit_vintage_list?'<span></span>':''?></td><td class="lower-index"><?=$can_edit_vintage_list?'<span></span>':''?></td><?php endif; ?>
<?php if($request_review): ?><td class="request-reviews"><?=$can_edit_vintage_list?'{if{can_request_reviews}}<span class="non-sticky-tooltip" data-tooltip="'.langTranslate('tasting','tasting','Tooltip: Request reviews','Start product tasting').'"></span>{endif{can_request_reviews}}':''?></td><td class="stop-reviews"><?=$can_edit_vintage_list?'{if{can_stop_reviews}}<span class="non-sticky-tooltip" data-tooltip="'.langTranslate('tasting','tasting','Tooltip: Stop reviews','Finish product tasting').'"></span>{endif{can_stop_reviews}}':''?></td><?php endif; ?>
<?php if($evaluations): ?><td class="manual-evaluation"><?=$can_edit_vintage_list?'{if{can_set_manual_evaluation}}<a class="set-manual-evaluation non-sticky-tooltip" data-tooltip="'.langTranslate('tasting','tasting','Tooltip: Set manual evaluation','Set manual evaluation').'" href="'.BASE_URL.'/tasting/'.$tasting_id.'/product/{{tpv_id}}/evaluation/manual/set"></a>{endif{can_set_manual_evaluation}}':''?>{if{can_view_manual_evaluation}}<a class="view-manual-evaluation non-sticky-tooltip" data-tooltip="<?=langTranslate('tasting','tasting','Tooltip: Manual evaluation','Manual evaluation')?>" href="<?=BASE_URL?>/tasting/<?=$tasting_id?>/product/{{tpv_id}}/evaluation/manual/view"></a>{endif{can_view_manual_evaluation}}</td><?php endif; ?>
<?php if($evaluation_scores): ?><td class="score">{{automatic_evaluation_score}}</td><td class="score">{{manual_evaluation_score}}</td><?php endif; ?>
<?php if($show_global_expert_automatic_evaluation): ?><td class="score">{{global_expert_automatic_evaluation_score}}</td><?php endif; ?>
langTranslate('tasting', 'vintage', 'Lot', 'Lot')<?php if($scores): 
        foreach($expert_level_list_keys as $expert_level): ?><td class="score {if{scores_<?=$expert_level?>_draw_attention}}draw-attention{endif{scores_<?=$expert_level?>_draw_attention}}">{if{scores_<?=$expert_level?>}}{if{scores_<?=$expert_level?>_multiplereviews}}<?=$can_merge_reviews?'<a href="'.BASE_URL.'/tasting/'.$tasting_id.'/stats/product/{{tpv_id}}/reviewmerge/<?=$expert_level?>">':''?>{{scores_<?=$expert_level?>_score}} ({{scores_<?=$expert_level?>_count}})<?=$can_merge_reviews?'</a>':''?>{endif{scores_<?=$expert_level?>_multiplereviews}}{!if{scores_<?=$expert_level?>_multiplereviews}}{if{scores_<?=$expert_level?>_review_id}}<a href="<?=BASE_URL?>/tasting/<?=$tasting_id?>/stats/product/{{tpv_id}}/review/{{scores_<?=$expert_level?>_review_id}}">{endif{scores_<?=$expert_level?>_review_id}}{{scores_<?=$expert_level?>_score}}{if{scores_<?=$expert_level?>_review_id}}</a>{endif{scores_<?=$expert_level?>_review_id}}{end!if{scores_<?=$expert_level?>_multiplereviews}}{endif{scores_<?=$expert_level?>}}</td><?php 
        endforeach;
?><td class="score">{{userscore_urls}}</td><?php        
    endif; ?>
<?php if($awaiting_review_count): ?><td class="awaiting-review-count">{if{awaiting_review_count}}{{awaiting_review_count}}{endif{awaiting_review_count}}</td><?php endif; ?>
<?php if($actions): ?><td class="edit">{if{can_edit}}<span class="non-sticky-tooltip" data-tooltip="<?=langTranslate('tasting','tasting','Tooltip: Edit','Edit')?>"></span>{endif{can_edit}}</td><td class="remove">{if{can_delete}}<span class="non-sticky-tooltip" data-tooltip="<?=langTranslate('tasting','tasting','Tooltip: Remove','Remove')?>"></span>{endif{can_delete}}</td><?php endif; ?>
</tr>
<?php if($show_desc): ?>{if{desc}}<tr class="desc"><td colspan="<?=$rowcount?>">{{desc}}</td></tr>{endif{desc}}<?php endif; ?>
</script>
<script type="template" class="vintagelist-item-userscore-template">
<a href="<?=BASE_URL?>/vintage/{{id}}/review/{{review_id}}">{{score}}</a>
</script>
<?php endif; //$can_refresh 
?>
<?php if($request_review): ?>
<script type="string" class="confirm_string_stop_reviews"><?=formatReplace(langTranslate('tasting','tasting','Are you sure you want to finish the tasting of "@1"?','Are you sure you want to finish the tasting of "@1"?'),'{{name}}')?></script>
<?php endif; ?>
<table class="viewTasting-vintage-list <?=$swap_reviews?'viewTasting-vintage-list-swap-reviews':''?> <?=$multiple_select?'viewTasting-vintage-list-multiple-select':''?>" data-t-id="<?=$tasting_id?>" data-actions="<?=$actions?1:0?>" data-request-review="<?=$request_review?1:0?>" data-evaluations="<?=$evaluations?1:0?>" data-show-global-expert-automatic-evaluation=<?=$show_global_expert_automatic_evaluation?1:0?> data-scores="<?=$scores?1:0?>" data-awaiting-review-count="<?=$awaiting_review_count?1:0?>" <?=isset($auto_refresh_timer)&&$auto_refresh_timer?'data-auto-refresh-timer="'.((int)$auto_refresh_timer).'"':''?> data-show-desc="<?=$show_desc?1:0?>" data-user-id="<?=$user_id?>" data-tpv-id="<?=$tpv_id?>" data-order-by-index="<?=$order_by_index?1:0?>">
<thead>
    <tr class="head-buttons"><th colspan="<?=$rowcount?>">
<?php if($can_refresh): ?>
        <span class="refresh"></span>
<?php endif; ?>
<?php if($can_add): ?>
        <span class="add"></span>
<?php endif; ?>
    </th></tr>
<?php if(!$tpv_id): ?>
    <tr class="header"><th colspan="<?=$rowcount?>"><?=langTranslate('tasting', 'tasting', 'Product List',  'Product List')?></th></tr>
<?php endif; ?>
    <tr>
<?php if($multiple_select): ?><th></th><?php endif; ?>
<?php if($only_can_review): ?><th></th><?php endif; ?>
<th></th><th></th><th class="name"><?=langTranslate('product','vintage','Name','Name')?></th><th><?=langTranslate('product','vintage','Volume','Volume')?></th><th class="preparation"><?=langTranslate('tasting','preparation','Preparation','Preparation')?></th><th class="blindness"></th>
<?php if($actions): ?><th class="raise-index"></th><th class="lower-index"></th><?php endif; ?>
<?php if($request_review): ?><th class="request-reviews"></th><th class="stop-reviews"></th><?php endif; ?>
<?php if($evaluations): ?><th class="manual-evaluation"></th><?php endif; ?>
<?php if($evaluation_scores): ?><th class="score automatic-evaluation"><span data-tooltip="<?=langTranslate('tasting','tasting','Tooltip: Automatic evaluation score','Automatic evaluation score')?>"></span></th><th class="score manual-evaluation"><span data-tooltip="<?=langTranslate('tasting','tasting','Tooltip: Manual evaluation score','Manual evaluation score')?>"></span></th><?php endif; ?>
<?php if($show_global_expert_automatic_evaluation): ?><th class="score global-expert-automatic-evaluation"><span data-tooltip="<?=langTranslate('tasting','tasting','Tooltip: Global evaluation score','Global evaluation score')?>"></span></th><?php endif; ?>
<?php if($scores): foreach($expert_level_list as $expert_level=>$expert_label):?><th class="score score<?=$expert_level?>"><span data-tooltip="<?=$expert_label?>"></span></th><?php endforeach; ?><th class="score score-personal"><span data-tooltip="<?=langTranslate('user','Expert Level','Personal','Personal')?>"></span></th><?php endif; ?>
<?php if($awaiting_review_count): ?><th class="awaiting-review-count"><?=langTranslate('tasting', 'tasting', 'Awaiting',  'Awaiting')?></th><?php endif; ?>
<?php if($actions): ?><th class="edit"></th><th class="remove"></th><?php endif; ?>
    </tr>
</thead>
<tbody>
<?php foreach($tasting_vintage_list as $vintage):?>
    <tr data-tpv-id="<?=$vintage['tpv_id']?>">
<?php if($multiple_select): ?><td class="multiple-select"><span></span></td><?php endif; ?>
<?php if($only_can_review): ?><td class="review-existance <?=isset($vintage['review_exists'])&&$vintage['review_exists']?'review-exists':''?>"><span></span></td><?php endif; ?>
<td class="index"><?=$vintage['index']?></td><td class="image wi-gallery"><?=$vintage['img']?'<img src="'.$vintage['img'].'" />':''?></td><td class="name"><?php if(!$stat_url):?><?=$vintage['id']?'<a href="'.BASE_URL.'/vintage/'.$vintage['id'].'">':''?><?=htmlentities($vintage['fullname'])?><?=$vintage['id']?'</a>':''?><?php else: ?><a href="<?=BASE_URL?>/tasting/<?=$tasting_id?>/stats/product/<?=$vintage['tpv_id']?>"><?=htmlentities($vintage['fullname'])?></a><?php endif;?><?=isset($vintage['awaiting_approval'])&&$vintage['awaiting_approval']?'<span class="awaiting-approval">'.langTranslate('product','vintage','Awaiting approval','Awaiting approval').'</span>':''?><?=$vintage['isprimeur']?'<span class="primeur">'.langTranslate('tasting','vintage','En primeur', 'En primeur').'</span>':''?><?=strlen($vintage['lot'])?'<span class="lot"><label>'.langTranslate('tasting', 'vintage', 'Lot', 'Lot').'</label>'.htmlentities($vintage['lot']).'</span>':''?></td><td class="volume"><?=htmlentities($vintage['volume'])?></td><td class="preparation <?=($can_edit_vintage_list&&isset($vintage['can_change_preparation'])&&$vintage['can_change_preparation'])?'can-change':''?>"><span class="preparation-text"><?=htmlentities($vintage['preparation_type_text'])?></span><?=$vintage['preparation_minutes_elapsed_pretty']?'<span class="preparation-time">'.$vintage['preparation_minutes_elapsed_pretty'].'</span>':''?></td><td class="blindness <?=$vintage['blind']?'blind':''?>"><span></span></td>
<?php if($actions): ?><td class="raise-index"><?=$can_edit_vintage_list?'<span></span>':''?></td><td class="lower-index"><?=$can_edit_vintage_list?'<span></span>':''?></td><?php endif; ?>
<?php if($request_review): ?><td class="request-reviews"><?=($can_edit_vintage_list&&isset($vintage['can_request_reviews'])&&$vintage['can_request_reviews'])?'<span class="non-sticky-tooltip" data-tooltip="'.langTranslate('tasting','tasting','Tooltip: Request reviews','Start product tasting').'"></span>':''?></td><td class="stop-reviews"><?=($can_edit_vintage_list&&isset($vintage['can_stop_reviews'])&&$vintage['can_stop_reviews'])?'<span class="non-sticky-tooltip" data-tooltip="'.langTranslate('tasting','tasting','Tooltip: Stop reviews','Finish product tasting').'"></span>':''?></td><?php endif; ?>
<?php if($evaluations): ?><td class="manual-evaluation"><?=($can_edit_vintage_list&&isset($vintage['can_set_manual_evaluation'])&&$vintage['can_set_manual_evaluation'])?'<a class="set-manual-evaluation non-sticky-tooltip" data-tooltip="'.langTranslate('tasting','tasting','Tooltip: Set manual evaluation','Set manual evaluation').'" href="'.BASE_URL.'/tasting/'.$tasting_id.'/product/'.$vintage['tpv_id'].'/evaluation/manual/set"></a>':''?><?=(isset($vintage['can_view_manual_evaluation'])&&$vintage['can_view_manual_evaluation'])?'<a class="view-manual-evaluation non-sticky-tooltip" data-tooltip="'.langTranslate('tasting','tasting','Tooltip: Manual evaluation','Manual evaluation').'" href="'.BASE_URL.'/tasting/'.$tasting_id.'/product/'.$vintage['tpv_id'].'/evaluation/manual/view"></a>':''?></td><?php endif; ?>
<?php if($evaluation_scores): ?><td class="score"><?=$vintage['automatic_evaluation_score']?></td><td class="score"><?=$vintage['manual_evaluation_score']?></td><?php endif; ?>
<?php if($show_global_expert_automatic_evaluation): ?><td class="score"><?=$vintage['global_expert_automatic_evaluation_score']?></td><?php endif; ?>
<?php
    if($scores){
        if(isset($vintage['scores'])){
            foreach($expert_level_list_keys as $expert_level){
                if(isset($vintage['scores'][$expert_level])){
                    if($vintage['scores'][$expert_level]['count']>1){
                        echo '<td class="score '.(isset($vintage['scores'][$expert_level]['draw_attention'])&&$vintage['scores'][$expert_level]['draw_attention']?'draw-attention':'').'">';
                        if($can_merge_reviews){
                            echo '<a href="'.BASE_URL.'/tasting/'.$tasting_id.'/stats/product/'.$vintage['tpv_id'].'/reviewmerge/'.$expert_level.'">';    
                        }
                        echo $vintage['scores'][$expert_level]['score'].' ('.$vintage['scores'][$expert_level]['count'].')';
                        if($can_merge_reviews){
                            echo '</a>';
                        }
                        echo '</td>';
                    } else {
                        echo '<td class="score '.(isset($vintage['scores'][$expert_level]['draw_attention'])&&$vintage['scores'][$expert_level]['draw_attention']?'draw-attention':'').'">';
                        if(isset($vintage['scores'][$expert_level]['review_id'])&&$vintage['scores'][$expert_level]['review_id']){
                            echo '<a href="'.BASE_URL.'/tasting/'.$tasting_id.'/stats/product/'.$vintage['tpv_id'].'/review/'.$vintage['scores'][$expert_level]['review_id'].'">';
                        }
                        echo $vintage['scores'][$expert_level]['score'];
                        if(isset($vintage['scores'][$expert_level]['review_id'])&&$vintage['scores'][$expert_level]['review_id']){
                            echo '</a>';
                        }
                        echo '</td>';
                    }
                } else {
                    echo '<td class="score"></td>';
                }
            }
        } else {
            foreach($expert_level_list_keys as $expert_level){
                echo '<td class="score"></td>';
            }
        }
        if(isset($vintage['userscore'])){
            echo '<td class="score">';
            foreach($vintage['userscore'] as $userscore){
                echo '<a href="'.BASE_URL.'/vintage/'.$vintage['id'].'/review/'.$userscore['review_id'].'">'.$userscore['score'].'</a>';    
            }
            
            echo '</td>';
        } else {
            echo '<td class="score"></td>';
        }
    }
?>
<?php if($awaiting_review_count): ?><td class="awaiting-review-count"><?=$vintage['awaiting_review_count']?$vintage['awaiting_review_count']:''?></td><?php endif; ?>
<?php if($actions): ?><td class="edit"><?=$vintage['can_edit']?'<span class="non-sticky-tooltip" data-tooltip="'.langTranslate('tasting','tasting','Tooltip: Edit','Edit').'"></span>':''?></td><td class="remove"><?=$vintage['can_delete']?'<span class="non-sticky-tooltip" data-tooltip="'.langTranslate('tasting','tasting','Tooltip: Remove','Remove').'"></span>':''?></td><?php endif; ?>
    </tr>
<?php   if($show_desc&&strlen($vintage['desc'])): ?>  
    <tr class="desc"><td colspan="<?=$rowcount?>"><?=prepareMultilineValue($vintage['desc'])?></td></tr>
<?php   endif; ?>
<?php endforeach; ?>
</tbody>
</table>
</div>
