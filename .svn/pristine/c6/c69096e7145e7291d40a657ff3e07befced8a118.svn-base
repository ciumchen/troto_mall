<?php
defined('YII_ENV') or define('YII_ENV', 'prod');

if ($_SERVER['REMOTE_ADDR']=='3.110.219.79') {
    define('YII_DEBUG', true);
} else {
    define('YII_DEBUG', false);
}

if (YII_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../common/config/main-local.php'),
    require(__DIR__ . '/../config/main.php'),
    require(__DIR__ . '/../config/main-local.php')
);

if (YII_DEBUG) {
    // configuration adjustments for 'dev' environment  
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii']['class'] = 'yii\gii\Module';
    $config['modules']['gii']['allowedIPs'] = ['*'];
}

(new yii\web\Application($config))->run();
