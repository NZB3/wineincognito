<?php 
    $expert_level_list_array_keys = array_keys($expert_level_list);

    if(isset($vintage_review_details_list['tastings']) && count($vintage_review_details_list['tastings'])>0): 
        $found_expert_levels = array();
        foreach($vintage_review_details_list['tastings'] as $tasting){
            $expert_levels = array_keys($tasting['scores']);
            foreach($expert_levels as $expert_level){
                if(in_array($expert_level, $found_expert_levels)){
                    continue;
                }
                if(!in_array($expert_level, $expert_level_list_array_keys)){
                    continue;
                }
                $found_expert_levels[] = $expert_level;
            }
        }
        $available_expert_levels = array();
        foreach($expert_level_list as $expert_level=>$dummy){
            if(in_array($expert_level, $found_expert_levels)){
                $available_expert_levels[] = $expert_level;
            }
        }
        unset($found_expert_levels);
        
?>
<table class="subcontent scoredetails scoredetails-tastings">
    <thead>
<?php if(!$show_depth_urls): ?>
        <tr class="head-buttons"><th colspan="<?=count($available_expert_levels)+1?>"><a class="mainbtn back" href="<?=BASE_URL?>/vintage/<?=$vintage_id?>/scoredetails"><?=langTranslate('menu','navigation','Back','Back')?></a></th></tr>
<?php endif; ?>
        <tr><th class="name" rowspan="2"><?=langTranslate('tasting','tasting','Name','Name')?></th><th class="score" colspan="<?=count($available_expert_levels)?>"><?=langTranslate('product','vintage','Score','Score')?></th></tr>
        <tr>
<?php foreach($available_expert_levels as $expert_level): ?>
<th><?=isset($expert_level_list[$expert_level])?htmlentities($expert_level_list[$expert_level]):''?></th>
<?php endforeach; ?>
        </tr>
    </thead>
    <tfoot>
        <tr><th class="name" rowspan="2"><?=langTranslate('tasting','tasting','Name','Name')?></th>
<?php foreach($available_expert_levels as $expert_level): ?>
<th><?=isset($expert_level_list[$expert_level])?htmlentities($expert_level_list[$expert_level]):''?></th>
<?php endforeach; ?>
        </tr>
        <tr><th class="score" colspan="<?=count($available_expert_levels)?>"><?=langTranslate('product','vintage','Score','Score')?></th></tr>
    </tfoot>
    <tbody>
<?php   foreach($vintage_review_details_list['tastings'] as $tasting): ?>
<tr><td class="name">
<?php       if($show_depth_urls): ?>
    <a href="<?=BASE_URL?>/vintage/<?=$vintage_id?>/scoredetails/tasting/<?=$tasting['id']?>"><?=formatReplace(langTranslate('tasting', 'tasting', 'Tasting №@1 from @2',  'Tasting №@1 from @2'), $tasting['id'], date('d.m.Y', $tasting['startts']))?></a>
<?php       else: ?>
    <?=formatReplace(langTranslate('tasting', 'tasting', 'Tasting №@1 from @2',  'Tasting №@1 from @2'), $tasting['id'], date('d.m.Y', $tasting['startts']))?>
<?php       endif; ?>
</td>
<?php
           foreach($available_expert_levels as $expert_level){ 
                if(isset($tasting['scores'][$expert_level])){
                    if($tasting['scores'][$expert_level]['count']>1){
                        if($user_id){
                            echo '<td class="score"><a href="'.BASE_URL.'/vintage/'.$vintage_id.'/reviewmerge/'.$expert_level.'/tasting/'.$tasting['id'].'/user/'.$user_id.'">'.$tasting['scores'][$expert_level]['score'].' ('.$tasting['scores'][$expert_level]['count'].')</a></td>';    
                        } else {
                            echo '<td class="score"><a href="'.BASE_URL.'/vintage/'.$vintage_id.'/reviewmerge/'.$expert_level.'/tasting/'.$tasting['id'].'">'.$tasting['scores'][$expert_level]['score'].' ('.$tasting['scores'][$expert_level]['count'].')</a></td>';    
                        }
                    } else {
                        echo '<td class="score"><a href="'.BASE_URL.'/vintage/'.$vintage_id.'/review/'.$tasting['scores'][$expert_level]['review_id'].'">'.$tasting['scores'][$expert_level]['score'].'</a></td>';
                    }
                } else {
                    echo '<td class="score"></td>';
                }
            } 
