<form>
    <div class="subcontent tasting-vintage-preparation">
        <ul>
<?php foreach($tasting_vintage_preparation_list as $tasting_vintage_preparation_type=>$tasting_vintage_preparation_label): ?>
<li><input type="radio" name="preparation-type" value="<?=$tasting_vintage_preparation_type?>" class="tasting-vintage-preparation-type" id="tasting-vintage-preparation-type-<?=$tasting_vintage_preparation_type?>" <?=($tasting_vintage_preparation_type==$tasting_vintage_preparation_data['preparation_type'])?'checked=checked':''?> /><label for="tasting-vintage-preparation-type-<?=$tasting_vintage_preparation_type?>"><?=htmlentities($tasting_vintage_preparation_label)?></label></li>
<?php endforeach; ?>
        </ul>
        <label for="tasting-vintage-preparation-time-elapsed" class="tasting-vintage-preparation-time-elapsed"><?=langTranslate('tasting','preparation','Started (minutes ago)','Started (minutes ago)')?></label>
        <input type="text" name="preparation-time" id="tasting-vintage-preparation-time-elapsed" class="tasting-vintage-preparation-time-elapsed" value="<?=htmlentities($tasting_vintage_preparation_data['preparation_time'])?>" />
        <input type="submit" value="<?=langTranslate('tasting','tasting','Save','Save')?>" />
    </div>
    <script language="JavaScript">
    $("div.tasting-vintage-preparation").closest("form").find("#tasting-vintage-preparation-time-elapsed").mask("999");
    $("div.tasting-vintage-preparation").closest("form").find("input.tasting-vintage-preparation-type").first().trigger("change");
    </script>
</form>