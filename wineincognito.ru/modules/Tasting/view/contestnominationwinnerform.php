<?php
    langSetDefault('tasting', 'contest');
?>
<form class="edit-contest-nomination-winner-form"><input type="hidden" name="vid" value="<?=$vintage_id?>" />
<table class="subcontent fieldlist">
<tbody>
    <tr><td class="label"><label for="edit-contest-nomination-winner-form-place"><?=langTranslate('Nomination Winner: Place','Place')?></label></td><td><input type="text" id="edit-contest-nomination-winner-form-place" name="place" value="<?=getPostVal('place',$place)?>" /></td></tr>
    <tr><td class="submit" colspan="2"><input type="submit" value="<?=langTranslate('Save', 'Save');?>" /></td></tr>
</tbody></table>
</form>
<?php langClean('tasting', 'contest')?>