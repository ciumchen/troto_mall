<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;

use common\components\WebSocketClient;
use common\models\SmsLog;
use common\models\ShoppingOrder;
use common\models\ShoppingOrderGoods;
use frontend\models\Users;

class WxpayController extends Controller {
    public $layout = 'blank';
    public $enableCsrfValidation = false;

    public function init(){
        parent::init();
        $this->enableCsrfValidation = false;
    }

    public function actionIndex() {
        Yii::$app->end();
        return $this->render('index', []);
    }

    public function actionNotify() {
        $wxpayLog  = dirname(dirname(__DIR__)) . '/logs/wxpay.log';
        $wxpayPost = file_get_contents('php://input');
        $wxpayRes  = simplexml_load_string($wxpayPost, 'SimpleXMLElement', LIBXML_NOCDATA);
        file_put_contents($wxpayLog, "\n\n".date('Y-m-d H:i:s'). "\n===================\n", FILE_APPEND);
        file_put_contents($wxpayLog, str_replace(['__set_state(array(','))'], '', var_export($wxpayRes, true))."\n", FILE_APPEND);

        //支付通知付款操作成功
        if($wxpayRes && $wxpayRes->result_code=='SUCCESS' && $wxpayRes->return_code=='SUCCESS') {
            //主动查询支付单结果
            $payRes = Yii::$app->wechat->payment->order->queryByOutTradeNumber($wxpayRes->out_trade_no);
            if ($payRes && $payRes['trade_state']==='SUCCESS') {
                //查询订单详细情况
                $orderModel = ShoppingOrder::find()->where(['ordersn'=>$wxpayRes->out_trade_no])->one();
                if ($orderModel->source=='BOX') {
                    $userInfo = Users::find()->where(['openid'=>$payRes['openid']])->one();
                }

                //校验订单状态和订单应付款额, 上线注意下两行处理
                // if ($orderModel && $orderModel->status<1 && $payRes['cash_fee']==$orderModel->price*100) {
                if ($orderModel && $orderModel->status<1) {
                    $userPickCode  = rand(100000, 999999);
                    $servePickCode = rand(100000, 999999);
                    $orderModel->status         = 1;
                    $orderModel->paytype        = 2;  //订单支付途径标注为在线
                    $orderModel->uid            = $orderModel->uid ?: $userInfo->uid;
                    $orderModel->from_user      = $orderModel->from_user ?: $payRes['openid'];
                    $orderModel->transid        = $payRes['transaction_id'];
                    $orderModel->paymenttime    = strtotime($payRes['time_end']);
                    $orderModel->pickcode_user  = $userPickCode;
                    $orderModel->pickcode_serve = '';
                    if ($orderModel->sendtype==1) {
                        $orderModel->pickcode_serve = $servePickCode;
                    }
                    $orderModel->ext            = $orderModel->ext.'^^用户完成支付('.date('Y-m-d H:i:s').')';
                    if ( $orderModel->save()) {
                        //$payRes['is_subscribe']为Y时候可以微信推送消息
                        echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                        //机柜下单需要推送支付结果
                        if ($orderModel->source=='BOX') {
                            $skus    = [];
                            $ogModel = new ShoppingOrderGoods;
                            $ogList  = $ogModel->getOrderGoodsList($orderModel->id);
                            if (isset($ogList[0])) {
                                $ogItem = $ogList[0];
                                $skus   = [
                                    "name"    => $ogItem['title'],
                                    "pathway" => $ogItem['pathway'],
                                    "sn"      => $ogItem['goodssn'],
                                    "sku"     => $ogItem['productsn'],
                                    "price"   => $ogItem['price'],
                                    "total"   => $ogItem['total'],
                                    "num"     => $ogItem['total'] - $ogItem['picknum'],
                                    "orderdt" => $orderModel->createtime,
                                    "pickdt"  => $ogItem['picktime'],
                                    "status"  => ShoppingOrder::getPickStatus($ogItem['total'], $ogItem['picknum']),
                                ];
                            }
                            $data = [
                                "action" => "broadcast/single",
                                "data"   => [
                                    "action"    => "cabinet/payres",
                                    "toDevices" => $orderModel->cabinetid,
                                    // "toDevices" => 1900000000,
                                    // "toFds" => [1,2,3,4,5,6,7,8,9,10,11,13],
                                    "data"      => [
                                        "ordersn" => strval($wxpayRes->out_trade_no),
                                        "timeout" => 180, //转入取胎操作超时时间
                                        "result"  => "PAY_FINISH",
                                        "sku"     => $skus
                                    ],
                                ],
                            ];
                            file_put_contents($wxpayLog, json_encode($data, JSON_UNESCAPED_UNICODE), FILE_APPEND);
                            $client = new WebSocketClient('119.23.167.82', 9555, 1000000000, 'TzkgMeDOlmWcuQ4R79CtPvqXSVnFB6If');
                            if (!$client->connect()) {
                                file_put_contents($wxpayLog, "\nConnect to WS failed, fail to push pay res.\n", FILE_APPEND);
                            }
                            if (!$client->send(json_encode($data, JSON_UNESCAPED_UNICODE))) {
                                file_put_contents($wxpayLog, "\nSend data to WS failed, fail to push pay res.\n", FILE_APPEND);
                            }
                        //其他途径下单推送取胎码给用户
                        } else {
                            $sendRs = SmsLog::send($userInfo->mobile,"您已完成订单({$orderModel->ordersn})支付，凭取胎码 {$userPickCode}，前往多轮多智能柜自助取胎。如遇问题，请致电".Yii::$app->params['serviceNumber']."协助！", SmsLog::TYPE_PICk);
                            file_put_contents($wxpayLog, "\n推送取胎短信 ".intval($sendRs), FILE_APPEND);
                            //上门安装服务订单，推送服务队
                            if ($orderModel->sendtype==1) {
                                // $sendRs = SmsLog::send($userInfo->mobile,"您已完成订单({$orderModel->ordersn})支付，凭取胎码 {$userPickCode}，前往多轮多智能柜自助取胎。如遇问题，请致电".Yii::$app->params['serviceNumber']."协助！", SmsLog::TYPE_PICk);
                                file_put_contents($wxpayLog, "\n推送服务队取胎短信 ".$servePickCode, FILE_APPEND);
                            }
                        }
                    }
                }
            }
        }
        exit;
    }

}


