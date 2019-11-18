<?php
defined('IN_IA') or exit('Access Denied');
$dos = array('display', 'record', 'exchange');
$do = in_array($do, $dos) ? $do : 'display';
if($do == 'display'){
	load()->model('mc.mapping.fans');
	$res = Fans_getUserInfoToList();
	// if($_W['isajax']){
	// 	$message = array();
	// 	$plid = !empty($_GPC['plid']) ? intval($_GPC['plid']) : 0;
	// 	if(!$plid){
	// 		$message['status'] = -200;
	// 		$message['msc'] = '参数不正确！';
	// 		die(json_encode($message));
	// 	}

	// 	$res = pdo_delete('core_paylog',array('plid' => $plid,'uniacid' => $_W['uniacid']));	
	// 	$res2 = pdo_fetchcolumn("SELECT COUNT(1) FROM ".tablename('core_paylog')." WHERE uniacid = :uniacid",array(':uniacid' => $_W['uniacid']));
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
template('mc/paylog');