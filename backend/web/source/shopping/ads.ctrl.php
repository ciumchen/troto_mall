<?php
defined('IN_IA') or exit('Access Denied');

$dos = array('display', 'detail', 'add', 'del');
$do = in_array($do, $dos) ? $do : 'display';
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

$pindex = max(1, intval($_GPC['page']));
$psize = 15;
$condition  = $module = '';
$pars = array();

load()->func('tpl');
load()->model('shopping.adv');

$adsTypeList = adsTypeList();
if ($do == 'display') {
	$list = Adv_getListToManage();
} else if ($do == 'detail') {
	$detailData = Adv_getDetailToManage();
} else if($do=='add'){
	$adid = intval($_GPC['adid']);
	$op_type = ($adid ? '修改' : '添加');

	if($operation == 'handle'){
		if(checksubmit('submit')){
			$adid = Adv_saveInfo();
			message('广告幻灯片信息更新成功！', url('shopping/ads',array('adid'=>$adid,'do'=>'detail','m'=>'ewei_shopping')), 'success');
		} else {
			$item = Adv_getDetailToManage();
		}
	}
} else if ($do == 'del') {
	message('暂时还不能删除，需要此功能联系开发！', url('shopping/ads',array('adid'=>$item['adid'],'do'=>'detail','m'=>'ewei_shopping')), 'success');
}
template('shopping/ads');

/*
 * 广告类型
 */
function adsTypeList() {
	return array(
			'1'=>'首页焦点轮播图', '2'=>'专题页广告', '3'=>'闪购页广告', '4'=>'分类及搜索页','5'=>'收藏夹(有商品时)','6'=>'客服中心页'
		);
}
function getAdsTypeStr($typeID) {
	$list = adsTypeList();
	return in_array($typeID, array_keys($list)) ? $list[$typeID] : '其他';
}