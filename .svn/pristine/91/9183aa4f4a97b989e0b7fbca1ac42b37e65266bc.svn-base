<?php
/**
 * 订单管理
 * shopping.order.mod.php
 *
 */

defined('IN_IA') or exit('Access Denied');

class ShoppingOrder
{

	#{{{ 订单状态
	const STAUTS_CANCEL = -1;//已取消
	const STAUTS_CLOSE = -2;//已关闭
	const STAUTS_SUBMIT = 0;//待付款
	const STAUTS_PAID = 1;//已付款
	const STAUTS_DELIVERED = 2;//已发货
	const STAUTS_MAN_CONFIRM = 3;//已收货
	const STAUTS_AUTO_CONFIRM = 4;//已评价
	const STAUTS_ORDER_FINISH = 5;//已完单

	public static $status = array(
		self::STAUTS_CANCEL => '已取消',
		self::STAUTS_CLOSE => '已关闭',
		self::STAUTS_SUBMIT => '待付款',
		self::STAUTS_PAID => '待发货',
		self::STAUTS_DELIVERED => '已发货',
		self::STAUTS_MAN_CONFIRM => '已收货',
		self::STAUTS_AUTO_CONFIRM => '已评价',
		self::STAUTS_ORDER_FINISH => '已完单',
	);
	#}}}

	#{{{ 支付方式
	const PAYTYPE_BALANCE = 1;//余额支付
	const PAYTYPE_ONLINE = 2;//在线支付
	const PAYTYPE_COD = 3;//货到付款
	public static $paytype = array(
		self::PAYTYPE_BALANCE => '余额支付',
		self::PAYTYPE_ONLINE => '在线支付',
		self::PAYTYPE_COD => '货到付款',
	);
	#}}}

	#{{{ 发货方式
	const SENDTYPE_EXPRESS = 1;//快递
	const SENDTYPE_SELF = 1;//自提
	#}}}

	public static $pool = array();

	public $data = array();
	public $ext = array();

	public static function singleton($id, $field = 'id', $refresh = false){
		$key = $field . '_' . $id;
		if(isset(self::$pool[$key]) && self::$pool[$key] && is_array(self::$pool[$key]) && $refresh = false){
			return self::$pool[$key];
		}
		$sql = "SELECT * FROM " . tablename('shopping_order') . " WHERE $field = :$field;";
		$data = pdo_fetch($sql, [':' . $field => $id]);
		$obj = new self();
		$obj->data = $data;
		$obj->ext = $data['ext'] ? json_decode($data['ext'], true) : array();
		$pool[$key] = $obj;
		return $obj;
	}

	public static function singletonList($id, $field = 'id'){
		$sql = "SELECT id FROM " . tablename('shopping_order') . " WHERE $field = :$field;";
		$data = pdo_fetchAll($sql, [':' . $field => $id]);
		$_data = array();
		foreach($data as $item){
			$_data[$item['id']] = self::singleton($item['id']);
		}
		return $_data;
	}

	public function __get($key){
		if(isset($this->data[$key])){
			return $this->data[$key];
		}elseif($key == 'child'){
			$this->child = [];
			if($this->pid == -1){
				$child = pdo_fetchall("SELECT id FROM " . tablename('shopping_order') . " WHERE pid = :pid",[':pid' => $this->id]);
				if($child){
					foreach($child as $id){
						$this->child[$id['id']] = self::singleton($id['id']);
					}
				}
			}
			return $this->child;
		}elseif($key == 'parent'){
			$this->parent = [];
			if($this->pid){
				$parent = pdo_fetchall("SELECT id FROM " . tablename('shopping_order') . " WHERE id = :id",[':id' => $this->pid]);
				if($parent){
					foreach($parent as $id){
						$this->parent[$id['id']] = self::singleton($id['id']);
					}
				}
			}
			return $this->parent;
		}
		return $this->$key;
	}

	public function __set($key, $value){
		if(isset($this->data[$key])){
			return $this->data[$key] = $value;
		}else if($key == 'child'){
			$this->child = $value;
		}else if($key == 'parent'){
			$this->parent = $value;
		}
		return $this->$key = $value;
	}

	public function _beforeSave(){
		$this->data['ext'] = $this->ext ? json_encode($this->ext) : '';
		//恢复库存 todo
	}

	public function _afterSave(){

		if($this->pid == 0){//没有子订单
			#code
		}else if($this->pid == -1){//有子订单
			$childOrders = self::singletonList($this->id, 'pid');

			//结束所有子订单
			foreach($childOrders as $childOrder){
				if($childOrder->status != $this->status){
					$childOrder->status = $this->status;
					$childOrder->save();
				}
			}
		}else{//是子订单
			$siblingsOrders = self::singletonList($this->pid, 'pid');
			$flag = true;
			foreach($siblingsOrders as $siblingsOrder){
				if($siblingsOrder->status != $this->status){
					$flag = false;
					break;
				}
			}
			//如果所有的子订单的状态相同，则更改父级订单的状态
			if($flag){
				$parentOrder = self::singleton($this->pid);
				if($parentOrder->status != $this->status){
					$parentOrder->status = $this->status;
					$parentOrder->save();
				}
			}
		}

		//如果有退款，则需要进行退款操作 todo
		//记录金钱日志 todo
		//微信推送 todo
		//分成计算 todo
	}

	public function save(){
		$this->_beforeSave();
		$_data = array();
		foreach($this->data as $field => $value){
			if($field == 'id'){
				continue;
			}
			$_data[$field]= "$value";
		}
		$params = array('id' => $this->id);
		pdo_update('shopping_order', $_data, $params);
		$this->_afterSave();
		return true;
	}

