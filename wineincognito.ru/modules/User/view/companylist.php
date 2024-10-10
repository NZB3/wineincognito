<?php langSetDefault('User', 'companylist'); 
if(!isset($can_set_api_access)){
    $can_set_api_access = false;
}
?>
<script type="string" id="confirm_string_request_join"><?=formatReplace(langTranslate('user','requestjoin','Are you sure you want to join @1?','Are you sure you want to join @1?'),'{{name}}')?></script>
<script type="string" id="confirm_string_delete_company"><?=formatReplace(langTranslate('user','deletecompany','Are you sure you want to delete @1?','Are you sure you want to delete @1?'),'{{name}}')?></script>

<table class="companylist">
    <thead>
        <tr><th><?=langTranslate('Name','Name')?></th><th><?=langTranslate('ITN','ITN')?></th><th class="separator"></th><th class="approve"></th><th class="api-access"></th><th class="delete"></th><th class="edit"></th><th class="join"></th></tr>
    </thead>
    <tfoot>
        <tr><th><?=langTranslate('Name','Name')?></th><th><?=langTranslate('ITN','ITN')?></th><th class="separator"></th><th class="approve"></th><th class="api-access"></th><th class="delete"></th><th class="edit"></th><th class="join"></th></tr>
    </tfoot>
    <tbody>
<?php foreach($companylist as $company): ?>
        <tr data-id="<?=$company['id']?>"><td class="name"><a href="<?=BASE_URL?>/company/<?=$company['id']?>"><?=htmlentities($company['name'])?></a></td><td><?=htmlentities($company['itn'])?></td><td></td><td class="approve">
<?php   if($company['can_approve']): ?>
            <span></span>
<?php   endif; ?>
        </td><td class="api-access <?=$company['can_use_api']?'has-access':''?>">
<?php   if($can_set_api_access): ?>
            <span></span>
<?php   endif; ?>
        </td><td class="delete">
<?php   if($company['can_delete']): ?>
            <span></span>
<?php   endif; ?>
        </td><td class="edit">
<?php   if($company['can_edit']): ?>
            <a href="<?=BASE_URL?>/company/<?=$company['id']?>/edit"></a>
<?php   endif; ?>
        </td><td class="join">
<?php   if($company['can_join']): ?>
            <span></span>
<?php   endif; ?>
        </td></tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php langClean('User', 'companylist')?>
