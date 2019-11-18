<?php
/**
 * load()->model('community.application');
 * 社区申请
 *
 */

function commappli_listToManage(){
	global $_W, $_GPC;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$condition = " where caid > 0 ";
	$pars = array();
	# 1小区 2写字楼
	if(!empty($_GPC['type'])){
		$condition .= ' and `type` = :type';
		$pars[':type'] = intval($_GPC['type']);
	}
	# 用户ID
	if(!empty($_GPC['uid'])){
		$condition .= ' and `uid` = :uid';
		$pars[':uid'] = intval($_GPC['uid']);
	}
	# 用户名
	if(!empty($_GPC['username'])){
		$condition .= ' and `username` like "%'.trim($_GPC['username']).'"&';
	}
	# 物业名
	if(trim($_GPC['communityname'])){
		$condition .= ' and communityname like "%'.trim($_GPC['communityname']).'%"';
	}

	$sql = "SELECT caid, uid, mobile, username,communityname, province, city, area, address, createdt, status, type FROM " . tablename('community_application') . " $condition ORDER BY createdt DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;

	$list = pdo_fetchall($sql, $pars);
	
	$total = pdo_fetchcolumn('SELECT COUNT(caid) FROM ' . tablename('community_application') . " $condition");
	$pager = pagination($total, $pindex, $psize);

	return array('list' => $list, 'pager' => $pager, 'total' => $total);
}

function commappli_postStatusToManage(){
	global $_W, $_GPC;
	$caid = (int) $_GPC['id'];
	$status = (int) $_GPC['status'];
	if($status == 0){
		$status = 1;
	}else if($status == 1){
		$status = 2;
	}
	$message = array('status' => $status);
	if(pdo_update('community_application', array('status' => $status), array('caid' => $caid))){
		$message['msg'] = '修改成功';
	}else{
		$message['msg'] = '修改失败';
	}
	ajaxReturn($message);
}

function commappli_addPostVillage(){
	global $_W, $_GPC;
	$data = array();
	$data['uid'] = $_W['member']['uid'];
	$data['username'] = $_GPC['userName'];
	$data['mobile'] = (int) $_GPC['commPhone'];
	$data['communityname'] = $_GPC['commName'];
	$data['province'] = $_GPC['commProvance'];
	$data['city'] = $_GPC['commCity'];
	$data['area'] = $_GPC['commArea'];
	$data['address'] = $_GPC['commAddress'];
	$data['createdt'] = time();
	$data['type'] = 1;
	pdo_insert("community_application",$data);
	$sid = pdo_insertid();
	if($sid > 0){
		message("申请成功！",url("community/index").'id='.$_GPC['id'].'m=ewei_shopping','success');
	} else {
		message("申请失败！",referer(),'error');
	}
}