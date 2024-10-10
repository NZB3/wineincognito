<?php 
if(!defined('IS_XMODULE')){
    exit();
}

require_once ABS_PATH.'/modules/Main/main.php';
function &get_instance(){
    static $XM;
    if(is_null($XM)){
        $XM = new \Main\Main();  
    }
    return $XM;
}
//module group var default
//group var default
//var default
function langTranslate(){
    static $lang;
    if(is_null($lang)){
        $lang = get_instance()->lang;
    }
    $args = func_get_args();
    $translation = call_user_func_array(array($lang, 'translate'), $args);
    if($translation===null){
        return $args[count($args)-1];//default
    }
    return $translation;
}
function langCurrId(){
    static $lang;
    if(is_null($lang)){
        $lang = get_instance()->lang;
    }
    return $lang->getCurrLangId();
}
function formatReplace(){
    $args = func_get_args();
    $count = count($args);
    if($count==0){
        return '';
    }
    $string = preg_replace('#@\d+#', '{{$0}}', $args[0]);
    for($i=1;$i<$count;$i++){
        $string = str_replace('{{@'.$i.'}}', $args[$i], $string);
    }
    $string = preg_replace('#\{\{@\d+\}\}#', '', $string);
    return $string;

}
function langSetDefault($module, $group = null){
    static $lang;
    if(is_null($lang)){
        $lang = get_instance()->lang;
    }
    $lang->setDefault($module, $group);
}

function langClean($module, $group = null){
    static $lang;
    if(is_null($lang)){
        $lang = get_instance()->lang;
    }
    $lang->clean($module, $group);
}
function redirect($url, $perm = false){
    if(!headers_sent()){
        header( 'Location: '.$url, true, $perm?301:302 );
        exit();
    }
    return false;
}
function getPostVal($postkey, $default){
    if(isset($_POST)){
        if(isset($_POST[$postkey])){
            return htmlentities((string)$_POST[$postkey]);
        }
        if(preg_match('#^([^\[]+)\[([^\]]+)\]$#',$postkey,$match)&&isset($_POST[$match[1]])&&is_array($_POST[$match[1]])&&isset($_POST[$match[1]][$match[2]])){
            return htmlentities((string)$_POST[$match[1]][$match[2]]);
        }
    }
    return htmlentities($default);
}
function getLangArrayVal($array, $key){
    if(!is_array($array)){
        return null;
    }
    return isset($array[$key])?trim((string)$array[$key]):null;
}
function getDateFormat($date=true,$time=true){
    $format = array();
    if($date){
        $format[] = 'd.m.Y';
    }
    if($time){
        $format[] = 'H:i';
    }
    return implode(' ', $format);
}
function prepareMultilineValue($text){
    return '<p class="multiline-value">'.preg_replace("#\n+#", '</p><p>', htmlentities($text)).'</p>';
}
function formatPrice($price,$currency = null){
    $price = (int)$price;
    if($price==0){
        return langTranslate('main','price','Free','Free');
    }
    if($currency===null){
        $currency = langTranslate('main','price','RUB','RUB');
    }
    return number_format($price,0,'.',' ').' '.$currency;
}
function prettifyMinutes($durationminutes,$short = false){
    $durationminutes = (int)$durationminutes;
    $result = '';
    $days = floor($durationminutes/1440);
    if($days>0){
        $result = $days.' '.langTranslate('main','time','d.','d.');
        if($short){
            return $result;
        }
    }
    $durationminutes = $durationminutes%1440;
    $hours = floor($durationminutes/60);
    if($hours>0){
        $result .= ' '.$hours.' '.langTranslate('main','time','h.','h.');
        if($short){
            return trim($result);
        }
    }
    $minutes = $durationminutes%60;
    if($minutes>0){
        $result .= ' '.$minutes.' '.langTranslate('main','time','m.','m.');
    }
    return trim($result);
}