<?php
namespace frontend\controllers;

use Yii;
use yii\db\Query;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\helpers\BaseArrayHelper;
use dosamigos\qrcode\QrCode;

use frontend\controllers\BaseController;

use common\models\SmsLog;
use frontend\models\Users;
use frontend\models\Broker;
use frontend\models\Coupon;
use frontend\models\UploadForm;
use frontend\models\ImsShoppingAdv;
use frontend\models\WeChat;
use frontend\models\ShoppingOrder;
use frontend\models\ShoppingOrderGoods;
use frontend\models\ShoppingCollect;
use frontend\models\ShoppingAddress;
use frontend\models\Corpteam;
use frontend\models\CorpteamDriver;
use frontend\models\MembersCreditsLog;

/**
 * 个人中心
 */
class MemberController extends BaseController {

    public $layouts = 'TrotoMain';
    public $enableCsrfValidation = false;
    public $stts = ['success' => 0, 'msg' => '', 'data' => []];

    public function actionT() {
        $this->layout = 'blank';

        // print_r(Yii::$app->geohash->decode('ws0br4utb5u'));
        var_dump(Yii::$app->geohash->encode( 113.780952, 22.68096 ));

        // var_dump(Yii::$app->wechat->payment);
        var_dump(Yii::$app->wechat->app->access_token->getToken());

        // var_dump( get_class_methods( Yii::$app->wechat->app->user ) );
        // var_dump( Yii::$app->wechat->app->user->list() );
        // var_dump( Yii::$app->wechat->app->user->get('ooTai53mgTnopUPrGwEvQwEyY6yU') );
    }

    public function actionIndex() {
        $this->layout = 'TrotoMain';
        return $this->render('troto_index', [
            'userInfo' => Users::find()->where(['uid'=>$this->userinfo['uid']])->one(),
            'signPackage' => Yii::$app->wechat->app->jssdk->buildConfig(['onMenuShareTimeline', 'onMenuShareAppMessage'], false),
        ]);
    }

    /**
     * 微信用户绑定手机号
     */
    public function actionUserBind() {
        $this->layout = 'blank';
        $validCode = SmsLog::generateCode();
        $cachekey  = 'sms.usage.'.date('ymd').'.'.$this->userinfo['uid'];

        //默认表单页
        if (Yii::$app->request->isGet) {
            return $this->render('userBind', []);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        //获取验证码
        if (Yii::$app->request->isAjax && Yii::$app->request->post('action')=='getcode') {
            //检查用户登录
            if (!isset($this->userinfo['uid']) || !$this->userinfo['uid']) {
                return ['code'=>4003, 'msg'=>'用户未登录，请先授权微信登录', 'data'=>[]];
            }
            //检查用户手机号绑定情况
            if ($this->userinfo['mobile']) {
                return ['code'=>4001, 'msg'=>'该用户已经绑定手机号到微信', 'data'=>[]];
            }
            //检查手机号绑定情况
            if (is_numeric(Yii::$app->request->post('mobile'))) {
                $checkUser = Users::find()->where(['mobile'=>Yii::$app->request->post('mobile')])->one();
                if ($checkUser) {
                    return ['code'=>4002, 'msg'=>'该手机号已经绑定其他微信用户', 'data'=>[]];
                }
            }
            //检查用户当日短信请求是否超每日最大配额
            $curUserDaySmsUsage = Yii::$app->cache->get($cachekey);
            if ($curUserDaySmsUsage!==false && (count($curUserDaySmsUsage)+1)>Yii::$app->params['sms.dayLimit']) {
                return ['code'=>4003, 'msg'=>'用户未登录，请先授权微信登录'];
            }

            //发送验证码 string(23) "1,201908071709234970866"
            $curUserDaySmsUsage = $curUserDaySmsUsage!==false ? array_push($curUserDaySmsUsage, ['dt'=>time(), 'code'=>$validCode]) : [['dt'=>time(), 'code'=>$validCode]];
            // var_dump($curUserDaySmsUsage);die;
            Yii::$app->cache->set($cachekey, $curUserDaySmsUsage, (strtotime(date('Y-m-d 00:00:00', strtotime('+1 days'))) - time()));
            $sendRs = SmsLog::send(Yii::$app->request->post('mobile'),'您正在添加手机号绑定，验证码 '.$validCode.'，有效期30分钟。', SmsLog::TYPE_BIND);
            if ($sendRs) {
                return ['code'=>2000, 'msg'=>'ok', 'data'=>[]];
            }
            // var_dump(Yii::$app->session);die;
        //处理提交，绑定信息
        } else if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $curUserDaySmsUsage = Yii::$app->cache->get($cachekey);
            if ($curUserDaySmsUsage) {
                $validCodeInfo = end($curUserDaySmsUsage);
                if (time()>($validCodeInfo['dt']+1800)) {
                    return ['code'=>4005, 'msg'=>'验证码已过期，请重新获取', 'data'=>[]];
                } else if ($validCodeInfo['code']!=Yii::$app->request->post('code')) {
                    return ['code'=>4003, 'msg'=>'无效验证码已过期，请重新获取', 'data'=>[]];
                } else {
                    $rs = Users::updateAll(['mobile'=>Yii::$app->request->post('mobile'), 'updatedt'=>time()], 'uid='.$this->userinfo['uid']);
                    if ($rs) {
                        //更新登录用户手机信息
                        // $userinfo = Yii::$app->session['userinfo'];
                        // $userinfo['mobile'] = Yii::$app->request->post('mobile');
                        // Yii::$app->session['userinfo']['mobile'] = $userinfo;
                        $this->userinfo['mobile'] = Yii::$app->request->post('mobile');
                        Yii::$app->session['userinfo'] = $this->userinfo;
                        return ['code'=>2000, 'msg'=>'信息绑定成功', 'data'=>['redirect'=>Yii::$app->session['_init_request_uri']?:'/']];
                    } else {
                        return ['code'=>2001, 'msg'=>'操作发生故障，请稍后再试', 'data'=>[]];
                    }
                }
            }
        }
    }

