<?php
namespace comapi\controllers;

use Yii;
use yii\web\Request;

class MaintenanceController extends BaseController {

    public function init() {
        parent::init();
        $this->checkAuth();
        $this->rpc = new \Yar_Client(Yii::$app->params['RpcServiceUri'].'?maintenance');
        $this->rpc->SetOpt(YAR_OPT_CONNECT_TIMEOUT, 3000);
        $this->rpc->SetOpt(YAR_OPT_TIMEOUT, 0);
    }

    /*
     * 补货开门
     * @param int phone 手机号
     * @param int password 密码
     * */
    public function actionVerify() {
        $phone    = intval(yii::$app->request->post('phone'));
        $password = intval(yii::$app->request->post('password'));
        $result = $this->rpc->verify($phone, $password, $this->deviceId);
        $this->resp['code'] = 2000;
        $this->resp['msg']  = 'ok';
        $this->resp['data'] = $result;
        return $this->resp;
    }
        /*
         * 补货开门
         * @param int phone 手机号
         * @param int password 密码
         * */
        public function actionVerify() {
            Yii::$app->response->format = $this->fromatJson;
            $resp = [
                'code' => '4003',
                'msg'  => 'Request Invalid',
                'data' => []
            ];
            $phone = yii::$app->request->post('phone');
            $password = yii::$app->request->post('password');
            // var_dump($phone, $password);die;
            if(is_numeric($phone) && is_numeric($password)) {
                try {
                    $data= $this->rpc->verify($phone, $password);
                    $resp['code'] = 2000;
                    $resp['msg'] = 'ok';
                    $resp['data'] = $data;
                } catch (Exception $e)
                {
                    $resp['code'] = 5001;
                    $resp['msg'] = $e->getMessage();
                }
            }
        }
        /*
         * 补货开门
         * @param int phone 手机号
         * @param int password 密码
         * */
        public function actionVerify() {
            Yii::$app->response->format = $this->fromatJson;;
            $resp = [
                'code' => '4003',
                'msg'  => 'Request Invalid',
                'data' => []
            ];
            $phone = yii::$app->request->post('phone');
            $password = yii::$app->request->post('password');
            // var_dump($phone, $password);die;
            if(is_numeric($phone) && is_numeric($password)) {
                try {
                    $data= $this->rpc->verify($phone, $password);
                    $resp['code'] = 2000;
                    $resp['msg'] = 'ok';
                    $resp['data'] = $data;
                } catch (Exception $e)
                {
                    $resp['code'] = 5001;
                    $resp['msg'] = $e->getMessage();
                }
            }
        }
    /*
     * 补货查询
     * @param int phone 手机号码
     * */
    public function actionReplenish() {
        $phone    = intval(yii::$app->request->post('phone'));
        $result = $this->rpc->replenish($phone, $this->deviceId);
        $this->resp['code'] = 2000;
        $this->resp['msg']  = 'ok';
        $this->resp['data'] = $result;
        return $this->resp;
    }
>>>>>>> .r60

    /*
     * 补货结果上报
     * @param string
     * */
    public function actionSubreplenish() {
        Yii::info(yii::$app->request->post(), 'com');
        $this->resp['code'] = 2000;
        $this->resp['msg']  = 'ok';
        return $this->resp;
    }

<<<<<<< .mine
        /*
         * 补货查询
         * @param int phone 手机号码
         * */
        public function actionReplenish() {
            Yii::$app->response->format = $this->fromatJson;
            $resp = [
                'code' => '4003',
                'msg'  => 'Request Invalid',
                'data' => []
            ];
            $phone = yii::$app->request->post('phone');
            // var_dump($phone, $password);die;
            if($phone && is_numeric($phone)) {
                try {
                    $data = $this->rpc->replenish($phone);
                    $resp['code'] = 2000;
                    $resp['msg'] = 'ok';
                    $resp['data'] = $data;
                } catch (Exception $e)
                {
                    $resp['code'] = 5001;
                    $resp['msg'] = $e->getMessage();
                }
            }

            return $resp;
        }

        /*
         * 补货结果
         * @param string
         * */
        public function actionSubreplenish() {
            Yii::$app->response->format = $this->fromatJson;
            $resp = [
                'code' => '4003',
                'msg'  => 'Request Invalid',
                'data' => []
            ];

            $data = yii::$app->request->post('data');
            // $replenishsn = yii::$app->request->post('replenishsn');
            // $tyres = yii::$app->request->post('tyres');
            // var_dump($tyres);die;
            if(is_string($data)) {
                try {
                    $data = $this->rpc->subreplenish($data);
                    $resp['code'] = 2000;
                    $resp['msg'] = 'ok';
                    $resp['data'] = $data;
                } catch (Exception $e)
                {
                    $resp['code'] = 5001;
                    $resp['msg'] = $e->getMessage();
                }
            }

            return $resp;
        }
    }||||||| .r50
        /*
         * 补货查询
         * @param int phone 手机号码
         * */
        public function actionReplenish() {
            Yii::$app->response->format = $this->fromatJson;;
            $resp = [
                'code' => '4003',
                'msg'  => 'Request Invalid',
                'data' => []
            ];
            $phone = yii::$app->request->post('phone');
            // var_dump($phone, $password);die;
            if($phone && is_numeric($phone)) {
                try {
                    $data = $this->rpc->replenish($phone);
                    $resp['code'] = 2000;
                    $resp['msg'] = 'ok';
                    $resp['data'] = $data;
                } catch (Exception $e)
                {
                    $resp['code'] = 5001;
                    $resp['msg'] = $e->getMessage();
                }
            }

            return $resp;
        }

        /*
         * 补货结果
         * @param string
         * */
        public function actionSubreplenish() {
            Yii::$app->response->format = $this->fromatJson;;
            $resp = [
                'code' => '4003',
                'msg'  => 'Request Invalid',
                'data' => []
            ];

            $data = yii::$app->request->post('data');
            // $replenishsn = yii::$app->request->post('replenishsn');
            // $tyres = yii::$app->request->post('tyres');
            // var_dump($tyres);die;
            if(is_string($data)) {
                try {
                    $data = $this->rpc->subreplenish($data);
                    $resp['code'] = 2000;
                    $resp['msg'] = 'ok';
                    $resp['data'] = $data;
                } catch (Exception $e)
                {
                    $resp['code'] = 5001;
                    $resp['msg'] = $e->getMessage();
                }
            }

            return $resp;
        }
    }=======
}>>>>>>> .r60
