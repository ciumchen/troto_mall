<?php
/**
 * load()->model('community.vendors');
 * 社区商家
 *
 */

function vendors_getVendorsOnceInfo(){
	global $_W,$_GPC;
	$data = array();
	$vendorid = !empty($_GPC['vendorid']) ? intval($_GPC['vendorid']) : 0;
	$sql = "SELECT * FROM ".tablename("community_vendors")." WHERE vendorid = :vendorid";
	$list = pdo_fetch($sql,array(":vendorid"=>$vendorid));	

	if(!empty($_GPC['sercateid'])){
		$sercateid = intval($_GPC['sercateid']);
		$res = pdo_fetch("SELECT * FROM ".tablename("community_service_category")." WHERE sercateid = {$sercateid}");
		if($res['parentid'] > 0){
			$data['sercateid'] = $res['parentid'];
			$data['parentid'] = $res['sercateid'];
			$data['class_list'] = pdo_fetchall("SELECT * FROM ".tablename("community_service_category")." WHERE 
									parentid = {$data['sercateid']}");
			$data['item_list'] = $list;
		} else {
			$data['sercateid'] = $res['sercateid'];
			$data['item_list'] = $list;
		}

		return $data;
	}

	return $list;
}

function vendors_addVendorsToManager($type = ""){
	global $_W, $_GPC;
	$data = array();
	if(!empty($_GPC['sercate_class'])){
		$sercateid = intval($_GPC['sercate_class']);
	} else {
		$sercateid = intval($_GPC['sercate']);
	}

	$comgroupid = intval($_GPC['comgroup']);

	if(!($sercateid and $comgroupid)) message("参数有错误！",referer(),"error");
	$title = trim(htmlspecialchars(str_replace(" ","",$_GPC['title'])));

	if(empty($title)){
		message("商家名称不能为空！",referer(),"error");
	}

	if(empty($_GPC['reside']['province'] && $_GPC['reside']['city'] && $_GPC['reside']['district'])){
		message("请完善省市区地址！",referer(),"error");
	}

	if(empty($_GPC['address'])){
		message("请填写详细地址！",referer(),"error");
	}

	if(strlen($_GPC['mobile']) > 13){
		message("手机号码格式错误！",referer(),"error");
	}
	
	$data['sercateid'] = $sercateid;
	$data['comgroupid'] = $comgroupid;
	$data['title'] = $title;
	$data['score'] = (float)($_GPC['score']);
	$data['average'] = (float)($_GPC['average']);
	$data['saled'] = intval($_GPC['saled']);
	$data['province'] = $_GPC['reside']['province'];
	$data['city'] = $_GPC['reside']['city'];
	$data['area'] = $_GPC['reside']['district'];
	$data['address'] = $_GPC['address'];
	if(is_array($_GPC['thumbs'])){
		$thumbs = serialize($_GPC['thumbs']);
	}
	$data['thumb'] = $_GPC['thumb'];
	$data['thumbs'] = $thumbs;
	$data['linkman'] = $_GPC['linkman'];
	$data['tel'] = $_GPC['tel'];
	$data['mobile'] = $_GPC['mobile'];
	$data['content'] = str_replace(" ","",$_GPC['content']);
	$data['notice'] = str_replace(" ","",$_GPC['notice']);
	$time = array(
		'starttime_1' => strtotime(str_replace("：",":",$_GPC['starttime_1'])),
		'endtime_1' => strtotime(str_replace("：",":",$_GPC['endtime_1']))
	);
	if(!empty($_GPC['starttime_2'] && $_GPC['endtime_2'])){
			$time['starttime_2'] =  strtotime(str_replace("：",":",$_GPC['starttime_2']));
			$time['endtime_2'] = strtotime(str_replace("：",":",$_GPC['endtime_2']));
	} 

	if (!empty($_GPC['starttime_3'] && $_GPC['endtime_3'])){
			$time['starttime_3'] =  strtotime(str_replace("：",":",$_GPC['starttime_3']));
			$time['endtime_3'] = strtotime(str_replace("：",":",$_GPC['endtime_3']));
	}

	$data['businesshours'] = serialize($time);
	$data['remark'] = htmlspecialchars($_GPC['remark']);
	$data['status'] = intval($_GPC['status']);

	if($type == 'add'){
		pdo_insert("community_vendors",$data);
		$res = pdo_insertid();
		$message = "添加";
	} elseif($type == 'editor') {

		$vendorid = intval($_GPC['vendorid']);
		$res = pdo_update("community_vendors",$data,array('vendorid' => $vendorid));
		$message = "修改";
	}
	if($res){
		message($message."成功！",url('community/vendors'),"success");
	}
	message($message."失败！",referer(),"error");
		
}

function vendors_getVendorsInfoMation(){
	global $_W, $_GPC;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;



	$list = pdo_fetchall("SELECT a.*,b.groupname,c.title as catetitle FROM ".tablename("community_vendors")." as a 
						left join ".tablename("community_group")." as b on a.comgroupid=b.comgroupid left join 
						".tablename("community_service_category")." as c on a.sercateid=c.sercateid WHERE 1 GROUP BY a.vendorid ASC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
	
	$total = pdo_fetchcolumn('SELECT COUNT(vendorid) FROM ' . tablename('community_vendors'));
	$pager = pagination($total, $pindex, $psize);

	return array('list' => $list, 'pager' => $pager, 'total' => $total);
}

function vendors_Alldetail($vendorid = 0){
	return pdo_fetch("SELECT a.*,b.groupname,c.title as catetitle FROM ".tablename("community_vendors")." as a 
						left join ".tablename("community_group")." as b on a.comgroupid=b.comgroupid left join 
						".tablename("community_service_category")." as c on a.sercateid=c.sercateid WHERE a.vendorid = :vendorid",array(":vendorid" => $vendorid));
}

function Confirm_timeSerialize($data = array(),$keywords = ""){
	if(empty($data)){
		return "";
	}
	$data = unserialize($data);
	if(empty($data[$keywords])){
		return "";
	}
	if($keywords == "endtime_1" || $keywords == "endtime_2" || $keywords == "endtime_3"){
		if($data[$keywords] == '1443628800'){
			return "24:00";
		}
	}
	return date('H:i',$data[$keywords]);
}	

function vendors_getGroupToDetail(){
	global $_GPC;

	$groupid = intval($_GPC['id']);

	$sql = "SELECT * FROM ".tablename("community_vendors")." WHERE comgroupid = {$groupid}";
	return pdo_fetchall($sql);
}

function vendors_prosAddVendorsInfo(){
	global $_W, $_GPC;
	$data = array();
	$data['sercateid'] = 1;
	$data['comgroupid'] = (int) ($_GPC['id']);
	$data['title'] = trim($_GPC['shopName']);
	$data['tel'] = $_GPC['shopTelAreaCode'].'-'.$_GPC['shopTel'];
	$data['province'] = $_GPC['shopProvance'];
	$data['city'] = $_GPC['shopCity'];
	$data['area'] = $_GPC['shopArea'];
	$data['address'] = trim($_GPC['shopAddr']);
	$data['content'] = trim($_GPC['shopDesc']);
	$img = explode(',', $_GPC['vendorImgList']);
	if(count($img) < 3){
		message("最少上传3张图片！",referer(),'error');
	}else if(count($img) > 6){
		message("最多上传6张图！",referer(),'error');
	}
	foreach ($img as $Index => $imgs) {
		$fCont = file_get_contents(tomedia($imgs));
		if(strlen($fCont) > 2097152){
			message("上传的图片不能大于2M！请重新上传！",referer(),'error');
			break;
		}	
	}
	$data['thumb'] = $img[0];
	$data['thumbs'] = serialize($img);
	$data['linkman'] = $_GPC['shopUserName'];
	$data['mobile'] = (int) ($_GPC['shopUserPhone']);
	$data['recode'] = $_GPC['shopUserWechat'];

	pdo_insert("community_vendors",$data);
	$sid = pdo_insertid();
	if($sid > 0){
		message("添加成功！",url('community/index').'id='.$_GPC['id'].'&m=ewei_shopping','success');
	} else {
		message("添加失败！",referer(),'error');
	}
}

