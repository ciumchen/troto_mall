<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Func;
use yii\httpclient\Client;

use dosamigos\qrcode\QrCode;

use common\models\User;
use frontend\models\Users as Members;
use frontend\models\WeChat;
use frontend\models\ShoppingOrder;
use frontend\models\CoreWxpayLog;
use frontend\models\CouponChengdu;

/**
 * wechat controller
 */
class WechatController extends Controller {
    
    const APP_DOMAIN_NAME = 'mall.troto.com.cn';

    public $log_file_path = '';
    public $enableCsrfValidation = false;

    public function init() {
        parent::init();
        $this->log_file_path = dirname(__DIR__) . '/logs/wechat.log';
    }

    public function actionWxpayNotify() {
        //记录推送日志
        $wxpaylog = dirname(__DIR__) . '/logs/wxpay.log';
        $postData = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postDataObj = simplexml_load_string($postData, 'SimpleXMLElement', LIBXML_NOCDATA);
        file_put_contents($wxpaylog, date('Y-m-d H:i:s'). "\n====================\n", FILE_APPEND);
        file_put_contents($wxpaylog, $postData . "\n", FILE_APPEND);

        $wxpayNotify = $postDataObj;
        if (isset($wxpayNotify->out_trade_no)) {
            $ShoppingOrderModel = new ShoppingOrder();
            $WeChatModel = new WeChat();
            $order = $ShoppingOrderModel::find()->where(['ordersn'=>$wxpayNotify->out_trade_no])->asArray()->one();
            //只有订单存在且订单状态为未支付时候才查询订单并操作订单和分成
            if (isset($order['status']) && $order['status']==0) {
                $payResult = $WeChatModel->orderQuery($order['ordersn']);
                if (isset($payResult['result_code'])){
                    //只要有检查都记录支付日志
                    $CoreWxpayLogModel = new CoreWxpayLog();
                    $logRs = $CoreWxpayLogModel->logWxpay($order['id'], $payResult,$order['ordersn']);
                    //成功支付订单了
                    $needPayTotal = $order['price']-$order['coupon'];
                    if($payResult['trade_state']=='SUCCESS' && ($payResult['total_fee']/100)==$needPayTotal){
                        $ShoppingOrderModel->paid($order['id']);
                        $OrderModel = new ShoppingOrder();
                        $orderOne = $OrderModel::findOne($order['id']);
                        $orderOne->transid = $payResult['transaction_id'];
                        $orderOne->paymenttime = strtotime($payResult['time_end']);
                        $orderOne->save();
                        //如果是父订单，更新子订单信息
                        if ($orderOne->hassub_order) {
                            ShoppingOrder::updateAll(['transid'=>$payResult['transaction_id'], 'paymenttime'=>strtotime($payResult['time_end'])], ['parent_ordersn'=>$order['ordersn']]);
                            //查询子订单id
                            $childOrder = $OrderModel::find()->select('id')
                                                    ->where(['parent_ordersn'=>$order['ordersn']])
                                                    ->asArray()->all();
                            foreach ($childOrder as $childOrderOne) {
                                $OrderModel->reduceGoodsTotal($childOrderOne['id'], 'PAYOFF');
                            }
                        } else {
                            $OrderModel->reduceGoodsTotal($order['id'], 'PAYOFF');
                        }
                    }
                    //记录日志
                    if ($logRs) {
                        echo '<xml>
                                <return_code><![CDATA[SUCCESS]]></return_code>
                                <return_msg><![CDATA[OK]]></return_msg>
                              </xml>';
                    } else {
                        echo '<xml>
                            <return_code><![CDATA[FAIL]]></return_code>
                            <return_msg><![CDATA[]]></return_msg>
                          </xml>';
                    }  
                }
                file_put_contents($wxpaylog, var_export($payResult, true) . "\n", FILE_APPEND); 
            } else {
                echo '<xml>
                        <return_code><![CDATA[SUCCESS]]></return_code>
                        <return_msg><![CDATA[OK]]></return_msg>
                      </xml>';
    }
        } else {
            echo 'ERROR_INVALID_REQUEST';
        }
        file_put_contents($wxpaylog, "===========================\n\n", FILE_APPEND);
    }

