<?php
return [
    'site_name' => '多轮多微服务',
    'site_keywords' => '自助购胎,多轮多微服务,智慧新零售服务',
    'site_descript' => '多轮多微服务，致力于打造便捷、高性价比的自助购买、换胎服务平台。',
    'admin_mail' => 'fw@troto.com.cn',
    
    'static_cdn_url' => 'http://cdn.10d15.com',
    'firstOrderDiscount' => [
        'startTime' => '2016-07-29 00:00:00',
        'endTime' => '2016-07-31 23:59:59',
        'day1' => 20,
        'day2' => 15,
        'day3' => 10
    ],
    'ImageType' => ["image/jpeg", "image/png", "image/jpg"],

    'debugIP' => ['113.87.12.26', '14.116.137.170'],
];

# Yii::$app->params['site_name']
# Yii::$app->params['admin_mail']
# Yii::$app->params['static_cdn_url']