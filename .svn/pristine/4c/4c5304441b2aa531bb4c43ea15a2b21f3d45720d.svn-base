<?php
/**
 * 红包
 * load()->model('redpacket');
 */

/**
 * 获取所有红包列表
 */
function Redpacket_getListToManage(){
	global $_W, $_GPC;
	$ret= pdo_fetchall('SELECT * FROM '.tablename('redpacket').' ORDER BY id DESC');
	return $ret;
}

/**
 * 创建红包
 */
function Redpacket_createNewOne($data) {
	return pdo_insert('redpacket', $data);
}

/**
 * 保存红包数据
 */
function Redpacket_saveInfo(){
	global $_W, $_GPC;
	$id = intval($_GPC['id']);
	$data = array(
			'startdt' => strtotime($_GPC['startdt']),
			'endingdt' => strtotime($_GPC['endingdt']),
			'sharetitle' => $_GPC['sharetitle'],
			'sharedesc' => $_GPC['sharedesc'],
			'sharethumb'=> $_GPC['sharethumb'],
			'updatedt'=>time()
		);
	//过滤提示
	//if(!$data['uid']){ message('获取用户不能为空', referer(), 'error'); }
	//id存在保存数据,不存在添加数据
	if($id){
		$rs = pdo_update('redpacket', $data, array('id' => $id));
	}else{
		$data['createdt'] = time();
		$data['sn'] = md5(time()*1000);//零时sn
		//获取优惠券所有类型数组
		$couponTypes= $_GPC['typeids'];
		$total = $_GPC['total'];
		$data['total']=$total;
		pdo_insert('redpacket', $data);
		$id = pdo_insertid();
		//获取权重
		$typeWeight=WeightCoefficient($couponTypes);
		//添加优惠券数据
		$couponIds=AddCouponData($typeWeight[0],$total);
		//添加红包记录
		$ret=AddRedpacketData($couponIds,$id,$typeWeight[1]);
		
	}
	return $id;
}
//获取所有生效的优惠券类型
function Coupon_getTypeList(){
	global $_W, $_GPC;
	return pdo_fetchall('SELECT * FROM '.tablename('coupon_type').' WHERE status=1 ORDER BY id DESC');
}
//生成红包数据
function AddRedpacketData($couponIds,$id,$typeValue){
	$data=array(
		'redpacketid'=>$id,
		'createdt'=>time(),
		'updatedt'=>time()
		);
	foreach ($couponIds as $key => $type) {
		foreach ($type as $v => $item) {
			$data['couponid']=$item;
			$data['coupontype']=$key;
			$data['coupon_value']=$typeValue[$key];
			pdo_insert('redpacket_coupon', $data);
		}
	}
	return true;
}
//添加优惠券记录
function AddCouponData($typeWeight,$total){
	$couponIds=array();
	$data=array(
		'expire_begin'=>time(),
		'expire_end'=>strtotime('+10 day'),
		'status'=>2,
		'source'=>7,
		'create_time'=>time(),
	);
	foreach ($typeWeight as $key => $item) {
		//根据权重添加数据
		$num=floor($item*$total);
		if ($item==1) {
			$num=$total;
		}
		for ($i=0; $i <$num; $i++) { 
			$data['type_id']=$key;
			$data['no']=getRandString(12);
			pdo_insert('coupon', $data);
			$couponIds[$key][]=pdo_insertid();
			$addnum++;
		}
	}
	//判断剩余红包数量
	$surplusNum=$total-$addnum;
	for ($i=0; $i <$surplusNum; $i++) { 
		$keyArr=array_keys($typeWeight);
		$data['type_id']=end($keyArr);
		$type_id=$data['type_id'];
		$data['no']=getRandString(12);
		pdo_insert('coupon', $data);
		$couponIds[$type_id][]=pdo_insertid();
	}
	return $couponIds;
}

function getRandString($bits) {
	$rstr = '';
	$chars = array(
		'1','2','3','4','5','6','7','8','9','0',
		'A','B','C','D','E','F','G','H','I','J','K','L','M','N',
		'O','P','Q','R','S','T','U','V','W','X','Y','Z',
	);
	for ($i=0; $i < $bits; $i++) { 
		$rstr .= $chars[rand(0,35)];
	}
	return $rstr;
}

//生成红包编号
function GetRedpacketSn(){

	return md5(time()*1000);
}

//根据类型获取权重
function WeightCoefficient($typeids){
	//获取有由类型数据
	$strType=implode(',',$typeids);
	$typedata=pdo_fetchall('SELECT id,value FROM '.tablename('coupon_type').' WHERE  id in('.$strType.') ORDER BY value DESC');
	$totalvalue=0;
	$typeWeight=array();
	$typeValue=array();
	foreach ($typedata as $key => $item) {
		$totalvalue+=$item['value'];
	}
	//如何通过红包数量合理分配红包类型 权重计算sprintf("%.2f",value/sum(value*))
	foreach ($typedata as $key => $item) {
		$typeWeight[$item['id']]=sprintf("%.2f",$item['value']/$totalvalue);
		$typeValue[$item['id']]=$item['value'];
	}
	//排序
	$len=count($typeWeight);
	$b=array_values($typeWeight); 
	for($i=0;$i<$len;$i++){
	    for ($j=0; $j < $len-1-$i; $j++) { 
	    	 if ($b[$j]>$b[$j+1]) {
	    	 	$temp  = $b[$j];
	    	 	$b[$j] = $b[$j+1];
	    	 	$b[$j+1]= $temp;
	    	 }
	    }
	}
	$i=0;
	foreach ($typeWeight as $key => $item) {
		$typeWeight[$key]=$b[$i];
		$i++;
	}
	$data=[$typeWeight,$typeValue];
	return $data;
}

/**获取单个红包详细*/
function Redpacket_getDetailToManage($id = 0){
	global $_W, $_GPC;
	$id || $id = $_GPC['id'];
	return pdo_fetch('select * from '.tablename('redpacket').'where id = :id', array(':id' => intval($id)));
}