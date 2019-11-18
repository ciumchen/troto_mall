<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-comapi',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'comapi\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-comapi',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-comapi', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the comapi
            'name' => 'advanced-comapi',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'class'               => 'yii\web\UrlManager',
            'enablePrettyUrl'     => true,
            'enableStrictParsing' => false,
            'showScriptName'      => false,
            'rules' => [
                '/auth'=>'base/auth',
                '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
            ],
        ],
    ],
    'params' => $params,
];
