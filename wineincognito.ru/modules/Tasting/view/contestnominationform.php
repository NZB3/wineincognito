<?php
    langSetDefault('tasting', 'contest');
?>
<form class="edit-contest-nomination-form">
<table class="subcontent fieldlist">
<thead>
    <tr class="header"><th colspan="2"><?=langTranslate('Nomination',  'Nomination')?></th></tr>
</thead>
<tbody>
    <tr><td class="label"><label for="edit-contest-nomination-form-name"><?=langTranslate('Nomination: Name','Name')?></label></td><td><input type="text" id="edit-contest-nomination-form-name" name="name" value="<?=getPostVal('name',$nomination_info['name'])?>" /></td></tr>
    <tr><td class="submit" colspan="2"><input type="submit" value="<?=langTranslate('Save', 'Save');?>" /></td></tr>
</tbody></table>
</form>
<?php langClean('tasting', 'contest')?>