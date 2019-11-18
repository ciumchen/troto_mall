<?php
namespace frontend\controllers;

use common\components\LogisticComponents;

use Yii;
use yii\helpers\Func;
use yii\helpers\BaseArrayHelper;
use yii\web\UploadedFile;
use yii\web\Response;
use frontend\controllers\BaseController;

use common\models\ShoppingOrder as SO;
use common\models\ShoppingOrderGoods as SOG;
use common\models\Regions;

use frontend\models\Users;
use frontend\models\UsersProfile;
use frontend\models\WeChat;
use frontend\models\ShoppingCategory;
use frontend\models\ShoppingOrder;
use frontend\models\ShoppingOrderGoods;
use frontend\models\ShoppingGoods;
use frontend\models\ShoppingCart;
use frontend\models\ShoppingAddress;
use frontend\models\ShoppingOrderLogistic;
use frontend\models\Broker;
use frontend\models\BrokerOrder;
use frontend\models\BrokerOrderReward;
use frontend\models\Coupon;
use frontend\models\CoreWxpayLog;


class OrderController extends BaseController {
    public function actionList() {
        $this->layout = 'TrotoMain';

        $status = Yii::$app->request->get('status', '');
        $status = $status=='' ? $status : intval($status);

        $cateList = ShoppingCategory::find()->asArray()->all();
        $cateList = array_column($cateList, null, 'id');

        $ShoppingOrderModel = new ShoppingOrder();
        $orderList = $ShoppingOrderModel->getOrderGoodsByUid($this->userinfo['uid'], $status);

        foreach ($orderList as $orderListKey => $orderListValue) {
            $orderList[$orderListKey]['goods'] = $ShoppingOrderModel->getGoodsByOrder($orderListValue['orderid']);
            $orderList[$orderListKey]['goodstotal'] = count($orderList[$orderListKey]['goods']);
        }

        return $this->render('troto_list', [
            'status'     =>  $status,
            'orderList' =>  $orderList,
            'cateList'  =>  $cateList,
            'signPackage' => '',
            //'signPackage' => Yii::$app->wechat->app->jssdk->buildConfig(['onMenuShareTimeline', 'onMenuShareAppMessage'], false),
        ]);
    }

    public function actionConfirm() {
        $this->layout = 'TrotoMain';
        $cartids = Yii::$app->request->post('cartids');
        $goodsList = [];
        if (!empty($cartids)) {
            $cartModel   = new ShoppingCart();
            $cartDataset = $cartModel->getCartGoodsByUid($this->userinfo['uid']);
            $cabinetid = isset($cartDataset[0]['cabinetid']) ? $cartDataset[0]['cabinetid'] : 0;
            foreach ($cartDataset as $cartItem) {
                if ($cartItem['cabinetid']==$cabinetid) $goodsList[] = $cartItem;
            }
        }
        return $this->render('troto_order_confirm', [
            'goodsList'   => $goodsList,
            'cartids'     => $cartids,
            'signPackage' => Yii::$app->wechat->app->jssdk->buildConfig(['onMenuShareTimeline', 'onMenuShareAppMessage'], false),
        ]);
    }

    public function actionPrepay($sn) {
        $this->layout = 'TrotoMain';
        $payment = Yii::$app->wechat->payment;

        //查询订单有效性
        $orderInfo = SO::find()->where(['ordersn'=>$sn, 'status'=>0])->one();

        //=====创建预付款单===============
        if($orderInfo) {
            $preOrder = $payment->order->unify([
                'out_trade_no' => $orderInfo->ordersn,
                // 'total_fee'    => $orderInfo->price*100,
                'total_fee'    => 1, //测试
                'openid'       => $this->userinfo['openid'],
                'body'         => '多轮多微服务自助购胎',
                'remark'       => '',
                'trade_type'   => 'JSAPI', //请对应换成你的支付方式对应的值类型
                'notify_url'   => 'http://mall.troto.com.cn/wxpay/notify',
            ]);
            //成功返回预付款单
            if($preOrder['return_code']=='SUCCESS' && $preOrder['result_code']=='SUCCESS') {
                //返回json字符串，如果想返回数组传第二个参数false
                // $preOrder = $this->wechat->payment->configForPayment();
                // var_dump($preOrder);exit;
                $paymentJson = $payment->jssdk->sdkConfig($preOrder['prepay_id']);
            }

            return $this->render('troto_pre_pay', [
                'sn'          => $orderInfo->ordersn,
                'amount'      => $orderInfo->price,
                'payment'     => $payment,
                'paymentJson' => $paymentJson,
                'signPackage' => Yii::$app->wechat->app->jssdk->buildConfig(['onMenuShareTimeline', 'onMenuShareAppMessage'], false),
            ]);
        } else{
            $this->redirect('/order/payment?sn='.$sn);
        }
    }

