<?php 
    if(!empty($contents)): 
?>
<div class="tab-content group-block"><ul class="tab-content-tabs"><?php
        $tabcount = 0;
        $firstgroup = null;
        $tabshtml = '';
        foreach($contents as $id=>$tabcontent){
            $tabshtml .= '<li data-tab-content-id="'.$id.'" '.($id==0?'class="tab-content-selected"':'').'>'.$tabcontent['header'].'</li>';
            $tabcount++;
            if($tabcount > 1 && ($id==count($contents)-3 || $tabcount == 3) || $id==count($contents)-1){
                if(!$firstgroup){
                    $firstgroup = '<li><ul class="tab-content-group tab-content-group-for-'.$tabcount.' ">'.$tabshtml.'</ul></li>';
                } else {
                    echo '<li><ul class="tab-content-group tab-content-group-for-'.$tabcount.' ">'.$tabshtml.'</ul></li>';
                }
                $tabcount = 0;
                $tabshtml = '';
            }
        }
        if($firstgroup){
            echo $firstgroup;
        }
?></ul></ul><?php
        foreach($contents as $id=>$tabcontent):
?><div data-tab-content-id="<?=$id?>" class="tab-content-content group-block <?=$id==0?'tab-content-selected':''?>"><?=$tabcontent['content']?></div><?php 
        endforeach; 
?></div>
<?php endif; ?>