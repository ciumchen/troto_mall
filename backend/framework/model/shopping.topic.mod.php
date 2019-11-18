<?php
/**
 * load()->model('shopping.topic');
 */

/**
 * 获取专题列表
 */
function Topic_getListToManage(){
	global $_W, $_GPC;
	return pdo_fetchall('SELECT * FROM '.tablename('shopping_topic').'ORDER BY topicid DESC');
}

/**
 * 保存专题信息
 */
function Topic_saveInfo(){
	global $_W, $_GPC;
	$topicid = intval($_GPC['topicid']);
	$data = array(
			'title' => trim($_GPC['title']),
			'description' => trim($_GPC['description']),
			'enabled' => intval($_GPC['enabled']),
			'isfocus' => intval($_GPC['isfocus']),
			'displayorder' => intval($_GPC['displayorder']),
			'link' => trim($_GPC['link']),
			'thumb' => trim($_GPC['thumb']),
			'content' => trim($_GPC['content']),
			'starttime' => strtotime($_GPC['starttime']), 
			'endtime' => strtotime($_GPC['endtime']), 
		);

	//过滤提示
	if(empty($data['title'])){ message('专题名称不能为空', referer(), 'error'); }
	if(empty($data['thumb'])){ message('专题图片不能为空', referer(), 'error'); }
	if(empty($data['starttime'])){ message('开始时间不能为空', referer(), 'error'); }
	if(empty($data['endtime'])){ message('结束时间不能为空', referer(), 'error'); }

	if ($data['link']!='') {
		$data['isjump'] = 1;
	}
	if($topicid){
		pdo_update('shopping_topic', $data, array('topicid' => $topicid));
	}else{
		$data['createtime'] = time();
		pdo_insert('shopping_topic', $data);
		$topicid = pdo_insertid();
	}
	return $topicid;
}

/**
 * 获取专题的详细信息
 */
function Topic_getDetailToManage($topicid = 0){
	global $_W, $_GPC;
	$topicid || $topicid = $_GPC['topicid'];
	return pdo_fetch('select * from '.tablename('shopping_topic').' where topicid = :topicid', array(':topicid' => intval($topicid)));
}