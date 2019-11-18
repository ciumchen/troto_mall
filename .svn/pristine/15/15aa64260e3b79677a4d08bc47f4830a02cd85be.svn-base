<?php
/**
 * 红包活动的处理
 * activity.redpacket.mod.php
 */
defined('IN_IA') or exit('Access Denied');

/**
 * 红包活动添加 
 *
 */
function postActivity($data = array()){
	if($data){
		pdo_insert('activity_redpacket', $data);
		return pdo_insertid();
	}
	return ;
}

/**
 * 获取红包活动
 *
 */
function getActivity(){
	global $_GPC, $_W;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$condition = ' a.uniacid = '.$_W['uniacid'];
	if(!($_W['user']['power'] & ADMINISTRATOR)){
		$condition .= ' AND a.operatorid = '.$_W['user']['uid'];
		$condition .= ' AND a.deleted = 0';
	}
	$res = array();
	$res['list'] = pdo_fetchall("SELECT 
									a.fid, a.countmoney, a.`money`, a.sendnum, a.getnum, a.timestart, a.timeend, a.deleted, a.status, a.createtime, a.gain, a.operatorid, b.username 
						 		 FROM " . tablename('activity_redpacket') . 
								" a LEFT JOIN ".tablename('users')." b on a.operatorid = b.uid WHERE {$condition} 
								ORDER BY createtime DESC
						  		LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
	$res['total'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('activity_redpacket') . " a LEFT JOIN ".tablename('users')." b on a.operatorid = b.uid WHERE {$condition}");
	$res['pager'] = pagination($res['total'], $pindex, $psize);
	return $res;
}

/**
 * 获取需要红包修改的信息
 * @param id
 */
function getInfoById($id = ''){
	return pdo_fetch('SELECT fid, countmoney, `money`, sendnum, getnum, timestart, gain, timeend, status, title, `desc`, thumb FROM ' . tablename('activity_redpacket') . ' WHERE fid = :id and deleted = 0', array(':id'=>$id));
}

/**
 * 获取需要红包修改的信息
 * @param id
 */
function redpacket_getInfoById($id = ''){
	$res = pdo_fetch('SELECT fid, countmoney, `money`, sendnum, getnum, timestart, gain, timeend, status, title, `desc`, thumb FROM ' . tablename('activity_redpacket') . ' WHERE fid = :id and deleted = 0', array(':id'=>$id));

	$time = ($res['timestart'] != $res['timeend']) ? ($res['timestart'] > TIMESTAMP || $res['timeend'] < TIMESTAMP) : false;
	if(empty($res)){
		$res['returnstatus'] = -600;
	}else if($res['status'] == 0 || $time && !$_W['isajax']){
		$res['returnstatus'] = -601;
	}else if($time){
		if($res['timestart'] > TIMESTAMP){
			$res['returnstatus'] = -603;
		}else if($res['timeend'] < TIMESTAMP){
			$res['returnstatus'] = -604;
		}
	}else if($res['sendnum'] != 0 && $res['sendnum'] <= $res['getnum']){
		$res['returnstatus'] = -610;
	}else if($res['countmoney'] <= $res['gain']){
		$res['returnstatus'] = -611;
	}
	return $res;
}

/**
 * 红包申请
 */
function Redpacket_saveInfo($id = 0, $url = ''){
	global $_GPC, $_W;
	$countmoney = floatval($_GPC['countmoney']);			//活动总金额
	$money = $_GPC['money'];								//金额设置：a(5,10,15,20);b(0为随机);c(>0平均金额)
	$sendnum = $_GPC['sendnum'];							//总数量；0默认抢完即可
	$timestart = $_GPC['timestart'];						//开始时间
	$timeend = $_GPC['timeend'];							//结束时间
	$title = $_GPC['title'];
	$desc = $_GPC['desc'];
	$thumb = tomedia($_GPC['thumb']);

	if($timeend < $timestart){
		message('限时时间中，开始时间不能大于结束时间！',referer(), 'error');
	}
	foreach(explode(',', $money) as $v){
		if($v > $countmoney){
			message('限时时间中，领取的金额不能大于总金额，请重新设置！',referer(), 'error');	
		}
	}
	if(!empty($id)){
		$condition = ' status = 0';
		if(!empty($countmoney)){
			$condition .= ' ,countmoney = '.$countmoney;
		}

		if($money != ''){
			$condition .= ' ,money = "'.$money.'"';
		}

		if(!empty($sendnum)){
			$condition .= ' ,sendnum = '.$sendnum;
		}

		if(!empty($timestart)){
			$condition .= ' ,timestart = "'.strtotime($timestart).'"';
		}

		if(!empty($timeend)){
			$condition .= ' ,timeend = "'.strtotime($timeend).'"';
		}

		if(!empty($title)){
			$condition .= ' ,title = "'.$title.'"';
		}

		if(!empty($desc)){
			$condition .= ' ,`desc` = "'.$desc.'"';
		}

		if(!empty($thumb)){
			$condition .= ' ,thumb = "'.$thumb.'"';
		}
		$where = ' where fid = :fid and deleted = 0 ';
		$pars = array(':fid' => $id);
		if(!($_W['user']['power'] & ADMINISTRATOR)){
			$where .=' and operatorid = :operatorid';
			$pars[':operatorid'] = $_W['user']['uid'];
		}

		$res = pdo_query('update '.tablename('activity_redpacket').' set '.$condition.$where, $pars);
		if($res){
			message('修改成功！', $url, 'success');
		}
	}else{
		$res = postActivity(
			array('countmoney' => $countmoney, 
				  'money' => $money, 
				  'sendnum' => $sendnum, 
				  'timestart' => strtotime($timestart), 
				  'timeend' => strtotime($timeend),
				  'uniacid' => $_W['uniacid'],
				  'createtime' => TIMESTAMP,
				  'title' => $title,
				  'desc' => $desc,
				  'thumb' => $thumb,
				  'operatorid' => $_W['user']['uid']
				  )
			);
		if($res){
			message('添加成功，请联系超级管理员修改该红包活动的状态！', $url, 'success');
		}
	}
	message('操作失败！',referer(), 'error');
}

/**
 * 活动状态
 * @param int id     活动ID
 * @param int status 状态
 */
function Redpacket_setStatus($id = 0,$status = 0){
	global $_W;

	//没有权限返回
	if(!($_W['user']['power'] & ADMINISTRATOR)){
		return false;
	}
	return pdo_update('activity_redpacket', array('status'=>$status), array('fid' => $id));
}