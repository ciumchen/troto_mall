<?php
namespace comapi\controllers;

use yii;
use yii\web\Controller;
use yii\web\Request;
use yii\web\Response;

class BaseController extends Controller {

    public $_yac;
    public $rpc;
    public $deviceId;
    public $resp = [
    	'code' => 4003,
    	'msg' => 'INVALID REQUEST.',
    	'data' => [],
    ];

    public  function init(){
        parent::init();

        ini_set("yar.debug",'off');
        $this->_yac = new \Yac('trt_ws_');

        $this->enableCsrfValidation = false;
        Yii::$app->response->format=Response::FORMAT_JSON;
    }

    /**
     * 检测登录
     */
    public function checkAuth(){
        if (Yii::$app->request->headers->has('access-device')) {
            $deviceId = intval(Yii::$app->request->headers->get('access-device'));
            if (Yii::$app->request->headers->has('access-token')) {
                //session in yac
                $devices = $this->_yac->get('api_device');
                if (isset($devices[$deviceId]) && $devices[$deviceId]['token']==Yii::$app->request->headers->get('access-token')) {
                    $this->deviceId = $deviceId;
                    return true;
                }
            }
        }
        //for debug
        if (Yii::$app->request->userIP=='113.87.182.223'){
            $this->deviceId = 1923000001;
            return true;
        }

        // return false;
        Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
        return $this->resp;
        exit;
    }

    /**
     * 授权登录
     */
    public function actionAuth() {
        $newToken = md5(microtime().rand(1,999999));
        $deviceId = intval(yii::$app->request->post('deviceid'));
        $token    = base64_decode(yii::$app->request->post('token'));
        $token    = strlen($token)=='16' ? $token : '';

        //验证授权
        if ($deviceId && $token) {
            //根据deviceid 查询设备信息(包含通信密钥)
            $this->rpc = new \Yar_Client(Yii::$app->params['RpcServiceUri'].'?cabinet');
            $this->rpc->SetOpt(YAR_OPT_CONNECT_TIMEOUT, 3000);
            $this->rpc->SetOpt(YAR_OPT_TIMEOUT, 0);
            try {
                $res = $this->rpc->info($deviceId);
            } catch (Exception $e) {
                echo "Exception: ", $e->getMessage();
            }

            //验证通信密钥
            $hash1 = substr(md5(trim($res['secret']).date('YmdHi')), 8 ,16);
            $hash2 = substr(md5(trim($res['secret']).date('YmdHi',strtotime('-1 minute'))), 8 ,16);
        // $this->resp['h1'] = base64_encode($hash1);

            if ($hash1==$token || $hash2==$token) {
                //set session in yac
                $devices = $this->_yac->get('api_device');
                $devices[$deviceId] = ['token'=>$newToken, 'expiredt'=>time()+7200];
                $this->_yac->set('api_device', $devices);
                $this->resp['code'] = 2000;
                $this->resp['msg']  = 'ok';
                $this->resp['data'] = $devices[$deviceId];
                // if (Yii::$app->request->headers->has('postman-token')) { $this->resp['data']['header']=Yii::$app->request->headers; }
            }
        }
        return $this->resp;
    }

}
