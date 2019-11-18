<?php
/**
 * 签到（前端逻辑调用）
 * 
 */
defined('IN_IA') or exit('Access Denied');


/**
 * 删除收藏
 * @param id int 
 * @param type int
 */
function delCollectById($id = 0,$type = 1){
	global $_W;
	if($id === 0 || empty($_W['member']['uid'])){
		return false;
	}
	return pdo_delete('mc_goods_collect', array('uid' => $_W['member']['uid'], 'collectid' => $id, 'type' => $type,'weid' => $_W['uniacid']));
}


/**
 * 获取我的签到记录
 *
 */
function getMySignin(){
	global $_W;
	$ret = array('signnum'=>date(t,time()), 'usersign' => array('times'=>0, 'counttimes'=>0, 'lasttime'=>0));

	//我的签到信息
	$usersign = pdo_fetch(' select times, counttimes,lasttime from '.tablename('mc_member_sign') . 
						' where  uid = :uid', array(':uid'=>$_W['member']['uid']));
	if(!empty($usersign)){
		$ret['usersign'] = $usersign;
		
	}
	//获取签到产品
	$ret['signgoods'] = getSignGoods();
	if(!empty($ret['signgoods'])){
		foreach ($ret['signgoods'] as $k => $v) {
			//查看那个产品可以
			$ret['signgoods'][$k]['enabled'] = ($v['exchangevalue'] <= $ret['usersign']['times']) ? true : false;
		}
	}
	$ret['usersign']['route'] = ($ret['usersign']['times']/$ret['signnum'])*100;
	return $ret;
}

/**
 * 记录签到日志
 *
 */
function Signrecord(){
	global $_W;
	$_SESSION['usersign'] = 1;
	
	load()->model('mc.member.sign.record');
	Signrecord_signIn();
}

function insertUserSign(){
	global $_W;
	$uid = $_W['member']['uid'];
	$data = array(
		'uid' => $uid,
		'times' => 1,
		'counttimes' => 1,
		'createtime' => TIMESTAMP,
		'lasttime' => TIMESTAMP,
	);
	if($uid){
		load()->model('mc.member.sign.record');
		Signrecord_signIn();
		pdo_insert('mc_member_sign', $data);
	}
	return false;
}

/**
 * 获取签到奖励的产品
 */
