<?php
defined('IN_IA') or exit('Access Denied');

$dos = array('display', 'detail', 'add', 'del');
$do = in_array($do, $dos) ? $do : 'display';
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

$pindex = max(1, intval($_GPC['page']));
$psize = 15;
$condition  = $module = '';
$pars = array();

load()->func('tpl');
load()->model('shopping.topic');

if ($do == 'display') {
	$list = Topic_getListToManage();
} else if ($do == 'detail') {
	$detailData = Topic_getDetailToManage();
} else if($do=='add'){
	$topicid = intval($_GPC['topicid']);
	$op_type = ($topicid ? '修改' : '添加');

	if($operation == 'handle'){
		if(checksubmit('submit')){
			$topicid = Topic_saveInfo();
			message('专题信息更新成功！', url('shopping/topic',array('topicid'=>$topicid,'do'=>'detail')), 'success');
		} else {
			$item = Topic_getDetailToManage();
		}
	}
} else if ($do == 'del') {
	message('暂时还不能删除，需要此功能联系开发！', url('shopping/topic',array('topicid'=>$item['topicid'],'do'=>'detail')), 'success');
}
template('shopping/topic');