?>
</tr>
<?php   endforeach; ?>      
    </tbody>
</table>
<?php endif; 

    if(isset($vintage_review_details_list['users']) && count($vintage_review_details_list['users'])>0): 
        $found_expert_levels = array();
        foreach($vintage_review_details_list['users'] as $user){
            $expert_levels = array_keys($user['scores']);
            foreach($expert_levels as $expert_level){
                if(in_array($expert_level, $found_expert_levels)){
                    continue;
                }
                if(!in_array($expert_level, $expert_level_list_array_keys)){
                    continue;
                }
                $found_expert_levels[] = $expert_level;
            }
        }
        $available_expert_levels = array();
        foreach($expert_level_list as $expert_level=>$dummy){
            if(in_array($expert_level, $found_expert_levels)){
                $available_expert_levels[] = $expert_level;
            }
        }
        unset($found_expert_levels);
        
?>
<table class="subcontent scoredetails scoredetails-users">
    <thead>
<?php if(!$show_depth_urls): ?>
        <tr class="head-buttons"><th colspan="<?=count($available_expert_levels)+2?>"><a class="mainbtn back" href="<?=BASE_URL?>/vintage/<?=$vintage_id?>/scoredetails"><?=langTranslate('menu','navigation','Back','Back')?></a></th></tr>
<?php endif; ?>
        <tr><th class="name" rowspan="2"><?=langTranslate('user','user','Name','Name')?></th><th class="expert" rowspan="2"><?=langTranslate('user','user','Expert','Expert')?></th><th class="score" colspan="<?=count($available_expert_levels)?>"><?=langTranslate('product','vintage','Score','Score')?></th></tr>
        <tr>
<?php foreach($available_expert_levels as $expert_level): ?>
<th><?=isset($expert_level_list[$expert_level])?htmlentities($expert_level_list[$expert_level]):''?></th>
<?php endforeach; ?>
        </tr>
    </thead>
    <tfoot>
        <tr><th class="name" rowspan="2"><?=langTranslate('user','user','Name','Name')?></th><th class="expert" rowspan="2"><?=langTranslate('user','user','Expert','Expert')?></th>
<?php foreach($available_expert_levels as $expert_level): ?>
<th><?=isset($expert_level_list[$expert_level])?htmlentities($expert_level_list[$expert_level]):''?></th>
<?php endforeach; ?>
        </tr>
        <tr><th class="score" colspan="<?=count($available_expert_levels)?>"><?=langTranslate('product','vintage','Score','Score')?></th></tr>
    </tfoot>
    <tbody>
<?php   foreach($vintage_review_details_list['users'] as $user): ?>
<tr><td class="name">
<?php       if($show_depth_urls): ?>
    <a href="<?=BASE_URL?>/vintage/<?=$vintage_id?>/scoredetails/user/<?=$user['id']?>"><?=htmlentities($user['name'])?></a>
<?php       else: ?>
    <?=htmlentities($user['name'])?>
<?php       endif; ?>
</td><td class="expert"><?=htmlentities($user['expert_level'])?></td>
<?php       
            foreach($available_expert_levels as $expert_level){ 
                if(isset($user['scores'][$expert_level])){
                    if($user['scores'][$expert_level]['count']>1){
                        if($tasting_id){
                            echo '<td class="score"><a href="'.BASE_URL.'/vintage/'.$vintage_id.'/reviewmerge/'.$expert_level.'/tasting/'.$tasting_id.'/user/'.$user['id'].'">'.$user['scores'][$expert_level]['score'].' ('.$user['scores'][$expert_level]['count'].')</a></td>';    
                        } else {
                            echo '<td class="score"><a href="'.BASE_URL.'/vintage/'.$vintage_id.'/reviewmerge/'.$expert_level.'/user/'.$user['id'].'">'.$user['scores'][$expert_level]['score'].' ('.$user['scores'][$expert_level]['count'].')</a></td>';    
                        }
                    } else {
                        echo '<td class="score"><a href="'.BASE_URL.'/vintage/'.$vintage_id.'/review/'.$user['scores'][$expert_level]['review_id'].'">'.$user['scores'][$expert_level]['score'].'</a></td>';
                    }
                } else {
                    echo '<td class="score"></td>';
                }
            }
?>
</tr>
<?php   endforeach; ?>      
    </tbody>
</table>
<?php endif; 
?>