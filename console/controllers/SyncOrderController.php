<?php
namespace console\controllers;

use yii;
use yii\helpers\Json;
use yii\console\Controller;
use console\models\Orders;
use console\models\ShoppingGoods;
use console\models\ShoppingOrderGoods;
/*
 * 定时任务取消超过 30min 未支付订单
 *
 * */
class SyncOrderController extends Controller
{
    public function actionUpdate()
    {
        $logFile = dirname(dirname(__FILE__)).'/logs/Order.log';
        $checktime = 1800;
        $_startTime = time();
        file_put_contents(dirname(dirname(__FILE__)).'/logs/SyncOrder.log', '[INFO]'.date('Y-m-d H:i:s')." 取消30min未支付订单任务启动。\n", FILE_APPEND);
        $OrderGoodsModel = new ShoppingOrderGoods();
        $current = time() - $checktime;
        try{
            // 查询所有 30min 内未支付订单数据
            $ShoppingOrderModel = new Orders();
            $list = ShoppingOrderGoods::find()->select('id,orderid,goodsid,total')->where(['status'=>0])->andwhere(['<=', 'createtime', $current])->asArray()->all();
            // var_dump($list);die;
            foreach ($list as $v)
            {
                $rs = $ShoppingOrderModel->updateOrderById(['status'=>-1, 'delect'=>1], $v['orderid']);
                $goodstotal = $v['total'];
            }
        }catch (\Exception $e) {
            file_put_contents($logFile, '[EROOR]'.date('Y-m-d H:i:s').' '.$e->getMessage()."\n", FILE_APPEND);
        }
    }
}