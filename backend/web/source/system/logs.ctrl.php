<?php 
defined('IN_IA') or exit('Access Denied');
$dos = array('wechat', 'system', 'database');
$do = in_array($do, $dos) ? $do : 'wechat';
load()->func('tpl');

$params = array();
$where  = '';
if ($_GPC['time']) {
		$starttime = strtotime($_GPC['time']['start']);
	$endtime = strtotime($_GPC['time']['end']);
	$timewhere = ' AND `createtime` >= :starttime AND `createtime` < :endtime';
	$params[':starttime'] = $starttime;
	$params[':endtime'] = $endtime;
}

if ($do == 'wechat') {
	$path = IA_ROOT . '/data/logs/';
	$files = glob($path . '*');
	$searchtime = date('Ymd', time());
	foreach ($files as $key=>$file) {
		$filename = substr($file, strripos($file, '/') + 1, 8);
		if ($filename == $searchtime) {
			$contents = file_get_contents($file);
		}
	}
	if (!empty($_GPC['searchtime'])) {
		$searchtime = $_GPC['searchtime'];
		$searchtime = str_replace('-', '', substr($searchtime, 0, 10));
				foreach ($files as $key=>$file) {
			$filename = substr($file, strripos($file, '/') + 1, 8);
			if ($filename == $searchtime) {
				$contents = file_get_contents($file);
			}
		}
	}
}

if ($do == 'system') {
	$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
	$where .= " WHERE `type` = '1'";
	$sql = 'SELECT * FROM ' . tablename('core_performance') . " $where $timewhere LIMIT " . ($pindex - 1) * $psize .','. $psize;
	$list = pdo_fetchall($sql, $params);
	foreach ($list as $key=>$value) {
		$list[$key]['type'] = '系统日志';
		$list[$key]['createtime'] = date('Y-m-d H:i:s', $value['createtime']);
	}
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('core_performance'). $where , $params);
	$pager = pagination($total, $pindex, $psize);
}

if ($do == 'database') {
	$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
	$where .= " WHERE `type` = '2'";
	$sql = 'SELECT * FROM ' . tablename('core_performance') . " $where $timewhere LIMIT " . ($pindex - 1) * $psize .','. $psize;
	$list = pdo_fetchall($sql, $params);
	foreach ($list as $key=>$value) {
		$list[$key]['type'] = '数据库日志';
		$list[$key]['createtime'] = date('Y-m-d H:i:s', $value['createtime']);
	}
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('core_performance'). $where , $params);
	$pager = pagination($total, $pindex, $psize);
}

template('system/logs');