function getSignGoods(){
	global $_W;

	$res = pdo_fetchall('SELECT ag.fid, ag.awardid, ag.awardtitle, ag.awardvalue, ag.exchangetype, ag.exchangevalue, ag.total, ag.exchangetimes, ag.endtime, sg.thumb, sg.marketprice
						 FROM '.tablename('activity_goods').' ag left join '.tablename('shopping_goods').' sg on ag.awardid = sg.id 
						 where ag.`status` = 1 and ag.uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
	if(!empty($res)){
		foreach($res as $key => $row){
			if($row['thumb']){
				$res[$key]['thumb'] = tomedia($row['thumb']);
			}else{
				$res[$key]['thumb'] = $_W['siteroot'].'addons/ewei_shopping/images/img/exchange-gift.png';
			}
			if($row['endtime'] != 0 && $row['endtime'] < time()){
				unset($res[$key]);
			}
			if(empty($row['marketprice'])){
				$res[$key]['marketprice'] = '0.00';
			}
			$res[$key]['link'] = "./index.php?i=16&j=16&c=entry&agid=".$row['fid']."&awardid=".$row['awardid']."&do=UserSign&m=ewei_shopping&wxref=mp.weixin.qq.com#wechat_redirect";
		}
	}
	return $res;
}

/**
 * 用户签到
 * continuity标识1的话，用户已经正式开始续签模式，接收到我们的推送
 */
function saveUserSign(){
	global $_W;
	$uid = $_W['member']['uid'];
	if(empty($uid)){
		return -200;
	}
	$ret = 200;
	$res = pdo_fetch(' select lasttime, uid, continuity from ' . tablename('mc_member_sign') . 
				 ' where uid = :uid', array(':uid' => $uid));
	if(!empty($res)){ 
		if($res['lasttime'] == 0){
			//上次签到时间判断为零，当前签到次数不加
			pdo_update("mc_members", array("lasttime" => TIMESTAMP), array('uid' => $uid));
			$ret = -100;//异常签到
		}else{
			if(date('Y-m-d', strtotime('- 1 day')) == date('Y-m-d', $res['lasttime'])){
				pdo_query('	update ' . tablename('mc_member_sign') .  
						  '	set times = times + 1, counttimes = counttimes + 1, continuity = 1, lasttime = ' . TIMESTAMP . 
						  ' where uid = :uid', array(':uid' => $uid));
				Signrecord();
			}else if(date('Y-m-d') == date('Y-m-d', $res['lasttime'])){
				$ret = -101;//今天已经签过了
			}else{
				if($res['continuity'] == 0){
					//当前未开启续签状态
					pdo_query('	update ' . tablename('mc_member_sign') .  
							  '	set times = times + 1, counttimes = counttimes + 1, continuity = 1, lasttime = ' . TIMESTAMP . 
							  ' where uid = :uid', array(':uid' => $uid));
					Signrecord();
				}elseif($res['continuity'] == 1){
					//超过1天以上为断签，设置为一
					pdo_query('	update ' . tablename('mc_member_sign') .  
							  '	set times = 1, counttimes = counttimes + 1, lasttime = ' . TIMESTAMP . 
							  ' where uid = :uid', array(':uid' => $uid));
					Signrecord();
				}
			}
		}
	}else{
		insertUserSign();
	}
	return $ret;
}

/**
 * 用户兑换奖励
 * @param int agid (奖励表)
 */
function signin_exchangeGoods($agid = 0, $actiontype = 1){
	global $_W, $_GPC;
	$uid = $_W['member']['uid'];
	$condition = ' WHERE a.uniacid = :uniacid and a.`status` = 1 and a.actiontype = :actiontype and a.fid = :agid ';
	$pars = array(':uniacid' => $_W['uniacid'], ':actiontype' => $actiontype, ':agid' => $agid);
	$res = pdo_fetch('SELECT a.awardid, a.awardtitle, a.awardvalue, a.actiontype, a.exchangetype, a.exchangevalue, a.total, a.exchangetimes, a.endtime, b.marketprice awardprice, b.thumb
					  FROM ' . tablename('activity_goods') .' a left join ' . tablename('shopping_goods') . ' b on a.awardid = b.id ' . $condition, $pars);
	if($res){
		if($res['endtime'] != 0 && $res['endtime'] < TIMESTAMP){
			//过期
			return -504;
		}
		if($res['total'] != 0 && $res['total'] <= $res['exchangetimes']){
			# 当前兑换完了
			return -510;
		}
		$usertimes = pdo_fetchcolumn(' SELECT times FROM '.tablename('mc_member_sign') . 
									 ' WHERE uid = :uid', array(':uid' => $uid));
		if($res['exchangevalue'] > $usertimes){
			# 次数不足
			return -501;
		}
		if($res['exchangetype'] == 1){
			# 产品
			$data = array(
						'uid' => $uid,
						'agid' => $agid,
						'awardid' => $res['awardid'],
						'awardtitle' => $res['awardtitle'],
						'awardprice' => $res['awardprice'],
						'exchangetype' => $res['exchangetype'],
						'thumb'	=> $res['thumb'],
						'createtime' => TIMESTAMP,
						'uniacid' => $_W['uniacid'],
						'status' => 0
						);
		}else if($res['exchangetype'] == 2){
			# 红包兑换券
			$data = array(
						'uid' => $uid,
						'agid' => $agid,
						'awardid' => $res['awardid'],
						'awardtitle' => $res['awardtitle'],
						'awardvalue' => $res['awardvalue'],
						'exchangetype' => $res['exchangetype'],
						'createtime' => TIMESTAMP,
						'uniacid' => $_W['uniacid']
						);
		}
		if(!empty($data)){
			# 添加领取日志
			pdo_insert('activity_goods_record', $data);
			$id = pdo_insertid();

			pdo_query('update '.tablename('activity_goods').'set exchangetimes = exchangetimes + 1 where fid = '.$agid);
			pdo_query('	update ' . tablename('mc_member_sign') .  
					  '	set times = times - '.$res['exchangevalue']. ', exchangetimes = exchangetimes + 1 '.
					  ' where uid = :uid', array(':uid' => $uid));
			if($res['exchangetype'] == 2){
				load()->model('mc');
				$setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
				// 添加 领取日志
				mc_credit_update(
						$uid, $setting['creditbehaviors']['currency'], $res['awardvalue'], 
						array($uid, '消费签到次数：'.$res['exchangevalue'].',领取签到红包 '.$setting['creditbehaviors']['currency'].':' . $res['awardvalue'].' 元')
					);
			}
			return $id;
		}
		return false;
	}
}

/**
 * 签到奖品选择地址快递
 * @param int agrid
 * @param int addrid
 */
function setAwardAddr(){
	global $_W, $_GPC;
	$agrid = intval($_GPC['agrid']);
	$addrid = intval($_GPC['addrid']);
	if($addrid == 0 || $agrid == 0){
		return -200;
	}
	return pdo_update('activity_goods_record',
					array('addrid' => $addrid, 'status' => 1), 
					array('agrid' => $agrid, 'uid' => $_W['member']['uid'], 'uniacid' => $_W['uniacid'], 'expresstime' => 0));
}

/**
 * 获取我的兑换记录
 * 
 */
function signin_getAwardRecord(){
	global $_W, $_GPC;
	$res = pdo_fetchall('SELECT agrid, awardtitle, awardvalue, awardprice, thumb, createtime, exchangetype, status 
						 FROM '.tablename('activity_goods_record').' where uid = :uid order by createtime desc', array(':uid' => $_W['member']['uid']));
	foreach ($res as $key => $value) {
		if($value['exchangetype'] == 2 && empty($value['thumb'])){
			$res[$key]['thumb'] = $_W['siteroot'].'addons/ewei_shopping/images/img/exchange-gift.png';
		}else{
			$res[$key]['thumb'] = tomedia($value['thumb']);
		}
		$res[$key]['createtime'] = date('Y-m-d H:i', $value['createtime']);
	}
	return $res;
}