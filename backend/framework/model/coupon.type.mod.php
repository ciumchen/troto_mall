<?php
/**
 * load()->model('coupon.type');
 */

/**
 * 获取优惠券类型列表
 */
function CouponType_getListToManage(){
	global $_W, $_GPC;
	return pdo_fetchall('SELECT * FROM '.tablename('coupon_type').'ORDER BY id DESC');
}


/**
 * 保存优惠券类型信息
 */
function CouponType_saveInfo(){
	global $_W, $_GPC;
	$typeid = intval($_GPC['typeid']);
	$data = array(
			'name' => trim($_GPC['name']),
			'value' => intval($_GPC['value']),
			'status' => intval($_GPC['status']),
			'threshold' => trim($_GPC['threshold']),
			'goodsid' => intval($_GPC['goodsid']),
		);

	//过滤提示
	if(empty($data['name'])){ message('类型名称不能为空', referer(), 'error'); }
	if(empty($data['value'])){ message('金额面值不能为空', referer(), 'error'); }
	if(empty($data['threshold'])){ message('订单满额条件必填', referer(), 'error'); }

	if($typeid){
		pdo_update('coupon_type', $data, array('id' => $typeid));
	}else{
		$data['createtime'] = time();
		pdo_insert('coupon_type', $data);
		$typeid = pdo_insertid();
	}
	return $typeid;
}

/**
 * 获取优惠券类型的详细信息
 */
function CouponType_getDetailToManage($typeid = 0){
	global $_W, $_GPC;
	$typeid || $typeid = $_GPC['typeid'];
	return pdo_fetch('select * from '.tablename('coupon_type').' where id = :typeid', array(':typeid' => intval($typeid)));
}

/**
 * 获取优惠券面额类型
 */
function CouponType_getNotesList(){
	return pdo_fetchall('select id,name,value from '.tablename('coupon_type'));
}