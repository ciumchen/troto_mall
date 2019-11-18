<?php
namespace frontline\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Session;
use yii\helpers\BaseArrayHelper;

class NewsController extends Controller{

    /**
     * 主页
     */
    public function actionIndex() {
        $cache = Yii::$app->cache;

        return $this->render('news', []);
    }

    /**
     * 产品详情
     */
    public function actionNewInfo(){
        return $this->render('news_info', []);
    }