    public function actionPayment($sn) {
        $sn = intval($sn);
        $orderModel = SO::find()->where(['ordersn'=>$sn])->one();
        //同步检测支付结果，如果还未支付
        if ($orderModel && $orderModel->status==0) {
            $payRes = Yii::$app->wechat->payment->order->queryByOutTradeNumber($sn);
            if($payRes['return_code']=='SUCCESS' && isset($payRes['trade_state']) && $payRes['trade_state']=='SUCCESS') {
                // if ($orderModel && $orderModel->status<1) {
                if ($orderModel && $orderModel->status<1 && $payRes['cash_fee']==$orderModel->price*100) {
                    $pickValidCode = rand(100000, 999999);
                    $orderModel->status         = 1;
                    $orderModel->uid            = $orderModel->uid ?: $userInfo->uid;
                    $orderModel->from_user      = $orderModel->from_user ?: $payRes['openid'];
                    $orderModel->transid        = $payRes['transaction_id'];
                    $orderModel->paymenttime    = strtotime($payRes['time_end']);
                    $orderModel->pickcode_user  = $pickValidCode;
                    $orderModel->pickcode_serve = '';
                    $orderModel->ext            = '^^'.$orderModel->ext.'用户完成支付('.date('Y-m-d H:i:s').')';
                    if ( $orderModel->save()) {
                        //$payRes['is_subscribe'], 为Y时可微信推送消息, 为N时可推荐关注
                        //非机柜下单推送取胎码短信给用户
                        if ($orderModel->source!='BOX') {
                            $sendRs = SmsLog::send($userInfo->mobile,"您已完成订单({$orderModel->ordersn})支付，凭取胎码 {$pickValidCode}，前往多轮多智能柜自助取胎。如遇问题，请致电".Yii::$app->params['serviceNumber']."协助！", SmsLog::TYPE_PICk);
                            file_put_contents($wxpayLog, "\n推送取胎短信 ".intval($sendRs), FILE_APPEND);
                        }
                    }
                }
            }
        }

        if ($orderModel) {
            $res = [
                'status' => $orderModel->status,
                'title' => '',
                'desc' => [],
                'payment' => $orderModel->price,
                'ordersn' => $orderModel->ordersn,
            ];
            if ($res['status']<0) {
                $res['title']='订单关闭';
                $res['desc']  = ['超时未支付，订单已被关闭，请重新下单！'];
            } else if ($res['status']==0) {
                $res['title']='订单未支付';
                $res['desc']  = ['订单未支付，请尽快下单，超时未支付将被关闭！'];
            } else if ($res['status']==1) {
                $res['title'] ='支付成功';
                $res['desc']  = ['稍后，您将收到系统发送的包含「取胎码」的短信；', '同时，「取胎码」在订单详情页也可以查看哦！'];
                if($orderModel->source=='BOX') {
                    $res['desc']  = ['请回机柜屏幕核验订单，并尽快完成取胎操作；', '若取胎失败，系统会补发「取胎码」到您手机！'];
                }
                
            } else if ($res['status']>1) {
                $res['title'] ='订单完成';
                $res['desc']  = ['订单已经完成，谢谢您的支持！'];
            }
        } else{
            $this->redirect('/');
        }

        $this->layout = 'TrotoMain';
        return $this->render('troto_pay_result', [
            'signPackage' => Yii::$app->wechat->app->jssdk->buildConfig(['onMenuShareTimeline', 'onMenuShareAppMessage'], false),
            'res'         => $res,
        ]);
    }

