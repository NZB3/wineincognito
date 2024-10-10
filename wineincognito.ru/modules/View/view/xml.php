<?php header("Content-Type: text/xml"); 
if(!function_exists('viewRecursiveXmlBuilding')){
    function viewRecursiveXmlBuilding($root, $content){
        if(!preg_match_all('#^[a-zA-Z]#', $root)){
            return '';
        }
        if(is_array($content)&&empty($content)){
            return '<'.$root.' />';
        }
        if(!is_array($content)){
            if(!strlen($content)){
                return '<'.$root.' />';
            }
            return '<'.$root.'>'.str_replace(array('&','<'), array('&amp;','&lt;'), $content).'</'.$root.'>';
        }
        $keys = array_keys($content);
        $sequential = true;
        foreach($keys as $key){
            if($key !== intval($key)){
                $sequential = false;
                break;
            }
        }
        $result = '';
        foreach($content as $key=>$value){
            if($sequential){
                $result .= viewRecursiveXmlBuilding($root, $value);
            } else {
                $result .= viewRecursiveXmlBuilding($key, $value);
            }
        }
        if($sequential){
            return $result;
        }
        if(!$result){
            return '<'.$root.' />';
        }
        return '<'.$root.'>'.$result.'</'.$root.'>';
    }
}
echo '<?xml version="1.0" encoding="UTF-8"?>'.viewRecursiveXmlBuilding($root,$content);