<?php
/**
 * customservice.ctrl.php
 * 社区服务（客服）
 *
 */
$dos = array('display', 'post');
$do = in_array($do, $dos) ? $do : 'display';

load()->model('community.custom.service');
load()->func('tpl');
if($do == 'display'){
	$res = service_getServiceInfoManage();
	template('community/customservice');
}else if($do == 'post'){
	if (checksubmit('submit')) {
		service_postToManage();
	}else{
		$item = comservice_detail();
	}
	template('community/customservice');
}