    /**
     * 微信商城创建订单
     * 逻辑参考 /common/rpc/CabinetService.php :: func suborder
     */
    public function actionCreate() {
        $this->enableCsrfValidation = false;
        Yii::$app->response->format=Response::FORMAT_JSON;
        $resp = ['code'=>2001, 'msg'=>'INVALID REQUEST.', 'data'=>[]];

        $orderType = intval($this->request->post('type'));
        $regionid  = intval($this->request->post('regionid'));
        $cartids   = $this->request->post('cartid');

        //查询用户购物车数据
        $cartModel = new ShoppingCart();
        $cartGoods = $cartModel->getCartGoodsByUid($this->userinfo['uid'], 1);
        var_dump($cartGoods);
        if (count($cartGoods) === 0) {
            $this->redirect('/cart/');
        }

        //创建订单
        $SOModel  = new SO();
        $SOModel->ordersn   = SO::generateOrderSn();
        $SOModel->weid      = 0;
        $SOModel->status    = 0;
        $SOModel->uid       = $this->userinfo['uid'];
        $SOModel->mobile    = $this->userinfo['mobile'];
        $SOModel->source    = 'WX';  //WX-微信商城，BOX-机柜
        $SOModel->from_user = $this->userinfo['openid'];
        $SOModel->price     = 1000.98;
        $SOModel->taxtotal  = 50.08;
        $SOModel->goodsprice= 950.90;
        $SOModel->paytype    = 2; //1余额，2在线
        $SOModel->sendtype   = 2; //1上门安装，2自提
        $SOModel->goodstype  = 1;
        $SOModel->addressid  = 0;
        $SOModel->createtime = time();
        //根据区县编码，查询补齐省市
        if ($orderType==1 && $regionid) {
            $regionSelected = Regions::find()->where(['regionid'=>$regionid])
                                             ->orWhere(['regionid'=>substr($regionid,0,2).'0000'])
                                             ->orWhere(['regionid'=>substr($regionid,0,4).'00'])
                                             ->all();
            foreach ($regionSelected as $region) {
                if ($region->level==0) $SOModel->province = $region->name;
                else if ($region->level==1) $SOModel->city = $region->name;
                else if ($region->level==2) $SOModel->area = $region->name;
            }
            $SOModel->sendtype   = 1; //1上门安装，2自提
        }
        $SOModel->ext = '用户微信内创建订单';

        if ($SOModel->save()) {
            $orderId = $SOModel->id;
        }

        //添加订单商品记录
        $SOGModel = new ShoppingOrderGoods();

        //若订单成功生成 扣减库存

        //若订单成功生成 删除购物车记录
        $CartData = new ShoppingCart();
        foreach ($cartGoods as $product) {
            // $CartData->deleteGoodsById($this->userinfo['uid'], $product['id']);
        }

        if ($orderId) {
            $resp['code'] = 2000;
            $resp['data']['ordersn'] = $SOModel->ordersn;
        }

        return $resp;
    }

    private function createOrderGoods($goods, $order_id) {
        $ShoppingOrderModel = new ShoppingOrder();

        foreach ($goods as $product) {
            $orderGoodsModel = new ShoppingOrderGoods();
            $orderGoodsModel->orderid = $order_id;
            $orderGoodsModel->goodsid = $product['id'];
            $orderGoodsModel->total = $product['total'];
            $orderGoodsModel->price = $product['marketprice'];
            $orderGoodsModel->createtime = time();

            //设置默认值
            $orderGoodsModel->weid = 0;
            $orderGoodsModel->optionid = 0;
            $orderGoodsModel->cancelgoods = 0;
            $orderGoodsModel->status = 0;
            $orderGoodsModel->deleted = 0;

            $orderGoodsModel->save();

            //依据下单方式减库存
            $ShoppingOrderModel->reduceGoodsTotal($order_id, 'ORDER');
        }
    }

    //显示订单信息，并提供支付按钮，发起支付。
    public function actionCancel($order_id = null)
    {
        if ($order_id === null OR !is_numeric($order_id)) {
            return 'order_id param error!';
        }

        $order = ShoppingOrder::find()->where(['id' => $order_id])->one();

        if ($order === null) {
            return 'order not exist!';
        }

        if ($order->status != 0) {
            return 'order was paid, can not cancel.';
        }

        //取消订单
        $order->status = -1;
        $order->save();

        $this->redirect('/member/order');
    }


