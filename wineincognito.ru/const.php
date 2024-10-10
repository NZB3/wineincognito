<?php
DEFINE('IS_XMODULE', 1);
DEFINE('ABS_PATH', dirname(__FILE__));
//variables
//if((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')||(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')) {
//    DEFINE('IS_HTTPS', true);
//} else {
//	DEFINE('IS_HTTPS', false);
//}

DEFINE('IS_HTTPS', false);

if(IS_HTTPS){
    DEFINE('BASE_URL', 'https://'.$_SERVER['HTTP_HOST']);
} else {
    DEFINE('BASE_URL', 'http://'.$_SERVER['HTTP_HOST']);
}
DEFINE('LOG_DIRECTORY',ABS_PATH.'/../xmlog');
//end of variables

DEFINE('VIEW_TYPE_UI', 0);
DEFINE('VIEW_TYPE_AI', 1);

DEFINE('LANG\DEFAULT_LANG_ID',2);// Russian

DEFINE('PRODUCT\FOUNDATION_ATTRIBUTE_GROUP_ID',9);

DEFINE('PRODUCT\LOCATION_ATTRIBUTE_GROUP_ID',8);//used in review interface
DEFINE('PRODUCT\GRAPE_ATTRIBUTE_GROUP_ID',7);//used in review interface
DEFINE('PRODUCT\GASTRONOMIC_SYSTEM_ATTRIBUTE_GROUP_ID',18);//used in review interface



