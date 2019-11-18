<?php
/**
 * 产品规格
 * shopping.spec.mod.php
 *
 */

defined('IN_IA') or exit('Access Denied');

/**
 * 获取产品规格名
 * 
 */
function Spec_getGoodsSpecByGoodsId($goodsid = 0){
	global $_GPC, $_W;
	return pdo_fetchall("select * from " . tablename('shopping_spec') . " where goodsid = :id order by displayorder asc", array(':id' => $goodsid));
}

/**
 * 保存产品规格(管理后台)
 *
 */
function Spec_saveGoodsSpec($id = 0){
	global $_GPC, $_W;
	$spec_ids = $_POST['spec_id'];
	$spec_titles = $_POST['spec_title'];
	$specids = array();
	$len = count($spec_ids);

	$spec_items = array();
	
	for ($k = 0; $k < $len; $k++) {
		$spec_id = "";
		$get_spec_id = $spec_ids[$k];
		
		$a = array(
			"weid" => $_W['uniacid'],
			"goodsid" => $id,
			"displayorder" => $k,
			"title" => $spec_titles[$get_spec_id]
		);
		if (is_numeric($get_spec_id)) {
			pdo_update("shopping_spec", $a, array("id" => $get_spec_id));
			$spec_id = $get_spec_id;
		} else {
			pdo_insert("shopping_spec", $a);
			$spec_id = pdo_insertid();
		}
		//子项
		$spec_item_ids = $_POST["spec_item_id_".$get_spec_id];
		$spec_item_titles = $_POST["spec_item_title_".$get_spec_id];
		$spec_item_shows = $_POST["spec_item_show_".$get_spec_id];
		$spec_item_thumbs = $_POST["spec_item_thumb_".$get_spec_id];
		$itemlen = count($spec_item_ids);
	
		$itemids = array();
		for ($n = 0; $n < $itemlen; $n++) {
			$item_id = "";
			$get_item_id = $spec_item_ids[$n];
			$d = array(
				"weid" => $_W['uniacid'],
				"specid" => $spec_id,
				"displayorder" => $n,
				"title" => $spec_item_titles[$n],
				"show" => $spec_item_shows[$n],
				"thumb"=>$spec_item_thumbs[$n]
			);
			if (is_numeric($get_item_id)) {
		
				pdo_update("shopping_spec_item", $d, array("id" => $get_item_id));
				$item_id = $get_item_id;
			} else {

				pdo_insert("shopping_spec_item", $d);
				$item_id = pdo_insertid();
			}
			$itemids[] = $item_id;
			//临时记录，用于保存规格项
			$d['get_id'] = $get_item_id;
			$d['id']= $item_id;
			$spec_items[] = $d;
		}
		//删除其他的
		if(count($itemids)>0){
			pdo_query("delete from " . tablename('shopping_spec_item') . " where weid={$_W['uniacid']} and specid=$spec_id and id not in (" . implode(",", $itemids) . ")");	
		}else{
			pdo_query("delete from " . tablename('shopping_spec_item') . " where weid={$_W['uniacid']} and specid=$spec_id");	
		}
		//更新规格项id
		pdo_update("shopping_spec", array("content" => serialize($itemids)), array("id" => $spec_id));
		$specids[] = $spec_id;
	}
	//删除其他的
	if( count($specids)>0){
		pdo_query("delete from " . tablename('shopping_spec') . " where weid={$_W['uniacid']} and goodsid=$id and id not in (" . implode(",", $specids) . ")");
	}
	else{
		pdo_query("delete from " . tablename('shopping_spec') . " where weid={$_W['uniacid']} and goodsid=$id");
	}
	return $spec_items;
}