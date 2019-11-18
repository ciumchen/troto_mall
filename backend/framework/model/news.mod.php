<?php
/**
 * 新闻
 * load()->model('news');
 */

/**
 * 获取所有新闻信息
 */
function News_getListToManage(){
	global $_W, $_GPC;
	$ret= pdo_fetchall('SELECT * FROM '.tablename('company_news').' ORDER BY id DESC');
	return $ret;
}

/**
 * 创建新闻
 */
function News_createNewOne($data) {
	return pdo_insert('company_news', $data);
}

/**
 * 更新新闻数据
 */
function News_saveInfo(){
	global $_W, $_GPC;
	$id = intval($_GPC['id']);
	$data = array(
			'createdt' => strtotime($_GPC['createdt']),
			'title' => $_GPC['title'],
			'intro' => $_GPC['intro'],
			'outlink' => $_GPC['outlink'],
			'content' => $_GPC['content'],
			'cateid'=> $_GPC['cateid'],
		);
	//过滤提示
	//if(!$data['uid']){ message('获取用户不能为空', referer(), 'error'); }
	//id存在保存数据,不存在添加数据
	if($id){
		$rs = pdo_update('company_news', $data, array('id' => $id));
	}else{
		pdo_insert('company_news', $data);
		$id = pdo_insertid();
		
	}
	return $id;
}

/**获取单个新闻详细*/
function news_getDetailToManage($id = 0){
	global $_W, $_GPC;
	$id || $id = $_GPC['id'];
	return pdo_fetch('select * from '.tablename('company_news').'where id = :id', array(':id' => intval($id)));
}

/***删除新闻**/
 function del_newsById(){
 	global  $_W,$_GPC;
 	$id = $_GPC['id'];
 	return pdo_delete('company_news',array('id'=>$id));
 }