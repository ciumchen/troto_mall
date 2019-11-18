<?php
/**
 * 社区商家
 *
 */
defined('IN_IA') or exit('Access Denied');

$dos = array('display','add');
$do = in_array($do, $dos) ? $do : 'display';
load()->func('tpl');
load()->model("community.service.sercate");
load()->model('community.group');
load()->model("community.vendors");

if($do == 'display'){
	$res = vendors_getVendorsInfoMation();
} else if($do == 'add'){
	$cateid = (!empty($_GPC['cateid'])) ? intval($_GPC['cateid']) : 0;
	if(!empty($_GPC['vendorid'])){
		$vendorid = intval($_GPC['vendorid']);
		$title = "编辑商家";
		$items = vendors_getVendorsOnceInfo();
	}
	if($_W['isajax']){
		$res = sercate_selectCateName();
		ajaxReturn($res);
	}
	$res = sercate_getAloneCateToInfo($cateid);
	$serviceType = sercate_getCateToInfoMation();
	// $children = array();
	// foreach ($serviceType as $Index => $row) {
	// 	if(!empty($row['parentid'])){
	// 		$children[$row['parentid']][] = $row;
	// 		unset($serviceType[$Index]);
	// 	}
	// }
	//var_dump($children);
	$items = vendors_getVendorsOnceInfo();
	$group = comgroup_selectByStatus();
	if($_W['ispost']){
		if(isset($_GPC['addsubmit']) && empty($_GPC['vendorid'])){
			vendors_addVendorsToManager('add');
		} else {
			vendors_addVendorsToManager('editor');
		}
	}
}

template('community/vendors');