<?php
/**
 * collect
 * 我的收藏
 */
defined('IN_IA') or exit('Access Denied');


/**
 * 删除收藏
 * @param id int 
 * @param type int
 */
function delCollectById($id = 0,$type = 1){
	global $_W;
	if($id === 0 || empty($_W['member']['uid'])){
		return false;
	}
	return pdo_delete('mc_goods_collect', array('uid' => $_W['member']['uid'], 'collectid' => $id, 'type' => $type,'weid' => $_W['uniacid']));
}


/**
 * 获取我的收藏
 * @param (int) type
 *
 */
function getCollectByType($type = 1){
	global $_W;
	return pdo_fetchall('SELECT b.id, b.thumb,b.title,b.marketprice,a.coid 
						 FROM '.tablename('mc_goods_collect'). 'a, '.tablename('shopping_goods').' b 
						 WHERE a.collectid = b.id and a.type = :type and a.uid = :uid', 
					array(':type'=>$type, ':uid' => $_W['member']['uid'])
				);
}

/**
 * 获取我对该商品收藏
 *	 collectid = 被收藏的ID
 *	 type = 1（商品） 
 */
function Collect_getMyCollectCountByGoodsId($goodsId = 0){
	global $_W;
	return pdo_fetchcolumn("SELECT count(coid) FROM " . tablename('mc_goods_collect') ." WHERE uid = :uid and collectid = :collectid and type = 1",
					array(':collectid'=>$goodsId,':uid' => $_W['member']['uid'])
				);
}