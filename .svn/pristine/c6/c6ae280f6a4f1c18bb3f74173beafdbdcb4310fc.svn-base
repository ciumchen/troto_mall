<?php
defined('IN_IA') or exit('Access Denied');

load()->model('mc');
load()->func('tpl');
$dos = array('display', 'insertUser');
$do = in_array($do, $dos) ? $do : 'display';

if($do == 'display'){


}else if($do == 'insertUser'){
	$pid = $_GPC['pid'];
	$page = $_GPC['page'];
	if(checksubmit('submit')){
		/**
		 * 添加用户
		 * 推送上家消息
		 * 
		 */
		
		// if(!empty($_GPC['avatar'])) {
		// 	if(strexists($_GPC['avatar'], 'attachment/images/global/avatars/avatar_')) {
		// 		$_GPC['avatar'] = str_replace($_W['attachurl'], '', $_GPC['avatar']);
		// 	}
		// }
		// $avatar = $_GPC['avatar'];
		$avatar = getAutoAvatar();
		$nickname = getNickname();

		$default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
		$data = array(
			'uniacid' => $_W['uniacid'], 
			'salt' => random(8),
			'groupid' => $default_groupid, 
			'createtime' => TIMESTAMP,
			'nickname' => $nickname,
			'gender' => rand(1,2),
			'residecity' => '',
			'resideprovince' => '',
			'nationality' => '',
			'avatar' => $avatar,
			'pid' => $pid,
			'status' => 2,
			'mobile' => '134'.substr(time(),2)
		);
		$password = 123456;
		$data['password'] = md5($password . $data['salt'] . $_W['config']['setting']['authkey']);
		
		pdo_insert('mc_members', $data);
		$uid = pdo_insertid();

		if($uid){
			$relation = getPid($pid, 3);
			$insert = array('uid' => $uid);
			$insert['uid1'] = $pid ? $pid : 0;
			$insert['uid2'] = $relation[1] ? $relation[1] : 0;
			$insert['uid3'] = $relation[2] ? $relation[2] : 0;

			pdo_insert('mc_relation',$insert);
			$text = "喜讯传来！".replaceStartFilter($nickname,1).'在 '.date('Y-m-d H:i:d').' 时扫描您的二维码关注云吉良品,成为您的富一代!';
			wechatPush($pid, array('text'=>array('content'=>$text)));

			$text = "喜讯通知！".$nickname.'在 '.date('Y-m-d H:i:d').' 时关注云吉良品，我们的小伙伴又增添了一枚!';
			wechatPush('opZZjs3o2cO1w9ztqdF4WEUELbu4', array('text'=>array('content' => $text)));

			message('已生成用户...', url('mc/huiyuan', array('page'=>$page)),'success');	
		}else{
			message('操作失败...', url('mc/huiyuan', array('page'=>$page)), 'danger');	
		}
		
	}
	
}
template('mc/operations');