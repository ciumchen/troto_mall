<?php
/**
 * load()->model('community.group');
 * 社区群
 *
 */

/**
 * 社群添加或者修改
 * @param int $id 群ID
 * @param int data 群信息
 */
function getGroupimg(){
	global $_GPC;
	$id = (int) ($_GPC['id']);
	return pdo_fetch("SELECT `thumb` FROM ".tablename("community_group")." WHERE comgroupid = {$id}");
}
function comgroup_insertToManage(){
	global $_W, $_GPC;
	$groupid = (int)$_GPC['id'];
	$data = array(
			'groupname' => $_GPC['groupname'],
			'communityname' => $_GPC['communityname'],
			'province' => $_GPC['reside']['province'],
			'city' => $_GPC['reside']['city'],
			'area' => $_GPC['reside']['district'],
			'address' => $_GPC['address'],
			'thumb' => $_GPC['thumb'],
			'thumbs' => $_GPC['thumbs'],
			'serid' => intval($_GPC['customservice']),
			'subway' => $_GPC['subway'],
			'bus' => $_GPC['bus'],
			'content' => $_GPC['content'],
			'tel' => $_GPC['tel'],
			'property' => $_GPC['property'],
			'createtime' => TIMESTAMP
		);

	if(is_array($data['thumbs'])){
		$data['thumbs'] = serialize($data['thumbs']);
	}
	if($groupid){
		$res = pdo_update('community_group', $data, array('comgroupid' => $groupid));
	}else{
		pdo_insert('community_group', $data);
		$res = pdo_insertid();
	}

	if($res){
		message('编辑成功', referer(), 'success');
	}
	message('操作失败！', referer(), 'danger');
}

/**
 * list
 *
 */
function comgroup_listToManage(){
	global $_W, $_GPC;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$condition = "";

	$sql = "SELECT ta.comgroupid, ta.groupname, ta.communityname, ta.province, ta.city, ta.area, ta.address, ta.thumb, ta.status, ta.subway,ta.bus, ta.createtime,
				tb.title as sername FROM " . tablename('community_group') . " as ta left join ".tablename("community_custom_service")." as tb on ta.serid=tb.serid 
				$condition ORDER BY ta.displayorder DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;

	$list = pdo_fetchall($sql);
	$total = pdo_fetchcolumn('SELECT COUNT(comgroupid) FROM ' . tablename('community_group') . " $condition");
	$pager = pagination($total, $pindex, $psize);

	return array('list' => $list, 'pager' => $pager, 'total' => $total);
}

function comgroup_detail(){
	global $_W, $_GPC;
	$groupid = (int)$_GPC['id'];
	$sql = "SELECT * FROM " . tablename('community_group') . " where comgroupid = :comgroupid";

	$res = pdo_fetch($sql, array(':comgroupid' => $groupid));
	$res['thumbs'] = unserialize($res['thumbs']);
	if(!is_array($res['thumbs']) || empty($res['thumbs'])){
		$res['thumbs'] = array();
	}
	return $res;
}

/**
 * 下拉选择可用
 *
 *
 */
function comgroup_selectByStatus($status = 1){
	$sql = "SELECT comgroupid, groupname FROM " . tablename('community_group') . " where status = $status ";
	return pdo_fetchall($sql);
}

function comgroup_toSearchByList(){
	global $_W, $_GPC;
	$data = array();
	$sh_title = !empty($_GPC['search']) ? htmlspecialchars(trim($_GPC['search'])) : null;
	if(!$sh_title){
		$data['status'] = false;
		return $data;
	}
	
	$res = pdo_fetchall("SELECT * FROM ".tablename("community_group")." WHERE `communityname` LIKE '%".$sh_title."%'");

	if(!$res){
		$data['status'] = false;
	} else {
		$data['result'] = $res;
		$data['status'] = true;
	}

	return $data;
}