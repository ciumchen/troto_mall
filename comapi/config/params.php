<?php
return [
    'adminEmail' => 'admin@example.com',

    'RpcServiceUri' => 'http://mall.troto.com.cn/rpc/index',

    'FROMAT_JSON'    => \yii\web\Response::FORMAT_JSON,
    'urlManager' => [
            'class'               => 'yii\web\UrlManager',
            'enablePrettyUrl'     => true,  //美化url==ture
            'enableStrictParsing' => false,  //不启用严格解析
            'showScriptName'      => false,   //隐藏index.php
            'rules' => [
                '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                '<controller:order>/<action:\w+>'=>'<controller>/<action>',
            ],
        ],
];
