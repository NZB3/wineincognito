<?php
error_reporting(0);
ini_set("display_errors", 0);

header('Content-Type: text/html; charset=utf-8');

require_once 'const.php';
require_once ABS_PATH.'/modules/Main/lib.php';
get_instance()->__start();

