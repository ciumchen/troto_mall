<?php
defined('IN_IA') or exit('Access Denied');
$dos = array('display', 'record', 'exchange');
$do = in_array($do, $dos) ? $do : 'display';

if($do == 'display'){
	load()->model('mc.member.credits.recharge');
	$res = Credits_getRechargeToList();
	// if($_W['isajax']){
	// 	$message = array();
	// 	$id = !empty($_GPC['id']) ? intval($_GPC['id']) : 0;
	// 	if(!$id){
	// 		$message['status'] = -200;
	// 		$message['msc'] = '参数不正确！';
	// 		die(json_encode($message));
	// 	}

	// 	$res = pdo_delete('mc_credits_recharge',array('id' => $id,'uniacid' => $_W['uniacid']));	
	// 	$res2 = pdo_fetchcolumn("SELECT COUNT(1) FROM ".tablename('mc_credits_recharge')." WHERE uniacid = :uniacid",array(':uniacid' => $_W['uniacid']));
	// 	if($res > 0 and $res and $res2 > 0){
	// 		$message['status'] = 200;
	// 		$message['msc'] = '删除成功！';
	// 		$message['total'] = $res2;
	// 	}else{
	// 		$message['status'] = -200;
	// 		$message['msc'] = '删除失败！';
	// 	}
	//    die(json_encode($message));
	// }
}

load()->func('tpl');
template('mc/credits');