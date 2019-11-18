<?php
/**
 * mc.comm.log.mod.php
 * 佣金流水
 */

function Journal_addRecord($data){
	if (!empty($data)) {
		return pdo_insert(tablename('mc_comm_log'), $data, $replace = FALSE);
	} else {
		return  false;
	}
}

/**
 * 获取佣金流水记录中用户上级
 */
function getPidsByCommLog($uid, $orderId, $level=1){
	$data = array();
	$sql = 'select DISTINCT pid,level from '.tablename('mc_comm_log').' where uid=:uid and orderid=:orderId order by level ASC';
	$CommLogUids = pdo_fetchAll($sql, array(':uid'=>$uid, ':orderId'=>$orderId));
	foreach ($CommLogUids as $pid) {
		if ($pid['level']<=$level) {
			$data[$pid['level']] = $pid['pid'];
		}
	}
	return $data;
}

/**
 * 获取分佣金时候记录的分成单价
 */
function getOrderGoodsCommByOrderId($orderId){
	$data = array();
	$sql = 'SELECT level,gid,optionid,comm from '.tablename('mc_comm_log').' where orderid=:orderId order by level ASC';
	return pdo_fetchAll($sql, array(':uid'=>$uid, ':orderId'=>$orderId));
}

/**
 * 获取佣金记录
 */
function getOrderCommList($orderId){
	global $_W,$_GPC;

	$psize = 10;
	$pindex = max(1, intval($_GPC['page']));
	$condition = '1=1';
	$result = array();

	if ($_GPC['time']['start']!='' && $_GPC['time']['end']!='') {
		$condition.= " AND tba.ftime between '".date('Y-m-d H:i:s',strtotime($_GPC['time']['start']))."' and '".date('Y-m-d H:i:s',strtotime($_GPC['time']['end']))."'";
		$result['param']['starttime'] = $_GPC['time']['start'];
		$result['param']['endtime'] = $_GPC['time']['end'];
	} else {
		$result['param']['starttime'] = date('Y-m-d', strtotime('-3 days'));
		$result['param']['endtime']   = date('Y-m-d');
	}
	if (intval($_GPC['uid'])) {
		$condition.= ' AND (tba.uid='.intval($_GPC['uid']).' OR tba.pid='.intval($_GPC['uid']).')';
	}
	if (intval($_GPC['tid'])) {
		$condition.= ' AND tbb.ordersn='.intval($_GPC['tid']);
	}
	if (intval($_GPC['status'])) {
		$condition.= ' AND tba.status='.intval($_GPC['status']);
	}

	$result['list'] = pdo_fetchall('SELECT
										tba.*,tbb.ordersn,tbc.nickname
									FROM
										'.tablename('mc_comm_log').' AS tba
									LEFT JOIN '.tablename('shopping_order').' AS tbb ON tba.orderid = tbb.id
									LEFT JOIN '.tablename('mc_members').' AS tbc ON tba.uid = tbc.uid 
									WHERE '.$condition." order by fid DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
	$result['total'] = pdo_fetchcolumn('SELECT COUNT(DISTINCT tba.fid)
									FROM
										'.tablename('mc_comm_log').' AS tba
									LEFT JOIN '.tablename('shopping_order').' AS tbb ON tba.orderid = tbb.id
									LEFT JOIN '.tablename('mc_members').' AS tbc ON tba.uid = tbc.uid 
									WHERE '.$condition);
	$result['pager'] = pagination($result['total'], $pindex, $psize);
	
	return $result;
}

