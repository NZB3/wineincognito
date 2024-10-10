<input type="checkbox" id="menu-structure-low-width-menu-show" /><label for="menu-structure-low-width-menu-show"><?=langTranslate('menu','navigation','Show menu','Show menu')?></label>
<ul id="menu-structure">
<?php 
$oldDepth = 0;
$firstitem = true;
foreach($menustructure as $menuitem){
    if($oldDepth<$menuitem['depth']){
        echo '<ul>';
        $firstitem = true;
    }
    if($oldDepth>$menuitem['depth']){
        echo str_repeat('</ul></li>', $oldDepth - $menuitem['depth']);
        $firstitem = true;
    }
    if(!$firstitem){
        echo '</li>';
    }
    $oldDepth = $menuitem['depth'];
    echo '<li class="'.($menuitem['act']?'visible':'hidden').'" data-id="'.$menuitem['id'].'"><span class="line"><a href="'.BASE_URL.$menuitem['url'].'">'.$menuitem['lbl'].'</a><span class="visibility-toggle"></span></span>';
}
;?>
</ul>