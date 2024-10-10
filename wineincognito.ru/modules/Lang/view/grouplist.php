<?php langSetDefault('Lang', 'list'); ?>
<table class="subcontent grouplist">
    <thead>
        <tr class="head-buttons"><th><a class="mainbtn back" href="<?=BASE_URL?>/translation/interface/modules"><?=langTranslate('menu','navigation','Back','Back')?></a></th></tr>
        <tr><th><?=langTranslate('Name','Name')?></th></tr>
    </thead>
    <tfoot>
        <tr><th><?=langTranslate('Name','Name')?></th></tr>
    </tfoot>
    <tbody>
<?php foreach($grouplist as $group): ?>
        <tr><td class="name"><a href="<?=BASE_URL?>/translation/interface/module/<?=$module_id?>/group/<?=$group['id']?>"><?=htmlentities($group['name'])?></a></td></tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php langClean('Lang', 'list')?>
