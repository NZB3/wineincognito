<?php 
    langSetDefault('tasting', 'tasting');
?>
<?php if(isset($invited)&&$invited): ?>
<p style="text-align:center; font-size:16pt; color:#4d4d4d;"><?=formatReplace(langTranslate('tasting','mail','You\'ve been invited to the @1tasting@2','You\'ve been invited to the @1tasting@2'),'<a style="color:#f46c31;" href="'.BASE_URL.'/tasting/'.$tastinginfo['id'].'">','</a>')?></p>
<?php   if(isset($user_response_list)&&!empty($user_response_list)&&isset($tu_id)&&isset($usercode)): ?>
<table width="740" cellpadding="5" cellspacing="0" border="0" bgcolor="#ffffff" class="pad_null">
    <tbody>
        <tr><td style="text-align:right; font-size:16pt; font-weight:normal; color:#4d4d4d; vertical-align: top;"><?=langTranslate('Your Response', 'Your Response');?></td><td style="text-align:left; font-size:16pt; font-weight:normal; vertical-align: top;"><ul style="list-style-type: none;">
<?php       foreach($user_response_list as $response_id=>$response_caption): ?>
            <li><a style="color:#f46c31;" href="<?=BASE_URL?>/tasting/<?=$tastinginfo['id']?>/user/<?=$tu_id?>/code/<?=$usercode?>/respond/<?=$response_id?>"><?=htmlentities($response_caption)?></a></li>
<?php       endforeach; ?>
        </ul></td></tr>
    </tbody>
</table>
<?php   endif; ?>
<?php endif; ?>
<?php if(isset($cancelled)&&$cancelled): ?>
<p style="text-align:center; font-size:16pt; color:#4d4d4d;"><?=langTranslate('tasting','mail','Tasting you\'ve been invited to has been cancelled','Tasting you\'ve been invited to has been cancelled')?></p>
<?php endif; ?>
<?php if(isset($invite_revoked)&&$invite_revoked): ?>
<p style="text-align:center; font-size:16pt; color:#4d4d4d;"><?=langTranslate('tasting','mail','Your invite to the tasting has been revoked','Your invite to the tasting has been revoked')?></p>
<?php endif; ?>
<?php if(isset($review_request)&&$review_request): ?>
<p style="text-align:center; font-size:16pt; color:#4d4d4d;"><?=langTranslate('tasting','mail','Requesting reviews for tasting you\'ve participated in','Requesting reviews for tasting you\'ve participated in')?></p>
<p style="text-align:center; font-size:16pt;"><a style="color:#f46c31;" href="<?=BASE_URL?>/myreview/pending/tasting/<?=$tastinginfo['id']?>/products"><?=langTranslate('tasting','mail','File reviews','File reviews')?></a></p>
<?php endif; ?>
<table width="740" cellpadding="5" cellspacing="0" border="0" bgcolor="#ffffff" class="pad_null">
    <thead>
        <tr><th colspan="2" style="font-size:16pt; text-align:center; color:#4d4d4d;">
<?php if(isset($invited)&&$invited):?>
            <a style="color:#f46c31;" href="<?=BASE_URL?>/tasting/<?=$tastinginfo['id']?>"><?=formatReplace(langTranslate('Tasting №@1 from @2',  'Tasting №@1 from @2'), $tastinginfo['id'], date('d.m.Y', $tastinginfo['startts']))?></a>
<?php else: ?>
            <?=formatReplace(langTranslate('Tasting №@1 from @2',  'Tasting №@1 from @2'), $tastinginfo['id'], date('d.m.Y', $tastinginfo['startts']))?>
<?php endif; ?>            
        </th></tr>
    </thead>
    <tbody>
        <tr><td style="text-align:right; font-weight:bold; vertical-align: top; color:#4d4d4d;"><?=langTranslate('Location', 'Location');?></td><td style="text-align:left; white-space: nowrap; vertical-align: top; color:#4d4d4d;"><?=htmlentities($tastinginfo['location'])?></td></tr>
        <tr><td style="text-align:right; font-weight:bold; vertical-align: top; color:#4d4d4d;"><?=langTranslate('Start','Start')?></td><td style="text-align:left; white-space: nowrap; vertical-align: top; color:#4d4d4d;"><?=date('d.m.Y H:i',$tastinginfo['startts'])?></td></tr>
        <tr><td style="text-align:right; font-weight:bold; vertical-align: top; color:#4d4d4d;"><?=langTranslate('End','End')?></td><td style="text-align:left; white-space: nowrap; vertical-align: top; color:#4d4d4d;"><?=date('d.m.Y H:i',$tastinginfo['endts'])?></td></tr>
        <tr><td style="text-align:right; font-weight:bold; vertical-align: top; color:#4d4d4d;"><?=langTranslate('Duration','Duration')?></td><td style="text-align:left; white-space: nowrap; vertical-align: top; color:#4d4d4d;"><?php
            $durationseconds = $tastinginfo['endts'] - $tastinginfo['startts'];
            $hours = floor($durationseconds/3600);
            if($hours>0){
                echo $hours.' '.langTranslate('h.','h.');
            }
            $minutes = ceil(($durationseconds%3600)/60);
            if($minutes>0){
                echo ' '.$minutes.' '.langTranslate('m.','m.');
            }

        ?></td></tr>
        <tr><td style="text-align:right; font-weight:bold; vertical-align: top; color:#4d4d4d;"><?=langTranslate('Description','Description')?></td><td style="text-align:left; vertical-align: top; color:#4d4d4d;"><?=prepareMultilineValue($tastinginfo['desc'])?></td></tr>
    </tbody>
</table>

<table width="740" cellpadding="5" cellspacing="0" border="1" bgcolor="#ffffff" class="pad_null" style="margin-top: 20px;">
<thead>
    <tr><th colspan="3" style="font-size:16pt; text-align:center; color:#4d4d4d;"><?=langTranslate('Product List',  'Product List')?></th></tr>
    <tr><th></th><th style="width:100%; font-size:14pt; color:#4d4d4d;"><?=langTranslate('product','vintage','Name','Name')?></th><th style="font-size:14pt; color:#4d4d4d;"><?=langTranslate('product','vintage','Volume','Volume')?></th></tr>
</thead>
<tbody>
<?php foreach($tasting_vintage_list as $vintage): ?>
    <tr><td style="text-align:right; white-space: nowrap; vertical-align: top; color:#4d4d4d;"><?=$vintage['index']?></td><td style="text-align:left; white-space: nowrap; vertical-align: top; color:#4d4d4d;"><?=($vintage['id']&&!$vintage['blind'])?'<a style="color:#f46c31;" href="'.BASE_URL.'/vintage/'.$vintage['id'].'" target="_blank">':''?><?=htmlentities($vintage['fullname'])?><?=($vintage['id']&&!$vintage['blind'])?'</a>':''?><?=$vintage['isprimeur']?'<p style="font-size:80%;font-style:italic;">'.langTranslate('tasting','vintage','En primeur', 'En primeur').'</p>':''?></td><td style="text-align:center; white-space: nowrap; vertical-align: top; color:#4d4d4d;"><?=htmlentities($vintage['volume'])?></td></tr>
<?php   if(strlen($vintage['desc'])): ?>  
    <tr><td style="text-align:left; vertical-align: top; color:#4d4d4d;" colspan="3"><?=prepareMultilineValue($vintage['desc'])?></td></tr>
<?php   endif; ?>
<?php endforeach; ?>
</tbody>
</table>
<?php langClean('tasting', 'tasting')?>
