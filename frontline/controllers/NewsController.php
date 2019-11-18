<?php
namespace frontline\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Session;
use yii\helpers\BaseArrayHelper;
use frontline\models\CompanyNews;

class NewsController extends Controller{

    /**
     * 主页
     */
    public function actionIndex() {
        $cache = Yii::$app->cache;
        $request = Yii::$app->request;
        $cateid = $request->get('cateid',1);
        $key='news_'.$cateid;
        $newModels = new CompanyNews();
        $newsList =$cache->get($key);
        if (empty($newsList)) {
            $newsList = $newModels->getNewsById($cateid);
            //将数据存入缓存
            $cache->set($key,serialize($newsList),180);
        }else{
            $newsList = unserialize($newsList);
        }
        return $this->render('news', [
            'cateid'=>$cateid,
            'newsList'=>$newsList
            ]);
    }

    /**
     * 产品详情
     */
    public function actionNewInfo(){
        $request = Yii::$app->request;
        $id = $request->get('id');
        $cateid = $request->get('cateid');
        $new = CompanyNews::find()->select('*')->where(['id'=>$id])->one();
        $new->pv+=1;
        $new->save();
        //获取上一篇文章
        $upnew = CompanyNews::find()->select('title,id,outlink')->where(['<','id',$id])->andwhere(['cateid'=>$cateid])->orderby(['id'=>'SORT_DESC'])->asarray()->one();
        //获取下一篇文章
        $nextnew = CompanyNews::find()->select('title,id,outlink')->where(['>','id',$id])->andwhere(['cateid'=>$cateid])->orderby(['id'=>'SORT_ASC'])->asarray()->one();
        return $this->render('news_info', [
            'new'=>$new,
            'upnew'=>$upnew,
            'cateid'=>$cateid,
            'nextnew'=>$nextnew
            ]);
    }
}
