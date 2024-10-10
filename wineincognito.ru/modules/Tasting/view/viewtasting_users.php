<?php 
    langSetDefault('tasting', 'tasting');
    if(!isset($can_edit_users)){
        $can_edit_users = false;
    }
    if(!isset($can_add)){
        $can_add = false;
    }
    if(!isset($can_refresh)){
        $can_refresh = false;
    }
    if(!isset($can_block_reviews)){
        $can_block_reviews = false;
    }
    $can_refresh = true;
    if(!isset($can_mark_user_presence)){
        $can_mark_user_presence = false;
    }
    if(!isset($actions)){
        $actions = false;
    }
    if(!isset($show_response)){
        $show_response = false;
    }
    if(!isset($show_background)){
        $show_background = false;
    }
    if(!isset($only_present)){
        $only_present = false;
    }
    if(!isset($evaluation_scores)){
        $evaluation_scores = false;
    }
    if(!isset($show_global_expert_automatic_evaluation)){
        $show_global_expert_automatic_evaluation = false;
    }
    if(!isset($stat_url)){
        $stat_url = false;
    }
    if(!isset($swap_url)){
        $swap_url = false;
    }
    if(!isset($product_id)){
        $product_id = null;
    }
    if(!isset($user_id)){
        $user_id = null;
    }
    if(!isset($expert_level_list)){
        $expert_level_list = array();
    }
    $expert_level_list_keys = array_keys($expert_level_list);
    $rowcount = 3+($actions?3:0)+($show_response?1:0)+($show_background?1:0)+($evaluation_scores?2:0)+($show_global_expert_automatic_evaluation?1:0)+($product_id?count($expert_level_list):0);
    $base_user_url = BASE_URL.'/user/{{id}}';
    if($stat_url){
        $base_user_url = BASE_URL.'/tasting/'.$tasting_id.'/stats/user/{{id}}';
    }elseif($swap_url){
        $base_user_url = BASE_URL.'/tasting/'.$tasting_id.'/swapreviews/user/{{id}}';
    }
?>
<div class="group-block">
<script type="template" class="userlist-item-template">
<tr data-id="{{id}}" class="{if{presence}}user-was-present{endif{presence}} {if{isguest}}guest-user{endif{isguest}}"><td class="name"><a href="<?=$base_user_url?>">{{name}}</a></td><td class="company">{if{company_id}}<a href="<?=BASE_URL?>/company/{{company_id}}" target="_blank">{{company_name}}</a>{endif{company_id}}</td>
<?php if($show_background): ?><td class="background">{{background}}</td><?php endif; ?>
<td class="expert">{{expert_level}}</td>
<?php if($show_response): ?><td class="response">{{response}}</td><?php endif; ?>
<?php if($evaluation_scores): ?><td class="score">{{automatic_evaluation_score}}</td><td class="score">{{manual_evaluation_score}}</td><?php endif; ?>
<?php if($show_global_expert_automatic_evaluation): ?><td class="score">{{global_expert_automatic_evaluation_score}}</td><?php endif; ?>
<?php if($product_id): 
    foreach($expert_level_list_keys as $expert_level): 
?><td class="score">{if{score_<?=$expert_level?>}}{if{score_score}}{if{score_review_id}}<a href="<?=BASE_URL?>/tasting/<?=$tasting_id?>/stats/product/<?=$product_id?>/review/{{score_review_id}}">{endif{score_review_id}}{{score_score}}{if{score_review_id}}</a>{endif{score_review_id}}{endif{score_score}}{if{score_faulty}}<span class="faulty"><?=langTranslate('Faulty','Faulty')?></span>{endif{score_faulty}}{if{score_didnottaste}}<span class="didnottaste"><?=langTranslate('Did not taste','Skipped')?></span>{endif{score_didnottaste}}{endif{score_<?=$expert_level?>}}</td><?php 
    endforeach;
    if($can_block_reviews):
