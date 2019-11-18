<?php
defined('IN_IA') or exit('Access Denied');
/**
 * 广告幻灯片
 * load()->model('shopping.adv');
 */

/**
 * 获取列表
 */
function Adv_getListToManage(){
	global $_W, $_GPC;
	$paras = array();
	$ret = array('param'=>array());
	$pindex = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 10;
	$condition = ' 1 ';

	//时间
	if (!empty($_GPC['time'])) {
		$paras[':starttime'] = $ret['param']['starttime'] = strtotime($_GPC['time']['start']);
		$paras[':endtime'] = $ret['param']['endtime'] = strtotime($_GPC['time']['end']) + 86399;
		$condition .= " AND starttime >= :starttime AND endtime <= :endtime ";
	}
	if (!isset($ret['param']['starttime']) || !isset($ret['param']['endtime'])) {
		$ret['param']['starttime'] = strtotime('-12 month');
		$ret['param']['endtime'] = time();
	}
	if (isset($_GPC['type']) && $_GPC['type']!='') {
		$condition .= ' AND type='.intval($_GPC['type']);
	}
	if (isset($_GPC['enabled']) && $_GPC['enabled']!='') {
		$condition .= ' AND enabled='.intval($_GPC['enabled']);
	}
	if (isset($_GPC['advname']) && $_GPC['advname']!='') {
		$condition .= " AND advname LIKE '%".$_GPC['advname']."%'";
	}

	$ret['list'] = pdo_fetchall('SELECT * FROM '.tablename('shopping_adv').' WHERE '.$condition.' ORDER BY adid DESC LIMIT ' . ($pindex - 1) * $psize . ','.$psize, $paras);
	$ret['total'] = pdo_fetchcolumn('SELECT count(adid) FROM '.tablename('shopping_adv')." WHERE $condition ", $paras);
	$ret['pager'] = pagination($ret['total'], $pindex, $psize);
	return $ret;
}

/**
 * 保存广告幻灯片信息
 */
function Adv_saveInfo(){
	global $_W, $_GPC;
	$adid = intval($_GPC['adid']);
	$data = array(
			'advname' => trim($_GPC['advname']),
			'enabled' => intval($_GPC['enabled']),
			'type' => intval($_GPC['type']),
			'displayorder' => intval($_GPC['displayorder']),
			'link' => trim($_GPC['link']),
			'thumb' => trim($_GPC['thumb']),
			'starttime' => strtotime($_GPC['starttime']), 
			'endtime' => strtotime($_GPC['endtime']), 
		);

	//过滤提示
	if(empty($data['advname'])){ message('广告幻灯片名称不能为空', referer(), 'error'); }
	if(empty($data['thumb'])){ message('广告幻灯片图片不能为空', referer(), 'error'); }
	if(empty($data['starttime'])){ message('开始时间不能为空', referer(), 'error'); }
	if(empty($data['endtime'])){ message('结束时间不能为空', referer(), 'error'); }

	if($adid){
		pdo_update('shopping_adv', $data, array('adid' => $adid));
	}else{
		pdo_insert('shopping_adv', $data);
		$adid = pdo_insertid();
	}
	return $adid;
}

/**
 * 获取广告幻灯片的详细信息
 */
function Adv_getDetailToManage($adid = 0){
	global $_W, $_GPC;
	$adid || $adid = $_GPC['adid'];
	return pdo_fetch('select * from '.tablename('shopping_adv').' where adid = :adid', array(':adid' => intval($adid)));
}