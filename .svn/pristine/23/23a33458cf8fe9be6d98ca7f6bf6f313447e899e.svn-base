<?php
/**
 * shopping.order.aftermarket.mod.php
 * 订单售后表
 */

/**
 * 保存用户售后信息
 * @param int id(订单ID)
 * @param int ogid(订单商品表ID)
 * @param int type(售后类型：1换货2退货) 
 * @param int num(数量) 
 * @param str desc(描述)
 * @param str imgUrl(图片集)
 */
function Aftermarket_saveInfo(){
	global $_W, $_GPC;
	$imgUrl = is_array($_GPC['imgUrl']) ? serialize($_GPC['imgUrl']) : '';			//图片
	if(empty($imgUrl)){
		return -404;
	}
	$orderid = intval($_GPC['id']);
	$ogid = intval($_GPC['ogid']);

	$res = pdo_fetch('SELECT a.addressid, count(a.id) num, a.cancelgoods, o.cancelgoods canceltype, o.`status`
					  FROM '.tablename('shopping_order').' a left join '.tablename('shopping_order_goods').' o on a.id = o.orderid
					  WHERE a.accomplish = 0 AND a.weid = :weid AND a.id = :orderid AND o.id = :ogid', 
					  array(':weid' => $_W['uniacid'], ':orderid' => $orderid, ':ogid' => $ogid));
	if($res['num'] && $res['canceltype'] == 0 && $res['status'] == 0){
		$data = array(
				'uniacid' => $_W['uniacid'],
				'uid' => $_W['member']['uid'],
				'orderid' => $orderid,						//订单ID
				'ogid' => $ogid,							//order_goods id
				'type' => intval($_GPC['type']),			//售后类型
				'desc' => ihtmlspecialchars($_GPC['desc']),	//描述
				'num' => intval($_GPC['num']),				//数量
				'thumbs' => $imgUrl,						//图片
				'createtime' => TIMESTAMP,
				'useraddrid' => $res['addressid']
			);
		//不等于申请中时置为1
		if($res['cancelgoods'] != 1){
			pdo_update('shopping_order', array('cancelgoods' => 1), array('id' => $orderid, 'uid' => $_W['member']['uid'], 'weid' => $_W['uniacid']));
		}
		pdo_update('shopping_order_goods', array('cancelgoods' => $data['type'], 'status' => 1), array('id' => $ogid, 'weid' => $_W['uniacid'], 'orderid' => $orderid));
		pdo_insert('shopping_order_aftermarket', $data);
		return pdo_insertid();
	}
	return false;
}

/**
 * 售后列表
 *
 */
