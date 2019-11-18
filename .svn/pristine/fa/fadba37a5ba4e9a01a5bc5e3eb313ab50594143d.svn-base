<?php
namespace console\controllers;

use yii;
use yii\db\Query;
use yii\console\Controller;
use common\models\ShoppingOrder;
use common\models\ShoppingOrderGoods;
use common\models\CabinetPathway;

use console\models\Members;

/**
 * 订单处理
 */
class OrderController extends Controller{
    const AUTO_RECEIPT_DAY = 7;  //发货 7 天后自动收货
    // const AUTO_CANCLE_DAY  = 2;  //提交订单 2 天内未支付则取消
    const AUTO_CANCLE_MIN  = 20;  //提交订单 20 分钟内未支付则取消
    const AUTO_DELETE_DAY  = 5;  //取消状态订单 3 天后删除

    /**
     * 订单自动收货
     * PS:使用：/mnt/projs/proj_10d15/trunk/yii order/auto-receipt>>/var/log/stat-order-receipt.log
     */
    public function actionAutoReceipt(){
        $logFile = dirname(dirname(__FILE__)).'/logs/OrderAutoReceipt.log';
         file_put_contents($logFile, '[INFO]'.date('Y-m-d H:i:s')." ======自动处理完成收货订单开始========\n", FILE_APPEND);
        try {
            $MembersModel = new Members();
            $current = time()-3600*24*(self::AUTO_RECEIPT_DAY);

            // 获取所有{self::AUTO_RECEIPT_DAY}天前可待收货的订单数据
            $list = ShoppingOrderGoods::find()->select("id, sendexpress")->where(['status' => 2])->andwhere(['<', 'sendexpress', $current])->orderBy('sendexpress asc')->asArray()->all();

            foreach ($list as $order) {
                // 自动收货， 并加上自动收货时间
                ShoppingOrderGoods::updateAll(['status' => 3, 'receipttime' => time()], 'id = ' . $order['id']);

                // 获取订单的分成日志
                $rewards = BrokersOrderReward::find()->where(['orderid' => $order['id']])->asArray()->all();
                foreach ($rewards as $item) {
                    if($item['status'] == 0){
                        // 将可分层的金额加入用户可用分成金额
                        BrokersOrderReward::updateAll(['status' => 1], 'id = ' . $item['id']);
                        Yii::$app->db->createCommand("UPDATE ".$MembersModel::tableName()." SET credits6=credits6+{$item['reward_amount']} WHERE uid={$item['brokerid']}")->execute();
                        file_put_contents($logFile, '[INFO]'.date('Y-m-d H:i:s')." 订单编号：{$order['id']}，分成用户ID：{$item['brokerid']}获得{$item['reward_amount']}，分成用户。自动收货结算完成。\n", FILE_APPEND);
                    }else{
                        file_put_contents($logFile, '[EROOR]'.date('Y-m-d H:i:s')." 订单编号：{$order['id']}，分成用户ID：{$item['brokerid']}，已分成：{$item['reward_amount']}。\n", FILE_APPEND);
                    }
                }
            }
        } catch (Exception $e) {
            file_put_contents($logFile, '[EROOR]'.date('Y-m-d H:i:s').' '.$e->getMessage()."\n", FILE_APPEND);
        }
         file_put_contents($logFile, '[INFO]'.date('Y-m-d H:i:s')." ======自动处理完成收货订单结束========\n", FILE_APPEND);
    }

