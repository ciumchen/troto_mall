<?php
/**
 * load()->model('community.expert')
 * 社群专家
 *
 */

function comexpert_listToManage(){
	global $_W, $_GPC;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$condition = " where a.expertid > 0";
	if($_GPC['expertname']){
		$condition .= ' and a.expertname like "%'.$_GPC['expertname'].'%"';
	}
	if($_GPC['comgroupid']){
		$condition .= ' and a.comgroupid = '.$_GPC['comgroupid'];
	}
	$sql = "SELECT a.*, b.groupname FROM ".tablename('community_expert')." a left join ".tablename('community_group')." b on a.comgroupid = b.comgroupid 
			$condition ORDER BY a.displayorder DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;

	$list = pdo_fetchall($sql);
	$total = pdo_fetchcolumn("SELECT a.*, b.groupname FROM ".tablename('community_expert')." a left join ".tablename('community_group')." b on a.comgroupid = b.comgroupid 
			$condition");
	$pager = pagination($total, $pindex, $psize);

	return array('list' => $list, 'pager' => $pager, 'total' => $total);
}

/**
 * 社区专家添加或修改
 * @param int $id expertid
 * @param data expert
 */
function comexpert_postToManage(){
	global $_W, $_GPC;
	$expertid = (int) $_GPC['id'];
	$data = array(
			'comgroupid' => (int) $_GPC['comgroupid'],
			'expertname' => $_GPC['expertname'],
			'label' 	 => $_GPC['label'],
			'profession' => $_GPC['profession'],
			'realname'	 => $_GPC['realname'],
			'avatar'	 => $_GPC['avatar'],
			'mobile'	 => $_GPC['mobile'],
			'content'	 => $_GPC['content'],
			'jointime'	 => TIMESTAMP
		);

	if($expertid){
		$res = pdo_update('community_expert', $data, array('expertid' => $expertid));
	}else{
		pdo_insert('community_expert', $data);
		$res = pdo_insertid();
	}
	if($res){
		message('编辑成功', referer(), 'success');
	}
	message('操作失败！', referer(), 'danger');
}


function comexpert_detail(){
	global $_W, $_GPC;
	$expertid = (int) $_GPC['id'];
	$sql = "SELECT * FROM " . tablename('community_expert') . " where expertid = :expertid";

	$res = pdo_fetch($sql, array(':expertid' => $expertid));
	return $res;
}

function comexpert_getToOnceDetail(){
	global $_GPC;

	$groupid = intval($_GPC['id']);
	$sql = "SELECT * FROM ".tablename("community_expert")." WHERE comgroupid = {$groupid}";

	return pdo_fetchall($sql);
}