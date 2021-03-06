<?php
// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',false);
if(YII_DEBUG) {
    ini_set("display_errors",1);
    error_reporting(E_ALL);
    $global_counter = 0;
}

// change the following paths if necessary
$yii=dirname(__FILE__).'/../yii/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

// define the root of this application
defined('ROOT') or define('ROOT',dirname(__FILE__));
defined('HTTP_HOST') or define('HTTP_HOST',$_SERVER["HTTP_HOST"]);
defined('WEB_ROOT') or define('WEB_ROOT',dirname($_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]));

// define local library paths (see StdLib class)
defined('LOCAL_LIBRARY_PATH') or define('LOCAL_LIBRARY_PATH',ROOT.'\\library\\');
defined('LOCAL_IMAGE_LIBRARY') or define('LOCAL_IMAGE_LIBRARY',LOCAL_LIBRARY_PATH."images\\");

// define web library paths (see StdLib class)
defined('WEB_LIBRARY_PATH') or define('WEB_LIBRARY_PATH','//'.WEB_ROOT.'/library/');
defined('WEB_IMAGE_LIBRARY') or define('WEB_IMAGE_LIBRARY',WEB_LIBRARY_PATH."images/");

include ROOT."/protected/models/Functions.php";
require_once($yii);
Yii::createWebApplication($config)->run();