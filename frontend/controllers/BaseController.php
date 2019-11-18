<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;

/**
 * 基础校验过滤
 */
abstract class BaseController extends Controller {
    
    const APP_DOMAIN_NAME = 'mall.troto.com.cn';

    public $userinfo = [
        'uid'=>0, 'openid'=>'', 'gender'=>'', 'mobile'=>'13992807685', 'avatar'=>''
        // 'uid'=>1, 'openid'=>'ooTai53mgTnopUPrGwEvQwEyY6yU', 'gender'=>'1', 'mobile'=>'13312345678', 'avatar'=>'http://thirdwx.qlogo.cn/mmopen/vi_32/jxW2GPLfXcVfdVzWsPx0GbUrooaoNaI3gydehzhT5XBVpXGEhibbcyqXV5gzibzenVHdVP8tRsWWqqREDibcrsUEQ/132'
    ];
    public $request;
    public $response;
    public $resMsg;
    public $encryptUid;
 
    public function init() {
        parent::init();

        //模拟登陆写入
        if (!Yii::$app->session['userinfo']) {
            Yii::$app->session['userinfo'] = [
                    'uid'=>2, 'openid'=>'ooTai51o-3e5s1xLTI9b1627f-4E', 'gender'=>1, 'mobile'=>'18676719887', 'avatar'=>'http://thirdwx.qlogo.cn/mmopen/vi_32/DYAIOgq83erHxDeXVxrdmQgdKGetVNhYMticAbfMtibRlBZY4A5DPTRWr3hce0nmD5fpKJz0O0LZkwxX1pu9txFg/13'
                ];
        }


        //for debug
        if (isset($_COOKIE['debug_uid']) && $_COOKIE['debug_uid']==1 && in_array(Yii::$app->request->userIP, Yii::$app->params['debugIP'])) {
           $this->userinfo = ['uid'=>1, 'openid'=>'ooTai53mgTnopUPrGwEvQwEyY6yU', 'gender'=>'1', 'mobile'=>'13992807685', 'avatar'=>'http://thirdwx.qlogo.cn/mmopen/vi_32/jxW2GPLfXcVfdVzWsPx0GbUrooaoNaI3gydehzhT5XBVpXGEhibbcyqXV5gzibzenVHdVP8tRsWWqqREDibcrsUEQ/132'];
        }

        $this->request = ($this->request === null) ? Yii::$app->request : $this->request;
        $this->response = ($this->response === null) ? Yii::$app->response : $this->response;
        //$this->authent = ($this->authent === null) ? new AuthenticationComponents() : $this->authent;
        $this->resMsg = ['code' => -200, 'msg' => '网络异常'];

        $sessUserInfo = $this->userinfo = Yii::$app->session['userinfo'] ?: $this->userinfo;

        //未登录
        if (!$sessUserInfo['uid']) {
            if ($this->isWeixin()) {
                //session存用户默认打开的网址
                Yii::$app->session['_init_request_uri'] = Yii::$app->request->absoluteUrl;
                $this->redirect('/wechat/oauth-get');
                Yii::$app->end(); //结束用户验证信息程序
            } else {
                $this->redirect('/site/login?url=/member');
                Yii::$app->end(); //结束用户验证信息程序
            }
        }
        //未设置手机号
        if (!$sessUserInfo['mobile'] && Yii::$app->request->pathInfo!='member/user-bind') {
            Yii::$app->session['_init_request_uri'] = Yii::$app->request->absoluteUrl;
            $this->redirect('/member/user-bind');
            Yii::$app->end(); //结束用户验证信息程序
        }

        // Yii::$app->end(); //结束用户验证信息程序
    }

    private function isWeixin() {
        return (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false);
    }

}