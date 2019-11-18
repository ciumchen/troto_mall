<?php
namespace comapi\controllers;

use Yii;
use yii\web\Request;

class CabinetController extends BaseController {

    public function init() {
        parent::init();
        $this->checkAuth();
        $this->rpc = new \Yar_Client(Yii::$app->params['RpcServiceUri'].'?cabinet');
        $this->rpc->SetOpt(YAR_OPT_CONNECT_TIMEOUT, 3000);
        $this->rpc->SetOpt(YAR_OPT_TIMEOUT, 0);
    }

    /*
     * 错误信息上报
     */
    public function actionBreakdown() {
        Yii::info(yii::$app->request->post(), 'com');
        $this->resp['code'] = 2000;
        $this->resp['msg']  = 'ok';
        return $this->resp;
    }

    /*
     * 柜机产品信息
     */
    public function actionGoods() {
        $result = $this->rpc->goods($this->deviceId);
        $this->resp['code'] = 2000;
        $this->resp['msg']  = 'ok';
        $this->resp['data'] = $result;
        return $this->resp;
    }

    /**
     * 机柜信息查询
     */
    public function actionInfo() {
        $result = $this->rpc->info($this->deviceId);
        if(isset($result['secret'])) unset($result['secret']);
        $this->resp['code'] = 2000;
        $this->resp['msg']  = 'ok';
        $this->resp['data'] = $result;
        return $this->resp;
    }

    /**
     * 机柜轮播广告
     */
    public function actionAds() {
        $result = $this->rpc->ads($this->deviceId);
        $this->resp['code'] = 2000;
        $this->resp['msg']  = 'ok';
        $this->resp['data'] = $result;
        return $this->resp;
    }

    /*
     * 柜机转态上报
     * */
    public function actionStatus() {
        Yii::info(var_export(yii::$app->request->post(), true), 'com');
        $this->resp['code'] = 2000;
        $this->resp['msg']  = 'ok';
        return $this->resp;
    }

    /*
     * 提交临时订单
     */
    public function actionSuborder() {
        $orderData = [];
        $orderData['pathway'] = intval(yii::$app->request->post('pathway'));
        $orderData['num']     = intval(yii::$app->request->post('num'));
        $orderData['sku']     = trim(yii::$app->request->post('sku'));
        $result = $this->rpc->suborder($orderData, $this->deviceId);
        $this->resp['code'] = 2000;
        $this->resp['msg']  = 'ok';
        $this->resp['debug']  = $orderData;
        if (!is_array($result)){
            $this->resp['code'] = 2001;
            $this->resp['msg']  = '下单失败';
        }
        $this->resp['data'] = $result;
        return $this->resp;
    }

    /*
     * 取货验证
     * @param int
     * */
    public function actionVerifycode() {
        $code = intval(yii::$app->request->post('code'));
        $result = $this->rpc->verifycode($code, $this->deviceId);
        $this->resp['code'] = 2000;
        $this->resp['msg']  = 'ok';
        $this->resp['data'] = $result;
        return $this->resp;
    }

    /*
     * 取货结果上报
     * @param int
     * */
    public function actionSubpick() {
        $code      = intval(yii::$app->request->post('code'));
        $ordersn   = intval(yii::$app->request->post('ordersn'));
        $inventory = yii::$app->request->post('inventory');

        $result = $this->rpc->subpick($ordersn, $inventory);
        $this->resp['code'] = 2000;
        $this->resp['msg']  = 'ok';
        if (!$result) {
            $this->resp['code'] = 2003;
            $this->resp['msg']  = 'FAIL';
        }
        return $this->resp;
    }

}
