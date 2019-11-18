<?php 
defined('IN_IA') or exit('Access Denied');

$do = !empty($do) ? $do : 'display';
$do = in_array($do, array('display', 'post', 'delete')) ? $do : 'display';
if($do == 'display'){
	load()->func('tpl');
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$type = (string) ($_GPC['searchtype']);
	$keywords = trim($_GPC['keywords']);
	$condition = 'uniacid = :uniacid';
	$params[':uniacid'] = $_W['uniacid'];

	switch ($type) {
		case 'uid':
			$condition .= " AND uid = :uid";
			$params[':uid'] = $keywords;
			break;
		case 'uname':
			$condition .= " AND fusername LIKE :fusername";
			$params[':fusername'] = "%{$keywords}%";
			break;
		case 'umobile':
			$condition .= " AND fmobile LIKE :fmobile";
			$params[':fmobile'] = "%{$keywords}%";
			break;
		case 'ip':
			$condition .= " AND fip = :fip";
			$params[':fip'] = $keywords;
			break;
		default:
			
			break;
	}

	$sql = "SELECT * FROM ".tablename("site_registration")." WHERE {$condition} GROUP BY rid DESC LIMIT ".($pindex - 1) * $psize.','.$psize;
	$list = pdo_fetchall($sql,$params);
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('site_registration') . " WHERE {$condition}",$params);

	$pager = pagination($total, $pindex, $psize);

	if($_W['isajax']){
		$rid = intval($_GPC['rid']);
		$message = array();
		if(empty($rid)):	
			$message['status'] = -200;
			$message['point'] = '参数不正确！';
			die(json_encode($message));
		endif;

		$result = pdo_delete("site_registration",array('rid' => $rid));
		if($result){
			$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('site_registration')." WHERE {$condition}",$params);
			$message['status'] = 200;
			$message['total'] = $total;
		} else{
			$message['status'] = -200;
			$message['point'] = "删除失败！";
		}
	die(json_encode($message));

	}

	template('site/sign');
}
