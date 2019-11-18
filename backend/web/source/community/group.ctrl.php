<?php
/**
 * group.ctrl.php 
 * 社区群
 *
 */

defined('IN_IA') or exit('Access Denied');
$dos = array('display', 'post');
$do = in_array($do, $dos) ? $do : 'display';

load()->func('tpl');
load()->model('community.group');
load()->model('community.custom.service');
if($do === 'display') {
	$res = comgroup_listToManage();
	
}else if($do === 'post'){
	if (checksubmit('submit')) {
		comgroup_insertToManage();
		ajaxreturn();
	}else{
		$group = comgroup_detail();
		$customlist = service_getServiceInfoManage();
	}
}

template('community/group');
