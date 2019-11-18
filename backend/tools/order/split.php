<?php
/**
 * Created by PhpStorm.
 * User: GarsonWang
 * Date: 2015/11/16
 * Time: 10:11
 */
include '../include.php';


/**
 * todo 目前是处理的所有数据
 * 后期的理想处理方式是：
 * 一旦有用户退款，立即推送，判断时机需要拆分
 * /

/**
 * 是否需要强制处理所有订单
 * 开启时机：
 * 1、需要处理某些订单的供应商异常的情况
 * 2、部分订单拆分异常的情况
 * 3、子订单数据异常：如价格、数量不对等
 */
$dealAll = true;//谨慎开启

$pool = array();//商品容器

$handle = fopen('php://stdin','r+');
$orders = [];
while ($row = fgets($handle)) {
	$row = explode("\t", trim($row));
	$sql = "SELECT * FROM " . tablename('shopping_order_goods') . " WHERE id = :id;";
	$item = pdo_fetch($sql, [':id' => $row[0]]);

	if(isset($pool[$item['goodsid']]) && is_array($pool[$item['goodsid']]) && $pool[$item['goodsid']]){
		$good = $pool[$item['goodsid']];
	}else{
		$sql = "SELECT * FROM " . tablename('shopping_goods') . " WHERE id = :id;";
		$good = pdo_fetch($sql, [':id' => $item['goodsid']]);
	}
	$sid = $good['sid'];//设置真实的sid
	$sid || $sid = 0;//没有指定供应商的产品
	$orders[$item['orderid']][$sid][] = $item;
}


foreach($orders as $parentOrderId => $order){
	$sql = "SELECT * FROM " . tablename('shopping_order') . " WHERE id = :id;";
	$parentOrder = pdo_fetch($sql, [':id' => $parentOrderId]);

	//重新拆分 todo

	if($parentOrder['pid']){//已拆分过的、或者是子订单，直接略过
		continue;
	}

	if(count($order) > 1){//超过一个供应商

		if($parentOrder['status'] != 1){//并且已付款，才需要拆分订单
			continue;
		}

		file_put_contents('/tmp/split.log', date('Y-m-d H:i:s') . "\t" . $parentOrderId . "\n",FILE_APPEND);

		foreach($order as $sid => $childOrder){
			$condition = array();
			foreach($parentOrder as $key => $value){
				if($key == 'id') continue;
				$condition[$key] = $value;
				//部分字段的值需要计算
				$condition['price'] = 0;
				foreach($childOrder as $item){
					$condition['price'] += $item['price'] * $item['total'];
				}
			}
			$condition['sid'] = $sid;//更新供应商
			$condition['pid'] = $parentOrderId;
			//生成子订单
			pdo_insert('shopping_order', $condition);
			//更新商品对应的子订单ID
			$childOrderId  = pdo_insertid();
			if($childOrderId){
				foreach($childOrder as $item){
					pdo_update('shopping_order_goods', ['orderid' => $childOrderId], ['id' => $item['id']]);
				}
			}
			//拆分标记
			pdo_update('shopping_order', ['pid' => -1], ['id' => $parentOrderId]);
		}
	}else{//不需要拆分的订单
		if($dealAll){
			$sid = key($order);
			if($parentOrder['sid'] != $sid){//更新供应商
				pdo_update('shopping_order', ['sid' => $sid], ['id' => $parentOrderId]);
			}
		}
	}
}



