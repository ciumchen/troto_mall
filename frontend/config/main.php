<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'home',
    'timeZone' => 'Asia/Shanghai',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'logVars' => [],  //[$_GET, $_POST, $_FILES, $_COOKIE,$_SESSION, $_SERVER ],
                    'levels' => ['error', 'warning'],   //info
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/' => '/home/index',
                '/cabinet' => '/goods/cabinet',
                '/service-region' => '/cart/region',
                '/tyres' => '/goods/cate',
                '<alias:cabinet-apk|about-us|service-policy|service-customer>' => 'site/<alias>',
            ],
        ],
    ],
    'params' => $params,
];
