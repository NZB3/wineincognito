<?php langSetDefault('product', 'attrval'); ?>
<table class="subcontent attrvalspellinglist compactable compact" data-id="<?=$attrval_id?>">
    <thead>
        <tr class="head-buttons"><th colspan="3">
			<script type="template" class="spelling-template-item"><tr class="editing"><td class="spelling"><input type="text" value="" /></td><td class="edit"><span class="edit"></span><span class="save"></span></td><td class="delete"><span></span></td></tr></script>
            <span class="add"></span>
        </th></tr>
        <tr class="header"><th colspan="3"><?=langTranslate('Alternate spellings','Alternate spellings')?></th></tr>
    </thead>
    <tbody>
<?php foreach($list as $spelling_id=>$spelling_text): ?>
        <tr data-id="<?=$spelling_id?>"><td class="spelling"><input type="text" disabled="disabled" value="<?=htmlentities($spelling_text)?>" /></td><td class="edit"><span class="edit"></span><span class="save"></span></td><td class="delete"><span></span></td></tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php langClean('product', 'attrval')?>
