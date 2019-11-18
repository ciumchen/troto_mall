<?php
namespace frontend\controllers;

use frontend\models\WeChat;
use Yii;
use yii\web\UploadedFile;
use yii\web\Session;
use yii\helpers\BaseArrayHelper;
use frontend\controllers\BaseController;
use frontend\models\Country;
use frontend\models\ImsShoppingAdv;
use frontend\models\ShoppingCategory;
use frontend\models\ShoppingGoods;
use frontend\models\ShoppingCart;
use frontend\models\ShoppingCollect;
use frontend\models\ShoppingTopic;

class GoodsController extends BaseController {

    public $enableCsrfValidation = false;

    public function init() {
        $this->layout = 'TrotoMain';
        parent::init();
    }

    /**
     * 商品分类页面
     */
    public function actionCate() {
        $cache = Yii::$app->cache;
        $categoryModel = new ShoppingCategory();

        $categoryId   = intval($this->request->get('id', 0));

        //获取分类列表
        $cateList  = $cache->get('ck_cate');
        $pcateList = $cache->get('ck_pcate');
        if ($cateList==false || $pcateList==false) {
            $cateList = $categoryModel->category();
            foreach ($cateList as $cateOne) {
                if ($cateOne['parentid']==0) {
                    $pcateList[] = $cateOne;
                }
            }
            if (!YII_DEBUG) {
                $cache->set('ck_cate',  serialize($cateList),  1800);
                $cache->set('ck_pcate', serialize($pcateList), 1800);
            }
        } else {
            $cateList  = unserialize($cateList);
            $pcateList = unserialize($pcateList);
        }

        //根据请求的id获取二级分类id
        $categoryName = '';
        $ccate = [];
        if (strlen($categoryId)<3) {
            foreach ($cateList as $cateItemOne) {
                if ($cateItemOne['parentid'] && substr($categoryId, 0, 2)==$cateItemOne['parentid']) {
                    $ccate[] = $cateItemOne;
                }
                if ($categoryId==$cateItemOne['id']) {
                    $categoryName = $cateItemOne['name'];
                }
            }
        } else {
            foreach ($cateList as $cateItemOne) {
                if ($cateItemOne['parentid'] && $cateItemOne['parentid']==$categoryId) {
                    $ccate[] = $cateItemOne;
                    $categoryName = $cateItemOne['name'];
                }
                if ($categoryId==$cateItemOne['id']) {
                    $categoryName = $cateItemOne['name'];
                }
            }
        }


        return $this->render('troto_cate', [
            'signPackage' => Yii::$app->wechat->app->jssdk->buildConfig(['onMenuShareTimeline', 'onMenuShareAppMessage'], false),
            'uid' => ($this->userinfo['uid'] != 0) ? $this->userinfo['uid'] : 0,
			'advData'    => $this->getAdvInfo(),
            'pcate'      => $pcateList,
            'ccate'      => $ccate,
            'categoryId' => $categoryId,
            'categoryName' => $categoryName,
        ]);
    }



    /**
     * 商品详情页
     */
    public function actionItem() {
        $goodsid = intval(Yii::$app->request->get('id'));
        if (!$goodsid) {
            return $this->redirect('/');
        }

        $GoodsModel = new ShoppingGoods();
        $goodsInfo  = $GoodsModel->getGoodsDetailByGoodsId($goodsid);
        if (!$goodsInfo) {
            return $this->redirect('/');
        }
        
        $signPackage = '';
        // var_dump( $GoodsModelDataShow);die;
        return $this->render('troto_detail', [
            'signPackage'  => $signPackage,
            'goods'        => $goodsInfo,
            'Relatedgoods' => $GoodsModel->getCategoryGoodsByCateId('relatedgoods', $goodsid, 4), /*4 = limit*/
            'goodsParam'   => $GoodsModel->goodsParam($goodsid),
            'uid'          => ($this->userinfo['uid'] != 0) ? $this->userinfo['uid'] : 0,
            'goodsOption'  => ($GoodsModel->goodsOption($goodsid)) ? $GoodsModel->goodsOption($goodsid) : 0
        ]);

    }

