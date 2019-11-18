<?php
/**
 * mc.bank.rechange.mod.php
 * 分成（积分）兑换
 */

function Rechange_getList(){
	global $_W,$_GPC;

	$psize = 15;
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
		$condition.= ' AND tba.uid='.intval($_GPC['uid']);
	}

	$result['list'] = pdo_fetchall('SELECT 
										tba.*,tbb.nickname 
									FROM 
										'.tablename('mc_bank_rechange').' tba left join '.tablename('mc_members').' tbb 
									ON tba.uid = tbb.uid
									WHERE '.$condition." order by fcompletetime DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
	$result['total'] = pdo_fetchcolumn('SELECT COUNT(DISTINCT tba.rechangeid) 
									FROM 
										'.tablename('mc_bank_rechange').' tba left join '.tablename('mc_members').' tbb 
									ON tba.uid = tbb.uid 
									WHERE '.$condition);
	$result['pager'] = pagination($result['total'], $pindex, $psize);
	
	return $result;
}