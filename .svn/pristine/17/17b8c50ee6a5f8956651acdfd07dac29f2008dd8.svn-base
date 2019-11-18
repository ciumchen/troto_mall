<?php
/**
 * application.ctrl.php
 * 社区申请
 *
 */

defined('IN_IA') or exit('Access Denied');
$dos = array('display', 'post', 'poststatus');
$do = in_array($do, $dos) ? $do : 'display';

load()->model('community.application');
if($do === 'display') {
	$res = commappli_listToManage();	
	
	template('community/application');
}else if($do === 'post'){
	// if (checksubmit('submit')) {
	// 	comexpert_postToManage();
	// 	ajaxreturn();
	// }else{
		
	// 	$expert = comexpert_detail();
	// 	$group = comgroup_selectByStatus();
	// }
	exit();
}else if($do == 'poststatus'){
	commappli_postStatusToManage();
	exit();
}