?><td class="block-review {if{score_blocked_by_moderator}}review-blocked{endif{score_blocked_by_moderator}}">{if{can_block_review}}<span class="non-sticky-tooltip" data-tooltip="<?=langTranslate('Tooltip: Review visibility','Review visibility')?>"></span>{endif{can_block_review}}</td><?php
    endif;
endif;
if($actions): ?><td class="present"><?=$can_mark_user_presence?'<span class="non-sticky-tooltip" data-tooltip="'.langTranslate('Tooltip: Present','Present').'"></span>':''?></td><td class="absent"><?=$can_mark_user_presence?'<span class="non-sticky-tooltip" data-tooltip="'.langTranslate('Tooltip: Absent','Absent').'"></span>':''?></td><td class="remove"><?=$can_edit_users?'<span class="non-sticky-tooltip" data-tooltip="'.langTranslate('Tooltip: Remove','Remove').'"></span>':''?></td><?php endif; ?>
</tr>
</script>
<?php
    $has_guests = false;
    $has_non_guests = false;
    foreach($tasting_user_list as $user){
        if($user['isguest']){
            $has_guests = true;
        }
        if(!$user['isguest']){
            $has_non_guests = true;
        }
        if($has_guests&&$has_non_guests){
            break;
        }
    }
?>
<table class="subcontent viewTasting-user-list <?=($has_non_guests&&$has_guests)?'has-guests guests-hide':''?>" data-t-id="<?=$tasting_id?>" data-only-present="<?=$only_present?1:0?>" data-evaluation-scores="<?=$evaluation_scores?1:0?>" data-show-global-expert-automatic-evaluation=<?=$show_global_expert_automatic_evaluation?1:0?> data-show-response="<?=$show_response?1:0?>" data-show-background="<?=$show_background?1:0?>" data-product-id="<?=$product_id?>" data-user-id="<?=$user_id?>">
<thead>
    <tr class="head-buttons"><th colspan="<?=$rowcount?>">
<?php if($can_refresh): ?>
        <span class="refresh"></span>
<?php endif;
    if($can_add): ?>
        <span class="add"></span>
<?php endif; ?>
    </th></tr>
<?php if(!$user_id): ?>
    <tr class="header"><th colspan="<?=$rowcount?>"><?=langTranslate('Invite List',  'Invite List')?></th></tr>
<?php endif; ?>    
    <tr><th class="name"><?=langTranslate('user','user','Name','Name')?></th><th class="company"><?=langTranslate('user','user','Company','Company')?></th>
<?php if($show_background): ?><th class="background"><?=langTranslate('user','user','Background','Background')?></th><?php endif; ?>
        <th class="expert"><?=langTranslate('user','user','Expert','Expert')?></th>
<?php if($show_response): ?><th><?=langTranslate('Response','Response')?></th><?php endif; ?>
<?php if($evaluation_scores): ?><th class="score automatic-evaluation"><span data-tooltip="<?=langTranslate('Tooltip: Automatic evaluation score','Automatic evaluation score')?>"></span></th><th class="score manual-evaluation"><span data-tooltip="<?=langTranslate('Tooltip: Manual evaluation score','Manual evaluation score')?>"></span></th><?php endif; ?>
<?php if($show_global_expert_automatic_evaluation): ?><th class="score global-expert-automatic-evaluation"><span data-tooltip="<?=langTranslate('Tooltip: Global evaluation score','Global evaluation score')?>"></span></th><?php endif; ?>
<?php if($product_id): foreach($expert_level_list as $expert_level=>$expert_label):?><th class="score score<?=$expert_level?>"><span data-tooltip="<?=$expert_label?>"></span></th><?php endforeach; if($can_block_reviews): ?><th class="block-review"></th><?php endif; endif; ?>
<?php if($actions): ?><th class="present"></th><th class="absent"></th><th class="remove"></th><?php endif; ?>
    </tr>
