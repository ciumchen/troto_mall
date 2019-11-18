<?php
/**
 * load()->model('signin.log');
 * 用户登陆日志（前端逻辑调用）
 * 
 */
defined('IN_IA') or exit('Access Denied');

/**
 * 插入日志表
 */
function signin_Run(){
	global $_W;
	//set_time_limit(0);
	$uid = $_W['member']['uid'];
	$data = array();

	$condition = ' WHERE uid = :uid ';	
	$pars = array(':uid' => $uid);

	$data['uid'] = $uid;
	$data['openid'] = $_W['fans']['from_user'];
	$data['ip'] = getip();
	$data['location'] = GetIpLookup();
	$data['agenttxt'] = signin_getUserAgentTxt();
	$data['wechatver'] = signin_getWechatVer("wechatver");
	$data['mobiletype'] = signin_getWechatVer('mobiletype');
	$data['createdt'] = TIMESTAMP;
	if(empty($uid)){
		$condition = ' WHERE ip = :ip ';
		unset($pars[':uid']);
		$pars[':ip'] = $data['ip'];
	}
	# 根据用户查询最后一次插入日志的时间戳，如果不存在则插入
	$ret = pdo_fetchcolumn("SELECT max(createdt) as createdt FROM ".tablename("log_signin").$condition, $pars);
	if (!($ret) || (($ret+30) <= TIMESTAMP)){		
		pdo_insert('log_signin',$data);
	}
	return true;
}
/**
 * 获取agent请求
 */
function signin_getUserAgentTxt(){

	return $_SERVER['HTTP_USER_AGENT'];
}

/**
 * 获取微信版本号 && 获取用户设备类型
 */
function signin_getWechatVer($type = ""){
	$user_agent = signin_getUserAgentTxt();
	if($type == 'wechatver'){
		//查找是否为微信浏览器
		if(strpos($user_agent,'MicroMessenger')){
			//获取MicroMessenger后面的版本号
			preg_match('/MicroMessenger\/([\d\.]+)/i', $user_agent, $matches);
			return $matches[1];
		} else {
			return null;
		
		}
	} else if($type == 'mobiletype'){
 	if (strpos($user_agent,'Mobile') !== false) {
			$clientkeywords = array(
				'nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','ipad','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamo','openwave','nexusone','cldc','midp','wap','mobile','windows phone'
			);

			preg_match("/(".implode('|',$clientkeywords).")/i",strtolower($user_agent),$matches);

			if(is_array($matches)){
				return $matches[1];
			}else {
				return null;
			}
		 } else {
		 	return "PC";
		 }
		
	}
	
}