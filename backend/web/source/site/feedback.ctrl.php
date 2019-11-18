<?php
/**
 * 用户反馈
 * 
 */
defined('IN_IA') or exit('Access Denied');

$dos = array('display', 'view');
$do = in_array($do, $dos) ? $do : 'display';
load()->model('site.feedback');

if ($do == 'display') {
	$res = getUserFeedback();
}else if($do == 'view'){
	$id = (int)$_GPC['id'];
	$row = feedback_detail($id);
}
template('site/feedback');