    public function actionIndex() {
        return 'ERROR_CONNECT_FAILED';
    }
    
    public function actionOrderNotify() {
        $users = [
            // 'o2mt-wYSWtd5-Xl7JRhnto_ojm_s', //63-dan
            'o2mt-wRqOosY4_1kkAuOeeXdvF-8', //110
            'o2mt-wSSwSx0kLAsN6zm5bw0daP0', //120
        ];

        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.WeChat::token();
        foreach ($users as $openid) {
            $msg = "【TEST】Hello~\n 有新订单咯，详情信息：~~~";
            $msgTxt = '{"touser":"'.$openid.'", "msgtype":"text", "text":{"content":"'.$msg.'"}}';
            WechatCallbackObj::httpRequest($url, true, $msgTxt);
            $msgTxt = '{"touser":"'.$openid.'", "msgtype":"image", "image":{"media_id":"aA2j8L3A45M-ppmt6qBUdV_WLw8fVnpSADZLvOX0w6g"}}';
            WechatCallbackObj::httpRequest($url, true, $msgTxt);
        }
    }

    public function actionEvent() {
        //修改服务器响应地址时用。
        if (isset($_GET["echostr"])) {
            return $_GET["echostr"];
        }
        file_put_contents($this->log_file_path, date('Y-m-d H:i:s').'请求过来了' . "\n", FILE_APPEND);

        $wechatObj = new WechatCallbackObj();
        $wechatObj->responseMsg($this); //响应消息的
    }

    //网页授权：1 请求code
    public function actionOauthGet($type='base') {
        //授权后重定向的回调链接地址
        $rebackUrl = Yii::$app->request->hostInfo.'/wechat/oauth-response';
        return Yii::$app->wechat->app->oauth->scopes(['snsapi_userinfo'])->redirect($rebackUrl);
        // $this->redirect($url);
    }

    //网页授权：2 接收code    redirect_uri/?code=CODE&state=STATE。
    public function actionOauthResponse() {

        $authUserInfo = Yii::$app->wechat->app->oauth->user();
        //通过openid判断是否为会员
        //=是会员，则设置登入信息；不是会员，新建账号
        $member = Members::find()->where(['openid' => $authUserInfo->id])->one();

        if ($member==null || empty($member)) {
            $member = new Members;
            $member->openid     = $authUserInfo->original['openid'];
            $member->unionid    = $authUserInfo->original['unionid']?:NULL;
            $member->nickname   = $authUserInfo->original['nickname'];
            $member->gender     = $authUserInfo->original['sex'];
            $member->avatar     = $authUserInfo->original['headimgurl'];
            $member->remark     = '';
            $member->status     = 1;
            $member->username   = microtime(true).rand(0,999);
            $member->password   = microtime(true);
            $member->salt       = rand(100000, 999999);
            $member->createtime = $member->updatedt = time();
            $member->joindate   = time();
            $member->lastvisit  = time();
            $member->lastip     = Yii::$app->request->userIP;
            $member->joinip     = Yii::$app->request->userIP;
            $rs = $member->save();
            if (!$rs) {
                Yii::info('[EROOR] '.date('Y-m-d H:i:s')." 新用户授权访问登录出错！\n");
            }
        }

        //设置登录信息
        Yii::$app->session['userinfo'] = [
            'uid'     => $member->uid,
            'openid'  => $member->openid,
            'unionid' => $member->unionid,
            'gender'  => $member->gender,
            'avatar'  => $member->avatar,
            'mobile'  => $member->mobile,
        ];

        $rebackUrl = isset(Yii::$app->session['_init_request_uri']) ? Yii::$app->session['_init_request_uri'] : '/';
        $this->redirect($rebackUrl);
        Yii::$app->end(); //结束用户验证信息程序
    }

