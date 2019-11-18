<?php
defined('IN_IA') or exit('Access Denied');

load()->model('mc');
$dos = array('display', 'view', 'initsync', 'updategroup', 'sms', 'cancelParent');
$do = in_array($do, $dos) ? $do : 'display';
if($do == 'display') {
	$_W['page']['title'] = '微信用户列表 - 微信 - 会员中心';
	# 删除微信用户
	if(checksubmit('submit')) {
		if (!empty($_GPC['delete'])) {
			$fanids = array();
			foreach($_GPC['delete'] as $v) {
				$fanids[] = intval($v);
			}
			pdo_query("DELETE FROM " . tablename('mc_mapping_fans') . " WHERE uniacid = :uniacid AND fanid IN ('" . implode("','", $fanids) . "')",array(':uniacid' => $_W['uniacid']));
			message('微信用户删除成功！', url('mc/fans/', array('type' => $_GPC['type'], 'acid' => $_GPC['acid'])), 'success');
		}
	}
	$accounts = uni_accounts();
	if(empty($accounts) || !is_array($accounts) || count($accounts) == 0){
		message('请指定公众号');
	}
	if(!isset($_GPC['acid'])){
		$account = current($accounts);
		if($account !== false){
			$acid = intval($account['acid']);
		}
	} else {
		$acid = intval($_GPC['acid']);
		if(!empty($acid) && !empty($accounts[$acid])) {
			$account = $accounts[$acid];
		}
	}
	reset($accounts);
	
	if($_W['isajax']) {
		$post = $_GPC['__input'];
		if($post['method'] == 'sync') {
			if(is_array($post['fanids'])) {
				$fanids = array();
				foreach($post['fanids'] as $fanid) {
					$fanid = intval($fanid);
					$fanids[] = $fanid;
				}
				$fanids = implode(',', $fanids);
				$sql = 'SELECT `fanid`,`uid`,`openid` FROM ' . tablename('mc_mapping_fans') . " WHERE `acid`='{$acid}' AND `fanid` IN ({$fanids})";
				$ds = pdo_fetchall($sql);
				$acc = WeAccount::create($acid);
				foreach($ds as $row) {
					$fan = $acc->fansQueryInfo($row['openid'], true);
					if(!is_error($fan)) {
						$group = $acc->fetchFansGroupid($row['openid']);
						$record = array();
						if(!is_error($group)) {
							$record['groupid'] = $group['groupid'];
						}
						$record['updatetime'] = TIMESTAMP;
						$record['followtime'] = $fan['subscribe_time'];
						$fan['nickname'] = stripcslashes($fan['nickname']);
						$record['nickname'] = stripslashes($fan['nickname']);
						$record['tag'] = iserializer($fan);
						$record['tag'] = base64_encode($record['tag']);
						pdo_update('mc_mapping_fans', $record, array('fanid' => $row['fanid']));
						
						if(!empty($row['uid'])) {
							$user = mc_fetch($row['uid'], array('nickname', 'gender', 'residecity', 'resideprovince', 'nationality', 'avatar'));
							$rec = array();
							if(empty($user['nickname']) && !empty($fan['nickname'])) {
																$rec['nickname'] = stripslashes($fan['nickname']);
							}
							if(empty($user['gender']) && !empty($fan['sex'])) {
								$rec['gender'] = $fan['sex'];
							}
							if(empty($user['residecity']) && !empty($fan['city'])) {
								$rec['residecity'] = $fan['city'] . '市';
							}
							if(empty($user['resideprovince']) && !empty($fan['province'])) {
								$rec['resideprovince'] = $fan['province'] . '省';
							}
							if(empty($user['nationality']) && !empty($fan['country'])) {
								$rec['nationality'] = $fan['country'];
							}
							if(empty($user['avatar']) && !empty($fan['headimgurl'])) {
								$rec['avatar'] = rtrim($fan['headimgurl'], '0') . 132;
							}
							if(!empty($rec)) {
								pdo_update('mc_members', $rec, array('uid' => $row['uid']));
							}
						}
					}
				}
			}
			exit('success');
		}
		if($post['method'] == 'download') {
			$acc = WeAccount::create($acid);
			if(!empty($post['next'])) {
				$_GPC['next_openid'] = $post['next'];
			}
			$fans = $acc->fansAll();
			if(!is_error($fans) && is_array($fans['fans'])) {
				$count = count($fans['fans']);
				$buffSize = ceil($count / 500);
				for($i = 0; $i < $buffSize; $i++) {
					$buffer = array_slice($fans['fans'], $i * 500, 500);
					$openids = implode("','", $buffer);
					$openids = "'{$openids}'";
					$sql = 'SELECT `openid` FROM ' . tablename('mc_mapping_fans') . " WHERE `acid`={$acid} AND `openid` IN ({$openids})";
					$ds = pdo_fetchall($sql);
					$exists = array();
					foreach($ds as $row) {
						$exists[] = $row['openid'];
					}
					$sql = '';
					foreach($buffer as $openid) {
						if(!empty($exists) && in_array($openid, $exists)) {
							continue;
						}
						$salt = random(8);
						$sql .= "('{$acid}', '{$_W['uniacid']}', 0, '{$openid}', '{$salt}', 1, 0, ''),";
					}
					if(!empty($sql)) {
						$sql = rtrim($sql, ',');
						$sql = 'INSERT INTO ' . tablename('mc_mapping_fans') . ' (`acid`, `uniacid`, `uid`, `openid`, `salt`, `follow`, `followtime`, `tag`) VALUES ' . $sql;
						pdo_query($sql);
					}
				}

				$ret = array();
				$ret['total'] = $fans['total'];
				$ret['count'] = count($fans['fans']) + 2;
				if(!empty($fans['next'])) {
					$ret['next'] = $fans['next'];
				}
				exit(json_encode($ret));
			} else {
				exit(json_encode($fans));
			}
		}
	}
	
	# 微信用户信息
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$pars = array();
	$condition = ' where a.`uniacid`=:uniacid ';
	$pars[':uniacid'] = $_W['uniacid'];
	
	$nickname = trim($_GPC['nickname']);
	if(!empty($nickname)) {
		$condition .= " AND a.nickname LIKE '%{$nickname}%' ";
	}
	$uid = trim($_GPC['uid']);
	if(!empty($uid)){
		$condition .= ' and a.uid = :uid ';
		$pars[':uid'] = $uid;
	}
	$openid = trim($_GPC['openid']);
	if(!empty($openid)){
		$condition .= " AND a.openid LIKE '%{$openid}%'  ";
	}
	if(empty($fansuid)){
		if(!empty($acid)) {
			$condition .= ' AND a.`acid`=:acid';
			$pars[':acid'] = $acid;
		}
	}
	if($_GPC['type'] == 'bind') {
		$condition .= ' AND a.`uid`>0';
		$type = 'bind';
	}
	if($_GPC['type'] == 'unbind') {
		$condition .= ' AND a.`uid`=0';
		$type = 'unbind';
	}
	$groups_data = pdo_fetchall('SELECT * FROM ' . tablename('mc_fans_groups') . ' WHERE uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
	if(!empty($groups_data)) {
		$groups = array();
		foreach($groups_data as $gr) {
			$groups[$gr['acid']] = iunserializer($gr['groups']);
		}
	}
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('mc_mapping_fans').' a '.$condition, $pars);

	$list = pdo_fetchall("SELECT a.fanid, a.uid, a.openid, a.follow, a.followtime, a.tag, a.groupid, a.acid, a.nickname
		FROM ".tablename('mc_mapping_fans') .'a '. $condition ." ORDER BY a.`fanid` DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $pars);
	if(!empty($list)) {
		foreach($list as &$v) {
			if (!empty($v['tag']) && is_string($v['tag'])) {
				if (is_base64($v['tag'])){
					$v['tag'] = base64_decode($v['tag']);
				}
								if (is_serialized($v['tag'])) {
					$v['tag'] = @iunserializer($v['tag']);
				}
				if(!empty($v['tag']['headimgurl'])) {
					$v['avatar'] = tomedia($v['tag']['headimgurl']);
					
				}
			}
			if(empty($v['tag'])) {
				$v['tag'] = array();
			}
			$v['account'] = $accounts[$v['acid']]['name'];
			unset($user,$niemmo,$niemmo_effective);
		}
	}
	$pager = pagination($total, $pindex, $psize);
}