	/**
	 * 超过 N 分钟未支付订单被取消
	 * PS:使用：/mnt/projs/proj_10d15/trunk/yii order/auto-cancle
	 */
	public function actionAutoCancle() {
        Yii::info('启动订单超时未支付检查任务。', 'order');

        $checkDtPoint = time()-60*(self::AUTO_CANCLE_MIN);
        $needCancleOrders = ShoppingOrder::find()->select('id,ordersn,status')
                                                 ->where(['status'=>0])
                                                 ->andWhere(['<', 'createtime', $checkDtPoint])
                                                 // ->asArray()
                                                 ->all();
        Yii::info('查到需要取消订单('.count($needCancleOrders).')笔。', 'order');

        $ShoppingOrderGoodsModel = new ShoppingOrderGoods();
        $ShoppingOrderModel = new ShoppingOrder();
        $CabinetPathwayModel = new CabinetPathway();
        $connection  = Yii::$app->db;
        $processCounter = 0;
        foreach ($needCancleOrders as $orderItem) {
            //根据订单商品表查询机柜轨道数据，并完成库存回滚
            //echo $orderItem->id.PHP_EOL;
            $goods = $ShoppingOrderGoodsModel->getOrderGoodsList($orderItem->id);
            //var_dump($goods);die;
            foreach ($goods as $value) {
                $goodsid = $value['goodsid'];
                $total = $value['total'];
                $orderid = $value['orderid'];
            }
            $cabinetList = CabinetPathway::find()->select('pathwayid,cabinetid,pathway,stock,goodsid')
                                                       ->where(['goodsid' => $goodsid])
                                                       ->all();
            foreach ($cabinetList as $vo) {
                $stock = $vo['stock'];
                $pathway = $vo['pathway'];
                $cabinetid = $vo['cabinetid'];
            }
            if ($goodsid == $goodsid) {
                $num = $total+$stock;
            }
            $sql = "UPDATE " . CabinetPathway::tableName() . " SET stock={$num} WHERE goodsid={$goodsid} and pathway={$pathway}";
            $command = $connection->createCommand($sql)->execute();
            //标注订单状态，取消标志位-1用户取消，-2系统取消
            $extText = '^^^系统取消超时未支付订单';
            $sql = "UPDATE " . ShoppingOrder::tableName() . " SET status=-2,ext=CONCAT(ext,'{$extText}') WHERE id={$orderid}";
            $command = $connection->createCommand($sql)->execute();
            //记录操作日志
            if ($command) {
                $processCounter++;
                Yii::info('取消订单('.$orderItem->ordersn.')成功。', 'order');
                //echo '取消订单('.$orderItem->ordersn.')成功。'.PHP_EOL;
            } else {
                Yii::info('取消订单('.$orderItem->ordersn.')失败。', 'order');
            }
            
        }

        Yii::info('结束订单超时未支付检查任务，成功取消('.$processCounter.')笔。', 'order');
        // print_r($needCancleOrders);
	}

	/**
	 * 取消超过N天的订单被删除
	 * PS:使用：/mnt/projs/proj_10d15/trunk/yii order/auto-delete>>/var/log/stat-order.log
	 */
	public function actionAutoDelete() {
		$logFile = dirname(dirname(__FILE__)).'/logs/OrderAuto.log';
		$current = time()-3600*24*(self::AUTO_DELETE_DAY);
		try {
			// 获取所有{self::AUTO_DELETE_DAY}天前可待收货的订单数据
			$list = ShoppingOrderGoods::find()->select("id")->where(['status'=>'-1'])->andwhere(['<=', 'createtime', $current])->asArray()->all();
			$ShoppingOrderModel = new ShoppingOrderGoods();
			$cancleTotal = 0;
			foreach ($list as $ov) {
				$rs = $ShoppingOrderModel->updateOrderById(['deleted'=>1], $ov['id']);
				if ($rs) {
					$cancleTotal++;
				}
			}
			file_put_contents($logFile, '[INFO]'.date('Y-m-d H:i:s')." 取消订单逾期被删除数量：{$cancleTotal}。\n", FILE_APPEND);
		} catch (Exception $e) {
			file_put_contents($logFile, '[EROOR]'.date('Y-m-d H:i:s').' '.$e->getMessage()."\n", FILE_APPEND);
		}
	}


    /**
     * 统计用户消费数据
     */
    public function actionUserSpending(){
        $logFile = dirname(dirname(__FILE__)).'/logs/UserPay.log';
        try {
            $MembersModel = new Members();
            // 获取所有拥有上级的用户id
            $list = Members::find()->select("uid")->where(['!=','brokerid','0'])->asArray()->all();
            foreach ($list as $item) {
                //统计当前用户支付完成总金额
                $orders = ShoppingOrderGoods::find()->select("price")->where(['uid'=>$item['uid']])->andwhere(['status'=>3])->all();
                $money=0;
                foreach ($orders as $key => $order) {
                    $money+=$order['price'];
                }
                Members::updateAll(['credits2' => $money], 'uid = ' . $item['uid']);
                file_put_contents($logFile, '[INFO]'.date('Y-m-d H:i:s').' uid：'.$item['uid']."  money:".$money."\n", FILE_APPEND);
            }
        } catch (Exception $e) {
            file_put_contents($logFile, '[EROOR]'.date('Y-m-d H:i:s').' '.$e->getMessage()."\n", FILE_APPEND);
        }
    }
}
