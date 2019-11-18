<?php
/**
 * 佣金提现申请
 * load()->model('reward.withdraw');
 */

/**
 * 获取列表
 */
function Reward_getListToManage(){
	global $_W, $_GPC;
	$paras = array();
	$ret = array('param'=>array());
	$pindex = max(1, intval($_GPC['page']));
	$psize = intval($_GPC['psize']) ? intval($_GPC['psize']) : 10;
	$condition = ' 1 ';

	//时间
	if (!empty($_GPC['time'])) {
		$paras[':starttime'] = $ret['param']['starttime'] = strtotime($_GPC['time']['start']);
		$paras[':endtime'] = $ret['param']['endtime'] = strtotime($_GPC['time']['end']) + 86399;
		$condition .= " AND (c.create_time between :starttime AND :endtime) ";
	}
	if (!isset($ret['param']['starttime']) || !isset($ret['param']['endtime'])) {
		$ret['param']['starttime'] = strtotime('-12 month');
		$ret['param']['endtime'] = time();
	}
	if (isset($_GPC['sn']) && $_GPC['sn']!='') {
		$condition .= " AND c.sn LIKE '%".$_GPC['sn']."%'";
	}

	$ret['list'] = pdo_fetchall('SELECT c.*,m.uid,m.nickname FROM '.tablename('brokers_reward_withdraw').' as c 
		LEFT JOIN '.tablename('members').' m on c.brokerid=m.uid 
		WHERE '.$condition.'
		ORDER BY c.id DESC LIMIT '.($pindex-1)*$psize.','.$psize, $paras);
	$ret['total'] = pdo_fetchcolumn('SELECT count(id) FROM '.tablename('brokers_reward_withdraw')." as c WHERE $condition ", $paras);
	$ret['pager'] = pagination($ret['total'], $pindex, $psize);
	return $ret;
}


/**
 * 保存佣金提现申请信息
 */
function Reward_saveInfo(){
	global $_W, $_GPC;
	$id     = intval($_GPC['applyid']);
	$status = intval($_GPC['status']);

	//过滤提示
	if(!intval($_GPC['status'])){
		message('处理操作不能为空', referer(), 'error');
	}
	if(!$id){
		message('处理操作不能为空', referer(), 'error');
	}

	//如果已经取消或者完结的不能再处理
	$reward = pdo_fetch('select * from '.tablename('brokers_reward_withdraw').' where id = :id', array(':id' => intval($id)));
	if ($reward['status']>2) {
		message('申请已经取消或者完结的不能再操作', referer(), 'error');
	}

	//查询用户可提现额度，够则减并记录日志
	$member = pdo_fetch('select * from '.tablename('members').' where uid = :id', array(':id' => intval($reward['brokerid'])));

	if ($member['credits6']<$reward['amount'] &&($status==1 || $status==3)) {
		message('申请提现金额大于可提现额度，操作失败！', referer(), 'error');
	} else {
		$data = array();
		$data['remark'] = json_decode($reward['remark'], true);
		$data['remark'][] = array(
				'operatorid'=>$_W['user']['uid'],
				'time'      => time(),
				'operator'  =>$_W['user']['username'],
				'nickname'  =>$_W['user']['remark'],
				'mark'      => $_GPC['remark'],
				'operation' => getStatusTypeStr($_GPC['status'])
		);
		$data['remark'] = json_encode($data['remark']);
		$data['status'] = $status;
		//发放现金流程：先减再更新再记录日志
		if ($status==3) {
			$newcredits6 = $member['credits6'] - $reward['amount'];
			$decrease = pdo_update('members', array('credits6'=>$newcredits6), array('uid'=>$reward['brokerid']));
			if ($decrease) {
				$creditsLog = array();
				$operate = $_W['user']['uid'].' - '.$_W['user']['username'].' - '.$_W['user']['remark'];
				$creditsLog['uid']     = $reward['brokerid'];
				$creditsLog['type']    = 'credits6';
				$creditsLog['amount']  = -$reward['amount'];
				$creditsLog['remarks'] = '用户申请提现(id - '.$id.')，操作员信息('.$operate.')，可提现分成减少￥'.$reward['amount'].'，操作时间'.date('Y-m-d H:i:s');
				pdo_insert('members_credits_log', $creditsLog);
			}
		}
		return pdo_update('brokers_reward_withdraw', $data, array('id' => $id));
	}
}

/**
 * 获取佣金提现申请的详细信息
 */
function Reward_getDetailToManage($id = 0){
	global $_W, $_GPC;
	$id || $id = $_GPC['applyid'];
	return pdo_fetch('select * from '.tablename('brokers_reward_withdraw').' where id = :id', array(':id' => intval($id)));
}


/* 状态类型 */
function statusTypeList() {
	return array(
		'0'=>'发起申请', '1'=>'审核可发', '2'=>'审核未通过', '3'=>'已发放', '4'=>'取消',
	);
}
function getStatusTypeStr($status) {
	$list = statusTypeList();
	return in_array($status, array_keys($list)) ? $list[$status] : '其他';
}