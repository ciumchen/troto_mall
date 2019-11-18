<?php
/**
 * load()->model('mc.mapping.fans');
 *
 */

function Fans_checkFollow($uid = 0){
	global $_GPC;
	$uid = intval($uid);
	if($uid != 0){
		return pdo_fetchcolumn('select count(fanid) from ims_mc_mapping_fans where uid = :uid and follow = 1', array(':uid'=>$uid));
	}
	return false;
}

/*
 * 根据uid或者openid查询用户信息
 */
function Fans_getUserInfoToList(){
	global $_W,$_GPC;

	$psize = 10;
	$pindex = max(1, intval($_GPC['page']));
	$condition = 'tba.uniacid = :uniacid';
	$paras[':uniacid'] = $_W['uniacid'];
	$result['param']['plid'] = $_GPC['plid'];
	$result['param']['uid'] = $_GPC['uid'];
	$result['param']['type'] =  $_GPC['type'] == '' ? (string) ('') : (string) ($_GPC['type']);
	$result['param']['tid'] = $_GPC['tid'];
	$result['param']['fee'] = (float) ($_GPC['fee']);
	$result['param']['status'] = $_GPC['status'] == '' ? (string) ('') : intval($_GPC['status']);
	$result['param']['module'] = $_GPC['module'] == '' ? (string) ('') : (string) ($_GPC['module']);
	if(!empty($result['param']['plid'])){
		$condition .= " AND tba.plid = ".(int) ($_GPC['plid']);
	}

	if($result['param']['type'] >= 0 AND $result['param']['type'] !== ''){
		$condition .= " AND tba.type = '".(string) ($_GPC['type'])."'";
	}
	if(!empty($result['param']['uid']) && is_numeric($result['param']['uid'])){
		$condition .= " AND tba.openid = ".$_GPC['uid'];
	} else {
		$condition .= " AND tba.openid LIKE '%".$_GPC['uid']."%'";
	}
	if(!empty($result['param']['tid'])){
		$condition .= " AND tba.tid LIKE '%".(int) ($_GPC['tid'])."%'";
	}

	if(!empty($result['param']['fee']) OR $result['param']['fee'] > 0){
		$condition .= " AND tba.fee = ".(float) ($_GPC['fee']);
	}

	if($result['param']['status'] >= 0 AND $result['param']['status'] !== ''){
		$condition .= " AND tba.status = ".(int) ($_GPC['status']);
	}

	if($result['param']['module'] >= 0 AND $result['param']['module'] !== ''){
		$condition .= " AND tba.module = '".(string) ($_GPC['module'])."'";
	}

	$result['list'] = pdo_fetchall("SELECT tba.*, tbb.nickname AS bnickname,tbc.nickname AS cnickname FROM ".tablename('core_paylog')." AS tba
						LEFT JOIN ".tablename('mc_members')." AS tbb ON tba.openid = tbb.uid
						LEFT JOIN ".tablename('mc_mapping_fans')." AS tbc ON tba.openid = tbc.openid WHERE {$condition} GROUP BY tba.plid ASC LIMIT 
							".($pindex - 1) * $psize.','.$psize,$paras);

	$result['total'] = pdo_fetchcolumn("SELECT COUNT(DISTINCT tba.plid) FROM ".tablename('core_paylog')." AS tba
						LEFT JOIN ".tablename('mc_members')." AS tbb ON tba.openid = tbb.uid
						LEFT JOIN ".tablename('mc_mapping_fans')." AS tbc ON tbb.uid = tbc.openid WHERE {$condition}", $paras);

	$result['pager'] = pagination($result['total'], $pindex, $psize);
	

	return $result;
}


function Fans_getOpenidByUid($uid){
	$uid = intval($uid);
	if($uid != 0){
		return pdo_fetchcolumn('select openid from ims_mc_mapping_fans where uid=:uid', array(':uid'=>$uid));
	}
	return false;
}


/**
 * 获取微信用户详细
 *
 */
function fans_detail($where){
	global $_W;
	
	if(empty($where)){
		return array();
	}
	if(empty($acid) && !empty($_W['uniacid'])){
		$acid = $_W['uniacid'];
	}
	$condition = ' WHERE acid = :acid ';
	$params = array();
	$params[':acid'] = $acid;
	if(isset($where['fanid'])){
		$condition .= ' AND fanid = :fanid ';
		$params[':fanid'] = $where['fanid'];
	}
	$sql = 'SELECT * FROM ' . tablename('mc_mapping_fans') . $condition .' LIMIT 1';
	$fan = pdo_fetch($sql, $params);

	if(!empty($fan)){
		if (!empty($fan['tag']) && is_string($fan['tag'])) {
			if (is_base64($fan['tag'])){
				$fan['tag'] = @base64_decode($fan['tag']);
			}
			if (is_serialized($fan['tag'])) {
				$fan['tag'] = @iunserializer($fan['tag']);
			}
			if(!empty($fan['tag']['headimgurl'])) {
				$fan['tag']['avatar'] = tomedia($fan['tag']['headimgurl']);
				unset($fan['tag']['headimgurl']);
			}
		}
		if(empty($fan['tag'])) {
			$fan['tag'] = array();
		}
	}
	return $fan;
}