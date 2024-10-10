<?php 
    langSetDefault('tasting', 'tasting');
?>
<form class="subcontent">
<script type="string" class="dropbox-template-empty-string"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></script>
<table class="subcontent viewTasting-user-attendance-response" data-t-id="<?=$tasting_id?>">
<thead>
    <tr class="header"><th colspan="2"><?=langTranslate('You\'ve been invited to the tasting','You\'ve been invited to the tasting')?></th></tr>
</thead>
<tbody>
    <tr><td class="label"><label for="dropbox-custom-user-attendance-response"><?=langTranslate('Your Response', 'Your Response');?></label></td><td><div class="dropbox fresh" data-custom="1"><input type="checkbox" id="dropbox-custom-user-attendance-response" /><label for="dropbox-custom-user-attendance-response"><?=langTranslate('main','dropbox','Click to select', 'Click to select');?></label><ul>
    <?php foreach($user_response_list as $response=>$caption): ?>
        <li class="item <?=$current_user_attendance_response===$response?'selected':''?>"><label><input type="checkbox" value="<?=$response?>" <?=$current_user_attendance_response===$response?'checked="checked"':''?> /><span></span><?=htmlentities($caption)?></label></li>
    <?php       endforeach; ?>
    </ul></div></td></tr>
</tbody>
</table>
</form>
<?php langClean('tasting', 'tasting')?>
