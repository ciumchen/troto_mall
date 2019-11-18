<?php
/**
 * 购物车管理
 * shopping.cart.mod.php
 *
 */

defined('IN_IA') or exit('Access Denied');

/**
 * 获取购物车数量
 * 
 */
function Cart_getCartTotal(){
	global $_GPC, $_W;
	$cartotal = pdo_fetchcolumn("SELECT sum(total) FROM " . tablename('shopping_cart') . " 
								 where weid = :weid and from_user = :from_user AND community = 0", 
								 array(':weid' => $_W['uniacid'], ':from_user' => $_W['fans']['from_user'])
								);
	if($cartotal >= 100){
		$cartotal = "99+";
	}
	return empty($cartotal) ? 0 : $cartotal;
}

/**
 * 获取购物车
 *
 */
function Cart_getMyCart(){
	global $_GPC, $_W;
	$list = pdo_fetchall("SELECT * FROM " . tablename('shopping_cart') . " 
						 WHERE  weid = :weid AND from_user = :fromuser AND community = 0", 
						 array(
						 	':fromuser' => $_W['fans']['from_user'], 
						 	':weid' => $_W['uniacid'])
					);
	if (!empty($list)) {
		foreach ($list as &$item) {
			$goods = pdo_fetch("SELECT  id,title, thumb, marketprice, unit, total,maxbuy FROM " . tablename('shopping_goods') . " WHERE id=:id limit 1", array(":id" => $item['goodsid']));
			//属性
			$option = pdo_fetch("select title,marketprice,stock from " . tablename("shopping_goods_option") . " where id=:id limit 1", array(":id" => $item['optionid']));
			if ($option) {
				$goods['title'] = $goods['title'];
				$goods['optionname'] = $option['title'];
				$goods['marketprice'] = $option['marketprice'];
				$goods['total'] = $option['stock'];
			}
			$item['goods'] = $goods;
			$item['totalprice'] = (floatval($goods['marketprice']) * intval($item['total']));
			// $totalprice += $item['totalprice'];
		}
		unset($item);
	}
	return $list;
}

/**
 * 修改购物车数量
 * @param id(购物车ID)
 * @param num(购物车数量)
 */
function Cart_putMyCartNumById($id = 0, $num = 0){
	global $_GPC, $_W;
	if($num == 0){
		$num = 1;
	}
	$sql = "update " . tablename('shopping_cart') . " set total = $num where id=:id";
	return pdo_query($sql, array(":id" => $id));
}

/**
 * 删除购物车
 */
function Cart_delMyCartById($id = 0){
	global $_GPC, $_W;
	return pdo_delete('shopping_cart', array('from_user' => $_W['fans']['from_user'], 'weid' => $_W['uniacid'], 'id' => $id));
}

/**
 * 删除全部
 */
function Cart_delAllById(){
	global $_W;
	pdo_delete('shopping_cart', array('from_user' => $_W['fans']['from_user'], 'weid' => $_W['uniacid']));
}

