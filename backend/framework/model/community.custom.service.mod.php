<?php
/**
 * load()->model('community.custom.service');
 * 社区服务信息（客服），每个物业可以绑定服务信息
 *
 */


function service_getServiceInfoManage(){
	global $_W, $_GPC;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$condition = " where serid > 0 ";
	$pars = array();

	if(!empty($_GPC['status'])){
		$condition .= ' and `status` = :status';
		$pars[':status'] = intval($_GPC['status']);
	}

	# 用户名
	if(!empty($_GPC['title'])){
		$condition .= ' and `title` like "%'.trim($_GPC['title']).'"&';
	}

	$sql = "SELECT * FROM " . tablename('community_custom_service') . " $condition ORDER BY serid DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;

	$list = pdo_fetchall($sql, $pars);
	
	$total = pdo_fetchcolumn('SELECT COUNT(serid) FROM ' . tablename('community_custom_service') . " $condition");
	$pager = pagination($total, $pindex, $psize);

	return array('list' => $list, 'pager' => $pager, 'total' => $total);
}


/**
 * 服务添加或修改
 * @param int $id serid
 * @param data service
 */
function service_postToManage(){
	global $_W, $_GPC;
	$serid = (int) $_GPC['id'];
	$data = array(
			'title' =>  trim($_GPC['title']),
			'explain' => trim($_GPC['explain']),
			'avatar' 	 => $_GPC['avatar'],
			'qrcode' => $_GPC['qrcode'],
			'validate'	 => trim($_GPC['validate']),			
			'status'	 => (int)$_GPC['status'],
		);

	if($serid){
		$res = pdo_update('community_custom_service', $data, array('serid' => $serid));
	}else{
		pdo_insert('community_custom_service', $data);
		$res = pdo_insertid();
	}
	if($res){
		message('编辑成功', referer(), 'success');
	}
	message('操作失败！', referer(), 'danger');
}


function comservice_detail(){
	global $_W, $_GPC;
	$serid = (int) $_GPC['id'];
	$sql = "SELECT * FROM ".tablename('community_custom_service')." where serid = :serid";

	return pdo_fetch($sql, array(':serid' => $serid));
}

function service_getServicefromGroupManage(){
	global $_W, $_GPC;
	$groupid = intval($_GPC['id']);
	$list = pdo_fetch("SELECT ser.title, ser.validate, ser.avatar, ser.qrcode FROM ".tablename("community_custom_service")." 
					as ser left join ".tablename("community_group")." as gro on ser.serid=gro.serid WHERE gro.comgroupid = {$groupid}");
	return $list;
}
