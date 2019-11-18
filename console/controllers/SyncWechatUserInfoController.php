<?php
namespace console\controllers;

use yii;
use yii\console\Controller;
use console\models\Members;
use yii\helpers\Json;

class SyncWechatUserInfoController extends Controller{

    /**
     * 定时任务同步微信用户信息
     * PS:使用：php /home/wwwroot/troto_mall/yii sync-wechat-user-info/update>>/var/log/sync-wechat.log
     */
    public function actionUpdate() {
        $_startTime = time();
        file_put_contents(dirname(dirname(__FILE__)).'/logs/SyncWechatUserInfo.log', '[INFO]'.date('Y-m-d H:i:s')." 同步微信关注用户信息任务启动。\n", FILE_APPEND);

        $MembersModel = new Members();
        $checkTotal = 0;
        do{
            //检查总共粉丝数量
            $fansList  = Yii::$app->wechat->app->user->list();
        // print_r($fansList);exit;
            $pageNextOpenid = $fansList->next_openid;
            $fansData       = $fansList->data->openid;
            foreach ($fansData as $opKey=>$fansOpenid) {
                //根据openid查库用户信息
                $wxUserInfo = $MembersModel->checkUserInfo($fansOpenid);
                $wxUserInfo = isset($wxUserInfo[0]) ? $wxUserInfo[0] : [];
                $wxfansInfo = Yii::$app->wechat->app->userz->get($fansOpenid);

                //==用户存在 更新用户数据
                if ($wxUserInfo) {
                    $rs = $MembersModel->saveUser([
                        'nickname'   => $wxfansInfo->nickname,
                        'unionid'    => $wxfansInfo->unionid?:NULL,
                        'avatar'     => $wxfansInfo->headimgurl,
                        'gender'     => $wxfansInfo->sex,
                        'groupid'    => $wxfansInfo->groupid,
                        'follow'     => $wxfansInfo->subscribe,
                        'followtime' => $wxfansInfo->subscribe_time,
                        'updatedt'   => time()
                    ], $fansOpenid);

                //==用户不存在 新建用户数据
                } else {
                    $rs = $MembersModel->saveUser([
                        'nickname'   => $wxfansInfo->nickname,
                        'openid'     => $wxfansInfo->openid,
                        'unionid'    => $wxfansInfo->unionid?:NULL,
                        'avatar'     => $wxfansInfo->headimgurl,
                        'gender'     => $wxfansInfo->sex,
                        'groupid'    => $wxfansInfo->groupid,
                        'remark'     => $wxfansInfo->remark,
                        'follow'     => $wxfansInfo->subscribe,
                        'followtime' => $wxfansInfo->subscribe_time,
                        'username'   => microtime(true).rand(0,999),
                        'password'   => microtime(true),
                        'salt'       => rand(100000, 999999),
                        'status'     => 1,
                        'createtime' => time(),
                        'updatedt'   => time(),
                    ]);
                }

                if ($rs) $checkTotal++;
            }

            //判断是否拉取完毕
            // $loopWhile = $fansList->total>10000 && $fansList->count==10000;
            $loopWhile = $fansList->count==10000;
        } while ($loopWhile);
        $_spendTime = time()-$_startTime;
        file_put_contents(dirname(dirname(__FILE__)).'/logs/SyncWechatUserInfo.log', '[INFO]'.date('Y-m-d H:i:s')." 同步微信关注用户信息任务结束，更新总数：{$checkTotal}，耗时：{$_spendTime}s。\n", FILE_APPEND);
    }

}