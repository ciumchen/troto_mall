<?php
namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class CoreWxpayLog extends ActiveRecord {

    public static function tableName() {
        return '{{%core_wxpay_log}}';
    }

    /**
     * 记录微信支付日志
     * @param  intval $orderid    订单ID
     * @param  array  $payResult  微信支付结果，result_code为必须key
     * @param  intval $ordersn    订单号
     * @return bool
     */
    public function logWxpay($orderid, $payResult, $ordersn='') {
        $log = $this::find()->where(['orderid' => $orderid])->one();

        //不存在日志记录则插入
        if ($log === null) {
            $log = new self();
            $log->orderid     = $orderid;
            $log->outtradeno  = $ordersn;
            $log->createtime  = time();
            $log->successtime = 0;
        }

        $log->appid      = $payResult['appid'];
        $log->mchid      = $payResult['mch_id'];
        $log->resultcode = $payResult['result_code'];
        $log->sign       = $payResult['sign'];
        $log->noncestr   = $payResult['nonce_str'];
        $log->tradestate = $payResult['trade_state'];
        $log->openid     = isset($payResult['openid']) ? $payResult['openid'] : '';
        $log->totalfee   = isset($payResult['total_fee']) ? ($payResult['total_fee']/100) : '';
        $log->cashfee    = isset($payResult['cash_fee']) ? ($payResult['cash_fee']/100) : '';
        $log->feetype    = isset($payResult['fee_type']) ? $payResult['fee_type'] : '';
        $log->banktype   = isset($payResult['bank_type']) ? $payResult['bank_type'] : '';
        $log->transid    = isset($payResult['transaction_id']) ? $payResult['transaction_id'] : '';
        $log->timeend    = isset($payResult['time_end']) ? strtotime($payResult['time_end']) : 0;
        $log->tradetype  = isset($payResult['trade_type']) ? $payResult['trade_type'] : '';
        $log->attach     = isset($payResult['attach']) ? $payResult['attach'] : '';

        if ($payResult['trade_state'] === 'SUCCESS') {
            $log->successtime = strtotime($payResult['time_end']);
        }

        $log->lasttime = time();
        return $log->save();
    }

}