<?php
/**
 * 产品自定义属性
 * load()->model('shopping.goods.param');
 *
 */

defined('IN_IA') or exit('Access Denied');

/**
 * 获取产品规格名
 * 
 */
function Param_getParamByGoodsId($goodsId = 0){
	global $_GPC, $_W;
	return pdo_fetchall("SELECT * FROM " . tablename('shopping_goods_param') . " WHERE goodsid = :goodsid order by displayorder asc", array(":goodsid" => $goodsId));
}

/**
 * 获取产地
 * 
 */
function Param_getFromByGoodsid($goodsId = 0){
	global $_GPC, $_W;
	return pdo_fetchall("SELECT title,value FROM " . tablename('shopping_goods_param') . " WHERE goodsid = :goodsid and (title = '产地' or title = 'from') order by displayorder asc", 
		array(":goodsid" => $goodsId));
}
/**
 * 处理自定义参数
 */
function Param_saveGoodsParam($id = 0){
	global $_GPC, $_W;
	$param_ids = $_POST['param_id'];
	$param_titles = $_POST['param_title'];
	$param_values = $_POST['param_value'];
	$param_displayorders = $_POST['param_displayorder'];
	$len = count($param_ids);
	$paramids = array();
	for ($k = 0; $k < $len; $k++) {
		$param_id = "";
		$get_param_id = $param_ids[$k];
		$a = array(
			"title"        => $param_titles[$k],
			"value"        => $param_values[$k],
			"goodsid"      => $id,
			"displayorder" => $param_displayorders[$k],
		);
		if (!is_numeric($get_param_id)) {
			pdo_insert("shopping_goods_param", $a);
			$param_id = pdo_insertid();
		} else {
			pdo_update("shopping_goods_param", $a, array('id' => $get_param_id));
			$param_id = $get_param_id;
		}
		$paramids[] = $param_id;
	}
	if (count($paramids) > 0) {
		pdo_query("DELETE FROM " . tablename('shopping_goods_param') . " WHERE goodsid = $id and id not in ( " . implode(',', $paramids) . ")");
	}else{
		pdo_query("DELETE FROM " . tablename('shopping_goods_param') . " WHERE goodsid = $id");
	}
}