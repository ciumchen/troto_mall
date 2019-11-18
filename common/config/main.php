<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        //config business log
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                //微信公号接口
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['wechat'],
                    'levels' => ['info', 'error', 'warning'],
                    'logVars' => ['*'],
                    // 'prefix' => function (){ return ''; },
                    'logFile' => dirname(dirname(__DIR__)).'/logs/wechat_'.date('ym').'.log',
                ],
                //微信支付接口
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['wxpay'],
                    'levels' => ['info', 'error', 'warning'],
                    'logVars' => [],
                    // 'prefix' => function (){ return ''; },
                    'logFile' => dirname(dirname(__DIR__)).'/logs/wxpay_'.date('ym').'.log',
                ],
                //设备长连接(ws)接口
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['wss'],
                    'levels' => ['info', 'error', 'warning'],
                    'logVars' => ['*'],
                    // 'prefix' => function (){ return ''; },
                    'logFile' => dirname(dirname(__DIR__)).'/logs/wss_'.date('ym').'.log',
                ],
                //设备com(http)接口
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['com'],
                    'levels' => ['info', 'error', 'warning'],
                    'logVars' => ['*'],
                    // 'prefix' => function (){ return ''; },
                    'logFile' => dirname(dirname(__DIR__)).'/logs/com_'.date('ym').'.log',
                ],
                //自动任务处理订单操作日志
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['order'],
                    'levels' => ['info', 'error', 'warning'],
                    'logVars' => ['*'],
                    // 'prefix' => function (){ return ''; },
                    'logFile' => dirname(dirname(__DIR__)).'/logs/order_'.date('ym').'.log',
                ],
                //自动任务处理补货单操作日志
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['replenish'],
                    'levels' => ['info'],
                    'logVars' => ['*'],
                    // 'prefix' => function (){ return ''; },
                    'logFile' => dirname(dirname(__DIR__)).'/logs/replenish_'.date('y').'.log',
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'geohash' => [
            'class' => 'sotechn\geohash\Geohash',
        ],
        'wechat' => [
            'class' => 'jianyan\easywechat\Wechat',
            'userOptions'    => [],  //用户身份类参数
            'sessionParam'   => 'wechatUser', //微信用户信息会话存储密钥
            'returnUrlParam' => '_wechatReturnUrl', //returnUrl存储在会话中
            //自定义服务模块
            'rebinds' => [
                // 'cache' => 'common\components\Cache',
            ]
        ],
    ],
];