function Aftermarket_getList(){
	global $_W;
	$list = pdo_fetchall('SELECT a.oaid, a.`type`, a.`status`, a.num, a.createtime aftermarkettime,b.createtime, b.ordersn, c.optionname, d.title, d.thumb
				FROM '.tablename('shopping_order_aftermarket').' a left join '.tablename('shopping_order').' b on a.orderid = b.id 
				left join '.tablename('shopping_order_goods').' c on a.ogid = c.id left join '.tablename('shopping_goods').' d on c.goodsid = d.id
				where a.uid = :uid and a.uniacid = :uniacid order by a.createtime desc
				', array(':uid' => $_W['member']['uid'], ':uniacid' => $_W['uniacid']));
	if(!empty($list)){
		foreach($list as &$item){
			$item['thumb'] = tomedia($item['thumb']);
		}
	}
	return $list;
}

/**
 * 售后详细
 *
 *
 */
function Aftermarket_getItem($id = 0){
	global $_W;
	$item = pdo_fetch('SELECT a.oaid, a.`type`, a.`status`, a.num, a.createtime aftermarkettime, a.said, a.useraddrid, b.createtime, b.ordersn, c.optionname, d.title, d.thumb
				FROM '.tablename('shopping_order_aftermarket').' a left join '.tablename('shopping_order').' b on a.orderid = b.id 
				left join '.tablename('shopping_order_goods').' c on a.ogid = c.id left join '.tablename('shopping_goods').' d on c.goodsid = d.id
				where a.uid = :uid and a.uniacid = :uniacid and a.oaid = :oaid
				', array(':uid' => $_W['member']['uid'], ':uniacid' => $_W['uniacid'], ':oaid' => $id));
	if(!empty($item)){
		$item['thumb'] = tomedia($item['thumb']);
		if($item['status'] >= 1){
			if($item['said']){
				$item['SupplierAddress'] = pdo_fetch('SELECT realname, mobile, province, city, area, address FROM '.tablename('shopping_suppliers_address').' 
													where uniacid = :uniacid and said = :said', 
													array(':uniacid' => $_W['uniacid'], ':said' => $item['said']));
			}
			if($item['useraddrid']){
				$item['UserAddress'] = pdo_fetch('SELECT realname, mobile, province, city, area, address FROM '.tablename('shopping_address').' 
													where weid = :weid and id = :useraddrid', 
													array(':weid' => $_W['uniacid'], ':useraddrid' => $item['useraddrid']));	
			}
		}

	}
	return $item;	
}


/**
 * 用户确认售后操作 
 * @param int id 		售后表ID
 * @param int type 		售后表类型
 * @param int express 	售后表快递号
 * @param int addressid 地址ID
 */
function Aftermarket_ConfirmOperation(){
	global $_W, $_GPC;
	load()->model('shopping.address');
	/**
	 * 判断数据真实性
	 * @param type {1：换货，2：退货}
	 * 			1：用户地址，物流单号
	 *			2：物流单号
	 */
	// $count = pdo_fetchcolumn('')
	$id = intval($_GPC['id']);
	$type = intval($_GPC['type']);
	$express = trim($_GPC['express']);
	if(empty($express)){
		return -101;
	}
	$res = pdo_fetch('SELECT count(a.oaid) oanum, b.id ogid FROM '.tablename('shopping_order_aftermarket').' a LEFT JOIN '.tablename('shopping_order_goods').' b on a.ogid = b.id 
			WHERE a.oaid = :oaid AND a.`status` = 1 ', array(':oaid'=>$id));
	if($res['oanum']){
		$condition = array('expresssn' => $express, 'status' => 2, 'expresstime' => TIMESTAMP);
		if($type == 1){
			$addressid = intval($_GPC['addressid']);
			$addrRes = Address_saveUser($addressid);
			if($addrRes != null && is_int($addrRes) && empty($addressid)){
				$condition['useraddrid'] = $addrRes;
			}
		}
		pdo_update('shopping_order_aftermarket', $condition, array('oaid' => $id,'status' => 1));
		pdo_update('shopping_order_goods', array('status' => 3), array('id' => $res['ogid'], 'status' => 2));
		return true;
	}
	return false;
}

/**
 * 售后订单
 * @param status 审核售后订单
 * @param accomplish 售后完成订单
 * @param type 售后类型
 * @param time 时间
 * @param uid
 * @param nickname
 */
function Order_getOrderAftermarketListToManage(){
	global $_GPC, $_W;
	$ret = array('param'=>array());
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$condition = " oa.uniacid = :uniacid";
	$paras = array(':uniacid' => $_W['uniacid']);
	$ret['param']['uid'] = $_GPC['uid'];
	$ret['param']['status'] = $_GPC['status'];
	$ret['param']['type'] = $_GPC['type'];
	$ret['param']['accomplish'] = $_GPC['accomplish'];

	if (!empty($_GPC['time'])) {
		$paras[':starttime'] = $ret['param']['starttime'] = strtotime($_GPC['time']['start']);
		$paras[':endtime'] = $ret['param']['endtime'] = strtotime($_GPC['time']['end']) + 86399;
		$condition .= " AND oa.createtime >= :starttime AND oa.createtime <= :endtime ";
	}
	if (!isset($ret['param']['starttime']) || !isset($ret['param']['endtime'])) {
		$ret['param']['starttime'] = strtotime('-1 month');
		$ret['param']['endtime'] = time();
	}
	//付款类型
	if (!empty($_GPC['paytype']) || $_GPC['paytype'] === '0') {
		$condition .= " AND o.paytype = '{$_GPC['paytype']}'";
	}
	//订单号
	if (!empty($_GPC['keyword'])) {
		$condition .= " AND o.ordersn LIKE '%{$_GPC['keyword']}%'";
	}
	//姓名或手机
	if (($member = $_GPC['member'])) {
		$addressid = pdo_fetchcolumn("select a.id from ".tablename('shopping_address')."a where a.realname LIKE '%{$member}%' or a.mobile LIKE '%{$member}%'");	
		$condition .= " AND o.addressid = ".intval($addressid);
	}
	//姓名或手机
	if (($nickname = $_GPC['nickname'])) {
		$userid = pdo_fetchcolumn("select a.uid from ".tablename('mc_members')."a where a.realname LIKE '%{$nickname}%' or a.nickname LIKE '%{$nickname}%'");
		$condition .= " AND o.uid = ".intval($userid);
		
	}
	//订单状态
	if ($ret['param']['status'] != '') {
		$condition .= ' AND oa.status = '.intval($ret['param']['status']);
	}
	//售后类型
	if (!empty($ret['param']['type'])) {
		$condition .= ' AND oa.type = '.intval($ret['param']['type']);
	}
	//订单完成
	if ($ret['param']['accomplish'] != ''){
		$condition .= ' AND oa.accomplish = ' . intval($ret['param']['accomplish']);	
	}
	//用户UID
	if (!empty($ret['param']['uid'])){
		$condition .= ' AND oa.uid = ' . intval($ret['param']['uid']);	
	}

	$ret['list'] = pdo_fetchall('SELECT 
				oa.oaid, oa.`type`, oa.`status`, oa.num, oa.createtime, oa.accomplish, oa.desc, oa.uid, oa.expresssn,
				o.ordersn, 
				og.optionname, og.price,  og.total, 
				g.title, g.unit
				FROM '.tablename('shopping_order_aftermarket').' oa left join '.tablename('shopping_order').' o on oa.orderid = o.id 
				left join '.tablename('shopping_order_goods').' og on oa.ogid = og.id left join '.tablename('shopping_goods').' g on og.goodsid = g.id
				where '.$condition.' 
				order by oa.createtime desc
				', $paras);
	$paytype = array (
			'0' => array('css' => 'default', 'name' => '未支付'),
			'1' => array('css' => 'danger','name' => '余额支付'),
			'2' => array('css' => 'info', 'name' => '在线支付'),
			'3' => array('css' => 'warning', 'name' => '货到付款')
	);
	$ret['total'] = pdo_fetchcolumn('SELECT  count(oa.oaid)
				FROM '.tablename('shopping_order_aftermarket').' oa left join '.tablename('shopping_order').' o on oa.orderid = o.id 
				left join '.tablename('shopping_order_goods').' og on oa.ogid = og.id left join '.tablename('shopping_goods').' g on og.goodsid = g.id
				where '.$condition, $paras);;
	$ret['pager'] = pagination($ret['total'], $pindex, $psize);
	$ret['param']['paytype'] = $paytype;
	return $ret;
}


/**
 * 订单详细
 * @param id
 */
function Aftermarket_getDetailToManage(){
	global $_GPC, $_W;
	$id = intval($_GPC['id']);
	$condition = " oa.uniacid = :uniacid and oa.oaid = ".$id;
	$pars = array(':uniacid' => $_W['uniacid']);
	$item = pdo_fetch('SELECT 
						oa.oaid, oa.`type`, oa.`status`, oa.num, oa.createtime, oa.accomplish, oa.`desc`, oa.uid, oa.expresssn, oa.thumbs, oa.said, oa.express, oa.expresstime, oa.useraddrid, oa.ogid, 
						o.ordersn, o.price, o.paytype, o.goodsprice, o.dispatchprice, o.status orderstatus, o.cancelgoods ordercancelgoods, o.accomplish orderaccomplish, o.createtime ordercreatetime,
						og.optionname, og.price optionprice,  og.total, o.id as orderid, 
						g.id goodsid, g.title, g.unit
						FROM `ims_shopping_order_aftermarket` oa left join `ims_shopping_order_goods` og on oa.ogid = og.id 
							left join `ims_shopping_order` o on o.id = oa.orderid
							left join `ims_shopping_goods` g on og.goodsid = g.id
						where '.$condition, $pars);
	if($item){
		if($item['said']){
			$item['suppliers'] = pdo_fetch('select sa.realname, sa.mobile, sa.province, sa.city, sa.area, sa.address from '.tablename('shopping_suppliers_address').' sa where sa.said ='.$item['said']);			
		}
		if($item['useraddrid']){
			$item['user'] = pdo_fetch('select realname, mobile, province, city, area, address from '.tablename('shopping_address').' where id = '.$item['useraddrid']);
		}
	}
	return $item;
}

/**
 * 售后确认 
 * @param int said(供应商地址)
 * @param int oaid(售后表)
 * @param int ogid(订单商品)
 */
function Aftermarket_ConfirmSale(){
	global $_W, $_GPC;
	$said = intval($_GPC['said']);
	$oaid = intval($_GPC['oaid']);
	$ogid = intval($_GPC['ogid']);
	if($said && $oaid && $ogid){
		$res = pdo_fetch('SELECT count(a.oaid) oanum, b.id ogid FROM '.tablename('shopping_order_aftermarket').' a LEFT JOIN '.tablename('shopping_order_goods').' b on a.ogid = b.id 
			WHERE a.oaid = :oaid AND a.`status` = 0 AND b.`status` = 1 ', array(':oaid'=>$oaid));
		if($res['oanum'] && ($ogid == $res['ogid'])){
			pdo_update('shopping_order_aftermarket', array('status'=>1,'said'=>$said), array('oaid'=>$oaid));
			pdo_update('shopping_order_goods', array('status'=>2), array('id'=>$ogid));
			message('操作成功，已设置已审核！', url('site/entry/aftermarket',array('m'=>'ewei_shopping','op'=>'detail','id'=>$oaid)), 'success');
		}
	}
	message('抱歉，参数异常，操作失败！', url('site/entry/aftermarket',array('m'=>'ewei_shopping','op'=>'display')), 'error');
}

/**
 * 订单完结
 *
 */
function Aftermarket_accomplish(){
	global $_W, $_GPC;
}