<?php
/**
 * sign.ctrl.php
 * 签到模块管理
 */
defined('IN_IA') or exit('Access Denied');
$dos = array('display', 'record', 'exchange');
$do = in_array($do, $dos) ? $do : 'display';

if($do == 'display'){
	load()->model('mc.member.sign');
	$res = MMSign_getListToManage();
}else if($do == 'record'){
	load()->model('mc.member.sign.record');
	$res = MMSRecord_getListToManage();
}else if($do == 'exchange'){
	load()->model('activity.goods.record');
	$res = AGRecord_getInfoToManage();
}

template('mc/sign');
