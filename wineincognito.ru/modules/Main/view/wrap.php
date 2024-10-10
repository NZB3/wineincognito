<!doctype html>
<html><head>
    <title><?=$title?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php foreach($includecss as $filename): ?>
    <link rel="stylesheet" type="text/css" href="<?=BASE_URL.$filename?>?v=2" />
<?php endforeach; ?>
<?php foreach($includejs as $filename): ?>
    <script src="<?=BASE_URL.$filename?>?v=2"></script>
<?php endforeach; ?>
<?php if($pushStateUrl):?>
    <script language="JavaScript">
        if(typeof(history.pushState)!=='undefined' && history.pushState instanceof Function){
            history.pushState({}, "<?=str_replace('"', '\x22', $title)?>", "<?=str_replace('"', '\x22', $pushStateUrl)?>");    
        }
    </script>
<?php endif; ?>
</head><body>
<div id="searchbar" <?=$menu?'class="menu-exists"':''?>>
    <table><tbody>
        <tr><td class="menu-visibility-toggle"><span class="show-menu-toggle"></span></td><td class="searchbar">
    <?php if($showsearchbar): ?>
            <form action="<?=BASE_URL?>/products/rated" method="POST"><input type="text" name="vintage_search_text" value="<?=getPostVal('vintage_search_text','')?>" /></form>
    <?php endif; ?>
        </td><td class="submenu"><?=$submenu?></td><td class="language-selector" data-cur-lang="<?=$curLangId?>">
        <?php if(!empty($language_selector)):   
                foreach($language_selector as $language_url): 
                    $language_url['name'] = mb_substr($language_url['name'],0,3,'UTF-8');
                    if(!$language_url['current']):
                ?><a href="<?=BASE_URL.$language_url['url']?>"><?=htmlentities($language_url['name'])?></a><?php
                    else: 
                    ?><span><?=htmlentities($language_url['name'])?></span><?php
                    endif;
                endforeach;
           endif; ?>
    </td></tr>
    </tbody></table>
</div>    
<?php if($menu): ?>
<div id="menu-background"></div>
<div id="menu"><?=$menu?></div>
<?php endif; ?>
<div id="content">
    <div id="infoBlock-group"><?php
    if(is_array($messagelog)){
        foreach($messagelog as $message){
            switch($message[1]){
                case 0:
                    $class = 'error';
                    break;
                case 1:
                    $class = 'warning';
                    break;
                case 2:
                    $class = 'success';
                    break;
                default:
                    continue 2;
            }
            echo '<div class="infoBlock '.$class.'">'.$message[0].'</div>';
        }
    }
?></div><div id="infoBlock-group-filler"></div><?=$content?></div>
<div id="confirmBox"><div class="dialog"><div class="text"></div><span class="buttons"><button class="ok"><?=langTranslate('main','confirmBox','OK','OK')?></button><button class="cancel"><?=langTranslate('main','confirmBox','Cancel','Cancel')?></button></div></div></div>
<?php if(preg_match('#wineincognito\.(?:ru|com)#', $_SERVER['SERVER_NAME'])): ?>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
   ym(53922391, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/53922391" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<?php endif; ?>
</body></html>