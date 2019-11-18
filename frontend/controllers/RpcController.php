<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;

use common\rpc\CabinetService;
use common\rpc\MaintenanceService;
use common\rpc\OrderService;

class RpcController extends Controller {
    public  $enableCsrfValidation = false;
    private $activeTime = 1440;
    private $ipArr = ['127.0.0.1','192.168.1.110'];
    private $container = [];

    public function init() {
        parent::init();
        $this->enableCsrfValidation = false;

        $this->container['Cabinet']     = new CabinetService();
        $this->container['Order']       = new OrderService();
        $this->container['Maintenance'] = new MaintenanceService();
    }

    public function actionIndex() {
        $service = Yii::$app->request->queryString ?:'Maintenance';
        // $service = $_SERVER['QUERY_STRING'] ?:'Maintenance';
        $service = ucfirst(strtolower($service));

        //验证调用服务的客户端IP
        // if (!in_array(Yii::$app->request->userIP, $this->ipArr)) {
        //     // return false;
        // }

        // //有效时间
        // if ((time() - $_SERVER['REQUEST_TIME']) > $this->activeTime) {
        //     return true;
        // }

        if (!isset($this->container[$service])) {
            return false;
        }

        //IMPORTANT! 终止yii-reponse发送处理逻辑，原始返回
        Yii::$app->response->send();

        try {
            $rpcServer = new \Yar_Server($this->container[$service]);
            $rpcServer->handle();
            Yii::$app->end();
        } catch (Exception $e) {
            return  $e->getMessage();
        }
    }

}