    /**
     * 主卡发钱
     * @return [type] [description]
     */
    public function actionHandout() {
        $this->layout = 'TrotoMain';
        // 根据主卡用户 uid 查出车队下面的副卡用户
        $expire = Yii::$app->request->get('expire', '');
        $expire = $expire=='' ? $expire : intval($expire);
        $corpteamModel = new Corpteam();
        $usersModel = new Users(); 
        $corpteamDriverModel = new corpteamDriver();
        $MembersCreditsLogModel = new MembersCreditsLog();
        $corpteamList = $corpteamModel->getCorpteam($this->userinfo['uid'], $expire);
        
        // 获取副卡用户分配总金额及最近分配金额的时间
        foreach ($corpteamList as $key => $distItem) {
            $sumAmount = $MembersCreditsLogModel->sumCreditsAmount($distItem['uid'],$distItem['driverid']);
            $createTime = $MembersCreditsLogModel->getCreateTime($distItem['uid'],$distItem['driverid']);
            $corpteamList[$key]['amount'] = $sumAmount['amount'];
            $corpteamList[$key]['createtime'] = $createTime['createtime'];
        }
        return $this->render('troto_handout', [
            'expire'       => $expire,
            'corpteamList' => $corpteamList,
            'signPackage'  => '',
            //'signPackage' => Yii::$app->wechat->app->jssdk->buildConfig(['onMenuShareTimeline', 'onMenuShareAppMessage'], false),
        ]);
    }

