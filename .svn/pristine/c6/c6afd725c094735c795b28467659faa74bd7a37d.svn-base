<?php

defined('IN_IA') or exit('Access Denied');

// load()->model('suppliers');
$dos = array('display', 'suppliers', 'address');
$do = in_array($do, $dos) ? $do : 'display';
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

$pindex = max(1, intval($_GPC['page']));
$psize = 15;
$condition  = $module = '';
$pars = array();
if($do == 'suppliers'){
	$module_types = '供应商';
	$id = intval($_GPC['sid']);
	$ptr_title = ($id ? '修改' : '添加').'供应商';
	load()->model('shopping.suppliers');
	if($operation == 'handle'){
		
		if(checksubmit('submit')){
			suppliers_saveInfo();
		}
		$item = suppliers_getDetailToManage();
	}else{
		$list = suppliers_getInfoToManage();
	}
	if($_W['isajax']){
		$message = array();
		if(!empty($_GPC['sid']) && isset($_GPC['type'])){

			if($_GPC['type']){
				$res = pdo_update("shopping_suppliers",array("status" => 0),array('sid' => intval($_GPC['sid'])));
				if($res > 0){
					$message['status'] = 200;
					$message['type'] = 0;
				} else {
					$message['status'] = -200;
					$message['msc'] = "不确定的错误！";
				}
			} else {
				$res = pdo_update("shopping_suppliers",array("status" => 1),array('sid' => intval($_GPC['sid'])));
				if($res > 0){
					$message['status'] = 200;
					$message['type'] = 1;
				} else {
					$message['status'] = -200;
					$message['msc'] = "不确定的错误！";
				}
			}
		} else {
			$message['status'] = -200;
			$message['msc'] = "参数不正确！";
		}
		die(json_encode($message));
	}
	template('ma/suppliers');
}else if($do == 'address'){
	$module_types = '供应商地址';
	$id = intval($_GPC['said']);
	if($_GPC['op'] == 'detail'){
		$ptr_title = '供应商地址详情';
		$detailList = pdo_fetch("SELECT taa.*,taa.mobile as amobile,tbb.*,tbb.mobile as bmobile FROM ".tablename("shopping_suppliers_address")." AS taa 
			LEFT JOIN ".tablename("shopping_suppliers")." AS tbb ON taa.sid = tbb.sid WHERE taa.said = :said",array(':said' => $id));
		
	} else {
		$ptr_title = ($id ? '修改' : '添加').'供应商地址';
	}

	load()->model('shopping.suppliers.address');
	if($operation == 'handle'){
		if(checksubmit('submit')){
			SAddress_saveInfoToManage();
		}
		$item = SAddress_getDetailToManage();
		$suppliersAllList = pdo_fetchall("SELECT sid,linkman,mobile FROM ".tablename('shopping_suppliers')." WHERE uniacid = :uniacid",array(':uniacid' => $_W['uniacid']));

	}else{
		$oaid = $_GPC['oaid'];			//售后ID
		$ogid = $_GPC['ogid'];			//订单产品

		$list = SAddress_getInfoToManage($_GPC['sid']);
		if($oaid && $ogid){
			$ptr_title = '售后地址选择';
		}
	}
	
	template('ma/address');
}
