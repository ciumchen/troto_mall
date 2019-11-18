<?php
namespace console\controllers;

use yii;
use yii\console\Controller;

class ExpiredCacheClearController extends Controller{

    /**
     * 定时任务清理过期文件缓存文件
     * PS.使用：php /home/wwwroot/troto_mall/yii expired-cache-clear/index
     */
    public function actionIndex() {
        Yii::$app->cache->gc();
    }

}