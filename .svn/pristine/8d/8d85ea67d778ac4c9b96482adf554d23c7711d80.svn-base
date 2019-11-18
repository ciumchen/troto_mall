<?php
/**
 * load()->model('shopping.suppliers');
 * 供应商信息表
 */

/**
 * 获取供应商信息 
 *
 */
function suppliers_getInfoToManage(){
	global $_W, $_GPC;
	return pdo_fetchall('select * from '.tablename('shopping_suppliers').' where status=1');
}

/**
 * 保存供应商信息
 */
function suppliers_saveInfo(){
	global $_W, $_GPC;
	$sid = intval($_GPC['sid']);
	$data = array(
			'company' => $_GPC['company'], 
			'linkman' => $_GPC['linkman'], 
			'mobile' => $_GPC['mobile'], 
			'tel' => $_GPC['tel'], 
			'email' => $_GPC['email'], 
			'qq' => $_GPC['qq'], 
			'site' => $_GPC['site'],
		);
	if(empty($data['company'])){
		message('公司名不能为空', referer(), 'error');	
	}
	if(empty($data['linkman'])){
		message('联系人不能为空', referer(), 'error');	
	}
	if($sid){
		pdo_update('shopping_suppliers', $data, array('sid' => $sid));
	}else{
		pdo_insert('shopping_suppliers', $data);
	}
	message('信息已保存！', url('ma/suppliers/suppliers'), 'success');
}

/**
 * 获取供应商的详细信息 
 *
 */
function suppliers_getDetailToManage($sid = 0){
	global $_W, $_GPC;
	$sid || $sid = $_GPC['sid'];
	return pdo_fetch('select company, linkman , tel, mobile, email, qq, site, `status` from '.tablename('shopping_suppliers').' where sid = :sid', array(':sid' => intval($sid)));
}