    public function actionCreateMenu() {
        $button = array(
            'button' => array(
                array(
                    'type' => 'view',
                    'name' => '进入商城',
                    'url' => 'http://'.self::APP_DOMAIN_NAME
                )
            )
        );

        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . WeChat::token();
        $result = $this->get_curl($url, $button);

        var_dump($result);
    }

    // 使用示例：<img src="/wechat/qr-test" />
    public function actionQrTest() {
        $outfile = false;
        $level = 3; // 0-L  1-M   2-Q  3-H 
        $size = 3;
        $margin = 4;
        $saveAndPrint = false;

        return QrCode::png('http://weixin.qq.com/q/ojqHN9-lSPkxmzWlxxIe', $outfile,
            $level,
            $size,
            $margin,
            $saveAndPrint);
    }


    /**
     * 获取微信授权链接
     */
    public function actionUserInfo($redirect_uri = 'http://'.self::APP_DOMAIN_NAME.'/wechat/re-token') {
        $redirect_uri = urlencode($redirect_uri);
        $AppID = Yii::$app->params['wechat']['AppID'];
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$AppID}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
        $this->redirect($url);
    }

    public function actionGetAccessToken() {
        //对调用url的代理验证IP（待完善）
        // Yii::$app->request->userIP
        $secret = Yii::$app->request->get('secret','');
        
        echo WeChat::token();
        exit();
    }

}

/**
 * WechatCallback object
 */
class WechatCallbackObj {

    const ERROR_NOTICE_OPENID = 'o2mt-wRqOosY4_1kkAuOeeXdvF-8';
    // const ERROR_NOTICE_OPENID = 'ofH2Awa0f3SRDJr7dNBMyoJIKFww';
    const ERROR_NOTICE_TEMPLATE = 'H8ef33fo9FA3p6xkA_Y3BSjY7875_NGall4sImbc4d4';
    // const ERROR_NOTICE_TEMPLATE = 'FX9UpiSLFLno60veJzcvXgyfznkR7AJpDi8A8orDDd4';