    public function actionDetail($order_id = null) {
        $this->layout = 'TrotoMain';
        return $this->render('troto_detail', []);

        $ShoppingOrderModel = new ShoppingOrder();
        if ($order_id === null OR !is_numeric($order_id)) {
            return 'orderid param error!';
        }

        $order = $ShoppingOrderModel::find()->where(['id' => $order_id])->one();

        if ($order === null) {
            return 'order not exist!';
        }

        //检查订单是否已支付
        if ($order->status == 0 and $_SERVER['HTTP_HOST'] === self::APP_DOMAIN_NAME) {
            if ($this->is_paid($order_id)) {
                $ShoppingOrderModel->paid($order_id);
            }
        }

        // 获取订单产品
        $uid = $this->userinfo['uid'];
        $orderGoodsModel = new ShoppingOrderGoods();
        $products = $orderGoodsModel->getByOrderId(Yii::$app->request->get('order_id'));
        
        foreach ($products as &$p) {
            $p['detail_url'] = '/goods/detail?id=' . $p['goodsid'] . '&uid=' . $uid;
        }


        //计算订单支付金额
        $order_price = $order->goodsprice;
        if ($order->coupon > 0) {
            $order_price -= $order->coupon;
        }

        //取订单的优惠券
        $Coupon = new Coupon();
        $coupon = $Coupon->getCouponByOrderId($order->id);
/*
        if ($coupon !== false) {
            $order_price -= $coupon['value'];
        }
*/
        return $this->render('detail', [
            'order' => $order,
            'taxtotal' => $order->taxtotal,
            'order_status_name' => ShoppingOrder::getStatusName($order->status),
            'orderGoods' => empty($products) ? 0 : $products,
            'coupon' => $coupon,
            'order_price' => $order_price
        ]);
    }

    public function actionLogistic($order_id) {
        // $sn = '9974410050478'; //测试
        // var_dump($logisticData = LogisticComponents::BdKuaidi($sn));exit();
        $orderid = intval($order_id);
        $orderDetail = ShoppingOrder::find()->where(['id'=>$orderid, 'uid'=>$this->userinfo['uid']])->asArray()->one();
        $orderDetail = ShoppingOrder::find()->where(['id'=>$orderid])->asArray()->one();
        if ($orderDetail==null || empty($orderDetail)) {
            // $this->redirect('/home/index');  //没用好像
            header('location: /home/index');exit();
        }
        //查询快递进度历史
        $logisticHistory = ShoppingOrderLogistic::find()
                                        ->where(['orderid'=>$orderid])
                                        ->asArray()
                                        ->one();
        //自提订单
        if((isset($orderDetail['sendexpress']) && $orderDetail['sendexpress']>0 && $orderDetail['expresssn']=='')){
            //已发货但未查到进度
            $logisticData['code']      = 200;
            $logisticData['process'][] = [
                    'time' => intval($orderDetail['sendexpress']),
                    'desc' => '您已通过自提方式取货，谢谢惠顾'
            ];
            $logisticData['company'] = [];

        //对于已经签收的历史订单直接查库
        } else if (isset($logisticHistory['finishdt']) && $logisticHistory['finishdt']) {
            $logisticData = json_decode($logisticHistory['history'], true);

        //如果发货超过3小时并且单号不为空则查询进度
        } else if(isset($orderDetail['sendexpress']) && $orderDetail['sendexpress']>0) {
            //已发货但未查到进度
            $logisticData['code']      = 200;
            $logisticData['process'][] = [
                    'time' => date('Y-m-d H:i:s', intval($orderDetail['sendexpress'])),
                    'desc' => '未查询到进度，等待快递平台更新同步'
            ];
            $logisticData['company'] = [];
            if (isset($orderDetail['expresssn']) && trim($orderDetail['expresssn'])!='' && ($orderDetail['sendexpress']+10800)<time()) {
                $logisticData = LogisticComponents::BdKuaidi(trim($orderDetail['expresssn']));
                if ($logisticData['code']==200) {
                    //对于已经签收的订单，记录快递进度详情
                    if (stripos($logisticData['process'][0]['desc'], '已签收')!==false) {
                        $model = new ShoppingOrderLogistic();
                        $model->orderid = $orderid;
                        $model->history = json_encode($logisticData);
                        $model->createdt = time();
                        $model->updatedt = time();
                        $model->finishdt = strtotime($logisticData['process'][0]['time']);
                        $model->save();
                    }
                } else {
                  $logisticData['code']      = 200;
                  $logisticData['process'][] = [
                          'time' => date('Y-m-d H:i:s', $orderDetail['sendexpress']),
                          'desc' => '快递小哥已经收货，暂未查到进度详情，请稍后再试'
                  ];
                  $logisticData['company'] = [];
                }
            }
        } else {
            $logisticData['code']      = 200;
            $logisticData['process'][] = [
                    'time' => date('Y-m-d H:i:s', $orderDetail['createtime']),
                    'desc' => '已经推送海关报审，请耐心等待'
            ];
            $logisticData['company'] = [];
        }

        return $this->render('logistic', [
                'order'    => $orderDetail,
                'logistic' => $logisticData,
            ]);
    }

