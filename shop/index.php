<?php
if(strpos($_SERVER['REQUEST_URI'], '/var/www/laptoptop7/shop/protected/commands/shell')!== false){
        $_SERVER['REQUEST_URI'] = str_replace('/var/www/laptoptop7/shop/protected/commands/shell','',$_SERVER['REQUEST_URI']);
}


ini_set('display_errors', 1);
// change the following paths if necessary
$yii=dirname(__FILE__).'/../Yii/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
//defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
//defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
require_once('protected/components/WebApplication.php');
Yii::createApplication('WebApplication', $config)->run();
