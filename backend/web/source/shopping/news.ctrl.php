<?php
defined('IN_IA') or exit('Access Denied');

$dos = array('type', 'manage');
$do = in_array($do, $dos) ? $do : 'news';

$operations = array('display', 'detail', 'add', 'del');
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

$pindex = max(1, intval($_GPC['page']));
$psize = 30;
$condition  = $module = '';
$pars = array();
load()->func('tpl');
/**红包展示页*/
if ($do == 'news') {
	load()->model('news');
	//直接展示所有数据
	if ($operation=='display') {
		$list = News_getListToManage();
	}
	//查看红包详情
	if ($operation=='detail') {
		$detailData=News_getDetailToManage();
	}
	//编辑,添加红包
	if ($operation=='handle') {
		$typeid = intval($_GPC['id']);
		$detailData=News_getDetailToManage();
		//保存数据跳转到红包详情
		if($_POST && checksubmit('submit')){
			$id = News_saveInfo();
			message('新闻信息更新成功！', url('shopping/news/nwes',array('id'=>$id,'op'=>'detail')), 'success');
		}
	}

	//删除红包
	if ($operation=='del') {
		$cateid=$_GPC['id'];
		del_newsById();
		message('新闻删除成功！', url('shopping/news/nwes',array('op'=>'display')), 'success');
	}

	template('shopping/news');
}