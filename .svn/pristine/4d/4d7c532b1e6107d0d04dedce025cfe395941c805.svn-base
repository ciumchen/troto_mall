<?php
/**
 * expert.ctrl.php
 * 社区专家
 *
 */

defined('IN_IA') or exit('Access Denied');
$dos = array('display', 'post');
$do = in_array($do, $dos) ? $do : 'display';

load()->func('tpl');
load()->model('community.expert');
load()->model('community.group');
if($do === 'display') {
	$group = comgroup_selectByStatus(2);
	$res = comexpert_listToManage();	
}else if($do === 'post'){
	if (checksubmit('submit')) {
		comexpert_postToManage();
		ajaxreturn();
	}else{
		
		$expert = comexpert_detail();
		$group = comgroup_selectByStatus();
	}
}

template('community/expert');
