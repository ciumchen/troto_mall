<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
            //'useFileTransport' => true, //放在本地的邮件列表,测试邮件的时候可以开启这个
            'transport' => [
                    'class' => 'Swift_SmtpTransport',
                    'host' => 'smtp.exmail.qq.com',
                    'username' => 'reporter@10d15.com',
                    'password' => 'Sdyk10152',
                    'port' => '465',
                    'encryption' => 'ssl',
            ],
        ],

    ],
    'params' => $params,
];