    /**
     * ajax 动态拉取加载分类数据
     */
    public function actionCateData() {
        $shoppingGoodsModel = new ShoppingGoods();

        if (Yii::$app->request->isAjax && Yii::$app->request->method=='POST') {
            $page = max(0, intval(Yii::$app->request->post('page')));
            $cateId = intval(Yii::$app->request->post('id'));            
        } else {
            $page = max(0, intval(Yii::$app->request->get('page')));
            $cateId = intval(Yii::$app->request->get('id'));
            $cateId = $cateId ? $cateId : 22;
        }

        if ($cateId && strlen($cateId)>2) {
            $w = ['ccate'=>$cateId, 'status'=>1, 'deleted'=>0, 'isflash'=>0];
        } else if ($cateId) {
            $w = ['pcate'=>$cateId, 'status'=>1, 'deleted'=>0, 'isflash'=>0];
        } else {
            $w = ['status'=>1, 'deleted'=>0, 'isflash'=>0];
        }
        $total = $shoppingGoodsModel->countGoodsTotal($w);
        $pageTotal = intval(ceil($total/8));

        if ($page > $pageTotal) {
            $string[] = "false";
            if (Yii::$app->request->isAjax && Yii::$app->request->method=='POST') {
                $string = ['total'=>$total, 'page'=>$pageTotal, 'data'=>[]];
            }
        } else{
            $offset = ($page * 8);
            $queryField = 'pcate';
            if ($cateId && strlen($cateId)>2) {
                $queryField = 'ccate';
            } else if ($cateId) {
                $queryField = 'pcate';
            }

            $category = $shoppingGoodsModel->getCategoryGoodsByCateId($queryField, $cateId, $offset);
            if (Yii::$app->request->isAjax && Yii::$app->request->method=='POST') {
                $goodsList = [];
                foreach ($category as $pk=>$product) {
                    unset($product['content']);
                    unset($product['comm1']);
                    unset($product['comm2']);
                    unset($product['comm3']);
                    unset($product['createtime']);
                    $category[$pk] = $product;
                    $goodsList[]  = $product['id'];
                }
                $favouriteGoods = $this->checkFavourite($goodsList, $this->userinfo['uid']);
                foreach ($category as $pk=>$product) {
                    $category[$pk]['fav'] = 0;
                    if (in_array($product['id'], $favouriteGoods)) {
                        $category[$pk]['fav'] = 1;
                    }
                }
                $string = ['total'=>$total, 'page'=>$pageTotal, 'data'=>$category];
            } else {
                foreach ($category as $product) {
                    $countryimg = '';
                    if (!empty($product['countryimg'])) {
                        $countryimg = '<span><img src="/images/country_flags/'.$product['countryimg'].'" style="width:15px; height:15px;display:block;float:left;"></span>';
                    }

                    $string[] = '<li><a href="/goods/detail?id='.$product['id'].'">
                            <img src="https://files.1card1.cn/Platform/1095601/20190708/4922d42f8bbc44b483d3de4277a21f92.jpg" style="background-image: url(/images/preload_goods.jpg); min-height:95px; background-size:contain;">
                            <h3>'.$product['title'].'</h3>
                            <p>'.$countryimg.'RMB <span class="class-price" style="color:#f00;font-weight:bold;">'.$product['marketprice'].'</span></p>
                            </a></li>';
                }
                if (empty($category)) {
                    $string[] = "false";
                }
            }
        }
        return json_encode($string);
    }

    /**
     * 商品搜索
     * @return json
     **/
    public function actionQ() {
        //获取产品名称
        $searchKeywords = trim($this->request->get('wd', ''));

		if($searchKeywords==''){
			return $this->redirect("/goods/cate");
		}

        $shoppingGoodsModel = new ShoppingGoods();

        // ajax 拉去搜索结果
        if ( trim($this->request->get('action')) == 'ajax') {

            //获取根据商品名称模糊查询
            $page = $this->request->get('page');

            //判断传过来的数量是否大于搜索数量，如果 大于的话，防止用户刷数据
            $count = $shoppingGoodsModel->countSearch($searchKeywords);
            $counts = ceil($count['count'] / 8); //总页数
            if ($page >= $counts) {
                return json_encode('false');
            }

            $position = ($page * 8);
            $SearchData = $shoppingGoodsModel->search($searchKeywords, $position);
            return json_encode($this->searchResult($SearchData, $position));
        } else {
            //获取根据商品名称模糊查询
			$category = new ShoppingCategory();
            $SearchData = $shoppingGoodsModel->search($searchKeywords);
            return $this->render('q', [
                'searchKeywords' => $searchKeywords,
				'advData' =>$this->getAdvInfo(),
                'search' => $this->searchResult($SearchData),
                'tags' => ($shoppingGoodsModel->label()) ? $shoppingGoodsModel->label() : 0
            ]);
        }
    }

    private function searchResult($SearchData, $limit = '')
    {
        $SearchDataJson = json_encode([
            'code' => 200,
            'msg' => '',
            'data' => [
                'total' => count($SearchData),
                'list' => [$SearchData]
            ]
        ]);

        $SearchDataDecode = json_decode($SearchDataJson);
        $string = '';
        foreach ($SearchDataDecode->data->list as $key => $prodct) {
            foreach ($prodct as $product_value) {
                if (!empty($product_value->countryimg)) {
                    $countryimg = '<span><img src="/images/country_flags/'.$product_value->countryimg.'" style="width:15px; height:15px;display:block;float:left;"></span>';
                } else {
                    $countryimg = '';
                }
                $string .= '<li><a href="/goods/detail?id='.$product_value->id.'"><img src="'.$product_value->thumb.'" style="background-image: url(/images/preload_goods.jpg); min-height:95px; background-size:contain;"><h3>'.$product_value->title.'</h3>
                    <p>'.$countryimg.'RMB <span class="class-price" style="color:#f00;font-weight:bold;">'.$product_value->marketprice.'</span></p>
					</a></li>';
            }
        }

        return (!empty($prodct)) ? $string : false;
    }

	
	/**
     * 搜索广告
     * @return array
     */
    private function getAdvInfo() {
        $ShoppingAdvmodel = new ImsShoppingAdv();
        return $ShoppingAdvmodel->adv(4);
    }


    /**
     * 检查商品列表及用户收藏状态
     * @param array $goodsList 商品列表
     * @param intval $uid 用户id
     * @return array
     */
    private function checkFavourite($goodsList, $uid) {
        $list = [];
        $goods = ShoppingCollect::find('goodsid')->where(['uid'=>$uid, 'status'=>1])
                               ->andWhere(
                                    ['in', 'goodsid', $goodsList]
                                )->asArray()->all();
        foreach ($goods as $goodsItem) {
            $list[] = $goodsItem['goodsid'];
        }
        return $list;
    }

}
//end file