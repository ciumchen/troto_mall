<?php
defined('IN_IA') or exit('Access Denied');
$dos = array('display', 'post', 'del', 'credit', 'address', 'orders');
$do = in_array($do, $dos) ? $do : 'display';
load()->model('mc');
$uid = intval($_GPC['uid']);
if (!$uid) {
	header('HTTP/1.1 403 Forbidden');
}

if($do == 'display') {
	# 会员列表
	$_W['page']['title'] = '会员列表 - 会员 - 会员中心';
	$groups = mc_groups();
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$condition = '';
	$condition .= $uid ? " AND `uid` = ".$uid : '';
	$condition .= empty($_GPC['mobile']) ? '' : " AND `mobile` LIKE '%".trim($_GPC['mobile'])."%'";
	$condition .= empty($_GPC['username']) ? '' : " AND (( `realname` LIKE '%".trim($_GPC['username'])."%' ) OR ( `nickname` LIKE '%".trim($_GPC['username'])."%' )) ";
	$condition .= intval($_GPC['groupid']) > 0 	? " AND `groupid` = '".intval($_GPC['groupid'])."'" : '';
	$sql = "SELECT uid, uniacid, groupid, realname, nickname, mobile, createtime, credit1, credit2  FROM ".tablename('mc_members')." WHERE uniacid = '{$_W['uniacid']}' ".$condition." ORDER BY createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
	$list = pdo_fetchall($sql);
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('mc_members')." WHERE uniacid = '{$_W['uniacid']}' ".$condition);
	$pager = pagination($total, $pindex, $psize);
}else if($do == 'post') {
	# 会员编辑
	$_W['page']['title'] = '编辑会员资料 - 会员 - 会员中心';
	if ($_W['ispost'] && $_W['isajax']) {
		$password = $_GPC['password'];
		$sql = 'SELECT * FROM '.tablename('members')." WHERE `uid` = :uid";
		$user = pdo_fetch($sql, array(':uid' => $uid));
		if(empty($user) || $user['uid'] != $uid) {
			exit('error');
		}
		$user['salt'] = ($user['salt']=='') ? rand(10000,99999) : $user['salt'];
		$password = md5($password . $user['salt'] . $_W['config']['setting']['authkey']);
		if (pdo_update('mc_members', array('password' => $password), array('uid' => $uid))) {
			exit('success');
		}
		exit('othererror');
	}
	if (checksubmit('submit')) {
		if (!empty($_GPC)) {
			if (!empty($_GPC['birth'])) {
				$_GPC['birthyear'] = $_GPC['birth']['year'];
				$_GPC['birthmonth'] = $_GPC['birth']['month'];
				$_GPC['birthday'] = $_GPC['birth']['day'];
			}
			if (!empty($_GPC['reside'])) {
				$_GPC['resideprovince'] = $_GPC['reside']['province'];
				$_GPC['residecity'] = $_GPC['reside']['city'];
				$_GPC['residedist'] = $_GPC['reside']['district'];
			}
			unset($_GPC['uid']);
			if(!empty($_GPC['fanid'])) {
								if(empty($_GPC['email']) && empty($_GPC['mobile'])) {
					$_GPC['email'] = md5($_GPC['openid']) . '@we7.cc';
				}
				$fanid = intval($_GPC['fanid']);
								$struct = array_keys(mc_fields());
				$struct[] = 'birthyear';
				$struct[] = 'birthmonth';
				$struct[] = 'birthday';
				$struct[] = 'resideprovince';
				$struct[] = 'residecity';
				$struct[] = 'residedist';
				$struct[] = 'groupid';
				unset($_GPC['reside'], $_GPC['birth']);

				foreach ($_GPC as $field => $value) {
					if(!in_array($field, $struct)) {
						unset($_GPC[$field]);
					}
				}
				if(!empty($_GPC['avatar'])) {
					if(strexists($_GPC['avatar'], 'attachment/images/global/avatars/avatar_')) {
						$_GPC['avatar'] = str_replace($_W['attachurl'], '', $_GPC['avatar']);
					}
				}
				$condition = '';
								if(!empty($_GPC['email'])) {
					$emailexists = pdo_fetchcolumn("SELECT email FROM ".tablename('mc_members')." WHERE uniacid = :uniacid AND email = :email " . $condition, array(':uniacid' => $_W['uniacid'], ':email' => trim($_GPC['email'])));
					if($emailexists) {
						unset($_GPC['email']);
					}
				}
				if(!empty($_GPC['mobile'])) {
					$mobilexists = pdo_fetchcolumn("SELECT mobile FROM ".tablename('mc_members')." WHERE uniacid = :uniacid AND mobile = :mobile " . $condition, array(':uniacid' => $_W['uniacid'], ':mobile' => trim($_GPC['mobile'])));
					if($mobilexists) {
						unset($_GPC['mobile']);
					}
				}
				$_GPC['uniacid'] = $_W['uniacid'];
				$_GPC['createtime'] = TIMESTAMP;
				pdo_insert('mc_members', $_GPC);
				$uid = pdo_insertid();
				pdo_update('mc_mapping_fans', array('uid' => $uid), array('fanid' => $fanid, 'uniacid' => $_W['uniacid']));
				message('更新资料成功！', url('mc/member/post', array('uid' => $uid)), 'success');
			} else {
				$email_effective = intval($_GPC['email_effective']);
				if(($email_effective == 1 && empty($_GPC['email']))) {
					unset($_GPC['email']);
				}
				$uid = mc_update($uid, $_GPC);
			}
		}
		message('更新资料成功！', referer(), 'success');
	}
	
	load()->func('tpl');
	$groups = mc_groups($_W['uniacid']);
	// $profile = mc_fetch($uid);
	$profile = pdo_fetch('SELECT * FROM '.tablename('members')." WHERE `uid` = :uid", array(':uid' => $uid));
	if(!empty($profile)) {
		if(empty($profile['email']) || (!empty($profile['email']) && substr($profile['email'], -6) == 'we7.cc' && strlen($profile['email']) == 39)) {
						$profile['email_effective'] = 1;
			$profile['email'] = '';
		} else {
						$profile['email_effective'] = 2;
		}
	}

	if(empty($uid)) {
		$fanid = intval($_GPC['fanid']);
		$tag = pdo_fetchcolumn('SELECT tag FROM ' . tablename('mc_mapping_fans') . ' WHERE uniacid = :uniacid AND fanid = :fanid', array(':uniacid' => $_W['uniacid'], ':fanid' => $fanid));
		if(is_base64($tag)){
			$tag = base64_decode($tag);
		}
		if(is_serialized($tag)){
			$fan = iunserializer($tag);
		}
		if(!empty($tag)) {
			if(!empty($fan['nickname'])) {
				$profile['nickname'] = $fan['nickname'];
			}
			if(!empty($fan['sex'])) {
				$profile['gender'] = $fan['sex'];
			}
			if(!empty($fan['city'])) {
				$profile['residecity'] = $fan['city'] . '市';
			}
			if(!empty($fan['province'])) {
				$profile['resideprovince'] = $fan['province'] . '省';
			}
			if(!empty($fan['country'])) {
				$profile['nationality'] = $fan['country'];
			}
			if(!empty($fan['headimgurl'])) {
				$profile['avatar'] = rtrim($fan['headimgurl'], '0') . 132;
			}
		}
	}
}else if($do == 'del') {
	$_W['page']['title'] = '删除会员资料 - 会员 - 会员中心';
	if(checksubmit('submit')) {
		if($uid) {
			$instr = implode(',',$uid);
			pdo_query("DELETE FROM ".tablename('mc_members')." WHERE `uniacid` = {$_W['uniacid']} AND `uid` IN ({$instr})");
			message('删除成功！', referer(), 'success');
		}
		message('请选择要删除的项目！', referer(), 'error');
	}
}else if($do == 'credit'){
	// $profile = mc_fetch($uid);
	$memberInfo = pdo_fetchall('SELECT * from '.tablename('members').'WHERE uid=:uid', array(':uid'=>intval($uid))
	);
	$memberInfo = isset($memberInfo[0]) ? $memberInfo[0] : [];
}else if($do == 'address'){
	load()->model('shopping.address');
	$res = Address_getUserAddressListByUid($uid);

	if($_W['isajax']){
		$addid = intval($_GPC['addid']);
		if(!empty($uid) and isset($uid)){
			$res=pdo_query("DELETE FROM ".tablename('shopping_address')." WHERE `id` = {$addid} ");
			if($res > 0){
				$info['status'] = 200;
			}else{
			   $info['status'] = -200;
			}
		}else{
			$info['status'] = -200;
		}
		ajaxReturn($info);
	}
}else if($do == 'orders'){
	$_W['page']['title'] = '用户订单记录 - 会员 - 会员中心';
	load()->model('shopping.order');
	$res = array();
	if ($uid) {
		$res = Order_getOrderListByUid($uid);
		$total = $res['total'];
		$pager = $res['pager'];
		$res = $res['data'];
	}
}

template('mc/member');