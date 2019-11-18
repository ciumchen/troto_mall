<?php
namespace frontline\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Session;
use yii\helpers\BaseArrayHelper;
use frontline\models\ShoppingGoods;

class ProductController extends Controller{

    /**
     * 主页
     */
    public function actionIndex() {
        $cache = Yii::$app->cache;
        $request = Yii::$app->request;
        $goodsModels = new ShoppingGoods();
        $typeid = $request->get("typeid",22);
        $key = "Pcate_".$typeid;
        $goods = $cache->get($key);
        if (empty($goods)) {
           $goods = $goodsModels->getCategoryGoodsByCateId($typeid);
           //加入缓存
           $cache->set($key,serialize($goods),1800);
        }else{
           $goods = unserialize($goods);
        }
        // echo "<PRE>";
        // print_r($goods);
        // exit();
        return $this->render('product', [
            'goods'=>$goods,
            'typeid'=>$typeid
            ]);
    }

    /**
     * 产品详情
     */
    public function actionGoodsInfo(){
        $request = Yii::$app->request;
        $goodsModels = new ShoppingGoods();
        $goodsid = $request->get('id');
        if (empty($goodsid)) {
            $this->redirect("/product/index");
        }

        $goodsInfo = $goodsModels->getGoodsInfo($goodsid);
        // echo "<PRE>";
        // print_r($goodsInfo);
        // exit();
        return $this->render('product_info', [
                'goods'=>$goodsInfo
            ]);
    }
}
