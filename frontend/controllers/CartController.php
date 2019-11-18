<?php
namespace frontend\controllers;

use Yii;
use yii\web\UploadedFile;
use yii\web\Session;
use yii\web\Response;
use yii\helpers\BaseArrayHelper;
use frontend\controllers\BaseController;
use common\models\Regions;
use frontend\models\ShoppingGoods;
use frontend\models\ShoppingCart;

class CartController extends BaseController {
    /*
    *购物车信息
    */
    public function actionIndex() {
        $this->layout = 'TrotoMain';
        $cartModel = new ShoppingCart();

        if (!$this->userinfo['uid']) {
            $cookieS = empty($_COOKIE['cart']) ? false : true;
            if ($cookieS == false) {
                return $this->redirect('/home');
            }
            
            $state = empty(json_decode($_COOKIE['cart'])) ? false : true;
            if ($cookieS && $state) {
                $c_name = $_COOKIE['cart'];
                $cartObject = json_decode($c_name);
                foreach ($cartObject as $key => $value) {
                    $cartArray[$key]['id'] = $value->cartid;
                    $cartArray[$key]['title'] = $value->title;
                    $cartArray[$key]['total'] = $value->total;
                    $cartArray[$key]['thumb'] = $value->thumb;
                    $cartArray[$key]['name']  = $value->name;
                    $cartArray[$key]['marketprice']    = $value->marketprice;
                    $cartArray[$key]['productprice']   = $value->productprice;
                    $cartArray[$key]['specifications'] = $value->specifications;
                    $cartArray[$key]['maxbuy']         = $value->maxbuy;
                }
                $value = $cartModel->SumCartTotal($cartArray);
            }
            $sum = 0;
        } else {
            $sum = $num =0;
             $cartDataTable = $cartModel->getCartGoodsByUid($this->userinfo['uid']);
            // 如果检测已经修改，重新查询被修改的数据
            foreach ($cartDataTable as $key => $value) {
                $cartArray[$value['cartid']] = $value;
                $sum += $value['price'] * $value['total'];
                $num++;
            }
        }

        $cartData = [];
        $cartDataset = $cartModel->getCartGoodsByUid($this->userinfo['uid']);
        foreach($cartDataset as $cartItem) {
            $cartData[$cartItem['cabinetid']][] = $cartItem;
        }

        return $this->render('troto_index', [
            'cartData' => $cartData,
            'sum' => isset($value['sum']) ? $value['sum'] : $sum,
            're' => 2,
            'uid' => ($this->userinfo['uid'] != 0) ? $this->userinfo['uid'] : 0,
            'signPackage' => Yii::$app->wechat->app->jssdk->buildConfig(['onMenuShareTimeline', 'onMenuShareAppMessage'], false),

        ]);
    }

    /**
     * 查询开放上门安装服务开放区域
     */
    public function actionRegion() {
        $this->enableCsrfValidation = false;
        Yii::$app->response->format=Response::FORMAT_JSON;
        $resp = ['code'=>2001, 'msg'=>'INVALID REQUEST.', 'data'=>[]];
        $RegionsModel = new Regions();
        $regionId = intval(Yii::$app->request->post('regionid'));

        $res = $RegionsModel->getOpenDist($regionId);
        foreach($res as $resKey=>$resValue) {
            $res[$resKey] = [ 'regionid'=>$resValue['regionid'], 'name'=>$resValue['name'], 'fee'=>$resValue['fee'] ];
        }
        if ($res) {
            $resp['code'] = 2000;
            $resp['msg']  = 'OK';
            $resp['data'] = $res;
        }
        return $resp;
    }

    /**
     * 查询购物车项目
     */
    public function actionList() {
        $resp = ['total'=>0, 'amount'=>0, 'data'=>[]];
        if ($this->userinfo['uid']) {
            $cartModel   = new ShoppingCart();
            $cartDataset = $cartModel->getCartGoodsByUid($this->userinfo['uid']);
            $resp['total']  = count($cartDataset);
            foreach($cartDataset as $cartItem) {
                $resp['data'][$cartItem['cabinetid']][] = $cartItem;
                $resp['amount'] += $cartItem['total']*$cartItem['price'];
            }
        }
        $resp['amount'] = number_format($resp['amount'], 2);
        return json_encode($resp);
    }

    /**
     * 修改购物车项目
     */
    public function actionEdit(){
        $this->enableCsrfValidation = false;
        Yii::$app->response->format=Response::FORMAT_JSON;
        $resp = ['code'=>2001, 'msg'=>'INVALID REQUEST.'];
        $cartid = intval(Yii::$app->request->post('cartid'));
        $total  = intval(Yii::$app->request->post('total'));
        if ($cartid&&$total) {
            $rs = ShoppingCart::updateAll(['total'=>$total], ['cartid'=>$cartid, 'uid'=>$this->userinfo['uid']]);
            if ($rs) {
                $resp['code'] =2000;
                $resp['msg']  = 'OK';
            }
        }
        return $resp;
    }

