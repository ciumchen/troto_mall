<?php
/**
 * site.feedback.mod.php
 * 用户反馈处理
 *
 */

/**
 * 用户反馈
 *
 */
function saveUserFeedback(){
	global $_GPC, $_W;
	$data = array(
				'uniacid' => $_W['uniacid'],
				'uid' => $_W['member']['uid'],
				'nickname' => $_W['member']['nickname'] ? $_W['member']['nickname'] : $_W['member']['realname'],
				'linkman' => $_GPC['name'],
				'mobile' => $_GPC['mobile'],
				'type' => $_GPC['type'],
				'email' => $_GPC['email'],
				'description' => ihtmlspecialchars($_GPC['description']),
				'createtime' => TIMESTAMP
				);
	pdo_insert('site_feedback', $data);
	return pdo_insertid();
}

/**
 * 获取所有用户反馈
 * @param nickname
 * @param linkman
 * @param mobile
 * @param email
 * @param type
 * @param uid
 */
function getUserFeedback(){
	global $_GPC, $_W;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$condition = 'uniacid = :uniacid';
	$params = array(':uniacid'=>$_W['uniacid']);
	if(!empty($_GPC['nickname'])){
		$condition .= " and nickname LIKE '%{$_GPC['nickname']}%'";
	}
	if(!empty($_GPC['linkman'])){
		$condition .= " and linkman LIKE '%{$_GPC['linkman']}%'";
	}
	if(!empty($_GPC['mobile'])){
		$condition .= " and mobile LIKE '%{$_GPC['mobile']}%'";
	}
	if(!empty($_GPC['email'])){
		$condition .= " and email LIKE '%{$_GPC['email']}%'";
	}
	if(!empty($_GPC['type'])){
		$condition .= " and type = :type";
		$params[':type'] = $_GPC['type'];
	}
	if(!empty($_GPC['uid'])){
		$condition .= " and uid = :uid";
		$params[':uid'] = (int)$_GPC['uid'];
	}
	$list = pdo_fetchall("SELECT fid,uid,nickname,linkman,mobile,email,type,description,createtime FROM ".tablename('site_feedback')." WHERE $condition LIMIT ".($pindex - 1) * $psize.','.$psize, $params);

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('site_feedback') . " WHERE $condition", $params);
	$pager = pagination($total, $pindex, $psize);
	return array('list' => $list, 'total' => $total, 'page' => $pager);
}

function feedback_detail($id){
	return pdo_fetch("select * from ". tablename('site_feedback') . " where fid = :fid", array(':fid' => $id));
}