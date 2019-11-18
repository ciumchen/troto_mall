<?php
/**
 * 优惠券
 * load()->model('coupon');
 */

/**
 * 获取列表
 */
function Coupon_getListToManage(){
	global $_W, $_GPC;
	$paras = array();
	$ret = array('param'=>array());
	$pindex = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 10;
	$condition = ' 1 ';

	if (isset($_GPC['sn']) && $_GPC['sn']!='') {
		$condition .= " AND no LIKE '%".$_GPC['sn']."%'";
	}
	//时间
	if (!empty($_GPC['time'])) {
		$paras[':starttime'] = $ret['param']['starttime'] = strtotime($_GPC['time']['start']);
		$paras[':endtime'] = $ret['param']['endtime'] = strtotime($_GPC['time']['end']) + 86399;
		$condition .= " AND got_time BETWEEN :starttime AND :endtime ";
	}
	if (!isset($ret['param']['starttime']) || !isset($ret['param']['endtime'])) {
		$ret['param']['starttime'] = strtotime('-6 month');
		$ret['param']['endtime'] = time();
	}
	if (isset($_GPC['typeid']) && $_GPC['typeid']!='') {
		$condition .= ' AND type_id='.intval($_GPC['typeid']);
	}
	if (isset($_GPC['source']) && $_GPC['source']!='') {
		$condition .= ' AND source='.intval($_GPC['source']);
	}

	$ret['list'] = pdo_fetchall('SELECT c.*, ct.name, ct.value FROM '.tablename('coupon').' as c
								 left join '.tablename('coupon_type').' as ct on ct.id=c.type_id 
								 WHERE '.$condition.'
								 ORDER BY id DESC LIMIT '.($pindex-1)*$psize.','.$psize, $paras);
	$ret['total'] = pdo_fetchcolumn('SELECT count(id) FROM '.tablename('coupon')." WHERE $condition ", $paras);
	$ret['pager'] = pagination($ret['total'], $pindex, $psize);
	return $ret;
}


/**
 * 创建优惠券
 */
function Coupon_createNewOne($data) {
	return pdo_insert('coupon', $data);
}


/**
 * 保存优惠券信息
 */
function Coupon_saveInfo(){
	global $_W, $_GPC;
	$id = intval($_GPC['id']);
	$data = array(
			'expire_begin' => strtotime($_GPC['expire']['start']),
			'expire_end'   => strtotime($_GPC['expire']['end']),
			'uid'    => intval($_GPC['uid']),
			'status' => intval($_GPC['status']),
		);

	//过滤提示
	// if(!$data['uid']){ message('获取用户不能为空', referer(), 'error'); }

	if($id){
		$rs = pdo_update('coupon', $data, array('id' => $id));
		$id = $rs ?  $rs: $id ;
	}else{
		$data['create_time'] = time();
		pdo_insert('coupon', $data);
		$id = pdo_insertid();
	}
	return $id;
}

/**
 * 获取优惠券的详细信息
 */
function Coupon_getDetailToManage($id = 0){
	global $_W, $_GPC;
	$id || $id = $_GPC['id'];
	return pdo_fetch('select c.*,ct.name,ct.value,ct.threshold,ct.goodsid from '.tablename('coupon').' c 
					  left join '.tablename('coupon_type').' ct on c.type_id=ct.id 
					  where c.id = :id', 
					  array(':id' => intval($id))
		);
}