    public function actionPaid($order_id = null)
    {
        $ShoppingOrderModel = new ShoppingOrder();
        if ($order_id === null OR !is_numeric($order_id)) {
            return 'orderid param error!';
        }

        $order = ShoppingOrder::find()->where(['id' => $order_id])->one();

        if ($order === null) {
            return 'order not exist!';
        }

        if ($order->status != 0) {
            usleep(300);
            $this->redirect('/order/detail?order_id='.$order_id);
            // return 'order status error!';
        }

        //检查订单是否已支付
        if ($_SERVER['HTTP_HOST'] === self::APP_DOMAIN_NAME) {
            if ($this->is_paid($order_id)) {
                $ShoppingOrderModel->paid($order_id);
            }
        } else {
            $ShoppingOrderModel->paid($order_id);
        }

        $this->redirect('/order/detail?order_id=' . $order_id);
    }

    public function actionDelivered($order_id = null)
    {
        if ($order_id === null OR !is_numeric($order_id)) {
            return 'orderid param error!';
        }

        $order = ShoppingOrder::find()->where(['id' => $order_id])->one();

        if ($order === null) {
            return 'order not exist!';
        }

        if ($order->status != 1) {
            //return 'order status error!';
        }

        $order->status = 3; //已收货
        $order->save();

        //分销提成状态更新为可提现
        BrokerOrderReward::updateAll(array('status' => 1), "orderid='$order_id'");
        $rewards = BrokerOrderReward::findAll(['orderid' => $order_id]);

        foreach ($rewards as $re) {
            $user = Users::find()->where(['uid' => $re->brokerid])->one();
            $user->credits6 += $re->reward_amount;
            $user->save();
        }

        $this->redirect('/order/detail?order_id=' . $order_id);
    }

    public function actionPaidError($order_id = null)
    {
        $ShoppingOrderModel = new ShoppingOrder();
        if ($order_id === null OR !is_numeric($order_id)) {
            return 'orderid param error!';
        }

        $order = ShoppingOrder::find()->where(['id' => $order_id])->one();

        if ($order === null) {
            return 'order not exist!';
        }

        //检查订单是否已支付
        if ($order->status == 0) {
            if ($this->is_paid($order_id)) {
                $ShoppingOrderModel->paid($order_id);
                $this->redirect('/order/detail?order_id=' . $order_id);
            }
        }

        return $this->render('paid_error', [
        ]);
    }

    public function actionPayCart() {
        $uid = $this->userinfo['uid'];

        $ShoppingAddressModel = new ShoppingAddress();
        $AddressModelData = $ShoppingAddressModel->getGoodsAddressByGoodsUid($uid, $isdefault = '');

        /*
        * 用户登录情况下去查询用户购物车表数据 则查询seesion购物车
        */
        if ($uid) {
            $CartData = new ShoppingCart();
            $CartModelData = $CartData->getCartGoodsByUid($uid);
        } else {
            $CartSessionData = Yii::$app->session->get('cartData');
        }

        return $this->render(($_SERVER['HTTP_HOST'] === self::APP_DOMAIN_NAME ? 'pay_prd' : 'pay_dev'), [
            'addressData' => $AddressModelData,
            'CartDataShow' => isset($CartModelData) ? $CartModelData : $CartSessionData
        ]);
    }

    //晒单现场
    public function actionShow()
    {
        return $this->render('show', [
        ]);
    }

    public function actionTest()
    {
        echo $this->getOrderSn();

        echo '<br/>';
        echo Func::guid();

        echo '-' . date('ymd', time()) . '-';

        $cache = Yii::$app->cache;

        $key = 'name';

        $data = $cache->get($key);

        if ($data === false) {
            echo 'set';

            // $data 在缓存中没有找到，则重新计算它的值

            // 将 $data 存放到缓存供下次使用
            $cache->set($key, 'Jack');
        }

        echo $data;
    }

