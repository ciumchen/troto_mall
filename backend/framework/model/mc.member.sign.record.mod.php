<?php
/**
 * load()->model('mc.member.sign.record');
 * 用户签到记录
 *
 */

/**
 * 用户签到
 */
function Signrecord_signIn(){
	global $_W;
	$uid = $_W['member']['uid'];	
	if($uid){
		$ip = getip();
		pdo_execute("call proc_sign_record({$uid}, '{$ip}', @o_code)");
		$res = pdo_fetchcolumn('select @o_code');
		return $res;
	}
	return -201;
}

function MMSRecord_getListToManage(){
	global $_W, $_GPC;
	$psize = 20;
	$pindex = max(1, intval($_GPC['page']));
	$nickname = trim($_GPC['nickname']);
	$uid = intval($_GPC['uid']);
	
	$condition = 'u.uniacid = :uniacid';
	$pars = array(':uniacid' => $_W['uniacid']);
	if(!empty($nickname)){
		$condition .= " and u.nickname LIKE '%{$nickname}%'";
	}

	if(!empty($uid)){
		$condition .= " and u.uid = :uid";
		$pars[':uid'] = $uid;
	}

	$list = pdo_fetchall("SELECT u.uid, u.nickname, s.signintime, s.signinip
						  FROM ".tablename('mc_members')." u right join ".tablename('mc_member_sign_record')." s on u.uid =s.uid  
						  WHERE $condition LIMIT ".($pindex - 1) * $psize.','.$psize, $pars);

	$total = pdo_fetchcolumn("SELECT COUNT(*)  FROM ".tablename('mc_members')." u right join ".tablename('mc_member_sign_record')." s on u.uid =s.uid WHERE $condition", $pars);
	$pager = pagination($total, $pindex, $psize);
	return array('list' => $list, 'total' => $total, 'page' => $pager);
}