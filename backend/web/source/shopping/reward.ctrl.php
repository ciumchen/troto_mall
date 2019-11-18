<?php
defined('IN_IA') or exit('Access Denied');

$dos = array('audit', 'manage');
$do = in_array($do, $dos) ? $do : 'manage';

$operations = array('display', 'detail', 'add');
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

$pindex = max(1, intval($_GPC['page']));
$psize = 30;
$condition  = $module = '';
$pars = array();

load()->func('tpl');

if ($do == 'manage') {
	load()->model('reward.withdraw');
	if ($operation == 'display') {
		$list = Reward_getListToManage();
	} else if($operation=='handle'){
		$applyid = intval($_GPC['applyid']);
		$item = Reward_getDetailToManage();
		$item['user'] = pdo_fetchcolumn('SELECT nickname from '.tablename('members').' WHERE uid='.$item['brokerid']);
		$item['remark'] = json_decode($item['remark'], true);
		if($_POST && checksubmit('submit')){
			$applyid = Reward_saveInfo();
			message('佣金提现申请信息更新成功并已记录操作日志！', url('shopping/reward/manage',array('applyid'=>$applyid,'op'=>'handle')), 'success');
		}
	} else if ($operation == 'del') {
		message('暂时还不能删除，需要此功能联系开发！', url('shopping/reward/manage',array('id'=>$item['applyid'],'op'=>'detail')), 'success');
	}
	template('shopping/reward_manage');
}
