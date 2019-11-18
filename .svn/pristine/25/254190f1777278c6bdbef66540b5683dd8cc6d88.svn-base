<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
use console\models\Members;
use yii\helpers\Json;
use newfrontend\models\AnonCommentsAvatar;
/**
 * 判断刷单头像是否失效(每月执行一次)
 */
class SdykComentsController extends Controller{

	public function actionDetectionIcon(){
		$url="http://cdn.10d15.com/anonavatar-img/";
		//获取所有头像信息
		$iconList=AnonCommentsAvatar::find()->asarray()->limit(1)->all();
		//遍历数据对比是否失效
		foreach ($iconList as $key => $icon) {
			//截取url判断是否已替换过
			if(strpos($icon['avatar'],'wx.qlogo.cn')){
				$data = self::curlGet($icon['avatar']);
				file_put_contents('tmp.jpg', $data);
				//如何图片失效替换数据库中的图片地址为默认的cdn地址
				if (md5_file('tmp.jpg')=='fee9458c29cdccf10af7ec01155dc7f0') {
					$iconOne=AnonCommentsAvatar::find()->where(['id'=>$icon['id']])->one();
					$iconOne->avatar=$url.$icon['id'].'.jpg';
					$iconOne->save();
				}
			}
		}
	}	
	private function curlGet($url, $timeout=3){
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    //强制使用IPV4协议解析域名
	    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	    //不校验证书信息
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    //curl判断协议，CURL_HTTP_VERSION_1_0/CURL_HTTP_VERSION_1_1
	    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_NONE);
	    //头部送出Expect，只POST有效？详见 http://smg.fun/JeoV06
	    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Expect: ']);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36');
	    $data = curl_exec($ch);
	    curl_close($ch);
	    return $data;
	}
}