	public function __call($methon, $params){
		d(func_get_args());
	}

}

/**
 * 统计用户各个订单的状态
 * 
 */
function getStatOrderByUid(){
	global $_GPC, $_W;
	$uid = $_W['member']['uid'];
	$res = array();
	$list = pdo_fetchall('SELECT `status`,count(id) num FROM '.tablename('shopping_order').' 
						WHERE (`status` >= 0) and uid = :uid and cancelgoods = 0 and accomplish = 0 and community = 0
						GROUP BY `status`', array(':uid' => $uid));
	foreach($list as $v){
		if($v['num']){
			$res[$v['status']] = $v['num'];
		}
	}
	$res[4] = pdo_fetchcolumn('SELECT count(id) FROM '.tablename('shopping_order').' WHERE  uid = :uid AND accomplish = 1 and status between 3 and 4 and community = 0 ', array(':uid' => $uid));
	$res[5] = pdo_fetchcolumn('SELECT count(oaid) FROM '.tablename('shopping_order_aftermarket').' WHERE  uid = :uid AND accomplish = 0', array(':uid' => $uid));
	return $res;
}

/**
 * 用户订单列表
 * @param int page
 * @param int status
 */
function Order_getMyOrderList(){
	global $_GPC, $_W;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 5;
	$status = intval($_GPC['status']);
	$accomplish = intval($_GPC['accomplish']);
	$condition = " uid = :user AND community = 0 ";
	$pars = array(':user' => $_W['member']['uid']);

	if ($accomplish == 0 && $status >= 0){
		if($status < 5){
			$condition .= " and status = {$status} and accomplish = 0";
		}else{
			$condition .= " and status >= 0 ";
		}
	}else if($accomplish == 1){
		$condition .= " and accomplish = {$accomplish} and status > 2";
	}

	//goodstype, paytype
	$list = pdo_fetchall("SELECT id, uid, ordersn, price, status, cancelgoods, sendexpress, send, createtime, dispatchprice, accomplish, expresssn
						  FROM ".tablename('shopping_order')."
						  WHERE $condition 
						  ORDER BY paymenttime DESC, `status` DESC
						  LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $pars);
	
	if (!empty($list)) {
		foreach ($list as &$row) {
			$goods = pdo_fetchall('SELECT o.goodsid, g.status as gstatus , o.price, o.total, o.optionname, o.cancelgoods, o.status, g.unit, g.title, g.thumb thumb1
								   FROM '.tablename('shopping_order_goods').' o LEFT JOIN '.tablename('shopping_goods').' g ON o.goodsid = g.id 
								   WHERE o.orderid = :orderid AND o.deleted = 0 ', 
								   array(':orderid' => $row['id']));
			$row['goods'] = $goods;
			$row['goodsnum'] = 0;
			foreach($row['goods'] as $k=>$v){
				$row['goodslink'] = url('entry/index/detail',array('m'=>'ewei_shopping','id'=>$v['goodsid']));
				$row['goods'][$k]['thumb1'] = tomedia($v['thumb1']);
				$row['goods'][$k]['state'] = OrderGoodState($v['cancelgoods'], $v['status']);
				$row['goodsnum'] += $v['total'];
			}
			$row['price'] = sprintf("%.2f", $row['price']);
			$row['createdate'] = date('Y-m-d H:i', $row['createtime']);
			$row['OrderType'] = OrderType($row['status'], $row['cancelgoods'], $row['accomplish']);
			$row['orderlink'] = url('entry/index/myorder',array('m'=>'ewei_shopping','orderid'=>$row['id'],'op'=>'detail'));
			$row['paylink'] = url('entry/index/pay',array('m'=>'ewei_shopping','orderid'=>$row['id']));
			$row['aftermarketlink'] = url('entry/index/Aftermarket',array('m'=>'ewei_shopping','id'=>$row['id']));
			if ($row['sendexpress']>strtotime('-60 days')) {
				$row['logisticslink'] = url('entry/index/logistics',array('m'=>'ewei_shopping','sn'=>$row['expresssn']));
			}
		}
	}
	return $list;
}

function Order_getMyOrderDetail($orderid = 0){
	global $_GPC, $_W;
	$condition = " id = :orderid";
	$pars = array(':weid' => $_W['uniacid']);
	$pars[':orderid'] = $orderid;
	$item = pdo_fetch("SELECT id, uid, ordersn, price, status, cancelgoods, sendexpress, send, createtime, dispatchprice, addressid, accomplish, paymenttime
					  FROM ".tablename('shopping_order')."
					  WHERE $condition ", $pars);
	if (!empty($item)) {
		$goodscondition =" o.orderid = :orderid AND o.deleted = 0 ";
		$goodspars = array(':orderid' => $item['id'], ':weid' => $_W['uniacid']);
		$item['goods'] = pdo_fetchall('SELECT o.id, o.goodsid, o.price, o.cancelgoods,o.total, o.optionname, g.unit, g.title, g.thumb thumb1
							   FROM '.tablename('shopping_order_goods').' o LEFT JOIN '.tablename('shopping_goods').' g ON o.goodsid = g.id 
							   WHERE '.$goodscondition, $goodspars);
		foreach($item['goods'] as $k=>$v){
			$item['goods'][$k]['thumb1'] = tomedia($v['thumb1']);
		}
		$item['aftermarketlink'] = url('entry/index/Aftermarket',array('m'=>'ewei_shopping','id'=>$item['id']));
		$item['price'] = sprintf("%.2f", $item['price']);
		$item['createdate'] = date('Y-m-d H:i', $item['createtime']);
		$item['paymenttime'] = date('Y-m-d H:i' , $item['paymenttime']);
	}else{
		message('抱歉，您的订单不存或是已经被取消！', url('entry/index/myorder',array('m'=>'ewei_shopping')), 'error');
	}

	return $item;
}

function Order_getMyOrderGoodsChoice($orderid = 0, $ogid = 0){
	global $_GPC, $_W;
	return pdo_fetch("SELECT a.id orderid, a.ordersn, a.price, a.`status`, o.cancelgoods, a.sendexpress, a.createtime, a.dispatchprice , o.id, o.goodsid, o.price, o.total, o.optionname, g.unit, g.title, g.thumb thumb1
					FROM ims_shopping_order a, ims_shopping_order_goods o, ims_shopping_goods g
					where a.id = :orderid and o.id = :ogid and  a.id = o.orderid and o.goodsid = g.id", 
						array(':orderid' => $orderid, 'ogid' => $ogid));
}


/**
 * 根据用户id获取订单列表(未区分社区订单)
 * @param int $uid 用户id
 * @return array
 */
function Order_getOrderListByUid($uid){
	global $_GPC, $_W;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;

	$condition = " uid=:user ";
	$pars = array(':user'=>$uid);

	if (isset($_GPC['status']) && trim($_GPC['status'])!='') {
		switch (intval($_GPC['status'])) {
			case '0':
				$condition .= " AND status=:status ";
				$pars[':status'] = intval($_GPC['status']);
				break;
			case '1':
				$condition .= " AND status=:status ";
				$pars[':status'] = intval($_GPC['status']);
				break;
			case '3':
				$condition .= " AND status<0 ";
				break;
			case '2':
				$condition .= " AND status between 2 and 4 ";
				break;
		}

	}

	//goodstype, paytype
	$list = pdo_fetchall("SELECT * FROM ".tablename('shopping_order')."
						  WHERE $condition 
						  ORDER BY paymenttime DESC, `status` DESC
						  LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $pars);
	if (!empty($list)) {
		foreach ($list as &$row) {
			// $goods = pdo_fetchall('SELECT o.goodsid, g.status as gstatus , o.price, o.total, o.optionname, o.cancelgoods, o.status, g.unit, g.title, g.thumb thumb1
			// 					   FROM '.tablename('shopping_order_goods').' o LEFT JOIN '.tablename('shopping_goods').' g ON o.goodsid = g.id 
			// 					   WHERE o.orderid = :orderid AND o.deleted = 0 AND o.weid = :weid', 
			// 					   array(':orderid' => $row['id'], ':weid' => $_W['uniacid']));
			// $row['goods'] = $goods;
			// foreach($row['goods'] as $k=>$v){
			// 	$row['goodslink'] = url('entry/index/detail',array('m'=>'ewei_shopping','id'=>$v['goodsid']));
			// 	$row['goods'][$k]['thumb1'] = tomedia($v['thumb1']);
			// 	$row['goods'][$k]['state'] = OrderGoodState($v['cancelgoods'], $v['status']);
			// 	$row['goodsnum'] += $v['total'];
			// }
			$row['goodsnum'] = 0;
			$row['price'] = sprintf("%.2f", $row['price']);
			$row['OrderType'] = OrderType($row['status'], $row['cancelgoods'], $row['accomplish']);
		}
	}

	$total = pdo_fetchcolumn("SELECT count(id) as totalNum FROM ".tablename('shopping_order')." WHERE $condition", $pars);
	$pager = pagination($total, $pindex, $psize);

	return array('total'=>$total, 'data'=>$list, 'pager'=>$pager);
}

/**
 * 管理后台订单
 * @param int status
 * @param int cancelgoods
 * @param int sendtype
 * @param int time
 */
function Order_getOrderListToManage(){
	global $_GPC, $_W;
	$ret = array('param'=>array());
	$pindex = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 10;
	$condition = " o.parent_ordersn IS NOT NULL AND o.parent_ordersn<>'' ";
	$condition = " o.hassub_order=0 ";

	$paras = array();
	$ret['param']['uid'] = intval($_GPC['uid']);
	$ret['param']['status'] = $_GPC['status'];
	$ret['param']['cancelgoods'] = $_GPC['cancelgoods'];
	$ret['param']['accomplish'] = intval($_GPC['accomplish']);
	$ret['param']['sendtype'] = !isset($_GPC['sendtype']) ? 0 : $_GPC['sendtype'];
	$ret['param']['sid'] = intval($_GPC['sid']);

	//时间
	if (!empty($_GPC['time'])) {
		$paras[':starttime'] = $ret['param']['starttime'] = strtotime($_GPC['time']['start']);
		$paras[':endtime'] = $ret['param']['endtime'] = strtotime($_GPC['time']['end']) + 86399;
		$condition .= " AND o.createtime >= :starttime AND o.createtime <= :endtime ";
	}
	if (!isset($ret['param']['starttime']) || !isset($ret['param']['endtime'])) {
		$ret['param']['starttime'] = strtotime('-12 month');
		$ret['param']['endtime'] = time();
	}
	# 付款类型
	if (!empty($_GPC['paytype']) || $_GPC['paytype'] === '0') {
		$condition .= " AND o.paytype = '{$_GPC['paytype']}'";
	}
	# 订单号
	if (!empty($_GPC['keyword'])) {
		$condition .= " AND o.ordersn LIKE '%{$_GPC['keyword']}%'";
	}
	# 姓名或昵称或手机
	if (!empty($_GPC['member'])) {
		$condition .= " AND (like a.realname LIKE '%{$_GPC['members']}%' or a.mobile LIKE '%{$_GPC['member']}%' u.nickname LIKE '%{$_GPC['member']}%')";
	}
	# 订单状态
	if ($ret['param']['status'] != '') {
		if($ret['param']['status'] == ShoppingOrder::STAUTS_MAN_CONFIRM || $ret['param']['status'] == ShoppingOrder::STAUTS_AUTO_CONFIRM){
			$condition .= " AND o.status between ".ShoppingOrder::STAUTS_MAN_CONFIRM." and ".ShoppingOrder::STAUTS_AUTO_CONFIRM;
		}elseif($ret['param']['status'] == ShoppingOrder::STAUTS_CANCEL){
			$condition .= " AND (o.status = ".ShoppingOrder::STAUTS_CANCEL." OR o.status = ".ShoppingOrder::STAUTS_CLOSE.")";
		}else{
			$condition .= " AND o.status = :status ";
			$paras[':status'] = intval($ret['param']['status']);
		}
	}

		//查询的是删除订单
		if ($ret['param']['status']=='-2') {
			$condition .= " AND o.deleted=1 ";
			$paras[':status'] = -1;
		} else {
			$condition .= " AND o.deleted=0";
		}

	# 退货申请
	if ($ret['param']['cancelgoods'] == 1) {
		$condition .= " AND o.cancelgoods = 1 ";
	}
	# 收货类型
	if (!empty($ret['param']['sendtype'])) {
		# 1快递；2自提 
		$condition .= " AND o.sendtype = :sendtype AND o.status != '3' AND o.status != '4'";
		$paras[':sendtype'] = intval($ret['param']['sendtype']);
	}
	# 订单完成
	if ($ret['param']['accomplish'] == 1){
		$condition .= " AND o.accomplish = '" . intval($ret['param']['accomplish']) . "' ";	
	}
	# 用户UID
	if (!empty($ret['param']['uid'])){
		$condition .= " AND o.uid = '" . intval($ret['param']['uid']) . "' ";	
	}
	# 供应商ID(当管理员有供应商ID时默认查看供应商的订单产品)
	if (!empty($ret['param']['sid']) || $_W['user']['sid']){
		$sid = $_W['user']['sid'] ? $_W['user']['sid'] : $ret['param']['sid'];
		$condition .= " AND o.id in (select og.orderid from ims_shopping_order_goods og where og.sid = {$sid} group by og.orderid)";
	}
	if($_W['user']['sid']){
		$condition .= " AND o.status >= 1 ";
	}

	$sql = 'SELECT 
				o.id, o.parent_ordersn,o.sid, o.uid , o.status , o.ordersn , o.price , 
				o.transid , o.cancelgoods , o.createtime , o.remark, o.dispatch, 
				o.paytype, o.sendtype, o.remark, o.paymenttime,o.realname,o.mobile,
				o.cancelgoods, o.accomplish,  o.creditsettle, o.deleted, o.source,
				u.nickname,cp.pathwayid, cp.cabinetid,c.name,
				wxpay.successtime
			FROM '. tablename('shopping_order') . ' o 
			LEFT JOIN  '.tablename('members').' u ON u.uid = o.uid 
			LEFT JOIN ' .tablename('shopping_order_goods') . ' og ON o.id = og.orderid
			LEFT JOIN ' .tablename('cabinet_pathway') . ' cp ON og.goodsid = cp.goodsid
			LEFT JOIN ' .tablename('cabinet') . ' c ON c.cabinetid = cp.cabinetid
			LEFT JOIN  '.tablename('core_wxpay_log').' wxpay ON wxpay.orderid = o.id 
			WHERE '.$condition 
			." ORDER BY o.createtime DESC "
			." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
	$ret['list'] = pdo_fetchall($sql,$paras);
    // d($ret['list']);

	$paytype = array(
			'1' => array('css' => 'danger','name' => '余额支付'),
			'2' => array('css' => 'info', 'name' => '在线支付'),
			'3' => array('css' => 'warning', 'name' => '货到付款')
	);
	$orderstatus = array(
			'-1' => array('css' => 'default', 'name' => '已取消'),
			'0' => array('css' => 'danger', 'name' => '待付款'),
			'1' => array('css' => 'info', 'name' => '待发货'),
			'2' => array('css' => 'warning', 'name' => '待收货'),
			'3' => array('css' => 'success', 'name' => '已收货'),
			'4' => array('css' => 'success', 'name' => '已评价'),
			'5' => array('css' => 'success', 'name' => '已完成')
	);
	if($_W['user']['sid']){
		unset($paytype[0]);
		unset($orderstatus[-2]);
		unset($orderstatus[-1]);
		unset($orderstatus[0]);
	}

	foreach ($ret['list'] as &$value) {
		$s = $value['status'];
		$value['statuscss'] = $orderstatus[$value['status']]['css'];
		if($value['cancelgoods'] == 1){
			$value['statuscss'] = 'danger';
		}
		$value['statusname'] = OrderType($value['status'], $value['cancelgoods'], $value['accomplish']);
		if ($s < 1) {
			$value['css'] = $paytype[$s]['css'];
			$value['paytype'] = $paytype[$s]['name'];
			continue;
		}
		$value['css'] = $paytype[$value['paytype']]['css'];
		if ($value['paytype'] == 2) {
			if (empty($value['transid'])) {
				$value['paytype'] = '支付宝支付';
			} else {
				$value['paytype'] = '微信支付';
			}
		} else {
			$value['paytype'] = $paytype[$value['paytype']]['name'];
		}
	}

	$ret['total'] = pdo_fetchcolumn("SELECT COUNT(o.uid) FROM 
								(" . tablename('shopping_order') . " o" . " LEFT JOIN ".tablename('shopping_address')." a 
								ON o.addressid = a.id) LEFT JOIN  ".tablename('members')." u ON u.uid = o.uid
								WHERE $condition ", $paras);
	$ret['pager'] = pagination($ret['total'], $pindex, $psize);
	return $ret;
}

function MZOrder_getOrderListToManage(){
	global $_GPC, $_W;
	$ret = array('param'=>array());
	$pindex = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 10;
	$paras = array();
	$ret['param']['uid'] = intval($_GPC['uid']);
	$ret['param']['status'] = $_GPC['status'];
	$ret['param']['sid'] = intval($_GPC['sid']);
	$condition = ' 1=1 ';

	//时间
	if (!empty($_GPC['time'])) {
		$paras[':starttime'] = $ret['param']['starttime'] = strtotime($_GPC['time']['start']);
		$paras[':endtime'] = $ret['param']['endtime'] = strtotime($_GPC['time']['end']) + 86399;
		$condition .= " AND o.createdt >= :starttime AND o.createdt <= :endtime ";
	}
	if (!isset($ret['param']['starttime']) || !isset($ret['param']['endtime'])) {
		$ret['param']['starttime'] = strtotime('-12 month');
		$ret['param']['endtime'] = time();
	}
	# 付款类型
	if (!empty($_GPC['paytype']) || $_GPC['paytype'] === '0') {
		$condition .= " AND o.paytype = '{$_GPC['paytype']}'";
	}
	# 订单号
	if (!empty($_GPC['keyword'])) {
		$condition .= " AND o.ordersn LIKE '%{$_GPC['keyword']}%'";
	}

	# 订单状态
	if ($ret['param']['status'] != '') {
		if($ret['param']['status'] == ShoppingOrder::STAUTS_MAN_CONFIRM || $ret['param']['status'] == ShoppingOrder::STAUTS_AUTO_CONFIRM){
			$condition .= " AND o.status between ".ShoppingOrder::STAUTS_MAN_CONFIRM." and ".ShoppingOrder::STAUTS_AUTO_CONFIRM;
		}elseif($ret['param']['status'] == ShoppingOrder::STAUTS_CANCEL){
			$condition .= " AND (o.status = ".ShoppingOrder::STAUTS_CANCEL." OR o.status = ".ShoppingOrder::STAUTS_CLOSE.")";
		}else{
			$condition .= " AND o.status = :status ";
			$paras[':status'] = intval($ret['param']['status']);
		}
	}

	# 退货申请
	if ($ret['param']['cancelgoods'] == 1) {
		$condition .= " AND o.cancelgoods = 1 ";
	}
	# 收货类型
	if (!empty($ret['param']['sendtype'])) {
		# 1快递；2自提 
		$condition .= " AND o.sendtype = :sendtype AND o.status != '3' AND o.status != '4'";
		$paras[':sendtype'] = intval($ret['param']['sendtype']);
	}
	# 订单完成
	if ($ret['param']['accomplish'] == 1){
		$condition .= " AND o.accomplish = '" . intval($ret['param']['accomplish']) . "' ";	
	}

	# 收货电话 / 收货人姓名 / 魅族商城用户UID
	if (!empty($ret['param']['uid'])){
		$condition .= " AND (o.uid = '" . intval($ret['param']['uid']) . "' ";	
	}
	# 供应商ID(当管理员有供应商ID时默认查看供应商的订单产品)
	if (!empty($ret['param']['sid']) || $_W['user']['sid']){
		$sid = $_W['user']['sid'] ? $_W['user']['sid'] : $ret['param']['sid'];
		$condition .= " AND o.id in (select og.orderid from ims_shopping_order_goods og where og.sid = {$sid} group by og.orderid)";
	}
	if($_W['user']['sid']){
		$condition .= " AND o.status >= 1 ";
	}

	$sql = 'SELECT o.*, g.title, w.name as warehouse
			FROM '. tablename('meizu_order') . ' o 
			LEFT JOIN  '.tablename('shopping_goods').' g ON g.id=o.itemId 
			LEFT JOIN  '.tablename('shopping_warehouse').' w ON g.wid=w.id
			WHERE '.$condition 
			." ORDER BY o.createdt DESC "
			." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
	$ret['list'] = pdo_fetchall($sql,$paras);

	$paytype = array(
			'1' => array('css' => 'danger','name' => '余额支付'),
			'2' => array('css' => 'info', 'name' => '在线支付'),
			'3' => array('css' => 'warning', 'name' => '货到付款')
	);
	$orderstatus = array(
			'-1' => array('css' => 'default', 'name' => '已取消'),
			'0' => array('css' => 'danger', 'name' => '已退货'),
			'1' => array('css' => 'info', 'name' => '待发货'),
			'2' => array('css' => 'warning', 'name' => '待收货'),
			'3' => array('css' => 'success', 'name' => '已收货'),
			'5' => array('css' => 'success', 'name' => '已完成')
	);
	if($_W['user']['sid']){
		unset($paytype[0]);
		unset($orderstatus[-2]);
		unset($orderstatus[-1]);
		unset($orderstatus[0]);
	}

	foreach ($ret['list'] as &$value) {
		$s = $value['status'];
		$value['statuscss'] = $orderstatus[$value['status']]['css'];
		if($value['cancelgoods'] == 1){
			$value['statuscss'] = 'danger';
		}
		$value['statusname'] = OrderType($value['status'], $value['cancelgoods'], $value['accomplish']);
		$value['css'] = $paytype[$value['paytype']]['css'];
		$value['paytype'] = '魅族商城';
	}

	$ret['total'] = pdo_fetchcolumn("SELECT COUNT(o.mzid) 
									 FROM ".tablename('meizu_order')." o 
									 WHERE $condition ", $paras);
	$ret['pager'] = pagination($ret['total'], $pindex, $psize);
	return $ret;
}

/**
 * 订单商品删除，管理员操作
 * @param int id 订单ID
 * @param int goodsid 商品ID
 * @business 获取当前订单商品的信息并判断没有删除过的，判断修改订单价格，修改订单商品状态，
 */
function Order_delOrderGoodsToManage(){
	global $_GPC, $_W;
	$orderid = intval($_GPC['id']);
	$ogid = intval($_GPC['ogid']);
	// if($count == 0 || $count == 1){
	// 	pdo_delete('shopping_order', array('id' => $orderid));
	// 	pdo_delete('shopping_order_goods', array('orderid' => $orderid));
	// 	// message('订单不存在或已被删除', referer(), 'error');	
	// }else{

		// $ogres = pdo_fetch('select price, total from '.tablename('shopping_order_goods').' where id = :ogid and deleted = 0', array(':ogid' => $ogid));
		// if(!empty($ogres)){
		// 	$money = (float) $ogres['price']*$ogres['total'];
		// 	pdo_query('UPDATE ' . tablename('shopping_order') .
		// 			  ' SET price = price - ' . $money . ', goodsprice = goodsprice -' . $money .
		// 			  ' WHERE id = :orderid', array(':orderid' => $orderid));

		// 	pdo_update('shopping_order_goods', array('deleted' => 1), array('id' => $ogid, 'orderid' => $orderid, 'status' => 0));
		// 	$ordercount = pdo_fetchcolumn('select count(id) from '.tablename('shopping_order_goods').' where orderid = :orderid and deleted = 0', array(':orderid' => $orderid));
		// 	if($ordercount == 0){
		// 		pdo_delete('shopping_order', array('id' => $orderid));
		// 		pdo_delete('shopping_order_goods', array('orderid' => $orderid));
		// 	}
		// 	message('操作成功，当前订单的商品已移除', referer(), 'success');
		// }
		// message('操作失败，当前订单的商品已移除', referer(), 'error');、
	
	// }
}

/**
 * 订单删除
 * @param int id 订单ID
 * @return message
 */
function Order_delOrderToManage(){
	global $_GPC, $_W;
	$orderid = intval($_GPC['id']);
	pdo_query('update '.tablename('shopping_order').' set deleted=1 where id=:orderid and status=-1', array(':orderid' => $orderid));
	// pdo_delete('shopping_order_goods', array('orderid' => $orderid));
	message('订单操作成功！', referer(), 'success');
}

/**
 * 订单详细管理后台
 */
function Order_getOrderDetailToManage($orderid = 0){
	$item = pdo_fetch("SELECT * FROM " . tablename('shopping_order') . " WHERE id = :id", array(':id' => $orderid));
	if (empty($item)) {
		message("抱歉，订单不存在!", referer(), "error");
	}
	if($item['pid'] == -1){
		$child = pdo_fetchall("SELECT * FROM " . tablename('shopping_order') . " WHERE pid = :pid",[':pid' => $item['id']]);
		if($child){
			$item['child'] = $child;
		}
	}
	return $item;
}
function Order_getMZOrderDetail($orderid = 0){
	$item = pdo_fetch('SELECT o.*,g.title,g.status FROM '.tablename('meizu_order').' o LEFT JOIN '.tablename('shopping_goods').' g on o.itemId=g.id WHERE o.mzid=:id', array(':id' => $orderid));
	if (empty($item)) {
		message("抱歉，订单不存在!", referer(), "error");
	}
	return $item;
}

/**
 * 订单发货状态并返回产品名
 */
function Order_confirmSendOrder($orderid = 0){
	global $_GPC, $_W;
	pdo_update(
		'shopping_order',
		array(
			'status' => 2,
			'remark' => $_GPC['remark'],
			'express' => $_GPC['express'],
			'expresscom' => $_GPC['expresscom'],
			'expresssn' => $_GPC['expresssn'],
			'sendexpress' => TIMESTAMP,
		),
		array('id' => $orderid,'cancelgoods'=>0)
	);
	$info = pdo_fetchall('select b.title,b.wid from ims_shopping_order_goods a,ims_shopping_goods b 
						  where a.orderid = :orderid and a.goodsid = b.id and (a.cancelgoods = 0 || a.cancelgoods = 2)',
						  array(':orderid'=>$orderid));
	$title = '';
	foreach ($info as $key => $value) {
		if($key == 0){
			$title = $value['title'];
		}else{
			$title .= ' 和 '.$value['title'];
		}
	}
	return $title;
}

/**
 * 取消发货，订单status 2 => 1
 *
 */
function Order_cancelSendOrder($orderid){
	global $_GPC, $_W;
	//pre($_GPC);exit;
	pdo_update(
		'shopping_order',
		array(
			'status' => 1,
			'remark' => $_GPC['remark']
		),
		array('id' => $orderid)
	);
}

/**
 * 根据订单号查询订单包含商品信息
 * 	1、业绩信息: a.price,a.total
 *	2、三级分成信息:  comm1,comm2,comm3
 *  3、price为订单总额，price2为订单内对应商品单价
 * @param int $orderId 订单编号
 * @return array
 */
function Order_getOrderGoodsDetailByOrderId($orderId) {
	$list = pdo_fetchall('SELECT distinct tba.id as orderGoodsId,tba.orderid,tba.goodsid,tba.optionid,tba.total,tba.price,
						tbb.uid,tbb.from_user,tbb.price AS price2,tbb.status,tba.cancelgoods AS order_goods_cancle,
						tbb.send,tbb.createtime,tbb.cancelgoods,tbb.express,tbb.expresssn,tbc.productprice,tbc.comm1,tbc.comm2,tbc.comm3,tbc.id as optionid,
						tbd.title,tbd.comm1 as comm11,tbd.comm2 as comm21,tbd.comm3 as comm31
					FROM ims_shopping_order_goods AS tba
					LEFT JOIN ims_shopping_order AS tbb ON tba.orderid=tbb.id
					LEFT JOIN ims_shopping_goods_option AS tbc ON tba.goodsid=tbc.goodsid
					LEFT JOIN ims_shopping_goods AS tbd ON tbd.id=tba.goodsid 
					WHERE tba.orderid=:id AND tbb.uid IS NOT NULL AND (tbc.id = tba.optionid OR tba.optionid=0) ', array(':id' => $orderId));
	//过滤订单内商品分成数据
	foreach ($list as $arrk => $arrv) {
		if (!isset($arrv['comm1']) || $arrv['comm1']='') {
			$list[$arrk]['comm1'] = $list[$arrk]['comm11'];
		}
		if (!isset($arrv['comm2']) || $arrv['comm2']='') {
			$list[$arrk]['comm2'] = $list[$arrk]['comm21'];
		}
		if (!isset($arrv['comm3']) || $arrv['comm3']='') {
			$list[$arrk]['comm3'] = $list[$arrk]['comm31'];
		}
	}
	return $list;
}

/**
 * 根据订单号查询订单商品售后信息(不含分成)
 * @param int $orderId 订单编号
 * @return array
 */
function Order_getOrderAftersaleByOrderId($orderId) {
	return pdo_fetchall('SELECT 
							tba.*, tbb.status as order_status, tbb.cancelgoods as order_cancle, tbb.accomplish as order_accomplish, tbc.optionid, tbc.goodsid
						FROM
							ims_shopping_order_aftermarket AS tba
						LEFT JOIN ims_shopping_order AS tbb ON tba.orderid = tbb.id 
						LEFT JOIN ims_shopping_order_goods AS tbc ON tbc.orderid=tba.oaid 
						WHERE tba.orderid=:id', array(':id' => $orderId));
}

/**
 * 根据订单号查询订单包含商品信息
 * 	1、业绩信息: a.price,a.total
 * @param int $orderId 订单编号
 * @return array
 */
function Order_getOrderGoodsByOrderId($orderId) {
	return pdo_fetchall('SELECT 
						tbb.uid,tba.orderid,tba.goodsid,tba.optionid,tba.total,tba.price,tbb.from_user,
						tbb.price AS price2,tbb.status,tbb.send,tbb.cancelgoods,tbb.express,tbb.expresssn,tbb.sendexpress ,tbb.receipttime 
					FROM '.tablename('shopping_order_goods').' AS tba 
					LEFT JOIN '.tablename('shopping_order').' AS tbb ON tba.orderid=tbb.id 
					WHERE tba.orderid=:id AND tbb.uid IS NOT NULL', array(':id' => $orderId));
}

/**
 * 根据订单号查询所包含的商品的附加信息
 * 	1、三级分成信息:  comm1,comm2,comm3
 * @param int $orderId 订单编号
 * @return array
 */
function Order_getOrderGoodsOptionByOrderId($orderId) {
	return pdo_fetchall('SELECT 
						tba.goodsid,tba.price,tba.total,tba.optionid,tbb.productprice,tbb.marketprice,tbb.stock,tbb.comm1,tbb.comm2,tbb.comm3 
					FROM '.tablename('shopping_order_goods').' AS tba 
					LEFT JOIN '.tablename('shopping_goods_option').' AS tbb ON tba.goodsid=tbb.goodsid 
					WHERE tba.orderid=:id', array(':id' => $orderId));
}


/**
 * 订单付款
 *
 */
function Order_confrimpay($orderid = 0, $item = array()){
	global $_GPC, $_W;
	if(!empty($item)){
		if($item['status'] == 0){
			load()->model('mc');
			$setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
			$fee = floatval($item['price']);

			//获取用户余额信息
			$credtis = mc_credit_fetch($item['uid']);

			//1.金额比较
			if($credtis[$setting['creditbehaviors']['currency']] >= $fee){

				//2.修改余额信息
				$result = mc_credit_update(
								$item['uid'], $setting['creditbehaviors']['currency'], -$fee, 
								array($item['uid'], '消费'.$setting['creditbehaviors']['currency'].':' . $fee)
								);
				if (is_error($result)) {
					message($result['message'], '', 'error');
				}

				//3.添加付款日志
				load()->model('core.paylog');
				$record = array(
					'uniacid' => $_W['uniacid'], 
					'openid' => $item['uid'], 
					'module' => 'ewei_shopping', 
					'type' => 'credit',				//支付类型
					'tid' => $item['id'],			//订单ID
					'fee' => $fee,					//付款金额
					'status' => ShoppingOrder::STAUTS_PAID					//付款状态
					);
				Paylog_handle($record);

				$orderObj = ShoppingOrder::singleton($orderid,'id', true);
				$orderObj->status = ShoppingOrder::STAUTS_PAID;
				$orderObj->ext['process'][] = array(
					'status' => ShoppingOrder::STAUTS_PAID,
					'user' => $_W['username'],
					'action' => '后台确认付款',
					'remark' => '余额支付',
					'time' => time(),
				);
				$orderObj->save();

				//4.付款操作
				$ret = array();
				$ret['result'] = 'success';
				$ret['type'] = $record['type'];
				$ret['tid'] = $record['tid'];
				$ret['user'] = $record['openid'];
				$ret['fee'] = $record['fee'];
				$ret['weid'] = $record['weid'];
				$ret['uniacid'] = $record['uniacid'];
				return $ret;
			}
			message('余额不足', referer(), 'error');
		}
		message('操作失败，此订单不是待付款不能操作', referer(), 'danger');
	}else{
		message('订单不存在', referer(), 'danger');
	}
}

/**
 * 获取最新订单
 * 用于下单成功显示
 */
function Order_getOrderByNewest($uid = null){
	global $_GPC,$_W;
	$sql = "SELECT o.id, o.ordersn, o.paymenttime, o.price, o.`status`, addr.realname, addr.province, addr.city, addr.area, addr.address, addr.mobile  
			FROM ims_shopping_order o left join ims_shopping_address addr on o.addressid = addr.id
			where o.uid = :uid and o.`status` >= 1 
			order by o.paymenttime desc 
			limit 1";
	$res = pdo_fetch($sql, array(':uid' => $uid, ':weid' => $_W['uniacid']));
	if($res){
		$res['goodscount'] = pdo_fetchcolumn('SELECT sum(total) FROM ims_shopping_order_goods WHERE orderid = :orderid', array(':orderid' => $res['id']));
	}
	
	return $res;
}

/**
 * 导出订单execl
 * @param $status = 1 导出需要发货的订单
 * @param $status = 2 导出已发货的订单
 * @remark 待接入供应商ID
 */
function Order_Orderex(){
	global $_GPC,$_W;
	require_once('../framework/library/phpexcel/PHPExcel.php');
	$objPHPExcel = new PHPExcel();
	$status = intval($_GPC['status']) ? intval($_GPC['status']) : 1;
	$statusTitle = ($status>1) ? '已发货订单' : '待发货订单';

	$condition = 'b.goodsid = c.id ';
	if($status == 1){
		$condition .= 'AND a.status=1 AND a.cancelgoods = 0 AND a.accomplish = 0 ';
	}else{
		$condition .= 'AND a.status>= 2 ';
	}
	if($_W['user']['sid']){
		$condition .= ' AND b.sid = '.$_W['user']['sid'];
	}

	$sql = 'SELECT
            c.title goodstitle,
            a.ordersn, a.price AS totalprice, a.createtime, a.remark, 
            a.paymenttime, a.transid, a.cancelgoods, a.status, 
            a.realname,	a.mobile,	a.province,	a.city,	a.area,	a.address, a.id_no, a.id_name, 
            b.optionname, b.price, b.total, (b.price * b.total) goodsprice, c.goodssn, c.productsn, e.company
          FROM '.tablename('shopping_order').' a
          LEFT JOIN '.tablename('shopping_order_goods').' b ON b.orderid=a.id
          LEFT JOIN '.tablename('shopping_goods').' c ON c.id=b.goodsid
          LEFT JOIN '.tablename('shopping_suppliers').' e ON e.sid=c.sid
          WHERE  '.$condition;
	$re = pdo_fetchall($sql, array(':weid'=>$_W['uniacid']));

	if($re){
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', '下单时间')
					->setCellValue('B1', '订单号')
					->setCellValue('C1', '供应商')
					->setCellValue('D1', '产品名')
					->setCellValue('E1', '规格')
					->setCellValue('F1', '型号编码')
					->setCellValue('G1', '商品条码')
					->setCellValue('H1', '付款')
					->setCellValue('I1', '单价')
					->setCellValue('J1', '数量')
					->setCellValue('K1', '单价总价')
					->setCellValue('L1', '支付状态')
					->setCellValue('M1', '退货申请')
					->setCellValue('N1', '收货人')
					->setCellValue('O1', '联系方式')
					->setCellValue('P1', '证件编号')
					->setCellValue('Q1', '收货地址')
					->setCellValue('R1', '备注信息');
		//set width  
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(28);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(55);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(6);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);		
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(50);
		foreach($re as $key=>$val) {
			if($val['status']&&$val['paymenttime']){
				$payResult = '支付成功';
			} else if($val['status']){
				$payResult = '异常订单';
			} else {
				$payResult = '未支付';
			}
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A'.($key+2), date('Y-m-d H:i:s', $val['createtime']))		  //创建时间
						->setCellValue('B'.($key+2), ' '.$val['ordersn'])							  //订单号
						->setCellValue('C'.($key+2), $val['company'])							      //供应商
						->setCellValue('D'.($key+2), $val['goodstitle'])							  //产品名
						->setCellValue('E'.($key+2), $val['optionname'])						  	  //规格名
						->setCellValue('F'.($key+2), ' '.$val['goodssn'])						  	  //商品编码
						->setCellValue('G'.($key+2), ' '.$val['productsn'])						  	  //商品条码
						->setCellValue('H'.($key+2), ' '.$val['totalprice'])						  //付款
						->setCellValue('I'.($key+2), ' '.$val['price'])								  //单价
						->setCellValue('J'.($key+2), ' '.$val['total'])								  //购买数
						->setCellValue('K'.($key+2), ' '.$val['goodsprice'])						  //单品总价
						->setCellValue('L'.($key+2), $payResult)
						->setCellValue('M'.($key+2), $val['cancelgoods']?'退货':'否')				
						->setCellValue('N'.($key+2), $val['realname'])								  //收货人
						->setCellValue('O'.($key+2), ' '.$val['mobile'].' ')
						->setCellValue('P'.($key+2), (trim($val['id_no'])!='') ? ' '.$val['id_no'] : ' '.$val['id_name'])
						->setCellValue('Q'.($key+2), $val['province'].$val['city'].$val['area'].$val['address'])
						->setCellValue('R'.($key+2), $val['remark']);
		}
		$objPHPExcel->getActiveSheet()->setTitle($statusTitle);
		$objPHPExcel->setActiveSheetIndex(0);

		$date = date('ymd_Hi');
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="ORDER_'.$date.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');

		exit;
	}
	message('操作失败，没有数据！', referer(), 'error');
}