    /**
     * 如果是子订单，订单金额就不用算了。
     *
     * @param $products
     * @param $order_sn
     * @return array
     */
    private function calOrderPrice($products, $order_sn)
    {
        $price = $taxtotal = 0;

        foreach ($products as $product) {
            $price += $product['marketprice'] * $product['total'];
            $taxtotal += number_format($product['marketprice']*$product['taxrate']/100,2)* $product['total'];
        }

        return [
            'goodsprice' => $price,
            'price'      => $price,
            'taxtotal'   => $taxtotal
        ];
    }

    /**
     * 创建订单
     * @param $orderGoods
     * @param $order_sn
     * @param $address_id 收货地址, null时获取默认地址
     */
    /**
     * [createOrder description]
     * @param  arrray $orderGoods [description]
     * @param  string $order_sn   [description]
     * @param  intval $address_id [description]
     * @param  intval $couponid   [description]
     * @param  intval $remark     订单备注
     * @return [type]             [description]
     */
    private function createOrder($orderGoods, $order_sn=null, $address_id=null, $couponid=null, $remark='')
    {
        $orderModel = $this->getDefaultOrderModel($address_id);

        if ($order_sn !== null) {
            if ($order_sn === 'single' || $order_sn === 'multi') {
                $orderModel->hassub_order = ($order_sn === 'single' ? 0 : 1); //有无子订单
            } else {
                $orderModel->parent_ordersn = $order_sn;
            }
        }

        //订单金额
        $orderPrice = $this->calOrderPrice($orderGoods, $order_sn);
        $orderModel->taxtotal   = $orderPrice['taxtotal'];
        $orderModel->goodsprice = $orderPrice['goodsprice'];
        $orderModel->price      = $orderModel->goodsprice + $orderModel->taxtotal;
        $orderModel->remark     = $remark;

        //首单优惠，主订单才有。
        if ($order_sn === null) {
            $firstOrderDiscount = $this->getFirstOrderDiscount(time());
            if ($firstOrderDiscount > 0) {
                $orderModel->coupon = $firstOrderDiscount;
                if ($orderModel->price >= $orderModel->coupon) {
                    $orderModel->price -= $orderModel->coupon;
                } else {
                    $orderModel->price = 0;
                }
            }
        }

        if ($order_sn === null) {
            $orderModel->status = ($orderModel->price == 0 ? 1 : 0);
        } else {
            $orderModel->status = 0;
        }

        //通用信息
        $uid = $this->userinfo['uid'];
        $orderModel->uid = $uid;
        $orderModel->ordersn = $this->getOrderSn();
        // $orderModel->transid = Func::guid();
        $orderModel->createtime = time();
        $orderModel->save();

        return $orderModel;
    }

    /**
     * 
     * @param $address_id, 用户收货地址，null时获取默认
     */
    private function getDefaultOrderModel($address_id = null)
    {
        $orderModel = new ShoppingOrder();
        $orderModel->weid = 0;
        $orderModel->from_user = '';
        $orderModel->sendtype = 0;
        $orderModel->paytype = 0;
        $orderModel->goodstype = 1;
        $orderModel->remark = '';
        $orderModel->addressid = 0;
        $orderModel->expresscom = '';
        $orderModel->expresssn = '';
        $orderModel->express = '';
        $orderModel->dispatchprice = '';
        $orderModel->dispatch = '';
        $orderModel->send = 0;
        $orderModel->cancelgoods = 0;
        $orderModel->sendexpress = 0;
        $orderModel->receipttime = 0;
        $orderModel->accomplish = 0;
        $orderModel->paymenttime = 0;
        $orderModel->community = 0;
        $orderModel->sid = 0;
        $orderModel->creditsettle = 0;
        $orderModel->id_no = '';
        $orderModel->id_name = '';
        $orderModel->coupon = 0;
        $orderModel->parent_ordersn = null;

        $AddressModel = new ShoppingAddress();
        $Address = $AddressModel->getAddressToShopping($this->userinfo['uid'], $address_id);
        $orderModel->realname   = $Address['realname'];
        $orderModel->mobile     = $Address['mobile'];
        $orderModel->province   = $Address['province'];
        $orderModel->city       = $Address['city'];
        $orderModel->area       = $Address['area'];
        $orderModel->address    = $Address['address'];
        $orderModel->id_no      = $Address['idno'];

        return $orderModel;
    }

