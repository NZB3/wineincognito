<?php 
    langSetDefault('user','request expert change');
?>
<div class="group-block">
<script type="string" class="confirm_string_decline_request"><?=langTranslate('Are you sure you want to decline this expert request?','Are you sure you want to decline this expert request?')?></script>
<script type="template" class="expert-request-expert-change-form-template">
<form><table class="subcontent fieldlist">
<thead>
    <tr class="header"><th colspan="2"><?=langTranslate('user','request expert change','Select expert level','Select expert level')?></th></tr>
</thead>
<tr><td class="label"><label for="dropbox-custom-expert-request-expert-change-form-expert-level"><?=langTranslate('user','viewUser','Expert', 'Expert');?></label></td><td class="user-expert-level"><script type="string" class="dropbox-template-empty-string"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?><\/script><div class="dropbox fresh" data-custom="1"><input type="checkbox" id="dropbox-custom-expert-request-expert-change-form-expert-level" /><label for="dropbox-custom-expert-request-expert-change-form-expert-level"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul>
<?php   foreach($expert_level_list as $expert_level=>$caption): ?>
<li class="item <?=$current_expert_level===$expert_level?'selected':''?>"><label><input type="checkbox" name="expertlevel" value="<?=$expert_level?>" <?=$current_expert_level===$expert_level?'checked="checked"':''?> /><span></span><?=htmlentities($caption)?></label></li>
<?php   endforeach; ?>
</ul></div></td></tr>
<tr><td class="submit" colspan="2"><input type="submit" value="<?=langTranslate('Save', 'Save');?>" /></td></tr>
</tbody></table></form>
</script>
<table class="subcontent expert-requests" data-user-id="<?=$user_id?>">
<thead>
    <tr class="header"><th colspan="4"><?=langTranslate('Expert requests',  'Expert requests')?></th></tr>
<?php if(!empty($requests)): ?>
    <tr><th></th><th class="comment"><?=langTranslate('Comment','Comment')?></th><th class="approve"></th><th class="decline"></th></tr>
<?php endif; ?>    
</thead>
<tbody>
<?php if(empty($requests)): ?>
    <tr class="noentries"><td colspan="4"><?=langTranslate('User doesn\'t have any requests', 'User doesn\'t have any requests');?></td></tr>
<?php else:
        $history_begun = false;
        $date_format = getDateFormat(true,false);
        foreach($requests as $request): 
            if(!$history_begun && !$request['active']):
                $history_begun = true;
?>
<tr><td colspan="4" class="header"><?=langTranslate('Request history','Request history')?></td></tr>
<?php       endif; ?>
<tr data-id="<?=$request['id']?>" class="<?=(!$request['active'])?'history '.($request['is_accepted']?'approved':'declined'):'active'?>"><td class="date"><?=date($date_format,$request['request_timestamp'])?></td><td class="comment"><?=prepareMultilineValue($request['comment'])?></td><td class="approve"><span></span></td><td class="decline"><span></span></td></tr>
<?php   endforeach;        
    endif; ?>    
</tbody>
</table>
</div>
<?php langClean('user','request expert change')?>