    public function actionSelected() {
        $this->enableCsrfValidation = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $resp = ['code'=>2001, 'msg'=>'INVALID REQUEST.'];
        $cartid = intval(Yii::$app->request->post('cartid'));
        if ($cartid) {
            $rs = ShoppingCart::deleteAll(['cartid'=>$cartid, 'uid'=>$this->userinfo['uid']]);
            if ($rs) {
                $resp['code'] =2000;
                $resp['msg']  = 'OK';
            }
        }
        return $resp;
    }

    /**
     * 删除购物车项目
     */
    public function actionDel(){
        $this->enableCsrfValidation = false;
        Yii::$app->response->format=Response::FORMAT_JSON;
        $resp = ['code'=>2001, 'msg'=>'INVALID REQUEST.'];
        $cartid = intval(Yii::$app->request->post('cartid'));
        if ($cartid) {
            $rs = ShoppingCart::deleteAll(['cartid'=>$cartid, 'uid'=>$this->userinfo['uid']]);
            // $rs = true;
            if ($rs) {
                $resp['code'] =2000;
                $resp['msg']  = 'OK';
            }
        }
        return $resp;
    }

    /*
    *加入购物车
    */
    public function actionAdd() {
        $this->enableCsrfValidation = false;
        Yii::$app->response->format=Response::FORMAT_JSON;

        $product_id = Yii::$app->request->get('id');
        $cartotal = Yii::$app->request->get('total');
        
        $user_ids = Yii::$app->session->get('userinfo');
        $user_id = $user_ids['uid'];
        $GoodsModel = new ShoppingGoods();
        $productData = $GoodsModel->getGoodsAllDetailByGoodsId($product_id);

        if (Yii::$app->request->get('op') == 'add') {
            //参数过滤
            if($product_id=='' || $cartotal==''){
                    $ret['parameter'] = true;
                    return json_encode($ret);
            }

            //产品id获取产品信息

            if ($user_id) {

                /*
                @用户登录状态
                */
                $cartModel = new ShoppingCart();
                $cartData = $cartModel->getCartGoodsByUid($user_id);
                $Index = false;
                $c_id = '';
                if ($cartData) {

                    foreach ($cartData as $key => $value) {

                        if ($product_id == $value['id']) {

                            $Index = true;
                            $c_id = $value['c_id'];
                        }

                    }
                }
                        //活动还没开始
                    if(time() < $productData['starttime']){
                        $ret['activity'] = false;
                        return json_encode($ret);
                    }
                //如果在闪购期间内则执行 21 < 19 
                if(time() > $productData['starttime'] && time() < $productData['f_endtime']){
                
                    //如果用户的数量大于闪购的数量则不能加入购物车
                    if($cartotal > $productData['total'] && $productData['f_endtime']!=''){
                        $ret['flash'] = false;
                        return json_encode($ret);
                    }
                }else if( time() > $productData['f_endtime'] && $productData['f_endtime']!=''){ //活动结束
                        $ret['flash'] = true;
                        return json_encode($ret);
                }
                if ($Index) {
                    $saveState = $cartModel->UpdateCart($c_id, $cartotal);
                    if ($saveState) {
                        $ret['msg'] = true;
                    } else {
                        $ret['update_msg'] = false;
                    }

                } else {
                    //查询规格的价格，按照规格价格来进行购买
                    $cartModel->goodsid = $product_id;
                    $cartModel->goodstype = "{$productData['type']}";
                    $cartModel->uid = "{$user_id}";
                    $cartModel->total = $cartotal;
                    $cartModel->marketprice = "{$productData['marketprice']}";
                    $cartModel->insert();
                    if ($cartModel->id) {
                        $ret['msg'] = true;
                        $ret['num'] = count($cartData) + 1;
                    } else {
                        $ret['msg'] = false;
                    }

                }

                return json_encode($ret);
            }
        } else if (Yii::$app->request->get('op') == 'del') {
            $product_id = Yii::$app->request->get('id');
            $sum = 0;
            $num = 0;
            if ($this->userinfo['uid']) {
                $cartModel = new ShoppingCart();
                $delect = $cartModel->deleteCartData($product_id);
                if ($delect) {
                    $ret['msg'] = true;
                } else {
                    $ret['msg'] = false;
                    //return;
                }
                $cartModel = new ShoppingCart();
                $cartDataTable = $cartModel->getCartGoodsByUid($this->userinfo['uid']);
                foreach ($cartDataTable as $key => $value) {
                    $CartData[$value['id']] = $value;
                    $sum += $CartData[$value['id']]['marketprice'] * $CartData[$value['id']]['total'];
                    $num++;
                }

            }
            //unset($_SESSION['carNum']);


            $ret['sumTotal'] = $sum;
            $ret['num'] = $num;
            return json_encode($ret);
        } else {
            echo "no cart";
        }
    }

    public function actionIsIn() {
        $product_id = Yii::$app->request->get('id');
        $uid = $this->userinfo['uid'];

        $cart = new ShoppingCart();
        $row = $cart->isIn($uid, $product_id);

        echo json_encode([
            'sucess' => true,
            'is_in' => ($row === null ? false : true)
        ]);
    }
}