    public function valid() {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        }
    }

    public function mergerImg($imgs) {
        list($max_width, $max_height) = getimagesize($imgs['bgimg']);
        $dests = imagecreatetruecolor($max_width, $max_height);
 
        //背景图
        $dst_im = imagecreatefrompng($imgs['bgimg']);
        imagecopy($dests,$dst_im,0,0,0,0,$max_width,$max_height);
        imagedestroy($dst_im);
 		
 		//二维码
        $src_im = imagecreatefrompng($imgs['qrimg']);
        $src_info = getimagesize($imgs['qrimg']);
        imagecopy($dests,$src_im,230,670,0,0,$src_info[0],$src_info[1]);
        imagedestroy($src_im);

        //设置头像
        $header_img = imagecreatefromjpeg($imgs['avatar']);
        $herder_info = getimagesize($imgs['avatar']);
        imagecopy($dests,$header_img,100,65,0,0,$herder_info[0],$herder_info[1]);
        imagedestroy($header_img);

        //画弧图像
        $circle_img = imagecreatefrompng($imgs['circle']);
        $circle_info = getimagesize($imgs['circle']);
        imagecopy($dests,$circle_img,0,0,0,0,$circle_info[0],$circle_info[1]);
        imagedestroy($circle_img);
 
        header("Content-type: image/jpeg");
        imagejpeg($dests, $imgs['dstimg']);
	}

	/*
	 * 图片类型转换
	 //使用方法：
		$im=imagecreatefromjpeg("./111.jpeg");//参数是图片的存方路径
		$maxwidth="190";//设置图片的最大宽度
		$maxheight="190";//设置图片的最大高度
		$name="mmmmmmmm";//图片的名称，随便取吧
		$filetype=".jpeg";//图片类型
		resizeImage($im,$maxwidth,$maxheight,$name,$filetype);//调用上面的函数
	 *
	*/
	public function resizeImage($im,$maxwidth,$maxheight,$name,$filetype='.jpeg'){
        $im = imagecreatefromjpeg($im);
        $pic_width = imagesx($im);
        $pic_height = imagesy($im);

        if(($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight)) {
            if($maxwidth && $pic_width>$maxwidth) {
                $widthratio = $maxwidth/$pic_width;
                $resizewidth_tag = true;
            }

            if($maxheight && $pic_height>$maxheight) {
                $heightratio = $maxheight/$pic_height;
                $resizeheight_tag = true;
            }

			if($resizewidth_tag && $resizeheight_tag) {
				if($widthratio<$heightratio) $ratio = $widthratio;
				else $ratio = $heightratio;
			}

            if($resizewidth_tag && !$resizeheight_tag) $ratio = $widthratio;
            if($resizeheight_tag && !$resizewidth_tag) $ratio = $heightratio;

            $newwidth = $pic_width * $ratio;
            $newheight = $pic_height * $ratio;

			if(function_exists("imagecopyresampled")) {
				$newim = imagecreatetruecolor($newwidth,$newheight);//PHP系统函数
				imagecopyresampled($newim,$im,0,0,0,0,$newwidth,$newheight,$pic_width,$pic_height);//PHP系统函数
			} else {
				$newim = imagecreate($newwidth,$newheight);
				imagecopyresized($newim,$im,0,0,0,0,$newwidth,$newheight,$pic_width,$pic_height);
			}

            // $name = $name.$filetype;
            header("Content-type: image/jpeg");
            imagejpeg($newim,$name);
            imagedestroy($newim);
        } else {
            // $name = $name.$filetype;
            header("Content-type: image/jpeg");
            imagejpeg($im,$name);
        }
	}

    /**
     * 通过openid获取用户信息
     * @param  string $openid 用户openid
     * @return array
     */
    private function getUserInfoByOpenid($openid) {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.WeChat::token().'&openid='.$openid.'&lang=zh_CN';
        $rs = self::httpRequest($url, false);
        return $rs ? $rs :[];
    }

    /**
     * 建立帐户
     * @param  object  $access_token_obj 微信传递过来消息
     * @param  integer $brokerid         分享者UID
     * @return intval
     */
    private function createAccount($access_token_obj, $brokerid=0) {
        $usersModel = new Members;
        //检查用户是否存在
        $userDetail = $usersModel->get(['openid'=>$access_token_obj->FromUserName]);
        if (isset($userDetail->uid)) {
            return $userDetail->uid;
        } else {
            $new_user = [];
            $userInfo = $this->getUserInfoByOpenid($access_token_obj->FromUserName);
            $userInfo = json_decode($userInfo);
            if ($userInfo) {
                $new_user['brokerid'] = $brokerid;
                $new_user['username'] = microtime(true);
                $new_user['nickname'] = isset($userInfo->nickname) ? trim($userInfo->nickname) : '十点一刻海淘粉';
                $new_user['unionid']  = isset($userInfo->unionid) ? $userInfo->unionid : null;
                $new_user['openid']   = $userInfo->openid;
                $new_user['sex']      = isset($userInfo->sex) ? $userInfo->sex : '';
                $new_user['avatar']   = isset($userInfo->headimgurl) ? $userInfo->headimgurl : '/images/hader.png';
                if ($new_user['nickname']) {
                    $new_user['nickname'] = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $new_user['nickname']);
                }
                $new_user['nickname'] = ($new_user['nickname']!='') ? $new_user['nickname'] : '十点一刻海淘粉';

            }
            return $usersModel->newAccount($new_user)->uid;
        }
    }

    public function customDefaultReply($openid) {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.WeChat::token();
        $msg = '亲爱的十点一刻海淘粉，如果您有问题或者需要客服帮助，请添加官方客服微信: shidianyike1015，或拨打客服电话：0755-88900080，客服小淘会在线为您服务哦。';
        $msgTxt = '{"touser":"'.$openid.'", "msgtype":"text", "text":{"content":"'.$msg.'"}}';
        WechatCallbackObj::httpRequest($url, true, $msgTxt);
        $msgTxt = '{"touser":"'.$openid.'", "msgtype":"image", "image":{"media_id":"aA2j8L3A45M-ppmt6qBUdV_WLw8fVnpSADZLvOX0w6g"}}';
        WechatCallbackObj::httpRequest($url, true, $msgTxt);
    }

    public function responseMsg($CI) {
        file_put_contents($CI->log_file_path, date('Y-m-d H:i:s').'请求被传见处理函数了' . "\n\n", FILE_APPEND);
        //获取微信推送的消息通知数据
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        file_put_contents($CI->log_file_path, $postStr . "\n==========\n", FILE_APPEND);  //记录原始数据，方便排错
        if (empty($postStr)) {
            echo "";
            exit;
        }

        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        file_put_contents($CI->log_file_path, var_export($postObj, true) . "\n\n", FILE_APPEND);

        //如果是用户发送的文本消息，则回复自动消息。
        if ($postObj->MsgType=='text' || $postObj->MsgType=='image') {
            $this->customDefaultReply($postObj->FromUserName);
            // $this->txt_reply($postObj, $CI);
        } else if ($postObj->MsgType == 'event') {
            //用户取关
            if ($postObj->Event == 'unsubscribe') {
                $user = Members::find()->where(['openid'=>$postObj->FromUserName])->one();
                if ($user && isset($user->uid)) {
                    $user->follow = 0;
                    $user->unfollowtime = time();
                    $user->save();
                    exit();
                }
                $txtContent = '无数海淘粉的证明，海淘还是十点一刻好哦，粉宝贝忍心离开吗？';
                echo $this->send_msg_txt($postObj, $txtContent);
            //用户关注
            } else if ($postObj->Event == 'subscribe') {
                $currentTime = time();
                //场景（带参二维码）
                if (isset($postObj->EventKey) && stripos($postObj->EventKey, 'qrscene_')!==FALSE) {
                    //数字：qrscene_123，字符：qrscene_tom
                    $this->createAccount($postObj, str_replace('qrscene_', '', $postObj->EventKey));
                    if ($currentTime>1481336100 && $currentTime<1483197300) {
                        $picContent[] = [
                            'title' => '发现最美',
                            'desc' => '圣诞将至，我从意大利和日本给大家带来了两个小礼物！虽然简单，但却真实，将最“真”的礼物带给大家···',
                            'pic'  => 'http://cdn.10d15.com/christmas-2016.jpg',
                            'url'  => 'http://mp.weixin.qq.com/s/KMQu7eSFixwNlAf2uzr6uA'
                        ];
                        $this->send_msg_article($postObj, $picContent);
                    } else {
                        $txtContent = '相信我，海淘还是十点一刻好！';
                        echo $this->send_msg_txt($postObj, $txtContent);
                    }
                //普通关注
                } else {
                    $this->createAccount($postObj);
                    //情人节活动
                    if ($currentTime>1481336100 && $currentTime<1483197300) {
                        $picContent[] = [
                            'title' => '发现最美',
                            'desc' => '圣诞将至，我从意大利和日本给大家带来了两个小礼物！虽然简单，但却真实，将最“真”的礼物带给大家···',
                            'pic'  => 'http://cdn.10d15.com/christmas-2016.jpg',
                            'url'  => 'http://mp.weixin.qq.com/s/KMQu7eSFixwNlAf2uzr6uA'
                        ];
                        $this->send_msg_article($postObj, $picContent);
                    //3.8妇女节关注活动早十点至晚6点
                    } else if ($currentTime>1488934800 && $currentTime<1488967200) {
                        $womensDayPic = '9oUHPVyeptIhnpb_XeyNgj-r5PMkRjFr_GEynUW3GzKHWqYbT3LvstLSOFpWwCdR';
                        echo $this->send_msg_img($postObj, $womensDayPic);
                    } else {
                        $txtContent = "欢迎关注十点一刻  /::*\n\n告诉我，你为什么关注十点一刻海淘馆？\n\n";
                        $txtContent.= "是不满国内的低品质商品，还是无从选择？\n\n那么现在就开启你的海淘之旅！";
                        echo $this->send_msg_txt($postObj, $txtContent);
                    }
                }
            //点击事件
            } else if ($postObj->Event == 'CLICK') {
                switch ($postObj->EventKey) {
                    case "MY_INIVITE_QR" :
                        //根据用户openid获取用户用户uid,nickname,avatar信息
                        $userInfo = Members::find()->where(['openid'=>$postObj->FromUserName])->asArray()->one();
                        // $userInfo = Members::find()->where(['openid'=>'o2mt-wfFlUduZLEwFm4SgJ6vUQpk'])->asArray()->one();
                        if (empty($userInfo)) {
                            $txtContent = "后台出错了，请稍后再试！";
                            echo $this->send_msg_txt($postObj, $txtContent);
                            //同时通知开发人员
                            $errorNotice = [
                                    'first' => ['value'=>"用户[".$postObj->FromUserName."]点击了生成广告图功能，但是没查到用户信息，直接回复了文本消息！", 'color'=>'#000099'],
                                    'keyword1' => ['value' => '用户点击公众号菜单', 'color'=>'#000099'],
                                    'keyword2' => ['value' => $_SERVER['SERVER_ADDR'], 'color'=>'#000099'],
                                    'keyword3' => ['value' => '没查到用户', 'color'=>'#000099'],
                                    'keyword4' => ['value' => 'ERROR_NO_USER','color'=>'#000099'],
                                    'keyword5' => ['value' => date('Y-m-d H:i:s'),'color'=>'#000099'],
                                    'remark'   => ['value' => '十点一刻海淘生活馆 业务系统通知！','color'=>"#000099"]
                            ];
                            $noticeRs = self::sendTemplateMsg(self::ERROR_NOTICE_OPENID, self::ERROR_NOTICE_TEMPLATE, $errorNotice);
                            if (!$noticeRs) {
                                file_put_contents($CI->log_file_path, '非法用户'.$postObj->FromUserName.'菜单点击事件请求失败，告警通知'.self::ERROR_NOTICE_OPENID.'失败！' . "\n\n", FILE_APPEND);
                            }
                            exit();
                        } else {
                            //检查用户头像是否存在，不存在或者7天内没变化则拉取最新
                            $avatarImg = dirname(__DIR__) . '/web/uploads/avatar/'.md5($userInfo['uid']).'.jpg';
                            if (!file_exists($avatarImg) || (filemtime($avatarImg)<(time()-86400*7))) {
                                if (stripos($userInfo['avatar'], 'http')!==FALSE) {
                                    $avatarImgData = self::httpRequest($userInfo['avatar'], false);
                                } else {
                                    $avatarImgData = file_get_contents(dirname(__DIR__) . '/web/uploads/avatar/hader.png');
                                }
                                $fp = fopen($avatarImg, 'w');
                                if ($fp) {
                                    fwrite($fp, $avatarImgData);
                                    fclose($fp); 
                                } else {
                                    file_put_contents($CI->log_file_path, date('Y-m-d H:i:s')."拉取用户到本地操作，写入文件 {$avatarImg}，句柄打开失败！" . "\n\n", FILE_APPEND);
                                }
                            }

                            //头像需要按照指定的尺寸
                            $this->resizeImage($avatarImg, 185, 185, $avatarImg);

                            //获取用户专属的推广二维码
                            $userid = $userInfo['uid'];
                            $wechatInvaiteQrObj = WeChat::followQrcode($userid);
                            $outfile = true;
                            $level = 3; // 0-L  1-M   2-Q  3-H
                            $size = 3;
                            $margin = 4;
                            $saveAndPrint = true;
                            $userInviteQrImg = dirname(__DIR__) . '/web/uploads/user_invite_qr/'.md5($userid).'.png';
                            QRcode::png($wechatInvaiteQrObj->url, $userInviteQrImg, 'L', 9, 2);

                            //合并图片
                            $userInviteImg = dirname(__DIR__) . '/web/uploads/user_invite_img/'.md5($userid).'.png';
                            //如果图片不存在或者生成时间超过30天重新生成
                            // if ( (!file_exists($userInviteImg)) || (filemtime($userInviteImg)<(time()-86400*30)) ) {
                                $imgs = [
                                    'bgimg'  => dirname(__DIR__).'/web/uploads/user_invite/invite-bg-layer.png',
                                    'qrimg'  => $userInviteQrImg,
                                    'avatar' => dirname(__DIR__) . '/web/uploads/avatar/'.md5($userid).'.jpg',
                                    'circle' => dirname(__DIR__).'/web/uploads/user_invite/invite-avatar-layer.png',
                                    'dstimg' => $userInviteImg
                                ];
                                // $imgs['avatar'] = dirname(__DIR__) . '/web/uploads/avatar/0.png'; //会出错，延后调兼容
                                $this->mergerImg($imgs);//合并图像
                            // }

                            //上传图片到公众平台并返回media_id
                            $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.WeChat::token().'&type=image';
                            $postResult = self::httpRequest($url, true, array('media'=>'@'.$userInviteImg), 1500);
                            file_put_contents($CI->log_file_path,(date('Ymd H:i:s').$postResult."\n\n"), FILE_APPEND);
                            $postResultObj = json_decode($postResult);
                            file_put_contents($CI->log_file_path,date('Ymd H:i:s').$postResultObj->media_id."\n\n", FILE_APPEND);

                            //图片回复消息给用户
                            echo $this->send_msg_img($postObj, $postResultObj->media_id);
                            break;
                        }
                    case "V1001_HELLO_WORLD" :
                        //要返回相关内容
                        break;
                    case "V1001_GOOD" :
                        //要返回相关内容
                        break;
                }
            }
            //$this->event_reply($postObj, $CI);
        }
    }

    //自动回复用户发送的文本消息
    function txt_reply($postObj, $CI) {
        $keyword = trim($postObj->Content);

        if ($keyword == 'test') {
            $this->send_msg_txt($postObj, 'I am Tom.');
        } else {
            echo '';
        }

        exit;

        $CI->load->model('cfg_wx_settings_model');
        $keyword_reply = $CI->cfg_wx_settings_model->findKeyword('auto_txt_' . $keyword);

        //判断有无关键字
        $reply = false;

        if ($keyword_reply) {
            $reply = $keyword_reply;
        } else {
            $default_reply = $CI->cfg_wx_settings_model->getRowByName('auto_txt');

            //有默认设置吗
            if ($default_reply) {
                $reply = $default_reply;
            }
        }

        //回复消息还是图文
        if ($reply) {
            $value = json_decode($reply->VALUE);
            if ($value->type == 'txt') {
                $this->send_msg_txt($postObj, $value->data);
            } else {
                $this->send_msg_article($postObj, $value->data);
            }
        }
    }


    /**
     * 1、响应菜单的点击事件
     * 2、关注微信号事件
     */
    private function event_reply($postObj, $CI) {
        $CI->load->model('cfg_wx_settings_model');

        $event_name = 'auto_event_' . $postObj->Event;

        if ($postObj->Event == 'CLICK') {
            $event_name .= ($postObj->EventKey == '' ? '' : '_' . $postObj->EventKey);
        }

        $reply = $CI->cfg_wx_settings_model->findKeyword($event_name);

        //回复消息还是图文
        if ($reply) {
            $value = json_decode($reply->VALUE);
            if ($value->type == 'txt') {
                $this->send_msg_txt($postObj, $value->data);
            } else {
                $this->send_msg_article($postObj, $value->data);
            }
        }
    }

    public function send_msg_img($postObj, $media_id) {
        $tpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[%s]]></MsgType>
                <Image>
                <MediaId><![CDATA[%s]]></MediaId>
                </Image>
                </xml>";

        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;

        $resultStr = sprintf($tpl, $fromUsername, $toUsername, time(), 'image', $media_id);

       return $resultStr;
    }

    /**
     * 给指定的openid用户发送模板消息
     * @param  string $openid     接受者openid
     * @param  string $templateId 模板ID
     * @param  array  $data       消息内容数组
     * @param  string $url        消息详情页
     * @return bool
     */
    public static function sendTemplateMsg($openid, $templateId, $message, $url="") {
        $log_file_path = dirname(__DIR__) . '/logs/wechat.log';
        $postUrl = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.WeChat::token();
        $data['touser']      = $openid;
        $data['template_id'] = trim($templateId);
        $data['url']         = $url;
        $data['data']        = $message;
        $response = self::httpRequest($postUrl, true, json_encode($data));
        $response = json_decode($response);
        if ($response->errcode) {
            file_put_contents($log_file_path, "### 发送模板消息发生错误 ###\n", FILE_APPEND);
            file_put_contents($log_file_path, var_export($response, true) . "\n\n", FILE_APPEND);
            return false;
        } else {
            return true;
        }
    }

    public function send_msg_txt($postObj, $msg) {
        $tpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType>text</MsgType>
                <Content><![CDATA[%s]]></Content>
                <FuncFlag>0</FuncFlag>
                </xml>";

        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;

        $resultStr = sprintf($tpl, $fromUsername, $toUsername, time(), $msg);

       return $resultStr;
    }

    private function send_msg_article($postObj, $msg) {
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;

        $msgTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>";
        $resultStr = sprintf($msgTpl, $fromUsername, $toUsername, time());

        $resultStr .= "<ArticleCount>" . count($msg) . "</ArticleCount> ";
        $resultStr .= "<Articles>";

        $itemTpl = "<item>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <PicUrl><![CDATA[%s]]></PicUrl>
                        <Url><![CDATA[%s]]></Url>
                    </item>";
        foreach ($msg as $item) {
            if (is_array($item)) {
                $resultStr .= sprintf($itemTpl, $item['title'], $item['desc'], $item['pic'], $item['url']);
            } else {
                $resultStr .= sprintf($itemTpl, $item->title, $item->desc, $item->pic, $item->url);
            }
        }

        $resultStr .= "</Articles></xml>";

        echo $resultStr;
    }


    private function checkSignature() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
	
	//根据option_id 获取用户id
	private function OpenidUid($openid){
		//$re =  Members::find()->where(['openid' => $openid])->one();
		return 3;
	}

    private function groups_members($postObj, $CI) {
        $scene_id = substr($postObj->EventKey, 8);

        $CI->load->model('cfg_wx_group_map_model');
        $group_id = $CI->cfg_wx_group_map_model->get_group_id($scene_id);

        if ($group_id === false) return;

        $body = '{
            "openid":"' . $postObj->FromUserName . '",
            "to_groupid": ' . $group_id . '
        }';

        $url = 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=' . $CI->car->get_access_token();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);
    }


	/*
	 * http-curl操作封装
	 * @param $url string
	 * @param $postType bool 是否使用post方式请求
	 * @param $postData array 请求数据
	 * @param $timeout  int 操作超时时间
	 * @return bool
	 */
	public static function httpRequest($url, $postType=true, $postData=array(), $timeout=1000){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');

        if ($postType) {
            if (is_array($postData) && !empty($postData)) {
              $filepost = false;
              foreach ($postData as $name => $value) {
                if (substr($value, 0, 1) == '@') {
                  $filepost = true;
                  break;
                }
              }
              if (!$filepost) {
                $postData = http_build_query($postData);
              }
            }
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        $data  = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);
        //如果发生curl错误则记录日志
        if($errno) {
            file_put_contents($this->log_file_path, '[ERROR] '.date('Y-m-d H:i:s')." CURL发生错误。{$errno} - {$error}\n", FILE_APPEND);
            return false;
        } else {
            return $data;
        }
	}

}
