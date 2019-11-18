<?php 

$_W['page']['title'] = '云服务诊断 - 云服务';
if(checksubmit()) {
	load()->model('setting');
	setting_save('', 'site');
	message('成功清除站点记录.', 'refresh');
}
setting_load('site');
if(empty($_W['setting']['site'])) {
	$_W['setting']['site'] = array();
}

template('cloud/diagnose');
