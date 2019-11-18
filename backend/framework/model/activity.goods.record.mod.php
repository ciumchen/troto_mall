<?php
/**
 * load()->model('activity.goods.record');
 * 兑换记录
 */

function AGRecord_getInfoToManage(){
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

	$list = pdo_fetchall("SELECT u.uid, u.nickname, s.agrid, s.exchangetype, s.awardid, s.awardtitle, s.awardvalue, s.awardprice, s.getnum, s.createtime, s.`status`, s.addrid, s.express, s.expresssn, s.expresstime 
						  FROM ".tablename('mc_members')." u right join ".tablename('activity_goods_record')." s on u.uid =s.uid  
						  WHERE $condition LIMIT ".($pindex - 1) * $psize.','.$psize, $pars);

	$total = pdo_fetchcolumn("SELECT COUNT(*)  FROM ".tablename('mc_members')." u right join ".tablename('activity_goods_record')." s on u.uid =s.uid WHERE $condition", $pars);
	$pager = pagination($total, $pindex, $psize);
	return array('list' => $list, 'total' => $total, 'page' => $pager);
}