<?php
defined('IN_IA') or exit('Access Denied');

$dos = array('type', 'manage');
$do = in_array($do, $dos) ? $do : 'manage';

$operations = array('display', 'detail', 'add', 'del');
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

$pindex = max(1, intval($_GPC['page']));
$psize = 30;
$condition  = $module = '';
$pars = array();

load()->func('tpl');

if ($do == 'type') {
	load()->model('coupon.type');

	if ($operation == 'display') {
		$list = CouponType_getListToManage();
	} else if ($operation == 'detail') {
		$detailData = CouponType_getDetailToManage();
		if ($detailData['goodsid']) {
			$goodsTitle = pdo_fetchcolumn('SELECT title FROM '.tablename('shopping_goods').' WHERE id=:goodsid', array(':goodsid'=>$detailData['goodsid']));
		}
	} else if($operation=='handle'){
		$typeid = intval($_GPC['typeid']);
		$item = CouponType_getDetailToManage();
		if($_POST && checksubmit('submit')){
			$typeid = CouponType_saveInfo();
			message('优惠券类型信息更新成功！', url('shopping/coupon/type',array('typeid'=>$typeid,'op'=>'detail')), 'success');
		}
	} else if ($operation == 'del') {
		message('暂时还不能删除，需要此功能联系开发！', url('shopping/coupon/type',array('id'=>$item['typeid'],'op'=>'detail')), 'success');
	}
	template('shopping/coupon_type');
} else if ($do == 'manage') {
	load()->model('coupon');
	load()->model('coupon.type');

	if ($operation == 'display') {
		$list = Coupon_getListToManage();
		$list['type']   = CouponType_getNotesList();
		$list['source'] = sourceTypeList();
	} else if ($operation == 'detail') {
		$detailData = Coupon_getDetailToManage();

		if ($detailData['goodsid']) {
			$goodsTitle = pdo_fetchcolumn('SELECT title FROM '.tablename('shopping_goods').' WHERE id=:goodsid', array(':goodsid'=>$detailData['goodsid']));
		}
	} else if($operation=='handle'){
		$id = intval($_GPC['id']);
		$item = Coupon_getDetailToManage();
		$list['type']   = CouponType_getNotesList();
		$list['source'] = sourceTypeList();
		$status   = statusTypeList();

		if($_POST && checksubmit('submit')){
			if (Coupon_saveInfo()) {
				message('优惠券信息更新成功！', url('shopping/coupon/manage',array('id'=>$id,'op'=>'detail')), 'success');
			} else {
				message('优惠券信息更新失败，请联系开发！', url('shopping/coupon/manage',array('id'=>$id,'op'=>'detail')), 'error');
			}
			
		}
	}  else if($operation=='batch'){
		$num   = intval($_GPC['num']);
		$total = 0;
		for (; $num>0;) {
			$data = array();
			$data['source']  = 2;
			$data['no']      = getRandString(12);
			$data['type_id'] = intval($_GPC['typeid']);
			$data['status']  = intval($_GPC['status']);
			$data['create_time']  = time();
			$data['expire_begin'] = strtotime($_GPC['starttime']);
			$data['expire_end']   = strtotime($_GPC['endtime']);
			if (Coupon_createNewOne($data)) {
				$num--;
			}
		}
		echo json_encode(['code'=>200, 'data'=>$_POST]);
		exit();
	} else if ($operation == 'del') {
		message('暂时还不能删除，需要此功能联系开发！', url('shopping/coupon/manage',array('id'=>$item['id'],'op'=>'del')), 'error');
	}
	template('shopping/coupon_manage');
}

/*
 * 状态类型
 */
function statusTypeList() {
	return array(
		'0'=>'初始状态', '1'=>'激活', '2'=>'生效', '3'=>'已使用', '4'=>'过期',
	);
}
function getStatusTypeStr($status) {
	$list = statusTypeList();
	return in_array($status, array_keys($list)) ? $list[$status] : '其他';
}
/*
 * 来源类型
 */
function sourceTypeList() {
	return array(
		'1'=>'注册送', '2'=>'领取', '3'=>'后台发放', '4'=>'满XX元送', '5'=>'单品送'
	);
}
function getSourceTypeStr($status) {
	$list = sourceTypeList();
	return in_array($status, array_keys($list)) ? $list[$status] : '未知';
}

function getRandString($bits) {
	$rstr = '';
	$chars = array(
		'1','2','3','4','5','6','7','8','9','0',
		'A','B','C','D','E','F','G','H','I','J','K','L','M','N',
		'O','P','Q','R','S','T','U','V','W','X','Y','Z',
	);
	for ($i=0; $i < $bits; $i++) { 
		$rstr .= $chars[rand(0,35)];
	}
	return $rstr;
}