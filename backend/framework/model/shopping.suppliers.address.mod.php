<?php
/**
 * shopping.suppliers.address.mod.php
 * 供应商地址
 */


/**
 * 获取供应商地址 
 * @param int sid (供应商ID)
 */
function SAddress_getInfoToManage($sid = 0){
	global $_W, $_GPC;
	$condition = '';
	if($sid){
		$condition .= ' and s.sid = '.$sid;
	}
	return pdo_fetchall('select s.company, a.said, a.realname, a.mobile, a.province, a.city, a.area, a.address, a.status 
						 from ims_shopping_suppliers_address  a left join ims_shopping_suppliers s on a.sid = s.sid'.$condition);
}

/**
 * 保存信息
 */
function SAddress_saveInfoToManage(){
	global $_W, $_GPC;
	$said = intval($_GPC['said']);
	if($said){
		$sid = intval($_GPC['sid']);
	} else {
		$sid = intval($_GPC['fetch-item']);
	}
	$data = array(
		'sid' => $sid,
		'realname' => $_GPC['realname'],
		'mobile' => $_GPC['mobile'],
		'province' => $_GPC['province'],
		'city' => $_GPC['city'],
		'area' => $_GPC['area'],
		'address' => $_GPC['address'],
		'uniacid' => $_W['uniacid']
		);
	if(empty($data['realname'])){
		message('联系人不能为空', referer(), 'error');	
	}
	if(empty($data['mobile'])){
		message('手机不能为空', referer(), 'error');	
	}
	if($said){
		unset($data['sid']);
		pdo_update('shopping_suppliers_address', $data, array('said' => $said));
	}else{
		pdo_insert('shopping_suppliers_address', $data);
	}
	message('信息已保存！', url('ma/suppliers/address'), 'success');
}

/**
 * 获取供应商地址 
 * @param int said (供应商地址 ID)
 */
function SAddress_getDetailToManage(){
	global $_W, $_GPC;
	return pdo_fetch('select a.said, a.realname, a.mobile, a.province, a.city, a.area, a.address, a.status 
						 from ims_shopping_suppliers_address  a   
						 where a.said = '.intval($_GPC['said']));
}