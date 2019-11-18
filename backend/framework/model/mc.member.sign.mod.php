<?php
/**
 * load()->model('mc.member.sign');
 * 用户签到
 *
 */

function MMSign_getListToManage(){
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

	$list = pdo_fetchall("SELECT u.uid, u.nickname, s.times, s.counttimes, s.createtime, s.lasttime, s.exchangetimes 
						  FROM ".tablename('mc_members')." u right join ".tablename('mc_member_sign')." s on u.uid =s.uid  
						  WHERE $condition 
						  ORDER BY s.exchangetimes DESC, s.counttimes DESC 
						  LIMIT ".($pindex - 1) * $psize.','.$psize, $pars);

	$total = pdo_fetchcolumn("SELECT COUNT(*)  FROM ".tablename('mc_members')." u right join ".tablename('mc_member_sign')." s on u.uid =s.uid WHERE $condition", $pars);
	$pager = pagination($total, $pindex, $psize);
	return array('list' => $list, 'total' => $total, 'pager' => $pager);
}