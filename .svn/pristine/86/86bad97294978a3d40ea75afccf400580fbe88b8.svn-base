<?php
/**
 * load()->model('event.log');
 * 事件表
 */

/**
 * 查看当前有没有事件
 * @param int $uid
 * 事件类型 $type
 *   1-抢红包, 2-充值, 3-积分兑换余额, 4-发红包, 5-签到兑换商品
 *   6-生成订单, 7-退换货, 8-修改上家, 9-优惠券关注领取, 10-优惠券关注激活
 */
function event_getEventByCard($uid){
	global $_W;
	$condition = " WHERE eventUser = :uid and eventType between 9 and 10";
	$pars = array(':uid' => $uid);
	$res =  pdo_fetchall("SELECT eventId id, eventUser uid, eventType type, eventData data FROM " . tablename('event_log') . $condition, $pars);
	return $res;
}

/**
 * 删除事件
 * @param int $eventid
 */
function event_deleteEvent($eventid){
	return pdo_delete('event_log', array('eventId' => $eventid));
}

function event_exist($uid, $type, $data){
	global $_W;
	$sql = 'select eventData from ims_event_log where eventUser = :user and eventType = :type';
	$res = pdo_fetchcolumn($sql, array(':user'=>$uid, ':type' => $type));
	if($res){
		$res = unserialize($res);
		if($data['cid'] == $res['cid']){
			return false;
		}
	}
	return true;
}