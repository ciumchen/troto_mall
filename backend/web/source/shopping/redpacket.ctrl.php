<?php
defined('IN_IA') or exit('Access Denied');

$dos = array('type', 'manage');
$do = in_array($do, $dos) ? $do : 'type';

$operations = array('display', 'detail', 'add', 'del');
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

$pindex = max(1, intval($_GPC['page']));
$psize = 30;
$condition  = $module = '';
$pars = array();
load()->func('tpl');
/**红包展示页*/
if ($do == 'type') {
	load()->model('redpacket');
	//直接展示所有数据
	if ($operation=='display') {
		$list = Redpacket_getListToManage();
	}
	//查看红包详情
	if ($operation=='detail') {
		$detailData=Redpacket_getDetailToManage();
	}
	//编辑,添加红包
	if ($operation=='handle') {
		$typeid = intval($_GPC['id']);
		$detailData=Redpacket_getDetailToManage();
		//获取所有优惠券类型
		$couponList=Coupon_getTypeList();
		//保存数据跳转到红包详情
		if($_POST && checksubmit('submit')){
			$id = Redpacket_saveInfo();
			message('红包信息更新成功！', url('shopping/redpacket/type',array('id'=>$id,'op'=>'detail')), 'success');
		}
	}

	//删除红包
	if ($operation=='del') {
		$typeid=$_GPC['id'];
		
	}

	template('shopping/redpacket');
}