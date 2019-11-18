<?php
/**
 * mc.bank.mod.php
 * 用户银行
 */

function Bank_getBankToList(){
	global $_W;
	return pdo_fetchall('SELECT bankid, fbankname, fbanklocation, fbankaccount, fusername, fmobile, fdefault 
						 FROM ims_mc_bank
						 WHERE ftype = 1 and uid = :uid and fstatus = 1', 
						 array(':uid' => $_W['member']['uid']));
}

function Bank_getBankDetailById($bankid = 0){
	global $_W;
	if($bankid === 0){
		return false;
	}
	return pdo_fetch('SELECT fbankname, fbanklocation, fbankaccount, fusername, fmobile 
						 FROM ims_mc_bank
						 WHERE ftype = 1 and uid = :uid and fstatus = 1 and bankid = :bankid', 
						 array(':uid' => $_W['member']['uid'], ':bankid' => $bankid));
}

function Bank_saveUserBank(){
	global $_W, $_GPC;
	$uid = $_W['member']['uid'];
	$bankid = intval($_GPC['bankid']);
	$bankname = $_GPC['bankname'];
	$backlocation = $_GPC['backlocation'];
	$backaccount = $_GPC['backaccount'];
	$username = $_GPC['username'];
	$mobile = $_GPC['mobile'];
	if(!empty($bankname) && !empty($backlocation) && !empty($backaccount) && !empty($username) && !empty($mobile)){
		pdo_query("call proc_saveUserBank({$uid}, {$bankid}, '{$bankname}', '{$backlocation}', '{$backaccount}', '{$username}', '{$mobile}', @PRESPCODE, @PRESPBANKID)");
		$res = pdo_query("select @PRESPCODE respcode, @PRESPBANKID respbankid");
		return $res;
	}
	return false;
}