</thead>
<tbody>
<?php foreach($tasting_user_list as $user): ?>
    <tr data-id="<?=$user['id']?>" class="<?=$user['presence']?'user-was-present':''?> <?=$user['isguest']?'guest-user':''?>"><td class="name"><a href="<?=str_replace('{{id}}',$user['id'],$base_user_url)?>"><?=htmlentities($user['name'])?></a></td><td class="company"><?php if($user['company_id']): ?><a href="<?=BASE_URL?>/company/<?=$user['company_id']?>" target="_blank"><?=htmlentities($user['company_name'])?></a><?php endif; ?></td>
<?php if($show_background): ?><td class="background"><?=htmlentities($user['background'])?></td><?php endif; ?>
    <td class="expert"><?=htmlentities($user['expert_level'])?></td>
<?php if($show_response): ?><td class="response"><?=htmlentities($user['response'])?></td><?php endif; ?>
<?php if($evaluation_scores): ?><td class="score"><?=$user['automatic_evaluation_score']?></td><td class="score"><?=$user['manual_evaluation_score']?></td><?php endif; ?>
<?php if($show_global_expert_automatic_evaluation): ?><td class="score"><?=$user['global_expert_automatic_evaluation_score']?></td><?php endif; ?>
<?php
    if($product_id){
        foreach($expert_level_list_keys as $expert_level){ 
            if(isset($user['score']) && $user['score']['expert_level']==$expert_level){
                if($user['score']['score']){
                    echo '<td class="score">';
                    if(isset($user['score']['review_id'])&&$user['score']['review_id']){
                        echo '<a href="'.BASE_URL.'/tasting/'.$tasting_id.'/stats/product/'.$product_id.'/review/'.$user['score']['review_id'].'">';
                    }
                    echo $user['score']['score'];
                    if(isset($user['score']['review_id'])&&$user['score']['review_id']){
                        echo '</a>';
                    }
                    echo '</td>';
                } elseif(isset($user['score']['faulty'])&&$user['score']['faulty']){
                    echo '<td class="score"><span class="faulty">'.langTranslate('Faulty','Faulty').'</span></td>';
                } elseif(isset($user['score']['faulty'])&&$user['score']['didnottaste']){
                    echo '<td class="score"><span class="didnottaste">'.langTranslate('Did not taste','Skipped').'</span></td>';
                } else {
                    echo '<td class="score"></td>';
                }
            } else {
                echo '<td class="score"></td>';
            }
        }
        if($can_block_reviews){
            echo '<td class="block-review '.(isset($user['review_blocked_by_moderator'])&&$user['review_blocked_by_moderator']?'review-blocked':'').'">'.(isset($user['can_block_review'])&&$user['can_block_review']?'<span class="non-sticky-tooltip" data-tooltip="'.langTranslate('Tooltip: Review visibility','Review visibility').'"></span>':'').'</td>';
        }
    }
?>
<?php if($actions): ?><td class="present"><?=$can_mark_user_presence?'<span class="non-sticky-tooltip" data-tooltip="'.langTranslate('Tooltip: Present','Present').'"></span>':''?></td><td class="absent"><?=$can_mark_user_presence?'<span class="non-sticky-tooltip" data-tooltip="'.langTranslate('Tooltip: Absent','Absent').'"></span>':''?></td><td class="remove"><?=$can_edit_users?'<span class="non-sticky-tooltip" data-tooltip="'.langTranslate('Tooltip: Remove','Remove').'"></span>':''?></td><?php endif; ?>
</tr>
<?php endforeach; ?>
</tbody>
<tfoot>
    <tr class="showguests"><th colspan="<?=$rowcount?>"><span class="show"><?=langTranslate('Show guests', 'Show guests');?></span><span class="hide"><?=langTranslate('Hide guests', 'Hide guests');?></span></th></tr>
</tfoot>
</table>
</div>
<?php langClean('tasting', 'tasting')?>
