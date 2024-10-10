<?php langSetDefault('product', 'vintage'); ?>
<table class="subcontent vintage-translations">
<thead>
    <tr><th class="desc"><?=langTranslate('Description','Description')?></th><th class="approve"></th><th class="deny"></th></tr>
</thead>
<tbody>
<?php if(empty($translations)): ?>
    <tr class="noentries"><td colspan="3"><?=langTranslate('No promoted translations found', 'No promoted translations found');?></td></tr>
<?php else: ?>
<?php   if(strlen($vintageinfo['desc'])): ?>
    <tr class="current"><td class="desc"><?=prepareMultilineValue($vintageinfo['desc'])?></td><td class="approve"></td><td class="deny"></td></tr>
<?php   endif; ?>
<?php   foreach($translations as $translation): ?>
    <tr data-id="<?=$translation['id']?>"><td class="desc"><?=prepareMultilineValue($translation['desc'])?></td><td class="approve"><span></span></td><td class="deny"><span></span></td></tr>
<?php   endforeach;
      endif; ?>
</tbody>
</table>
<?php langClean('product', 'vintage')?>
