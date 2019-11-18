<?php
/**
 * load()->model('shopping.order.goods');
 * 订单商品表
 */

/**
 * 获取订单商品的基础信息
 * @param int orderid 订单ID
 * @param int deleted = 1 移出订单 
 */
function Sogoods_getTitleInfo($orderid = 0, $deleted = 0){
	global $_W;
	$res = pdo_fetchall('SELECT o.price, o.total, o.optionname, g.title  
						 FROM '.tablename('shopping_order_goods').' o left join '.tablename('shopping_goods').' g on o.goodsid = g.id 
						 WHERE o.orderid = :orderid AND o.weid = :weid AND o.deleted = :deleted', 
						 array(':orderid' => $orderid, ':weid' => $_W['uniacid'], ':deleted' => $deleted));
	return $res;
}

function Goods_confirmSendOrderGoods($goodsid=0,$orderid = 0){
	global $_GPC, $_W;
	pdo_update(
		'shopping_order',
		array(
			'status' => 2,
			'remark' => $_GPC['remark'],
			'sendexpress' => TIMESTAMP,
		),
		array('id' => $orderid,'cancelgoods'=>0)
	);
	pdo_update(
		'shopping_order_goods',
		array(
			'express' => $_GPC['expresscom'],
			'expresssn' => $_GPC['expresssn'],
			'expresscom' => $_GPC['express'],
			'expresstime' => TIMESTAMP,
			),
		array('goodsid' => $goodsid, 'orderid' => $orderid)
		);
	$info = pdo_fetchall('select b.title from ims_shopping_order_goods a,ims_shopping_goods b 
						  where a.orderid = :orderid and a.goodsid = b.id and a.goodsid = :goodsid and (a.cancelgoods = 0 || a.cancelgoods = 2)',
						  array(':orderid'=>$orderid,':goodsid' => $goodsid));
	$title = '';

	foreach ($info as $key => $value) {
		$title = $value['title'];
	}
	return $title;
}

/**
 * 根据订单ID获取订单下的商品
 *
 */
function sogoods_listByOrderId($id){
	global $_W;
	$condition = " WHERE o.orderid='{$id}' ";
	if($_W['user']['sid']){
		$condition .= ' and o.sid = '.$_W['user']['sid'];
	}
	return pdo_fetchall("SELECT g.title, g.id, g.type, g.status, g.wid, g.taxrate,
							o.id, o.goodsid,o.sid,o.cancelgoods,o.total, o.optionname, o.optionid, o.price , o.status, o.deleted, cp.pathwayid, cp.cabinetid
					    FROM " . tablename('shopping_order_goods') ." o left join " . tablename('shopping_goods') . " g on o.goodsid=g.id  left join" . tablename('cabinet_pathway') . 'cp on cp.goodsid = g.id' . $condition . 'order by o.orderid desc');
}

/**
 * 根据订单ID获取订单下的商品及其属性信息
 * @param intval $orderId 订单号
 * @return array
 */
function goods_getOrderGoodsDetailByOrderId($orderId){
	global $_W;
	return pdo_fetchall('SELECT tba.goodsid,tba.optionid,tba.optionname,tba.total as num,tbb.status,tbb.title,tbb.thumb,tbb.marketprice,tbb.total,tbc.marketprice as optionmarketprice,tbc.stock
			FROM '.tablename('shopping_order_goods').' tba 
			LEFT JOIN '.tablename('shopping_goods').' tbb ON tbb.id=tba.goodsid 
			LEFT JOIN '.tablename('shopping_goods_option').' tbc on tbc.goodsid=tba.goodsid
			WHERE tba.orderid=:id AND tba.weid=:weid AND (tba.optionid=tbc.id || tba.optionid=0)', array(':id'=>$orderId, ':weid'=>$_W['uniacid']));
}
