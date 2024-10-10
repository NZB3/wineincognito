<?php 
    langSetDefault('User', 'joinCompany');
?>
<div class="subcontent joinCompany">
    <span class="invite"><?=formatReplace(langTranslate('You\'ve been invited to company @1',  'You\'ve been invited to company @1'),
                '<a href="'.BASE_URL.'/company/'.$companyinfo['id'].'" target="_blank">'.htmlentities($companyinfo['name']).'</a>')?></span>
    <form method="POST"><input type="hidden" name="action" value="joincompany" /><input type="hidden" name="join" value="1" /><input type="submit" value="<?=langTranslate('Join', 'Join')?>" /></form>
</div>
<?php langClean('User', 'joinCompany')?>
