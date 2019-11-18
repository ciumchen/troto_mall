<?php
/**
 * mc.exchange.mod.php
 * 用户银行
 */

function Exchange_getListByStatus($status = 0){
	global $_W;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$condition = ' b.fstatus = :status ';
	$pars = array(':status' => $status);
	$list = pdo_fetchall('SELECT 
							b.rechangeid, a.fbankname, a.fbanklocation, a.fbankaccount, a.fusername, a.fmobile,
						    b.uid, b.fdesc, b.fstatus, b.fmoney, b.foperationlog, b.fcreatetime, b.fcompletiontime 
						 FROM 
							ims_mc_bank a right join ims_mc_credit_exchange b 
							on a.bankid = b.bankid and a.uid = b.uid
						 WHERE '.$condition, $pars);
	if(empty($list)){
		return false;
	}
	$total = pdo_fetchcolumn('SELECT count(b.rechangeid) FROM ims_mc_bank a right join ims_mc_credit_exchange b on a.bankid = b.bankid and a.uid = b.uid WHERE '.$condition, $pars);
	$pager = pagination($total, $pindex, $psize);
	return array('list' => $list, 'total' => $total, 'page' => $pager);
}

