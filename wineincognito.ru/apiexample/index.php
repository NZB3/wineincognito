<?php

include('../const.php');
DEFINE('API_URL',BASE_URL.'/api');
DEFINE('TIMEOUT',300);
DEFINE('LOGIN','ca2');
DEFINE('PASS','123');
DEFINE('AUTH_BASIC_STRING',base64_encode(LOGIN.':'.PASS));

function getPage($method,$params){
    $ch = curl_init();
    $header = array();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, TIMEOUT);
    curl_setopt($ch, CURLOPT_URL, API_URL);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    $post = array();
    $post['action'] = trim($method);
    if(is_array($params)&&!empty($params)){
        $post['payload'] = $params;    
    }
    $post = json_encode($post);
    $header[] = "Content-Type:application/json";
    $header[] = "Content-Length:".strlen($post);
    curl_setopt($ch, CURLOPT_POST, true);
    @curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $header[] = "Cache-Control:max-age=0";
    $header[] = 'Authorization: Basic '.AUTH_BASIC_STRING;
    $header[] = "X-Requested-With:XMLHttpRequest";
    $header[] = "Connection:keep-alive";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    $contentPage = curl_exec($ch);
    if ($contentPage === false) {//Ошибка обнаружена curl
        $code = curl_errno($ch);
        curl_close($ch);
        return false;
    }
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($http_code != 200){
        var_dump($http_code);
        return false;
    }
    $result = @json_decode($contentPage,true);
    unset($contentPage);
    if(empty($result)){
        return false;
    }
    if(isset($result['success'])&&$result['success']==1){
        if(isset($result['payload'])&&!empty($result['payload'])){
            return $result['payload'];
        }
        return true;
    }
    if(isset($result['error'])&&$result['error']==1){
        var_dump('ERROR: '.$result['errmsg']);
    }
    return false;
}
echo 'getVolumeList:<br />';
if($content = getPage('getVolumeList',null)){
    var_dump($content);
}
echo '<br /><br />';
echo 'setProductPrice:<br />';
if($content = getPage('setProductPrice',array(
        array(
                'id'=>6957,
                'volume'=>1913,
                'price'=>600,
                'url'=>'https://www.google.com/',
            ),//correct
        array(
                'id'=>6957,
                'year'=>'NV',
                'volume'=>1915,
                'gift'=>1,
                'price'=>1500,
                'url'=>'https://www.google.com/',
            ),//correct
        array(
                'id'=>6957,
                'year'=>'NV',
                'volume'=>191511,
                'price'=>1200,
                'url'=>'https://www.google.com/',
            ),//Invalid value of volume
        array(
                'id'=>6957,
                'year'=>2017,
                'volume'=>1913,
                'price'=>600,
                'url'=>'https://www.google.com/',
            ),//Product is not a vintage product
        array(
                'id'=>6817,
                'year'=>2015,
                'volume'=>1913,
                'price'=>599.99,
                'url'=>'https://www.google.com/',
            ),//correct
        array(
                'id'=>6817,
                'year'=>2027,
                'volume'=>1913,
                'price'=>599.99,
                'url'=>'https://www.google.com/',
            ),//Vintage doesn't exist
        array(
                'id'=>9999999,
                'year'=>2015,
                'volume'=>1913,
                'price'=>599.99,
                'url'=>'https://www.google.com/',
            )//Product doesn't exist
    ))){
    var_dump($content);
}
echo '<br /><br />';
echo 'WI Scores:<br />';
if($content = getPage('getWIScores',null)){
    var_dump($content);
}
echo '<br /><br />';
echo 'Personal WI Scores:<br />';
if($content = getPage('getPersonalWIScores',null)){
    var_dump($content);
}
echo '<br /><br />';
echo 'Targeted WI Scores:<br />';
if($content = getPage('getWIScores',array(
        array('id'=>6957),
        array('id'=>6817,'year'=>2015),
        array('id'=>6752,'year'=>2014),
    ))){
    var_dump($content);
}
echo '<br /><br />';
echo 'Targeted Personal WI Scores:<br />';
if($content = getPage('getPersonalWIScores',array(
        array('id'=>6957),
        array('id'=>6817,'year'=>2015),
        array('id'=>6752,'year'=>2014),
    ))){
    var_dump($content);
}

