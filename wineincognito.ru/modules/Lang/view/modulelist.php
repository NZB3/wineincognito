<?php langSetDefault('Lang', 'list'); ?>
<table class="subcontent modulelist">
    <thead>
        <tr><th><?=langTranslate('Name','Name')?></th></tr>
    </thead>
    <tfoot>
        <tr><th><?=langTranslate('Name','Name')?></th></tr>
    </tfoot>
    <tbody>
<?php foreach($modulelist as $module): ?>
        <tr><td class="name"><a href="<?=BASE_URL?>/translation/interface/module/<?=$module['id']?>/groups"><?=htmlentities($module['name'])?></a></td></tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php langClean('Lang', 'list')?>