    //测试用例
    public function actionGetFirstOrderDiscountTest()
    {
        $this->getFirstOrderDiscount(strtotime('2016-07-14 23:59:59'));
        $this->getFirstOrderDiscount(strtotime('2016-07-15 0:0:1'));
        $this->getFirstOrderDiscount(strtotime('2016-07-15 23:59:59'));
        $this->getFirstOrderDiscount(strtotime('2016-07-16 10:01:01'));
        $this->getFirstOrderDiscount(strtotime('2016-07-16 10:01:01'));
        $this->getFirstOrderDiscount(strtotime('2016-07-17 10:01:01'));
        $this->getFirstOrderDiscount(strtotime('2016-07-17 23:59:59'));
        $this->getFirstOrderDiscount(strtotime('2016-07-18 00:0:00'));
    }

    private function getFirstOrderDiscount($now)
    {
        return 0;
        $discountParam = Yii::$app->params['firstOrderDiscount'];
        $startTime = strtotime($discountParam['startTime']);
        $endTime = strtotime($discountParam['endTime']);

        $coupon = 0;

        if ($now > $startTime and $now < $endTime) {
            $ShoppingOrderModel = new ShoppingOrder();

            if ($ShoppingOrderModel->isFirstOrder($this->userinfo['uid'])) {
                $offset = floor(($now - $startTime) / (24 * 3600)) + 1;
                $coupon = $discountParam['day' . $offset];
            }
        }

        //echo date('Y-m-d H:i:s', $now) . ' - ' . $coupon . '<br/>';  //与测试用例配合使用

        return $coupon;
    }

    /**
     * 订单号生成测试用例
     */
    public function actionOrderSnTest()
    {
        for ($n = 0; $n < 30; $n++) {
            echo $this->getOrderSn() . '<br/>';
        }
    }


    /**
     * 订单系列号规则，示例：SDYK201607141129030030001
     *   1、字母代码没有规定。
     *   2、时间精确到毫秒。
     *   3、尾部系列号4位。
     * @return string
     */
    private function getOrderSn()
    {
        $cache = Yii::$app->cache;
        $key = 'order_sn';
        $order_sn = $cache->get($key);

        if ($order_sn === false) {
            $order_sn = 1;
        } else {
            if ($this->isNextDaySn()) { //订单系列号隔天从1开始，00:00切换。
                $order_sn = 1;
            } else {
                $order_sn++;
            }
        }

        $cache->set($key, $order_sn);

        return 'SDYK' . date('YmdHis') . $this->get_millisecond() . vsprintf('%04d', $order_sn);
    }

    private function isNextDaySn()
    {
        $cache = Yii::$app->cache;
        $day_now = date('Ymd');
        $key = 'day_sn';
        $day_sn = $cache->get($key);

        if ($day_sn === $day_now) {
            return false;
        } else {
            $cache->set($key, $day_now);
            return true;
        }
    }

    private function get_millisecond()
    {
        list($usec, $sec) = explode(" ", microtime());
        $msec = vsprintf('%03d', $usec * 1000);
        return $msec;
    }

    //私有方法，不用检查$order_id有效性。
    private function is_paid($order_id) {
        $trade_state = '';

        $order = ShoppingOrder::find()->where(['id' => $order_id])->one();

        if ($order) {
            $WeChat = new WeChat();
            $result = $WeChat->orderQuery($order->ordersn);
            if (isset($result['result_code']) && $result['result_code']=='SUCCESS') {
                $needPayTotal = $order->price - $order->coupon;
                if($result['trade_state']=='SUCCESS' && ($result['total_fee']/100)==$needPayTotal){
                    $trade_state = $result['trade_state'];
                }

                //只要有检查都记录支付日志
                $CoreWxpayLogModel = new CoreWxpayLog();
                $CoreWxpayLogModel->logWxpay($order->id, $result);
            }
        }
        return ($trade_state === 'SUCCESS');
    }

    public function actionPayQuery($order_id = null)
    {
        $order = ShoppingOrder::find()->where(['id' => $order_id])->one();

        $WeChat = new WeChat();
        $result = $WeChat->orderQuery($order->ordersn);

        var_dump($result);
        var_dump($this->is_paid($order_id));
    }


}
//end file