<?php 
    langSetDefault('tasting', 'tasting'); 
    $showstatus = true;
    $showpendingreviewcount = false;
    $showprice = true;
    $showattendanceresponse = true;

    $autosearch = true;
    $default_status = array(0,1,2);
    $only_owned_default = false;
    if(!isset($currently_participating_default)){
        $currently_participating_default = false;
    }
    if(!isset($pendingreview)){
        $pendingreview = false;
    }
    if($pendingreview){
        $autosearch = true;
        $default_status = array();
        $only_owned_default = false;

        $showstatus = false;
        $showpendingreviewcount = true;
        $showprice = false;
    }
    $colcount = 2 + ($showstatus?1:0) + ($showattendanceresponse?1:0) + ($showpendingreviewcount?1:0) + ($showprice?1:0);
?>
<div class="subcontent filter-block tasting-filter">
<div class="tutorial-block"><?=langTranslate('Ongoing tastings that you\'re possibly participating in','Ongoing tastings that you\'re possibly participating in')?></div>
<table class="subcontent datalist">
    <thead>
        <tr class="head-buttons"><th colspan="<?=$colcount?>">
<?php if(isset($can_add) && $can_add): ?>
            <a class="add" href="<?=BASE_URL?>/tasting/add"></a>
<?php endif; ?>
        </th></tr>
        <tr class="pagination"><th colspan="<?=$colcount?>"><ul class="pagination"></ul></th></tr>
        <tr><th class="name"><?=langTranslate('Name','Name')?></th>
<?php if($showstatus): ?>
            <th class="status"><?=langTranslate('Status','Status')?></th>
<?php endif; ?>
<?php if($showattendanceresponse): ?>
            <th class="attendance-response"></th>
<?php endif; ?>
<?php if($showprice): ?>
            <th class="price"><?=langTranslate('Price','Price')?></th>
<?php endif; ?>
<?php if($showpendingreviewcount): ?>
            <th class="pending-review-count"></th>
<?php endif; ?>
            <th class="info"></th>
        </tr>
    </thead>
    <tfoot>
        <tr><th class="name"><?=langTranslate('Name','Name')?></th>
<?php if($showstatus): ?>
            <th class="status"><?=langTranslate('Status','Status')?></th>
<?php endif; ?>
<?php if($showattendanceresponse): ?>
            <th class="attendance-response"></th>
<?php endif; ?>
<?php if($showprice): ?>
            <th class="price"><?=langTranslate('Price','Price')?></th>
<?php endif; ?>
<?php if($showpendingreviewcount): ?>
            <th class="pending-review-count"></th>
<?php endif; ?>
            <th class="info"></th>
        </tr>
        <tr class="pagination"><th colspan="<?=$colcount?>"><ul class="pagination"></ul></th></tr>
    </tfoot>
    <tbody>
<?php if(empty($tastinglist)): ?>
        <tr class="noentries"><td colspan="<?=$colcount?>"><?=langTranslate('Sorry, no matches found', 'Sorry, no matches found');?></td></tr>
<?php else: 
        foreach($tastinglist as $tasting): ?>
<tr class="item"><td class="name">
    <a href="<?=$pendingreview?($tasting['ranking_scoring']?BASE_URL.'/myreview/pending/tasting/'.$tasting['id'].'/ranking':BASE_URL.'/myreview/pending/tasting/'.$tasting['id'].'/products'):BASE_URL.'/tasting/'.$tasting['id']?>"><?=htmlentities($tasting['name'])?></a>
<?php if(strlen($tasting['location'])): ?>
    <span class="location"><?=htmlentities($tasting['location'])?></span>
<?php endif; ?>
</td>
<?php if($showstatus): ?>
<td class="status"><?=htmlentities($tasting['status'])?></td>
<?php endif; ?>
<?php if($showattendanceresponse): ?>
<td class="attendance-response <?=htmlentities($tasting['attendance_response_status'])?>"><span <?=strlen($tasting['attendance_response_status_text'])?'data-tooltip="'.htmlentities($tasting['attendance_response_status_text']).'"':''?>></span></td>
<?php endif; ?>
<?php if($showprice): ?>
<td class="price"><?=htmlentities($tasting['personal_price'])?></th>
<?php endif; ?>
<?php if($showpendingreviewcount): ?>
<td class="pending-review-count"><?=htmlentities($tasting['pending_review_count'])?></td>
<?php endif; ?>
<td class="info"><a href="<?=BASE_URL?>/tasting/<?=$tasting['id']?>"></td>
</tr>
<?php   endforeach; 
    endif; ?>
    </tbody>
</table>
</div>
<?php langClean('tasting', 'tasting')?>