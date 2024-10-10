<?php 
    langSetDefault('User', 'viewCompany');
?>
<table class="subcontent viewCompany">
<thead>
    <tr class="head-buttons"><th colspan="2">
        <?php if($companyinfo['can_approve']): ?>
        <span class="approve" data-id="<?=$companyinfo['id']?>"></span>
        <?php endif; ?>
        <?php if($companyinfo['can_delete']): ?>
        <script type="string" id="confirm_string_delete_company"><?=formatReplace(langTranslate('user','deletecompany','Are you sure you want to delete @1?','Are you sure you want to delete @1?'),htmlentities($companyinfo['name']))?></script>
        <span class="delete" data-id="<?=$companyinfo['id']?>"></span>
        <?php endif; ?>
        <?php if($companyinfo['can_edit']): ?>
        <a class="edit" href="<?=BASE_URL?>/company/<?=$companyinfo['id']?>/edit"></a>
        <?php endif; ?>
        <?php if($companyinfo['can_join']): ?>
        <script type="string" id="confirm_string_request_join"><?=formatReplace(langTranslate('user','requestjoin','Are you sure you want to join @1?','Are you sure you want to join @1?'),htmlentities($companyinfo['name']))?></script>
        <span class="join" data-id="<?=$companyinfo['id']?>"></span>
        <?php endif; ?>
    </th></tr>
</thead>
<tbody>
<tr><td class="label"><label><?=langTranslate('Name', 'Name');?></label></td><td><?=htmlentities($companyinfo['name'])?></td></tr>
<tr><td class="label"><label><?=langTranslate('ITN', 'ITN');?></label></td><td><?=htmlentities($companyinfo['itn'])?></td></tr>
<tr><td colspan="2" class="header"><?=langTranslate('Contact person', 'Contact person');?></td></tr>
<tr><td class="label"><label><?=langTranslate('Contact person name', 'Name');?></label></td><td><a href="<?=BASE_URL?>/user/<?=$companyinfo['owner_id']?>"><?=htmlentities($companyinfo['owner_name'])?></a></td></tr>
<tr><td class="label"><label><?=langTranslate('Contact person e-mail', 'E-mail');?></label></td><td><?=htmlentities($companyinfo['owner_email'])?></td></tr>
</tbody></table>
<?php langClean('User', 'viewCompany')?>
