<?php
/**
 * wayne
 * 
 */
defined('IN_IA') or exit('Access Denied');


/**
 * 获取分类导航栏(首页推荐)
 *
 */
function Category_getCategoryToIndex(){
	global $_W;
	return pdo_fetchall(" SELECT id,name,thumb,description FROM " . tablename('shopping_category').
						" WHERE weid = :weid AND parentid = 0 AND isrecommand = 1 AND enabled = '1' AND community = 0  
						  ORDER BY displayorder DESC ", array(':weid' => $_W['uniacid']));
}

/**
 * 获取产品分类
 *
 */
function Category_getCategoryToParent(){
	global $_W;
	return pdo_fetchall(" SELECT id,name,thumb,description FROM " . tablename('shopping_category') .
						" WHERE weid = :weid AND parentid = 0 AND enabled = '1' AND community = 0 
						  ORDER BY displayorder DESC ", array(':weid' => $_W['uniacid']));
}


/**
 * 根据主分类获取子分类列表
 * @param int $pcateid 主分类id
 * @return array
 */
function Category_getCategoryChildsByPcate($pcateId){
	global $_W;
	return pdo_fetchall(" SELECT id,name,thumb,description,displayorder FROM " . tablename('shopping_category') .
						" WHERE weid = :weid AND parentid=:pcateId AND enabled='1' ORDER BY displayorder DESC ", array(':weid' => $_W['uniacid'], ':pcateId'=>$pcateId));
}