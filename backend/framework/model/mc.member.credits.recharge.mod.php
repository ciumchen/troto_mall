<?php
/**
 * load()->model('mc.member.credits.recharge');
 * 用户充值记录
 *
 */

function Credits_getRechargeToList(){
	global $_W,$_GPC;
	$psize = 10;
	$pindex = max(1, intval($_GPC['page']));
	$condition = " a.uniacid = :uniacid";
	$paras[':uniacid'] = $_W['uniacid'];
	$res['param']['id'] = intval($_GPC['id']);
	$res['param']['uid'] = intval($_GPC['uid']);
	$res['param']['nickname'] = $_GPC['nickname'];
	$res['param']['status'] = $_GPC['status'] == '' ? (string) ('') : intval($_GPC['status']);
	$res['param']['tid'] = $_GPC['tid'];
	$res['param']['transid'] = $_GPC['transid'];
	$res['param']['fee'] = (float) ($_GPC['fee']);

	//搜索功能
	if(!empty($res['param']['id'])){
		$condition .= " AND a.id = ".(integer) ($_GPC['id']);
	}

	if(!empty($res['param']['uid'])){
		$condition .= " AND a.uid =".(integer) ($_GPC['uid']);
	}

	if(!empty($res['param']['nickname'])){
		$condition .= " AND b.nickname = ".(string) ($_GPC['nickname']);
	}

	if($res['param']['status'] >= 0 AND $res['param']['status'] !== ''){
		$condition .= " AND a.status = ".(int) ($_GPC['status']);
	}

	if(!empty($res['param']['tid'])){
		$condition .= " AND a.tid LIKE '%".$_GPC['tid']."%'";
	}

	if(!empty($res['param']['transid'])){
		$condition .= " AND a.transid LIKE '%".$_GPC['transid']."%'";
	}

	if($res['param']['fee'] >= 0 AND $res['param']['fee'] != ''){
		$condition .= " AND a.fee = ".(float) ($_GPC['fee']);
	}

	if (!empty($_GPC['time'])) {
		$paras[':starttime'] = $res['param']['starttime'] = strtotime($_GPC['time']['start']);
		$paras[':endtime'] = $res['param']['endtime'] = strtotime($_GPC['time']['end']) + 86399;
		$condition .= " AND a.createtime >= :starttime AND a.createtime <= :endtime ";
	}

	if (!isset($res['param']['starttime']) OR !isset($res['param']['endtime'])) {
		$res['param']['starttime'] = strtotime('-1 month');
		$res['param']['endtime'] = time();
	}

	$sql = "SELECT a.*,b.nickname FROM ".tablename("mc_credits_recharge")." as a LEFT JOIN 
			".tablename("mc_members")." as b ON a.uid = b.uid WHERE {$condition} ORDER BY id DESC
			 LIMIT ".($pindex - 1) * $psize.','.$psize;


	$res['list'] = pdo_fetchall($sql,$paras);
	//var_dump($res);exit;
	$res['total'] = pdo_fetchcolumn("SELECT COUNT(*)  FROM ".tablename("mc_credits_recharge")." as a LEFT JOIN 
			".tablename("mc_members")." as b ON a.uid = b.uid WHERE {$condition}
			 ", $paras);
	$res['pager'] = pagination($res['total'], $pindex, $psize);
	
	return $res;
}