if($do == 'view') {
	$_W['page']['title'] = '微信用户详情 - 微信 - 会员中心';
	$fanid = intval($_GPC['id']);
	if(empty($fanid)) {
		message('访问错误.');
	}
	load()->model('mc.mapping.fans');
	$row = fans_detail(array('fanid' => $fanid));
	if($row){
		$account = WeAccount::create($row['acid']);
		$accountInfo = $account->fetchAccountInfo();
		$row['account'] = $accountInfo['name'];
	}
	if(!$row['uid']){
		$row['user'] = '未登记为会员 (不关注且不授权的用户)';
	}else{
		$row['user'] = $row['nickname'] ? $row['nickname'] : $row['tag']['nickname'];
	}
}else if($do == 'initsync') {
	$acid = intval($_GPC['acid']);

	if(intval($_GPC['page']) == 0) {
		message('正在更新微信用户数据,请不要关闭浏览器', url('mc/fans/initsync', array('page' => 1, 'acid' => $acid)), 'success');
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 50;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mc_mapping_fans') . " WHERE uniacid = :uniacid AND acid = :acid", array(':uniacid' => $_W['uniacid'], ':acid' => $acid));
	$total_page = ceil($total / $psize);
	$ds = pdo_fetchall("SELECT * FROM ".tablename('mc_mapping_fans') ." WHERE uniacid = :uniacid AND acid = :acid ORDER BY `fanid` DESC LIMIT ".($pindex - 1) * $psize.','.$psize, array(':uniacid' => $_W['uniacid'], ':acid' => $acid));
	$acc = WeAccount::create($acid);
	if(!empty($ds)) {
		foreach($ds as $row) {
			if(!empty($row['tag'])) {
				continue;
			}
			$fan = $acc->fansQueryInfo($row['openid'], true);
			if(!is_error($fan)) {
				$group = $acc->fetchFansGroupid($row['openid']);
				$record = array();
				if(!is_error($group)) {
					$record['groupid'] = $group['groupid'];
				}
				$record['updatetime'] = TIMESTAMP;
				$record['followtime'] = $fan['subscribe_time'];
				$fan['nickname'] = stripcslashes($fan['nickname']);
				$record['nickname'] = stripslashes($fan['nickname']);
				$record['tag'] = iserializer($fan);
				$record['tag'] = base64_encode($record['tag']);
				pdo_update('mc_mapping_fans', $record, array('fanid' => $row['fanid']));
				
				if(!empty($row['uid'])) {
					$user = mc_fetch($row['uid'], array('nickname', 'gender', 'residecity', 'resideprovince', 'nationality', 'avatar'));
					$rec = array();
					if(empty($user['nickname']) && !empty($fan['nickname'])) {
						$rec['nickname'] = stripslashes($fan['nickname']);
					}
					if(empty($user['gender']) && !empty($fan['sex'])) {
						$rec['gender'] = $fan['sex'];
					}
					if(empty($user['residecity']) && !empty($fan['city'])) {
						$rec['residecity'] = $fan['city'] . '市';
					}
					if(empty($user['resideprovince']) && !empty($fan['province'])) {
						$rec['resideprovince'] = $fan['province'] . '省';
					}
					if(empty($user['nationality']) && !empty($fan['country'])) {
						$rec['nationality'] = $fan['country'];
					}
					if(empty($user['avatar']) && !empty($fan['headimgurl'])) {
						$rec['avatar'] = rtrim($fan['headimgurl'], '0') . 132;
					}
					if(!empty($rec)) {
						pdo_update('mc_members', $rec, array('uid' => $row['uid']));
					}
				}
			}
		}
	}
	$pindex++;
	$log = ($pindex - 1) * $psize;
	if($pindex > $total_page) {
		message('微信用户数据更新完成', url('mc/fans'), 'success');
	} else {
		message('正在更新微信用户数据,请不要关闭浏览器,已完成更新 ' . $log . ' 条数据。', url('mc/fans/initsync', array('page' => $pindex, 'acid' => $acid)));
	}
}

if($do == 'updategroup') {
	if($_W['isajax']) {
		$acid = intval($_GPC['acid']);
		$groupid = intval($_GPC['groupid']);
		$openid = trim($_GPC['openid']);
		if($acid > 0 && !empty($openid)) {
			$acc = WeAccount::create($acid);
			$data = $acc->updateFansGroupid($openid, $groupid);
			if(is_error($data)) {
				exit(json_encode(array('status' => 'error', 'mess' => $data['message'])));
			} else {
				pdo_update('mc_mapping_fans', array('groupid' => $groupid), array('uniacid' => $_W['uniacid'], 'openid' => $openid));
				exit(json_encode(array('status' => 'success')));
			}
		} else {
			exit(json_encode(array('status' => 'error', 'mess' => '公众号信息和微信用户openid错误')));
		}
	}
}
if($do == 'cancelParent'){
	//删除上家
	$downid = intval($_GPC['uid']);
	
	pdo_update('mc_members', array('pid' => 0), array('uid' => $downid));

	pdo_update('mc_relation', array('uid1' => '0', 'uid2' => '0', 'uid3' => '0'), array('uid' => $downid));
	pdo_update('mc_relation', array('uid2' => '0', 'uid3' => '0'), array('uid1' => $downid));
	pdo_update('mc_relation', array('uid3' => '0'), array('uid2' => $downid));
	
	message('修改成功',  referer(), 'success');
}
template('mc/fans');