    /*
    * 主卡给副卡用户分配金额 HandoutHandl
    */
    public function actionHandl(){
        $this->layout = 'TrotoMain';
        $toDriverUid = intval(Yii::$app->request->get('uid'));
        $driverid = Yii::$app->request->get('driverid');
        $corpteamDriverModel    = new CorpteamDriver();
        if (Yii::$app->request->isAjax) {
            $deposit = floatval(Yii::$app->request->post('deposit'));
            $usersModel             = new Users();
            $corpteamModel          = new Corpteam();
            $corpteamDriverModel    = new CorpteamDriver();
            $vipUserInfo = Users::find()->where(['uid'=>$this->userinfo['uid']])->one();
            $vipUserList = $corpteamModel->getCorpteam($vipUserInfo['uid']);
            if (!$vipUserInfo || $vipUserInfo->status!=1 || $vipUserInfo->deposit<$deposit ) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['code'=>4003, 'msg'=>'当前主卡用户余额不足或者账户状态异常，请检查后重试！', 'data'=>[]];
            }

            //发放1:主卡账户(当前操作用户)减余额，成功记录日志
            $usersModel->deposit = $vipUserInfo->deposit - $deposit;
            $rechargeRs = $usersModel->updateAll(['deposit' => $usersModel->deposit],['uid' => $this->userinfo['uid']]);
            if ($rechargeRs) {
                $MembersCreditsLogModel = new MembersCreditsLog();
                $MembersCreditsLogModel->uid        = $this->userinfo['uid'];
                $MembersCreditsLogModel->type       = 4;
                $MembersCreditsLogModel->amount     = $deposit;
                $MembersCreditsLogModel->remarks    = "主卡用户[".$this->userinfo['uid']."]分配金额到[{$toDriverUid}]，金额：".$deposit;
                $MembersCreditsLogModel->createtime = time();
                $MembersCreditsLogModel->driverid = $driverid;
                $MembersCreditsLogModel->save();

            }
            //发放2:上一步操作成功则给副卡用户加余额 记录日志
            if ($rechargeRs) {
                $usersModel = new Users();
                $driverUserInfo = $usersModel->userInfo($toDriverUid);
                $usersModel['deposit'] = $driverUserInfo['deposit'] + $deposit;
                $rechargeRs = $usersModel->updateAll(['deposit' => $usersModel->deposit], ['uid' => $toDriverUid]);
                $MembersCreditsLogModel = new MembersCreditsLog();
                $MembersCreditsLogModel->uid        = $toDriverUid;
                $MembersCreditsLogModel->type       = 5;
                $MembersCreditsLogModel->amount     = $deposit;
                $MembersCreditsLogModel->remarks    = "副卡用户[".$toDriverUid."]分配到金额：".$deposit;
                $MembersCreditsLogModel->createtime = time();
                $MembersCreditsLogModel->driverid   = $driverid;
                $MembersCreditsLogModel->save();
            }
            if ($MembersCreditsLogModel && $MembersCreditsLogModel) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['code'=>2000, 'msg'=>'当前主卡用户分配['.$this->userinfo['uid']."]分配金额到[{$toDriverUid}]，金额：".$deposit];
            }
        }
        $usersModel     = new Users();
        $MembersCreditsLogModel = new MembersCreditsLog();
        $vipUserInfo    = Users::find()->where(['uid'=>$this->userinfo['uid']])->one();
        $driverUserList = $usersModel->userInfo($toDriverUid);
        $toDriverUser   = $corpteamDriverModel->getUserDriver($driverid);
        $corpteamUserList = $usersModel->userInfo($toDriverUser['uid']);
        $sumAmount = $MembersCreditsLogModel->sumCreditsAmount($toDriverUid,$toDriverUser['driverid']);
        $driverUserLog = $MembersCreditsLogModel->getCreditsLog($toDriverUid,$toDriverUser['driverid']);
        return $this->render('troto_handout_page', [
            'vipUserInfo'      => $vipUserInfo,
            'driverUserList'   => $driverUserList,
            'toDriverUser'     => $toDriverUser,
            'corpteamUserList' => $corpteamUserList,
            'driverUserLog'    => $driverUserLog,
            'sumAmount'        => $sumAmount,
            'signPackage'      => '',
                //'signPackage' => Yii::$app->wechat->app->jssdk->buildConfig(['onMenuShareTimeline', 'onMenuShareAppMessage'], false),
        ]);
    }

    /**
     * 用户积分记录
     * @return [type] [description]
     */
    public function actionCredits() {
        $this->layout = 'TrotoMain';
        return $this->render('troto_credits', [
            'signPackage' => Yii::$app->wechat->app->jssdk->buildConfig(['onMenuShareTimeline', 'onMenuShareAppMessage'], false),
        ]);
    }

    //用户名修改
    public function actionUserName() {
        $UserModel = new Users();
        $request = Yii::$app->request;

        if (!empty($request->post('submit'))) {

            $UserInfoData = $UserModel->userinfo($request->get('uid'), $request->post('nickname'));

            if (!empty($UserInfoData)) {
                Yii::$app->session->setFlash('error', "该<b>{$request->post('nickname')}</b>已存在");
                $this->redirect("/member/profile");
                return;
            }

            if (empty($request->get('uid'))) {
                return $this->redirect("/member/profile");
            }
            $states = $UserModel->updateUserInfo('nickname', $request->post('nickname'), $request->get('uid'));
            if ($states) {
                Yii::$app->session->setFlash('success', "修改昵称成功");
            } else {
                Yii::$app->session->setFlash('error', "修改昵称失败，请稍后重试");
            }
            $this->redirect("/member/profile");

        }

        return $this->render('username', [
            'UserInfo' => $UserModel->userinfo($this->userinfo['uid']),
        ]);
    }

    /**
     * 显示用户订单列表
     */
    public function actionOrder() {
        $status = intval($this->request->get('status', '-1'));

        //获取全部订单（包含两天内未支付的订单）
        if ($status=='') {
            # code...
        }
        //取订单信息
        $ShoppingOrderModel = new ShoppingOrder();
        $orders = $ShoppingOrderModel->getOrderByUid($this->userinfo['uid']);

        //取订单货品列表
        $orderGoodsModel = new ShoppingOrderGoods();
        foreach ($orders as &$order) {
            $order['goods'] = $orderGoodsModel->getByOrderId($order['id']);
        }

        $renderFile = 'order';
        if ($status!='-1') {
            $renderFile = $renderFile.'_'.$status;
        }
        return $this->render($renderFile, [
            'orderData'  => $orders,
            'statusList' => $ShoppingOrderModel::$orderStatus,
        ]);
    }

    /**
     * 用户中心-邀请好友页面
     */
    public function actionInvite() {
        if (isset(Yii::$app->session['userinfo']['uid'])) {
            $userinfo = Users::find()->where(['uid'=>Yii::$app->session['userinfo']['uid']])->asArray()->one();
        }
        if (empty($userinfo) ||  $userinfo['avatar']=='') {
            $userinfo['avatar'] = '/images/hader.png';
        }
        $WeChat = new WeChat();

        return $this->render('invite', [ 
            'userInfo'=>$userinfo,
            'signPackage' => $WeChat->getSignPackage(),
        ]);
    }

    // 使用示例：<img src="/wechat/qr-test" />
    public function actionInviteQrCode()
    {
        $qrcode = WeChat::followQrcode($this->userinfo['uid']);

        $outfile = false;
        $level = 3; // 0-L  1-M   2-Q  3-H
        $size = 3;
        $margin = 4;
        $saveAndPrint = false;

        return QrCode::png($qrcode->url, $outfile,
            $level,
            $size,
            $margin,
            $saveAndPrint);
    }

    public function actionCoupon()
    {
        $Coupon = new Coupon();
        $status_coupons = $Coupon->getListByStatus($this->userinfo['uid']);

        return $this->render('coupon', [
            'status_coupons' => $status_coupons
        ]);
    }

    public function actionAddress()
    {
        $UserAddressModel = new ShoppingAddress();

        return $this->render('address', [
            'UserAddress' => $UserAddressModel->getGoodsAddressByGoodsUid($this->userinfo['uid'], $isdefault = '')
        ]);
    }

    public function actionAddressData()
    {
        $searchStr = Yii::$app->request->get('q', '');
        $searchLevel = Yii::$app->request->get('l', 0);  //区域级别
        $data = [];
        echo json_encode($data);
        exit();
    }

    public function actionNewAddress() {
        $request = Yii::$app->request;

        $UserAddressModel = new ShoppingAddress();
        $UserAddressModel->uid       = $this->userinfo['uid'];
        $UserAddressModel->realname  = trim($request->post('realname'));
        $UserAddressModel->idno      = trim($request->post('idno'));  //身份证号
        $UserAddressModel->mobile    = trim($request->post('mobile'));
        $UserAddressModel->province  = trim($request->post('s_province'));
        $UserAddressModel->city      = trim($request->post('s_city'));
        $UserAddressModel->area      = trim($request->post('s_county'));
        $UserAddressModel->address   = trim($request->post('address'));
        $UserAddressModel->isdefault = ($request->post('isdefault') == 'on') ? 1 : 0;
        $UserAddressModel->insert();

        //如果是默认地址，则修改状态
        if ($request->post('isdefault') == 'on') {
            $UserAddressModel->isdefault($this->userinfo['uid'], $UserAddressModel->id);
        }
        if ($UserAddressModel->id) {
            Yii::$app->session->setFlash('success', '添加收货地址成功。');
        } else {
            Yii::$app->session->setFlash('error', '添加收货地址失败，请稍后重试。');
        }
        $this->redirect("/member/address");

    }

    public function actionAddressSubmit() {
        $request  = Yii::$app->request;
        $num      = $request->get('num');
        $addresid = intval($request->get('id'));

        $UserAddressModel = new ShoppingAddress();

        //如果该user有多个地址，那么就选择不等于的id之外都修改成不是默认地址
        if ($request->post('isdefault') == 'on') {
            $UserAddressModel->isdefault($this->userinfo['uid'], $addresid);
        }

        $updateStates = $UserAddressModel->UpdateAddress(
            $request->post('realname'),
            $request->post('idno'),
            $request->post('mobile'),
            $request->post('s_province'),
            $request->post('s_city'),
            $request->post('s_county'),
            $request->post('address'),
            $request->post('isdefault'),
            $addresid
        );

        if ($updateStates) {
            Yii::$app->session->setFlash('success', '修改成功。');
        } else {
            Yii::$app->session->setFlash('error', '修改失败，重复修改相同内容。');
        }

        $this->redirect("/member/addressedit?id={$addresid}&num={$num}");
    }

    public function actionAddressedit() {
        $addresid = intval(Yii::$app->request->get('id', 0));
        $uid = $this->userinfo['uid'];

        $AddressData = ShoppingAddress::find()->where(['id'=>$addresid, 'uid'=>$uid])->one();

        if (empty($AddressData)) {
            return $this->redirect('/member/address');
        }

        return $this->render('address_edit', [
            'edit' => $AddressData,
            'id' => $addresid,
            'num' => Yii::$app->request->get('num')
        ]);
    }

    public function actionDeleteAddress() {
        $addressid = intval(Yii::$app->request->get('id'));

        $delectStates = false;
        if ($addressid) {
            $UserAddressModel = new ShoppingAddress();
            $delectStates = $UserAddressModel->deleteAddress($addressid);
        }
        $ret['msg'] = $delectStates ? true : false;
        return json_encode($ret);
    }

    public function actionShareMoney()
    {
        $uid = $this->userinfo['uid'];
        $member = Users::find()->where(['uid' => $uid])->one();


        //取分销下线人员
        $Broker = new Broker();
        $allUnderLines = $Broker->getLevelMember($uid);

        //下线总人数。代理商取总人数，经销商只取三代。
        $totalUnderLine = 0;
        foreach ($allUnderLines as $key => $under) {
            if ($member->brokerid != 0 and $key > 2) break;

            $totalUnderLine += count($under);
        }

        //二代分销商帐户信息
        $threeUnderLines = [[], [], []];

        foreach ($allUnderLines as $key => $brokers) {
            if ($key > 1) break;

            foreach ($brokers as $brokeid) {
                $threeUnderLines[$key][] = Users::find()->asArray()->where(['uid' => $brokeid])->one();
            }
        }

        $underLinesOrder = [];
        $agent = 0;

        return $this->render('share_money', [
            'member' => $member,
            'agent'  => $agent,
            'threeUnderLines' => $threeUnderLines,
            'underLinesOrder' => $underLinesOrder,
            'totalUnderLine'  => $totalUnderLine
        ]);
    }
	
	/**
     * 收藏栏目广告
     * @return array
     */
    private function getAdvInfo() {
        $ShoppingAdvmodel = new ImsShoppingAdv();
        return $ShoppingAdvmodel->adv(5);
    }

    /**
     * 获取用户所有地址
     */
    public function actionGetAddress(){
        $ShoppingAddressModel = new ShoppingAddress();

        $res = $ShoppingAddressModel->getAddressByUid($this->userinfo['uid'], $isdefault = '');
        if($res){
            $this->stts['success'] = 1;
            $this->stts['data'] = $res;
        }
        return json_encode($this->stts);
    }

}
