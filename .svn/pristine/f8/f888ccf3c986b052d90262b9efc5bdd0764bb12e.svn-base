<?php
/**
 * 产品管理
 * shopping.goods.mod.php
 *
 */

defined('IN_IA') or exit('Access Denied');

/**
 * 根据条件获取产品
 * @param int page 		分页数
 * @param int pcate 	顶级分类
 * @param int ccate 	下级分类
 * @param str keyword 	搜索名
 * 
 * @param int sortb0 		时间排序
 * @param int sortb1 		销售排序
 * @param int sortb2 		点击量排序
 * @param int sortb3 		销售价排序
 */
function getGoodsByCondition(){
	global $_GPC, $_W;
	load()->model('goods.visit');
	$pindex = max(1, intval($_GPC["page"]));
	$psize = 8;
	$condition = '';
	if (!empty($_GPC['ccate'])) {
		$cid = intval($_GPC['ccate']);
		$condition .= " AND ccate = '{$cid}'";
		$_GPC['pcate'] = pdo_fetchcolumn("SELECT parentid FROM " . tablename('shopping_category') . " WHERE id = :id", array(':id' => intval($_GPC['ccate'])));
		goods_addVisitLogInfoMation($cid, 3);
	} elseif (!empty($_GPC['pcate'])) {
		$cid = intval($_GPC['pcate']);
		$condition .= " AND pcate = '{$cid}'";
		goods_addVisitLogInfoMation($cid, 3);
	}
	if (!empty($_GPC['keyword'])) {
		$_GPC['keyword'] = urldecode($_GPC['keyword']);
		$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
		goods_addVisitLogInfoMation($_GPC['keyword'], 2);
	}

	$sort = empty($_GPC['sort']) ? 0 : $_GPC['sort'];
	$sortfield = "displayorder asc";
	$sortb0 = empty($_GPC['sortb0']) ? "asc" : $_GPC['sortb0'];
	$sortb1 = empty($_GPC['sortb1']) ? "desc" : $_GPC['sortb1'];
	$sortb2 = empty($_GPC['sortb2']) ? "desc" : $_GPC['sortb2'];
	$sortb3 = empty($_GPC['sortb3']) ? "asc" : $_GPC['sortb3'];
	if ($sort == 0) {
		$sortb00 = $sortb0 == "desc" ? "asc" : "desc";
		$sortfield = "createtime " . $sortb0;
		$sortb11 = "desc";
		$sortb22 = "desc";
		$sortb33 = "asc";
	} else if ($sort == 1) {
		$sortb11 = $sortb1 == "desc" ? "asc" : "desc";
		$sortfield = "sales " . $sortb1;
		$sortb00 = "desc";
		$sortb22 = "desc";
		$sortb33 = "asc";
	} else if ($sort == 2) {
		$sortb22 = $sortb2 == "desc" ? "asc" : "desc";
		$sortfield = "viewcount " . $sortb2;
		$sortb00 = "desc";
		$sortb11 = "desc";
		$sortb33 = "asc";
	} else if ($sort == 3) {
		$sortb33 = $sortb3 == "asc" ? "desc" : "asc";
		$sortfield = "marketprice " . $sortb3;
		$sortb00 = "desc";
		$sortb11 = "desc";
		$sortb22 = "desc";
	}
	
	$list = pdo_fetchall("SELECT marketprice, id, title, thumb, thumb1, istime, timeend 
						  FROM " . tablename('shopping_goods') . " 
						  WHERE weid = '{$_W['uniacid']}'  and deleted = 0 AND status = '1' AND type = 1 AND pcate <> 41 $condition 
						  ORDER BY $sortfield LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

	foreach ($list as &$r) {
		if ($r['istime'] == 1) {
			$arr = time_tran($r['timeend']);
			$r['timelaststr'] = $arr[0];
			$r['timelast'] = $arr[1];
		}
	}
	unset($r);
	return $list;
}
/**
 * 手工订单
 * 存在返回地址ID
 * 不存在则添加
 */
function Address_PostUserAddress($Users = ''){
	global $_W;
	if(!is_array($Users) && !is_array($data)){
		return false;
	}

	// if($Users['status'] == 'default'){
	// 	$openid = pdo_fetchcolumn("SELECT openid FROM ".tablename("mc_mapping_fans")." WHERE uid = :uid",array(':uid'=>$Users['uid']));

	// 	if(empty($openid)){
	// 		$openid = $Users['uid'];
	// 	}
	// 	$condition = ' WHERE id = :id and openid = :openid and weid = :weid';
	// 	$a = pdo_fetchcolumn('select id from '.tablename('shopping_address') .$condition, array(':id' => $Users['id'],':weid' => $_W['uniacid'], ':openid' => $openid));
	// 	var_dump();
	// }else{
		$openid = pdo_fetchcolumn("SELECT openid FROM ".tablename("mc_mapping_fans")." WHERE uid = :uid",array(':uid'=>$Users['uid']));
	
		if(empty($openid)){
			$openid = $Users['uid'];
		}

		$datas = array(
			'weid' => $_W['weid'],
			'openid' => $openid,
			'realname' => $Users['real_name'],
			'mobile' => $Users['real_mobile'],
			'province' => $Users['province'],
			'city' => $Users['city'],
			'area' => $Users['district'],
			'address' => $Users['real_address'],
			'uid' => $Users['uid'],
			'isdefault' => 1
			);
		$result = pdo_insert('shopping_address',$datas);
		return pdo_insertid();
	//}
}

function getOrderDispatChprice($dispatch="",$goodsDetail=null,$type=""){
		if(!is_array($dispatch) && !is_array($goodsDetail)){
			return false;
		}
		if($type == 'stock'){
			foreach ($dispatch as &$d) {
				$weight = 0;
				$weight+=$goodsDetail['weight'] * $goodsDetail['stock'];
				$price = 0;
				if ($weight <= $d['firstweight']) {
					$price = $d['firstprice'];
				} else {
					$price = $d['firstprice'];
					$secondweight = $weight - $d['firstweight'];
					if ($secondweight % $d['secondweight'] == 0) {
							$price+= (int) ( $secondweight / $d['secondweight'] ) * $d['secondprice'];
					} else {
							$price+= (int) ( $secondweight / $d['secondweight'] + 1 ) * $d['secondprice'];
					}
				}
					$d['price'] = $price;
			}
		} else {
			foreach ($dispatch as &$d) {
				$weight = 0;
				$weight+=$goodsDetail['weight'] * $goodsDetail['total'];
				$price = 0;
				if ($weight <= $d['firstweight']) {
					$price = $d['firstprice'];
				} else {
					$price = $d['firstprice'];
					$secondweight = $weight - $d['firstweight'];
					if ($secondweight % $d['secondweight'] == 0) {
							$price+= (int) ( $secondweight / $d['secondweight'] ) * $d['secondprice'];
					} else {
							$price+= (int) ( $secondweight / $d['secondweight'] + 1 ) * $d['secondprice'];
					}
				}
					$d['price'] = $price;
			}
		}
		//运费
		$dispatchprice = 0;

		foreach ($dispatch as $d) {
			if($dispatchid){
				if ($d['id'] == $dispatchid) {
						$dispatchprice = $d['price'];
						$sendtype = $d['dispatchtype'];
				}	
			}else{
				$dispatchid = $d['id'];
				$dispatchprice = $d['price'];
				$sendtype = $d['dispatchtype'];							
			}
									
		}

		$data['dispatchprice'] = $dispatchprice;
		$data['sentype'] = $sendtype;
		return $data;
}

function getOrderItemLists($result="",$goodsDetail=null,$goodsprice=""){
	global $_W,$_CONTENT;

	// $openid = pdo_fetch("SELECT openid FROM ".tablename("mc_mapping_fans")." WHERE uid = :uid",array(':uid'=>$_CONTENT['uid']));

	// if(!$openid){
	// 		$openid = $_CONTENT['uid'];
	// 	}else{
	// 		$openid = $openid['openid'];
	// }

	// $item_lists['weid'] = $_W['weid'];
	// $item_lists['from_user'] = $openid;
	// $item_lists['uid'] = $_CONTENT['uid'];
	// $item_lists['ordersn'] = date('md').random(4,1);
	$item_lists['price'] = ($goodsDetail['marketprice'] * $goodsDetail['total']) + $result['dispatchprice'];
	$item_lists['dispatchprice'] = $result['dispatchprice'];
	$item_lists['goodsprice'] = $_CONTENT['goodsprice'];
	// $item_lists['status'] = 0;
	// $item_lists['sendtype'] = intval($result['sendtype']);
	// $item_lists['dispatch'] =$_CONTENT['dispatchid'];
	// $item_lists['remark'] = $_CONTENT['for_remark'];
	// $item_lists['addressid'] = $_CONTENT['adId']['id'];
/*	$item_lists['expresscom'] = $_CONTENT['expresscom'];
	$item_lists['express'] = $_CONTENT['express'];*/
	// $item_lists['createtime'] = TIMESTAMP;
	// $item_lists['paytype'] = 1;
	// $item_lists['transid'] = 0;
	return $item_lists;
}

/**
 * 注册用户和运营用户的openid 为正常 uid
 * @param uid
 */
function getUserDefaultAddressId($uid = '',$type = true){
	global $_W;
	if($uid == ''){
		return false;
	}
	$condition = ' WHERE isdefault = 1 ';
	if($type){
		$condition .= ' and openid = :openid AND weid = :weid';
	}else{
		$condition = ' ORDER BY RAND() ';
	}
	$addressid =  pdo_fetchcolumn("SELECT id FROM " . tablename('shopping_address') . "  {$condition}",
							array(':weid'=>$_W['uniacid'], ':openid' => $uid));

	if($addressid){
		return $addressid;
	}
	return getUserDefaultAddressId($uid, false);
}

function Goods_getGoodsToOrderConfirm(){
	global $_W;
	// message('抱歉，商品限购时间已到，无法购买了！', $backUrl, "error");
}

/**
 * 每日特惠
 * @param where
 * @param psize
 * @param page
 * 排序：根据排序
 */
function Goods_getGoodsTofavorable($where = array(), $psize = 9){
	global $_GPC, $_W;

	$pindex = max(1, intval($_GPC["page"]));
	$condition = ' weid = :weid  AND deleted = 0 AND status = 1  AND type = 1 AND pcate <> 41 ';
	$orderBy = 'displayorder DESC';
	$pars = array(':weid' => $_W['uniacid']);
	$limit =  "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;

	//推荐
	if(isset($where['isdiscount'])){
		$condition .= ' and isdiscount = :isdiscount ';
		$pars[':isdiscount'] = $where['isdiscount'];
	}
	//新品
	if(isset($where['isnew'])){
		$condition .= ' and isnew = :isnew ';
		$pars[':isnew'] = $where['isnew'];
	}
	//热门
	if(isset($where['ishot'])){
		$condition .= ' and ishot = :ishot ';
		$pars[':ishot'] = $where['ishot'];
	}
	//闪购商品
	if(isset($where['isflash'])){
		$condition .= ' and isflash = :isflash ';
		$pars[':isflash'] = $where['isflash'];
	}
	//分类
	if(isset($where['pcate'])){
		$condition .= ' and pcate = :pcate ';
		$pars[':pcate'] = $where['pcate'];
	}
	//销量最高
	if(isset($where['sales'])){
		$orderBy = 'sales desc';
	}
	$sql = " SELECT id, title, thumb, thumb1, fdesc, marketprice price, price, sales,thumbtheme FROM " . tablename('shopping_goods') . " 
						  WHERE {$condition}  
						  ORDER BY {$orderBy} {$limit}";
	$list = pdo_fetchall($sql, $pars);
	if($list){
		foreach($list as $k=>$v){
			if($k == 0){
				$list[$k]['top'] = 1;
			}else{
				$list[$k]['top'] = 0;
			}
			$list[$k]['thumb'] = tomedia($v['thumb']);
			$list[$k]['thumb1'] = tomedia($v['thumb1']);
			$list[$k]['thumbtheme'] = tomedia($v['thumbtheme']);
			$list[$k]['link'] = url('entry/index/detail',array('m'=>'ewei_shopping','id'=>$v['id']));
		}
	}
	return $list;
}

/**
 * 根据产品分类获取产品
 * pcate(产品分类)
 * 
 */
function Goods_getGoodsByCategoryId($CategoryId = 0){
	global $_W;
	$condition = ' WHERE weid = :weid  AND deleted = 0 AND status = "1" AND type = 1';

	$pars = array(':weid' => $_W['uniacid']);
	if($CategoryId != 0){
		$condition .= ' AND pcate = :pcate ';
		$pars[':pcate'] = $CategoryId;
	}
	$list = pdo_fetchall("SELECT id, title, thumb, thumb1, marketprice price, price, fdesc FROM " . tablename('shopping_goods') . " 
						 $condition
						 ORDER BY displayorder DESC limit 3", $pars);
	foreach($list as $k=>$v){
		if($k == 0){
			$list[$k]['top'] = 1;
		}else{
			$list[$k]['top'] = 0;
		}
		$list[$k]['thumb'] = tomedia($v['thumb']);
		$list[$k]['thumb1'] = tomedia($v['thumb1']);
		$list[$k]['link'] = url('entry/index/detail',array('m'=>'ewei_shopping','id'=>$v['id']));
	}
	return $list;
}

/**
 * 获取当行产品信息
 *
 * @调用：产品详细和产品修改
 */
function Goods_getGoodsByDetail($goodsId = 0){
	$goods = pdo_fetch("SELECT * FROM " . tablename('shopping_goods') . " WHERE id = :id", array(':id' => $goodsId));

	return $goods;
}

/**
 * 修改产品点击量 
 *
 */
function Goods_putGoodsViewcount($goodsId = 0){
	global $_W;
	return pdo_query("UPDATE " . tablename('shopping_goods') . " 
			  		  SET viewcount = viewcount+1 
			  		  WHERE id =:id AND weid = :weid ", array(":id" => $goodsId, ':weid' => $_W['uniacid']));
}

/**
 * 商品详细获取规格
 *
 */
function Goods_getGoodsSpec($goodsId = 0){
	load()->model('shopping.spec');
	load()->model('shopping.spec.item');
	load()->model('shopping.goods.param');
	load()->model('shopping.goods.option');
	//规格及规格项
	$allspecs = Spec_getGoodsSpecByGoodsId($goodsId);					//获取该商品相关规格
	foreach ($allspecs as &$s) {
		$s['items'] = Spec_getSpecItemBySpecId($s['id']);				//根据该商品相关规格->获取规格值
	}

	unset($s);
	//处理规格项
	$options = Option_getOptionByGoodsId($goodsId);

	//排序好的specs
	$specs = array();
	//找出数据库存储的排列顺序
	if (count($options) > 0) {
		$specitemids = explode("_", $options[0]['specs'] );
		foreach($specitemids as $itemid){
			foreach($allspecs as $ss){
				$items = $ss['items'];
				foreach($items as $it){
					if($it['id']==$itemid){
						$specs[] = $ss;
						break;
					}
				}
			}
		}
	}

	return array('specs' => $specs,'options' => $options);
}

/**
 * 用于管里后台把规格渲染成表格
 * 
 */
function Goods_setSpecsToHtml($options = array(), $specs = array()){
	$html = '';
	$html .= '<table class="table table-bordered table-condensed">';
	$html .= '<thead>';
	$html .= '<tr class="active">';
	$len = count($specs);
	$newlen = 1; //多少种组合
	$h = array(); //显示表格二维数组
	$rowspans = array(); //每个列的rowspan
	for ($i = 0; $i < $len; $i++) {
		//表头
		$html .= "<th style='width:80px;'>" . $specs[$i]['title'] . "</th>";
		//计算多种组合
		$itemlen = count($specs[$i]['items']);
		if ($itemlen <= 0) {
			$itemlen = 1;
		}
		$newlen *= $itemlen;
		//初始化 二维数组
		$h = array();
		for ($j = 0; $j < $newlen; $j++) {
			$h[$i][$j] = array();
		}
		//计算rowspan
		$l = count($specs[$i]['items']);
		$rowspans[$i] = 1;
		for ($j = $i + 1; $j < $len; $j++) {
			$rowspans[$i]*= count($specs[$j]['items']);
		}
	}
	// print_r($rowspans);exit();
	$html .= '<th class="info" style="width:100px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">库存</div><div class="input-group"><input type="text" class="form-control option_stock_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_stock\');"></a></span></div></div></th>';
	$html .= '<th class="success" style="width:120px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">销售价格</div><div class="input-group"><input type="text" class="form-control option_marketprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_marketprice\');"></a></span></div></div></th>';
	$html .= '<th class="warning" style="width:120px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">市场价格</div><div class="input-group"><input type="text" class="form-control option_productprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_productprice\');"></a></span></div></div></th>';
	$html .= '<th class="danger" style="width:120px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">成本价格</div><div class="input-group"><input type="text" class="form-control option_costprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_costprice\');"></a></span></div></div></th>';
	$html .= '<th class="info" style="width:120px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">重量（克）</div><div class="input-group"><input type="text" class="form-control option_weight_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_weight\');"></a></span></div></div></th>';
	$html .= '<th class="info" style="width:100px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">一级提成</div><div class="input-group"><input type="text" class="form-control option_comm1_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_comm1\');"></a></span></div></div></th>';
	$html .= '<th class="info" style="width:100px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">二级提成</div><div class="input-group"><input type="text" class="form-control option_comm2_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_comm2\');"></a></span></div></div></th>';
	$html .= '<th class="info" style="width:100px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">三级提成</div><div class="input-group"><input type="text" class="form-control option_comm3_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_comm3\');"></a></span></div></div></th>';
	$html .= '</tr></thead>';
	for($m=0;$m<$len;$m++){
		$k = 0;$kid = 0;$n=0;
		for($j=0;$j<$newlen;$j++){
			$rowspan = $rowspans[$m]; //9
			if( $j % $rowspan==0){
				$h[$m][$j]=array("html"=> "<td rowspan='".$rowspan."'>".$specs[$m]['items'][$kid]['title']."</td>","id"=>$specs[$m]['items'][$kid]['id']);
				// $k++; if($k>count($specs[$m]['items'])-1) { $k=0; }
			}
			else{
				$h[$m][$j]=array("html"=> "","id"=>$specs[$m]['items'][$kid]['id']);
			}
			$n++;
			if($n==$rowspan){
				$kid++; if($kid>count($specs[$m]['items'])-1) { $kid=0; }
				$n=0;
			}
		}
	}
	$hh = "";
	for ($i = 0; $i < $newlen; $i++) {
		$hh.="<tr>";
		$hh.="<input type='hidden' name='option_specLenth[]' value='".$i."'/>";
		$ids = array();
		for ($j = 0; $j < $len; $j++) {
			$hh.=$h[$j][$i]['html'];
			$ids[] = $h[$j][$i]['id'];
		}
		$ids = implode("_", $ids);
		$val = array("id" => "","title"=>"", "stock" => "", "deduct" => "", "price" => "", "marketprice" => "", "weight" => "", "comm1" => "", "comm2" => "", "comm3" => "");
		foreach ($options as $o) {
			if ($ids === $o['specs']) {
				$val = array(
					"id" => $o['id'],
					"title" =>$o['title'],
					"stock" => $o['stock'],
					"deduct" => $o['costprice'],
					"price" => $o['productprice'],
					"marketprice" => $o['marketprice'],
					"weight" => $o['weight'],
					"comm1" => $o['comm1'],
					"comm2" => $o['comm2'],
					"comm3" => $o['comm3'],
				);
				break;
			}
		}
		$hh .= '<td class="info">';
		$hh .= '<input name="option_stock_' . $ids . '[]"  type="text" class="form-control option_stock option_stock_' . $ids . '" value="' . $val['stock'] . '"/></td>';
		$hh .= '<input name="option_id_' . $ids . '[]"  type="hidden" class="form-control option_id option_id_' . $ids . '" value="' . $val['id'] . '"/>';
		$hh .= '<input name="option_ids[]"  type="hidden" class="form-control option_ids option_ids_' . $ids . '" value="' . $ids . '"/>';
		$hh .= '<input name="option_title_' . $ids . '[]"  type="hidden" class="form-control option_title option_title_' . $ids . '" value="' . $val['title'] . '"/>';
		$hh .= '</td>';
		$hh .= '<td class="success"><input name="option_marketprice_' . $ids . '[]" type="text" class="form-control option_marketprice option_marketprice_' . $ids . '" value="' . $val['marketprice'] . '"/></td>';
		$hh .= '<td class="warning"><input name="option_productprice_' . $ids . '[]" type="text" class="form-control option_productprice option_productprice_' . $ids . '" " value="' . $val['productprice'] . '"/></td>';
		$hh .= '<td class="danger"><input name="option_costprice_' . $ids . '[]" type="text" class="form-control option_costprice option_costprice_' . $ids . '" " value="' . $val['costprice'] . '"/></td>';
		$hh .= '<td class="info"><input name="option_weight_' . $ids . '[]" type="text" class="form-control option_weight option_weight_' . $ids . '" " value="' . $val['weight'] . '"/></td>';
		$hh .= '<td class="info"><input name="option_comm1_' . $ids . '[]" type="text" class="form-control option_comm1 option_comm1_' . $ids . '" " value="' . $val['comm1'] . '"/></td>';
		$hh .= '<td class="info"><input name="option_comm2_' . $ids . '[]" type="text" class="form-control option_comm2 option_comm2_' . $ids . '" " value="' . $val['comm2'] . '"/></td>';
		$hh .= '<td class="info"><input name="option_comm3_' . $ids . '[]" type="text" class="form-control option_comm3 option_comm3_' . $ids . '" " value="' . $val['comm3'] . '"/></td>';
		$hh .= '</tr>';
	}
	$html .= $hh;
	$html .= "</table>";
	return $html;
}

/**
 * 保存产品信息
 */
function Goods_saveGoods($id = 0){
	global $_W, $_GPC;
	$data = array(
		'weid' 			=> intval($_W['uniacid']),
		'displayorder' 	=> intval($_GPC['displayorder']),
		'title'			=> $_GPC['goodsname'],
		'pcate' 		=> intval($_GPC['pcate']),
		'ccate' 		=> intval($_GPC['ccate']),
		'thumb' 		=> $_GPC['thumb'],
		'thumb1' 		=> $_GPC['thumb1'],
		'thumbtheme'	=> $_GPC['thumbtheme'],
		'type' 			=> intval($_GPC['type']),
		'isrecommand' 	=> intval($_GPC['isrecommand']),
		'ishot' 		=> intval($_GPC['ishot']),
		'isnew' 		=> intval($_GPC['isnew']),
		'isdiscount' 	=> intval($_GPC['isdiscount']),
		'istime'		=> intval($_GPC['istime']),
		'isflash'		=> intval($_GPC['isflash']),
		'timestart' 	=> strtotime($_GPC['timestart']),
		'timeend' 		=> strtotime($_GPC['timeend']),
		'description' 	=> $_GPC['description'],
		'content' 		=> htmlspecialchars_decode($_GPC['content']),
		'unit' 			=> $_GPC['unit'],
		'createtime' 	=> TIMESTAMP,
		'total'			=> intval($_GPC['total']),
		'totalcnf' 		=> intval($_GPC['totalcnf']),
		'weight' 		=> $_GPC['weight'],
		'deduct' 		=> $_GPC['costprice'],
		'price' 		=> $_GPC['productprice'],
		'originalprice' => $_GPC['originalprice'],
		'marketprice' 	=> $_GPC['marketprice'],
		'productprice'  => $_GPC['productprice'],
		'goodssn' 		=> $_GPC['goodssn'],
		'productsn' 	=> $_GPC['productsn'],
		'credit' 		=> intval($_GPC['credit']),
		'maxbuy' 		=> intval($_GPC['maxbuy']),
		'hasoption' 	=> intval($_GPC['hasoption']),
		'sales' 		=> intval($_GPC['sales']),
		'fdesc' 		=> $_GPC['fdesc'],
		'country'		=> $_GPC['country'],
		'brand'			=> $_GPC['brand'],
		'label' 		=> $_GPC['label'],
		'sid' 			=> intval($_GPC['sid']),
		'wid' 			=> intval($_GPC['wid']),
		'comm1' 		=> floatval($_GPC['comm1']),
		'comm2' 		=> floatval($_GPC['comm2']),
		'comm3' 		=> floatval($_GPC['comm3']),
		'relatedgoods'	=> trim($_GPC['relatedgoods'])
	);
	if($data['total'] === -1) {
		$data['total'] = 0;
		$data['totalcnf'] = 2;
	}
	if(is_array($_GPC['thumbs'])){
		$data['thumb_url'] = serialize($_GPC['thumbs']);
	}
	// if($data['taxrate']<0 || $data['taxrate']>30){
	// 	message('税率合理范围是0%--50%！', $backUrl, "error");
	// }
	if (empty($id)) {
		pdo_insert('shopping_goods', $data);
		return pdo_insertid();
	}else{
		unset($data['createtime']);
		pdo_update('shopping_goods', $data, array('id' => $id));
		Goods_UpdateGoodsOrderPrice($id);
	}
	return $id;
}

/**
 * 修改订单价格
 * goodlist = empty return false
 * 判断是否为规格商品
 */
function Goods_UpdateGoodsOrderPrice($goodsid = ""){
	// $goodsid = !empty($goodsid) ? $goodsid : false;

	// $goodlist = pdo_fetchall("SELECT a.id, a.dispatchprice , b.orderid, b.total , b.optionid ,c.marketprice FROM ".tablename("shopping_order")." 
	// 			AS a LEFT JOIN ".tablename("shopping_order_goods")." AS b ON a.id = b.orderid LEFT JOIN ".tablename("shopping_goods_option")."
	// 		 	AS c ON b.goodsid = c.goodsid and b.optionid = c.id WHERE b.goodsid = {$goodsid}");
	//var_dump($goodlist);exit;
	
	// if(!empty($goodlist)){
	// 	foreach ($goodlist as $sheets) {
	// 		if($sheets['optionid'] != 0 && $sheets['marketprice'] != '' || !empty($sheets['optionid'])){
	// 			$price = (int) ($sheets['marketprice'] + $sheets['dispatchprice']) * $sheets['total'];
	// 			pdo_update("shopping_order", array('price' => $price,'goodsprice' => $price), array('id' => $sheets['orderid']));
	// 			pdo_update("shopping_order_goods", array('price' => $sheets['marketprice']), array('orderid' => $sheets['orderid']));
	// 	 	}else{
	// 	 		// $price = (int) ($nprice + $sheets['dispatchprice']) * $sheets['total'];
	// 	 		// pdo_update("shopping_order", array('price' => $price,'goodsprice' => $price), array('id' => $sheets['orderid']));
	// 	 		// pdo_update("shopping_order_goods", array('price' => $nprice), array('orderid' => $sheets['orderid']));
	// 	 	}	 	
	// 	}

	// }else{
	// 	return false;
	// }
}

/**
 * 每日特惠
 * isdiscount = 1
 * 排序：根据排序
 */
function Goods_getGoodsToSignAddGoods(){
	global $_GPC, $_W;
	$pindex = max(1, intval($_GPC["page"]));
	$psize = 5;
	$list = pdo_fetchall("SELECT id, title, thumb FROM " . tablename('shopping_goods') . " 
						  WHERE weid = :weid  AND deleted = 0 AND status = 1  AND type = 1 
						  ORDER BY displayorder DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $_W['uniacid']));
	if(!empty($list)){
		foreach($list as $k=>$v){
			$list[$k]['thumb'] = tomedia($v['thumb']);
		}
	}
	return $list;
}

/**
 * 首页产品（排序高、首页分类显示） 
 * 
 */
function Goods_getGoodsToHead(){
	global $_W;
	$sql = "SELECT o.`label`, o.id, o.title, o.title, o.thumb1, o.marketprice, o.fdesc, o.viewcount
			FROM ims_shopping_goods o
			WHERE o.isrecommand = 1 AND o.weid = :weid AND deleted = 0 AND o.status = 1
			ORDER BY o.displayorder DESC";
	$pars = array(':weid' => $_W['uniacid']);
	return pdo_fetchall($sql, $pars);
}


/**
 * 管理后台商品管理列表
 * @param str keyword 关键词
 * @param int cate_2 子分类
 * @param int cate_1 顶级分类
 * @param int status 状态
 * @param int isnew  新品
 * @param int isrecommand 推荐
 * @param int ishot  热门
 * @param int isdiscount  热销
 * @param int istime  限时
 * @param int isimport  进口
 * @param int $sid 供应商ID
 */
function Goods_ListToManage(){
	global $_GPC, $_W;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$condition = " WHERE a.deleted=0 ";

	if (!empty($_GPC['cate_2'])) {
		$cid = intval($_GPC['cate_2']);
		$condition .= " AND a.ccate = '{$cid}'";
	} elseif (!empty($_GPC['cate_1'])) {
		$cid = intval($_GPC['cate_1']);
		$condition .= " AND a.pcate = '{$cid}'";
	}
	if (!isset($_GPC['status'])) {
		$_GPC['status'] = 1;
	}
	$condition .= " AND a.status = '" . intval($_GPC['status']) . "'";

	//关键词模糊查找
	$keyword = trim($_GPC['keyword']);
	if (!empty($keyword)) {
		$condition .= " AND (a.title LIKE '%{$keyword}%' OR a.fdesc LIKE '%{$keyword}%' OR a.tags LIKE '%{$keyword}%')";
	}

	if (isset($_GPC['isnew'])) {
		$condition .= " AND a.isnew = '" . intval($_GPC['isnew']) . "'";
	}
	if (isset($_GPC['isrecommand'])) {
		$condition .= " AND a.isrecommand = '" . intval($_GPC['isrecommand']) . "'";
	}
	if (isset($_GPC['ishot'])) {
		$condition .= " AND a.ishot = '" . intval($_GPC['ishot']) . "'";
	}
	if (isset($_GPC['isflash'])) {
		$condition .= " AND a.isflash = '" . intval($_GPC['isflash']) . "'";
	}
	if (isset($_GPC['isdiscount'])) {
		$condition .= " AND a.isdiscount = '" . intval($_GPC['isdiscount']) . "'";
	}
	if (isset($_GPC['istime'])) {
		$condition .= " AND a.isrecommand = '" . intval($_GPC['istime']) . "'";
	}
	if (isset($_GPC['isbrush'])) {
		$condition .= " AND a.isbrush = '" . intval($_GPC['isbrush']) . "'";
	}
	if ($_GPC['sid'] || $_W['user']['sid']) {
		$sid = $_W['user']['sid'] ? $_W['user']['sid'] : intval($_GPC['sid']);
		$condition .= " AND a.sid = '{$sid}'";
	}
	$list = pdo_fetchall("SELECT a.*, b.company FROM " . tablename('shopping_goods') . " a LEFT JOIN ".tablename('shopping_suppliers')." b on a.sid = b.sid $condition ORDER BY a.createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
	$total = pdo_fetchcolumn('SELECT COUNT(a.id) FROM ' . tablename('shopping_goods') . " a $condition");
	$pager = pagination($total, $pindex, $psize);

	return array('list' => $list, 'pager' => $pager, 'total' => $total);
}

/**
 * 按分类取得全部商品
 * @param int $cateId 分类
 * @return array
 */
function Goods_getGoodsByCate($cateId){
	global $_W;
	$cateId = intval($cateId);
	// return pdo_fetchall("SELECT * FROM " . tablename('shopping_goods') . " WHERE weid = '{$_W['uniacid']}' and ccate='{$cateId}' and deleted=0 and timestart<=".time()." and timeend>=".time()." ORDER BY createtime DESC");
	$res = pdo_fetchall("SELECT id, thumb, title, total, marketprice FROM " . tablename('shopping_goods') . " 
						 WHERE weid = :weid and ccate = :ccate and deleted = 0  and status=1 ORDER BY displayorder DESC, createtime DESC", array(':weid' => $_W['uniacid'], ':ccate' => $cateId));	
	foreach($res as $k=>$v){
		$res[$k]['thumb'] = tomedia($v['thumb']);
	}
	return $res;
}

/**
 * 检查将要购买的商品
 * @param goodsid   商品ID
 * @param optionid  规格
 * @param buynum    购买数量
 * @return array || str
 */
function Goods_checkGoods($goodsid = 0, $optionid = 0, $buynum = 0){
	global $_W;
	$goodsid = intval($goodsid);
	if($goodsid != 0 && $buynum != 0){
		$res = pdo_fetch("SELECT id goodsid, total, title, marketprice, maxbuy FROM " . tablename('shopping_goods') . " WHERE id = :id and status = 1 and deleted = 0", array(':id' => $goodsid));
		if(!empty($res)){
			if(!empty($optionid)){
				$optionres = pdo_fetch("select marketprice, stock from " . tablename('shopping_goods_option') . " where id = :id limit 1", array(":id" => $optionid));
				if($optionres){
					$res['marketprice'] = $optionres['marketprice'];
					$res['total'] = $optionres['stock'];
					$res['optionid'] = $optionid;
				}
			}
			if($buynum > $res['maxbuy'] && $res['maxbuy'] != 0 ){
				return $res['title'].' 只限购 '.$res['maxbuy'].' 件！';
			}
			if($buynum > $res['total'] || $res['total'] == 0){
				return $res['title'].' 已销售完，请选择其他产品！';
			}
			unset($res['maxbuy']);
			unset($res['title']);
			$res['weid'] = $_W['uniacid'];
			$res['goodstype'] = 1;
			$res['community'] = 1;
			$res['total'] = $buynum;
			$res['createtime'] = TIMESTAMP;
			$res['from_user'] = $_W['fans']['from_user'];
			return $res;
		}
		return '有商品不存在请重新选择！';
	}
	return '提交的参数丢失，请刷新后再试！';
}


/**
 * SG_Goodsex 导出商品
 * @param intval $status  上架状态, 0-下架，1-上架，空-全部
 * @return file
 */
function SG_Goodsex($status='') {
	global $_W;
	require_once('../framework/library/phpexcel/PHPExcel.php');
	$objPHPExcel = new PHPExcel();

	$condition = 'sg.deleted=0';
	if($status == 1){
		$statusTitle = '上架商品';
		$condition .= ' AND sg.status=1';
	}else if($status == 0){
		$statusTitle = '下架商品';
		$condition .= ' AND sg.status=0';
	}else{
		$statusTitle = '全部商品';
	}

	$sql = 'SELECT sg.*, sc.name as pcate, sc2.name as ccate  FROM '.tablename('shopping_goods').' sg 
          LEFT JOIN '.tablename('shopping_category').' sc ON sc.id=sg.pcate
          LEFT JOIN '.tablename('shopping_category').' sc2 ON sc2.id=sg.ccate
          WHERE  '.$condition;
	$re = pdo_fetchall($sql, array());

	if($re){
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', '商品ID')
					->setCellValue('B1', '品名')
					->setCellValue('C1', '原价')
					->setCellValue('D1', '销售价')
					->setCellValue('E1', '库存')
					->setCellValue('F1', '分成总额')
					->setCellValue('G1', '大类')
					->setCellValue('H1', '小类')
					->setCellValue('I1', '型号编码')
					->setCellValue('J1', '商品条码')
					->setCellValue('K1', '品牌名')
					->setCellValue('L1', '自营')
					->setCellValue('M1', '税率')
					->setCellValue('N1', '商品标签');
		//set width  
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(58);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(16);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(6);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(6);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(18);
		foreach($re as $key=>$val) {
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A'.($key+2),$val['id'])
						->setCellValue('B'.($key+2), $val['title'])
						->setCellValue('C'.($key+2), $val['originalprice'])
						->setCellValue('D'.($key+2), $val['marketprice'])
						->setCellValue('E'.($key+2), $val['total'])
						->setCellValue('F'.($key+2), $val['comm1'])
						->setCellValue('G'.($key+2), $val['pcate'])
						->setCellValue('H'.($key+2), $val['ccate'])
						->setCellValue('I'.($key+2), $val['goodssn'])
						->setCellValue('J'.($key+2), $val['productsn'])
						->setCellValue('K'.($key+2), $val['brand'])
						->setCellValue('L'.($key+2), $val['selfrun']?'是':'否')
						->setCellValue('M'.($key+2), $val['taxrate'].'%')
						->setCellValue('N'.($key+2), $val['tags']);
		}
		$objPHPExcel->getActiveSheet()->setTitle($statusTitle);
		$objPHPExcel->setActiveSheetIndex(0);

		$date = date('ymd_His');
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="GOODS_'.$date.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');

		exit;
	}
	message('操作失败，没有数据！', referer(), 'error');
}