<?php
defined('IN_IA') or exit('Access Denied');

$uniacid = intval($_GPC['uniacid']);
$role = uni_permission($_W['uid'], $uniacid);
var_dump($role);
if(empty($role)) {
	message('操作失败, 非法访问.');
}
isetcookie('__uniacid', $uniacid, 7 * 86400);
header('location: ' . url('home/welcome'));