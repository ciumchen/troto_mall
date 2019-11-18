<?php
/**
 * load()->model('mc.get.bonus');
 *
 *
 */


/**
 * 判断用户是否领取，根据uniacid, getuid, 时间判断当前是否可以领取
 * @param int $snid
 * @param int $uid
 */
function getBonus_existGain($snid = 0, $uid = 0){
	global $_W;
	return pdo_fetchcolumn("SELECT count(1) FROM " . tablename('mc_get_bonus') . " 
							WHERE uniacid = :uniacid and getuid = :getuid  and snid = :snid and status = 2 and senduid = 0", 
							array(':uniacid' => $_W['uniacid'], 
								  ':getuid' => $uid,
								  ':snid' => $snid
									)
							);
}

/**
 * 根据红包ID获取当前已抢的日志
 * @param int $snid
 *
 */
function getBonus_list($id = 0, $uid = 0){
	global $_W;	

	$res = pdo_fetchall('select u.nickname,u.tag, a.getuid uid, FROM_UNIXTIME( a.time, "%m-%d %H:%i" ) time,a.money, a.fid
							  from '. tablename('mc_mapping_fans').' u right join '. tablename('mc_get_bonus').' a on u.uid = a.getuid
							  where a.status = 2 and a.senduid = 0 and a.snid = :snid  order by a.time desc', array(':snid' => $id));
	$user = false;
	if($res){
		foreach($res as $k=>$v){
			if (!empty($v['tag']) && is_string($v['tag'])) {
				if (is_base64($v['tag'])){
					$v['tag'] = @base64_decode($v['tag']);
				}
				if (is_serialized($v['tag'])) {
					$res[$k]['tag'] = @iunserializer($v['tag']);
					$res[$k]['avatar'] = $res[$k]['tag']['headimgurl'];					
					if($v['tag']['nickname'] && !$v['nickname']){
						$res[$k]['nickname'] = $res[$k]['tag']['nickname'];
						
					}
					unset($res[$k]['tag']);
				}				
			}else{
				$res[$k]['nickname'] = !empty($v['nickname']) ? $v['nickname'] : '小吉鹿';
				$res[$k]['avatar'] = !empty($v['avatar']) ? $v['avatar'] : $_W['attachurl'].'images/global/avatars/avatar_7.jpg';;
			}
			if($v['uid'] == $uid && $uid != 0){
				$user = array('money' => $v['money']);
			}
		}
	}

	return array('list' => $res, 'user' => $user);
}

/**
 * [查看当前红包记录]
 *
 * @param [intval] $uid [<用户ID>]
 * @param [intval] $status [<记录标识>, <1用户红包2群红包3大米红包>]
 * @version $Id$
 * @return [array] [<{time}>]
 */
function getBonus_timerecord($getuid, $status = 1){

	return pdo_fetchall('SELECT time FROM '. tablename('mc_get_bonus'). ' 
						 WHERE getuid = :getuid and status = :status', 
		array(':getuid' => $getuid, ':status' => $status));
}

/**
 * 抢红包记录添加
 * 
 * @param  [array] data数组
 * @version $Id$
 * @return [intval] 表ID
 */
function getBonus_post($data){
	pdo_insert('mc_get_bonus', $data);
	return pdo_insertid();
}

/**
 * 抢红阿伯数据
 * 
 * @param  [intval] $id 
 * @param  [intval] $getuid
 * @param  [intval] $status
 * @return [array] $detailDate
 */
function getBonus_getDetail($id, $getuid, $status){
	return pdo_fetch('SELECT * FROM '. tablename('mc_get_bonus'). ' 
						 WHERE getuid = :getuid and status = :status and fid = :fid', 
		array(':getuid' => $getuid, ':status' => $status, ':fid' => $id));
}