<?php
namespace frontend\controllers;

use Yii;
use yii\web\UploadedFile;
use yii\web\Session;
use yii\helpers\BaseArrayHelper;
use frontend\controllers\BaseController;

use frontend\models\WeChat;
use frontend\models\Country;
use frontend\models\ImsShoppingAdv;
use frontend\models\ShoppingCategory;
use frontend\models\ShoppingGoods;
use frontend\models\ShoppingCart;
use frontend\models\ShoppingCollect;
use frontend\models\ShoppingTopic;

class HomeController extends BaseController {
    public function init() {
        parent::init();
        $this->layout = 'TrotoMain';
    }

    /**
     *支付成功页
     */
    public function actionT() {
        return $this->render('troto_payres', []);
    }

    /**
     * 主页
     */
    public function actionIndex() {
        //获取首页产品
        $shoppingGoodsModel    = new ShoppingGoods();
        $ShoppingCategoryModel = new ShoppingCategory();
        $categories = $ShoppingCategoryModel->category();
        $categories = array_column($categories, null, 'id');
        $trotoIndexGoods = $shoppingGoodsModel->getTrotoIndexGoodsList();

        return $this->render('troto_index', [
            'uid'         => $this->userinfo['uid'],
            'newGoods'    => $trotoIndexGoods['new'],
            'hotGoods'    => $trotoIndexGoods['hot'],
            'categories'  => $categories,
            'slides'      => $this->getAdvInfo(), //首页焦点图(文章/活动/商品)
            'signPackage' => '',
            // 'signPackage' => Yii::$app->wechat->app->jssdk->buildConfig(['onMenuShareTimeline', 'onMenuShareAppMessage'], false),
        ]);
    }

    /**
     * ajax 动态拉取加载推荐数据
     */
    public function actionIsrecommand(){
        $page = intval(Yii::$app->request->get('page'));

        $shoppingGoodsModel = new ShoppingGoods();
        $count = $shoppingGoodsModel->goodsCountIsrecommand();

        $pageTotal = ceil($count['count'] / 8);
        if($page >= $pageTotal){
            $string[] = "false";
            return json_encode($string);
        }
		$position = ($page * 8);
        $category = $shoppingGoodsModel->getCategoryGoodsByCateId('isrecommand', 1, $position);
        
        foreach ($category as $product){
            $img = '';
            if(!empty($product['countryimg'])){
                $img = "/images/country_flags/{$product['countryimg']}";
            }
            $string[] = '<li><a href="/goods/detail?id='.$product['id'].'" class="wares-ul">
                            <img src="'.$product['thumb'].'" style="background-image: url(/images/preload_goods.jpg); min-height:136px; background-size:contain;" />
                            <h3><span><img src="'.$img.'"" style="width:15px; height:15px"></span>'.$product['title']."</h3>
                            <h2>RMB {$product['marketprice']}</h2>
                            <p>查看详情<span class='icon-more2'></span></p>
                        </a></li>";
        }
        
        if(!empty($category)){
             return json_encode($string);
        }else{
            $string[] = "false";
            return json_encode($string);
        }
    }

    /**
     * 首页幻灯片
     */
    private function getAdvInfo() {
        $ShoppingAdvmodel = new ImsShoppingAdv();
        return $ShoppingAdvmodel->adv(1);
    }

}