<?php langSetDefault('Lang', 'list'); ?>
<table class="subcontent translationlist">
    <thead>
        <tr class="head-buttons"><th colspan="<?=count($languageList)+1?>"><a class="mainbtn back" href="<?=BASE_URL?>/translation/interface/module/<?=$module_id?>/groups"><?=langTranslate('menu','navigation','Back','Back')?></a></th></tr>
        <tr><th rowspan="2"><?=langTranslate('Variable','Variable')?></th><th colspan="<?=count($languageList)?>"><?=langTranslate('Translation','Translation')?></th></tr>
        <tr>
<?php foreach($languageList as $language):?>
            <th><?=htmlentities($language['name'])?></th>
<?php endforeach; ?>
        </tr>
    </thead>
    <tfoot>
        <tr><th><?=langTranslate('Variable','Variable')?></th>
<?php foreach($languageList as $language):?>
            <th><?=htmlentities($language['name'])?></th>
<?php endforeach; ?>
        </tr>
    </tfoot>
    <tbody>
<?php foreach($stringlist as $string): ?>
        <tr data-id="<?=$string['id']?>"><td><?=htmlentities($string['string'])?></td>
<?php foreach($languageList as $language):?>
            <td data-lang-id="<?=$language['id']?>"><span class="hiddenmeasure"></span><input type="text" disabled="disabled" value="<?=isset($string['translation'][$language['id']])?htmlentities($string['translation'][$language['id']]):''?>" /><span class="edit"></span><span class="save"></span><span class="saving"></span></td>
<?php endforeach; ?>
        </tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php langClean('Lang', 'list')?>
